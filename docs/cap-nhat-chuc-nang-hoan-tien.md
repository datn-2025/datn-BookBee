# Cập Nhật Chức Năng Hoàn Tiền - Hệ Thống Quản Lý Đơn Hàng

## Tổng Quan
Tài liệu này mô tả chi tiết việc cập nhật chức năng hoàn tiền trong hệ thống quản lý đơn hàng, đảm bảo luồng xử lý đúng với các trạng thái đơn hàng và thanh toán.

## Vấn Đề Trước Khi Cập Nhật

### 1. Thiếu Logic Cập Nhật Trạng Thái
- Khi tạo yêu cầu hoàn tiền, trạng thái đơn hàng không được cập nhật thành "Đang Hoàn Tiền"
- Sau khi hoàn tiền xong, trạng thái không được cập nhật thành "Đã Hoàn Tiền"
- Người dùng vẫn có thể tạo nhiều yêu cầu hoàn tiền cho cùng một đơn hàng

### 2. Hiển thị Giao Diện Không Chính Xác
- Nút "Yêu cầu hoàn tiền" vẫn hiển thị ngay cả khi đơn hàng đã trong trạng thái hoàn tiền
- Không có thông báo rõ ràng về trạng thái hoàn tiền cho người dùng
- Logic kiểm tra điều kiện hoàn tiền chưa đầy đủ

## Giải Pháp Đã Triển Khai

### 1. Cập Nhật Client RefundController

#### File: `app/Http/Controllers/Client/RefundController.php`

**Thay đổi chính:**
- Thêm logic cập nhật trạng thái thanh toán thành "Đang Hoàn Tiền" khi tạo yêu cầu hoàn tiền
- Cải thiện điều kiện kiểm tra eligibility cho hoàn tiền

```php
// Cập nhật trạng thái thanh toán đơn hàng thành "Đang Hoàn Tiền"
$refundingStatus = \App\Models\PaymentStatus::where('name', 'Đang Hoàn Tiền')->first();
if ($refundingStatus) {
    $order->update(['payment_status_id' => $refundingStatus->id]);
}
```

**Điều kiện kiểm tra hoàn tiền:**
- Đơn hàng phải có trạng thái "Thành công"
- Trạng thái thanh toán phải là "Đã Thanh Toán"
- Không được trong trạng thái "Đang Hoàn Tiền" hoặc "Đã Hoàn Tiền"
- Không có yêu cầu hoàn tiền đang pending hoặc processing

### 2. Cập Nhật Admin OrderController

#### File: `app/Http/Controllers/Admin/OrderController.php`

**Thay đổi chính:**
- Thêm dependency injection cho `PaymentRefundService`
- Sử dụng service chuyên biệt để xử lý hoàn tiền
- Loại bỏ logic cập nhật trạng thái trùng lặp
- Cải thiện error handling

```php
protected $paymentRefundService;

public function __construct(OrderService $orderService, PaymentRefundService $paymentRefundService)
{
    $this->orderService = $orderService;
    $this->paymentRefundService = $paymentRefundService;
}
```

**Xử lý hoàn tiền:**
```php
if ($refund->refund_method === 'wallet') {
    $result = $this->paymentRefundService->refundToWallet($order, $refund->amount, $refund);
} elseif ($refund->refund_method === 'vnpay') {
    $result = $this->paymentRefundService->refundVnpay($order, $refund);
}
```

### 3. Sửa Lỗi PaymentRefundService

#### File: `app/Services/PaymentRefundService.php`

**Lỗi đã sửa:**
- Sửa lỗi cú pháp trong câu lệnh `where()` thiếu tham số
- Loại bỏ logic cập nhật order status không cần thiết
- Đảm bảo chỉ cập nhật payment status

```php
// Trước (có lỗi)
$orderStatus = OrderStatus::where('Đã hoàn tiền')->first();

// Sau (đã sửa)
$refundedStatus = PaymentStatus::where('name', 'Đã Hoàn Tiền')->first();
if ($refundedStatus) {
    $order->update(['payment_status_id' => $refundedStatus->id]);
}
```

### 4. Cập Nhật Giao Diện Client

#### File: `resources/views/clients/account/order-details.blade.php`

