# Cập Nhật Luồng Nạp Tiền Ví Điện Tử

## Tổng Quan
Đã cập nhật luồng nạp tiền ví điện tử để phù hợp với thực tế: người dùng sẽ chuyển khoản trước, sau đó upload bill để xác nhận.

## Thay Đổi Chính

### 1. Luồng Cũ (Không Hợp Lý)
- Người dùng phải upload bill **trước khi** chuyển khoản
- Không phù hợp với thực tế vì bill chỉ có sau khi chuyển khoản hoàn tất

### 2. Luồng Mới (Hợp Lý)
1. Người dùng chọn số tiền và phương thức "Chuyển khoản ngân hàng"
2. Hệ thống tạo giao dịch pending và hiển thị QR code
3. Người dùng thực hiện chuyển khoản qua ngân hàng
4. Sau khi chuyển khoản, người dùng upload bill trên cùng trang QR
5. Admin duyệt giao dịch dựa trên bill đã upload

## Files Đã Thay Đổi

### 1. Routes
**File:** `routes/web.php`
- Thêm route `POST /wallet/upload-bill` với tên `wallet.uploadBill`

### 2. Controller
**File:** `app/Http/Controllers/WalletController.php`

#### Thay đổi method `deposit()`:
- Xóa validation bắt buộc `bill_image` cho chuyển khoản ngân hàng
- Xóa logic upload bill trong method này
- Chỉ tạo giao dịch pending và chuyển đến trang QR

#### Thêm method `uploadBill()`:
```php
public function uploadBill(Request $request)
{
    // Validation
    $request->validate([
        'transaction_id' => 'required|exists:wallet_transactions,id',
        'bill_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048'
    ]);
    
    // Kiểm tra quyền sở hữu và trạng thái giao dịch
    // Upload và lưu bill image
    // Cập nhật giao dịch
}
```

### 3. View QR Code
**File:** `resources/views/wallets/qr.blade.php`

#### Thêm form upload bill:
- Form với `enctype="multipart/form-data"`
- Input file cho bill image với validation client-side
- Preview ảnh trước khi upload
- Hidden input chứa `transaction_id`

#### Thêm JavaScript:
- Validation kích thước file (max 2MB)
- Validation định dạng file (JPG, PNG, GIF)
- Preview ảnh real-time
- Loading state cho nút submit

### 4. View Deposit Form
**File:** `resources/views/wallets/deposit.blade.php`
- Xóa trường upload bill
- Xóa JavaScript xử lý upload bill
- Đơn giản hóa form chỉ còn số tiền và phương thức thanh toán

## Tính Năng Mới

### 1. Upload Bill Sau Chuyển Khoản
- **Vị trí:** Trang QR code (`/wallet/qr`)
- **Chức năng:** Upload bill sau khi đã chuyển khoản
- **Validation:** 
  - File ảnh (JPG, PNG, GIF)
  - Tối đa 2MB
  - Kiểm tra quyền sở hữu giao dịch
  - Kiểm tra trạng thái pending
  - Không cho upload nếu đã có bill

### 2. Bảo Mật
- Kiểm tra `transaction_id` thuộc về user hiện tại
- Chỉ cho phép upload khi giao dịch ở trạng thái `pending`
- Không cho phép upload lại nếu đã có bill

### 3. User Experience
- Preview ảnh trước khi upload
- Validation real-time
- Loading state khi đang xử lý
- Thông báo rõ ràng về trạng thái

## Luồng Hoạt Động Chi Tiết

### Bước 1: Tạo Yêu Cầu Nạp Tiền
1. User truy cập `/wallet/deposit`
2. Chọn số tiền và "Chuyển khoản ngân hàng"
3. Submit form → `WalletController@deposit`
4. Tạo `WalletTransaction` với status `pending`
5. Redirect đến `/wallet/qr` với thông tin QR

### Bước 2: Hiển thị QR và Form Upload
1. Hiển thị QR code với thông tin chuyển khoản
2. Hiển thị form upload bill
3. User thực hiện chuyển khoản qua ngân hàng

### Bước 3: Upload Bill
1. User chọn ảnh bill từ thiết bị
2. Preview ảnh và validation client-side
3. Submit form → `WalletController@uploadBill`
4. Server validation và lưu file
5. Cập nhật `bill_image` trong `WalletTransaction`
6. Redirect về `/wallet` với thông báo thành công

### Bước 4: Admin Duyệt
1. Admin xem danh sách giao dịch pending
2. Xem bill đã upload qua modal
3. Duyệt hoặc từ chối giao dịch

## Lợi Ích

### 1. Phù Hợp Thực Tế
- Người dùng chuyển khoản trước, có bill mới upload
- Luồng tự nhiên và hợp lý

### 2. Tăng Tính Minh Bạch
- Bill thực tế từ ngân hàng
- Admin có bằng chứng rõ ràng để duyệt

### 3. Cải Thiện UX
- Không bắt buộc upload file ngay từ đầu
- Có thể chụp bill ngay sau khi chuyển khoản
- Preview ảnh trước khi upload

### 4. Bảo Mật Tốt Hơn
- Kiểm tra quyền sở hữu giao dịch
- Validation đầy đủ
- Không cho phép thao tác trái phép

## Lưu Ý Kỹ Thuật

### 1. Database
- Trường `bill_image` trong `wallet_transactions` có thể NULL
- Giao dịch được tạo với `bill_image = NULL` ban đầu
- Cập nhật `bill_image` sau khi upload

### 2. Storage
- File bill lưu trong `storage/app/public/wallet_bills/`
- Tên file: `bill_{timestamp}_{uniqid}.{extension}`
- Cần đảm bảo symbolic link `storage:link` đã được tạo

### 3. Validation
- Client-side: JavaScript validation ngay khi chọn file
- Server-side: Laravel validation rules
- File size: max 2MB
- File types: jpeg, jpg, png, gif

## Tương Thích

### 1. Backward Compatibility
- Giao dịch cũ vẫn hoạt động bình thường
- Admin vẫn có thể duyệt giao dịch không có bill
- Không ảnh hưởng đến các chức năng khác

### 2. Database Migration
- Không cần migration mới (trường `bill_image` đã tồn tại)
- Dữ liệu cũ không bị ảnh hưởng

## Testing

### 1. Test Cases
- Upload bill thành công
- Upload file không hợp lệ (size, format)
- Upload bill cho giao dịch không thuộc về user
- Upload bill cho giao dịch đã processed
- Upload bill khi đã có bill

### 2. Manual Testing
1. Tạo giao dịch nạp tiền bằng chuyển khoản
2. Kiểm tra QR code hiển thị đúng
3. Upload bill và kiểm tra preview
4. Kiểm tra file được lưu đúng
5. Admin kiểm tra bill trong giao diện quản lý

---

**Ngày cập nhật:** $(date)
**Phiên bản:** 2.0
**Tác giả:** Development Team