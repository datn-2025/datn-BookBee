# Tài liệu FE – Thuộc tính & Biến thể Sách

Tài liệu này mô tả chi tiết cách FE xây dựng UI để quản lý (tạo/sửa) thuộc tính và biến thể sách, tương thích với BE hiện có trong `app/Http/Controllers/Admin/AdminBookController.php`.

## 1. Tổng quan

- "Thuộc tính" (Attribute) là đặc điểm của sách (ví dụ: Khổ, Bìa, Màu...).
- "Giá trị thuộc tính" (Attribute Value) là các giá trị cụ thể (A5, Cứng, Đen...).
- "Biến thể" (Variant) là một tổ hợp của nhiều giá trị thuộc tính, đi kèm SKU, giá thêm, và tồn kho.
- Khi sử dụng biến thể, tổng tồn kho sách vật lý được tính bằng tổng tồn kho của tất cả biến thể.

## 2. Mô hình dữ liệu liên quan (BE)

- Bảng `book_variants`: chứa các biến thể của sách
  - Trường chính: `id (uuid)`, `book_id (uuid)`, `sku (nullable)`, `extra_price (decimal)`, `stock (int)`
- Bảng `book_variant_attribute_values`: pivot giữa `book_variants` và `attribute_values`
  - Trường: `id (uuid, primary)`, `book_variant_id (uuid)`, `attribute_value_id (uuid)`, timestamps
  - LƯU Ý: Pivot này bắt buộc có `id` UUID → khi BE `attach` sẽ luôn đính kèm `['id' => (string) Str::uuid()]`

Các quan hệ được BE sử dụng trong `AdminBookController@edit()`:
- `variants.attributeValues.attribute` để preload biến thể và các giá trị thuộc tính tương ứng.

## 3. Dòng dữ liệu FE ⇄ BE

- FE gửi form bao gồm các `variants` (nếu dùng biến thể). Mỗi biến thể:
  - `variants[i][attribute_value_ids][]`: mảng `attribute_value_id` (UUID) tạo nên tổ hợp (ít nhất 1 phần tử)
  - `variants[i][sku]` (tùy chọn)
  - `variants[i][extra_price]` (>= 0, mặc định 0)
  - `variants[i][stock]` (>= 0, mặc định 0)
- Nếu không gửi `variants`, BE sẽ rơi về luồng cũ (existing/new attribute values). Khuyến nghị dùng `variants` cho chuẩn mới.

## 4. UI đề xuất (Create/Edit)

- Khu chọn giá trị thuộc tính theo nhóm (mỗi thuộc tính là 1 group):
  - Select các `attribute.values`, nút "Thêm" để tạo các "chip" giá trị đã chọn.
  - Container hiển thị các giá trị đã chọn: `.selected-variants-container`.
- Nút "Tạo tổ hợp biến thể":
  - Lấy tất cả giá trị đã chọn của từng thuộc tính → sinh tổ hợp Descartes → render bảng.
- Bảng biến thể:
  - Cột nhãn: `AttributeName: ValueName | ...`
  - Inputs:
    - `variants[i][sku]` (text)
    - `variants[i][extra_price]` (number, min=0)
    - `variants[i][stock]` (number, min=0)
  - Hidden inputs `variants[i][attribute_value_ids][]` cho từng `valueId`.
  - Nút xóa dòng.
- Tổng tồn kho biến thể:
  - Ô readonly hiển thị tổng: `#total_variant_stock_display` (gợi ý)
  - BE sẽ tự tính lại và cập nhật về `Sách Vật Lý`.

Tham chiếu view mẫu: `resources/views/admin/books/edit.blade.php`
- `#variants_section`, `#variants_tbody`, JS `updateTotalVariantStock()`

## 5. Validation phía FE

- __Không trùng tổ hợp__: Chuẩn hóa mỗi tổ hợp trước khi submit bằng cách sort `attribute_value_ids` rồi join thành key. Dùng Set để phát hiện trùng.
- __Tổ hợp rỗng__: Mỗi biến thể phải có ít nhất 1 `attribute_value_id`.
- __Giá trị số__: `extra_price >= 0`, `stock >= 0`.
- __SKU__: tùy chọn; có thể auto-suggest từ các value name (slugified, viết hoa).

BE sẽ kiểm tra lại và trả lỗi 422 nếu:
- Trùng tổ hợp: "Phát hiện trùng lặp tổ hợp biến thể. Vui lòng kiểm tra lại."
- Tổ hợp rỗng: "Mỗi biến thể phải có ít nhất 1 giá trị thuộc tính."

## 6. Luồng Edit (Preload)

- BE trả về `$book->variants` kèm `attributeValues.attribute`.
- FE render sẵn các dòng trong bảng biến thể:
  - Nhãn được build từ `attributeValues` (ví dụ: `Khổ: A5 | Bìa: Cứng`).
  - Hidden inputs `variants[idx][attribute_value_ids][]` cho mỗi `av->id`.
  - Inputs `sku`, `extra_price`, `stock` lấy từ `variant`.
- JS nên gọi `updateTotalVariantStock()` khi trang load để hiển thị tổng tồn.

