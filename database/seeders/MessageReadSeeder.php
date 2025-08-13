<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MessageReadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $messages = Message::all();
        $users = User::whereIn('email', ['admin@bookbee.com', 'user@bookbee.com'])->get();

        if ($messages->isEmpty() || $users->count() < 2) {
            $this->command->error("Thiếu dữ liệu messages hoặc users để seed message_reads!");
            return;
        }

        foreach ($messages as $msg) {
            foreach ($users as $user) {
                MessageRead::updateOrCreate([
                    'message_id' => $msg->id,
                    'user_id' => $user->id,
                ], [
                    'id' => (string) Str::uuid(),
                    'read_at' => Carbon::now()->subSeconds(rand(1, 300)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Đã tạo các lượt đọc tin nhắn (message_reads).');
    }
}
