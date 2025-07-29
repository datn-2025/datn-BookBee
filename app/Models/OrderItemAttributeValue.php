<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItemAttributeValue extends Model
{
    protected $table = 'order_item_attribute_values';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'order_item_id', 'attribute_value_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
