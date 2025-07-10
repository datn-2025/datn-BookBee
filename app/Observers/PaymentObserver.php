<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;

class PaymentObserver
{
    public function creating(Payment $payment)
    {
        // dd('Observer chạy', $payment->toArray());
        if (!$payment->payment_status_id) {
            $paymentMethod = PaymentMethod::find($payment->payment_method_id);

            if ($paymentMethod) {
                $methodName = strtolower($paymentMethod->name);

                if ($methodName === 'thanh toán khi nhận hàng') {
                    $statusName = 'Chờ Xử Lý';
                } else {
                    $statusName = 'Đã Thanh Toán';
                    $payment->paid_at = now();
                }

                $status = PaymentStatus::where('name', $statusName)->first();
                if ($status) {
                    $payment->payment_status_id = $status->id;
                }
            }
        }
    }
}
