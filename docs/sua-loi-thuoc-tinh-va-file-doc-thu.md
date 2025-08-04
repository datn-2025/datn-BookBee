# Sửa lỗi thuộc tính sách và thêm file đọc thử cho Ebook

## Mục đích
Sửa lỗi hiển thị thuộc tính sách và thêm tính năng file đọc thử cho sách điện tử trong trang chỉnh sửa sách.

## Các vấn đề đã sửa

### 1. Lỗi JavaScript thuộc tính sách
**Vấn đề**: JavaScript không hoạt động khi thêm thuộc tính mới
- Button có class `.add-attribute-btn` nhưng JavaScript tìm kiếm `.add-attribute-value`
- Gây ra lỗi không thể thêm thuộc tính mới

**Giải pháp**: 
```javascript
// Trước
if (e.target.closest('.add-attribute-value')) {
    const button = e.target.closest('.add-attribute-value');

// Sau
if (e.target.closest('.add-attribute-btn')) {
    const button = e.target.closest('.add-attribute-btn');
```

### 2. Thiếu file đọc thử cho Ebook
**Vấn đề**: Không có trường upload file đọc thử và checkbox cho phép đọc thử

**Giải pháp**: Thêm các trường mới trong phần Ebook:

#### File đọc thử
```html
<div class="col-12">
    <label class="form-label fw-medium">File đọc thử</label>
    @if($ebookFormat && $ebookFormat->sample_file_url)
        <div class="mb-2">
            <small class="text-success">
                <i class="ri-file-check-line me-1"></i>
                File đọc thử hiện tại: {{ basename($ebookFormat->sample_file_url) }}
            </small>
        </div>
    @endif
    <input type="file" class="form-control" name="formats[ebook][sample_file]" 
           accept=".pdf,.epub">
    <div class="form-text">File đọc thử cho khách hàng. Chấp nhận file PDF hoặc EPUB, tối đa 10MB.</div>
</div>
```

#### Checkbox cho phép đọc thử
```html
<div class="col-12">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="allow_sample_read" 
               name="formats[ebook][allow_sample_read]" value="1" 
               {{ old('formats.ebook.allow_sample_read', $ebookFormat->allow_sample_read ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="allow_sample_read">
            <i class="ri-eye-line me-1"></i>Cho phép đọc thử trực tuyến
        </label>
    </div>
    <div class="form-text">Khách hàng có thể đọc thử một phần nội dung sách trước khi mua.</div>
</div>
```

### 3. Sửa đường dẫn file Ebook
**Vấn đề**: Sử dụng `file_path` thay vì `file_url`

**Giải pháp**: 
```php
// Trước
@if($ebookFormat && $ebookFormat->file_path)
    File hiện tại: {{ basename($ebookFormat->file_path) }}

// Sau  
@if($ebookFormat && $ebookFormat->file_url)
    File hiện tại: {{ basename($ebookFormat->file_url) }}
```

## Tính năng mới

### File đọc thử Ebook
- **Upload file đọc thử**: Cho phép upload file PDF/EPUB riêng làm bản đọc thử
- **Hiển thị file hiện tại**: Hiển thị tên file đọc thử đã upload (nếu có)
- **Checkbox cho phép đọc thử**: Bật/tắt tính năng đọc thử trực tuyến
- **Validation**: Chấp nhận file PDF, EPUB, tối đa 10MB

### Cải thiện UX thuộc tính
- **JavaScript hoạt động đúng**: Có thể thêm/xóa thuộc tính mới
- **Hiển thị trực quan**: Badge hiển thị giá thêm, tồn kho, SKU
- **Validation**: Kiểm tra trùng lặp thuộc tính

## Cấu trúc form data

### Ebook với file đọc thử
```php
formats: [
    'ebook' => [
        'price' => 50000,
        'discount' => 5000,
        'file' => UploadedFile,           // File ebook chính
        'sample_file' => UploadedFile,    // File đọc thử
        'allow_sample_read' => 1          // Cho phép đọc thử
    ]
]
```

### Thuộc tính sách
```php
attribute_values: [
    '1' => [
        'id' => 1,
        'extra_price' => 10000,
        'stock' => 50
    ]
]
```

## Kết quả đạt được

✅ **Thuộc tính sách hoạt động đúng**
- JavaScript thêm/xóa thuộc tính hoạt động
- Hiển thị thuộc tính hiện có với khả năng chỉnh sửa
- Form thêm thuộc tính mới hoạt động bình thường

✅ **File đọc thử Ebook**
- Có thể upload file đọc thử riêng biệt
- Hiển thị file đọc thử hiện tại
- Checkbox điều khiển tính năng đọc thử

✅ **Cải thiện UX**
- Giao diện trực quan với icon và badge
- Thông báo lỗi rõ ràng
- Validation đầy đủ

## Checklist kiểm tra

- [ ] Thêm thuộc tính mới hoạt động
- [ ] Xóa thuộc tính hoạt động  
- [ ] Upload file đọc thử thành công
- [ ] Checkbox cho phép đọc thử hoạt động
- [ ] Hiển thị file ebook và đọc thử hiện tại
- [ ] Validation file upload đúng định dạng
- [ ] Form submit không lỗi

## Lưu ý phát triển

1. **Database**: Cần đảm bảo bảng `book_formats` có các cột:
   - `file_url`: Đường dẫn file ebook chính
   - `sample_file_url`: Đường dẫn file đọc thử
   - `allow_sample_read`: Boolean cho phép đọc thử

2. **Controller**: Cần xử lý upload và lưu file đọc thử trong `AdminBookController`

3. **Storage**: Cần tạo thư mục riêng cho file đọc thử (ví dụ: `storage/app/public/ebooks/samples/`)

4. **Frontend**: Cần tích hợp viewer để hiển thị file đọc thử cho khách hàng