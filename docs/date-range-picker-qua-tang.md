# Date Range Picker cho Quà Tặng Sách

## Mô tả chức năng

Chức năng Date Range Picker cho phép người quản trị chọn khoảng thời gian áp dụng khuyến mãi quà tặng kèm theo sách một cách trực quan và dễ sử dụng. Thay vì phải nhập hai trường ngày riêng biệt, người dùng chỉ cần click vào một trường duy nhất để chọn cả ngày bắt đầu và ngày kết thúc.

## Cách sử dụng

### Trong giao diện Admin

1. **Tạo sách mới (Create)**:
   - Vào trang tạo sách mới
   - Tích checkbox "Sách có kèm quà tặng"
   - Điền thông tin quà tặng
   - Click vào trường "Thời gian khuyến mãi quà tặng"
   - Chọn ngày bắt đầu, sau đó chọn ngày kết thúc
   - Hệ thống sẽ tự động lưu khoảng thời gian đã chọn

2. **Chỉnh sửa sách (Edit)**:
   - Vào trang chỉnh sửa sách
   - Nếu sách đã có quà tặng, khoảng thời gian hiện tại sẽ được hiển thị
   - Click vào trường để thay đổi khoảng thời gian mới

### Tính năng chính

- **Chọn khoảng ngày**: Click một lần để chọn ngày bắt đầu, click lần thứ hai để chọn ngày kết thúc
- **Hiển thị trực quan**: Khoảng ngày được hiển thị dưới dạng "YYYY-MM-DD to YYYY-MM-DD"
- **Validation tự động**: Hệ thống tự động kiểm tra tính hợp lệ của khoảng ngày
- **Responsive**: Giao diện tương thích với mọi thiết bị

## Mã nguồn

### HTML Structure

```html
<div class="col-12">
    <label class="form-label fw-medium">Thời gian khuyến mãi quà tặng</label>
    <input type="text" class="form-control @error('gift_date_range') is-invalid @enderror" 
           id="gift_date_range" name="gift_date_range" 
           placeholder="Chọn khoảng thời gian khuyến mãi...">
    
    <!-- Hidden inputs để lưu giá trị ngày -->
    <input type="hidden" id="gift_start_date" name="start_date">
    <input type="hidden" id="gift_end_date" name="end_date">
    
    @error('gift_date_range')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    <div class="form-text">
        <i class="ri-information-line me-1"></i>
        Chọn khoảng thời gian áp dụng khuyến mãi quà tặng. Nhấp vào ô để chọn ngày bắt đầu và kết thúc.
    </div>
</div>
```

### JavaScript Implementation

```javascript
// Initialize gift date range picker
const giftDateRangePicker = document.getElementById('gift_date_range');
if (giftDateRangePicker) {
    flatpickr(giftDateRangePicker, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        onChange: function(selectedDates, dateStr, instance) {
            const startInput = document.getElementById('gift_start_date');
            const endInput = document.getElementById('gift_end_date');
            if (selectedDates.length === 2) {
                startInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                endInput.value = instance.formatDate(selectedDates[1], 'Y-m-d');
            } else {
                startInput.value = '';
                endInput.value = '';
            }
        }
    });
}

// Đảm bảo khi submit form luôn lấy lại giá trị nếu user không chọn lại ngày
const form = document.querySelector('form');
if (form) {
    form.addEventListener('submit', function() {
        const giftDateRange = document.getElementById('gift_date_range');
        if (giftDateRange && giftDateRange.value && giftDateRange.value.includes(' to ')) {
            const parts = giftDateRange.value.split(' to ');
            document.getElementById('gift_start_date').value = parts[0].trim();
            document.getElementById('gift_end_date').value = parts[1].trim();
        }
    });
}
```

### Database Structure

```php
// Migration: book_gifts table
Schema::create('book_gifts', function (Blueprint $table) {
    $table->id();
    $table->uuid('book_id');
    $table->string('gift_name');
    $table->text('gift_description')->nullable();
    $table->string('gift_image')->nullable();
    $table->integer('quantity')->default(0);
    $table->date('start_date')->nullable(); // Ngày bắt đầu
    $table->date('end_date')->nullable();   // Ngày kết thúc
    $table->timestamps();
    
    $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
});
```

## Kết quả mong muốn

1. **Giao diện thân thiện**: Người dùng có thể dễ dàng chọn khoảng thời gian mà không cần nhập thủ công
2. **Dữ liệu chính xác**: Hệ thống tự động chuyển đổi và lưu trữ ngày bắt đầu và ngày kết thúc
3. **Validation hiệu quả**: Kiểm tra tính hợp lệ của khoảng ngày được chọn
4. **Tương thích**: Hoạt động tốt trên mọi trình duyệt và thiết bị
5. **Nhất quán**: Sử dụng cùng thư viện và cách tiếp cận như phần combo sách

## Lưu ý kỹ thuật

- Sử dụng thư viện **Flatpickr** để tạo date range picker
- Dữ liệu được lưu vào hai trường riêng biệt trong database: `start_date` và `end_date`
- Trường hiển thị (`gift_date_range`) chỉ để giao diện, không lưu vào database
- Sử dụng hidden inputs để đảm bảo dữ liệu được submit chính xác
- Event listener trên form submit để đảm bảo dữ liệu luôn được cập nhật