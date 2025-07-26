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
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=200' }}" 
                                     alt="Avatar" class="w-32 h-32 object-cover rounded-full shadow-md mx-auto">
                                <div class="mt-3">
                                    <label for="avatar-input" class="btn-save w-full">
                                        <i class="fas fa-camera"></i> Chọn ảnh
                                    </label>
                                    <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png" class="hidden">
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
                            <button type="submit" class="btn btn-save w-full">
                                <i class="fas fa-save me-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            @elseif(request('type', '1') == 3) <!-- Change Password -->
                <form method="POST" action="{{ route('account.password.update') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-lock text-primary me-1"></i>
                            Mật khẩu hiện tại
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input id="current_password" type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-key text-primary me-1"></i>
                            Mật khẩu mới
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-check-circle text-primary me-1"></i>
                            Xác nhận mật khẩu mới
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <input id="password_confirmation" type="password" 
                                   class="form-control" 
                                   name="password_confirmation" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('account.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-key me-2"></i>Cập nhật mật khẩu
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
    
    // Function để load provinces
    function loadProvinces(callback = null) {
        console.log('Loading provinces...');
        $.getJSON('https://provinces.open-api.vn/api/p/', function(provinces) {
            console.log('Provinces loaded:', provinces.length);
            $('#city').html('<option value="">Chọn Tỉnh/Thành phố</option>');
            provinces.forEach(function(province) {
                $("#city").append(`<option value="${province.code}">${province.name}</option>`);
            });
            if (callback) callback(provinces);
        }).fail(function(xhr, status, error) {
            console.error('Failed to load provinces:', error);
            toastr.error('Không thể tải dữ liệu tỉnh thành. Vui lòng thử lại!');
        });
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
                let selectedProvinceCode = null;
                
                provinces.forEach(function(province) {
                    if (province.name === data.city) {
                        selectedProvinceCode = province.code;
                        $("#city").val(province.code);
                        console.log('Selected province:', province.name, province.code);
                    }
                });
                
                if (selectedProvinceCode) {
                    // Load districts
                    $.getJSON(`https://provinces.open-api.vn/api/p/${selectedProvinceCode}?depth=2`, function(provinceData) {
                        $("#district").html('<option value="">Chọn Quận/Huyện</option>');
                        let selectedDistrictCode = null;
                        
                        provinceData.districts.forEach(function(district) {
                            $("#district").append(`<option value="${district.code}">${district.name}</option>`);
                            if (district.name === data.district) {
                                selectedDistrictCode = district.code;
                                $("#district").val(district.code);
                                console.log('Selected district:', district.name, district.code);
                            }
                        });
                        
                        if (selectedDistrictCode) {
                            // Load wards
                            $.getJSON(`https://provinces.open-api.vn/api/d/${selectedDistrictCode}?depth=2`, function(districtData) {
                                $("#ward").html('<option value="">Chọn Phường/Xã</option>');
                                districtData.wards.forEach(function(ward) {
                                    $("#ward").append(`<option value="${ward.code}">${ward.name}</option>`);
                                    if (ward.name === data.ward) {
                                        $("#ward").val(ward.code);
                                        console.log('Selected ward:', ward.name, ward.code);
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
            const provinceCode = $(this).val();
            console.log('Province selected:', provinceCode);
            
            // Reset quận và phường
            $("#district").html('<option value="">Chọn Quận/Huyện</option>');
            $("#ward").html('<option value="">Chọn Phường/Xã</option>');
            
            if (provinceCode) {
                console.log('Loading districts for province:', provinceCode);
                // Lấy quận/huyện
                $.getJSON(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`, function(provinceData) {
                    console.log('Districts loaded:', provinceData.districts?.length);
                    if (provinceData.districts) {
                        provinceData.districts.forEach(function(district) {
                            $("#district").append(`<option value="${district.code}">${district.name}</option>`);
                        });
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Failed to load districts:', error);
                    toastr.error('Không thể tải dữ liệu quận/huyện');
                });
            }
        });

        // Xử lý khi chọn quận
        $("#district").change(function() {
            const districtCode = $(this).val();
            console.log('District selected:', districtCode);
            
            // Reset phường
            $("#ward").html('<option value="">Chọn Phường/Xã</option>');
            
            if (districtCode) {
                console.log('Loading wards for district:', districtCode);
                // Lấy phường/xã
                $.getJSON(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`, function(districtData) {
                    console.log('Wards loaded:', districtData.wards?.length);
                    if (districtData.wards) {
                        districtData.wards.forEach(function(ward) {
                            $("#ward").append(`<option value="${ward.code}">${ward.name}</option>`);
                        });
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Failed to load wards:', error);
                    toastr.error('Không thể tải dữ liệu phường/xã');
                });
            }
        });
    });
    // Submit form
    $('#addressForm').submit(function(e) {
        e.preventDefault();
        
        // Lấy tên thực tế của tỉnh/quận/phường thay vì code
        const cityName = $('#city option:selected').text();
        const districtName = $('#district option:selected').text();
        const wardName = $('#ward option:selected').text();
        
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
</script>
@endpush
@endsection
