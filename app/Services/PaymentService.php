<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentStatus;
use Illuminate\Support\Str;

class PaymentService
{
    public function createPayment(array $data)
    {
        return Payment::create([
            'id' => (string) Str::uuid(),
            'order_id' => $data['order_id'],
            'transaction_id' => $data['transaction_id'],
            'payment_method_id' => $data['payment_method_id'],
            'amount' => $data['amount'],
            // 'paid_at' => now(),
            // 'payment_status_id' => PaymentStatus::where('name', 'Chờ Xử Lý')->first()->id
        ]);
    }

    public function updatePaymentStatus(Payment $payment, string $status)
    {
        $paymentStatus = PaymentStatus::where('name', $status)->first();

        if (!$paymentStatus) {
            throw new \Exception('Trạng thái thanh toán không hợp lệ');
        }

        $payment->update([
            'payment_status_id' => $paymentStatus->id,
            'paid_at' => $status === 'paid' ? now() : null
        ]);

        // Cập nhật trạng thái thanh toán của đơn hàng
        $payment->order->update([
            'payment_status_id' => $paymentStatus->id
        ]);

        return $payment;
    }

    public function processPayment(Payment $payment)
    {
        // TODO: Implement payment gateway integration
        // For now, just simulate successful payment
        return $this->updatePaymentStatus($payment, 'paid');
    }
}
