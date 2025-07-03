<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use App\Models\Voucher;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Payment; // Thêm import Payment
use App\Models\User;
use App\Models\OrderCancellation; // Added for order cancellation
use App\Models\OrderItemAttributeValue; // Added for order item attributes
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\EmailService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\QrCodeService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    protected $orderService;
    protected $voucherService;
    protected $paymentService;
    protected $emailService;
    protected $qrCodeService;

    public function __construct(
        OrderService $orderService,
        VoucherService $voucherService,
        PaymentService $paymentService,
        EmailService $emailService,
        QrCodeService $qrCodeService
    ) {
        $this->orderService = $orderService;
        $this->voucherService = $voucherService;
        $this->paymentService = $paymentService;
        $this->emailService = $emailService;
        $this->qrCodeService = $qrCodeService;
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        $vouchers = $this->voucherService->getAvailableVouchers($user);
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        // Lấy thông tin giỏ hàng
        $cartItems = $user->cart()->with(['book.images', 'bookFormat'])->get();

        // Kiểm tra nếu giỏ hàng có cả sách vật lý và sách ebook
        $hasPhysicalBook = false;
        $hasEbook = false;
        $mixedFormatCart = false;

        foreach ($cartItems as $item) {
            if ($item->bookFormat) {
                // Kiểm tra format_name đ��� xác định loại sách
                if (strtolower($item->bookFormat->format_name) === 'ebook') {
                    $hasEbook = true;
                } else {
                    $hasPhysicalBook = true;
                }

                // Nếu tìm thấy cả hai loại, dừng vòng lặp
                if ($hasPhysicalBook && $hasEbook) {
                    $mixedFormatCart = true;
                    break;
                }
            }
        }

        // Nếu giỏ hàng có cả sách vật lý và ebook, ẩn phương thức thanh toán COD
        if ($mixedFormatCart) {
            // Lọc bỏ phương thức thanh toán khi nhận hàng (COD)
            $paymentMethods = $paymentMethods->filter(function($method) {
                return !str_contains(strtolower($method->name), 'khi nhận hàng') &&
                       !str_contains(strtolower($method->name), 'cod');
            });
        }

        // Tính tổng tiền
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('orders.checkout', compact(
            'addresses',
            'vouchers',
            'paymentMethods',
            'cartItems',
            'subtotal',
            'mixedFormatCart' // Truyền biến này để hiển thị thông báo trong view
        ));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $user = Auth::user();
        $rules = [
            'voucher_code' => 'nullable|exists:vouchers,code',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_method' => 'required|in:standard,express',
            'shipping_fee_applied' => 'required|numeric',
            'note' => 'nullable|string|max:500',

            // Address rules
            'address_id' => [
                'required_without:new_address_city_name', // Bắt buộc khi không có địa chỉ mới
                'nullable',
                'exists:addresses,id,user_id,' . ($user ? $user->id : 'NULL')
            ],

            // New address rules (chỉ bắt buộc khi không có address_id)
            'new_recipient_name' => [
                'required_without:address_id',
                'nullable',
                'string',
                'max:255'
            ],
            'new_phone' => [
                'required_without:address_id',
                'nullable',
                'string',
                'max:20'
            ],
            'new_email' => [
                'required',
                'string',
                'max:50'
            ],
            'new_address_city_name' => [
                'required_without:address_id',
                'nullable',
                'string',
                'max:100'
            ],
            'new_address_district_name' => [
                'required_without:address_id',
                'nullable',
                'string',
                'max:100'
            ],
            'new_address_ward_name' => [
                'required_without:address_id',
                'nullable',
                'string',
                'max:100'
            ],
            'new_address_detail' => [
                'required_without:address_id',
                'nullable',
                'string',
                'max:255'
            ],
        ];

        $request->validate($rules);
        $addressIdToUse = null;
        $newAddressCreated = false;

        try {
            DB::beginTransaction();
            // Determine if creating a new address or using an existing one
            if ($request->address_id) {
                $addressIdToUse = $request->input('address_id');
            } else {
                $address = Address::create([
                    'user_id' => $user->id,
                    'address_detail' => $request->input('new_address_detail'),
                    'city' => $request->input('new_address_city_name'),
                    'district' => $request->input('new_address_district_name'),
                    'ward' => $request->input('new_address_ward_name'),
                    'is_default' => false
                ]);
                $addressIdToUse = $address->id;
            }

            if (!$addressIdToUse) {
                throw new \Exception('Địa chỉ giao hàng không hợp lệ.');
            }

            // Lấy thông tin giỏ hàng của người dùng
            $cartItems = $user->cart()->with(['book.images', 'bookFormat'])->get();

            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
            }

            $orderStatus = OrderStatus::where('name', 'Chờ xác nhận')->firstOrFail();
            $paymentStatus = PaymentStatus::where('name', 'Chờ Xử Lý')->firstOrFail();

            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $actualDiscountAmount = $request->discount_amount_applied;
            if ($request->filled('applied_voucher_code')) {
                $voucher = Voucher::where('code', $request->applied_voucher_code)->first();
                // dd($voucher->id);
                if ($voucher) {
                    Log::info("Attempting to validate voucher: {$voucher->code}");
                    $now = now();
                    if ($voucher->status != 'active') {
                        Toastr::error('Mã giảm giá không còn hiệu lực');
                        return redirect()->back();
                    }

                    if ($voucher->quantity !== null && $voucher->quantity <= 0) {
                        Toastr::error('Mã giảm giá đã hết số lượng áp dụng');
                        return redirect()->back();
                    }

                    if ($voucher->start_date && $voucher->start_date > $now) {
                        Toastr::error('Mã giảm giá chỉ có hiệu lực từ ngày ' . $voucher->start_date->format('d/m/Y'));
                        return redirect()->back();
                    }

                    if ($voucher->end_date && $voucher->end_date < $now) {
                        Toastr::error('Mã giảm giá đã hết hạn sử dụng');
                        return redirect()->back();
                    }

                    if ($voucher->min_purchase_amount && $subtotal < $voucher->min_purchase_amount) {
                        Toastr::error('Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_purchase_amount) . 'đ để áp dụng mã');
                        return redirect()->back();
                    }

                    $voucherId = $voucher->id;
                } else {
                    Log::warning("Voucher '{$request->voucher_code}' not found.");
                }
            }
            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
//            dd($paymentMethod);
            // Tính tổng tiền cuối cùng
            $finalTotalAmount = $subtotal + $request->shipping_fee_applied - $actualDiscountAmount;

            // Nếu thanh toán VNPay, tạo order trước rồi chuyển hướng
            if ($paymentMethod->name == 'Thanh toán vnpay') {
                // Tạo order trước khi chuyển đến VNPay
                $order = Order::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'order_code' => 'BBE-' . time(),
                    'address_id' => $addressIdToUse,
                    'recipient_name' => $request->new_recipient_name,
                    'recipient_phone' => $request->new_phone,
                    'recipient_email' => $request->new_email,
                    'payment_method_id' => $request->payment_method_id,
                    'voucher_id' => $voucherId ?? null,
                    'note' => $request->note,
                    'order_status_id' => $orderStatus->id,
                    'payment_status_id' => $paymentStatus->id,
                    'total_amount' => $finalTotalAmount,
                    'shipping_fee' => $request->shipping_fee_applied,
                    'discount_amount' => (int) $actualDiscountAmount,
                ]);

                // Tạo OrderItems
                foreach ($cartItems as $cartItem) {
                    $orderItem = OrderItem::create([
                        'id' => (string) Str::uuid(),
                        'order_id' => $order->id,
                        'book_id' => $cartItem->book_id,
                        'book_format_id' => $cartItem->book_format_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->price,
                        'total' => $cartItem->quantity * $cartItem->price,
                    ]);

                    // Lưu thuộc tính sản phẩm
                    $attributeValueIds = $cartItem->attribute_value_ids ?? [];
                    if (!empty($attributeValueIds) && is_array($attributeValueIds)) {
                        foreach ($attributeValueIds as $attributeValueId) {
                            if ($attributeValueId) {
                                OrderItemAttributeValue::create([
                                    'id' => (string) Str::uuid(),
                                    'order_item_id' => $orderItem->id,
                                    'attribute_value_id' => $attributeValueId,
                                ]);
                            }
                        }
                    }
                }

                // Tạo mã QR cho đơn hàng
                $this->qrCodeService->generateOrderQrCode($order);
                // Commit transaction trước khi chuyển đến VNPay
                DB::commit();
                // Dữ liệu để truy��n cho VNPay
                $vnpayData = [
                    'order_id' => $order->id,
                    'payment_status_id' => $order->payment_status_id,
                    'payment_method_id' => $order->payment_method_id,
                    'order_code' => $order->order_code,
                    'amount' => $order->total_amount,
                    'order_info' => "Thanh toán đơn hàng " . $order->order_code,
                ];
//                dd($vnpayData);
                return $this->vnpay_payment($vnpayData);
            }
            $finalTotalAmount = $subtotal + $request->shipping_fee_applied - $actualDiscountAmount;
