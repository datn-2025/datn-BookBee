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
      theme: {      extend: {
        colors: {
          'custom-black': '#000000',
          'custom-white': '#ffffff',
          'custom-gray': '#6b7280',
          'custom-light-gray': '#f9fafb',
          'custom-blue': '#1e40af',
          'custom-dark-gray': '#374151',
          'custom-silver': '#d1d5db',
          'custom-red': '#dc2626',
          'custom-green': '#16a34a',
        },
        fontFamily: {
          'clean': ['Inter', 'Arial', 'Helvetica', 'sans-serif'],
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
    
    .clean-hover:hover {
      transform: translateY(-0.25px);
      transition: transform 0.04s ease;
    }
    
    .clean-btn {
      transition: transform 0.025s ease;
    }
    
    .clean-btn:hover {
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
    
    .line-clamp-1 {
      overflow: hidden;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 1;
    }
    
    /* Clean loading animation */
    @keyframes cleanPulse {
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
    
    .clean-loading {
      animation: cleanPulse 1s infinite;
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
<body class="bg-white font-sans antialiased">
    {{-- Thay thế navbar cũ bằng navbar layout chung --}}
    @include('layouts.partials.navbar')
    
    <!-- Enhanced Hero Section theo phong cách trang chủ -->
    <section class="w-full bg-white py-32 md:py-40 relative overflow-hidden">
        <!-- Background Elements - Minimal style -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 rounded-none transform rotate-45 translate-x-36 -translate-y-36"></div>
            <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
            <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 items-center px-6 md:px-10 gap-10 max-w-screen-xl mx-auto">
            {{-- Left text - Typography Style --}}
            <div class="space-y-8 text-gray-900">
                <!-- Pre-title -->
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">
                        BOOKBEE COLLECTION
                    </span>
                </div>

                <!-- Main headline -->
                <h1 class="text-5xl md:text-7xl font-black uppercase leading-[0.9] tracking-tight text-black">
                    <span class="block">BOOK</span>
                    <span class="block text-gray-400">STORE</span>
                    <span class="block">PREMIUM</span>
                </h1>

                <!-- Subtitle -->
                <div class="space-y-4">
                    <p class="text-xl md:text-2xl font-medium text-gray-700 max-w-lg">
                        Khám phá những cuốn sách hay nhất thế giới. Bộ sưu tập cao cấp dành cho độc giả đam mê.
                    </p>

                    <!-- Stats highlight -->
                    <div class="flex items-center gap-4">
                        <span class="bg-red-600 text-white px-4 py-2 text-sm font-bold uppercase tracking-wide">
                            {{ $books->total() }}+ SÁCH
                        </span>
                        <span class="text-lg font-bold text-black">Đa dạng thể loại</span>
                    </div>
                </div>

                <!-- Breadcrumb -->
                <div class="flex items-center gap-3 text-sm uppercase tracking-wider pt-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-black transition-colors duration-300 hover:underline font-medium">Trang chủ</a>
                    <div class="w-4 h-0.5 bg-gray-400"></div>
                    <span class="text-black font-bold">Cửa hàng</span>
                </div>
            </div>

            {{-- Right image --}}
            <div class="flex justify-center">
                <div class="relative group">
                    <div class="relative">
                        <img src="{{ asset('storage/images/banner-image2.png') }}"
                            class="h-80 md:h-96 object-contain transform group-hover:scale-105 transition-transform duration-700"
                            alt="BookBee Store">

                        <!-- Badge -->
                        <div class="absolute -top-6 -left-6 bg-black text-white px-6 py-3 transform group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform duration-500">
                            <div class="text-center">
                                <div class="text-sm font-bold uppercase tracking-wide">NEW</div>
                                <div class="text-xs uppercase tracking-wider text-gray-300">ARRIVALS</div>
                            </div>
                        </div>

                        <!-- Accent -->
                        <div class="absolute -bottom-4 -right-4 bg-white border-2 border-black px-4 py-2 transform group-hover:translate-x-1 group-hover:translate-y-1 transition-transform duration-500">
                            <span class="text-xs font-bold uppercase tracking-wide text-black">Premium</span>
                        </div>
                    </div>

                    <!-- Background shape -->
                    <div class="absolute inset-0 -z-10 bg-gray-100 transform translate-x-4 translate-y-4 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-700"></div>
                </div>
            </div>
        </div>
    </section>
 
    <!-- Stats Section theo phong cách trang chủ -->
    <section class="bg-white py-20 md:py-24 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-64 h-1 bg-black opacity-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-black opacity-5 transform rotate-45"></div>
            <div class="absolute top-1/2 right-10 w-0.5 h-24 bg-black opacity-30"></div>
        </div>

        <div class="relative z-10 max-w-screen-xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-red-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-red-100 group-hover:scale-110 transition-all duration-500"></div>
                    <div class="p-8 text-center relative z-10">
                        <div class="text-3xl font-black text-black mb-2 group-hover:text-red-600 transition-colors duration-300">{{ $books->total() }}+</div>
                        <div class="text-sm text-gray-600 uppercase tracking-wide font-medium group-hover:text-gray-800 transition-colors duration-300">Sách có sẵn</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-red-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>
                
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-green-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-green-100 group-hover:scale-110 transition-all duration-500"></div>
                    <div class="p-8 text-center relative z-10">
                        <div class="text-3xl font-black text-black mb-2 group-hover:text-green-600 transition-colors duration-300">{{ $categories->count() }}+</div>
                        <div class="text-sm text-gray-600 uppercase tracking-wide font-medium group-hover:text-gray-800 transition-colors duration-300">Thể loại</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-green-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>
                
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-blue-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-blue-100 group-hover:scale-110 transition-all duration-500"></div>
                    <div class="p-8 text-center relative z-10">
                        <div class="text-3xl font-black text-black mb-2 group-hover:text-blue-600 transition-colors duration-300">{{ $authors->count() }}+</div>
                        <div class="text-sm text-gray-600 uppercase tracking-wide font-medium group-hover:text-gray-800 transition-colors duration-300">Tác giả</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-blue-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>
                
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-yellow-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-yellow-100 group-hover:scale-110 transition-all duration-500"></div>
                    <div class="p-8 text-center relative z-10">
                        <div class="text-3xl font-black text-black mb-2 group-hover:text-yellow-600 transition-colors duration-300">24/7</div>
                        <div class="text-sm text-gray-600 uppercase tracking-wide font-medium group-hover:text-gray-800 transition-colors duration-300">Hỗ trợ</div>
                        <div class="absolute bottom-0 left-0 h-1 bg-yellow-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Container theo phong cách trang chủ -->
    <div class="bg-white min-h-screen">
      <div class="max-w-screen-xl mx-auto px-6 py-20">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Product Listing -->
            <main class="flex-1 lg:order-2">
              <!-- Header Controls theo phong cách clean -->
              <div class="bg-white border border-gray-100 shadow-lg p-8 mb-8 relative overflow-hidden">
                <!-- Background accent -->
                <div class="absolute top-0 right-0 w-24 h-24 bg-gray-50 transform rotate-45 translate-x-12 -translate-y-12"></div>
                
                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                  <div class="flex items-center space-x-6">
                    <!-- Title với pre-title -->
                    <div>
                      <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-0.5 bg-black"></div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">COLLECTION</span>
                      </div>
                      <h2 class="text-3xl font-black text-black tracking-tight uppercase">
                        <span class="text-gray-400">BỘ SƯU TẬP</span> SÁCH
                      </h2>
                    </div>
                    
                    <div class="h-8 w-px bg-gray-300"></div>
                    
                    <div class="bg-black text-white px-4 py-2 text-sm font-bold uppercase tracking-wide">
                      {{ $books->total() }} ITEMS
                    </div>
                  </div>
                </div>
                
                <!-- Results info -->
                <div class="mt-6 flex items-center justify-between text-sm text-gray-600 border-t border-gray-100 pt-4">
                  <span class="font-medium">Hiển thị {{ $books->firstItem() }}–{{ $books->lastItem() }} của {{ $books->total() }} kết quả</span>
                  <div class="flex items-center space-x-3">
                    <span class="font-medium">Chế độ xem:</span>
                    <div class="flex border border-gray-200">
                      <button id="grid-view-btn" class="p-2 bg-black text-white transition-colors hover:bg-gray-800">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                        </svg>
                      </button>
                      <button id="list-view-btn" class="p-2 bg-white text-gray-600 border-l border-gray-200 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
 xua
              <!-- Active Filters Display -->
              @if(request('search') || request('authors') || request('brands') || request('min_price') || request('max_price'))
              <div class="bg-gray-50 border border-gray-200 p-6 mb-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-gray-100 transform rotate-45 translate-x-8 -translate-y-8"></div>
                
                <div class="relative z-10">
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-4 h-0.5 bg-gray-600"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">ACTIVE FILTERS</span>
                  </div>
                  
                  <div class="flex flex-wrap gap-3">
                    @if(request('search'))
                      <span class="inline-flex items-center gap-2 bg-black text-white px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Tìm kiếm: "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="hover:text-red-300 transition-colors">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </a>
                      </span>
                    @endif
                    
                    @if(request('authors'))
                      @php
                        $selectedAuthor = $authors->firstWhere('id', request('authors'));
                      @endphp
                      @if($selectedAuthor)
                        <span class="inline-flex items-center gap-2 bg-blue-600 text-white px-3 py-2 text-sm font-medium">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                          </svg>
                          Tác giả: {{ $selectedAuthor->name }}
                          <a href="{{ request()->fullUrlWithQuery(['authors' => null]) }}" class="hover:text-red-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                          </a>
                        </span>
                      @endif
                    @endif
                    
                    @if(request('brands'))
                      @php
                        $selectedBrand = $brands->firstWhere('id', request('brands'));
                      @endphp
                      @if($selectedBrand)
                        <span class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-2 text-sm font-medium">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                          </svg>
                          NXB: {{ $selectedBrand->name }}
                          <a href="{{ request()->fullUrlWithQuery(['brands' => null]) }}" class="hover:text-red-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                          </a>
                        </span>
                      @endif
                    @endif
                    
                    @if(request('min_price') || request('max_price'))
                      <span class="inline-flex items-center gap-2 bg-red-600 text-white px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Giá: 
                        @if(request('min_price') && request('max_price'))
                          {{ number_format(request('min_price')) }}₫ - {{ number_format(request('max_price')) }}₫
                        @elseif(request('min_price'))
                          Từ {{ number_format(request('min_price')) }}₫
                        @elseif(request('max_price'))
                          Đến {{ number_format(request('max_price')) }}₫
                        @endif
                        <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="hover:text-red-300 transition-colors">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </a>
                      </span>
                    @endif
                    
                    <!-- Clear All Filters -->
                    <a href="{{ route('books.index') }}" 
                       class="inline-flex items-center gap-2 bg-gray-600 text-white px-3 py-2 text-sm font-medium hover:bg-gray-700 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                      Xóa tất cả bộ lọc
                    </a>
                  </div>
                </div>
              </div>
              @endif

              <!-- Product Grid theo phong cách trang chủ -->
              <div id="books-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($books as $book)
                <!-- Grid View Card theo phong cách clean -->
                <div class="book-card grid-view group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 overflow-hidden cursor-pointer transform hover:-translate-y-2">
                  <div class="relative overflow-hidden">
                    <!-- Enhanced Gift Badge -->
                    @if(!empty($book->has_gift) && $book->has_gift > 0)
                    <div class="absolute top-3 left-3 z-30" title="Quà tặng: {{ $book->gift_names ?? 'Sách có quà tặng kèm theo' }}">
                      <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-3 py-2 text-xs font-bold uppercase tracking-wide border-2 border-white shadow-lg transform hover:scale-105 transition-all duration-200">
                        <div class="flex items-center space-x-1">
                          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.236 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.764A3.001 3.001 0 015 5zm2.764 0a1 1 0 012 0H7.764zM11 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                          </svg>
                          <span>Quà Tặng </span>
                        </div>
                      </div>
                    </div>
                    @endif
                    
                    <!-- Discount Badge -->
                    @if(!empty($book->discount))
                    <div class="absolute top-3 right-3 z-20">
                      <span class="bg-red-600 text-white px-3 py-1 text-xs font-bold uppercase tracking-wide">
                        -{{ $book->discount }}%
                      </span>
                    </div>
                    @endif

                    <!-- Book Cover -->
                    <div class="aspect-[3/4] overflow-hidden bg-gray-50">
                      <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default.jpg') }}" 
                           alt="{{ $book->title }}"
                           class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>

                    <!-- Quick Actions -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                      <div class="flex space-x-2">
                        <a href="{{ route('books.show', $book->slug) }}" 
                           class="bg-black text-white p-3 hover:bg-gray-800 transition-colors duration-300">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                        </a>
                        <button class="btn-wishlist bg-white text-red-600 border border-gray-200 p-3 hover:bg-red-600 hover:text-white transition-colors duration-300" 
                                data-book-id="{{ $book->id }}">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Product Info -->
                  <div class="p-6">
                    <!-- Category Tag -->
                    <div class="mb-3">
                      <span class="inline-block bg-gray-100 text-black px-3 py-1 text-xs font-bold uppercase tracking-wide">
                        {{ $book->category_name ?? 'Chưa phân loại' }}
                      </span>
                    </div>
                    
                    <!-- Book Title -->
                    <h3 class="font-bold text-lg text-black mb-3 group-hover:text-gray-700 transition-colors duration-300 leading-tight">
                      <a href="{{ route('books.show', $book->slug) }}" class="line-clamp-2 hover:underline">
                        {{ $book->title }}
                      </a>
                    </h3>

                    <!-- Author -->
                    <p class="text-gray-600 text-sm font-medium mb-4 line-clamp-1">
                      {{ $book->author_name ?? 'Chưa rõ tác giả' }}
                    </p>

                    <!-- Gift Info -->
                    @if(!empty($book->has_gift) && $book->has_gift > 0)
                    <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500">
                      <div class="flex items-start space-x-2">
                        <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.236 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.764A3.001 3.001 0 015 5zm2.764 0a1 1 0 012 0H7.764zM11 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-xs text-red-700 font-medium line-clamp-2">
                          {{ $book->gift_names ? (strlen($book->gift_names) > 35 ? substr($book->gift_names, 0, 35) . '...' : $book->gift_names) : 'Có quà tặng kèm theo' }}
                        </span>
                      </div>
                    </div>
                    @endif

                    <!-- Bottom Section -->
                    <div class="space-y-4">
                      <!-- Status -->
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-1">
                          @php
                            // Logic mới: ƯU TIÊN books.status trước, sau đó mới xét stock
                            $physicalStock = (int) ($book->physical_stock ?? 0);
                            $hasEbook = (bool) ($book->has_ebook ?? false);
                            $bookStatus = $book->status ?? 'Không rõ';
                            
                            // ƯU TIÊN 1: Kiểm tra trạng thái chính của sách (books.status)
                            $statusText = $bookStatus;
                            $statusClass = '';
                            $dotColor = '';
                            
                            switch($bookStatus) {
                                case 'Ngừng Kinh Doanh':
                                    $statusText = 'Ngừng KD';
                                    $dotColor = 'bg-gray-500';
                                    $statusClass = 'text-gray-600';
                                    break;
                                case 'Sắp Ra Mắt':
                                    $statusText = 'Sắp ra mắt';
                                    $dotColor = 'bg-yellow-500';
                                    $statusClass = 'text-yellow-600';
                                    break;
                                case 'Hết Hàng Tồn Kho':
                                    $statusText = 'Hết hàng';
                                    $dotColor = 'bg-red-500';
                                    $statusClass = 'text-red-600';
                                    break;
                                case 'Còn Hàng':
                                default:
                                    // ƯU TIÊN 2: Chỉ khi status = 'Còn Hàng' thì mới check stock
                                    if ($hasEbook) {
                                        $statusText = 'Ebook có sẵn';
                                        $dotColor = 'bg-blue-500';
                                        $statusClass = 'text-blue-600';
                                    } elseif ($physicalStock === 0) {
                                        $statusText = 'Hết hàng';
                                        $dotColor = 'bg-red-500';
                                        $statusClass = 'text-red-600';
                                    } elseif ($physicalStock > 0 && $physicalStock < 10) {
                                        $statusText = 'Sắp hết (' . $physicalStock . ')';
                                        $dotColor = 'bg-yellow-500';
                                        $statusClass = 'text-yellow-600';
                                    } else {
                                        $statusText = 'Còn hàng (' . $physicalStock . ')';
                                        $dotColor = 'bg-green-500';
                                        $statusClass = 'text-green-600';
                                    }
                                    break;
                            }
                          @endphp
                          
                          <span class="w-2 h-2 {{ $dotColor }} rounded-full"></span>
                          <span class="text-xs {{ $statusClass }} font-medium">{{ $statusText }}</span>
                        </div>
                      </div>

                      <!-- Price & Add to Cart -->
                      <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                          <span class="text-xl font-black text-black">
                            {{ number_format($book->min_price ?? 0, 0, ',', '.') }}₫
                          </span>
                          @if(!empty($book->discount))
                          <br>
                          <span class="text-sm text-gray-500 line-through">
                            {{ number_format(($book->min_price ?? 0) * 1.2, 0, ',', '.') }}₫
                          </span>
                          @endif
                        </div>
                        <a href="{{ route('books.show', $book->slug) }}" 
                           class="bg-black text-white px-4 py-2 text-sm font-bold uppercase tracking-wide hover:bg-gray-800 transition-colors duration-300">
                          Chi tiết
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- List View Card theo phong cách clean (Hidden by default) -->
                <div class="book-card list-view hidden group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 overflow-hidden cursor-pointer">
                  <div class="flex p-6 space-x-6">
                    <!-- Book Cover -->
                    <div class="relative flex-shrink-0">
                      <!-- Enhanced Gift Badge -->
                      @if(!empty($book->has_gift) && $book->has_gift > 0)
                      <div class="absolute -top-2 -left-2 z-30" title="Quà tặng: {{ $book->gift_names ?? 'Sách có quà tặng kèm theo' }}">
                        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-3 py-2 text-xs font-bold uppercase tracking-wide border-2 border-white shadow-lg transform hover:scale-105 transition-all duration-200">
                          <div class="flex items-center space-x-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.236 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.764A3.001 3.001 0 015 5zm2.764 0a1 1 0 012 0H7.764zM11 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                            <span>GIFT</span>
                          </div>
                        </div>
                      </div>
                      @endif
                      
                      <!-- Discount Badge -->
                      @if(!empty($book->discount))
                      <div class="absolute -top-2 -right-2 z-20">
                        <span class="bg-red-600 text-white px-3 py-1 text-xs font-bold uppercase tracking-wide">
                          -{{ $book->discount }}%
                        </span>
                      </div>
                      @endif

                      <div class="w-32 h-40 overflow-hidden bg-gray-50">
                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default.jpg') }}" 
                             alt="{{ $book->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                      </div>
                    </div>

                    <!-- Book Info -->
                    <div class="flex-1 flex flex-col justify-between">
                      <div>
                        <!-- Category Tag & Title -->
                        <div class="flex items-start justify-between mb-4">
                          <div>
                            <span class="inline-block bg-gray-100 text-black px-3 py-1 text-xs font-bold uppercase tracking-wide mb-3">
                              {{ $book->category_name ?? 'Chưa phân loại' }}
                            </span>
                            <h3 class="font-black text-xl text-black group-hover:text-gray-700 transition-colors duration-300 leading-tight line-clamp-2">
                              <a href="{{ route('books.show', $book->slug) }}" class="hover:underline">
                                {{ $book->title }}
                              </a>
                            </h3>
                          </div>
                          
                          <!-- Actions -->
                          <div class="flex space-x-2">
                            <a href="{{ route('books.show', $book->slug) }}" 
                               class="bg-white border-2 border-gray-200 text-black p-3 hover:bg-black hover:border-black hover:text-white transition-all duration-300">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                              </svg>
                            </a>
                            <button class="btn-wishlist bg-white border-2 border-gray-200 text-black p-3 hover:bg-red-600 hover:border-red-600 hover:text-white transition-all duration-300" 
                                    data-book-id="{{ $book->id }}">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                              </svg>
                            </button>
                          </div>
                        </div>

                        <!-- Book Details -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                          <div>
                            <p class="text-gray-600 text-sm font-medium mb-1">
                              <i class="fas fa-user me-1"></i>Tác giả:
                            </p>
                            <p class="text-black font-medium">{{ $book->author_name ?? 'Chưa rõ' }}</p>
                          </div>
                          <div>
                            <p class="text-gray-600 text-sm font-medium mb-1">
                              <i class="fas fa-building me-1"></i>NXB:
                            </p>
                            <p class="text-black font-medium">{{ $book->brand_name ?? 'Chưa rõ' }}</p>
                          </div>
                        </div>

                        <!-- Gift Info -->
                        @if(!empty($book->has_gift) && $book->has_gift > 0)
                        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500">
                          <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-red-600 font-medium">
                              Quà tặng: {{ $book->gift_names ? (strlen($book->gift_names) > 50 ? substr($book->gift_names, 0, 50) . '...' : $book->gift_names) : 'Có sẵn' }}
                            </span>
                          </div>
                        </div>
                        @endif
                      </div>

                      <!-- Bottom Row: Status, Price -->
                      <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                        <!-- Status -->
                        <div class="flex items-center space-x-2">
                          @php
                            // Logic mới: ƯU TIÊN books.status trước, sau đó mới xét stock
                            $physicalStock = (int) ($book->physical_stock ?? 0);
                            $hasEbook = (bool) ($book->has_ebook ?? false);
                            $bookStatus = $book->status ?? 'Không rõ';
                            
                            // ƯU TIÊN 1: Kiểm tra trạng thái chính của sách (books.status)
                            $statusText = $bookStatus;
                            $statusClass = '';
                            $dotColor = '';
                            
                            switch($bookStatus) {
                                case 'Ngừng Kinh Doanh':
                                    $statusText = 'Ngừng kinh doanh';
                                    $dotColor = 'bg-gray-500';
                                    $statusClass = 'text-gray-600';
                                    break;
                                case 'Sắp Ra Mắt':
                                    $statusText = 'Sắp ra mắt';
                                    $dotColor = 'bg-yellow-500';
                                    $statusClass = 'text-yellow-600';
                                    break;
                                case 'Hết Hàng Tồn Kho':
                                    $statusText = 'Hết hàng';
                                    $dotColor = 'bg-red-500';
                                    $statusClass = 'text-red-600';
                                    break;
                                case 'Còn Hàng':
                                default:
                                    // ƯU TIÊN 2: Chỉ khi status = 'Còn Hàng' thì mới check stock
                                    if ($hasEbook) {
                                        $statusText = 'Ebook có sẵn';
                                        $dotColor = 'bg-blue-500';
                                        $statusClass = 'text-blue-600';
                                    } elseif ($physicalStock === 0) {
                                        $statusText = 'Hết hàng';
                                        $dotColor = 'bg-red-500';
                                        $statusClass = 'text-red-600';
                                    } elseif ($physicalStock > 0 && $physicalStock < 10) {
                                        $statusText = 'Còn ' . $physicalStock . ' cuốn';
                                        $dotColor = 'bg-yellow-500';
                                        $statusClass = 'text-yellow-600';
                                    } else {
                                        $statusText = 'Còn ' . $physicalStock . ' cuốn';
                                        $dotColor = 'bg-green-500';
                                        $statusClass = 'text-green-600';
                                    }
                                    break;
                            }
                          @endphp
                          
                          <span class="w-2 h-2 {{ $dotColor }} rounded-full"></span>
                          <span class="text-sm {{ $statusClass }} font-medium">{{ $statusText }}</span>
                        </div>

                        <!-- Price -->
                        <div class="text-right">
                          <span class="text-2xl font-black text-black">
                            {{ number_format($book->min_price ?? 0, 0, ',', '.') }}₫
                          </span>
                          @if(!empty($book->discount))
                          <br>
                          <span class="text-sm text-gray-500 line-through">
                            {{ number_format(($book->min_price ?? 0) * 1.2, 0, ',', '.') }}₫
                          </span>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>

              <!-- Pagination theo phong cách trang chủ -->
              <nav class="mt-16 flex justify-center">
                <div class="bg-white border border-gray-100 shadow-lg p-6 relative overflow-hidden">
                  <!-- Background accent -->
                  <div class="absolute top-0 right-0 w-16 h-16 bg-gray-50 transform rotate-45 translate-x-8 -translate-y-8"></div>
                  
                  <div class="relative z-10 flex items-center space-x-3">
                    <!-- Prev Button -->
                    @if ($books->onFirstPage())
                      <span class="px-6 py-3 text-gray-400 cursor-not-allowed bg-gray-100 font-medium uppercase tracking-wide">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                      </span>
                    @else
                      <a href="{{ $books->previousPageUrl() }}" 
                         class="px-6 py-3 text-black hover:bg-black hover:text-white transition-all duration-300 font-medium uppercase tracking-wide border border-gray-200 hover:border-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                      </a>
                    @endif

                    <!-- Page Numbers -->
                    @foreach ($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                      @if ($page == $books->currentPage())
                        <span class="px-6 py-3 bg-black text-white font-black uppercase tracking-wide">
                          {{ $page }}
                        </span>
                      @else
                        <a href="{{ $url }}" 
                           class="px-6 py-3 text-black hover:bg-black hover:text-white transition-all duration-300 font-medium uppercase tracking-wide border border-gray-200 hover:border-black">
                          {{ $page }}
                        </a>
                      @endif
                    @endforeach

                    <!-- Next Button -->
                    @if ($books->hasMorePages())
                      <a href="{{ $books->nextPageUrl() }}" 
                         class="px-6 py-3 text-black hover:bg-black hover:text-white transition-all duration-300 font-medium uppercase tracking-wide border border-gray-200 hover:border-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </a>
                    @else
                      <span class="px-6 py-3 text-gray-400 cursor-not-allowed bg-gray-100 font-medium uppercase tracking-wide">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </span>
                    @endif
                  </div>
                  
                  <!-- Page info -->
                  <div class="text-center mt-4 text-sm text-gray-600 font-medium uppercase tracking-wide">
                    Trang {{ $books->currentPage() }} / {{ $books->lastPage() }}
                  </div>
                </div>
              </nav>
            </main>

          <!-- Sidebar Filters theo phong cách trang chủ -->
          <aside class="w-full lg:w-80 lg:order-1">
            <div class="bg-white border border-gray-100 shadow-lg p-6 sticky top-8 relative overflow-hidden">
              <!-- Background accent -->
              <div class="absolute top-0 right-0 w-20 h-20 bg-gray-50 transform rotate-45 translate-x-10 -translate-y-10"></div>
              
              <div class="relative z-10">
                <!-- Search Section -->
                <div class="mb-8">
                  <!-- Title với pre-title -->
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">SEARCH</span>
                  </div>
                  <h3 class="text-lg font-black text-black mb-6 uppercase tracking-wide">
                    Tìm kiếm sách
                  </h3>
                  <form method="GET" action="{{ url()->current() }}" role="search" class="relative">
                    <input 
                      name="search" 
                      type="search" 
                      placeholder="Tìm kiếm sách, tác giả, NXB..." 
                      aria-label="Search"
                      value="{{ request('search') ?? '' }}"
                      id="search-input"
                      class="w-full px-4 py-3 pr-20 border-2 border-gray-200 focus:border-black focus:outline-none transition-colors duration-300 hover:border-gray-400">
                    
                    @if(request('search'))
                      <a href="{{ route('books.index') }}" 
                         class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors duration-300"
                         title="Xóa tìm kiếm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </a>
                    @endif
                    
                    <button type="submit" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-600 hover:text-black transition-colors duration-300">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                    </button>
                  </form>
                </div>

                <!-- Categories Filter -->
                <div class="mb-8">
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">CATEGORY</span>
                  </div>
                  <h3 class="text-lg font-black text-black mb-6 uppercase tracking-wide">
                    Danh mục
                  </h3>
                  <select
                    aria-label="Chọn danh mục"
                    onchange="location = this.value;"
                    class="w-full px-4 py-3 border-2 border-gray-200 focus:border-black focus:outline-none transition-colors duration-300 bg-white hover:border-gray-400">
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
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">AUTHOR</span>
                  </div>
                  <h3 class="text-lg font-black text-black mb-6 uppercase tracking-wide">
                    Tác giả
                  </h3>
                  <select
                    aria-label="Chọn tác giả"
                    onchange="location = this.value;"
                    class="w-full px-4 py-3 border-2 border-gray-200 focus:border-black focus:outline-none transition-colors duration-300 bg-white hover:border-gray-400">
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
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">PUBLISHER</span>
                  </div>
                  <h3 class="text-lg font-black text-black mb-6 uppercase tracking-wide">
                    Nhà xuất bản
                  </h3>
                  <select
                    aria-label="Chọn nhà xuất bản"
                    onchange="location = this.value;"
                    class="w-full px-4 py-3 border-2 border-gray-200 focus:border-black focus:outline-none transition-colors duration-300 bg-white hover:border-gray-400">
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
                  <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">PRICE</span>
                  </div>
                  <h3 class="text-lg font-black text-black mb-6 uppercase tracking-wide">
                    Lọc theo giá
                  </h3>
                  
                  <!-- Custom price form -->
                  <form method="GET" action="{{ url()->current() }}" id="customPriceForm">
                    <!-- Preserve other filters -->
                    @if(request('search'))
                      <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('authors'))
                      <input type="hidden" name="authors" value="{{ request('authors') }}">
                    @endif
                    @if(request('brands'))
                      <input type="hidden" name="brands" value="{{ request('brands') }}">
                    @endif
                    
                    <!-- Price inputs -->
                    <div class="space-y-4 mb-6">
                      <div class="flex space-x-2">
                        <input type="number" 
                               name="min_price" 
                               placeholder="Giá từ" 
                               value="{{ request('min_price') }}"
                               class="flex-1 px-4 py-3 border-2 border-gray-200 focus:border-black focus:outline-none transition-colors duration-300 hover:border-gray-400">
                        <span class="flex items-center px-2 text-gray-500 font-medium">-</span>
                        <input type="number" 
                               name="max_price" 
                               placeholder="Giá đến" 
                               value="{{ request('max_price') }}"
                               class="flex-1 px-4 py-3 border-2 border-gray-200 focus:border-black focus:outline-none transition-colors duration-300 hover:border-gray-400">
                      </div>
                      
                      <p class="text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Nhập giá bằng VNĐ (ví dụ: 25000)
                      </p>
                      
                      <div class="flex space-x-3">
                        <button type="submit"
                                class="flex-1 bg-black text-white py-3 font-bold uppercase tracking-wide hover:bg-gray-800 transition-colors duration-300">
                          Áp dụng
                        </button>
                        
                        @if(request('min_price') || request('max_price'))
                        <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" 
                           class="px-4 py-3 bg-gray-100 text-black hover:bg-gray-200 transition-colors duration-300 flex items-center justify-center">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </a>
                        @endif
                      </div>
                    </div>
                  </form>
                </div>

                <!-- Reset Filter -->
                <div class="pt-6 border-t border-gray-100">
                  <a href="{{ url('/books') }}" 
                     class="w-full block text-center bg-gray-100 text-black py-3 font-bold uppercase tracking-wide hover:bg-gray-200 transition-colors duration-300">
                    Xóa tất cả bộ lọc
                  </a>
                </div>
              </div>
            </div>
          </aside>

        </div>
      </div>
    </div>

    <!-- Enhanced JavaScript for Premium Interactions -->
    <script>
      // View Mode Toggle Functionality
      document.addEventListener('DOMContentLoaded', function() {
        const gridViewBtn = document.getElementById('grid-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const booksContainer = document.getElementById('books-container');
        const gridCards = document.querySelectorAll('.book-card.grid-view');
        const listCards = document.querySelectorAll('.book-card.list-view');

        // Price filter functionality
        const minPriceInput = document.querySelector('input[name="min_price"]');
        const maxPriceInput = document.querySelector('input[name="max_price"]');

        // Price validation
        if (minPriceInput && maxPriceInput) {
          function validatePriceRange() {
            const minPrice = parseInt(minPriceInput.value) || 0;
            const maxPrice = parseInt(maxPriceInput.value) || Infinity;
            
            if (minPrice > maxPrice && maxPrice !== Infinity) {
              maxPriceInput.setCustomValidity('Giá tối đa phải lớn hơn giá tối thiểu');
              maxPriceInput.classList.add('border-red-500');
            } else {
              maxPriceInput.setCustomValidity('');
              maxPriceInput.classList.remove('border-red-500');
            }
          }
          
          minPriceInput.addEventListener('input', validatePriceRange);
          maxPriceInput.addEventListener('input', validatePriceRange);
        }

        // Grid View (Default) - only if buttons exist
        if (gridViewBtn && listViewBtn) {
        gridViewBtn.addEventListener('click', function() {
          // Update button states
          gridViewBtn.classList.remove('bg-gray-100', 'text-gray-600');
          gridViewBtn.classList.add('bg-black', 'text-white');
          listViewBtn.classList.remove('bg-black', 'text-white');
          listViewBtn.classList.add('bg-gray-100', 'text-gray-600');

          // Update container layout
          booksContainer.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8';

          // Show grid cards, hide list cards
          gridCards.forEach(card => card.classList.remove('hidden'));
          listCards.forEach(card => card.classList.add('hidden'));
        });

        // List View
        listViewBtn.addEventListener('click', function() {
          // Update button states
          listViewBtn.classList.remove('bg-gray-100', 'text-gray-600');
          listViewBtn.classList.add('bg-black', 'text-white');
          gridViewBtn.classList.remove('bg-black', 'text-white');
          gridViewBtn.classList.add('bg-gray-100', 'text-gray-600');

          // Update container layout
          booksContainer.className = 'space-y-6';

          // Show list cards, hide grid cards
          listCards.forEach(card => card.classList.remove('hidden'));
          gridCards.forEach(card => card.classList.add('hidden'));
        });
        }
      });

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
              button.classList.add('bg-red-500', 'text-white');
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
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded shadow-lg transform transition-all duration-300 translate-x-full ${
          type === 'success' ? 'bg-green-600 text-white' : 
          type === 'error' ? 'bg-red-600 text-white' : 
          'bg-black text-white'
        }`;
        toast.innerHTML = `<span class="font-semibold">${message}</span>`;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.style.transform = 'translateX(0)', 50);
        setTimeout(() => {
          toast.style.transform = 'translateX(100%)';
          setTimeout(() => document.body.removeChild(toast), 300);
        }, 2000);
      }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  </body>
</html>