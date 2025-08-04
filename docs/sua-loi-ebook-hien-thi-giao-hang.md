# Sửa Lỗi Ebook Hiển Thị Giao Hàng

## Mô Tả Lỗi
Khi mua sách ebook, hệ thống hiển thị tùy chọn "nhận hàng trực tiếp tại cửa hàng" thay vì hiển thị thông tin ebook và gửi link tải về email.

## Nguyên Nhân
**[LỖI]**: Sai tham số trong method `prepareOrderData` của OrderService
**[FILE]**: `app/Services/OrderService.php` - dòng 783
**[NGUYÊN NHÂN]**: 
- Dòng 783: `'delivery_method' => $request->shipping_method,` thay vì `$request->delivery_method`
- Dòng 819: Thiếu tham số `$shipping_method` khi gọi method `prepareOrderData`

## Giải Pháp Đã Áp Dụng

### 1. Sửa Logic Xác Định Delivery Method
**File**: `app/Services/OrderService.php`
**Dòng**: 783

```php
// Trước khi sửa
'delivery_method' => $request->shipping_method,

// Sau khi sửa
'delivery_method' => $request->delivery_method,
```

### 2. Sửa Tham Số Method Call
**File**: `app/Services/OrderService.php`
**Dòng**: 819-826

```php
// Trước khi sửa
$orderData = $this->prepareOrderData(
    $request, 
    $user, 
    $addressId, 
    $voucherData['voucher_id'], 
    $subtotal, 
    $actualDiscountAmount
);

// Sau khi sửa
$orderData = $this->prepareOrderData(
    $request, 
    $user, 
    $addressId, 
    $voucherData['voucher_id'], 
    $subtotal, 
    $request->shipping_method,
    $actualDiscountAmount
);
```

## Kết Quả Mong Muốn
Sau khi sửa lỗi:
- Đơn hàng ebook sẽ có `delivery_method = 'ebook'` thay vì `'delivery'` hoặc `'pickup'`
- Giao diện sẽ hiển thị đúng thông tin ebook
- Email chứa link tải ebook sẽ được gửi đến khách hàng
- Không hiển thị thông tin giao hàng vật lý cho ebook

## Cách Tránh Lỗi Tương Lai
1. **Kiểm tra tên tham số**: Đảm bảo sử dụng đúng tên tham số (`delivery_method` vs `shipping_method`)
2. **Validate method signature**: Kiểm tra số lượng và thứ tự tham số khi gọi method
3. **Test case**: Tạo test case riêng cho từng loại đơn hàng (ebook, vật lý, hỗn hợp)
4. **Code review**: Review kỹ các thay đổi liên quan đến logic đặt hàng

## Các File Liên Quan
- `app/Services/OrderService.php` - Logic xử lý đơn hàng
- `app/Http/Controllers/OrderController.php` - Controller xử lý đặt hàng
- `resources/views/clients/account/order-details.blade.php` - Giao diện chi tiết đơn hàng
- `resources/views/orders/checkout.blade.php` - Giao diện checkout

## Ghi Chú
Lỗi này ảnh hưởng đến trải nghiệm người dùng khi mua ebook, khiến họ nhầm lẫn về cách nhận sản phẩm. Việc sửa lỗi này đảm bảo logic đặt hàng hoạt động chính xác cho tất cả các loại sản phẩm.