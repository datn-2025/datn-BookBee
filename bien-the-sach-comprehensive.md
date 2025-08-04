# Hệ Thống Quản Lý Biến Thể Sách - BookBee

## 📋 Tổng Quan

Hệ thống biến thể sách trong BookBee cho phép quản lý các phiên bản khác nhau của cùng một cuốn sách với các thuộc tính khác nhau như định dạng, ngôn ngữ, kích thước, màu sắc, v.v. Mỗi biến thể có thể có giá cả, số lượng tồn kho và mã SKU riêng biệt.

## 🏗️ Kiến Trúc Hệ Thống

### 1. Cấu Trúc Database

#### Bảng `attributes`
```sql
- id: Primary Key (UUID)
- name: Tên thuộc tính (varchar) - VD: "Định dạng", "Ngôn ngữ", "Kích thước"
- created_at, updated_at: Timestamps
```

#### Bảng `attribute_values`  
```sql
- id: Primary Key (UUID)
- attribute_id: Foreign Key → attributes.id
- value: Giá trị thuộc tính (varchar) - VD: "Bìa cứng", "Tiếng Việt", "A4"
- created_at, updated_at: Timestamps
```

#### Bảng `book_attribute_values` (Pivot Table)
```sql
- id: Primary Key (UUID)
- book_id: Foreign Key → books.id
- attribute_value_id: Foreign Key → attribute_values.id
- extra_price: Giá thêm cho biến thể (decimal)
- stock: Số lượng tồn kho riêng (integer)
- sku: Mã SKU riêng cho biến thể (varchar)
- created_at, updated_at: Timestamps
```

### 2. Relationships

```
Book (1) ←→ (n) BookAttributeValue (n) ←→ (1) AttributeValue (n) ←→ (1) Attribute
```

## 🎯 Tính Năng Chính

### 1. Quản Lý Thuộc Tính Biến Thể

#### A. Thêm Thuộc Tính Cho Sách
- **Chọn thuộc tính**: Dropdown hiển thị tất cả thuộc tính có sẵn
- **Chọn giá trị**: Dropdown hiển thị các giá trị của thuộc tính đã chọn
- **Giá thêm**: Số tiền thêm cho biến thể này (VNĐ)
- **Số lượng**: Số lượng tồn kho cho biến thể
- **SKU**: Tự động tạo mã SKU cho biến thể

#### B. Hiển Thị Thuộc Tính Hiện Có
- Danh sách các thuộc tính đã được gán cho sách
- Hiển thị giá thêm và tồn kho của từng biến thể
- Cho phép chỉnh sửa inline giá thêm và tồn kho
- Nút xóa để gỡ bỏ thuộc tính

### 2. Hệ Thống Tồn Kho Theo Biến Thể

#### A. Quản Lý Stock
- **Số lượng riêng biệt**: Mỗi biến thể có tồn kho độc lập
- **Cảnh báo tồn kho thấp**: Thông báo khi stock < 10
- **Kiểm tra hết hàng**: Logic kiểm tra stock = 0
- **Tự động trừ khi bán**: Hệ thống tự động giảm stock khi có đơn hàng

#### B. Trạng Thái Tồn Kho
- **Còn hàng**: stock > 10
- **Tồn kho thấp**: 1 ≤ stock < 10
- **Hết hàng**: stock = 0

### 3. Hệ Thống SKU Tự Động

#### A. Quy Tắc Tạo SKU
Format: `MÃ_CHA-HẬU_TỐ`

**Ví dụ:**
- Sách "Đắc Nhân Tâm" (ISBN: 978-604-2-12345-6)
  - Bìa cứng: `978-604-2-12345-6-BC`
  - Bìa mềm: `978-604-2-12345-6-BM`
  - Tiếng Việt: `978-604-2-12345-6-VI`
  - Tiếng Anh: `978-604-2-12345-6-EN`

