<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\OrderStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\User;
use App\Models\Address;
use App\Services\OrderService;
use App\Services\PaymentRefundService;
use App\Services\GhnService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Jobs\SendOrderStatusUpdatedMail;



class OrderController extends Controller
{
    protected $orderService;
    protected $paymentRefundService;
    protected $ghnService;

    public function __construct(OrderService $orderService, PaymentRefundService $paymentRefundService, GhnService $ghnService)
    {
        $this->orderService = $orderService;
        $this->paymentRefundService = $paymentRefundService;
        $this->ghnService = $ghnService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'address', 'orderStatus', 'paymentStatus'])
            ->orderBy('created_at', 'desc');
        $orderStatuses = OrderStatus::query()->get();
        $paymentStatuses = PaymentStatus::query()->get();
        // tìm kiếm đơn hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%$search%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
                  });
            });
        }
        if ($request->filled('status')) {
            $query->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }
        if ($request->filled('payment')) {
            $query->whereHas('paymentStatus', function ($q) use ($request) {
                $q->where('name', $request->payment);
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(10);

        // lấy ra số đơn hàng theo trạng thái
        $orderCounts = [
            'total' => Order::count(),
            'Chờ Xác Nhận' => Order::whereHas('orderStatus', function ($q) {
                $q->where('name', 'Chờ Xác Nhận');
            })->count(),
            'Đã Giao Thành Công' => Order::whereHas('orderStatus', function ($q) {
                $q->where('name', 'Đã Giao Thành Công');
            })->count(),
            'Đã Hủy' => Order::whereHas('orderStatus', function ($q) {
                $q->where('name', 'Đã Hủy');
            })->count(),
        ];

        return view('admin.orders.index', compact('orders', 'orderCounts', 'orderStatuses', 'paymentStatuses'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'address',
            'orderStatus',
            'paymentStatus',
            'voucher',
            'payments',
            'invoice',
        ])->findOrFail($id);

        // Get order items with their attribute values
        $orderItems = OrderItem::where('order_id', $id)
            ->with(['book', 'attributeValues.attribute', 'bookFormat', 'collection'])
            ->get();
        foreach ($orderItems as $item) {
            $bookFormat = optional($item->bookFormat)->format_name;  // Safely access 'name' of 'bookFormat'
        }
        // dd($bookFormat);
        // Generate QR code if not exists
        // if (!$order->qr_code) {
        //     $this->generateQrCode($order);
        // }

        return view('admin.orders.show', compact('order', 'orderItems', 'bookFormat'));
    }

    public function edit($id)
    {
        $order = Order::with(['user', 'address', 'orderStatus', 'paymentStatus'])->findOrFail($id);
        $currentStatus = $order->orderStatus->name;
        $allowedNames = OrderStatusHelper::getNextStatuses($currentStatus);
        $orderStatuses = OrderStatus::whereIn('name', $allowedNames)->get();
        $paymentStatuses = PaymentStatus::all();

        // Generate QR code if not exists
        // if (!$order->qr_code) {
        //     $this->generateQrCode($order);
        // }

        return view('admin.orders.edit', compact('order', 'orderStatuses', 'paymentStatuses'));
    }

    public function showRefund($id)
    {
        try {
            $order = Order::with(['user', 'orderStatus', 'paymentStatus', 'paymentMethod'])
                         ->findOrFail($id);

            // Kiểm tra điều kiện hoàn tiền - Thay đổi: Cho phép hoàn tiền với đơn hàng "Thành công"
            if ($order->orderStatus->name !== 'Thành công') {
                Toastr::error('Chỉ có thể hoàn tiền cho đơn hàng đã hoàn thành thành công', 'Lỗi');
                return redirect()->route('admin.orders.show', $id);
            }

            if (in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền'])) {
                Toastr::error('Đơn hàng đã được hoàn tiền hoặc đang trong quá trình hoàn tiền', 'Lỗi');
                return redirect()->route('admin.orders.show', $id);
            }

            if ($order->paymentStatus->name !== 'Đã Thanh Toán') {
                Toastr::error('Chỉ có thể hoàn tiền cho đơn hàng đã thanh toán', 'Lỗi');
                return redirect()->route('admin.orders.show', $id);
            }

            return view('admin.orders.refund', compact('order'));
        } catch (\Exception $e) {
            Log::error('Error showing refund form: ' . $e->getMessage());
            Toastr::error('Có lỗi xảy ra khi hiển thị form hoàn tiền', 'Lỗi');
            return redirect()->route('admin.orders.index');
        }
    }

    public function processRefund(Request $request, $id)
    {
        $request->validate([
            'refund_method' => 'required|in:wallet,vnpay',
            'refund_amount' => 'required|numeric|min:0',
            'refund_reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);
            
            // Validate refund conditions
            if ($order->orderStatus->name !== 'Thành công') {
                throw new \Exception('Chỉ có thể hoàn tiền cho đơn hàng đã hoàn thành thành công');
            }

            if ($order->paymentStatus->name === 'Đã Hoàn Tiền') {
                throw new \Exception('Đơn hàng này đã được hoàn tiền trước đó');
            }

            if ($request->refund_amount > $order->total_amount) {
                throw new \Exception('Số tiền hoàn không được vượt quá tổng tiền đơn hàng');
            }

            // Process refund based on method
            if ($request->refund_method === 'wallet') {
                $this->orderService->refundToWallet($order, $request->refund_amount);
            } else {
                $this->orderService->refundVnpay($order, $request->refund_amount);
            }

            // Log refund information
            Log::info('Order refund processed', [
                'order_id' => $order->id,
                'amount' => $request->refund_amount,
                'method' => $request->refund_method,
                'reason' => $request->refund_reason,
                'processed_by' => auth()->id()
            ]);

            DB::commit();
            Toastr::success('Hoàn tiền đơn hàng thành công', 'Thành công');
            return redirect()->route('admin.orders.show', $id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing refund: ' . $e->getMessage());
            Toastr::error($e->getMessage(), 'Lỗi');
            return back()->withInput();
        }
    }

    /**
     * Danh sách yêu cầu hoàn tiền
     */
    public function refundList(Request $request)
    {
        $query = \App\Models\RefundRequest::with(['order.user', 'order.orderStatus', 'order.paymentStatus']);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $refunds = $query->latest()->paginate(20);

        return view('admin.orders.refunds.index', compact('refunds'));
    }

    /**
     * Chi tiết yêu cầu hoàn tiền
     */
    public function refundDetail($id)
    {
        $refund = \App\Models\RefundRequest::with(['order.user', 'order.orderStatus', 'order.paymentStatus', 'order.paymentMethod'])
            ->findOrFail($id);

        return view('admin.orders.refunds.show', compact('refund'));
    }

    /**
     * Xử lý yêu cầu hoàn tiền
     */
    public function processRefundRequest(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:completed,rejected',
            'admin_note' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $refund = \App\Models\RefundRequest::findOrFail($id);
            $order = $refund->order;

            if ($request->status === 'completed') {
                // Xử lý hoàn tiền theo phương thức được chọn
                if ($refund->refund_method === 'vnpay') {
                    // Hoàn tiền qua VNPay
                    try {
                        $result = $this->paymentRefundService->refundVnpay($order, $refund);
                        Log::info('VNPay refund processed successfully via admin', [
                            'order_id' => $order->id,
                            'refund_id' => $refund->id,
                            'amount' => $refund->amount,
                            'result' => $result
                        ]);
                    } catch (\Exception $vnpayError) {
                        Log::error('VNPay refund failed', [
                            'order_id' => $order->id,
                            'refund_id' => $refund->id,
                            'error' => $vnpayError->getMessage()
                        ]);
                        throw $vnpayError;
                    }
                } elseif ($refund->refund_method === 'wallet') {
                    // Hoàn tiền vào ví
                    Log::info('Starting wallet refund via admin approval', [
                        'refund_id' => $refund->id,
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'amount' => $refund->amount
                    ]);
                    
                    try {
                        $result = $this->paymentRefundService->refundToWallet($order, $refund->amount, $refund);
                        Log::info('Wallet refund processed successfully via admin', [
                            'order_id' => $order->id,
                            'refund_id' => $refund->id,
                            'amount' => $refund->amount,
                            'result' => $result
                        ]);
                    } catch (\Exception $walletError) {
                        Log::error('Wallet refund failed', [
                            'order_id' => $order->id,
                            'refund_id' => $refund->id,
                            'error' => $walletError->getMessage(),
                            'trace' => $walletError->getTraceAsString()
                        ]);
                        throw $walletError;
                    }
                }
            }

            // Cập nhật yêu cầu hoàn tiền
            $refund->update([
                'status' => $request->status,
                'admin_note' => $request->admin_note,
                'processed_at' => now(),
            ]);

            // Gửi thông báo cho khách hàng (qua order user relationship)
            // $order->user->notify(new \App\Notifications\RefundStatusUpdatedNotification($refund));

            // Log hoạt động
            Log::info('Refund request processed', [
                'refund_id' => $refund->id,
                'order_id' => $order->id,
                'status' => $request->status,
                'processed_by' => auth('admin')->id()
            ]);

            DB::commit();
            Toastr::success('Yêu cầu hoàn tiền đã được xử lý thành công', 'Thành công');
            return redirect()->route('admin.refunds.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing refund request', [
                'refund_id' => $refund->id ?? 'unknown',
                'order_id' => $order->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Toastr::error('Có lỗi xảy ra khi xử lý yêu cầu hoàn tiền. Vui lòng thử lại sau.', 'Lỗi');
            return back()->withInput();
        }
    }

    public function refundStatus($id)
    {
        try {
            $order = Order::with(['payments' => function($query) {
                $query->where('type', 'refund')->latest();
            }])->findOrFail($id);

            $lastRefundPayment = $order->payments->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $lastRefundPayment ? $lastRefundPayment->status : null,
                    'amount' => $lastRefundPayment ? $lastRefundPayment->amount : 0,
                    'processed_at' => $lastRefundPayment ? $lastRefundPayment->created_at->format('d/m/Y H:i:s') : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking refund status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra trạng thái hoàn tiền'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_statuses,id',
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'cancellation_reason' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);
            $currentStatus = $order->orderStatus->name;
            $newStatus = OrderStatus::findOrFail($request->order_status_id)->name;
            $allowed = OrderStatusHelper::getNextStatuses($currentStatus);

            // Kiểm tra nếu trạng thái mới là "Đã hủy" thì yêu cầu lý do hủy hàng
            if ($newStatus === 'Đã hủy' && empty($request->cancellation_reason)) {
                Toastr::error('Vui lòng nhập lý do hủy hàng khi đổi trạng thái thành "Đã hủy"', 'Lỗi');
                return back()->withInput();
            }

            // ✅ Kiểm tra hợp lệ TRƯỚC khi cập nhật
            if (!in_array($newStatus, $allowed)) {
                Toastr::error("Trạng thái mới không hợp lệ với trạng thái hiện tại", 'Lỗi');
                return back()->withInput();
            }

            $updateData = [
                'order_status_id' => $request->order_status_id,
                'payment_status_id' => $request->payment_status_id,
            ];

            // Nếu trạng thái mới là "Đã hủy", thiết lập ngày hủy và lý do hủy
            if ($newStatus === 'Đã hủy') {
                $updateData['cancelled_at'] = now();
                $updateData['cancellation_reason'] = $request->cancellation_reason;
            }

            $order->update($updateData);

            // Ghi log
            Log::info("Order {$order->id} status changed from {$currentStatus} to {$newStatus} by admin");

            DB::commit();

            // Gửi mail thông báo cập nhật trạng thái đơn hàng qua queue
            dispatch(new SendOrderStatusUpdatedMail($order, $newStatus));

            Toastr::success('Cập nhật trạng thái đơn hàng thành công', 'Thành công');
            return redirect()->route('admin.orders.show', $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status: ' . $e->getMessage());

            Toastr::error('Lỗi khi cập nhật trạng thái đơn hàng. Vui lòng thử lại.', 'Lỗi');
            return back();
        }
    }

    /**
     * Tạo đơn hàng GHN cho đơn hàng hiện có
     */
    public function createGhnOrder($id)
    {
        try {
            $order = Order::with(['address', 'orderItems.book'])->findOrFail($id);
            
            // Kiểm tra điều kiện tạo đơn GHN
            if ($order->delivery_method !== 'delivery') {
                Toastr::error('Chỉ có thể tạo đơn GHN cho đơn hàng giao hàng tận nơi', 'Lỗi');
                return back();
            }
            
            if ($order->ghn_order_code) {
                Toastr::error('Đơn hàng đã có mã vận đơn GHN', 'Lỗi');
                return back();
            }
            
            // Tạo đơn hàng GHN
            $result = $this->orderService->createGhnOrder($order);
            
            if ($result && isset($result['order_code'])) {
                Toastr::success('Tạo đơn hàng GHN thành công. Mã vận đơn: ' . $result['order_code'], 'Thành công');
            } else {
                Toastr::error('Không thể tạo đơn hàng GHN. Vui lòng kiểm tra thông tin địa chỉ và thử lại.', 'Lỗi');
            }
            
            return back();
        } catch (\Exception $e) {
            Log::error('Error creating GHN order: ' . $e->getMessage());
            Toastr::error('Có lỗi xảy ra khi tạo đơn hàng GHN', 'Lỗi');
            return back();
        }
    }
    
    /**
     * Cập nhật thông tin theo dõi GHN
     */
    public function updateGhnTracking($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            if (!$order->ghn_order_code) {
                Toastr::error('Đơn hàng chưa có mã vận đơn GHN', 'Lỗi');
                return back();
            }
            
            // Lấy thông tin theo dõi từ GHN
            $trackingData = $this->ghnService->getOrderDetail($order->ghn_order_code);
            
            if ($trackingData) {
                // Cập nhật thông tin theo dõi
                $order->update([
                    'ghn_tracking_data' => $trackingData,
                    'updated_at' => now()
                ]);
                
                Toastr::success('Cập nhật thông tin theo dõi thành công', 'Thành công');
            } else {
                Toastr::error('Không thể lấy thông tin theo dõi từ GHN', 'Lỗi');
            }
            
            return back();
        } catch (\Exception $e) {
            Log::error('Error updating GHN tracking: ' . $e->getMessage());
            Toastr::error('Có lỗi xảy ra khi cập nhật thông tin theo dõi', 'Lỗi');
            return back();
        }
    }
    
    /**
     * Hủy đơn hàng GHN
     */
    public function cancelGhnOrder($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            if (!$order->ghn_order_code) {
                Toastr::error('Đơn hàng chưa có mã vận đơn GHN', 'Lỗi');
                return back();
            }
            
            // Hủy đơn hàng GHN (nếu GHN API hỗ trợ)
            // $result = $this->ghnService->cancelOrder($order->ghn_order_code);
            
            // Tạm thời chỉ xóa thông tin GHN khỏi đơn hàng
            $order->update([
                'ghn_order_code' => null,
                'ghn_service_type_id' => null,
                'expected_delivery_date' => null,
                'ghn_tracking_data' => null
            ]);
            
            Toastr::success('Đã hủy liên kết với đơn hàng GHN', 'Thành công');
            return back();
        } catch (\Exception $e) {
            Log::error('Error canceling GHN order: ' . $e->getMessage());
            Toastr::error('Có lỗi xảy ra khi hủy đơn hàng GHN', 'Lỗi');
            return back();
        }
    }
}


    //    tạo qr cho đơn hàng
    // private function generateQrCode(Order $order)
    // {
    //     try {
    //         // Create QR code with order information
    //         $orderInfo = [
    //             'id' => $order->id,
    //             'customer' => $order->user->name ?? 'N/A',
    //             'total' => $order->total_amount,
    //             'date' => $order->created_at->format('Y-m-d H:i:s')
    //         ];

    //         $qrCode = QrCode::format('png')
    //             ->size(200)
    //             ->errorCorrection('H')
    //             ->generate(json_encode($orderInfo));

    //         $filename = 'order_qr/order_' . substr($order->id, 0, 8) . '.png';
    //         Storage::disk('public')->put($filename, $qrCode);

    //         // Update order with QR code path
    //         $order->update(['qr_code' => $filename]);

    //     } catch (\Exception $e) {
    //         Log::error('Error generating QR code: ' . $e->getMessage());
    //     }
    // }
