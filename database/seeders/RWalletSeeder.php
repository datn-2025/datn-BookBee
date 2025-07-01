<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RWalletSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('Vui lòng seed bảng users trước khi seed wallets!');
            return;
        }

        foreach ($users as $user) {
            // Tránh trường hợp seed trùng ví
            if ($user->wallet) {
                continue;
            }

            Wallet::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'balance' => rand(0, 1000000), // Số dư ví ngẫu nhiên từ 0 đến 1 triệu VNĐ
            ]);
        }
    }
}
