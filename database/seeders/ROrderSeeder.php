<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\Voucher;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ROrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'User');
        })->get();

        $orderStatuses = OrderStatus::all();
        $paymentStatuses = PaymentStatus::all();
        $vouchers = Voucher::all();

        if ($users->isEmpty() || $orderStatuses->isEmpty() || $paymentStatuses->isEmpty()) {
            $this->command->error('Vui lòng seed đầy đủ users, order_statuses, payment_statuses trước khi seed orders!');
            return;
        }

        foreach ($users as $user) {
            $addresses = $user->addresses;

            if ($addresses->isEmpty()) {
                $this->command->warn("User {$user->email} chưa có địa chỉ, bỏ qua đơn hàng.");
                continue;
            }

            // Mỗi user có 1 - 3 đơn hàng
            for ($i = 1; $i <= rand(1, 3); $i++) {
                $voucher = $vouchers->random() ?? null;

                Order::create([
                    'id' => (string) Str::uuid(),
                    'order_code' => strtoupper('ORD' . Str::random(8)),
                    'user_id' => $user->id,
                    'address_id' => $addresses->random()->id,
                    'voucher_id' => $voucher ? $voucher->id : null,
                    'total_amount' => rand(100000, 500000),
                    'note' => 'Giao hàng trong giờ hành chính.',
                    'order_status_id' => $orderStatuses->random()->id,
                    'payment_status_id' => $paymentStatuses->random()->id,
                ]);
            }
        }
    }
}
