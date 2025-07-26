# Sửa lỗi Validation Định dạng Sách

## Mô tả vấn đề

**Trước khi sửa:**
- Khi chỉ chọn định dạng sách vật lý (không chọn ebook), hệ thống vẫn validate các trường ebook
- Lỗi xuất hiện: "Giá bán ebook phải là số" ngay cả khi không chọn ebook
- Người dùng không thể tạo sách chỉ có định dạng vật lý

**Sau khi sửa:**
- Validation chỉ áp dụng cho định dạng được chọn
- Có thể tạo sách chỉ có định dạng vật lý, chỉ có ebook, hoặc cả hai
- Không còn lỗi validation không cần thiết

## Nguyên nhân

Validation rules sử dụng `required_if:has_ebook,1` nhưng giá trị boolean từ form có thể là `true/false` thay vì `1/0`, dẫn đến validation không hoạt động đúng.

## Giải pháp

### 1. Sửa Validation Rules

**Trước:**
```php
'formats.physical.price' => 'required_if:has_physical,1|numeric|min:0',
'formats.physical.stock' => 'required_if:has_physical,1|integer|min:0',
'formats.ebook.price' => 'required_if:has_ebook,1|numeric|min:0',
'formats.ebook.file' => 'required_if:has_ebook,1|mimes:pdf,epub|max:50000',
```

**Sau:**
```php
'formats.physical.price' => 'required_if:has_physical,true|nullable|numeric|min:0',
'formats.physical.stock' => 'required_if:has_physical,true|nullable|integer|min:0',
'formats.ebook.price' => 'required_if:has_ebook,true|nullable|numeric|min:0',
'formats.ebook.file' => 'required_if:has_ebook,true|nullable|mimes:pdf,epub|max:50000',
```

### 2. Thay đổi chính

- Đổi từ `required_if:has_ebook,1` thành `required_if:has_ebook,true`
- Thêm `nullable` để cho phép trường rỗng khi không chọn định dạng
- Áp dụng cho cả method `store` và `update` trong `AdminBookController`

## Files đã thay đổi

### 1. `app/Http/Controllers/Admin/AdminBookController.php`

**Method `store` (dòng 141-148):**
- Sửa validation cho `formats.physical.price`, `formats.physical.stock`
- Sửa validation cho `formats.ebook.price`, `formats.ebook.file`

**Method `update` (dòng 404-409):**
- Sửa validation tương tự như method `store`
- Đảm bảo tính nhất quán giữa tạo mới và cập nhật

## Kết quả

### Trước khi sửa:
- ❌ Không thể tạo sách chỉ có định dạng vật lý
- ❌ Lỗi validation "Giá bán ebook phải là số" khi không chọn ebook
- ❌ Trải nghiệm người dùng kém

### Sau khi sửa:
- ✅ Có thể tạo sách chỉ có định dạng vật lý
- ✅ Có thể tạo sách chỉ có định dạng ebook
- ✅ Có thể tạo sách có cả hai định dạng
- ✅ Validation chỉ áp dụng cho định dạng được chọn
- ✅ Trải nghiệm người dùng được cải thiện

## Cách test

1. **Test tạo sách chỉ có định dạng vật lý:**
   - Vào `/admin/books/create`
   - Chỉ tick "Sách vật lý", không tick "Ebook"
   - Điền thông tin sách vật lý
   - Submit form → Không có lỗi validation

2. **Test tạo sách chỉ có định dạng ebook:**
   - Chỉ tick "Ebook", không tick "Sách vật lý"
   - Điền thông tin ebook
   - Submit form → Không có lỗi validation

3. **Test tạo sách có cả hai định dạng:**
   - Tick cả "Sách vật lý" và "Ebook"
   - Điền đầy đủ thông tin
   - Submit form → Hoạt động bình thường

## Lưu ý

- Thay đổi này không ảnh hưởng đến dữ liệu hiện có
- Validation vẫn đảm bảo tính toàn vẹn dữ liệu
- Cải thiện trải nghiệm người dùng khi tạo/sửa sách
- Tương thích với cả tạo mới và cập nhật sách

---

**Ngày tạo:** $(date)
**Tác giả:** AI Assistant
**Phiên bản:** 1.0