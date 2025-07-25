<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Mail;
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
}
