<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test GHN API</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-center">Test GHN API Integration</h1>
        
        <div class="space-y-4">
            <!-- Tỉnh/Thành phố -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tỉnh/Thành phố</label>
                <select id="province" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="">Chọn Tỉnh/Thành phố</option>
                </select>
            </div>
            
            <!-- Quận/Huyện -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quận/Huyện</label>
                <select id="district" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="">Chọn Quận/Huyện</option>
                </select>
            </div>
            
            <!-- Phường/Xã -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phường/Xã</label>
                <select id="ward" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="">Chọn Phường/Xã</option>
                </select>
            </div>
            
            <!-- Kết quả -->
            <div id="result" class="mt-6 p-4 bg-gray-50 rounded-md hidden">
                <h3 class="font-semibold text-lg mb-2">Kết quả:</h3>
                <div id="result-content"></div>
            </div>
            
            <!-- Loading -->
            <div id="loading" class="text-center hidden">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Đang tải...</p>
            </div>
        </div>
    </div>

    <script>
        // Load provinces on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProvinces();
        });

        async function loadProvinces() {
            showLoading(true);
            try {
                const response = await fetch('/api/ghn/provinces');
                const data = await response.json();
                
                const provinceSelect = document.getElementById('province');
                if (data.success) {
                    provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
                    data.data.forEach(province => {
                        provinceSelect.innerHTML += `<option value="${province.ProvinceID}">${province.ProvinceName}</option>`;
                    });
                    showResult(`Đã tải ${data.data.length} tỉnh/thành phố thành công!`, 'success');
                } else {
                    showResult('Lỗi khi tải danh sách tỉnh/thành phố', 'error');
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
                showResult('Lỗi kết nối khi tải tỉnh/thành phố', 'error');
            }
            showLoading(false);
        }

        async function loadDistricts(provinceId) {
            showLoading(true);
            try {
                const response = await fetch('/api/ghn/districts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        province_id: parseInt(provinceId)
                    })
                });
                const data = await response.json();
                
                const districtSelect = document.getElementById('district');
                const wardSelect = document.getElementById('ward');
                
                if (data.success) {
                    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                    wardSelect.disabled = true;
                    
                    data.data.forEach(district => {
                        districtSelect.innerHTML += `<option value="${district.DistrictID}">${district.DistrictName}</option>`;
                    });
                    
                    districtSelect.disabled = false;
                    showResult(`Đã tải ${data.data.length} quận/huyện thành công!`, 'success');
                } else {
                    showResult('Lỗi khi tải danh sách quận/huyện', 'error');
                }
            } catch (error) {
                console.error('Error loading districts:', error);
                showResult('Lỗi kết nối khi tải quận/huyện', 'error');
            }
            showLoading(false);
        }

        async function loadWards(districtId) {
            showLoading(true);
            try {
                const response = await fetch('/api/ghn/wards', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        district_id: parseInt(districtId)
                    })
                });
                const data = await response.json();
                
                const wardSelect = document.getElementById('ward');
                
                if (data.success) {
                    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                    
                    data.data.forEach(ward => {
                        wardSelect.innerHTML += `<option value="${ward.WardCode}">${ward.WardName}</option>`;
                    });
                    
                    wardSelect.disabled = false;
                    showResult(`Đã tải ${data.data.length} phường/xã thành công!`, 'success');
                } else {
                    showResult('Lỗi khi tải danh sách phường/xã', 'error');
                }
            } catch (error) {
                console.error('Error loading wards:', error);
                showResult('Lỗi kết nối khi tải phường/xã', 'error');
            }
            showLoading(false);
        }

        async function calculateShippingFee(districtId, wardCode) {
            showLoading(true);
            try {
                const response = await fetch('/api/ghn/shipping-fee', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        to_district_id: parseInt(districtId),
                        to_ward_code: wardCode,
                        weight: 500
                    })
                });
                const data = await response.json();
                
                if (data.success && data.data.total) {
                    const fee = new Intl.NumberFormat('vi-VN').format(data.data.total);
                    showResult(`Phí vận chuyển: ${fee}đ`, 'success');
                } else {
                    showResult('Lỗi khi tính phí vận chuyển', 'error');
                }
            } catch (error) {
                console.error('Error calculating shipping fee:', error);
                showResult('Lỗi kết nối khi tính phí vận chuyển', 'error');
            }
            showLoading(false);
        }

        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            const resultContent = document.getElementById('result-content');
            
            resultContent.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}"></div>
                    <span class="${type === 'success' ? 'text-green-700' : 'text-red-700'}">${message}</span>
                </div>
            `;
            
            resultDiv.classList.remove('hidden');
        }

        function showLoading(show) {
            const loadingDiv = document.getElementById('loading');
            if (show) {
                loadingDiv.classList.remove('hidden');
            } else {
                loadingDiv.classList.add('hidden');
            }
        }

        // Event listeners
        document.getElementById('province').addEventListener('change', function() {
            const provinceId = this.value;
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');
            
            // Reset dependent selects
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            districtSelect.disabled = true;
            wardSelect.disabled = true;
            
            if (provinceId) {
                loadDistricts(provinceId);
            }
        });

        document.getElementById('district').addEventListener('change', function() {
            const districtId = this.value;
            const wardSelect = document.getElementById('ward');
            
            // Reset dependent select
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            wardSelect.disabled = true;
            
            if (districtId) {
                loadWards(districtId);
            }
        });

        document.getElementById('ward').addEventListener('change', function() {
            const wardCode = this.value;
            const districtId = document.getElementById('district').value;
            
            if (districtId && wardCode) {
                calculateShippingFee(districtId, wardCode);
            }
        });
    </script>
</body>
</html>