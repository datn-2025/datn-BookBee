<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;
    public $notificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        Log::info('[OrderStatusUpdated] Event được khởi tạo', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => $order->user_id
        ]);
        
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->notificationData = [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'updated_at' => now()->format('d/m/Y H:i')
        ];
        
        Log::info('[OrderStatusUpdated] Notification data prepared', $this->notificationData);
        
        // Lưu thông báo vào database cho khách hàng
        $this->saveNotificationForCustomer();
        
        Log::info('[OrderStatusUpdated] Event constructor completed');
    }

    /**
     * Lưu thông báo vào database cho khách hàng
     */
    private function saveNotificationForCustomer()
    {
        $statusMessages = [
            'Chờ xác nhận' => 'Đơn hàng của bạn đang chờ xác nhận',
            'Đã xác nhận' => 'Đơn hàng của bạn đã được xác nhận',
            'Đang chuẩn bị' => 'Đơn hàng của bạn đang được chuẩn bị',
            'Đang giao' => 'Đơn hàng của bạn đang được giao',
            'Đã giao' => 'Đơn hàng của bạn đã được giao thành công',
            'Đã hủy' => 'Đơn hàng của bạn đã bị hủy'
        ];
        
        $message = $statusMessages[$this->newStatus] ?? "Trạng thái đơn hàng đã thay đổi thành: {$this->newStatus}";
        
        Notification::create([
            'user_id' => $this->order->user_id,
            'type' => 'order_status_updated',
            'type_id' => $this->order->id,
            'title' => 'Cập nhật trạng thái đơn hàng ' . '#' . $this->order->order_code,
            'message' => $message,
            'data' => $this->notificationData
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channelName = 'customer-' . $this->order->user_id;
        $channels = [
            new Channel($channelName), // Channel riêng cho từng khách hàng
        ];
        
        Log::info('[OrderStatusUpdated] Broadcasting on channels', [
            'channels' => [$channelName],
            'user_id' => $this->order->user_id
        ]);
        
        return $channels;
    }

    /**
     * Tên event khi broadcast
     */
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    /**
     * Dữ liệu được broadcast
     */
    public function broadcastWith(): array
    {
        Log::info('[OrderStatusUpdated] Broadcasting with data', $this->notificationData);
        
        return $this->notificationData;
    }
}
