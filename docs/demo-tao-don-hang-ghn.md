# Demo Tạo Đơn Hàng GHN Cho Admin

## Tình Huống Hiện Tại
Hệ thống hiện tại **chưa có đơn hàng nào có mã GHN**, đó là lý do admin không thấy thông tin GHN trong trang chi tiết đơn hàng.

## Cách Tạo Đơn Hàng Để Test GHN

### Phương Án 1: Tạo Đơn Hàng Mới Từ Frontend

#### Bước 1: Truy Cập Trang Đặt Hàng
1. Mở trình duyệt và vào: `http://localhost:8000`
2. Đăng nhập tài khoản khách hàng
3. Thêm sách vào giỏ hàng
4. Vào trang checkout: `http://localhost:8000/orders/checkout`

#### Bước 2: Điền Thông Tin Giao Hàng
1. **Chọn phương thức**: "Giao hàng tận nơi"
2. **Địa chỉ giao hàng**: Chọn hoặc thêm địa chỉ mới
   - Tỉnh/Thành phố: Hồ Chí Minh
   - Quận/Huyện: Quận 1
   - Phường/Xã: Phường Bến Nghé
   - Địa chỉ chi tiết: 123 Nguyễn Huệ
3. **Phương thức vận chuyển**: Chọn "Giao hàng tiết kiệm"
4. **Thanh toán**: Chọn phương thức thanh toán

#### Bước 3: Hoàn Tất Đặt Hàng
1. Click "Đặt hàng"
2. Hoàn tất thanh toán (nếu cần)
3. Hệ thống sẽ tự động tạo đơn GHN (nếu cấu hình đúng)

### Phương Án 2: Tạo Đơn GHN Thủ Công Từ Admin

#### Bước 1: Tìm Đơn Hàng Phù Hợp
1. Vào admin panel: `http://localhost:8000/admin`
2. Vào **Quản lý đơn hàng**
3. Tìm đơn hàng có:
   - Phương thức: "Giao hàng tận nơi"
   - Trạng thái: "Chờ Xác Nhận" hoặc "Đã Xác Nhận"
   - Địa chỉ đầy đủ

#### Bước 2: Tạo Đơn GHN
1. Click vào đơn hàng để xem chi tiết
2. Tìm phần **"Thông tin vận chuyển GHN"** bên phải
3. Click nút **"Tạo đơn hàng GHN"**
4. Chờ hệ thống xử lý và hiển thị kết quả

## Kết Quả Mong Đợi

### Khi Tạo Thành Công
```
✅ Tạo đơn hàng GHN thành công. Mã vận đơn: L338UP

┌─ Thông tin vận chuyển GHN ─────────────┐
│ ✅ Đã tạo vận đơn                      │
│                                        │
│ 🚚 Mã vận đơn GHN: L338UP             │
│ ⚙️  Loại dịch vụ: ID 2                │
│ 📅 Ngày giao dự kiến: 28/01/2025      │
│                                        │
│ 📊 Trạng thái vận chuyển              │
│ ● Chờ lấy hàng                        │
│                                        │
│ [🔄 Cập nhật theo dõi] [❌ Hủy GHN]   │
└────────────────────────────────────────┘
```

### Khi Tạo Thất Bại
```
❌ Không thể tạo đơn hàng GHN. Vui lòng kiểm tra thông tin địa chỉ và thử lại.
```

## Kiểm Tra Cấu Hình GHN

### 1. Kiểm Tra File .env
```env
# GHN Configuration
GHN_API_URL=https://dev-online-gateway.ghn.vn
GHN_API_KEY=your_ghn_token_here
GHN_SHOP_ID=your_shop_id_here
GHN_SHOP_DISTRICT_ID=1442
GHN_SHOP_WARD_CODE=21211
```

### 2. Kiểm Tra Routes
Đảm bảo các routes sau đã được định nghĩa:
```php
// Admin GHN routes
Route::post('/{id}/ghn/create', [OrderController::class, 'createGhnOrder'])->name('ghn.create');
Route::post('/{id}/ghn/update-tracking', [OrderController::class, 'updateGhnTracking'])->name('ghn.update-tracking');
Route::post('/{id}/ghn/cancel', [OrderController::class, 'cancelGhnOrder'])->name('ghn.cancel');
```

### 3. Kiểm Tra Database
Đảm bảo bảng `orders` có các trường:
- `ghn_order_code`
- `ghn_service_type_id`
- `expected_delivery_date`
- `ghn_tracking_data`

## Troubleshooting

### Lỗi "Chỉ có thể tạo đơn GHN cho đơn hàng giao hàng tận nơi"
**Giải pháp:**
1. Kiểm tra `delivery_method` của đơn hàng
2. Đảm bảo = 'delivery'

### Lỗi "Chỉ có thể tạo đơn GHN khi đơn hàng ở trạng thái..."
**Giải pháp:**
1. Cập nhật trạng thái đơn hàng thành "Đã Xác Nhận"
2. Hoặc "Chờ Xác Nhận"

### Lỗi "Không thể tạo đơn hàng GHN"
**Giải pháp:**
1. Kiểm tra thông tin địa chỉ có đầy đủ `district_id` và `ward_code`
2. Kiểm tra cấu hình GHN trong file .env
3. Kiểm tra log Laravel: `storage/logs/laravel.log`

### Không Thấy Nút "Tạo Đơn Hàng GHN"
**Nguyên nhân:**
- Đơn hàng không phải giao hàng tận nơi
- Đã có mã GHN rồi
- Trạng thái không phù hợp

**Giải pháp:**
1. Kiểm tra phương thức giao hàng
2. Kiểm tra trạng thái đơn hàng
3. Refresh trang

## Sau Khi Có Đơn GHN Đầu Tiên

### Chức Năng Có Thể Sử Dụng
1. **Cập nhật theo dõi**: Lấy thông tin mới từ GHN
2. **Xem lịch sử vận chuyển**: Timeline di chuyển hàng hóa
3. **Hủy liên kết**: Xóa thông tin GHN khỏi đơn hàng

### Thông Tin Hiển Thị
- Mã vận đơn GHN
- Loại dịch vụ (1: nhanh, 2: tiêu chuẩn)
- Ngày giao dự kiến
- Trạng thái hiện tại
- Mô tả chi tiết

## Liên Hệ Hỗ Trợ

Nếu gặp vấn đề khi tạo đơn GHN:
1. Chụp ảnh màn hình lỗi
2. Kiểm tra log Laravel
3. Liên hệ bộ phận IT với thông tin:
   - ID đơn hàng
   - Thông báo lỗi
   - Thời gian xảy ra

---

**Lưu ý**: Sau khi tạo thành công đơn GHN đầu tiên, admin sẽ thấy đầy đủ thông tin và có thể sử dụng các chức năng quản lý GHN như mô tả trong tài liệu.