#### B. Hậu Tố Được Định Nghĩa
- `BC`: Bìa cứng
- `BM`: Bìa mềm  
- `VI`: Tiếng Việt
- `EN`: Tiếng Anh
- `SZ`: Kích thước
- `DF`: Định dạng khác
- `LG`: Ngôn ngữ khác
- `VAR`: Biến thể khác

## 💻 Implementation Details

### 1. Models

#### A. BookAttributeValue Model
```php
class BookAttributeValue extends Model
{
    protected $fillable = [
        'book_id',
        'attribute_value_id', 
        'extra_price',
        'stock',
        'sku'
    ];

    // Relationships
    public function book(): BelongsTo
    public function attributeValue(): BelongsTo

    // Stock Management Methods
    public function isInStock(): bool
    public function isLowStock(): bool
    public function getStockStatusAttribute(): string
    public function decreaseStock(int $quantity): bool
    public function increaseStock(int $quantity): bool
}
```

#### B. Book Model (Extended)
```php
// Relationship với thuộc tính
public function attributeValues(): BelongsToMany
{
    return $this->belongsToMany(AttributeValue::class, 'book_attribute_values')
        ->withPivot('extra_price', 'stock', 'sku')
        ->withTimestamps();
}

// Aggregate Methods
public function getTotalVariantStockAttribute(): int
public function hasVariantInStock(): bool
public function getLowStockVariants()
public function getOutOfStockVariants()
```

#### C. AttributeValue Model
```php
class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value'];

    public function attribute(): BelongsTo
    public function books(): BelongsToMany
}
```

### 2. Controller Logic

#### A. AdminBookController
```php
// Validation cho biến thể
'attribute_values.*.stock' => 'nullable|integer|min:0'

// Tạo biến thể mới
BookAttributeValue::create([
    'book_id' => $book->id,
    'attribute_value_id' => $data['id'], 
    'extra_price' => $data['extra_price'] ?? 0,
    'stock' => $data['stock'] ?? 0,
    'sku' => $this->generateVariantSku($book, $data['id'])
]);

// Cập nhật biến thể hiện có
$book->attributeValues()->updateExistingPivot($valueId, [
    'extra_price' => $data['extra_price'] ?? 0,
    'stock' => $data['stock'] ?? 0,
]);
```

#### B. SKU Generation Logic
```php
private function generateVariantSku($book, $attributeValueId)
{
    $attributeValue = AttributeValue::with('attribute')->find($attributeValueId);
    $parentCode = $book->isbn ?: 'BOOK-' . substr($book->id, 0, 8);
    
    // Logic tạo suffix dựa trên loại thuộc tính
    $suffix = '';
    $attributeName = strtolower($attributeValue->attribute->name ?? '');
    $attributeValueName = strtolower($attributeValue->value ?? '');
    
    // Định dạng sách
    if (strpos($attributeName, 'định dạng') !== false) {
        $suffix = strpos($attributeValueName, 'cứng') !== false ? 'BC' : 'BM';
    }
    // Ngôn ngữ  
    elseif (strpos($attributeName, 'ngôn ngữ') !== false) {
        $suffix = strpos($attributeValueName, 'việt') !== false ? 'VI' : 'EN';
    }
    // Kích thước
    elseif (strpos($attributeName, 'kích thước') !== false) {
        $suffix = 'SZ';
    }
    else {
        $suffix = 'VAR';
    }
    
    return $parentCode . '-' . $suffix;
}
```

### 3. Frontend Implementation

