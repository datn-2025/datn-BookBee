# Chức Năng Thanh Toán Bằng Ví Người Dùng

## Tổng Quan
Đã thêm chức năng thanh toán bằng ví điện tử nội bộ vào hệ thống đặt hàng, cho phép người dùng thanh toán trực tiếp từ số dư ví của họ.

## Các Thay Đổi Chính

### 1. OrderService - Thêm Các Method Mới

#### `checkWalletBalance(User $user, $amount)`
- **Mục đích**: Kiểm tra số dư ví của người dùng
- **Chức năng**:
  - Kiểm tra ví có tồn tại không
  - Kiểm tra số dư có đủ để thanh toán không
  - Throw exception nếu không đủ điều kiện
- **Trả về**: Boolean true nếu hợp lệ

#### `processWalletPayment(Order $order, User $user)`
- **Mục đích**: Xử lý thanh toán bằng ví điện tử
- **Chức năng**:
  - Kiểm tra số dư ví
  - Trừ tiền từ ví người dùng
  - Tạo giao dịch ví (WalletTransaction)
  - Cập nhật trạng thái thanh toán đơn hàng
  - Sử dụng Database Transaction để đảm bảo tính nhất quán
- **Trả về**: Boolean true nếu thành công

#### `processOrderCreationWithWallet($request, User $user)`
- **Mục đích**: Xử lý toàn bộ quá trình tạo đơn hàng với hỗ trợ thanh toán ví
- **Chức năng**:
  - Tương tự `processOrderCreation()` nhưng có thêm kiểm tra ví
  - Kiểm tra số dư ví trước khi tạo đơn hàng
  - Trả về thông tin bổ sung về loại thanh toán
- **Trả về**: Array chứa `order`, `payment_method`, `cart_items`, `is_wallet_payment`

### 2. OrderController - Cập Nhật Method `store()`

#### Xử Lý Thanh Toán Ví
- Sử dụng `processOrderCreationWithWallet()` thay vì `processOrderCreation()`
- Thêm logic xử lý thanh toán ví:
  - Gọi `processWalletPayment()` để xử lý thanh toán
  - Tạo Payment record với transaction_id đặc biệt
  - Tạo hóa đơn ngay lập tức (không cần chờ admin xác nhận)
  - Hiển thị thông báo thành công khác biệt

#### Luồng Xử Lý
```
OrderController::store()
├── Validate request
├── OrderService::processOrderCreationWithWallet()
│   ├── handleDeliveryAddress()
│   ├── validateCartItems()
│   ├── calculateCartSubtotal()
│   ├── validateVoucher()
│   ├── checkWalletBalance() [Nếu là thanh toán ví]
│   ├── prepareOrderData()
│   └── createOrderWithItems()
├── Xử lý thanh toán ví [Nếu chọn ví điện tử]
│   ├── processWalletPayment()
│   ├── createPayment()
│   ├── clearUserCart()
│   ├── generateOrderQrCode()
│   ├── sendOrderConfirmation()
│   └── createInvoice()
├── Xử lý VNPay payment [Nếu chọn VNPay]
├── Xử lý COD payment [Nếu chọn COD]
└── Return response
```

### 3. Giao Diện Checkout - Cập Nhật UI

#### Hiển Thị Thông Tin Ví
- Thêm icon đặc biệt cho phương thức "Ví điện tử"
- Hiển thị số dư ví hiện tại
- Cảnh báo nếu số dư không đủ
- Styling với màu xanh lá để phân biệt

#### JavaScript Validation
- Kiểm tra số dư ví khi người dùng chọn phương thức thanh toán
- Vô hiệu hóa nút "Đặt hàng" nếu số dư không đủ
- Thay đổi text nút thành "SỐ DƯ VÍ KHÔNG ĐỦ"
- Hiển thị thông báo lỗi bằng Toastr
- Theo dõi thay đổi tổng tiền (do voucher) và kiểm tra lại

### 4. Database - Giao Dịch Ví

