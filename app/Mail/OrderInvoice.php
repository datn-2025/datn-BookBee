<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $storeSettings;

    public function __construct(Order $order, $storeSettings = null)
    {
        $this->order = $order;
        $this->storeSettings = $storeSettings;
    }

    public function build()
    {
        return $this->subject('Hóa đơn đơn hàng #' . $this->order->order_code)
            ->view('emails.orders.invoice')
            ->with([
                'order' => $this->order,
                'storeSettings' => $this->storeSettings
            ]);
    }
}
