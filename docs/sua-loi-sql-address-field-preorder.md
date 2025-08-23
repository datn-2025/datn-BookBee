# Sửa Lỗi SQL: Field 'address' Doesn't Have Default Value

## 🚨 Mô Tả Lỗi

**Lỗi gặp phải:**
```
SQLSTATE[HY000]: General error: 1364 Field 'address' doesn't have a default value
```

**Nguyên nhân:**
- Khi tạo preorder cho **ebook**, hệ thống không truyền các trường địa chỉ
- Nhưng trong database, các trường địa chỉ được định nghĩa là **NOT NULL**
- Dẫn đến lỗi SQL khi insert record

## 🔧 Giải Pháp

### 1. Tạo Migration Mới

```bash
php artisan make:migration make_address_nullable_in_preorders
```

### 2. Cập Nhật Migration

**File:** `database/migrations/2025_08_07_123131_make_address_nullable_in_preorders.php`

```php
public function up(): void
{
    Schema::table('preorders', function (Blueprint $table) {
        // Làm các trường địa chỉ thành nullable cho ebook
        $table->text('address')->nullable()->change();
        $table->string('province_code')->nullable()->change();
        $table->string('province_name')->nullable()->change();
        $table->string('district_code')->nullable()->change();
        $table->string('district_name')->nullable()->change();
        $table->string('ward_code')->nullable()->change();
        $table->string('ward_name')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('preorders', function (Blueprint $table) {
        // Khôi phục lại trạng thái ban đầu
        $table->text('address')->nullable(false)->change();
        $table->string('province_code')->nullable(false)->change();
        $table->string('province_name')->nullable(false)->change();
        $table->string('district_code')->nullable(false)->change();
        $table->string('district_name')->nullable(false)->change();
        $table->string('ward_code')->nullable(false)->change();
        $table->string('ward_name')->nullable(false)->change();
    });
}
```

### 3. Chạy Migration

```bash
php artisan migrate
```

## 📋 Logic Xử Lý Trong Controller

**File:** `app/Http/Controllers/PreorderController.php`

```php
// Chỉ lưu địa chỉ nếu không phải ebook
if (!$isEbook) {
    $preorderData = array_merge($preorderData, [
        'address' => $validated['address'],
        'province_code' => $validated['province_code'],
        'province_name' => $validated['province_name'],
        'district_code' => $validated['district_code'],
        'district_name' => $validated['district_name'],
        'ward_code' => $validated['ward_code'],
        'ward_name' => $validated['ward_name']
    ]);
}
```

## ✅ Kết Quả Sau Khi Sửa

### Ebook Preorder
- ✅ Tạo thành công không cần địa chỉ
- ✅ Các trường địa chỉ = `NULL`
- ✅ Không có lỗi SQL

### Sách Vật Lý Preorder
- ✅ Tạo thành công với đầy đủ địa chỉ
- ✅ Các trường địa chỉ có giá trị
- ✅ Logic vận chuyển hoạt động bình thường

## 🧪 Test Cases

### Test Case 1: Tạo Preorder Ebook
```php
// Dữ liệu không có địa chỉ
$preorderData = [
    'user_id' => $user->id,
    'book_id' => $book->id,
    'book_format_id' => $ebookFormat->id,
    'customer_name' => 'Test Customer',
    'email' => 'test@example.com',
    'phone' => '0123456789',
    'quantity' => 1,
    'unit_price' => 45000,
    'total_amount' => 45000,
    'selected_attributes' => [],
    'status' => 'pending',
    'notes' => 'Test preorder ebook'
];

$preorder = Preorder::create($preorderData);
// ✅ Thành công, address = NULL
```

### Test Case 2: Tạo Preorder Sách Vật Lý
```php
// Dữ liệu có đầy đủ địa chỉ
$preorderDataPhysical = array_merge($preorderData, [
    'address' => '123 Test Street',
    'province_code' => '01',
    'province_name' => 'Hà Nội',
    'district_code' => '001',
    'district_name' => 'Ba Đình',
    'ward_code' => '00001',
    'ward_name' => 'Phúc Xá'
]);

$preorderPhysical = Preorder::create($preorderDataPhysical);
// ✅ Thành công, có đầy đủ địa chỉ
```

## 📁 Files Đã Thay Đổi

1. **database/migrations/2025_08_07_123131_make_address_nullable_in_preorders.php** - Migration mới
2. **app/Http/Controllers/PreorderController.php** - Logic đã có sẵn (không thay đổi)

## 🔍 Kiểm Tra Sau Khi Sửa

```bash
# Kiểm tra migration đã chạy
php artisan migrate:status

# Test tạo preorder qua giao diện
# 1. Truy cập trang tạo preorder
# 2. Chọn ebook format
# 3. Điền thông tin và submit
# 4. Kiểm tra không có lỗi SQL
```

## 💡 Lưu Ý Quan Trọng

- **Ebook**: Không cần địa chỉ vận chuyển → các trường địa chỉ = `NULL`
- **Sách vật lý**: Cần địa chỉ vận chuyển → các trường địa chỉ có giá trị
- Migration có thể rollback nếu cần thiết
- Logic trong controller đã xử lý đúng từ trước

## 🎯 Kết Luận

Lỗi đã được sửa hoàn toàn bằng cách:
1. ✅ Làm các trường địa chỉ thành `nullable` trong database
2. ✅ Giữ nguyên logic xử lý trong controller
3. ✅ Test thành công cho cả ebook và sách vật lý
4. ✅ Không ảnh hưởng đến chức năng hiện có