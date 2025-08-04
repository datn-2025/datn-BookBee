# Đánh Giá Combo và Hiển Thị Định Dạng Sách - BookBee

## Tổng Quan

Hệ thống đánh giá đã được mở rộng để hỗ trợ đánh giá combo (bộ sách) và hiển thị định dạng sách (Physical/Ebook) trong các đánh giá.

## Tính Năng Mới

### 1. Đánh Giá Combo

#### Cấu Trúc Database
- **Bảng `reviews`** đã được mở rộng với cột `collection_id` để hỗ trợ đánh giá combo
- **Unique constraint mới**: `(user_id, book_id, collection_id, order_id)` để ngăn đánh giá trùng lặp
- Một đánh giá có thể thuộc về:
  - Sách đơn lẻ (`book_id` không null, `collection_id` null)
  - Combo (`collection_id` không null, `book_id` null hoặc không null)

#### Model Updates

**Review Model (`app/Models/Review.php`)**:
```php
// Relationship mới
public function collection(): BelongsTo
{
    return $this->belongsTo(Collection::class);
}

// Helper methods
public function isComboReview(): bool
{
    return !is_null($this->collection_id);
}

public function getProductNameAttribute(): string
{
    if ($this->isComboReview()) {
        return $this->collection->name ?? 'Combo không xác định';
    }
    return $this->book->title ?? 'Sách không xác định';
}

public function getProductTypeAttribute(): string
{
    return $this->isComboReview() ? 'Combo' : 'Sách';
}
```

**Collection Model (`app/Models/Collection.php`)**:
```php
// Relationship mới
public function reviews()
{
    return $this->hasMany(Review::class);
}

// Tính toán rating trung bình
public function getAverageRatingAttribute()
{
    return $this->reviews()->whereIn('status', ['approved', 'visible'])->avg('rating') ?? 0;
}

// Đếm số lượng đánh giá
public function getReviewCountAttribute()
{
    return $this->reviews()->whereIn('status', ['approved', 'visible'])->count();
}
```

### 2. Hiển Thị Định Dạng Sách

#### Giao Diện Client
- **Trang chi tiết sách** (`resources/views/clients/show.blade.php`):
  - Hiển thị loại sản phẩm (Sách/Combo)
  - Hiển thị định dạng (Physical/Ebook) từ thông tin đơn hàng
  - Badge màu sắc phân biệt: Ebook (xanh dương), Physical (xanh lá)

#### Giao Diện Admin
- **Danh sách đánh giá** (`resources/views/admin/reviews/index.blade.php`):
  - Cột "Loại & Định dạng" mới
  - Badge hiển thị loại sản phẩm và định dạng
  - Link đến trang chi tiết combo/sách tương ứng

- **Chi tiết đánh giá** (`resources/views/admin/reviews/response.blade.php`):
  - Thông tin chi tiết combo (nếu là đánh giá combo)
  - Hiển thị định dạng sản phẩm từ order item
  - Giao diện khác biệt cho combo (màu xanh lá) và sách (màu xám)

## Migration

**File**: `database/migrations/2025_08_01_133835_add_collection_support_to_reviews_table.php`

```php
// Thêm cột collection_id
$table->uuid('collection_id')->nullable()->after('book_id');

// Foreign key constraint
$table->foreign('collection_id')
    ->references('id')
    ->on('collections')
    ->onDelete('cascade');

// Unique constraint mới
$table->unique(['user_id', 'book_id', 'collection_id', 'order_id'], 'unique_user_product_order_review');
```

## Controller Updates

### AdminReviewController

```php
// Load thêm relationships
$reviews = Review::with(['book', 'user', 'collection', 'order.orderItems'])

// Logic lấy đánh giá khác của cùng sản phẩm
if ($review->isComboReview()) {
    $otherReviews = Review::where('collection_id', $review->collection_id)
        ->where('id', '!=', $review->id)
        ->with(['user', 'collection', 'order.orderItems'])
        ->orderBy('created_at', 'desc')
        ->paginate(5);
} else {
    $otherReviews = Review::where('book_id', $review->book_id)
        ->where('id', '!=', $review->id)
        ->with(['user', 'book', 'order.orderItems'])
        ->orderBy('created_at', 'desc')
        ->paginate(5);
}
```

## Cách Sử Dụng

### 1. Đánh Giá Combo

1. Khách hàng mua combo và đơn hàng hoàn thành
2. Trong trang "Đơn hàng của tôi", xuất hiện form đánh giá cho combo
3. Khách hàng có thể đánh giá toàn bộ combo (không phải từng sách riêng lẻ)
4. Đánh giá combo hiển thị trên trang chi tiết combo

### 2. Xem Định Dạng Sách

1. **Trang chi tiết sách**: Mỗi đánh giá hiển thị định dạng sách mà khách hàng đã mua
2. **Admin**: Có thể xem định dạng trong danh sách và chi tiết đánh giá
3. **Phân biệt màu sắc**:
   - Ebook: Badge xanh dương
   - Physical: Badge xanh lá/xám

## Lợi Ích

### Cho Khách Hàng
- Có thể đánh giá combo như một sản phẩm tổng thể
- Biết được định dạng sách mà người đánh giá đã mua
- Đánh giá chính xác hơn dựa trên trải nghiệm thực tế

### Cho Admin
- Quản lý đánh giá combo và sách trong cùng một hệ thống
- Theo dõi đánh giá theo định dạng sản phẩm
- Phân tích xu hướng đánh giá giữa Ebook và Physical

## Files Liên Quan

### Models
- `app/Models/Review.php` - Model đánh giá với hỗ trợ combo
- `app/Models/Collection.php` - Model combo với relationship reviews

### Controllers
- `app/Http/Controllers/Admin/AdminReviewController.php` - Quản lý đánh giá admin

### Views
- `resources/views/clients/show.blade.php` - Hiển thị đánh giá trên trang sách
- `resources/views/admin/reviews/index.blade.php` - Danh sách đánh giá admin
- `resources/views/admin/reviews/response.blade.php` - Chi tiết đánh giá admin

### Migrations
- `database/migrations/2025_08_01_133835_add_collection_support_to_reviews_table.php`

## Cải Tiến Tương Lai

1. **Form đánh giá combo**: Tạo form riêng cho đánh giá combo
2. **Thống kê đánh giá**: Phân tích đánh giá theo định dạng
3. **Filter nâng cao**: Lọc đánh giá theo combo/sách, định dạng
4. **Đánh giá từng sách trong combo**: Cho phép đánh giá chi tiết từng sách
5. **So sánh đánh giá**: So sánh đánh giá giữa Ebook và Physical của cùng một sách

## Kết Luận

Hệ thống đánh giá đã được mở rộng thành công để hỗ trợ:
- ✅ Đánh giá combo
- ✅ Hiển thị định dạng sách trong đánh giá
- ✅ Giao diện admin cải tiến
- ✅ Tương thích ngược với đánh giá sách hiện tại
- ✅ Database structure linh hoạt cho tương lai