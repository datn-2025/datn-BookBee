<nav class="bg-white px-6 py-5 shadow-sm border-b">
    <div
        class="max-w-screen-xl mx-auto flex flex-col gap-2 md:flex-row md:items-center md:justify-between min-h-[80px]">
        {{-- Trái: Logo --}}
        <div class="flex items-center justify-between md:justify-start gap-3">
            <a href="#">
                <img src="{{ asset('storage/' . (get_setting() ? get_setting()->logo : 'default_logo.png')) }}"
                     alt="Logo" class="h-32 w-auto"/>
            </a>
        </div>
        {{-- Giữa: Menu chính --}}
        <ul
            class="flex flex-wrap justify-center items-center gap-8 text-base semi-bold uppercase tracking-wide text-gray-900">
            <li><a href="/" class="hover:text-red-500 text-red-500">Trang chủ</a></li>
            <li><a href="#" class="hover:text-red-500">Giới thiệu</a></li>
            <li><a href="#" class="hover:text-red-500">Cửa hàng</a></li>
            <li><a href="#" class="hover:text-red-500">Tin tức</a></li>
            <li><a href="#" class="hover:text-red-500">Liên hệ</a></li>
        </ul>
        {{-- Phải: Tìm kiếm + icon + dòng phụ --}}
        <div class="flex flex-col items-end gap-1 text-sm md:text-xs">
            {{-- Dòng phụ --}}
            <div class="flex items-center gap-4">


                {{-- Cờ + chọn ngôn ngữ --}}
                <nav class="relative">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('storage/images/vn-flag.png') }}" alt="Vietnam Flag" class="h-4 w-6">
                        {{-- <span class="text-sm font-medium">VietNam</span> --}}
                    </div>
                </nav>


            </div>
            {{-- Dòng icon + tìm kiếm --}}
            <div class="flex items-center gap-3 text-[15px] text-gray-800">
                {{-- Thanh tìm kiếm --}}
                <div class="relative">
                    <input type="text" placeholder="tìm kiếm"
                           class="pl-3 pr-9 py-1 text-sm rounded border border-gray-300 focus:outline-none focus:border-black w-40">
                    <i class="fas fa-search absolute right-2 top-1.5 text-sm text-gray-500"></i>
                </div>
                <!-- <i class="far fa-user hover:text-black cursor-pointer"></i> -->
                <!-- Thay thế phần user icon cũ bằng code này -->
                <div style="position: relative; display: inline-block;">
                    <a href="{{ auth()->check() ? '#' : route('login') }}"
                       style="text-decoration: none; color: inherit;"
                       onmouseover="this.nextElementSibling.style.display='block'"
                       onmouseout="this.nextElementSibling.style.display='none'">
                        <i class="far fa-user"></i>
                        @auth
                            <span style="margin-left: 5px;">{{ Auth::user()->name }}</span>
                        @endauth
                    </a>


                    <div style="position: absolute; right: 0; background: white;
                border: 1px solid #ddd; border-radius: 4px; display: none;" onmouseover="this.style.display='block'"
                         onmouseout="this.style.display='none'">
                        @auth
                            <a href="{{ route('account.profile') }}" style="display: block; padding: 8px 15px; color: #333; text-decoration: none;
                      border-bottom: 1px solid #eee; white-space: nowrap;">
                                <i class="fas fa-user-circle" style="margin-right: 8px;"></i> Tài khoản
                            </a>
                            <a href="{{ route('orders.index') }}" style="display: block; padding: 8px 15px; color: #333; text-decoration: none;
                      border-bottom: 1px solid #eee; white-space: nowrap;">
                                <i class="fas fa-user-circle" style="margin-right: 8px;"></i> Đơn Hàng Của Tôi
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" style="width: 100%; text-align: left; padding: 8px 15px;
                               background: none; border: none; color: #333; cursor: pointer;">
                                    <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i> Đăng xuất
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" style="display: block; padding: 8px 15px; color: #333; text-decoration: none;
                      border-bottom: 1px solid #eee; white-space: nowrap;">
                                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i> Đăng nhập
                            </a>
                            <a href="{{ route('register') }}"
                               style="display: block; padding: 8px 15px; color: #333; text-decoration: none;">
                                <i class="fas fa-user-plus" style="margin-right: 8px;"></i> Đăng ký
                            </a>
                        @endauth
                    </div>
                </div>
                <i class="far fa-heart hover:text-black cursor-pointer"></i>
                <div class="relative">
                    <a href="{{route('cart.index')}}"> <i
                            class="fas fa-shopping-bag hover:text-black cursor-pointer"></i></a>
                    <span
                        class="absolute -top-2 -right-2 text-[10px] bg-yellow-400 text-black font-bold rounded-full px-1"></span>
                </div>
            </div>
        </div>


    </div>
</nav>

