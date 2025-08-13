# Thêm Màu Mè Cho Trạng Thái Đơn Hàng và Thanh Toán

## Mô tả chức năng
Thêm màu sắc phân biệt cho các trạng thái đơn hàng và trạng thái thanh toán trong trang danh sách đơn hàng và chi tiết đơn hàng, giúp người dùng dễ dàng nhận biết trạng thái hiện tại.

## Thay đổi chính

### 1. Màu sắc trạng thái đơn hàng

**Bảng màu trạng thái đơn hàng:**
- 🟡 **Chờ xác nhận**: `bg-yellow-500 text-white` (Vàng)
- 🔵 **Đã xác nhận**: `bg-blue-500 text-white` (Xanh dương)
- 🟣 **Đang chuẩn bị**: `bg-indigo-500 text-white` (Xanh tím)
- 🟪 **Đang giao hàng**: `bg-purple-500 text-white` (Tím)
- 🟢 **Đã giao/Thành công**: `bg-green-500 text-white` (Xanh lá)
- 🔴 **Đã hủy**: `bg-red-500 text-white` (Đỏ)
- ⚫ **Mặc định**: `bg-gray-500 text-white` (Xám)

### 2. Màu sắc trạng thái thanh toán

**Bảng màu trạng thái thanh toán:**
- 🟢 **Đã Thanh Toán**: `text-green-600 font-bold` (Xanh lá đậm)
- 🟡 **Chờ Thanh Toán/Chờ Xử Lý**: `text-yellow-600 font-bold` (Vàng đậm)
- 🔵 **Đang Xử Lý**: `text-blue-600 font-bold` (Xanh dương đậm)
- 🔴 **Thất Bại**: `text-red-600 font-bold` (Đỏ đậm)
- ⚫ **Chưa thanh toán**: `text-gray-600 font-bold` (Xám đậm)
- ⚫ **Mặc định**: `text-black font-bold` (Đen)

## File được thay đổi

### 1. `resources/views/clients/account/orders.blade.php`

**Thay đổi trạng thái đơn hàng chính (dòng 125-140):**
```php
@php
    $orderStatusName = $order->orderStatus->name ?? '';
    $orderStatusClass = match($orderStatusName) {
        'Chờ xác nhận' => 'bg-yellow-500 text-white',
        'Đã xác nhận' => 'bg-blue-500 text-white',
        'Đang chuẩn bị' => 'bg-indigo-500 text-white',
        'Đang giao hàng' => 'bg-purple-500 text-white',
        'Đã giao', 'Thành công' => 'bg-green-500 text-white',
        'Đã hủy' => 'bg-red-500 text-white',
        default => 'bg-gray-500 text-white'
    };
@endphp
<span class="status-badge {{ $orderStatusClass }}">
    {{ $order->orderStatus->name }}
</span>
```

**Thay đổi trạng thái đơn hàng con (dòng 182-197):**
```php
@php
    $childOrderStatusName = $childOrder->orderStatus->name ?? '';
    $childOrderStatusClass = match($childOrderStatusName) {
        'Chờ xác nhận' => 'bg-yellow-500 text-white',
        'Đã xác nhận' => 'bg-blue-500 text-white',
        'Đang chuẩn bị' => 'bg-indigo-500 text-white',
        'Đang giao hàng' => 'bg-purple-500 text-white',
        'Đã giao', 'Thành công' => 'bg-green-500 text-white',
        'Đã hủy' => 'bg-red-500 text-white',
        default => 'bg-gray-500 text-white'
    };
@endphp
<span class="status-badge {{ $childOrderStatusClass }}">
    {{ $childOrder->orderStatus->name }}
</span>
```

**Thay đổi trạng thái thanh toán (dòng 416-427):**
```php
@php
    $paymentStatusName = $order->paymentStatus->name ?? 'Chưa thanh toán';
    $paymentStatusClass = match($paymentStatusName) {
        'Đã Thanh Toán' => 'text-green-600 font-bold',
        'Chờ Thanh Toán', 'Chờ Xử Lý' => 'text-yellow-600 font-bold',
        'Đang Xử Lý' => 'text-blue-600 font-bold',
        'Thất Bại' => 'text-red-600 font-bold',
        'Chưa thanh toán' => 'text-gray-600 font-bold',
        default => 'text-black font-bold'
    };
@endphp
<span class="{{ $paymentStatusClass }}">{{ $paymentStatusName }}</span>
```

### 2. `resources/views/clients/account/order-details.blade.php`

