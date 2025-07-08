@extends('layouts.backend')

@section('title', 'Quản lý tài khoản')

@section('content')
<div class="container-fluid">
    <!-- Start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Quản lý tài khoản Admin</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tài khoản</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#profile-tab" role="tab">
                                <i class="fas fa-user me-2"></i>Thông tin cá nhân
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#password-tab" role="tab">
                                <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content p-3 text-muted">
                        <!-- Profile Tab -->
                        <div class="tab-pane active" id="profile-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <div class="avatar-container mb-3">
                                            <img src="{{ $admin->avatar ? asset('storage/' . $admin->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($admin->name) . '&background=6366f1&color=fff&size=200' }}" 
                                                 alt="Avatar" 
                                                 class="img-fluid rounded-circle border-3 border-light shadow" 
                                                 style="width: 150px; height: 150px; object-fit: cover;">
                                        </div>
                                        <h5 class="font-size-16 mb-1">{{ $admin->name }}</h5>
                                        <p class="text-muted mb-2">{{ $admin->role->name ?? 'Admin' }}</p>
                                        <div class="mt-3">
                                            <div class="text-muted small">
                                                <i class="fas fa-envelope me-1"></i>{{ $admin->email }}
                                            </div>
                                            @if($admin->phone)
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-phone me-1"></i>{{ $admin->phone }}
                                                </div>
                                            @endif
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-calendar me-1"></i>Tham gia: {{ $admin->created_at->format('d/m/Y') }}
                                            </div>
                                            @if($admin->last_login_at)
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-clock me-1"></i>Đăng nhập cuối: {{ $admin->last_login_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Tên <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" 
                                                       class="form-control @error('name') is-invalid @enderror" 
                                                       name="name" 
                                                       value="{{ old('name', $admin->name) }}" 
                                                       required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="email" 
                                                       class="form-control @error('email') is-invalid @enderror" 
                                                       name="email" 
                                                       id="email_input"
                                                       value="{{ old('email', $admin->email) }}" 
                                                       data-original="{{ $admin->email }}"
                                                       required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Password confirmation for email change -->
                                        <div class="row mb-3" id="password_confirmation_field" style="display: none;">
                                            <label class="col-sm-2 col-form-label">Xác thực mật khẩu <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" 
                                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                           id="password_confirmation_input" 
                                                           name="password_confirmation"
                                                           placeholder="Nhập mật khẩu để xác thực thay đổi email">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation_input')">
                                                        <i class="far fa-eye" id="password_confirmation_input_icon"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">Nhập mật khẩu hiện tại để xác thực thay đổi email</small>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Số điện thoại</label>
                                            <div class="col-sm-10">
                                                <input type="text" 
                                                       class="form-control @error('phone') is-invalid @enderror" 
                                                       name="phone" 
                                                       value="{{ old('phone', $admin->phone) }}">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-2 col-form-label">Avatar</label>
                                            <div class="col-sm-10">
                                                <input type="file" 
                                                       class="form-control @error('avatar') is-invalid @enderror" 
                                                       name="avatar" 
                                                       accept="image/*">
                                                <small class="text-muted">Chỉ chấp nhận file hình ảnh (JPEG, PNG, JPG, GIF). Tối đa 2MB.</small>
                                                @error('avatar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-10 offset-sm-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>Cập nhật thông tin
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Password Tab -->
                        <div class="tab-pane" id="password-tab" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <form action="{{ route('admin.profile.password.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('current_password') is-invalid @enderror" 
                                                       id="current_password" 
                                                       name="current_password" 
                                                       required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                    <i class="far fa-eye" id="current_password_icon"></i>
                                                </button>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password" 
                                                       required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                    <i class="far fa-eye" id="password_icon"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                                                <div class="mt-1">
                                                    <small>Độ mạnh: <span id="password_strength" class="fw-bold"></span></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                       id="password_confirmation" 
                                                       name="password_confirmation" 
                                                       required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                                    <i class="far fa-eye" id="password_confirmation_icon"></i>
                                                </button>
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-key me-2"></i>Đổi mật khẩu
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-container {
        position: relative;
        display: inline-block;
    }

    .nav-tabs-custom .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        color: #495057;
    }

    .nav-tabs-custom .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .nav-tabs-custom .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        
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

    // Monitor email changes
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email_input');
        const passwordField = document.getElementById('password_confirmation_field');
        const passwordInput = document.getElementById('password_confirmation_input');
        
        if (emailInput && passwordField) {
            const originalEmail = emailInput.getAttribute('data-original');
            
            emailInput.addEventListener('input', function() {
                if (this.value !== originalEmail && this.value.trim() !== '') {
                    passwordField.style.display = 'block';
                    passwordInput.setAttribute('required', '');
                } else {
                    passwordField.style.display = 'none';
                    passwordInput.removeAttribute('required');
                    passwordInput.value = '';
                }
            });
        }
    });

    // Preview avatar on file select
    document.querySelector('input[name="avatar"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.avatar-container img').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Password strength indicator
    function checkPasswordStrength(password) {
        const strengthMeter = document.getElementById('password_strength');
        if (!strengthMeter) return;
        
        let strength = 0;
        const checks = [
            /.{8,}/, // At least 8 characters
            /[a-z]/, // Lowercase letter
            /[A-Z]/, // Uppercase letter
            /[0-9]/, // Number
            /[^A-Za-z0-9]/ // Special character
        ];
        
        checks.forEach(check => {
            if (check.test(password)) strength++;
        });
        
        const strengthTexts = ['Rất yếu', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
        const strengthColors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'];
        
        strengthMeter.textContent = strengthTexts[strength - 1] || '';
        strengthMeter.style.color = strengthColors[strength - 1] || '#6c757d';
    }

    // Add password strength indicator to new password field
    const newPasswordField = document.getElementById('password');
    if (newPasswordField) {
        newPasswordField.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }
</script>
@endpush
@endsection
