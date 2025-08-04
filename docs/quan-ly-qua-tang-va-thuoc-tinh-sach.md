# Quản Lý Quà Tặng và Thuộc Tính Sách - BookBee

## Tổng Quan

Hệ thống quản lý sách đã được mở rộng với hai tính năng mới:
1. **Quản lý quà tặng kèm theo sách**
2. **Quản lý thuộc tính sách với biến thể**

Các tính năng này giúp tăng tính linh hoạt trong việc quản lý sản phẩm và cải thiện trải nghiệm khách hàng.

## 1. Quản Lý Quà Tặng

### Mô tả chức năng
Quà tặng là các sản phẩm đi kèm với sách như bookmark, postcard, sticker, v.v. Mỗi sách có thể có một quà tặng kèm theo với thời gian khuyến mãi cụ thể.

### Cấu trúc Database
**Bảng `book_gifts`:**
```sql
CREATE TABLE book_gifts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    book_id VARCHAR(36) NOT NULL,
    gift_name VARCHAR(255) NOT NULL,
    gift_description TEXT,
    gift_image VARCHAR(255),
    quantity INT DEFAULT 1,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (book_id) REFERENCES books(id)
);
```

### Tính năng chính

#### Thêm quà tặng (Create)
- **Checkbox toggle**: Bật/tắt chế độ có quà tặng
- **Tên quà tặng**: Bắt buộc khi có quà tặng
- **Mô tả**: Mô tả chi tiết về quà tặng
- **Hình ảnh**: Upload ảnh quà tặng (JPG, PNG, GIF, tối đa 2MB)
- **Số lượng**: Số lượng quà tặng kèm theo
- **Thời gian**: Ngày bắt đầu và kết thúc chương trình tặng quà

#### Sửa quà tặng (Edit)
- Hiển thị thông tin quà tặng hiện tại
- Cho phép cập nhật tất cả thông tin
- Giữ nguyên ảnh cũ nếu không upload ảnh mới
- Có thể xóa quà tặng bằng cách bỏ check "Sách có kèm quà tặng"

### Giao diện

#### Card Quà Tặng
```html
<!-- Header với gradient màu -->
<div class="card-header bg-gradient text-white" 
     style="background: linear-gradient(45deg, #ff6b6b, #ffa500) !important;">
    <h5 class="mb-0">
        <i class="ri-gift-line me-2"></i>Quà tặng kèm theo
    </h5>
</div>

<!-- Form toggle -->
<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" id="has_gift" name="has_gift" value="1">
    <label class="form-check-label fw-medium" for="has_gift">
        <i class="ri-gift-2-line me-1"></i>Sách có kèm quà tặng
    </label>
</div>
```

#### JavaScript xử lý
```javascript
// Toggle hiển thị form quà tặng
function toggleGiftSection() {
    const giftCheckbox = document.getElementById('has_gift');
    const giftSection = document.getElementById('gift_section');
    
    if (giftCheckbox && giftSection) {
        giftSection.style.display = giftCheckbox.checked ? 'block' : 'none';
    }
}

// Preview ảnh quà tặng
if (giftImageInput) {
    giftImageInput.addEventListener('change', function(e) {
        const preview = document.getElementById('gift_image_preview');
        preview.innerHTML = '';
        
        if (e.target.files[0]) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(e.target.files[0]);
            img.className = 'img-thumbnail';
            img.style.maxHeight = '150px';
            preview.appendChild(img);
        }
    });
}
```

## 2. Quản Lý Thuộc Tính Sách

### Mô tả chức năng
Thuộc tính sách cho phép tạo các biến thể của sách với giá thêm và số lượng tồn kho riêng biệt. Ví dụ: ngôn ngữ, loại bìa, kích thước, v.v.

### Cấu trúc Database

