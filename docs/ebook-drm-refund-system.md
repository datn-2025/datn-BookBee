# Hệ thống DRM và Hoàn tiền Ebook

## Mô tả chức năng

Hệ thống DRM (Digital Rights Management) và hoàn tiền ebook được thiết kế để:
- Quản lý quyền tải xuống ebook với giới hạn số lần tải
- Theo dõi lịch sử tải xuống của người dùng
- Xử lý yêu cầu hoàn tiền dựa trên trạng thái đã tải hay chưa
- Áp dụng chính sách hoàn tiền linh hoạt (100% nếu chưa tải, 40% nếu đã tải)

## Cấu trúc Database

### Bảng `book_formats` (đã cập nhật)
```sql
-- Thêm các cột DRM
max_downloads INT DEFAULT 5 COMMENT 'Số lần tải tối đa cho ebook'
drm_enabled BOOLEAN DEFAULT TRUE COMMENT 'Bật/tắt DRM cho ebook'
download_expiry_days INT DEFAULT 365 COMMENT 'Số ngày hết hạn tải ebook'
```

### Bảng `ebook_downloads` (mới)
```sql
id CHAR(36) PRIMARY KEY -- UUID
user_id BIGINT UNSIGNED -- FK to users
order_id BIGINT UNSIGNED -- FK to orders
book_format_id BIGINT UNSIGNED -- FK to book_formats
ip_address VARCHAR(45) -- IP address khi tải
user_agent TEXT -- User agent khi tải
downloaded_at TIMESTAMP -- Thời gian tải
created_at TIMESTAMP
updated_at TIMESTAMP
```

## Models

### EbookDownload Model
- **File**: `app/Models/EbookDownload.php`
- **Chức năng**: Quản lý lịch sử tải xuống ebook
- **Relationships**: 
  - `belongsTo(User::class)`
  - `belongsTo(Order::class)`
  - `belongsTo(BookFormat::class)`

### BookFormat Model (cập nhật)
- **File**: `app/Models/BookFormat.php`
- **Thêm methods**:
  - `canUserDownload($user, $order)`: Kiểm tra quyền tải
  - `getRemainingDownloads($user, $order)`: Số lần tải còn lại
  - `downloads()`: Relationship với EbookDownload

## Controllers

### EbookDownloadController (cập nhật)
- **File**: `app/Http/Controllers/EbookDownloadController.php`
- **Chức năng**: Xử lý tải xuống ebook với DRM
- **Methods**:
  - `download($formatId)`: Tải ebook với kiểm tra DRM
  - `view($formatId)`: Xem ebook online
  - `downloadSample($formatId)`: Tải mẫu (không cần auth)
  - `viewSample($formatId)`: Xem mẫu online

### EbookRefundController (mới)
- **File**: `app/Http/Controllers/EbookRefundController.php`
- **Chức năng**: Xử lý yêu cầu hoàn tiền ebook
- **Methods**:
  - `show($order)`: Hiển thị form hoàn tiền
  - `store($order)`: Tạo yêu cầu hoàn tiền
  - `preview($order)`: Preview số tiền hoàn trả (AJAX)

## Services

### EbookRefundService (mới)
- **File**: `app/Services/EbookRefundService.php`
- **Chức năng**: Logic xử lý hoàn tiền ebook
- **Methods**:
  - `calculateRefundAmount($order, $user)`: Tính số tiền hoàn
  - `createEbookRefundRequest($order, $user, $reason, $details)`: Tạo yêu cầu
  - `canRefundEbook($order, $user)`: Kiểm tra điều kiện hoàn tiền

## Views

### Ebook Refund Form
- **File**: `resources/views/ebook-refund/show.blade.php`
- **Chức năng**: Form yêu cầu hoàn tiền ebook
- **Features**:
  - Hiển thị thông tin đơn hàng
  - Tính toán số tiền hoàn dựa trên trạng thái tải
  - Form nhập lý do hoàn tiền
  - Hiển thị chính sách hoàn tiền

### Order Details (cập nhật)
- **File**: `resources/views/clients/account/order-details.blade.php`
- **Thêm**: Nút "YÊU CẦU HOÀN TIỀN EBOOK" cho đơn hàng có ebook

## Routes

### Ebook Download Routes
```php
Route::prefix('ebook')->name('ebook.')->group(function() {
    // Sample downloads (public)
    Route::get('/sample/download/{formatId}', [EbookDownloadController::class, 'downloadSample']);
    Route::get('/sample/view/{formatId}', [EbookDownloadController::class, 'viewSample']);
    
    // Protected downloads (auth required)
    Route::middleware('auth')->group(function() {
        Route::get('/download/{formatId}', [EbookDownloadController::class, 'download']);
        Route::get('/view/{formatId}', [EbookDownloadController::class, 'view']);
    });
});
```

### Ebook Refund Routes
```php
Route::prefix('ebook-refund')->name('ebook-refund.')->middleware('auth')->group(function() {
    Route::get('/{order}', [EbookRefundController::class, 'show'])->name('show');
    Route::post('/{order}', [EbookRefundController::class, 'store'])->name('store');
    Route::get('/preview/{order}', [EbookRefundController::class, 'preview'])->name('preview');
});
```

