@extends('layouts.account.layout')

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
                                <form id="addressForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addressModalLabel">Thêm/Sửa địa chỉ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="address_id">
                                        <div class="mb-3">
                                            <label class="form-label">Tỉnh/Thành phố</label>
                                            <select id="city" name="city" class="form-select" required></select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Quận/Huyện</label>
                                            <select id="district" name="district" class="form-select" required></select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phường/Xã</label>
                                            <select id="ward" name="ward" class="form-select" required></select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Địa chỉ chi tiết</label>
                                            <input type="text" name="address_detail" id="address_detail" class="form-control" required>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
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
    function openAddressModal(isEdit = false, data = null) {
        editId = null;
        $('#addressForm')[0].reset();
        $('#address_id').val('');
        $('#is_default').prop('checked', false);
        if (isEdit && data) {
            editId = data.id;
            $('#address_id').val(data.id);
            $('#city').html(`<option selected>${data.city}</option>`);
            $('#district').html(`<option selected>${data.district}</option>`);
            $('#ward').html(`<option selected>${data.ward}</option>`);
            $('#address_detail').val(data.address_detail);
            $('#is_default').prop('checked', data.is_default);
        } else {
            $('#city, #district, #ward').html('<option value="">Chọn</option>');
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
    $(function() {
        $.getJSON('https://provinces.open-api.vn/api/p/', function(provinces) {
            $('#city').append(provinces.map(p => `<option value="${p.name}">${p.name}</option>`));
        });
        $('#city').change(function() {
            const city = $(this).val();
            $('#district').html('<option value="">Chọn</option>');
            $('#ward').html('<option value="">Chọn</option>');
            if (city) {
                $.getJSON(`https://provinces.open-api.vn/api/p/${encodeURIComponent(city)}?depth=2`, function(data) {
                    $('#district').append(data.districts.map(d => `<option value="${d.name}">${d.name}</option>`));
                });
            }
        });
        $('#district').change(function() {
            const city = $('#city').val();
            const district = $(this).val();
            $('#ward').html('<option value="">Chọn</option>');
            if (city && district) {
                $.getJSON(`https://provinces.open-api.vn/api/d/${encodeURIComponent(district)}?depth=2`, function(data) {
                    $('#ward').append(data.wards.map(w => `<option value="${w.name}">${w.name}</option>`));
                });
            }
        });
    });
    // Submit form
    $('#addressForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        let url = '/account/addresses';
        let method = 'POST';
        if (editId) {
            url = `/account/addresses/${editId}`;
            method = 'PUT';
        }
        $.ajax({
            url: url,
            type: method,
            data: formData + '&_token={{ csrf_token() }}',
            success: function() { location.reload(); },
            error: function(xhr) { toastr.error('Lưu địa chỉ thất bại!'); }
        });
    });
</script>
@endpush
@endsection
