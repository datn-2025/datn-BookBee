# Chức Năng Đặt Trước Sách - BookBee

## Tổng Quan

Chức năng đặt trước sách cho phép khách hàng đặt mua sách trước khi sách được phát hành chính thức. Thay vì sử dụng giỏ hàng, khách hàng sẽ điền form thanh toán trực tiếp với thông tin biến thể và thanh toán.

## Tính Năng Chính

### 1. Quản Lý Sách Đặt Trước
- **Trạng thái đặt trước**: Sách có thể được đánh dấu là "đặt trước"
- **Ngày phát hành**: Xác định ngày sách sẽ được phát hành
- **Giá đặt trước**: Giá đặc biệt cho khách đặt trước
- **Giới hạn đặt trước**: Số lượng tối đa có thể đặt trước

### 2. Luồng Đặt Trước
1. **Chọn sách đặt trước**: Khách hàng chọn sách có trạng thái đặt trước
2. **Chọn biến thể**: Chọn các thuộc tính như bìa cứng/mềm, ngôn ngữ, v.v.
3. **Form thanh toán**: Điền thông tin cá nhân và địa chỉ giao hàng
4. **Xác nhận đơn hàng**: Tạo đơn hàng với trạng thái "Đặt trước"
5. **Thông báo thành công**: Hiển thị thông tin đơn hàng và ngày giao dự kiến

### 3. Ưu Đãi Đặt Trước
- **Miễn phí vận chuyển**: Tất cả đơn đặt trước được miễn phí ship
- **Giá ưu đãi**: Giá đặt trước thường thấp hơn giá bán lẻ
- **Đảm bảo có hàng**: Khách đặt trước được ưu tiên nhận sách
- **Hủy đơn linh hoạt**: Có thể hủy đơn trước 7 ngày phát hành

## Cấu Trúc Database

### 1. Bảng `books` - Thêm Trường Đặt Trước
```sql
ALTER TABLE books ADD COLUMN pre_order BOOLEAN DEFAULT FALSE;
ALTER TABLE books ADD COLUMN release_date DATE NULL;
ALTER TABLE books ADD COLUMN pre_order_price DECIMAL(10,2) NULL;
ALTER TABLE books ADD COLUMN stock_preorder_limit INTEGER NULL;
```

### 2. Các Trường Mới
- `pre_order`: Đánh dấu sách có thể đặt trước
- `release_date`: Ngày phát hành dự kiến
- `pre_order_price`: Giá đặt trước (có thể khác giá bán thường)
- `stock_preorder_limit`: Giới hạn số lượng đặt trước

## Implementation

### 1. Model Book - Thêm Phương Thức

```php
// app/Models/Book.php

/**
 * Kiểm tra sách có đang trong trạng thái đặt trước không
 */
public function isPreOrder(): bool
{
    return $this->pre_order && 
           $this->release_date && 
           $this->release_date->isFuture();
}

/**
 * Kiểm tra sách đã được phát hành chưa
 */
public function isReleased(): bool
{
    return $this->release_date && 
           $this->release_date->isPast();
}

/**
 * Lấy giá hiển thị (ưu tiên giá đặt trước nếu có)
 */
public function getDisplayPrice(): float
{
    if ($this->isPreOrder() && $this->pre_order_price) {
        return $this->pre_order_price;
    }
    return $this->price ?? 0;
}

/**
 * Scope: Lấy các sách đang đặt trước
 */
public function scopePreOrder($query)
{
    return $query->where('pre_order', true)
                 ->where('release_date', '>', now());
}

/**
 * Scope: Lấy các sách sắp phát hành
 */
public function scopeUpcomingRelease($query)
{
    return $query->where('release_date', '>', now())
                 ->orderBy('release_date', 'asc');
}
```

### 2. Controller PreOrderController

```php
// app/Http/Controllers/PreOrderController.php

class PreOrderController extends Controller
{
    /**
     * Hiển thị form đặt trước
     */
    public function showForm(Book $book)
    {
        if (!$book->isPreOrder()) {
            abort(404, 'Sách này không trong trạng thái đặt trước');
        }
        
        return view('books.preorder-form', compact('book'));
    }
    
    /**
     * Hiển thị form thanh toán
     */
    public function checkout(Request $request)
    {
        // Validate input
        // Kiểm tra giới hạn đặt trước
        // Tính toán giá
        // Hiển thị form thanh toán
        
        return view('books.preorder-checkout', compact('orderData'));
    }
    
    /**
     * Xử lý đặt trước và tạo đơn hàng
     */
    public function submit(Request $request)
    {
        // Validate thông tin khách hàng
        // Tạo đơn hàng với trạng thái "Đặt trước"
        // Tạo order item
        // Lưu thông tin biến thể
        // Chuyển hướng đến trang thành công
    }
    
    /**
     * Hiển thị trang thành công
     */
    public function success($orderId)
    {
        $order = Order::with(['orderItems.book'])
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        return view('books.preorder-success', compact('order'));
    }
}
```

### 3. Routes

