# Sửa Lỗi User ID trong Bảng Preorders

## Mô tả lỗi

**Lỗi SQL**: `SQLSTATE[HY000]: General error: 1364 Field 'user_id' doesn't have a default value`

**Nguyên nhân**: Trường `user_id` trong bảng `preorders` được định nghĩa là NOT NULL và không có giá trị mặc định, nhưng hệ thống cần hỗ trợ khách hàng đặt trước mà chưa đăng ký tài khoản.

## Giải pháp

### 1. Tạo Migration để làm user_id nullable

```bash
php artisan make:migration make_user_id_nullable_in_preorders_table
```

### 2. Cập nhật Migration

**File**: `database/migrations/2025_08_07_131325_make_user_id_nullable_in_preorders_table.php`

```php
public function up(): void
{
    Schema::table('preorders', function (Blueprint $table) {
        // Xóa foreign key constraint trước
        $table->dropForeign(['user_id']);
        
        // Làm user_id nullable để hỗ trợ khách hàng chưa đăng ký
        $table->uuid('user_id')->nullable()->change();
        
        // Thêm lại foreign key với nullable
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('preorders', function (Blueprint $table) {
        // Xóa foreign key
        $table->dropForeign(['user_id']);
        
        // Khôi phục user_id về not nullable
        $table->uuid('user_id')->nullable(false)->change();
        
        // Thêm lại foreign key ban đầu
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}
```

### 3. Chạy Migration

```bash
php artisan migrate
```

## Các trường được cập nhật

- **user_id**: Từ NOT NULL thành NULLABLE
- **Foreign Key**: Thay đổi từ `onDelete('cascade')` thành `onDelete('set null')`

## Logic xử lý trong Controller

Controller `PreorderController` đã xử lý đúng logic:

```php
// Nếu user đã đăng nhập
if (Auth::check()) {
    $preorderData['user_id'] = Auth::id();
} else {
    // user_id sẽ là null cho khách hàng chưa đăng ký
    $preorderData['user_id'] = null;
}
```

## Kết quả test

✅ **Ebook preorder**: Tạo thành công với user_id = null
✅ **Physical book preorder**: Tạo thành công với user_id = null
✅ **Preorder count**: Được cập nhật chính xác

## Lưu ý

- Thay đổi này cho phép khách hàng đặt trước mà không cần đăng ký tài khoản
- Dữ liệu khách hàng vẫn được lưu đầy đủ thông qua các trường: `customer_name`, `email`, `phone`
- Khi khách hàng đăng ký sau này, có thể liên kết preorder thông qua email

## Files liên quan

- **Migration**: `database/migrations/2025_08_07_131325_make_user_id_nullable_in_preorders_table.php`
- **Model**: `app/Models/Preorder.php`
- **Controller**: `app/Http/Controllers/PreorderController.php`