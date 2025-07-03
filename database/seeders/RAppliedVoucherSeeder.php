<?php

namespace Database\Seeders;

use App\Models\AppliedVoucher;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RAppliedVoucherSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $vouchers = Voucher::all();
        $orders = Order::all();

        if ($users->isEmpty() || $vouchers->isEmpty() || $orders->isEmpty()) {
            $this->command->error('Vui lòng seed bảng users, vouchers và orders trước!');
            return;
        }

        // Áp dụng mỗi voucher cho một vài đơn hàng ngẫu nhiên
        foreach ($orders as $order) {
            $voucher = $vouchers->random();
            $user = $users->random();

            AppliedVoucher::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'voucher_id' => $voucher->id,
                'order_id' => $order->id,
                'used_at' => now()->subDays(rand(1, 30)),
                'usage_count' => 1
            ]);
        }
    }
}
