# Sửa Lỗi JavaScript Trong Trang Checkout

## Mô tả vấn đề
Trang checkout gặp các lỗi JavaScript sau:
1. `updateOrderTotal is not defined` - Hàm không được định nghĩa
2. `GET http://127.0.0.1:8000/test-broadcast.js net::ERR_ABORTED 404 (Not Found)` - File không tồn tại
3. Không thể lấy danh sách quận/huyện khi chọn tỉnh/thành phố

## Nguyên nhân

### 1. Lỗi hàm updateOrderTotal
- Trong code JavaScript của trang checkout, có các lời gọi `updateOrderTotal()` nhưng hàm này không được định nghĩa
- Hàm thực tế được sử dụng là `updateTotal()`

### 2. Lỗi file test-broadcast.js
- File `test-broadcast.js` được tham chiếu trong `app.blade.php` nhưng không tồn tại trong thư mục `public/`
- Gây ra lỗi 404 khi tải trang

### 3. Lỗi không lấy được quận/huyện
- Do các lỗi JavaScript trên làm gián đoạn việc thực thi code
- Các hàm `loadDistricts()` và `loadWards()` không được gọi đúng cách

## Giải pháp đã áp dụng

### 1. Thay thế tên hàm
**File**: `resources/views/orders/checkout.blade.php`

```javascript
// Trước (LỖI)
updateOrderTotal();

// Sau (ĐÚNG)
updateTotal();
```

**Các vị trí đã sửa**:
- Dòng 1742: Trong hàm `updateShippingFeeDisplay()`
- Dòng 1893: Trong hàm `updateShippingFeeDisplay()`
- Dòng 1974: Trong hàm `resetShippingInfo()`

### 2. Xóa tham chiếu file không tồn tại
**File**: `resources/views/layouts/app.blade.php`

```html
<!-- Trước (LỖI) -->
<script src="{{ asset('test-broadcast.js') }}"></script>

<!-- Sau (ĐÚNG) -->
<!-- Test broadcast script removed - file not found -->
```

## Cấu trúc hàm JavaScript chính

### Hàm updateTotal()
```javascript
function updateTotal() {
    console.log('updateTotal called');
    const subtotalValue = {{ $subtotal }};
    const shippingFeeText = document.getElementById('shipping-fee').textContent.trim();
    const shippingFee = parseFloat(shippingFeeText.replace(/\./g, "")) || 0;
    
    let total = subtotalValue - discountValue + shippingFee;
    total = Math.max(0, total);
    document.getElementById('total-amount').textContent = `${number_format(total)}đ`;
    
    // Cập nhật hidden fields
    document.getElementById('form_hidden_total_amount').value = total;
    document.getElementById('form_hidden_discount_amount').value = discountValue;
    document.getElementById('form_hidden_shipping_fee').value = shippingFee;
}
```

### Các hàm GHN API
- `loadDistricts(provinceId)` - Tải danh sách quận/huyện
- `loadWards(districtId)` - Tải danh sách phường/xã
- `loadShippingServices(districtId)` - Tải dịch vụ vận chuyển
- `calculateShippingFeeWithService(districtId, wardCode)` - Tính phí vận chuyển

## Kiểm tra sau khi sửa

### 1. Kiểm tra Console
- Mở Developer Tools (F12)
- Vào tab Console
- Không còn lỗi `updateOrderTotal is not defined`
- Không còn lỗi 404 cho `test-broadcast.js`

### 2. Kiểm tra chức năng
- Chọn tỉnh/thành phố → Danh sách quận/huyện được tải
- Chọn quận/huyện → Danh sách phường/xã được tải
- Chọn phường/xã → Phí vận chuyển được tính
- Tổng tiền được cập nhật đúng

### 3. Kiểm tra Network
- Các API call đến `/api/ghn/*` hoạt động bình thường
- Không có request 404 nào

## Lưu ý quan trọng

### 1. Tên hàm nhất quán
- Luôn sử dụng `updateTotal()` thay vì `updateOrderTotal()`
- Kiểm tra tất cả file JavaScript trước khi deploy

### 2. Quản lý file static
- Không tham chiếu đến file không tồn tại
- Sử dụng `asset()` helper để đảm bảo đường dẫn đúng

### 3. Error handling
- Luôn có try-catch cho các API call
- Hiển thị fallback UI khi API lỗi

## Troubleshooting

### Nếu vẫn gặp lỗi JavaScript
1. Xóa cache trình duyệt (Ctrl+Shift+R)
2. Kiểm tra file `checkout.blade.php` có syntax error không
3. Đảm bảo tất cả hàm được định nghĩa trước khi gọi

### Nếu không tải được quận/huyện
1. Kiểm tra API routes trong `routes/api.php`
2. Kiểm tra GhnController và GhnService
3. Kiểm tra CSRF token trong meta tag

### Nếu phí vận chuyển không cập nhật
1. Kiểm tra hidden field `form_hidden_shipping_fee`
2. Kiểm tra hàm `updateTotal()` được gọi đúng
3. Kiểm tra API GHN shipping-fee

## Kết luận
Việc sửa lỗi JavaScript này đảm bảo:
- Trang checkout hoạt động mượt mà
- Không có lỗi console
- Chức năng chọn địa chỉ và tính phí vận chuyển hoạt động đúng
- Tổng tiền được cập nhật chính xác