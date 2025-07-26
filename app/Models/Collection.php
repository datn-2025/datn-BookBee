<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'name',
        'description',
        'cover_image',
        'slug',
        'status',
        'start_date',
        'end_date',
        'combo_price',
        'combo_stock', // Thêm trường này để fillable
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'combo_price' => 'decimal:2',
        'combo_stock' => 'integer', // Thêm kiểu dữ liệu
    ];

    protected $dates = ['deleted_at'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_collections');
    }

    public function summary()
    {
        return $this->hasOne(ComboSummary::class);
    }

    public function hasSummary()
    {
        return $this->summary()->exists();
    }

    public function getSummaryAttribute()
    {
        return $this->getRelationValue('summary');
    }
}
