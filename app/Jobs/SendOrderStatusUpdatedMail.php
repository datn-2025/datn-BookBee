<?php

namespace App\Jobs;

use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusUpdatedMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $newStatus;

    public function __construct(Order $order, $newStatus)
    {
        $this->order = $order;
        $this->newStatus = $newStatus;
    }

    public function handle()
    {
        Mail::to($this->order->user->email)->send(new OrderStatusUpdatedMail($this->order, $this->newStatus));
    }
}
