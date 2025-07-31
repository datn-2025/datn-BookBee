# Sửa Logic Status Priority Cho Trang Chi Tiết Sách (Client)

## Mô tả
Hoàn thiện việc triển khai logic ưu tiên status cho trang chi tiết sách client (`/resources/views/clients/show.blade.php`), đảm bảo tính nhất quán với các trang khác đã được sửa.

## Vấn đề trước khi sửa
Trang chi tiết sách client vẫn sử dụng logic cũ:
- Chỉ dựa vào `book_formats.stock` để xác định status hiển thị
- Sử dụng giá trị hardcode (-1, -2, 0, >0) thay vì kiểm tra `books.status` thực tế
- Logic JavaScript không đồng bộ với logic PHP
- Validation khi thêm vào giỏ hàng chưa tuân theo status priority

## Giải pháp triển khai

### 1. Cập nhật PHP Logic (Server-side Rendering)

**File:** `resources/views/clients/show.blade.php`

**Thay đổi logic hiển thị status ban đầu:**
```php
// Cũ: Chỉ kiểm tra stock
if ($isEbook) {
    $statusText = 'EBOOK - CÓ SẴN';
} elseif ($defaultStock > 0) {
    $statusText = 'CÒN HÀNG';
} elseif ($defaultStock === 0) {
    $statusText = 'HẾT HÀNG';
} // ...

// Mới: Ưu tiên books.status trước
switch ($book->status) {
    case 'Ngừng Kinh Doanh':
        $statusText = 'NGƯNG KINH DOANH';
        // ...
        break;
    case 'Sắp Ra Mắt':
        $statusText = 'SẮP RA MẮT';
        // ...
        break;
    case 'Hết Hàng Tồn Kho':
        $statusText = 'HẾT HÀNG TỒN KHO';
        // ...
        break;
    case 'Còn Hàng':
    default:
        // Chỉ khi status = 'Còn Hàng' mới kiểm tra stock
        if ($isEbook) {
            $statusText = 'EBOOK - CÓ SẴN';
        } elseif ($defaultStock == 0) {
            $statusText = 'HẾT HÀNG (Stock)';
        } elseif ($defaultStock >= 1 && $defaultStock <= 9) {
            $statusText = 'SẮP HẾT HÀNG';
        } elseif ($defaultStock >= 10) {
            $statusText = 'CÒN HÀNG';
        }
        break;
}
```

### 2. Thêm Data Attribute cho JavaScript

**Thay đổi:**
```html
<!-- Cũ -->
<span id="bookPrice" data-base-price="{{ $defaultPrice }}">

<!-- Mới -->
<span id="bookPrice" data-base-price="{{ $defaultPrice }}" data-book-status="{{ $book->status }}">
```

### 3. Cập nhật JavaScript Logic (Client-side Updates)

**Thay đổi function `updatePriceAndStock()`:**

**Elements mới:**
```javascript
// Cũ: Sử dụng bookStock element không tồn tại
const bookStockElement = document.getElementById('bookStock');

// Mới: Sử dụng đúng elements có trong HTML
const stockBadgeElement = document.getElementById('stockBadge');
const stockDotElement = document.getElementById('stockDot'); 
const stockTextElement = document.getElementById('stockText');
```

**Logic status mới:**
```javascript
// Lấy book.status từ data attribute
const bookStatus = bookPriceElement.dataset.bookStatus || 'Còn Hàng';

// Priority 1: Check books.status first
switch (bookStatus) {
    case 'Ngừng Kinh Doanh':
        stockText = 'NGƯNG KINH DOANH';
        badgeClass = 'bg-gray-100 text-gray-700 border-gray-300';
        dotClass = 'bg-gray-500';
        break;
    case 'Sắp Ra Mắt':
        stockText = 'SẮP RA MẮT';
        badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
        dotClass = 'bg-yellow-500';
        break;
    case 'Hết Hàng Tồn Kho':
        stockText = 'HẾT HÀNG TỒN KHO';
        badgeClass = 'bg-red-50 text-red-700 border-red-200';
        dotClass = 'bg-red-500';
        break;
    case 'Còn Hàng':
    default:
        // Priority 2: Only when status = 'Còn Hàng', check stock levels
        if (stock == 0) {
            stockText = 'HẾT HÀNG (Stock)';
            badgeClass = 'bg-red-50 text-red-700 border-red-200';
            dotClass = 'bg-red-500';
        } else if (stock >= 1 && stock <= 9) {
            stockText = 'SẮP HẾT HÀNG';
            badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
            dotClass = 'bg-yellow-500';
        } else if (stock >= 10) {
            stockText = 'CÒN HÀNG';
            badgeClass = 'bg-green-50 text-green-700 border-green-200';
            dotClass = 'bg-green-500';
        }
        break;
}
```

