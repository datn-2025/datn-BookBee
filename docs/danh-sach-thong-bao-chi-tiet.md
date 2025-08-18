# Danh Sách Thông Báo Chi Tiết

## Mô tả chức năng
Trang danh sách thông báo chi tiết hiển thị tất cả thông báo của người dùng với giao diện tương tự như frontend, sử dụng API từ `NotificationController` để lấy dữ liệu.

## Tính năng chính
- Hiển thị danh sách thông báo với phân trang
- Phân loại thông báo theo tabs (All, Messages, Alerts)
- Đánh dấu thông báo đã đọc/chưa đọc
- Đánh dấu tất cả thông báo đã đọc
- Hiển thị số lượng thông báo mới
- Liên kết đến các trang liên quan (đơn hàng, ví tiền)
- Toast notifications cho feedback
- Responsive design

## Cấu trúc file

### 1. View Template
**File:** `resources/views/notifications/index.blade.php`
- Extends layout `layouts.app`
- Hiển thị header với số thông báo mới
- Tabs để phân loại thông báo
- Danh sách thông báo với icon và styling
- Pagination
- JavaScript để xử lý tương tác

### 2. Controller
**File:** `app/Http/Controllers/NotificationController.php`
- `index()`: Hiển thị trang danh sách thông báo
- `markAsRead($id)`: Đánh dấu một thông báo đã đọc
- `markAllAsRead()`: Đánh dấu tất cả thông báo đã đọc

### 3. Routes
**File:** `routes/web.php`
```php
Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
Route::patch('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::patch('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
```

### 4. Navigation
**File:** `resources/views/layouts/partials/navbar.blade.php`
- Thêm link "Xem tất cả thông báo" trong dropdown notifications
- Dẫn đến route `notifications.index`

## Giao diện

### Header
- Background màu xanh (#3b82f6)
- Tiêu đề "Notifications"
- Badge hiển thị số thông báo mới

### Tabs
- All: Hiển thị tất cả thông báo
- Messages: Placeholder cho tin nhắn
- Alerts: Placeholder cho cảnh báo

### Danh sách thông báo
- Icon tương ứng với loại thông báo:
  - `order`: Shopping bag icon (màu xanh lá)
  - `payment`: Credit card icon (màu xanh)
  - `wallet`: Wallet icon (màu vàng)
  - `system`: Cog icon (màu xám)
- Tiêu đề thông báo (có thể là link)
- Nội dung thông báo
- Thời gian tạo (sử dụng `diffForHumans()`)
- Trạng thái đã đọc/chưa đọc
- Button "Đánh dấu đã đọc" cho thông báo chưa đọc

### Tương tác
- Click vào thông báo để đi đến trang liên quan
- Đánh dấu đơn lẻ thông báo đã đọc
- Đánh dấu tất cả thông báo đã đọc
- Toast notifications cho feedback

## Cách sử dụng

1. **Truy cập trang:**
   - URL: `/notifications`
   - Hoặc click "Xem tất cả thông báo" trong dropdown navbar

2. **Xem thông báo:**
   - Thông báo chưa đọc có background màu xanh nhạt
   - Thông báo đã đọc có background trắng

3. **Đánh dấu đã đọc:**
   - Click button "Đánh dấu đã đọc" cho từng thông báo
   - Hoặc click "Đánh dấu tất cả đã đọc" ở cuối trang

4. **Điều hướng:**
   - Click vào tiêu đề thông báo để đi đến trang liên quan
   - Sử dụng pagination để xem thêm thông báo

## API sử dụng

### Lấy danh sách thông báo
```php
$notifications = Notification::where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->paginate(10);
```

### Đếm thông báo chưa đọc
```php
$unreadCount = Notification::where('user_id', $user->id)
    ->whereNull('read_at')
    ->count();
```

### Đánh dấu đã đọc
```php
// Đơn lẻ
$notification->read_at = now();
$notification->save();

// Tất cả
Notification::where('user_id', $user->id)
    ->whereNull('read_at')
    ->update(['read_at' => now()]);
```

## JavaScript Functions

### Tab Switching
```javascript
$('.tab-button').click(function() {
    // Chuyển đổi tab active
    // Hiển thị nội dung tương ứng
});
```

### Mark as Read
```javascript
$('.mark-as-read-btn').click(function() {
    // AJAX call để đánh dấu đã đọc
    // Cập nhật UI
    // Hiển thị toast
});
```

### Toast Notifications
```javascript
function showToast(message, type) {
    // Tạo và hiển thị toast notification
    // Auto remove sau 3 giây
}
```

## Styling

### CSS Classes
- `.notification-item`: Container cho mỗi thông báo
- `.tab-button`: Button cho tabs
- `.tab-content`: Nội dung của mỗi tab
- `.mark-as-read-btn`: Button đánh dấu đã đọc

### Colors
- Primary: `#3b82f6` (blue-500)
- Success: `#10b981` (green-500)
- Warning: `#f59e0b` (yellow-500)
- Danger: `#ef4444` (red-500)
- Gray: `#6b7280` (gray-500)

## Kết quả mong muốn

1. **Giao diện:**
   - Tương tự như mockup với header xanh, tabs, và danh sách thông báo
   - Responsive trên các thiết bị
   - Icons và colors phù hợp với từng loại thông báo

2. **Chức năng:**
   - Hiển thị đúng danh sách thông báo của user
   - Phân trang hoạt động
   - Đánh dấu đã đọc hoạt động
   - Navigation đến các trang liên quan
   - Toast feedback cho user actions

3. **Performance:**
   - Load nhanh với pagination
   - AJAX calls không reload trang
   - Smooth transitions và animations

## Lưu ý

- Chỉ user đã đăng nhập mới có thể truy cập
- Chỉ hiển thị thông báo của user hiện tại
- Sử dụng middleware auth để bảo vệ routes
- Toast notifications tự động biến mất sau 3 giây
- Responsive design cho mobile và desktop