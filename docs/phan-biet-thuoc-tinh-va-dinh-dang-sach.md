# Phân Biệt Thuộc Tính Sách và Định Dạng Sách

## Tổng Quan

Trong hệ thống quản lý sách, có **2 khái niệm quản lý số lượng độc lập**:

1. **Thuộc tính sách (Book Attributes)** - Biến thể sản phẩm
2. **Định dạng sách (Book Formats)** - Loại hình sản phẩm

## 📚 Định Dạng Sách (Book Formats)

### Khái niệm
**Định dạng sách** là **loại hình xuất bản** của cùng một cuốn sách.

### Các loại định dạng
- **Sách vật lý**: Sách giấy, có thể cầm nắm
- **Ebook**: Sách điện tử (PDF, EPUB)

### Cấu trúc dữ liệu
```php
// Bảng: book_formats
class BookFormat extends Model {
    protected $fillable = [
        'book_id',           // ID sách
        'format_name',       // "Sách Vật Lý" hoặc "Ebook"
        'price',             // Giá bán
        'discount',          // Giảm giá
        'stock',             // Số lượng tồn kho
        'file_url',          // File ebook (chỉ cho ebook)
        'sample_file_url',   // File xem thử
        'allow_sample_read', // Cho phép đọc thử
        'max_downloads',     // Số lần tải tối đa
        'drm_enabled',       // Bảo vệ bản quyền
        'download_expiry_days' // Ngày hết hạn tải
    ];
}
```

### Ví dụ thực tế
```
Sách: "Lập Trình Laravel"
├── Định dạng 1: Sách Vật Lý
│   ├── Giá: 250,000đ
│   ├── Giảm giá: 50,000đ
│   └── Tồn kho: 100 cuốn
└── Định dạng 2: Ebook
    ├── Giá: 150,000đ
    ├── Giảm giá: 30,000đ
    ├── File: laravel-guide.pdf
    └── Tồn kho: Không giới hạn
```

## 🏷️ Thuộc Tính Sách (Book Attributes)

### Khái niệm
**Thuộc tính sách** là **biến thể của cùng một định dạng**, tạo ra các phiên bản khác nhau.

### Các loại thuộc tính
- **Màu sắc**: Đỏ, Xanh, Vàng
- **Kích thước**: A4, A5, Pocket
- **Ngôn ngữ**: Tiếng Việt, Tiếng Anh
- **Loại bìa**: Bìa mềm, Bìa cứng
- **Phiên bản**: Thường, Đặc biệt, Collector

### Cấu trúc dữ liệu
```php
// Bảng: attributes
class Attribute extends Model {
    protected $fillable = ['name']; // "Màu sắc", "Kích thước"
}

// Bảng: attribute_values  
class AttributeValue extends Model {
    protected $fillable = [
        'attribute_id', // ID thuộc tính
        'value'         // "Đỏ", "A4", "Tiếng Việt"
    ];
}

// Bảng: book_attribute_values (Pivot)
class BookAttributeValue extends Model {
    protected $fillable = [
        'book_id',            // ID sách
        'attribute_value_id', // ID giá trị thuộc tính
        'extra_price',        // Giá thêm cho biến thể này
        'stock',              // Số lượng tồn kho riêng
        'sku'                 // Mã SKU riêng
    ];
}
```

### Ví dụ thực tế
```
Sách: "Lập Trình Laravel" (Định dạng: Sách Vật Lý)
├── Biến thể 1: Bìa mềm + Tiếng Việt
│   ├── Giá thêm: +0đ
│   ├── Tồn kho: 50 cuốn
│   └── SKU: LPL-BM-TV-001
├── Biến thể 2: Bìa cứng + Tiếng Việt  
│   ├── Giá thêm: +50,000đ
│   ├── Tồn kho: 30 cuốn
│   └── SKU: LPL-BC-TV-002
└── Biến thể 3: Bìa cứng + Tiếng Anh
    ├── Giá thêm: +100,000đ
    ├── Tồn kho: 20 cuốn
    └── SKU: LPL-BC-TA-003
```

## 🔄 Mối Quan Hệ Giữa Định Dạng và Thuộc Tính

### Cấu trúc phân cấp
```
Sách (Book)
├── Định dạng 1: Sách Vật Lý (BookFormat)
│   ├── Giá cơ bản: 250,000đ
│   ├── Tồn kho cơ bản: 100 cuốn
│   └── Thuộc tính (BookAttributeValue):
│       ├── Bìa mềm (+0đ, 50 cuốn)
│       ├── Bìa cứng (+50,000đ, 30 cuốn)
│       └── Phiên bản đặc biệt (+100,000đ, 20 cuốn)
└── Định dạng 2: Ebook (BookFormat)
    ├── Giá cơ bản: 150,000đ
    ├── Tồn kho: Không giới hạn
    └── Thuộc tính (BookAttributeValue):
        ├── PDF (+0đ)
        ├── EPUB (+10,000đ)
        └── Có âm thanh (+50,000đ)
```

