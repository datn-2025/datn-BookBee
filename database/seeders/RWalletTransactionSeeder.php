<?php

namespace Database\Seeders;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RWalletTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $wallets = Wallet::all();
        $orders = Order::all();

        if ($wallets->isEmpty()) {
            $this->command->error('Vui lòng seed bảng wallets trước khi seed wallet_transactions!');
            return;
        }

        foreach ($wallets as $wallet) {
            // Mỗi ví có 3 - 7 giao dịch
            $transactionCount = rand(3, 7);

            for ($i = 0; $i < $transactionCount; $i++) {
                $type = fake()->randomElement(['deposit', 'withdraw', 'refund', 'payment']);
                $amount = match ($type) {
                    'deposit', 'refund' => rand(50000, 500000),    // Nạp hoặc hoàn tiền
                    'withdraw', 'payment' => rand(10000, 300000),  // Rút hoặc thanh toán
                    default => 0
                };

                WalletTransaction::create([
                    'id' => (string) Str::uuid(),
                    'wallet_id' => $wallet->id,
                    'amount' => $amount,
                    'type' => $type,
                    'description' => $this->generateDescription($type),
                    'related_order_id' => $type === 'payment' && $orders->isNotEmpty() ? $orders->random()->id : null,
                ]);
            }
        }
    }

    private function generateDescription($type): string
    {
        return match ($type) {
            'deposit' => 'Nạp tiền vào ví',
            'withdraw' => 'Rút tiền khỏi ví',
            'refund' => 'Hoàn tiền từ hệ thống',
            'payment' => 'Thanh toán đơn hàng',
            default => 'Giao dịch ví',
        };
    }
}
