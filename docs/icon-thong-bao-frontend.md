# Icon Thông Báo Frontend

## Mô tả chức năng

Chức năng icon thông báo frontend cho phép khách hàng đã đăng nhập xem các thông báo realtime về trạng thái đơn hàng của họ thông qua một dropdown menu trên thanh điều hướng.

## Tính năng chính

### 1. Icon thông báo với badge
- **Vị trí**: Thanh điều hướng frontend, giữa wishlist và cart
- **Hiển thị**: Chỉ hiển thị khi người dùng đã đăng nhập (`@auth`)
- **Badge**: Hiển thị số lượng thông báo chưa đọc (tối đa 99+)
- **Icon**: SVG bell icon với hover effect

### 2. Dropdown thông báo
- **Kích thước**: 20rem width, max-height 300px với scroll
- **Header**: Tiêu đề "Thông báo" và số lượng thông báo
- **Danh sách**: Hiển thị tối đa 5 thông báo mới nhất
- **Empty state**: Hiển thị khi chưa có thông báo nào

### 3. Thông báo realtime
- **Kênh**: `customer-orders` channel
- **Sự kiện**: `OrderStatusUpdated`
- **Loại thông báo**: Cập nhật trạng thái đơn hàng
- **Hiệu ứng**: Fade-in animation cho thông báo mới

## Cách sử dụng

### Cho khách hàng
1. **Đăng nhập** vào tài khoản
2. **Xem icon thông báo** trên thanh điều hướng (bên cạnh wishlist)
3. **Click vào icon** để mở dropdown thông báo
4. **Xem thông báo** về trạng thái đơn hàng
5. **Click bên ngoài** để đóng dropdown

### Khi có thông báo mới
1. **Badge đỏ** sẽ hiển thị số lượng thông báo
2. **Thông báo browser** (nếu được cho phép)
3. **Âm thanh thông báo** (nếu có file âm thanh)
4. **Thông báo mới** sẽ xuất hiện ở đầu danh sách

## Cấu trúc mã nguồn

### 1. HTML Template
**File**: `resources/views/layouts/partials/navbar.blade.php`

```html
{{-- Notifications --}}
@auth
<div class="notification-dropdown" style="position: relative;">
    <button type="button" class="notification-btn" onclick="toggleNotificationDropdown()">
        <!-- Bell Icon -->
        <svg>...</svg>
        <span id="notification-badge" class="notification-badge">0</span>
    </button>
    
    <div id="notification-dropdown" class="notification-dropdown-menu">
        <!-- Header -->
        <div>
            <h6>Thông báo</h6>
            <span id="notification-count">0 thông báo mới</span>
        </div>
        
        <!-- Notification List -->
        <div id="notification-list">
            <!-- Empty state hoặc danh sách thông báo -->
        </div>
    </div>
</div>
@endauth
```

### 2. JavaScript Functions
**File**: `public/js/frontend-notifications.js`

#### Các hàm chính:

```javascript
// Toggle dropdown
function toggleNotificationDropdown()

// Hiển thị/ẩn dropdown
function showNotificationDropdown()
function hideNotificationDropdown()

// Thêm thông báo mới
function addFrontendNotificationToDropdown(notification)

// Cập nhật counter và badge
function updateFrontendNotificationCounter()

// Lắng nghe thông báo khách hàng
function listenForCustomerNotifications()

// Khởi tạo hệ thống
function initializeFrontendNotifications()
```

#### Cấu trúc thông báo:
```javascript
{
    title: 'Cập nhật đơn hàng',
    message: 'Đơn hàng #123 đã được cập nhật trạng thái: processing',
    time: 'Vừa xong',
    type: 'order'
}
```

### 3. Styling
- **Inline CSS**: Sử dụng inline styles cho tính tương thích
- **Responsive**: Dropdown tự động điều chỉnh vị trí
- **Animations**: Smooth transitions và fade effects
- **Colors**: Theo theme của website

### 4. Laravel Echo Integration
**File**: `resources/views/layouts/app.blade.php`

```html
<!-- Scripts -->
<script src="{{ asset('js/frontend-notifications.js') }}"></script>

<!-- User data for JavaScript -->
<script>
    window.userRole = '{{ auth()->user()->role ?? "guest" }}';
    window.userId = {{ auth()->id() ?? 'null' }};
</script>
```

## Kết quả mong muốn

### 1. Giao diện
- ✅ Icon thông báo hiển thị trên navbar
- ✅ Badge đỏ hiển thị số lượng thông báo
- ✅ Dropdown mở/đóng mượt mà
- ✅ Danh sách thông báo có scroll
- ✅ Empty state khi chưa có thông báo

### 2. Chức năng
- ✅ Nhận thông báo realtime từ server
- ✅ Hiển thị thông báo mới ở đầu danh sách
- ✅ Giới hạn 5 thông báo mới nhất
- ✅ Cập nhật counter và badge tự động
- ✅ Đóng dropdown khi click bên ngoài

### 3. Trải nghiệm người dùng
- ✅ Thông báo browser (nếu được cho phép)
- ✅ Âm thanh thông báo (nếu có)
- ✅ Hiệu ứng mượt mà
- ✅ Responsive trên mobile

## Lưu ý kỹ thuật

### 1. Điều kiện hiển thị
- Chỉ hiển thị cho người dùng đã đăng nhập
- Kiểm tra `userRole` và `userId` từ meta tags
- Xử lý trường hợp Echo chưa được khởi tạo

### 2. Performance
- Giới hạn số lượng thông báo hiển thị
- Sử dụng event delegation
- Lazy loading cho âm thanh

### 3. Browser Compatibility
- Kiểm tra Notification API support
- Fallback cho trình duyệt cũ
- Cross-browser CSS compatibility

### 4. Security
- Validate user permissions
- Sanitize notification content
- CSRF protection cho API calls

## Troubleshooting

### Lỗi thường gặp:

1. **Icon không hiển thị**
   - Kiểm tra người dùng đã đăng nhập chưa
   - Xem console có lỗi JavaScript không

2. **Dropdown không mở**
   - Kiểm tra function `toggleNotificationDropdown` có được load không
   - Xem CSS z-index có bị conflict không

3. **Không nhận được thông báo**
   - Kiểm tra Laravel Echo đã khởi tạo chưa
   - Xem Pusher connection có thành công không
   - Kiểm tra user role và channel permissions

4. **Thông báo không hiển thị đúng**
   - Kiểm tra cấu trúc dữ liệu notification
   - Xem function `addFrontendNotificationToDropdown` có lỗi không

### Debug commands:
```javascript
// Kiểm tra Echo
console.log(window.Echo);

// Kiểm tra user data
console.log(window.userRole, window.userId);

// Test thêm thông báo
addFrontendNotificationToDropdown({
    title: 'Test',
    message: 'Test message',
    time: 'Vừa xong',
    type: 'order'
});
```

## Tích hợp với backend

Chức năng này tích hợp với:
- **Event**: `OrderStatusUpdated`
- **Channel**: `customer-orders`
- **Model**: `Order`
- **Pusher**: Realtime broadcasting

Khi có cập nhật đơn hàng, server sẽ broadcast event và frontend sẽ tự động nhận và hiển thị thông báo cho khách hàng.