<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChatTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy role IDs
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();
        
        if (!$adminRole || !$userRole) {
            $this->command->error('❌ Không tìm thấy roles! Hãy chạy RRoleSeeder trước.');
            return;
        }

        // Tạo admin và customer test
        $admin = User::firstOrCreate(
            ['email' => 'admin@bookbee.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin BookBee',
                'email' => 'admin@bookbee.com',
                'password' => bcrypt('password'),
                'phone' => '0123456789',
                'status' => 'Hoạt Động',
                'role_id' => $adminRole->id,
            ]
        );

        $customer1 = User::firstOrCreate(
            ['email' => 'customer1@test.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Nguyễn Văn A',
                'email' => 'customer1@test.com',
                'password' => bcrypt('password'),
                'phone' => '0987654321',
                'status' => 'Hoạt Động',
                'role_id' => $userRole->id,
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@test.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Trần Thị B',
                'email' => 'customer2@test.com',
                'password' => bcrypt('password'),
                'phone' => '0912345678',
                'status' => 'Hoạt Động',
                'role_id' => $userRole->id,
            ]
        );

        // Tạo cuộc trò chuyện 1
        $conversation1 = Conversation::firstOrCreate(
            [
                'customer_id' => $customer1->id,
                'admin_id' => $admin->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'last_message_at' => now()->subMinutes(5),
            ]
        );

        // Tạo cuộc trò chuyện 2
        $conversation2 = Conversation::firstOrCreate(
            [
                'customer_id' => $customer2->id,
                'admin_id' => $admin->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'last_message_at' => now()->subMinutes(10),
            ]
        );

        // Tạo tin nhắn cho conversation 1
        $messages1 = [
            [
                'sender_id' => $customer1->id,
                'content' => 'Chào admin, em muốn hỏi về sách mới nhất.',
                'created_at' => now()->subMinutes(15),
            ],
            [
                'sender_id' => $admin->id,
                'content' => 'Chào em! Admin rất vui được hỗ trợ em. Em muốn tìm hiểu về loại sách nào?',
                'created_at' => now()->subMinutes(14),
            ],
            [
                'sender_id' => $customer1->id,
                'content' => 'Em quan tâm đến sách về lập trình và công nghệ ạ.',
                'created_at' => now()->subMinutes(13),
            ],
            [
                'sender_id' => $admin->id,
                'content' => 'Tuyệt vời! Chúng mình có rất nhiều sách về lập trình hay. Em có muốn xem danh sách bestseller không?',
                'created_at' => now()->subMinutes(12),
            ],
            [
                'sender_id' => $customer1->id,
                'content' => 'Có ạ, em muốn xem. Và cho em hỏi có sách về AI và Machine Learning không ạ?',
                'created_at' => now()->subMinutes(5),
            ],
        ];

        foreach ($messages1 as $messageData) {
            Message::create([
                'id' => (string) Str::uuid(),
                'conversation_id' => $conversation1->id,
                'sender_id' => $messageData['sender_id'],
                'content' => $messageData['content'],
                'type' => 'text',
                'created_at' => $messageData['created_at'],
                'updated_at' => $messageData['created_at'],
            ]);
        }

        // Tạo tin nhắn cho conversation 2
        $messages2 = [
            [
                'sender_id' => $customer2->id,
                'content' => 'Xin chào, em muốn hỏi về tình trạng đơn hàng.',
                'created_at' => now()->subMinutes(20),
            ],
            [
                'sender_id' => $admin->id,
                'content' => 'Chào em! Em cho admin biết mã đơn hàng được không?',
                'created_at' => now()->subMinutes(19),
            ],
            [
                'sender_id' => $customer2->id,
                'content' => 'Mã đơn hàng của em là BB12345678 ạ.',
                'created_at' => now()->subMinutes(18),
            ],
            [
                'sender_id' => $admin->id,
                'content' => 'Admin đã kiểm tra rồi. Đơn hàng của em đang được chuẩn bị và sẽ giao trong 2-3 ngày tới.',
                'created_at' => now()->subMinutes(17),
            ],
            [
                'sender_id' => $customer2->id,
                'content' => 'Cảm ơn admin nhiều ạ! 😊',
                'created_at' => now()->subMinutes(10),
            ],
        ];

        foreach ($messages2 as $messageData) {
            Message::create([
                'id' => (string) Str::uuid(),
                'conversation_id' => $conversation2->id,
                'sender_id' => $messageData['sender_id'],
                'content' => $messageData['content'],
                'type' => 'text',
                'created_at' => $messageData['created_at'],
                'updated_at' => $messageData['created_at'],
            ]);
        }

        // Tạo MessageRead cho một số tin nhắn
        $allMessages = Message::whereIn('conversation_id', [$conversation1->id, $conversation2->id])->get();
        
        foreach ($allMessages as $message) {
            // Admin đọc tất cả tin nhắn từ customer
            if ($message->sender_id !== $admin->id) {
                MessageRead::firstOrCreate([
                    'message_id' => $message->id,
                    'user_id' => $admin->id,
                ], [
                    'id' => (string) Str::uuid(),
                    'read_at' => $message->created_at->addMinutes(1),
                ]);
            }
            
            // Customer đọc một số tin nhắn từ admin (không phải tất cả để test unread)
            if ($message->sender_id === $admin->id && $message->created_at < now()->subMinutes(15)) {
                $customerId = $message->conversation->customer_id;
                MessageRead::firstOrCreate([
                    'message_id' => $message->id,
                    'user_id' => $customerId,
                ], [
                    'id' => (string) Str::uuid(),
                    'read_at' => $message->created_at->addMinutes(2),
                ]);
            }
        }

        // Cập nhật last_message_at cho conversations
        $conversation1->update(['last_message_at' => now()->subMinutes(5)]);
        $conversation2->update(['last_message_at' => now()->subMinutes(10)]);

        $this->command->info('✅ Đã tạo dữ liệu test cho chat:');
        $this->command->info('- 1 Admin: admin@bookbee.com');
        $this->command->info('- 2 Customers: customer1@test.com, customer2@test.com');
        $this->command->info('- 2 Conversations với tin nhắn mẫu');
        $this->command->info('- MessageRead records để test read status');
        $this->command->info('Password cho tất cả accounts: password');
    }
}
