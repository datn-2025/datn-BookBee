<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ get_setting() ? get_setting()->name_website : 'BookBee' }} - @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('storage/' . (get_setting() ? get_setting()->favicon : 'default_favicon.ico')) }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Disable hover effects -->
    <link href="{{ asset('css/disable-hover-effects.css') }}" rel="stylesheet" />
    <!-- Nuclear option - disable all effects -->
    <link href="{{ asset('css/disable-all-effects.css') }}" rel="stylesheet" />
   

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Cart Count Manager -->
    <script src="{{ asset('js/cart-count-manager.js') }}"></script>
    
    <!-- Wishlist Count Manager -->
    <script src="{{ asset('js/wishlist-count-manager.js') }}"></script>

    <!-- Prevent FOUC Script -->
    <script>
        // Ensure page loads smoothly without flash
        document.documentElement.style.visibility = 'visible';
        document.documentElement.style.opacity = '1';
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>

    <!-- IntersectionObserver polyfill -->
    <script>
        if (!('IntersectionObserver' in window)) {
            document.write('<script src="https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver"><\/script>');
        }
    </script>

    <!-- Google Fonts Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Critical CSS để prevent FOUC -->
    <style>
      /* Critical CSS - Load ngay để tránh navbar nháy */
      nav {
        background-color: white;
        border-bottom: 1px solid #f3f4f6;
        position: relative;
        z-index: 50;
      }
      
      .nav-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
      }
      
      .nav-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 4rem;
      }
      
      .nav-logo h2 {
        font-size: 1.5rem;
        font-weight: 900;
        color: black;
        text-transform: uppercase;
        letter-spacing: -0.025em;
        margin: 0;
      }
      
      .nav-desktop {
        display: none;
      }
      
      @media (min-width: 768px) {
        .nav-desktop {
          display: flex;
          align-items: center;
          gap: 2rem;
        }
        
        .nav-menu {
          display: flex;
          gap: 2rem;
        }
        
        .nav-link {
          color: #374151;
          font-weight: 500;
          font-size: 0.875rem;
          text-transform: uppercase;
          letter-spacing: 0.05em;
          text-decoration: none;
          transition: color 0.2s ease;
        }
        
        .nav-link:hover {
          color: black;
        }
        
        .nav-icons {
          display: flex;
          align-items: center;
          gap: 1rem;
        }
      }
      
      .mobile-menu-btn {
        display: block;
        padding: 0.5rem;
        color: #6b7280;
        background: none;
        border: none;
        cursor: pointer;
      }
      
      @media (min-width: 768px) {
        .mobile-menu-btn {
          display: none;
        }
      }
      
      /* Prevent FOUC và ensure smooth loading */
      html {
        visibility: visible !important;
        opacity: 1 !important;
      }
      
      body {
        margin: 0;
        min-height: 100vh;
        visibility: visible !important;
        opacity: 1 !important;
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      }
      
      /* Ensure all elements load without flash */
      * {
        box-sizing: border-box;
      }
      
      .adidas-nav {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      }
      .adidas-btn {
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
      }
      .adidas-btn:hover {
        transform: scale(1.05);
      }
      .adidas-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
      }
      .adidas-btn:hover::before {
        left: 100%;
      }
      .adidas-gradient-text {
        background: linear-gradient(45deg, #000000, #767677, #000000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }
      /* Chat notification animation */
      @keyframes bounce {
        0%, 20%, 53%, 80%, 100% {
          animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
          transform: translate3d(0,0,0);
        }
        40%, 43% {
          animation-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
          transform: translate3d(0, -10px, 0);
        }
        70% {
          animation-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
          transform: translate3d(0, -7px, 0);
        }
        90% {
          transform: translate3d(0,-2px,0);
        }
      }
      .animate-bounce {
        animation: bounce 1s;
      }
    </style>
</head>

<body style="margin:0; min-height:100vh;">
    @include('layouts.partials.navbar')
    <div id="notification" class=" alert mx-3 invisible" >
    </div>
    @yield('content')

    {!! Toastr::message() !!}

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Chat Widget - Chỉ hiển thị khi đăng nhập --}}
    @auth
        @include('components.chat-widget')
    @endauth

    @include('components.chatbot-widget')


    @stack('scripts')
    @include('layouts.partials.footer')

    <!-- Test broadcast script removed - file not found -->

    <!-- Chat script moved to app.js -->
    
    <!-- Address selection scripts are now handled by individual pages using GHN API -->
</body>

</html>