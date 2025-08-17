<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Setting;
use App\Models\BookAttributeValue;
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
            'orderItems.book.gifts',
            'orderItems.collection',
            'orderItems.bookFormat',
            'orderItems.attributeValues.attribute',
            'orderStatus',
            'paymentStatus',
            'shippingAddress',
            'billingAddress',
            'address', // Thêm address relationship
            'paymentMethod', // Thêm payment method relationship
            'voucher',
            'parentOrder', // Thêm parent order relationship
            'parentOrder.voucher', // Thêm voucher của parent order
            'childOrders.orderItems.book.gifts',
            'childOrders.orderItems.collection',
            'childOrders.orderItems.bookFormat',
            'childOrders.orderItems.attributeValues.attribute',
            'childOrders.orderStatus',
            'childOrders.paymentStatus',
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
                
                // ✨ THÊM MỚI: Cộng lại stock thuộc tính sản phẩm
                $this->increaseAttributeStock($item);
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
     * Cộng lại stock thuộc tính sản phẩm khi hủy đơn hàng
     */
    private function increaseAttributeStock($orderItem)
    {
        // Lấy thuộc tính từ OrderItemAttributeValue
        $orderItemAttributes = $orderItem->orderItemAttributeValues;
        
        if ($orderItemAttributes && $orderItemAttributes->count() > 0) {
            foreach ($orderItemAttributes as $orderItemAttribute) {
                $bookAttributeValue = BookAttributeValue::where('book_id', $orderItem->book_id)
                    ->where('attribute_value_id', $orderItemAttribute->attribute_value_id)
                    ->first();
                
                if ($bookAttributeValue) {
                    $bookAttributeValue->increment('stock', $orderItem->quantity);
                    
                    Log::info('Increased attribute stock on order cancellation:', [
                        'book_id' => $orderItem->book_id,
                        'attribute_value_id' => $orderItemAttribute->attribute_value_id,
                        'quantity_increased' => $orderItem->quantity,
                        'new_stock' => $bookAttributeValue->fresh()->stock
                    ]);
                } else {
                    Log::warning('BookAttributeValue not found for stock increase:', [
                        'book_id' => $orderItem->book_id,
                        'attribute_value_id' => $orderItemAttribute->attribute_value_id
                    ]);
                }
            }
        }
    }

    /**
     * Hiển thị trang đơn hàng gộp với thiết kế mới
     */
    public function unified(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = Order::with([
            'orderItems.book.gifts',
            'orderItems.collection', 
            'orderItems.bookFormat',
            'orderItems.attributeValues.attribute',
            'orderStatus', 
            'paymentStatus',
            'paymentMethod',
            'address',
            'voucher',
            'reviews',
            'refundRequests',
            'childOrders.orderItems.book.gifts',
            'childOrders.orderItems.collection',
            'childOrders.orderItems.bookFormat',
            'childOrders.orderItems.attributeValues.attribute',
            'childOrders.orderStatus',
            'childOrders.paymentStatus',
            'childOrders.refundRequests'
        ])->where('user_id', Auth::id())
          ->whereNull('parent_order_id') // Chỉ lấy đơn hàng cha hoặc đơn hàng đơn lẻ
          ->latest();
            
        // Lọc theo trạng thái nếu không phải 'all'
        if ($status !== 'all') {
            $statusMap = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận', 
                'preparing' => 'Đang chuẩn bị',
                'shipping' => ['Đang giao hàng', 'Đã giao thành công'],
                'delivered' => ['Đã giao', 'Đã giao hàng', 'Đã nhận hàng', 'Thành công'],
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
                
                // ✨ THÊM MỚI: Cộng lại stock thuộc tính sản phẩm
                $this->increaseAttributeStock($item);
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

    /**
     * Xác nhận đã nhận hàng - chuyển trạng thái từ "Đã giao thành công" thành "Thành công"
     */
    public function confirmReceived($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            // Kiểm tra quyền sở hữu đơn hàng
            if ($order->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
            }
            
            // Kiểm tra trạng thái đơn hàng phải là "Đã giao thành công"
            if ($order->orderStatus->name !== 'Đã giao thành công') {
                return redirect()->back()->with('error', 'Chỉ có thể xác nhận nhận hàng cho đơn hàng đã được giao thành công.');
            }
            
            DB::beginTransaction();
            
            // Tìm trạng thái "Thành công"
            $successStatus = OrderStatus::where('name', 'Thành công')->first();
            if (!$successStatus) {
                throw new \Exception('Không tìm thấy trạng thái "Thành công"');
            }
            
            // Cập nhật trạng thái đơn hàng
            $order->update([
                'order_status_id' => $successStatus->id,
                'delivered_at' => now() // Cập nhật thời gian hoàn thành
            ]);
            
            // Ghi log
            Log::info('Order confirmed as received by customer', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'user_id' => auth()->id()
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Đã xác nhận nhận hàng thành công. Cảm ơn bạn đã mua sắm tại BookBee!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming order received', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xác nhận nhận hàng. Vui lòng thử lại sau.');
        }
    }
}