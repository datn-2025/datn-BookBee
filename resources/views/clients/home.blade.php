@extends('layouts.app')

@section('title', 'Trang chủ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=AdihausDIN:wght@400;700&family=TitilliumWeb:wght@300;400;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
<div class="bookbee-home-page">
    <section class="w-full bg-white py-32 md:py-40 relative overflow-hidden">
        <!-- Background Elements - Minimal Adidas Style -->
        <div class="absolute inset-0 pointer-events-none">
            <div
                class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 rounded-none transform rotate-45 translate-x-36 -translate-y-36">
            </div>
            <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
            <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 items-center px-6 md:px-10 gap-10 max-w-screen-xl mx-auto">
            {{-- Phần văn bản bên trái --}}
            <div class="space-y-8 text-gray-900">
                <!-- Tiêu đề phụ với hiệu ứng đặc biệt -->
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-0.5 bg-gradient-to-r from-amber-600 to-orange-600"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600 flex items-center gap-2">
                        <i class="fas fa-book-open text-amber-500"></i>
                        BỘ SƯU TẬP ĐẶC BIỆT BOOKBEE
                    </span>
                </div>

                <!-- Tiêu đề chính - Typography đậm đà -->
                <h2 class="hero-title text-5xl md:text-7xl font-black uppercase leading-[0.9] tracking-tight text-black">
                    <span class="block">TRI THỨC</span>
                    <span class="block text-amber-600">KHÔNG</span>
                    <span class="block">GIỚI HẠN</span>
                </h2>

                <!-- Phụ đề -->
                <div class="space-y-4">
                    <p class="body-text text-xl md:text-2xl font-medium text-gray-700 max-w-lg">
                        Bộ sưu tập sách đặc biệt với tri thức không giới hạn dành cho mọi lứa tuổi
                    </p>

                    <!-- Nổi bật giá - Kiểu dáng vuông -->
                    <div class="flex items-center gap-4">
                        {{-- <span class="bg-red-600 text-white px-4 py-2 text-sm font-bold uppercase tracking-wide">
                            <i class="fas fa-tags mr-1"></i>
                            GIẢM 30%
                        </span> --}}
                        <span class="text-2xl font-bold text-amber-600">Mua ngay hôm nay!</span>
                    </div>
                </div>

                <!-- Nút hành động - Kiểu vuông -->
                <div class="pt-4">
                    <a href="{{ route('books.index') }}"
                        class="group bg-amber-600 text-white px-10 py-4 font-bold text-sm uppercase tracking-[0.1em] hover:bg-amber-700 transition-all duration-300 flex items-center gap-3 w-max button-text">
                        <i class="fas fa-search text-white"></i>
                        <span>KHÁM PHÁ NGAY</span>
                        <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                    </a>
                </div>
            </div>

            {{-- Right image - Background style --}}
            <div class="flex justify-center">
                <div class="relative group">
                    <!-- Background image style -->
                    <div class="relative h-80 md:h-96 w-80 md:w-96 bg-cover bg-center bg-no-repeat transform group-hover:scale-105 transition-transform duration-700"
                         style="background-image: url('{{ asset('storage/images/banner_home.jpg') }}');">
                    </div>

                    <!-- Background geometric shape -->
                    <div
                        class="absolute inset-0 -z-10 bg-gray-100 transform translate-x-4 translate-y-4 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-700">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-white py-20 md:py-24 relative overflow-hidden">
        <!-- Enhanced Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-64 h-1 bg-black opacity-20 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-black opacity-5 transform rotate-45 animate-bounce-slow">
            </div>
            <div class="absolute top-1/2 right-10 w-0.5 h-24 bg-black opacity-30"></div>
            <!-- Floating particles -->
            <div class="absolute top-20 left-1/4 w-2 h-2 bg-black opacity-10 rounded-full animate-float"></div>
            <div class="absolute bottom-20 right-1/4 w-3 h-3 bg-black opacity-5 rounded-full animate-float-delayed"></div>
        </div>

        <div class="relative z-10 max-w-screen-xl mx-auto px-6">
            {{-- Enhanced Features Section --}}
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-slate-600 transform origin-left scale-x-0 animate-slide-in"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        <i class="fas fa-shield-alt text-slate-500 mr-2"></i>
                        TẠI SAO CHỌN BOOKBEE
                    </span>
                    <div class="w-12 h-0.5 bg-slate-600 transform origin-right scale-x-0 animate-slide-in-right"></div>
                </div>
                <h2 class="section-title text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                    UY TÍN - CHẤT LƯỢNG - TẬN TÂM
                </h2>
            </div>

            <!-- Enhanced Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1: Free Shipping -->
                <div class="feature-card group bg-white border border-gray-100 hover:border-slate-600 hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2 h-full flex flex-col">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-slate-100 group-hover:scale-110 transition-all duration-500"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-500/0 to-slate-500/0 group-hover:from-slate-500/5 group-hover:to-transparent transition-all duration-500"></div>

                    <div class="p-8 text-center relative z-10 flex flex-col items-center justify-center flex-1">
                        <div class="w-16 h-16 bg-slate-600 text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-slate-700 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                            <i class="fas fa-shipping-fast text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                            <div class="absolute inset-0 bg-slate-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500"></div>
                        </div>

                        <!-- BLOCK CỐ ĐỊNH -->
                        <div class="flex flex-col items-center w-full">
                            <div class="h-20 flex items-center justify-center w-full">
                                <h3 class="text-lg font-bold uppercase tracking-wide text-black group-hover:text-slate-600 transition-colors duration-300 text-center">
                                    GIAO HÀNG MIỄN PHÍ
                                </h3>
                            </div>
                            <div class="w-8 h-0.5 bg-slate-600 my-3 group-hover:w-16 group-hover:bg-slate-500 transition-all duration-500"></div>
                            <div class="h-16 flex items-center justify-center w-full">
                                <p class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                                    Miễn phí vận chuyển toàn quốc
                                </p>
                            </div>
                        </div>
                        <!-- /BLOCK CỐ ĐỊNH -->

                        <div class="absolute bottom-0 left-0 h-1 bg-slate-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>

            <!-- Feature 2: Quality -->
            <div class="feature-card group bg-white border border-gray-100 hover:border-blue-600 hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2 h-full flex flex-col">
                <div class="absolute top-0 right-0 w-16 h-16 bg-blue-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-blue-100 group-hover:scale-110 transition-all duration-500"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/0 group-hover:from-blue-500/5 group-hover:to-transparent transition-all duration-500"></div>

                <div class="p-8 text-center relative z-10 flex flex-col items-center justify-center flex-1">
                    <div class="w-16 h-16 bg-blue-600 text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-blue-700 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                        <i class="fas fa-award text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                        <div class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500"></div>
                    </div>

                    <div class="flex flex-col items-center w-full">
                        <div class="h-20 flex items-center justify-center w-full">
                            <h3 class="text-lg font-bold uppercase tracking-wide text-black group-hover:text-blue-600 transition-colors duration-300 text-center">
                                CAM KẾT CHẤT LƯỢNG
                            </h3>
                        </div>
                        <div class="w-8 h-0.5 bg-blue-600 my-3 group-hover:w-16 group-hover:bg-blue-500 transition-all duration-500"></div>
                        <div class="h-16 flex items-center justify-center w-full">
                            <p class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                                Sản phẩm chính hãng 100%
                            </p>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 h-1 bg-blue-500 w-0 group-hover:w-full transition-all duration-700"></div>
                </div>
            </div>

            <!-- Feature 3: Daily Offers -->
            <div class="feature-card group bg-white border border-gray-100 hover:border-amber-600 hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2 h-full flex flex-col">
                <div class="absolute top-0 right-0 w-16 h-16 bg-amber-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-amber-100 group-hover:scale-110 transition-all duration-500"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/0 to-amber-500/0 group-hover:from-amber-500/5 group-hover:to-transparent transition-all duration-500"></div>

                <div class="p-8 text-center relative z-10 flex flex-col items-center justify-center flex-1">
                    <div class="w-16 h-16 bg-amber-600 text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-amber-700 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                        <i class="fas fa-gift text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                        <div class="absolute inset-0 bg-amber-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500"></div>
                    </div>

                    <div class="flex flex-col items-center w-full">
                        <div class="h-20 flex items-center justify-center w-full">
                            <h3 class="text-lg font-bold uppercase tracking-wide text-black group-hover:text-amber-600 transition-colors duration-300 text-center">
                                ƯU ĐÃI MỖI NGÀY
                            </h3>
                        </div>
                        <div class="w-8 h-0.5 bg-amber-600 my-3 group-hover:w-16 group-hover:bg-amber-500 transition-all duration-500"></div>
                        <div class="h-16 flex items-center justify-center w-full">
                            <p class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                                Khuyến mãi hấp dẫn liên tục
                            </p>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 h-1 bg-amber-500 w-0 group-hover:w-full transition-all duration-700"></div>
                </div>
            </div>

            <!-- Feature 4: Secure Payment -->
            <div class="feature-card group bg-white border border-gray-100 hover:border-slate-600 hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2 h-full flex flex-col">
                <div class="absolute top-0 right-0 w-16 h-16 bg-slate-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-slate-100 group-hover:scale-110 transition-all duration-500"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-slate-500/0 to-slate-500/0 group-hover:from-slate-500/5 group-hover:to-transparent transition-all duration-500"></div>

                <div class="p-8 text-center relative z-10 flex flex-col items-center justify-center flex-1">
                    <div class="w-16 h-16 bg-slate-600 text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-slate-700 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                        <i class="fas fa-lock text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                        <div class="absolute inset-0 bg-slate-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500"></div>
                    </div>

                    <div class="flex flex-col items-center w-full">
                        <div class="h-20 flex items-center justify-center w-full">
                            <h3 class="text-lg font-bold uppercase tracking-wide text-black group-hover:text-slate-600 transition-colors duration-300 text-center">
                                THANH TOÁN AN TOÀN
                            </h3>
                        </div>
                        <div class="w-8 h-0.5 bg-slate-600 my-3 group-hover:w-16 group-hover:bg-slate-500 transition-all duration-500"></div>
                        <div class="h-16 flex items-center justify-center w-full">
                            <p class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                                Hỗ trợ nhiều hình thức bảo mật
                            </p>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 h-1 bg-slate-500 w-0 group-hover:w-full transition-all duration-700"></div>
                </div>
            </div>
        </div>


            <!-- Enhanced Stats Section -->
            @if(isset($statistics) && $statistics['has_real_data'])
            <div class="stats-section mt-20 pt-16 border-t border-gray-200">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-red-500 transition-colors duration-300"
                            data-target="{{ $statistics['customers'] }}">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            <i class="fas fa-users mr-1"></i>
                            KHÁCH HÀNG</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    
                    @if($statistics['books_sold'] > 0)
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-green-500 transition-colors duration-300"
                            data-target="{{ $statistics['books_sold'] }}">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            <i class="fas fa-book mr-1"></i>
                            SÁCH ĐÃ BÁN</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    @else
                    <div class="space-y-2 group cursor-pointer">
                        <div
                            class="text-3xl md:text-4xl font-black text-black group-hover:text-yellow-500 transition-colors duration-300">
                            24/7</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            <i class="fas fa-headset mr-1"></i>
                            HỖ TRỢ</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    @endif
                    
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-pink-500 transition-colors duration-300"
                            data-target="{{ $statistics['delivery_hours'] }}">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            <i class="fas fa-truck-fast mr-1"></i>
                            GIỜ GIAO HÀNG</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-blue-500 transition-colors duration-300"
                            data-target="{{ $statistics['quality_percentage'] }}">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            <i class="fas fa-certificate mr-1"></i>
                            % CHẤT LƯỢNG</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($statistics))
            <!-- Stats Section when no real data available -->
            <div class="mt-20 pt-16 border-t border-gray-200">
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center gap-4 mb-4">
                        <div class="w-12 h-0.5 bg-black opacity-20"></div>
                        <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-400">
                            ĐANG THU THẬP DỮ LIỆU THỐNG KÊ
                        </span>
                        <div class="w-12 h-0.5 bg-black opacity-20"></div>
                    </div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider">
                        Thống kê sẽ được hiển thị khi có đơn hàng thành công
                    </p>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- SÁCH THEO DANH MỤC - ADIDAS STYLE -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-amber-600"></div>
                    <div>
                        <h2 class="section-title text-3xl md:text-4xl font-black uppercase tracking-tight text-black">DANH MỤC SÁCH</h2>
                        <div class="w-16 h-0.5 bg-amber-600 mt-2"></div>
                    </div>
                </div>
                <a href="{{ route('books.index') }}" 
                   class="bg-amber-600 text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-amber-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    XEM TẤT CẢ
                </a>
            </div>

            <!-- Category Tabs -->
            <div class="flex gap-0 mb-12 overflow-x-auto">
                @foreach ($categories as $index => $category)
                    <button type="button" 
                            class="tab-button flex-shrink-0 px-8 py-4 font-bold text-sm uppercase tracking-wider transition-all duration-300 cursor-pointer select-none {{ $index === 0 ? ' bg-amber-600 text-white' : 'bg-gray-100 text-black hover:bg-gray-200' }}"
                            data-tab="tab-{{ $category->id }}"
                            style="pointer-events: auto; user-select: none; -webkit-tap-highlight-color: transparent;">
                        <i class="fas fa-bookmark mr-2"></i>
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Content Tabs -->
            @foreach ($categories as $index => $category)
                <div id="tab-{{ $category->id }}" class="tab-content {{ $index === 0 ? 'block' : 'hidden' }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($category->books as $book)
                            @php
                                $format = $book->formats->first();
                                $price = $format->price ?? 0;
                                $discount = $format->discount ?? 0;
                                $finalPrice = $discount > 0 ? $price - $discount : $price;
                                
                                // Check if book has ebook format
                                $hasEbook = $book->formats->contains(function($format) {
                                    return stripos($format->format_name, 'ebook') !== false;
                                });
                            @endphp
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="group bg-white border-2 border-gray-100 hover:border-black transition-all duration-300 cursor-pointer">
                                
                                <!-- Image Container -->
                                <div class="aspect-square bg-gray-50 overflow-hidden relative">
                                    <img src="{{ $book->cover_image 
    ? asset('storage/' . $book->cover_image) 
    : ($book->images->first() 
        ? asset('storage/' . $book->images->first()->image_url) 
        : asset('images/default.jpg')) }}"
                                         alt="{{ $book->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    
                                    @if ($discount > 0)
                                        <div class="absolute top-3 left-3 bg-red-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                            -{{ number_format($discount) }} đ
                                        </div>
                                    @endif

                                    @if ($hasEbook)
                                        <div class="absolute top-3 right-3 bg-blue-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                            EBOOK
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Content -->
                                <div class="p-4 space-y-2">
                                    <h3 class="book-title font-bold text-black text-sm uppercase tracking-wide group-hover:opacity-70 transition-opacity">
                                        {{ Str::limit($book->title, 40) }}
                                    </h3>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">
                                        {{ $category->name ?? 'CHƯA CÓ DANH MỤC' }}
                                    </p>
                                    <div class="flex items-center justify-between pt-2">
                                        <div class="price-section">
                                            @if ($discount > 0)
                                                <span class="text-gray-400 line-through text-sm">{{ number_format($price, 0, ',', '.') }}₫</span>
                                                <span class="text-black font-bold text-lg ml-2">{{ number_format($finalPrice, 0, ',', '.') }}₫</span>
                                            @else
                                                <span class="text-black font-bold text-lg">{{ number_format($price, 0, ',', '.') }}₫</span>
                                            @endif
                                        </div>
                                        <div class="w-6 h-0.5 bg-black group-hover:w-8 transition-all duration-300"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- TẤT CẢ SÁCH - ADIDAS STYLE -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-slate-600"></div>
                    <div>
                        <h2 class="section-title text-3xl md:text-4xl font-black uppercase tracking-tight text-black">TẤT CẢ SÁCH</h2>
                        <div class="w-16 h-0.5 bg-slate-600 mt-2"></div>
                    </div>
                </div>
                <a href="{{ route('books.index') }}" 
                   class="bg-slate-600 text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-slate-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    XEM TẤt CẢ
                </a>
            </div>

            <!-- Books Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="allBooksGrid">
                @foreach ($allBooks as $index => $book)
                    @php
                        $format = $book->formats->first();
                        $price = $format->price ?? 0;
                        $discount = $format->discount ?? 0;
                        $finalPrice = $discount > 0 ? $price - $discount : $price;
                        
                        // Check if book has ebook format
                        $hasEbook = $book->formats->contains(function($format) {
                            return stripos($format->format_name, 'ebook') !== false;
                        });
                    @endphp
                    <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                         class="book-item group bg-white border-2 border-gray-100 hover:border-black transition-all duration-300 cursor-pointer {{ $index >= 8 ? 'hidden' : '' }}">
                        
                        <!-- Image Container -->
                        <div class="aspect-square bg-gray-50 overflow-hidden relative">
                            <img src="{{ $book->cover_image 
                                ? asset('storage/' . $book->cover_image) 
                                : ($book->images->first() 
                                    ? asset('storage/' . $book->images->first()->image_url) 
                                    : asset('images/default.jpg')) }}"
                                 alt="{{ $book->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            
                            @if ($discount > 0)
                                <div class="absolute top-3 left-3 bg-red-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                    -{{ number_format($discount) }} đ
                                </div>
                            @endif
                            
                            @if ($hasEbook)
                                <div class="absolute top-3 right-3 bg-blue-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                    EBOOK
                                </div>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="p-4 space-y-2">
                            <h3 class="book-title font-bold text-black text-sm uppercase tracking-wide group-hover:opacity-70 transition-opacity">
                                {{ Str::limit($book->title, 40) }}
                            </h3>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">
                                {{ $book->category->name ?? 'CHƯA CÓ DANH MỤC' }}
                            </p>
                            <div class="flex items-center justify-between pt-2">
                                <div class="price-section">
                                    @if ($discount > 0)
                                        <span class="text-gray-400 line-through text-sm">{{ number_format($price, 0, ',', '.') }}₫</span>
                                        <span class="text-black font-bold text-lg ml-2">{{ number_format($finalPrice, 0, ',', '.') }}₫</span>
                                    @else
                                        <span class="text-black font-bold text-lg">{{ number_format($price, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                                <div class="w-6 h-0.5 bg-black group-hover:w-8 transition-all duration-300"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- XEM THÊM Button -->
            {{-- @if(count($allBooks) > 8)
                <div class="text-center mt-8">
                    <button id="showMoreBooks" 
                            class="bg-slate-600 text-white px-8 py-3 font-bold text-sm uppercase tracking-wider hover:bg-slate-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        XEM THÊM ({{ count($allBooks) - 8 }} sách)
                    </button>
                    <button id="showLessBooks" 
                            class="bg-gray-600 text-white px-8 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-700 transition-colors hidden">
                        <i class="fas fa-minus mr-2"></i>
                        THU GỌN
                    </button>
                </div>
            @endif --}}
        </div>
    </section>

    <!-- SÁCH BÁN CHẠY - ADIDAS STYLE -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-amber-600"></div>
                    <div>
                        <h2 class="section-title text-3xl md:text-4xl font-black uppercase tracking-tight text-black">SÁCH BÁN CHẠY</h2>
                        <div class="w-16 h-0.5 bg-amber-600 mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Grid Layout - 3 Columns Equal -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Best Selling Book -->
                @if($featuredBooks->first())
                <div class="bg-black text-white relative overflow-hidden group cursor-pointer h-[600px]"
                     onclick="window.location='{{ route('books.show', ['slug' => $featuredBooks->first()->slug]) }}'">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent z-10"></div>
                    <img src="{{ $featuredBooks->first()->cover_image
    ? asset('storage/' . $featuredBooks->first()->cover_image)
    : ($featuredBooks->first()->images->first()
        ? asset('storage/' . $featuredBooks->first()->images->first()->image_url)
        : asset('images/default.jpg')) }}"
                         alt="Best Selling Book"
                         class="absolute inset-0 w-full h-full object-cover opacity-70 group-hover:opacity-90 transition-opacity duration-500">
                    
                    <div class="relative z-20 p-6 flex flex-col justify-end h-full">
                        <div class="space-y-3">
                            <span class="bg-white text-black px-3 py-1 text-xs font-bold uppercase tracking-wider">
                                <i class="fas fa-fire mr-1"></i>
                                BÁN CHẠY
                            </span>
                            <h3 class="book-title text-xl font-bold uppercase tracking-tight">{{ Str::limit($featuredBooks->first()->title, 40) }}</h3>
                            <div class="flex items-center justify-between">
                                @php
                                    $format = $featuredBooks->first()->formats->first();
                                    $price = $format->price ?? 0;
                                    $discount = $format->discount ?? 0;
                                    $finalPrice = $discount > 0 ? $price - $discount : $price;
                                @endphp
                                <p class="text-lg font-bold">{{ number_format($finalPrice, 0, ',', '.') }}₫</p>
                                <p class="text-sm bg-white text-black px-2 py-1 rounded font-medium">
                                    Đã bán: {{ number_format($featuredBooks->first()->total_sold ?? 0) }}
                                </p>
                            </div>
                            <div class="w-12 h-0.5 bg-white"></div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Latest Books -->
                <div class="bg-white border-2 border-gray-200 p-6 h-[600px] flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="feature-title text-lg font-bold uppercase tracking-wide">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            MỚI NHẤT
                        </h3>
                        <div class="w-8 h-0.5 bg-slate-600"></div>
                    </div>
                    <div class="flex-1 space-y-4 overflow-hidden">
                        @foreach ($latestBooks->take(3) as $book)
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="flex gap-4 p-3 hover:bg-gray-50 cursor-pointer group transition-colors border-b border-gray-100 last:border-b-0">
                                <img src="{{ $book->cover_image 
                                    ? asset('storage/' . $book->cover_image) 
                                    : ($book->images->first() 
                                        ? asset('storage/' . $book->images->first()->image_url) 
                                        : asset('images/default.jpg')) }}"
                                     alt="{{ $book->title }}" 
                                     class="w-24 h-32 object-cover shadow-lg rounded flex-shrink-0">
                                <div class="flex-1 min-w-0 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-base group-hover:opacity-70 transition-opacity leading-tight mb-2 truncate">{{ Str::limit($book->title, 30) }}</h4>
                                        <p class="text-sm text-gray-500 uppercase tracking-wider mb-2 truncate">{{ $book->authors && $book->authors->count() ? $book->authors->first()->name : 'N/A' }}</p>
                                    </div>
                                    <p class="text-lg font-bold text-black truncate">{{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Best Reviewed -->
                <div class="bg-white border-2 border-gray-200 p-6 h-[600px] flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="feature-title text-lg font-bold uppercase tracking-wide">
                            <i class="fas fa-medal text-amber-500 mr-2"></i>
                            ĐÁNH GIÁ CAO
                        </h3>
                        <div class="w-8 h-0.5 bg-amber-600"></div>
                    </div>
                    <div class="flex-1 space-y-4 overflow-hidden">
                        @foreach ($bestReviewedBooks->take(3) as $book)
                            @php
                                $rating = round($book->reviews->avg('rating'), 1);
                            @endphp
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="flex gap-4 p-3 hover:bg-gray-50 cursor-pointer group transition-colors border-b border-gray-100 last:border-b-0">
                                <img src="{{ $book->cover_image 
                                    ? asset('storage/' . $book->cover_image) 
                                    : ($book->images->first() 
                                        ? asset('storage/' . $book->images->first()->image_url) 
                                        : asset('images/default.jpg')) }}"
                                     alt="{{ $book->title }}" 
                                     class="w-24 h-32 object-cover shadow-lg rounded flex-shrink-0">
                                <div class="flex-1 min-w-0 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-base group-hover:opacity-70 transition-opacity leading-tight mb-2 truncate">{{ Str::limit($book->title, 30) }}</h4>
                                        <div class="flex text-yellow-400 text-sm mb-2">
                                            @for ($i = 0; $i < 5; $i++)
                                                {{ $i < $rating ? '★' : '☆' }}
                                            @endfor
                                            <span class="text-gray-500 ml-2 text-xs">({{ $rating }})</span>
                                        </div>
                                    </div>
                                    <p class="text-lg font-bold text-black truncate">{{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sale Books Section - Clean Minimal Style -->
            <div class="mt-12 bg-white border-2 border-gray-200 relative overflow-hidden">
                <!-- Simple Background Element -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50"></div>
                
                <div class="relative z-10 p-8">
                    <!-- Header Section -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-1 h-12 bg-red-600"></div>
                            <div>
                                <h3 class="section-title text-3xl md:text-4xl font-black uppercase tracking-tight text-red-600">GIẢM GIÁ ĐẶC BIỆT</h3>
                                <div class="w-16 h-0.5 bg-red-600 mt-2"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="bg-red-600 text-white px-4 py-2 text-lg font-bold uppercase tracking-wider">
                                <i class="fas fa-percent mr-1"></i>
                                SALE
                            </span>
                        </div>
                    </div>
                    
                    <!-- Books Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="saleBooksGrid">
                        @foreach ($saleBooks as $index => $book)
                            @php
                                $format = $book->formats->first();
                                $oldPrice = $format->price ?? 0;
                                $discount = $format->discount ?? 0;
                                $newPrice = $oldPrice - $discount;
                                $discountPercent = $oldPrice > 0 ? round(($discount / $oldPrice) * 100) : 0;
                            @endphp
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="sale-book-item group bg-white border-2 border-gray-100 hover:border-black transition-all duration-300 cursor-pointer {{ $index >= 8 ? 'hidden' : '' }}">
                                
                                <!-- Image Container -->
                                <div class="aspect-square bg-gray-50 overflow-hidden relative">
                                    <img src="{{ $book->cover_image 
                                        ? asset('storage/' . $book->cover_image) 
                                        : ($book->images->first() 
                                            ? asset('storage/' . $book->images->first()->image_url) 
                                            : asset('images/default.jpg')) }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    
                                    @if ($discount > 0)
                                        <div class="absolute top-3 left-3 bg-red-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                            <i class="fas fa-fire mr-1"></i>
                                            -{{ number_format($discount) }} đ
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Content -->
                                <div class="p-4 space-y-2">
                                    <h3 class="font-bold text-black text-sm uppercase tracking-wide group-hover:opacity-70 transition-opacity">
                                        {{ Str::limit($book->title, 40) }}
                                    </h3>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">
                                        {{ $book->category->name ?? 'CHƯA CÓ DANH MỤC' }}
                                    </p>
                                    <div class="flex items-center justify-between pt-2">
                                        <div class="price-section">
                                            @if ($discount > 0)
                                                <span class="text-gray-400 line-through text-sm">{{ number_format($oldPrice, 0, ',', '.') }}₫</span>
                                                <span class="text-black font-bold text-lg ml-2">{{ number_format($newPrice, 0, ',', '.') }}₫</span>
                                            @else
                                                <span class="text-black font-bold text-lg">{{ number_format($oldPrice, 0, ',', '.') }}₫</span>
                                            @endif
                                        </div>
                                        <div class="w-6 h-0.5 bg-black group-hover:w-8 transition-all duration-300"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- <!-- Call to Action -->
                    @if(count($saleBooks) > 8)
                        <div class="text-center mt-8">
                            <button id="showMoreSaleBooks" 
                                    class="bg-black text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-700 transition-colors">
                                XEM TẤT CẢ KHUYẾN MÃI ({{ count($saleBooks) - 8 }} sách)
                            </button>
                            <button id="showLessSaleBooks" 
                                    class="bg-black text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-700 transition-colors hidden">
                                THU GỌN
                            </button>
                        </div>
                    @else
                        <div class="text-center mt-8">
                            <button id="showMoreSaleBooks" 
                                    class="bg-black text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-700 transition-colors">
                                XEM TẤT CẢ KHUYẾN MÃI ({{ count($saleBooks) - 8 }} sách)
                            </button>
                            <button id="showLessSaleBooks" 
                                    class="bg-black text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-700 transition-colors hidden">
                                THU GỌN
                            </button>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </section>

    <!-- COMBO SÁCH - ADIDAS STYLE -->
    @if(isset($combos) && $combos->count())
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-purple-600"></div>
                    <div>
                        <h2 class="section-title text-3xl md:text-4xl font-black uppercase tracking-tight text-black">COMBO SÁCH</h2>
                        <div class="w-16 h-0.5 bg-purple-600 mt-2"></div>
                    </div>
                </div>
                <a href="{{ route('combos.index') }}" 
                   class="bg-slate-600 text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-slate-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    XEM TẤT CẢ
                </a>
            </div>
            
            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($combos as $combo)
                    <div onclick="window.location='{{ route('combos.show', ['slug' => $combo->slug]) }}'"
                         class="group bg-white border-2 border-gray-100 hover:border-black transition-all duration-300 cursor-pointer">
                        
                        <!-- Image -->
                        <div class="aspect-square bg-gray-50 overflow-hidden relative">
                            <img src="{{ $combo->cover_image ? asset('storage/' . $combo->cover_image) : asset('images/default.jpg') }}" 
                                 alt="{{ $combo->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 bg-purple-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                <i class="fas fa-layer-group mr-1"></i>
                                COMBO
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-4 space-y-2">
                            <h3 class="font-bold text-black text-sm uppercase tracking-wide group-hover:opacity-70 transition-opacity">
                                {{ $combo->name }}
                            </h3>
                            <p class="text-lg font-bold text-black">{{ number_format($combo->combo_price, 0, ',', '.') }}₫</p>
                            <div class="flex items-center justify-between pt-2">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-books mr-1"></i>
                                    {{ $combo->books->count() }} CUỐN
                                </span>
                                <div class="w-6 h-0.5 bg-black group-hover:w-8 transition-all duration-300"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- SÁCH ĐẶT TRƯỚC - PREORDER BOOKS SECTION -->
    @if(isset($preorderBooks) && $preorderBooks->count())
    <section class="bg-gradient-to-r from-blue-50 to-indigo-50 py-20" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-blue-600"></div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-blue-900">
                            🔖 ĐẶT TRƯỚC SÁCH MỚI
                        </h2>
                        <p class="text-blue-600 text-sm font-medium mt-2">Đặt trước ngay - Nhận ưu đãi đặc biệt</p>
                        <div class="w-16 h-0.5 bg-blue-600 mt-2"></div>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center gap-2 text-blue-600">
                    <i class="ri-calendar-line text-lg"></i>
                    <span class="text-sm font-medium">Sắp ra mắt</span>
                </div>
            </div>
            
            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($preorderBooks as $book)
                    <div class="group bg-white border-2 border-blue-100 hover:border-blue-500 transition-all duration-300 shadow-sm hover:shadow-lg cursor-pointer relative overflow-hidden flex flex-col h-full">
                        @php
                            $originalPrice = $book->formats->first()->price ?? 0;
                            $preorderPrice = $book->getPreorderPrice();
                            $discountPercent = ($preorderPrice && $originalPrice > 0 && $preorderPrice < $originalPrice)
                                ? round((($originalPrice - $preorderPrice) / $originalPrice) * 100)
                                : 0;
                        @endphp
                        
                        <!-- Preorder Badge -->
                        <div class="absolute top-3 left-3 bg-red-600 text-white px-3 py-1 text-xs font-bold uppercase tracking-wide z-10 shadow-lg
">
                            ĐẶT TRƯỚC
                        </div>

                        <!-- Release Date Badge -->
                        <div class="absolute top-3 right-3 bg-yellow-400 text-black px-2 py-1 text-xs font-bold uppercase z-10">
                            {{ $book->release_date ? $book->release_date->format('d/m/Y') : 'TBD' }}
                        </div>
                        @if($discountPercent > 0)
                            <!-- Discount Percent Badge -->
                            <div class="absolute top-12 left-3 bg-red-600 text-white px-2 py-1 text-xs font-bold uppercase z-10 shadow">
                                -{{ $discountPercent }}%
                            </div>
                        @endif
                        
                        <!-- Image -->
                        <div class="aspect-[3/4] bg-white overflow-hidden relative">
                            @php
                                $cardImage = null;
                                if ($book->images && $book->images->isNotEmpty()) {
                                    $cardImage = asset('storage/' . $book->images->first()->image_url);
                                } elseif (!empty($book->cover_image)) {
                                    $cardImage = asset('storage/' . $book->cover_image);
                                } else {
                                    $cardImage = asset('images/default.jpg');
                                }
                            @endphp
                            <img src="{{ $cardImage }}"
                                 alt="{{ $book->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        </div>
                        
                        <!-- Content -->
                        <div class="p-4 space-y-3 flex flex-col">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ $book->title }}
                            </h3>
                            
                            <div class="space-y-2">
                                @if($book->authors && $book->authors->isNotEmpty())
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">
                                        {{ $book->authors->first()->name }}
                                    </p>
                                @endif
                                
                                <!-- Price -->
                                <div class="flex items-center justify-between">
                                    @php
                                        $preorderPrice = $book->getPreorderPrice();
                                        $originalPrice = $book->formats->first()->price ?? 0;
                                        $savePercent = ($preorderPrice && $originalPrice > 0 && $preorderPrice < $originalPrice)
                                            ? round((($originalPrice - $preorderPrice) / $originalPrice) * 100)
                                            : 0;
                                    @endphp
                                    
                                    <div class="space-y-1">
                                        @if($preorderPrice && $preorderPrice < $originalPrice)
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg font-bold text-blue-600">
                                                    {{ number_format($preorderPrice, 0, ',', '.') }}₫
                                                </span>
                                                <span class="text-sm text-gray-400 line-through">
                                                    {{ number_format($originalPrice, 0, ',', '.') }}₫
                                                </span>
                                            </div>
                                            <div class="bg-red-100 text-red-600 px-2 py-1 text-xs font-bold rounded inline-block">
                                                TIẾT KIỆM {{ number_format($originalPrice - $preorderPrice, 0, ',', '.') }}₫
                                            </div>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ number_format($preorderPrice ?: $originalPrice, 0, ',', '.') }}₫
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Action Button -->
                            <div class=" mb-0">
                                <button onclick="window.location.href='{{ route('books.show', $book->slug) }}'"
                                        class="w-full h-10 bg-black text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-center">
                                    <i class="ri-bookmark-line mr-2 text-sm"></i>
                                    <span>ĐẶT TRƯỚC NGAY</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View More Button -->
            @if($preorderBooks->count() >= 8)
            <div class="text-center mt-12">
                <a href="{{ route('books.index', ['filter' => 'preorder']) }}" 
                   class="inline-flex items-center gap-3 bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white px-8 py-4 font-bold text-sm uppercase tracking-wide transition-all duration-300 group">
                    <span>XEM TẤT CẢ SÁCH ĐẶT TRƯỚC</span>
                    <div class="w-4 h-0.5 bg-current group-hover:w-8 transition-all duration-300"></div>
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

    <!-- TIN TỨC - ADIDAS STYLE -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-indigo-600"></div>
                    <div>
                        <h2 class="section-title text-3xl md:text-4xl font-black uppercase tracking-tight text-black">TIN TỨC</h2>
                        <div class="w-16 h-0.5 bg-indigo-600 mt-2"></div>
                    </div>
                </div>
                <a href="{{ route('news.index') }}" class="bg-indigo-600 text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-newspaper mr-2"></i>
                    XEM TẤT CẢ
                </a>
            </div>

            <!-- Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($articles->take(3) as $article)
                    <article class="group bg-white border-2 border-gray-100 hover:border-black transition-all duration-300">
                        <a href="{{ route('news.show', $article->id) }}" class="block">
                            <!-- Image -->
                            <div class="aspect-[4/3] bg-gray-100 overflow-hidden relative">
                                <img src="{{ asset('storage/' . $article->thumbnail) }}" 
                                     alt="{{ $article->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-3 left-3 bg-indigo-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                    <i class="fas fa-newspaper mr-1"></i>
                                    TIN TỨC
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 uppercase tracking-wider font-bold">
                                        {{ $article->created_at->format('d.m.Y') }}
                                    </span>
                                    <div class="w-6 h-0.5 bg-black group-hover:w-8 transition-all duration-300"></div>
                                </div>
                                
                                <h3 class="font-bold text-lg text-black leading-tight group-hover:opacity-70 transition-opacity">
                                    {{ $article->title }}
                                </h3>
                                
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    {{ Str::limit($article->summary, 100) }}
                                </p>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-newspaper text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">CHƯA CÓ TIN TỨC</h3>
                        <p class="text-gray-500 uppercase tracking-wider text-sm">Hãy quay lại sau</p>
                    </div>
                @endforelse
            </div>

            <!-- Newsletter Subscription -->
            <div class="mt-16 bg-black text-white p-8">
                <div class="text-center max-w-2xl mx-auto">
                    <h3 class="section-title text-2xl font-bold uppercase tracking-wide mb-4">ĐĂNG KÝ NHẬN TIN</h3>
                    <p class="body-text text-white/80 mb-8">Nhận thông tin mới nhất về sách và ưu đãi đặc biệt</p>
                    <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                        <input type="email" placeholder="Email của bạn"
                            class="flex-1 px-6 py-4 bg-white/10 border border-white/20 text-white placeholder-white/60 focus:outline-none focus:border-white/40">
                        <button type="submit"
                            class="button-text bg-indigo-600 text-white px-8 py-4 font-bold text-sm uppercase tracking-wider hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>
                            ĐĂNG KÝ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script>
        // XEM THÊM / THU GỌN functionality for books
        document.addEventListener('DOMContentLoaded', function() {
            const showMoreBtn = document.getElementById('showMoreBooks');
            const showLessBtn = document.getElementById('showLessBooks');
            const bookItems = document.querySelectorAll('.book-item');
            
            // Sale books expand/collapse functionality
            const showMoreSaleBtn = document.getElementById('showMoreSaleBooks');
            const showLessSaleBtn = document.getElementById('showLessSaleBooks');
            const saleBookItems = document.querySelectorAll('.sale-book-item');
            
            if (showMoreBtn) {
                showMoreBtn.addEventListener('click', function() {
                    // Show all hidden books
                    bookItems.forEach(function(item, index) {
                        if (index >= 8) {
                            item.classList.remove('hidden');
                        }
                    });
                    
                    // Toggle buttons
                    showMoreBtn.classList.add('hidden');
                    showLessBtn.classList.remove('hidden');
                    
                    // Smooth scroll to show the newly revealed books
                    setTimeout(function() {
                        if (bookItems[8]) {
                            bookItems[8].scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'nearest' 
                            });
                        }
                    }, 100);
                });
            }
            
            if (showLessBtn) {
                showLessBtn.addEventListener('click', function() {
                    // Hide books after index 7 (keep first 8 books)
                    bookItems.forEach(function(item, index) {
                        if (index >= 8) {
                            item.classList.add('hidden');
                        }
                    });
                    
                    // Toggle buttons
                    showLessBtn.classList.add('hidden');
                    showMoreBtn.classList.remove('hidden');
                    
                    // Smooth scroll back to the section
                    document.getElementById('allBooksGrid').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            }
            
            // Sale books expand functionality
            if (showMoreSaleBtn) {
                showMoreSaleBtn.addEventListener('click', function() {
                    // Show all hidden sale books
                    saleBookItems.forEach(function(item, index) {
                        if (index >= 8) {
                            item.classList.remove('hidden');
                        }
                    });
                    
                    // Toggle buttons
                    showMoreSaleBtn.classList.add('hidden');
                    showLessSaleBtn.classList.remove('hidden');
                    
                    // Smooth scroll to show the newly revealed books
                    setTimeout(function() {
                        if (saleBookItems[8]) {
                            saleBookItems[8].scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'nearest' 
                            });
                        }
                    }, 100);
                });
            }
            
            // Sale books collapse functionality
            if (showLessSaleBtn) {
                showLessSaleBtn.addEventListener('click', function() {
                    // Hide books after index 7 (keep first 8 books)
                    saleBookItems.forEach(function(item, index) {
                        if (index >= 8) {
                            item.classList.add('hidden');
                        }
                    });
                    
                    // Toggle buttons
                    showLessSaleBtn.classList.add('hidden');
                    showMoreSaleBtn.classList.remove('hidden');
                    
                    // Smooth scroll back to the section
                    document.getElementById('saleBooksGrid').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            }
        });

        // ===== PREORDER MODAL FUNCTIONALITY =====
        function openPreorderModal(bookId) {
            @auth
                // If user is logged in, redirect to preorder form
                window.location.href = `/preorders/create/${bookId}`;
            @else
                // If not logged in, redirect to login with intended URL
                window.location.href = `/login?intended=/preorders/create/${bookId}`;
            @endauth
        }
    </script>

    <!-- Preorder Modal (for quick view/info) -->
    <div id="preorderModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-white max-w-md w-full rounded-lg shadow-2xl transform transition-all duration-300 scale-95" id="preorderModalContent">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="ri-bookmark-line text-blue-600"></i>
                    Đặt Trước Sách
                </h3>
                <button onclick="closePreorderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-6">
                <!-- Book Info Placeholder -->
                <div id="bookInfo" class="space-y-4">
                    <div class="animate-pulse space-y-3">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-8 bg-gray-200 rounded w-full"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    @auth
                        <button id="proceedToPreorder" 
                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 font-bold text-sm uppercase tracking-wide transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="ri-bookmark-line"></i>
                            Tiến Hành Đặt Trước
                        </button>
                    @else
                        <button onclick="window.location.href='/login'" 
                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-6 font-bold text-sm uppercase tracking-wide transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="ri-login-circle-line"></i>
                            Đăng Nhập Để Đặt Trước
                        </button>
                    @endauth
                    
                    <button onclick="closePreorderModal()" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm uppercase tracking-wide transition-colors">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closePreorderModal() {
            const modal = document.getElementById('preorderModal');
            const modalContent = document.getElementById('preorderModalContent');
            
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.style.display = 'none';
                modalContent.classList.remove('scale-95');
            }, 150);
        }

        // Close modal when clicking outside
        document.getElementById('preorderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePreorderModal();
            }
        });
    </script>

    <script src="{{ asset('js/home.js') }}"></script>
@endpush
