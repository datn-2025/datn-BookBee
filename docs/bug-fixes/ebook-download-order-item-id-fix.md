# Sửa lỗi "Field 'order_item_id' doesn't have a default value"

## Vấn đề

Khi user tải ebook, hệ thống báo lỗi:
```
SQLSTATE[HY000]: General error: 1364 Field 'order_item_id' doesn't have a default value
```

## Nguyên nhân

Sau khi thêm field `order_item_id` vào bảng `ebook_downloads`, code tạo `EbookDownload` mới trong `EbookDownloadController.php` không được cập nhật để bao gồm `order_item_id`.

### Code cũ (SAI):
```php
// EbookDownloadController.php - dòng 81-86
EbookDownload::create([
    'user_id' => $user->id,
    'order_id' => $order->id,
    'book_format_id' => $bookFormat->id,  // ← Thiếu order_item_id
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

## Giải pháp

### 1. Cập nhật `EbookDownloadController.php`

**File**: `app/Http/Controllers/EbookDownloadController.php`

#### Thêm logic tìm `order_item_id`:
```php
// Tìm order_item tương ứng với ebook này
$orderItem = $order->orderItems->first(function ($item) use ($bookFormat) {
    // Trường hợp 1: Mua trực tiếp ebook
    if ($item->book_format_id === $bookFormat->id && !$item->is_combo) {
        return true;
    }
    // Trường hợp 2: Mua sách vật lý có ebook kèm theo
    if ($item->book_id === $bookFormat->book_id && !$item->is_combo && 
        $item->bookFormat && $item->bookFormat->format_name !== 'Ebook') {
        return true;
    }
    return false;
});

if (!$orderItem) {
    abort(403, 'Không tìm thấy order item tương ứng với ebook này.');
}
```

#### Cập nhật logic tạo `EbookDownload`:
```php
// Code mới - ĐÚNG
EbookDownload::create([
    'user_id' => $user->id,
    'order_id' => $order->id,
    'order_item_id' => $orderItem->id,  // ← Thêm order_item_id
    'book_format_id' => $bookFormat->id,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

### 2. Cập nhật Test Cases

**File**: `tests/Feature/EbookDrmTest.php`

#### Thêm property `orderItem`:
```php
protected $orderItem;
```

#### Cập nhật setup:
```php
$this->orderItem = OrderItem::factory()->create([
    'order_id' => $this->order->id,
    'book_format_id' => $this->ebookFormat->id,
    'quantity' => 1,
    'price' => 100000
]);
```

#### Cập nhật tất cả `EbookDownload::create`:
```php
EbookDownload::create([
    'user_id' => $this->user->id,
    'order_id' => $this->order->id,
    'order_item_id' => $this->orderItem->id,  // ← Thêm order_item_id
    'book_format_id' => $this->ebookFormat->id,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Test Agent',
    'downloaded_at' => now()
]);
```

## Kết quả

### Test thành công:
```
✅ Tìm thấy order: 403a0dff-5686-40a2-924c-e5483c8b53e3
✅ Tìm thấy ebook item: 4268e882-7dfe-430a-906a-53ffb83db8ef
✅ Tạo EbookDownload thành công!
Download ID: 703091c6-d186-4507-a1de-fe360918aab0
Order Item ID: 4268e882-7dfe-430a-906a-53ffb83db8ef
📊 Download count cho item này: 1
📊 Download count theo logic cũ: 1
✅ Logic mới và cũ cho kết quả giống nhau
```

### Các trường hợp được xử lý:
1. **Mua trực tiếp ebook**: `book_format_id` khớp với ebook format
2. **Mua sách vật lý có ebook kèm**: `book_id` khớp và format khác Ebook
3. **Validation**: Kiểm tra `order_item` tồn tại trước khi tạo download

## Files đã thay đổi

1. **Controller**: `app/Http/Controllers/EbookDownloadController.php`
   - Thêm logic tìm `order_item_id`
   - Cập nhật `EbookDownload::create`

2. **Test**: `tests/Feature/EbookDrmTest.php`
   - Thêm property `$orderItem`
   - Cập nhật setup và tất cả test cases

## Lưu ý quan trọng

1. **Backward Compatibility**: Logic vẫn hỗ trợ cả 2 trường hợp mua ebook
2. **Error Handling**: Thêm validation để đảm bảo `order_item` tồn tại
3. **Data Integrity**: Đảm bảo `order_item_id` luôn được set khi tạo download
4. **Test Coverage**: Tất cả test cases đã được cập nhật

---

**Ngày sửa**: 2025-08-01  
**Trạng thái**: ✅ Hoàn thành  
**Impact**: 🔧 Critical Fix - Sửa lỗi runtime khi tải ebook