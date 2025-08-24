<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class EbookPurchaseConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Order $order,
    ) {}
    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận mua ebook thành công',
        );
    }

    /**
     * Xác định view và data
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.ebook-purchase-confirmation',
            with: [
                'order' => $this->order,
            ],
        );
    }

}
