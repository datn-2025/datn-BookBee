# Quản Lý Sách Đặt Trước - Admin

## Mô tả chức năng

Chức năng quản lý sách đặt trước trong admin cho phép quản trị viên:
- Xem danh sách tất cả đơn đặt trước
- Tạo đơn đặt trước mới cho khách hàng
- Xem chi tiết đơn đặt trước
- Cập nhật trạng thái đơn đặt trước
- Chuyển đổi đơn đặt trước thành đơn hàng chính thức
- Xóa đơn đặt trước đã hủy
- Cập nhật hàng loạt trạng thái
- Xuất dữ liệu ra file CSV

## Cấu trúc file

### Controller
- **File**: `app/Http/Controllers/Admin/AdminPreorderController.php`
- **Chức năng**: Xử lý tất cả logic quản lý đơn đặt trước

### Views
- **Danh sách**: `resources/views/admin/preorders/index.blade.php`
- **Chi tiết**: `resources/views/admin/preorders/show.blade.php`
- **Tạo mới**: `resources/views/admin/preorders/create.blade.php`

### API Controller
- **File**: `app/Http/Controllers/Api/LocationController.php`
- **Chức năng**: Cung cấp API để lấy danh sách quận/huyện và phường/xã

## Các tính năng chính

### 1. Danh sách đơn đặt trước (`index`)
- Hiển thị thống kê tổng quan (tổng số, theo trạng thái)
- Bộ lọc theo:
  - Trạng thái đơn hàng
  - Sách
  - Khoảng thời gian
  - Tìm kiếm theo tên, email, số điện thoại
- Phân trang
- Các thao tác:
  - Xem chi tiết
  - Cập nhật trạng thái
  - Chuyển thành đơn hàng
  - Xóa (chỉ đơn đã hủy)
  - Cập nhật hàng loạt

### 2. Tạo đơn đặt trước mới (`create`, `store`)
- Chọn sách hỗ trợ đặt trước
- Chọn định dạng sách (Ebook/Physical)
- Nhập thông tin khách hàng:
  - Khách lẻ hoặc thành viên có sẵn
  - Thông tin liên hệ
  - Địa chỉ giao hàng (nếu không phải Ebook)
- Tự động tính toán giá tiền
- Validation đầy đủ

### 3. Chi tiết đơn đặt trước (`show`)
- Hiển thị đầy đủ thông tin đơn hàng
- Timeline trạng thái
- Form cập nhật trạng thái
- Thông tin khách hàng và địa chỉ
- Nút chuyển đổi thành đơn hàng (nếu sách đã phát hành)

### 4. Cập nhật trạng thái (`updateStatus`)
- Các trạng thái: pending, confirmed, processing, shipped, delivered, cancelled
- Tự động cập nhật timestamp tương ứng
- Gửi email thông báo cho khách hàng
- Ghi log lỗi nếu có

### 5. Chuyển đổi thành đơn hàng (`convertToOrder`)
- Kiểm tra sách đã phát hành
- Kiểm tra trạng thái hợp lệ
- Tạo Order và OrderItem mới
- Cập nhật trạng thái preorder
- Chuyển hướng đến trang chi tiết đơn hàng

### 6. Xóa đơn đặt trước (`destroy`)
- Chỉ cho phép xóa đơn đã hủy
- Xóa vĩnh viễn khỏi database

### 7. Cập nhật hàng loạt (`bulkUpdateStatus`)
- Chọn nhiều đơn đặt trước
- Cập nhật trạng thái cùng lúc
- Validation danh sách ID

### 8. Xuất dữ liệu (`export`)
- Xuất ra file CSV
- Áp dụng các bộ lọc giống như trang danh sách
- Bao gồm tất cả thông tin quan trọng

## Routes

