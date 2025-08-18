# Hệ Thống Thông Báo Realtime - Hướng Dẫn Hoàn Chỉnh

## 📋 Tổng Quan

Hệ thống thông báo realtime sử dụng Laravel Echo + Pusher để gửi thông báo tức thời cho admin và khách hàng khi có sự kiện quan trọng xảy ra.

## 🏗️ Kiến Trúc Hệ Thống

### 1. Backend Components

#### Models
- **Notification Model**: `app/Models/Notification.php`
  - Lưu trữ thông báo trong database
  - Quan hệ với User model

#### Events
- **OrderCreated**: `app/Events/OrderCreated.php`
  - Kích hoạt khi có đơn hàng mới
  - Broadcast đến channel `admin-orders`
  - Lưu thông báo cho tất cả admin

- **OrderStatusUpdated**: `app/Events/OrderStatusUpdated.php`
  - Kích hoạt khi trạng thái đơn hàng thay đổi
  - Broadcast đến channel riêng của khách hàng

- **WalletDeposited**: `app/Events/WalletDeposited.php`
  - Kích hoạt khi khách hàng nạp tiền vào ví
  - Broadcast đến channel riêng của khách hàng (`customer-{userId}`)
  - Broadcast đến channel admin (`admin-wallets`)
  - Lưu thông báo cho khách hàng và tất cả admin

- **WalletWithdrawn**: `app/Events/WalletWithdrawn.php`
  - Kích hoạt khi khách hàng rút tiền từ ví
  - Broadcast đến channel riêng của khách hàng (`customer-{userId}`)
  - Broadcast đến channel admin (`admin-wallets`)
  - Lưu thông báo cho khách hàng và tất cả admin

#### Database
```sql
-- Migration: notifications table
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36),
    title VARCHAR(255),
    message TEXT,
    type ENUM('info', 'success', 'warning', 'error'),
    data JSON,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 2. Frontend Components

#### JavaScript Files
- **resources/js/echo.js**: Cấu hình Laravel Echo
- **public/js/notifications.js**: Xử lý thông báo realtime
- **resources/js/app.js**: Import echo.js

#### Views
- **layouts/backend.blade.php**: Layout cho admin
- **layouts/app.blade.php**: Layout cho frontend
- **test-notification.blade.php**: Trang test hệ thống

## ⚙️ Cấu Hình

### 1. Environment Variables (.env)
```env
BROADCAST_CONNECTION=pusher
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=2037895
PUSHER_APP_KEY=4829621e97569df33e7c
PUSHER_APP_SECRET=3110a2fc236e55c753d9
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=ap2

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"

QUEUE_CONNECTION=sync
```

### 2. Vite Configuration (vite.config.js)
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### 3. Broadcasting Configuration (config/broadcasting.php)
```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true,
        'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusherapp.com',
        'port' => env('PUSHER_PORT', 443),
        'scheme' => env('PUSHER_SCHEME', 'https'),
    ],
],
```

## 🚀 Cách Sử Dụng

### 1. Kích Hoạt Thông Báo Đơn Hàng Mới

```php
// Trong Controller khi tạo đơn hàng
use App\Events\OrderCreated;

public function store(Request $request)
{
    // Tạo đơn hàng
    $order = Order::create($request->validated());
    
    // Kích hoạt event
    event(new OrderCreated($order));
    
    return response()->json(['success' => true]);
}
```

### 2. Kích Hoạt Thông Báo Cập Nhật Trạng Thái

```php
// Trong Controller khi cập nhật trạng thái
use App\Events\OrderStatusUpdated;

public function updateStatus(Request $request, Order $order)
{
    $oldStatus = $order->status;
    $order->update(['status' => $request->status]);
    
    // Kích hoạt event
    event(new OrderStatusUpdated($order, $oldStatus, $request->status));
    
    return response()->json(['success' => true]);
}
```

### 3. Kích Hoạt Thông Báo Nạp Tiền Ví

```php
// Trong Controller khi nạp tiền vào ví
use App\Events\WalletDeposited;

