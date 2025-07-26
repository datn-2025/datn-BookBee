<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'image', 'status'];

    public $incrementing = false;
    protected $keyType = 'string';

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'category_id');
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

    // public $incrementing = false;
    // protected $keyType = 'string';

    // Danh mục cha
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Danh mục con
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Sản phẩm thuộc danh mục
    public function products()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}
