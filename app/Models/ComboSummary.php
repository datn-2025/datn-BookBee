<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComboSummary extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'collection_id',
        'summary',
        'detailed_summary', 
        'key_points',
        'themes',
        'benefits',
        'ai_model',
        'status',
        'error_message'
    ];

    protected $casts = [
        'key_points' => 'array',
        'themes' => 'array',
        'benefits' => 'array'
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

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }
}
