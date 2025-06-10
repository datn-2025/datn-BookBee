<!-- Adidas-Style Top Navigation -->
<nav class="bg-black text-white py-2 px-4 text-xs">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
    <div class="flex space-x-4">
      <a href="#" class="hover:text-gray-300 transition-colors">Miễn phí vận chuyển</a>
      <a href="#" class="hover:text-gray-300 transition-colors">Đổi trả & Hoàn tiền</a>
      <a href="#" class="hover:text-gray-300 transition-colors">Hỗ trợ</a>
    </div>
    <div class="flex space-x-4">
      @auth
        <a href="{{ route('account.showUser') }}" class="hover:text-gray-300 transition-colors">{{ Auth::user()->name }}</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="hover:text-gray-300 transition-colors">Đăng xuất</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="hover:text-gray-300 transition-colors">Đăng nhập</a>
        <a href="{{ route('account.register') }}" class="hover:text-gray-300 transition-colors">Đăng ký</a>
      @endauth
    </div>
  </div>
</nav>

<!-- Main Navigation -->
<nav class="bg-white shadow-lg sticky top-0 z-50 adidas-nav">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <!-- Logo -->
      <div class="flex items-center">
        <a href="/" class="flex items-center">
          <h1 class="text-2xl font-black text-black">
            BOOK<span class="adidas-gradient-text">BEE</span>
          </h1>
        </a>
      </div>

      <!-- Navigation Links -->
      <div class="hidden md:flex space-x-8">
        <a href="/" class="nav-link text-black hover:text-blue-800 font-medium transition-colors duration-200 uppercase tracking-wide {{ request()->routeIs('home') ? 'text-blue-800 font-bold border-b-2 border-blue-800' : '' }}">Trang chủ</a>
        <a href="/books" class="nav-link text-black hover:text-blue-800 font-medium transition-colors duration-200 uppercase tracking-wide {{ request()->routeIs('books.*') ? 'text-blue-800 font-bold border-b-2 border-blue-800' : '' }}">Cửa hàng</a>
        <a href="#" class="nav-link text-black hover:text-blue-800 font-medium transition-colors duration-200 uppercase tracking-wide">Danh mục</a>
        <a href="#" class="nav-link text-black hover:text-blue-800 font-medium transition-colors duration-200 uppercase tracking-wide">Giới thiệu</a>
        <a href="#" class="nav-link text-black hover:text-blue-800 font-medium transition-colors duration-200 uppercase tracking-wide">Liên hệ</a>
      </div>

      <!-- Action Icons -->
      <div class="flex items-center space-x-4">
        <!-- Search -->
        <div class="relative hidden lg:block">
          <form method="GET" action="{{ route('books.index') }}" class="flex">
            <input type="search" name="search" placeholder="Tìm kiếm sách..."
                   value="{{ request('search') }}"
                   class="w-64 px-4 py-2 border-2 border-gray-200 rounded-l-xl focus:border-black focus:outline-none transition-all duration-200 text-sm">
            <button type="submit" class="search-btn bg-black text-white px-4 py-2 rounded-r-xl hover:bg-blue-800 transition-colors duration-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </button>
          </form>
        </div>

        <!-- User Account -->
        <div class="user-dropdown relative">
          <button class="user-btn p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
            <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </button>

          <!-- Dropdown menu -->
          <div class="dropdown-menu">
            @auth
              <a href="{{ route('account.showUser') }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                <div class="flex items-center space-x-3">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  <span>Tài khoản của tôi</span>
                </div>
              </a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">
                  <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Đăng xuất</span>
                  </div>
                </button>
              </form>
            @else
              <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                <div class="flex items-center space-x-3">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                  </svg>
                  <span>Đăng nhập</span>
                </div>
              </a>
              <a href="{{ route('account.register') }}" class="block px-4 py-2 text-sm text-black hover:bg-gray-100">
                <div class="flex items-center space-x-3">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                  </svg>
                  <span>Đăng ký</span>
                </div>
              </a>
            @endauth
          </div>
        </div>

        <!-- Wishlist -->
        <button class="adidas-btn p-2 rounded-full hover:bg-gray-100 transition-colors duration-200 relative">
          <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
          </svg>
          <span class="badge-bounce absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
        </button>

        <!-- Shopping Cart -->
        <button class="adidas-btn p-2 rounded-full hover:bg-gray-100 transition-colors duration-200 relative">
          <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
          </svg>
          <span class="badge-bounce absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
        </button>
      </div>

      <!-- Mobile menu button -->
      <button class="md:hidden p-2" id="mobile-menu-btn">
        <svg class="w-6 h-6 text-black">
          <path fill="none" stroke="currentColor" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile Navigation -->
  <div class="md:hidden hidden" id="mobile-menu">
    <div class="px-4 py-4 space-y-2 bg-gray-50 border-t">
      <!-- Mobile Search -->
      <div class="mb-4">
        <form method="GET" action="{{ route('books.index') }}" class="flex">
          <input type="search" name="search" placeholder="Tìm kiếm sách..."
                 value="{{ request('search') }}"
                 class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-l-xl focus:border-black focus:outline-none">
          <button type="submit" class="search-btn bg-black text-white px-4 py-2 rounded-r-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </button>
        </form>
      </div>

      <!-- Mobile Menu Links -->
      <a href="/" class="mobile-menu-item block py-3 text-black hover:text-blue-800 font-medium uppercase tracking-wide {{ request()->routeIs('home') ? 'text-blue-800 font-bold' : '' }}">Trang chủ</a>
      <a href="/books" class="mobile-menu-item block py-3 text-black hover:text-blue-800 font-medium uppercase tracking-wide {{ request()->routeIs('books.*') ? 'text-blue-800 font-bold' : '' }}">Cửa hàng</a>
      <a href="#" class="mobile-menu-item block py-3 text-black hover:text-blue-800 font-medium uppercase tracking-wide">Danh mục</a>
      <a href="#" class="mobile-menu-item block py-3 text-black hover:text-blue-800 font-medium uppercase tracking-wide">Giới thiệu</a>
      <a href="#" class="mobile-menu-item block py-3 text-black hover:text-blue-800 font-medium uppercase tracking-wide">Liên hệ</a>
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
  document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
  });

  // Enhanced dropdown functionality with better UX
  document.addEventListener('DOMContentLoaded', function() {
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
      dropdown.addEventListener('mouseenter', function() {
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

      dropdown.addEventListener('mouseleave', function() {
        if (window.innerWidth > 768) {
          hoverTimeout = setTimeout(() => {
            dropdownMenu.classList.remove('show');
          }, 100);
        }
      });

      // Enhanced click functionality for mobile/touch
      userBtn.addEventListener('click', function(e) {
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
      document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
          dropdownMenu.classList.remove('show');
        }
      });

      // Close dropdown on escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          dropdownMenu.classList.remove('show');
          userBtn.focus(); // Return focus to button
        }
      });

      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          dropdownMenu.classList.remove('show');
        }
      });
    }
  });
</script>