### 4. Cập nhật Validation Logic

**Function `addToCart()` - validation mới:**
```javascript
// Cũ: Chỉ kiểm tra stock values
if (stock <= 0 || stock === -1 || stock === -2) {
    toastr.error('Sản phẩm này hiện không có sẵn để đặt hàng!');
    return;
}

// Mới: Ưu tiên books.status
const bookStatus = bookPriceElement?.dataset.bookStatus || 'Còn Hàng';

// Priority 1: Check books.status first
if (bookStatus === 'Ngừng Kinh Doanh' || bookStatus === 'Sắp Ra Mắt' || bookStatus === 'Hết Hàng Tồn Kho') {
    toastr.error('Sản phẩm này hiện không có sẵn để đặt hàng!');
    return;
}

// Priority 2: Only when status = 'Còn Hàng', check stock levels
if (bookStatus === 'Còn Hàng' && stock <= 0) {
    toastr.error('Sản phẩm này hiện hết hàng!');
    return;
}
```

### 5. Cập nhật Điều Kiện Hiển Thị Số Lượng

**PHP template:**
```php
<!-- Cũ -->
@if(($defaultStock > 0 || $isEbook) && $defaultStock !== -1 && $defaultStock !== -2)

<!-- Mới -->
@if(
    ($book->status === 'Còn Hàng' && $defaultStock > 0) || 
    $isEbook
)
```

**JavaScript:**
```javascript
// Chỉ hiển thị số lượng khi status = 'Còn Hàng' và có stock
if (stock > 0 && bookStatus === 'Còn Hàng') {
    stockQuantityDisplay.style.display = 'inline';
} else {
    stockQuantityDisplay.style.display = 'none';
}
```

## Kết quả đạt được

### Status Priority Logic được áp dụng nhất quán:
1. **Priority 1:** `books.status` được kiểm tra trước **cho cả ebooks và sách vật lý**
   - `Ngừng Kinh Doanh` → Hiển thị "NGƯNG KINH DOANH" 
   - `Sắp Ra Mắt` → Hiển thị "SẮP RA MẮT" 
   - `Hết Hàng Tồn Kho` → Hiển thị "HẾT HÀNG TỒN KHO"

2. **Priority 2:** Chỉ khi `books.status = 'Còn Hàng'` mới hiển thị chi tiết
   - **Ebooks:** Hiển thị "EBOOK - CÓ SẴN"
   - **Sách vật lý:** Kiểm tra `book_formats.stock`
     - `stock = 0` → "HẾT HÀNG (Stock)"
     - `1 ≤ stock ≤ 9` → "SẮP HẾT HÀNG"
     - `stock ≥ 10` → "CÒN HÀNG"

### Đã sửa vấn đề ebooks:
- ✅ **Trước:** Ebooks luôn hiển thị "CÓ SẴN" bất kể `books.status`
- ✅ **Sau:** Ebooks cũng tuân theo status priority như sách vật lý

### Tính nhất quán:
- ✅ Admin Books Index: Đã sửa
- ✅ Front-end Books Index: Đã sửa  
- ✅ **Client Book Detail Page: Đã sửa** ← **Hoàn thành**

### UX Improvements:
- Hiển thị status chính xác theo database
- Validation đúng khi thêm vào giỏ hàng
- Logic JavaScript đồng bộ với PHP
- Màu sắc và UI consistent

## Files đã thay đổi
- ✅ `/resources/views/clients/show.blade.php` - Cập nhật toàn bộ logic status priority

## Kiểm tra
1. Truy cập trang chi tiết sách
2. Thay đổi định dạng sách → Status cập nhật đúng theo logic mới
3. Thử thêm vào giỏ hàng với các trạng thái khác nhau → Validation hoạt động đúng
4. Kiểm tra hiển thị số lượng còn lại → Chỉ hiện khi status = "Còn Hàng"

## Ghi chú kỹ thuật
- Sử dụng `data-book-status` attribute để truyền data từ PHP sang JavaScript
- Elements IDs được cập nhật cho đúng với HTML structure thực tế
- Logic validation được tách riêng cho rõ ràng
- Tương thích với toastr notifications và fallback alerts
