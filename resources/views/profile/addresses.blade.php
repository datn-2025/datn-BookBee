@extends('layouts.app')

@section('title', 'Quản lý địa chỉ - BookBee')

@section('content')
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f0f2f5;
        min-height: 100vh;
    }
    .sidebar {
        min-width: 250px;
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        position: sticky;
        top: 60px;
    }
    .sidebar a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        margin-bottom: 8px;
        color: #4b5563;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .sidebar a::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }
    .sidebar a:hover::before,
    .sidebar a.active::before {
        transform: scaleY(1);
    }
    .sidebar a i {
        margin-right: 12px;
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }
    .sidebar a:hover,
    .sidebar a.active {
        background: #f3f4f6;
        color: #6366f1;
        transform: translateX(5px);
    }
    .sidebar a:hover i,
    .sidebar a.active i {
        transform: scale(1.2);
    }
    .main-content {
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .address-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        position: relative;
        background: #fff;
    }
    .address-card:hover {
        border-color: #6366f1;
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
        transform: translateY(-2px);
    }
    .address-card.default {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
    }
    .default-badge {
        position: absolute;
        top: -8px;
        right: 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 12px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        transform: translateY(-1px);
    }
    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border: none;
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
    }
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: none;
    }
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
    }
    .btn-sm {
        padding: 8px 16px;
        font-size: 0.875rem;
    }
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    .modal-header {
        border-bottom: 1px solid #f3f4f6;
        padding: 24px;
    }
    .modal-body {
        padding: 24px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #d1d5db;
    }
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<!-- Thêm CSS cho Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
    @if(Session::has('success'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        }
        toastr.success("{{ Session::get('success') }}", "Thành công!");
    @endif
    
    @if(Session::has('error'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        }
        toastr.error("{{ Session::get('error') }}", "Lỗi!");
    @endif
</script>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="sidebar">
                <a href="{{ route('account.profile') }}">
                    <i class="fas fa-user"></i>
                    <span>Thông tin cá nhân</span>
                </a>
                <a href="{{ route('account.addresses') }}" class="active">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Quản lý địa chỉ</span>
                </a>
                <a href="{{ route('account.changePassword') }}">
                    <i class="fas fa-lock"></i>
                    <span>Đổi mật khẩu</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Quản lý địa chỉ
                    </h2>
                    <button class="btn btn-primary" onclick="openAddressModal()">
                        <i class="fas fa-plus me-2"></i>
                        Thêm địa chỉ mới
                    </button>
                </div>

                <!-- Danh sách địa chỉ -->
                <div id="addressList">
                    @if(isset($addresses) && $addresses->count() > 0)
                        @foreach($addresses as $address)
                            <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                                @if($address->is_default)
                                    <div class="default-badge">
                                        <i class="fas fa-star me-1"></i>
                                        Mặc định
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-0 text-muted">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            {{ $address->address_detail }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if(!$address->is_default)
                                            <button class="btn btn-success btn-sm me-2" onclick="setDefaultAddress('{{ $address->id }}')">
                                                <i class="fas fa-star"></i>
                                                Đặt mặc định
                                            </button>
                                        @endif
                                        <button class="btn btn-warning btn-sm me-2" onclick="editAddress('{{ $address->id }}')">
                                            <i class="fas fa-edit"></i>
                                            Sửa
                                        </button>
                                        @if(!$address->is_default)
                                            <button class="btn btn-danger btn-sm" onclick="deleteAddress('{{ $address->id }}')">
                                                <i class="fas fa-trash"></i>
                                                Xóa
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-map-marker-alt"></i>
                            <h4>Chưa có địa chỉ nào</h4>
                            <p>Thêm địa chỉ giao hàng để thuận tiện cho việc đặt hàng</p>
                            <button class="btn btn-primary" onclick="openAddressModal()">
                                <i class="fas fa-plus me-2"></i>
                                Thêm địa chỉ đầu tiên
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm/sửa địa chỉ -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Thêm địa chỉ mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addressForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">                        <div class="col-md-4">
                            <label for="tinh" class="form-label">Tỉnh/Thành phố</label>
                            <select class="form-select" id="tinh" name="city_code" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                            <input type="hidden" id="ten_tinh" name="city">
                        </div>
                        <div class="col-md-4">
                            <label for="quan" class="form-label">Quận/Huyện</label>
                            <select class="form-select" id="quan" name="district_code" required>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                            <input type="hidden" id="ten_quan" name="district">
                        </div>
                        <div class="col-md-4">
                            <label for="phuong" class="form-label">Phường/Xã</label>
                            <select class="form-select" id="phuong" name="ward_code" required>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                            <input type="hidden" id="ten_phuong" name="ward">
                        </div>
                        <div class="col-12">
                            <label for="address_detail" class="form-label">Địa chỉ cụ thể</label>
                            <textarea class="form-control" id="address_detail" name="address_detail" rows="3" placeholder="Số nhà, tên đường..." required></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1">
                                <label class="form-check-label" for="is_default">
                                    Đặt làm địa chỉ mặc định
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-text">Lưu địa chỉ</span>
                        <div class="loading d-none"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let isEdit = false;
    let editId = null;

    $(document).ready(function() {
        // Lấy tỉnh thành
        $.getJSON('https://provinces.open-api.vn/api/p/', function(provinces) {
            provinces.forEach(function(province) {
                $("#tinh").append(`<option value="${province.code}">${province.name}</option>`);
            });
        });        // Xử lý khi chọn tỉnh
        $("#tinh").change(function() {
            const provinceCode = $(this).val();
            const provinceName = $(this).find("option:selected").text();
            $("#ten_tinh").val(provinceName);
            
            // Reset quận và phường
            $("#quan").html('<option value="">Chọn Quận/Huyện</option>');
            $("#phuong").html('<option value="">Chọn Phường/Xã</option>');
            $("#ten_quan").val('');
            $("#ten_phuong").val('');
            
            if (provinceCode) {
                // Lấy quận/huyện
                $.getJSON(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`, function(provinceData) {
                    provinceData.districts.forEach(function(district) {
                        $("#quan").append(`<option value="${district.code}">${district.name}</option>`);
                    });
                });
            }
        });

        // Xử lý khi chọn quận
        $("#quan").change(function() {
            const districtCode = $(this).val();
            const districtName = $(this).find("option:selected").text();
            $("#ten_quan").val(districtName);
            
            // Reset phường
            $("#phuong").html('<option value="">Chọn Phường/Xã</option>');
            $("#ten_phuong").val('');
            
            if (districtCode) {
                // Lấy phường/xã
                $.getJSON(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`, function(districtData) {
                    districtData.wards.forEach(function(ward) {
                        $("#phuong").append(`<option value="${ward.code}">${ward.name}</option>`);
                    });
                });
            }
        });

        // Xử lý khi chọn phường
        $("#phuong").change(function() {
            const wardName = $(this).find("option:selected").text();
            $("#ten_phuong").val(wardName);
        });        // Submit form
        $('#addressForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate that province/district/ward names are selected
            if (!$('#ten_tinh').val() || !$('#ten_quan').val() || !$('#ten_phuong').val()) {
                toastr.error('Vui lòng chọn đầy đủ Tỉnh/Thành phố, Quận/Huyện và Phường/Xã');
                return;
            }
            
            const $submitBtn = $('#submitBtn');
            const $btnText = $submitBtn.find('.btn-text');
            const $loading = $submitBtn.find('.loading');
            
            // Hiển thị loading
            $btnText.addClass('d-none');
            $loading.removeClass('d-none');
            $submitBtn.prop('disabled', true);
            
            // Chuẩn bị data
            const formData = new FormData(this);
            
            // Set URL và method based on edit mode
            const url = isEdit ? `/account/addresses/${editId}` : '{{ route("account.addresses.store") }}';
            if (isEdit) {
                formData.append('_method', 'PUT');
            }
            
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message || 'Lưu địa chỉ thành công!');
                    $('#addressModal').modal('hide');
                    location.reload(); // Reload trang để cập nhật danh sách
                },
                error: function(xhr) {
                    console.log('Error response:', xhr.responseJSON);
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        const message = xhr.responseJSON?.message || 'Có lỗi xảy ra. Vui lòng thử lại!';
                        toastr.error(message);
                    }
                },
                complete: function() {
                    // Ẩn loading
                    $btnText.removeClass('d-none');
                    $loading.addClass('d-none');
                    $submitBtn.prop('disabled', false);
                }
            });
        });
    });    function openAddressModal() {
        isEdit = false;
        editId = null;
        $('#addressModalLabel').html('<i class="fas fa-map-marker-alt me-2"></i>Thêm địa chỉ mới');
        $('#addressForm')[0].reset();
        
        // Reset dropdowns and hidden fields
        $("#tinh").html('<option value="">Chọn Tỉnh/Thành phố</option>');
        $("#quan").html('<option value="">Chọn Quận/Huyện</option>');
        $("#phuong").html('<option value="">Chọn Phường/Xã</option>');
        $("#ten_tinh").val('');
        $("#ten_quan").val('');
        $("#ten_phuong").val('');
        
        // Load provinces
        $.getJSON('https://provinces.open-api.vn/api/p/', function(provinces) {
            provinces.forEach(function(province) {
                $("#tinh").append(`<option value="${province.code}">${province.name}</option>`);
            });
        });
        
        $('#addressModal').modal('show');
    }

    function editAddress(id) {
        isEdit = true;
        editId = id;
        $('#addressModalLabel').html('<i class="fas fa-edit me-2"></i>Chỉnh sửa địa chỉ');
        
        // Load thông tin địa chỉ
        $.get(`/account/addresses/${id}/edit`, function(data) {
            $('#address_detail').val(data.address_detail);
            $('#is_default').prop('checked', data.is_default);
            
            // Load provinces và select current values
            $.getJSON('https://provinces.open-api.vn/api/p/', function(provinces) {
                $("#tinh").html('<option value="">Chọn Tỉnh/Thành phố</option>');
                
                provinces.forEach(function(province) {
                    const selected = province.name === data.city ? 'selected' : '';
                    $("#tinh").append(`<option value="${province.code}" ${selected}>${province.name}</option>`);
                });
                
                if (data.city) {
                    $("#ten_tinh").val(data.city);
                    // Trigger change để load districts
                    $("#tinh").trigger('change');
                    
                    // Set district after provinces are loaded
                    setTimeout(function() {
                        $("#quan").find('option').each(function() {
                            if ($(this).text() === data.district) {
                                $(this).prop('selected', true);
                                $("#ten_quan").val(data.district);
                                $("#quan").trigger('change');
                                
                                // Set ward after districts are loaded
                                setTimeout(function() {
                                    $("#phuong").find('option').each(function() {
                                        if ($(this).text() === data.ward) {
                                            $(this).prop('selected', true);
                                            $("#ten_phuong").val(data.ward);
                                        }
                                    });
                                }, 500);
                            }
                        });
                    }, 500);
                }
            });
        });
        
        $('#addressModal').modal('show');
    }

    function setDefaultAddress(id) {
        if (confirm('Bạn có muốn đặt địa chỉ này làm mặc định?')) {
            $.ajax({
                url: `/account/addresses/${id}/set-default`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success('Đã đặt làm địa chỉ mặc định!');
                    location.reload();
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                }
            });
        }
    }

    function deleteAddress(id) {
        if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
            $.ajax({
                url: `/account/addresses/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success('Đã xóa địa chỉ thành công!');
                    location.reload();
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                }
            });
        }
    }
</script>
@endpush
@endsection