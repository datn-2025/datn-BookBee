# Ẩn Thao Tác Đơn Hàng Cha

## Mô tả chức năng
Chức năng này đảm bảo rằng các đơn hàng cha (parent order) sẽ không hiển thị phần thao tác hủy đơn hàng và hoàn tiền ebook trong trang chi tiết đơn hàng.

## Nguyên nhân
Đơn hàng cha là đơn hàng tổng hợp chứa các đơn hàng con (child orders). Việc hủy hoặc hoàn tiền cần được thực hiện trên từng đơn hàng con riêng biệt, không phải trên đơn hàng cha.

## Cách hoạt động

### 1. Xác định đơn hàng cha
Sử dụng phương thức `isParentOrder()` trong model Order để kiểm tra:
```php
$order->isParentOrder()
```

### 2. Logic hiển thị thao tác
Trong file `resources/views/clients/account/order-details.blade.php`, điều kiện hiển thị phần thao tác được cập nhật:

```blade
@if(!$order->isParentOrder() && (\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name) || $canRefundEbook))
    <!-- Hiển thị phần thao tác đơn hàng -->
@endif
```

### 3. Các trường hợp áp dụng
- **Đơn hàng cha**: Không hiển thị nút hủy đơn hàng và hoàn tiền ebook
- **Đơn hàng con**: Hiển thị bình thường theo logic cũ
- **Đơn hàng thường**: Hiển thị bình thường theo logic cũ

## File được thay đổi

### `resources/views/clients/account/order-details.blade.php`
- **Dòng 692**: Thêm điều kiện `!$order->isParentOrder()` vào logic hiển thị thao tác

## Kết quả mong muốn

1. **Đơn hàng cha**: Không hiển thị phần "THAO TÁC ĐƠN HÀNG"
2. **Đơn hàng con**: Vẫn hiển thị đầy đủ các thao tác như hủy đơn hàng, hoàn tiền ebook (nếu đủ điều kiện)
3. **Đơn hàng thường**: Không bị ảnh hưởng, hoạt động bình thường

## Lợi ích

1. **Tránh nhầm lẫn**: Người dùng không thể thực hiện thao tác không hợp lệ trên đơn hàng cha
2. **Logic rõ ràng**: Thao tác hủy/hoàn tiền chỉ thực hiện trên đơn hàng con cụ thể
3. **Trải nghiệm tốt hơn**: Giao diện sạch sẽ, không hiển thị các nút không cần thiết

## Cách kiểm tra

1. Tạo một đơn hàng hỗn hợp (mixed order) để có đơn hàng cha
2. Truy cập trang chi tiết đơn hàng cha
3. Xác nhận không có phần "THAO TÁC ĐƠN HÀNG" hiển thị
4. Truy cập trang chi tiết đơn hàng con
5. Xác nhận phần thao tác vẫn hiển thị bình thường (nếu đủ điều kiện)

## Ghi chú

- Chức năng này chỉ ảnh hưởng đến giao diện hiển thị, không thay đổi logic backend
- Đơn hàng con vẫn có thể được hủy/hoàn tiền theo quy trình bình thường
- Phù hợp với luồng xử lý đơn hàng hỗn hợp hiện tại