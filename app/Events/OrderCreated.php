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

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $notificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        Log::info('[OrderCreated] Event được khởi tạo', ['order_id' => $order->id]);
        
        $this->order = $order;
        $this->notificationData = [
            'order_id' => $order->id,
            'user_name' => $order->user->name,
            'total_amount' => number_format($order->total_amount),
            'created_at' => $order->created_at->format('d/m/Y H:i')
        ];
        
        Log::info('[OrderCreated] Notification data prepared', $this->notificationData);
        
        // Lưu thông báo vào database cho admin
        $this->saveNotificationForAdmin();
        
        Log::info('[OrderCreated] Event constructor completed');
    }

    /**
     * Lưu thông báo vào database cho admin
     */
    private function saveNotificationForAdmin()
    {
        // Lưu thông báo vào database cho tất cả admin
        $role_id = Role::where('name', 'Admin')->value('id');
        $adminUsers = User::where('role_id', $role_id)->get();
        
        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'order_created',
                'type_id' => $this->order->id,
                'title' => 'Đơn hàng mới',
                'message' => "Có đơn hàng mới từ khách hàng {$this->order->user->name} với giá trị {$this->order->total_amount}đ",
                'data' => json_encode($this->notificationData)
            ]);
        }
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
        
        Log::info('[OrderCreated] Broadcasting on channels', ['channels' => ['admin-orders']]);
        
        return $channels;
    }

    /**
     * Tên event khi broadcast
     */
    public function broadcastAs(): string
    {
        return 'order.created';
    }

    /**
     * Dữ liệu được broadcast
     */
    public function broadcastWith(): array
    {
        Log::info('[OrderCreated] Broadcasting with data', $this->notificationData);
        
        return $this->notificationData;
    }

}
