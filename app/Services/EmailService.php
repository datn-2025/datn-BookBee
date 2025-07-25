<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmation;
use App\Mail\OrderInvoice;
use App\Mail\EbookPurchaseConfirmation;

class EmailService
{
    public function sendOrderConfirmation(Order $order)
    {
        $order->load(['user', 'orderItems.book', 'address']);

        Mail::to($order->user->email)
            ->send(new OrderConfirmation($order));
    }

    public function sendOrderInvoice(Order $order)
    {
        $order->load(['user', 'orderItems.book', 'address', 'payments.paymentMethod']);

        Mail::to($order->user->email)
            ->send(new OrderInvoice($order));
    }

    public function sendEbookPurchaseConfirmation(Order $order)
    {
        // Load relationships needed for the email
        $order->load(['user', 'orderItems.book.author', 'orderItems.bookFormat']);

        // Check if order contains any ebooks
        $hasEbooks = $order->orderItems->some(function ($item) {
            return $item->bookFormat && $item->bookFormat->format_name === 'Ebook';
        });
        // dd($hasEbooks);
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
