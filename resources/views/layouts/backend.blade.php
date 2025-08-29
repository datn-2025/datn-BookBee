<!DOCTYPE html>
<html lang="vi" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <!-- Meta & Title -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="BookBee Admin Dashboard" name="description" />
    <meta content="Your Team" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Pusher Configuration -->
    <meta name="pusher-key" content="{{ env('VITE_PUSHER_APP_KEY') }}">
    <meta name="pusher-cluster" content="{{ env('VITE_PUSHER_APP_CLUSTER') }}">
    <meta name="user-id" content="{{ auth()->id() ?? '' }}">
    <meta name="user-role" content="{{ auth()->user()->role->name ?? '' }}">
    
    <title>{{ get_setting()->name_website ?? 'BookBee' }} - @yield('title')</title>

    <!-- App favicon -->
    <link rel="shortcut icon"
        href="{{ asset('storage/' . (get_setting() ? get_setting()->favicon : 'default_favicon.ico')) }}" />

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" />

    <!-- Icon Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.3.67/css/materialdesignicons.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel= "stylesheet"
        href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
     <!-- Chat CSS -->
    <link href="{{ asset('css/chat.css') }}" rel="stylesheet" />
     @vite(['resources/js/app.js', 'resources/css/app.css'])
     <!-- Laravel Echo / Pusher Setup -->
    <script>
        // Import Echo from Vite
        window.Pusher = window.Pusher || {};
        
        // Setup Echo when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo !== 'undefined') {
                console.log('Laravel Echo initialized successfully');
            } else {
                console.warn('Laravel Echo not available');
            }
        });
    </script>
    <!-- Plugin CSS -->
    <link href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <!-- TinyMCE -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#description, #content',
            height: 300,
            menubar: false,
            plugins: 'lists link image preview code fullscreen',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image | preview code fullscreen',
            placeholder: 'Nhập mô tả chi tiết...'
        });
    </script>

    <!-- Toastr Config -->
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "3000",
            extendedTimeOut: "1000",
            preventDuplicates: true,
            showDuration: "300",
            hideDuration: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
     <!-- Chat Configuration -->
    <script>
        window.currentUserId = {{ auth('admin')->check() ? auth('admin')->id() : (auth()->check() ? auth()->id() : 'null') }};
        window.Laravel = {
            user: {
                id: {{ auth('admin')->check() ? auth('admin')->id() : (auth()->check() ? auth()->id() : 'null') }},
                name: '{{ auth('admin')->check() ? auth('admin')->user()->name : (auth()->check() ? auth()->user()->name : '') }}'
            }
        };
    </script>

    <!-- Custom inline styles -->
    <style>
        .collapse.show+a .toggle-icon,
        .nav-link[aria-expanded="true"] .toggle-icon {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }

        /* Simple Validation Error Styling */
        .invalid-feedback {
            display: block !important;
            background-color: #f8d7da;
            color: #721c24;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
            font-size: 0.875rem;
            margin-top: 6px;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus,
        .form-select.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Simple success feedback styling */
        .valid-feedback {
            display: block !important;
            background-color: #d4edda;
            color: #155724;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            font-size: 0.875rem;
            margin-top: 6px;
        }

        /* Fix cho menu dropdown */
        .app-menu {
            z-index: 1050 !important;
            display: block !important;
            visibility: visible !important;
            position: fixed !important;
        }
        
        .navbar-nav .nav-item .collapse {
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-item .collapse.show {
            display: block !important;
            visibility: visible !important;
        }
        
        .menu-dropdown {
            background-color: rgba(255, 255, 255, 0.05);
            border-left: 2px solid #007bff;
            margin-left: 15px;
        }
        
        .menu-dropdown .nav-link {
            padding-left: 25px;
            font-size: 13px;
        }

        /* Đảm bảo sidebar không bị ẩn khi click dropdown */
        .sidebar-hidden .app-menu {
            display: block !important;
        }
        
        .vertical-overlay {
            pointer-events: none;
        }
        
        /* Fix cho Bootstrap collapse */
        .collapse:not(.show) {
            display: none;
        }
        
        .collapse.show {
            display: block !important;
        }

        /* Override mọi style có thể ẩn sidebar khi click dropdown */
        body:not(.sidebar-enable) .app-menu,
        body.twocolumn-panel .app-menu,
        body.vertical-sidebar-enable .app-menu {
            display: block !important;
            transform: translateX(0) !important;
            left: 0 !important;
        }

        /* Ngăn chặn sidebar bị ẩn */
        .app-menu:not(.hide-forced) {
            display: block !important;
            visibility: visible !important;
        }

        /* Admin Notification Dropdown Styles */
        .admin-notification-dropdown {
            position: relative;
        }
        
        .admin-notification-dropdown #admin-notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1050;
            min-width: 320px;
            max-width: 400px;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.375rem;
            background-color: #fff;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
        
        .admin-notification-dropdown .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .admin-notification-dropdown .notification-item:hover {
            background-color: #f8fafc;
        }
        
        .admin-notification-dropdown .notification-item.unread {
            background-color: #f0f9ff;
            border-left: 3px solid #3b82f6;
        }
        
        .admin-notification-dropdown .notification-item.read {
            opacity: 0.7;
        }
        
        .admin-notification-dropdown .notification-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .admin-notification-dropdown .notification-content {
            flex: 1;
            min-width: 0;
        }
        
        .admin-notification-dropdown .notification-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1f2937;
            margin-bottom: 0.25rem;
            line-height: 1.25;
        }
        
        .admin-notification-dropdown .notification-message {
            font-size: 0.8125rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
            line-height: 1.4;
        }
        
        .admin-notification-dropdown .notification-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        /* Badge positioning */
        #admin-notification-badge {
            top: -2px;
            right: -2px;
        }
        
        /* Dark mode support */
        [data-layout-mode="dark"] .admin-notification-dropdown #admin-notification-dropdown {
            background-color: #1f2937;
            border-color: rgba(255,255,255,.15);
        }
        
        [data-layout-mode="dark"] .admin-notification-dropdown .notification-item {
            border-bottom-color: #374151;
        }
        
        [data-layout-mode="dark"] .admin-notification-dropdown .notification-item:hover {
            background-color: #374151;
        }
        
        [data-layout-mode="dark"] .admin-notification-dropdown .notification-item.unread {
            background-color: #1e3a8a;
        }
        
        [data-layout-mode="dark"] .admin-notification-dropdown .notification-title {
            color: #f9fafb;
        }
        
        [data-layout-mode="dark"] .admin-notification-dropdown .notification-message {
            color: #d1d5db;
        }
        
        [data-layout-mode="dark"] .admin-notification-dropdown .notification-time {
            color: #9ca3af;
        }
    </style>

    @livewireStyles

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- Logo (dark & light) -->
                        <div class="navbar-brand-box horizontal-logo">
                            {{-- @foreach (['dark', 'light'] as $mode)
                                <a href="index.html" class="logo logo-{{ $mode }}" title="Trang chủ">
                                    <span class="logo-sm">
                                        <img src="{{ asset('assets/images/logo-sm.png') }}" alt="Logo nhỏ"
                                            height="22">
                                    </span>
                                    <span class="logo-lg">
                                        <img src="{{ asset("assets/images/logo-{$mode}.png") }}"
                                            alt="Logo {{ $mode }}" height="17">
                                    </span>
                                </a>
                            @endforeach --}}
                        </div>

                        <!-- Toggle sidebar menu -->
                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon" title="Mở menu">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>


                    <div class="d-flex align-items-center">
                        <!-- Search (Mobile Only) -->
                        <div class="dropdown d-md-none topbar-head-dropdown header-item">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" title="Tìm kiếm">
                                <i class="bx bx-search fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
                                <form class="p-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..."
                                            aria-label="Search">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Fullscreen Toggle (Desktop Only) -->
                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                data-toggle="fullscreen" title="Toàn màn hình">
                                <i class="bx bx-fullscreen fs-22"></i>
                            </button>
                        </div>

                        <!-- Dark Mode Toggle (Desktop Only) -->
                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button"
                                class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode"
                                title="Chuyển chế độ sáng/tối">
                                <i class="bx bx-moon fs-22"></i>
                            </button>
                        </div>

                        <!-- Admin Notification Dropdown -->
                        <div class="dropdown topbar-head-dropdown ms-1 header-item admin-notification-dropdown" id="adminNotificationDropdown">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                onclick="toggleAdminNotificationDropdown()" title="Thông báo Admin">
                                <i class="bx bx-bell fs-22"></i>
                                <span id="admin-notification-badge"
                                    class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"
                                    style="display: none;">
                                    0
                                </span>
                            </button>

                            <div id="admin-notification-dropdown" class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                style="opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.3s ease; pointer-events: none;">
                                <!-- Header -->
                                <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                    <div class="p-3 d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 fs-16 fw-semibold text-white">Thông báo Admin</h6>
                                        <span id="admin-notification-count" class="badge bg-light-subtle text-body fs-13">0 thông báo mới</span>
                                    </div>
                                </div>

                                <!-- Notification List -->
                                <div id="admin-notification-list" style="max-height: 350px; overflow-y: auto;">
                                    <!-- Notifications will be loaded here by JavaScript -->
                                    <div style="padding: 2rem 1rem; text-align: center; color: #6b7280;">
                                        <svg style="height: 3rem; width: 3rem; margin: 0 auto 1rem; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p style="margin: 0; font-size: 0.875rem;">Chưa có thông báo nào</p>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="p-2 border-top d-grid">
                                    <a href="/admin/notifications" class="btn btn-sm btn-link fw-semibold text-decoration-none text-center">
                                        Xem tất cả thông báo
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                title="Tài khoản">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=6366f1&color=fff&size=50' }}"
                                        alt="Avatar" style="width: 32px; height: 32px; object-fit: cover;">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->name }}</span>
                                        <span
                                            class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ auth()->user()->email }}</span>
                                    </span>
                                </span>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}!</h6>
                                <!-- item-->
                                <a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle">Quản lý tài khoản</span></a>
                                <a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i
                                        class="fa-solid fa-key text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Đổi Mật Khẩu</span></a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- removeNotificationModal -->
        <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548"
                                style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete
                                It!</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- Logo -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-lg">
                        <img src="{{ asset('storage/' . (get_setting() ? get_setting()->logo : 'default_logo.png')) }}"
                            alt="" width="200px">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <!-- Sidebar content -->
            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Báo Cáo Thống Kê</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.dashboard') }}">
                                <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Báo cáo tổng
                                    quan</span>
                            </a>
                        </li> <!-- end Dashboard Menu -->
                        <li class="menu-title"><span data-key="t-menu">Quản Lý Hệ Thống</span></li>
                        @permission('user.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarUsers" data-bs-toggle="collapse">
                                    <i class="ri-account-circle-line"></i> <span data-key="t-authentication">Quản lý người
                                        dùng</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarUsers">
                                    <ul class="nav nav-sm flex-column">
                                        @permission('user.view')
                                        <li><a href="{{ route('admin.users.index') }}" class="nav-link">Danh sách người
                                                dùng</a></li>
                                        @endpermission
                                        @permission('staff.view')
                                        <li><a href="{{ route('admin.staff.index') }}" class="nav-link">Danh sách nhân viên</a></li>
                                        @endpermission
                                        @permission('role.view')
                                            <li><a href="{{ route('admin.roles.index') }}" class="nav-link">Danh sách vai trò</a></li>
                                        @endpermission
                                        @permission('permission.view')
                                            <li><a href="{{ route('admin.permissions.index') }}" class="nav-link">Danh sách quyền</a></li>
                                        @endpermission
                                    </ul>
                                </div>
                            </li>
                            
                        @endpermission
                        <!-- Quản lý sản phẩm -->
                        @permission('book.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse"
                                    aria-expanded="false">
                                    <i class="ri-apps-2-line"></i> <span data-key="t-apps">Quản lý sản phẩm</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarApps">
                                    <ul class="nav nav-sm flex-column">
                                        <li><a href="{{ route('admin.books.index') }}" class="nav-link">Danh sách</a>
                                        </li>
                                        @permission('attribute.view')
                                            <li><a href="{{ route('admin.attributes.index') }}" class="nav-link">Thuộc
                                                    tính</a></li>
                                        @endpermission
                                        @permission('collection.view')
                                            <li><a href="{{ route('admin.collections.index') }}" class="nav-link">Combo
                                                    sách</a></li>
                                        @endpermission
                                    </ul>
                                </div>
                            </li>
                        @endpermission
                        <!-- Quản lý danh mục -->
                        @permission('category.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarCate" data-bs-toggle="collapse"
                                    aria-expanded="false">
                                    <i class="ri-apps-2-line"></i> <span data-key="t-categories">Quản lý danh mục sản
                                        phẩm</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarCate">
                                    <ul class="nav nav-sm flex-column">

                                        <li><a href="{{ route('admin.categories.index') }}" class="nav-link">Loại
                                                sách</a></li>

                                        <li><a href="{{ route('admin.categories.authors.index') }}" class="nav-link">Tác
                                                giả</a></li>
                                        <li><a href="{{ route('admin.categories.brands.brand') }}"
                                                class="nav-link">Thương hiệu</a></li>
                                    </ul>
                                </div>
                            </li>
                        @endpermission
                        <!-- Đơn hàng -->
                        @permission('order.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarOrders" data-bs-toggle="collapse"
                                    aria-expanded="false">
                                    <i class="ri-pages-line"></i> <span data-key="t-orders">Quản lý đơn hàng</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarOrders">
                                    <ul class="nav nav-sm flex-column">
                                        <li><a href="{{ route('admin.orders.index') }}" class="nav-link">Danh sách đơn
                                                hàng</a></li>
                                        @permission('refund.view')
                                            <li><a href="{{ route('admin.refunds.index') }}" class="nav-link">Yêu cầu hoàn
                                                    tiền</a></li>
                                        @endpermission
                                        <li><a href="{{ route('admin.preorders.index') }}" class="nav-link">Danh sách đơn
                                                đặt trước</a></li>
                                    </ul>
                                </div>
                            </li>
                        @endpermission
                        <!-- Tin tức -->
                        @permission('news.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarNews" data-bs-toggle="collapse"
                                    aria-expanded="false">
                                    <i class="ri-newspaper-line"></i> <span data-key="t-news">Quản lý tin tức</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarNews">
                                    <ul class="nav nav-sm flex-column">
                                        <li><a href="{{ route('admin.news.index') }}" class="nav-link">Danh sách</a></li>
                                        @permission('news.create')
                                            <li><a href="{{ route('admin.news.create') }}" class="nav-link">Thêm mới</a></li>
                                        @endpermission
                                    </ul>
                                </div>
                            </li>
                        @endpermission

                        <!-- Liên hệ -->
                        @permission('contact.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarContacts" data-bs-toggle="collapse"
                                    aria-expanded="false">
                                    <i class="ri-mail-line"></i> <span data-key="t-contacts">Quản lý liên hệ</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarContacts">
                                    <ul class="nav nav-sm flex-column">
                                        <li><a href="{{ route('admin.contacts.index') }}" class="nav-link">Danh sách liên
                                                hệ</a></li>
                                    </ul>
                                </div>
                            </li>
                        @endpermission
                        <!-- Khuyến mãi -->
                        @permission('voucher.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarAdvanceUI" data-bs-toggle="collapse"
                                    aria-expanded="false">
                                    <i class="ri-stack-line"></i> <span data-key="t-promotions">Quản lý khuyến mãi</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarAdvanceUI">
                                    <ul class="nav nav-sm flex-column">
                                        @permission('voucher.view')
                                            <li><a href="{{ route('admin.vouchers.index') }}" class="nav-link">Danh sách</a>
                                            </li>
                                        @endpermission
                                        @permission('voucher.create')
                                            <li><a href="advance-ui-nestable.html" class="nav-link">Thêm</a></li>
                                        @endpermission
                                    </ul>
                                </div>
                            </li>
                        @endpermission
                        <!-- Thanh toán -->
                        @permission('payment-method.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarForms" data-bs-toggle="collapse"
                                    aria-expanded="false">
                                    <i class="ri-file-list-3-line"></i> <span data-key="t-payments">Thanh toán & phương
                                        thức</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarForms">
                                    <ul class="nav nav-sm flex-column">
                                        <li><a href="{{ route('admin.payment-methods.index') }}" class="nav-link">Danh
                                                sách</a></li>
                                        @permission('payment-method.create')
                                            <li><a href="{{ route('admin.payment-methods.create') }}"
                                                    class="nav-link">Thêm</a></li>
                                        @endpermission
                                        @permission('payment-method.history')
                                            <li><a href="{{ route('admin.payment-methods.history') }}" class="nav-link">Lịch
                                                    sử thanh toán</a></li>
                                        @endpermission
                                    </ul>
                                </div>
                            </li>
                        @endpermission
                        @permission('wallet.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarWallets" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarWallets">
                                    <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Quản lý ví</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarWallets">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{ route('admin.wallets.index') }}" class="nav-link"
                                                data-key="t-basic-elements">Danh
                                                sách</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('admin.wallets.depositHistory') }}" class="nav-link"
                                                data-key="t-basic-elements">Nạp ví</a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="{{ route('admin.wallets.withdrawHistory') }}" class="nav-link"
                                                data-key="t-basic-elements">Rút ví</a>
                                        </li>

                                    </ul>
                                </div>
                            </li>
                        @endpermission
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.chat.index') }}">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Chat</span>
                            </a>
                        </li>
                        @permission('review.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="{{ route('admin.reviews.index') }}">
                                    <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Bình luận & Đánh
                                        giá</span>
                                </a>
                            </li>
                        @endpermission
                        @permission('invoice.view')
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="{{ route('admin.invoices.index') }}">
                                    <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Hóa Đơn</span>
                                </a>
                            </li>
                        @endpermission
                        @permission('setting.view')
                            <li class="menu-title"><span data-key="t-menu">Cấu Hình</span></li>
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="{{ route('admin.settings.index') }}">
                                    <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Cấu hình website</span>
                                </a>
                            </li>
                        @endpermission

                    </ul>
                </div>
            </div>

            <div class="sidebar-background"></div>
        </div>

        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                @hasSection('content')
                 <div id="notification" class=" alert mx-3 invisible" >
                    </div>
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </div>
            <!-- End Page-content -->

            {{-- Footer --}}
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> BookBee
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by Bookbee
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!--preloader-->
    <div id="preloader"
        class="d-flex align-items-center justify-content-center position-fixed top-0 start-0 w-100 h-100 bg-white z-50">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Đang tải...</span>
        </div>
    </div>

    {{-- <div class="customizer-setting d-none d-md-block">
        <button type="button" class="btn btn-info btn-icon btn-lg rounded-pill shadow-lg p-2"
            data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas"
            aria-controls="theme-settings-offcanvas" title="Tùy chỉnh giao diện">
            <i class="mdi mdi-cog-outline mdi-spin fs-22"></i>
        </button>
    </div> --}}

    {{-- <!-- Theme Settings -->
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex align-items-center bg-primary bg-gradient p-3 offcanvas-header">
            <h5 class="m-0 me-2 text-white">Theme Customizer</h5>

            <button type="button" class="btn-close btn-close-white ms-auto" id="customizerclose-btn"
                data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div data-simplebar class="h-100">
                <div class="p-4">
                    <h6 class="mb-0 fw-semibold text-uppercase">Layout</h6>
                    <p class="text-muted">Choose your layout</p>

                    <div class="row gy-3">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input id="customizer-layout01" name="data-layout" type="radio" value="vertical"
                                    class="form-check-input">
                                <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout01">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Vertical</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input id="customizer-layout02" name="data-layout" type="radio" value="horizontal"
                                    class="form-check-input">
                                <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout02">
                                    <span class="d-flex h-100 flex-column gap-1">
                                        <span class="bg-light d-flex p-1 gap-1 align-items-center">
                                            <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                                            <span class="d-block p-1 pb-0 px-2 bg-primary-subtle ms-auto"></span>
                                            <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                                        </span>
                                        <span class="bg-light d-block p-1"></span>
                                        <span class="bg-light d-block p-1 mt-auto"></span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Horizontal</h5>
                        </div>
                        <!-- end col -->

                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input id="customizer-layout04" name="data-layout" type="radio" value="semibox"
                                    class="form-check-input">
                                <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout04">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0 p-1">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column pt-1 pe-2">
                                                <span class="bg-light d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Semi Box</h5>
                        </div>
                        <!-- end col -->
                    </div>

                    <div class="colorscheme-cardradio">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-bs-theme"
                                        id="layout-mode-light" value="light">
                                    <label class="form-check-label p-0 avatar-md w-100" for="layout-mode-light">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Light</h5>
                            </div>

                            <div class="col-4">
                                <div class="form-check card-radio dark">
                                    <input class="form-check-input" type="radio" name="data-bs-theme"
                                        id="layout-mode-dark" value="dark">
                                    <label class="form-check-label p-0 avatar-md w-100 bg-dark"
                                        for="layout-mode-dark">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span
                                                    class="bg-white bg-opacity-10 d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-white bg-opacity-10 d-block p-1"></span>
                                                    <span class="bg-white bg-opacity-10 d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Dark</h5>
                            </div>
                        </div>
                    </div>

                    <div id="sidebar-visibility">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Visibility</h6>
                        <p class="text-muted">Choose show or Hidden sidebar.</p>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-visibility"
                                        id="sidebar-visibility-show" value="show">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-visibility-show">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0 p-1">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column pt-1 pe-2">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Show</h5>
                            </div>
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-visibility"
                                        id="sidebar-visibility-hidden" value="hidden">
                                    <label class="form-check-label p-0 avatar-md w-100 px-2"
                                        for="sidebar-visibility-hidden">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column pt-1 px-2">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Hidden</h5>
                            </div>
                        </div>
                    </div>

                    <div id="layout-width">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Layout Width</h6>
                        <p class="text-muted">Choose Fluid or Boxed layout.</p>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-layout-width"
                                        id="layout-width-fluid" value="fluid">
                                    <label class="form-check-label p-0 avatar-md w-100" for="layout-width-fluid">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Fluid</h5>
                            </div>
                            <div class="col-4">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="data-layout-width"
                                        id="layout-width-boxed" value="boxed">
                                    <label class="form-check-label p-0 avatar-md w-100 px-2" for="layout-width-boxed">
                                        <span class="d-flex gap-1 h-100 border-start border-end">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Boxed</h5>
                            </div>
                        </div>
                    </div>

                    <div id="layout-position">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Layout Position</h6>
                        <p class="text-muted">Choose Fixed or Scrollable Layout Position.</p>

                        <div class="btn-group radio" role="group">
                            <input type="radio" class="btn-check" name="data-layout-position"
                                id="layout-position-fixed" value="fixed">
                            <label class="btn btn-light w-sm" for="layout-position-fixed">Fixed</label>

                            <input type="radio" class="btn-check" name="data-layout-position"
                                id="layout-position-scrollable" value="scrollable">
                            <label class="btn btn-light w-sm ms-0" for="layout-position-scrollable">Scrollable</label>
                        </div>
                    </div>
                    <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Topbar Color</h6>
                    <p class="text-muted">Choose Light or Dark Topbar Color.</p>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-topbar"
                                    id="topbar-color-light" value="light">
                                <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-light">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Light</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-check card-radio">
                                <input class="form-check-input" type="radio" name="data-topbar"
                                    id="topbar-color-dark" value="dark">
                                <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-dark">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-primary d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <h5 class="fs-13 text-center mt-2">Dark</h5>
                        </div>
                    </div>

                    <div id="sidebar-size">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Size</h6>
                        <p class="text-muted">Choose a size of Sidebar.</p>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size"
                                        id="sidebar-size-default" value="lg">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-default">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Default</h5>
                            </div>

                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size"
                                        id="sidebar-size-compact" value="md">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-compact">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span class="d-block p-1 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Compact</h5>
                            </div>

                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size"
                                        id="sidebar-size-small" value="sm">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-small">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1">
                                                    <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Small (Icon View)</h5>
                            </div>

                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar-size"
                                        id="sidebar-size-small-hover" value="sm-hover">
                                    <label class="form-check-label p-0 avatar-md w-100"
                                        for="sidebar-size-small-hover">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1">
                                                    <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Small Hover View</h5>
                            </div>
                        </div>
                    </div>

                    <div id="sidebar-view">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar View</h6>
                        <p class="text-muted">Choose Default or Detached Sidebar view.</p>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-layout-style"
                                        id="sidebar-view-default" value="default">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-view-default">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Default</h5>
                            </div>
                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-layout-style"
                                        id="sidebar-view-detached" value="detached">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-view-detached">
                                        <span class="d-flex h-100 flex-column">
                                            <span class="bg-light d-flex p-1 gap-1 align-items-center px-2">
                                                <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                                                <span class="d-block p-1 pb-0 px-2 bg-primary-subtle ms-auto"></span>
                                                <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                                            </span>
                                            <span class="d-flex gap-1 h-100 p-1 px-2">
                                                <span class="flex-shrink-0">
                                                    <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                                                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="bg-light d-block p-1 mt-auto px-2"></span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Detached</h5>
                            </div>
                        </div>
                    </div>
                    <div id="sidebar-color">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Color</h6>
                        <p class="text-muted">Choose a color of Sidebar.</p>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio" data-bs-toggle="collapse"
                                    data-bs-target="#collapseBgGradient.show">
                                    <input class="form-check-input" type="radio" name="data-sidebar"
                                        id="sidebar-color-light" value="light">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-color-light">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-white border-end d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Light</h5>
                            </div>
                            <div class="col-4">
                                <div class="form-check sidebar-setting card-radio" data-bs-toggle="collapse"
                                    data-bs-target="#collapseBgGradient.show">
                                    <input class="form-check-input" type="radio" name="data-sidebar"
                                        id="sidebar-color-dark" value="dark">
                                    <label class="form-check-label p-0 avatar-md w-100" for="sidebar-color-dark">
                                        <span class="d-flex gap-1 h-100">
                                            <span class="flex-shrink-0">
                                                <span class="bg-primary d-flex h-100 flex-column gap-1 p-1">
                                                    <span
                                                        class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                    <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                </span>
                                            </span>
                                            <span class="flex-grow-1">
                                                <span class="d-flex h-100 flex-column">
                                                    <span class="bg-light d-block p-1"></span>
                                                    <span class="bg-light d-block p-1 mt-auto"></span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <h5 class="fs-13 text-center mt-2">Dark</h5>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-link avatar-md w-100 p-0 overflow-hidden border collapsed"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseBgGradient"
                                    aria-expanded="false" aria-controls="collapseBgGradient">
                                    <span class="d-flex gap-1 h-100">
                                        <span class="flex-shrink-0">
                                            <span class="bg-vertical-gradient d-flex h-100 flex-column gap-1 p-1">
                                                <span
                                                    class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                                <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                                            </span>
                                        </span>
                                        <span class="flex-grow-1">
                                            <span class="d-flex h-100 flex-column">
                                                <span class="bg-light d-block p-1"></span>
                                                <span class="bg-light d-block p-1 mt-auto"></span>
                                            </span>
                                        </span>
                                    </span>
                                </button>
                                <h5 class="fs-13 text-center mt-2">Gradient</h5>
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="collapse" id="collapseBgGradient">
                            <div class="d-flex gap-2 flex-wrap img-switch p-2 px-3 bg-light rounded">

                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar"
                                        id="sidebar-color-gradient" value="gradient">
                                    <label class="form-check-label p-0 avatar-xs rounded-circle"
                                        for="sidebar-color-gradient">
                                        <span class="avatar-title rounded-circle bg-vertical-gradient"></span>
                                    </label>
                                </div>
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar"
                                        id="sidebar-color-gradient-2" value="gradient-2">
                                    <label class="form-check-label p-0 avatar-xs rounded-circle"
                                        for="sidebar-color-gradient-2">
                                        <span class="avatar-title rounded-circle bg-vertical-gradient-2"></span>
                                    </label>
                                </div>
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar"
                                        id="sidebar-color-gradient-3" value="gradient-3">
                                    <label class="form-check-label p-0 avatar-xs rounded-circle"
                                        for="sidebar-color-gradient-3">
                                        <span class="avatar-title rounded-circle bg-vertical-gradient-3"></span>
                                    </label>
                                </div>
                                <div class="form-check sidebar-setting card-radio">
                                    <input class="form-check-input" type="radio" name="data-sidebar"
                                        id="sidebar-color-gradient-4" value="gradient-4">
                                    <label class="form-check-label p-0 avatar-xs rounded-circle"
                                        for="sidebar-color-gradient-4">
                                        <span class="avatar-title rounded-circle bg-vertical-gradient-4"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="sidebar-img">
                        <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Images</h6>
                        <p class="text-muted">Choose a image of Sidebar.</p>

                        <div class="d-flex gap-2 flex-wrap img-switch">
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image"
                                    id="sidebarimg-none" value="none">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-none">
                                    <span
                                        class="avatar-md w-auto bg-light d-flex align-items-center justify-content-center">
                                        <i class="ri-close-fill fs-20"></i>
                                    </span>
                                </label>
                            </div>

                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image"
                                    id="sidebarimg-01" value="img-1">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-01">
                                    <img src="{{ asset('assets/images/sidebar/img-1.jpg') }}" alt=""
                                        class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>

                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image"
                                    id="sidebarimg-02" value="img-2">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-02">
                                    <img src="{{ asset('assets/images/sidebar/img-2.jpg') }}" alt=""
                                        class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image"
                                    id="sidebarimg-03" value="img-3">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-03">
                                    <img src="{{ asset('assets/images/sidebar/img-3.jpg') }}" alt=""
                                        class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>
                            <div class="form-check sidebar-setting card-radio">
                                <input class="form-check-input" type="radio" name="data-sidebar-image"
                                    id="sidebarimg-04" value="img-4">
                                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-04">
                                    <img src="{{ asset('assets/images/sidebar/img-4.jpg') }}" alt=""
                                        class="avatar-md w-auto object-fit-cover">
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div> --}}

    {{-- ================== PLUGIN JS (EXTERNAL) ================== --}}
    <script src="https://cdn.jsdelivr.net/npm/simplebar@6.2.5/dist/simplebar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/node-waves@0.7.6/dist/waves.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.29.1/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world-merc.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.pagination.js/0.1.1/list.pagination.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- ================== INTERNAL PLUGINS ================== --}}
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    {{-- ================== MENU ACTIVE HANDLER ================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(item => {
                const itemUrl = item.getAttribute('href');
                if (!itemUrl) return;
                const itemPath = new URL(itemUrl, window.location.origin).pathname;
                if (currentUrl === itemPath) {
                    item.classList.add('active');
                    const parentCollapse = item.closest('.collapse');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                        const toggleBtn = parentCollapse.closest('.nav-item')?.querySelector('.menu-link');
                        if (toggleBtn) {
                            toggleBtn.classList.remove('collapsed');
                            toggleBtn.classList.add('active');
                            toggleBtn.setAttribute('aria-expanded', 'true');
                            const parentItem = toggleBtn.closest('.nav-item');
                            parentItem?.classList.add('active');
                            parentItem?.querySelector('.nav-link')?.classList.add('active');
                        }
                    }
                }
            });

            // Fix cho sidebar dropdown menu - ngăn chặn việc ẩn toàn bộ sidebar
            document.querySelectorAll('.navbar-nav .nav-link[data-bs-toggle="collapse"]').forEach(function(trigger) {
                trigger.addEventListener('click', function(event) {
                    // Không preventDefault để Bootstrap collapse vẫn hoạt động
                    const targetSelector = this.getAttribute('data-bs-target') || this.getAttribute('href');
                    const targetElement = document.querySelector(targetSelector);
                    
                    if (targetElement) {
                        // Đảm bảo sidebar không bị ẩn
                        const sidebar = document.querySelector('.app-menu');
                        if (sidebar) {
                            sidebar.style.display = 'block';
                            sidebar.style.visibility = 'visible';
                        }
                        
                        // Ngăn chặn event bubbling để không trigger hamburger menu
                        event.stopPropagation();
                    }
                });
            });

            // Ngăn chặn click trên dropdown menu ẩn sidebar
            document.querySelectorAll('.menu-dropdown').forEach(function(dropdown) {
                dropdown.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });

            // Override hamburger menu behavior để không ảnh hưởng đến dropdown
            const hamburgerIcon = document.querySelector('#topnav-hamburger-icon');
            if (hamburgerIcon) {
                hamburgerIcon.addEventListener('click', function(event) {
                    // Chỉ toggle sidebar khi không có dropdown menu nào đang mở
                    const openDropdowns = document.querySelectorAll('.menu-dropdown.show');
                    if (openDropdowns.length === 0) {
                        document.body.classList.toggle('sidebar-hidden');
                    }
                });
            }

            // Bootstrap collapse event handlers
            document.querySelectorAll('.collapse').forEach(function(collapseElement) {
                collapseElement.addEventListener('show.bs.collapse', function(event) {
                    // Đảm bảo sidebar vẫn hiển thị khi dropdown mở
                    const sidebar = document.querySelector('.app-menu');
                    if (sidebar) {
                        sidebar.style.display = 'block';
                        sidebar.style.visibility = 'visible';
                    }
                    event.stopPropagation();
                });

                collapseElement.addEventListener('hide.bs.collapse', function(event) {
                    // Đảm bảo sidebar vẫn hiển thị khi dropdown đóng
                    const sidebar = document.querySelector('.app-menu');
                    if (sidebar) {
                        sidebar.style.display = 'block'; 
                        sidebar.style.visibility = 'visible';
                    }
                    event.stopPropagation();
                });
            });

            // Đảm bảo sidebar không bị ẩn khi click vào dropdown
            document.querySelectorAll('.app-menu .nav-link').forEach(function(link) {
                if (link.hasAttribute('data-bs-toggle')) {
                    link.addEventListener('click', function(e) {
                        // Ngăn chặn việc ẩn sidebar
                        e.stopPropagation();
                    });
                }
            });

            // MutationObserver để theo dõi và ngăn chặn sidebar bị ẩn
            const sidebar = document.querySelector('.app-menu');
            if (sidebar) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                            const currentStyle = sidebar.getAttribute('style');
                            if (currentStyle && (currentStyle.includes('display: none') || currentStyle.includes('visibility: hidden'))) {
                                // Reset style để hiển thị sidebar
                                sidebar.style.display = 'block';
                                sidebar.style.visibility = 'visible';
                            }
                        }
                    });
                });

                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
            }

            // Đảm bảo sidebar luôn hiển thị
            setInterval(function() {
                const sidebarElement = document.querySelector('.app-menu');
                if (sidebarElement) {
                    const computed = window.getComputedStyle(sidebarElement);
                    if (computed.display === 'none' || computed.visibility === 'hidden') {
                        sidebarElement.style.display = 'block';
                        sidebarElement.style.visibility = 'visible';
                    }
                }
            }, 100);
        });
    </script>

    {{-- ================== SWIPER INIT ================== --}}
    <script>
        const thumbnailSlider = new Swiper(".thumbnail-slider", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
                640: {
                    slidesPerView: 4
                },
                768: {
                    slidesPerView: 5
                },
                1024: {
                    slidesPerView: 6
                },
            },
        });

        const mainSlider = new Swiper(".main-slider", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
            thumbs: {
                swiper: thumbnailSlider
            }
        });
    </script>

    {{-- ================== TOASTR + SESSION CLEANUP ================== --}}
    {!! Toastr::message() !!}
    <script>
        if (performance.getEntriesByType('navigation')[0]?.type === 'back_forward') {
            document.querySelector('#toast-container')?.remove();
        }
    </script>

    {{-- ================== LIVEWIRE & PAGE-SPECIFIC SCRIPTS ================== --}}
    @livewireScripts
    
    <!-- Laravel Echo & Notifications -->
    <script src="{{ asset('js/notifications.js') }}"></script>
    <script src="{{ asset('js/admin-notifications.js') }}"></script>
    
    @yield('scripts')
    @stack('scripts')
</body>

</html>