**Bảng `attributes`:**
```sql
CREATE TABLE attributes (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Bảng `attribute_values`:**
```sql
CREATE TABLE attribute_values (
    id VARCHAR(36) PRIMARY KEY,
    attribute_id VARCHAR(36) NOT NULL,
    value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (attribute_id) REFERENCES attributes(id)
);
```

**Bảng `book_attribute_values`:**
```sql
CREATE TABLE book_attribute_values (
    id VARCHAR(36) PRIMARY KEY,
    book_id VARCHAR(36) NOT NULL,
    attribute_value_id VARCHAR(36) NOT NULL,
    extra_price DECIMAL(10,2) DEFAULT 0,
    stock INT DEFAULT 0,
    sku VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (attribute_value_id) REFERENCES attribute_values(id)
);
```

### Tính năng chính

#### Thêm thuộc tính
- **Chọn thuộc tính**: Dropdown hiển thị tất cả thuộc tính có sẵn
- **Chọn giá trị**: Dropdown hiển thị các giá trị của thuộc tính đã chọn
- **Giá thêm**: Số tiền thêm cho biến thể này (VNĐ)
- **Số lượng**: Số lượng tồn kho cho biến thể
- **SKU**: Tự động tạo mã SKU cho biến thể

#### Hiển thị thuộc tính đã chọn
- **Card layout**: Hiển thị dưới dạng card với thông tin đầy đủ
- **Badge màu sắc**: 
  - Xanh lá: Giá thêm
  - Xanh dương: Số lượng tồn kho
  - Xám: Mã SKU
- **Nút xóa**: Cho phép xóa thuộc tính đã thêm

### Giao diện

#### Card Thuộc Tính
```html
<!-- Header màu tím -->
<div class="card-header bg-purple text-white" 
     style="background-color: #6f42c1 !important;">
    <h5 class="mb-0">
        <i class="ri-price-tag-3-line me-2"></i>Thuộc tính sách
    </h5>
</div>

<!-- Form thêm thuộc tính -->
<div class="attribute-group mb-4 p-3 border rounded bg-light">
    <h6 class="fw-bold text-primary mb-3">
        <i class="ri-bookmark-line me-1"></i>{{ $attribute->name }}
    </h6>
    
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <select class="form-select attribute-select">
                <!-- Options -->
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control attribute-extra-price">
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control attribute-stock">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary add-attribute-value">
                <i class="ri-add-line me-1"></i>Thêm
            </button>
        </div>
    </div>
    
    <!-- Container hiển thị thuộc tính đã chọn -->
    <div class="selected-variants-container"></div>
