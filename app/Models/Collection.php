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
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'combo_price' => 'decimal:2',
    ];

    protected $dates = ['deleted_at'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_collections');
    }
}
