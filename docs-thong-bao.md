# Chức năng Thông báo Realtime

## 1. Mục tiêu
Cung cấp hệ thống thông báo realtime cho website để:
- Cập nhật trạng thái đơn hàng cho khách hàng ngay khi có thay đổi.
- Thông báo cho admin khi có đơn hàng mới hoặc sự kiện quan trọng liên quan đến quản lý.

## 2. Đối tượng nhận thông báo
- **Khách hàng**:
  - Nhận thông báo khi:
    - Đơn hàng mới được tạo.
    - Trạng thái đơn hàng thay đổi (xác nhận, đang xử lý, đang giao, đã giao, hủy...).
    - Có tin nhắn mới từ bộ phận CSKH.
- **Admin**:
  - Nhận thông báo khi:
    - Có đơn hàng mới.
    - Có yêu cầu hỗ trợ / khiếu nại mới.
    - Có sản phẩm hết hàng hoặc gần hết hàng.

## 3. Kịch bản nghiệp vụ

### 3.1. Thông báo trạng thái đơn hàng cho khách hàng
1. Khách hàng đặt hàng thành công.
2. Backend tạo đơn hàng trong database.
3. Backend phát sự kiện (`OrderCreated`) qua Pusher.
4. Khách hàng nhận thông báo ngay trên giao diện (popup / toast / bell icon).
5. Khi admin thay đổi trạng thái đơn hàng:
   - Backend phát sự kiện (`OrderStatusUpdated`) kèm thông tin:
     - Mã đơn hàng
     - Trạng thái mới
     - Thời gian cập nhật
6. Khách hàng thấy thông báo trạng thái thay đổi ngay lập tức.
7. lưu thông báo vào db để người dùng có thể xem lại được thông báo

### 3.2. Thông báo đơn hàng mới cho Admin
1. Khi khách hàng đặt hàng thành công:
   - Backend phát sự kiện (`NewOrderNotification`) tới kênh dành cho admin.
   - Nội dung gồm:
     - Mã đơn hàng
     - Tên khách hàng
     - Tổng giá trị
     - Thời gian đặt hàng
2. Admin nhận thông báo ngay trên dashboard.

### 3.3. Thông báo yêu cầu hỗ trợ
1. Khách hàng gửi yêu cầu hỗ trợ.
2. Backend phát sự kiện (`SupportRequestCreated`) tới admin.
3. Admin nhận thông báo với nội dung:
   - Tên khách hàng
   - Nội dung yêu cầu
   - Thời gian gửi

## 4. Yêu cầu kỹ thuật
- **Công nghệ**: Laravel + Laravel Echo + Pusher (hoặc Laravel WebSockets nếu muốn dùng self-hosted).
- **Môi trường**:
  - Đã cấu hình `.env`:
    ```env
    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    VITE_PUSHER_HOST="${PUSHER_HOST}"
    VITE_PUSHER_PORT="${PUSHER_PORT}"
    VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
    BROADCAST_DRIVER=pusher
    QUEUE_CONNECTION=sync
    ```
- **Phát sự kiện**: sử dụng `broadcast(new EventName($data))`.
- **Frontend**:
  - Sử dụng Laravel Echo để lắng nghe sự kiện:
    ```javascript
    Echo.channel('orders')
        .listen('OrderCreated', (data) => {
            console.log('Đơn hàng mới:', data);
        });
    ```

## 5. Lợi ích
- Tăng trải nghiệm người dùng khi thông tin được cập nhật tức thì.
- Giảm thời gian xử lý cho admin.
- Nâng cao hiệu quả quản lý đơn hàng.
