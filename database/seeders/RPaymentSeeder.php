<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $paymentMethods = PaymentMethod::all();
        $paymentStatuses = PaymentStatus::all();

        if ($orders->isEmpty() || $paymentMethods->isEmpty() || $paymentStatuses->isEmpty()) {
            $this->command->error('Vui lòng seed đầy đủ orders, payment_methods, payment_statuses trước khi seed payments!');
            return;
        }

        foreach ($orders as $order) {
            $paymentMethod = $paymentMethods->random();
            $paymentStatus = $paymentStatuses->random();

            Payment::create([
                'id' => (string) Str::uuid(),
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'transaction_id' => strtoupper('TXN' . Str::random(10)),
                'amount' => $order->total_amount ?? rand(100000, 500000), // giả định có cột total_amount trong order
                'paid_at' => now()->subDays(rand(1, 30)),
                'payment_status_id' => $paymentStatus->id
            ]);
        }
    }
}
