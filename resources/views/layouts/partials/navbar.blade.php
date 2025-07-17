<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ asset('storage/' . (get_setting() ? get_setting()->logo : 'default_logo.png')) }}"
                         alt="Logo" class="h-10 w-auto"/>
                </a>
            </div>

            {{-- Mobile menu button --}}
            <div class="md:hidden">
                <button type="button" id="mobile-menu-btn" class="p-2 text-gray-600 hover:text-black focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex md:items-center md:space-x-8">
                {{-- Main Menu --}}
                <nav class="flex space-x-8">
                    <a href="{{ route('home') }}" 
                       class="nav-link text-gray-900 font-medium text-sm uppercase tracking-wide {{ request()->routeIs('home') ? 'text-black font-semibold' : 'hover:text-black' }}">
                        Trang chủ
                    </a>
                    <a href="#" 
                       class="nav-link text-gray-900 font-medium text-sm uppercase tracking-wide hover:text-black">
                        Giới thiệu
                    </a>
                    <a href="{{ route('books.index') }}" 
                       class="nav-link text-gray-900 font-medium text-sm uppercase tracking-wide {{ request()->routeIs('books.*') ? 'text-black font-semibold' : 'hover:text-black' }}">
                        Cửa hàng
                    </a>
                    <a href="{{ route('news.index') }}" 
                       class="nav-link text-gray-900 font-medium text-sm uppercase tracking-wide {{ request()->routeIs('news.*') ? 'text-black font-semibold' : 'hover:text-black' }}">
                        Tin tức
                    </a>
                    <a href="{{ route('contact.form') }}" 
                       class="nav-link text-gray-900 font-medium text-sm uppercase tracking-wide {{ request()->routeIs('contact.*') ? 'text-black font-semibold' : 'hover:text-black' }}">
                        Liên hệ
                    </a>
                </nav>

                {{-- Right Side Icons --}}
                <div class="flex items-center space-x-4">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('books.search') }}" class="relative">
                        <input type="text" 
                               name="search"
                               placeholder="Tìm kiếm..."
                               value="{{ request('search') }}"
                               class="w-64 pl-4 pr-10 py-2 text-sm border border-gray-300 rounded-none focus:outline-none focus:border-black"
                               autocomplete="off">
                        <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-black">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>

                    {{-- User Account --}}
                    <div class="user-dropdown relative">
                        <button class="user-btn flex items-center space-x-2 p-2 text-gray-600 hover:text-black focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            @auth
                                <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                            @endauth
                        </button>
                        
                        <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white border border-gray-200 shadow-lg">
                            @auth
                                <a href="{{ route('account.profile') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Tài khoản
                                </a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Đơn hàng
                                </a>
                                <a href="{{ route('wallet.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Ví của tôi
                                </a>
                                <div class="border-t border-gray-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Đăng nhập
                                </a>
                                <a href="{{ route('register') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Đăng ký
                                </a>
                            @endauth
                        </div>
                    </div>

                    {{-- Wishlist --}}
                    <a href="{{ route('wishlist.index') }}" class="p-2 text-gray-600 hover:text-black focus:outline-none">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-black">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 h-4 w-4 bg-black text-white text-xs rounded-full flex items-center justify-center">0</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100">
            <div class="px-2 pt-4 pb-3 space-y-1">
                <a href="{{ route('home') }}" 
                   class="mobile-menu-item block px-3 py-2 text-base font-medium text-gray-900 {{ request()->routeIs('home') ? 'bg-gray-50 text-black' : 'hover:bg-gray-50' }}">
                    Trang chủ
                </a>
                <a href="#" 
                   class="mobile-menu-item block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">
                    Giới thiệu
                </a>
                <a href="{{ route('books.index') }}" 
                   class="mobile-menu-item block px-3 py-2 text-base font-medium text-gray-900 {{ request()->routeIs('books.*') ? 'bg-gray-50 text-black' : 'hover:bg-gray-50' }}">
                    Cửa hàng
                </a>
                <a href="{{ route('news.index') }}" 
                   class="mobile-menu-item block px-3 py-2 text-base font-medium text-gray-900 {{ request()->routeIs('news.*') ? 'bg-gray-50 text-black' : 'hover:bg-gray-50' }}">
                    Tin tức
                </a>
                <a href="{{ route('contact.form') }}" 
                   class="mobile-menu-item block px-3 py-2 text-base font-medium text-gray-900 {{ request()->routeIs('contact.*') ? 'bg-gray-50 text-black' : 'hover:bg-gray-50' }}">
                    Liên hệ
                </a>
                
                {{-- Mobile Search --}}
                <div class="px-3 py-2">
                    <form method="GET" action="{{ route('books.search') }}" class="relative">
                        <input type="text" 
                               name="search"
                               placeholder="Tìm kiếm..."
                               value="{{ request('search') }}"
                               class="w-full pl-4 pr-10 py-2 text-sm border border-gray-300 rounded-none focus:outline-none focus:border-black"
                               autocomplete="off">
                        <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Clean navigation styles */
    .nav-link {
        position: relative;
        transition: color 0.2s ease;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0;
        height: 2px;
        background-color: #000;
        transition: width 0.2s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    /* User dropdown */
    .user-dropdown {
        position: relative;
    }

    .user-dropdown .dropdown-menu {
        position: absolute;
        right: 0;
        top: 100%;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s ease;
        pointer-events: none;
        z-index: 50;
    }

    .user-dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }

    .user-dropdown .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* Mobile menu */
    .mobile-menu-item {
        transition: all 0.2s ease;
    }

    /* Focus states for accessibility */
    button:focus,
    input:focus,
    a:focus {
        outline: 2px solid #000;
        outline-offset: 2px;
    }

    /* Search input */
    input[type="text"] {
        transition: border-color 0.2s ease;
    }

    /* Clean button styles */
    button {
        transition: color 0.2s ease;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .user-dropdown:hover .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            pointer-events: none;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // User dropdown functionality
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown) {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            const userBtn = dropdown.querySelector('.user-btn');

            // Click functionality for mobile/touch devices
            if (userBtn && dropdownMenu) {
                userBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });

                // Close dropdown on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        dropdownMenu.classList.remove('show');
                        userBtn.focus();
                    }
                });
            }
        }
    });
</script>
