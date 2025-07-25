# Cải thiện Logic Hiển thị Trạng thái Sách

## Mô tả vấn đề

**Trước khi sửa:**
- Trạng thái sách chỉ dựa vào trường `status` trong database
- Không phản ánh thực tế số lượng tồn kho
- Không có cảnh báo khi sách sắp hết hàng

**Sau khi sửa:**
- Trạng thái hiển thị dựa trên số lượng tồn kho thực tế
- Tự động hiển thị "Hết Hàng" khi stock = 0
- Tự động hiển thị "Sắp Hết Hàng" khi stock < 10
- Giữ nguyên các trạng thái khác từ database

## Logic mới

### 1. Ưu tiên kiểm tra tồn kho

```php
// Kiểm tra số lượng tồn kho của sách vật lý
$physicalFormat = $book->formats->where('format_name', 'Sách Vật Lý')->first();
$stock = $physicalFormat ? $physicalFormat->stock : null;
```

### 2. Xác định trạng thái theo thứ tự ưu tiên

**Ưu tiên 1: Hết hàng (stock = 0)**
```php
if ($stock !== null && $stock == 0) {
    $statusText = 'Hết Hàng';
    $statusClass = 'badge bg-danger';
}
```

**Ưu tiên 2: Sắp hết hàng (0 < stock < 10)**
```php
elseif ($stock !== null && $stock > 0 && $stock < 10) {
    $statusText = 'Sắp Hết Hàng';
    $statusClass = 'badge bg-warning';
}
```

**Ưu tiên 3: Giữ nguyên trạng thái gốc**
```php
else {
    // Sử dụng trạng thái từ database
    switch($book->status) {
        case 'Còn Hàng': // bg-success
        case 'Hết Hàng Tồn Kho': // bg-dark  
        case 'Ngừng Kinh Doanh': // bg-secondary
        case 'Sắp Ra Mắt': // bg-info
        default: // bg-primary
    }
}
```

## Các trạng thái và màu sắc

| Trạng thái | Điều kiện | Màu badge | Class CSS |
|------------|-----------|-----------|----------|
| **Hết Hàng** | stock = 0 | Đỏ | `bg-danger` |
| **Sắp Hết Hàng** | 0 < stock < 10 | Vàng | `bg-warning` |
| **Còn Hàng** | stock ≥ 10 & status = 'Còn Hàng' | Xanh lá | `bg-success` |
| **Hết Hàng Tồn Kho** | Trạng thái gốc | Đen | `bg-dark` |
| **Ngừng Kinh Doanh** | Trạng thái gốc | Xám | `bg-secondary` |
| **Sắp Ra Mắt** | Trạng thái gốc | Xanh dương | `bg-info` |
| **Khác** | Trạng thái khác | Xanh | `bg-primary` |

## Files đã thay đổi

### 1. `resources/views/admin/books/index.blade.php`

**Dòng 146-182:** Thay đổi logic xác định trạng thái sách

- Thêm kiểm tra số lượng tồn kho của sách vật lý
- Ưu tiên hiển thị trạng thái dựa trên tồn kho
- Giữ nguyên trạng thái gốc cho các trường hợp khác
- Cải thiện màu sắc badge cho các trạng thái

## Lợi ích

### 1. **Quản lý tồn kho tốt hơn**
- Admin có thể nhanh chóng nhận biết sách hết hàng
- Cảnh báo sớm khi sách sắp hết hàng
- Giúp kịp thời nhập thêm hàng

### 2. **Trải nghiệm người dùng**
- Thông tin trạng thái chính xác và trực quan
- Màu sắc phân biệt rõ ràng các trạng thái
- Dễ dàng quét nhanh danh sách sách

### 3. **Tính linh hoạt**
- Vẫn giữ nguyên các trạng thái đặc biệt (Sắp Ra Mắt, Ngừng Kinh Doanh)
- Có thể dễ dàng điều chỉnh ngưỡng cảnh báo (hiện tại < 10)
- Tương thích với cả sách vật lý và ebook

## Cách test

1. **Test sách hết hàng:**
   - Tạo sách có stock = 0
   - Kiểm tra hiển thị "Hết Hàng" với badge đỏ

2. **Test sách sắp hết hàng:**
   - Tạo sách có stock từ 1-9
   - Kiểm tra hiển thị "Sắp Hết Hàng" với badge vàng

3. **Test sách còn hàng:**
   - Tạo sách có stock ≥ 10
   - Kiểm tra hiển thị trạng thái gốc từ database

4. **Test sách chỉ có ebook:**
   - Tạo sách chỉ có định dạng ebook
   - Kiểm tra hiển thị trạng thái gốc (không bị ảnh hưởng)

## Lưu ý

- Logic chỉ áp dụng cho sách có định dạng vật lý
- Sách chỉ có ebook sẽ hiển thị trạng thái gốc
- Có thể điều chỉnh ngưỡng cảnh báo bằng cách thay đổi `< 10`
- Màu sắc badge tuân theo Bootstrap 5 color scheme

---

**Ngày tạo:** $(date)
**Tác giả:** AI Assistant  
**Phiên bản:** 1.0