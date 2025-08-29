<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'order_id', 'book_id', 'book_format_id', 'book_gift_id', 'collection_id', 'is_combo', 'item_type', 'quantity', 'price', 'total',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }

    public function bookFormat()
    {
        return $this->belongsTo(BookFormat::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'order_item_attribute_values',
            'order_item_id',
            'attribute_value_id'
        );
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Quan hệ tới quà tặng đi kèm sách
     */
    public function bookGift()
    {
        return $this->belongsTo(BookGift::class, 'book_gift_id');
    }

    protected $casts = [
        'is_combo' => 'boolean',
    ];

    /**
     * Kiểm tra xem item này có phải là combo không
     */
    public function isCombo(): bool
    {
        return $this->is_combo === true;
    }

    /**
     * Lấy tên sản phẩm (sách hoặc combo)
     */
    public function getItemName(): string
    {
        if ($this->isCombo()) {
            return $this->collection->name ?? 'Combo không xác định';
        }
        return $this->book->title ?? 'Sách không xác định';
    }

    /**
     * Lấy giá của item
     */
    public function getItemPrice(): float
    {
        return $this->price;
    }
}
