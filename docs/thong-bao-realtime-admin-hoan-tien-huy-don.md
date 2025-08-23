# Thông Báo Realtime Cho Admin - Hoàn Tiền & Hủy Đơn Hàng

## Mô Tả
Chức năng thông báo realtime cho admin khi có các sự kiện hoàn tiền và hủy đơn hàng từ người dùng. Hệ thống sẽ tự động tạo thông báo và lưu vào database để admin có thể theo dõi và xử lý kịp thời.

## Tính Năng Chính

### 1. Thông Báo Yêu Cầu Hoàn Tiền
- **Khi nào**: Khi khách hàng tạo yêu cầu hoàn tiền cho đơn hàng
- **Ai nhận**: Tất cả admin trong hệ thống
- **Nội dung**: Thông tin đơn hàng, khách hàng, số tiền hoàn, lý do hoàn tiền
- **Loại thông báo**: `refund_request`

### 2. Thông Báo Hủy Đơn Hàng
- **Khi nào**: Khi khách hàng hủy đơn hàng
- **Ai nhận**: Tất cả admin trong hệ thống
- **Nội dung**: Thông tin đơn hàng, khách hàng, số tiền hoàn (nếu có), lý do hủy
- **Loại thông báo**: `order_cancelled`

### 3. Thông Báo Đơn Hàng Mới (Bonus)
- **Khi nào**: Khi có đơn hàng mới được tạo
- **Ai nhận**: Tất cả admin trong hệ thống
- **Nội dung**: Thông tin đơn hàng mới, khách hàng, giá trị đơn hàng
- **Loại thông báo**: `new_order`

## Các File Liên Quan

### 1. Events
**Đường dẫn**: `app/Events/`
- `RefundRequested.php`: Event cho yêu cầu hoàn tiền
- `OrderCancelled.php`: Event cho hủy đơn hàng
- `OrderCreated.php`: Event cho đơn hàng mới (đã có sẵn)

### 2. NotificationService.php
**Đường dẫn**: `app/Services/NotificationService.php`

**Các phương thức mới**:
- `createRefundRequestNotificationForAdmin($order, $reason, $amount)`: Dispatch RefundRequested Event
- `createOrderCancellationNotificationForAdmin($order, $reason, $refundAmount)`: Dispatch OrderCancelled Event
- `createNewOrderNotificationForAdmin($order)`: Tạo thông báo đơn hàng mới

### 3. RefundController.php (Client)
**Đường dẫn**: `app/Http/Controllers/Client/RefundController.php`

**Tích hợp**: Trong method `store()` - gọi thông báo sau khi tạo yêu cầu hoàn tiền thành công

### 4. OrderClientController.php
**Đường dẫn**: `app/Http/Controllers/Client/OrderClientController.php`

**Tích hợp**: Trong method `cancel()` - gọi thông báo sau khi hủy đơn hàng thành công

### 5. OrderController.php
**Đường dẫn**: `app/Http/Controllers/OrderController.php`

**Tích hợp**: Trong method `cancel()` - gọi thông báo sau khi hủy đơn hàng thành công

### 6. Frontend
**Đường dẫn**: `public/js/notifications.js`

**Chức năng**: Xử lý nhận và hiển thị thông báo realtime

## Cách Hoạt Động

### Luồng Thông Báo Yêu Cầu Hoàn Tiền
1. Khách hàng tạo yêu cầu hoàn tiền qua `RefundController::store()`
2. Sau khi lưu yêu cầu hoàn tiền thành công
3. Gọi `NotificationService::createRefundRequestNotificationForAdmin()`
4. Service dispatch `RefundRequested` Event
5. Event tự động tạo thông báo cho tất cả admin
6. Event broadcast `refund.requested` qua channel `admin-orders`
7. Frontend nhận event và hiển thị toast notification

### Luồng Thông Báo Hủy Đơn Hàng
1. Khách hàng hủy đơn hàng qua các controller
2. Sau khi cập nhật trạng thái đơn hàng thành công
3. Gọi `NotificationService::createOrderCancellationNotificationForAdmin()`
4. Service dispatch `OrderCancelled` Event
5. Event tự động tạo thông báo cho tất cả admin
6. Event broadcast `order.cancelled` qua channel `admin-orders`
7. Frontend nhận event và hiển thị toast notification

## Cấu Trúc Thông Báo

### Thông Báo Yêu Cầu Hoàn Tiền
```php
[
    'user_id' => $admin->id,
    'type' => 'refund_request',
    'type_id' => $order->id,
    'title' => 'Yêu cầu hoàn tiền mới',
    'message' => 'Khách hàng {name} yêu cầu hoàn tiền đơn hàng #{order_code} với số tiền {amount}đ - Lý do: {reason}',
    'data' => [
        'order_id' => $order->id,
        'order_code' => $order->order_code,
        'customer_name' => $order->user->name,
        'customer_email' => $order->user->email,
        'refund_reason' => $reason,
        'refund_amount' => $amount,
        'original_amount' => $order->total_amount
    ]
]
```

### Thông Báo Hủy Đơn Hàng
```php
[
    'user_id' => $admin->id,
    'type' => 'order_cancelled',
    'type_id' => $order->id,
    'title' => 'Đơn hàng bị hủy',
    'message' => 'Khách hàng {name} đã hủy đơn hàng #{order_code} và đã hoàn tiền {amount}đ vào ví - Lý do: {reason}',
    'data' => [
        'order_id' => $order->id,
        'order_code' => $order->order_code,
        'customer_name' => $order->user->name,
        'customer_email' => $order->user->email,
        'cancellation_reason' => $reason,
        'refund_amount' => $refundAmount,
        'original_amount' => $order->total_amount
    ]
]
```

