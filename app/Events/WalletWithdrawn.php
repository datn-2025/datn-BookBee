<?php

namespace App\Events;

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

class WalletWithdrawn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $amount;
    public $target;
    public $transactionId;
    public $notificationData;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, float $amount, string $transactionId = null, $target)
    {
        Log::info('[WalletWithdrawn] Event được khởi tạo', [
            'user_id' => $user->id,
            'amount' => $amount,
            'transaction_id' => $transactionId
        ]);
        
        $this->user = $user;
        $this->amount = $amount;
        $this->target = $target;
        $this->transactionId = $transactionId;
        $this->notificationData = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'amount' => number_format($amount, 0, ',', '.') . 'đ',
            'transaction_id' => $transactionId,
            'withdrawn_at' => now()->format('d/m/Y H:i')
        ];
        
        Log::info('[WalletWithdrawn] Notification data prepared', $this->notificationData);
        
        if($this->target === 'customer') {
            $this->saveNotificationForCustomer();
        }
        else{
            $this->saveNotificationForAdmin();
        }
        
        Log::info('[WalletWithdrawn] Event constructor completed');
    }

    /**
     * Lưu thông báo vào database cho khách hàng
     */
    private function saveNotificationForCustomer()
    {
        $message = "Bạn đã rút thành công " . number_format($this->amount, 0, ',', '.') . "đ từ ví";
        
        Notification::create([
            'user_id' => $this->user->id,
            'type' => 'wallet_withdrawn',
            'title' => 'Rút tiền thành công',
            'message' => $message,
            'data' => $this->notificationData
        ]);
    }

    /**
     * Lưu thông báo vào database cho admin
     */
    private function saveNotificationForAdmin()
    {
        $message = "Khách hàng {$this->user->name} đã rút " . number_format($this->amount, 0, ',', '.') . "đ từ ví";
        
        // Lấy tất cả admin (tạm thời lấy user đầu tiên để test)
        // $adminUsers = User::take(1)->get();
        $role = Role::where('name', 'Admin')->first();
        $adminUsers = User::where('role_id', $role->id)->get();

        // Tạo thông báo cho từng admin
        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'wallet_withdraw_admin',
                'title' => 'Yêu cầu rút tiền ví',
                'message' => $message,
                'data' => array_merge($this->notificationData, [
                    'customer_name' => $this->user->name,
                    'customer_email' => $this->user->email
                ])
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
        $customerChannelName = 'customer-' . $this->user->id;
        $adminChannelName = 'admin-wallets';
        
        $channels = [
            new Channel($customerChannelName), // Channel riêng cho từng khách hàng
            new Channel($adminChannelName), // Channel cho admin
        ];
        
        Log::info('[WalletWithdrawn] Broadcasting on channels', [
            'channels' => [$customerChannelName, $adminChannelName],
            'user_id' => $this->user->id
        ]);
        
        return $channels;
    }

    /**
     * Tên event khi broadcast
     */
    public function broadcastAs(): string
    {
        return 'wallet.withdrawn';
    }

    /**
     * Dữ liệu được broadcast
     */
    public function broadcastWith(): array
    {
        Log::info('[WalletWithdrawn] Broadcasting with data', $this->notificationData);
        
        return $this->notificationData;
    }
}