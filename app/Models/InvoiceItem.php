<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'book_id',
        'collection_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    public $incrementing = false; 
    protected $keyType = 'string';

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Lấy tên item (sách hoặc combo)
     */
    public function getItemName(): string
    {
        if ($this->collection_id && $this->collection) {
            return $this->collection->name;
        }
        
        if ($this->book_id && $this->book) {
            return $this->book->title;
        }
        
        return 'Sản phẩm không xác định';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
