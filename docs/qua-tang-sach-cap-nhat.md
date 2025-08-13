# Chức Năng Quà Tặng Sách - Cập Nhật

## Mô Tả Chức Năng

Chức năng quà tặng sách cho phép admin tạo và quản lý quà tặng kèm theo sách. Quà tặng có thể được tạo cho chính sách đang tạo/sửa hoặc cho một sách khác đã có sẵn.

## Các Tính Năng Chính

### 1. Tạo Quà Tặng Khi Tạo Sách Mới
- Admin có thể tạo quà tặng ngay khi tạo sách mới
- Quà tặng có thể được gán cho chính sách đang tạo hoặc sách khác
- Dropdown "Chọn sách nhận quà tặng" với option mặc định "Sách hiện tại (đang tạo)"

### 2. Cập Nhật Quà Tặng Khi Sửa Sách
- Hiển thị thông tin quà tặng hiện tại (nếu có)
- Cho phép cập nhật hoặc tạo mới quà tặng
- Logic xóa quà tặng cũ và tạo mới khi cập nhật

### 3. Validation Dữ Liệu
- `gift_book_id`: nullable, uuid, exists:books,id
- `gift_name`: nullable, string, max:255
- `gift_description`: nullable, string
- `gift_image`: nullable, image, max:2048KB
- `quantity`: nullable, integer, min:0
- `gift_start_date`: nullable, date
- `gift_end_date`: nullable, date, after_or_equal:gift_start_date

## Cấu Trúc Database

### Bảng `book_gifts`
```sql
- id (primary key)
- book_id (foreign key to books.id)
- gift_name (string)
- gift_description (text, nullable)
- gift_image (string, nullable)
- quantity (integer)
- start_date (date, nullable)
- end_date (date, nullable)
- created_at
- updated_at
```

## Logic Xử Lý

### Trong AdminBookController

#### Phương thức `store` (Tạo sách mới)
```php
// Lưu quà tặng nếu có
if ($request->filled('gift_name')) {
    // Nếu có chọn sách khác thì dùng gift_book_id, không thì dùng sách hiện tại
    $bookId = $request->filled('gift_book_id') ? $request->input('gift_book_id') : $book->id;
    
    $giftData = [
        'book_id' => $bookId,
        'gift_name' => $request->input('gift_name'),
        'gift_description' => $request->input('gift_description'),
        'quantity' => $request->input('quantity', 0),
        'start_date' => $request->input('gift_start_date'),
        'end_date' => $request->input('gift_end_date'),
    ];
    if ($request->hasFile('gift_image')) {
        $giftData['gift_image'] = $request->file('gift_image')->store('gifts', 'public');
    }
    BookGift::create($giftData);
}
```

#### Phương thức `update` (Cập nhật sách)
```php
// Cập nhật quà tặng
// Xóa quà tặng cũ
$book->gifts()->delete();

// Tạo quà tặng mới nếu có
if ($request->filled('gift_name')) {
    // Logic tương tự như store
    // ...
}
```

#### Phương thức `edit` (Hiển thị form sửa)
```php
// Lấy quà tặng hiện tại của sách (nếu có)
$currentGift = $book->gifts->first();

return view('admin.books.edit', compact(
    // ... other variables
    'currentGift'
));
```

## Giao Diện Form

### Form Tạo Sách (`create.blade.php`)
```html
<!-- Dropdown chọn sách nhận quà tặng -->
<select name="gift_book_id">
    <option value="" selected>Sách hiện tại (đang tạo)</option>
    @foreach($books as $book)
        <option value="{{ $book->id }}">{{ $book->title }}</option>
    @endforeach
</select>

<!-- Các trường thông tin quà tặng -->
<input type="text" name="gift_name" placeholder="Tên quà tặng">
<textarea name="gift_description" placeholder="Mô tả quà tặng"></textarea>
<input type="number" name="quantity" placeholder="Số lượng">
<input type="file" name="gift_image" accept="image/*">

<!-- Date range picker cho thời gian khuyến mãi -->
<input type="text" id="gift_date_range" placeholder="Chọn khoảng thời gian">
<input type="hidden" name="gift_start_date">
<input type="hidden" name="gift_end_date">
```

