<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'brand_id',
        'category_id',
        'status',
        'cover_image',
        'isbn',
        'publication_date',
        'page_count'
    ];

    protected $casts = [
        'publication_date' => 'date',
        'page_count' => 'integer'
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

    protected $keyType = 'string';
    public $incrementing = false;

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_books')
            ->using(AuthorBook::class);
    }

    public function formats(): HasMany
    {
        return $this->hasMany(BookFormat::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BookImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'book_attribute_values', 'book_id', 'attribute_value_id')
            ->withPivot('extra_price')
            ->withTimestamps();
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
    public function summary()
    {
        return $this->hasOne(BookSummary::class);
    }

    public function hasSummary()
    {
        return $this->summary()->exists();
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(BookGift::class);
    }
    
}
