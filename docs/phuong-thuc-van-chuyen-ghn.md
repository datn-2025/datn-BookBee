# Phương Thức Vận Chuyển Sử Dụng API GHN

## Mô tả chức năng
Hệ thống hiển thị 2 phương thức vận chuyển chính từ API GHN:
1. **Giao hàng tiết kiệm** (service_type_id = 2) - 3-5 ngày làm việc
2. **Giao hàng nhanh** (service_type_id = 1) - 1-2 ngày làm việc

## Cách hoạt động

### 1. Tải dịch vụ vận chuyển
- Khi người dùng chọn quận/huyện, hệ thống gọi API GHN để lấy danh sách dịch vụ
- Lọc chỉ lấy 2 dịch vụ: giao hàng nhanh (1) và giao hàng tiết kiệm (2)
- Sắp xếp: giao hàng tiết kiệm hiển thị trước (mặc định được chọn)

### 2. Hiển thị giao diện
- **Giao hàng tiết kiệm**: Icon màu xanh dương, thời gian 3-5 ngày
- **Giao hàng nhanh**: Icon màu cam, thời gian 1-2 ngày
- Phí vận chuyển được tính khi chọn đầy đủ địa chỉ

### 3. Fallback khi API lỗi
- Hiển thị 2 lựa chọn cố định với cùng giao diện
- Giá trị mặc định: giao hàng tiết kiệm (value="2")

## Code implementation

### Hàm loadShippingServices
```javascript
async function loadShippingServices(districtId) {
    // Gọi API GHN để lấy dịch vụ
    const response = await fetch('/api/ghn/services', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            to_district_id: parseInt(districtId)
        })
    });
    
    const data = await response.json();
    
    if (data.success && data.data && data.data.length > 0) {
        // Lọc chỉ lấy 2 dịch vụ: giao hàng nhanh (1) và giao hàng tiết kiệm (2)
        const filteredServices = data.data.filter(service => 
            service.service_type_id === 1 || service.service_type_id === 2
        );
        
        // Sắp xếp: giao hàng tiết kiệm (2) trước, giao hàng nhanh (1) sau
        filteredServices.sort((a, b) => b.service_type_id - a.service_type_id);
        
        // Render UI cho từng dịch vụ
        filteredServices.forEach((service, index) => {
            const serviceName = service.service_type_id === 2 ? 'Giao hàng tiết kiệm' : 'Giao hàng nhanh';
            const serviceDescription = service.service_type_id === 2 ? '3-5 ngày làm việc' : '1-2 ngày làm việc';
            // ... render HTML
        });
    }
}
```

### HTML Structure - API Services
```html
<div id="shipping-services-container" class="hidden">
    <div id="shipping-services-list" class="grid grid-cols-1 gap-3">
        <!-- Services sẽ được load từ API GHN -->
    </div>
</div>
```

### HTML Structure - Fallback
```html
<div id="shipping-services-fallback" class="hidden">
    <div class="grid grid-cols-1 gap-3">
        <!-- Giao hàng tiết kiệm -->
        <label class="group relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
            <input type="radio" name="shipping_method" value="2" class="sr-only" checked>
            <div class="flex items-center justify-center w-4 h-4 border-2 border-gray-300 rounded-full group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500 mr-3">
                <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="font-medium text-gray-900">Giao hàng tiết kiệm</span>
                </div>
                <p class="text-xs text-gray-600">3-5 ngày làm việc</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-blue-600">Phí ship sẽ được tính khi chọn địa chỉ</div>
            </div>
        </label>
        
        <!-- Giao hàng nhanh -->
        <label class="group relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-orange-300 hover:bg-orange-50 transition-all duration-200 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
            <input type="radio" name="shipping_method" value="1" class="sr-only">
            <!-- ... tương tự -->
        </label>
    </div>
</div>
```

## Luồng xử lý

### 1. Khi chọn quận/huyện
```javascript
document.getElementById('quan')?.addEventListener('change', function() {
    const districtId = this.value;
    
    if (districtId) {
        loadWards(districtId);
        loadShippingServices(districtId); // Tải dịch vụ vận chuyển
    }
});
```

### 2. Khi chọn phường/xã
```javascript
document.getElementById('phuong')?.addEventListener('change', function() {
    const wardCode = this.value;
    const districtId = document.getElementById('form_hidden_district_id').value;
    
    if (districtId && wardCode) {
        calculateShippingFeeWithService(districtId, wardCode); // Tính phí ship
    }
});
```

### 3. Khi thay đổi phương thức vận chuyển
```javascript
document.addEventListener('change', function(e) {
    if (e.target.name === 'shipping_method') {
        const districtId = document.getElementById('form_hidden_district_id').value;
        const wardCode = document.getElementById('form_hidden_ward_code').value;
        
        if (districtId && wardCode) {
            calculateShippingFeeWithService(districtId, wardCode); // Tính lại phí
        }
    }
});
```

## Tính phí vận chuyển

