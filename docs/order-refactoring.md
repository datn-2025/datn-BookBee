# Refactoring OrderController - Chuyển Logic Tạo Đơn Hàng Vào OrderService

## Tổng Quan
Đã thực hiện refactoring để chuyển các logic tạo đơn hàng từ `OrderController` vào `OrderService` nhằm tuân thủ nguyên tắc **Single Responsibility Principle** và **Clean Code**.

## Các Thay Đổi Chính

### 1. OrderService - Thêm Các Method Mới

#### `handleDeliveryAddress($request, User $user)`
- **Mục đích**: Xử lý địa chỉ giao hàng (tạo mới hoặc sử dụng có sẵn)
- **Chức năng**: 
  - Kiểm tra nếu có `address_id` thì sử dụng địa chỉ có sẵn
  - Nếu không có thì tạo địa chỉ mới từ thông tin form
- **Trả về**: ID của địa chỉ được sử dụng

#### `validateVoucher($voucherCode, $subtotal)`
- **Mục đích**: Validate và lấy thông tin voucher
- **Chức năng**:
  - Kiểm tra voucher có tồn tại không
  - Validate trạng thái active
  - Kiểm tra số lượng còn lại
  - Validate thời gian hiệu lực
  - Kiểm tra giá trị đơn hàng tối thiểu
- **Trả về**: Array chứa `voucher_id` và `discount_amount`

#### `validateCartItems(User $user)`
- **Mục đích**: Validate giỏ hàng của người dùng
- **Chức năng**:
  - Lấy cart items với các relationship cần thiết
  - Kiểm tra cart không rỗng
  - Validate dữ liệu cart item hợp lệ
- **Trả về**: Collection của cart items

#### `calculateCartSubtotal($cartItems)`
- **Mục đích**: Tính tổng tiền giỏ hàng
- **Chức năng**: Tính tổng `price * quantity` của tất cả items
- **Trả về**: Số tiền subtotal

#### `prepareOrderData($request, User $user, $addressId, $voucherId, $subtotal, $discountAmount)`
- **Mục đích**: Chuẩn bị dữ liệu đơn hàng
- **Chức năng**:
  - Lấy order status và payment status mặc định
  - Tính tổng tiền cuối cùng
  - Chuẩn bị array dữ liệu cho việc tạo order
- **Trả về**: Array dữ liệu order

#### `processOrderCreation($request, User $user)`
- **Mục đích**: Xử lý toàn bộ quá trình tạo đơn hàng
- **Chức năng**:
  1. Xử lý địa chỉ giao hàng
  2. Validate giỏ hàng
  3. Tính tổng tiền
  4. Validate voucher
  5. Lấy thông tin phương thức thanh toán
  6. Chuẩn bị dữ liệu đơn hàng
  7. Tạo đơn hàng
- **Trả về**: Array chứa `order`, `payment_method`, `cart_items`

#### `clearUserCart(User $user)`
- **Mục đích**: Xóa giỏ hàng sau khi tạo đơn hàng thành công
- **Chức năng**: Xóa tất cả items trong cart của user

### 2. OrderController - Refactoring Method `store()`

#### Trước Khi Refactor
- Method `store()` có hơn 200 dòng code
- Chứa nhiều logic phức tạp:
  - Xử lý địa chỉ giao hàng
  - Validate voucher
  - Validate giỏ hàng
  - Tính toán giá tiền
  - Tạo đơn hàng
  - Xử lý thanh toán VNPay vs COD

#### Sau Khi Refactor
- Method `store()` chỉ còn khoảng 80 dòng code
- Logic được chia thành các bước rõ ràng:
  1. Validate request
  2. Gọi `OrderService::processOrderCreation()`
  3. Xử lý thanh toán VNPay hoặc COD
  4. Tạo QR code và gửi email
  5. Trả về response

### 3. Cập Nhật Method `createOrderWithItems()`
- Thêm field `delivery_method` vào việc tạo Order
- Đảm bảo tất cả dữ liệu cần thiết được lưu đầy đủ

## Lợi Ích Của Refactoring

