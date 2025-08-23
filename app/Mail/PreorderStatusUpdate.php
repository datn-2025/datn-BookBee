<?php

namespace App\Mail;

use App\Models\Preorder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreorderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $preorder;
    public $oldStatus;

    public function __construct(Preorder $preorder, $oldStatus)
    {
        $this->preorder = $preorder;
        $this->oldStatus = $oldStatus;
    }

    public function build()
    {
        return $this->subject('Cập nhật trạng thái đơn đặt trước - BookBee')
                    ->view('emails.preorder.status-update')
                    ->with([
                        'preorder' => $this->preorder,
                        'book' => $this->preorder->book,
                        'format' => $this->preorder->bookFormat,
                        'oldStatus' => $this->oldStatus
                    ]);
    }
}
