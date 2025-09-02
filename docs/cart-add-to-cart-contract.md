# Add to Cart – Hợp đồng API và Quy tắc xử lý (Biến thể mới)

Tài liệu này mô tả chi tiết luồng “Thêm vào giỏ hàng” theo hệ biến thể mới (book_variants + book_variant_attribute_values) đang được áp dụng trong `app/Http/Controllers/Cart/CartController.php`.

## 1) Endpoint & Method

- POST `route('cart.add')` (xem `routes/web.php`)
- Controller: `CartController@addToCart(Request $request)`

## 2) Payload (Request)

- Trường chung:
  - `book_id` (uuid, required)
  - `quantity` (int >= 1, required)
  - `book_format_id` (uuid, optional) – nếu không gửi, BE chọn format đầu tiên

- Biến thể mới (khuyến nghị):
  - `variant_id` (uuid, optional) – id trong bảng `book_variants`.
  - BE sẽ tự suy ra mảng `attribute_value_ids` từ pivot `book_variant_attribute_values` của `variant_id`.

- Legacy (tạm thời còn hỗ trợ):
  - `attribute_value_ids` (string JSON, optional) – mảng các UUID `attribute_value_id`.
  - Hoặc `attributes[key] = valueId` – BE sẽ gom lại thành mảng ids.

- Combo (nếu có):
  - `type = 'combo'` và/hoặc `combo_id`, `collection_id` – chuyển hướng sang `addComboToCart()`.

## 3) Validation chính

- `book_id` tồn tại trong `books`.
- `book_format_id` (nếu có) tồn tại trong `book_formats`.
- `variant_id` (nếu có) tồn tại trong `book_variants` và phải thuộc `book_id` đã gửi.
- Với Ebook: bỏ qua mọi thuộc tính/biến thể, quantity sẽ bị ép = 1.

## 4) Suy luận thuộc tính từ biến thể mới

- Khi có `variant_id`:
  - BE lấy `attribute_value_ids` từ pivot `book_variant_attribute_values` theo `book_variant_id = variant_id`.
  - Ghi log hỗ trợ debug.

- Khi không có `variant_id` (legacy):
  - Ưu tiên parse `attribute_value_ids` (JSON string) nếu có.
  - Sau đó merge thêm từ `attributes[key] = valueId` nếu có.
  - Lọc giữ lại những id hợp lệ trong bảng `attribute_values`.

## 5) Phân loại Ebook vs Sách vật lý

- Dựa theo `book_formats.format_name`:
  - Ebook: `stripos(format_name, 'ebook') !== false`
  - Ebook luôn: `quantity = 1`, bỏ qua stock check theo biến thể.

## 6) Kiểm tra tồn kho (Stock) – Hierarchical

- Chỉ áp dụng cho sách vật lý.
- `availableStock` khởi tạo = `book_formats.stock`.
- Nếu có `variant_id` (hệ mới):
  - Lấy `stock` từ `book_variants` theo `id = variant_id` và `book_id` tương ứng.
  - `availableStock = min(format_stock, variant_stock)`.
- Nếu KHÔNG có `variant_id` nhưng có `attribute_value_ids` (legacy):
  - Lấy `min(stock)` từ `book_attribute_values` theo `book_id` + `attribute_value_ids`.
  - `availableStock = min(format_stock, minVariantStock)`.
- Nếu không đủ stock cho số lượng yêu cầu (hoặc tổng số lượng sau khi merge cùng item cũ trong giỏ): trả 422 với thông báo, `available_stock`, `stock_level`, `variant_sku` (nếu có).

## 7) Tính giá (Price)

- `basePrice = book_formats.price`
- `discount = book_formats.discount` (số tiền trực tiếp) → `finalPrice = max(0, basePrice - discount)`
- Sách vật lý + có biến thể:
  - Nếu có `variant_id` (mới): cộng `book_variants.extra_price` vào `finalPrice`.
  - Nếu chỉ có `attribute_value_ids` (legacy): cộng tổng `extra_price` từ `book_attribute_values` theo các ids.
- Kiểm tra `finalPrice > 0`, ngược lại trả 422.

## 8) Gộp item trong giỏ (Merge)

- Tìm item cùng `user_id`, `book_id`, `book_format_id`:
  - Nếu có `variant_id`: so sánh thêm `variant_id`.
  - Nếu không có `variant_id`: yêu cầu `variant_id` NULL và so khớp `attribute_value_ids` (legacy) đối với sách vật lý.
- Nếu đã tồn tại:
  - Ebook: luôn cố định `quantity = 1`.
  - Vật lý: cộng dồn số lượng; kiểm tra lại stock với tổng số lượng mới.
  - Cập nhật `quantity` và `price` nếu cần.
- Nếu chưa tồn tại: insert item mới.

## 9) Lưu dữ liệu vào bảng `carts`

- Các cột quan trọng:
  - `user_id`, `book_id`, `book_format_id`
  - `quantity`
  - `attribute_value_ids` (JSON) – cache tổ hợp thuộc tính cho hiển thị
  - `price` – giá tại thời điểm thêm vào giỏ (đã gồm discount + extra_price nếu có)
  - Biến thể mới:
    - Tên cột khuyến nghị: `book_variant_id` (nullable, FK -> `book_variants`)
    - Lưu ý: Code hiện tại dùng trường `variant_id` ở một số chỗ (ví dụ: select trong `index()`), cần đồng bộ về `book_variant_id` để nhất quán với migration.

## 10) Quy ước FE theo hệ mới (khuyến nghị)

- Gửi `variant_id` cho sách vật lý có biến thể.
- Không bắt buộc gửi `attribute_value_ids`; BE sẽ tự suy ra từ `variant_id`.
- Trường hợp không có biến thể: chỉ gửi `book_id`, `book_format_id`, `quantity`.
- Ebook: quantity FE gửi có thể > 1 nhưng BE sẽ ép = 1.

## 11) Trả về (Response)

- Thành công: 200 JSON, có thể trả kèm thông tin thông báo, tổng số lượng trong giỏ (tuỳ UI), hoặc redirect/back cho non-JSON.
- Lỗi: 4xx JSON với thông điệp chi tiết (không đủ tồn kho, biến thể không hợp lệ, giá không hợp lệ...).

## 12) Ghi chú & Hướng dẫn chuyển đổi

- Hệ mới ưu tiên `variant_id`. Legacy `attribute_value_ids` vẫn hoạt động tạm thời nhưng nên loại bỏ dần.
- Cần đồng bộ tên cột trong DB và code:
  - Migration mới: `carts.book_variant_id` (đã thêm FK + index).
  - Code còn dùng: `carts.variant_id` ở `CartController@index()` và phần gộp giỏ.
  - Khuyến nghị: refactor code để dùng thống nhất `book_variant_id`.
- `docs/variants/fe-db-contracts.md` là tài liệu nền về schema/cách tính giá và tồn kho.

## 13) Liên kết mã nguồn chính

- Controller: `app/Http/Controllers/Cart/CartController.php`
- Model: `app/Models/Cart.php` (có hỗ trợ combo và `getTotalPrice()`)
- Migration carts: `database/migrations/2025_05_26_163207_create_carts_table.php` (đã thêm `book_variant_id`)
