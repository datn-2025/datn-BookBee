<?php

namespace App\Events;

use App\Models\Order;
use App\Models\User;
use App\Models\Notification;
use App\Models\Role;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $reason;
    public $refundAmount;
    public $notificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $reason, float $refundAmount = 0)
    {
        Log::info('[OrderCancelled] Event được khởi tạo', [
            'order_id' => $order->id,
            'reason' => $reason,
            'refund_amount' => $refundAmount
        ]);
        
        $this->order = $order;
        $this->reason = $reason;
        $this->refundAmount = $refundAmount;
        
        $this->notificationData = [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'customer_name' => $order->user->name,
            'customer_email' => $order->user->email,
            'cancellation_reason' => $reason,
            'refund_amount' => number_format($refundAmount, 0, ',', '.'),
            'original_amount' => number_format($order->total_amount, 0, ',', '.'),
            'cancelled_at' => now()->format('d/m/Y H:i')
        ];
        
        Log::info('[OrderCancelled] Notification data prepared', $this->notificationData);
        
        // Lưu thông báo vào database cho admin
        $this->saveNotificationForAdmin();

        $this->saveNotificationForCustomer();
        
        Log::info('[OrderCancelled] Event constructor completed');
    }

    /**
     * Lưu thông báo vào database cho admin
     */
    private function saveNotificationForAdmin()
    {
        // Lưu thông báo vào database cho tất cả admin
        $role_id = Role::where('name', 'Admin')->value('id');
        $adminUsers = User::where('role_id', $role_id)->get();
        
        $refundMessage = $this->refundAmount > 0 
            ? " và hoàn lại {$this->notificationData['refund_amount']}đ"
            : '';
        
        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'order_cancelled',
                'type_id' => $this->order->id,
                'title' => 'Đơn hàng bị hủy',
                'message' => "Đơn hàng #{$this->order->order_code} của khách hàng {$this->order->user->name} đã bị hủy{$refundMessage} - Lý do: {$this->reason}",
                'data' => json_encode($this->notificationData)
            ]);
        }
        
        Log::info('[OrderCancelled] Notifications saved for admins', ['admin_count' => $adminUsers->count()]);
    }

    private function saveNotificationForCustomer(){

        Notification::create([
            'user_id' => $this->order->user_id,
            'type' => 'order_cancelled',
            'type_id' => $this->order->id,
            'title' => 'Hủy đơn hàng thành công',
            'message' => "Bạn đã hủy đơn hàng #{$this->order->order_code}, tiền đã được hoàn vào ví.",
            'data' => json_encode($this->notificationData)
        ]);

        Log::info('[OrderCancelled] Notification saved for customer', ['customer_id' => $this->order->user_id]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('admin-orders'), // Channel công khai cho admin
        ];
        
        Log::info('[OrderCancelled] Broadcasting on channels', ['channels' => ['admin-orders']]);
        
        return $channels;
    }

    /**
     * Tên event khi broadcast
     */
    public function broadcastAs(): string
    {
        return 'order.cancelled';
    }

    /**
     * Dữ liệu được broadcast
     */
    public function broadcastWith(): array
    {
        Log::info('[OrderCancelled] Broadcasting with data', $this->notificationData);
        
        return $this->notificationData;
    }
}