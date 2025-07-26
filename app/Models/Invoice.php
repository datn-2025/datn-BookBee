<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'refund_request_id',
        'invoice_date',
        'total_amount',
        'refund_amount',
        'refund_method',
        'refund_reason',
        'refund_processed_at'
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refund_processed_at' => 'datetime'
    ];

    public $incrementing = false; 
    protected $keyType = 'string';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refundRequest(): BelongsTo
    {
        return $this->belongsTo(RefundRequest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
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
}