Ví dụ Blade (rút gọn):
```blade
@foreach ($book->variants as $idx => $variant)
  @php
    $labelParts = [];
    foreach ($variant->attributeValues as $av) {
      $labelParts[] = ($av->attribute->name ?? 'Thuộc tính') . ': ' . ($av->value ?? '');
    }
    $label = implode(' | ', $labelParts);
  @endphp
  <tr>
    <td>
      <div class="fw-medium">{{ $label }}</div>
      @foreach($variant->attributeValues as $av)
        <input type="hidden" name="variants[{{ $idx }}][attribute_value_ids][]" value="{{ $av->id }}">
      @endforeach
    </td>
    <td><input type="text" class="form-control form-control-sm" name="variants[{{ $idx }}][sku]" value="{{ $variant->sku }}"></td>
    <td><input type="number" class="form-control form-control-sm" name="variants[{{ $idx }}][extra_price]" min="0" value="{{ $variant->extra_price }}"></td>
    <td><input type="number" class="form-control form-control-sm" name="variants[{{ $idx }}][stock]" min="0" value="{{ $variant->stock }}"></td>
    <td class="text-center">
      <button type="button" class="btn btn-sm btn-outline-danger remove-variant-row"><i class="ri-delete-bin-line"></i></button>
    </td>
  </tr>
@endforeach
```

## 7. Gợi ý JS

Sinh tổ hợp Descartes, render, và tính tổng tồn kho (jQuery):
```js
function cartesian(arr) {
  return arr.reduce((a, b) => a.flatMap(d => b.map(e => [].concat(d, e))));
}
function slugifyForSku(str) {
  return (str || '')
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-zA-Z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 20);
}
function updateTotalVariantStock() {
  const $stocks = $('input[name^="variants"][name$="[stock]"]');
  let total = 0;
  $stocks.each(function() {
    const v = parseInt($(this).val(), 10);
    if (!isNaN(v)) total += v;
  });
  $('#total_variant_stock_display').val(total);
}
$(document).on('input', 'input[name^="variants"][name$="[stock]"]', updateTotalVariantStock);
$(document).on('click', '.remove-variant-row', function(){
  $(this).closest('tr').remove();
  updateTotalVariantStock();
});
$(document).ready(updateTotalVariantStock);
```

## 8. API/Contract BE quan trọng

- File: `app/Http/Controllers/Admin/AdminBookController.php`
- Phương thức `update()` (đối với edit):
  - Nếu có `variants`:
    - Chuẩn hóa tổ hợp (sort + join) để ngăn trùng.
    - Upsert biến thể theo tổ hợp.
    - Đồng bộ pivot: tự tính `toAttach`/`toDetach` và `attach` với payload có `id` UUID.
    - Xóa biến thể không còn trong payload.
    - Cập nhật `stock` cho định dạng `Sách Vật Lý` = tổng stock biến thể.
  - Nếu không có `variants`: giữ luồng thuộc tính cũ.

## 9. Edge cases

- Xóa toàn bộ biến thể: cần gửi `variants` là mảng rỗng (đảm bảo key tồn tại). Nếu không, controller sẽ đi vào luồng cũ và không xóa biến thể.
- Sách chỉ có Ebook: BE không cập nhật tồn kho vật lý.
- SKU trống: chấp nhận; có thể sinh tự động phía FE.

## 10. Checklist triển khai FE

- [ ] Chọn nhiều giá trị cho mỗi thuộc tính và hiển thị chip.
- [ ] Nút tạo tổ hợp sinh đầy đủ tất cả tổ hợp có thể.
- [ ] Bảng biến thể có đầy đủ hidden `attribute_value_ids[]` + inputs `sku`, `extra_price`, `stock`.
- [ ] Cập nhật tổng tồn kho khi thay đổi stock/xóa dòng.
- [ ] Kiểm tra trùng tổ hợp trước khi submit.
- [ ] Kiểm tra giá trị số >= 0.
- [ ] Preload dữ liệu biến thể khi vào trang edit.

## 11. Ví dụ payload (JSON tương đương)

```json
{
  "title": "Lập trình Laravel Pro",
  "isbn": "978-604-...-...",
  "page_count": 320,
  "has_physical": true,
  "formats": {
    "physical": { "price": 120000, "discount": 10000 }
  },
  "has_ebook": false,
  "variants": [
    {
      "attribute_value_ids": [
        "112e80dd-4579-44d9-bc31-e3bb57734e8c",
        "1f492733-df42-40bf-abe4-0e90c2ae9909"
      ],
      "sku": "A5-CUNG-DEN",
      "extra_price": 5000,
      "stock": 10
    },
    {
      "attribute_value_ids": [
        "112e80dd-4579-44d9-bc31-e3bb57734e8c",
        "7559ef77-73b5-4522-900c-52d9dc8b7a9a"
      ],
      "sku": "A5-CUNG-TRANG",
      "extra_price": 10000,
      "stock": 5
    }
  ]
}
```

---

Nếu cần, mình có thể tách phần bảng biến thể thành một Blade partial hoặc viết JS module riêng để tái sử dụng giữa `create.blade.php` và `edit.blade.php`.
