# Danh Sách Thông Báo Người Dùng

## Mô Tả Chức Năng

Chức năng hiển thị tất cả thông báo của người dùng trong một trang riêng biệt, thay vì chỉ hiển thị 3 thông báo mới nhất trong dropdown.

## Tính Năng

- ✅ Hiển thị tất cả thông báo của người dùng với phân trang (20 thông báo/trang)
- ✅ Phân biệt thông báo đã đọc/chưa đọc bằng màu sắc và border
- ✅ Đánh dấu thông báo đã đọc từng cái một
- ✅ Đánh dấu tất cả thông báo đã đọc cùng lúc
- ✅ Hiển thị icon phù hợp theo loại thông báo (order, payment, wallet, system)
- ✅ Hiển thị thời gian tạo và thời gian tương đối (time ago)
- ✅ Link "Xem tất cả thông báo" trong dropdown thông báo
- ✅ Responsive design với Bootstrap
- ✅ Toast notifications cho feedback người dùng

## Các File Liên Quan

### 1. API Controller
**File:** `app/Http/Controllers/Api/NotificationController.php`
- `all()`: API endpoint lấy tất cả thông báo với phân trang

### 2. Web Controller
**File:** `app/Http/Controllers/NotificationController.php`
- `index()`: Hiển thị trang danh sách thông báo
- `markAsRead($id)`: Đánh dấu thông báo đã đọc
- `markAllAsRead()`: Đánh dấu tất cả thông báo đã đọc

### 3. Routes
**File:** `routes/api.php`
```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications/all', [NotificationController::class, 'all']);
});
```

**File:** `routes/web.php`
```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});
```

### 4. View Template
**File:** `resources/views/notifications/index.blade.php`
- Giao diện danh sách thông báo với Bootstrap
- JavaScript xử lý đánh dấu đã đọc
- Toast notifications
- Responsive design

### 5. Frontend JavaScript
**File:** `public/js/frontend-notifications.js`
- Thêm link "Xem tất cả thông báo" vào dropdown
- Cập nhật hàm `displayNotificationsInDropdown()`

## Cách Sử Dụng

### 1. Truy Cập Trang Danh Sách Thông Báo

**URL:** `/notifications`

**Yêu cầu:** Người dùng phải đăng nhập

### 2. Từ Dropdown Thông Báo

1. Click vào icon thông báo trên navbar
2. Click vào link "Xem tất cả thông báo" ở cuối dropdown

### 3. Đánh Dấu Thông Báo Đã Đọc

**Đánh dấu từng thông báo:**
- Click nút "Đánh dấu đã đọc" trên từng thông báo

**Đánh dấu tất cả:**
- Click nút "Đánh dấu tất cả đã đọc" ở header

## API Endpoints

### 1. Lấy Tất Cả Thông Báo
```http
GET /api/notifications/all?page=1
```

**Response:**
```json
{
    "success": true,
    "data": {
        "notifications": [
            {
                "id": 1,
                "title": "Đơn hàng mới",
                "message": "Bạn có đơn hàng mới #12345",
                "type": "order",
                "is_read": false,
                "created_at": "15/01/2025 10:30",
                "time_ago": "2 giờ trước"
            }
        ],
        "unread_count": 5,
        "total_count": 25,
        "current_page": 1,
        "last_page": 2,
        "per_page": 20
    }
}
```

### 2. Đánh Dấu Thông Báo Đã Đọc
```http
PATCH /notifications/{id}/read
```

### 3. Đánh Dấu Tất Cả Đã Đọc
```http
PATCH /notifications/mark-all-read
```

## Giao Diện

### 1. Trang Danh Sách Thông báo

- **Header:** Tiêu đề + badge số thông báo chưa đọc + nút "Đánh dấu tất cả đã đọc"
- **Danh sách:** Mỗi thông báo hiển thị:
  - Icon theo loại thông báo
  - Tiêu đề và nội dung
  - Thời gian tạo và time ago
  - Trạng thái đã đọc/chưa đọc
  - Nút đánh dấu đã đọc (nếu chưa đọc)
- **Pagination:** Phân trang Bootstrap
- **Empty State:** Hiển thị khi chưa có thông báo

### 2. Dropdown Thông Báo

- Hiển thị tối đa 3 thông báo mới nhất
- Link "Xem tất cả thông báo" ở cuối (chỉ hiện khi có thông báo)

## Loại Thông Báo & Icon

| Loại | Icon | Màu |
|------|------|-----|
| `order` | `bx-shopping-bag` | Xanh lá |
| `payment` | `bx-credit-card` | Xanh dương |
| `wallet` | `bx-wallet` | Vàng |
| `system` | `bx-cog` | Xám |
| Mặc định | `bx-bell` | Xanh dương |

## Trạng Thái Thông Báo

### Chưa Đọc
- Background: `rgba(13, 110, 253, 0.05)`
- Border trái: Xanh dương 3px
- Nút: "Đánh dấu đã đọc"

### Đã Đọc
- Background: Trắng
- Không có border đặc biệt
- Badge: "Đã đọc" (màu xanh)

## Test Chức Năng

### 1. Test Routes
```bash
php artisan route:list --name=notifications
```

### 2. Test Truy Cập (Cần Đăng Nhập)
```bash
# Web route
curl http://localhost:8000/notifications

# API route
curl http://localhost:8000/api/notifications/all
```

### 3. Test Giao Diện
1. Đăng nhập vào hệ thống
2. Truy cập `/notifications`
3. Kiểm tra hiển thị thông báo
4. Test đánh dấu đã đọc
5. Test phân trang
6. Kiểm tra dropdown có link "Xem tất cả"

## Kết Quả Mong Muốn

✅ **Người dùng có thể:**
- Xem tất cả thông báo của mình trong một trang riêng
- Phân biệt thông báo đã đọc/chưa đọc
- Đánh dấu thông báo đã đọc từng cái hoặc tất cả
- Truy cập trang từ dropdown thông báo
- Xem thông báo với phân trang

✅ **Hệ thống:**
- API endpoint mới không giới hạn số lượng thông báo
- Web controller và routes hoạt động đúng
- Giao diện responsive và thân thiện
- JavaScript xử lý tương tác mượt mà
- Toast notifications cho feedback

## Lưu Ý Kỹ Thuật

1. **Middleware Auth:** Tất cả routes đều yêu cầu đăng nhập
2. **Phân Trang:** Sử dụng Laravel pagination (20 items/page)
3. **AJAX:** Đánh dấu đã đọc không reload trang
4. **Bootstrap:** Sử dụng Bootstrap 5 cho giao diện
5. **Icons:** Sử dụng BoxIcons (bx-*)
6. **Toast:** Bootstrap toast cho thông báo feedback
7. **Responsive:** Tương thích mobile và desktop