# Tách Đơn Hàng Khi Mua Cả Ebook và Sách Vật Lý

## Mô tả chức năng

Khi người dùng mua cả ebook và sách vật lý trong cùng một giỏ hàng, hệ thống sẽ tự động tách đơn hàng thành:
- **Đơn hàng cha**: Chứa thông tin tổng quan và thanh toán
- **Đơn hàng con 1**: Chứa các sách vật lý → Giao hàng, tính phí ship
- **Đơn hàng con 2**: Chứa các ebook → Không tính ship, gửi email link tải ngay

## Cấu trúc Database

### Bảng Orders
```sql
- id (UUID): Khóa chính
- parent_order_id (UUID, nullable): Tham chiếu đến đơn hàng cha
- order_code (string): Mã đơn hàng hiển thị
- delivery_method (string): 'mixed', 'delivery', 'ebook'
- ... (các trường khác)
```

### Relationships
```php
// Trong Model Order
public function parentOrder(): BelongsTo
public function childOrders(): HasMany
public function isParentOrder(): bool
public function isChildOrder(): bool
```

## Luồng xử lý

### 1. Phát hiện Mixed Format Cart
```php
// Trong OrderController::store()
$cartItems = $this->orderService->validateCartItems($user);
$isMixedFormat = $this->mixedOrderService->hasMixedFormats($cartItems);
```

### 2. Tách giỏ hàng
```php
// Trong MixedOrderService
public function separateCartItems($cartItems)
{
    $physicalItems = collect();
    $ebookItems = collect();
    
    foreach ($cartItems as $item) {
        if (strtolower($item->bookFormat->format_name) === 'ebook') {
            $ebookItems->push($item);
        } else {
            $physicalItems->push($item);
        }
    }
    
    return [
        'physical' => $physicalItems,
        'ebook' => $ebookItems
    ];
}
```

### 3. Tạo đơn hàng cha-con
```php
// 1. Tạo đơn hàng cha
$parentOrder = Order::create([
    'order_code' => 'BBE-PARENT-' . time(),
    'delivery_method' => 'mixed',
    'parent_order_id' => null,
    // ... thông tin khác
]);

// 2. Tạo đơn hàng con cho sách vật lý
$physicalOrder = Order::create([
    'order_code' => 'BBE-PHYSICAL-' . time(),
    'delivery_method' => 'delivery',
    'parent_order_id' => $parentOrder->id,
    'shipping_fee' => $request->shipping_fee_applied,
    // ... thông tin khác
]);

// 3. Tạo đơn hàng con cho ebook
$ebookOrder = Order::create([
    'order_code' => 'BBE-EBOOK-' . time(),
    'delivery_method' => 'ebook',
    'parent_order_id' => $parentOrder->id,
    'shipping_fee' => 0,
    'address_id' => null,
    // ... thông tin khác
]);
```

### 4. Phân bổ voucher và discount
```php
// Tính tỷ lệ phân bổ discount
$physicalDiscountRatio = $physicalSubtotal / $totalSubtotal;
$physicalDiscountAmount = $totalDiscountAmount * $physicalDiscountRatio;
$ebookDiscountAmount = $totalDiscountAmount - $physicalDiscountAmount;
```

### 5. Xử lý thanh toán
- **Thanh toán ví**: Trừ tiền từ đơn hàng cha, cập nhật trạng thái cho các đơn con
- **VNPay**: Thanh toán tổng tiền từ đơn hàng cha
- **COD**: Không khả dụng cho mixed format

### 6. Xử lý sau thanh toán
```php
// Tạo đơn hàng GHN cho sách vật lý
if ($physicalOrder->delivery_method === 'delivery') {
    $this->orderService->createGhnOrder($physicalOrder);
}

// Tạo mã QR cho các đơn hàng
$this->qrCodeService->generateOrderQrCode($parentOrder);
$this->qrCodeService->generateOrderQrCode($physicalOrder);
$this->qrCodeService->generateOrderQrCode($ebookOrder);

// Gửi email xác nhận
$this->emailService->sendOrderConfirmation($parentOrder);
$this->emailService->sendOrderConfirmation($physicalOrder);

// Gửi email ebook ngay lập tức
$this->emailService->sendEbookDownloadEmail($ebookOrder);
```

## Giao diện người dùng

### 1. Trang Checkout
```html
@if(isset($mixedFormatCart) && $mixedFormatCart)
<div class="bg-red-600 text-white p-6 mb-8">
    <h3>LƯU Ý QUAN TRỌNG</h3>
    <p>Giỏ hàng của bạn có cả sách vật lý và sách điện tử (ebook).</p>
    <div class="bg-white/10 p-4 rounded">
        <h4>📦 ĐƠN HÀNG SẼ ĐƯỢC CHIA THÀNH 2 PHẦN:</h4>
        <ul>
            <li>• Đơn 1: Chứa các sách vật lý → Giao hàng tận nơi, tính phí ship</li>
            <li>• Đơn 2: Chứa các ebook → Gửi email link tải ngay sau khi thanh toán</li>
        </ul>
        <p>* Phương thức thanh toán khi nhận hàng không khả dụng cho đơn hàng này.</p>
    </div>
</div>
@endif
```

