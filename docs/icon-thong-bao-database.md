# Icon Thông Báo Hiển Thị Từ Database

## Mô Tả Chức Năng

Icon thông báo trong navbar của client và admin sẽ hiển thị các thông báo mà tài khoản đó đã nhận từ database, với các tính năng:
- Hiển thị tối đa 3 thông báo mới nhất
- Thanh cuộn khi có nhiều hơn 3 thông báo
- Badge hiển thị số lượng thông báo chưa đọc
- Đánh dấu thông báo đã đọc khi click
- Tự động reload mỗi 30 giây
- Tích hợp với thông báo realtime

## Các File Liên Quan

### 1. API Controller
**File:** `app/Http/Controllers/Api/NotificationController.php`
- `index()`: Lấy danh sách 3 thông báo mới nhất
- `markAsRead($id)`: Đánh dấu thông báo đã đọc
- `markAllAsRead()`: Đánh dấu tất cả thông báo đã đọc

### 2. API Routes
**File:** `routes/api.php`
```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
```

### 3. Frontend JavaScript
**File:** `public/js/frontend-notifications.js`

#### Các Hàm Chính:
- `loadNotificationsFromDatabase()`: Gọi API để lấy thông báo
- `displayNotificationsInDropdown(notifications)`: Hiển thị thông báo trong dropdown
- `updateNotificationBadge(unreadCount)`: Cập nhật badge số lượng
- `markNotificationAsRead(notificationId)`: Đánh dấu đã đọc
- `toggleNotificationDropdown()`: Bật/tắt dropdown

### 4. HTML Template
**File:** `resources/views/layouts/partials/navbar.blade.php`
- Dropdown thông báo với `max-height: 240px` và `overflow-y: auto`
- Badge hiển thị số lượng thông báo chưa đọc
- Icon thông báo với hover effect

## Cấu Trúc Dữ Liệu API

### Response từ `/api/notifications`:
```json
{
    "success": true,
    "data": {
        "notifications": [
            {
                "id": 1,
                "title": "Đơn hàng mới",
                "message": "Đơn hàng #12345 đã được tạo thành công",
                "type": "order",
                "is_read": false,
                "created_at": "15/01/2025 10:30",
                "time_ago": "2 giờ trước"
            }
        ],
        "unread_count": 3,
        "total_count": 3
    }
}
```

## Các Loại Thông Báo

1. **order** (Đơn hàng) - Màu xanh lá
2. **payment** (Thanh toán) - Màu xanh dương
3. **shipping** (Vận chuyển) - Màu vàng
4. **system** (Hệ thống) - Màu xám
5. **default** - Màu tím

## Tính Năng UI/UX

### Badge Thông Báo
- Hiển thị số lượng thông báo chưa đọc
- Ẩn khi không có thông báo chưa đọc
- Hiển thị "99+" khi có hơn 99 thông báo

### Dropdown Thông Báo
- Tối đa 3 thông báo hiển thị
- Thanh cuộn tự động khi có nhiều hơn
- Thông báo chưa đọc có background màu xanh nhạt
- Dot xanh bên phải cho thông báo chưa đọc
- Hover effect khi di chuột

### Tương Tác
- Click vào thông báo để đánh dấu đã đọc
- Click outside để đóng dropdown
- Tự động reload mỗi 30 giây
- Tích hợp với thông báo realtime

## Cách Test

### 1. Tạo Thông Báo Test
Truy cập: `http://localhost:8000/test-create-notifications`

### 2. Đăng Nhập
Đăng nhập với tài khoản có role "User" để xem thông báo

### 3. Kiểm Tra Chức Năng
- Badge hiển thị số lượng đúng
- Dropdown hiển thị tối đa 3 thông báo
- Thanh cuộn hoạt động
- Click để đánh dấu đã đọc
- Thông báo realtime cập nhật dropdown

## Lưu Ý Kỹ Thuật

1. **Authentication**: API yêu cầu user đã đăng nhập
2. **CSRF Protection**: Sử dụng CSRF token trong header
3. **Error Handling**: Xử lý lỗi 401, 404, 500
4. **Performance**: Giới hạn 3 thông báo để tối ưu tốc độ
5. **Real-time Integration**: Tự động reload khi nhận thông báo mới

## Tích Hợp Với Thông Báo Realtime

Khi nhận thông báo realtime mới:
1. Hiển thị browser notification
2. Phát âm thanh thông báo
3. Tự động reload thông báo từ database sau 1 giây
4. Cập nhật badge và dropdown

## Cấu Hình CSS

- Dropdown width: `20rem`
- Max height: `240px` (cho 3 thông báo)
- Overflow: `auto` (thanh cuộn)
- Z-index: `9999` (luôn hiển thị trên cùng)
- Animation: Fade in/out với transform

## Troubleshooting

### Lỗi 401 - Unauthorized
- Kiểm tra user đã đăng nhập
- Kiểm tra session còn hiệu lực

### Không Hiển Thị Thông Báo
- Kiểm tra API endpoint hoạt động
- Kiểm tra CSRF token
- Kiểm tra JavaScript console

### Badge Không Cập Nhật
- Kiểm tra hàm `updateNotificationBadge()`
- Kiểm tra dữ liệu `unread_count` từ API

### Thanh Cuộn Không Hoạt Động
- Kiểm tra CSS `max-height` và `overflow-y`
- Kiểm tra số lượng thông báo > 3