<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderClientController extends Controller
{
    // Method index đã được thay thế bằng unified()

    /**
     * Hiển thị chi tiết đơn hàng
     */ 
    public function show($id)
    {
        $order = Order::with([
            'orderItems.book.images',
            'orderItems.collection',
            'orderItems.bookFormat',
            'orderItems.attributeValues',
            'orderStatus',
            'paymentStatus',
            'shippingAddress',
            'billingAddress',
            'voucher',
            'childOrders.orderItems.book.images',
            'childOrders.orderItems.collection',
            'childOrders.orderItems.bookFormat',
            'refundRequests' // Thêm để load thông tin yêu cầu hoàn tiền
        ])->where('user_id', Auth::id())
          ->findOrFail($id);

        return view('clients.account.order-details', compact('order'));
    }

    /**
     * Cập nhật đơn hàng (ví dụ: hủy đơn hàng)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:1000'
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        
        // Kiểm tra xem đơn hàng có thể hủy hay không
        if (!\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name)) {
            return redirect()->route('account.orders.index')
                ->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại: ' . $order->orderStatus->name);
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn hàng thành "Đã hủy"
            $order->update([
                'order_status_id' => OrderStatus::where('name', 'Đã hủy')->first()->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason ?? 'Khách hàng hủy đơn hàng'
            ]);

            // Cộng lại tồn kho cho các sản phẩm trong đơn hàng
            $order->orderItems->each(function ($item) {
                if ($item->bookFormat && $item->bookFormat->stock !== null) {
                    Log::info("+ lại tồn kho cho book_format_id {$item->bookFormat->id}, số lượng: {$item->quantity}");
                    $item->bookFormat->increment('stock', $item->quantity);
                }
            });

            // Hoàn tiền vào ví nếu đơn hàng đã thanh toán
            if ($order->paymentStatus->name === 'Đã Thanh Toán') {
                try {
                    $paymentRefundService = app(\App\Services\PaymentRefundService::class);
                    $refundResult = $paymentRefundService->refundToWallet($order, $order->total_amount);
                    
                    if ($refundResult) {
                        Log::info('Order 1 cancellation refund successful', [
                            'order_id' => $order->id,
                            'order_code' => $order->order_code,
                            'amount' => $order->total_amount,
                            'user_id' => $order->user_id
                        ]);
                        
                        $message = 'Đã hủy đơn hàng và hoàn tiền vào ví thành công';
                    } else {
                        Log::warning('Order cancellation refund failed but order still cancelled', [
                            'order_id' => $order->id,
                            'order_code' => $order->order_code
                        ]);
                        
                        $message = 'Đã hủy đơn hàng thành công. Tiền hoàn sẽ được xử lý trong thời gian sớm nhất.';
                    }
                } catch (\Exception $refundError) {
                    Log::error('Order cancellation refund error', [
                        'order_id' => $order->id,
                        'error' => $refundError->getMessage(),
                        'trace' => $refundError->getTraceAsString()
                    ]);
                    
                    $message = 'Đã hủy đơn hàng thành công. Tiền hoàn sẽ được xử lý trong thời gian sớm nhất.';
                }
            } else {
                $message = 'Đã hủy đơn hàng thành công';
            }

            DB::commit();
            return redirect()->route('account.orders.index')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('account.orders.index')
                ->with('error', 'Có lỗi xảy ra khi hủy đơn hàng. Vui lòng thử lại sau.');
        }
    }

    /**
     * Xóa đơn hàng (soft delete)
     */
    public function destroy($id)
    {
        $order = Order::where('user_id', Auth::id())
                    ->whereIn('order_status_id', function($query) {
                        $query->select('id')
                              ->from('order_statuses')
                              ->whereIn('name', ['Đã hủy', 'Giao thất bại']);
                    })
                    ->findOrFail($id);

        $order->delete();

        return redirect()->route('account.orders.index')
            ->with('success', 'Đã xóa đơn hàng thành công');
    }

    /**
     * Hiển thị trang đơn hàng gộp với thiết kế mới
     */
    public function unified(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = Order::with([
            'orderItems.book.images',
            'orderItems.collection', 
            'orderItems.bookFormat',
            'orderStatus', 
            'paymentStatus',
            'paymentMethod',
            'address',
            'voucher',
            'reviews',
            'refundRequests'
        ])->where('user_id', Auth::id())
          ->latest();
            
        // Lọc theo trạng thái nếu không phải 'all'
        if ($status !== 'all') {
            $statusMap = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận', 
                'preparing' => 'Đang chuẩn bị',
                'shipping' => 'Đang giao hàng',
                'delivered' => ['Đã giao', 'Đã giao hàng', 'Thành công'],
                'cancelled' => 'Đã hủy'
            ];
            
            if (isset($statusMap[$status])) {
                $statusNames = is_array($statusMap[$status]) ? $statusMap[$status] : [$statusMap[$status]];
                $query->whereHas('orderStatus', function($q) use ($statusNames) {
                    $q->whereIn('name', $statusNames);
                });
            }
        }
        
        $orders = $query->paginate(10);
        
        // Lấy thông tin cửa hàng
        $storeSettings = Setting::first();
        
        return view('clients.account.orders', compact('orders', 'storeSettings'));
    }

    /**
     * Hủy đơn hàng từ trang unified orders
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:1000'
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        
        // Kiểm tra xem đơn hàng có thể hủy hay không
        if (!\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name)) {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại: ' . $order->orderStatus->name);
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn hàng thành "Đã hủy"
            $order->update([
                'order_status_id' => OrderStatus::where('name', 'Đã hủy')->first()->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason ?? 'Khách hàng hủy đơn hàng'
            ]);

             $order->orderItems->each(function ($item) {
                if ($item->bookFormat && $item->bookFormat->stock !== null) {
                    Log::info("Cộng lại tồn kho cho book_format_id {$item->bookFormat->id}, số lượng: {$item->quantity}");
                    $item->bookFormat->increment('stock', $item->quantity);
                }
            });

            // Hoàn tiền vào ví nếu đơn hàng đã thanh toán
            if ($order->paymentStatus->name === 'Đã Thanh Toán') {
                try {
                    $paymentRefundService = app(\App\Services\PaymentRefundService::class);
                    $refundResult = $paymentRefundService->refundToWallet($order, $order->total_amount);
                    
                    if ($refundResult) {
                        Log::info('Order 2 cancellation refund successful', [
                            'order_id' => $order->id,
                            'order_code' => $order->order_code,
                            'amount' => $order->total_amount,
                            'user_id' => $order->user_id
                        ]);
                        
                        $message = 'Đã hủy đơn hàng và hoàn tiền vào ví thành công';
                    } else {
                        Log::warning('Order cancellation refund failed but order still cancelled', [
                            'order_id' => $order->id,
                            'order_code' => $order->order_code
                        ]);
                        
                        $message = 'Đã hủy đơn hàng thành công. Tiền hoàn sẽ được xử lý trong thời gian sớm nhất.';
                    }
                } catch (\Exception $refundError) {
                    Log::error('Order cancellation refund error', [
                        'order_id' => $order->id,
                        'error' => $refundError->getMessage(),
                        'trace' => $refundError->getTraceAsString()
                    ]);
                    
                    $message = 'Đã hủy đơn hàng thành công. Tiền hoàn sẽ được xử lý trong thời gian sớm nhất.';
                }
            } else {
                $message = 'Đã hủy đơn hàng thành công';
            }

            DB::commit();
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng. Vui lòng thử lại sau.');
        }
    }
}