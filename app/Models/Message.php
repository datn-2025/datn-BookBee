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
  // tạo id theo uuid
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

}
