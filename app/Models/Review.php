<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
class Review extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'book_id',
        'collection_id',
        'order_id',
        'rating',
        'comment',
        'status',
        'admin_response'
    ];
    
    // Các trạng thái có thể có của review
    const STATUS_APPROVED = 'approved';  // Đã duyệt, hiển thị công khai
    const STATUS_PENDING = 'pending';    // Chờ duyệt
    const STATUS_HIDDEN = 'hidden';      // Bị ẩn bởi admin
    const STATUS_VISIBLE = 'visible';    // Hiển thị (legacy)

    protected $casts = [
        'rating' => 'integer'
    ];

    protected $dates = ['deleted_at'];

    public $incrementing = false; 
    protected $keyType = 'string';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
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
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Kiểm tra xem đánh giá này có phải cho combo không
     */
    public function isComboReview(): bool
    {
        return !is_null($this->collection_id);
    }

    /**
     * Lấy tên sản phẩm được đánh giá (sách hoặc combo)
     */
    public function getProductNameAttribute(): string
    {
        if ($this->isComboReview()) {
            return $this->collection->name ?? 'Combo không xác định';
        }
        return $this->book->title ?? 'Sách không xác định';
    }

    /**
     * Lấy loại sản phẩm được đánh giá
     */
    public function getProductTypeAttribute(): string
    {
        return $this->isComboReview() ? 'Combo' : 'Sách';
    }
}
