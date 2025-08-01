# Hiển thị trạng thái hoàn tiền Ebook cho người dùng

## Mô tả vấn đề
Trước đây, khi đơn hàng ebook đang trong quá trình hoàn tiền hoặc đã được hoàn tiền, người dùng vẫn có thể thấy các nút "Đọc Online" và "Tải Xuống" mà không biết rằng ebook đã không còn khả dụng do đã được hoàn tiền.

## Giải pháp thực hiện

### 1. Thêm thông báo trạng thái hoàn tiền

**File:** `resources/views/clients/account/order-details.blade.php`

Thêm logic hiển thị thông báo trạng thái hoàn tiền trong phần "TẢI EBOOK":

#### Trạng thái "Đang Hoàn Tiền"
- Hiển thị thông báo màu vàng với icon đồng hồ
- Tiêu đề: "EBOOK ĐANG ĐƯỢC HOÀN TIỀN"
- Nội dung: "Yêu cầu hoàn tiền ebook của bạn đang được xử lý. Trong thời gian này, bạn không thể tải xuống hoặc đọc ebook."

#### Trạng thái "Đã Hoàn Tiền"
- Hiển thị thông báo màu đỏ với icon X
- Tiêu đề: "EBOOK ĐÃ ĐƯỢC HOÀN TIỀN"
- Nội dung: "Ebook đã được hoàn tiền thành công. Bạn không còn quyền truy cập vào nội dung này."

### 2. Vô hiệu hóa nút tải xuống và đọc online

**Thay đổi cho cả hai trường hợp:**
- Ebook mua trực tiếp
- Ebook kèm theo sách vật lý

**Logic:**
```php
@if(in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền']))
    <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-400 text-white font-bold uppercase tracking-wide cursor-not-allowed">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
        </svg>
        Không khả dụng
    </span>
@else
    {{-- Hiển thị nút bình thường --}}
@endif
```

## Các trạng thái được kiểm tra

1. **Đang Hoàn Tiền** (`$order->paymentStatus->name === 'Đang Hoàn Tiền'`)
   - Hiển thị thông báo màu vàng
   - Vô hiệu hóa nút tải xuống/đọc online
   - Thông báo rằng ebook đang được xử lý hoàn tiền

2. **Đã Hoàn Tiền** (`$order->paymentStatus->name === 'Đã Hoàn Tiền'`)
   - Hiển thị thông báo màu đỏ
   - Vô hiệu hóa nút tải xuống/đọc online
   - Thông báo rằng ebook đã được hoàn tiền và không còn quyền truy cập

## Luồng hoạt động

1. **Trạng thái bình thường:**
   - Hiển thị nút "Đọc Online" và "Tải Xuống"
   - Không có thông báo đặc biệt

2. **Khi yêu cầu hoàn tiền được tạo:**
   - Trạng thái thanh toán chuyển thành "Đang Hoàn Tiền"
   - Hiển thị thông báo màu vàng
   - Thay thế nút bằng "Không khả dụng"

3. **Khi hoàn tiền hoàn tất:**
   - Trạng thái thanh toán chuyển thành "Đã Hoàn Tiền"
   - Hiển thị thông báo màu đỏ
   - Tiếp tục hiển thị "Không khả dụng"

## Lợi ích

1. **Trải nghiệm người dùng tốt hơn:**
   - Người dùng biết rõ trạng thái đơn hàng
   - Không bị nhầm lẫn về khả năng truy cập ebook

2. **Tính nhất quán:**
   - Giao diện phản ánh đúng logic backend
   - Đồng bộ với việc chặn download trong `EbookDownloadController`

3. **Bảo vệ bản quyền:**
   - Ngăn chặn truy cập không hợp lệ
   - Rõ ràng về quyền sở hữu nội dung

## Tương tác với các tính năng khác

- **EbookDownloadController:** Đã được cập nhật để chặn download khi đơn hàng đang/đã hoàn tiền
- **EbookRefundService:** Xử lý logic hoàn tiền và cập nhật trạng thái
- **Order Status Management:** Đồng bộ với hệ thống quản lý trạng thái đơn hàng

## Ghi chú quan trọng

1. **Kiểm tra trạng thái:** Sử dụng `paymentStatus->name` thay vì `orderStatus->name` để kiểm tra trạng thái hoàn tiền
2. **Responsive Design:** Thông báo được thiết kế responsive với Tailwind CSS
3. **Accessibility:** Sử dụng cursor-not-allowed và màu sắc phù hợp cho trạng thái disabled
4. **Icon phù hợp:** Sử dụng icon đồng hồ cho "đang xử lý" và icon X cho "đã hoàn tiền"

## Kiểm thử

1. **Tạo đơn hàng ebook**
2. **Yêu cầu hoàn tiền** → Kiểm tra hiển thị thông báo màu vàng
3. **Admin xử lý hoàn tiền** → Kiểm tra hiển thị thông báo màu đỏ
4. **Thử click vào nút "Không khả dụng"** → Đảm bảo không có hành động nào xảy ra

## Tài liệu liên quan

- [Ebook Download Refund Restriction](./ebook-download-refund-restriction.md)
- [Ebook DRM Refund System](../ebook-drm-refund-system.md)
- [Ebook Refund Implementation Summary](../ebook-refund-implementation-summary.md)