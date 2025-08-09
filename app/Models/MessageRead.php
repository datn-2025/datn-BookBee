<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MessageRead extends Model
{
    /** @use HasFactory<\Database\Factories\MessageReadFactory> */
    use HasFactory;
     public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'message_id',
        'user_id',
        'read_at',
    ];

    // Quan hệ với tin nhắn
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    // Ai là người đã đọc
    public function user()
    {
        return $this->belongsTo(User::class);
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
