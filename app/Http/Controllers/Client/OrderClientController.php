<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderClientController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        
        $query = Order::with(['orderItems.book.images', 'orderStatus', 'voucher'])
            ->where('user_id', Auth::id())
            ->latest();
            
        if ($status) {
            $query->whereHas('orderStatus', function($q) use ($status) {
                $q->where('name', $status);
            });
        }
        
        $orders = $query->paginate(10);
        
        // Lấy số lượng đơn hàng theo từng trạng thái
        $orderCounts = Order::where('user_id', Auth::id())
            ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->selectRaw('order_statuses.name as status, count(*) as count')
            ->groupBy('order_statuses.name')
            ->pluck('count', 'status')
            ->toArray();
            
        $orderCounts['all'] = array_sum($orderCounts);
        
        // Màu sắc cho từng trạng thái
        $orderStatusColors = [
            'Chờ xác nhận' => 'bg-yellow-100 text-yellow-800',
            'Đã xác nhận' => 'bg-blue-100 text-blue-800',
            'Đang chuẩn bị' => 'bg-indigo-100 text-indigo-800',
            'Đang giao hàng' => 'bg-purple-100 text-purple-800',
            'Đã giao hàng' => 'bg-green-100 text-green-800',
            'Đã nhận hàng' => 'bg-green-100 text-green-800',
            'Thành công' => 'bg-green-100 text-green-800',
            'Đã hủy' => 'bg-red-100 text-red-800',
            'Giao thất bại' => 'bg-red-100 text-red-800',
            'Đã hoàn tiền' => 'bg-gray-100 text-gray-800',
        ];
        
        return view('clients.account.orders', compact('orders', 'orderCounts', 'orderStatusColors'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */ 
    public function show($id)
    {
        $order = Order::with([
            'orderItems.book.images',
            'orderStatus',
            'paymentStatus',
            'shippingAddress',
            'billingAddress',
            'voucher'
        ])->where('user_id', Auth::id())
          ->findOrFail($id);

        return view('clients.account.order-detail', compact('order'));
    }

    /**
     * Cập nhật đơn hàng (ví dụ: hủy đơn hàng)
     */
    public function update(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())
                    ->whereIn('order_status_id', function($query) {
                        $query->select('id')
                              ->from('order_statuses')
                              ->whereIn('name', ['Chờ xác nhận', 'Đã xác nhận', 'Đang chuẩn bị']);
                    })
                    ->findOrFail($id);

        // Kiểm tra nếu đơn hàng có thể hủy
        $order->update([
            'order_status_id' => OrderStatus::where('name', 'Đã hủy')->first()->id,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);

        return redirect()->route('account.orders.index')
            ->with('success', 'Đã hủy đơn hàng thành công');
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
}