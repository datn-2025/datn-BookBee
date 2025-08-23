# Hệ Thống Thông Báo Realtime

## Mô tả chức năng

Hệ thống thông báo realtime cho phép gửi thông báo tức thời đến admin và khách hàng khi có các sự kiện quan trọng xảy ra trong hệ thống, như:
- Đơn hàng mới được tạo (thông báo cho admin)
- Trạng thái đơn hàng thay đổi (thông báo cho khách hàng)
- Các sự kiện khác trong tương lai

## Công nghệ sử dụng

- **Laravel Echo**: Client-side JavaScript library để lắng nghe events
- **Pusher**: WebSocket service provider cho realtime communication
- **Laravel Events & Broadcasting**: Server-side event system
- **SweetAlert2**: Hiển thị thông báo đẹp mắt
- **Toastr**: Thông báo toast nhẹ nhàng

## Cấu trúc hệ thống

### 1. Models

#### Notification Model
- **File**: `app/Models/Notification.php`
- **Bảng**: `notifications`
- **Chức năng**: Lưu trữ thông báo trong database

**Cấu trúc bảng:**
```sql
- id (UUID)
- user_id (UUID) - Người nhận thông báo
- title (string) - Tiêu đề thông báo
- message (text) - Nội dung thông báo
- type (string) - Loại thông báo (order_created, order_status_updated, etc.)
- data (json) - Dữ liệu bổ sung
- read_at (timestamp) - Thời gian đọc thông báo
- created_at, updated_at
```

### 2. Events

#### OrderCreated Event
- **File**: `app/Events/OrderCreated.php`
- **Kích hoạt**: Khi có đơn hàng mới được tạo
- **Channel**: `admin-notifications`
- **Người nhận**: Admin users

#### OrderStatusUpdated Event
- **File**: `app/Events/OrderStatusUpdated.php`
- **Kích hoạt**: Khi trạng thái đơn hàng thay đổi
- **Channel**: `user.{user_id}`
- **Người nhận**: Khách hàng sở hữu đơn hàng

### 3. Frontend Integration

#### Laravel Echo Configuration
- **File**: `resources/js/echo.js`
- **Cấu hình**: Pusher credentials từ meta tags
- **Channels**: Private channels cho bảo mật

#### Notification Handler
- **File**: `public/backend/js/notifications.js`
- **Chức năng**: 
  - Lắng nghe events từ Laravel Echo
  - Hiển thị thông báo bằng SweetAlert2/Toastr
  - Phát âm thanh thông báo
  - Cập nhật badge số lượng thông báo

## Cách sử dụng

### 1. Cấu hình Environment

Trong file `.env`, đảm bảo có các cấu hình sau:

```env
BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=sync

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST=
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
```

### 2. Thêm Meta Tags vào Layout

Trong `resources/views/layouts/backend.blade.php`:

```html
<!-- Pusher Meta Tags -->
<meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
<meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
<meta name="user-id" content="{{ Auth::id() }}">
<meta name="user-role" content="{{ Auth::user()->role->name ?? 'user' }}">
```

### 3. Import Scripts

Trong phần scripts của layout:

```html
<script src="{{ asset('js/echo.js') }}"></script>
<script src="{{ asset('backend/js/notifications.js') }}"></script>
```

### 4. Tạo Event Mới

Để tạo event thông báo mới:

```bash
php artisan make:event YourEventName
```

Event class cần implement `ShouldBroadcast`:

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class YourEventName implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        
        // Lưu thông báo vào database
        $this->saveNotification();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('your-channel-name');
    }

    public function broadcastAs()
    {
        return 'your-event-name';
    }

    private function saveNotification()
    {
        // Logic lưu thông báo vào database
    }
}
```

### 5. Dispatch Event

Trong Controller hoặc Service:

```php
use App\Events\YourEventName;

// Dispatch event
event(new YourEventName($data));
```

### 6. Lắng nghe Event trong Frontend

Trong `notifications.js`:

```javascript
// Lắng nghe channel
Echo.private('your-channel-name')
    .listen('.your-event-name', (e) => {
        // Hiển thị thông báo
        showNotification(e.title, e.message, 'success');
        
        // Phát âm thanh
        playNotificationSound();
        
        // Cập nhật badge
        updateNotificationBadge();
    });
```

## Các loại thông báo

### 1. Thông báo Admin
- **Channel**: `admin-notifications`
- **Events**: OrderCreated, PaymentReceived, etc.
- **Hiển thị**: SweetAlert2 với icon thông tin

### 2. Thông báo Khách hàng
- **Channel**: `user.{user_id}`
- **Events**: OrderStatusUpdated, ShippingUpdate, etc.
- **Hiển thị**: Toastr notification

## Tùy chỉnh

### 1. Thay đổi âm thanh thông báo

Trong `notifications.js`, sửa đường dẫn file âm thanh:

```javascript
function playNotificationSound() {
    const audio = new Audio('/path/to/your/sound.mp3');
    audio.play().catch(e => console.log('Cannot play sound:', e));
}
```

### 2. Tùy chỉnh giao diện thông báo

Sửa các hàm hiển thị trong `notifications.js`:

```javascript
function showSweetAlert(title, message, type = 'info') {
    Swal.fire({
        title: title,
        text: message,
        icon: type,
        timer: 5000,
        timerProgressBar: true,
        // Thêm các tùy chỉnh khác
    });
}
```

### 3. Thêm channel mới

Trong `notifications.js`, thêm listener cho channel mới:

```javascript
// Channel cho moderator
if (userRole === 'moderator') {
    Echo.private('moderator-notifications')
        .listen('.ModeratorEvent', (e) => {
            // Xử lý thông báo cho moderator
        });
}
```

## Troubleshooting

### 1. Thông báo không hiển thị

**Kiểm tra:**
- Pusher credentials trong `.env`
- Meta tags trong layout
- Console browser có lỗi không
- Laravel Echo có kết nối thành công không

**Debug:**
```javascript
// Thêm vào console để debug
console.log('Echo instance:', window.Echo);
console.log('User ID:', document.querySelector('meta[name="user-id"]').content);
```

### 2. Events không được broadcast

**Kiểm tra:**
- Event class có implement `ShouldBroadcast` không
- `BROADCAST_DRIVER=pusher` trong `.env`
- Queue worker có chạy không (nếu dùng queue)

### 3. Private channel không hoạt động

**Kiểm tra:**
- Route `channels.php` có định nghĩa authorization không
- User có được authenticate không
- CSRF token có hợp lệ không

## Kết quả mong muốn

✅ **Admin nhận thông báo khi:**
- Có đơn hàng mới
- Có thanh toán mới
- Có yêu cầu hỗ trợ

✅ **Khách hàng nhận thông báo khi:**
- Trạng thái đơn hàng thay đổi
- Đơn hàng được giao thành công
- Có tin nhắn từ admin

✅ **Tính năng bổ sung:**
- Âm thanh thông báo
- Badge hiển thị số lượng thông báo chưa đọc
- Lưu trữ thông báo trong database
- Đánh dấu đã đọc/chưa đọc

## Bảo mật

- Sử dụng Private Channels để đảm bảo chỉ user được phép mới nhận thông báo
- Validate authorization trong `routes/channels.php`
- Không expose sensitive data trong broadcast data
- Sử dụng HTTPS cho production environment

## Performance

- Sử dụng Queue để xử lý events không đồng bộ
- Giới hạn số lượng thông báo lưu trong database
- Cleanup thông báo cũ định kỳ
- Optimize Pusher connection pooling