# Chức năng Thông báo Thanh toán Thành công

## Mô tả chức năng

Chức năng này tự động tạo thông báo cho người dùng khi thanh toán đơn hàng thành công. Thông báo sẽ được lưu vào cơ sở dữ liệu và hiển thị trong hệ thống thông báo của người dùng.

## Các tính năng chính

1. **Tự động tạo thông báo**: Khi thanh toán thành công, hệ thống tự động tạo thông báo cho người dùng
2. **Lưu vào database**: Thông báo được lưu vào bảng `notifications` với đầy đủ thông tin
3. **Hiển thị thông tin chi tiết**: Thông báo bao gồm mã đơn hàng và số tiền thanh toán
4. **Tích hợp với hệ thống thông báo**: Thông báo sẽ xuất hiện trong dropdown thông báo và trang danh sách thông báo

## Các file liên quan

### 1. NotificationService
- **File**: `app/Services/NotificationService.php`
- **Chức năng**: Chứa logic tạo các loại thông báo khác nhau
- **Method chính**: `createPaymentSuccessNotification($order, $user)`

### 2. OrderController
- **File**: `app/Http/Controllers/OrderController.php`
- **Chức năng**: Xử lý thanh toán và gọi NotificationService
- **Các điểm tích hợp**:
  - Thanh toán đơn hàng hỗn hợp (mixed order)
  - Thanh toán bằng ví điện tử
  - Thanh toán VNPay

### 3. Notification Model
- **File**: `app/Models/Notification.php`
- **Chức năng**: Model để tương tác với bảng notifications

## Cách hoạt động

### 1. Luồng thanh toán thành công

```
User thanh toán đơn hàng
    ↓
OrderController xử lý thanh toán
    ↓
Thanh toán thành công
    ↓
Gọi NotificationService::createPaymentSuccessNotification()
    ↓
Lưu thông báo vào database
    ↓
Thông báo hiển thị trong hệ thống
```

### 2. Các điểm tích hợp trong OrderController

#### a) Thanh toán đơn hàng hỗn hợp (Mixed Order)
```php
// Trong method checkout() - xử lý mixed order
$this->notificationService->createPaymentSuccessNotification($parentOrder, $user);
```

#### b) Thanh toán bằng ví điện tử
```php
// Trong method checkout() - xử lý wallet payment
$this->notificationService->createPaymentSuccessNotification($order, $user);
```

#### c) Thanh toán VNPay
```php
// Trong method vnpay_return() - xử lý VNPay callback
$this->notificationService->createPaymentSuccessNotification($order, Auth::user());
```

## Cấu trúc thông báo

### Thông tin được lưu
- **user_id**: ID của người dùng nhận thông báo
- **type**: `payment_success`
- **title**: "Thanh toán thành công"
- **message**: "Thanh toán đơn hàng #{order_code} đã thành công với số tiền {total_amount}đ"
- **data**: JSON chứa thông tin chi tiết đơn hàng
- **type_id**: ID của đơn hàng
- **read_at**: null (chưa đọc)

### Ví dụ dữ liệu
```json
{
    "id": 119,
    "user_id": "0178fafb-6bbf-46f0-a590-a895d4f23f96",
    "type": "payment_success",
    "title": "Thanh toán thành công",
    "message": "Thanh toán đơn hàng #ORDDKHRW82C đã thành công với số tiền 248.810đ",
    "data": {
        "order_id": "1b60cd06-1a8e-4d7a-a0e1-385ecec8a064",
        "order_code": "ORDDKHRW82C",
        "total_amount": 248810,
        "payment_method": "vnpay"
    },
    "type_id": "1b60cd06-1a8e-4d7a-a0e1-385ecec8a064",
    "read_at": null,
    "created_at": "2025-08-17T14:51:17.000000Z"
}
```

## API Endpoints liên quan

### 1. Lấy danh sách thông báo
- **URL**: `GET /api/notifications`
- **Mô tả**: Lấy danh sách thông báo của user hiện tại

### 2. Lấy tất cả thông báo
- **URL**: `GET /api/notifications/all`
- **Mô tả**: Lấy tất cả thông báo có phân trang

### 3. Đánh dấu đã đọc
- **URL**: `PATCH /api/notifications/{id}/mark-as-read`
- **Mô tả**: Đánh dấu một thông báo đã đọc

## Giao diện người dùng

### 1. Dropdown thông báo
- Hiển thị thông báo thanh toán thành công với icon ✅
- Thông báo chưa đọc sẽ có màu nền khác biệt
- Click vào thông báo sẽ đánh dấu đã đọc

### 2. Trang danh sách thông báo
- **URL**: `/notifications`
- Hiển thị tất cả thông báo với phân trang
- Có thể đánh dấu tất cả đã đọc
- Lọc theo loại thông báo

## Loại thông báo & Icon

| Loại | Icon | Màu sắc |
|------|------|----------|
| payment_success | ✅ | Xanh lá |
| order_status_updated | 📦 | Xanh dương |
| new_order (Admin) | 🛒 | Cam |

## Hướng dẫn Test

### 1. Test thủ công
1. Đăng nhập với tài khoản user
2. Thêm sản phẩm vào giỏ hàng
3. Tiến hành thanh toán (VNPay hoặc ví điện tử)
4. Hoàn tất thanh toán
5. Kiểm tra thông báo trong dropdown
6. Kiểm tra trang `/notifications`

### 2. Test bằng code
```php
// Tạo thông báo test
$notificationService = new NotificationService();
$order = Order::first();
$user = User::first();
$notification = $notificationService->createPaymentSuccessNotification($order, $user);
```

### 3. Kiểm tra database
```sql
SELECT * FROM notifications 
WHERE type = 'payment_success' 
ORDER BY created_at DESC 
LIMIT 10;
```

## Lưu ý kỹ thuật

1. **Dependency Injection**: NotificationService được inject vào OrderController thông qua constructor
2. **Error Handling**: Nếu việc tạo thông báo thất bại, không ảnh hưởng đến luồng thanh toán chính
3. **Performance**: Việc tạo thông báo được thực hiện đồng bộ, không ảnh hưởng đáng kể đến performance
4. **Security**: Chỉ user sở hữu đơn hàng mới nhận được thông báo

## Tương lai mở rộng

1. **Real-time notifications**: Tích hợp WebSocket để gửi thông báo real-time
2. **Email notifications**: Gửi email thông báo thanh toán thành công
3. **SMS notifications**: Gửi SMS cho các đơn hàng giá trị cao
4. **Push notifications**: Thông báo đẩy cho mobile app