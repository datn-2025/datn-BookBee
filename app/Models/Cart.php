<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'book_id',
        'book_format_id',
        'variant_id', // Thêm variant_id cho hệ thống biến thể mới
        'collection_id',
        'is_combo',
        'is_preorder',
        'attribute_value_id',
        'quantity',
        'attribute_value_ids',
        'price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'attribute_value_ids' => 'array',
        'is_combo' => 'boolean',
        'is_preorder' => 'boolean'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function bookFormat(): BelongsTo
    {
        return $this->belongsTo(BookFormat::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(BookVariant::class, 'variant_id');
    }

    /**
     * Get the attribute values for this cart item
     */
    public function getAttributeValuesAttribute()
    {
        if (!$this->attribute_value_ids || empty($this->attribute_value_ids)) {
            return collect();
        }
        
        return \App\Models\AttributeValue::whereIn('id', $this->attribute_value_ids)
            ->with('attribute')
            ->get();
    }

    /**
     * Check if this cart item is a combo
     */
    public function isCombo(): bool
    {
        return $this->is_combo === true;
    }
}