**Cải thiện hiển thị:**

1. **Logic hiển thị nút hoàn tiền:**
   - Chỉ hiển thị khi đơn hàng "Thành công" và "Đã Thanh Toán"
   - Ẩn nút khi đã có yêu cầu hoàn tiền

2. **Thông báo trạng thái hoàn tiền:**
   - **Đang Hoàn Tiền:** Hiển thị thông báo màu vàng với icon đồng hồ
   - **Đã Hoàn Tiền:** Hiển thị thông báo màu xanh với icon check

3. **Nút xem chi tiết:**
   - Cho phép người dùng xem chi tiết yêu cầu hoàn tiền
   - Liên kết đến trang trạng thái hoàn tiền

```php
{{-- Hiển thị thông báo khi đơn hàng đang hoàn tiền --}}
@if($order->orderStatus->name === 'Thành công' && in_array($order->paymentStatus->name, ['Đang Hoàn Tiền']))
    <div class="bg-yellow-50 border-2 border-yellow-200 p-6">
        <h3 class="text-base font-bold text-yellow-800 uppercase tracking-wide">
            ĐANG XỬ LÝ HOÀN TIỀN
        </h3>
        <p class="text-sm text-yellow-700 mt-1">
            Yêu cầu hoàn tiền của bạn đang được xử lý. Chúng tôi sẽ thông báo khi có kết quả.
        </p>
    </div>
@endif

{{-- Hiển thị thông báo khi đơn hàng đã hoàn tiền thành công --}}
@if($order->orderStatus->name === 'Thành công' && $order->paymentStatus->name === 'Đã Hoàn Tiền')
    <div class="bg-green-50 border-2 border-green-200 p-6">
        <h3 class="text-base font-bold text-green-800 uppercase tracking-wide">
            ĐÃ HOÀN TIỀN THÀNH CÔNG
        </h3>
        <p class="text-sm text-green-700 mt-1">
            Tiền đã được hoàn về tài khoản của bạn thành công.
        </p>
    </div>
@endif
```

## Luồng Hoạt Động Mới

### 1. Tạo Yêu Cầu Hoàn Tiền (Client)
```
1. Người dùng truy cập chi tiết đơn hàng
2. Kiểm tra điều kiện:
   - Đơn hàng: "Thành công"
   - Thanh toán: "Đã Thanh Toán"
   - Chưa có yêu cầu hoàn tiền pending/processing
3. Hiển thị nút "Yêu cầu hoàn tiền"
4. Người dùng điền form yêu cầu hoàn tiền
5. Hệ thống:
   - Tạo RefundRequest với status "pending"
   - Cập nhật payment_status thành "Đang Hoàn Tiền"
   - Ẩn nút "Yêu cầu hoàn tiền"
   - Hiển thị thông báo "Đang xử lý hoàn tiền"
```

### 2. Xử Lý Hoàn Tiền (Admin)
```
1. Admin xem danh sách yêu cầu hoàn tiền
2. Chọn yêu cầu cần xử lý
3. Quyết định approve/reject:
   
   Nếu APPROVE:
   - Gọi PaymentRefundService.refundToWallet() hoặc refundVnpay()
   - Service tự động cập nhật payment_status thành "Đã Hoàn Tiền"
   - Cập nhật RefundRequest status thành "completed"
   - Gửi email thông báo
   
   Nếu REJECT:
   - Cập nhật RefundRequest status thành "rejected"
   - Cập nhật payment_status về "Đã Thanh Toán"
   - Gửi email thông báo
```

### 3. Hiển Thị Trạng Thái (Client)
```
1. Đang Hoàn Tiền:
   - Hiển thị thông báo màu vàng
   - Nút "Xem chi tiết hoàn tiền"
   - Ẩn nút "Yêu cầu hoàn tiền"

2. Đã Hoàn Tiền:
   - Hiển thị thông báo màu xanh
   - Nút "Xem chi tiết hoàn tiền"
   - Ẩn nút "Yêu cầu hoàn tiền"

3. Đã Thanh Toán (chưa hoàn tiền):
   - Hiển thị nút "Yêu cầu hoàn tiền"
   - Ẩn thông báo trạng thái hoàn tiền
```

