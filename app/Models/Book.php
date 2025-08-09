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
        'page_count',
        'release_date',
        'pre_order',
        'pre_order_price',
        'stock_preorder_limit',
        'preorder_count',
        'preorder_description'
    ];

    protected $casts = [
        'publication_date' => 'date',
        'release_date' => 'date',
        'page_count' => 'integer',
        'pre_order' => 'boolean',
        'pre_order_price' => 'decimal:2',
        'stock_preorder_limit' => 'integer',
        'preorder_count' => 'integer'
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
            ->withPivot('extra_price', 'stock', 'sku') // Thêm stock và sku vào pivot
            ->withTimestamps();
    }

    /**
     * Relationship để lấy BookAttributeValue records với nested relationships
     */
    public function bookAttributeValues(): HasMany
    {
        return $this->hasMany(BookAttributeValue::class);
    }

    /**
     * Lấy tổng số lượng tồn kho của tất cả biến thể
     */
    public function getTotalVariantStockAttribute(): int
    {
        return $this->attributeValues()->sum('book_attribute_values.stock');
    }

    /**
     * Kiểm tra xem sách có biến thể nào còn hàng không
     */
    public function hasVariantInStock(): bool
    {
        return $this->attributeValues()->where('book_attribute_values.stock', '>', 0)->exists();
    }

    /**
     * Lấy các biến thể có tồn kho thấp
     */
    public function getLowStockVariants()
    {
        return $this->attributeValues()
            ->wherePivot('stock', '>', 0)
            ->wherePivot('stock', '<', 10)
            ->get();
    }

    /**
     * Lấy các biến thể hết hàng
     */
    public function getOutOfStockVariants()
    {
        return $this->attributeValues()
            ->wherePivot('stock', '<=', 0)
            ->get();
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
        return $this->reviews()->whereIn('status', ['approved', 'visible'])->avg('rating') ?? 0;
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

    /**
     * Quan hệ với Preorders
     */
    public function preorders(): HasMany
    {
        return $this->hasMany(Preorder::class);
    }

    /**
     * Kiểm tra sách có thể đặt trước không
     */
    public function canPreorder(): bool
    {
        return $this->pre_order && 
               $this->release_date && 
               $this->release_date->isFuture() &&
               $this->hasPreorderSlotsAvailable();
    }

    /**
     * Kiểm tra còn slot đặt trước không
     */
    public function hasPreorderSlotsAvailable(): bool
    {
        if (!$this->stock_preorder_limit) {
            return true; // Không giới hạn
        }
        
        return $this->getPreorderedQuantity() < $this->stock_preorder_limit;
    }

    /**
     * Lấy số slot đặt trước còn lại
     */
    public function getRemainingPreorderSlots(): int
    {
        if (!$this->stock_preorder_limit) {
            return 999999; // Không giới hạn
        }
        
        return max(0, $this->stock_preorder_limit - $this->getPreorderedQuantity());
    }

    /**
     * Kiểm tra sách có đang trong trạng thái đặt trước không
     */
    public function isPreOrder(): bool
    {
        return $this->pre_order && 
               $this->release_date && 
               $this->release_date->isFuture();
    }

    /**
     * Kiểm tra sách đã được phát hành chưa
     */
    public function isReleased(): bool
    {
        return $this->release_date && $this->release_date->isPast();
    }

    /**
     * Lấy giá hiển thị (ưu tiên giá đặt trước nếu có)
     */
    public function getDisplayPrice(): float
    {
        if ($this->isPreOrder() && $this->pre_order_price) {
            return $this->pre_order_price;
        }
        
        // Lấy giá từ format đầu tiên
        $firstFormat = $this->formats()->first();
        return $firstFormat ? $firstFormat->price : 0;
    }

    /**
     * Lấy giá preorder (ưu tiên pre_order_price, nếu không có thì lấy từ format)
     */
    public function getPreorderPrice($format = null): float
    {
        if ($this->pre_order_price) {
            return $this->pre_order_price;
        }

        if ($format && $format->price) {
            return $format->price;
        }

        // Lấy giá từ format đầu tiên
        $firstFormat = $this->formats()->first();
        return $firstFormat ? $firstFormat->price : 0;
    }

    /**
     * Lấy số lượng đã đặt trước
     */
    public function getPreorderedQuantity(): int
    {
        return $this->preorders()
            ->whereIn('status', ['pending', 'confirmed', 'processing'])
            ->sum('quantity');
    }

    /**
     * Scope: Lấy các sách có thể đặt trước
     */
    public function scopePreorderable($query)
    {
        return $query->where('pre_order', true)
                     ->where('release_date', '>', now());
    }

    /**
     * Scope: Lấy các sách sắp phát hành
     */
    public function scopeUpcomingRelease($query)
    {
        return $query->where('release_date', '>', now())
                     ->orderBy('release_date', 'asc');
    }
    
}