## Điểm Tích Hợp

### 1. RefundController::store()
```php
// Tạo thông báo cho admin về yêu cầu hoàn tiền mới
$this->notificationService->createRefundRequestNotificationForAdmin(
    $order,
    $request->reason,
    $order->total_amount
);
```

### 2. OrderClientController::cancel()
```php
// Tạo thông báo cho admin về việc hủy đơn hàng
$this->notificationService->createOrderCancellationNotificationForAdmin(
    $order,
    $request->cancellation_reason ?? 'Khách hàng hủy đơn hàng',
    $order->paymentStatus->name === 'Đã Thanh Toán' ? $order->total_amount : 0
);
```

### 3. OrderController::cancel()
```php
// Tạo thông báo cho admin về việc hủy đơn hàng
$this->notificationService->createOrderCancellationNotificationForAdmin(
    $order,
    implode(", ", $selectedReasons),
    $order->paymentStatus->name === 'Đã Thanh Toán' ? $order->total_amount : 0
);
```

## Loại Thông Báo & Icon

| Loại | Type | Icon | Màu sắc |
|------|------|------|--------|
| Yêu cầu hoàn tiền | `refund_request` | 💰 | Vàng/Orange |
| Hủy đơn hàng | `order_cancelled` | ❌ | Đỏ |
| Đơn hàng mới | `new_order` | 🛒 | Xanh lá |

## Hướng Dẫn Test

### Test Thủ Công
1. **Test yêu cầu hoàn tiền**:
   - Đăng nhập với tài khoản khách hàng
   - Tạo yêu cầu hoàn tiền cho một đơn hàng
   - Kiểm tra thông báo trong admin panel

2. **Test hủy đơn hàng**:
   - Đăng nhập với tài khoản khách hàng
   - Hủy một đơn hàng có thể hủy được
   - Kiểm tra thông báo trong admin panel

### Test Bằng Code
```php
// Đã test thành công với script test_refund_cancel_notifications.php
// Kết quả: Tất cả thông báo được tạo và lưu thành công
```

### Kiểm Tra Database
```sql
-- Kiểm tra thông báo mới nhất
SELECT * FROM notifications 
WHERE type IN ('refund_request', 'order_cancelled', 'new_order')
ORDER BY created_at DESC 
LIMIT 10;

-- Kiểm tra thông báo theo admin
SELECT u.name as admin_name, n.type, n.title, n.message, n.created_at
FROM notifications n
JOIN users u ON n.user_id = u.id
JOIN roles r ON u.role_id = r.id
WHERE r.name = 'Admin'
AND n.type IN ('refund_request', 'order_cancelled')
ORDER BY n.created_at DESC;
```

## Lưu Ý Kỹ Thuật

### 1. Event-Driven Architecture
- Sử dụng Laravel Events để tách biệt logic
- Events implement `ShouldBroadcast` interface
- Service chỉ dispatch events, Events xử lý logic thông báo

### 2. Broadcasting
- Events tự động broadcast qua channel `admin-orders`
- Event Names: `refund.requested`, `order.cancelled`, `order.created`
- Frontend sử dụng Laravel Echo để nhận thông báo realtime

### 3. Xử Lý Lỗi
- Tất cả Events và Service đều có try-catch để xử lý lỗi
- Ghi log chi tiết khi có lỗi xảy ra trong Events
- Không làm gián đoạn luồng chính khi tạo thông báo thất bại

### 4. Performance
- Sử dụng Eloquent relationship để tối ưu query
- Events tự động tạo thông báo cho tất cả admin
- Ghi log để theo dõi hiệu suất

### 5. Data Type & Security
- Events có type hints cho tham số
- Sử dụng `(float)` cast để đảm bảo `number_format()` hoạt động đúng
- Không expose thông tin nhạy cảm trong thông báo
- Validate dữ liệu đầu vào trước khi dispatch events

## Hướng Mở Rộng Trong Tương Lai

### 1. Real-time Notifications
- Tích hợp WebSocket/Pusher để gửi thông báo realtime
- Hiển thị popup notification trong admin panel

### 2. Email Notifications
- Gửi email thông báo cho admin khi có sự kiện quan trọng
- Template email tùy chỉnh cho từng loại thông báo

### 3. Notification Settings
- Cho phép admin tùy chỉnh loại thông báo muốn nhận
- Cài đặt thời gian gửi thông báo

### 4. Analytics
- Thống kê số lượng hoàn tiền/hủy đơn theo thời gian
- Dashboard hiển thị xu hướng hoàn tiền/hủy đơn

### 5. Auto Actions
- Tự động xử lý một số trường hợp hoàn tiền đơn giản
- Workflow tự động cho việc xử lý yêu cầu hoàn tiền

## Kết Luận

Chức năng thông báo realtime cho admin về hoàn tiền và hủy đơn hàng đã được triển khai thành công với:

✅ **Hoàn thành**:
- Tạo các method thông báo trong NotificationService
- Tích hợp vào các controller xử lý hoàn tiền và hủy đơn
- Test chức năng và đảm bảo hoạt động đúng
- Không sửa đổi logic hiện có khác

✅ **Đảm bảo**:
- Thông báo được tạo cho tất cả admin
- Thông tin chi tiết và đầy đủ
- Xử lý lỗi an toàn
- Performance tối ưu

Admin giờ đây sẽ nhận được thông báo kịp thời về các sự kiện hoàn tiền và hủy đơn hàng từ khách hàng, giúp xử lý và phản hồi nhanh chóng hơn.