# Sửa lỗi không tạo và gửi hóa đơn sau thanh toán

## Mô tả lỗi
Sau khi khách hàng thanh toán thành công, hệ thống không tự động tạo và gửi hóa đơn về email khách hàng.

## Nguyên nhân

### 1. Thanh toán COD
- Hệ thống cố gắng tạo hóa đơn ngay khi tạo đơn hàng COD (khi trạng thái thanh toán vẫn là "Chờ Xử Lý")
- Logic tạo hóa đơn yêu cầu trạng thái thanh toán phải là "Đã Thanh Toán"
- Admin cập nhật trạng thái thanh toán thành "Đã Thanh Toán" nhưng không có logic tạo hóa đơn

### 2. Thanh toán VNPay
- Logic tạo hóa đơn đã có trong `vnpayReturn` method nhưng có thể gặp lỗi trong quá trình thực thi

### 3. Vấn đề với Combo Items trong InvoiceService
- InvoiceService không xử lý đúng combo items (có `collection_id` nhưng `book_id` = null)
- Bảng `invoice_items` không có cột `collection_id` và `book_id` không cho phép null
- Gây lỗi "Column 'book_id' cannot be null" khi tạo hóa đơn cho đơn hàng có combo

## Giải pháp đã áp dụng

### 1. Sửa logic thanh toán COD

#### File: `app/Http/Controllers/OrderController.php`
- **Loại bỏ**: Việc tạo hóa đơn ngay khi tạo đơn hàng COD
- **Lý do**: COD chỉ nên tạo hóa đơn khi admin xác nhận thanh toán

```php
// Đã loại bỏ code này:
// try {
//     $this->invoiceService->processInvoiceForPaidOrder($order);
//     Log::info('Invoice created and sent for COD order', ['order_id' => $order->id]);
// } catch (\Exception $e) {
//     Log::error('Failed to create invoice for COD order', [
//         'order_id' => $order->id,
//         'error' => $e->getMessage()
//     ]);
// }

// Thay thế bằng:
Log::info('COD order created successfully - Invoice will be created when payment is confirmed by admin', ['order_id' => $order->id]);
```

#### File: `app/Http/Controllers/Admin/AdminPaymentMethodController.php`
- **Thêm**: Logic tạo hóa đơn khi admin cập nhật trạng thái thanh toán thành "Đã Thanh Toán"
- **Import thêm**: `InvoiceService`, `Log`

```php
if (mb_strtolower($status->name, 'UTF-8') === 'đã thanh toán') {
    $payment->paid_at = now();

    // Gửi mail nếu có Ebook
    $order = $payment->order;
    if ($order) {
        $hasEbook = $order->orderItems()
            ->whereHas('book.formats', function ($query) {
                $query->where('format_name', 'Ebook');
            })
            ->exists();

        if ($hasEbook) {
            Mail::to($order->user->email)->send(new EbookPurchaseConfirmation($order));
        }
        
        // Tạo và gửi hóa đơn khi thanh toán thành công
        try {
            app(\App\Services\InvoiceService::class)->processInvoiceForPaidOrder($order);
            Log::info('Invoice created and sent for admin-confirmed payment', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Failed to create invoice for admin-confirmed payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
```

### 2. Xác nhận logic VNPay

#### File: `app/Http/Controllers/OrderController.php`
- **Xác nhận**: Logic tạo hóa đơn cho VNPay đã có trong method `vnpayReturn`
- **Có sẵn**: Error handling và logging cho việc tạo hóa đơn

```php
// Tạo và gửi hóa đơn cho thanh toán VNPay thành công
try {
    $this->invoiceService->processInvoiceForPaidOrder($order);
    Log::info('Invoice created and sent for VNPay order', ['order_id' => $order->id]);
} catch (\Exception $e) {
    Log::error('Failed to create invoice for VNPay order', [
        'order_id' => $order->id,
        'error' => $e->getMessage()
    ]);
}
```

### 3. Sửa vấn đề Combo Items trong InvoiceService

