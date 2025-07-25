<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookCollection extends Model
{
    use HasFactory;
    protected $table = 'book_collections';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'book_id',
        'collection_id',
        'order_column',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Tự động gán UUID nếu chưa có
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Quan hệ với Book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Quan hệ với Collection
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
