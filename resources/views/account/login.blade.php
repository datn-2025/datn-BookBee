
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Trang tài khoản')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @stack('styles')
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
        }

        .left-section {
            background: #2c3e50;
            color: #fff;
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
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

        .social-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .social-button {
            border: 2px solid #e0e0e0;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 44px;
            width: 44px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .social-button:hover {
            color: #fff;
            transform: translateY(-2px);
        }

        .social-button.google:hover {
            background: #db4437;
            border-color: #db4437;
            box-shadow: 0 4px 12px rgba(219, 68, 55, 0.2);
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
            color: #777;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #ddd;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
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
            outline: none;
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

        .forgot-password {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            display: block;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: #2c3e50;
            text-decoration: underline;
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

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .divider {
            color: #666;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body style="margin:0; min-height:100vh;">
    <div class="auth-container">
        <div class="left-section">
            <div class="content-wrapper">
                <h1>Xin chào!</h1>
                <p>Nhập thông tin của bạn và bắt đầu hành trình với chúng tôi</p>
                <a href="{{ route('register') }}" class="btn btn-outline">ĐĂNG KÝ</a>
            </div>
        </div>
        <div class="right-section">
            <div class="form-container">
                <h2>Đăng nhập</h2>
                <div class="social-buttons">
                    <a href="{{ route('auth.google') }}" class="social-button google">
                        <i class="fab fa-google"></i>
                    </a>
                </div>
                <div class="divider">hoặc sử dụng tài khoản của bạn</div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" required autofocus
                            value="{{ old('email') }}" />
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required />
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-password">Quên mật khẩu?</a>
                    <button type="submit" class="btn btn-primary">ĐĂNG NHẬP</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
