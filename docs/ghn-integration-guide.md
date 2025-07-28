php# Hướng Dẫn Tích Hợp Giao Hàng Nhanh (GHN)

## Tổng Quan

Hệ thống đã được tích hợp với API Giao Hàng Nhanh (GHN) để hỗ trợ tính phí vận chuyển, tạo đơn hàng và theo dõi vận chuyển tự động.

## Cấu Hình

### 1. Biến Môi Trường (.env)

```env
# GHN Configuration
GHN_API_URL=https://dev-online-gateway.ghn.vn
GHN_TOKEN=your_ghn_token_here
GHN_SHOP_ID=your_shop_id_here
GHN_FROM_DISTRICT_ID=1442
GHN_FROM_WARD_CODE=21211
```

### 2. Cấu Hình Services (config/services.php)

```php
'ghn' => [
    'api_url' => env('GHN_API_URL', 'https://dev-online-gateway.ghn.vn'),
    'token' => env('GHN_TOKEN', ''),
    'shop_id' => env('GHN_SHOP_ID', ''),
    'from_district_id' => env('GHN_FROM_DISTRICT_ID', 1442),
    'from_ward_code' => env('GHN_FROM_WARD_CODE', '21211'),
],
```

## Cấu Trúc Database

### Bảng Orders - Các trường GHN mới:
- `ghn_order_code`: Mã vận đơn GHN
- `ghn_service_type_id`: ID loại dịch vụ GHN
- `expected_delivery_date`: Ngày giao hàng dự kiến
- `ghn_tracking_data`: Dữ liệu theo dõi (JSON)

### Bảng Addresses - Các trường GHN mới:
- `province_id`: ID tỉnh/thành phố theo GHN
- `district_id`: ID quận/huyện theo GHN
- `ward_code`: Mã phường/xã theo GHN

## Các Tính Năng Chính

### 1. Tính Phí Vận Chuyển

**Frontend (Checkout):**
- Tự động load danh sách tỉnh/thành phố, quận/huyện, phường/xã
- Tính phí vận chuyển real-time khi chọn địa chỉ
- Hiển thị thời gian giao hàng dự kiến

**API Endpoints:**
```
GET /api/ghn/provinces - Lấy danh sách tỉnh/thành phố
POST /api/ghn/districts - Lấy danh sách quận/huyện
POST /api/ghn/wards - Lấy danh sách phường/xã
POST /api/ghn/shipping-fee - Tính phí vận chuyển
POST /api/ghn/lead-time - Lấy thời gian giao hàng dự kiến
```

### 2. Tạo Đơn Hàng GHN

**Tự động:**
- Sau khi đơn hàng được tạo thành công và thanh toán
- Chỉ áp dụng cho đơn hàng có `delivery_method = 'delivery'`

**Thủ công (Admin):**
- Trang chi tiết đơn hàng admin có nút "Tạo đơn hàng GHN"
- Chỉ có thể tạo khi đơn hàng ở trạng thái "Chờ Xác Nhận" hoặc "Đã Xác Nhận"

### 3. Theo Dõi Đơn Hàng

**Client:**
- Trang chi tiết đơn hàng hiển thị mã vận đơn và ngày giao dự kiến
- JavaScript tự động load thông tin theo dõi từ GHN
- Timeline hiển thị lịch sử vận chuyển

**Admin:**
- Trang chi tiết đơn hàng admin hiển thị đầy đủ thông tin GHN
- Nút "Cập nhật theo dõi" để refresh thông tin từ GHN
- Nút "Hủy liên kết GHN" để xóa thông tin GHN khỏi đơn hàng

## Cách Sử Dụng

### 1. Cho Khách Hàng

1. **Đặt hàng:**
   - Chọn "Giao hàng tận nơi" trong checkout
   - Chọn địa chỉ giao hàng (tỉnh/thành, quận/huyện, phường/xã)
   - Hệ thống tự động tính phí vận chuyển
   - Hoàn tất thanh toán

2. **Theo dõi đơn hàng:**
   - Vào "Tài khoản" > "Đơn hàng của tôi"
   - Click vào đơn hàng cần theo dõi
   - Xem thông tin vận chuyển và timeline

### 2. Cho Admin

1. **Quản lý đơn hàng:**
   - Vào "Quản lý đơn hàng" > Chi tiết đơn hàng
   - Xem thông tin GHN ở cột bên phải
   - Tạo đơn GHN nếu chưa có
   - Cập nhật thông tin theo dõi

2. **Xử lý đơn hàng:**
   - Cập nhật trạng thái đơn hàng theo tiến trình
   - Theo dõi tình trạng vận chuyển qua GHN
   - Xử lý các vấn đề phát sinh

## API Reference

### GhnService Methods

```php
// Lấy danh sách tỉnh/thành phố
$provinces = $ghnService->getProvinces();

// Lấy danh sách quận/huyện
$districts = $ghnService->getDistricts($provinceId);

// Lấy danh sách phường/xã
$wards = $ghnService->getWards($districtId);

// Tính phí vận chuyển
$fee = $ghnService->calculateShippingFee($toDistrictId, $toWardCode, $weight, $serviceTypeId);

// Lấy thời gian giao hàng dự kiến
$leadTime = $ghnService->getLeadTime($toDistrictId, $toWardCode, $serviceTypeId);

// Tạo đơn hàng GHN
$result = $ghnService->createOrder($orderData);

// Lấy thông tin đơn hàng
$info = $ghnService->getOrderInfo($orderCode);

// Lấy danh sách dịch vụ
$services = $ghnService->getServices($fromDistrictId, $toDistrictId);
```

