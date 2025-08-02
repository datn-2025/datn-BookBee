# Chức năng ví điện tử nâng cao - Upload Bill và Hóa đơn rút tiền

## Tổng quan

Chức năng này bổ sung hai tính năng quan trọng cho hệ thống ví điện tử:
1. **Upload bill khi nạp tiền** - Người dùng phải upload ảnh bill chuyển khoản khi nạp tiền bằng phương thức chuyển khoản ngân hàng
2. **Tự động tạo hóa đơn rút tiền** - Hệ thống tự động tạo và gửi hóa đơn qua email khi admin xác nhận rút tiền

## 1. Chức năng Upload Bill khi nạp tiền

### Mô tả
Khi người dùng chọn phương thức "Chuyển khoản ngân hàng" để nạp tiền, hệ thống sẽ yêu cầu upload ảnh bill chuyển khoản để admin có thể kiểm tra và duyệt giao dịch.

### Files đã thay đổi

#### 1. Database Migration
- **File:** `database/migrations/2025_07_25_234752_add_bill_image_to_wallet_transactions_table.php`
- **Mục đích:** Thêm trường `bill_image` vào bảng `wallet_transactions`

```php
$table->string('bill_image')->nullable()->after('customer_name')->comment('Ảnh bill chuyển khoản khi nạp tiền');
```

#### 2. Model WalletTransaction
- **File:** `app/Models/WalletTransaction.php`
- **Thay đổi:** Thêm `bill_image` vào `$fillable`

#### 3. Form nạp tiền
- **File:** `resources/views/wallets/deposit.blade.php`
- **Thay đổi:**
  - Thêm `enctype="multipart/form-data"` cho form
  - Thêm trường upload file với validation phía client
  - Thêm preview ảnh khi upload
  - JavaScript để hiển thị/ẩn trường upload dựa trên phương thức thanh toán

#### 4. Controller xử lý nạp tiền
- **File:** `app/Http/Controllers/WalletController.php`
- **Thay đổi:**
  - Validation bắt buộc upload bill cho chuyển khoản ngân hàng
  - Xử lý lưu file ảnh vào storage
  - Lưu đường dẫn file vào database

### Validation Rules
```php
if ($request->payment_method === 'bank_transfer') {
    $validationRules['bill_image'] = 'required|image|mimes:jpeg,jpg,png,gif|max:2048';
}
```

### Lưu trữ file
- **Thư mục:** `storage/app/public/wallet_bills/`
- **Định dạng tên file:** `bill_{timestamp}_{uniqid}.{extension}`
- **Kích thước tối đa:** 2MB
- **Định dạng hỗ trợ:** JPG, PNG, GIF

## 2. Chức năng tự động tạo hóa đơn rút tiền

### Mô tả
Khi admin duyệt giao dịch rút tiền, hệ thống sẽ tự động tạo hóa đơn và gửi qua email cho người dùng.

### Files đã tạo mới

#### 1. WalletInvoiceService
- **File:** `app/Services/WalletInvoiceService.php`
- **Mục đích:** Xử lý tạo và gửi hóa đơn rút tiền
- **Phương thức chính:**
  - `createAndSendWithdrawInvoice()` - Tạo và gửi hóa đơn
  - `createWithdrawInvoice()` - Tạo hóa đơn trong database
  - `sendWithdrawInvoiceEmail()` - Gửi email hóa đơn

#### 2. Mail Class
- **File:** `app/Mail/WalletWithdrawInvoice.php`
- **Mục đích:** Xử lý gửi email hóa đơn rút tiền

#### 3. Email Template
- **File:** `resources/views/emails/wallet-withdraw-invoice.blade.php`
- **Mục đích:** Template email hóa đơn rút tiền với thiết kế responsive

### Files đã cập nhật

#### 1. Admin WalletController
- **File:** `app/Http/Controllers/Admin/WalletController.php`
- **Thay đổi:** Tích hợp WalletInvoiceService vào phương thức `approveTransaction()`

#### 2. Admin View - Lịch sử nạp ví
- **File:** `resources/views/admin/wallets/deposit.blade.php`
- **Thay đổi:**
  - Thêm cột "Bill" để hiển thị nút xem bill
  - Thêm modal để xem ảnh bill
  - JavaScript xử lý modal

