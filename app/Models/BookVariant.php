<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class BookVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'sku',
        'extra_price',
        'stock',
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

    // Quan hệ
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'book_variant_attribute_values')
            ->withTimestamps();
    }

    // Helper tồn kho
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
