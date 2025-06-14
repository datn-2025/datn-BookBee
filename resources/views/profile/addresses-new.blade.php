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
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #6366f1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<!-- External CSS and JS Libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<!-- Loading Overlay -->
<div class="loading-overlay d-none">
    <div class="spinner"></div>
</div>

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
                <a href="{{ route('account.orders') }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Đơn hàng của tôi</span>
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
                    <button class="btn btn-primary" id="addAddressBtn">
                        <i class="fas fa-plus me-2"></i>
                        Thêm địa chỉ mới
                    </button>
                </div>

                <!-- Danh sách địa chỉ -->
                <div id="addressList">
                    @if(isset($addresses) && $addresses->count() > 0)
                        @foreach($addresses as $address)
                            <div class="address-card {{ $address->is_default ? 'default' : '' }}" data-id="{{ $address->id }}">
                                @if($address->is_default)
                                    <div class="default-badge">
                                        <i class="fas fa-star me-1"></i>
                                        Mặc định
                                    </div>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-2">{{ $address->recipient_name }}</h5>
                                        <p class="mb-1"><i class="fas fa-phone text-primary me-2"></i>{{ $address->phone }}</p>
                                        <p class="mb-0 text-muted">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            {{ $address->address_detail }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        @if(!$address->is_default)
                                            <button class="btn btn-success btn-sm me-2 set-default" data-id="{{ $address->id }}">
                                                <i class="fas fa-star"></i>
                                                Đặt mặc định
                                            </button>
                                        @endif
                                        <button class="btn btn-warning btn-sm me-2 edit-address" data-id="{{ $address->id }}">
                                            <i class="fas fa-edit"></i>
                                            Sửa
                                        </button>
                                        @if(!$address->is_default)
                                            <button class="btn btn-danger btn-sm delete-address" data-id="{{ $address->id }}">
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
                            <button class="btn btn-primary" id="addAddressBtn2">
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
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Thêm địa chỉ mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addressForm" method="POST" action="{{ route('account.addresses.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="recipient_name" class="form-label">Họ và tên người nhận *</label>
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Số điện thoại *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-4">
                            <label for="province" class="form-label">Tỉnh/Thành phố *</label>
                            <select class="form-select" id="province" name="city" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="district" class="form-label">Quận/Huyện *</label>
                            <select class="form-select" id="district" name="district" required>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ward" class="form-label">Phường/Xã *</label>
                            <select class="form-select" id="ward" name="ward" required>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="address_detail" class="form-label">Địa chỉ cụ thể *</label>
                            <textarea class="form-control" id="address_detail" name="address_detail" rows="3" placeholder="Số nhà, tên đường..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Address Management Script -->
<script src="{{ asset('js/address-management.js') }}"></script>

<script>
// Display session messages
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

// Setup CSRF token for AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Additional event handlers for duplicate button IDs
$(document).ready(function() {
    $('#addAddressBtn2').on('click', function() {
        $('#addAddressBtn').trigger('click');
    });
});
</script>

@endsection
