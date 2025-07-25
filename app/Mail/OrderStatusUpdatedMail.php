<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $newStatus;

    public function __construct(Order $order, $newStatus)
    {
        $this->order = $order;
        $this->newStatus = $newStatus;
    }

    public function build()
    {
        return $this->subject('Cập nhật trạng thái đơn hàng')
            ->view('emails.order_status_updated')
            ->with([
                'order' => $this->order,
                'newStatus' => $this->newStatus,
            ]);
    }
}
