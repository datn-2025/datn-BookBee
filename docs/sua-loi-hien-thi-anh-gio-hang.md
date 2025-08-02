# Sửa lỗi hiển thị ảnh trong giỏ hàng

## Mô tả vấn đề
Giỏ hàng không hiển thị ảnh sách và combo do:
1. Sự khác biệt trong cách xử lý đường dẫn ảnh giữa combo và sách thường
2. Thiếu ảnh mặc định khi không có ảnh
3. Logic xử lý đường dẫn ảnh không nhất quán

## Nguyên nhân

### 1. Trong CartController (app/Http/Controllers/Cart/CartController.php)
- Dữ liệu ảnh được lấy từ trường `cover_image` của bảng `books` và `collections`
- Đường dẫn ảnh được lưu dưới dạng: `books/ten-file.jpg` hoặc `collections/ten-file.jpg`

### 2. Trong view cart.blade.php
- **Combo**: Sử dụng `asset('storage/' . $item->image)` ✅
- **Sách thường**: Chỉ sử dụng `asset($item->image)` ❌
- Thiếu xử lý trường hợp không có ảnh

## Cách khắc phục đã thực hiện

### 1. Tạo ảnh mặc định
Tạo file `public/images/default-book.svg` làm ảnh mặc định khi không có ảnh:

```svg
<svg width="200" height="250" viewBox="0 0 200 250" fill="none" xmlns="http://www.w3.org/2000/svg">
  <!-- Book Cover Background -->
  <rect width="200" height="250" rx="8" fill="#f3f4f6"/>
  <rect x="4" y="4" width="192" height="242" rx="4" fill="#e5e7eb"/>
  
  <!-- Book Icon -->
  <g transform="translate(75, 90)">
    <rect width="50" height="60" rx="2" fill="#9ca3af"/>
    <rect x="5" y="5" width="40" height="50" rx="1" fill="#d1d5db"/>
    
    <!-- Book Lines -->
    <line x1="10" y1="15" x2="40" y2="15" stroke="#9ca3af" stroke-width="1"/>
    <line x1="10" y1="22" x2="35" y2="22" stroke="#9ca3af" stroke-width="1"/>
    <line x1="10" y1="29" x2="38" y2="29" stroke="#9ca3af" stroke-width="1"/>
    <line x1="10" y1="36" x2="32" y2="36" stroke="#9ca3af" stroke-width="1"/>
    <line x1="10" y1="43" x2="36" y2="43" stroke="#9ca3af" stroke-width="1"/>
  </g>
  
  <!-- Text -->
  <text x="100" y="180" text-anchor="middle" fill="#6b7280" font-family="Arial, sans-serif" font-size="12" font-weight="500">Không có ảnh</text>
  <text x="100" y="200" text-anchor="middle" fill="#9ca3af" font-family="Arial, sans-serif" font-size="10">Ảnh mặc định</text>
</svg>
```

### 2. Cập nhật logic hiển thị ảnh trong cart.blade.php

**Trước khi sửa:**
```php
<!-- Combo -->
<img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title ?? 'Combo image' }}">

<!-- Sách thường -->
<img src="{{ asset($item->image) }}" alt="{{ $item->title ?? 'Book image' }}">
```

**Sau khi sửa:**
```php
<!-- Cả combo và sách thường -->
<img src="{{ $item->image ? (str_starts_with($item->image, 'http') ? $item->image : asset('storage/' . $item->image)) : asset('images/default-book.svg') }}" 
     alt="{{ $item->title ?? 'Book image' }}">
```

### 3. Logic xử lý đường dẫn ảnh

```php
$imageUrl = $item->image ? 
    (str_starts_with($item->image, 'http') ? 
        $item->image : 
        asset('storage/' . $item->image)
    ) : 
    asset('images/default-book.svg');
```

**Giải thích:**
1. Kiểm tra xem có ảnh không (`$item->image`)
2. Nếu có ảnh:
   - Nếu là URL đầy đủ (bắt đầu bằng 'http'): sử dụng trực tiếp
   - Nếu là đường dẫn tương đối: thêm `storage/` prefix
3. Nếu không có ảnh: sử dụng ảnh mặc định

## Kết quả mong muốn

✅ Ảnh combo hiển thị đúng  
✅ Ảnh sách thường hiển thị đúng  
✅ Hiển thị ảnh mặc định khi không có ảnh  
✅ Xử lý được cả URL đầy đủ và đường dẫn tương đối  

## Cách tránh lỗi tương tự

1. **Nhất quán trong xử lý đường dẫn ảnh**: Luôn sử dụng cùng một logic xử lý cho tất cả loại ảnh
2. **Luôn có ảnh mặc định**: Tạo ảnh mặc định cho mọi trường hợp không có ảnh
3. **Kiểm tra dữ liệu**: Luôn kiểm tra xem dữ liệu có tồn tại trước khi sử dụng
4. **Sử dụng helper function**: Tạo helper function để xử lý đường dẫn ảnh thống nhất

## Ghi chú

- File ảnh được lưu trong `storage/app/public/`
- Đường dẫn trong database: `books/filename.jpg` hoặc `collections/filename.jpg`
- URL truy cập: `domain.com/storage/books/filename.jpg`
- Ảnh mặc định: `domain.com/images/default-book.svg`