<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@bookbee.com')->first();
        $customer = User::where('email', 'user@bookbee.com')->first();

        if (!$admin || !$customer) {
            $this->command->error("Vui lòng seed user admin và user thường trước!");
            return;
        }

        // Tạo 1 cuộc trò chuyện giữa admin và user
        $conversation = Conversation::firstOrCreate([
            'customer_id' => $customer->id,
            'admin_id' => $admin->id,
        ], [
            'id' => (string) Str::uuid(),
            'last_message_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Lưu lại conversation_id để dùng trong MessageSeeder
        config(['bookbee.conversation_id' => $conversation->id]);

        $this->command->info('✅ Đã tạo cuộc trò chuyện mẫu giữa admin và user.');
    }
}
