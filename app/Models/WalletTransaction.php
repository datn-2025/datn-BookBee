<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WalletTransaction extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'wallet_id', 'amount', 'type', 'description', 'related_order_id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'related_order_id');
    }
}
