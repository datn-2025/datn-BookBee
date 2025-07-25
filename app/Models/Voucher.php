<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'discount_percent',
        'max_discount',
        'min_order_value',
        'quantity',
        'valid_from',
        'valid_to',
        'status'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'discount_percent' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'quantity' => 'integer',
        'status' => 'string'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function appliedVouchers(): HasMany
    {
        return $this->hasMany(AppliedVoucher::class);
    }

    public function conditions()
    {
        return $this->hasMany(VoucherCondition::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'voucher_conditions', 'voucher_id', 'condition_id')
                    ->where('type', 'category');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'voucher_conditions', 'voucher_id', 'condition_id')
                    ->where('type', 'author');
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'voucher_conditions', 'voucher_id', 'condition_id')
                    ->where('type', 'brand');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'voucher_conditions', 'voucher_id', 'condition_id')
                    ->where('type', 'book');
    }


    public function isValid()
    {
        $now = now()->startOfDay();
        return $this->status === 'active'
            && $now->between($this->valid_from, $this->valid_to)
            && $this->quantity > 0;
    }

    public function getUsedCountAttribute()
    {
        return $this->appliedVouchers()->count();
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->used_count;
    }

    public function canApplyToBook(Book $book)
    {
        // Check if voucher applies to all books
        if ($this->conditions()->where('type', 'all')->exists()) {
            return true;
        }

        return $this->conditions()
            ->where(function ($query) use ($book) {
                $query->where(function ($q) use ($book) {
                    $q->where('type', 'book')
                      ->where('condition_id', $book->id);
                })
                ->orWhere(function ($q) use ($book) {
                    $q->where('type', 'category')
                      ->where('condition_id', $book->category_id);
                })
                ->orWhere(function ($q) use ($book) {
                    $q->where('type', 'author')
                      ->where('condition_id', $book->author_id);
                })
                ->orWhere(function ($q) use ($book) {
                    $q->where('type', 'brand')
                      ->where('condition_id', $book->brand_id);
                });
            })
            ->exists();
    }

    public function calculateDiscount($totalAmount)
    {
        if (!$this->isValid() || $totalAmount < $this->min_order_value) {
            return 0;
        }

        $discount = ($totalAmount * $this->discount_percent) / 100;
        return min($discount, $this->max_discount);
    }
}