```php
// Admin Preorders Routes
Route::prefix('admin/preorders')->name('admin.preorders.')->group(function () {
    Route::get('/', [AdminPreorderController::class, 'index'])->name('index');
    Route::get('/create', [AdminPreorderController::class, 'create'])->name('create');
    Route::post('/', [AdminPreorderController::class, 'store'])->name('store');
    Route::get('/export', [AdminPreorderController::class, 'export'])->name('export');
    Route::get('/{preorder}', [AdminPreorderController::class, 'show'])->name('show');
    Route::patch('/{preorder}/status', [AdminPreorderController::class, 'updateStatus'])->name('update-status');
    Route::post('/{preorder}/convert-to-order', [AdminPreorderController::class, 'convertToOrder'])->name('convert-to-order');
    Route::delete('/{preorder}', [AdminPreorderController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-update-status', [AdminPreorderController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
});

// API Routes for Location
Route::get('/api/districts/{provinceId}', [LocationController::class, 'getDistricts']);
Route::get('/api/wards/{districtId}', [LocationController::class, 'getWards']);
```

## Validation Rules

### Tạo đơn đặt trước mới:
```php
[
    'book_id' => 'required|exists:books,id',
    'book_format_id' => 'required|exists:book_formats,id',
    'quantity' => 'required|integer|min:1',
    'customer_name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'phone' => 'required|string|max:20',
    'user_id' => 'nullable|exists:users,id',
    'province_id' => 'nullable|exists:provinces,id',
    'district_id' => 'nullable|exists:districts,id',
    'ward_id' => 'nullable|exists:wards,id',
    'address' => 'nullable|string|max:500',
    'notes' => 'nullable|string|max:1000',
    'status' => 'required|in:pending,confirmed',
    'expected_delivery_date' => 'nullable|date|after:today'
]
```

### Cập nhật trạng thái:
```php
[
    'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
    'notes' => 'nullable|string|max:1000'
]
```

## JavaScript Features

### Form tạo đơn đặt trước:
- Dynamic loading của book formats khi chọn sách
- Tự động tính toán tổng tiền
- Ẩn/hiện form địa chỉ cho Ebook
- Auto-fill thông tin khi chọn thành viên
- AJAX loading cho districts/wards

### Trang danh sách:
- Checkbox selection cho bulk actions
- Modal xác nhận cho các thao tác quan trọng
- Real-time search và filtering

## Models liên quan

- **Preorder**: Model chính
- **Book**: Sách được đặt trước
- **BookFormat**: Định dạng sách
- **User**: Khách hàng (nullable)
- **Province, District, Ward**: Địa chỉ giao hàng
- **Order, OrderItem**: Cho chức năng chuyển đổi

## Email Notifications

- Gửi email khi cập nhật trạng thái đơn đặt trước
- Sử dụng `PreorderStatusUpdate` Mailable
- Xử lý lỗi gửi email và ghi log

## Security & Permissions

- Tất cả routes đều trong middleware admin
- Validation đầy đủ cho tất cả input
- Transaction database cho các thao tác quan trọng
- Log lỗi chi tiết

## Cách sử dụng

1. **Truy cập trang quản lý**: `/admin/preorders`
2. **Tạo đơn mới**: Click "Tạo đơn đặt trước" → Điền form → Submit
3. **Xem chi tiết**: Click vào ID đơn hàng hoặc "Xem chi tiết"
4. **Cập nhật trạng thái**: Trong trang chi tiết hoặc dropdown actions
5. **Chuyển thành đơn hàng**: Khi sách đã phát hành và đơn ở trạng thái phù hợp
6. **Xuất dữ liệu**: Click "Xuất Excel" với các bộ lọc mong muốn

## Kết quả mong muốn

- Admin có thể quản lý toàn bộ quy trình đặt trước sách
- Giao diện thân thiện, dễ sử dụng
- Tự động hóa các quy trình (tính tiền, gửi email, cập nhật trạng thái)
- Báo cáo và thống kê chi tiết
- Tích hợp mượt mà với hệ thống đơn hàng hiện có