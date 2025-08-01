# Tính năng: Cộng lại tồn kho khi hủy đơn hàng

## Mô tả vấn đề
Trước đây, khi khách hàng hủy đơn hàng, hệ thống chỉ cập nhật trạng thái đơn hàng thành "Đã hủy" nhưng không cộng lại số lượng sản phẩm vào tồn kho. Điều này dẫn đến việc tồn kho bị giảm không chính xác và có thể gây ra tình trạng thiếu hàng ảo.

## Nguyên nhân
- Logic cộng lại tồn kho đã được viết sẵn nhưng bị comment và có debug code (dd())
- Chức năng hủy đơn hàng chỉ tập trung vào việc cập nhật trạng thái mà bỏ qua việc quản lý tồn kho
- Thiếu tính nhất quán giữa các controller xử lý hủy đơn hàng

## Giải pháp

### 1. Sửa lại OrderController.php
- Bỏ comment và debug code trong logic cộng lại tồn kho
- Đảm bảo logic được thực thi trong transaction để đảm bảo tính toàn vẹn dữ liệu
- Thêm logging để theo dõi quá trình cộng lại tồn kho

### 2. Cập nhật OrderClientController.php
- Thêm logic cộng lại tồn kho vào cả hai hàm `cancel()` và `update()`
- Đảm bảo tính nhất quán với OrderController.php

### 3. Logic cộng lại tồn kho
```php
// Cộng lại tồn kho cho các sản phẩm trong đơn hàng
$order->orderItems->each(function ($item) {
    if ($item->bookFormat && $item->bookFormat->stock !== null) {
        Log::info("Cộng lại tồn kho cho book_format_id {$item->bookFormat->id}, số lượng: {$item->quantity}");
        $item->bookFormat->increment('stock', $item->quantity);
    }
});
```

## Quy trình hoạt động

### Trước khi sửa
1. Khách hàng hủy đơn hàng
2. Hệ thống cập nhật trạng thái đơn hàng thành "Đã hủy"
3. **Tồn kho không được cộng lại** ❌
4. Hoàn tiền vào ví (nếu đã thanh toán)

### Sau khi sửa
1. Khách hàng hủy đơn hàng
2. Hệ thống cập nhật trạng thái đơn hàng thành "Đã hủy"
3. **Cộng lại tồn kho cho từng sản phẩm trong đơn hàng** ✅
4. Hoàn tiền vào ví (nếu đã thanh toán)
5. Ghi log quá trình cộng lại tồn kho

## Kết quả test

### Test case: Hủy đơn hàng có 1 sản phẩm
- **Đơn hàng test**: BBE-1754045107
- **Sản phẩm**: Du lịch Đài Loan mùa Hoa Anh Đào
- **Tồn kho trước khi hủy**: 4
- **Số lượng trong đơn hàng**: 1
- **Tồn kho sau khi hủy**: 5 ✅
- **Kết quả**: THÀNH CÔNG

### Kiểm tra tính toàn vẹn
- Trạng thái đơn hàng được cập nhật chính xác
- Tồn kho được cộng lại đúng số lượng
- Transaction rollback hoạt động bình thường
- Logging được ghi đầy đủ

## Tương tác với các tính năng khác

### 1. Quản lý tồn kho
- **Ảnh hưởng**: Tồn kho được cập nhật chính xác khi hủy đơn hàng
- **Lợi ích**: Tránh tình trạng thiếu hàng ảo, đảm bảo tồn kho phản ánh đúng thực tế

### 2. Hệ thống đặt hàng
- **Ảnh hưởng**: Sản phẩm có thể được đặt lại sau khi đơn hàng khác bị hủy
- **Lợi ích**: Tăng khả năng bán hàng, giảm tình trạng "hết hàng" không chính xác

### 3. Báo cáo tồn kho
- **Ảnh hưởng**: Báo cáo tồn kho chính xác hơn
- **Lợi ích**: Hỗ trợ quản lý kho tốt hơn, quyết định nhập hàng chính xác

### 4. Hoàn tiền
- **Ảnh hưởng**: Không ảnh hưởng đến logic hoàn tiền
- **Lợi ích**: Cả hai tính năng hoạt động độc lập và bổ trợ cho nhau

## Lưu ý quan trọng

### 1. Điều kiện cộng lại tồn kho
- Chỉ cộng lại tồn kho cho `bookFormat` có `stock !== null`
- Không cộng lại cho sản phẩm không quản lý tồn kho
- Chỉ áp dụng khi đơn hàng thực sự bị hủy

### 2. Tính toàn vẹn dữ liệu
- Toàn bộ quá trình được thực hiện trong database transaction
- Nếu có lỗi xảy ra, tất cả thay đổi sẽ được rollback
- Logging đầy đủ để theo dõi và debug

### 3. Performance
- Logic cộng lại tồn kho được thực hiện cho từng item riêng biệt
- Sử dụng `increment()` method để đảm bảo atomic operation
- Không ảnh hưởng đáng kể đến performance do số lượng item trong đơn hàng thường ít

## Files đã thay đổi

1. **app/Http/Controllers/OrderController.php**
   - Bỏ comment logic cộng lại tồn kho
   - Bỏ debug code (dd())
   - Uncomment logic tạo OrderCancellation và cập nhật trạng thái

2. **app/Http/Controllers/Client/OrderClientController.php**
   - Thêm logic cộng lại tồn kho vào hàm `cancel()`
   - Thêm logic cộng lại tồn kho vào hàm `update()`

## Cách test

### Test thủ công
1. Tạo đơn hàng với sản phẩm có tồn kho
2. Ghi nhận tồn kho hiện tại
3. Hủy đơn hàng
4. Kiểm tra tồn kho đã được cộng lại chưa
5. Kiểm tra log để xác nhận quá trình

### Test tự động
```bash
php test_order_cancel_stock_restore.php
```

## Lợi ích

1. **Quản lý tồn kho chính xác**: Tồn kho phản ánh đúng thực tế
2. **Tăng doanh số**: Sản phẩm có thể được bán lại sau khi đơn hàng khác bị hủy
3. **Giảm confusion**: Khách hàng không gặp tình trạng "hết hàng" khi thực tế còn hàng
4. **Hỗ trợ quản lý**: Admin có thông tin tồn kho chính xác để ra quyết định
5. **Tính nhất quán**: Logic hủy đơn hàng nhất quán trên tất cả các controller

## Tài liệu liên quan

- [Tính năng hoàn tiền vào ví khi hủy đơn hàng](./order-cancel-wallet-refund.md)
- [Quản lý trạng thái đơn hàng](../features/order-status-management.md)
- [Hệ thống quản lý tồn kho](../features/inventory-management.md)