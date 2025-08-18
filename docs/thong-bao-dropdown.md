# Chức năng Thông báo Dropdown

## Mô tả chức năng

Chức năng thông báo dropdown cho phép hiển thị thông báo realtime trong dropdown của admin panel khi có đơn hàng mới được tạo. Thông báo sẽ được thêm vào danh sách dropdown với định dạng: tiêu đề + message + thời gian.

## Tính năng chính

- **Hiển thị realtime**: Thông báo xuất hiện ngay lập tức khi có đơn hàng mới
- **Giới hạn số lượng**: Chỉ hiển thị tối đa 3 thông báo mới nhất
- **Thanh cuộn**: Tự động thêm thanh cuộn khi có nhiều hơn 3 thông báo
- **Hiệu ứng**: Thông báo mới có hiệu ứng fade-in mượt mà
- **Cập nhật badge**: Tự động cập nhật số lượng thông báo trên badge

## Cách sử dụng

### 1. Đăng nhập với quyền Admin

Để nhận thông báo dropdown, bạn cần đăng nhập với tài khoản có role "Admin".

### 2. Tạo đơn hàng mới

Khi có đơn hàng mới được tạo từ frontend, hệ thống sẽ:
- Phát sự kiện `OrderCreated`
- Broadcast trên channel `admin-orders`
- Hiển thị thông báo trong dropdown

### 3. Xem thông báo

Click vào icon thông báo trên header để xem danh sách thông báo trong dropdown.

## Cấu trúc mã nguồn

### JavaScript Functions

#### `addNotificationToDropdown(notification)`

```javascript
/**
 * Thêm thông báo mới vào dropdown
 * @param {Object} notification - Thông tin thông báo
 * @param {string} notification.title - Tiêu đề thông báo
 * @param {string} notification.message - Nội dung thông báo
 * @param {string} notification.time - Thời gian thông báo
 * @param {string} notification.icon - Icon class (bx-*)
 * @param {string} notification.type - Loại thông báo (success, info, warning, danger)
 */
```

**Chức năng:**
- Tạo HTML cho thông báo mới
- Thêm vào đầu danh sách dropdown
- Giới hạn tối đa 3 thông báo
- Thêm hiệu ứng fade-in
- Cập nhật header thông báo

#### `getNotificationColor(type)`

```javascript
/**
 * Lấy màu sắc cho loại thông báo
 * @param {string} type - Loại thông báo
 * @returns {string} - Màu sắc tương ứng
 */
```

**Các loại màu sắc:**
- `success`: Xanh lá (đơn hàng mới)
- `info`: Xanh dương (thông tin)
- `warning`: Vàng (cảnh báo)
- `danger`: Đỏ (lỗi)
- `primary`: Xanh chính (mặc định)

#### `updateNotificationHeader()`

```javascript
/**
 * Cập nhật header thông báo (số lượng)
 */
```

**Chức năng:**
- Cập nhật badge số lượng thông báo
- Cập nhật text trong tab "All"

### Event Listener

#### `listenForAdminNotifications()`

```javascript
window.Echo.channel('admin-orders')
    .listen('.order.created', (data) => {
        // Hiển thị toast notification
        showNotification(...);
        
        // Thêm thông báo vào dropdown
        addNotificationToDropdown({
            title: 'Đơn hàng mới!',
            message: `Đơn hàng từ ${data.customer_name} - ${data.total_amount}đ`,
            time: 'Vừa xong',
            icon: 'bx-shopping-bag',
            type: 'success'
        });
        
        // Phát âm thanh và cập nhật badge
        playNotificationSound();
        updateNotificationBadge();
    });
```

## Cấu trúc HTML Dropdown

```html
<div id="all-noti-tab" class="tab-pane fade show active">
    <div class="pe-2" data-simplebar style="max-height: 300px;">
        <!-- Thông báo sẽ được thêm vào đây -->
        <div class="text-reset notification-item d-block dropdown-item position-relative">
            <div class="d-flex">
                <div class="avatar-xs me-3 flex-shrink-0">
                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-16">
                        <i class="bx bx-shopping-bag"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <a href="#!" class="stretched-link">
                        <h6 class="mt-0 mb-2 lh-base">Đơn hàng mới!</h6>
                    </a>
                    <p class="mb-1 fs-13 text-muted">Đơn hàng từ Nguyễn Văn A - 150,000đ</p>
                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                        <i class="mdi mdi-clock-outline"></i> Vừa xong
                    </p>
                </div>
                <div class="px-2 fs-15">
                    <div class="form-check notification-check">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

## Kết quả mong muốn

### Khi có đơn hàng mới:

1. **Toast notification** xuất hiện ở góc màn hình
2. **Âm thanh thông báo** được phát
3. **Badge thông báo** trên header được cập nhật
4. **Dropdown thông báo** được thêm item mới với:
   - Icon giỏ hàng màu xanh
   - Tiêu đề: "Đơn hàng mới!"
   - Message: "Đơn hàng từ [Tên khách hàng] - [Số tiền]đ"
   - Thời gian: "Vừa xong"
   - Hiệu ứng fade-in mượt mà

### Quản lý danh sách:

- Chỉ hiển thị **tối đa 3 thông báo** mới nhất
- Thông báo cũ nhất sẽ bị **xóa tự động** khi có thông báo mới
- **Thanh cuộn** xuất hiện khi cần thiết
- **Header badge** hiển thị tổng số thông báo chưa đọc

## Lưu ý kỹ thuật

- Chức năng chỉ hoạt động với user có role "Admin" (không phân biệt hoa thường)
- Cần đảm bảo Laravel Echo và Pusher đã được cấu hình đúng
- File JavaScript: `public/js/notifications.js`
- Channel: `admin-orders`
- Event: `order.created`