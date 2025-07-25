

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quên mật khẩu</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f5f0;
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="left-section">
        <div class="content-wrapper">
            <h1>Quên mật khẩu?</h1>
            <p>Nhập email của bạn để nhận liên kết đặt lại mật khẩu</p>
            <a href="{{ route('login') }}" class="btn btn-outline">ĐĂNG NHẬP</a>
        </div>
    </div>
     <div class="right-section">
            <div class="form-container">
                <h2>Đặt lại mật khẩu</h2>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required value="{{ old('email') }}" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">GỬI LIÊN KẾT ĐẶT LẠI MẬT KHẨU</button>
                </form>
            </div>
        </div>
</div>
</div>

@push('styles')
<style>
    .auth-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        width: 900px;
        max-width: 100%;
        min-height: 540px;
        display: flex;
        overflow: hidden;
    }

    .left-section {
        background: #2c3e50;
        color: #fff;
        width: 40%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        position: relative;
        overflow: hidden;
    }

    .left-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-4.0.3') center/cover;
        opacity: 0.2;
        z-index: 0;
    }

    .content-wrapper {
        max-width: 300px;
        position: relative;
        z-index: 1;
    }

    .left-section h1 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 2.5rem;
        line-height: 1.2;
    }

    .left-section p {
        font-size: 15px;
        line-height: 1.8;
        margin-bottom: 30px;
        color: rgba(255, 255, 255, 0.9);
    }

    .right-section {
        width: 60%;
        background: #fff;
        display: flex;
        align-items: center;
    }

    .form-container {
        padding: 40px;
        width: 100%;
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 30px;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 2rem;
        color: #2c3e50;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        padding: 14px 16px;
        width: 100%;
        margin-bottom: 15px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: #fff;
        border-color: #2c3e50;
        box-shadow: 0 4px 12px rgba(44, 62, 80, 0.1);
    }

    .btn-outline {
        border: 2px solid #fff;
        background: transparent;
        color: #fff;
        padding: 14px 48px;
        border-radius: 30px;
        text-transform: uppercase;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        letter-spacing: 1px;
        font-size: 14px;
    }

    .btn-outline:hover {
        background: #fff;
        color: #2c3e50;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background: #2c3e50;
        border: none;
        color: #fff;
        padding: 14px 48px;
        border-radius: 30px;
        text-transform: uppercase;
        font-weight: 500;
        width: 100%;
        margin-top: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        letter-spacing: 1px;
        font-size: 14px;
    }

    .btn-primary:hover {
        background: #34495e;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
    }

    .alert {
        margin-bottom: 20px;
        padding: 16px;
        border-radius: 6px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #edf7ed;
        color: #1e4620;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #fff3f3;
        border: 1px solid #ffcdd2;
        color: #e53935;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }
</style>
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
        };
    </script>
    {!! Toastr::message() !!}
    @stack('scripts')
</body>
</html>

