# Quản Lý Biến Thể Sản Phẩm - BookBee

## Tổng Quan

Hệ thống quản lý biến thể sản phẩm đã được cải tiến để hỗ trợ quản lý số lượng tồn kho và mã SKU theo từng biến thể thuộc tính của sách, tuân thủ theo docs biến thể đã định nghĩa.

## Tính Năng Mới

### 1. Quản Lý Số Lượng Tồn Kho Theo Biến Thể

#### Cấu Trúc Database
- **Bảng `book_attribute_values`** đã được mở rộng với:
  - `stock`: Số lượng tồn kho cho từng biến thể
  - `sku`: Mã SKU riêng cho từng biến thể

#### Quy Tắc Tạo Mã SKU
Theo docs biến thể, mã SKU được tạo theo format: `MÃ_CHA-HẬU_TỐ`

**Ví dụ:**
- Sách "Đắc Nhân Tâm" (ISBN: 978-604-2-12345-6)
  - Bìa cứng: `978-604-2-12345-6-BC`
  - Bìa mềm: `978-604-2-12345-6-BM`
  - Tiếng Việt: `978-604-2-12345-6-VI`
  - Tiếng Anh: `978-604-2-12345-6-EN`

**Hậu tố được định nghĩa:**
- `BC`: Bìa cứng
- `BM`: Bìa mềm
- `VI`: Tiếng Việt
- `EN`: Tiếng Anh
- `SZ`: Kích thước
- `VAR`: Biến thể khác

### 2. Tính Năng Quản Lý Tồn Kho

#### Model BookAttributeValue
```php
// Kiểm tra còn hàng
$variant->isInStock(); // true/false

// Kiểm tra tồn kho thấp (< 10)
$variant->isLowStock(); // true/false

// Lấy trạng thái tồn kho
$variant->stock_status; // 'Còn hàng', 'Tồn kho thấp', 'Hết hàng'

// Trừ tồn kho khi bán
$variant->decreaseStock(5);

// Tăng tồn kho khi nhập hàng
$variant->increaseStock(10);
```

#### Model Book
```php
// Tổng tồn kho tất cả biến thể
$book->total_variant_stock;

// Kiểm tra có biến thể nào còn hàng
$book->hasVariantInStock();

// Lấy biến thể tồn kho thấp
$book->getLowStockVariants();

// Lấy biến thể hết hàng
$book->getOutOfStockVariants();
```

### 3. Cập Nhật AdminBookController

#### Validation Mới
```php
'attribute_values.*.stock' => 'nullable|integer|min:0'
```

#### Xử Lý Lưu Biến Thể
```php
BookAttributeValue::create([
    'book_id' => $book->id,
    'attribute_value_id' => $data['id'],
    'extra_price' => $data['extra_price'] ?? 0,
    'stock' => $data['stock'] ?? 0,
    'sku' => $this->generateVariantSku($book, $data['id'])
]);
```

## Quy Trình Sử Dụng

### 1. Thêm Sách Mới
1. Nhập thông tin cơ bản sách
2. Chọn thuộc tính (định dạng, ngôn ngữ, kích thước...)
3. **MỚI**: Nhập số lượng tồn kho cho từng biến thể
4. Hệ thống tự động tạo mã SKU cho từng biến thể

### 2. Quản Lý Tồn Kho
1. **Nhập kho**: Cập nhật số lượng cho từng biến thể
2. **Kiểm soát xuất/nhập**: Tự động trừ kho khi bán
3. **Cảnh báo**: Thông báo khi tồn kho dưới 10
4. **Báo cáo**: Thống kê biến thể bán chạy/chậm

### 3. Theo Dõi Biến Thể
- Xem tổng tồn kho tất cả biến thể
- Danh sách biến thể tồn kho thấp
- Danh sách biến thể hết hàng
- Lịch sử xuất/nhập kho theo biến thể

## Migration

**File**: `database/migrations/2025_01_15_000001_add_stock_and_sku_to_book_attribute_values_table.php`

```php
Schema::table('book_attribute_values', function (Blueprint $table) {
    $table->integer('stock')->default(0)->after('extra_price');
    $table->string('sku', 100)->nullable()->after('stock');
    $table->index('sku');
    $table->index('stock');
});
```

## Lợi Ích

### Cho Admin
- Quản lý chính xác tồn kho từng biến thể
- Tránh bán vượt quá số lượng có sẵn
- Theo dõi biến thể bán chạy để nhập hàng kịp thời
- Mã SKU riêng biệt giúp quản lý kho dễ dàng

### Cho Khách Hàng
- Biết chính xác biến thể nào còn hàng
- Không bị hủy đơn do hết hàng
- Thông tin tồn kho minh bạch

## Files Liên Quan

### Models
- `app/Models/BookAttributeValue.php` - Model quản lý biến thể với stock/SKU
- `app/Models/Book.php` - Model sách với methods tổng hợp tồn kho

### Controllers
- `app/Http/Controllers/Admin/AdminBookController.php` - Quản lý sách với biến thể

### Migrations
- `database/migrations/2025_01_15_000001_add_stock_and_sku_to_book_attribute_values_table.php`

## Cải Tiến Tương Lai

1. **Giao diện quản lý tồn kho**: Trang riêng để nhập/xuất kho theo biến thể
2. **Báo cáo chi tiết**: Thống kê bán hàng theo biến thể
3. **Cảnh báo tự động**: Email thông báo khi tồn kho thấp
4. **Lịch sử xuất/nhập**: Theo dõi lịch sử thay đổi tồn kho
5. **Tích hợp barcode**: Quét mã vạch để quản lý kho

## Kết Luận

Hệ thống quản lý biến thể đã được cải tiến thành công để hỗ trợ:
- ✅ Quản lý số lượng tồn kho theo biến thể
- ✅ Tạo mã SKU tự động theo quy tắc
- ✅ Methods tiện ích cho kiểm tra tồn kho
- ✅ Validation đầy đủ cho dữ liệu nhập
- ✅ Tương thích với hệ thống hiện tại
- ✅ Tuân thủ docs biến thể đã định nghĩa