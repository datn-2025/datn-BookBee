# Sửa Lỗi Đặt Hàng - Thiếu Trường recipient_name Trong Bảng Addresses

## Mô Tả Lỗi

**Lỗi**: `SQLSTATE[HY000]: General error: 1364 Field 'recipient_name' doesn't have a default value`

**File**: `app/Services/OrderService.php` - phương thức `handleDeliveryAddress()`

**Nguyên Nhân**: 
1. Khi tạo địa chỉ mới trong quá trình đặt hàng, không truyền trường `recipient_name` và `phone`
2. Model `Address` thiếu `recipient_name` và `phone` trong `$fillable` array
3. Cột `recipient_name` trong bảng `addresses` không có giá trị mặc định và không được đánh dấu nullable

## Giải Pháp

### 1. Sửa OrderService.php

Trong phương thức `handleDeliveryAddress()`, thêm `recipient_name` và `phone` vào `$addressData`:

```php
// Tạo địa chỉ mới với thông tin GHN
$addressData = [
    'user_id' => $user->id,
    'recipient_name' => $request->new_recipient_name ?: $user->name,
    'phone' => $request->new_phone ?: $user->phone,
    'address_detail' => $request->new_address_detail,
    'city' => $request->new_address_city_name,
    'district' => $request->new_address_district_name,
    'ward' => $request->new_address_ward_name,
    'is_default' => false
];
```

### 2. Sửa Model Address

Thêm `recipient_name` và `phone` vào `$fillable` array:

```php
protected $fillable = [
    'user_id',
    'recipient_name',
    'phone',
    'address_detail',
    'city',
    'district',
    'ward',
    'is_default',
    'province_id',
    'district_id',
    'ward_code'
];
```

## Cấu Trúc Dữ Liệu

### Migration Addresses Table

```php
Schema::create('addresses', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('user_id');
    $table->string('recipient_name'); // Bắt buộc
    $table->string('phone');          // Bắt buộc
    $table->text('address_detail')->nullable();
    $table->string('city', 100);
    $table->string('district', 100);
    $table->string('ward', 100);
    $table->boolean('is_default');
    $table->timestamps();
});
```

### Form Checkout

Các trường input trong form checkout:

```html
<input type="text" name="new_recipient_name" id="new_recipient_name" 
       placeholder="Nhập họ và tên đầy đủ" required>
       
<input type="tel" name="new_phone" id="new_phone" 
       placeholder="Nhập số điện thoại" required>
```

## Validation Rules

Trong `OrderController`, đảm bảo validation cho các trường mới:

```php
'new_recipient_name' => [
    'required_without:address_id',
    'nullable',
    'string',
    'max:255'
],
'new_phone' => [
    'required_without:address_id',
    'nullable',
    'string',
    'max:20'
],
```

## Cách Tránh Lỗi Tương Lai

1. **Kiểm tra Migration và Model đồng bộ**: Đảm bảo tất cả cột trong migration đều có trong `$fillable` của Model
2. **Test tạo dữ liệu**: Luôn test việc tạo record mới sau khi thêm migration
3. **Sử dụng Factory**: Cập nhật Factory khi thêm trường mới
4. **Validation đầy đủ**: Đảm bảo validation cho tất cả trường bắt buộc

## Kết Quả

- ✅ Đặt hàng với địa chỉ mới hoạt động bình thường
- ✅ Thông tin người nhận được lưu đầy đủ
- ✅ Không còn lỗi SQL khi tạo address
- ✅ Dữ liệu address có đầy đủ thông tin cần thiết

## Ghi Chú

- Trường `recipient_name` và `phone` là bắt buộc trong bảng `addresses`
- Nếu user không nhập, sẽ sử dụng thông tin từ `user.name` và `user.phone` làm mặc định
- Cần đảm bảo form checkout có đầy đủ các trường input cần thiết