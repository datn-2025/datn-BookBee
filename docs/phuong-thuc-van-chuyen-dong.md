# Phương Thức Vận Chuyển Động - Hiển Thị Khi Chọn Địa Chỉ

## Mô Tả Chức Năng

Chức năng này thay đổi cách hiển thị phương thức vận chuyển trên trang checkout:
- **Ẩn phần "Chọn cách thức giao hàng" ban đầu**
- **Chỉ hiển thị khi người dùng chọn địa chỉ** (địa chỉ có sẵn hoặc nhập địa chỉ mới đầy đủ)
- **Bao gồm tùy chọn "Nhận hàng trực tiếp"** và phương thức vận chuyển GHN (chỉ giao hàng tiết kiệm)

## Luồng Hoạt Động

### 1. Trạng Thái Ban Đầu
- Phần phương thức vận chuyển được ẩn (`display: none`)
- Người dùng chỉ thấy phần chọn địa chỉ và phương thức thanh toán

### 2. Khi Chọn Địa Chỉ Có Sẵn
- Người dùng chọn một địa chỉ từ danh sách địa chỉ đã lưu
- JavaScript tự động hiển thị phần phương thức vận chuyển
- Hiển thị các tùy chọn: Nhận hàng trực tiếp, Giao hàng tiết kiệm

### 3. Khi Nhập Địa Chỉ Mới
- Người dùng điền đầy đủ: Tỉnh/Thành phố, Quận/Huyện, Phường/Xã, Địa chỉ cụ thể
- JavaScript kiểm tra real-time và hiển thị phương thức vận chuyển khi đầy đủ

## Cấu Trúc Code

### HTML Structure

```html
<!-- Phương thức vận chuyển - Ẩn ban đầu -->
<div class="shipping-section" style="display: none;">
    <div class="flex items-center gap-3 mb-4">
        <h3>Phương thức vận chuyển</h3>
        <p>Chọn dịch vụ giao hàng</p>
    </div>
    
    <!-- Fallback options -->
    <div id="shipping-services-fallback" class="hidden">
        <!-- Nhận hàng trực tiếp -->
        <label>
            <input type="radio" name="delivery_method" value="pickup">
            <span>Nhận hàng trực tiếp</span>
            <span class="badge">FREE</span>
        </label>
        
        <!-- Giao hàng tiết kiệm -->
        <label>
            <input type="radio" name="shipping_method" value="2" checked>
            <span>Giao hàng tiết kiệm</span>
        </label>
        

    </div>
</div>
```

### JavaScript Logic

```javascript
// Ẩn phương thức vận chuyển ban đầu
const shippingSection = document.querySelector('.shipping-section');
if (shippingSection) {
    shippingSection.style.display = 'none';
}

// Hiển thị phương thức vận chuyển
function showShippingMethods() {
    if (shippingSection) {
        shippingSection.style.display = 'block';
        const fallbackOptions = document.getElementById('shipping-services-fallback');
        if (fallbackOptions) {
            fallbackOptions.classList.remove('hidden');
        }
        const loadingElement = document.getElementById('shipping-services-loading');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }
}

// Sự kiện chọn địa chỉ có sẵn
document.querySelectorAll('input[name="address_id"]').forEach(input => {
    input.addEventListener('change', function() {
        if (this.checked) {
            showShippingMethods();
        }
    });
});

// Kiểm tra địa chỉ mới đầy đủ
function checkNewAddressComplete() {
    const city = document.getElementById('tinh')?.value;
    const district = document.getElementById('quan')?.value;
    const ward = document.getElementById('phuong')?.value;
    const detail = document.getElementById('new_address_detail')?.value?.trim();
    
    if (city && district && ward && detail) {
        showShippingMethods();
    }
}

// Sự kiện cho form địa chỉ mới
['tinh', 'quan', 'phuong', 'new_address_detail'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('change', checkNewAddressComplete);
        element.addEventListener('input', checkNewAddressComplete);
    }
});

// Xử lý chọn phương thức vận chuyển
document.addEventListener('change', function(e) {
    if (e.target.name === 'delivery_method') {
        document.getElementById('form_hidden_delivery_method').value = e.target.value;
        if (e.target.value === 'pickup') {
            document.getElementById('shipping-fee').textContent = '0đ';
            document.getElementById('form_hidden_shipping_fee').value = 0;
        }
        updateTotal();
    }
    
    if (e.target.name === 'shipping_method') {
        document.getElementById('form_hidden_shipping_method').value = e.target.value;
        document.getElementById('form_hidden_delivery_method').value = 'delivery';
        updateTotal();
    }
});
```

## Hidden Fields

```html
<input type="hidden" name="delivery_method" id="form_hidden_delivery_method" value="delivery">
<input type="hidden" name="shipping_method" id="form_hidden_shipping_method" value="2">
<input type="hidden" name="shipping_fee_applied" id="form_hidden_shipping_fee" value="20000">
```

## Các Phương Thức Vận Chuyển

### 1. Nhận Hàng Trực Tiếp (pickup)
- **Giá trị**: `delivery_method = 'pickup'`
- **Phí ship**: 0đ
- **Màu sắc**: Xanh lá (green)
- **Icon**: Biểu tượng cửa hàng
- **Mô tả**: "Đến cửa hàng nhận"

