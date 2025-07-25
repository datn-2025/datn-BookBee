# Tích Hợp VNPay vào PreorderController - Tài Liệu Nghiệp Vụ

## Tổng Quan
Tài liệu này mô tả việc tích hợp thanh toán VNPay vào hệ thống đặt trước sách (PreorderController) của ứng dụng BookStore.

## Mục Tiêu
- Cho phép khách hàng thanh toán trực tuyến khi đặt trước sách qua VNPay
- Xử lý callback từ VNPay để cập nhật trạng thái đơn đặt trước
- Gửi email xác nhận sau khi thanh toán thành công
- Quản lý các trường hợp thanh toán thất bại

## Luồng Nghiệp Vụ

### 1. Quy Trình Đặt Trước Sách với VNPay

#### Bước 1: Khách hàng chọn thanh toán VNPay
- Khách hàng điền thông tin đặt trước sách
- Chọn phương thức thanh toán "Thanh toán vnpay"
- Hệ thống kiểm tra và xác thực dữ liệu đầu vào

#### Bước 2: Tạo đơn đặt trước
```php
// Tạo preorder với trạng thái PENDING
$preorder = Preorder::create([
    'user_id' => Auth::id(),
    'book_id' => $request->book_id,
    'book_format_id' => $request->book_format_id,
    'payment_method_id' => $request->payment_method_id,
    'customer_name' => $request->customer_name,
    'email' => $request->email,
    'phone' => $request->phone,
    'address' => $request->address,
    // ... các trường khác
    'status' => Preorder::STATUS_PENDING,
    'total_amount' => $totalAmount,
]);

// Tạo mã đơn đặt trước
$preorderCode = 'PRE-' . date('YmdHis') . '-' . $preorder->id;
$preorder->update(['preorder_code' => $preorderCode]);
```

#### Bước 3: Chuyển hướng đến VNPay
- Tạo dữ liệu thanh toán VNPay
- Tạo payment record với trạng thái "Chờ Xử Lý"
- Chuyển hướng khách hàng đến cổng thanh toán VNPay

### 2. Xử Lý Callback từ VNPay

#### Bước 1: Xác thực chữ ký số
```php
// Kiểm tra tính hợp lệ của chữ ký từ VNPay
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
if ($secureHash !== $vnp_SecureHash) {
    // Xử lý lỗi xác thực
}
```

#### Bước 2: Xử lý kết quả thanh toán

**Thanh toán thành công (vnp_ResponseCode = '00'):**
```php
$preorder->update([
    'status' => Preorder::STATUS_CONFIRMED,
    'confirmed_at' => now(),
    'payment_status' => 'Đã Thanh Toán',
    'vnpay_transaction_id' => $vnp_TransactionNo
]);
```

**Thanh toán thất bại:**
```php
$preorder->update([
    'status' => Preorder::STATUS_CANCELLED,
    'cancelled_at' => now(),
    'payment_status' => 'Thất Bại',
    'cancellation_reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode
]);
```

## Cấu Trúc Dữ Liệu

### Bảng Preorders - Các Trường Mới
```sql
ALTER TABLE preorders ADD COLUMN preorder_code VARCHAR(255) NULL;
ALTER TABLE preorders ADD COLUMN payment_status VARCHAR(255) NULL;
ALTER TABLE preorders ADD COLUMN vnpay_transaction_id VARCHAR(255) NULL;
ALTER TABLE preorders ADD COLUMN cancellation_reason TEXT NULL;
ALTER TABLE preorders ADD COLUMN cancelled_at TIMESTAMP NULL;
```

### Trạng Thái Đơn Đặt Trước
- `STATUS_PENDING`: Chờ xác nhận (mặc định)
- `STATUS_CONFIRMED`: Đã xác nhận (sau khi thanh toán thành công)
- `STATUS_CANCELLED`: Đã hủy (thanh toán thất bại)
- `STATUS_PROCESSING`: Đang xử lý
- `STATUS_SHIPPED`: Đã giao hàng
- `STATUS_DELIVERED`: Đã nhận hàng

### Trạng Thái Thanh Toán
- `Chờ Xử Lý`: Trạng thái ban đầu
- `Đã Thanh Toán`: Thanh toán thành công
- `Thất Bại`: Thanh toán thất bại

## API Endpoints

### 1. Tạo Đơn Đặt Trước với VNPay
- **URL**: `POST /preorders`
- **Method**: `PreorderController@store`
- **Mô tả**: Tạo đơn đặt trước và chuyển hướng đến VNPay nếu chọn thanh toán VNPay

