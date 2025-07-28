# Hướng dẫn Migration từ Provinces Open API sang GHN API

## Tổng quan

Tài liệu này mô tả quá trình migration từ API `provinces.open-api.vn` sang GHN API để khắc phục lỗi CORS và tăng tính nhất quán trong hệ thống.

## Vấn đề gặp phải

### Lỗi CORS
```
Access to XMLHttpRequest at 'https://provinces.open-api.vn/api/p/268?depth=2' from origin 'http://127.0.0.1:8000' has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

### Lỗi 404
```
GET https://provinces.open-api.vn/api/p/268?depth=2 net::ERR_FAILED 404 (Not Found)
```

## Nguyên nhân

1. **API bên ngoài không ổn định**: `provinces.open-api.vn` có thể gặp sự cố hoặc thay đổi endpoint
2. **CORS Policy**: API bên ngoài không cho phép truy cập từ domain localhost
3. **Không nhất quán**: Sử dụng 2 API khác nhau (provinces.open-api.vn và GHN) gây conflict
4. **Dependency**: Phụ thuộc vào dịch vụ bên ngoài không kiểm soát được

## Giải pháp

### 1. Loại bỏ API cũ

#### File: `resources/views/layouts/app.blade.php`
**Trước:**
```javascript
$.getJSON('https://provinces.open-api.vn/api/p/', function(provinces) {
    provinces.forEach(function(province) {
        $("#tinh").append(`<option value="${province.code}">${province.name}</option>`);
    });
});
```

**Sau:**
```html
<!-- Address selection scripts are now handled by individual pages using GHN API -->
```

### 2. Cập nhật các file sử dụng địa chỉ

#### File: `resources/views/clients/profile/profile.blade.php`

**Function load provinces mới:**
```javascript
async function loadProvinces(callback = null) {
    try {
        const response = await fetch('/api/ghn/provinces', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.data) {
            $('#city').html('<option value="">Chọn Tỉnh/Thành phố</option>');
            data.data.forEach(function(province) {
                $("#city").append(`<option value="${province.ProvinceID}" data-name="${province.ProvinceName}">${province.ProvinceName}</option>`);
            });
            if (callback) callback(data.data);
        }
    } catch (error) {
        console.error('Failed to load provinces:', error);
        toastr.error('Không thể tải dữ liệu tỉnh thành. Vui lòng thử lại!');
    }
}
```

#### File: `resources/views/profile/addresses.blade.php`

Tương tự cập nhật các function:
- `loadProvinces()`
- `loadDistricts(provinceId)`
- `loadWards(districtId)`
- `loadProvincesForEdit(data)`

### 3. Cập nhật Event Handlers

**Trước (API cũ):**
```javascript
$("#tinh").change(function() {
    const provinceCode = $(this).val();
    $.getJSON(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`, function(provinceData) {
        // Process data
    });
});
```

**Sau (GHN API):**
```javascript
$("#tinh").change(function() {
    const provinceId = $(this).val();
    if (provinceId) {
        loadDistricts(provinceId);
    }
});
```

### 4. Cập nhật cách lấy tên địa chỉ

**Trước:**
```javascript
const cityName = $('#city option:selected').text();
```

**Sau:**
```javascript
const cityName = $('#city option:selected').attr('data-name') || $('#city option:selected').text();
```

## Cấu trúc dữ liệu

### API cũ (provinces.open-api.vn)
```json
{
  "code": "01",
  "name": "Thành phố Hà Nội",
  "districts": [
    {
      "code": "001",
      "name": "Quận Ba Đình"
    }
  ]
}
```

### GHN API
```json
{
  "success": true,
  "data": [
    {
      "ProvinceID": 269,
      "ProvinceName": "Lào Cai",
      "CountryID": 1
    }
  ]
}
```

## Lợi ích của việc migration

1. **Tính nhất quán**: Chỉ sử dụng 1 API duy nhất (GHN)
2. **Không có lỗi CORS**: API nội bộ qua Laravel backend
3. **Kiểm soát tốt hơn**: Có thể cache, log, và xử lý lỗi tập trung
4. **Hiệu suất**: Giảm số lượng request trực tiếp từ frontend
5. **Bảo mật**: API key GHN được bảo vệ ở backend

## Kiểm tra sau migration

1. **Trang checkout**: `/checkout` - Kiểm tra chọn địa chỉ và tính phí ship
2. **Trang profile**: `/account/profile` - Kiểm tra thêm/sửa địa chỉ
3. **Trang addresses**: `/account/addresses` - Kiểm tra quản lý địa chỉ
4. **Console browser**: Không còn lỗi CORS hoặc 404
5. **Network tab**: Chỉ thấy request đến `/api/ghn/*`

## Troubleshooting

### Lỗi "Failed to load provinces"
- Kiểm tra API routes trong `routes/api.php`
- Kiểm tra GHN token và cấu hình trong `.env`
- Kiểm tra CSRF token trong meta tag

### Lỗi "Cannot read property of undefined"
- Kiểm tra cấu trúc response từ GHN API
- Kiểm tra mapping field names (ProvinceID vs code)

### Địa chỉ không được chọn đúng khi edit
- Kiểm tra function `loadProvincesForEdit()`
- Kiểm tra việc so sánh tên địa chỉ (ProvinceName vs name)

## Kết luận

Việc migration từ provinces.open-api.vn sang GHN API đã:
- Khắc phục hoàn toàn lỗi CORS
- Tăng tính ổn định và nhất quán của hệ thống
- Cải thiện trải nghiệm người dùng
- Giảm dependency vào dịch vụ bên ngoài không kiểm soát được

Tất cả các chức năng liên quan đến địa chỉ (tỉnh/quận/phường) đều hoạt động bình thường với GHN API.