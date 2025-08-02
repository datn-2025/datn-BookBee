# Sửa Lỗi Cột parent_order_id Trong Bảng Orders

## Mô Tả Lỗi

**Lỗi**: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'orders.parent_order_id' in 'where clause'`

**File**: `app/Http/Controllers/Admin/OrderController.php` - dòng 46-47

**Nguyên Nhân**: 
- Model `Order` có định nghĩa quan hệ `parentOrder()` và `childOrders()` sử dụng cột `parent_order_id`
- OrderController trong phương thức `index()` đang load quan hệ này qua `with(['parentOrder', 'childOrders'])`
- Nhưng cột `parent_order_id` không tồn tại trong bảng `orders`

## Giải Pháp

### 1. Tạo Migration Thêm Cột parent_order_id

Tạo file migration: `2025_01_15_000000_add_parent_order_id_to_orders_table.php`

```php
Schema::table('orders', function (Blueprint $table) {
    $table->uuid('parent_order_id')->nullable()->after('id');
    $table->foreign('parent_order_id')
          ->references('id')
          ->on('orders')
          ->onDelete('set null');
    $table->index('parent_order_id');
});
```

### 2. Chạy Migration

```bash
php artisan migrate
```

## Cấu Trúc Quan Hệ

### Model Order

```php
/**
 * Đơn hàng cha (parent order)
 */
public function parentOrder(): BelongsTo
{
    return $this->belongsTo(Order::class, 'parent_order_id');
}

/**
 * Các đơn hàng con (child orders)
 */
public function childOrders(): HasMany
{
    return $this->hasMany(Order::class, 'parent_order_id');
}
```

### Sử Dụng Trong Controller

```php
$query = Order::with([
    'user', 
    'address', 
    'orderStatus', 
    'paymentStatus',
    'parentOrder',    // Quan hệ đến đơn hàng cha
    'childOrders'     // Quan hệ đến các đơn hàng con
])->orderBy('created_at', 'desc');
```

## Cách Tránh Lỗi Tương Lai

1. **Kiểm tra cấu trúc database trước khi định nghĩa quan hệ trong Model**
2. **Tạo migration ngay khi thêm quan hệ mới vào Model**
3. **Test các quan hệ sau khi tạo migration**
4. **Sử dụng `Schema::hasColumn()` để kiểm tra sự tồn tại của cột trước khi sử dụng**

## Kết Quả

- ✅ Cột `parent_order_id` đã được thêm vào bảng `orders`
- ✅ Quan hệ `parentOrder` và `childOrders` hoạt động bình thường
- ✅ Danh sách đơn hàng admin không còn bị lỗi
- ✅ Hỗ trợ tính năng đơn hàng cha-con (nếu cần thiết)

## Ghi Chú

- Cột `parent_order_id` được thiết lập `nullable()` để không ảnh hưởng đến dữ liệu hiện tại
- Sử dụng `onDelete('set null')` để đảm bảo tính toàn vẹn dữ liệu khi xóa đơn hàng cha
- Đã thêm index cho cột `parent_order_id` để tối ưu hiệu suất truy vấn