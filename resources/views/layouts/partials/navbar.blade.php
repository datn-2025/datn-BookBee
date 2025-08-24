<nav style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%); border-bottom: 2px solid #f59e0b; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: all 0.3s ease-in-out; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);" id="main-navbar">
    <div class="nav-container" style="max-width: 1280px; margin: 0 auto; padding: 0 1rem;">
        <div class="nav-content" style="display: flex; justify-content: space-between; align-items: center; height: 4rem;">
            {{-- Logo --}}
            <div class="nav-logo" style="flex-shrink: 0;">
                <a href="{{ route('home') }}" style="display: block; text-decoration: none;">
                    <h2 style="font-size: 1.5rem; font-weight: 900; color: #f59e0b; text-transform: uppercase; letter-spacing: -0.025em; margin: 0; text-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);">BOOK<span style="color: #1f2937;">BEE</span></h2>
                </a>
            </div>

            {{-- Mobile menu button --}}
            <div style="display: block;" class="md-hidden">
                <button type="button" id="mobile-menu-btn" class="mobile-menu-btn" style="display: block; padding: 0.5rem; color: #f59e0b; background: none; border: none; cursor: pointer; transition: color 0.2s ease;" onmouseover="this.style.color='#d97706'" onmouseout="this.style.color='#f59e0b'">
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
                       style="color: {{ request()->routeIs('home') ? '#f59e0b' : '#374151' }}; font-weight: {{ request()->routeIs('home') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
                        Trang chủ
                    </a>
                    <a href="{{ route('about') }}" 
                       class="nav-link"
                       style="color: {{ request()->routeIs('about') ? '#f59e0b' : '#374151' }}; font-weight: {{ request()->routeIs('about') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
                        Giới thiệu
                    </a>
                    <!-- Dropdown Cửa hàng -->
                    <div class="shop-dropdown">
                        <button class="nav-link" 
                                style="color: {{ request()->routeIs('books.*') || request()->routeIs('combos.*') ? '#f59e0b' : '#374151' }}; font-weight: {{ request()->routeIs('books.*') || request()->routeIs('combos.*') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: 0.25rem; background: none; border: none; cursor: pointer;"
                                aria-expanded="false"
                                aria-haspopup="true">
                            Cửa hàng
                            <svg style="width: 1rem; height: 1rem; transform: rotate(0deg); transition: transform 0.2s ease;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu" style="position: absolute; left: 0; top: 100%; margin-top: 0.5rem; width: 12rem; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px solid #f59e0b; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s ease; pointer-events: none; z-index: 50; border-radius: 8px;">
                            <!-- Books with submenu -->
                            <div class="books-dropdown-item" style="position: relative;">
                                <a href="{{ route('books.index') }}" 
                                   class="books-main-link"
                                   style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; text-decoration: none; transition: all 0.2s ease; {{ request()->routeIs('books.*') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-weight: 600;' : '' }}"
                                   onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'; }"
                                   onmouseout="if(!'{{ request()->routeIs('books.*') }}') { this.style.background='transparent'; this.style.color='#374151'; }">
                                    <div style="display: flex; align-items: center; gap: 0.75rem; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 0.25rem; height: 1rem; background-color: #f59e0b;"></div>
                                            <span style="text-transform: uppercase; letter-spacing: 0.05em;">Sách</span>
                                        </div>
                                        <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </a>
                                
                                <!-- Books Categories Submenu -->
                                <div class="books-submenu" style="position: absolute; left: 100%; top: 0; width: 14rem; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px solid #f59e0b; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); opacity: 0; visibility: hidden; transform: translateX(-8px); transition: all 0.2s ease; pointer-events: none; z-index: 60; max-height: 20rem; overflow-y: auto; border-radius: 8px;">
                                    @if(isset($navCategories) && $navCategories->count() > 0)
                                        @foreach($navCategories as $navCategory)
                                        <a href="{{ route('books.index', $navCategory->slug) }}" 
                                           style="display: block; padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 400; color: #6b7280; text-decoration: none; transition: all 0.2s ease;"
                                           onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                           onmouseout="this.style.background='transparent'; this.style.color='#6b7280'">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; justify-content: space-between;">
                                                <span>{{ $navCategory->name }}</span>
                                                <span style="font-size: 0.7rem; color: #9ca3af;">({{ $navCategory->books_count }})</span>
                                            </div>
                                        </a>
                                        @endforeach
                                    @else
                                        <div style="padding: 0.75rem 1rem; font-size: 0.8rem; color: #9ca3af; text-align: center;">
                                            Chưa có danh mục sách
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <a href="{{ route('combos.index') }}" 
                               style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; text-decoration: none; transition: all 0.2s ease; {{ request()->routeIs('combos.*') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-weight: 600;' : '' }}"
                               onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'; }"
                               onmouseout="if(!'{{ request()->routeIs('combos.*') }}') { this.style.background='transparent'; this.style.color='#374151'; }">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 0.25rem; height: 1rem; background-color: #f59e0b;"></div>
                                    <span style="text-transform: uppercase; letter-spacing: 0.05em;">Combo sách</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('news.index') }}" 
                       class="nav-link"
                       style="color: {{ request()->routeIs('news.*') ? '#f59e0b' : '#374151' }}; font-weight: {{ request()->routeIs('news.*') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
                        Tin tức
                    </a>
                    <a href="{{ route('contact.form') }}" 
                       class="nav-link"
                       style="color: {{ request()->routeIs('contact.*') ? '#f59e0b' : '#374151' }}; font-weight: {{ request()->routeIs('contact.*') ? '600' : '500' }}; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; transition: color 0.2s ease;">
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
                               style="width: 16rem; padding-left: 1rem; padding-right: 2.5rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem; border: 2px solid #f59e0b; border-radius: 25px; outline: none; transition: all 0.2s ease; background: rgba(248, 250, 252, 0.8); color: #374151; backdrop-filter: blur(10px);"
                               autocomplete="off"
                               onfocus="this.style.borderColor='#d97706'; this.style.background='rgba(255, 255, 255, 0.9)';"
                               onblur="this.style.borderColor='#f59e0b'; this.style.background='rgba(248, 250, 252, 0.8)';">
                        <button type="submit" style="position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center; padding-right: 0.75rem; color: #f59e0b; background: none; border: none; cursor: pointer; transition: color 0.2s ease;"
                                onmouseover="this.style.color='#d97706'"
                                onmouseout="this.style.color='#f59e0b'">
                            <svg style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>

                    {{-- User Account --}}
                    <div class="user-dropdown" style="position: relative;">
                        <button class="user-btn" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #374151; background: none; border: none; cursor: pointer; transition: color 0.2s ease;"
                                onmouseover="this.style.color='#f59e0b'"
                                onmouseout="this.style.color='#374151'">
                            <svg style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            @auth
                                <span style="font-size: 0.875rem; font-weight: 500;">{{ Auth::user()->name }}</span>
                            @endauth
                        </button>
                        
                        <div class="dropdown-menu user-dropdown-menu">
                            @auth
                                <a href="{{ route('account.profile') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                   onmouseout="this.style.background='transparent'; this.style.color='#374151'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Tài khoản
                                </a>
                                <a href="{{ route('orders.index') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                   onmouseout="this.style.background='transparent'; this.style.color='#374151'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Đơn hàng
                                </a>
                                <a href="{{ route('wallet.index') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                   onmouseout="this.style.background='transparent'; this.style.color='#374151'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Ví của tôi
                                </a>
                                <div style="border-top: 1px solid #e5e7eb;">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" style="width: 100%; text-align: left; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; background: none; border: none; cursor: pointer; transition: all 0.2s ease;"
                                                onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                                onmouseout="this.style.background='transparent'; this.style.color='#374151'">
                                            <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                   onmouseout="this.style.background='transparent'; this.style.color='#374151'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Đăng nhập
                                </a>
                                <a href="{{ route('register') }}" style="display: block; padding: 0.75rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                   onmouseout="this.style.background='transparent'; this.style.color='#374151'">
                                    <svg style="display: inline; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Đăng ký
                                </a>
                            @endauth
                        </div>
                    </div>

                    {{-- Notifications --}}
                    @auth
                    <div class="notification-dropdown" style="position: relative;">
                        <button type="button" class="notification-btn" style="position: relative; padding: 0.5rem; color: #374151; background: none; border: none; cursor: pointer; transition: color 0.2s ease;"
                                onmouseover="this.style.color='#f59e0b'"
                                onmouseout="this.style.color='#374151'"
                                onclick="toggleNotificationDropdown()">
                            <svg style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="notification-badge" class="notification-badge" style="position: absolute; top: -0.25rem; right: -0.25rem; height: 1rem; width: 1rem; background-color: #ef4444; color: white; font-size: 0.75rem; border-radius: 50%; display: none; align-items: center; justify-content: center;">0</span>
                        </button>
                        
                        <div id="notification-dropdown" class="notification-dropdown-menu" style="position: absolute; right: 0; top: 100%; margin-top: 0.5rem; width: 20rem; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px solid #f59e0b; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s ease; pointer-events: none; z-index: 9999; border-radius: 8px;">
                            <!-- Header -->
                            <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <h6 style="margin: 0; font-size: 1rem; font-weight: 600; color: white;">Thông báo</h6>
                                <span id="notification-count" style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.8);">0 thông báo mới</span>
                            </div>
                            
                            <!-- Notification List -->
                            <div id="notification-list" style="max-height: 240px; overflow-y: auto;">
                                <div style="padding: 2rem 1rem; text-align: center; color: #6b7280;">
                                    <svg style="height: 3rem; width: 3rem; margin: 0 auto 1rem; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <p style="margin: 0; font-size: 0.875rem;">Chưa có thông báo nào</p>
                                </div>
                            </div>
                            
                            <!-- View All Link -->
                            <div style="border-top: 1px solid #e5e7eb; padding: 0.75rem;">
                                <a href="{{ route('notifications.index') }}" style="display: block; text-align: center; color: #f59e0b; text-decoration: none; font-size: 0.875rem; font-weight: 500; padding: 0.5rem; border-radius: 0.375rem; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='white'"
                                   onmouseout="this.style.background='transparent'; this.style.color='#f59e0b'">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>
                    @endauth

                    {{-- Wishlist --}}
                    <a href="{{ route('wishlist.index') }}" style="position: relative; padding: 0.5rem; color: #374151; text-decoration: none; transition: color 0.2s ease;"
                       onmouseover="this.style.color='#f59e0b'"
                       onmouseout="this.style.color='#374151'">
                        <svg style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        @if(isset($wishlistItemCount) && $wishlistItemCount > 0)
                            <span style="position: absolute; top: -0.25rem; right: -0.25rem; height: 1rem; width: 1rem; background-color: #ef4444; color: white; font-size: 0.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                {{ $wishlistItemCount > 99 ? '99+' : $wishlistItemCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" style="position: relative; padding: 0.5rem; color: #374151; text-decoration: none; transition: color 0.2s ease;"
                       onmouseover="this.style.color='#f59e0b'"
                       onmouseout="this.style.color='#374151'">
                        <svg style="height: 1.25rem; width: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if(isset($cartItemCount) && $cartItemCount > 0)
                            <span style="position: absolute; top: -0.25rem; right: -0.25rem; height: 1rem; width: 1rem; background-color: #f59e0b; color: black; font-size: 0.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                {{ $cartItemCount > 99 ? '99+' : $cartItemCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" style="display: none; border-top: 2px solid #f59e0b; background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);">
            <div style="padding: 1rem 0.5rem;">
                <a href="{{ route('home') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: white; text-decoration: none; {{ request()->routeIs('home') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: black;' : '' }} transition: all 0.2s ease; border-radius: 6px; margin-bottom: 0.25rem;"
                   onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='black'; }"
                   onmouseout="if(!'{{ request()->routeIs('home') }}') { this.style.background='transparent'; this.style.color='white'; }">
                    Trang chủ
                </a>
                <a href="{{ route('about') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: white; text-decoration: none; {{ request()->routeIs('about') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: black;' : '' }} transition: all 0.2s ease; border-radius: 6px; margin-bottom: 0.25rem;"
                   onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='black'; }"
                   onmouseout="if(!'{{ request()->routeIs('about') }}') { this.style.background='transparent'; this.style.color='white'; }">
                    Giới thiệu
                </a>
                <a href="{{ route('books.index') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: white; text-decoration: none; {{ request()->routeIs('books.*') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: black;' : '' }} transition: all 0.2s ease; border-radius: 6px; margin-bottom: 0.25rem;"
                   onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='black'; }"
                   onmouseout="if(!'{{ request()->routeIs('books.*') }}') { this.style.background='transparent'; this.style.color='white'; }">
                    Cửa hàng
                </a>
                <a href="{{ route('combos.index') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: white; text-decoration: none; {{ request()->routeIs('combos.*') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: black;' : '' }} transition: all 0.2s ease; border-radius: 6px; margin-bottom: 0.25rem;"
                   onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='black'; }"
                   onmouseout="if(!'{{ request()->routeIs('combos.*') }}') { this.style.background='transparent'; this.style.color='white'; }">
                    Combo sách
                </a>
                <a href="{{ route('news.index') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: white; text-decoration: none; {{ request()->routeIs('news.*') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: black;' : '' }} transition: all 0.2s ease; border-radius: 6px; margin-bottom: 0.25rem;"
                   onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='black'; }"
                   onmouseout="if(!'{{ request()->routeIs('news.*') }}') { this.style.background='transparent'; this.style.color='white'; }">
                    Tin tức
                </a>
                <a href="{{ route('contact.form') }}" 
                   style="display: block; padding: 0.75rem; font-size: 1rem; font-weight: 500; color: white; text-decoration: none; {{ request()->routeIs('contact.*') ? 'background: linear-gradient(135deg, #f59e0b, #d97706); color: black;' : '' }} transition: all 0.2s ease; border-radius: 6px; margin-bottom: 0.25rem;"
                   onmouseover="if(!this.style.background.includes('gradient')) { this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'; this.style.color='black'; }"
                   onmouseout="if(!'{{ request()->routeIs('contact.*') }}') { this.style.background='transparent'; this.style.color='white'; }">
                    Liên hệ
                </a>
                
                {{-- Mobile Search --}}
                <div style="padding: 0.75rem;">
                    <form method="GET" action="{{ route('books.search') }}" style="position: relative;">
                        <input type="text" 
                               name="search"
                               placeholder="Tìm kiếm..."
                               value="{{ request('search') }}"
                               style="width: 100%; padding-left: 1rem; padding-right: 2.5rem; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 0.875rem; border: 2px solid #f59e0b; border-radius: 25px; outline: none; transition: all 0.2s ease; background: rgba(248, 250, 252, 0.8); color: #374151; backdrop-filter: blur(10px);"
                               autocomplete="off"
                               onfocus="this.style.borderColor='#d97706'; this.style.background='rgba(255, 255, 255, 0.9)';"
                               onblur="this.style.borderColor='#f59e0b'; this.style.background='rgba(248, 250, 252, 0.8)';">
                        <button type="submit" style="position: absolute; top: 0; right: 0; bottom: 0; display: flex; align-items: center; padding-right: 0.75rem; color: #f59e0b; background: none; border: none; cursor: pointer; transition: color 0.2s ease;" onmouseover="this.style.color='#d97706'" onmouseout="this.style.color='#f59e0b'">
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
    /* Fixed navbar với shadow khi scroll */
    #main-navbar {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    #main-navbar.scrolled {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 50%, rgba(255, 255, 255, 0.98) 100%);
    }

    /* Padding top cho body để tránh bị che bởi fixed navbar */
    body {
        padding-top: 4rem;
    }

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
        background-color: #f59e0b;
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
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid #f59e0b;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s ease;
        pointer-events: none;
        z-index: 9999;
        border-radius: 8px;
    }

    .user-dropdown .dropdown-menu.show,
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
        margin-top: 0.5rem;
        width: 12rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid #f59e0b;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s ease;
        pointer-events: none;
        z-index: 50;
        border-radius: 8px;
    }

    .shop-dropdown:hover .dropdown-menu,
    .shop-dropdown .dropdown-menu.show {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    /* Force dropdown to show on hover - fallback */
    .shop-dropdown:hover > .dropdown-menu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    .user-dropdown:hover > .dropdown-menu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    /* Books dropdown with submenu */
    .books-dropdown-item {
        position: relative;
    }

    .books-dropdown-item .books-submenu {
        position: absolute;
        left: 100%;
        top: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-8px);
        transition: all 0.2s ease;
        pointer-events: none;
        z-index: 60;
    }

    .books-dropdown-item:hover .books-submenu {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateX(0) !important;
        pointer-events: auto !important;
    }

    /* Keep parent dropdown open when hovering over submenu */
    .shop-dropdown:hover .dropdown-menu,
    .dropdown-menu:hover,
    .books-submenu:hover {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    .books-submenu:hover {
        transform: translateX(0) !important;
    }

    /* User dropdown hover effect */
    .user-dropdown:hover .dropdown-menu {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    /* Keep dropdown open when hovering over the dropdown menu itself */
    .user-dropdown .dropdown-menu:hover {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    /* Mobile menu */
    .mobile-menu-item {
        transition: all 0.2s ease;
    }
    
    /* Hover effects cho desktop */
    @media (min-width: 768px) {
        .nav-link:hover {
            color: #f59e0b !important;
        }

        .shop-dropdown:hover button svg {
            transform: rotate(180deg);
        }

        /* Ensure smooth transitions when hovering */
        .user-dropdown:hover .dropdown-menu,
        .shop-dropdown:hover .dropdown-menu {
            transition-delay: 0ms !important;
        }

        /* Keep dropdowns open when moving mouse between trigger and menu */
        .user-dropdown::before,
        .shop-dropdown::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            height: 0.5rem;
            background: transparent;
            z-index: 9998;
        }

        /* Additional spacing for books submenu */
        .books-dropdown-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 100%;
            bottom: 0;
            width: 0.5rem;
            background: transparent;
            z-index: 59;
        }
    }
    
    /* Focus states for accessibility */
    button:focus,
    input:focus,
    a:focus {
        outline: 2px solid #f59e0b;
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
        const navbar = document.getElementById('main-navbar');
        let lastScrollY = window.scrollY;

        // Thêm class 'scrolled' khi cuộn xuống
        function handleScroll() {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScrollY = currentScrollY;
        }

        // Lắng nghe sự kiện scroll
        window.addEventListener('scroll', handleScroll, { passive: true });

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

            let clickToggled = false;

            // Click functionality for toggle
            if (shopBtn && dropdownMenu) {
                shopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    clickToggled = !clickToggled;
                    
                    // Toggle the 'show' class for click-based control
                    if (clickToggled) {
                        dropdownMenu.classList.add('show');
                        shopBtn.querySelector('svg').style.transform = 'rotate(180deg)';
                        shopBtn.setAttribute('aria-expanded', 'true');
                    } else {
                        dropdownMenu.classList.remove('show');
                        shopBtn.querySelector('svg').style.transform = 'rotate(0deg)';
                        shopBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Reset click state when mouse leaves the dropdown
                shopDropdown.addEventListener('mouseleave', function() {
                    if (clickToggled) {
                        setTimeout(() => {
                            clickToggled = false;
                            dropdownMenu.classList.remove('show');
                            shopBtn.querySelector('svg').style.transform = 'rotate(0deg)';
                            shopBtn.setAttribute('aria-expanded', 'false');
                        }, 300); // Small delay to prevent flicker
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!shopDropdown.contains(e.target)) {
                        clickToggled = false;
                        dropdownMenu.classList.remove('show');
                        shopBtn.querySelector('svg').style.transform = 'rotate(0deg)';
                        shopBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Close dropdown on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        clickToggled = false;
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

            let clickToggled = false;

            // Click functionality for toggle
            if (userBtn && dropdownMenu) {
                userBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    clickToggled = !clickToggled;
                    
                    // Toggle the 'show' class for click-based control
                    if (clickToggled) {
                        dropdownMenu.classList.add('show');
                    } else {
                        dropdownMenu.classList.remove('show');
                    }
                });

                // Reset click state when mouse leaves the dropdown
                dropdown.addEventListener('mouseleave', function() {
                    if (clickToggled) {
                        setTimeout(() => {
                            clickToggled = false;
                            dropdownMenu.classList.remove('show');
                        }, 300); // Small delay to prevent flicker
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        clickToggled = false;
                        dropdownMenu.classList.remove('show');
                    }
                });

                // Close dropdown on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        clickToggled = false;
                        dropdownMenu.classList.remove('show');
                        userBtn.focus();
                    }
                });
            }
        }
    });
</script>