```php
// routes/web.php

Route::prefix('preorder')->name('preorder.')->group(function () {
    Route::get('/', [PreOrderController::class, 'index'])->name('index');
    Route::get('/form/{book}', [PreOrderController::class, 'showForm'])->name('form');
    Route::post('/checkout', [PreOrderController::class, 'checkout'])->name('checkout');
    Route::post('/submit', [PreOrderController::class, 'submit'])->name('submit');
    Route::get('/success/{order}', [PreOrderController::class, 'success'])->name('success');
    Route::get('/my-preorders', [PreOrderController::class, 'myPreOrders'])->name('my-preorders');
    Route::post('/cancel/{order}', [PreOrderController::class, 'cancel'])->name('cancel');
});
```

## Views

### 1. Form Thanh Toán (`preorder-checkout.blade.php`)
- **Thông tin sách**: Hiển thị sách, biến thể đã chọn
- **Form khách hàng**: Họ tên, email, số điện thoại, địa chỉ
- **Phương thức thanh toán**: COD, chuyển khoản, ví điện tử
- **Tóm tắt đơn hàng**: Giá, phí ship (miễn phí), tổng cộng
- **Thông tin phát hành**: Ngày phát hành dự kiến

### 2. Trang Thành Công (`preorder-success.blade.php`)
- **Thông báo thành công**: Icon check, thông điệp cảm ơn
- **Thông tin đơn hàng**: Mã đơn, ngày đặt, trạng thái, tổng tiền
- **Chi tiết sản phẩm**: Sách đã đặt, biến thể, số lượng
- **Thông tin giao hàng**: Người nhận, địa chỉ, ngày giao dự kiến
- **Lưu ý quan trọng**: Hướng dẫn và chính sách hủy đơn
- **Nút hành động**: Xem đơn hàng, tiếp tục mua sắm

## Quy Trình Xử Lý

### 1. Validation
- **Trạng thái sách**: Phải đang trong trạng thái đặt trước
- **Ngày phát hành**: Phải trong tương lai
- **Giới hạn đặt trước**: Không vượt quá số lượng cho phép
- **Thông tin khách hàng**: Đầy đủ và hợp lệ
- **Biến thể**: Phải tồn tại và thuộc về sách

### 2. Tạo Đơn Hàng
- **Trạng thái**: "Đặt trước" hoặc "Chờ xác nhận"
- **Thanh toán**: "Chờ thanh toán" hoặc "Đã thanh toán"
- **Phí ship**: 0 (miễn phí cho đặt trước)
- **Ghi chú**: Thông tin ngày phát hành và địa chỉ giao hàng

### 3. Lưu Thông Tin Biến Thể
- **OrderItem**: Thông tin cơ bản về sách và số lượng
- **OrderItemAttributeValue**: Lưu các thuộc tính đã chọn

## Tính Năng Bổ Sung

### 1. Quản Lý Đơn Đặt Trước
- **Danh sách đơn đặt trước**: Khách hàng xem các đơn đã đặt
- **Hủy đơn**: Cho phép hủy trước 7 ngày phát hành
- **Thông báo**: Email thông báo khi sách sẵn sàng giao

### 2. Admin Management
- **Quản lý sách đặt trước**: Bật/tắt trạng thái đặt trước
- **Cập nhật ngày phát hành**: Thay đổi ngày phát hành
- **Theo dõi đơn đặt trước**: Thống kê số lượng đặt trước
- **Xử lý đơn hàng**: Chuyển đổi trạng thái khi sách được phát hành

## Lợi Ích

### Cho Khách Hàng
- **Đảm bảo có sách**: Không lo hết hàng khi phát hành
- **Giá ưu đãi**: Giá đặt trước thường tốt hơn
- **Miễn phí ship**: Tiết kiệm chi phí vận chuyển
- **Trải nghiệm mượt mà**: Không cần qua giỏ hàng

### Cho Nhà Bán
- **Dự đoán nhu cầu**: Biết trước số lượng cần nhập
- **Cash flow**: Thu tiền trước khi có hàng
- **Marketing**: Tạo buzz cho sách mới
- **Quản lý tồn kho**: Tối ưu hóa việc nhập hàng

## Cải Tiến Tương Lai

### 1. Thông Báo Tự Động
- Email thông báo khi sách sẵn sàng giao
- SMS nhắc nhở trước ngày phát hành
- Push notification trên app mobile

### 2. Tích Hợp Thanh Toán
- Thanh toán online qua VNPay, MoMo
- Thanh toán trả góp cho sách đắt tiền
- Ví điện tử nội bộ

### 3. Gamification
- Điểm thưởng cho khách đặt trước
- Ranking khách hàng VIP
- Ưu đãi đặc biệt cho early adopters

## Files Đã Tạo/Cập Nhật

### Models
- `app/Models/Book.php` - Thêm phương thức đặt trước

### Controllers
- `app/Http/Controllers/PreOrderController.php` - Controller xử lý đặt trước

### Views
- `resources/views/books/preorder-checkout.blade.php` - Form thanh toán
- `resources/views/books/preorder-success.blade.php` - Trang thành công

### Routes
- `routes/web.php` - Thêm routes cho đặt trước

### Migrations
- `database/migrations/add_preorder_fields_to_books_table.php` - Thêm trường đặt trước

### Documentation
- `docs/chuc-nang-dat-truoc-sach.md` - Tài liệu hướng dẫn

---

**Ngày tạo:** {{ date('d/m/Y') }}  
**Tác giả:** AI Assistant  
**Trạng thái:** Hoàn thành  
**Version:** 1.0