**Thay đổi trạng thái đơn hàng (dòng 77-89):**
```php
@php
    $orderStatusName = $order->orderStatus->name ?? '';
    $orderStatusClass = match($orderStatusName) {
        'Chờ xác nhận' => 'bg-yellow-500 text-white',
        'Đã xác nhận' => 'bg-blue-500 text-white',
        'Đang chuẩn bị' => 'bg-indigo-500 text-white',
        'Đang giao hàng' => 'bg-purple-500 text-white',
        'Đã giao', 'Thành công' => 'bg-green-500 text-white',
        'Đã hủy' => 'bg-red-500 text-white',
        default => 'bg-gray-500 text-white'
    };
@endphp
<span class="status-badge {{ $orderStatusClass }}">
    {{ $order->orderStatus->name }}
</span>
```

**Thay đổi trạng thái thanh toán (dòng 220-231):**
```php
@php
    $paymentStatusName = $order->paymentStatus->name ?? 'Chưa thanh toán';
    $paymentStatusClass = match($paymentStatusName) {
        'Đã Thanh Toán' => 'text-green-600 font-bold',
        'Chờ Thanh Toán', 'Chờ Xử Lý' => 'text-yellow-600 font-bold',
        'Đang Xử Lý' => 'text-blue-600 font-bold',
        'Thất Bại' => 'text-red-600 font-bold',
        'Chưa thanh toán' => 'text-gray-600 font-bold',
        default => 'text-black font-bold'
    };
@endphp
<span class="{{ $paymentStatusClass }}">{{ $paymentStatusName }}</span>
```

## Kỹ thuật sử dụng

### PHP Match Expression
Sử dụng `match()` expression của PHP 8+ để ánh xạ tên trạng thái với class CSS tương ứng:

```php
$statusClass = match($statusName) {
    'Trạng thái 1' => 'class-1',
    'Trạng thái 2' => 'class-2',
    default => 'class-mặc-định'
};
```

### Tailwind CSS Classes
- **Background colors**: `bg-{color}-{intensity}` (ví dụ: `bg-green-500`)
- **Text colors**: `text-{color}-{intensity}` (ví dụ: `text-green-600`)
- **Font weight**: `font-bold`
- **Text color**: `text-white`, `text-black`

## Lợi ích

### 1. Trải nghiệm người dùng tốt hơn
- **Nhận biết nhanh**: Màu sắc giúp người dùng nhận biết trạng thái ngay lập tức
- **Trực quan**: Không cần đọc text, chỉ cần nhìn màu đã biết trạng thái
- **Phân biệt rõ ràng**: Mỗi trạng thái có màu riêng biệt

### 2. Giao diện chuyên nghiệp
- **Thống nhất**: Màu sắc nhất quán trên toàn hệ thống
- **Hiện đại**: Sử dụng màu sắc theo xu hướng UI/UX
- **Dễ bảo trì**: Logic màu sắc tập trung, dễ thay đổi

### 3. Accessibility
- **Contrast tốt**: Màu nền và chữ có độ tương phản cao
- **Semantic colors**: Màu sắc có ý nghĩa (đỏ = lỗi, xanh = thành công)

## Cách kiểm tra

### 1. Trang danh sách đơn hàng
1. Truy cập `/account/orders`
2. Kiểm tra màu sắc trạng thái đơn hàng trong header
3. Kiểm tra màu sắc trạng thái thanh toán trong thông tin đơn hàng
4. Kiểm tra màu sắc cho đơn hàng con (nếu có)

### 2. Trang chi tiết đơn hàng
1. Click vào một đơn hàng để xem chi tiết
2. Kiểm tra màu sắc trạng thái đơn hàng trong header
3. Kiểm tra màu sắc trạng thái thanh toán trong thông tin chi tiết

### 3. Test các trạng thái khác nhau
- Tạo đơn hàng mới (Chờ xác nhận - Vàng)
- Xác nhận đơn hàng (Đã xác nhận - Xanh dương)
- Chuẩn bị hàng (Đang chuẩn bị - Xanh tím)
- Giao hàng (Đang giao hàng - Tím)
- Hoàn thành (Thành công - Xanh lá)
- Hủy đơn (Đã hủy - Đỏ)

## Ghi chú kỹ thuật

### Tương thích
- **PHP**: Yêu cầu PHP 8.0+ cho `match()` expression
- **Tailwind CSS**: Sử dụng các class có sẵn trong Tailwind
- **Browser**: Tương thích với tất cả browser hiện đại

### Performance
- **Minimal impact**: Chỉ thêm logic PHP đơn giản
- **No JavaScript**: Không cần JavaScript, render server-side
- **CSS efficient**: Sử dụng utility classes có sẵn

### Maintainability
- **Centralized logic**: Logic màu sắc tập trung trong từng file
- **Easy to extend**: Dễ dàng thêm trạng thái mới
- **Consistent**: Đảm bảo tính nhất quán trong toàn hệ thống