## 3. Luồng hoạt động

### Luồng nạp tiền với bill
1. Người dùng chọn "Chuyển khoản ngân hàng"
2. Form hiển thị trường upload bill (bắt buộc)
3. Người dùng upload ảnh bill và submit
4. Hệ thống validate và lưu ảnh vào storage
5. Tạo giao dịch với trạng thái "pending"
6. Admin xem bill trong trang quản lý và duyệt giao dịch

### Luồng rút tiền với hóa đơn
1. Người dùng tạo yêu cầu rút tiền
2. Admin duyệt giao dịch rút tiền
3. Hệ thống tự động:
   - Cập nhật trạng thái giao dịch
   - Trừ tiền từ wallet_lock
   - Tạo hóa đơn trong database
   - Gửi email hóa đơn cho người dùng

## 4. Cấu trúc hóa đơn rút tiền

### Thông tin hóa đơn
- **Số hóa đơn:** `WD-{YYYYMMDD}-{transaction_id}`
- **Loại hóa đơn:** `withdraw`
- **Nội dung:** "Rút tiền từ ví điện tử"

### Email template bao gồm
- Header với thông tin cửa hàng
- Thông tin hóa đơn và khách hàng
- Chi tiết giao dịch rút tiền
- Thông tin tài khoản nhận tiền
- Footer với thông tin liên hệ

## 5. Bảo mật và Validation

### Upload Bill
- Chỉ chấp nhận file ảnh
- Kích thước tối đa 2MB
- Tên file được mã hóa để tránh conflict
- Lưu trữ trong thư mục protected

### Hóa đơn rút tiền
- Chỉ tạo khi admin duyệt
- Log đầy đủ quá trình tạo hóa đơn
- Xử lý lỗi không ảnh hưởng đến việc duyệt giao dịch

## 6. Cách sử dụng

### Cho người dùng
1. **Nạp tiền:**
   - Truy cập trang nạp ví
   - Chọn "Chuyển khoản ngân hàng"
   - Upload ảnh bill chuyển khoản
   - Chờ admin duyệt

2. **Rút tiền:**
   - Tạo yêu cầu rút tiền
   - Chờ admin duyệt
   - Nhận email hóa đơn khi được duyệt

### Cho admin
1. **Duyệt nạp tiền:**
   - Vào trang "Nạp ví"
   - Click "Xem bill" để kiểm tra
   - Duyệt hoặc từ chối giao dịch

2. **Duyệt rút tiền:**
   - Vào trang "Rút ví"
   - Duyệt giao dịch
   - Hệ thống tự động gửi hóa đơn

## 7. Lưu ý kỹ thuật

### Storage
- Cần tạo symbolic link: `php artisan storage:link`
- Đảm bảo quyền ghi cho thư mục storage

### Email
- Cần cấu hình email trong `.env`
- Kiểm tra queue nếu sử dụng email queue

### Log
- Tất cả hoạt động đều được log
- Kiểm tra log tại `storage/logs/laravel.log`

## 8. Troubleshooting

### Lỗi upload file
- Kiểm tra `upload_max_filesize` và `post_max_size` trong PHP
- Đảm bảo thư mục storage có quyền ghi

### Lỗi gửi email
- Kiểm tra cấu hình email
- Xem log để biết chi tiết lỗi
- Email không gửi được không ảnh hưởng đến việc duyệt giao dịch

### Lỗi hiển thị ảnh
- Chạy `php artisan storage:link`
- Kiểm tra đường dẫn file trong database

## 9. Tác động

### Cải thiện
✅ **Tăng tính minh bạch:** Admin có thể kiểm tra bill trước khi duyệt

✅ **Tự động hóa:** Hóa đơn rút tiền được tạo và gửi tự động

✅ **Trải nghiệm người dùng:** Người dùng nhận được hóa đơn đầy đủ

✅ **Quản lý tốt hơn:** Admin có đầy đủ thông tin để ra quyết định

### Không ảnh hưởng
✅ **Các chức năng khác:** Không thay đổi luồng VNPay và COD

✅ **Dữ liệu cũ:** Giao dịch cũ vẫn hoạt động bình thường

✅ **Performance:** Không ảnh hưởng đến hiệu suất hệ thống