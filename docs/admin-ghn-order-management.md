# Quản lý đơn hàng GHN cho Admin

## Mô tả chức năng
Chức năng quản lý đơn hàng GHN cho phép admin tạo, theo dõi và hủy đơn hàng vận chuyển thông qua hệ thống GHN (Giao Hàng Nhanh).

## Các tính năng chính

### 1. Tạo đơn hàng GHN
- **Điều kiện**: Đơn hàng phải có `delivery_method = 'delivery'` và chưa có mã vận đơn GHN
- **Chức năng**: Tạo đơn hàng vận chuyển trên hệ thống GHN và lưu mã vận đơn
- **Route**: `admin.orders.create-ghn-order`

### 2. Cập nhật thông tin theo dõi
- **Điều kiện**: Đơn hàng đã có mã vận đơn GHN
- **Chức năng**: Lấy thông tin mới nhất từ GHN và cập nhật vào database
- **Route**: `admin.orders.update-ghn-tracking`

### 3. Hủy liên kết đơn hàng GHN
- **Điều kiện**: Đơn hàng đã có mã vận đơn GHN
- **Chức năng**: Xóa thông tin GHN khỏi đơn hàng (không hủy đơn trên GHN)
- **Route**: `admin.orders.cancel-ghn-order`

## Cách sử dụng

### Trong giao diện Admin

1. **Truy cập chi tiết đơn hàng**:
   - Vào "Quản lý đơn hàng" > Chọn đơn hàng cần xử lý
   - Xem thông tin GHN ở phần "Thông tin vận chuyển GHN"

2. **Tạo đơn hàng GHN**:
   - Click nút "Tạo đơn hàng GHN" (chỉ hiện khi chưa có mã vận đơn)
   - Hệ thống sẽ tự động gửi thông tin đến GHN và lưu mã vận đơn

3. **Cập nhật thông tin theo dõi**:
   - Click nút "Cập nhật theo dõi" (chỉ hiện khi đã có mã vận đơn)
   - Hệ thống sẽ lấy thông tin mới nhất từ GHN

4. **Hủy liên kết GHN**:
   - Click nút "Hủy GHN" để xóa thông tin GHN khỏi đơn hàng

## Mã nguồn

### Controller Methods

```php
// Tạo đơn hàng GHN
public function createGhnOrder($id)
{
    try {
        $order = Order::with(['address', 'orderItems.book'])->findOrFail($id);
        
        // Kiểm tra điều kiện
        if ($order->delivery_method !== 'delivery') {
            Toastr::error('Chỉ có thể tạo đơn GHN cho đơn hàng giao hàng tận nơi', 'Lỗi');
            return back();
        }
        
        if ($order->ghn_order_code) {
            Toastr::error('Đơn hàng đã có mã vận đơn GHN', 'Lỗi');
            return back();
        }
        
        // Tạo đơn hàng GHN
        $result = $this->orderService->createGhnOrder($order);
        
        if ($result && isset($result['order_code'])) {
            Toastr::success('Tạo đơn hàng GHN thành công. Mã vận đơn: ' . $result['order_code'], 'Thành công');
        } else {
            Toastr::error('Không thể tạo đơn hàng GHN. Vui lòng kiểm tra thông tin địa chỉ và thử lại.', 'Lỗi');
        }
        
        return back();
    } catch (\Exception $e) {
        Log::error('Error creating GHN order: ' . $e->getMessage());
        Toastr::error('Có lỗi xảy ra khi tạo đơn hàng GHN', 'Lỗi');
        return back();
    }
}

// Cập nhật thông tin theo dõi
public function updateGhnTracking($id)
{
    try {
        $order = Order::findOrFail($id);
        
        if (!$order->ghn_order_code) {
            Toastr::error('Đơn hàng chưa có mã vận đơn GHN', 'Lỗi');
            return back();
        }
        
        // Lấy thông tin theo dõi từ GHN
        $trackingData = $this->ghnService->getOrderDetail($order->ghn_order_code);
        
        if ($trackingData) {
            // Cập nhật thông tin theo dõi
            $order->update([
                'ghn_tracking_data' => $trackingData,
                'updated_at' => now()
            ]);
            
            Toastr::success('Cập nhật thông tin theo dõi thành công', 'Thành công');
        } else {
            Toastr::error('Không thể lấy thông tin theo dõi từ GHN', 'Lỗi');
        }
        
        return back();
    } catch (\Exception $e) {
        Log::error('Error updating GHN tracking: ' . $e->getMessage());
        Toastr::error('Có lỗi xảy ra khi cập nhật thông tin theo dõi', 'Lỗi');
        return back();
    }
}

// Hủy liên kết đơn hàng GHN
public function cancelGhnOrder($id)
{
    try {
        $order = Order::findOrFail($id);
        
        if (!$order->ghn_order_code) {
            Toastr::error('Đơn hàng chưa có mã vận đơn GHN', 'Lỗi');
            return back();
        }
        
        // Xóa thông tin GHN khỏi đơn hàng
        $order->update([
            'ghn_order_code' => null,
            'ghn_service_type_id' => null,
            'expected_delivery_date' => null,
            'ghn_tracking_data' => null
        ]);
        
        Toastr::success('Đã hủy liên kết với đơn hàng GHN', 'Thành công');
        return back();
    } catch (\Exception $e) {
        Log::error('Error canceling GHN order: ' . $e->getMessage());
        Toastr::error('Có lỗi xảy ra khi hủy đơn hàng GHN', 'Lỗi');
        return back();
    }
}
```

