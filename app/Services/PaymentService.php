<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

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

        // Tự động tạo hóa đơn khi thanh toán thành công
        if ($status === 'paid' || $paymentStatus->name === 'Đã Thanh Toán') {
            try {
                $this->invoiceService->processInvoiceForPaidOrder($payment->order);
                Log::info('Invoice created automatically for paid order', [
                    'order_id' => $payment->order->id,
                    'payment_id' => $payment->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create invoice for paid order', [
                    'order_id' => $payment->order->id,
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);
                // Không throw exception để không ảnh hưởng đến luồng thanh toán
            }
        }

        return $payment;
    }

    public function processPayment(Payment $payment)
    {
        // TODO: Implement payment gateway integration
        // For now, just simulate successful payment
        return $this->updatePaymentStatus($payment, 'paid');
    }

    /**
     * Create payment record for preorder
     */
    public function createPreorderPayment(array $data)
    {
        // For preorders, we'll create a simplified payment record
        // This could be stored in a separate preorder_payments table if needed
        return Payment::create([
            'id' => (string) Str::uuid(),
            'order_id' => null, // No order yet for preorders
            'transaction_id' => $data['transaction_id'],
            'payment_method_id' => $data['payment_method_id'],
            'amount' => $data['amount'],
            'payment_status_id' => $data['payment_status_id'],
            'paid_at' => $data['paid_at'] ?? null,
            'notes' => 'Preorder payment for ID: ' . $data['preorder_id']
        ]);
    }
}
