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

    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
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

    public function appliedVoucher(): HasOne
    {
        return $this->hasOne(AppliedVoucher::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

     public function shipping()
    {
        return $this->hasOne(Shipping::class);
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
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
