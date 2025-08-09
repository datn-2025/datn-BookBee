# Cập Nhật Logic CartController Cho Ebooks

## 📋 Tóm Tắt Thay Đổi

Đã cập nhật logic trong `CartController.php` để **ebooks không xử lý attributes/variants** và **không tính extra price từ biến thể**.

## 🔧 Các Thay Đổi Đã Thực Hiện

### 1. Loại Bỏ Yêu Cầu Attributes Cho Ebooks

**Trước đây:**
```php
if ($isEbook) {
    // Đối với ebook: chỉ lấy thuộc tính ngôn ngữ
    $validAttributeIds = DB::table('attribute_values')
        ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
        ->whereIn('attribute_values.id', $attributeValueIds)
        ->where(function ($q) {
            $q->where('attributes.name', 'LIKE', '%Ngôn Ngữ%')
                ->orWhere('attributes.name', 'LIKE', '%language%');
        })
        ->pluck('attribute_values.id')
        ->toArray();

    if (empty($validAttributeIds)) {
        return response()->json([
            'error' => 'Vui lòng chọn ngôn ngữ cho sách điện tử'
        ], 422);
    }
}
```

**Sau khi thay đổi:**
```php
if ($isEbook) {
    // Đối với ebook: không xử lý thuộc tính nào cả
    $validAttributeIds = [];
    
    Log::info('Cart addToCart - Ebook attributes ignored:', [
        'book_id' => $bookId,
        'requested_attributes' => $attributeValueIds,
        'format_id' => $bookFormatId
    ]);
}
```

### 2. Không Tính Extra Price Cho Ebooks

**Trước đây:**
```php
// Tính thêm extra_price từ biến thể nếu có
if (!empty($validAttributeIds)) {
    $attributeExtraPrice = DB::table('book_attribute_values')
        ->whereIn('attribute_value_id', $validAttributeIds)
        ->where('book_id', $bookId)
        ->sum('extra_price');

    $finalPrice += $attributeExtraPrice;
}
```

**Sau khi thay đổi:**
```php
// Tính thêm extra_price từ biến thể nếu có (chỉ cho sách vật lý)
if (!empty($validAttributeIds) && !$isEbook) {
    $attributeExtraPrice = DB::table('book_attribute_values')
        ->whereIn('attribute_value_id', $validAttributeIds)
        ->where('book_id', $bookId)
        ->sum('extra_price');

    $finalPrice += $attributeExtraPrice;
} elseif (!empty($validAttributeIds) && $isEbook) {
    Log::info('Cart addToCart - Skipped attribute extra price for ebook:', [
        'book_id' => $bookId,
        'attribute_ids' => $validAttributeIds,
        'note' => 'Ebooks do not have extra price for variants'
    ]);
}
```

### 3. Cập Nhật Logic Kiểm Tra Sản Phẩm Đã Có Trong Giỏ

**Trước đây:**
```php
$existingCart = Cart::where('user_id', Auth::id())
    ->where('book_id', $bookId)
    ->where('book_format_id', $bookFormatId)
    ->whereJsonContains('attribute_value_ids', json_decode($attributeJson, true))
    ->first();
```

**Sau khi thay đổi:**
```php
$existingCartQuery = Cart::where('user_id', Auth::id())
    ->where('book_id', $bookId)
    ->where('book_format_id', $bookFormatId);

// Chỉ kiểm tra attributes cho sách vật lý
if (!$isEbook) {
    $existingCartQuery->whereJsonContains('attribute_value_ids', json_decode($attributeJson, true));
}

$existingCart = $existingCartQuery->first();
```

### 4. Cập Nhật Logic Hiển Thị Giỏ Hàng

**Trong phương thức `index()`:**
```php
// Add extra price from attributes if any (only for physical books)
if (!empty($cartItem->attribute_value_ids) && $cartItem->attribute_value_ids !== '[]') {
    $attributeIds = json_decode($cartItem->attribute_value_ids, true);
    if ($attributeIds && is_array($attributeIds)) {
        // Check if this is an ebook
        $isEbook = $bookInfo->format_name && stripos($bookInfo->format_name, 'ebook') !== false;
        
        if (!$isEbook) {
            // Only add extra price for physical books
            $extraPrice = DB::table('book_attribute_values')
                ->whereIn('attribute_value_id', $attributeIds)
                ->where('book_id', $cartItem->book_id)
                ->sum('extra_price');
            $finalPrice += $extraPrice;
        }
    }
}
```

## 🎯 Kết Quả Đạt Được

### ✅ Đối Với Ebooks:
- **Không yêu cầu** chọn thuộc tính ngôn ngữ
- **Không tính** extra price từ biến thể
- **Đơn giản hóa** logic kiểm tra trong giỏ hàng
- **Luôn quantity = 1** cho ebooks

### ✅ Đối Với Physical Books:
- **Vẫn hoạt động đầy đủ** với tất cả logic cũ
- **Vẫn yêu cầu** chọn thuộc tính nếu có
- **Vẫn tính** extra price từ biến thể
- **Vẫn kiểm tra** tồn kho theo phân cấp

## 🔍 Logic Flow Mới

### Ebooks:
1. Frontend: Không hiển thị attributes
2. Frontend: Không gửi attribute_value_ids
3. Backend: Bỏ qua validation attributes
4. Backend: Không tính extra price
5. Backend: Quantity luôn = 1
6. Cart: Hiển thị đơn giản không có variant info

### Physical Books:
1. Frontend: Hiển thị đầy đủ attributes
2. Frontend: Gửi đầy đủ attribute_value_ids
3. Backend: Validate attributes như cũ
4. Backend: Tính extra price từ variants
5. Backend: Kiểm tra tồn kho phân cấp
6. Cart: Hiển thị đầy đủ variant info

## 📝 Lưu Ý Quan Trọng

1. **Backward Compatibility**: Logic cũ vẫn hoạt động cho physical books
2. **Data Integrity**: Không ảnh hưởng đến dữ liệu đã có trong database
3. **Performance**: Giảm thiểu queries không cần thiết cho ebooks
4. **Logging**: Thêm logs chi tiết để debug và monitor

## 🚀 Kiểm Tra Hoạt Động

### Test Cases Cần Thực Hiện:

1. **Ebook thêm vào giỏ:**
   - Không có attributes trong request
   - Quantity = 1
   - Giá = base price - discount (không có extra price)

2. **Physical book thêm vào giỏ:**
   - Có attributes trong request
   - Quantity theo user input
   - Giá = base price - discount + extra price

3. **Giỏ hàng hiển thị:**
   - Ebooks: Không hiển thị variant info
   - Physical books: Hiển thị đầy đủ variant info

4. **Cập nhật giỏ hàng:**
   - Ebooks: Quantity luôn = 1
   - Physical books: Validate stock như cũ