### OrderService GHN Methods

```php
// Tính phí vận chuyển GHN
$fee = $orderService->calculateGhnShippingFee($order, $address);

// Tạo đơn hàng GHN
$result = $orderService->createGhnOrder($order);
```

## Xử Lý Lỗi

### Các Lỗi Thường Gặp

1. **Lỗi 404 "Not Found" khi gọi API:**
   - **Nguyên nhân**: URL API không đúng hoặc tên biến môi trường không khớp
   - **Giải pháp**: 
     - Kiểm tra `GHN_API_KEY` trong file `.env` (không phải `GHN_TOKEN`)
     - Đảm bảo URL API đúng: `https://dev-online-gateway.ghn.vn`
     - Chạy `php artisan config:clear` sau khi thay đổi config

2. **Lỗi 400 "expected=int, got=string":**
   - **Nguyên nhân**: GHN API yêu cầu các ID phải là kiểu `integer`, nhưng Laravel `env()` function trả về `string`
   - **Giải pháp**:
     - Sửa `config/services.php` để cast sang integer:
     ```php
     'ghn' => [
         'shop_id' => (int) env('GHN_SHOP_ID'),
         'from_district_id' => (int) env('GHN_SHOP_DISTRICT_ID', 1454),
         // ...
     ],
     ```
     - Sửa JavaScript để convert sang integer:
     ```javascript
     province_id: parseInt(provinceId),
     district_id: parseInt(districtId)
     ```
     - Chạy `php artisan config:clear`

3. **Token không hợp lệ:**
   - Kiểm tra `GHN_TOKEN` trong file .env
   - Đảm bảo token còn hiệu lực

4. **Shop ID không đúng:**
   - Kiểm tra `GHN_SHOP_ID` trong file .env
   - Đảm bảo shop đã được kích hoạt

5. **Địa chỉ không hợp lệ:**
   - Kiểm tra province_id, district_id, ward_code
   - Đảm bảo địa chỉ nằm trong vùng phục vụ của GHN

6. **Trọng lượng vượt quá giới hạn:**
   - Kiểm tra trọng lượng sản phẩm
   - Chia nhỏ đơn hàng nếu cần

### Logging

Tất cả các lỗi GHN được log vào Laravel log với prefix "GHN Error":

```php
Log::error('GHN Error: ' . $message, $context);
```

## Testing

### Environment Testing

1. **Development:**
   - Sử dụng GHN Sandbox API
   - URL: `https://dev-online-gateway.ghn.vn`

2. **Production:**
   - Sử dụng GHN Production API
   - URL: `https://online-gateway.ghn.vn`

### Test Cases

1. **Tính phí vận chuyển:**
   - Test với các địa chỉ khác nhau
   - Test với trọng lượng khác nhau
   - Test với dịch vụ khác nhau

2. **Tạo đơn hàng:**
   - Test tạo đơn thành công
   - Test xử lý lỗi khi tạo đơn
   - Test cập nhật thông tin đơn hàng

3. **Theo dõi đơn hàng:**
   - Test load thông tin theo dõi
   - Test hiển thị timeline
   - Test cập nhật trạng thái

## Bảo Mật

### API Security

1. **Token Management:**
   - Không commit token vào git
   - Sử dụng environment variables
   - Rotate token định kỳ

2. **Data Validation:**
   - Validate tất cả input từ client
   - Sanitize dữ liệu trước khi gửi API
   - Check quyền truy cập

3. **Error Handling:**
   - Không expose sensitive information
   - Log chi tiết cho debugging
   - Return generic error messages

## Monitoring

### Metrics cần theo dõi:

1. **API Response Time:**
   - Thời gian phản hồi của GHN API
   - Timeout và retry logic

2. **Success Rate:**
   - Tỷ lệ thành công khi tạo đơn
   - Tỷ lệ thành công khi tính phí

3. **Error Rate:**
   - Số lượng lỗi API
   - Loại lỗi phổ biến

## Troubleshooting

### Debug Steps

1. **Kiểm tra cấu hình:**
   ```bash
   php artisan config:cache
   php artisan config:clear
   ```

2. **Kiểm tra database:**
   ```bash
   php artisan migrate:status
   ```

3. **Kiểm tra logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep "GHN"
   ```

4. **Test API connection:**
   ```php
   $ghnService = app(GhnService::class);
   $result = $ghnService->getProvinces();
   dd($result);
   ```

## Changelog

### Version 1.0.0 (2025-01-26)
- Tích hợp cơ bản với GHN API
- Tính phí vận chuyển real-time
- Tạo đơn hàng tự động
- Theo dõi đơn hàng
- Admin management interface
- Client tracking interface

---

**Tác giả:** Development Team  
**Ngày tạo:** 26/01/2025  
**Phiên bản:** 1.0.0