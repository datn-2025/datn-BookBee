<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\EbookDownload;
class BookFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'format_name',
        'price',
        'discount',
        'stock',
        'file_url',
        'sample_file_url',
        'allow_sample_read',
        'max_downloads',
        'drm_enabled',
        'download_expiry_days'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'stock' => 'integer',
        'allow_sample_read' => 'boolean',
        'max_downloads' => 'integer',
        'drm_enabled' => 'boolean',
        'download_expiry_days' => 'integer'
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

    public $incrementing = false; 
    protected $keyType = 'string';

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function downloads()
    {
        return $this->hasMany(EbookDownload::class);
    }

    /**
     * Kiểm tra xem user có thể tải ebook này không
     */
    public function canUserDownload($userId, $orderId = null)
    {
        if (!$this->drm_enabled) {
            return true;
        }

        $downloadCount = $this->downloads()
            ->where('user_id', $userId)
            ->when($orderId, function($query) use ($orderId) {
                return $query->where('order_id', $orderId);
            })
            ->count();

        return $downloadCount < $this->max_downloads;
    }

    /**
     * Lấy số lần tải còn lại
     */
    public function getRemainingDownloads($userId, $orderId = null)
    {
        if (!$this->drm_enabled) {
            return 999; // Không giới hạn
        }

        $downloadCount = $this->downloads()
            ->where('user_id', $userId)
            ->when($orderId, function($query) use ($orderId) {
                return $query->where('order_id', $orderId);
            })
            ->count();

        return max(0, $this->max_downloads - $downloadCount);
    }
}
