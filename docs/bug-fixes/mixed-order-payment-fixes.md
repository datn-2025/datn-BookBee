# Khắc phục lỗi thanh toán đơn hàng hỗn hợp (Mixed Order)

## Mô tả vấn đề

Khi người dùng mua cả ebook và sách vật lý, hệ thống tạo đơn hàng hỗn hợp (mixed order) với cấu trúc cha-con. Tuy nhiên, có một số lỗi xảy ra trong quá trình xử lý thanh toán:

1. **Lỗi VNPay Return**: Method `vnpayReturn` không xử lý mixed order, chỉ xử lý đơn hàng thông thường
2. **Lỗi null parameters**: Các tham số như `applied_voucher_code`, `discount_amount_applied`, `recipient_name`, `recipient_phone` có thể null
3. **Lỗi hủy đơn hàng**: Khi thanh toán VNPay thất bại, chỉ hủy đơn cha mà không hủy các đơn con

## Các lỗi đã được khắc phục

### 1. Cập nhật VNPay Return Handler

**File**: `app/Http/Controllers/OrderController.php`

**Vấn đề**: Method `vnpayReturn` không xử lý mixed order khi thanh toán thành công

**Giải pháp**: Thêm logic kiểm tra mixed order và xử lý riêng biệt:

```php
// Kiểm tra xem có phải mixed order không
if ($order->delivery_method === 'mixed' && $order->isParentOrder()) {
    // Xử lý mixed order - cập nhật trạng thái cho các đơn con
    $physicalOrder = $order->childOrders()->where('delivery_method', 'delivery')->first();
    $ebookOrder = $order->childOrders()->where('delivery_method', 'ebook')->first();
    
    if ($physicalOrder) {
        $physicalOrder->update(['payment_status_id' => $paymentStatus->id]);
    }
    if ($ebookOrder) {
        $ebookOrder->update(['payment_status_id' => $paymentStatus->id]);
    }
    
    // Xử lý sau thanh toán cho mixed order
    $this->mixedOrderService->handlePostOrderCreation($order, $physicalOrder, $ebookOrder, Auth::user());
} else {
    // Xử lý đơn hàng thông thường
    // ...
}
```

### 2. Xử lý null parameters trong MixedOrderService

**File**: `app/Services/MixedOrderService.php`

**Vấn đề**: Các tham số có thể null gây lỗi khi tạo đơn hàng

**Giải pháp**: Thêm null coalescing operator và fallback values:

```php
// Validate voucher với null check
$voucherData = $this->orderService->validateVoucher($request->applied_voucher_code ?? null, $totalSubtotal);
$totalDiscountAmount = $request->discount_amount_applied ?? 0;

// Recipient info với fallback
'recipient_name' => $request->new_recipient_name ?: $user->name,
'recipient_phone' => $request->new_phone ?: $user->phone,
'recipient_email' => $request->new_email ?: $user->email,
```

### 3. Xử lý hủy đơn hàng mixed order khi thanh toán thất bại

**File**: `app/Http/Controllers/OrderController.php`

**Vấn đề**: Khi VNPay thanh toán thất bại, chỉ hủy đơn cha mà không hủy các đơn con

**Giải pháp**: Thêm logic hủy tất cả đơn con khi đơn cha bị hủy:

```php
// Kiểm tra xem có phải mixed order không
if ($order->delivery_method === 'mixed' && $order->isParentOrder()) {
    // Hủy đơn hàng cha và các đơn con
    $childOrders = $order->childOrders;
    
    foreach ($childOrders as $childOrder) {
        $childOrder->update([
            'order_status_id' => $cancelledStatus->id,
            'payment_status_id' => $failedPaymentStatus->id,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode
        ]);
        
        // Tạo bản ghi hủy đơn hàng con
        OrderCancellation::create([
            'order_id' => $childOrder->id,
            'reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode,
            'cancelled_by' => $order->user_id,
            'cancelled_at' => now(),
        ]);
    }
}
```

## Luồng xử lý sau khi khắc phục

### 1. Thanh toán thành công

1. **Đơn hàng thông thường**: Xử lý như cũ (tạo GHN, gửi email, tạo QR)
2. **Mixed order**: 
   - Cập nhật trạng thái thanh toán cho các đơn con
   - Gọi `handlePostOrderCreation` để:
     - Tạo đơn GHN cho sách vật lý
     - Tạo QR code cho tất cả đơn hàng
     - Gửi email xác nhận cho đơn cha và đơn sách vật lý
     - Gửi email ebook download cho đơn ebook
     - Xóa giỏ hàng

### 2. Thanh toán thất bại

1. **Đơn hàng thông thường**: Hủy đơn hàng như cũ
2. **Mixed order**:
   - Hủy tất cả đơn con trước
   - Hủy đơn cha
   - Tạo bản ghi OrderCancellation cho tất cả đơn hàng

## Testing

### Test case 1: Thanh toán VNPay thành công cho mixed order
1. Thêm cả ebook và sách vật lý vào giỏ hàng
2. Checkout với VNPay
3. Thanh toán thành công
4. **Kết quả mong đợi**:
   - Đơn cha và các đơn con có trạng thái "Đã Thanh Toán"
   - Nhận email xác nhận đơn hàng
   - Nhận email link download ebook
   - Đơn sách vật lý có mã GHN

### Test case 2: Thanh toán VNPay thất bại cho mixed order
1. Thêm cả ebook và sách vật lý vào giỏ hàng
2. Checkout với VNPay
3. Hủy thanh toán hoặc thanh toán thất bại
4. **Kết quả mong đợi**:
   - Đơn cha và các đơn con đều có trạng thái "Đã hủy"
   - Có bản ghi OrderCancellation cho tất cả đơn hàng
   - Hiển thị thông báo lỗi phù hợp

### Test case 3: Thanh toán ví điện tử cho mixed order
1. Thêm cả ebook và sách vật lý vào giỏ hàng
2. Checkout với ví điện tử (đủ số dư)
3. **Kết quả mong đợi**:
   - Thanh toán thành công ngay lập tức
   - Xử lý tương tự như VNPay thành công

## Lưu ý kỹ thuật

1. **Transaction Safety**: Tất cả thao tác được wrap trong DB::transaction()
2. **Error Handling**: Có try-catch và rollback khi có lỗi
3. **Logging**: Log chi tiết cho việc debug
4. **Null Safety**: Kiểm tra null cho tất cả tham số có thể null
5. **Consistency**: Đảm bảo trạng thái đồng bộ giữa đơn cha và đơn con

## Các file đã được cập nhật

1. `app/Http/Controllers/OrderController.php`
   - Method `vnpayReturn`: Thêm xử lý mixed order
   - Thêm logic hủy đơn con khi thanh toán thất bại

2. `app/Services/MixedOrderService.php`
   - Method `createMixedFormatOrders`: Thêm null checks
   - Method `createParentOrder`: Thêm fallback values
   - Method `createPhysicalChildOrder`: Thêm fallback values

3. `app/Services/EmailService.php`
   - Method `sendEbookDownloadEmail`: Đã có sẵn từ trước

## Kết luận

Sau khi khắc phục các lỗi trên, hệ thống mixed order đã hoạt động ổn định với:
- Xử lý thanh toán VNPay chính xác
- Xử lý lỗi và hủy đơn hàng đúng cách
- Gửi email và tạo các tác vụ sau thanh toán đầy đủ
- Đảm bảo tính nhất quán dữ liệu

Người dùng giờ đây có thể mua cả ebook và sách vật lý trong cùng một lần thanh toán mà không gặp lỗi.