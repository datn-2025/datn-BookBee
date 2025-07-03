<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'book_title',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'quantity',
        'unit_price',
        'shipping_cost',
        'book_total',
        'total_price',
        'book_format_id',
        'book_format_name',
        'attributes',
        'attributes_display',
        'status',
        'admin_notes',    ];

    protected $casts = [
        'attributes' => 'array',
        'attributes_display' => 'array',
        'unit_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'book_total' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_PREPARING => 'Đang chuẩn bị',
            self::STATUS_SHIPPED => 'Đã gửi hàng',
            self::STATUS_DELIVERED => 'Đã giao hàng',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public function getStatusTextAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? 'Không xác định';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-800',
            self::STATUS_PREPARING => 'bg-purple-100 text-purple-800',
            self::STATUS_SHIPPED => 'bg-indigo-100 text-indigo-800',
            self::STATUS_DELIVERED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
        ];
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('book_title', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_email', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%");
        });
    }

    // Format methods
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', '.') . '₫';
    }

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', '.') . '₫';
    }

    public function getFormattedBookTotalAttribute()
    {
        return number_format($this->book_total, 0, ',', '.') . '₫';
    }

    public function getFormattedShippingCostAttribute()
    {
        return number_format($this->shipping_cost, 0, ',', '.') . '₫';
    }

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function bookFormat()
    {
        return $this->belongsTo(BookFormat::class, 'book_format_id');
    }
}
