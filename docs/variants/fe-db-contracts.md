# FE DB Contracts – Thuộc tính & Biến thể cho Giao diện, Giỏ hàng, Thanh toán

Tài liệu này mô tả chi tiết các bảng, cột, quan hệ và quy tắc tính giá/tồn kho để FE triển khai UI biến thể, giỏ hàng và thanh toán tương thích với BE hiện tại.

## 1) Bảng & Schema liên quan

- [books]
  - id (uuid)
  - title, slug, ...

- [book_formats] – Định dạng sách (Vật lý/Ebook)
  - id (uuid)
  - book_id (uuid, fk -> books)
  - format_name (string) – ví dụ: "Sách Vật Lý", "Ebook"
  - price (decimal(12,2)) – giá niêm yết
  - discount (decimal(12,2)) – số tiền giảm trực tiếp (không phải %)
  - stock (int) – tồn kho cấp định dạng (vật lý mới áp dụng)

- [attributes], [attribute_values]
  - attributes: id (uuid), name (string)
  - attribute_values: id (uuid), attribute_id (uuid, fk -> attributes), value (string)

- [book_variants] – Biến thể tiêu chuẩn (mới)
  - id (uuid)
  - book_id (uuid, fk -> books)
  - sku (nullable, string)
  - extra_price (decimal(12,2), default 0)
  - stock (int, default 0)

- [book_variant_attribute_values] – Pivot biến thể ↔ giá trị thuộc tính (mới)
  - id (uuid, primary) – BẮT BUỘC phải set thủ công khi insert
  - book_variant_id (uuid, fk -> book_variants)
  - attribute_value_id (uuid, fk -> attribute_values)
  - unique(book_variant_id, attribute_value_id)

- [book_attribute_values] – Bảng DI SẢN (legacy) cho biến thể cũ 1-1
  - id (uuid)
  - book_id (uuid)
  - attribute_value_id (uuid)
  - extra_price (decimal)
  - stock (int)
  - sku (nullable)
  - Ghi chú: Dùng trong giỏ hàng hiện tại để tính extra_price và min stock (sẽ hướng tới thay bằng [book_variants]).

- [carts] – Giỏ hàng
  - id (uuid)
  - user_id (uuid)
  - book_id (uuid, nullable khi là combo)
  - book_format_id (uuid, nullable khi là combo)
  - collection_id (uuid, nullable – dùng cho combo)
  - is_combo (bool, default false)
  - quantity (int, default 1)
  - is_selected (bool, default 1) – chọn/không chọn để thanh toán
  - attribute_value_ids (json, nullable) – MẢNG các attribute_value_id được chọn (FE gửi vào)
  - price (decimal(12,2)) – giá item tại thời điểm thêm vào giỏ (đã gồm discount + extra_price nếu có)

- [order_items] – Dòng đơn hàng
  - id (uuid)
  - order_id (uuid)
  - book_id (uuid, nullable – khi combo)
  - book_format_id (uuid, nullable)
  - collection_id (uuid, nullable – khi combo)
  - is_combo (bool)
  - item_type (string, default 'book')
  - quantity (int)
  - price (decimal(12,2)) – giá đơn vị
  - total (decimal(12,2)) – price * quantity (có thể đã trừ/áp dụng gì phía BE trước khi tạo)

- [order_item_attribute_values] – Pivot order_item ↔ attribute_value
  - order_item_id (uuid)
  - attribute_value_id (uuid)

## 2) Quan hệ & Cách hiển thị tên biến thể

- Biến thể = tổ hợp nhiều `attribute_value_id`. Hiển thị label dạng: `AttributeName: ValueName | AttributeName: ValueName`.
- Khi edit, BE trả về `$book->variants` kèm `attributeValues.attribute`. FE dùng dữ liệu này render bảng biến thể + hidden inputs `variants[i][attribute_value_ids][]`.

## 3) UI (Create/Edit) – FE → BE

- Sử dụng chuẩn mới `variants[]`:
  - `variants[i][attribute_value_ids][]`: mảng các UUID `attribute_value_id` (>= 1)
  - `variants[i][sku]`: string (optional)
  - `variants[i][extra_price]`: number >= 0 (optional, default 0)
  - `variants[i][stock]`: number >= 0 (optional, default 0)
- BE logic `update()`:
  - Chặn trùng tổ hợp (sort + join key), chặn tổ hợp rỗng → trả 422 với key `variants`.
  - Upsert biến thể; sync pivot bằng attach/detach THỦ CÔNG với UUID `id` cho pivot.
  - Xóa biến thể không còn trong payload.
  - Cập nhật `stock` của định dạng "Sách Vật Lý" = tổng `stock` biến thể.

## 4) Giỏ hàng – Hợp đồng dữ liệu & Quy tắc tính