### Hàm calculateShippingFeeWithService
```javascript
async function calculateShippingFeeWithService(districtId, wardCode) {
    try {
        const selectedService = document.querySelector('input[name="shipping_method"]:checked');
        const serviceTypeId = selectedService ? selectedService.value : 2;
        
        const response = await fetch('/api/ghn/shipping-fee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                to_district_id: parseInt(districtId),
                to_ward_code: wardCode,
                weight: 500,
                service_type_id: parseInt(serviceTypeId)
            })
        });
        
        const data = await response.json();
        
        if (data.success && data.data.total) {
            const shippingFee = data.data.total;
            
            // Lấy tên dịch vụ từ label được chọn
            const serviceName = selectedService?.closest('label').querySelector('.font-medium')?.textContent || 
                              (serviceTypeId == 2 ? 'Giao hàng tiết kiệm' : 'Giao hàng nhanh');
            
            updateShippingFeeDisplay(shippingFee, serviceName);
            getLeadTime(districtId, serviceTypeId);
            
            return shippingFee;
        }
    } catch (error) {
        console.error('Error calculating shipping fee:', error);
        const selectedService = document.querySelector('input[name="shipping_method"]:checked');
        const serviceTypeId = selectedService ? selectedService.value : 2;
        const serviceName = serviceTypeId == 2 ? 'Giao hàng tiết kiệm' : 'Giao hàng nhanh';
        updateShippingFeeDisplay(30000, serviceName);
    }
}
```

## API Endpoints sử dụng

### 1. Lấy dịch vụ vận chuyển
```
POST /api/ghn/services
Body: {
    "to_district_id": 1442
}
```

### 2. Tính phí vận chuyển
```
POST /api/ghn/shipping-fee
Body: {
    "to_district_id": 1442,
    "to_ward_code": "21012",
    "weight": 500,
    "service_type_id": 2
}
```

### 3. Lấy thời gian giao hàng dự kiến
```
POST /api/ghn/lead-time
Body: {
    "to_district_id": 1442,
    "service_type_id": 2
}
```

## Mapping service_type_id

| service_type_id | Tên hiển thị | Thời gian | Màu sắc |
|-----------------|--------------|-----------|----------|
| 1 | Giao hàng nhanh | 1-2 ngày làm việc | Cam (orange) |
| 2 | Giao hàng tiết kiệm | 3-5 ngày làm việc | Xanh dương (blue) |

## Xử lý lỗi

### 1. Khi API GHN lỗi
- Hiển thị fallback với 2 lựa chọn cố định
- Phí mặc định: 30,000đ
- Vẫn có thể đặt hàng bình thường

### 2. Khi không có dịch vụ hỗ trợ
- Hiển thị thông báo "No supported services available"
- Chuyển sang fallback

### 3. Khi tính phí lỗi
- Sử dụng phí mặc định 30,000đ
- Hiển thị tên dịch vụ đã chọn
- Log error để debug

## Lợi ích

### 1. Tính chính xác
- Phí vận chuyển được tính theo API GHN thực tế
- Thời gian giao hàng dự kiến chính xác
- Hỗ trợ nhiều loại dịch vụ

### 2. Trải nghiệm người dùng
- Giao diện đẹp, dễ sử dụng
- Phản hồi nhanh khi chọn địa chỉ
- Hiển thị rõ ràng phí và thời gian

### 3. Độ tin cậy
- Có fallback khi API lỗi
- Xử lý lỗi graceful
- Không ảnh hưởng đến quá trình đặt hàng

## Kiểm tra chức năng

### 1. Test case cơ bản
1. Chọn tỉnh/thành phố → Danh sách quận/huyện được tải
2. Chọn quận/huyện → Danh sách phường/xã và dịch vụ vận chuyển được tải
3. Chọn phường/xã → Phí vận chuyển được tính
4. Thay đổi phương thức vận chuyển → Phí được cập nhật

### 2. Test case lỗi
1. Ngắt mạng → Hiển thị fallback
2. API GHN lỗi → Sử dụng phí mặc định
3. Không có dịch vụ hỗ trợ → Chuyển sang fallback

### 3. Kiểm tra UI
1. Icon và màu sắc đúng cho từng dịch vụ
2. Tên dịch vụ hiển thị chính xác
3. Phí và thời gian cập nhật đúng
4. Responsive trên mobile

## Troubleshooting

### Lỗi thường gặp

1. **Không tải được dịch vụ vận chuyển**
   - Kiểm tra API key GHN
   - Kiểm tra shop_id trong config
   - Kiểm tra district_id có hợp lệ không

2. **Phí vận chuyển không chính xác**
   - Kiểm tra weight trong request
   - Kiểm tra service_type_id
   - Kiểm tra ward_code

3. **Fallback không hiển thị**
   - Kiểm tra HTML structure
   - Kiểm tra CSS classes
   - Kiểm tra JavaScript error

### Debug
```javascript
// Bật debug trong console
console.log('Selected service:', selectedService);
console.log('Service type ID:', serviceTypeId);
console.log('District ID:', districtId);
console.log('Ward code:', wardCode);
```

## Kết luận
Hệ thống phương thức vận chuyển đã được tối ưu để:
- Chỉ hiển thị 2 lựa chọn chính: giao hàng nhanh và tiết kiệm
- Tích hợp hoàn toàn với API GHN
- Có fallback khi gặp lỗi
- Giao diện đẹp và dễ sử dụng
- Tính phí chính xác theo từng dịch vụ