### Form Sửa Sách (`edit.blade.php`)
- Tương tự form tạo nhưng có hiển thị dữ liệu hiện tại
- Sử dụng `$currentGift` để fill dữ liệu cũ
- Hiển thị ảnh quà tặng hiện tại (nếu có)

## Cách Sử Dụng

### 1. Tạo Quà Tặng Cho Sách Hiện Tại
1. Vào trang tạo sách mới
2. Điền thông tin sách
3. Trong phần "Quà Tặng", để dropdown "Chọn sách nhận quà tặng" ở giá trị mặc định
4. Điền tên quà tặng và các thông tin khác
5. Lưu sách

### 2. Tạo Quà Tặng Cho Sách Khác
1. Vào trang tạo sách mới
2. Điền thông tin sách
3. Trong dropdown "Chọn sách nhận quà tặng", chọn sách đích
4. Điền thông tin quà tặng
5. Lưu sách

### 3. Cập Nhật Quà Tặng
1. Vào trang sửa sách
2. Thông tin quà tặng hiện tại sẽ được hiển thị
3. Cập nhật thông tin cần thiết
4. Lưu thay đổi

## Test Cases Đã Kiểm Tra

### ✅ Test Case 1: Tạo quà tặng cho sách hiện tại
- Tạo sách mới với quà tặng
- Không chọn `gift_book_id`
- Quà tặng được tạo với `book_id` = ID của sách vừa tạo

### ✅ Test Case 2: Tạo quà tặng cho sách khác
- Tạo sách mới với quà tặng
- Chọn `gift_book_id` là ID của sách khác
- Quà tặng được tạo với `book_id` = `gift_book_id`

### ✅ Test Case 3: Validation dữ liệu
- Tất cả validation rules hoạt động đúng
- Ngày kết thúc phải >= ngày bắt đầu
- File ảnh phải đúng định dạng và kích thước

### ✅ Test Case 4: Cập nhật quà tặng
- Hiển thị đúng thông tin quà tặng hiện tại
- Xóa quà tặng cũ và tạo mới khi cập nhật
- Logic xử lý `gift_book_id` hoạt động đúng

## Lưu Ý Kỹ Thuật

1. **Tên trường ngày tháng**: Sử dụng `gift_start_date` và `gift_end_date` thay vì `start_date` và `end_date`
2. **Logic chọn sách**: Nếu không chọn `gift_book_id`, mặc định sử dụng ID của sách hiện tại
3. **Xử lý ảnh**: Ảnh quà tặng được lưu trong thư mục `storage/app/public/gifts`
4. **Relationship**: Book hasMany BookGift, BookGift belongsTo Book
5. **Soft Delete**: Khi xóa sách, các quà tặng liên quan cũng bị ảnh hưởng

## Kết Quả Mong Muốn

- ✅ Admin có thể tạo quà tặng khi tạo/sửa sách
- ✅ Quà tặng có thể được gán cho sách hiện tại hoặc sách khác
- ✅ Form validation hoạt động đúng
- ✅ Dữ liệu được lưu chính xác vào database
- ✅ Giao diện thân thiện và dễ sử dụng
- ✅ Logic xử lý robust và không có lỗi

## Cập Nhật Gần Đây

**Ngày**: 05/08/2025
**Thay đổi**:
- Sửa logic lưu quà tặng để hỗ trợ tạo quà tặng cho sách hiện tại
- Cập nhật giao diện form với dropdown không bắt buộc
- Thêm validation cho `gift_book_id`
- Đồng bộ tên trường ngày tháng giữa controller và view
- Thêm hiển thị quà tặng hiện tại trong form edit
- Test kỹ lưỡng tất cả các trường hợp sử dụng