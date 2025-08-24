<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Preorder extends Model
{
    use HasFactory;

    // Constants cho trạng thái preorder
    const STATUS_CHO_DUYET = 'Chờ duyệt';
    const STATUS_DA_DUYET = 'Đã duyệt';
    const STATUS_SAN_SANG_CHUYEN_DOI = 'Sẵn sàng chuyển đổi';
    const STATUS_DA_CHUYEN_THANH_DON_HANG = 'Đã chuyển thành đơn hàng';
    const STATUS_DA_HUY = 'Đã hủy';

    // Danh sách tất cả trạng thái hợp lệ
    const VALID_STATUSES = [
        self::STATUS_CHO_DUYET,
        self::STATUS_DA_DUYET,
        self::STATUS_SAN_SANG_CHUYEN_DOI,
        self::STATUS_DA_CHUYEN_THANH_DON_HANG,
        self::STATUS_DA_HUY,
    ];

    // Mapping trạng thái cũ sang mới (để migration)
    const STATUS_MAPPING = [
        'pending' => self::STATUS_CHO_DUYET,
        'Chờ xác nhận' => self::STATUS_CHO_DUYET,
        'confirmed' => self::STATUS_DA_DUYET,
        'Đã xác nhận' => self::STATUS_DA_DUYET,
        'processing' => self::STATUS_SAN_SANG_CHUYEN_DOI,
        'shipped' => self::STATUS_SAN_SANG_CHUYEN_DOI,
        'delivered' => self::STATUS_DA_CHUYEN_THANH_DON_HANG,
        'cancelled' => self::STATUS_DA_HUY,
        'Đã hủy' => self::STATUS_DA_HUY,
    ];

    protected $fillable = [
        'user_id',
        'book_id',
        'book_format_id',
        'customer_name',
        'email',
        'ebook_delivery_email',
        'phone',
        'address',
        'province_code',
        'province_name',
        'district_code',
        'district_name',
        'ward_code',
        'ward_name',
        'quantity',
        'unit_price',
        'total_amount',
        'shipping_fee',
        'selected_attributes',
        'status',
        'notes',
        'expected_delivery_date',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'converted_at',
        'converted_order_id',
        'payment_method_id',
        'payment_status'
    ];

    protected $casts = [
        'selected_attributes' => 'array',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'expected_delivery_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'converted_at' => 'datetime'
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

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với Book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Quan hệ với BookFormat
     */
    public function bookFormat(): BelongsTo
    {
        return $this->belongsTo(BookFormat::class);
    }

    /**
     * Quan hệ với PaymentMethod
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Quan hệ với Order đã chuyển đổi
     */
    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_CHO_DUYET);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_DA_DUYET);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_SAN_SANG_CHUYEN_DOI);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SAN_SANG_CHUYEN_DOI);
    }

    public function scopeNotConverted($query)
    {
        return $query->where('status', '!=', self::STATUS_DA_CHUYEN_THANH_DON_HANG)
                    ->orWhereNull('converted_order_id');
    }

    public function scopeByStatus($query, $status)
    {
        // Hỗ trợ cả trạng thái cũ và mới
        $normalizedStatus = self::STATUS_MAPPING[$status] ?? $status;
        return $query->where('status', $normalizedStatus);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DA_CHUYEN_THANH_DON_HANG);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_DA_HUY);
    }

    public function scopeReadyToConvert($query)
    {
        return $query->where('status', self::STATUS_SAN_SANG_CHUYEN_DOI);
    }

    public function scopeConverted($query)
    {
        return $query->where('status', self::STATUS_DA_CHUYEN_THANH_DON_HANG);
    }

    /**
     * Accessors
     */
    public function getStatusTextAttribute()
    {
        // Nếu status đã là trạng thái mới (tiếng Việt), trả về luôn
        if (in_array($this->status, self::VALID_STATUSES)) {
            return $this->status;
        }

        // Chuyển đổi từ trạng thái cũ sang mới
        return self::STATUS_MAPPING[$this->status] ?? $this->status ?? 'Không xác định';
    }

    /**
     * Chuẩn hóa trạng thái từ cũ sang mới
     */
    public function normalizeStatus()
    {
        if (!in_array($this->status, self::VALID_STATUSES)) {
            $newStatus = self::STATUS_MAPPING[$this->status] ?? self::STATUS_CHO_DUYET;
            $this->update(['status' => $newStatus]);
        }
        return $this;
    }

    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->ward_name . ', ' . $this->district_name . ', ' . $this->province_name;
    }

    /**
     * Methods
     */
    public function isEbook()
    {
        return $this->bookFormat && strtolower($this->bookFormat->format_name) === 'ebook';
    }

    public function isPhysical()
    {
        return !$this->isEbook();
    }

    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_CHO_DUYET, self::STATUS_DA_DUYET]);
    }

    public function canBeApproved()
    {
        return $this->status === self::STATUS_CHO_DUYET;
    }

    public function canBeConverted()
    {
        return in_array($this->status, [self::STATUS_DA_DUYET, self::STATUS_SAN_SANG_CHUYEN_DOI]) && 
               $this->book && 
               !$this->isConverted();
    }

    public function isConverted()
    {
        return $this->status === self::STATUS_DA_CHUYEN_THANH_DON_HANG && 
               !is_null($this->converted_order_id);
    }

    public function canBeShipped()
    {
        return $this->status === self::STATUS_DA_DUYET && 
               $this->isPhysical();
    }

    public function isReadyToConvert()
    {
        return $this->status === self::STATUS_SAN_SANG_CHUYEN_DOI;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_CHO_DUYET;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_DA_DUYET;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_DA_HUY;
    }

    /**
     * Kiểm tra xem preorder có thể chuyển sang trạng thái mới không
     */
    public function canTransitionTo($newStatus)
    {
        $allowedTransitions = [
            self::STATUS_CHO_DUYET => [self::STATUS_DA_DUYET, self::STATUS_DA_HUY],
            self::STATUS_DA_DUYET => [self::STATUS_SAN_SANG_CHUYEN_DOI, self::STATUS_DA_HUY],
            self::STATUS_SAN_SANG_CHUYEN_DOI => [self::STATUS_DA_CHUYEN_THANH_DON_HANG, self::STATUS_DA_HUY],
            self::STATUS_DA_CHUYEN_THANH_DON_HANG => [], // Không thể chuyển từ trạng thái này
            self::STATUS_DA_HUY => [] // Không thể chuyển từ trạng thái này
        ];

        return in_array($newStatus, $allowedTransitions[$this->status] ?? []);
    }

    /**
     * Chuyển đổi trạng thái với validation
     */
    public function transitionTo($newStatus, $additionalData = [])
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Không thể chuyển từ trạng thái '{$this->status}' sang '{$newStatus}'"
            );
        }

        $updateData = array_merge(['status' => $newStatus], $additionalData);
        
        // Thêm timestamp tương ứng
        switch ($newStatus) {
            case self::STATUS_DA_DUYET:
                $updateData['confirmed_at'] = now();
                break;
            case self::STATUS_DA_CHUYEN_THANH_DON_HANG:
                $updateData['converted_at'] = now();
                break;
        }

        return $this->update($updateData);
    }

    public function markAsApproved()
    {
        $this->update([
            'status' => self::STATUS_DA_DUYET,
            'confirmed_at' => now()
        ]);
    }

    public function markAsReadyToConvert()
    {
        $this->update(['status' => self::STATUS_SAN_SANG_CHUYEN_DOI]);
    }

    public function markAsConverted($orderId = null)
    {
        $this->update([
            'status' => self::STATUS_DA_CHUYEN_THANH_DON_HANG,
            'converted_at' => now(),
            'converted_order_id' => $orderId
        ]);
    }

    public function markAsCancelled()
    {
        \DB::transaction(function () {
            // Hoàn trả stock khi hủy preorder
            $this->restoreStockForCancelledPreorder();
            
            // Cập nhật status
            $this->update(['status' => self::STATUS_DA_HUY]);
        });
    }
    
    /**
     * Hoàn trả số lượng tồn kho khi hủy preorder
     */
    private function restoreStockForCancelledPreorder()
    {
        // Trừ preorder_count của sách
        $this->book->decrement('preorder_count', $this->quantity);
        
        // Hoàn trả stock của book format nếu có
        if ($this->bookFormat) {
            $this->bookFormat->increment('stock', $this->quantity);
        }
        
        // Hoàn trả stock của các thuộc tính được chọn
        if (!empty($this->selected_attributes)) {
            foreach ($this->selected_attributes as $attributeData) {
                if (isset($attributeData['attribute_name']) && isset($attributeData['value'])) {
                    // Tìm attribute value dựa trên tên và giá trị
                    $attributeValue = \App\Models\AttributeValue::whereHas('attribute', function($query) use ($attributeData) {
                        $query->where('name', $attributeData['attribute_name']);
                    })->where('value', $attributeData['value'])->first();
                    
                    if ($attributeValue) {
                        $bookAttributeValue = \App\Models\BookAttributeValue::where('book_id', $this->book_id)
                            ->where('attribute_value_id', $attributeValue->id)
                            ->first();
                            
                        if ($bookAttributeValue) {
                            $bookAttributeValue->increment('stock', $this->quantity);
                        }
                    }
                }
            }
        }
    }
}
