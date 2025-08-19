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
        
        // Lấy thông tin cài đặt cửa hàng
        $storeSettings = \App\Models\Setting::first();

        Mail::to($order->user->email)
            ->send(new OrderConfirmation($order, $storeSettings));
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
        
        // Lấy thông tin cài đặt cửa hàng
        $storeSettings = \App\Models\Setting::first();

        Mail::to($order->user->email)
            ->send(new OrderInvoice($order, $storeSettings));
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
            'orderItems.book.formats',
            'orderItems.bookFormat',
            'orderItems.collection'
        ]);

        // Chỉ kiểm tra ebook được mua trực tiếp, không bao gồm sách vật lý có ebook kèm theo
        $hasEbooks = $order->orderItems->some(function ($item) {
            // Chỉ gửi email khi mua trực tiếp ebook
            if (!$item->is_combo && $item->bookFormat && $item->bookFormat->format_name === 'Ebook') {
                return true;
            }
            
            return false;
        });
        
        if (!$hasEbooks) {
            Log::info('No ebooks found in order (only direct ebook purchases)', ['order_id' => $order->id]);
            return;
        }

        try {
            Mail::to($order->user->email)
                ->send(new EbookPurchaseConfirmation($order))
                ->subject('Xác nhận mua ebook thành công');
            Log::info('Ebook purchase confirmation email sent', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send ebook purchase confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send ebook download email for child ebook orders
     */
    public function sendEbookDownloadEmail(Order $order)
    {
        // Load relationships cho ebook order
        $order->load([
            'user', 
            'orderItems.book.authors', 
            'orderItems.book.formats',
            'orderItems.bookFormat',
            'orderItems.collection'
        ]);

        // Kiểm tra xem đơn hàng có chứa ebook không
        $hasEbooks = $order->orderItems->some(function ($item) {
            // Chỉ kiểm tra ebook trực tiếp
            if (!$item->is_combo && $item->bookFormat && $item->bookFormat->format_name === 'Ebook') {
                return true;
            }
            return false;
        });
        
        if (!$hasEbooks) {
            Log::info('No ebooks found in child order', ['order_id' => $order->id]);
            return;
        }

        try {
            Mail::to($order->user->email)
                ->send(new EbookPurchaseConfirmation($order));
            Log::info('Ebook download email sent for child order', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send ebook download email for child order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
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