public function deposit(Request $request)
{
    $user = auth()->user();
    $amount = $request->amount;
    $transactionId = 'TXN_' . time();
    
    // Xử lý nạp tiền vào ví
    // ...
    
    // Kích hoạt event
    event(new WalletDeposited($user, $amount, $transactionId));
    
    return response()->json(['success' => true]);
}
```

### 4. Kích Hoạt Thông Báo Rút Tiền Ví

```php
// Trong Controller khi rút tiền từ ví
use App\Events\WalletWithdrawn;

public function withdraw(Request $request)
{
    $user = auth()->user();
    $amount = $request->amount;
    $transactionId = 'TXN_' . time();
    
    // Xử lý rút tiền từ ví
    // ...
    
    // Kích hoạt event
    event(new WalletWithdrawn($user, $amount, $transactionId));
    
    return response()->json(['success' => true]);
}
```

### 5. Lắng Nghe Thông Báo Trong JavaScript

```javascript
// Cho Admin - Đơn hàng
window.Echo.channel('admin-orders')
    .listen('order.created', (data) => {
        console.log('Đơn hàng mới:', data);
        // Hiển thị thông báo
        showNotification('success', 'Đơn hàng mới', data.message);
        // Cập nhật badge
        updateNotificationBadge();
        // Thêm vào dropdown
        addNotificationToDropdown(data);
    });

// Cho Admin - Thông báo ví
window.Echo.channel('admin-wallets')
    .listen('wallet.deposited', (data) => {
        console.log('Nạp tiền ví:', data);
        showNotification(
            'success',
            '💰 Nạp tiền ví!',
            `${data.customer_name} đã nạp ${data.amount}đ vào ví`
        );
        updateNotificationBadge();
        addNotificationToDropdown({
            title: 'Nạp tiền ví!',
            message: `${data.customer_name} - ${data.amount}đ`,
            time: 'Vừa xong',
            icon: 'bx-wallet',
            type: 'success'
        });
    })
    .listen('wallet.withdrawn', (data) => {
        console.log('Rút tiền ví:', data);
        showNotification(
            'warning',
            '💸 Rút tiền ví!',
            `${data.customer_name} đã rút ${data.amount}đ từ ví`
        );
        updateNotificationBadge();
        addNotificationToDropdown({
            title: 'Rút tiền ví!',
            message: `${data.customer_name} - ${data.amount}đ`,
            time: 'Vừa xong',
            icon: 'bx-money-withdraw',
            type: 'warning'
        });
    });

// Cho Khách hàng - Đơn hàng
window.Echo.channel(`customer-${userId}`)
    .listen('order.status.updated', (data) => {
        console.log('Trạng thái đơn hàng cập nhật:', data);
        showNotification('info', 'Cập nhật đơn hàng', data.message);
    })
    // Lắng nghe thông báo nạp tiền ví
    .listen('wallet.deposited', (data) => {
        console.log('Nạp tiền vào ví:', data);
        showNotification(
            'success', 
            '💰 Nạp tiền thành công',
            `Bạn đã nạp thành công ${new Intl.NumberFormat('vi-VN').format(data.amount)}đ vào ví lúc ${data.deposited_at}`
        );
    })
    // Lắng nghe thông báo rút tiền ví
    .listen('wallet.withdrawn', (data) => {
        console.log('Rút tiền từ ví:', data);
        showNotification(
            'info',
            '💸 Rút tiền thành công', 
            `Bạn đã rút thành công ${new Intl.NumberFormat('vi-VN').format(data.amount)}đ từ ví lúc ${data.withdrawn_at}`
        );
    });
```

## 🧪 Testing

### 1. Test Routes
```php
// routes/web.php
Route::get('/test-notification', function () {
    $order = Order::first();
    if ($order) {
        event(new OrderCreated($order));
        return response()->json([
            'message' => 'Test notification sent',
            'order' => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'customer_name' => $order->customer_name,
                'total_amount' => $order->total_amount
            ],
            'timestamp' => now()
        ]);
    }
    return response()->json(['error' => 'No orders found'], 404);
});

Route::get('/test-notification-page', function () {
    return view('test-notification');
});
```

### 2. Test Commands
```bash
# Build assets
npm run build