### 1. **Single Responsibility Principle**
- `OrderController` chỉ tập trung vào xử lý HTTP request/response
- `OrderService` chịu trách nhiệm về business logic tạo đơn hàng

### 2. **Code Reusability**
- Các method trong `OrderService` có thể được tái sử dụng ở nhiều nơi khác
- Dễ dàng test từng method riêng biệt

### 3. **Maintainability**
- Code dễ đọc và hiểu hơn
- Dễ dàng sửa đổi logic business mà không ảnh hưởng đến controller
- Mỗi method có một chức năng cụ thể, rõ ràng

### 4. **Error Handling**
- Tập trung xử lý lỗi ở service layer
- Controller chỉ cần catch exception và trả về response phù hợp

### 5. **Testing**
- Dễ dàng viết unit test cho từng method trong service
- Mock dependencies dễ dàng hơn

## Cấu Trúc Code Sau Refactoring

```
OrderController::store()
├── Validate request
├── OrderService::processOrderCreation()
│   ├── handleDeliveryAddress()
│   ├── validateCartItems()
│   ├── calculateCartSubtotal()
│   ├── validateVoucher()
│   ├── prepareOrderData()
│   └── createOrderWithItems()
├── Handle VNPay payment
├── Handle COD payment
├── Generate QR code
├── Send email confirmation
└── Return response
```

## Các File Được Thay Đổi

1. **`app/Services/OrderService.php`**
   - Thêm 7 method mới
   - Thêm các import cần thiết
   - Cập nhật method `createOrderWithItems()`

2. **`app/Http/Controllers/OrderController.php`**
   - Refactor method `store()` từ 200+ dòng xuống ~80 dòng
   - Loại bỏ duplicate code
   - Cải thiện error handling

## Cập Nhật Mới: Chức Năng Thanh Toán Bằng Ví

### Thêm Method Mới Trong OrderService

#### `checkWalletBalance(User $user, $amount)`
- **Mục đích**: Kiểm tra số dư ví của người dùng
- **Chức năng**: Validate ví tồn tại và số dư đủ để thanh toán
- **Trả về**: Boolean hoặc throw Exception

#### `processWalletPayment(Order $order, User $user)`
- **Mục đích**: Xử lý thanh toán bằng ví điện tử
- **Chức năng**:
  - Trừ tiền từ ví người dùng
  - Tạo WalletTransaction record
  - Cập nhật trạng thái thanh toán đơn hàng
  - Sử dụng Database Transaction
- **Trả về**: Boolean

#### `processOrderCreationWithWallet($request, User $user)`
- **Mục đích**: Phiên bản mở rộng của `processOrderCreation()` với hỗ trợ ví
- **Chức năng**: Tương tự method gốc nhưng có thêm kiểm tra số dư ví
- **Trả về**: Array với thông tin bổ sung về `is_wallet_payment`

### Cập Nhật OrderController

#### Luồng Xử Lý Mới
```
OrderController::store()
├── Validate request
├── OrderService::processOrderCreationWithWallet()
├── Xử lý thanh toán ví [Mới]
│   ├── processWalletPayment()
│   ├── createPayment()
│   ├── createInvoice() [Ngay lập tức]
│   └── clearUserCart()
├── Xử lý VNPay payment
├── Xử lý COD payment
└── Return response
```

### Cải Tiến UI Checkout

- **Hiển thị số dư ví**: Thông tin realtime về số dư
- **Validation JavaScript**: Kiểm tra số dư trước khi submit
- **UX cải thiện**: Cảnh báo và vô hiệu hóa nút khi cần

## Kết Luận

Việc refactoring và mở rộng này giúp:
- Code trở nên clean và maintainable hơn
- Tuân thủ các nguyên tắc SOLID
- Dễ dàng mở rộng và bảo trì trong tương lai
- Tăng khả năng test và debug
- Giảm thiểu code duplication
- **Mới**: Hỗ trợ đầy đủ thanh toán bằng ví điện tử
- **Mới**: Cải thiện trải nghiệm người dùng với validation realtime
- **Mới**: Tự động hóa quy trình thanh toán nội bộ