# Tự động cập nhật trạng thái đơn hàng Ebook thành "Thành công" khi thanh toán thành công

## Mô tả vấn đề
Trước đây, khi khách hàng mua đơn hàng ebook và thanh toán thành công, trạng thái đơn hàng vẫn ở "Chờ xác nhận" thay vì tự động chuyển thành "Thành công". Điều này gây khó khăn trong việc quản lý và theo dõi đơn hàng ebook.

## Giải pháp thực hiện

### 1. Tạo method helper trong OrderService

**File:** `app/Services/OrderService.php`

Thêm method `updateEbookOrderStatusOnPaymentSuccess()` để:
- Kiểm tra đơn hàng có phải là ebook không (`delivery_method === 'ebook'`)
- Kiểm tra trạng thái thanh toán đã là "Đã Thanh Toán" chưa
- Tự động cập nhật trạng thái đơn hàng thành "Thành công"
- Ghi log để theo dõi

```php
public function updateEbookOrderStatusOnPaymentSuccess(Order $order)
{
    // Kiểm tra nếu đơn hàng là ebook và đã thanh toán
    if ($order->delivery_method === 'ebook') {
        $paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();
        $successStatus = OrderStatus::where('name', 'Thành công')->first();
        
        if ($paymentStatus && $successStatus && $order->payment_status_id == $paymentStatus->id) {
            $order->update([
                'order_status_id' => $successStatus->id
            ]);
            
            Log::info('Ebook order status updated to success', [
                'order_id' => $order->id,
                'order_code' => $order->order_code
            ]);
        }
    }
}
```

### 2. Cập nhật OrderController

**File:** `app/Http/Controllers/OrderController.php`

Thêm gọi method mới trong 2 trường hợp:

#### a) Thanh toán bằng ví điện tử
```php
// Gửi email ebook nếu đơn hàng có ebook
$this->emailService->sendEbookPurchaseConfirmation($order);

// Cập nhật trạng thái đơn hàng ebook thành 'Thành công' nếu đã thanh toán
$this->orderService->updateEbookOrderStatusOnPaymentSuccess($order);
```

#### b) VNPay return (thanh toán thành công)
```php
// Gửi email ebook nếu đơn hàng có ebook
$this->emailService->sendEbookPurchaseConfirmation($order);

// Cập nhật trạng thái đơn hàng ebook thành 'Thành công' nếu đã thanh toán
$this->orderService->updateEbookOrderStatusOnPaymentSuccess($order);
```

### 3. Cập nhật MixedOrderService

**File:** `app/Services/MixedOrderService.php`

Thêm xử lý cho đơn hàng hỗn hợp (có cả sách vật lý và ebook):

#### a) Trong method `processMixedOrderPayment()`
```php
// Cập nhật trạng thái thanh toán cho các đơn con
$paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();
if ($paymentStatus) {
    $physicalOrder->update(['payment_status_id' => $paymentStatus->id]);
    $ebookOrder->update(['payment_status_id' => $paymentStatus->id]);
    
    // Cập nhật trạng thái đơn hàng ebook thành 'Thành công' ngay sau khi thanh toán
    $this->orderService->updateEbookOrderStatusOnPaymentSuccess($ebookOrder);
}
```

#### b) Trong method `handlePostOrderCreation()`
```php
// Gửi email ebook ngay lập tức
$this->emailService->sendEbookDownloadEmail($ebookOrder);

// Cập nhật trạng thái đơn hàng ebook thành 'Thành công' nếu đã thanh toán
$this->orderService->updateEbookOrderStatusOnPaymentSuccess($ebookOrder);
```

### 4. Cập nhật AdminPaymentMethodController

**File:** `app/Http/Controllers/Admin/AdminPaymentMethodController.php`

Thêm xử lý khi admin cập nhật trạng thái thanh toán thành "Đã thanh toán":

```php
// Cập nhật trạng thái đơn hàng ebook thành 'Thành công' nếu đã thanh toán
app(\App\Services\OrderService::class)->updateEbookOrderStatusOnPaymentSuccess($order);

// Tạo và gửi hóa đơn khi thanh toán thành công
```

## Các trường hợp được xử lý

1. **Đơn hàng ebook đơn lẻ:**
   - Thanh toán bằng ví điện tử → Tự động chuyển thành "Thành công"
   - Thanh toán bằng VNPay → Tự động chuyển thành "Thành công"
   - Admin cập nhật trạng thái thanh toán → Tự động chuyển thành "Thành công"

2. **Đơn hàng hỗn hợp (Mixed Order):**
   - Đơn con ebook sẽ tự động chuyển thành "Thành công" khi thanh toán thành công
   - Đơn con sách vật lý vẫn giữ trạng thái "Chờ xác nhận" để xử lý giao hàng

3. **Thanh toán COD:**
   - Không áp dụng cho ebook (ebook không hỗ trợ COD)

## Lợi ích

1. **Tự động hóa:** Giảm thiểu thao tác thủ công của admin
2. **Trải nghiệm khách hàng:** Khách hàng thấy trạng thái đơn hàng chính xác ngay sau khi thanh toán
3. **Quản lý hiệu quả:** Admin dễ dàng phân biệt đơn hàng ebook đã hoàn thành và đơn hàng vật lý cần xử lý
4. **Tính nhất quán:** Áp dụng cho tất cả các phương thức thanh toán và trường hợp

## Kiểm tra

Sau khi triển khai, cần kiểm tra:

1. Tạo đơn hàng ebook và thanh toán bằng ví điện tử
2. Tạo đơn hàng ebook và thanh toán bằng VNPay
3. Tạo đơn hàng hỗn hợp và kiểm tra trạng thái từng đơn con
4. Admin cập nhật trạng thái thanh toán từ giao diện quản trị

Trạng thái đơn hàng ebook phải tự động chuyển thành "Thành công" trong tất cả các trường hợp trên.