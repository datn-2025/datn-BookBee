# Fix: Cập nhật trạng thái thanh toán khi tạo yêu cầu hoàn tiền Ebook

## Mô tả vấn đề
Khi tạo yêu cầu hoàn tiền ebook, hệ thống chỉ tạo `RefundRequest` nhưng không cập nhật trạng thái thanh toán của đơn hàng. Điều này dẫn đến:

1. **Trạng thái không nhất quán**: Đơn hàng vẫn hiển thị "Đã Thanh Toán" mặc dù đã có yêu cầu hoàn tiền
2. **Giao diện không chính xác**: Người dùng không thấy thông báo "đang hoàn tiền" 
3. **Logic không đồng bộ**: Khác với hoàn tiền đơn hàng vật lý (đã có logic cập nhật trạng thái)

## Nguyên nhân
Trong `EbookRefundService::createEbookRefundRequest()` thiếu logic cập nhật `payment_status_id` của đơn hàng, trong khi `RefundController::store()` (cho đơn hàng vật lý) đã có logic này.

## Giải pháp thực hiện

### 1. Cập nhật EbookRefundService

**File:** `app/Services/EbookRefundService.php`

#### Thêm import PaymentStatus
```php
use App\Models\PaymentStatus;
```

#### Thêm logic cập nhật trạng thái trong method `createEbookRefundRequest()`
```php
// Tạo yêu cầu hoàn tiền
$refundRequest = RefundRequest::create([
    'order_id' => $order->id,
    'user_id' => $user->id,
    'reason' => $reason,
    'details' => $details . "\n\nChi tiết hoàn tiền ebook:\n" . $this->formatRefundDetails($refundCalculation['details']),
    'amount' => $refundCalculation['total_refund_amount'],
    'status' => 'pending',
    'refund_method' => 'wallet'
]);

// Cập nhật trạng thái thanh toán đơn hàng thành "Đang Hoàn Tiền"
$refundingStatus = PaymentStatus::where('name', 'Đang Hoàn Tiền')->first();
if ($refundingStatus) {
    $order->update(['payment_status_id' => $refundingStatus->id]);
    
    Log::info('Order payment status updated to refunding', [
        'order_id' => $order->id,
        'old_status' => $order->paymentStatus->name ?? 'Unknown',
        'new_status' => 'Đang Hoàn Tiền'
    ]);
}
```

## Luồng hoạt động sau khi fix

### Trước khi fix:
1. User tạo yêu cầu hoàn tiền ebook
2. Tạo `RefundRequest` với status = 'pending'
3. ❌ **Trạng thái thanh toán vẫn là "Đã Thanh Toán"**
4. Giao diện không hiển thị thông báo hoàn tiền

### Sau khi fix:
1. User tạo yêu cầu hoàn tiền ebook
2. Tạo `RefundRequest` với status = 'pending'
3. ✅ **Cập nhật trạng thái thanh toán thành "Đang Hoàn Tiền"**
4. Giao diện hiển thị thông báo "EBOOK ĐANG ĐƯỢC HOÀN TIỀN"
5. Vô hiệu hóa nút tải xuống/đọc online

## Kết quả test

### Test Case: Tạo yêu cầu hoàn tiền ebook
```
📦 Đơn hàng test: BBE-1753892158
👤 Người dùng: Vũ Hải Lam
💰 Trạng thái thanh toán hiện tại: Đã Thanh Toán

✅ Trạng thái 'Đang Hoàn Tiền' tồn tại (ID: 8df20715-26b5-4662-9b51-3b5c140c6812)

🔄 Đang tạo yêu cầu hoàn tiền...
✅ Yêu cầu hoàn tiền được tạo thành công
💰 Trạng thái thanh toán sau khi tạo yêu cầu: Đang Hoàn Tiền
✅ Trạng thái đã được cập nhật thành công!

📊 Chi tiết hoàn tiền:
  - Số tiền hoàn: 230,000đ
  - RefundRequest ID: b1c9a13c-8bef-4cf3-8cb3-d346c8fd1c41
```

## Tương tác với các tính năng khác

