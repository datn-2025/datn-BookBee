<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmation;
use App\Mail\OrderInvoice;
use App\Mail\EbookPurchaseConfirmation;
use App\Mail\RefundInvoiceMail;

class EmailService
{
    public function sendOrderConfirmation(Order $order)
    {
        // Load relationships cho cả sách lẻ và combo
        $order->load([
            'user', 
            'orderItems.book', 
            'orderItems.bookFormat',
            'orderItems.collection', 
            'address',
            'orderStatus',
            'paymentMethod'
        ]);

        Mail::to($order->user->email)
            ->send(new OrderConfirmation($order));
    }

    public function sendOrderInvoice(Order $order)
    {
        // Load relationships cho cả sách lẻ và combo
        $order->load([
            'user', 
            'orderItems.book', 
            'orderItems.bookFormat',
            'orderItems.collection',
            'address', 
            'payments.paymentMethod',
            'orderStatus',
            'paymentMethod'
        ]);

        Mail::to($order->user->email)
            ->send(new OrderInvoice($order));
    }

    public function sendRefundInvoice(Invoice $invoice, RefundRequest $refundRequest)
    {
        // Load các relationship cần thiết
        $invoice->load([
            'order.user', 
            'order.orderItems.book', 
            'order.address',
            'items.book'
        ]);

        Mail::to($invoice->order->user->email)
            ->queue(new RefundInvoiceMail($invoice, $refundRequest));
    }

    public function sendEbookPurchaseConfirmation(Order $order)
    {
        // Load relationships cho cả sách lẻ và combo
        $order->load([
            'user', 
            'orderItems.book.authors', 
            'orderItems.bookFormat',
            'orderItems.collection'
        ]);

        // Check if order contains any ebooks (chỉ áp dụng cho sách lẻ, không phải combo)
        $hasEbooks = $order->orderItems->some(function ($item) {
            return !$item->is_combo && $item->bookFormat && $item->bookFormat->format_name === 'Ebook';
        });
        
        if (!$hasEbooks) {
            return;
        }

        Mail::to($order->user->email)
            ->send(new EbookPurchaseConfirmation($order));
    }

    /**
     * Send preorder confirmation email
     */
    public function sendPreorderConfirmation($preorder)
    {
        // Load relationships needed for the email
        $preorder->load(['user', 'book.author', 'bookFormat']);

        try {
            Log::info('Starting to send preorder confirmation email', [
                'preorder_id' => $preorder->id,
                'preorder_code' => $preorder->preorder_code,
                'email' => $preorder->email,
                'mail_driver' => config('mail.default')
            ]);

            Mail::raw(
                "Xin chào {$preorder->customer_name},\n\n" .
                "Cảm ơn bạn đã đặt trước sách '{$preorder->book->title}'.\n" .
                "Mã đơn đặt trước: {$preorder->preorder_code}\n" .
                "Số lượng: {$preorder->quantity}\n" .
                "Tổng tiền: " . number_format($preorder->total_amount) . " VND\n\n" .
                "Chúng tôi sẽ liên hệ với bạn khi sách được phát hành.\n\n" .
                "Trân trọng,\n" .
                "Đội ngũ BookStore",
                function ($message) use ($preorder) {
                    $message->to($preorder->email)
                           ->subject('Xác nhận đặt trước sách - ' . $preorder->preorder_code);
                }
            );
            
            Log::info('Preorder confirmation email sent successfully', [
                'preorder_id' => $preorder->id,
                'email' => $preorder->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send preorder confirmation email', [
                'preorder_id' => $preorder->id,
                'email' => $preorder->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw để PreorderController có thể catch
        }
    }
}
