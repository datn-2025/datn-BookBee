<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Import HasUuids

class OrderCancellation extends Model
{
    use HasFactory, HasUuids; // Use HasUuids

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'reason',
        'cancelled_by',
        'cancelled_at',
        'refund_status',
        'cancellation_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cancelled_at' => 'datetime',
        'id' => 'string', // Ensure UUID is treated as a string
    ];

    /**
     * Get the order that was cancelled.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who cancelled the order.
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}