<?php

namespace App\Mail;

use App\Models\WalletTransaction;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletWithdrawInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $invoice;
    public $storeSettings;

    /**
     * Create a new message instance.
     */
    public function __construct(WalletTransaction $transaction, Invoice $invoice, Setting $storeSettings = null)
    {
        $this->transaction = $transaction;
        $this->invoice = $invoice;
        $this->storeSettings = $storeSettings;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hóa đơn rút tiền từ ví điện tử - ' . $this->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.wallet-withdraw-invoice',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
