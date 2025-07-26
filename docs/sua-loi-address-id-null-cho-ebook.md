# Sửa Lỗi Address ID Null Cho Đơn Hàng Ebook

## Mô tả lỗi

**[LỖI]**: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'address_id' cannot be null

**[FILE]**: `app/Services/OrderService.php` - phương thức `handleDeliveryAddress`

**[NGUYÊN NHÂN]**: 
- Cột `address_id` trong bảng `orders` không cho phép giá trị `null`
- Khi tạo đơn hàng ebook, không cần địa chỉ giao hàng nên `address_id` được set thành `null`
- Database constraint không cho phép điều này

## Giải pháp đã triển khai

### 1. Tạo migration để cho phép address_id nullable

```php
// File: database/migrations/2025_07_26_112805_make_address_id_nullable_in_orders_table.php

Schema::table('orders', function (Blueprint $table) {
    // Xóa foreign key constraint trước
    $table->dropForeign(['address_id']);
    
    // Thay đổi cột address_id thành nullable
    $table->uuid('address_id')->nullable()->change();
    
    // Thêm lại foreign key constraint với nullable
    $table->foreign('address_id')
        ->references('id')
        ->on('addresses')
        ->onDelete('restrict');
});
```

### 2. Cập nhật enum delivery_method

Thêm giá trị 'ebook' vào enum `delivery_method`:

```sql
ALTER TABLE orders MODIFY COLUMN delivery_method ENUM('delivery', 'pickup', 'ebook') DEFAULT 'delivery';
```

### 3. Cập nhật OrderService.php

```php
// Phương thức handleDeliveryAddress
public function handleDeliveryAddress($request, User $user)
{
    // Nếu là đơn hàng ebook, không cần địa chỉ giao hàng
    if ($request->delivery_method === 'ebook') {
        return null;
    }
    
    // Logic xử lý địa chỉ cho sách vật lý...
}

// Phương thức processOrderCreation và processOrderCreationWithWallet
if (!$addressId && $request->delivery_method !== 'ebook') {
    throw new \Exception('Địa chỉ giao hàng không hợp lệ.');
}

// Phương thức prepareOrderData
$isEbookOrder = $request->delivery_method === 'ebook';

return [
    'address_id' => $addressId, // null cho ebook
    'recipient_name' => $isEbookOrder ? ($request->new_recipient_name ?: $user->name) : $request->new_recipient_name,
    'recipient_phone' => $isEbookOrder ? ($request->new_phone ?: $user->phone) : $request->new_phone,
    'shipping_fee' => ($request->delivery_method === 'pickup' || $isEbookOrder) ? 0 : $request->shipping_fee_applied,
    // ...
];
```

### 4. Cập nhật validation trong OrderController.php

```php
$isEbookOrder = $request->delivery_method === 'ebook';

$rules = [
    'payment_method_id' => 'required|exists:payment_methods,id',
    'delivery_method' => 'required|in:delivery,pickup,ebook',
    'new_email' => 'required|email',
    // ...
];

// Chỉ yêu cầu thông tin địa chỉ khi không phải ebook
if (!$isEbookOrder) {
    $rules = array_merge($rules, [
        'new_recipient_name' => 'required|string|max:255',
        'new_phone' => 'required|string|max:20',
        // địa chỉ rules...
    ]);
}
```

## Kết quả

- Đơn hàng ebook có thể được tạo thành công với `address_id = null`
- Không cần nhập thông tin địa chỉ giao hàng cho ebook
- Phí vận chuyển tự động = 0 cho ebook
- Database constraint được cập nhật phù hợp

## Cách tránh lỗi tương lai

1. **Kiểm tra database constraint**: Trước khi cho phép giá trị null trong code, đảm bảo database schema hỗ trợ
2. **Test migration**: Luôn test migration trên database development trước khi deploy
3. **Validation nhất quán**: Đảm bảo validation rules phù hợp với business logic
4. **Documentation**: Ghi lại các thay đổi database schema và business rules

## Các file đã thay đổi

### Database & Migration:
- `database/migrations/2025_07_26_112805_make_address_id_nullable_in_orders_table.php` (mới)
- `database/migrations/2025_07-24_000000_add_delivery_method_to_orders_table.php` (cập nhật enum)

### Backend Logic:
- `app/Services/OrderService.php` (cập nhật logic xử lý địa chỉ)
- `app/Http/Controllers/OrderController.php` (cập nhật validation)

### Email Templates:
- `resources/views/emails/orders/confirmation.blade.php` (xử lý ebook, kiểm tra null)

### Admin Views:
- `resources/views/admin/orders/index.blade.php` (hiển thị ebook, kiểm tra null)
- `resources/views/admin/orders/show.blade.php` (chi tiết đơn hàng ebook)

### Client Views:
- `resources/views/orders/checkout.blade.php` (UI checkout ebook)
- `resources/views/clients/account/orders.blade.php` (danh sách đơn hàng)
- `resources/views/clients/account/order-details.blade.php` (chi tiết đơn hàng)

## Test cases cần kiểm tra

1. Tạo đơn hàng ebook thành công
2. Tạo đơn hàng sách vật lý vẫn yêu cầu địa chỉ
3. Tạo đơn hàng combo (ebook + sách vật lý) yêu cầu địa chỉ
4. Validation hoạt động đúng cho từng loại đơn hàng