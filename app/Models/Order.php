<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_order_id',
        'address_id',
        'voucher_id',
        'total_amount',
        'order_code',
        'order_status_id',
        'payment_method_id',
        'payment_status_id',
        'qr_code',
        'shipping_fee',
        'note',
        'discount_amount',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'cancelled_at',
        'cancellation_reason',
        'delivery_method',
        'ghn_order_code',
        'ghn_service_type_id',
        'expected_delivery_date',
        'ghn_tracking_data',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'expected_delivery_date' => 'datetime',
        'ghn_tracking_data' => 'array',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function paymentStatus(): BelongsTo
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function appliedVoucher(): HasOne
    {
        return $this->hasOne(AppliedVoucher::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Cuộc hội thoại liên quan đến đơn hàng
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'order_id');
    }

    //  public function shipping()
    // {
    //     return $this->hasOne(shipping::class);
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Đơn hàng cha (parent order)
     */
    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    /**
     * Các đơn hàng con (child orders)
     */
    public function childOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    /**
     * Kiểm tra xem đây có phải là đơn hàng cha không
     */
    public function isParentOrder(): bool
    {
        return $this->parent_order_id === null && $this->childOrders()->exists();
    }

    /**
     * Kiểm tra xem đây có phải là đơn hàng con không
     */
    public function isChildOrder(): bool
    {
        return $this->parent_order_id !== null;
    }

    public function shippingAddress()
    {
        // Giả sử bạn có trường shipping_address_id trong bảng orders
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        // Giả sử bạn có trường billing_address_id trong bảng orders
        return $this->belongsTo(Address::class, 'billing_address_id');
    }
}
