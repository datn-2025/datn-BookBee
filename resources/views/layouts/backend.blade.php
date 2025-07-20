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
    <link href="https://cdn.lineawesome.com/1.3.0/line-awesome/css/line-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    <!-- Plugin CSS -->
    <link href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

     @vite(['resources/js/app.js', 'resources/css/app.css'])

     <script src="{{ asset('js/chat.js') }}"></script>


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
    {{-- <script>
        window.currentUserId = {{ auth()->id() }};
    </script> --}}

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

    <!-- Custom inline styles -->
    <style>
        .collapse.show+a .toggle-icon,
        .nav-link[aria-expanded="true"] .toggle-icon {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
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

                        <!-- Notification Dropdown -->
                        <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"
                                title="Thông báo">
                                <i class="bx bx-bell fs-22"></i>
                                <span
                                    class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                                    3 <span class="visually-hidden">unread messages</span>
                                </span>
                            </button>

                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">
                                <!-- Header -->
                                <div class="dropdown-head bg-primary bg-pattern rounded-top">
                                    <div class="p-3 d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                                        <span class="badge bg-light-subtle text-body fs-13">4 New</span>
                                    </div>
                                    <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom px-2 pt-2"
                                        id="notificationItemsTab" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                                href="#all-noti-tab" role="tab">All (4)</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                                href="#messages-tab" role="tab">Messages</a></li>
                                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                                href="#alerts-tab" role="tab">Alerts</a></li>
                                    </ul>
                                </div>

                                <!-- Tab content -->
                                <div class="tab-content position-relative" id="notificationItemsTabContent">
                                    <!-- All Notifications -->
                                    <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab"
                                        role="tabpanel">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2">
                                            @foreach (range(1, 4) as $i)
                                                <div
                                                    class="text-reset notification-item d-block dropdown-item position-relative">
                                                    <div class="d-flex">
                                                        <div class="avatar-xs me-3 flex-shrink-0">
                                                            <span
                                                                class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                                <i class="bx bx-badge-check"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <a href="#!" class="stretched-link">
                                                                <h6 class="mt-0 mb-2 lh-base">Bạn có thông báo mới số
                                                                    {{ $i }}</h6>
                                                            </a>
                                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                                <i class="mdi mdi-clock-outline"></i>
                                                                {{ $i * 5 }} phút trước
                                                            </p>
                                                        </div>
                                                        <div class="px-2 fs-15">
                                                            <div class="form-check notification-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="notification-check{{ $i }}">
                                                                <label class="form-check-label"
                                                                    for="notification-check{{ $i }}"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="my-3 text-center view-all">
                                                <button type="button"
                                                    class="btn btn-soft-success waves-effect waves-light">
                                                    Xem tất cả <i class="ri-arrow-right-line align-middle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Messages -->
                                    <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel">
                                        <div class="pe-2" data-simplebar style="max-height: 300px;">
                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ asset('assets/images/users/avatar-3.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">James Lemire</h6>
                                                        </a>
                                                        <p class="fs-13 text-muted mb-1">We talked about a project on
                                                            LinkedIn.</p>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <i class="mdi mdi-clock-outline"></i> 30 min ago
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- thêm các tin khác nếu cần -->
                                        </div>
                                    </div>

                                    <!-- Alerts -->
                                    <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel"></div>

                                    <!-- Footer actions -->
                                    <div class="notification-actions d-flex text-muted justify-content-center">
                                        Chọn <div id="select-content" class="text-body fw-semibold px-1">0</div> mục
                                        <button type="button" class="btn btn-link link-danger p-0 ms-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#removeNotificationModal">Xoá</button>
                                    </div>
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
                                        alt="Avatar"
                                        style="width: 32px; height: 32px; object-fit: cover;">
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
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.users.index') }}">
                                <i class="ri-account-circle-line"></i> <span data-key="t-authentication">Quản lý người
                                    dùng</span>
                            </a>
                        </li>

                        <!-- Quản lý sản phẩm -->
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse"
                                aria-expanded="false">
                                <i class="ri-apps-2-line"></i> <span data-key="t-apps">Quản lý sản phẩm</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarApps">
                                <ul class="nav nav-sm flex-column">
                                    <li><a href="{{ route('admin.books.index') }}" class="nav-link">Danh sách</a>
                                    </li>
                                    <li><a href="{{ route('admin.attributes.index') }}" class="nav-link">Thuộc
                                            tính</a></li>
                                    <li><a href="{{ route('admin.collections.index') }}" class="nav-link">Combo
                                            sách</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Quản lý danh mục -->
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

                        <!-- Đơn hàng -->
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.orders.index') }}">
                                <i class="ri-pages-line"></i> <span data-key="t-orders">Quản lý đơn hàng</span>
                            </a>
                        </li>

                        <!-- Tin tức -->
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarNews" data-bs-toggle="collapse"
                                aria-expanded="false">
                                <i class="ri-newspaper-line"></i> <span data-key="t-news">Quản lý tin tức</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarNews">
                                <ul class="nav nav-sm flex-column">
                                    <li><a href="{{ route('admin.news.index') }}" class="nav-link">Danh sách</a></li>
                                    <li><a href="{{ route('admin.news.create') }}" class="nav-link">Thêm mới</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Liên hệ -->
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

                        <!-- Khuyến mãi -->
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAdvanceUI" data-bs-toggle="collapse"
                                aria-expanded="false">
                                <i class="ri-stack-line"></i> <span data-key="t-promotions">Quản lý khuyến mãi</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAdvanceUI">
                                <ul class="nav nav-sm flex-column">
                                    <li><a href="{{ route('admin.vouchers.index') }}" class="nav-link">Danh sách</a>
                                    </li>
                                    <li><a href="advance-ui-nestable.html" class="nav-link">Thêm</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Thanh toán -->
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
                                    <li><a href="{{ route('admin.payment-methods.create') }}"
                                            class="nav-link">Thêm</a></li>
                                    <li><a href="{{ route('admin.payment-methods.history') }}" class="nav-link">Lịch
                                            sử thanh toán</a></li>
                                </ul>
                            </div>
                        </li>
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
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.chat.index') }}">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Chat</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.reviews.index') }}">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Bình luận & Đánh
                                    giá</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.invoices.index') }}">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Hóa Đơn</span>
                            </a>
                        </li>
                        <li class="menu-title"><span data-key="t-menu">Cấu Hình</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('admin.settings.index') }}">
                                <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Cấu hình website</span>
                            </a>
                        </li>


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
    @yield('scripts')
    @stack('scripts')
</body>

</html>
