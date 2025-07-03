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
            <!-- Enhanced Header Section -->
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
            {{-- Enhanced Tab Content Section --}}
            @foreach ($categories as $index => $category)
                <div id="tab-tab-{{ $category->id }}" class="tab-content {{ $index === 0 ? 'block' : 'hidden' }} relative">
                    <div class="swiper categorySwiper" id="swiper-{{ $category->id }}">
                        <div class="swiper-wrapper">
                            @foreach ($category->books as $book)
                                <div class="swiper-slide pb-6">
                                    <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                        class="group bg-white border border-transparent hover:border-black rounded transition duration-300 overflow-hidden flex flex-col h-[510px]">
                                        <div class="relative aspect-[1/1.05] bg-gray-100 overflow-hidden">
                                            <img src="{{ asset('storage/images/' . $book->image) }}"
                                                alt="{{ $book->title }}">
                                            <div class="absolute top-2 right-2 z-10">
                                                <i
                                                    class="far fa-heart text-2xl text-gray-700 hover:text-red-500 cursor-pointer"></i>
                                            </div>
                                        </div>
                                        <div class="p-4 flex flex-col justify-between flex-1">
                                            <p class="text-red-500 font-bold">
                                                Giá tiền
                                                {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                                            </p>
                                            <h3 class="text-sm font-semibold mt-1">{{ $book->title }}</h3>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $category->name ?? 'Chưa có danh mục' }}
                                            </p>
                                            <a href="#"
                                                class="mt-4 inline-block bg-black text-white px-4 py-2 rounded text-sm hover:bg-gray-800 text-center w-full">
                                                Thêm vào giỏ hàng →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

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
            <div class="mt-20 pt-16 border-t border-gray-200" data-aos="fade-up" data-aos-delay="600">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-red-500 transition-colors duration-300"
                            data-target="1000">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            KHÁCH HÀNG</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
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
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-pink-500 transition-colors duration-300"
                            data-target="48">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            GIỜ GIAO HÀNG</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                    <div class="space-y-2 group cursor-pointer">
                        <div class="text-3xl md:text-4xl font-black text-black counter-animate group-hover:text-blue-500 transition-colors duration-300"
                            data-target="100">0</div>
                        <div
                            class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold group-hover:text-gray-700 transition-colors duration-300">
                            % CHẤT LƯỢNG</div>
                        <div
                            class="w-8 h-0.5 bg-black mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-4 py-16 max-w-screen-xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex gap-2 flex-wrap text-base font-semibold uppercase">
                @foreach ($categories as $index => $category)
                    <button
                        class="tab-button px-6 py-2 rounded-full border-b-2 transition-all duration-200
                        {{ $index === 0 ? 'border-black text-black font-bold' : 'border-transparent text-gray-500 hover:text-black hover:border-black' }}"
                        data-tab="tab-{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
            <a href="#"
                class="text-base font-semibold uppercase border-b-2 border-black hover:opacity-70 transition">Xem tất
                cả</a>
        </div>
        @foreach ($categories as $index => $category)
            <div id="tab-tab-{{ $category->id }}" class="tab-content {{ $index === 0 ? 'block' : 'hidden' }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                    @foreach ($category->books as $book)
                        @php
                            $format = $book->formats->first();
                            $price = $format->price ?? $book->price;
                            $discount = $format->discount ?? 0;
                            $finalPrice = $discount > 0 ? $price - ($price * $discount) / 100 : $price;
                        @endphp
                        <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                            class="cursor-pointer flex flex-col bg-white group transition-all duration-200">
                            <div class="aspect-[1/1] bg-gray-100 flex items-center justify-center overflow-hidden">
                                @php
                                    $img = $book->image ?? '';
                                    $imgPath = public_path('storage/images/' . $img);
                                @endphp
                                <img src="{{ $img && file_exists($imgPath) ? asset('storage/images/' . $img) : asset('images/product-item1.png') }}"
                                    alt="{{ $book->title }}"
                                    class="object-contain w-full h-full transition duration-300 group-hover:scale-105 group-hover:brightness-105" />
                            </div>
                            <div class="py-4 px-2 flex flex-col gap-1 text-left">
                                <span class="font-bold text-lg text-black">
                                    @if ($discount > 0)
                                        <span
                                            class="text-gray-400 line-through mr-2">{{ number_format($price, 0, ',', '.') }}₫</span>
                                        <span class="text-red-600">{{ number_format($finalPrice, 0, ',', '.') }}₫</span>
                                    @else
                                        {{ number_format($price, 0, ',', '.') }}₫
                                    @endif
                                </span>
                                <span class="text-base text-black font-semibold mt-1">{{ $book->title }}</span>
                                <span class="text-sm text-gray-500">{{ $category->name ?? 'Chưa có danh mục' }}</span>
                                @if ($discount > 0)
                                    <span class="text-xs text-red-500 font-semibold mt-1">-{{ $discount }}% Giảm
                                        giá</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <section class="w-full bg-white py-20 md:py-32">
        <div class="grid grid-cols-1 md:grid-cols-2 items-center px-6 md:px-10 gap-16 max-w-screen-xl mx-auto">
            <!-- Ảnh sách bên trái -->
            <div class="flex justify-center">
                <div class="bg-gray-100 p-8 rounded-lg">
                    <img src="{{ asset('storage/images/banner-image3.png') }}" class="h-80 md:h-96 object-contain"
                        alt="Sách đặc biệt">
                </div>
            </div>

            <!-- Nội dung bên phải -->
            <div class="text-left space-y-6 text-black">
                <h2 class="text-4xl md:text-5xl font-bold leading-tight">
                    Thời gian đọc sách
                </h2>
                <p class="text-lg md:text-xl text-gray-600">
                    Mỗi giây trôi qua là một cơ hội để khám phá tri thức mới
                </p>

                <!-- Hiển thị thời gian -->
                <div class="space-y-2">
                    <div class="text-3xl md:text-4xl font-mono font-bold text-black" id="clock-time">00:00:00</div>
                    <div class="text-base text-gray-500" id="clock-date">Thứ..., 00/00/0000</div>
                </div>

                <a href="#"
                    class="inline-block bg-black text-white px-8 py-3 text-sm font-semibold hover:bg-gray-800 transition duration-300 uppercase tracking-wide">
                    Mua ngay
                </a>
            </div>
        </div>
    </section>


    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header Section -->
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-black mb-4">KHÁM PHÁ BỘ SƯU TẬP</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Tuyển chọn những cuốn sách hay nhất từ các thể loại khác
                    nhau</p>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Featured Books - Large Card -->
                <div class="lg:col-span-6">
                    <div class="bg-black text-white rounded-none overflow-hidden h-full min-h-[600px] relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent z-10"></div>
                        <img src="{{ $featuredBooks->first()?->images->first() ? asset('storage/' . $featuredBooks->first()->images->first()->image_url) : asset('storage/default.jpg') }}"
                            alt="Featured"
                            class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-80 transition-opacity duration-500">

                        <div class="relative z-20 p-8 flex flex-col justify-end h-full">
                            <div class="mb-6">
                                <span class="text-white/80 text-sm font-medium tracking-wider uppercase">NỔI BẬT</span>
                                <h3 class="text-3xl md:text-4xl font-bold mt-2 mb-4 leading-tight">
                                    {{ $featuredBooks->first()?->title ?? 'Sách nổi bật' }}
                                </h3>
                                <p class="text-white/90 mb-6">{{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}
                                </p>
                                <p class="text-2xl font-bold text-white">
                                    {{ number_format($featuredBooks->first()?->formats->first()?->price ?? 0, 0, ',', '.') }}₫
                                </p>
                            </div>

                            <button
                                onclick="window.location='{{ route('books.show', ['slug' => $featuredBooks->first()?->slug ?? '#']) }}'"
                                class="bg-white text-black px-8 py-4 font-bold text-sm tracking-wider uppercase hover:bg-gray-100 transition-colors duration-300 w-fit">
                                KHÁM PHÁ NGAY
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-6 space-y-8">

                    <!-- Top Row - Latest & Best Reviewed -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Latest Books -->
                        <div class="bg-white p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-black uppercase tracking-wide">MỚI NHẤT</h3>
                                <div class="w-8 h-0.5 bg-black"></div>
                            </div>

                            <div class="space-y-4">
                                @foreach ($latestBooks->take(3) as $book)
                                    <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                        class="flex items-center gap-4 p-3 hover:bg-gray-50 transition-colors duration-200 cursor-pointer group">
                                        <img src="{{ asset('storage/' . ($book->images->first()->image_url ?? 'default.jpg')) }}"
                                            alt="{{ $book->title }}" class="w-12 h-16 object-cover">
                                        <div class="flex-1">
                                            <h4
                                                class="font-semibold text-sm text-black group-hover:text-gray-600 transition-colors">
                                                {{ Str::limit($book->title, 40) }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1">{{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}
                                            </p>
                                            <p class="text-sm font-bold text-black mt-1">
                                                {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Best Reviewed -->
                        <div class="bg-white p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-black uppercase tracking-wide">ĐÁNH GIÁ CAO</h3>
                                <div class="w-8 h-0.5 bg-black"></div>
                            </div>

                            <div class="space-y-4">
                                @foreach ($bestReviewedBooks->take(3) as $book)
                                    @php
                                        $rating = round($book->reviews->avg('rating'), 1);
                                    @endphp
                                    <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                        class="flex items-center gap-4 p-3 hover:bg-gray-50 transition-colors duration-200 cursor-pointer group">
                                        <img src="{{ asset('storage/' . ($book->images->first()->image_url ?? 'default.jpg')) }}"
                                            alt="{{ $book->title }}" class="w-12 h-16 object-cover">
                                        <div class="flex-1">
                                            <h4
                                                class="font-semibold text-sm text-black group-hover:text-gray-600 transition-colors">
                                                {{ Str::limit($book->title, 40) }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1">{{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="flex text-yellow-400 text-xs">
                                                    @for ($i = 0; $i < 5; $i++)
                                                        @if ($i < $rating)
                                                            ★
                                                        @else
                                                            ☆
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-500">({{ $rating }})</span>
                                            </div>
                                            <p class="text-sm font-bold text-black mt-1">
                                                {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Bottom - Sale Books -->
                    <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white p-8 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12">
                        </div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-2xl font-bold uppercase tracking-wide">GIẢM GIÁ ĐẶC BIỆT</h3>
                                    <p class="text-white/90 mt-1">Tiết kiệm đến 50% cho sách chọn lọc</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-4xl font-bold">50%</span>
                                    <p class="text-sm">OFF</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($saleBooks->take(2) as $book)
                                    @php
                                        $format = $book->formats->first();
                                        $oldPrice = $format->price ?? 0;
                                        $discount = $format->discount ?? 0;
                                        $newPrice = $oldPrice - $oldPrice * ($discount / 100);
                                    @endphp
                                    <div onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'"
                                        class="flex items-center gap-4 p-4 bg-white/10 backdrop-blur-sm rounded hover:bg-white/20 transition-colors duration-200 cursor-pointer">
                                        <img src="{{ asset('storage/' . ($book->images->first()->image_url ?? 'default.jpg')) }}"
                                            alt="{{ $book->title }}" class="w-16 h-20 object-cover">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-sm mb-1">{{ Str::limit($book->title, 30) }}</h4>
                                            <p class="text-xs text-white/80 mb-2">{{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <span class="line-through text-white/60 text-sm">
                                                    {{ number_format($oldPrice, 0, ',', '.') }}₫
                                                </span>
                                                <span class="text-white font-bold text-lg">
                                                    {{ number_format($newPrice, 0, ',', '.') }}₫
                                                </span>
                                            </div>
                                            @if ($discount > 0)
                                                <span
                                                    class="inline-block bg-white text-red-500 text-xs px-2 py-1 rounded mt-1 font-bold">
                                                    -{{ $discount }}%
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button
                                class="mt-6 bg-white text-red-500 px-8 py-3 font-bold text-sm tracking-wider uppercase hover:bg-gray-100 transition-colors duration-300">
                                XEM TẤT CẢ GIẢM GIÁ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom CTA -->
            <div class="text-center mt-16">
                <button
                    class="bg-black text-white px-12 py-4 font-bold text-sm tracking-wider uppercase hover:bg-gray-800 transition-colors duration-300">
                    XEM TẤT CẢ SẢN PHẨM
                </button>
            </div>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header Section -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-4xl md:text-5xl font-bold text-black uppercase tracking-tight mb-2">
                            SẢN PHẨM MỚI NHẤT
                        </h2>
                        <div class="w-20 h-1 bg-black"></div>
                    </div>
                    <a href="#"
                        class="group flex items-center gap-2 text-black font-bold text-sm uppercase tracking-wider hover:opacity-70 transition-opacity">
                        Xem tất cả
                        <span class="transform group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
                <p class="text-lg text-gray-600 max-w-xl">
                    Khám phá những cuốn sách mới nhất được tuyển chọn đặc biệt cho bạn
                </p>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($books as $book)
                    <div class="group cursor-pointer"
                        onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'">
                        <!-- Product Image -->
                        <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden mb-4">
                            <img src="{{ asset('storage/images/' . $book->image) }}"
                                class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105"
                                alt="{{ $book->title }}">

                            <!-- Overlay Actions -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300">
                                <div
                                    class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <button
                                        class="bg-white/90 hover:bg-white text-black p-3 rounded-full shadow-lg backdrop-blur-sm transition-all duration-200 hover:scale-110">
                                        <i class="far fa-heart text-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Quick Add to Cart -->
                            <div
                                class="absolute bottom-0 left-0 right-0 bg-black text-white py-3 px-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                <button
                                    class="w-full text-center font-bold text-sm uppercase tracking-wider hover:opacity-80 transition-opacity">
                                    Thêm vào giỏ hàng
                                </button>
                            </div>

                            <!-- New Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="bg-black text-white px-3 py-1 text-xs font-bold uppercase tracking-wide">
                                    Mới
                                </span>
                            </div>
                        </div>
                        <div class="p-4 bg-white flex flex-col flex-1 justify-between h-[180px]">
                            <h3 class="text-base font-semibold text-gray-800">{{ $book->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}
                            <p class="text-red-500 font-bold">
                                Giá tiền {{ number_format($book->formats->first()->price ?? 0, 0, ',', '.') }}₫
                            </p>
                            <a href="#"
                                class="mt-4 inline-block bg-black text-white px-4 py-2 rounded text-sm hover:bg-gray-800 text-center w-full">
                                Thêm vào giỏ hàng →
                            </a>
                        </div>

                        <!-- Product Info -->
                        <div class="space-y-2">
                            <h3
                                class="font-bold text-lg text-black group-hover:opacity-70 transition-opacity line-clamp-2">
                                {{ $book->title }}
                            </h3>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">
                                {{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}
                            </p>
                            <div class="flex items-center justify-between">
                                <p class="text-xl font-bold text-black">
                                    {{ number_format($book->price, 0, ',', '.') }}₫
                                </p>
                                <div class="flex items-center text-yellow-400 text-sm">
                                    <span class="mr-1">★</span>
                                    <span class="text-gray-500">4.5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-book text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Chưa có sản phẩm nào</h3>
                        <p class="text-gray-500">Hãy quay lại sau để khám phá những cuốn sách mới nhất</p>
                    </div>
                @endforelse
            </div>

            <!-- Load More Button -->
            @if ($books->count() >= 8)
                <div class="text-center mt-16">
                    <button
                        class="bg-black text-white px-12 py-4 font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors duration-300 group">
                        Xem thêm sản phẩm
                        <span class="ml-2 transform group-hover:translate-x-1 transition-transform">→</span>
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="py-20 bg-black text-white relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-48 translate-x-48"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/5 rounded-full translate-y-36 -translate-x-36"></div>
            <div
                class="absolute top-1/2 left-1/2 w-2 h-32 bg-white/10 transform -translate-x-1/2 -translate-y-1/2 rotate-45">
            </div>
        @endif
    </div>
</section>

<!-- SÁCH SẮP RA MẮT Section -->
<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header Section -->
        <div class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-black uppercase tracking-tight mb-2">
                        SÁCH SẮP RA MẮT
                    </h2>
                    <div class="w-20 h-1 bg-black"></div>
                </div>
                <a href="#" 
                   class="group flex items-center gap-2 text-black font-bold text-sm uppercase tracking-wider hover:opacity-70 transition-opacity">
                    Xem tất cả
                    <span class="transform group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>
            <p class="text-lg text-gray-600 max-w-xl">
                Khám phá những cuốn sách mới nhất sắp được phát hành
            </p>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($upcomingBooks as $book)
                <div class="group cursor-pointer" onclick="window.location='{{ route('books.show', ['slug' => $book->slug]) }}'">
                    <!-- Product Image -->
                    <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden mb-4">
                        <img src="{{asset('storage/images/' . $book->image)}}"
                             class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105"
                             alt="{{$book->title}}">
                        
                        <!-- Overlay Actions -->
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300">
                            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button class="bg-white/90 hover:bg-white text-black p-3 rounded-full shadow-lg backdrop-blur-sm transition-all duration-200 hover:scale-110">
                                    <i class="far fa-heart text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Quick Pre-order -->
                        <div class="absolute bottom-0 left-0 right-0 bg-black text-white py-3 px-4 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            <button onclick="event.stopPropagation(); preOrderFromHome('{{ $book->id }}', '{{ $book->title }}', '{{ $book->slug }}')" 
                                    class="w-full text-center font-bold text-sm uppercase tracking-wider hover:opacity-80 transition-opacity">
                                Đặt sách trước
                            </button>
                        </div>

                        <!-- New Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="bg-black text-white px-3 py-1 text-xs font-bold uppercase tracking-wide">
                                Sắp có
                            </span>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-2">
                        <h3 class="font-bold text-lg text-black group-hover:opacity-70 transition-opacity line-clamp-2">
                            {{$book->title}}
                        </h3>
                        <p class="text-sm text-gray-500 uppercase tracking-wide">
                            {{$book->author?->name ?? 'Không rõ'}}
                        </p>
                        @if($book->release_date)
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-r-lg p-2">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Ra mắt</p>
                                        <p class="text-sm text-blue-800 font-bold">{{ $book->release_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <p class="text-xl font-bold text-black">
                                {{number_format($book->formats->first()->price ?? 0, 0, ',', '.')}}₫
                            </p>
                            <div class="flex items-center text-yellow-400 text-sm">
                                <span class="mr-1">★</span>
                                <span class="text-gray-500">4.5</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-book text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Chưa có sách sắp ra mắt</h3>
                    <p class="text-gray-500">Hãy quay lại sau để cập nhật những cuốn sách mới nhất</p>
                </div>
            @endforelse
        </div>

        <!-- Load More Button -->
        @if($upcomingBooks->count() >= 8)
            <div class="text-center mt-16">
                <button class="bg-black text-white px-12 py-4 font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors duration-300 group">
                    Xem thêm sản phẩm
                    <span class="ml-2 transform group-hover:translate-x-1 transition-transform">→</span>
                </button>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/home.js') }}"></script>
<script>
    // Pre-order from home page
    function preOrderFromHome(bookId, bookTitle, bookSlug) {
        // Check if user is logged in
        @auth
            // Redirect to book detail page with preorder hash for auto-open modal
            window.location.href = `/books/${bookSlug}#preorder`;
        @else
            // Show login prompt
            if (typeof toastr !== 'undefined') {
                toastr.warning('Bạn cần đăng nhập để đặt sách trước', 'Chưa đăng nhập!', {
                    timeOut: 3000,
                    positionClass: 'toast-top-right',
                    closeButton: true,
                    progressBar: true
                });
            } else {
                alert('Bạn cần đăng nhập để đặt sách trước');
            }
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
        @endauth
    }
</script>
@endpush

