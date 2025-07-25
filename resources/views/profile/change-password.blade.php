@extends('layouts.app')

@section('title', 'Đổi mật khẩu - BookBee')

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
    .form-control {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 12px;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        transform: translateY(-1px);
    }
    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-label i {
        color: #6366f1;
        font-size: 0.9em;
    }
    .btn-save {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        border-radius: 10px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .btn-save::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            to right,
            transparent,
            rgba(255, 255, 255, 0.2),
            transparent
        );
        transition: 0.5s;
    }
    .btn-save:hover::before {
        left: 100%;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }
    .input-group-text {
        background: transparent;
        border-right: none;
        color: #6366f1;
    }
    .input-group .form-control {
        border-left: none;
    }
    .input-group .btn-outline-secondary {
        border-color: #e5e7eb;
        color: #6366f1;
    }
    .input-group .btn-outline-secondary:hover {
        background-color: #f3f4f6;
        color: #4f46e5;
    }
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        .sidebar {
            margin-bottom: 20px;
            position: static;
        }
        .main-content {
            padding: 25px;
        }
    }
</style>

<!-- Thêm CSS cho Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

<!-- Thêm JS cho Toastr và jQuery -->
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
                <a href="{{ route('account.addresses') }}">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Quản lý địa chỉ</span>
                </a>
                <a href="{{ route('account.changePassword') }}" class="active">
                    <i class="fas fa-lock"></i>
                    <span>Đổi mật khẩu</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="main-content">
                <h2 class="mb-3">
                    <i class="fas fa-lock text-primary me-2"></i>
                    Đổi mật khẩu
                </h2>
                <p class="text-muted mb-4">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
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
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = event.currentTarget.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection
