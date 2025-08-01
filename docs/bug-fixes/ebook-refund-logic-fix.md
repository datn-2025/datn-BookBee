# Sửa lỗi Logic Refund Ebook

## Vấn đề
Logic refund ebook hiển thị sai số lượng download vì:
- Bảng `ebook_downloads` lưu `order_id` (tham chiếu đến bảng `orders`)
- Logic query đếm theo `order_id + book_format_id`
- Trong một order có thể có nhiều `order_items` cùng `book_format_id`
- Dẫn đến việc đếm sai số lượng download cho từng item riêng lẻ

## Nguyên nhân chi tiết

### Trước khi sửa:
```php
// Logic cũ - SAI
$downloadCount = EbookDownload::where('user_id', $user->id)
    ->where('order_id', $order->id)
    ->where('book_format_id', $item->book_format_id)
    ->count();
```

**Vấn đề**: Nếu một order có 2 items cùng `book_format_id`, logic này sẽ đếm tất cả downloads của cả 2 items cho mỗi item.

### Ví dụ cụ thể:
- Order có 2 items cùng book_format_id: `f1a76e15-0fe7-4c09-8b21-5afef62b70b2`
  - Item 1: `221b0198-012a-44cd-a7de-ed8cd6a11867` (đã tải 3 lần)
  - Item 2: `8781d443-609d-4bc6-ac7e-062b4e8a307b` (chưa tải)
- Logic cũ: Cả 2 items đều hiển thị 3 downloads
- Logic mới: Item 1 = 3 downloads, Item 2 = 0 downloads

## Giải pháp

### 1. Thêm field `order_item_id` vào bảng `ebook_downloads`

**Migration**: `2025_08_01_052745_add_order_item_id_to_ebook_downloads_table.php`

```php
Schema::table('ebook_downloads', function (Blueprint $table) {
    $table->uuid('order_item_id')->nullable()->after('order_id');
});

// Cập nhật dữ liệu hiện có
DB::statement("
    UPDATE ebook_downloads ed 
    SET order_item_id = (
        SELECT oi.id 
        FROM order_items oi 
        WHERE oi.order_id = ed.order_id 
        AND oi.book_format_id = ed.book_format_id 
        LIMIT 1
    )
");

// Thêm foreign key constraint
Schema::table('ebook_downloads', function (Blueprint $table) {
    $table->uuid('order_item_id')->nullable(false)->change();
    $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
    $table->index(['user_id', 'order_item_id']);
});
```

### 2. Cập nhật Model `EbookDownload`

**File**: `app/Models/EbookDownload.php`

```php
// Thêm vào fillable
protected $fillable = [
    'user_id',
    'order_id',
    'order_item_id', // ← Thêm mới
    'book_format_id',
    'ip_address',
    'user_agent',
    'downloaded_at'
];

// Thêm relationship
public function orderItem(): BelongsTo
{
    return $this->belongsTo(OrderItem::class);
}
```

### 3. Cập nhật Logic trong `EbookRefundService`

**File**: `app/Services/EbookRefundService.php`

```php
// Logic mới - ĐÚNG
$downloadCount = EbookDownload::where('user_id', $user->id)
    ->where('order_item_id', $item->id) // ← Thay đổi chính
    ->count();
```

## Kết quả sau khi sửa

### Test Case:
**Order ID**: `241a704d-1119-4490-ada2-ee40d92291ce`

#### Trước khi sửa:
- Item 1: 2 downloads → Không thể refund
- Item 2: 2 downloads → Không thể refund
- **Kết quả**: Không thể refund toàn bộ order

#### Sau khi sửa:
- Item 1: 3 downloads → Không thể refund (0%)
- Item 2: 0 downloads → Có thể refund (100%)
- **Kết quả**: Có thể refund 230,000đ cho Item 2

## Files đã thay đổi

1. **Migration**: `database/migrations/2025_08_01_052745_add_order_item_id_to_ebook_downloads_table.php`
2. **Model**: `app/Models/EbookDownload.php`
3. **Service**: `app/Services/EbookRefundService.php`

## Lưu ý quan trọng

1. **Dữ liệu cũ**: Migration tự động cập nhật dữ liệu hiện có
2. **Performance**: Thêm index `['user_id', 'order_item_id']` để tối ưu query
3. **Data Integrity**: Foreign key constraint đảm bảo tính toàn vẹn dữ liệu
4. **Backward Compatibility**: Vẫn giữ field `order_id` để tương thích

## Test Cases

✅ **Order với 1 ebook item**: Hoạt động bình thường  
✅ **Order với nhiều ebook items cùng book_format**: Đếm chính xác từng item  
✅ **Order với ebook items khác book_format**: Hoạt động bình thường  
✅ **Refund calculation**: Tính toán chính xác cho từng item  

---

**Ngày sửa**: 2025-08-01  
**Trạng thái**: ✅ Hoàn thành  
**Impact**: 🔧 Critical Fix - Sửa lỗi logic nghiêm trọng