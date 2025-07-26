# Sửa lỗi gửi hóa đơn qua email khi thanh toán bằng ví điện tử

## Mô tả vấn đề

Khi khách hàng thanh toán bằng ví điện tử, hệ thống đã tạo được hóa đơn nhưng không gửi hóa đơn qua email cho khách hàng.

## Nguyên nhân

Trong file `OrderController.php`, khi xử lý thanh toán ví điện tử, hệ thống chỉ gọi:
```php
$this->invoiceService->createInvoiceForOrder($order);
```

Thay vì gọi:
```php
$this->invoiceService->processInvoiceForPaidOrder($order);
```

Phương thức `processInvoiceForPaidOrder()` sẽ vừa tạo hóa đơn vừa gửi email, trong khi `createInvoiceForOrder()` chỉ tạo hóa đơn.

## Giải pháp đã áp dụng

### File: `app/Http/Controllers/OrderController.php`

**Trước khi sửa (dòng 220-221):**
```php
// Tạo hóa đơn ngay lập tức cho thanh toán ví
$this->invoiceService->createInvoiceForOrder($order);
```

**Sau khi sửa:**
```php
// Tạo và gửi hóa đơn ngay lập tức cho thanh toán ví
try {
    $this->invoiceService->processInvoiceForPaidOrder($order);
    Log::info('Invoice created and sent for wallet payment', ['order_id' => $order->id]);
} catch (\Exception $e) {
    Log::error('Failed to create invoice for wallet payment', [
        'order_id' => $order->id,
        'error' => $e->getMessage()
    ]);
}
```

## Luồng xử lý hóa đơn sau khi sửa

### 1. Thanh toán ví điện tử
- Tạo đơn hàng
- Xử lý thanh toán từ ví
- Gửi email xác nhận đơn hàng
- **Tạo và gửi hóa đơn qua email** ✅

### 2. Thanh toán VNPay
- Tạo đơn hàng
- Chuyển hướng đến VNPay
- Sau khi thanh toán thành công: tạo và gửi hóa đơn ✅

### 3. Thanh toán COD
- Tạo đơn hàng
- Gửi email xác nhận
- Hóa đơn sẽ được tạo và gửi khi admin xác nhận thanh toán ✅

## Phương thức liên quan

### InvoiceService::processInvoiceForPaidOrder()
```php
public function processInvoiceForPaidOrder(Order $order)
{
    try {
        // Tạo hóa đơn
        $invoice = $this->createInvoiceForOrder($order);
        
        // Gửi email hóa đơn
        $this->sendInvoiceEmail($order);

        Log::info('Invoice processing completed for paid order', [
            'order_id' => $order->id,
            'invoice_id' => $invoice->id
        ]);

        return $invoice;
    } catch (\Exception $e) {
        Log::error('Failed to process invoice for paid order', [
            'order_id' => $order->id,
            'error' => $e->getMessage()
        ]);
        
        throw $e;
    }
}
```

### EmailService::sendOrderInvoice()
```php
public function sendOrderInvoice(Order $order)
{
    // Load relationships cho cả sách lẻ và combo
    $order->load([
        'user', 
        'orderItems.book', 
        'orderItems.bookFormat',
        'orderItems.collection',
        'address', 
        'payments.paymentMethod',
        'orderStatus',
        'paymentMethod'
    ]);
    
    // Lấy thông tin cài đặt cửa hàng
    $storeSettings = \App\Models\Setting::first();

    Mail::to($order->user->email)
        ->send(new OrderInvoice($order, $storeSettings));
}
```

## Kiểm tra sau khi sửa

1. **Tạo đơn hàng mới với thanh toán ví điện tử**
2. **Kiểm tra email của khách hàng** - phải nhận được 2 email:
   - Email xác nhận đơn hàng
   - Email hóa đơn
3. **Kiểm tra log** - phải có log "Invoice created and sent for wallet payment"

## Lưu ý

- Đảm bảo cấu hình email đã được thiết lập đúng
- Kiểm tra queue nếu sử dụng email queue
- Theo dõi log để phát hiện lỗi gửi email

## Tác động

✅ **Đã sửa:** Khách hàng thanh toán ví điện tử giờ đây sẽ nhận được hóa đơn qua email
✅ **Không ảnh hưởng:** Các phương thức thanh toán khác vẫn hoạt động bình thường
✅ **Cải thiện:** Trải nghiệm khách hàng tốt hơn với thông tin đầy đủ