<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $refundRequest;

    public function __construct(Invoice $invoice, RefundRequest $refundRequest)
    {
        $this->invoice = $invoice;
        $this->refundRequest = $refundRequest;
    }

    public function build()
    {
        return $this->subject('Hóa đơn hoàn tiền - Đơn hàng #' . $this->invoice->order->order_code)
            ->view('emails.refund.invoice')
            ->with([
                'invoice' => $this->invoice,
                'refundRequest' => $this->refundRequest,
                'order' => $this->invoice->order
            ]);
    }
}
