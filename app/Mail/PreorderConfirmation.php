<?php

namespace App\Mail;

use App\Models\Preorder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreorderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $preorder;

    public function __construct(Preorder $preorder)
    {
        $this->preorder = $preorder;
    }

    public function build()
    {
        return $this->subject('Xác nhận đặt trước sách - BookBee')
                    ->view('emails.preorder.confirmation')
                    ->with([
                        'preorder' => $this->preorder,
                        'book' => $this->preorder->book,
                        'format' => $this->preorder->bookFormat
                    ]);
    }
}