#### A. Admin Interface (edit.blade.php)
```blade
<!-- Hiển thị thuộc tính hiện có -->
@foreach($bookAttributes as $bookAttr)
    <div class="d-flex justify-content-between align-items-center">
        <span class="badge bg-primary">{{ $bookAttr->value }}</span>
        <div>
            <input type="number" name="existing_attributes[{{ $bookAttr->id }}][extra_price]" 
                   value="{{ $bookAttr->pivot->extra_price ?? 0 }}" />
            <input type="number" name="existing_attributes[{{ $bookAttr->id }}][stock]" 
                   value="{{ $bookAttr->pivot->stock ?? 0 }}" />
        </div>
    </div>
@endforeach

<!-- Form thêm thuộc tính mới -->
<select class="attribute-select" data-attribute-id="{{ $attribute->id }}">
    @foreach($attribute->values as $value)
        <option value="{{ $value->id }}">{{ $value->value }}</option>
    @endforeach
</select>
```

#### B. JavaScript Logic
```javascript
// Thêm thuộc tính
document.addEventListener('click', function(e) {
    if (e.target.closest('.add-attribute-btn')) {
        const attributeGroup = e.target.closest('.attribute-group');
        const select = attributeGroup.querySelector('.attribute-select');
        const extraPriceInput = attributeGroup.querySelector('.attribute-extra-price');
        const stockInput = attributeGroup.querySelector('.attribute-stock');
        
        // Validation và tạo hidden inputs
        const selectedDiv = document.createElement('div');
        selectedDiv.innerHTML = `
            <input type="hidden" name="attribute_values[${valueId}][id]" value="${valueId}">
            <input type="hidden" name="attribute_values[${valueId}][extra_price]" value="${extraPrice}">
            <input type="hidden" name="attribute_values[${valueId}][stock]" value="${stock}">
        `;
    }
});
```

#### C. Client Interface (show.blade.php)
```blade
<!-- Hiển thị thuộc tính cho khách hàng -->
@foreach($book->attributeValues->unique('attribute_id') as $attrVal)
    @php
        $isLanguageAttribute = stripos($attributeName, 'Ngôn Ngữ') !== false;
    @endphp
    <div class="attribute-item" data-is-language="{{ $isLanguageAttribute ? 'true' : 'false' }}">
        <select name="attributes[{{ $attrVal->id }}]" id="attribute_{{ $attrVal->id }}">
            @foreach($filteredValues as $bookAttrVal)
                <option value="{{ $bookAttrVal->attribute_value_id }}" 
                        data-price="{{ $bookAttrVal->extra_price ?? 0 }}">
                    {{ $bookAttrVal->attributeValue->value }}
                </option>
            @endforeach
        </select>
    </div>
@endforeach
```

### 4. Cart & Order Integration

#### A. Cart Logic
```php
// Lấy thông tin biến thể khi thêm vào giỏ
$validAttributeIds = DB::table('attribute_values')
    ->join('book_attribute_values', 'attribute_values.id', '=', 'book_attribute_values.attribute_value_id')
    ->where('book_attribute_values.book_id', $bookId)
    ->whereIn('attribute_values.id', $attributeValueIds)
    ->pluck('attribute_values.id')
    ->toArray();

// Tính giá với extra_price
$attributeExtraPrice = DB::table('book_attribute_values')
    ->whereIn('attribute_value_id', $validAttributeIds)
    ->where('book_id', $bookId)
    ->sum('extra_price');
```

#### B. Stock Validation
```php
// Kiểm tra tồn kho khi đặt hàng
foreach ($cartItems as $item) {
    if ($item->attribute_value_ids) {
        $variantStock = BookAttributeValue::where('book_id', $item->book_id)
            ->whereIn('attribute_value_id', $attributeIds)
            ->sum('stock');
            
        if ($variantStock < $item->quantity) {
            throw new \Exception('Biến thể không đủ số lượng');
        }
    }
}
```

## 🎨 Giao Diện Người Dùng

### 1. Admin Interface Features

#### A. Trang Tạo/Sửa Sách
- **Section thuộc tính**: Chỉ hiển thị khi chọn "Sách vật lý"
- **Thuộc tính hiện có**: Hiển thị dạng card với thông tin đầy đủ
- **Form thêm mới**: Row với dropdown, input giá và stock
- **Real-time validation**: Kiểm tra duplicate, format số
- **SKU preview**: Hiển thị SKU sẽ được tạo