### 2. Callback VNPay cho Preorder
- **URL**: `GET /preorder/vnpay/return`
- **Method**: `PreorderController@vnpayReturn`
- **Mô tả**: Xử lý callback từ VNPay sau khi khách hàng thanh toán

## Services Được Sử Dụng

### 1. PaymentService
```php
// Tạo payment record cho preorder
public function createPreorderPayment(array $data)
{
    return Payment::create([
        'id' => (string) Str::uuid(),
        'order_id' => null, // Chưa có order cho preorder
        'transaction_id' => $data['transaction_id'],
        'payment_method_id' => $data['payment_method_id'],
        'amount' => $data['amount'],
        'payment_status_id' => $data['payment_status_id'],
        'paid_at' => $data['paid_at'] ?? null,
        'notes' => 'Preorder payment for ID: ' . $data['preorder_id']
    ]);
}
```

### 2. EmailService
```php
// Gửi email xác nhận đặt trước
public function sendPreorderConfirmation($preorder)
{
    // Gửi email thông báo đặt trước thành công
    Mail::raw($emailContent, function ($message) use ($preorder) {
        $message->to($preorder->email)
               ->subject('Xác nhận đặt trước sách - ' . $preorder->preorder_code);
    });
}
```

## Cấu Hình VNPay

### Config trong `config/services.php`
```php
'vnpay' => [
    'tmn_code' => env('VNPAY_TMN_CODE'),
    'hash_secret' => env('VNPAY_HASH_SECRET'),
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
],
```

### Biến Môi Trường (.env)
```
VNPAY_TMN_CODE=your_terminal_code
VNPAY_HASH_SECRET=your_hash_secret
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
```

## Xử Lý Lỗi

### 1. Lỗi Xác Thực Chữ Ký
- Log lỗi chi tiết
- Hiển thị thông báo lỗi cho người dùng
- Chuyển hướng về trang sản phẩm

### 2. Lỗi Không Tìm Thấy Preorder
- Log lỗi với preorder_code
- Hiển thị thông báo lỗi
- Chuyển hướng về trang chủ

### 3. Lỗi Xử Lý Thanh toán
- Rollback transaction
- Log lỗi chi tiết
- Hiển thị thông báo lỗi chung

### 4. **LỖI QUAN TRỌNG: Payments Table Constraint Violation**

**Mô tả lỗi:**
```
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'order_id' cannot be null
```

**Nguyên nhân:**
- Bảng `payments` có ràng buộc NOT NULL trên cột `order_id`
- Đối với preorder, chưa có order nên `order_id` sẽ là NULL
- Khi cố gắng tạo payment record cho preorder, lỗi constraint violation xảy ra

**Giải pháp đã áp dụng:**
```php
// KHÔNG tạo payment record cho preorder
// Thay vào đó, lưu thông tin thanh toán trực tiếp trong preorder record
$preorder->update([
    'payment_status' => 'Đã Thanh Toán',
    'vnpay_transaction_id' => $vnp_TransactionNo,
    'confirmed_at' => now()
]);
```

**Các giải pháp thay thế (không được áp dụng):**
1. Tạo bảng `preorder_payments` riêng biệt
2. Sửa đổi bảng `payments` để cho phép `order_id` NULL
3. Tạo một order tạm thời cho preorder

**Lưu ý quan trọng:**
- Khi chuyển preorder thành order, cần tạo payment record tương ứng
- Đảm bảo đồng bộ thông tin thanh toán giữa preorder và order
- Monitor việc tạo order từ preorder để tránh mất thông tin thanh toán

### 5. **CÁC SỬA ĐỔI BỔ SUNG ĐÃ THỰC HIỆN**

**Cập nhật Model Preorder:**
```php
// Thêm các trường mới vào fillable array
protected $fillable = [
    // ... các trường cũ
    'preorder_code',
    'payment_status', 
    'vnpay_transaction_id',
    'cancellation_reason',
    'cancelled_at'
];
```

**Sửa lỗi Redirect Routes:**
- **Vấn đề:** Route `preorders.show` không tồn tại
- **Giải pháp:** Chuyển về route `clients.show` hoặc `home`
```php
// Thay vì:
return redirect()->route('preorders.show', $preorder->id);

// Sử dụng:
return redirect()->route('clients.show', ['id' => $preorder->book_id])
    ->with('success', 'Đặt trước sách thành công! Mã đơn: ' . $preorder->preorder_code);
```

**Cải thiện Error Handling:**
- Tất cả redirect lỗi đều về route `home` với thông báo rõ ràng
- Thêm logging chi tiết cho tất cả các trường hợp lỗi
- Sử dụng Toastr để hiển thị thông báo user-friendly

