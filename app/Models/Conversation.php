<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationFactory> */
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'customer_id',
        'admin_id',
        'order_id',
        'last_message_at',
    ];
    // Người dùng là khách hàng
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Người dùng là admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Danh sách tin nhắn
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Đơn hàng liên quan
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
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
