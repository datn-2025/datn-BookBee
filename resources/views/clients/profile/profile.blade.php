@extends('layouts.account.layout')

@section('title', 'Tài Khoản Của Tôi')

@push('scripts')
<script>
    @if(Session::has('success'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        toastr.success("{{ Session::get('success') }}", "Thành công!");
    @endif
    @if(Session::has('error'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        toastr.error("{{ Session::get('error') }}", "Lỗi!");
    @endif
    @if ($errors->any())
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };
        @foreach ($errors->all() as $error)
            toastr.error("{{ $error }}", "Lỗi!");
        @endforeach
    @endif
</script>
@endpush

@section('account_content')
<div class="bg-white border border-black shadow mb-8" style="border-radius: 0;">
    <div class="px-8 py-6 border-b border-black bg-black">
        <h1 class="text-2xl font-bold text-white uppercase tracking-wide">Tài Khoản Của Tôi</h1>
    </div>
    <div class="p-8">
        <div class="flex space-x-1 mb-8 border-b border-black">
            @foreach ([1 => 'Thông Tin Cá Nhân', 2 => 'Địa Chỉ', 3 => 'Đổi Mật Khẩu'] as $type => $label)
                <a href="{{ route('account.profile', ['type' => $type]) }}"
                   class="flex-1 text-center px-6 py-3 text-base font-semibold border-b-2 transition
                       {{ request('type', '1') == $type ? 'border-black text-black bg-white' : 'border-transparent text-gray-500 hover:text-black hover:bg-gray-100' }}"
                   style="border-radius: 0;">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="space-y-6">
            <!-- Profile Section -->
            @if(request('type', '1') == 1)
                <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Avatar Section -->
                        <div class="w-full md:w-1/4 text-center">
                            <div class="avatar">
                                <img id="avatar-preview" 
                                     src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=200' }}" 
                                     alt="Avatar" class="w-32 h-32 object-cover rounded-full shadow-md mx-auto"
                                     data-original="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=200' }}">
                                <div class="mt-3">
                                    <label for="avatar-input" class="btn-save w-full btn btn-primary">
                                        <i class="fas fa-camera"></i> Chọn ảnh
                                    </label>
                                    <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png,image/jpg" class="hidden">
                                    <button type="button" id="cancel-avatar" class="btn btn-outline-secondary w-full mt-2 d-none">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- User Info Form -->
                        <div class="w-full md:w-3/4">
                            <div class="mb-4">
                                <label class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" name="phone" value="{{ Auth::user()->phone }}">
                            </div>

                            <!-- Save Button -->
                            <button type="submit" class="btn btn-primary w-full">
                                <i class="fas fa-save me-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            @elseif(request('type', '1') == 3) <!-- Change Password -->
                <form method="POST" action="{{ route('account.password.update') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label text-gray-700 font-medium">
                            <i class="fas fa-lock text-blue-500 me-1"></i>
                            Mật khẩu hiện tại
                        </label>
                        <div class="relative">
                            <div class="flex">
                                <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-50 border border-r-0 border-gray-300 rounded-l-md">
                                    <i class="fas fa-lock text-gray-500"></i>
                                </span>
                                <input id="current_password" type="password" 
                                       class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5 @error('current_password') border-red-500 @enderror" 
                                       name="current_password" required>
                                <button class="inline-flex items-center px-3 text-sm font-medium text-gray-900 bg-gray-200 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors duration-200" 
                                        type="button" 
                                        tabindex="-1"
                                        onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye text-gray-600" id="current_password_icon"></i>
                                </button>
                            </div>
                        </div>
                        @error('current_password')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label text-gray-700 font-medium">
                            <i class="fas fa-key text-blue-500 me-1"></i>
                            Mật khẩu mới
                        </label>
                        <div class="relative">
                            <div class="flex">
                                <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-50 border border-r-0 border-gray-300 rounded-l-md">
                                    <i class="fas fa-key text-gray-500"></i>
                                </span>
                                <input id="password" type="password" 
                                       class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5 @error('password') border-red-500 @enderror" 
                                       name="password" required>
                                <button class="inline-flex items-center px-3 text-sm font-medium text-gray-900 bg-gray-200 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors duration-200" 
                                        type="button" 
                                        tabindex="-1"
                                        onclick="togglePassword('password')">
                                    <i class="fas fa-eye text-gray-600" id="password_icon"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password_confirmation" class="form-label text-gray-700 font-medium">
                            <i class="fas fa-check-circle text-blue-500 me-1"></i>
                            Xác nhận mật khẩu mới
                        </label>
                        <div class="relative">
                            <div class="flex">
                                <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-50 border border-r-0 border-gray-300 rounded-l-md">
                                    <i class="fas fa-check-circle text-gray-500"></i>
                                </span>
                                <input id="password_confirmation" type="password" 
                                       class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5" 
                                       name="password_confirmation" required>
                                <button class="inline-flex items-center px-3 text-sm font-medium text-gray-900 bg-gray-200 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors duration-200" 
                                        type="button" 
                                        tabindex="-1"
                                        onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye text-gray-600" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('account.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" id="password-submit-btn" class="btn text-white fw-bold py-2 px-4 rounded" style="background-color: #3b82f6; border: none;">
                            <span class="btn-text">
                                <i class="fas fa-key me-2"></i>Cập nhật mật khẩu
                            </span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Đang xử lý...
                            </span>
                        </button>
                    </div>
                </form>
            @elseif(request('type', '1') == 2)
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Quản lý địa chỉ</h2>
                        <button class="btn btn-primary" onclick="openAddressModal()">
                            <i class="fas fa-plus me-2"></i>Thêm địa chỉ mới
                        </button>
                    </div>
                    <!-- Modal Thêm/Sửa Địa Chỉ -->
                    <div id="addressModal" class="modal fade" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="addressForm" method="POST" action="{{ route('account.addresses.store') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addressModalLabel">Thêm/Sửa địa chỉ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="address_id" value="{{ old('id') }}">
                                        <div class="mb-3">
                                            <label class="form-label">Tỉnh/Thành phố</label>
                                            <select id="city" name="city" class="form-select" required>
                                                @if(old('city'))
                                                    <option value="{{ old('city') }}" selected>{{ old('city') }}</option>
                                                @else
                                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Quận/Huyện</label>
                                            <select id="district" name="district" class="form-select" required>
                                                @if(old('district'))
                                                    <option value="{{ old('district') }}" selected>{{ old('district') }}</option>
                                                @else
                                                    <option value="">Chọn Quận/Huyện</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phường/Xã</label>
                                            <select id="ward" name="ward" class="form-select" required>
                                                @if(old('ward'))
                                                    <option value="{{ old('ward') }}" selected>{{ old('ward') }}</option>
                                                @else
                                                    <option value="">Chọn Phường/Xã</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Địa chỉ chi tiết</label>
                                            <input type="text" name="address_detail" id="address_detail" class="form-control" value="{{ old('address_detail') }}" required>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" {{ old('is_default') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_default">Đặt làm mặc định</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn"><span class="btn-text">Lưu</span><span class="loading d-none ms-2 spinner-border spinner-border-sm"></span></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="addressList">
                        @if(isset($addresses) && $addresses->count() > 0)
                            @foreach($addresses as $address)
                                <div class="address-card {{ $address->is_default ? 'default' : '' }} mb-3 p-3 border border-black d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                                    <div class="flex-grow-1">
                                        <span class="fw-bold">{{ $address->address_detail }}</span>, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                                        @if($address->is_default)
                                            <span class="badge bg-dark ms-2">Mặc định</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 mt-md-0 d-flex gap-2">
                                        @if(!$address->is_default)
                                            <button class="btn btn-success btn-sm" onclick="setDefaultAddress('{{ $address->id }}')"><i class="fas fa-star"></i></button>
                                        @endif
                                        <button class="btn btn-warning btn-sm" onclick="editAddress('{{ $address->id }}')"><i class="fas fa-edit"></i></button>
                                        @if(!$address->is_default)
                                            <button class="btn btn-danger btn-sm" onclick="deleteAddress('{{ $address->id }}')"><i class="fas fa-trash"></i></button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-map-marker-alt"></i>
                                <h4>Chưa có địa chỉ nào</h4>
                                <p>Thêm địa chỉ giao hàng để thuận tiện cho việc đặt hàng</p>
                                <button class="btn btn-primary" onclick="openAddressModal()">
                                    <i class="fas fa-plus me-2"></i>Thêm địa chỉ đầu tiên
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    let editId = null;
    
    // Function để load provinces từ GHN API
    async function loadProvinces(callback = null) {
        console.log('Loading provinces from GHN API...');
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
                console.log('Provinces loaded:', data.data.length);
                $('#city').html('<option value="">Chọn Tỉnh/Thành phố</option>');
                data.data.forEach(function(province) {
                    $("#city").append(`<option value="${province.ProvinceID}" data-name="${province.ProvinceName}">${province.ProvinceName}</option>`);
                });
                if (callback) callback(data.data);
            } else {
                throw new Error('Failed to load provinces');
            }
        } catch (error) {
            console.error('Failed to load provinces:', error);
            toastr.error('Không thể tải dữ liệu tỉnh thành. Vui lòng thử lại!');
        }
    }
    
    // Function để load districts từ GHN API
    async function loadDistricts(provinceId, callback = null) {
        console.log('Loading districts from GHN API for province:', provinceId);
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
            
            if (data.success && data.data) {
                console.log('Districts loaded:', data.data.length);
                $('#district').html('<option value="">Chọn Quận/Huyện</option>');
                data.data.forEach(function(district) {
                    $("#district").append(`<option value="${district.DistrictID}" data-name="${district.DistrictName}">${district.DistrictName}</option>`);
                });
                if (callback) callback(data.data);
            } else {
                throw new Error('Failed to load districts');
            }
        } catch (error) {
            console.error('Failed to load districts:', error);
            toastr.error('Không thể tải dữ liệu quận/huyện. Vui lòng thử lại!');
        }
    }
    
    // Function để load wards từ GHN API
    async function loadWards(districtId, callback = null) {
        console.log('Loading wards from GHN API for district:', districtId);
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
            
            if (data.success && data.data) {
                console.log('Wards loaded:', data.data.length);
                $('#ward').html('<option value="">Chọn Phường/Xã</option>');
                data.data.forEach(function(ward) {
                    $("#ward").append(`<option value="${ward.WardCode}" data-name="${ward.WardName}">${ward.WardName}</option>`);
                });
                if (callback) callback(data.data);
            } else {
                throw new Error('Failed to load wards');
            }
        } catch (error) {
            console.error('Failed to load wards:', error);
            toastr.error('Không thể tải dữ liệu phường/xã. Vui lòng thử lại!');
        }
    }
    
    function openAddressModal(isEdit = false, data = null) {
        console.log('Opening address modal, isEdit:', isEdit, 'data:', data);
        editId = null;
        $('#addressForm')[0].reset();
        $('#address_id').val('');
        $('#is_default').prop('checked', false);
        
        if (isEdit && data) {
            console.log('Edit mode - loading data for:', data.id);
            editId = data.id;
            $('#address_id').val(data.id);
            $('#address_detail').val(data.address_detail);
            $('#is_default').prop('checked', data.is_default);
            
            // Load provinces và select current values
            loadProvinces(function(provinces) {
                let selectedProvinceId = null;
                
                provinces.forEach(function(province) {
                    if (province.ProvinceName === data.city) {
                        selectedProvinceId = province.ProvinceID;
                        $("#city").val(province.ProvinceID);
                        console.log('Selected province:', province.ProvinceName, province.ProvinceID);
                    }
                });
                
                if (selectedProvinceId) {
                    // Load districts từ GHN API
                    loadDistricts(selectedProvinceId, function(districts) {
                        let selectedDistrictId = null;
                        
                        districts.forEach(function(district) {
                            if (district.DistrictName === data.district) {
                                selectedDistrictId = district.DistrictID;
                                $("#district").val(district.DistrictID);
                                console.log('Selected district:', district.DistrictName, district.DistrictID);
                            }
                        });
                        
                        if (selectedDistrictId) {
                            // Load wards từ GHN API
                            loadWards(selectedDistrictId, function(wards) {
                                wards.forEach(function(ward) {
                                    if (ward.WardName === data.ward) {
                                        $("#ward").val(ward.WardCode);
                                        console.log('Selected ward:', ward.WardName, ward.WardCode);
                                    }
                                });
                            });
                        }
                    });
                }
            });
        } else {
            console.log('Add mode - loading provinces');
            // Reset và load provinces cho form mới
            loadProvinces();
            $('#district').html('<option value="">Chọn Quận/Huyện</option>');
            $('#ward').html('<option value="">Chọn Phường/Xã</option>');
        }
        
        $('#addressModal').modal('show');
    }
    function editAddress(id) {
        $.get(`/account/addresses/${id}/edit`, function(res) {
            openAddressModal(true, res);
        });
    }
    function deleteAddress(id) {
        if (confirm('Bạn chắc chắn muốn xóa địa chỉ này?')) {
            $.ajax({
                url: `/account/addresses/${id}`,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function() { location.reload(); },
                error: function() { toastr.error('Xóa địa chỉ thất bại!'); }
            });
        }
    }
    function setDefaultAddress(id) {
        $.post(`/account/addresses/${id}/set-default`, {_token: '{{ csrf_token() }}'}, function() {
            location.reload();
        });
    }
    // Load city/district/ward
    $(document).ready(function() {
        console.log('Document ready - setting up event handlers');
        
        // Xử lý khi chọn tỉnh
        $("#city").change(function() {
            const provinceId = $(this).val();
            console.log('Province selected:', provinceId);
            
            // Reset quận và phường
            $("#district").html('<option value="">Chọn Quận/Huyện</option>');
            $("#ward").html('<option value="">Chọn Phường/Xã</option>');
            
            if (provinceId) {
                loadDistricts(provinceId);
            }
        });

        // Xử lý khi chọn quận
        $("#district").change(function() {
            const districtId = $(this).val();
            console.log('District selected:', districtId);
            
            // Reset phường
            $("#ward").html('<option value="">Chọn Phường/Xã</option>');
            
            if (districtId) {
                loadWards(districtId);
            }
        });
    });
    // Submit form
    $('#addressForm').submit(function(e) {
        e.preventDefault();
        
        // Lấy tên thực tế của tỉnh/quận/phường từ data-name attributes
        const cityName = $('#city option:selected').attr('data-name') || $('#city option:selected').text();
        const districtName = $('#district option:selected').attr('data-name') || $('#district option:selected').text();
        const wardName = $('#ward option:selected').attr('data-name') || $('#ward option:selected').text();
        
        console.log('Form submit - City:', cityName, 'District:', districtName, 'Ward:', wardName);
        
        // Kiểm tra xem đã chọn đầy đủ chưa
        if (!cityName || cityName === 'Chọn Tỉnh/Thành phố' || 
            !districtName || districtName === 'Chọn Quận/Huyện' || 
            !wardName || wardName === 'Chọn Phường/Xã') {
            toastr.error('Vui lòng chọn đầy đủ Tỉnh/Thành phố, Quận/Huyện và Phường/Xã');
            return;
        }
        
        // Tạo FormData object để gửi
        const formData = new FormData();
        formData.append('city', cityName);
        formData.append('district', districtName);
        formData.append('ward', wardName);
        formData.append('address_detail', $('#address_detail').val());
        
        // Xử lý is_default - chỉ gửi khi được check
        if ($('#is_default').is(':checked')) {
            formData.append('is_default', '1');
        }
        
        formData.append('_token', '{{ csrf_token() }}');
        
        let url = '/account/addresses';
        let method = 'POST';
        
        if (editId) {
            url = `/account/addresses/${editId}`;
            formData.append('_method', 'PUT');
        }
        
        console.log('Submitting to:', url, 'Method:', method);
        
        // Disable submit button
        const $submitBtn = $('#submitBtn');
        const $btnText = $submitBtn.find('.btn-text');
        const $loading = $submitBtn.find('.loading');
        
        $btnText.addClass('d-none');
        $loading.removeClass('d-none');
        $submitBtn.prop('disabled', true);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) { 
                console.log('Success response:', response);
                toastr.success('Lưu địa chỉ thành công!');
                $('#addressModal').modal('hide');
                location.reload(); 
            },
            error: function(xhr) { 
                console.log('Error response:', xhr);
                console.log('Error status:', xhr.status);
                console.log('Error responseJSON:', xhr.responseJSON);
                console.log('Error responseText:', xhr.responseText);
                
                let errorMessage = 'Lưu địa chỉ thất bại!';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        // Laravel validation errors
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0];
                        if (Array.isArray(firstError)) {
                            errorMessage = firstError[0];
                        } else {
                            errorMessage = firstError;
                        }
                    }
                } else if (xhr.status === 404) {
                    errorMessage = 'Route không tồn tại (404)';
                } else if (xhr.status === 422) {
                    errorMessage = 'Dữ liệu không hợp lệ (422)';
                } else if (xhr.status === 500) {
                    errorMessage = 'Lỗi server (500)';
                }
                
                toastr.error(errorMessage);
            },
            complete: function() {
                // Re-enable submit button
                $btnText.removeClass('d-none');
                $loading.addClass('d-none');
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Function to toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        
        if (field && icon) {
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }

    // Handle password change form submission
    $(document).ready(function() {
        // Xử lý form đổi mật khẩu
        $('form[action="{{ route('account.password.update') }}"]').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const form = $(this);
            const submitBtn = $('#password-submit-btn');
            const btnText = submitBtn.find('.btn-text');
            const btnLoading = submitBtn.find('.btn-loading');
            
            // Lấy data trước khi disable form
            const formData = form.serialize();
            
            // Hiển thị loading và disable nút
            btnText.addClass('d-none');
            btnLoading.removeClass('d-none');
            submitBtn.prop('disabled', true);
            
            // Chỉ disable button khác, không disable input để data vẫn được gửi
            form.find('button').not('#password-submit-btn').prop('disabled', true);
            
            // Submit form via AJAX với data đã lấy trước
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    // Success - show toastr and redirect
                    if (response.message) {
                        toastr.success(response.message, 'Thành công');
                    }
                    
                    setTimeout(function() {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
                },
                error: function(xhr) {
                    // Reset UI
                    btnText.removeClass('d-none');
                    btnLoading.addClass('d-none');
                    submitBtn.prop('disabled', false);
                    form.find('button').prop('disabled', false);
                    
                    if (xhr.status === 419) {
                        // CSRF token expired
                        toastr.error('Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.', 'Lỗi');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0], 'Lỗi');
                            });
                        } else if (xhr.responseJSON?.message) {
                            toastr.error(xhr.responseJSON.message, 'Lỗi');
                        }
                    } else if (xhr.status === 429) {
                        // Rate limiting
                        const message = xhr.responseJSON?.message || 'Bạn đã thử quá nhiều lần. Vui lòng đợi.';
                        toastr.error(message, 'Lỗi');
                    } else if (xhr.status === 409) {
                        // Processing conflict
                        const message = xhr.responseJSON?.message || 'Yêu cầu đang được xử lý.';
                        toastr.warning(message, 'Cảnh báo');
                    } else {
                        // Other errors
                        const message = xhr.responseJSON?.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
                        toastr.error(message, 'Lỗi');
                    }
                }
            });
            
            // Fallback timeout để tự động enable lại sau 15s
            setTimeout(function() {
                if (submitBtn.prop('disabled')) {
                    btnText.removeClass('d-none');
                    btnLoading.addClass('d-none');
                    submitBtn.prop('disabled', false);
                    form.find('button').prop('disabled', false);
                    toastr.warning('Đã hết thời gian chờ. Vui lòng thử lại.', 'Cảnh báo');
                }
            }, 15000);
        });
    });

    // Avatar preview functionality
    $(document).ready(function() {
        const avatarInput = $('#avatar-input');
        const avatarPreview = $('#avatar-preview');
        const cancelBtn = $('#cancel-avatar');
        const originalSrc = avatarPreview.attr('data-original');
        
        // Xử lý khi chọn file ảnh
        avatarInput.on('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Kiểm tra loại file
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    toastr.error('Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG', 'Lỗi');
                    resetAvatar();
                    return;
                }
                
                // Kiểm tra kích thước file (max 1MB)
                if (file.size > 1024 * 1024) {
                    toastr.error('Kích thước ảnh không được vượt quá 1MB', 'Lỗi');
                    resetAvatar();
                    return;
                }
                
                // Tạo preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.attr('src', e.target.result);
                    cancelBtn.removeClass('d-none');
                    toastr.info('Ảnh đã được chọn. Bấm "Lưu thay đổi" để cập nhật.', 'Thông báo');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Xử lý khi bấm hủy
        cancelBtn.on('click', function() {
            resetAvatar();
            toastr.info('Đã khôi phục ảnh gốc', 'Thông báo');
        });
        
        // Function reset avatar về trạng thái ban đầu
        function resetAvatar() {
            avatarPreview.attr('src', originalSrc);
            avatarInput.val('');
            cancelBtn.addClass('d-none');
        }
        
        // Xử lý khi form được reset (nếu có lỗi validation)
        $('form').on('reset', function() {
            setTimeout(resetAvatar, 100); // Delay để đảm bảo form đã reset
        });
    });
