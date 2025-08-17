<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Preorder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'book_format_id',
        'customer_name',
        'email',
        'phone',
        'address',
        'province_code',
        'province_name',
        'district_code',
        'district_name',
        'ward_code',
        'ward_name',
        'quantity',
        'unit_price',
        'total_amount',
        'selected_attributes',
        'status',
        'notes',
        'expected_delivery_date',
        'confirmed_at',
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'selected_attributes' => 'array',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expected_delivery_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
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

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với Book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Quan hệ với BookFormat
     */
    public function bookFormat(): BelongsTo
    {
        return $this->belongsTo(BookFormat::class);
    }


    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Accessors
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã gửi hàng',
            'delivered' => 'Đã chuyển thành đơn hàng',
            'cancelled' => 'Đã hủy'
        ];

        // Nếu status đã là tiếng Việt, trả về luôn
        if (in_array($this->status, $statuses)) {
            return $this->status;
        }

        return $statuses[$this->status] ?? $this->status ?? 'Không xác định';
    }

    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->ward_name . ', ' . $this->district_name . ', ' . $this->province_name;
    }

    /**
     * Methods
     */
    public function isEbook()
    {
        return $this->bookFormat && strtolower($this->bookFormat->format_name) === 'ebook';
    }

    public function isPhysical()
    {
        return !$this->isEbook();
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeConfirmed()
    {
        return $this->status === 'pending';
    }

    public function markAsConfirmed()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
    }

    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsShipped()
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now()
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }
}
