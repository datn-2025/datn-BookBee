<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Tạo hóa đơn cho đơn hàng đã thanh toán thành công
     */
    public function createInvoiceForOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            // Kiểm tra đơn hàng đã có hóa đơn chưa
            if ($order->invoice) {
                Log::info('Invoice already exists for order', [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'existing_invoice_id' => $order->invoice->id
                ]);
                return $order->invoice;
            }

            // Kiểm tra trạng thái thanh toán
            if ($order->paymentStatus->name !== 'Đã Thanh Toán') {
                Log::warning('Cannot create invoice for unpaid order', [
                    'order_id' => $order->id,
                    'payment_status' => $order->paymentStatus->name
                ]);
                throw new \Exception('Không thể tạo hóa đơn cho đơn hàng chưa thanh toán');
            }

            Log::info('Creating invoice for paid order', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'total_amount' => $order->total_amount
            ]);

            // Tạo hóa đơn
            $invoice = Invoice::create([
                'id' => (string) Str::uuid(),
                'order_id' => $order->id,
                'type' => 'sale', // Hóa đơn bán hàng
                'invoice_date' => now(),
                'total_amount' => $order->total_amount
            ]);

            // Tạo chi tiết hóa đơn từ order items
            foreach ($order->orderItems as $orderItem) {
                InvoiceItem::create([
                    'id' => (string) Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'book_id' => $orderItem->book_id,
                    'quantity' => $orderItem->quantity,
                    'price' => $orderItem->price
                ]);
            }

            Log::info('Invoice created successfully', [
                'invoice_id' => $invoice->id,
                'order_id' => $order->id,
                'total_items' => $order->orderItems->count()
            ]);

            return $invoice;
        });
    }

    /**
     * Tạo hóa đơn hoàn tiền
     */
    public function createRefundInvoice(Order $order, $refundRequest = null, $refundAmount = null)
    {
        return DB::transaction(function () use ($order, $refundRequest, $refundAmount) {
            $amount = $refundAmount ?? $order->total_amount;
            // dd($order, $refundRequest, $amount);

            Log::info('Creating refund invoice', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'refund_amount' => $amount
            ]);
            // dd($order->id, $refundRequest?->id, $refundRequest?->refund_method, $refundRequest?->reason);
            // Tạo hóa đơn hoàn tiền
            $invoice = Invoice::create([
                'id' => (string) Str::uuid(),
                'order_id' => $order->id,
                'type' => 'refund', // Hóa đơn hoàn tiền
                'refund_request_id' => $refundRequest?->id,
                'invoice_date' => now(),
                'total_amount' => -$amount, // Số âm để phân biệt với hóa đơn bán hàng
                'refund_amount' => $amount,
                'refund_method' => $refundRequest?->refund_method,
                'refund_reason' => $refundRequest?->reason,
                'refund_processed_at' => now()
            ]);
            // dd($invoice);
            // Tạo chi tiết hóa đơn hoàn tiền
            // dd($refundRequest, $order->total_amount);
            if ($refundRequest && $amount == $order->total_amount) {
                // Hoàn tiền toàn bộ - copy tất cả items
                // dd($order->orderItems);
                foreach ($order->orderItems as $orderItem) {
                    InvoiceItem::create([
                        'id' => (string) Str::uuid(),
                        'invoice_id' => $invoice->id,
                        'book_id' => $orderItem->book_id,
                        'quantity' => -$orderItem->quantity, // Số âm để phân biệt
                        'price' => $orderItem->price
                    ]);
                }
            } else {
                // Hoàn tiền một phần hoặc không có chi tiết cụ thể
                // Tạo một item tổng quát
                if ($order->orderItems->isNotEmpty()) {
                    $firstItem = $order->orderItems->first();
                    InvoiceItem::create([
                        'id' => (string) Str::uuid(),
                        'invoice_id' => $invoice->id,
                        'book_id' => $firstItem->book_id,
                        'quantity' => -1,
                        'price' => $amount
                    ]);
                }
            }

            Log::info('Refund invoice created successfully', [
                'invoice_id' => $invoice->id,
                'order_id' => $order->id,
                'refund_amount' => $amount
            ]);

            return $invoice;
        });
    }

    /**
     * Gửi hóa đơn qua email
     */
    public function sendInvoiceEmail(Order $order)
    {
        try {
            // Load các relationship cần thiết
            $order->load(['user', 'orderItems.book', 'address', 'payments.paymentMethod']);

            // Gửi email hóa đơn
            app(EmailService::class)->sendOrderInvoice($order);

            Log::info('Invoice email sent successfully', [
                'order_id' => $order->id,
                'customer_email' => $order->user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Tự động tạo và gửi hóa đơn khi thanh toán thành công
     */
    public function processInvoiceForPaidOrder(Order $order)
    {
        try {
            // Tạo hóa đơn
            $invoice = $this->createInvoiceForOrder($order);
            
            // Gửi email hóa đơn
            $this->sendInvoiceEmail($order);

            Log::info('Invoice processing completed for paid order', [
                'order_id' => $order->id,
                'invoice_id' => $invoice->id
            ]);

            return $invoice;
        } catch (\Exception $e) {
            Log::error('Failed to process invoice for paid order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
