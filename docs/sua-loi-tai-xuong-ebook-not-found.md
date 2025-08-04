# Sửa Lỗi Tải Xuống Ebook Not Found

## Mô Tả Lỗi
Sau khi thanh toán xong đơn hàng ebook, khi người dùng nhấn nút "Tải Xuống" thì bị lỗi "not found" hoặc "403 Forbidden".

## Nguyên Nhân
**[LỖI]**: Logic kiểm tra đơn hàng trong EbookDownloadController không chính xác
**[FILE]**: `app/Http/Controllers/EbookDownloadController.php` - dòng 38-44 và 65-73
**[NGUYÊN NHÂN]**: 
- Logic kiểm tra đơn hàng trong method `download` quá đơn giản, chỉ dựa vào `order_id` từ request
- Không kiểm tra đúng relationship giữa Order, OrderItem và BookFormat
- Logic tìm OrderItem không chính xác, có thể không tìm được item tương ứng với ebook

## Giải Pháp Đã Áp Dụng

### 1. Sửa Logic Kiểm Tra Đơn Hàng
**File**: `app/Http/Controllers/EbookDownloadController.php`
**Dòng**: 38-57

```php
// Trước khi sửa
$order = \App\Models\Order::where('id', $request->order_id)
    ->where('user_id', $user->id)
    ->whereHas('paymentStatus', fn($q) => $q->where('name', 'Đã Thanh Toán'))
    ->first();

if (!$order) {
    return abort(403, 'Đơn hàng không hợp lệ hoặc chưa thanh toán.');
}

// Sau khi sửa
// Kiểm tra user đã mua ebook này chưa
$order = Order::where('user_id', $user->id)
    ->whereHas('orderItems', function ($query) use ($bookFormat) {
        $query->where(function ($q) use ($bookFormat) {
            $q->where('book_format_id', $bookFormat->id)
              ->where('is_combo', false);
        })->orWhere(function ($q) use ($bookFormat) {
            $q->where('book_id', $bookFormat->book_id)
              ->where('is_combo', false)
              ->whereHas('bookFormat', function ($subQuery) {
                  $subQuery->where('format_name', 'Ebook');
              });
        });
    })
    ->whereHas('paymentStatus', function ($query) {
        $query->where('name', 'Đã Thanh Toán');
    })
    ->first();

if (!$order) {
    abort(403, 'Bạn chưa mua ebook này hoặc đơn hàng chưa được thanh toán.');
}
```

### 2. Sửa Logic Tìm OrderItem
**File**: `app/Http/Controllers/EbookDownloadController.php`
**Dòng**: 65-75

```php
// Trước khi sửa
$orderItem = $order->orderItems()
->where('is_combo', false)
->whereHas('bookFormat', fn($q) => $q->where('format_name', 'Ebook'))
->first();

if (!$orderItem) {
    abort(403, 'Không tìm thấy order item tương ứng với ebook này.');
}

// Sau khi sửa
// Tìm order item tương ứng với ebook này
$orderItem = $order->orderItems()
    ->where(function ($query) use ($bookFormat) {
        $query->where('book_format_id', $bookFormat->id)
              ->where('is_combo', false);
    })
    ->first();
    
if (!$orderItem) {
    abort(403, 'Không tìm thấy order item tương ứng với ebook này.');
}
```

## Kết Quả Mong Muốn
Sau khi sửa lỗi:
- Người dùng có thể tải xuống ebook sau khi thanh toán thành công
- Logic kiểm tra đơn hàng chính xác, tìm đúng đơn hàng chứa ebook
- Hệ thống tìm đúng OrderItem tương ứng với BookFormat
- Không còn lỗi "not found" hoặc "403 Forbidden" khi tải ebook

## Logic Hoạt Động Sau Khi Sửa
1. **Kiểm tra BookFormat**: Đảm bảo là ebook và file tồn tại
2. **Tìm Order**: Tìm đơn hàng của user có chứa ebook này và đã thanh toán
3. **Kiểm tra OrderItem**: Tìm item cụ thể trong đơn hàng tương ứng với ebook
4. **Kiểm tra DRM**: Kiểm tra số lần tải và thời hạn (nếu có)
5. **Trả về file**: Download file ebook

## Cách Tránh Lỗi Tương Lai
1. **Consistent Logic**: Sử dụng logic kiểm tra đơn hàng nhất quán giữa các method (download và view)
2. **Proper Relationships**: Luôn kiểm tra đúng relationship giữa các model
3. **Comprehensive Testing**: Test với nhiều trường hợp: ebook đơn lẻ, ebook trong mixed order, ebook combo
4. **Error Handling**: Cung cấp thông báo lỗi rõ ràng cho từng trường hợp

## Các File Liên Quan
- `app/Http/Controllers/EbookDownloadController.php` - Controller xử lý tải ebook
- `app/Models/Order.php` - Model đơn hàng
- `app/Models/OrderItem.php` - Model item trong đơn hàng
- `app/Models/BookFormat.php` - Model định dạng sách
- `resources/views/clients/account/order-details.blade.php` - Giao diện hiển thị nút tải

## Ghi Chú
Lỗi này nghiêm trọng vì ảnh hưởng trực tiếp đến khả năng sử dụng sản phẩm đã mua của khách hàng. Việc sửa lỗi này đảm bảo trải nghiệm người dùng mượt mà và logic kinh doanh hoạt động đúng.