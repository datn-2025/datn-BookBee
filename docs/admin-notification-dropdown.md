# Admin Notification Dropdown

## Mô tả chức năng

Hệ thống thông báo dropdown cho admin panel, hiển thị thông báo theo dạng: **tiêu đề + nội dung ngắn + thời gian**. Tương tự như hệ thống thông báo frontend nhưng được tối ưu cho admin.

## Cấu trúc file

### 1. Backend Layout
**File:** `resources/views/layouts/backend.blade.php`
- Dropdown thông báo admin với ID phù hợp
- Button toggle với icon bell và badge số lượng
- Container hiển thị danh sách thông báo
- Link "Xem tất cả thông báo" dẫn đến `/admin/notifications`

### 2. JavaScript Handler
**File:** `public/js/admin-notifications.js`
- Xử lý toggle dropdown
- Lấy thông báo từ API
- Hiển thị thông báo trong dropdown
- Cập nhật badge số lượng
- Đánh dấu thông báo đã đọc
- Lắng nghe thông báo real-time qua Pusher
- Hiển thị browser notification
- Phát âm thanh thông báo

### 3. Controller
**File:** `app/Http/Controllers/Admin/AdminNotificationController.php`
- `index()`: Hiển thị trang danh sách thông báo admin
- `all()`: API lấy thông báo cho dropdown (limit 10)
- `markAsRead()`: Đánh dấu một thông báo đã đọc
- `markAllAsRead()`: Đánh dấu tất cả thông báo đã đọc

### 4. View Template
**File:** `resources/views/admin/notifications/index.blade.php`
- Trang danh sách thông báo admin đầy đủ
- Phân trang và tìm kiếm
- Đánh dấu đã đọc từng thông báo hoặc tất cả
- Icon và màu sắc theo loại thông báo

### 5. Routes
**File:** `routes/web.php`
```php
// Admin Notifications
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('index');
    Route::patch('/{id}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::patch('/mark-all-read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
});
```

**File:** `routes/api.php`
```php
// Admin Notification API routes
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/notifications/all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'all']);
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead']);
});
```

## Giao diện

### Dropdown Structure
- **Header**: "Thông báo Admin" với badge số lượng thông báo mới
- **Notification List**: Danh sách thông báo với:
  - Icon theo loại thông báo (màu sắc khác nhau)
  - Tiêu đề thông báo
  - Nội dung ngắn
  - Thời gian (sử dụng `diffForHumans()`)
  - Trạng thái đã đọc/chưa đọc
- **Footer**: Link "Xem tất cả thông báo"
- **Empty State**: Hiển thị khi chưa có thông báo

### Icon và Màu sắc theo Loại
- `new_order`: Shopping bag (màu xanh lá)
- `order_cancelled`: X-circle (màu đỏ)
- `refund_request`: Undo (màu vàng)
- `low_stock`: Error (màu đỏ)
- `new_user`: User-plus (màu xanh)
- `payment`: Credit card (màu xanh)
- `system`: Cog (màu xám)
- `default`: Bell (màu xanh)

## Cách sử dụng

### 1. Xem Thông báo
- Click vào icon bell trên navbar admin
- Dropdown sẽ hiển thị 10 thông báo mới nhất
- Badge hiển thị số thông báo chưa đọc

### 2. Đánh dấu Đã đọc
- Click vào thông báo để đánh dấu đã đọc
- Thông báo đã đọc sẽ có màu mờ hơn

### 3. Xem Tất cả
- Click "Xem tất cả thông báo" để đi đến trang danh sách đầy đủ
- URL: `/admin/notifications`

### 4. Trang Danh sách Đầy đủ
- Hiển thị tất cả thông báo với phân trang
- Đánh dấu từng thông báo hoặc tất cả đã đọc
- Tìm kiếm và lọc thông báo

## API Endpoints

### GET `/api/admin/notifications/all`
- Lấy 10 thông báo mới nhất cho dropdown
- Trả về: `{ success, notifications, unread_count }`