#### File: `database/migrations/2025_07_25_134634_add_collection_support_to_invoice_items_table.php`
- **Thêm**: Cột `collection_id` nullable để hỗ trợ combo items
- **Sửa**: Cho phép `book_id` nullable
- **Xóa**: Unique constraint cũ `['invoice_id', 'book_id']`

#### File: `app/Models/InvoiceItem.php`
- **Thêm**: `collection_id` vào fillable
- **Thêm**: Relationship `collection()`
- **Thêm**: Method `getItemName()` để lấy tên sản phẩm (sách hoặc combo)

#### File: `app/Services/InvoiceService.php`
- **Sửa**: Logic tạo invoice items để xử lý cả combo và sách lẻ
- **Thêm**: Kiểm tra `is_combo` và `collection_id` khi tạo invoice items
- **Áp dụng**: Cho cả method `createInvoiceForOrder` và `createRefundInvoice`

```php
// Logic mới trong InvoiceService
if ($orderItem->is_combo && $orderItem->collection_id) {
    // Đối với combo, tạo invoice item với collection info
    InvoiceItem::create([
        'id' => (string) Str::uuid(),
        'invoice_id' => $invoice->id,
        'book_id' => null, // Combo không có book_id
        'collection_id' => $orderItem->collection_id,
        'quantity' => $orderItem->quantity,
        'price' => $orderItem->price
    ]);
} else {
    // Đối với sách lẻ
    InvoiceItem::create([
        'id' => (string) Str::uuid(),
        'invoice_id' => $invoice->id,
        'book_id' => $orderItem->book_id,
        'quantity' => $orderItem->quantity,
        'price' => $orderItem->price
    ]);
}
```

## Luồng hoạt động sau khi sửa

### Thanh toán COD
1. Khách hàng đặt hàng với phương thức COD
2. Đơn hàng được tạo với trạng thái thanh toán "Chờ Xử Lý"
3. Email xác nhận đơn hàng được gửi (không có hóa đơn)
4. Admin xác nhận thanh toán và cập nhật trạng thái thành "Đã Thanh Toán"
5. **Hệ thống tự động tạo và gửi hóa đơn qua email**

### Thanh toán VNPay
1. Khách hàng đặt hàng với phương thức VNPay
2. Khách hàng thanh toán qua VNPay
3. VNPay callback về hệ thống
4. Hệ thống cập nhật trạng thái thanh toán thành "Đã Thanh Toán"
5. **Hệ thống tự động tạo và gửi hóa đơn qua email**
6. Email xác nhận đơn hàng được gửi

## Cách kiểm tra

### 1. Test thanh toán COD
1. Tạo đơn hàng với phương thức COD
2. Vào admin panel, tìm payment của đơn hàng
3. Cập nhật trạng thái thanh toán thành "Đã Thanh Toán"
4. Kiểm tra email khách hàng có nhận được hóa đơn không
5. Kiểm tra log file có thông báo tạo hóa đơn thành công không

### 2. Test thanh toán VNPay
1. Tạo đơn hàng với phương thức VNPay
2. Thực hiện thanh toán qua VNPay (có thể dùng sandbox)
3. Sau khi thanh toán thành công, kiểm tra email khách hàng
4. Kiểm tra log file có thông báo tạo hóa đơn thành công không

### 3. Kiểm tra database
```sql
-- Kiểm tra hóa đơn đã được tạo
SELECT * FROM invoices WHERE order_id = 'ORDER_ID';

-- Kiểm tra chi tiết hóa đơn
SELECT * FROM invoice_items WHERE invoice_id = 'INVOICE_ID';
```

### 4. Kiểm tra log files
```bash
# Kiểm tra log Laravel
tail -f storage/logs/laravel.log | grep -i invoice

# Tìm log liên quan đến order cụ thể
grep "order_id.*ORDER_ID" storage/logs/laravel.log
```

## Files đã thay đổi

1. **app/Http/Controllers/OrderController.php**
   - Loại bỏ logic tạo hóa đơn cho COD ngay khi tạo đơn hàng
   - Giữ nguyên logic tạo hóa đơn cho VNPay

