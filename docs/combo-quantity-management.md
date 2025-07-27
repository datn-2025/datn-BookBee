# Quản Lý Số Lượng Combo - Tài Liệu Thay Đổi

## Tổng Quan
Đã thêm tính năng quản lý số lượng cho combo sách, bao gồm:
- Thêm trường số lượng khi tạo/sửa combo
- Kiểm tra số lượng khi thêm vào giỏ hàng
- Kiểm tra số lượng khi thanh toán
- Hiển thị số lượng trong danh sách admin

## Các Thay Đổi Đã Thực Hiện

### 1. Database & Model
- **Migration**: `2025_07_05_000001_add_combo_stock_to_collections_table.php` đã tồn tại
- **Model Collection**: Đã có trường `combo_stock` trong `$fillable` và `$casts`

### 2. Form Tạo/Sửa Combo

#### File: `resources/views/admin/collections/create.blade.php`
- Thêm trường "Số lượng combo" sau trường "Giá combo"
- Validation: `min="0"`, `nullable`
- Ghi chú: "Để trống nếu không giới hạn số lượng"

#### File: `resources/views/admin/collections/edit.blade.php`
- Thêm trường "Số lượng combo" tương tự create
- Hiển thị giá trị hiện tại: `value="{{ old('combo_stock', $collection->combo_stock) }}"`

### 3. Controller Validation

#### File: `app/Http/Controllers/Admin/CollectionController.php`
**Phương thức `store()` và `update()`:**
```php
// Thêm validation
'combo_stock' => 'nullable|integer|min:0',

// Thêm vào $data
$data = $request->only(['name', 'start_date', 'end_date', 'combo_price', 'combo_stock', 'description']);
```

### 4. Kiểm Tra Số Lượng Khi Thêm Vào Giỏ Hàng

#### File: `app/Http/Controllers/Cart/CartController.php`
**Phương thức `addComboToCart()`:**
- Kiểm tra `combo_stock !== null`
- Kiểm tra combo đã hết hàng (`combo_stock <= 0`)
- Kiểm tra tổng số lượng yêu cầu không vượt quá tồn kho
- Tính cả số lượng đã có trong giỏ hàng

**Phương thức `updateCart()` (cho combo):**
- Kiểm tra số lượng khi cập nhật giỏ hàng
- Không cho phép cập nhật vượt quá tồn kho

### 5. Kiểm Tra Số Lượng Khi Thanh Toán

#### File: `app/Services/OrderService.php`
**Phương thức `validateComboItem()`:**
```php
// Kiểm tra tồn kho combo
if ($cartItem->collection->combo_stock !== null && $cartItem->collection->combo_stock < $cartItem->quantity) {
    throw new \Exception('Combo "' . $cartItem->collection->name . '" không đủ số lượng. Còn lại: ' . $cartItem->collection->combo_stock);
}
```

**Phương thức `createComboOrderItem()`:**
```php
// Cập nhật tồn kho combo sau khi tạo đơn hàng
if ($cartItem->collection->combo_stock !== null) {
    $cartItem->collection->decrement('combo_stock', $cartItem->quantity);
}
```

### 6. Hiển Thị Trong Admin

#### File: `resources/views/admin/collections/index.blade.php`
- Thêm cột "Số lượng" vào bảng danh sách
- Hiển thị badge màu theo số lượng:
  - Xanh lá: > 10
  - Vàng: 1-10
  - Đỏ: 0
  - "Không giới hạn" nếu `combo_stock` là `null`

## Logic Kiểm Tra Số Lượng

### 1. Khi Thêm Vào Giỏ Hàng
```php
if ($combo->combo_stock !== null) {
    $existingQuantity = // Số lượng đã có trong giỏ hàng
    $totalRequestedQuantity = $existingQuantity + $quantity;
    
    if ($combo->combo_stock <= 0) {
        // Combo đã hết hàng
    }
    
    if ($totalRequestedQuantity > $combo->combo_stock) {
        // Vượt quá tồn kho
    }
}
```

### 2. Khi Cập Nhật Giỏ Hàng
```php
if ($combo->combo_stock !== null) {
    if ($quantity > $combo->combo_stock) {
        // Không cho phép cập nhật vượt quá tồn kho
    }
}
```

### 3. Khi Thanh Toán
```php
if ($cartItem->collection->combo_stock !== null && $cartItem->collection->combo_stock < $cartItem->quantity) {
    throw new \Exception('Combo không đủ số lượng');
}
```

### 4. Sau Khi Tạo Đơn Hàng
```php
if ($cartItem->collection->combo_stock !== null) {
    $cartItem->collection->decrement('combo_stock', $cartItem->quantity);
}
```

## Tính Năng

### ✅ Đã Hoàn Thành
1. Thêm trường số lượng combo trong form tạo/sửa
2. Validation số lượng combo trong controller
3. Kiểm tra số lượng khi thêm vào giỏ hàng
4. Kiểm tra số lượng khi cập nhật giỏ hàng
5. Kiểm tra số lượng khi thanh toán
6. Cập nhật tồn kho sau khi tạo đơn hàng
7. Hiển thị số lượng trong danh sách admin với badge màu

### 🎯 Lợi Ích
- **Quản lý tồn kho**: Kiểm soát chính xác số lượng combo có sẵn
- **Tránh overselling**: Không cho phép bán vượt quá số lượng có sẵn
- **Linh hoạt**: Có thể để trống để không giới hạn số lượng
- **Trải nghiệm người dùng**: Thông báo rõ ràng khi hết hàng hoặc không đủ số lượng
- **Quản lý admin**: Hiển thị trực quan tình trạng tồn kho

### 📝 Ghi Chú
- Nếu `combo_stock` là `null`: Không giới hạn số lượng
- Nếu `combo_stock` là `0`: Combo đã hết hàng
- Kiểm tra được thực hiện ở nhiều điểm: thêm giỏ hàng, cập nhật giỏ hàng, thanh toán
- Tồn kho được cập nhật tự động sau khi tạo đơn hàng thành công