### PATCH `/api/admin/notifications/{id}/read`
- Đánh dấu một thông báo đã đọc
- Trả về: `{ success, message }`

### PATCH `/api/admin/notifications/mark-all-read`
- Đánh dấu tất cả thông báo đã đọc
- Trả về: `{ success, message, updated_count }`

## Real-time Features

### Pusher Integration
- Lắng nghe các channel thông báo admin:
  - `admin-notifications`
  - `new-order`
  - `order-cancelled`
  - `refund-request`
  - `low-stock`
  - `new-user`

### Browser Notifications
- Yêu cầu quyền thông báo từ browser
- Hiển thị thông báo desktop khi có thông báo mới
- Phát âm thanh thông báo

### Auto Refresh
- Tự động tải lại thông báo mỗi 30 giây
- Cập nhật badge và dropdown real-time

## JavaScript Functions

### Core Functions
- `toggleAdminNotificationDropdown()`: Toggle hiển thị dropdown
- `addAdminNotificationToDropdown(notification)`: Thêm thông báo vào dropdown
- `loadAdminNotificationsFromDatabase()`: Tải thông báo từ API
- `markAdminNotificationAsRead(id)`: Đánh dấu thông báo đã đọc
- `updateAdminNotificationCounter(count)`: Cập nhật badge số lượng

### Helper Functions
- `getAdminNotificationIcon(type)`: Lấy icon theo loại
- `getAdminNotificationColor(type)`: Lấy màu theo loại
- `showAdminNotification(title, message, type)`: Hiển thị browser notification
- `playAdminNotificationSound()`: Phát âm thanh

## Styling

### CSS Classes
- `.admin-notification-dropdown`: Container dropdown
- `.notification-item`: Item thông báo
- `.notification-item.unread`: Thông báo chưa đọc
- `.notification-item.read`: Thông báo đã đọc
- `#admin-notification-badge`: Badge số lượng
- `#admin-notification-list`: Container danh sách

### Responsive Design
- Dropdown responsive trên mobile
- Max-height với scroll cho danh sách dài
- Touch-friendly cho mobile

## Kết quả mong muốn

### Chức năng hoạt động
✅ Dropdown hiển thị thông báo admin
✅ Badge hiển thị số thông báo chưa đọc
✅ Đánh dấu thông báo đã đọc
✅ Real-time notifications qua Pusher
✅ Browser notifications
✅ Âm thanh thông báo
✅ Trang danh sách thông báo đầy đủ
✅ API endpoints hoạt động
✅ Responsive design

### Trải nghiệm người dùng
- Admin có thể xem thông báo nhanh chóng
- Thông báo real-time không bỏ lỡ
- Giao diện thân thiện và dễ sử dụng
- Tích hợp mượt mà với admin panel

## Lưu ý kỹ thuật

1. **Authentication**: Sử dụng `auth:admin` middleware
2. **Database**: Thông báo lưu trong bảng `notifications` với `notifiable_type = 'App\Models\Admin'`
3. **Permissions**: Không cần permission đặc biệt, chỉ cần đăng nhập admin
4. **Performance**: Limit 10 thông báo trong dropdown, phân trang trong trang danh sách
5. **Security**: CSRF token và validation đầy đủ

## Troubleshooting

### Lỗi thường gặp
1. **Dropdown không hiển thị**: Kiểm tra JavaScript console và API endpoints
2. **Badge không cập nhật**: Kiểm tra API response và function `updateAdminNotificationCounter`
3. **Real-time không hoạt động**: Kiểm tra Pusher configuration và channels
4. **Thông báo không đánh dấu đã đọc**: Kiểm tra CSRF token và API endpoints

### Debug
- Mở Developer Tools > Console để xem lỗi JavaScript
- Kiểm tra Network tab để xem API requests
- Verify database có thông báo với `notifiable_type = 'App\Models\Admin'`