# Test notification đơn hàng
curl http://localhost:8000/test-notification

# Test thông báo nạp tiền ví (khách hàng)
curl http://localhost:8000/test-wallet-deposit

# Test thông báo rút tiền ví (khách hàng)
curl http://localhost:8000/test-wallet-withdraw

# Test thông báo nạp tiền ví cho admin
curl http://localhost:8000/test-admin-wallet-deposit

# Test thông báo rút tiền ví cho admin
curl http://localhost:8000/test-admin-wallet-withdraw

# Mở trang test
http://localhost:8000/test-notification-page
```

### 3. Trang Test Interactive
Truy cập: `http://localhost:8000/test-notification-page`

Trang này cung cấp:
- Kiểm tra kết nối Echo
- Test thông báo local
- Gửi test order
- Console log realtime
- Dropdown thông báo mô phỏng

## 🔧 Troubleshooting

### 1. Echo không khởi tạo
**Triệu chứng**: `Echo is undefined`

**Giải pháp**:
```bash
# Kiểm tra build
npm run build

# Kiểm tra .env
echo $VITE_PUSHER_APP_KEY
echo $VITE_PUSHER_APP_CLUSTER
```

### 2. Không nhận được thông báo
**Triệu chứng**: Event được fire nhưng frontend không nhận

**Kiểm tra**:
1. Pusher credentials đúng
2. User đã đăng nhập
3. Meta tags user-id và user-role có giá trị
4. Channel name đúng
5. Event name đúng

### 3. Lỗi CORS
**Triệu chứng**: `Access-Control-Allow-Origin` error

**Giải pháp**:
```php
// config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],
```

### 4. Authentication Error
**Triệu chứng**: `403 Forbidden` khi connect private channel

**Kiểm tra**:
1. CSRF token trong meta tag
2. Auth headers trong echo.js
3. Broadcasting auth route

## 📊 Monitoring

### 1. Debug Logs
```javascript
// Trong notifications.js
console.log('🚀 Khởi tạo thông báo cho', userRole, 'ID:', userId);
console.log('✅ Echo sẵn sàng');
console.log('🎉 Nhận được thông báo:', data);
```

### 2. Laravel Logs
```php
// Trong Event
Log::info('OrderCreated event fired', ['order_id' => $this->order->id]);
```

### 3. Pusher Dashboard
Truy cập Pusher dashboard để xem:
- Connection count
- Message count
- Error logs

## 🔒 Security

### 1. Private Channels
```php
// routes/channels.php
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

### 2. CSRF Protection
```javascript
// echo.js
auth: {
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    }
}
```

## 📈 Performance

### 1. Giới Hạn Thông Báo
```javascript
// Giới hạn 5 thông báo trong dropdown
if (list.children.length > 5) {
    list.removeChild(list.lastChild);
}
```

### 2. Debounce Updates
```javascript
// Debounce badge updates
let badgeUpdateTimeout;
function updateBadge(count) {
    clearTimeout(badgeUpdateTimeout);
    badgeUpdateTimeout = setTimeout(() => {
        // Update badge
    }, 100);
}
```

## 🎯 Best Practices

1. **Luôn validate dữ liệu** trước khi broadcast
2. **Sử dụng queue** cho events nặng
3. **Giới hạn số lượng** thông báo hiển thị
4. **Implement retry logic** cho failed connections
5. **Log tất cả events** để debug
6. **Test trên nhiều browser** và device
7. **Monitor Pusher usage** để tránh vượt quota

## 📝 Changelog

### v1.0.0 (2025-08-17)
- ✅ Tạo Model Notification và migration
- ✅ Tạo Event OrderCreated và OrderStatusUpdated
- ✅ Cấu hình Laravel Echo và Pusher
- ✅ Tạo JavaScript notifications.js
- ✅ Tạo trang test interactive
- ✅ Debug và fix các vấn đề kết nối
- ✅ Hoàn thiện tài liệu hướng dẫn

---

**Tác giả**: AI Assistant  
**Ngày tạo**: 17/08/2025  
**Phiên bản**: 1.0.0