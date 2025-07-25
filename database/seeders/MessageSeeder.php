<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conversationId = config('bookbee.conversation_id');
        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            $this->command->error("Không tìm thấy cuộc trò chuyện để seed messages!");
            return;
        }

        $messages = [
            [
                'sender_id' => $conversation->customer_id,
                'content' => 'Chào admin, em muốn hỏi về sách mới.',
            ],
            [
                'sender_id' => $conversation->admin_id,
                'content' => 'Chào em, em cần hỗ trợ gì nhỉ?',
            ],
            [
                'sender_id' => $conversation->customer_id,
                'content' => 'Sách "Nhà giả kim" còn hàng không ạ?',
            ],
        ];

        foreach ($messages as $msg) {
            Message::create([
                'id' => (string) Str::uuid(),
                'conversation_id' => $conversation->id,
                'sender_id' => $msg['sender_id'],
                'content' => $msg['content'],
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Đã tạo các tin nhắn mẫu trong cuộc trò chuyện.');
    }
    
}