### 2. Giao Hàng Tiết Kiệm (shipping_method = 2)
- **Giá trị**: `shipping_method = '2'`, `delivery_method = 'delivery'`
- **Phí ship**: Tính theo API GHN
- **Màu sắc**: Xanh dương (blue)
- **Icon**: Biểu tượng vận chuyển
- **Mô tả**: "3-5 ngày làm việc"
- **Mặc định**: Được chọn sẵn



## Xử Lý Phí Vận Chuyển

### Nhận Hàng Trực Tiếp
```javascript
if (e.target.value === 'pickup') {
    document.getElementById('shipping-fee').textContent = '0đ';
    document.getElementById('form_hidden_shipping_fee').value = 0;
}
```

### Giao Hàng GHN
- Phí ship được tính thông qua API GHN
- Hiển thị "Phí ship sẽ được tính khi chọn địa chỉ" ban đầu
- Cập nhật phí thực tế sau khi có địa chỉ đầy đủ

## Tích Hợp Với Logic Hiện Có

### Form Validation
- Không thay đổi validation backend
- Vẫn kiểm tra `delivery_method` và `shipping_method`
- Vẫn xử lý `address_id` cho địa chỉ có sẵn

### API GHN
- Vẫn tích hợp với API GHN để tính phí ship
- Vẫn sử dụng `service_type_id` (2 = tiết kiệm)
- Vẫn lấy thời gian giao hàng dự kiến

### Đơn Hàng Ebook
- Đơn hàng chỉ có ebook vẫn ẩn toàn bộ phần vận chuyển
- `$hasOnlyEbooks` vẫn hoạt động như cũ

## Lợi Ích

### 1. Trải Nghiệm Người Dùng
- **Giao diện gọn gàng**: Không hiển thị quá nhiều thông tin cùng lúc
- **Luồng logic**: Chọn địa chỉ trước, sau đó mới chọn cách vận chuyển
- **Tương tác tự nhiên**: Hiển thị tùy chọn khi cần thiết

### 2. Tính Năng
- **Linh hoạt**: Hỗ trợ cả nhận tại cửa hàng và giao hàng
- **Tích hợp**: Kết hợp với API GHN và VNPay
- **Responsive**: Hoạt động tốt trên mọi thiết bị

### 3. Hiệu Suất
- **Tải nhanh**: Không tải API GHN ngay từ đầu
- **Tối ưu**: Chỉ hiển thị khi cần thiết
- **Ít lỗi**: Giảm thiểu lỗi do thiếu thông tin địa chỉ

## Kiểm Thử

### Test Case 1: Chọn Địa Chỉ Có Sẵn
1. Vào trang checkout
2. Kiểm tra phần vận chuyển bị ẩn
3. Chọn một địa chỉ có sẵn
4. Kiểm tra phần vận chuyển hiển thị
5. Chọn "Nhận hàng trực tiếp" → Phí ship = 0đ
6. Chọn "Giao hàng tiết kiệm" → Phí ship được tính

### Test Case 2: Nhập Địa Chỉ Mới
1. Vào trang checkout
2. Chuyển sang tab "Địa chỉ mới"
3. Điền từng trường: Tỉnh → Quận → Phường → Địa chỉ cụ thể
4. Kiểm tra phần vận chuyển hiển thị sau khi điền đầy đủ
5. Test các phương thức vận chuyển

### Test Case 3: Đơn Hàng Ebook
1. Thêm chỉ ebook vào giỏ hàng
2. Vào checkout
3. Kiểm tra phần vận chuyển không hiển thị
4. Kiểm tra `delivery_method = 'ebook'`

## Xử Lý Sự Cố

### Lỗi: Phần vận chuyển không hiển thị
**Nguyên nhân**: JavaScript không chạy hoặc selector sai
**Giải pháp**: 
- Kiểm tra console browser
- Kiểm tra class `.shipping-section`
- Kiểm tra event listener

### Lỗi: Phí ship không cập nhật
**Nguyên nhân**: Hidden field không được cập nhật
**Giải pháp**:
- Kiểm tra `form_hidden_shipping_fee`
- Kiểm tra hàm `updateTotal()`
- Kiểm tra API GHN response

### Lỗi: Validation backend
**Nguyên nhân**: Thiếu `delivery_method` hoặc `shipping_method`
**Giải pháp**:
- Kiểm tra hidden fields có giá trị
- Kiểm tra form submit
- Kiểm tra validation rules

## File Liên Quan

- **View**: `resources/views/orders/checkout.blade.php`
- **Controller**: `app/Http/Controllers/OrderController.php`
- **Service**: `app/Services/OrderService.php`
- **API**: `routes/api.php` (GHN endpoints)
- **Migration**: `database/migrations/*_add_delivery_method_to_orders_table.php`

## Cập Nhật Gần Đây

- **[2025-01-15]**: Thay đổi logic hiển thị phương thức vận chuyển
- **[2025-01-15]**: Thêm tùy chọn "Nhận hàng trực tiếp" vào phương thức vận chuyển
- **[2025-01-15]**: Cập nhật JavaScript để hiển thị động khi chọn địa chỉ
- **[2025-01-15]**: Tối ưu trải nghiệm người dùng với luồng chọn địa chỉ → phương thức vận chuyển