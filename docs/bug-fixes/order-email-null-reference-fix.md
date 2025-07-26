# Fix Lỗi "Attempt to read property 'title' on null" trong Email Confirmation

## Mô Tả Lỗi

**Thời gian**: 2025-07-25 12:49:36  
**Lỗi**: `Attempt to read property "title" on null`  
**File**: `resources/views/emails/orders/confirmation.blade.php:86`  
**Nguyên nhân**: Template email cố gắng truy cập `$item->book->title` mà không kiểm tra xem item có phải là combo hay không.

## Nguyên Nhân Chi Tiết

### 1. Cấu Trúc OrderItem
- OrderItem có thể là:
  - **Sách lẻ**: `book_id` và `book_format_id` có giá trị, `collection_id` = null
  - **Combo**: `collection_id` có giá trị, `book_id` và `book_format_id` = null

### 2. Lỗi Trong Email Template
```blade
<!-- Code cũ - LỖI -->
@foreach($order->orderItems as $item)
<tr>
    <td>{{ $item->book->title }}</td> <!-- Lỗi khi item là combo -->
    <td>{{ $item->quantity }}</td>
    <td>{{ number_format($item->price) }} VNĐ</td>
    <td>{{ number_format($item->total) }} VNĐ</td>
</tr>
@endforeach
```

### 3. Thiếu Relationships trong EmailService
```php
// Code cũ - THIẾU relationships
$order->load(['user', 'orderItems.book', 'address']);
```

## Giải Pháp Đã Áp Dụng

### 1. Cập Nhật Email Template
**File**: `resources/views/emails/orders/confirmation.blade.php`

```blade
<!-- Code mới - ĐÃ FIX -->
@foreach($order->orderItems as $item)
<tr>
    <td>
        @if($item->is_combo)
            {{ $item->collection->name ?? 'Combo không xác định' }}
            <small>(Combo)</small>
        @else
            {{ $item->book->title ?? 'Sách không xác định' }}
            @if($item->bookFormat)
                <small>({{ $item->bookFormat->format_name }})</small>
            @endif
        @endif
    </td>
    <td>{{ $item->quantity }}</td>
    <td>{{ number_format($item->price) }} VNĐ</td>
    <td>{{ number_format($item->total) }} VNĐ</td>
</tr>
@endforeach
```

### 2. Cập Nhật EmailService
**File**: `app/Services/EmailService.php`

```php
public function sendOrderConfirmation(Order $order)
{
    // Load relationships cho cả sách lẻ và combo
    $order->load([
        'user', 
        'orderItems.book', 
        'orderItems.bookFormat',
        'orderItems.collection', 
        'address',
        'orderStatus',
        'paymentMethod'
    ]);

    Mail::to($order->user->email)
        ->send(new OrderConfirmation($order));
}
```

## Cách Tránh Lỗi Tương Tự

### 1. Luôn Kiểm Tra Null Reference
```blade
<!-- BAD -->
{{ $item->book->title }}

<!-- GOOD -->
{{ $item->book->title ?? 'Không xác định' }}
```

### 2. Kiểm Tra Loại Item Trước Khi Truy Cập
```blade
@if($item->is_combo)
    <!-- Xử lý combo -->
    {{ $item->collection->name }}
@else
    <!-- Xử lý sách lẻ -->
    {{ $item->book->title }}
@endif
```

### 3. Load Đầy Đủ Relationships
```php
// Luôn load tất cả relationships cần thiết
$order->load([
    'orderItems.book',
    'orderItems.bookFormat', 
    'orderItems.collection'
]);
```

### 4. Sử dụng Helper Methods
```php
// Trong OrderItem model
public function getItemName(): string
{
    if ($this->isCombo()) {
        return $this->collection->name ?? 'Combo không xác định';
    }
    return $this->book->title ?? 'Sách không xác định';
}
```

## Checklist Khi Làm Việc Với OrderItem

- [ ] Kiểm tra `is_combo` trước khi truy cập `book` hoặc `collection`
- [ ] Load đầy đủ relationships: `book`, `bookFormat`, `collection`
- [ ] Sử dụng null coalescing operator (`??`) cho safety
- [ ] Test với cả sách lẻ và combo
- [ ] Kiểm tra email template với dữ liệu thực tế

## Files Đã Fix

### Email Templates
- `resources/views/emails/orders/confirmation.blade.php` ✅
- `resources/views/emails/orders/ebook-purchase-confirmation.blade.php` ✅
- `resources/views/emails/orders/invoice.blade.php` ✅

### View Templates
- `resources/views/orders/show.blade.php` ✅
- `resources/views/clients/account/order-detail.blade.php` ✅

### Services
- `app/Services/EmailService.php` ✅

### Models & Logic
- `app/Models/OrderItem.php` - Model chính
- `app/Services/OrderService.php` - Logic tạo order

### Files Cần Kiểm Tra Thêm
- `resources/views/clients/account/purchases.blade.php` ⚠️
- `resources/views/clients/account/review_form.blade.php` ⚠️
- `resources/views/admin/invoices/show.blade.php` ⚠️
- `resources/views/admin/invoices/pdf.blade.php` ⚠️

## Ghi Chú

Lỗi này thường xảy ra khi:
1. Hệ thống hỗ trợ nhiều loại item (sách lẻ + combo)
2. Template không được cập nhật theo cấu trúc dữ liệu mới
3. Relationships không được load đầy đủ
4. Thiếu null safety checks

**Luôn nhớ**: Khi có nhiều loại dữ liệu trong cùng một model, cần kiểm tra loại trước khi truy cập properties cụ thể.