#### B. Trang Danh Sách Sách
- **Cột biến thể**: Hiển thị tóm tắt các biến thể
- **Badge stock**: Màu sắc theo mức tồn kho
- **Tooltip SKU**: Hiển thị SKU khi hover

### 2. Client Interface Features

#### A. Trang Chi Tiết Sách
- **Dropdown thuộc tính**: Cho phép chọn biến thể
- **Cập nhật giá real-time**: Giá thay đổi theo biến thể
- **Thông tin stock**: Hiển thị số lượng còn lại
- **Ebook handling**: Chỉ hiển thị thuộc tính ngôn ngữ cho ebook

#### B. Trang Giỏ Hàng
- **Thông tin biến thể**: Hiển thị thuộc tính đã chọn
- **Giá chi tiết**: Phân tách giá gốc và giá thêm
- **Validation số lượng**: Kiểm tra với stock của biến thể

## 🔧 Migration và Setup

### 1. Database Migration
```php
// Migration: add_stock_and_sku_to_book_attribute_values_table.php
Schema::table('book_attribute_values', function (Blueprint $table) {
    $table->integer('stock')->default(0)->after('extra_price');
    $table->string('sku', 100)->nullable()->after('stock');
    $table->index('sku');
    $table->index('stock');
});
```

### 2. Factory Support
```php
// BookAttributeValueFactory.php
class BookAttributeValueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'attribute_value_id' => AttributeValue::factory(),
            'extra_price' => $this->faker->optional()->randomFloat(2, 0, 50000),
            'stock' => $this->faker->numberBetween(0, 100),
            'sku' => $this->faker->unique()->regexify('[A-Z0-9]{10}-[A-Z]{2,3}')
        ];
    }
}
```

## 📊 Business Logic

### 1. Pricing Logic
```
Giá cuối = Giá cơ bản + Sum(Extra price của các thuộc tính đã chọn)
```

### 2. Stock Management Logic
```
Stock sách = Stock format + Sum(Stock các biến thể)
Stock kiểm tra = Min(Stock format, Stock biến thể đã chọn)
```

### 3. Display Priority
1. **Ebook**: Chỉ hiển thị thuộc tính ngôn ngữ
2. **Sách vật lý**: Hiển thị tất cả thuộc tính
3. **Combo**: Không hiển thị thuộc tính (quản lý riêng)

## 🚀 Quy Trình Sử Dụng

### 1. Admin Workflow

#### A. Tạo Sách Mới
1. Nhập thông tin cơ bản sách
2. Chọn "Sách vật lý" để hiển thị section thuộc tính  
3. Chọn thuộc tính và giá trị từ dropdown
4. Nhập giá thêm và số lượng tồn kho
5. Click "Thêm" - hệ thống tự động tạo SKU
6. Lưu sách - tất cả biến thể được tạo

#### B. Quản Lý Biến Thể
1. Vào trang edit sách
2. Xem danh sách biến thể hiện có
3. Chỉnh sửa giá thêm/stock inline
4. Thêm biến thể mới nếu cần
5. Xóa biến thể không cần thiết

### 2. Customer Workflow

#### A. Mua Sách Có Biến Thể
1. Vào trang chi tiết sách
2. Chọn định dạng (physical/ebook)
3. Chọn các thuộc tính mong muốn
4. Giá tự động cập nhật theo biến thể
5. Chọn số lượng (trong giới hạn stock)
6. Thêm vào giỏ hàng

#### B. Checkout Process
1. Xem lại thông tin biến thể trong giỏ
2. Hệ thống validate stock trước khi thanh toán
3. Tạo đơn hàng với thông tin biến thể đầy đủ
4. Tự động trừ stock sau khi đặt hàng thành công

## 🔍 Advanced Features