#### WalletTransaction Record
- **wallet_id**: ID của ví
- **amount**: Số tiền thanh toán
- **type**: 'payment'
- **description**: 'Thanh toán đơn hàng {order_code}'
- **related_order_id**: ID đơn hàng
- **status**: 'completed'
- **payment_method**: 'wallet'

#### Payment Record
- **transaction_id**: '{order_code}_WALLET'
- **payment_status_id**: 'Đã Thanh Toán'
- **paid_at**: Thời gian hiện tại

## Quy Trình Thanh Toán Ví

### 1. Điều Kiện Tiên Quyết
- Người dùng đã đăng nhập
- Có ví điện tử đã kích hoạt
- Số dư ví ≥ Tổng tiền đơn hàng

### 2. Quy Trình Thanh Toán
1. **Khởi tạo**: Người dùng chọn "Ví điện tử" tại trang thanh toán
2. **Kiểm tra**: Hệ thống validate số dư ví
3. **Tạo đơn hàng**: Tạo order với trạng thái "Chờ xác nhận"
4. **Xử lý thanh toán**:
   - Trừ tiền từ ví
   - Tạo WalletTransaction
   - Cập nhật trạng thái thanh toán → "Đã Thanh Toán"
5. **Hoàn tất**:
   - Tạo hóa đơn ngay lập tức
   - Gửi email xác nhận
   - Hiển thị thông báo thành công

### 3. Xử Lý Lỗi
- **Không có ví**: "Bạn chưa có ví điện tử. Vui lòng liên hệ admin để kích hoạt."
- **Số dư không đủ**: "Số dư ví không đủ để thanh toán. Số dư hiện tại: {balance}đ"
- **Lỗi giao dịch**: Rollback toàn bộ transaction, giữ nguyên số dư ví

## Lợi Ích

### 1. **Trải Nghiệm Người Dùng**
- Thanh toán nhanh chóng, không cần chuyển hướng
- Không cần nhập thông tin thẻ/ngân hàng
- Xác nhận đơn hàng ngay lập tức

### 2. **Bảo Mật**
- Không xử lý thông tin thẻ tín dụng
- Giao dịch nội bộ, giảm rủi ro
- Database transaction đảm bảo tính nhất quán

### 3. **Quản Lý**
- Tự động tạo hóa đơn
- Không cần admin xác nhận thanh toán
- Theo dõi giao dịch qua WalletTransaction

### 4. **Kinh Doanh**
- Giảm phí giao dịch từ cổng thanh toán
- Tăng tỷ lệ chuyển đổi
- Khuyến khích người dùng nạp tiền vào ví

## Các File Được Thay Đổi

1. **`app/Services/OrderService.php`**
   - Thêm 3 method mới cho xử lý ví
   - Import thêm Wallet và WalletTransaction models

2. **`app/Http/Controllers/OrderController.php`**
   - Cập nhật method `store()` để hỗ trợ thanh toán ví
   - Thêm logic tạo hóa đơn ngay lập tức

3. **`resources/views/orders/checkout.blade.php`**
   - Cập nhật UI hiển thị thông tin ví
   - Thêm JavaScript validation số dư ví
   - Styling cho phương thức thanh toán ví

## Kiểm Thử

### 1. Test Cases
- ✅ Thanh toán thành công với số dư đủ
- ✅ Từ chối thanh toán khi số dư không đủ
- ✅ Xử lý trường hợp chưa có ví
- ✅ Rollback khi có lỗi trong quá trình thanh toán
- ✅ Tạo đúng WalletTransaction và Payment records
- ✅ Cập nhật đúng số dư ví sau thanh toán

### 2. UI Testing
- ✅ Hiển thị đúng số dư ví
- ✅ Cảnh báo khi số dư không đủ
- ✅ Vô hiệu hóa nút đặt hàng khi cần thiết
- ✅ Responsive trên các thiết bị

## Kết Luận

Việc thêm chức năng thanh toán bằng ví điện tử đã:
- Hoàn thiện hệ thống thanh toán
- Cải thiện trải nghiệm người dùng
- Tăng tính bảo mật và tin cậy
- Tuân thủ các nguyên tắc Clean Code và SOLID
- Dễ dàng mở rộng và bảo trì trong tương lai