## Chính sách DRM

### Giới hạn tải xuống
- **Mặc định**: 5 lần tải cho mỗi ebook
- **Có thể tùy chỉnh**: Admin có thể thay đổi `max_downloads` cho từng format
- **Kiểm tra**: Mỗi lần tải sẽ kiểm tra số lần đã tải vs giới hạn

### Thời hạn tải xuống
- **Mặc định**: 365 ngày kể từ ngày mua
- **Có thể tùy chỉnh**: Admin có thể thay đổi `download_expiry_days`
- **Kiểm tra**: Tính từ `created_at` của order đến thời điểm tải

### Bật/tắt DRM
- **Flag**: `drm_enabled` trong bảng `book_formats`
- **Mặc định**: `true` (bật DRM)
- **Khi tắt**: Không giới hạn số lần tải và thời hạn

## Chính sách hoàn tiền

### Điều kiện hoàn tiền
1. **Đơn hàng đã thanh toán**: `payment_status` = 'Đã Thanh Toán'
2. **Chứa ebook**: Có ít nhất 1 item là ebook
3. **Chưa có yêu cầu hoàn tiền**: Không có `RefundRequest` nào cho đơn hàng này
4. **Trong thời hạn**: Tối đa 7 ngày kể từ ngày đặt hàng
5. **Điều kiện tải xuống**: Ebook chỉ được tải tối đa 1 lần mới có thể hoàn tiền

### Mức hoàn tiền
- **Chưa tải file**: 100% giá trị ebook
- **Đã tải 1 lần**: 40% giá trị ebook
- **Đã tải trên 1 lần**: 0% (Không được hoàn tiền)
- **Tính toán**: Dựa trên bảng `ebook_downloads`

## Cách sử dụng

### Cho Admin
1. **Quản lý DRM settings**:
   - Vào trang chỉnh sửa book format
   - Thiết lập `max_downloads`, `download_expiry_days`, `drm_enabled`

2. **Xem lịch sử tải xuống**:
   - Truy cập bảng `ebook_downloads` để theo dõi

3. **Xử lý yêu cầu hoàn tiền**:
   - Vào trang quản lý `RefundRequest`
   - Xem chi tiết và phê duyệt/từ chối

### Cho User
1. **Tải ebook**:
   - Vào trang chi tiết đơn hàng
   - Click "Tải Xuống" hoặc "Đọc Online"
   - Hệ thống sẽ kiểm tra DRM tự động

2. **Yêu cầu hoàn tiền**:
   - Vào trang chi tiết đơn hàng
   - Click "YÊU CẦU HOÀN TIỀN EBOOK" (nếu đủ điều kiện)
   - Điền form và gửi yêu cầu

## Kết quả mong muốn

### Bảo mật
- ✅ Kiểm soát số lần tải xuống
- ✅ Theo dõi lịch sử truy cập
- ✅ Giới hạn thời gian tải
- ✅ Ngăn chặn chia sẻ không kiểm soát

### Trải nghiệm người dùng
- ✅ Quy trình hoàn tiền rõ ràng
- ✅ Tính toán hoàn tiền tự động
- ✅ Thông báo giới hạn tải xuống
- ✅ Interface thân thiện

### Quản lý
- ✅ Theo dõi hành vi tải xuống
- ✅ Linh hoạt trong chính sách DRM
- ✅ Tự động hóa quy trình hoàn tiền
- ✅ Báo cáo chi tiết

## Migration Commands

```bash
# Chạy migration để tạo bảng và cột mới
php artisan migrate

# Nếu cần rollback
php artisan migrate:rollback --step=2
```

## Testing

### Test Cases
1. **DRM Functionality**:
   - Tải ebook trong giới hạn
   - Tải ebook vượt giới hạn (should fail)
   - Tải ebook hết hạn (should fail)
   - Tải ebook khi DRM disabled (should work)

2. **Refund System**:
   - Yêu cầu hoàn tiền ebook chưa tải (100%)
   - Yêu cầu hoàn tiền ebook đã tải 1 lần (40%)
   - Yêu cầu hoàn tiền ebook đã tải trên 1 lần (0% - should fail)
   - Yêu cầu hoàn tiền ngoài thời hạn (should fail)
   - Yêu cầu hoàn tiền đơn hàng không có ebook (should fail)

3. **Security**:
   - Truy cập ebook không sở hữu (should fail)
   - Truy cập ebook chưa thanh toán (should fail)
   - Download link security

## Lưu ý quan trọng

1. **File Storage**: Ebook files được lưu trong `storage/app/private/ebooks/` (không public)
2. **Security**: Mọi download đều qua controller để kiểm tra quyền
3. **Logging**: Tất cả hoạt động tải xuống được log
4. **UUID**: Sử dụng UUID cho bảng `ebook_downloads` để bảo mật
5. **Validation**: Kiểm tra đầy đủ quyền trước khi cho phép tải

## Tương lai mở rộng

- **Watermark**: Thêm watermark vào file PDF
- **Encryption**: Mã hóa file ebook
- **Analytics**: Báo cáo chi tiết về hành vi tải xuống
- **API**: Tạo API cho mobile app
- **Notification**: Thông báo khi có yêu cầu hoàn tiền mới