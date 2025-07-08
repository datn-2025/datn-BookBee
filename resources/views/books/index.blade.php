<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
  <title>BookBee - Cửa hàng sách cao cấp</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'adidas-black': '#000000',
            'adidas-white': '#ffffff',
            'adidas-gray': '#767677',
            'adidas-light-gray': '#f4f4f4',
            'adidas-blue': '#1e3a8a',
            'adidas-dark-gray': '#2d2d2d',
            'adidas-silver': '#c4c4c4',
            'adidas-red': '#d71921',
            'adidas-green': '#69be28',
          },
          fontFamily: {
            'adidas': ['AdihausDIN', 'Arial', 'Helvetica', 'sans-serif'],
          },
          animation: {
            'slide-in': 'slideIn 0.25s ease-out',
            'fade-in': 'fadeIn 0.15s ease-in',
            'bounce-soft': 'bounceSoft 1s infinite',
          }
        }
      }
    }
  </script>
  
  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
  
  <!-- Custom CSS -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');
    
    * {
      font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .adidas-hover:hover {
      transform: translateY(-0.25px);
      transition: transform 0.04s ease;
    }
    
    .adidas-btn {
      transition: transform 0.025s ease;
    }
    
    .adidas-btn:hover {
      transform: scale(1.005);
    }
    
    .book-card {
      transition: transform 0.025s ease, box-shadow 0.025s ease;
      box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    
    .book-card:hover {
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transform: translateY(-0.25px);
    }
    
    .filter-section {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      backdrop-filter: blur(10px);
    }

    .scale-102 {
      transform: scale(1.02);
    }
    
    .line-clamp-2 {
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
    }
    
    /* Adidas-style loading animation */
    @keyframes adidasPulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
    
    @keyframes slideIn {
      from { transform: translateX(-100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    @keyframes bounceSoft {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-2px); }
      60% { transform: translateY(-1px); }
    }
    
    @keyframes giftPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    .adidas-loading {
      animation: adidasPulse 1s infinite;
    }
    
    .combo-badge {
      background: linear-gradient(135deg, #7c3aed, #5b21b6);
      box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
      transition: all 0.3s ease;
    }
    
    .combo-badge:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
    }
    
    .gift-badge {
      background: linear-gradient(135deg, #ec4899, #be185d);
      box-shadow: 0 2px 8px rgba(236, 72, 153, 0.3);
      animation: giftPulse 2s infinite;
      transition: all 0.3s ease;
    }
    
    .gift-badge:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(236, 72, 153, 0.4);
      animation: none;
    }
  </style>
</head>
<body class="bg-adidas-white font-adidas antialiased">
    {{-- Thay thế navbar cũ bằng navbar layout chung --}}
    @include('layouts.partials.navbar')
    
    <!-- Enhanced Adidas-Style Hero Section -->
    <section class="bg-adidas-black text-adidas-white py-20 relative overflow-hidden adidas-stripes">
      <!-- Animated Background -->
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-adidas-black via-adidas-dark-gray to-adidas-black opacity-90"></div>
        <div class="absolute top-10 left-10 w-20 h-20 bg-adidas-white opacity-5 rounded-full floating"></div>
        <div class="absolute bottom-20 right-20 w-16 h-16 bg-adidas-white opacity-5 rounded-full floating" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/4 w-12 h-12 bg-adidas-white opacity-5 rounded-full floating" style="animation-delay: 2s;"></div>
      </div>
      
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center animate-fade-in">
          <div class="mb-6">
            <h1 class="text-6xl md:text-8xl lg:text-9xl font-black tracking-tighter mb-4 transform hover:scale-105 transition-transform duration-250">
              <span class="inline-block animate-slide-in">BOOK</span><span class="text-adidas-white inline-block animate-slide-in" style="animation-delay: 0.1s;">BEE</span>
            </h1>
            <div class="h-1 w-24 bg-adidas-white mx-auto mb-6 animate-slide-in" style="animation-delay: 0.2s;"></div>
          </div>
          
          <p class="text-xl md:text-3xl lg:text-4xl font-light text-adidas-gray mb-8 animate-slide-in tracking-widest" style="animation-delay: 0.3s;">
            IMPOSSIBLE IS NOTHING
          </p>
          
          <p class="text-lg md:text-xl text-adidas-silver mb-10 max-w-2xl mx-auto animate-slide-in" style="animation-delay: 0.4s;">
            Khám phá những cuốn sách hay nhất thế giới. Bộ sưu tập cao cấp dành cho độc giả đam mê.
          </p>
          
          <!-- Call to Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12 animate-slide-in" style="animation-delay: 0.5s;">
            <button class="adidas-btn ripple bg-adidas-white text-adidas-black px-8 py-4 rounded-lg font-bold uppercase tracking-wide hover:bg-adidas-gray hover:text-adidas-white transition-all duration-150 neon-glow">
              Mua ngay
            </button>
            <button class="adidas-btn ripple border-2 border-adidas-white text-adidas-white px-8 py-4 rounded-lg font-bold uppercase tracking-wide hover:bg-adidas-white hover:text-adidas-black transition-all duration-150">
              Khám phá danh mục
            </button>
          </div>
          
          <!-- Breadcrumb -->
          <div class="flex justify-center items-center space-x-3 text-sm uppercase tracking-wider animate-slide-in" style="animation-delay: 0.6s;">
            <a href="/" class="text-adidas-gray hover:text-adidas-white transition-colors duration-100 hover:underline">Trang chủ</a>
            <svg class="w-4 h-4 text-adidas-gray">
              <use xlink:href="#alt-arrow-right-outline"></use>
            </svg>
            <span class="text-adidas-white font-semibold">Cửa hàng</span>
          </div>
        </div>
      </div>
      
      <!-- Enhanced Three Stripes Design Element -->
      <div class="absolute top-0 right-0 w-40 h-full opacity-10 flex space-x-3">
        <div class="w-8 h-full bg-gradient-to-b from-adidas-white to-transparent transform skew-x-12 animate-slide-in" style="animation-delay: 0.7s;"></div>
        <div class="w-8 h-full bg-gradient-to-b from-adidas-white to-transparent transform skew-x-12 animate-slide-in" style="animation-delay: 0.8s;"></div>
        <div class="w-8 h-full bg-gradient-to-b from-adidas-white to-transparent transform skew-x-12 animate-slide-in" style="animation-delay: 0.9s;"></div>
      </div>
      
      <!-- Adidas-style geometric patterns -->
      <div class="absolute bottom-0 left-0 w-full h-2 bg-gradient-to-r from-adidas-black via-adidas-white to-adidas-black opacity-20"></div>
    </section>
 
    <!-- Adidas-Style Stats Section -->
    <section class="bg-adidas-light-gray py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div class="text-center group">
            <div class="bg-adidas-white p-6 rounded-lg shadow-sm group-hover:shadow-md transition-shadow duration-75 transform group-hover:-translate-y-1">
              <div class="text-3xl font-black text-adidas-black mb-2">10K+</div>
              <div class="text-sm text-adidas-gray uppercase tracking-wide">Sách có sẵn</div>
            </div>
          </div>
          <div class="text-center group">
            <div class="bg-adidas-white p-6 rounded-lg shadow-sm group-hover:shadow-md transition-shadow duration-75 transform group-hover:-translate-y-1">
              <div class="text-3xl font-black text-adidas-black mb-2">5K+</div>
              <div class="text-sm text-adidas-gray uppercase tracking-wide">Khách hàng hài lòng</div>
            </div>
          </div>
          <div class="text-center group">
            <div class="bg-adidas-white p-6 rounded-lg shadow-sm group-hover:shadow-md transition-shadow duration-75 transform group-hover:-translate-y-1">
              <div class="text-3xl font-black text-adidas-black mb-2">500+</div>
              <div class="text-sm text-adidas-gray uppercase tracking-wide">Tác giả</div>
            </div>
          </div>
          <div class="text-center group">
            <div class="bg-adidas-white p-6 rounded-lg shadow-sm group-hover:shadow-md transition-shadow duration-75 transform group-hover:-translate-y-1">
              <div class="text-3xl font-black text-adidas-black mb-2">24/7</div>
              <div class="text-sm text-adidas-gray uppercase tracking-wide">Hỗ trợ</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Enhanced Adidas-Style Main Container -->
    <div class="bg-gradient-to-b from-adidas-light-gray to-adidas-white min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Enhanced Adidas-Style Product Listing -->
            <main class="flex-1 lg:order-2">
              <!-- Header Controls -->
              <div class="glass bg-adidas-white rounded-2xl shadow-lg p-8 mb-8 backdrop-blur-lg">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                  <div class="flex items-center space-x-6">
                    <h2 class="text-3xl font-black text-adidas-black tracking-tight">
                      BỘ SƯU TẬP <span class="adidas-gradient-text">SÁCH</span>
                    </h2>
                    <div class="h-8 w-px bg-gradient-to-b from-adidas-black to-adidas-gray"></div>
                    <div class="bg-adidas-black text-adidas-white px-4 py-2 rounded-full text-sm font-bold">
                      {{ $books->total() }} ITEMS
                    </div>
                  </div>
                  <div class="flex items-center space-x-6">
                    <span class="text-sm font-bold text-adidas-gray uppercase tracking-wider">Sắp xếp theo:</span>
                    <select onchange="location = this.value;" 
                            class="bg-adidas-white border-2 border-adidas-light-gray rounded-xl px-6 py-3 text-adidas-black font-semibold focus:border-adidas-black focus:outline-none transition-all duration-100 shadow-sm">
                      <option value="">Nổi bật</option>
                      <option value="name_asc">Tên A-Z</option>
                      <option value="name_desc">Tên Z-A</option>
                      <option value="price_asc">Giá thấp đến cao</option>
                      <option value="price_desc">Giá cao đến thấp</option>
                      <option value="rating_desc">Đánh giá cao nhất</option>
                      <option value="rating_asc">Đánh giá thấp nhất</option>
                    </select>
                  </div>
                </div>
                
                <!-- Results info -->
                <div class="mt-4 flex items-center justify-between text-sm text-adidas-gray">
                  <span>Hiển thị {{ $books->firstItem() }}–{{ $books->lastItem() }} của {{ $books->total() }} kết quả</span>
                  <div class="flex items-center space-x-2">
                    <span>Xem:</span>
                    <button class="p-2 bg-adidas-black text-adidas-white rounded-lg">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                      </svg>
                    </button>
                    <button class="p-2 bg-adidas-light-gray text-adidas-gray rounded-lg hover:bg-adidas-gray hover:text-adidas-white transition-colors">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Enhanced Adidas-Style Product Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($books as $book)
                <div class="book-card bg-adidas-white rounded-2xl overflow-hidden group cursor-pointer shadow-lg">
                  <div class="relative overflow-hidden">
                    <!-- Combo Badge -->
                    @if(!empty($book->has_combo) && $book->has_combo > 0)
                    <div class="absolute top-4 left-4 z-30" title="Combo: {{ $book->combo_names ?? 'Sách có combo khuyến mãi' }}">
                      <div class="flex items-center space-x-1 bg-gradient-to-r from-purple-600 to-purple-800 text-white px-3 py-1 rounded-full shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        <span class="text-xs font-black uppercase tracking-wide">COMBO</span>
                      </div>
                    </div>
                    @endif

                    <!-- Gift Badge -->
                    @if(!empty($book->has_gift) && $book->has_gift > 0)
                    <div class="absolute top-4 {{ !empty($book->has_combo) && $book->has_combo > 0 ? 'left-20' : 'left-4' }} z-30" title="Quà tặng: {{ $book->gift_names ?? 'Sách có quà tặng kèm theo' }}">
                      <div class="flex items-center space-x-1 bg-gradient-to-r from-pink-500 to-pink-700 text-white px-3 py-1 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 animate-pulse">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.236 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.764A3.001 3.001 0 015 5zm2.764 0a1 1 0 012 0H7.764zM11 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-xs font-black uppercase tracking-wide">QUÀ TẶNG</span>
                      </div>
                    </div>
                    @endif
                    
                    <!-- Discount Badge -->
                    @if(!empty($book->discount))
                    <div class="absolute top-4 right-4 z-20">
                      <span class="bg-adidas-red text-adidas-white px-3 py-1 text-xs font-black uppercase rounded-full shadow-lg animate-bounce-soft">
                        -{{ $book->discount }}%
                      </span>
                    </div>
                    @endif

                    <!-- Book Cover -->
                    <div class="aspect-[3/4] overflow-hidden bg-gradient-to-br from-adidas-light-gray to-adidas-silver relative">
                      @php
                        $imagePath = public_path('images/' . $book->cover_image);
                      @endphp
                      <img src="{{ file_exists($imagePath) ? asset('images/' . $book->cover_image) : asset('images/product-item1.png') }}" 
                           alt="{{ $book->title }}"
                           class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-75">
                    </div>

                    <!-- Hover Actions -->
                    <div class="absolute inset-0 bg-adidas-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-75 flex items-center justify-center opacity-0 group-hover:opacity-100">
                      <div class="flex space-x-3 transform translate-y-1 group-hover:translate-y-0 transition-transform duration-75">
                        <a href="{{ route('books.show', $book->slug) }}" 
                           class="bg-adidas-white text-adidas-black p-3 rounded-full hover:bg-adidas-black hover:text-adidas-white transition-all duration-50 shadow-lg">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5L21 21M7 13l-2.5 5"></path>
                          </svg>
                        </a>
                        <button class="btn-wishlist bg-adidas-white text-adidas-black p-3 rounded-full hover:bg-adidas-red hover:text-adidas-white transition-all duration-50 shadow-lg" 
                                data-book-id="{{ $book->id }}">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                          </svg>
                        </button>
                        <button class="bg-adidas-white text-adidas-black p-3 rounded-full hover:bg-adidas-blue hover:text-adidas-white transition-all duration-50 shadow-lg">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Product Info -->
                  <div class="p-6">
                    <!-- Category Tag -->
                    <div class="mb-3">
                      <span class="inline-block bg-adidas-light-gray text-adidas-black px-3 py-1 text-xs font-bold uppercase rounded-full">
                        {{ $book->category_name ?? 'Chưa phân loại' }}
                      </span>
                    </div>
                    
                    <!-- Book Title -->
                    <h3 class="font-black text-xl text-adidas-black mb-3 group-hover:text-adidas-blue transition-colors duration-75 leading-tight">
                      <a href="{{ route('books.show', $book->slug) }}" class="line-clamp-2 hover:underline">
                        {{ $book->title }}
                      </a>
                    </h3>

                    <!-- Author -->
                    <p class="text-adidas-gray text-sm font-semibold uppercase tracking-wider mb-2">
                      TÁC GIẢ: {{ strtoupper($book->author_name ?? 'Chưa rõ tác giả') }}
                    </p>
                    
                    <!-- Publisher/Brand -->
                    <p class="text-adidas-gray text-xs font-medium mb-2">
                      <i class="fas fa-building me-1"></i>
                      NXB: {{ $book->brand_name ?? 'Chưa rõ' }}
                    </p>
                    
                    <!-- Category -->
                    <p class="text-adidas-gray text-xs font-medium mb-4">
                      <i class="fas fa-folder me-1"></i>
                      Danh mục: {{ $book->category_name ?? 'Chưa phân loại' }}
                    </p>

                    <!-- Combo and Gift Info -->
                    @if((!empty($book->has_combo) && $book->has_combo > 0) || (!empty($book->has_gift) && $book->has_gift > 0))
                    <div class="space-y-2 mb-4">
                      @if(!empty($book->has_combo) && $book->has_combo > 0)
                      <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <span class="text-xs text-purple-600 font-medium">
                          Combo: {{ $book->combo_names ? (strlen($book->combo_names) > 30 ? substr($book->combo_names, 0, 30) . '...' : $book->combo_names) : 'Có sẵn' }}
                        </span>
                      </div>
                      @endif
                      
                      @if(!empty($book->has_gift) && $book->has_gift > 0)
                      <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-pink-500 rounded-full"></div>
                        <span class="text-xs text-pink-600 font-medium">
                          Quà tặng: {{ $book->gift_names ? (strlen($book->gift_names) > 30 ? substr($book->gift_names, 0, 30) . '...' : $book->gift_names) : 'Có sẵn' }}
                        </span>
                      </div>
                      @endif
                    </div>
                    @endif

                    <!-- Rating -->
                    <div class="flex items-center justify-between mb-6">
                      <div class="flex items-center space-x-2">
                        <div class="flex space-x-1">
                          @php
                            $ratingRounded = round($book->avg_rating ?? 0);
                          @endphp
                          @for ($i = 1; $i <= 5; $i++)
                          <svg class="w-5 h-5 {{ $i <= $ratingRounded ? 'text-yellow-400' : 'text-adidas-light-gray' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                          </svg>
                          @endfor
                        </div>
                        <span class="text-sm font-bold text-adidas-black">({{ number_format($book->avg_rating ?? 0, 1) }})</span>
                      </div>
                      <div class="flex items-center space-x-2">
                        @php
                          $physicalStock = $book->physical_stock ?? 0;
                          $hasEbook = $book->has_ebook ?? 0;
                          $bookStatus = $book->status ?? 'Không rõ';
                        @endphp
                        
                        @switch($bookStatus)
                          @case('Còn Hàng')
                            <span class="w-2 h-2 bg-adidas-green rounded-full"></span>
                            <span class="text-xs text-adidas-green font-medium">Còn hàng</span>
                            @break
                          @case('Hết Hàng Tồn Kho')
                            <span class="w-2 h-2 bg-adidas-red rounded-full"></span>
                            <span class="text-xs text-adidas-red font-medium">Hết hàng</span>
                            @break
                          @case('Sắp Ra Mắt')
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            <span class="text-xs text-yellow-600 font-medium">Sắp ra mắt</span>
                            @break
                          @case('Ngừng Kinh Doanh')
                            <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                            <span class="text-xs text-gray-600 font-medium">Ngừng kinh doanh</span>
                            @break
                          @default
                            @if($physicalStock > 0)
                              {{-- Fallback dựa trên stock nếu status không rõ --}}
                              <span class="w-2 h-2 bg-adidas-green rounded-full"></span>
                              <span class="text-xs text-adidas-gray font-medium">Còn {{ $physicalStock }} cuốn</span>
                            @elseif($hasEbook)
                              {{-- Hết sách vật lý nhưng còn ebook --}}
                              <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                              <span class="text-xs text-blue-600 font-medium">Ebook có sẵn</span>
                            @else
                              {{-- Hết hàng hoàn toàn --}}
                              <span class="w-2 h-2 bg-adidas-red rounded-full"></span>
                              <span class="text-xs text-adidas-red font-medium">Hết hàng</span>
                            @endif
                        @endswitch
                      </div>
                    </div>

                    <!-- Price -->
                    <div class="text-center">
                      <span class="text-2xl font-black text-adidas-black">
                        {{ number_format($book->min_price ?? 0, 0, ',', '.') }}₫
                      </span>
                      @if(!empty($book->discount))
                      <span class="text-sm text-adidas-gray line-through ml-2">
                        {{ number_format(($book->min_price ?? 0) * 1.2, 0, ',', '.') }}₫
                      </span>
                      @endif
                    </div>
                  </div>
                </div>
                @endforeach
              </div>

              <!-- Enhanced Adidas-Style Pagination -->
              <nav class="mt-16 flex justify-center">
                <div class="bg-adidas-white rounded-2xl shadow-lg p-6">
                  <div class="flex items-center space-x-3">
                    <!-- Prev Button -->
                    @if ($books->onFirstPage())
                      <span class="px-6 py-3 text-adidas-gray cursor-not-allowed bg-adidas-light-gray rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                      </span>
                    @else
                      <a href="{{ $books->previousPageUrl() }}" 
                         class="adidas-btn px-6 py-3 text-adidas-black hover:bg-adidas-black hover:text-adidas-white rounded-xl transition-all duration-100 font-semibold shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                      </a>
                    @endif

                    <!-- Page Numbers -->
                    @foreach ($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                      @if ($page == $books->currentPage())
                        <span class="px-6 py-3 bg-gradient-to-r from-adidas-black to-adidas-dark-gray text-adidas-white rounded-xl font-black shadow-lg">
                          {{ $page }}
                        </span>
                      @else
                        <a href="{{ $url }}" 
                           class="adidas-btn px-6 py-3 text-adidas-black hover:bg-adidas-black hover:text-adidas-white rounded-xl transition-all duration-100 font-semibold shadow-sm">
                          {{ $page }}
                        </a>
                      @endif
                    @endforeach

                    <!-- Next Button -->
                    @if ($books->hasMorePages())
                      <a href="{{ $books->nextPageUrl() }}" 
                         class="adidas-btn px-6 py-3 text-adidas-black hover:bg-adidas-black hover:text-adidas-white rounded-xl transition-all duration-100 font-semibold shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </a>
                    @else
                      <span class="px-6 py-3 text-adidas-gray cursor-not-allowed bg-adidas-light-gray rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </span>
                    @endif
                  </div>
                  
                  <!-- Page info -->
                  <div class="text-center mt-4 text-sm text-adidas-gray">
                    Trang {{ $books->currentPage() }} / {{ $books->lastPage() }}
                  </div>
                </div>
              </nav>
            </main>

          <!-- Adidas-Style Sidebar Filters -->
          <aside class="w-full lg:w-80 lg:order-1">
            <div class="filter-section bg-adidas-white rounded-lg shadow-sm p-6 sticky top-8">
              
              <!-- Search Section -->
              <div class="mb-8">
                <h3 class="text-lg font-bold text-adidas-black mb-4 uppercase tracking-wide border-b-2 border-adidas-light-gray pb-2">
                  Tìm kiếm sách
                </h3>
                <form method="GET" action="{{ url()->current() }}" role="search" class="relative">
                  <input 
                    name="search" 
                    type="search" 
                    placeholder="Tìm kiếm sách, tác giả, NXB, danh mục..." 
                    aria-label="Search"
                    value="{{ request('search') ?? '' }}"                          class="w-full px-4 py-3 pr-12 border-2 border-adidas-light-gray rounded-lg focus:border-adidas-black focus:outline-none transition-colors duration-100">
                      <button type="submit" 
                              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-adidas-gray hover:text-adidas-black transition-colors duration-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                      </button>
                </form>
              </div>

              <!-- Categories Filter -->
              <div class="mb-8">
                <h3 class="text-lg font-bold text-adidas-black mb-4 uppercase tracking-wide border-b-2 border-adidas-light-gray pb-2">
                  Danh mục
                </h3>
                <select
                  aria-label="Chọn danh mục"
                  onchange="location = this.value;"
                  class="w-full px-4 py-3 border-2 border-adidas-light-gray rounded-lg focus:border-adidas-black focus:outline-none transition-colors duration-100 bg-adidas-white">
                  <option value="{{ url('/books') . '?' . http_build_query(request()->except('category')) }}"
                    {{ request()->segment(2) === null ? 'selected' : '' }}>
                    Tất cả danh mục
                  </option>
                  @foreach($categories as $cat)
                    <option value="{{ url('/books/' . $cat->slug) . '?' . http_build_query(request()->except('authors', 'brands')) }}"
                      {{ request()->segment(2) == $cat->slug ? 'selected' : '' }}>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Authors Filter -->
              <div class="mb-8">
                <h3 class="text-lg font-bold text-adidas-black mb-4 uppercase tracking-wide border-b-2 border-adidas-light-gray pb-2">
                  Tác giả
                </h3>
                <select
                  aria-label="Chọn tác giả"
                  onchange="location = this.value;"
                  class="w-full px-4 py-3 border-2 border-adidas-light-gray rounded-lg focus:border-adidas-black focus:outline-none transition-colors duration-100 bg-adidas-white">
                  <option value="{{ url()->current() . '?' . http_build_query(request()->except('authors')) }}">
                    Tất cả tác giả
                  </option>
                  @foreach ($authors as $author)
                    <option value="{{ url()->current() }}?authors={{ $author->id }}"
                      {{ in_array($author->id, (array) request('authors', [])) ? 'selected' : '' }}>
                      {{ $author->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Publishers Filter -->
              <div class="mb-8">
                <h3 class="text-lg font-bold text-adidas-black mb-4 uppercase tracking-wide border-b-2 border-adidas-light-gray pb-2">
                  Nhà xuất bản
                </h3>
                <select
                  aria-label="Chọn nhà xuất bản"
                  onchange="location = this.value;"
                  class="w-full px-4 py-3 border-2 border-adidas-light-gray rounded-lg focus:border-adidas-black focus:outline-none transition-colors duration-100 bg-adidas-white">
                  <option value="{{ url()->current() . '?' . http_build_query(request()->except('brands')) }}">
                    Tất cả nhà xuất bản
                  </option>
                  @foreach ($brands as $brand)
                    <option value="{{ url()->current() }}?brands={{ $brand->id }}"
                      {{ in_array($brand->id, (array) request('brands', [])) ? 'selected' : '' }}>
                      {{ $brand->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Price Filter -->
              <div class="mb-8">
                <h3 class="text-lg font-bold text-adidas-black mb-4 uppercase tracking-wide border-b-2 border-adidas-light-gray pb-2">
                  Khoảng giá
                </h3>
                <form method="GET" action="{{ url()->current() }}">
                  <div class="space-y-3">
                    <label class="flex items-center space-x-3 cursor-pointer group">
                      <input type="radio" name="price_range" value="1-10" 
                             {{ request('price_range') == '1-10' ? 'checked' : '' }}
                             class="w-4 h-4 text-adidas-black focus:ring-adidas-black">
                      <span class="text-adidas-gray group-hover:text-adidas-black transition-colors duration-100">0 - 10,000 ₫</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer group">
                      <input type="radio" name="price_range" value="10-50" 
                             {{ request('price_range') == '10-50' ? 'checked' : '' }}
                             class="w-4 h-4 text-adidas-black focus:ring-adidas-black">
                      <span class="text-adidas-gray group-hover:text-adidas-black transition-colors duration-100">10,000 - 50,000 ₫</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer group">
                      <input type="radio" name="price_range" value="50-100" 
                             {{ request('price_range') == '50-100' ? 'checked' : '' }}
                             class="w-4 h-4 text-adidas-black focus:ring-adidas-black">
                      <span class="text-adidas-gray group-hover:text-adidas-black transition-colors duration-100">50,000 - 100,000 ₫</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer group">
                      <input type="radio" name="price_range" value="100+" 
                             {{ request('price_range') == '100+' ? 'checked' : '' }}
                             class="w-4 h-4 text-adidas-black focus:ring-adidas-black">
                      <span class="text-adidas-gray group-hover:text-adidas-black transition-colors duration-100">Trên 100,000 ₫</span>
                    </label>
                  </div>
                  <button type="submit" 
                          class="adidas-btn w-full mt-4 bg-adidas-black text-adidas-white py-3 rounded-lg font-semibold uppercase tracking-wide hover:bg-adidas-blue transition-colors duration-100">
                    Áp dụng bộ lọc
                  </button>
                </form>
              </div>

              <!-- Reset Filter -->
              <div class="pt-6 border-t border-adidas-light-gray">
                <a href="{{ url('/books') }}" 
                   class="adidas-btn w-full block text-center bg-adidas-light-gray text-adidas-black py-3 rounded-lg font-semibold uppercase tracking-wide hover:bg-adidas-gray hover:text-adidas-white transition-colors duration-100">
                  Xóa tất cả bộ lọc
                </a>
              </div>

            </div>
          </aside>

        </div>
      </div>
    </div>

    <!-- Enhanced JavaScript for Premium Interactions -->
    <script>
      // Wishlist functionality with visual feedback
      document.querySelectorAll('.btn-wishlist').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();

          if (this.disabled) return;

          const button = this;
          const bookId = button.dataset.bookId;
          const originalHTML = button.innerHTML;

          // Visual feedback
          button.disabled = true;
          button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path fill="currentColor" d="m4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

          fetch('/wishlist/add', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ book_id: bookId })
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              button.classList.add('bg-adidas-red', 'text-adidas-white');
              button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>';
              showToast('Đã thêm vào danh sách yêu thích!', 'success');
            } else {
              button.innerHTML = originalHTML;
              button.disabled = false;
              showToast(data.message || 'Lỗi khi thêm vào danh sách yêu thích!', 'error');
            }
          })
          .catch(() => {
            button.innerHTML = originalHTML;
            button.disabled = false;
            showToast('Lỗi kết nối!', 'error');
          });
        });
      });

      // Simple toast notification system
      function showToast(message, type = 'info') {
        const toast = document.createElement('div');                        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transform transition-all duration-75 translate-x-full ${
          type === 'success' ? 'bg-adidas-green text-white' : 
          type === 'error' ? 'bg-adidas-red text-white' : 
          'bg-adidas-black text-white'
        }`;
        toast.innerHTML = `<span class="font-semibold">${message}</span>`;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.style.transform = 'translateX(0)', 50);
        setTimeout(() => {
          toast.style.transform = 'translateX(100%)';
          setTimeout(() => document.body.removeChild(toast), 75);
        }, 2000);
      }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  </body>
</html>