### 2. Danh sách đơn hàng
```html
<h3>
    ĐƠN HÀNG #{{ $order->order_code }}
    @if($order->delivery_method === 'mixed')
    <span class="bg-yellow-500 text-black text-xs font-bold uppercase rounded">
        HỖN HỢP
    </span>
    @endif
</h3>

@if($order->delivery_method === 'mixed')
<div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4">
    <h5>📦 ĐƠN HÀNG ĐÃ ĐƯỢC CHIA THÀNH 2 PHẦN</h5>
    <p>Sách vật lý sẽ được giao hàng, ebook sẽ được gửi qua email</p>
</div>
@endif
```

### 3. Chi tiết đơn hàng
```html
@if($order->delivery_method === 'mixed')
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
    <h5>📦 ĐƠN HÀNG ĐÃ ĐƯỢC CHIA THÀNH 2 PHẦN:</h5>
    <div class="space-y-2">
        @foreach($order->childOrders as $childOrder)
        <div class="flex justify-between items-center bg-white p-2 rounded border">
            <div>
                <span class="font-semibold">{{ $childOrder->order_code }}</span>
                <span class="text-gray-600">
                    ({{ $childOrder->delivery_method === 'delivery' ? 'Sách vật lý - Giao hàng' : 'Ebook - Gửi email' }})
                </span>
            </div>
            <span class="font-bold text-blue-600">{{ number_format($childOrder->total_amount) }}đ</span>
        </div>
        @endforeach
    </div>
</div>
@endif
```

## Các file liên quan

### Models
- `app/Models/Order.php`: Thêm relationships cho parent-child orders

### Services
- `app/Services/MixedOrderService.php`: Service xử lý đơn hàng hỗn hợp
- `app/Services/OrderService.php`: Service đơn hàng chính

### Controllers
- `app/Http/Controllers/OrderController.php`: Xử lý logic checkout và tạo đơn hàng

### Views
- `resources/views/orders/checkout.blade.php`: Trang thanh toán
- `resources/views/clients/account/orders.blade.php`: Danh sách đơn hàng
- `resources/views/clients/account/order-details.blade.php`: Chi tiết đơn hàng

## Lưu ý kỹ thuật

### 1. Transaction Safety
- Tất cả thao tác tạo đơn hàng được wrap trong DB::transaction()
- Rollback nếu có lỗi xảy ra

### 2. Payment Processing
- Thanh toán được xử lý trên đơn hàng cha
- Trạng thái thanh toán được đồng bộ xuống các đơn con

### 3. Email Notifications
- Gửi email xác nhận cho đơn hàng cha và đơn sách vật lý
- Gửi email ebook download ngay sau khi thanh toán thành công

### 4. Shipping
- Chỉ tạo đơn hàng GHN cho đơn sách vật lý
- Ebook không có phí ship và không cần địa chỉ giao hàng

### 5. Order Status Management
- Đơn hàng cha theo dõi trạng thái tổng quan
- Các đơn con có thể có trạng thái khác nhau
- Đơn ebook thường chuyển sang "Hoàn thành" ngay sau thanh toán

## Kết quả mong muốn

1. **Trải nghiệm người dùng tốt**: Thông báo rõ ràng về việc tách đơn hàng
2. **Xử lý thanh toán chính xác**: Tính toán đúng phí ship và phân bổ discount
3. **Giao hàng hiệu quả**: Sách vật lý được giao hàng, ebook được gửi email ngay
4. **Quản lý đơn hàng dễ dàng**: Admin có thể theo dõi và quản lý các đơn hàng cha-con
5. **Tính nhất quán dữ liệu**: Đảm bảo tính toàn vẹn dữ liệu qua các transaction

## Cách sử dụng

1. **Người dùng thêm cả sách vật lý và ebook vào giỏ hàng**
2. **Vào trang checkout, hệ thống hiển thị thông báo về việc tách đơn hàng**
3. **Chọn phương thức thanh toán (không có COD)**
4. **Nhập địa chỉ giao hàng (cho sách vật lý)**
5. **Hoàn tất thanh toán**
6. **Nhận email xác nhận và link download ebook**
7. **Theo dõi trạng thái giao hàng sách vật lý**

## Troubleshooting

### Lỗi thường gặp:
1. **Không tạo được đơn hàng con**: Kiểm tra transaction và rollback
2. **Email ebook không được gửi**: Kiểm tra EmailService và queue
3. **Phí ship không đúng**: Kiểm tra logic tính phí ship cho từng loại đơn hàng
4. **Trạng thái thanh toán không đồng bộ**: Kiểm tra logic cập nhật trạng thái

### Debug:
```php
// Log để debug
Log::info('Mixed format order creation', [
    'parent_order_id' => $parentOrder->id,
    'physical_order_id' => $physicalOrder->id,
    'ebook_order_id' => $ebookOrder->id,
    'user_id' => $user->id
]);
```