<style>
    /* Main Navigation */
    .adidas-nav {
        position: relative;
        z-index: 1000;
    }

    .adidas-nav {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .adidas-btn {
        transition: background-color 0.2s ease;
    }

    .adidas-btn:hover {
        background-color: #f3f4f6;
    }

    .adidas-gradient-text {
        background: linear-gradient(45deg, #000000, #767677, #000000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Navigation links hover effects */
    .nav-link {
        position: relative;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #3b82f6;
        transition: width 0.3s ease;
    }

    .nav-link:hover::before {
        width: 100%;
    }

    /* Enhanced dropdown styles */
    .user-dropdown {
        position: relative;
    }

    .user-dropdown .dropdown-menu {
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.98);
        transition: all 0.2s ease;
        pointer-events: none;
        z-index: 9999;
        background: #ffffff !important;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
        border: 2px solid #f3f4f6;
        min-width: 200px;
        display: block;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    /* Arrow pointer */
    .user-dropdown .dropdown-menu::before {
        content: '';
        position: absolute;
        top: -7px;
        right: 20px;
        width: 14px;
        height: 14px;
        background: #ffffff !important;
        transform: rotate(45deg);
        border: 2px solid #f3f4f6;
        border-bottom: none;
        border-right: none;
        z-index: -1;
        box-shadow: -2px -2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Show dropdown on hover */
    .user-dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Alternative fallback for touch devices */
    .user-dropdown .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Enhanced user button */
    .user-dropdown .user-btn {
        transition: all 0.15s ease;
        position: relative;
        overflow: hidden;
    }

    .user-dropdown .user-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
        transform: translate(-50%, -50%);
        transition: all 0.15s ease;
        border-radius: 50%;
    }

    .user-dropdown:hover .user-btn {
        background: #f3f4f6;
        transform: translateY(-0.5px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .user-dropdown:hover .user-btn::before {
        width: 20px;
        height: 20px;
    }

    .user-dropdown:hover .user-btn svg {
        transform: scale(1.05);
        color: #3b82f6;
    }

    .user-dropdown .user-btn svg {
        transition: all 0.15s ease;
        position: relative;
        z-index: 1;
    }

    /* Enhanced dropdown items */
    .dropdown-menu a,
    .dropdown-menu button {
        transition: all 0.15s ease;
        position: relative;
        overflow: hidden;
        margin: 2px 4px;
        border-radius: 6px;
        font-weight: 500;
        letter-spacing: 0.01em;
        color: #000000 !important;
    }

    .dropdown-menu a::before,
    .dropdown-menu button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.05), transparent);
        transition: left 0.25s ease;
    }

    .dropdown-menu a:hover,
    .dropdown-menu button:hover {
        background: #f8fafc !important;
        transform: translateX(2px);
        color: #1e40af !important;
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.12);
        border-radius: 6px;
    }

    .dropdown-menu a:hover::before,
    .dropdown-menu button:hover::before {
        left: 100%;
    }

    /* Special styling for first and last items */
    .dropdown-menu a:first-child,
    .dropdown-menu form:first-child button {
        margin-top: 8px;
    }

    .dropdown-menu a:last-child,
    .dropdown-menu form:last-child button {
        margin-bottom: 8px;
    }

    /* Logout button special styling */
    .dropdown-menu form button[type="submit"] {
        color: #dc2626 !important;
        font-weight: 600;
    }

    .dropdown-menu form button[type="submit"]:hover {
        background: #fef2f2 !important;
        color: #991b1b !important;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.12);
        border-radius: 6px;
    }

    /* Mobile show state for touch devices */
    .dropdown-menu.show {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) scale(1) !important;
        pointer-events: auto !important;
    }

    /* Enhanced loading animation */
    @keyframes dropdownSlideIn {
        0% {
            opacity: 0;
            transform: translateY(-8px) scale(0.98);
        }
        50% {
            opacity: 0.8;
            transform: translateY(-2px) scale(0.99);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .dropdown-menu.show {
        animation: dropdownSlideIn 0.2s ease;
    }

    /* Stagger animation for menu items */
    .dropdown-menu a,
    .dropdown-menu button {
        opacity: 0;
        animation: fadeInUp 0.15s ease forwards;
    }

    .dropdown-menu.show a:nth-child(1),
    .dropdown-menu.show button:nth-child(1) {
        animation-delay: 0.05s;
    }

    .dropdown-menu.show a:nth-child(2),
    .dropdown-menu.show button:nth-child(2) {
        animation-delay: 0.075s;
    }

    .dropdown-menu.show a:nth-child(3),
    .dropdown-menu.show button:nth-child(3) {
        animation-delay: 0.1s;
    }

    .dropdown-menu.show a:nth-child(4),
    .dropdown-menu.show button:nth-child(4) {
        animation-delay: 0.125s;
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(5px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Hover state items show immediately */
    .user-dropdown:hover .dropdown-menu a,
    .user-dropdown:hover .dropdown-menu button {
        opacity: 1;
        animation: none;
    }

    /* Ripple effect for button clicks */
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.15);
        transform: scale(0);
        animation: rippleAnimation 0.3s linear;
        pointer-events: none;
    }

    @keyframes rippleAnimation {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }

    /* Microinteractions for better feedback */
    .dropdown-menu a:active,
    .dropdown-menu button:active {
        transform: translateX(2px) scale(0.99);
    }

    /* Focus states for accessibility */
    .user-btn:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    .dropdown-menu a:focus,
    .dropdown-menu button:focus {
        outline: 2px solid #3b82f6;
        outline-offset: -2px;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    }

    /* Smooth transitions for better perceived performance */
    .user-dropdown * {
        will-change: transform, opacity;
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .user-dropdown .dropdown-menu {
            border: 2px solid #000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background: #000;
            color: #fff;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .user-dropdown .dropdown-menu,
        .dropdown-menu a,
        .dropdown-menu button,
        .user-dropdown .user-btn {
            transition: none;
            animation: none;
        }

        .dropdown-menu.show {
            animation: none;
        }
    }

    /* Cart/Wishlist badge */
    .badge-bounce {
        transition: transform 0.2s ease;
    }

    .adidas-btn:hover .badge-bounce {
        transform: scale(1.1);
    }

    /* Search button hover */
    .search-btn {
        transition: background-color 0.2s ease;
    }

    .search-btn:hover {
        background-color: #1e40af;
    }

    /* Mobile menu */
    .mobile-menu-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .mobile-menu-item:hover {
        background: #f8fafc;
        border-left-color: #3b82f6;
        padding-left: 16px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .user-dropdown .dropdown-menu {
            right: 0;
            min-width: 180px;
            margin-top: 12px;
            border-radius: 10px;
        }

        .user-dropdown .dropdown-menu::before {
            right: 16px;
        }

        /* On mobile, prefer click over hover */
        .user-dropdown:hover .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.98);
            pointer-events: none;
        }

        .user-dropdown .dropdown-menu.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) scale(1) !important;
            pointer-events: auto !important;
        }

        /* Enhanced touch targets for mobile */
        .dropdown-menu a,
        .dropdown-menu button {
            padding: 12px 16px;
            font-size: 15px;
            margin: 1px 3px;
        }
    }

    @media (min-width: 769px) {
        /* Desktop hover should work */
        .user-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        /* Smooth hover delay */
        .user-dropdown .dropdown-menu {
            transition-delay: 0.05s;
        }

        .user-dropdown:hover .dropdown-menu {
            transition-delay: 0s;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .user-dropdown .dropdown-menu {
            background: #1f2937;
            border: 1px solid #374151;
        }

        .user-dropdown .dropdown-menu::before {
            background: #1f2937;
            border-color: #374151;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            color: #f9fafb;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background: #374151;
            color: #60a5fa;
        }
    }
</style>

<script>
    // Simple mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function () {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    });

    // Enhanced dropdown functionality with better UX
    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.querySelector('.user-dropdown');
        if (!dropdown) return;

        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        const userBtn = dropdown.querySelector('.user-btn');
        let hoverTimeout;
        let clickTimeout;

        // Ensure dropdown menu is properly initialized
        if (dropdownMenu) {
            // Remove any conflicting classes
            dropdownMenu.classList.remove('hidden');

            // Enhanced hover functionality for desktop
            dropdown.addEventListener('mouseenter', function () {
                clearTimeout(hoverTimeout);
                if (window.innerWidth > 768) {
                    dropdownMenu.classList.add('show');
                    // Stagger animation for items
                    const items = dropdownMenu.querySelectorAll('a, button');
                    items.forEach((item, index) => {
                        item.style.animationDelay = `${0.1 + (index * 0.05)}s`;
                    });
                }
            });

            dropdown.addEventListener('mouseleave', function () {
                if (window.innerWidth > 768) {
                    hoverTimeout = setTimeout(() => {
                        dropdownMenu.classList.remove('show');
                    }, 100);
                }
            });

            // Enhanced click functionality for mobile/touch
            userBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                clearTimeout(clickTimeout);

                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.className = 'ripple-effect';
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);

                // Toggle the dropdown with smooth animation
                if (dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                } else {
                    // Close any other open dropdowns first
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) menu.classList.remove('show');
                    });

                    dropdownMenu.classList.add('show');

                    // Stagger animation for items
                    const items = dropdownMenu.querySelectorAll('a, button');
                    items.forEach((item, index) => {
                        item.style.animationDelay = `${0.1 + (index * 0.05)}s`;
                    });
                }
            });

            // Close dropdown when clicking outside with smooth animation
            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });

            // Close dropdown on escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    dropdownMenu.classList.remove('show');
                    userBtn.focus(); // Return focus to button
                }
            });

            // Handle window resize
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
    });
</script>