## 💡 Câu Trả Lời Cho Câu Hỏi

### ❓ "Khi thêm số lượng ở thuộc tính sách, thì định dạng có cần thêm số lượng không?"

### ✅ **Trả lời: CÓ, cần thêm số lượng cho cả hai**

#### Lý do:

1. **Định dạng sách** và **thuộc tính sách** là **2 cấp độ quản lý tồn kho khác nhau**

2. **Định dạng sách** quản lý tồn kho **tổng thể** của loại hình sản phẩm

3. **Thuộc tính sách** quản lý tồn kho **chi tiết** của từng biến thể

#### Ví dụ minh họa:
```
Sách: "Lập Trình Laravel"

📖 Định dạng: Sách Vật Lý
├── Tồn kho tổng: 100 cuốn ← CẦN NHẬP
└── Phân bổ theo thuộc tính:
    ├── Bìa mềm: 50 cuốn ← CẦN NHẬP
    ├── Bìa cứng: 30 cuốn ← CẦN NHẬP
    └── Đặc biệt: 20 cuốn ← CẦN NHẬP
    └── Tổng: 100 cuốn ✓

💻 Định dạng: Ebook  
├── Tồn kho tổng: Không giới hạn ← CẦN NHẬP
└── Phân bổ theo thuộc tính:
    ├── PDF: Không giới hạn ← CẦN NHẬP
    ├── EPUB: Không giới hạn ← CẦN NHẬP
    └── Có âm thanh: Không giới hạn ← CẦN NHẬP
```

## 🎯 Quy Trình Nhập Liệu Đề Xuất

### Bước 1: Nhập thông tin định dạng
```
☑️ Sách vật lý
├── Giá: 250,000đ
├── Giảm giá: 50,000đ  
└── Số lượng: 100 cuốn ← BẮT BUỘC

☑️ Ebook
├── Giá: 150,000đ
├── Giảm giá: 30,000đ
├── File: upload-file.pdf
└── Số lượng: Không giới hạn ← BẮT BUỘC
```

### Bước 2: Nhập thuộc tính (nếu có)
```
🏷️ Thuộc tính: Loại bìa
├── Giá trị: Bìa mềm
├── Giá thêm: +0đ
└── Số lượng: 50 cuốn ← BẮT BUỘC

🏷️ Thuộc tính: Loại bìa  
├── Giá trị: Bìa cứng
├── Giá thêm: +50,000đ
└── Số lượng: 30 cuốn ← BẮT BUỘC
```

## 🔍 Validation Logic

### Kiểm tra tồn kho
```php
// Tổng tồn kho thuộc tính <= Tồn kho định dạng
$formatStock = $bookFormat->stock; // 100
$attributeStockSum = $book->attributeValues()
    ->where('book_format_id', $bookFormat->id)
    ->sum('pivot.stock'); // 50 + 30 + 20 = 100

if ($attributeStockSum > $formatStock) {
    throw new Exception('Tổng tồn kho thuộc tính vượt quá tồn kho định dạng');
}
```

## 📋 Tóm Tắt

| Khía cạnh | Định dạng sách | Thuộc tính sách |
|-----------|----------------|------------------|
| **Mục đích** | Loại hình xuất bản | Biến thể sản phẩm |
| **Ví dụ** | Sách vật lý, Ebook | Màu sắc, Kích thước |
| **Tồn kho** | Tổng thể | Chi tiết |
| **Bắt buộc** | Có | Tùy chọn |
| **Số lượng** | **CẦN NHẬP** | **CẦN NHẬP** |
| **Mối quan hệ** | 1 sách : N định dạng | 1 sách : N thuộc tính |

### 🎯 **Kết luận**
**CẢ HAI đều cần nhập số lượng** vì chúng phục vụ 2 mục đích quản lý tồn kho khác nhau:
- **Định dạng**: Quản lý tồn kho theo loại hình sản phẩm
- **Thuộc tính**: Quản lý tồn kho theo biến thể chi tiết

Việc nhập đầy đủ cả hai giúp:
✅ Quản lý tồn kho chính xác  
✅ Tránh overselling  
✅ Báo cáo tồn kho chi tiết  
✅ Trải nghiệm khách hàng tốt hơn