//            dd($request->new_email);
            $order = Order::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'order_code' => 'BBE-' . time(), // Consider a more robust unique order code generation
                'address_id' => $addressIdToUse,
                'recipient_name' => $request->new_recipient_name,
                'recipient_phone' => $request->new_phone,
                'recipient_email' => $request->new_email,
                // 'shipping_method' => $request->shipping_method,
                'payment_method_id' => $request->payment_method_id,
                'voucher_id' => $voucherId ?? null, // Changed to voucher_id
                'note' => $request->note,
                'order_status_id' => $orderStatus->id,
                'payment_status_id' => $paymentStatus->id,
                'total_amount' => $finalTotalAmount,
                'shipping_fee' => $request->shipping_fee_applied,
                'discount_amount' => (int) $actualDiscountAmount,
            ]);
//            dd($order->recipient_email);

            // Create OrderItems
            foreach ($cartItems as $cartItem) {
                $orderItem = OrderItem::create([
                    'id' => (string) Str::uuid(),
                    'order_id' => $order->id,
                    'book_id' => $cartItem->book_id,
                    'book_format_id' => $cartItem->book_format_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->quantity * $cartItem->price,
                ]);

                // ---- START: Added logic for saving order item attributes ----
                // IMPORTANT: Adjust '$cartItem->attribute_value_ids' if your cart item structure is different
                // For example, if attributes are in $cartItem->options['selected_attributes']
                // then use: $attributeValueIds = $cartItem->options['selected_attributes'] ?? [];
                $attributeValueIds = $cartItem->attribute_value_ids ?? [];
                // dd($attributeValueIds);
                if (!empty($attributeValueIds) && is_array($attributeValueIds)) {
                    foreach ($attributeValueIds as $attributeValueId) {
                        if ($attributeValueId) { // Ensure ID is not null or empty
                            // dd($attributeValueId);
                            OrderItemAttributeValue::create([
                                'id' => (string) Str::uuid(), // Assuming your pivot table also uses UUIDs for its PK
                                'order_item_id' => $orderItem->id,
                                'attribute_value_id' => $attributeValueId,
                            ]);
                        }
                    }
                } else {
                    OrderItemAttributeValue::create([
                        'id' => (string) Str::uuid(),
                        'order_item_id' => $orderItem->id,
                        'attribute_value_id' => 0,  // Save null into the attribute_value_id column
                    ]);
                }
            }

            $payment = $this->paymentService->createPayment([
                'order_id' => $order->id,
                'transaction_id' => $order->order_code,
                'payment_method_id' => $request->payment_method_id,
                'payment_status_id' => $order->payment_status_id,
                'amount' => $order->total_amount,
                'paid_at' => now() // Set paid_at ngay lập tức cho thanh toán thường
            ]);
            DB::commit();
            // Generate and save QR Code using QrCodeService
            $this->qrCodeService->generateOrderQrCode($order);
            $this->emailService->sendOrderConfirmation($order);
            $successMessage = 'Đặt hàng thành công!';

            // Clear the user's cart after successful order