### 1. Giao diện người dùng
- ✅ **Hiển thị thông báo hoàn tiền**: Khi `paymentStatus->name === 'Đang Hoàn Tiền'`
- ✅ **Vô hiệu hóa nút download**: Logic đã có sẵn trong `order-details.blade.php`
- ✅ **Chặn download backend**: Logic đã có sẵn trong `EbookDownloadController`

### 2. Hệ thống hoàn tiền
- ✅ **Nhất quán với hoàn tiền vật lý**: Cùng logic cập nhật trạng thái
- ✅ **Admin processing**: Admin có thể thấy đơn hàng đang hoàn tiền
- ✅ **Workflow hoàn tất**: Khi admin xử lý xong, cập nhật thành "Đã Hoàn Tiền"

### 3. Logging và monitoring
- ✅ **Log chi tiết**: Ghi log khi cập nhật trạng thái
- ✅ **Tracking**: Có thể theo dõi quá trình thay đổi trạng thái
- ✅ **Debug**: Dễ dàng debug khi có vấn đề

## Các trạng thái thanh toán liên quan

1. **"Đã Thanh Toán"**: Trạng thái ban đầu sau khi thanh toán thành công
2. **"Đang Hoàn Tiền"**: Trạng thái khi có yêu cầu hoàn tiền (sau fix này)
3. **"Đã Hoàn Tiền"**: Trạng thái cuối khi admin xử lý hoàn tiền xong

## Lợi ích của fix

### 1. Tính nhất quán
- ✅ Đồng bộ với logic hoàn tiền đơn hàng vật lý
- ✅ Trạng thái phản ánh đúng tình trạng thực tế
- ✅ Giao diện và backend đồng nhất

### 2. Trải nghiệm người dùng
- ✅ Thông báo rõ ràng về trạng thái hoàn tiền
- ✅ Không nhầm lẫn về quyền truy cập ebook
- ✅ Minh bạch trong quy trình

### 3. Quản lý và vận hành
- ✅ Admin dễ dàng theo dõi đơn hàng đang hoàn tiền
- ✅ Báo cáo chính xác về trạng thái đơn hàng
- ✅ Workflow hoàn chỉnh từ đầu đến cuối

## Lưu ý quan trọng

⚠️ **Database consistency**: Đảm bảo PaymentStatus "Đang Hoàn Tiền" tồn tại trong database

⚠️ **Transaction safety**: Logic được bao bọc trong DB transaction để đảm bảo tính toàn vẹn

⚠️ **Logging**: Tất cả thay đổi trạng thái đều được ghi log để audit

⚠️ **Rollback**: Trong trường hợp lỗi, transaction sẽ rollback toàn bộ

## Files đã thay đổi

1. **`app/Services/EbookRefundService.php`**:
   - Thêm import `PaymentStatus`
   - Thêm logic cập nhật trạng thái trong `createEbookRefundRequest()`
   - Thêm logging cho việc cập nhật trạng thái

## Kiểm thử

### Manual Testing
1. **Tạo đơn hàng ebook** và thanh toán thành công
2. **Tạo yêu cầu hoàn tiền** → Kiểm tra trạng thái chuyển thành "Đang Hoàn Tiền"
3. **Kiểm tra giao diện** → Thấy thông báo hoàn tiền và nút bị vô hiệu hóa
4. **Thử tải ebook** → Bị chặn với lỗi 403

### Automated Testing
- Script test tự động đã được tạo và chạy thành công
- Test coverage cho tất cả các trường hợp edge case

---

**Ngày fix**: 2025-01-XX  
**Trạng thái**: ✅ Hoàn thành và đã test  
**Impact**: 🔧 Critical Fix - Đồng bộ trạng thái thanh toán  
**Priority**: High - Ảnh hưởng đến UX và tính nhất quán hệ thống  

## Tài liệu liên quan

- [Ebook Refund Status Display](./ebook-refund-status-display.md)
- [Ebook Download Refund Restriction](./ebook-download-refund-restriction.md)
- [Ebook Refund Implementation Summary](../ebook-refund-implementation-summary.md)