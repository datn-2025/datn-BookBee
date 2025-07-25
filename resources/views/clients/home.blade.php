@extends('layouts.app')
@section('title', 'BookBee')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
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
            {{-- Left text - Adidas Typography Style --}}
            <div class="space-y-8 text-gray-900">
                <!-- Pre-title với Adidas style -->
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">
                        BOOKBEE SPECIAL
                    </span>
                </div>

                <!-- Main headline - Bold Adidas typography -->
                <h2 class="text-5xl md:text-7xl font-black uppercase leading-[0.9] tracking-tight text-black">
                    <span class="block">IMPOSSIBLE</span>
                    <span class="block text-gray-400">IS</span>
                    <span class="block">NOTHING</span>
                </h2>

                <!-- Subtitle -->
                <div class="space-y-4">
                    <p class="text-xl md:text-2xl font-medium text-gray-700 max-w-lg">
                        Bộ sưu tập sách đặc biệt với tri thức không giới hạn
                    </p>

                    <!-- Price highlight - Clean Adidas style -->
                    <div class="flex items-center gap-4">
                        <span class="bg-red-600 text-white px-4 py-2 text-sm font-bold uppercase tracking-wide">
                            GIẢM 30%
                        </span>
                        <span class="text-2xl font-bold text-black">Mua ngay hôm nay!</span>
                    </div>
                </div>

                <!-- CTA Button - Adidas style -->
                <div class="pt-4">
                    <a href="#"
                        class="group bg-black text-white px-10 py-4 font-bold text-sm uppercase tracking-[0.1em] hover:bg-gray-800 transition-all duration-300 flex items-center gap-3 w-max">
                        <span>XEM NGAY</span>
                        <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                    </a>
                </div>
            </div>

            {{-- Right image - Clean presentation --}}
            <div class="flex justify-center">
                <div class="relative group">
                    <!-- Main image với clean style -->
                    <div class="relative">
                        <img src="{{ asset('storage/images/banner-image2.png') }}"
                            class="h-80 md:h-96 object-contain transform group-hover:scale-105 transition-transform duration-700"
                            alt="Banner BookBee">

                        <!-- Clean badge thay vì rounded -->
                        <div
                            class="absolute -top-6 -left-6 bg-black text-white px-6 py-3 transform group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform duration-500">
                            <div class="text-center">
                                <div class="text-sm font-bold uppercase tracking-wide">NEW</div>
                                <div class="text-xs uppercase tracking-wider text-gray-300">Collection</div>
                            </div>
                        </div>

                        <!-- Minimal accent -->
                        <div
                            class="absolute -bottom-4 -right-4 bg-white border-2 border-black px-4 py-2 transform group-hover:translate-x-1 group-hover:translate-y-1 transition-transform duration-500">
                            <span class="text-xs font-bold uppercase tracking-wide text-black">Premium</span>
                        </div>
                    </div>

                    <!-- Background geometric shape -->
                    <div
                        class="absolute inset-0 -z-10 bg-gray-100 transform translate-x-4 translate-y-4 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-700">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-white py-20 md:py-24 relative overflow-hidden" data-aos="fade-up">
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
            <div class="text-center mb-16" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black transform origin-left scale-x-0 animate-slide-in"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600 opacity-0 animate-fade-in-up"
                        style="animation-delay: 0.3s;">
                        WHY CHOOSE BOOKBEE
                    </span>
                    <div class="w-12 h-0.5 bg-black transform origin-right scale-x-0 animate-slide-in-right"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black opacity-0 animate-fade-in-up"
                    style="animation-delay: 0.5s;">
                    IMPOSSIBLE IS NOTHING
                </h2>
            </div>

            <!-- Enhanced Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1: Free Shipping -->
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2"
                    data-aos="fade-up" data-aos-delay="200">
                    <!-- Enhanced geometric background -->
                    <div
                        class="absolute top-0 right-0 w-16 h-16 bg-red-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-red-100 group-hover:scale-110 transition-all duration-500">
                    </div>

                    <!-- Hover overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-red-500/0 to-red-500/0 group-hover:from-red-500/5 group-hover:to-transparent transition-all duration-500">
                    </div>

                    <div class="p-8 text-center relative z-10">
                        <!-- Enhanced Icon -->
                        <div
                            class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-red-500 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                            <i
                                class="fas fa-shipping-fast text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                            <!-- Icon glow effect -->
                            <div
                                class="absolute inset-0 bg-red-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500">
                            </div>
                        </div>

                        <!-- Enhanced Content -->
                        <h3
                            class="text-lg font-bold uppercase tracking-wide text-black mb-2 group-hover:text-red-600 transition-colors duration-300">
                            GIAO HÀNG MIỄN PHÍ
                        </h3>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto mb-4 group-hover:w-16 group-hover:bg-red-500 transition-all duration-500">
                        </div>
                        <p
                            class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                            Miễn phí vận chuyển toàn quốc
                        </p>

                        <!-- Progress indicator -->
                        <div
                            class="absolute bottom-0 left-0 h-1 bg-red-500 w-0 group-hover:w-full transition-all duration-700">
                        </div>
                    </div>
                </div>

                <!-- Feature 2: Quality -->
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2"
                    data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="absolute top-0 right-0 w-16 h-16 bg-yellow-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-yellow-100 group-hover:scale-110 transition-all duration-500">
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-yellow-500/0 to-yellow-500/0 group-hover:from-yellow-500/5 group-hover:to-transparent transition-all duration-500">
                    </div>

                    <div class="p-8 text-center relative z-10">
                        <div
                            class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-yellow-500 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                            <i
                                class="fas fa-certificate text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                            <div
                                class="absolute inset-0 bg-yellow-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500">
                            </div>
                        </div>

                        <h3
                            class="text-lg font-bold uppercase tracking-wide text-black mb-2 group-hover:text-yellow-600 transition-colors duration-300">
                            CAM KẾT CHẤT LƯỢNG
                        </h3>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto mb-4 group-hover:w-16 group-hover:bg-yellow-500 transition-all duration-500">
                        </div>
                        <p
                            class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                            Sản phẩm chính hãng 100%
                        </p>

                        <div
                            class="absolute bottom-0 left-0 h-1 bg-yellow-500 w-0 group-hover:w-full transition-all duration-700">
                        </div>
                    </div>
                </div>

                <!-- Feature 3: Daily Offers -->
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2"
                    data-aos="fade-up" data-aos-delay="400">
                    <div
                        class="absolute top-0 right-0 w-16 h-16 bg-pink-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-pink-100 group-hover:scale-110 transition-all duration-500">
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-pink-500/0 to-pink-500/0 group-hover:from-pink-500/5 group-hover:to-transparent transition-all duration-500">
                    </div>

                    <div class="p-8 text-center relative z-10">
                        <div
                            class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-pink-500 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                            <i
                                class="fas fa-gift text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                            <div
                                class="absolute inset-0 bg-pink-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500">
                            </div>
                        </div>

                        <h3
                            class="text-lg font-bold uppercase tracking-wide text-black mb-2 group-hover:text-pink-600 transition-colors duration-300">
                            ƯU ĐÃI MỖI NGÀY
                        </h3>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto mb-4 group-hover:w-16 group-hover:bg-pink-500 transition-all duration-500">
                        </div>
                        <p
                            class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                            Khuyến mãi hấp dẫn liên tục
                        </p>

                        <div
                            class="absolute bottom-0 left-0 h-1 bg-pink-500 w-0 group-hover:w-full transition-all duration-700">
                        </div>
                    </div>
                </div>

                <!-- Feature 4: Secure Payment -->
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden cursor-pointer transform hover:-translate-y-2"
                    data-aos="fade-up" data-aos-delay="500">
                    <div
                        class="absolute top-0 right-0 w-16 h-16 bg-blue-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-blue-100 group-hover:scale-110 transition-all duration-500">
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/0 group-hover:from-blue-500/5 group-hover:to-transparent transition-all duration-500">
                    </div>

                    <div class="p-8 text-center relative z-10">
                        <div
                            class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-blue-500 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 relative">
                            <i
                                class="fas fa-lock text-xl transform group-hover:scale-125 transition-transform duration-300"></i>
                            <div
                                class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500">
                            </div>
                        </div>

                        <h3
                            class="text-lg font-bold uppercase tracking-wide text-black mb-2 group-hover:text-blue-600 transition-colors duration-300">
                            THANH TOÁN AN TOÀN
                        </h3>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto mb-4 group-hover:w-16 group-hover:bg-blue-500 transition-all duration-500">
                        </div>
                        <p
                            class="text-sm text-gray-600 leading-relaxed uppercase tracking-wider group-hover:text-gray-800 transition-colors duration-300">
                            Hỗ trợ nhiều hình thức bảo mật
                        </p>

                        <div
                            class="absolute bottom-0 left-0 h-1 bg-blue-500 w-0 group-hover:w-full transition-all duration-700">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Section -->
            @if(isset($statistics) && $statistics['has_real_data'])
            <div class="mt-20 pt-16 border-t border-gray-200" data-aos="fade-up" data-aos-delay="600">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-red-500 transition-colors duration-300"
                            data-target="{{ $statistics['customers'] }}">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
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
                            % CHẤT LƯỢNG</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($statistics))
            <!-- Stats Section when no real data available -->
            <div class="mt-20 pt-16 border-t border-gray-200" data-aos="fade-up" data-aos-delay="600">
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
                    <div class="w-1 h-12 bg-black"></div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">DANH MỤC SÁCH</h2>
                        <div class="w-16 h-0.5 bg-black mt-2"></div>
                    </div>
                </div>
                <a href="{{ route('books.index') }}" 
                   class="bg-black text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors">
                    XEM TẤT CẢ
                </a>
            </div>

            <!-- Category Tabs -->
            <div class="flex gap-0 mb-12 overflow-x-auto">
                @foreach ($categories as $index => $category)
                    <button class="tab-button flex-shrink-0 {{ $index === 0 ? 'bg-black text-white' : 'bg-gray-100 text-black hover:bg-gray-200' }} px-8 py-4 font-bold text-sm uppercase tracking-wider transition-colors"
                            data-tab="tab-{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Content Tabs -->
            @foreach ($categories as $index => $category)
                <div id="tab-tab-{{ $category->id }}" class="tab-content {{ $index === 0 ? 'block' : 'hidden' }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($category->books as $book)
                            @php
                                $format = $book->formats->first();
                                $price = $format->price ?? 0;
                                $discount = $format->discount ?? 0;
                                $finalPrice = $discount > 0 ? $price - ($price * $discount) / 100 : $price;
                            @endphp
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="group bg-white border-2 border-gray-100 hover:border-black transition-all duration-300 cursor-pointer">
                                
                                <!-- Image Container -->
                                <div class="aspect-square bg-gray-50 overflow-hidden relative">
                                    <img src="{{ $book->images->first() ? asset('storage/' . $book->images->first()->image_url) : ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default.jpg')) }}"
                                         alt="{{ $book->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    
                                    @if ($discount > 0)
                                        <div class="absolute top-3 left-3 bg-red-600 text-white px-2 py-1 text-xs font-bold uppercase">
                                            -{{ $discount }}%
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Content -->
                                <div class="p-4 space-y-2">
                                    <h3 class="font-bold text-black text-sm uppercase tracking-wide group-hover:opacity-70 transition-opacity">
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

    <!-- SÁCH NỔI BẬT - ADIDAS STYLE -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-black"></div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">SÁCH NỔI BẬT</h2>
                        <div class="w-16 h-0.5 bg-black mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Featured Book - Large -->
                @if($featuredBooks->first())
                <div class="lg:col-span-2 bg-black text-white relative overflow-hidden group cursor-pointer"
                     onclick="window.location='{{ route('books.show', ['slug' => $featuredBooks->first()->slug]) }}'">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent z-10"></div>
                    <img src="{{ $featuredBooks->first()->images->first() ? asset('storage/' . $featuredBooks->first()->images->first()->image_url) : ($featuredBooks->first()->cover_image ? asset('storage/' . $featuredBooks->first()->cover_image) : asset('images/default.jpg')) }}"
                         alt="Featured Book"
                         class="absolute inset-0 w-full h-full object-cover opacity-70 group-hover:opacity-90 transition-opacity duration-500">
                    
                    <div class="relative z-20 p-8 flex flex-col justify-end h-80">
                        <div class="space-y-4">
                            <span class="bg-white text-black px-3 py-1 text-xs font-bold uppercase tracking-wider">NỔI BẬT</span>
                            <h3 class="text-2xl font-bold uppercase tracking-tight">{{ $featuredBooks->first()->title }}</h3>
                            <p class="text-lg font-bold">{{ number_format($featuredBooks->first()->formats->first()->price ?? 0, 0, ',', '.') }}₫</p>
                            <div class="w-12 h-0.5 bg-white"></div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Latest Books -->
                <div class="bg-white border-2 border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold uppercase tracking-wide">MỚI NHẤT</h3>
                        <div class="w-8 h-0.5 bg-black"></div>
                    </div>
                    <div class="space-y-4">
                        @foreach ($latestBooks->take(3) as $book)
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="flex gap-3 p-2 hover:bg-gray-50 cursor-pointer group transition-colors">
                                <img src="{{ $book->images->first() ? asset('storage/' . $book->images->first()->image_url) : ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default.jpg')) }}"
                                     alt="{{ $book->title }}" 
                                     class="w-12 h-16 object-cover">
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm group-hover:opacity-70 transition-opacity">{{ Str::limit($book->title, 30) }}</h4>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">{{ $book->authors && $book->authors->count() ? $book->authors->first()->name : 'N/A' }}</p>
                                    <p class="text-sm font-bold mt-1">{{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Best Reviewed -->
                <div class="bg-white border-2 border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold uppercase tracking-wide">ĐÁNH GIÁ CAO</h3>
                        <div class="w-8 h-0.5 bg-black"></div>
                    </div>
                    <div class="space-y-4">
                        @foreach ($bestReviewedBooks->take(3) as $book)
                            @php
                                $rating = round($book->reviews->avg('rating'), 1);
                            @endphp
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="flex gap-3 p-2 hover:bg-gray-50 cursor-pointer group transition-colors">
                                <img src="{{ $book->images->first() ? asset('storage/' . $book->images->first()->image_url) : ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default.jpg')) }}"
                                     alt="{{ $book->title }}" 
                                     class="w-12 h-16 object-cover">
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm group-hover:opacity-70 transition-opacity">{{ Str::limit($book->title, 30) }}</h4>
                                    <div class="flex text-yellow-400 text-xs mt-1">
                                        @for ($i = 0; $i < 5; $i++)
                                            {{ $i < $rating ? '★' : '☆' }}
                                        @endfor
                                        <span class="text-gray-500 ml-1">({{ $rating }})</span>
                                    </div>
                                    <p class="text-sm font-bold mt-1">{{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sale Books Section -->
            <div class="mt-12 bg-red-600 text-white p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold uppercase tracking-wide">GIẢM GIÁ ĐẶC BIỆT</h3>
                            <div class="w-16 h-0.5 bg-white mt-2"></div>
                        </div>
                        <div class="text-right">
                            <span class="text-4xl font-black">SALE</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($saleBooks->take(2) as $book)
                            @php
                                $format = $book->formats->first();
                                $oldPrice = $format->price ?? 0;
                                $discount = $format->discount ?? 0;
                                $newPrice = $oldPrice - ($oldPrice * $discount / 100);
                            @endphp
                            <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                 class="flex gap-4 p-4 bg-white/10 hover:bg-white/20 cursor-pointer transition-colors">
                                <img src="{{ $book->images->first() ? asset('storage/' . $book->images->first()->image_url) : ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default.jpg')) }}"
                                     alt="{{ $book->title }}" 
                                     class="w-16 h-20 object-cover">
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm uppercase">{{ Str::limit($book->title, 25) }}</h4>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="line-through text-white/60 text-sm">{{ number_format($oldPrice, 0, ',', '.') }}₫</span>
                                        <span class="font-bold text-lg">{{ number_format($newPrice, 0, ',', '.') }}₫</span>
                                    </div>
                                    @if ($discount > 0)
                                        <span class="inline-block bg-white text-red-600 text-xs px-2 py-1 mt-1 font-bold uppercase">-{{ $discount }}%</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                    <div class="w-1 h-12 bg-black"></div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">COMBO SÁCH</h2>
                        <div class="w-16 h-0.5 bg-black mt-2"></div>
                    </div>
                </div>
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
                            <div class="absolute top-3 left-3 bg-yellow-400 text-black px-2 py-1 text-xs font-bold uppercase">COMBO</div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-4 space-y-2">
                            <h3 class="font-bold text-black text-sm uppercase tracking-wide group-hover:opacity-70 transition-opacity">
                                {{ $combo->name }}
                            </h3>
                            <p class="text-lg font-bold text-black">{{ number_format($combo->combo_price, 0, ',', '.') }}₫</p>
                            <div class="flex items-center justify-between pt-2">
                                <span class="text-xs text-gray-500 uppercase tracking-wider">{{ $combo->books->count() }} CUỐN</span>
                                <div class="w-6 h-0.5 bg-black group-hover:w-8 transition-all duration-300"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- TIN TỨC - ADIDAS STYLE -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-black"></div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">TIN TỨC</h2>
                        <div class="w-16 h-0.5 bg-black mt-2"></div>
                    </div>
                </div>
                <a href="{{ route('news.index') }}" class="bg-black text-white px-6 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors">
                    XEM TẤT CẢ
                </a>
            </div>

            <!-- Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($articles->take(3) as $article)
                    <article class="group bg-white border-2 border-gray-100 hover:border-black transition-all duration-300">
                        <!-- Image -->
                        <div class="aspect-[4/3] bg-gray-100 overflow-hidden relative">
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" 
                                 alt="{{ $article->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 bg-black text-white px-2 py-1 text-xs font-bold uppercase">
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
                    <h3 class="text-2xl font-bold uppercase tracking-wide mb-4">ĐĂNG KÝ NHẬN TIN</h3>
                    <p class="text-white/80 mb-8">Nhận thông tin mới nhất về sách và ưu đãi đặc biệt</p>
                    <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                        <input type="email" placeholder="Email của bạn"
                            class="flex-1 px-6 py-4 bg-white/10 border border-white/20 text-white placeholder-white/60 focus:outline-none focus:border-white/40">
                        <button type="submit"
                            class="bg-white text-black px-8 py-4 font-bold text-sm uppercase tracking-wider hover:bg-gray-100 transition-colors">
                            ĐĂNG KÝ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/home.js') }}"></script>
@endpush
