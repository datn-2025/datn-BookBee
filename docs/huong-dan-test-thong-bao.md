# Hướng Dẫn Test Chức Năng Thông Báo

## Mô Tả
Tài liệu này hướng dẫn cách test chức năng hiển thị thông báo từ database trong icon navbar.

## Yêu Cầu
- Đã đăng nhập với tài khoản có `role_id = 2` (User)
- Database đã có bảng `notifications`
- Server Laravel và Vite đang chạy

## Cách Test

### 1. Tạo Thông Báo Test Qua Tinker

```bash
php artisan tinker
```

Sau đó chạy các lệnh sau:

```php
// Tìm user có role_id = 2
$user = App\Models\User::where('role_id', 2)->first();

// Nếu không có user, tạo user test
if (!$user) {
    $user = App\Models\User::create([
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => bcrypt('password'),
        'role_id' => 2,
        'email_verified_at' => now()
    ]);
}

// Xóa thông báo cũ
App\Models\Notification::where('user_id', $user->id)->delete();

// Tạo thông báo mới
App\Models\Notification::create([
    'user_id' => $user->id,
    'title' => 'Đơn hàng mới',
    'message' => 'Bạn có đơn hàng mới #12345',
    'type' => 'order',
    'is_read' => false
]);

App\Models\Notification::create([
    'user_id' => $user->id,
    'title' => 'Thanh toán thành công',
    'message' => 'Thanh toán đơn hàng #12344 đã thành công',
    'type' => 'payment',
    'is_read' => false
]);

App\Models\Notification::create([
    'user_id' => $user->id,
    'title' => 'Giao hàng',
    'message' => 'Đơn hàng #12343 đang được giao',
    'type' => 'shipping',
    'is_read' => false
]);

App\Models\Notification::create([
    'user_id' => $user->id,
    'title' => 'Thông báo đã đọc',
    'message' => 'Đây là thông báo đã được đọc',
    'type' => 'info',
    'is_read' => true
]);

echo "Đã tạo 4 thông báo test cho user: " . $user->name;
```

### 2. Đăng Nhập và Kiểm Tra

1. Truy cập: `http://localhost:8000/login`
2. Đăng nhập với:
   - Email: `testuser@example.com`
   - Password: `password`
3. Sau khi đăng nhập, kiểm tra icon thông báo trên navbar

### 3. Kết Quả Mong Đợi

- **Badge thông báo**: Hiển thị số `3` (3 thông báo chưa đọc)
- **Dropdown thông báo**: 
  - Hiển thị tối đa 3 thông báo
  - Có thanh cuộn nếu nhiều hơn 3 thông báo
  - Thông báo chưa đọc có nền màu xanh nhạt
  - Thông báo đã đọc có nền trắng
- **Click vào thông báo**: Đánh dấu thông báo đã đọc và cập nhật badge

### 4. Test API Trực Tiếp

Kiểm tra API endpoint:
```bash
curl -X GET "http://localhost:8000/api/notifications" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json"
```

### 5. Xử Lý Sự Cố

#### Lỗi 401 - Unauthorized
- Đảm bảo đã đăng nhập
- Kiểm tra CSRF token trong meta tag

#### Không hiển thị thông báo
- Kiểm tra console browser có lỗi JavaScript không
- Đảm bảo API endpoint `/api/notifications` hoạt động
- Kiểm tra user có `role_id = 2` không

#### Badge không cập nhật
- Kiểm tra hàm `updateNotificationBadge()` trong JavaScript
- Đảm bảo API trả về đúng số lượng thông báo chưa đọc

## Tính Năng Đã Triển Khai

- ✅ Hiển thị thông báo từ database
- ✅ Badge hiển thị số thông báo chưa đọc
- ✅ Dropdown hiển thị tối đa 3 thông báo với thanh cuộn
- ✅ Phân biệt thông báo đã đọc/chưa đọc
- ✅ Click để đánh dấu thông báo đã đọc
- ✅ Tự động reload thông báo mỗi 30 giây
- ✅ Tích hợp với thông báo realtime

## API Endpoints

- `GET /api/notifications` - Lấy danh sách thông báo
- `POST /api/notifications/{id}/mark-read` - Đánh dấu thông báo đã đọc

## Files Liên Quan

- `app/Http/Controllers/Api/NotificationController.php` - API Controller
- `routes/api.php` - API Routes
- `public/js/frontend-notifications.js` - Frontend JavaScript
- `resources/views/layouts/partials/navbar.blade.php` - HTML Template