### Routes cần thiết

```php
// Trong routes/web.php (admin routes group)
Route::post('/orders/{id}/create-ghn-order', [OrderController::class, 'createGhnOrder'])->name('orders.create-ghn-order');
Route::post('/orders/{id}/update-ghn-tracking', [OrderController::class, 'updateGhnTracking'])->name('orders.update-ghn-tracking');
Route::post('/orders/{id}/cancel-ghn-order', [OrderController::class, 'cancelGhnOrder'])->name('orders.cancel-ghn-order');
```

## Xử lý lỗi

### Các lỗi thường gặp

1. **"Chỉ có thể tạo đơn GHN cho đơn hàng giao hàng tận nơi"**
   - **Nguyên nhân**: Đơn hàng có `delivery_method` khác 'delivery'
   - **Giải pháp**: Chỉ tạo đơn GHN cho đơn hàng giao hàng

2. **"Đơn hàng đã có mã vận đơn GHN"**
   - **Nguyên nhân**: Đơn hàng đã được tạo trên GHN trước đó
   - **Giải pháp**: Sử dụng chức năng cập nhật thông tin thay vì tạo mới

3. **"Không thể tạo đơn hàng GHN"**
   - **Nguyên nhân**: Thiếu thông tin địa chỉ GHN (district_id, ward_code) hoặc lỗi API
   - **Giải pháp**: Kiểm tra và cập nhật thông tin địa chỉ đầy đủ

4. **"Đơn hàng chưa có mã vận đơn GHN"**
   - **Nguyên nhân**: Cố gắng cập nhật/hủy đơn chưa được tạo trên GHN
   - **Giải pháp**: Tạo đơn GHN trước khi thực hiện các thao tác khác

## Lưu ý quan trọng

1. **Thông tin địa chỉ**: Đảm bảo địa chỉ giao hàng có đầy đủ `district_id` và `ward_code` từ GHN
2. **Trạng thái đơn hàng**: Chỉ tạo đơn GHN cho đơn hàng đã được xác nhận
3. **Xử lý lỗi**: Luôn có fallback và thông báo lỗi rõ ràng cho admin
4. **Log**: Tất cả thao tác đều được ghi log để debug khi cần thiết

## Kết quả mong muốn

- Admin có thể dễ dàng quản lý đơn hàng GHN từ giao diện admin
- Thông tin vận chuyển được đồng bộ giữa hệ thống và GHN
- Xử lý lỗi mượt mà, không làm crash hệ thống
- Thông báo rõ ràng cho admin về kết quả thao tác