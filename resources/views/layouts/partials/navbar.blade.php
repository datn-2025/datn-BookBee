<nav class="bg-white border-b border-gray-100" style="background-color: white; border-bottom: 1px solid #f3f4f6; position: relative; z-index: 50;">
    <div class="nav-container" style="max-width: 1280px; margin: 0 auto; padding: 0 1rem;">
        <div class="nav-content" style="display: flex; justify-content: space-between; align-items: center; height: 4rem;">
            {{-- Logo --}}
            <div class="nav-logo" style="flex-shrink: 0;">
                <a href="{{ route('home') }}" style="display: block; text-decoration: none;">
                    <h2 style="font-size: 1.5rem; font-weight: 900; color: black; text-transform: uppercase; letter-spacing: -0.025em; margin: 0;">BOOK<span style="color: black;">BEE</span></h2>
                </a>
            </div>

            {{-- Mobile menu button --}}
            <div style="display: block;" class="md-hidden">
                <button type="button" id="mobile-menu-btn" class="mobile-menu-btn" style="display: block; padding: 0.5rem; color: #6b7280; background: none; border: none; cursor: pointer;">
                    <svg style="height: 1.5rem; width: 1.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            {{-- Desktop Navigation --}}
            <div class="nav-desktop" style="display: none; align-items: center; gap: 2rem;">
                {{-- Main Menu --}}
                <nav class="nav-menu" style="display: flex; gap: 2rem;">
                    <a href="{{ route('home') }}" 
                       class="nav-link" 
                       style="color: {{ request()->routeIs('home') ? 'black' : '#374151' }}; font-weight: {{ request()->routeIs('home') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
                        Trang chủ
                    </a>
                    <a href="{{ route('about') }}" 
                       class="nav-link"
                       style="color: {{ request()->routeIs('about') ? 'black' : '#374151' }}; font-weight: {{ request()->routeIs('about') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
                        Giới thiệu
                    </a>
                    <!-- Dropdown Cửa hàng -->
                    <div class="shop-dropdown">
                        <button class="nav-link" 
                                style="color: {{ request()->routeIs('books.*') || request()->routeIs('combos.*') ? 'black' : '#374151' }}; font-weight: {{ request()->routeIs('books.*') || request()->routeIs('combos.*') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: 0.25rem; background: none; border: none; cursor: pointer;"
                                aria-expanded="false"
                                aria-haspopup="true">
                            Cửa hàng
                            <svg style="width: 1rem; height: 1rem; transform: rotate(0deg); transition: transform 0.2s ease;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu" style="position: absolute; left: 0; top: 100%; margin-top: 0.5rem; width: 12rem; background-color: white; border: 2px solid #e5e7eb; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s ease; pointer-events: none; z-index: 50;">
                            <a href="{{ route('books.index') }}" 
                               style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; text-decoration: none; transition: background-color 0.2s ease; {{ request()->routeIs('books.*') ? 'background-color: #f9fafb; color: black; font-weight: 600;' : '' }}"
                               onmouseover="this.style.backgroundColor='#f9fafb'; this.style.color='black'"
                               onmouseout="this.style.backgroundColor='{{ request()->routeIs('books.*') ? '#f9fafb' : 'white' }}'; this.style.color='{{ request()->routeIs('books.*') ? 'black' : '#374151' }}'">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 0.25rem; height: 1rem; background-color: black;"></div>
                                    <span style="text-transform: uppercase; letter-spacing: 0.05em;">Sách</span>
                                </div>
                            </a>
                            <a href="{{ route('combos.index') }}" 
                               style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; text-decoration: none; transition: background-color 0.2s ease; {{ request()->routeIs('combos.*') ? 'background-color: #f9fafb; color: black; font-weight: 600;' : '' }}"
                               onmouseover="this.style.backgroundColor='#f9fafb'; this.style.color='black'"
                               onmouseout="this.style.backgroundColor='{{ request()->routeIs('combos.*') ? '#f9fafb' : 'white' }}'; this.style.color='{{ request()->routeIs('combos.*') ? 'black' : '#374151' }}'">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 0.25rem; height: 1rem; background-color: black;"></div>
                                    <span style="text-transform: uppercase; letter-spacing: 0.05em;">Combo sách</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('news.index') }}" 
                       class="nav-link"
                       style="color: {{ request()->routeIs('news.*') ? 'black' : '#374151' }}; font-weight: {{ request()->routeIs('news.*') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
                        Tin tức
                    </a>
                    <a href="{{ route('contact.form') }}" 
                       class="nav-link"
                       style="color: {{ request()->routeIs('contact.*') ? 'black' : '#374151' }}; font-weight: {{ request()->routeIs('contact.*') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
                        Liên hệ
                    </a>
                </nav>

                {{-- Right Side Icons --}}
                <div class="nav-icons" style="display: flex; align-items: center; gap: 1rem;">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('books.search') }}" style="position: relative;">
                        <input type="text" 
                               name="search"
                               placeholder="Tìm kiếm..."
                               value="{{ request('search') }}"
                               style="width: 16rem; padding-left: 1rem; padding-right: 2.5rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0; outline: none; transition: border-color 0.2s ease;"
                               autocomplete="off"
                               onfocus="this.style.borderColor='black'"
                               onblur="this.style.borderColor='#d1d5db'">
                        <button type="submit" style="position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center; padding-right: 0.75rem; color: #6b7280; background: none; border: none; cursor: pointer; transition: color 0.2s ease;"
                                onmouseover="this.style.color='black'"
                                onmouseout="this.style.color='#6b7280'">
                            <svg style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>

                    {{-- User Account --}}
                    <div class="user-dropdown" style="position: relative;">
                        <button class="user-btn" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #6b7280; background: none; border: none; cursor: pointer; transition: color 0.2s ease;"
                                onmouseover="this.style.color='black'"
                                onmouseout="this.style.color='#6b7280'">
                            <svg style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            @auth
                                <span style="font-size: 0.875rem; font-weight: 500;">{{ Auth::user()->name }}</span>
                            @endauth
                        </button>
                        
                        <div class="dropdown-menu user-dropdown-menu">
                            @auth
                                <a href="{{ route('account.profile') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: background-color 0.2s ease;"
                                   onmouseover="this.style.backgroundColor='#f9fafb'"
                                   onmouseout="this.style.backgroundColor='white'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Tài khoản
                                </a>
                                <a href="{{ route('orders.index') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: background-color 0.2s ease;"
                                   onmouseover="this.style.backgroundColor='#f9fafb'"
                                   onmouseout="this.style.backgroundColor='white'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Đơn hàng
                                </a>
                                <a href="{{ route('wallet.index') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: background-color 0.2s ease;"
                                   onmouseover="this.style.backgroundColor='#f9fafb'"
                                   onmouseout="this.style.backgroundColor='white'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Ví của tôi
                                </a>
                                <div style="border-top: 1px solid #f3f4f6;">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" style="width: 100%; text-align: left; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; background: none; border: none; cursor: pointer; transition: background-color 0.2s ease;"
                                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                                onmouseout="this.style.backgroundColor='white'">
                                            <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: background-color 0.2s ease;"
                                   onmouseover="this.style.backgroundColor='#f9fafb'"
                                   onmouseout="this.style.backgroundColor='white'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Đăng nhập
                                </a>
                                <a href="{{ route('register') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: background-color 0.2s ease;"
                                   onmouseover="this.style.backgroundColor='#f9fafb'"
                                   onmouseout="this.style.backgroundColor='white'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Đăng ký
                                </a>
                            @endauth
                        </div>
                    </div>

                    {{-- Wishlist --}}
                    <a href="{{ route('wishlist.index') }}" style="padding: 0.5rem; color: #6b7280; text-decoration: none; transition: color 0.2s ease;"
                       onmouseover="this.style.color='black'"
                       onmouseout="this.style.color='#6b7280'">
                        <svg style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" style="position: relative; padding: 0.5rem; color: #6b7280; text-decoration: none; transition: color 0.2s ease;"
                       onmouseover="this.style.color='black'"
                       onmouseout="this.style.color='#6b7280'">
                        <svg style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if(isset($cartItemCount) && $cartItemCount > 0)
                            <span style="position: absolute; top: -0.25rem; right: -0.25rem; height: 1rem; width: 1rem; background-color: black; color: white; font-size: 0.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                {{ $cartItemCount > 99 ? '99+' : $cartItemCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" style="display: none; border-top: 1px solid #f3f4f6;">
            <div style="padding: 1rem 0.5rem;">
                <a href="{{ route('home') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: #111827; text-decoration: none; {{ request()->routeIs('home') ? 'background-color: #f9fafb; color: black;' : '' }} transition: background-color 0.2s ease;"
                   onmouseover="if(!this.style.backgroundColor) this.style.backgroundColor='#f9fafb'"
                   onmouseout="if(!'{{ request()->routeIs('home') }}') this.style.backgroundColor=''">
                    Trang chủ
                </a>
                <a href="{{ route('about') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: #111827; text-decoration: none; {{ request()->routeIs('about') ? 'background-color: #f9fafb; color: black;' : '' }} transition: background-color 0.2s ease;"
                   onmouseover="if(!this.style.backgroundColor) this.style.backgroundColor='#f9fafb'"
                   onmouseout="if(!'{{ request()->routeIs('about') }}') this.style.backgroundColor=''">
                    Giới thiệu
                </a>
                <a href="{{ route('books.index') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: #111827; text-decoration: none; {{ request()->routeIs('books.*') ? 'background-color: #f9fafb; color: black;' : '' }} transition: background-color 0.2s ease;"
                   onmouseover="if(!this.style.backgroundColor) this.style.backgroundColor='#f9fafb'"
                   onmouseout="if(!'{{ request()->routeIs('books.*') }}') this.style.backgroundColor=''">
                    Cửa hàng
                </a>
                <a href="{{ route('combos.index') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: #111827; text-decoration: none; {{ request()->routeIs('combos.*') ? 'background-color: #f9fafb; color: black;' : '' }} transition: background-color 0.2s ease;"
                   onmouseover="if(!this.style.backgroundColor) this.style.backgroundColor='#f9fafb'"
                   onmouseout="if(!'{{ request()->routeIs('combos.*') }}') this.style.backgroundColor=''">
                    Combo sách
                </a>
                <a href="{{ route('news.index') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: #111827; text-decoration: none; {{ request()->routeIs('news.*') ? 'background-color: #f9fafb; color: black;' : '' }} transition: background-color 0.2s ease;"
                   onmouseover="if(!this.style.backgroundColor) this.style.backgroundColor='#f9fafb'"
                   onmouseout="if(!'{{ request()->routeIs('news.*') }}') this.style.backgroundColor=''">
                    Tin tức
                </a>
                <a href="{{ route('contact.form') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: #111827; text-decoration: none; {{ request()->routeIs('contact.*') ? 'background-color: #f9fafb; color: black;' : '' }} transition: background-color 0.2s ease;"
                   onmouseover="if(!this.style.backgroundColor) this.style.backgroundColor='#f9fafb'"
                   onmouseout="if(!'{{ request()->routeIs('contact.*') }}') this.style.backgroundColor=''">
                    Liên hệ
                </a>
                
                {{-- Mobile Search --}}
                <div style="padding: 0.75rem;">
                    <form method="GET" action="{{ route('books.search') }}" style="position: relative;">
                        <input type="text" 
                               name="search"
                               placeholder="Tìm kiếm..."
                               value="{{ request('search') }}"
                               style="width: 100%; padding-left: 1rem; padding-right: 2.5rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 0; outline: none; transition: border-color 0.2s ease;"
                               autocomplete="off"
                               onfocus="this.style.borderColor='black'"
                               onblur="this.style.borderColor='#d1d5db'">
                        <button type="submit" style="position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center; padding-right: 0.75rem; color: #6b7280; background: none; border: none; cursor: pointer;">
                            <svg style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
    /* Responsive styles với pure CSS */
    @media (min-width: 768px) {
        .md-hidden { display: none !important; }
        .nav-desktop { display: flex !important; }
        .mobile-menu-btn { display: none !important; }
    }

    .nav-link {
        position: relative;
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
        margin-top: 0.5rem;
        width: 12rem;
        background-color: white;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s ease;
        pointer-events: none;
        z-index: 9999;
    }

    .user-dropdown .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* User dropdown hover effect - đây là phần quan trọng */
    .user-dropdown:hover .dropdown-menu {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    /* Shop dropdown */
    .shop-dropdown {
        position: relative;
    }

    .shop-dropdown .dropdown-menu {
        position: absolute;
        left: 0;
        top: 100%;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s ease;
        pointer-events: none;
        z-index: 50;
    }

    .shop-dropdown:hover .dropdown-menu,
    .shop-dropdown .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* User dropdown hover effect */
    .user-dropdown:hover .dropdown-menu {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    /* Debug - để test hover effect */
    .user-dropdown:hover {
        background-color: rgba(255, 0, 0, 0.1) !important;
    }

    /* Force show dropdown for testing */
    .user-dropdown .dropdown-menu {
        display: block !important;
    }

    /* Mobile menu */
    .mobile-menu-item {
        transition: all 0.2s ease;
    }
    
    /* Hover effects cho desktop */
    @media (min-width: 768px) {
        .nav-link:hover {
            color: black !important;
        }

        .shop-dropdown:hover button svg {
            transform: rotate(180deg);
        }
        
        .shop-dropdown:hover .dropdown-menu {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
        }
    }
    
    /* Focus states for accessibility */
    button:focus,
    input:focus,
    a:focus {
        outline: 2px solid #000;
        outline-offset: 2px;
    }
    
    /* Prevent text selection on buttons */
    button {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                if (mobileMenu.style.display === 'none' || !mobileMenu.style.display) {
                    mobileMenu.style.display = 'block';
                } else {
                    mobileMenu.style.display = 'none';
                }
            });
        }

        // Shop dropdown functionality
        const shopDropdown = document.querySelector('.shop-dropdown');
        if (shopDropdown) {
            const dropdownMenu = shopDropdown.querySelector('.dropdown-menu');
            const shopBtn = shopDropdown.querySelector('button');

            // Click functionality
            if (shopBtn && dropdownMenu) {
                shopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isVisible = dropdownMenu.style.opacity === '1' || dropdownMenu.classList.contains('show');
                    
                    if (isVisible) {
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.visibility = 'hidden';
                        dropdownMenu.style.transform = 'translateY(-8px)';
                        dropdownMenu.style.pointerEvents = 'none';
                        dropdownMenu.classList.remove('show');
                        shopBtn.querySelector('svg').style.transform = 'rotate(0deg)';
                        shopBtn.setAttribute('aria-expanded', 'false');
                    } else {
                        dropdownMenu.style.opacity = '1';
                        dropdownMenu.style.visibility = 'visible';
                        dropdownMenu.style.transform = 'translateY(0)';
                        dropdownMenu.style.pointerEvents = 'auto';
                        dropdownMenu.classList.add('show');
                        shopBtn.querySelector('svg').style.transform = 'rotate(180deg)';
                        shopBtn.setAttribute('aria-expanded', 'true');
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!shopDropdown.contains(e.target)) {
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.visibility = 'hidden';
                        dropdownMenu.style.transform = 'translateY(-8px)';
                        dropdownMenu.style.pointerEvents = 'none';
                        dropdownMenu.classList.remove('show');
                        shopBtn.querySelector('svg').style.transform = 'rotate(0deg)';
                        shopBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Close dropdown on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.visibility = 'hidden';
                        dropdownMenu.style.transform = 'translateY(-8px)';
                        dropdownMenu.style.pointerEvents = 'none';
                        dropdownMenu.classList.remove('show');
                        shopBtn.querySelector('svg').style.transform = 'rotate(0deg)';
                        shopBtn.setAttribute('aria-expanded', 'false');
                        shopBtn.focus();
                    }
                });
            }
        }

        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown) {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            const userBtn = dropdown.querySelector('.user-btn');

            // Click functionality for mobile/touch devices
            if (userBtn && dropdownMenu) {
                userBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (dropdownMenu.style.opacity === '1') {
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.visibility = 'hidden';
                        dropdownMenu.style.transform = 'translateY(-8px)';
                        dropdownMenu.style.pointerEvents = 'none';
                    } else {
                        dropdownMenu.style.opacity = '1';
                        dropdownMenu.style.visibility = 'visible';
                        dropdownMenu.style.transform = 'translateY(0)';
                        dropdownMenu.style.pointerEvents = 'auto';
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.visibility = 'hidden';
                        dropdownMenu.style.transform = 'translateY(-8px)';
                        dropdownMenu.style.pointerEvents = 'none';
                    }
                });

                // Close dropdown on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        dropdownMenu.style.opacity = '0';
                        dropdownMenu.style.visibility = 'hidden';
                        dropdownMenu.style.transform = 'translateY(-8px)';
                        dropdownMenu.style.pointerEvents = 'none';
                        userBtn.focus();
                    }
                });
            }
        }
    });
</script>