2. **app/Http/Controllers/Admin/AdminPaymentMethodController.php**
   - Thêm logic tạo hóa đơn khi admin cập nhật trạng thái thanh toán
   - Thêm import InvoiceService và Log
   - Thêm error handling và logging

3. **database/migrations/2025_07_25_134634_add_collection_support_to_invoice_items_table.php**
   - Thêm cột `collection_id` nullable
   - Cho phép `book_id` nullable
   - Thêm foreign key và index cho `collection_id`
   - Xóa unique constraint cũ

4. **app/Models/InvoiceItem.php**
   - Thêm `collection_id` vào fillable
   - Thêm relationship `collection()`
   - Thêm method `getItemName()`

5. **app/Services/InvoiceService.php**
   - Sửa logic tạo invoice items để hỗ trợ combo items
   - Áp dụng cho cả `createInvoiceForOrder` và `createRefundInvoice`

6. **resources/views/admin/orders/show.blade.php**
   - Thêm kiểm tra null cho `$book` trước khi truy cập thuộc tính
   - Thêm xử lý hiển thị combo items với `collection_id`
   - Thêm fallback cho trường hợp sản phẩm không tồn tại

7. **resources/views/admin/invoices/show.blade.php**
   - Thêm kiểm tra null cho `$item->book` trước khi truy cập thuộc tính
   - Thêm xử lý hiển thị combo items trong invoice
   - Thêm fallback cho trường hợp sản phẩm không tồn tại

8. **app/Http/Controllers/Admin/OrderController.php**
   - Thêm `collection` vào eager loading của orderItems

9. **app/Http/Controllers/Admin/AdminInvoiceController.php**
   - Thêm `collection` vào eager loading của invoice items

## Lưu ý quan trọng

1. **Logging**: Tất cả các thao tác tạo hóa đơn đều được log để dễ debug
2. **Error Handling**: Lỗi tạo hóa đơn không làm gián đoạn luồng chính
3. **Transaction Safety**: InvoiceService sử dụng database transaction
4. **Email Templates**: Đảm bảo email templates đã được sửa để hiển thị đúng combo items

## Kết quả khắc phục

Sau khi áp dụng tất cả các giải pháp:

### Trước khi sửa:
- **3 đơn hàng VNPay** đã thanh toán thành công nhưng **chưa có hóa đơn**
- Lỗi "Column 'book_id' cannot be null" khi tạo hóa đơn cho combo items
- Hệ thống không tự động tạo hóa đơn cho COD khi admin xác nhận thanh toán
- Lỗi "Attempt to read property on null" trong view admin khi hiển thị đơn hàng/hóa đơn có combo

### Sau khi sửa:
- **Tất cả đơn hàng VNPay đã thanh toán đều có hóa đơn**
- Hệ thống hỗ trợ tạo hóa đơn cho cả sách lẻ và combo items
- COD tự động tạo hóa đơn khi admin xác nhận thanh toán
- VNPay tự động tạo hóa đơn ngay sau khi thanh toán thành công
- View admin hiển thị đúng thông tin cho cả sách lẻ và combo

### Thống kê hóa đơn:
- **Tổng số hóa đơn**: 24
- **Hóa đơn bán hàng**: 23  
- **Hóa đơn hoàn tiền**: 1

### Đơn hàng đã được khắc phục:
1. **Order BBE-1753450375**: ✅ Đã tạo hóa đơn và gửi email
2. **Order BBE-1753448545**: ✅ Đã tạo hóa đơn và gửi email
3. **Order BBE-1753370063**: ✅ Đã tạo hóa đơn và gửi email

## Troubleshooting

Nếu vẫn không nhận được hóa đơn:

1. **Kiểm tra log files** để xem có lỗi gì không
2. **Kiểm tra email configuration** trong `.env`
3. **Kiểm tra queue jobs** nếu sử dụng queue cho email
4. **Kiểm tra database** xem hóa đơn có được tạo không
5. **Kiểm tra trạng thái thanh toán** có đúng là "Đã Thanh Toán" không