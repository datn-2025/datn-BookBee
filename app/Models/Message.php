<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory;
     public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'type',
        'file_path',
        'is_auto_reply',
    ];

    protected $casts = [
        'is_auto_reply' => 'boolean',
    ];

    // Quan hệ với cuộc hội thoại
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // Người gửi tin nhắn
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Danh sách người đã đọc
    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }

    /**
     * Check if message has been read by a specific user
     */
    public function isReadBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    /**
     * Get read status for a specific user
     */
    public function getReadStatus($userId)
    {
        $readRecord = $this->reads()->where('user_id', $userId)->first();
        return $readRecord ? $readRecord->read_at : null;
    }

    /**
     * Check if sender is admin
     */
    public function isAdmin()
    {
        return $this->sender && $this->sender->role && $this->sender->role->name === 'admin';
    }
  // tạo id theo uuid
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
