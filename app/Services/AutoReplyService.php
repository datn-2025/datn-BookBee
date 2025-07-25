<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoReplyService
{
    /**
     * Kiểm tra và gửi tin nhắn tự động nếu admin không hoạt động
     */
    public function checkAndSendAutoReply(Conversation $conversation, Message $customerMessage)
    {
        // Chỉ gửi tự động khi tin nhắn từ customer
        if ($customerMessage->sender_id === $conversation->admin_id) {
            return false;
        }

        // Lấy thông tin admin
        $admin = User::find($conversation->admin_id);
        if (!$admin) {
            Log::warning('Admin not found for conversation', ['conversation_id' => $conversation->id]);
            return false;
        }

        // Kiểm tra admin có đang hoạt động không (trong 15 phút gần đây)
        if ($admin->isActiveWithin(15)) {
            Log::info('Admin is active, no auto-reply needed', [
                'admin_id' => $admin->id,
                'last_seen' => $admin->last_seen
            ]);
            return false;
        }

        // Kiểm tra xem đã gửi tin nhắn tự động chưa (tránh spam)
        $recentAutoReply = Message::where('conversation_id', $conversation->id)
            ->where('sender_id', $admin->id)
            ->where('content', 'LIKE', '%Cảm ơn bạn đã nhắn%')
            ->where('created_at', '>=', now()->subHours(2)) // Chỉ kiểm tra trong 2 giờ gần đây
            ->exists();

        if ($recentAutoReply) {
            Log::info('Auto-reply already sent recently', [
                'conversation_id' => $conversation->id,
                'admin_id' => $admin->id
            ]);
            return false;
        }

        // Tạo tin nhắn tự động
        return $this->sendAutoReplyMessage($conversation, $admin);
    }

    /**
     * Gửi tin nhắn tự động
     */
    private function sendAutoReplyMessage(Conversation $conversation, User $admin)
    {
        try {
            $autoReplyContent = $this->getAutoReplyContent($admin);

            $autoMessage = new Message([
                'sender_id' => $admin->id,
                'content' => $autoReplyContent,
                'type' => 'text',
                'is_auto_reply' => true // Thêm flag để đánh dấu tin nhắn tự động
            ]);

            $conversation->messages()->save($autoMessage);

            // Cập nhật thời gian tin nhắn cuối
            $conversation->update(['last_message_at' => now()]);

            // Broadcast event
            broadcast(new MessageSent($autoMessage));

            Log::info('Auto-reply sent successfully', [
                'conversation_id' => $conversation->id,
                'admin_id' => $admin->id,
                'message_id' => $autoMessage->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send auto-reply', [
                'conversation_id' => $conversation->id,
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Lấy nội dung tin nhắn tự động
     */
    private function getAutoReplyContent(User $admin)
    {
        $messages = [
            "Cảm ơn bạn đã nhắn! Hiện tại admin đang bận, sẽ phản hồi bạn sớm nhất có thể.",
            "Xin chào! Admin hiện tại đang không online, chúng tôi sẽ trả lời bạn trong thời gian sớm nhất.",
            "Cảm ơn tin nhắn của bạn! Do admin đang bận, vui lòng đợi một chút để nhận được phản hồi.",
            "Chào bạn! Admin đang trong giờ nghỉ hoặc bận việc khác, sẽ liên hệ lại với bạn sớm nhất có thể."
        ];

        // Lấy ngẫu nhiên một tin nhắn
        $randomMessage = $messages[array_rand($messages)];

        // Thêm thông tin thời gian nếu cần
        $currentHour = (int) now()->format('H');
        
        if ($currentHour >= 18 || $currentHour < 8) {
            $randomMessage .= " Hiện tại đang ngoài giờ làm việc (8:00 - 18:00), chúng tôi sẽ phản hồi vào ngày làm việc tiếp theo.";
        }

        return $randomMessage;
    }

    /**
     * Đánh dấu admin đang hoạt động
     */
    public static function markAdminAsActive($adminId)
    {
        try {
            $admin = User::find($adminId);
            if ($admin) {
                $admin->update(['last_seen' => now()]);
                Log::debug('Admin activity updated', ['admin_id' => $adminId]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update admin activity', [
                'admin_id' => $adminId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
