# Cập nhật hiển thị trạng thái hoàn tiền từ bảng refund_request

## Mô tả vấn đề
Trước đây, giao diện người dùng chỉ hiển thị trạng thái thanh toán từ bảng `payment_status` mà không hiển thị trạng thái chi tiết của yêu cầu hoàn tiền từ bảng `refund_request`. Điều này khiến người dùng không thể biết được trạng thái cụ thể của yêu cầu hoàn tiền (đang chờ, đang xử lý, hoàn thành, từ chối).

## Nguyên nhân
- UI chỉ dựa vào `payment_status` để hiển thị trạng thái thanh toán
- Không load relationship `refundRequests` trong controller
- Thiếu logic hiển thị trạng thái chi tiết từ bảng `refund_request`

## Giải pháp

### 1. Cập nhật Controller
**File**: `app/Http/Controllers/Client/OrderClientController.php`

#### Phương thức `show()` (order-details.blade.php)
- Đã thêm `refundRequests` vào relationship load

#### Phương thức `unified()` (orders.blade.php)
- Thêm `refundRequests` vào relationship load

### 2. Cập nhật View order-details.blade.php
**File**: `resources/views/clients/account/order-details.blade.php`

#### Hiển thị trạng thái hoàn tiền cho Ebook
- Thay thế logic dựa vào `paymentStatus` bằng logic dựa vào `refund_request`
- Hiển thị các trạng thái: pending, processing, completed, rejected
- Sử dụng màu sắc khác nhau cho từng trạng thái

#### Hiển thị trạng thái hoàn tiền cho Physical/Mixed Orders
- Thay thế logic dựa vào `paymentStatus` bằng logic dựa vào `refund_request`
- Hiển thị thông tin chi tiết về yêu cầu hoàn tiền
- Thêm thông tin ngày yêu cầu, ngày xử lý, ghi chú admin

### 3. Cập nhật View orders.blade.php
**File**: `resources/views/clients/account/orders.blade.php`

#### Hiển thị trạng thái thanh toán
- Ưu tiên hiển thị trạng thái từ `refund_request` nếu có
- Fallback về `paymentStatus` nếu không có yêu cầu hoàn tiền
- Sử dụng màu sắc phù hợp cho từng trạng thái

#### Thêm section thông tin hoàn tiền
- Hiển thị thông tin chi tiết về yêu cầu hoàn tiền mới nhất
- Bao gồm: số tiền, ngày yêu cầu, ngày xử lý, ghi chú admin
- Sử dụng border và background color phù hợp với trạng thái

## Mapping trạng thái

### Trạng thái refund_request
- `pending` → "ĐANG CHỜ HOÀN TIỀN" (màu vàng)
- `processing` → "ĐANG XỬ LÝ HOÀN TIỀN" (màu xanh dương)
- `completed` → "ĐÃ HOÀN TIỀN" (màu xanh lá)
- `rejected` → "TỪ CHỐI HOÀN TIỀN" (màu đỏ)

### Màu sắc UI
- **Pending**: `text-yellow-600`, `border-yellow-500`, `bg-yellow-50`
- **Processing**: `text-blue-600`, `border-blue-500`, `bg-blue-50`
- **Completed**: `text-green-600`, `border-green-500`, `bg-green-50`
- **Rejected**: `text-red-600`, `border-red-500`, `bg-red-50`

## Lợi ích

1. **Thông tin chính xác**: Người dùng thấy được trạng thái thực tế của yêu cầu hoàn tiền
2. **Trải nghiệm tốt hơn**: Hiển thị thông tin chi tiết về quá trình hoàn tiền
3. **Tính minh bạch**: Người dùng biết được lý do từ chối (nếu có) qua ghi chú admin
4. **Theo dõi tiến trình**: Có thể thấy ngày yêu cầu và ngày xử lý

## Files đã thay đổi

1. **Controller**: `app/Http/Controllers/Client/OrderClientController.php`
   - Thêm `refundRequests` relationship vào `show()` và `unified()`

2. **View**: `resources/views/clients/account/order-details.blade.php`
   - Cập nhật logic hiển thị trạng thái hoàn tiền cho ebook
   - Cập nhật logic hiển thị trạng thái hoàn tiền cho physical/mixed orders

3. **View**: `resources/views/clients/account/orders.blade.php`
   - Cập nhật hiển thị trạng thái thanh toán
   - Thêm section thông tin chi tiết yêu cầu hoàn tiền

## Lưu ý quan trọng

1. **Backward Compatibility**: Vẫn hiển thị `paymentStatus` nếu không có yêu cầu hoàn tiền
2. **Performance**: Sử dụng eager loading để tránh N+1 query
3. **UI Consistency**: Sử dụng cùng style và màu sắc cho cả hai trang
4. **Data Safety**: Luôn kiểm tra sự tồn tại của relationship trước khi truy cập

---

**Ngày cập nhật**: 2025-01-27  
**Trạng thái**: ✅ Hoàn thành  
**Impact**: 🎨 UI Enhancement - Cải thiện trải nghiệm người dùng