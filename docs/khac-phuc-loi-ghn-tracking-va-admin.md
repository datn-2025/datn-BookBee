# Khắc Phục Lỗi GHN Tracking và Chức Năng Admin

## Vấn Đề Đã Được Khắc Phục

### 1. Lỗi "Lỗi khi tải thông tin theo dõi" trong Client

#### Nguyên Nhân
- **API Route Parameter Mismatch**: Phương thức `trackOrder` trong `GhnController` đang validate `order_code` từ request body, nhưng JavaScript gọi GET request với `orderCode` trong URL path.
- **Route Definition**: Route được định nghĩa là `GET /api/ghn/tracking/{orderCode}` nhưng controller method không nhận parameter từ URL.

#### Giải Pháp Đã Triển Khai
**File**: `app/Http/Controllers/GhnController.php`

**Trước đây (SAI)**:
```php
public function trackOrder(Request $request): JsonResponse
{
    $request->validate([
        'order_code' => 'required|string'
    ]);

    try {
        $orderDetail = $this->ghnService->getOrderDetail($request->order_code);
        // ...
    }
}
```

**Sau khi sửa (ĐÚNG)**:
```php
public function trackOrder($orderCode): JsonResponse
{
    try {
        $orderDetail = $this->ghnService->getOrderDetail($orderCode);
        // ...
    }
}
```

### 2. Lỗi Route Names Không Khớp trong Admin

#### Nguyên Nhân
- **Route Names Mismatch**: View admin đang gọi `admin.orders.ghn.update-tracking` và `admin.orders.ghn.cancel` nhưng routes được định nghĩa với names khác.

#### Giải Pháp Đã Triển Khai
**File**: `routes/web.php`

**Cập nhật route names trong admin orders group**:
```php
// GHN routes
Route::post('/{id}/ghn/create', [OrderController::class, 'createGhnOrder'])->name('orders.ghn.create');
Route::post('/{id}/ghn/update-tracking', [OrderController::class, 'updateGhnTracking'])->name('orders.ghn.update-tracking');
Route::post('/{id}/ghn/cancel', [OrderController::class, 'cancelGhnOrder'])->name('orders.ghn.cancel');
```

**Kết quả**: Với prefix `admin.` từ route group, route names sẽ là:
- `admin.orders.ghn.create`
- `admin.orders.ghn.update-tracking`
- `admin.orders.ghn.cancel`

## Chức Năng GHN trong Admin

### 1. Cập Nhật Theo Dõi

#### Mục Đích
- Lấy thông tin mới nhất từ GHN API
- Cập nhật trạng thái vận chuyển vào database
- Hiển thị thông tin tracking cho admin

#### Cách Sử Dụng
1. **Truy cập**: Admin Panel > Quản lý đơn hàng > Chi tiết đơn hàng
2. **Điều kiện**: Đơn hàng phải có mã vận đơn GHN
3. **Thao tác**: Click nút "Cập nhật theo dõi" (màu xanh dương)
4. **Kết quả**: Thông tin tracking được cập nhật từ GHN

#### Mã Nguồn
**File**: `app/Http/Controllers/Admin/OrderController.php`

```php
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
```

### 2. Hủy Liên Kết GHN

#### Mục Đích
- Xóa thông tin GHN khỏi đơn hàng
- **Lưu ý**: Không hủy đơn hàng trên hệ thống GHN, chỉ xóa liên kết
- Cho phép tạo lại đơn GHN nếu cần

#### Cách Sử Dụng
1. **Truy cập**: Admin Panel > Quản lý đơn hàng > Chi tiết đơn hàng
2. **Điều kiện**: Đơn hàng phải có mã vận đơn GHN
3. **Thao tác**: Click nút "Hủy liên kết GHN" (màu đỏ)
4. **Xác nhận**: Hệ thống sẽ hỏi xác nhận trước khi thực hiện
5. **Kết quả**: Thông tin GHN được xóa khỏi đơn hàng

#### Mã Nguồn
**File**: `app/Http/Controllers/Admin/OrderController.php`

