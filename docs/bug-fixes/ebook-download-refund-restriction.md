# Chặn Tải Ebook Khi Đơn Hàng Đang Hoàn Tiền

## Vấn đề
Trước đây, người dùng vẫn có thể tải ebook ngay cả khi đơn hàng đang trong quá trình hoàn tiền hoặc đã được hoàn tiền. Điều này tạo ra lỗ hổng trong hệ thống bảo vệ bản quyền và có thể bị lạm dụng.

## Giải pháp
Thêm logic kiểm tra trạng thái hoàn tiền vào `EbookDownloadController` để ngăn chặn việc tải và xem ebook khi:

1. **Trạng thái thanh toán** là "Đang Hoàn Tiền" hoặc "Đã Hoàn Tiền"
2. **Có yêu cầu hoàn tiền** đang chờ xử lý (status: `pending` hoặc `processing`)

## Thay đổi Code

### File: `app/Http/Controllers/EbookDownloadController.php`

#### 1. Method `download()` - Dòng 47-61
```php
// Kiểm tra trạng thái hoàn tiền - Không cho phép tải ebook khi đang hoàn tiền
if ($order->paymentStatus && in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền'])) {
    abort(403, 'Không thể tải ebook khi đơn hàng đang trong quá trình hoàn tiền hoặc đã được hoàn tiền.');
}

// Kiểm tra có yêu cầu hoàn tiền đang chờ xử lý không
$hasActiveRefundRequest = \App\Models\RefundRequest::where('order_id', $order->id)
    ->whereIn('status', ['pending', 'processing'])
    ->exists();
    
if ($hasActiveRefundRequest) {
    abort(403, 'Không thể tải ebook khi có yêu cầu hoàn tiền đang được xử lý.');
}
```

#### 2. Method `view()` - Dòng 187-201
```php
// Kiểm tra trạng thái hoàn tiền - Không cho phép xem ebook khi đang hoàn tiền
if ($order->paymentStatus && in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền'])) {
    abort(403, 'Không thể xem ebook khi đơn hàng đang trong quá trình hoàn tiền hoặc đã được hoàn tiền.');
}

// Kiểm tra có yêu cầu hoàn tiền đang chờ xử lý không
$hasActiveRefundRequest = \App\Models\RefundRequest::where('order_id', $order->id)
    ->whereIn('status', ['pending', 'processing'])
    ->exists();
    
if ($hasActiveRefundRequest) {
    abort(403, 'Không thể xem ebook khi có yêu cầu hoàn tiền đang được xử lý.');
}
```

## Logic Kiểm tra

### Các trường hợp bị chặn:

1. **Trạng thái thanh toán "Đang Hoàn Tiền"**
   - Đơn hàng đang trong quá trình hoàn tiền
   - HTTP 403: "Không thể tải/xem ebook khi đơn hàng đang trong quá trình hoàn tiền hoặc đã được hoàn tiền."

2. **Trạng thái thanh toán "Đã Hoàn Tiền"**
   - Đơn hàng đã được hoàn tiền hoàn tất
   - HTTP 403: "Không thể tải/xem ebook khi đơn hàng đang trong quá trình hoàn tiền hoặc đã được hoàn tiền."

3. **Có yêu cầu hoàn tiền đang chờ xử lý**
   - RefundRequest với status: `pending` hoặc `processing`
   - HTTP 403: "Không thể tải/xem ebook khi có yêu cầu hoàn tiền đang được xử lý."

### Các trường hợp được phép:

1. **Trạng thái thanh toán "Đã Thanh Toán"** và không có yêu cầu hoàn tiền
2. **Yêu cầu hoàn tiền đã bị từ chối** (status: `rejected`)
3. **Yêu cầu hoàn tiền đã hoàn thành** nhưng trạng thái thanh toán chưa cập nhật

## Kết quả Test

### Test Case 1: Trạng thái bình thường
```
✅ Trạng thái: Đã Thanh Toán
✅ Có thể tải ebook: YES
```