</script>
@endpush

@push('styles')
<style>
/* Custom styles for password toggle buttons */
.password-toggle-btn {
    transition: all 0.2s ease-in-out;
}

.password-toggle-btn:hover {
    background-color: #e5e7eb !important;
    transform: scale(1.05);
}

/* Loading button styles */
#password-submit-btn {
    position: relative;
    min-width: 180px;
    transition: all 0.3s ease;
}

#password-submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    background-color: #6b7280 !important;
}

.btn-loading {
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
    animation: spinner-border 1s linear infinite;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}

/* Enhanced input focus styles */
.form-control:focus,
input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Avatar preview styles */
#avatar-preview {
    transition: all 0.3s ease;
    border: 3px solid #e5e7eb;
}

#avatar-preview:hover {
    border-color: #3b82f6;
    transform: scale(1.05);
}

#cancel-avatar {
    transition: all 0.2s ease;
}

#cancel-avatar:hover {
    background-color: #ef4444;
    color: white;
    border-color: #ef4444;
}

/* Button hover effects */
.btn-outline-gray-300 {
    border: 1px solid #d1d5db;
    color: #6b7280;
}

.btn-outline-gray-300:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
    color: #4b5563;
}

/* Gradient button enhancements */
.bg-gradient-to-r {
    background: linear-gradient(to right, #3b82f6, #2563eb);
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.bg-gradient-to-r:hover {
    background: linear-gradient(to right, #2563eb, #1d4ed8);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Error message styles */
.text-red-500 {
    color: #ef4444;
    font-size: 0.875rem;
}

/* Input group enhancements */
.input-group-modern {
    display: flex;
    align-items: stretch;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.input-group-modern input {
    border-radius: 0;
}

.input-group-modern .input-group-text {
    background-color: #f9fafb;
    border-right: none;
}

.input-group-modern .btn {
    border-left: none;
}
</style>
@endpush
@endsection
