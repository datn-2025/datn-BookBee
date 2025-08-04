# Luồng Đánh Giá Sách - BookBee

## Mô tả chức năng

Hệ thống đánh giá sách cho phép khách hàng đánh giá và bình luận về các cuốn sách đã mua sau khi đơn hàng hoàn thành.

## Luồng hoạt động

### 1. Điều kiện đánh giá
- Khách hàng phải đăng nhập
- Đơn hàng phải có trạng thái "Thành công"
- Sách phải có trong đơn hàng đã hoàn thành
- Mỗi sách trong mỗi đơn hàng chỉ được đánh giá 1 lần

### 2. Quy trình đánh giá

#### Bước 1: Truy cập trang đánh giá
- Khách hàng vào **Tài khoản > Đơn mua**
- Chọn tab "Đã hoàn thành" hoặc "Tất cả"
- Tìm đơn hàng có trạng thái "Thành công"
- Nhấn vào form đánh giá cho sách cần đánh giá

#### Bước 2: Điền thông tin đánh giá
- **Đánh giá sao**: Từ 1-5 sao (bắt buộc)
- **Nội dung bình luận**: Tối đa 1000 ký tự (bắt buộc)

#### Bước 3: Gửi đánh giá
- Hệ thống validate dữ liệu
- Tạo record trong bảng `reviews` với status `approved`
- Hiển thị thông báo thành công

### 3. Trạng thái đánh giá

| Trạng thái | Mô tả | Hiển thị công khai |
|------------|-------|-------------------|
| `approved` | Đã duyệt, hiển thị công khai | ✅ |
| `pending` | Chờ admin duyệt | ❌ |
| `hidden` | Bị ẩn bởi admin | ❌ |
| `visible` | Hiển thị (legacy) | ✅ |

### 4. Quản lý đánh giá (Admin)

#### Xem danh sách đánh giá
- Truy cập **Admin > Bình luận & Đánh giá**
- Lọc theo trạng thái, sản phẩm, khách hàng, rating
- Tìm kiếm theo nội dung bình luận

#### Phản hồi đánh giá
- Admin có thể phản hồi đánh giá của khách hàng
- Mỗi đánh giá chỉ được phản hồi 1 lần
- Phản hồi hiển thị dưới đánh giá gốc

#### Thay đổi trạng thái
- **Approved/Visible → Hidden**: Ẩn đánh giá
- **Hidden → Approved**: Hiển thị lại đánh giá
- **Pending → Approved**: Duyệt đánh giá

## Cấu trúc Database

### Bảng `reviews`
```sql
CREATE TABLE reviews (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    book_id VARCHAR(36) NOT NULL,
    order_id VARCHAR(36) NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    status ENUM('visible', 'hidden', 'approved', 'pending') DEFAULT 'approved',
    admin_response TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    
    UNIQUE KEY unique_review (user_id, book_id, order_id)
);
```

## Files liên quan

### Controllers
- `app/Http/Controllers/Client/ReviewClientController.php` - Xử lý đánh giá từ khách hàng
- `app/Http/Controllers/Admin/AdminReviewController.php` - Quản lý đánh giá (admin)

### Models
- `app/Models/Review.php` - Model đánh giá

### Views
- `resources/views/clients/account/orders.blade.php` - Form đánh giá
- `resources/views/admin/reviews/index.blade.php` - Danh sách đánh giá (admin)
- `resources/views/admin/reviews/response.blade.php` - Form phản hồi (admin)

### Routes
- `routes/web.php` - Định nghĩa routes cho đánh giá

## Lỗi thường gặp và cách khắc phục

### Lỗi: SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status'

**Nguyên nhân**: 
- Cột `status` trong bảng `reviews` được định nghĩa là `ENUM('visible', 'hidden')`
- Code đang cố gắng insert giá trị `'approved'` không có trong enum

**Cách khắc phục**:
1. Tạo migration cập nhật enum:
```php
// database/migrations/2025_08_01_000000_fix_reviews_status_enum.php
DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('visible', 'hidden', 'approved', 'pending') DEFAULT 'approved'");
```

2. Cập nhật Model Review:
```php
// app/Models/Review.php
const STATUS_APPROVED = 'approved';
const STATUS_PENDING = 'pending';
const STATUS_HIDDEN = 'hidden';
const STATUS_VISIBLE = 'visible';
```

3. Cập nhật Controller và Factory để sử dụng status mới

### Lỗi: Không thể đánh giá sách

**Kiểm tra**:
- Đơn hàng có trạng thái "Thành công" không?
- Sách có trong đơn hàng không?
- Đã đánh giá sách này chưa?
- User có quyền truy cập đơn hàng không?

## Cải tiến trong tương lai

1. **Hệ thống điểm thưởng**: Tặng điểm cho khách hàng khi đánh giá
2. **Đánh giá có hình ảnh**: Cho phép upload ảnh kèm đánh giá
3. **Phân loại đánh giá**: Theo chất lượng nội dung, giao hàng, v.v.
4. **Thống kê đánh giá**: Dashboard cho admin theo dõi xu hướng đánh giá
5. **Email thông báo**: Gửi email cho khách hàng khi admin phản hồi

## Kết quả mong muốn

- ✅ Khách hàng có thể đánh giá sách đã mua
- ✅ Admin có thể quản lý và phản hồi đánh giá
- ✅ Đánh giá hiển thị trên trang chi tiết sách
- ✅ Hệ thống ngăn chặn đánh giá trùng lặp
- ✅ Dữ liệu đánh giá được lưu trữ an toàn và chính xác