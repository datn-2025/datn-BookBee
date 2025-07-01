<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookGift extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_id', 'gift_name', 'gift_description', 'gift_image', 'quantity', 'start_date', 'end_date'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
