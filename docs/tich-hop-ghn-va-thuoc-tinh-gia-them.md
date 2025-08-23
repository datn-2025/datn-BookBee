# Tích Hợp GHN API và Thuộc Tính Giá Thêm cho Đặt Trước Sách

## Mô Tả Chức Năng

Chức năng này tích hợp API của Giao Hàng Nhanh (GHN) để:
- Lấy danh sách tỉnh/thành, quận/huyện, phường/xã
- Tính phí vận chuyển tự động
- Xử lý thuộc tính sách với giá thêm trong đặt trước
- Hiển thị chi tiết giá bao gồm giá cơ bản, phí thuộc tính và phí vận chuyển

## Các File Đã Được Cập Nhật

### 1. Frontend - Form Đặt Trước
**File**: `resources/views/preorders/create.blade.php`

**Thay đổi chính**:
- Thêm section hiển thị phí vận chuyển với ghi chú "Miễn phí cho đặt trước"
- Cập nhật section tổng tiền với breakdown chi tiết:
  - Giá sách
  - Phí thuộc tính 
  - Phí vận chuyển (gạch ngang + "Miễn phí")
- Tích hợp JavaScript để:
  - Load tỉnh/thành từ GHN API
  - Load quận/huyện khi chọn tỉnh
  - Load phường/xã khi chọn quận
  - Tính phí vận chuyển tự động
  - Cập nhật giá theo thuộc tính được chọn

### 2. Backend - Controller
**File**: `app/Http/Controllers/PreOrderController.php`

**Thay đổi chính**:
- Cập nhật phương thức `store()` để:
  - Tính giá cơ bản từ sách
  - Tính giá thêm từ thuộc tính được chọn
  - Sử dụng relationship `bookAttributeValues()` để lấy thông tin giá thêm
  - Tổng hợp giá cuối cùng = giá cơ bản + phí thuộc tính
  - Đặt phí ship = 0 (miễn phí cho đặt trước)

### 3. API Routes
**File**: `routes/api.php`

**Thay đổi chính**:
- Thay đổi route `/provinces` từ POST sang GET
- Thêm route `/calculate-fee` cho tính phí vận chuyển
- Thêm alias `/shipping-fee` để tương thích ngược

### 4. GHN Controller
**File**: `app/Http/Controllers/GhnController.php`

**Thay đổi chính**:
- Cập nhật response của `calculateShippingFee()` để bao gồm field `fee` ở level root
- Đảm bảo tương thích với frontend đang expect

### 5. View Chi Tiết Đặt Trước
**File**: `resources/views/preorders/show.blade.php`

**Thay đổi chính**:
- Thay thế hiển thị "Tổng tiền" đơn giản bằng breakdown chi tiết:
  - Giá cơ bản
  - Phí thuộc tính (nếu có)
  - Đơn giá
  - Số lượng
  - Tổng tiền
  - Ghi chú miễn phí vận chuyển

## Cách Sử Dụng

### 1. Đặt Trước Sách Vật Lý
1. Chọn sách và định dạng vật lý
2. Chọn số lượng
3. Chọn thuộc tính (nếu có) - giá sẽ tự động cập nhật
4. Nhập thông tin khách hàng
5. Chọn địa chỉ giao hàng:
   - Chọn tỉnh/thành → tự động load quận/huyện
   - Chọn quận/huyện → tự động load phường/xã
   - Nhập địa chỉ chi tiết
6. Hệ thống tự động tính phí ship (hiển thị miễn phí)
7. Xác nhận đặt trước

### 2. Đặt Trước E-book
1. Chọn sách và định dạng e-book
2. Chọn số lượng
3. Nhập thông tin khách hàng (không cần địa chỉ)
4. Xác nhận đặt trước

## Luồng Tính Giá

### Công Thức Tính Giá
```
Giá cơ bản = Book.getPreorderPrice(bookFormat)
Phí thuộc tính = Sum(BookAttributeValue.extra_price của các thuộc tính được chọn)
Đơn giá = Giá cơ bản + Phí thuộc tính
Tổng tiền = Đơn giá × Số lượng
Phí vận chuyển = 0 (miễn phí cho đặt trước)
```

### Ví Dụ
```
Sách: "Lập Trình Laravel" - Giá đặt trước: 200,000đ
Thuộc tính được chọn:
- Bìa cứng: +50,000đ
- Tiếng Việt: +0đ

Tính toán:
- Giá cơ bản: 200,000đ
- Phí thuộc tính: 50,000đ
- Đơn giá: 250,000đ
- Số lượng: 2
- Tổng tiền: 500,000đ
- Phí vận chuyển: 0đ (miễn phí)
```

## API Endpoints

### GHN Integration
```
GET /api/provinces - Lấy danh sách tỉnh/thành
GET /api/districts?province_id={id} - Lấy danh sách quận/huyện
GET /api/wards?district_id={id} - Lấy danh sách phường/xã
POST /api/calculate-fee - Tính phí vận chuyển
POST /api/shipping-fee - Alias cho calculate-fee
```

### Request/Response Examples

**Tính phí vận chuyển:**
```javascript
// Request
POST /api/calculate-fee
{
  "to_district_id": 1442,
  "to_ward_code": "21012",
  "weight": 500,
  "service_type_id": 2
}

// Response
{
  "success": true,
  "fee": 25000,
  "data": {
    "total": 25000,
    "shipping_fee": 25000,
    "formatted_fee": "25,000đ"
  }
}
```

## Cấu Trúc Database

### Bảng Liên Quan
- `preorders` - Thông tin đặt trước
- `books` - Thông tin sách
- `book_formats` - Định dạng sách
- `book_attribute_values` - Thuộc tính sách với giá thêm
- `attribute_values` - Giá trị thuộc tính
- `attributes` - Loại thuộc tính

### Relationship Models
```
Book → BookAttributeValue → AttributeValue → Attribute
Preorder → Book
Preorder → BookFormat
```

## Lưu Ý Quan Trọng

1. **Miễn phí vận chuyển**: Tất cả đơn đặt trước đều được miễn phí vận chuyển
2. **Thuộc tính chỉ áp dụng cho sách vật lý**: E-book không có thuộc tính
3. **Validation địa chỉ**: Sách vật lý bắt buộc phải có địa chỉ giao hàng
4. **Tự động cập nhật giá**: JavaScript tự động tính toán khi thay đổi thuộc tính
5. **GHN API**: Cần cấu hình đúng token và shop_id trong GHNService

## Troubleshooting

### Lỗi Thường Gặp

1. **Không load được tỉnh/thành**:
   - Kiểm tra GHN API token
   - Kiểm tra route `/api/provinces`

2. **Không tính được phí ship**:
   - Kiểm tra district_id và ward_code
   - Kiểm tra route `/api/calculate-fee`

3. **Giá thuộc tính không cập nhật**:
   - Kiểm tra relationship `bookAttributeValues`
   - Kiểm tra dữ liệu `extra_price` trong database

4. **JavaScript không hoạt động**:
   - Kiểm tra console browser có lỗi không
   - Kiểm tra các element selector trong JS

## Kết Quả Mong Muốn

- ✅ Form đặt trước hiển thị đầy đủ thông tin giá
- ✅ Tích hợp GHN API cho địa chỉ và phí ship
- ✅ Xử lý thuộc tính với giá thêm chính xác
- ✅ Hiển thị chi tiết breakdown giá trong trang xem đặt trước
- ✅ Miễn phí vận chuyển cho tất cả đơn đặt trước
- ✅ Validation đầy đủ cho dữ liệu đầu vào
- ✅ UX/UI thân thiện và trực quan