## Cấu Trúc Database

### Bảng `payment_statuses`
```sql
- 'Chờ Xử Lý'
- 'Chưa thanh toán' 
- 'Đã Thanh Toán'
- 'Thất Bại'
- 'Đang Hoàn Tiền'  ← Trạng thái mới được sử dụng
- 'Đã Hoàn Tiền'   ← Trạng thái mới được sử dụng
```

### Bảng `refund_requests`
```sql
- status: ['pending', 'processing', 'completed', 'rejected']
- refund_method: ['vnpay', 'wallet']
- amount: decimal(10,2)
- reason: enum
- details: text
```

## Tính Năng Mới

### 1. Bảo Mật
- Kiểm tra ownership của đơn hàng
- Validation điều kiện hoàn tiền nghiêm ngặt
- Ngăn chặn tạo multiple yêu cầu hoàn tiền

### 2. Trải Nghiệm Người Dùng
- Thông báo trạng thái rõ ràng với màu sắc phân biệt
- Ẩn/hiện nút phù hợp với trạng thái
- Link xem chi tiết hoàn tiền

### 3. Quản Lý Admin
- Sử dụng service chuyên biệt
- Error handling tốt hơn
- Logging chi tiết

## Lưu Ý Kỹ Thuật

### 1. Database Transaction
- Tất cả thao tác hoàn tiền được wrap trong DB transaction
- Rollback tự động khi có lỗi

### 2. Service Layer
- PaymentRefundService xử lý logic hoàn tiền
- Tách biệt logic business khỏi controller
- Dễ dàng test và maintain

### 3. Error Handling
- Try-catch blocks cho tất cả thao tác quan trọng
- Logging chi tiết cho debugging
- User-friendly error messages

## Kiểm Thử

### 1. Test Cases Cần Kiểm Tra

**Tạo yêu cầu hoàn tiền:**
- ✅ Đơn hàng "Thành công" + "Đã Thanh Toán" → Cho phép tạo yêu cầu
- ✅ Đơn hàng "Đang Hoàn Tiền" → Không cho phép tạo yêu cầu mới
- ✅ Đơn hàng "Đã Hoàn Tiền" → Không cho phép tạo yêu cầu mới
- ✅ Đơn hàng có yêu cầu pending → Không cho phép tạo yêu cầu mới

**Xử lý hoàn tiền (Admin):**
- ✅ Approve yêu cầu → Cập nhật trạng thái thành "Đã Hoàn Tiền"
- ✅ Reject yêu cầu → Giữ nguyên trạng thái "Đã Thanh Toán"
- ✅ Hoàn tiền vào ví → Cộng tiền vào wallet
- ✅ Hoàn tiền VNPay → Gọi API VNPay

**Hiển thị giao diện:**
- ✅ "Đã Thanh Toán" → Hiển thị nút "Yêu cầu hoàn tiền"
- ✅ "Đang Hoàn Tiền" → Hiển thị thông báo màu vàng
- ✅ "Đã Hoàn Tiền" → Hiển thị thông báo màu xanh
- ✅ Có yêu cầu pending → Hiển thị nút "Xem trạng thái"

### 2. Regression Testing
- Kiểm tra các chức năng đặt hàng không bị ảnh hưởng
- Kiểm tra thanh toán bình thường
- Kiểm tra các trạng thái đơn hàng khác

## Kết Luận

Việc cập nhật chức năng hoàn tiền đã giải quyết được:

1. **Vấn đề trạng thái:** Đơn hàng được cập nhật trạng thái đúng theo luồng hoàn tiền
2. **Vấn đề UX:** Người dùng có thông tin rõ ràng về trạng thái hoàn tiền
3. **Vấn đề bảo mật:** Ngăn chặn tạo multiple yêu cầu hoàn tiền
4. **Vấn đề maintainability:** Code được tổ chức tốt hơn với service layer

Hệ thống hiện tại đã sẵn sàng để handle luồng hoàn tiền một cách chính xác và user-friendly.

---

**Ngày cập nhật:** {{ date('d/m/Y') }}  
**Phiên bản:** 1.0  
**Tác giả:** Development Team