### 6. **LỖI AJAX/VNPay REDIRECT - ĐÃ SỬA**

**Vấn đề:**
- Frontend gửi AJAX request mong đợi JSON response
- Khi thanh toán VNPay, controller redirect thay vì trả JSON
- Gây lỗi "Có lỗi xảy ra khi gửi yêu cầu!" trong JavaScript

**Giải pháp đã áp dụng:**

**Backend (PreorderController):**
```php
// Thay vì redirect trực tiếp
return $this->vnpay_payment($vnpayData);

// Trả về JSON với URL VNPay
$vnpayUrl = $this->vnpay_payment($vnpayData);
return response()->json([
    'success' => true,
    'redirect_to_vnpay' => true,
    'vnpay_url' => $vnpayUrl,
    'message' => 'Chuyển hướng đến VNPay để thanh toán',
    'preorder_id' => $preorder->id
]);
```

**Frontend (JavaScript):**
```javascript
.then(data => {
    if (data.success) {
        // Kiểm tra nếu là thanh toán VNPay
        if (data.redirect_to_vnpay && data.vnpay_url) {
            // Hiển thị thông báo và chuyển hướng
            toastr.info('Đang chuyển hướng đến VNPay...', 'Thanh toán');
            closePreorderModal();
            setTimeout(() => {
                window.location.href = data.vnpay_url;
            }, 1000);
        } else {
            // Xử lý thanh toán thường
            toastr.success(data.message, 'Thành công!');
            closePreorderModal();
            form.reset();
        }
    }
});
```

**Kết quả:**
- ✅ AJAX request hoạt động bình thường
- ✅ VNPay redirect được xử lý đúng cách
- ✅ User experience mượt mà với thông báo rõ ràng
- ✅ Không còn lỗi "Có lỗi xảy ra khi gửi yêu cầu!"

## Logging và Monitoring

### Các Sự Kiện Được Log
1. **Thanh toán thành công**: Log thông tin preorder và transaction
2. **Thanh toán thất bại**: Log mã lỗi và lý do
3. **Lỗi xác thực chữ ký**: Log hash mong đợi và nhận được
4. **Lỗi gửi email**: Log lỗi và thông tin preorder

### Ví Dụ Log
```php
Log::info('Preorder payment completed successfully', [
    'preorder_id' => $preorder->id,
    'preorder_code' => $preorder->preorder_code,
    'transaction_id' => $vnp_TransactionNo
]);
```

## Bảo Mật

### 1. Xác Thực Chữ Ký
- Sử dụng HMAC SHA512 để xác thực dữ liệu từ VNPay
- So sánh chữ ký nhận được với chữ ký tính toán

### 2. Validation Dữ Liệu
- Kiểm tra tất cả dữ liệu đầu vào
- Sử dụng Laravel Validator
- Kiểm tra tồn tại của book, payment method, etc.

### 3. Transaction Safety
- Sử dụng database transaction
- Rollback khi có lỗi
- Đảm bảo tính nhất quán dữ liệu

## Testing

### 1. Test Cases Cần Kiểm Tra
- Tạo preorder với thanh toán VNPay thành công
- Xử lý callback VNPay thành công
- Xử lý callback VNPay thất bại
- Xử lý lỗi xác thực chữ ký
- Xử lý lỗi không tìm thấy preorder

### 2. Test Environment
- Sử dụng VNPay Sandbox cho testing
- Mock email service để tránh gửi email thật
- Sử dụng test database

## Triển Khai Production

### 1. Checklist Triển Khai
- [ ] Cấu hình VNPay production credentials
- [ ] Chạy migration để thêm các trường mới
- [ ] Test thanh toán với số tiền nhỏ
- [ ] Kiểm tra email notification
- [ ] Monitor logs sau khi triển khai

### 2. Monitoring
- Theo dõi tỷ lệ thanh toán thành công/thất bại
- Monitor response time của VNPay
- Kiểm tra logs lỗi thường xuyên

## Kết Luận

Việc tích hợp VNPay vào PreorderController đã được hoàn thành với đầy đủ các tính năng:
- Xử lý thanh toán trực tuyến cho đặt trước sách
- Quản lý trạng thái thanh toán và đơn đặt trước
- Gửi email xác nhận tự động
- Xử lý lỗi và logging chi tiết
- Bảo mật và validation dữ liệu

Hệ thống đã sẵn sàng để xử lý thanh toán VNPay cho chức năng đặt trước sách một cách an toàn và hiệu quả.