//            $user->cart()->delete();

            Toastr::success($successMessage);
            if ($newAddressCreated) {
                $successMessage .= ' Địa chỉ mới của bạn đã được lưu.';
            }
            return redirect()->route('orders.show', $order->id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Toastr::error('Lỗi khi tạo đơn hàng 1' . $e->getMessage());
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo đơn hàng 2' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            Toastr::error('Lỗi khi tạo đơn hàng 2' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này');
        }

        $order->load([
            'orderItems.book.images',
            'orderItems.bookFormat',
            'orderStatus',
            'paymentStatus',
            'payments.paymentMethod',
            'address',
            'user',
            'paymentMethod'
        ]);

        return view('orders.show', compact('order'));
    }

    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with(['orderStatus', 'paymentStatus'])
            ->orderByDesc('created_at')
            ->paginate(7);
//        dd($orders);
        return view('orders.index', compact('orders'));
    }

    // public function applyVoucher(Request $request)
    // {
    //     // dd($request->all());
    //     Log::debug('ApplyVoucher Request Data:', $request->all());
    //     $request->validate([
    //         'voucher_code' => 'required|exists:vouchers,code',
    //         'subtotal' => 'required|numeric|min:0'
    //     ]);

    //     $voucher = Voucher::where('code', $request->voucher_code)->first();
    //     // dd($voucher);
    //     $discount = $this->voucherService->calculateDiscount($voucher, $request->subtotal);

    //     return response()->json([
    //         'success' => true,
    //         'discount_amount' => $discount
    //     ]);
    // }

    public function applyVoucher(Request $request)
    {
        Log::debug('ApplyVoucher Request Data:', $request->all());
        $request->validate([
            'voucher_code' => 'required|exists:vouchers,code',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $voucher = Voucher::where('code', $request->voucher_code)->first();
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'errors' => ['Voucher không tồn tại']
            ]);
        }

        $discountResult = $this->voucherService->calculateDiscount($voucher, $request->subtotal);

        if (isset($discountResult['errors'])) {
            return response()->json([
                'success' => false,
                'errors' => $discountResult['errors']
            ]);
        }

        return response()->json([
            'success' => true,
            'voucher_code' => $request->voucher_code,
            'discount_amount' => $discountResult['discount']
        ]);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|array|min:1', // Người dùng phải chọn ít nhất một lý do
            'reason.*' => 'string|max:255', // Đảm bảo mỗi lý do là chuỗi hợp lệ
            'other_reason' => 'nullable|string|max:255', // Nếu có "Lý do khác", kiểm tra riêng
        ]);

        $order = Order::findOrFail($request->order_id);
        $user = Auth::user();

        // Authorization: Ensure the user owns the order
        if ($order->user_id !== $user->id) {
            Toastr::error('Bạn không có quyền hủy đơn hàng này.');
            return redirect()->back()->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }
        // dd($request->order_id, $order->orderStatus->name);

        // Check if order status allows cancellation (e.g., not 'Đang giao hàng', 'Đã giao', 'Đã hủy')
        // You might need to adjust these status names based on your OrderStatusSeeder
        $cancellableStatuses = ['Chờ xác nhận'];
        if (!in_array($order->orderStatus->name, $cancellableStatuses)) {
            Toastr::error('Không thể hủy đơn hàng ở trạng thái hiện tại: ' . $order->orderStatus->name);
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại: ' . $order->orderStatus->name);
        }

        DB::beginTransaction();
        try {
            // Ghép "Lý do khác" nếu có
            $selectedReasons = $request->input('reason', []);
            if (!empty($request->input('other_reason'))) {
                $selectedReasons[] = "Lý do khác: " . $request->input('other_reason');
            }
            // Create OrderCancellation record
            OrderCancellation::create([
                'order_id' => $order->id,
                'reason' => implode(", ", $selectedReasons), // Lưu danh s��ch lý do dưới dạng chuỗi
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
                // 'refund_status' will use its default 'not_applicable'
            ]);

            // Update Order status to 'Cancelled'
            $cancelledStatus = OrderStatus::where('name', 'Đã hủy')->first();
            if (!$cancelledStatus) {
                // Fallback or error if 'Đã hủy' status doesn't exist
                Log::error('Order status "Đã hủy" not found.');
                Toastr::error('Lỗi hệ thống: Trạng thái hủy đơn hàng không tồn tại.');
                DB::rollBack();
                return redirect()->back()->with('error', 'Lỗi hệ thống khi hủy đơn hàng.');
            }
            $order->order_status_id = $cancelledStatus->id;
            $order->save();

            DB::commit();

            // Optionally, send a cancellation email
            // $this->emailService->sendOrderCancellationEmail($order);

            Toastr::success('Đơn hàng đã được hủy thành công.');
            return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được hủy thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi hủy đơn hàng ' . $order->id . ': ' . $e->getMessage());
            Toastr::error('Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng.');
        }
    }
    public function vnpay_payment($data)
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
//        $vnp_Returnurl = "http://127.0.0.1:8000/orders/{$data['order_id']}"; // Đúng route xử lý callback
        $vnp_Returnurl = route("vnpay.return"); // Đúng route xử lý callbackz
        $vnp_TxnRef = $data['order_code']; // Sử dụng order_code làm transaction reference
        $vnp_OrderInfo = $data['order_info'];
        $vnp_Amount = (int)($data['amount'] * 100); // VNPay yêu cầu amount * 100
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_OrderType" => "other",
        );

        ksort($inputData);

        $query = http_build_query($inputData);
        $hashdata = $query;

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;

        // Tạo payment record với trạng thái "Chờ Xử Lý"
        $this->paymentService->createPayment([
            'order_id' => $data['order_id'],
            'payment_method_id' => $data['payment_method_id'],
            'payment_status_id' => $data['payment_status_id'],
            'transaction_id' => $data['order_code'],
            'amount' => $data['amount'],
            'paid_at' => now()
        ]);

        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_SecureHash = $request->vnp_SecureHash;

        // Lấy tất cả tham số trừ vnp_SecureHash
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if ($key !== 'vnp_SecureHash') {
                $inputData[$key] = $value;
            }
        }

        // Sắp xếp theo key
        ksort($inputData);

        // Tạo hash string
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra tính hợp lệ của chữ ký
        if ($secureHash !== $vnp_SecureHash) {
            Log::error('VNPay signature verification failed', [
                'expected' => $secureHash,
                'received' => $vnp_SecureHash
            ]);
            return redirect()->route('orders.checkout')->with('error', 'Có lỗi xảy ra trong quá trình thanh toán');
        }

        // Lấy thông tin từ VNPay response
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TxnRef = $request->vnp_TxnRef; // order_code
        $vnp_Amount = $request->vnp_Amount / 100; // Chia 100 vì VNPay nhân 100
        $vnp_TransactionNo = $request->vnp_TransactionNo;

        try {
            DB::beginTransaction();

            // Tìm đơn hàng theo order_code
            $order = Order::where('order_code', $vnp_TxnRef)->first();

            if (!$order) {
                DB::rollBack();
                Log::error('Order not found for VNPay return', ['order_code' => $vnp_TxnRef]);
                return redirect()->route('orders.checkout')->with('error', 'Không tìm thấy đơn hàng');
            }

            // Tìm payment record
            $payment = Payment::where('order_id', $order->id)
                              ->where('transaction_id', $vnp_TxnRef)
                              ->first();

            if ($vnp_ResponseCode === '00') {
                // Thanh toán thành công - cập nhật trạng thái thành "Đã Thanh Toán"
                $paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();
                
                if (!$paymentStatus) {
                    Log::error('Payment status "Đã Thanh Toán" not found');
                    throw new \Exception('Trạng thái thanh toán không tồn tại');
                }

                if ($payment) {
                    $payment->update([
                        'payment_status_id' => $paymentStatus->id,
                        'paid_at' => now(), // Set thời gian thanh toán
                        'transaction_id' => $vnp_TransactionNo // Cập nhật với transaction ID từ VNPay
                    ]);
                    
                    Log::info('Payment updated successfully', [
                        'payment_id' => $payment->id,
                        'transaction_id' => $vnp_TransactionNo
                    ]);
                }

                // Cập nhật trạng thái thanh toán của đơn hàng
                $order->update([
                    'payment_status_id' => $paymentStatus->id
                ]);
                
                Log::info('Order payment status updated to "Đã Thanh Toán"', [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code
                ]);

                // Xóa giỏ hàng sau khi thanh toán thành công
                Auth::user()->cart()->delete();

                // Gửi email xác nhận
                $this->emailService->sendOrderConfirmation($order);

                // Tạo QR code nếu chưa có
                if (!$order->qr_code) {
                    $this->generateQrCode($order);
                }

                DB::commit();

                Toastr::success('Thanh toán thành công! Đơn hàng của b��n đã được xác nhận.');
                return redirect()->route('orders.show', $order->id);

            } else {
                // Thanh toán thất bại - Hủy đơn hàng
                $cancelledStatus = OrderStatus::where('name', 'Đã hủy')->first();
                $failedPaymentStatus = PaymentStatus::where('name', 'Thất Bại')->first();

                if ($payment) {
                    $payment->update([
                        'payment_status_id' => $failedPaymentStatus->id
                    ]);
                }

                // Cập nhật trạng thái đơn hàng thành "Đã hủy"
                $order->update([
                    'order_status_id' => $cancelledStatus->id,
                    'payment_status_id' => $failedPaymentStatus->id
                ]);

                // Tạo bản ghi hủy đơn hàng
                OrderCancellation::create([
                    'order_id' => $order->id,
                    'reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode,
                    'cancelled_by' => $order->user_id,
                    'cancelled_at' => now(),
                ]);

                DB::commit();

                Toastr::error('Thanh toán thất bại! Đơn hàng đã được hủy tự động.');
                return redirect()->route('orders.checkout')->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing VNPay return', [
                'error' => $e->getMessage(),
                'order_code' => $vnp_TxnRef
            ]);

            Toastr::error('Có lỗi xảy ra trong quá trình xử lý thanh toán.');
            return redirect()->route('orders.checkout')->with('error', 'Có lỗi xảy ra trong quá trình xử lý thanh toán.');
        }
    }
}
