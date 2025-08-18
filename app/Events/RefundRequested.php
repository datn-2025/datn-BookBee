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

class RefundRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $reason;
    public $amount;
    public $target;
    public $reasonStatus;
    public $adminNote;
    public $notificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $reason, float $amount, string $target = null, string $adminNote = null, $reasonStatus = null)
    {
        Log::info('[RefundRequested] Event được khởi tạo', [
            'order_id' => $order->id,
            'reason' => $reason,
            'amount' => $amount
        ]);
        
        $this->order = $order;
        $this->reason = $reason;
        $this->amount = $amount;
        $this->target = $target;
        $this->adminNote = $adminNote;
        $this->reasonStatus = $reasonStatus;
        $this->notificationData = [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'customer_name' => $order->user->name,
            'customer_email' => $order->user->email,
            'refund_reason' => $reason,
            'admin_note' => $adminNote,
            'target' => $target,
            'refund_amount' => number_format($amount, 0, ',', '.'),
            'original_amount' => number_format($order->total_amount, 0, ',', '.'),
            'created_at' => now()->format('d/m/Y H:i')
        ];
        
        Log::info('[RefundRequested] Notification data prepared', $this->notificationData);
        if($this->target === 'customer') {
            if($this->reasonStatus === 'rejected') {
                $this->saveNotificationForCustomerReject();
            } else {
                $this->saveNotificationForCustomer();
            }
        } else {
            $this->saveNotificationForAdmin();
        }
        // Lưu thông báo vào database cho admin
        $this->saveNotificationForAdmin();
        
        Log::info('[RefundRequested] Event constructor completed');
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
                'type' => 'refund_request_admin',
                'type_id' => $this->order->id,
                'title' => 'Yêu cầu hoàn tiền mới',
                'message' => "Khách hàng {$this->order->user->name} yêu cầu hoàn tiền đơn hàng #{$this->order->order_code} với số tiền {$this->notificationData['refund_amount']}đ - Lý do: {$this->reason}",
                'data' => json_encode($this->notificationData)
            ]);
        }
        
        Log::info('[RefundRequested] Notifications saved for admins', ['admin_count' => $adminUsers->count()]);
    }

    private function saveNotificationForCustomer() {
        
        Notification::create([
            'user_id' => $this->order->user_id,
            'type' => 'refund_request',
            'type_id' => $this->order->id,
            'title' => 'Yêu cầu hoàn tiền thành công',
            'message' => "Admin đã duyệt yêu cầu hoàn tiền cho đơn hàng #{$this->order->order_code} - Lý do: {$this->adminNote}",
            'data' => json_encode($this->notificationData)
        ]);

        Log::info('[RefundRequested] Notification saved for customer', ['customer_id' => $this->order->user_id]);
    }
    private function saveNotificationForCustomerReject() {
        
        Notification::create([
            'user_id' => $this->order->user_id,
            'type' => 'refund_request',
            'type_id' => $this->order->id,
            'title' => 'Yêu cầu hoàn tiền bị từ chối',
            'message' => "Admin đã từ chối yêu cầu hoàn tiền cho đơn hàng #{$this->order->order_code} - Lý do: {$this->adminNote}",
            'data' => json_encode($this->notificationData)
        ]);

        Log::info('[RefundRequested] Notification saved for customer', ['customer_id' => $this->order->user_id]);
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
        
        Log::info('[RefundRequested] Broadcasting on channels', ['channels' => ['admin-orders']]);
        
        return $channels;
    }

    /**
     * Tên event khi broadcast
     */
    public function broadcastAs(): string
    {
        return 'refund.requested';
    }

    /**
     * Dữ liệu được broadcast
     */
    public function broadcastWith(): array
    {
        Log::info('[RefundRequested] Broadcasting with data', $this->notificationData);
        
        return $this->notificationData;
    }
}