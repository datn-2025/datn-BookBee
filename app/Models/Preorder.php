<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Preorder extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    protected $fillable = [
        'user_id',
        'book_id',
        'book_format_id',
        'payment_method_id',
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
        'delivered_at',
        'preorder_code',
        'payment_status',
        'vnpay_transaction_id',
        'cancellation_reason',
        'cancelled_at'
    ];

    protected $casts = [
        'selected_attributes' => 'array',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expected_delivery_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    // Relationships
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

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Accessor for displaying selected attributes
    public function getSelectedAttributesDisplayAttribute(): string
    {
        if (!$this->selected_attributes || empty($this->selected_attributes)) {
            return 'Không có thuộc tính';
        }

        $attributeIds = $this->selected_attributes;
        if (is_string($attributeIds)) {
            $attributeIds = json_decode($attributeIds, true);
        }

        if (!is_array($attributeIds) || empty($attributeIds)) {
            return 'Không có thuộc tính';
        }

        $attributes = \App\Models\AttributeValue::whereIn('id', $attributeIds)
            ->with('attribute')
            ->get()
            ->map(function ($attrValue) {
                return $attrValue->attribute->name . ': ' . $attrValue->value;
            })
            ->toArray();

        return !empty($attributes) ? implode(', ', $attributes) : 'Không có thuộc tính';
    }

    // Helper method to get formatted attributes for display
    public function getFormattedAttributesAttribute(): array
    {
        if (!$this->selected_attributes || empty($this->selected_attributes)) {
            return [];
        }

        $attributeIds = $this->selected_attributes;
        if (is_string($attributeIds)) {
            $attributeIds = json_decode($attributeIds, true);
        }

        if (!is_array($attributeIds) || empty($attributeIds)) {
            return [];
        }

        return \App\Models\AttributeValue::whereIn('id', $attributeIds)
            ->with('attribute')
            ->get()
            ->map(function ($attrValue) {
                return [
                    'attribute_name' => $attrValue->attribute->name,
                    'value' => $attrValue->value,
                    'display' => $attrValue->attribute->name . ': ' . $attrValue->value
                ];
            })
            ->toArray();
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    // Status methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isShipped(): bool
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // Status helpers
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_SHIPPED => 'Đã gửi hàng',
            self::STATUS_DELIVERED => 'Đã giao hàng',
            self::STATUS_CANCELLED => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_SHIPPED => 'secondary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'dark'
        };
    }

    // Scope methods
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
}
