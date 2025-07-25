<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Đăng nhập | BookBee Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="https://cdn.jsdelivr.net/gh/themesbrand/velzon/favicon.ico">

    {{-- Styles - CDN Links --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@themesbrand/velzon/css/app.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@themesbrand/velzon/css/custom.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@themesbrand/velzon/js/layout.js"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- jQuery (Toastr cần jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
    </script>
</head>

<body>
    <div class="auth-page-wrapper pt-5">
        <!-- Background -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120">
                    <path d="M0,36 C144,53.6 432,123.2 720,124 C1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- Auth content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5 mt-5">
                        <div class="card mt-5">
                            <div class="card-body p-4">
                                <div class="text-center mt-3">
                                    <img src="/images/logo-admin-layout-login.png" alt="logo" height="60" class="mb-2">
                                    <h5 class="text-primary">Chào mừng trở lại!</h5>
                                    <p class="text-muted">Đăng nhập để tiếp tục với BookBee Admin</p>
                                </div>

                                <div class="p-2 mt-4">
                                    {{-- Hiển thị lỗi đăng nhập chung (key "login") --}}
                                    @error('login')
                                        <div class="alert alert-danger" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    {{-- Form login --}}
                                    <form method="POST" action="{{ route('admin.login.submit') }}"
                                        class="needs-validation" novalidate>
                                        @csrf
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" placeholder="Nhập email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Mật khẩu</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" name="password" id="password"
                                                    class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                    placeholder="Nhập mật khẩu" minlength="6">
                                                <button
                                                    class="btn btn-link position-absolute end-0 top-0 text-muted password-addon"
                                                    type="button" id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">Nhớ tài khoản</label>
                                        </div> --}}

                                        <div class="mt-4 mb-5">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="ri-login-box-line align-middle me-1"></i> Đăng nhập
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

        <!-- Footer -->
        <footer class="footer text-center">
            <div class="container">
                <p class="mb-0 text-muted">
                    @
                    <script>
                        document.write(new Date().getFullYear())
                    </script> BookBee Admin Dashboard
                    <i class="mdi mdi-heart text-danger"></i>
                </p>
            </div>
        </footer>
    </div>

    <!-- Scripts - CDN Links -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simplebar@6.2.5/dist/simplebar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/node-waves@0.7.6/dist/waves.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.0/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lordicon-element@2.1.0/dist/lordicon-element.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@themesbrand/velzon/js/plugins.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@themesbrand/velzon/js/pages/particles.app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@themesbrand/velzon/js/pages/password-addon.init.js"></script>
    {!! Toastr::message() !!}
</body>

</html>