### Test Case 2: Có yêu cầu hoàn tiền pending
```
✅ Tạo RefundRequest với status: pending
🔒 Có thể tải ebook: NO (có yêu cầu hoàn tiền pending)
```

### Test Case 3: Trạng thái "Đang Hoàn Tiền"
```
✅ Cập nhật trạng thái: Đang Hoàn Tiền
🔒 Có thể tải ebook: NO (đang hoàn tiền)
```

### Test Case 4: Trạng thái "Đã Hoàn Tiền"
```
✅ Cập nhật trạng thái: Đã Hoàn Tiền
🔒 Có thể tải ebook: NO (đã hoàn tiền)
```

## Luồng Hoạt động

### Khi người dùng cố gắng tải ebook:

1. **Kiểm tra đăng nhập** ✅
2. **Kiểm tra định dạng file** ✅
3. **Kiểm tra file tồn tại** ✅
4. **Kiểm tra quyền sở hữu** ✅
5. **🆕 Kiểm tra trạng thái hoàn tiền** ⚠️
   - Nếu đang hoàn tiền → Chặn (HTTP 403)
   - Nếu đã hoàn tiền → Chặn (HTTP 403)
6. **🆕 Kiểm tra yêu cầu hoàn tiền** ⚠️
   - Nếu có pending/processing → Chặn (HTTP 403)
7. **Kiểm tra DRM** ✅
8. **Cho phép tải** ✅

## Lợi ích

### Bảo vệ bản quyền
- ✅ Ngăn chặn tải ebook sau khi yêu cầu hoàn tiền
- ✅ Tránh lạm dụng: tải ebook rồi yêu cầu hoàn tiền
- ✅ Bảo vệ quyền lợi nhà xuất bản

### Tính nhất quán
- ✅ Logic áp dụng cho cả `download()` và `view()`
- ✅ Thông báo lỗi rõ ràng cho người dùng
- ✅ Tuân thủ nguyên tắc "không thể truy cập nội dung đã hoàn tiền"

### Trải nghiệm người dùng
- ✅ Thông báo lỗi dễ hiểu
- ✅ Không gây nhầm lẫn về quyền truy cập
- ✅ Khuyến khích sử dụng hợp lý

## Tương tác với các tính năng khác

### Hệ thống hoàn tiền ebook
- **Tương thích**: Logic này bổ sung cho hệ thống hoàn tiền hiện có
- **Không xung đột**: Không ảnh hưởng đến logic tính toán hoàn tiền
- **Tăng cường bảo mật**: Ngăn chặn lạm dụng sau khi hoàn tiền

### DRM System
- **Bổ sung**: Thêm một lớp bảo vệ nữa ngoài DRM
- **Ưu tiên**: Kiểm tra hoàn tiền trước khi kiểm tra DRM
- **Hiệu quả**: Giảm tải cho hệ thống DRM

## Lưu ý quan trọng

⚠️ **Thứ tự kiểm tra**: Kiểm tra hoàn tiền được đặt sau kiểm tra quyền sở hữu nhưng trước kiểm tra DRM

⚠️ **Performance**: Thêm 1 query để kiểm tra RefundRequest, cần monitor performance

⚠️ **Edge cases**: Cần xử lý trường hợp RefundRequest bị xóa nhưng trạng thái thanh toán chưa cập nhật

## Monitoring

### Metrics cần theo dõi:
- Số lần bị chặn do trạng thái hoàn tiền
- Số lần bị chặn do có yêu cầu hoàn tiền
- Performance impact của query RefundRequest

### Logs quan trọng:
- Tất cả các lần bị chặn đều được log với HTTP 403
- Có thể thêm custom log để tracking behavior

---

**Ngày triển khai**: 2025-08-01  
**Trạng thái**: ✅ Hoàn thành  
**Impact**: 🔒 Security Enhancement - Bảo vệ bản quyền ebook  
**Test**: ✅ Đã test đầy đủ các trường hợp