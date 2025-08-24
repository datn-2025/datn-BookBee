<?php

namespace App\Mail;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BookReleaseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $book;
    public $preorders;

    public function __construct(Book $book, Collection $preorders)
    {
        $this->book = $book;
        $this->preorders = $preorders;
    }

    public function build()
    {
        return $this->subject('Sách đã ra mắt - BookBee')
                    ->view('emails.preorder.book-release')
                    ->with([
                        'book' => $this->book,
                        'preorders' => $this->preorders
                    ]);
    }
}