### 1. Inventory Management
```php
// Báo cáo tồn kho thấp
$lowStockVariants = Book::with('attributeValues')
    ->whereHas('attributeValues', function($q) {
        $q->where('book_attribute_values.stock', '>', 0)
          ->where('book_attribute_values.stock', '<', 10);
    })->get();

// Biến thể bán chạy
$topSellingVariants = BookAttributeValue::join('order_items', ...)
    ->select('book_attribute_values.*', DB::raw('SUM(order_items.quantity) as total_sold'))
    ->groupBy('book_attribute_values.id')
    ->orderBy('total_sold', 'desc')
    ->get();
```

### 2. Price Calculation Helpers
```php
// Tính giá biến thể
public function getVariantPrice(array $attributeValueIds): float
{
    $basePrice = $this->formats->where('format_name', 'Sách Vật Lý')->first()->price ?? 0;
    $extraPrice = $this->attributeValues()
        ->whereIn('attribute_value_id', $attributeValueIds)
        ->sum('book_attribute_values.extra_price');
    
    return $basePrice + $extraPrice;
}
```

### 3. SEO and URL Handling
```php
// URL với biến thể
/sach/{slug}?variant={sku}
/sach/dac-nhan-tam?variant=978-604-2-12345-6-BC

// Canonical URL management
public function getCanonicalVariantUrl(array $attributes = []): string
{
    $url = route('book.show', ['id' => $this->id, 'slug' => $this->slug]);
    if (!empty($attributes)) {
        $sku = $this->getVariantSku($attributes);
        $url .= '?variant=' . $sku;
    }
    return $url;
}
```

## 🛡️ Error Handling và Validation

### 1. Common Issues
- **Null AttributeValue**: Sử dụng null coalescing `??` 
- **Missing Pivot Data**: Eager load với `withPivot()`
- **Stock Validation**: Kiểm tra trước khi cho phép mua
- **Duplicate Attributes**: Validation trong JavaScript và backend

### 2. Validation Rules
```php
// Controller validation
'attribute_values' => 'nullable|array',
'attribute_values.*.id' => 'required|exists:attribute_values,id',
'attribute_values.*.extra_price' => 'nullable|numeric|min:0',
'attribute_values.*.stock' => 'nullable|integer|min:0',

// JavaScript validation
if (existingValue) {
    alert(`Thuộc tính ${valueName} đã được thêm`);
    return;
}
```

### 3. Error Recovery
```php
// Graceful degradation
try {
    $variantPrice = $book->getVariantPrice($attributeIds);
} catch (\Exception $e) {
    $variantPrice = $book->base_price;
    \Log::warning('Variant price calculation failed', [
        'book_id' => $book->id,
        'attributes' => $attributeIds,
        'error' => $e->getMessage()
    ]);
}
```

## 📈 Performance Considerations

### 1. Database Optimization
- **Indexes**: SKU, stock, attribute_value_id
- **Eager Loading**: Load relationships để tránh N+1
- **Caching**: Cache attribute lists và frequent queries

### 2. Frontend Optimization
- **Lazy Loading**: Load attributes khi cần
- **Debounce**: Debounce price updates
- **Local Storage**: Cache attribute selections

## 🔮 Future Enhancements

### 1. Advanced Features
- **Bulk Stock Management**: Cập nhật stock hàng loạt
- **Variant Images**: Ảnh riêng cho từng biến thể  
- **Dynamic Pricing**: Giá thay đổi theo thời gian
- **Inventory Alerts**: Email thông báo stock thấp

### 2. Integration Possibilities
- **Barcode Generation**: Tạo barcode từ SKU
- **POS Integration**: Đồng bộ với hệ thống bán hàng
- **Warehouse Management**: Tích hợp quản lý kho
- **Analytics**: Báo cáo chi tiết theo biến thể

---

**Tài liệu được tạo**: {{ date('Y-m-d H:i:s') }}  
**Phiên bản**: 1.0  
**Tác giả**: AI Assistant  
**Trạng thái**: Hoàn thành và đang vận hành
