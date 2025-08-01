# Khắc phục vấn đề hiển thị Ebook Downloads trong Admin

## Mô tả vấn đề

Dữ liệu trong bảng `ebook_downloads` đã có 2 lượt tải nhưng giao diện admin vẫn hiển thị 0 lượt tải.

## Nguyên nhân có thể

### 1. **Browser Cache**
- Trình duyệt đã cache view cũ
- CSS/JS cache chưa được refresh

### 2. **Laravel Cache**
- View cache chưa được clear
- Application cache chưa được refresh

### 3. **Database Connection**
- Kết nối database có thể bị lag
- Transaction chưa được commit đúng cách

## Kiểm tra dữ liệu

### Command kiểm tra
```bash
php artisan app:check-ebook-downloads --order-id=241a704d-1119-4490-ada2-ee40d92291ce
```

### Kết quả mong đợi
```
=== KIỂM TRA ORDER #241a704d-1119-4490-ada2-ee40d92291ce ===
User ID: b437a713-e6b8-47d7-b990-c8f45b6f6fbc
Order Status: Thành công
Số ebook trong order: 2

--- Ebook: Du lịch Đài Loan mùa Hoa Anh Đào ---
Book Format ID: f1a76e15-0fe7-4c09-8b21-5afef62b70b2
DRM Enabled: Yes
Max Downloads: 5
Download Count: 2
  - Downloaded at: 2025-08-01 03:38:42 from 127.0.0.1
  - Downloaded at: 2025-08-01 03:39:42 from 127.0.0.1
```

## Các bước khắc phục

### Bước 1: Clear tất cả cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### Bước 2: Restart server
```bash
# Dừng server hiện tại (Ctrl+C)
php artisan serve --host=0.0.0.0 --port=8000
```

### Bước 3: Hard refresh browser
- **Chrome/Edge**: `Ctrl + Shift + R`
- **Firefox**: `Ctrl + F5`
- Hoặc mở Incognito/Private mode

### Bước 4: Kiểm tra Developer Tools
1. Mở Developer Tools (F12)
2. Vào tab Network
3. Check "Disable cache"
4. Refresh trang

## Logic Query trong View

### File: `resources/views/admin/orders/show.blade.php`

```php
@php
    $downloadCount = \App\Models\EbookDownload::where('user_id', $order->user_id)
        ->where('order_id', $order->id)
        ->where('book_format_id', $item->bookFormat->id)
        ->count();
@endphp
```

### Điều kiện hiển thị
```php
@if($item->bookFormat && $item->bookFormat->format_name === 'Ebook')
    {{-- Hiển thị thông tin downloads --}}
@endif
```

## Xác minh dữ liệu

### Dữ liệu trong database
- **Order ID**: `241a704d-1119-4490-ada2-ee40d92291ce`
- **User ID**: `b437a713-e6b8-47d7-b990-c8f45b6f6fbc`
- **Book Format ID**: `f1a76e15-0fe7-4c09-8b21-5afef62b70b2`
- **Download Count**: `2`

### Downloads records
```
ID: 4a40ecb5-a2ba-493a-9ccd-4433c71d98d4
Downloaded: 2025-08-01 03:38:42
IP: 127.0.0.1

ID: 9389b4c0-ad3e-4b4c-914c-3800d4547d6e
Downloaded: 2025-08-01 03:39:42
IP: 127.0.0.1
```

## Kết luận

Logic query và dữ liệu database đều chính xác. Vấn đề chủ yếu do:
1. **Browser cache** - cần hard refresh
2. **Laravel cache** - cần clear cache
3. **Server restart** - cần restart để áp dụng thay đổi

## Lưu ý

- Luôn clear cache sau khi cập nhật view
- Sử dụng Incognito mode khi test
- Kiểm tra Network tab trong Developer Tools
- Đảm bảo server đã restart sau thay đổi

## Command hữu ích

```bash
# Kiểm tra ebook downloads
php artisan app:check-ebook-downloads

# Clear tất cả cache
php artisan optimize:clear

# Restart server
php artisan serve --host=0.0.0.0 --port=8000
```