</div>
```

#### JavaScript xử lý thuộc tính
```javascript
// Xử lý thêm thuộc tính
document.addEventListener('click', function(e) {
    if (e.target.closest('.add-attribute-value')) {
        const button = e.target.closest('.add-attribute-value');
        const attributeGroup = button.closest('.attribute-group');
        
        // Lấy thông tin từ form
        const select = attributeGroup.querySelector('.attribute-select');
        const extraPriceInput = attributeGroup.querySelector('.attribute-extra-price');
        const stockInput = attributeGroup.querySelector('.attribute-stock');
        const selectedValuesContainer = attributeGroup.querySelector('.selected-variants-container');
        
        // Validation
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption.value) {
            alert('Vui lòng chọn một giá trị thuộc tính');
            return;
        }
        
        // Kiểm tra trùng lặp
        const existingValue = attributeGroup.querySelector(`input[name="attribute_values[${valueId}][id]"]`);
        if (existingValue) {
            alert(`Thuộc tính ${valueName} đã được thêm`);
            return;
        }
        
        // Tạo element hiển thị
        const selectedDiv = document.createElement('div');
        selectedDiv.className = 'selected-attribute-value mb-2 p-3 border rounded bg-white shadow-sm';
        selectedDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <div class="fw-medium text-dark mb-1">
                        <i class="ri-bookmark-line me-1 text-primary"></i>${valueName}
                    </div>
                    <div class="small text-muted">
                        <span class="badge bg-success-subtle text-success me-2">
                            <i class="ri-money-dollar-circle-line me-1"></i>+${extraPrice.toLocaleString('vi-VN')}đ
                        </span>
                        <span class="badge bg-info-subtle text-info me-2">
                            <i class="ri-archive-line me-1"></i>${stock} sp
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="ri-barcode-line me-1"></i>SKU: Tự động tạo
                        </span>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm remove-attribute-value">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
            <input type="hidden" name="attribute_values[${valueId}][id]" value="${valueId}">
            <input type="hidden" name="attribute_values[${valueId}][extra_price]" value="${extraPrice}">
            <input type="hidden" name="attribute_values[${valueId}][stock]" value="${stock}">
        `;
        
        selectedValuesContainer.appendChild(selectedDiv);
        
        // Reset form
        select.selectedIndex = 0;
        extraPriceInput.value = '0';
        stockInput.value = '0';
    }
});
```

## Model Relationships

### Book Model
```php
// Relationship với quà tặng
public function gifts(): HasMany
{
    return $this->hasMany(BookGift::class);
}

// Relationship với thuộc tính
public function attributeValues(): BelongsToMany
{
    return $this->belongsToMany(AttributeValue::class, 'book_attribute_values', 'book_id', 'attribute_value_id')
        ->withPivot('extra_price', 'stock', 'sku')
        ->withTimestamps();
}
```

### BookGift Model
```php
protected $fillable = [
    'book_id', 'gift_name', 'gift_description', 'gift_image', 
    'quantity', 'start_date', 'end_date'
];

public function book()
{
    return $this->belongsTo(Book::class);
}
```

## Controller Logic

### AdminBookController
```php
// Lưu quà tặng
if ($request->filled('gift_name')) {
    $giftData = [
        'book_id' => $book->id,
        'gift_name' => $request->input('gift_name'),
        'gift_description' => $request->input('gift_description'),
        'quantity' => $request->input('quantity', 0),
        'start_date' => $request->input('start_date'),
        'end_date' => $request->input('end_date'),
    ];
    if ($request->hasFile('gift_image')) {
        $giftData['gift_image'] = $request->file('gift_image')->store('gifts', 'public');
    }
    BookGift::create($giftData);
}

// Lưu thuộc tính
if ($request->filled('attribute_values')) {
    foreach ($request->attribute_values as $valueId => $data) {
        BookAttributeValue::create([
            'book_id' => $book->id,
            'attribute_value_id' => $data['id'],
            'extra_price' => $data['extra_price'] ?? 0,
            'stock' => $data['stock'] ?? 0,
            'sku' => $this->generateVariantSku($book, $data['id'])
        ]);
    }
}
```

## Files Liên Quan

### Views
- `resources/views/admin/books/create.blade.php` - Form thêm sách với quà tặng và thuộc tính
- `resources/views/admin/books/edit.blade.php` - Form sửa sách với quà tặng và thuộc tính

### Models
- `app/Models/Book.php` - Model sách với relationships
- `app/Models/BookGift.php` - Model quà tặng
- `app/Models/Attribute.php` - Model thuộc tính
- `app/Models/AttributeValue.php` - Model giá trị thuộc tính
- `app/Models/BookAttributeValue.php` - Model pivot cho thuộc tính sách

### Controllers
- `app/Http/Controllers/Admin/AdminBookController.php` - Controller quản lý sách

## Lợi Ích

### Cho Admin
- **Quản lý quà tặng**: Tăng tính hấp dẫn của sản phẩm
- **Thuộc tính linh hoạt**: Quản lý biến thể sách với giá và tồn kho riêng
- **Giao diện trực quan**: Form dễ sử dụng với preview ảnh
- **Validation đầy đủ**: Tránh lỗi nhập liệu

### Cho Khách Hàng
- **Thông tin rõ ràng**: Biết được quà tặng kèm theo
- **Lựa chọn đa dạng**: Nhiều biến thể sách để chọn
- **Giá cả minh bạch**: Hiển thị giá thêm cho từng biến thể
- **Tồn kho chính xác**: Biết được số lượng còn lại

## Cải Tiến Trong Tương Lai

1. **Quà tặng**:
   - Hỗ trợ nhiều quà tặng cho một sách
   - Quà tặng theo điều kiện (mua từ X cuốn)
   - Quà tặng ngẫu nhiên

2. **Thuộc tính**:
   - Thuộc tính có ảnh riêng
   - Thuộc tính ảnh hưởng đến giá vận chuyển
   - Combo thuộc tính (ví dụ: bìa cứng + tiếng Anh)

3. **Giao diện**:
   - Drag & drop sắp xếp thuộc tính
   - Bulk edit thuộc tính
   - Import/Export thuộc tính từ Excel