<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
class BookAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'attribute_value_id',
        'extra_price',
        'stock', // Số lượng tồn kho theo biến thể
        'sku', // Mã SKU cho biến thể
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

    public $incrementing = false; 
    protected $keyType = 'string';

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class);
    }

    /**
     * Kiểm tra xem biến thể có còn hàng không
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Kiểm tra xem biến thể có tồn kho thấp không (dưới 10)
     */
    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock < 10;
    }

    /**
     * Lấy trạng thái tồn kho
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Hết hàng';
        } elseif ($this->stock < 10) {
            return 'Tồn kho thấp';
        } else {
            return 'Còn hàng';
        }
    }

    /**
     * Trừ số lượng tồn kho khi bán
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            return $this->save();
        }
        return false;
    }

    /**
     * Tăng số lượng tồn kho khi nhập hàng
     */
    public function increaseStock(int $quantity): bool
    {
        $this->stock += $quantity;
        return $this->save();
    }
}
