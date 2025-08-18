<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'type_id',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra thông báo đã được đọc chưa
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Scope để lấy thông báo chưa đọc
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope để lấy thông báo theo loại
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