```php
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

## Khi Nào Sử Dụng Các Chức Năng

### Cập Nhật Theo Dõi
**Sử dụng khi**:
- Cần kiểm tra trạng thái vận chuyển mới nhất
- Khách hàng hỏi về tình trạng đơn hàng
- Theo dõi định kỳ các đơn hàng đang vận chuyển
- Sau khi có thông báo từ GHN về thay đổi trạng thái

**Tần suất khuyến nghị**:
- 1-2 lần/ngày cho đơn hàng đang vận chuyển
- Khi có yêu cầu từ khách hàng
- Khi cần cập nhật trạng thái đơn hàng

### Hủy Liên Kết GHN
**Sử dụng khi**:
- Tạo nhầm đơn GHN với thông tin sai
- Cần tạo lại đơn GHN với thông tin mới
- Đơn hàng bị hủy nhưng đã tạo GHN
- Chuyển sang phương thức vận chuyển khác

**Lưu ý quan trọng**:
- Chức năng này chỉ xóa liên kết, không hủy đơn trên GHN
- Nếu cần hủy đơn trên GHN, phải liên hệ trực tiếp với GHN
- Sau khi hủy liên kết, có thể tạo đơn GHN mới

## Xử Lý Lỗi Thường Gặp

### 1. "Không thể lấy thông tin theo dõi từ GHN"
**Nguyên nhân**:
- Mã vận đơn không tồn tại trên GHN
- API GHN tạm thời không khả dụng
- Token GHN hết hạn

**Giải pháp**:
1. Kiểm tra mã vận đơn trên website GHN
2. Thử lại sau 15-30 phút
3. Kiểm tra cấu hình GHN trong `.env`
4. Xem log Laravel: `storage/logs/laravel.log`

### 2. "Đơn hàng chưa có mã vận đơn GHN"
**Nguyên nhân**:
- Cố gắng cập nhật/hủy đơn chưa có GHN

**Giải pháp**:
1. Tạo đơn GHN trước bằng nút "Tạo đơn hàng GHN"
2. Kiểm tra điều kiện tạo đơn GHN

### 3. Routes không hoạt động
**Nguyên nhân**:
- Route cache chưa được clear
- Route names không khớp

**Giải pháp**:
```bash
php artisan route:clear
php artisan config:clear
```

## Quy Trình Xử Lý Đơn Hàng GHN

### 1. Đơn Hàng Mới (Tự Động)
```
Đặt hàng → Thanh toán → Tự động tạo GHN → Tracking data sẵn sàng
```

### 2. Đơn Hàng Cũ (Thủ Công)
```
Admin → Chi tiết đơn hàng → Tạo GHN → Cập nhật theo dõi
```

### 3. Theo Dõi Hàng Ngày
```
Admin → Chi tiết đơn hàng → Cập nhật theo dõi → Kiểm tra trạng thái
```

### 4. Xử Lý Vấn Đề
```
Phát hiện lỗi → Hủy liên kết GHN → Tạo lại GHN → Cập nhật theo dõi
```

## Monitoring và Logging

### Log Events Quan Trọng
```php
// Khi cập nhật tracking thành công
Log::info('GHN tracking updated successfully', [
    'order_id' => $order->id,
    'ghn_order_code' => $order->ghn_order_code
]);

// Khi hủy liên kết GHN
Log::info('GHN order link cancelled', [
    'order_id' => $order->id,
    'ghn_order_code' => $order->ghn_order_code
]);

// Khi có lỗi
Log::error('Error updating GHN tracking', [
    'order_id' => $order->id,
    'error' => $e->getMessage()
]);
```

### Kiểm Tra Log
```bash
# Xem log mới nhất
tail -f storage/logs/laravel.log

# Tìm log GHN
grep -i "ghn" storage/logs/laravel.log

# Tìm log lỗi
grep -i "error.*ghn" storage/logs/laravel.log
```

## Kết Luận

Sau khi khắc phục:

✅ **API Tracking hoạt động ổn định** cho client  
✅ **Chức năng admin GHN hoạt động đầy đủ**  
✅ **Route names đã được đồng bộ**  
✅ **Error handling được cải thiện**  
✅ **Logging đầy đủ cho debugging**  

Hệ thống GHN giờ đây hoạt động mượt mà cả ở client và admin, với khả năng xử lý lỗi tốt và logging chi tiết để hỗ trợ troubleshooting.