- Endpoint thêm giỏ: `CartController@addToCart()` nhận:
  - `book_id` (required, uuid)
  - `quantity` (required, int>=1) – EBOOK sẽ bị ép = 1 ở BE
  - `book_format_id` (nullable, uuid) – nếu không gửi, BE chọn format đầu tiên
  - `attribute_value_ids` (nullable, string JSON) HOẶC `attributes[]` (array các value id)

- BE xử lý `attribute_value_ids`:
  - Nếu là Ebook: BỎ QUA mọi thuộc tính (coi như không có variants)
  - Nếu là sách vật lý: lọc giữ lại các `attribute_value_id` hợp lệ có trong DB
  - Lưu xuống `carts.attribute_value_ids` dạng JSON (mảng id, unique)

- Tính giá `price` lưu trong cart item:
  - `price = book_format.price - book_format.discount` (không âm)
  - Nếu sách vật lý và có biến thể: CỘNG tổng `extra_price` từ bảng LEGACY `book_attribute_values`
    - Truy vấn theo `book_id` + `attribute_value_ids`

- Quy tắc tồn kho (hierarchical stock):
  - Start: `availableStock = book_format.stock`
  - Nếu có `attribute_value_ids` (và không phải Ebook): Lấy `min(stock)` từ LEGACY `book_attribute_values` theo các id này cho `book_id` hiện tại
  - `availableStock = min(availableStock, minVariantStock)` nếu tồn tại min
  - Khi thêm vào giỏ hoặc tăng số lượng, BE validate số lượng ≤ availableStock

- Lưu ý chuyển đổi:
  - Giỏ hàng hiện đang dựa trên bảng LEGACY `book_attribute_values` để tính `extra_price` và `min stock`.
  - Hệ thống biến thể mới đã lưu vào `book_variants` + `book_variant_attribute_values`; để giỏ hàng dùng dữ liệu mới, BE sẽ cần refactor CartController (không yêu cầu FE thay đổi ngay). FE TIẾP TỤC gửi `attribute_value_ids` như hiện tại.

## 5) Checkout / Đơn hàng – Mapping FE → DB

- Với mỗi cart item được chọn (`is_selected = 1`):
  - Tạo `order_items` với: `book_id`, `book_format_id`, `quantity`, `price`, `total`.
  - Nếu có `attribute_value_ids`: lưu pivot `order_item_attribute_values`.
  - Với Combo: `is_combo = 1`, `collection_id` set, `book_id` null.

- Voucher: BE tự kiểm tra/áp dụng ở `OrderController@store` (không tin tưởng client); FE chỉ gửi `applied_voucher_code` nếu có.

## 6) Contract FE cụ thể

- Gửi `attribute_value_ids` ở FE: JSON string của mảng UUID
  - Ví dụ: `["112e80dd-4579-44d9-bc31-e3bb57734e8c","1f492733-df42-40bf-abe4-0e90c2ae9909"]`
  - Hoặc gửi `attributes[key]=valueId` → BE chuyển đổi sang mảng ids.
- Khi render giỏ hàng:
  - Dữ liệu mỗi item từ `CartController@index()` đã gồm:
    - `title`, `image`, `format_name`, `author_name`, `price` (đã bao discount+extra_price), `stock` (đã tính phân cấp), `gifts` (chỉ vật lý), `is_selected`
  - FE chỉ cần hiển thị và cho phép tăng/giảm `quantity` trong giới hạn `stock`.

## 7) Ví dụ payload FE

- Thêm vào giỏ (sách vật lý):
```json
{
  "book_id": "b3b0d1e8-...",
  "book_format_id": "f1a2c3d4-...",
  "quantity": 2,
  "attribute_value_ids": "[\"112e80dd-4579-44d9-bc31-e3bb57734e8c\",\"1f492733-df42-40bf-abe4-0e90c2ae9909\"]"
}
```

- Thêm vào giỏ (ebook, bỏ qua biến thể):
```json
{
  "book_id": "b3b0d1e8-...",
  "book_format_id": "f1a2c3d4-...",
  "quantity": 1
}
```

## 8) Gợi ý UI/FE

- Trang chi tiết sách (physical): cho phép chọn attributes → sinh `attribute_value_ids` → gọi add-to-cart.
- Trang giỏ hàng: hiển thị `price` đã tính, `stock` khả dụng; tăng/giảm `quantity` theo stock.
- Trang checkout: hiển thị lại item, giá, voucher code (optional), tổng tiền.

## 9) Ghi chú triển khai

- Khi BE chuyển giỏ hàng sang dùng bảng biến thể mới, FE KHÔNG cần đổi payload, vẫn gửi `attribute_value_ids`; BE sẽ map sang `book_variants` theo tổ hợp.
- Pivot mới `book_variant_attribute_values` bắt buộc `id` UUID khi insert (BE đã xử lý attach/detach thủ công).
