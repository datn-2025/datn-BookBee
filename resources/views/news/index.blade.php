@extends('layouts.app')
@section('title', 'Tin tức & Sự kiện')

@push('styles')
<style>
    /* Clean Adidas-style design với UX enhancements */
    .news-page {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    /* Smooth page loading animation */
    .fade-in {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Staggered animation delays */
    .fade-in-delay-1 { animation-delay: 0.1s; }
    .fade-in-delay-2 { animation-delay: 0.2s; }
    .fade-in-delay-3 { animation-delay: 0.3s; }
    .fade-in-delay-4 { animation-delay: 0.4s; }
    
    /* Loading skeleton */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .news-hero {
        position: relative;
        overflow: hidden;
        background: #fff;
        border-bottom: 2px solid #000;
    }
    
    .news-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100%;
        background: #000;
        opacity: 0.05;
        transform: skewX(-15deg);
    }
    
    /* Enhanced parallax effect */
    .parallax-bg {
        will-change: transform;
        transition: transform 0.1s ease-out;
    }
    
    .featured-badge {
        background: #000;
        color: #fff;
        padding: 8px 16px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.75rem;
        position: relative;
        overflow: hidden;
    }
    
    .featured-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }
    
    .featured-badge:hover::before {
        left: 100%;
    }
    
    /* Enhanced news cards */
    .news-card {
        background: #fff;
        border: 2px solid #f5f5f5;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .news-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: #000;
        transition: left 0.3s ease;
    }
    
    .news-card:hover::before {
        left: 0;
    }
    
    .news-card:hover {
        border-color: #000;
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    /* Image hover effects */
    .news-card .image-container {
        position: relative;
        overflow: hidden;
    }
    
    .news-card .image-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.4);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .news-card:hover .image-overlay {
        opacity: 1;
    }
    
    /* Enhanced category tags */
    .category-tag {
        background: #000;
        color: #fff;
        padding: 6px 14px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 0.7rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .category-tag:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    /* Interactive read more button */
    .read-more-btn {
        background: #000;
        color: #fff;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        border: none;
        transition: all 0.3s ease;
    }
    
    .read-more-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .read-more-btn:hover::before {
        left: 100%;
    }
    
    .read-more-btn:hover {
        background: #333;
        transform: translateX(4px) scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    /* Interactive section divider */
    .section-divider {
        width: 60px;
        height: 4px;
        background: #000;
        margin: 0 auto;
        transition: width 0.4s ease;
    }
    
    .section-divider:hover {
        width: 100px;
    }
    
    /* Enhanced pagination */
    .pagination-btn {
        background: #fff;
        border: 2px solid #000;
        color: #000;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }
    
    .pagination-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: #000;
        transition: left 0.3s ease;
        z-index: -1;
    }
    
    .pagination-btn:hover::before {
        left: 0;
    }
    
    .pagination-btn:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    
    .pagination-btn.active {
        background: #000;
        color: #fff;
        transform: scale(1.1);
    }
    
    /* Enhanced sidebar cards */
    .sidebar-card {
        background: #fff;
        border: 2px solid #f5f5f5;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .sidebar-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background: #000;
        transition: width 0.4s ease;
    }
    
    .sidebar-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }
    
    .sidebar-card:hover::after {
        width: 100%;
    }
    
    /* Progress reading bar */
    .reading-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #000, #333);
        z-index: 1000;
        transition: width 0.1s ease;
    }
    
    /* Back to top button */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: #000;
        color: #fff;
        border: none;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .back-to-top:hover {
        background: #333;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    
    /* Search suggestion dropdown */
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #000;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
    
    .search-suggestions.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .search-suggestion-item {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s ease;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
    
    .search-suggestion-item:hover {
        background: #f8f8f8;
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Loading states */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .loading-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f0f0f0;
        border-top: 3px solid #000;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Tooltip */
    .tooltip {
        position: relative;
        display: inline-block;
    }
    
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #000;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
    
    /* Filter buttons */
    .filter-btn {
        background: #fff;
        border: 2px solid #000;
        color: #000;
        padding: 8px 16px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: #000;
        transition: left 0.3s ease;
        z-index: -1;
    }
    
    .filter-btn:hover::before,
    .filter-btn.active::before {
        left: 0;
    }
    
    .filter-btn:hover,
    .filter-btn.active {
        color: #fff;
        transform: translateY(-2px);
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .news-card:hover {
            transform: translateY(-4px) scale(1.01);
        }
        
        .back-to-top {
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
        }
    }
</style>
@endpush

@section('content')
<div class="news-page">
    <!-- Reading Progress Bar -->
    <div class="reading-progress"></div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" title="Về đầu trang">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Hero Banner Section -->
    <section class="news-hero bg-white py-20 md:py-32 relative overflow-hidden fade-in">
        <!-- Background Elements - Adidas Style -->
        <div class="absolute inset-0 pointer-events-none parallax-bg">
            <div class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 transform rotate-45 translate-x-36 -translate-y-36"></div>
            <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
            <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
        </div>

        @if($featuredNews = $news->firstWhere('is_featured', true))
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-10">
                    {{-- Left text - Adidas Typography Style --}}
                    <div class="space-y-8 text-gray-900">
                        <!-- Pre-title với Adidas style -->
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-8 h-0.5 bg-black"></div>
                            <span class="featured-badge text-xs uppercase tracking-[0.2em]">
                                TIN NỔI BẬT
                            </span>
                        </div>

                        <!-- Main headline - Bold Adidas typography -->
                        <h1 class="text-4xl md:text-6xl font-black uppercase leading-[0.9] tracking-tight text-black">
                            <span class="block">TIN TỨC</span>
                            <span class="block text-gray-400">&</span>
                            <span class="block">SỰ KIỆN</span>
                        </h1>

                        <!-- Subtitle -->
                        <div class="space-y-4">
                            <p class="text-lg md:text-xl font-medium text-gray-700 max-w-lg">
                                {{ $featuredNews->title }}
                            </p>

                            <!-- Meta info - Clean Adidas style -->
                            <div class="flex items-center gap-4">
                                <div class="flex items-center text-gray-600">
                                    <div class="w-2 h-2 bg-black mr-2"></div>
                                    {{ $featuredNews->created_at->format('d M Y') }}
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <div class="w-2 h-2 bg-black mr-2"></div>
                                    5 phút đọc
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button - Adidas style -->
                        <div class="pt-4">
                            <a href="{{ route('news.show', $featuredNews->id) }}"
                                class="read-more-btn group inline-flex items-center px-8 py-4 font-bold text-sm uppercase tracking-[0.1em] hover:shadow-lg transition-all duration-300">
                                <span>ĐỌC NGAY</span>
                                <div class="w-4 h-0.5 bg-white ml-3 transform group-hover:w-8 transition-all duration-300"></div>
                            </a>
                        </div>
                    </div>

                    {{-- Right image - Clean presentation --}}
                    <div class="flex justify-center">
                        <div class="relative group">
                            <!-- Main image với clean style -->
                            <div class="relative">
                                <img src="{{ $featuredNews->thumbnail ?? '/images/news-default.jpg' }}"
                                    class="h-80 md:h-96 w-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                                    alt="{{ $featuredNews->title }}">

                                <!-- Clean badge thay vì rounded -->
                                <div class="absolute -top-6 -left-6 bg-black text-white px-6 py-3 transform group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform duration-500">
                                    <div class="text-center">
                                        <div class="text-sm font-bold uppercase tracking-wide">NEW</div>
                                        <div class="text-xs uppercase tracking-wider text-gray-300">Article</div>
                                    </div>
                                </div>

                                <!-- Minimal accent -->
                                <div class="absolute -bottom-4 -right-4 bg-white border-2 border-black px-4 py-2 transform group-hover:translate-x-1 group-hover:translate-y-1 transition-transform duration-500">
                                    <span class="text-xs font-bold uppercase tracking-wide text-black">Featured</span>
                                </div>
                            </div>

                            <!-- Background geometric shape -->
                            <div class="absolute inset-0 -z-10 bg-gray-100 transform translate-x-4 translate-y-4 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-700"></div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Fallback khi không có featured news -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="w-8 h-0.5 bg-black"></div>
                    <span class="featured-badge text-xs uppercase tracking-[0.2em]">
                        BOOKBEE NEWS
                    </span>
                    <div class="w-8 h-0.5 bg-black"></div>
                </div>
                
                <h1 class="text-4xl md:text-6xl font-black uppercase leading-[0.9] tracking-tight text-black mb-8">
                    <span class="block">TIN TỨC</span>
                    <span class="block text-gray-400">&</span>
                    <span class="block">SỰ KIỆN</span>
                </h1>
                
                <p class="text-lg md:text-xl font-medium text-gray-700 max-w-2xl mx-auto">
                    Khám phá những câu chuyện thú vị và cập nhật mới nhất từ thế giới sách
                </p>
            </div>
        @endif
    </section>

<!-- Main Content Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col xl:flex-row gap-12">
        <!-- News Grid -->
        <div class="xl:w-2/3">
            <!-- Section Header - Adidas Style -->
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="w-8 h-0.5 bg-black"></div>
                    <span class="featured-badge text-xs uppercase tracking-[0.2em]">
                        TIN TỨC MỚI NHẤT
                    </span>
                    <div class="w-8 h-0.5 bg-black"></div>
                </div>
                
                <h2 class="text-3xl md:text-4xl font-black uppercase text-black mb-4 tracking-tight">
                    BÀI VIẾT NỔI BẬT
                </h2>
                
                <div class="section-divider mb-6"></div>
                
                <p class="text-lg text-gray-600 max-w-2xl mx-auto font-medium mb-8">
                    Khám phá những câu chuyện thú vị và cập nhật mới nhất từ thế giới sách
                </p>
                
                <!-- Search and Filter Section -->
                <div class="max-w-md mx-auto mb-8">
                    <div class="relative">
                        <input type="text" 
                               id="newsSearch" 
                               placeholder="Tìm kiếm tin tức..."
                               class="w-full px-4 py-3 pr-12 border-2 border-gray-300 focus:border-black focus:outline-none text-sm font-medium uppercase tracking-wide">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <!-- Search suggestions dropdown -->
                        <div class="search-suggestions" id="searchSuggestions">
                            <div class="search-suggestion-item">Sách mới</div>
                            <div class="search-suggestion-item">Tác giả nổi tiếng</div>
                            <div class="search-suggestion-item">Khuyến mãi</div>
                        </div>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="flex flex-wrap justify-center gap-2 mb-8">
                    <button class="filter-btn active" data-category="all">TẤT CẢ</button>
                    <button class="filter-btn" data-category="sach-moi">SÁCH MỚI</button>
                    <button class="filter-btn" data-category="khuyen-mai">KHUYẾN MÃI</button>
                    <button class="filter-btn" data-category="su-kien">SỰ KIỆN</button>
                </div>
            </div>

            <!-- News Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                @foreach($news->where('is_featured', false) as $index => $item)
                <article class="news-card group fade-in fade-in-delay-{{ ($index % 4) + 1 }}">
                    <!-- Image Container -->
                    <div class="image-container relative overflow-hidden">
                        <a href="{{ route('news.show', $item->id) }}" class="block">
                            <img src="{{ $item->thumbnail ?? '/images/news-default.jpg' }}"
                                 alt="{{ $item->title }}"
                                 class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110">
                        </a>
                        <!-- Image Overlay -->
                        <div class="image-overlay">
                            <span>ĐỌC NGAY</span>
                        </div>
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="category-tag">{{ $item->category ?? 'TIN TỨC' }}</span>
                        </div>
                        <!-- Reading Time Badge -->
                        <div class="absolute top-4 right-4">
                            <div class="tooltip">
                                <span class="bg-black text-white px-2 py-1 text-xs font-bold">5 MIN</span>
                                <span class="tooltiptext">Thời gian đọc ước tính</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center text-gray-500 text-sm font-medium">
                                <div class="w-2 h-2 bg-black mr-2"></div>
                                {{ $item->created_at->format('d/m/Y') }}
                            </div>
                            <div class="flex items-center text-gray-500 text-sm font-medium">
                                <div class="w-2 h-2 bg-black mr-2"></div>
                                5 phút đọc
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="mb-4">
                            <a href="{{ route('news.show', $item->id) }}"
                               class="text-xl lg:text-2xl font-bold text-black hover:text-gray-600 transition-colors duration-300 line-clamp-2 leading-tight uppercase tracking-wide">
                                {{ $item->title }}
                            </a>
                        </h3>
                        
                        <!-- Summary -->
                        <p class="text-gray-600 line-clamp-3 mb-6 leading-relaxed">{{ $item->summary }}</p>
                        
                        <!-- Read More -->
                        <div class="flex items-center justify-between">
                            <a href="{{ route('news.show', $item->id) }}" 
                               class="inline-flex items-center text-black font-bold hover:text-gray-600 transition-colors duration-200 uppercase text-sm tracking-wide">
                                ĐỌC TIẾP
                                <div class="w-4 h-0.5 bg-black ml-3 transform group-hover:w-8 transition-all duration-300"></div>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Pagination - Adidas Style -->
            <div class="flex justify-center items-center space-x-2 py-8">
                {{-- Previous Button --}}
                @if ($news->onFirstPage())
                    <span class="pagination-btn px-4 py-2 cursor-not-allowed opacity-50">
                        TRƯỚC
                    </span>
                @else
                    <a href="{{ $news->previousPageUrl() }}" 
                       class="pagination-btn px-4 py-2 hover:shadow-lg">
                        TRƯỚC
                    </a>
                @endif

                {{-- Page Numbers --}}
                <div class="flex items-center space-x-1">
                    @foreach ($news->getUrlRange(1, $news->lastPage()) as $page => $url)
                        @if ($page == $news->currentPage())
                            <span class="pagination-btn active px-4 py-2 shadow-md">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="pagination-btn px-4 py-2 hover:shadow-lg">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Button --}}
                @if ($news->hasMorePages())
                    <a href="{{ $news->nextPageUrl() }}" 
                       class="pagination-btn px-4 py-2 hover:shadow-lg">
                        SAU
                    </a>
                @else
                    <span class="pagination-btn px-4 py-2 cursor-not-allowed opacity-50">
                        SAU
                    </span>
                @endif
            </div>
        </div>

        <!-- Sidebar - Adidas Style -->
        <div class="lg:w-1/3">
            <!-- Featured News Section -->
            <div class="sidebar-card p-6 mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-4 h-0.5 bg-black"></div>
                    <h3 class="text-lg font-bold text-black uppercase tracking-wide">TIN NỔI BẬT</h3>
                </div>
                
                @foreach($news->where('is_featured', true)->take(3) as $featured)
                <div class="mb-6 last:mb-0">
                    <a href="{{ route('news.show', $featured->id) }}" class="group block">
                        <div class="overflow-hidden mb-3">
                            <img src="{{ $featured->thumbnail ?? '/images/news-default.jpg' }}"
                                 alt="{{ $featured->title }}"
                                 class="w-full h-32 object-cover transform transition duration-500 group-hover:scale-110">
                        </div>
                        <h4 class="font-bold text-black group-hover:text-gray-600 transition-colors duration-200 line-clamp-2 uppercase text-sm tracking-wide">
                            {{ $featured->title }}
                        </h4>
                        <div class="flex items-center text-gray-500 text-xs mt-2 font-medium">
                            <div class="w-1 h-1 bg-black mr-2"></div>
                            {{ $featured->created_at->format('d M Y') }}
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <!-- Latest News Section -->
            <div class="sidebar-card p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-4 h-0.5 bg-black"></div>
                    <h3 class="text-lg font-bold text-black uppercase tracking-wide">CẬP NHẬT MỚI</h3>
                </div>
                
                @foreach($news->sortByDesc('created_at')->take(5) as $latest)
                <div class="flex items-center space-x-4 mb-6 last:mb-0 group">
                    <div class="flex-shrink-0 w-16 h-16">
                        <img src="{{ $latest->thumbnail ?? '/images/news-default.jpg' }}"
                             alt="{{ $latest->title }}"
                             class="w-full h-full object-cover">
                    </div>
                    <div>
                        <a href="{{ route('news.show', $latest->id) }}" 
                           class="font-bold text-black hover:text-gray-600 transition-colors duration-200 line-clamp-2 text-sm uppercase tracking-wide">
                            {{ $latest->title }}
                        </a>
                        <div class="flex items-center text-gray-500 text-xs mt-1 font-medium">
                            <div class="w-1 h-1 bg-black mr-2"></div>
                            {{ $latest->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reading progress bar
    const progressBar = document.querySelector('.reading-progress');
    
    function updateProgress() {
        const scrollTop = window.pageYOffset;
        const docHeight = document.body.scrollHeight - window.innerHeight;
        const progress = scrollTop / docHeight;
        progressBar.style.width = progress * 100 + '%';
    }
    
    window.addEventListener('scroll', updateProgress);
    
    // Back to top button
    const backToTopButton = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });
    
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Parallax effect for background elements
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        parallaxElements.forEach(function(element) {
            element.style.transform = `translateY(${rate}px)`;
        });
    });
    
    // Loading overlay (simulate loading)
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Show loading briefly on page load
    loadingOverlay.classList.add('active');
    setTimeout(() => {
        loadingOverlay.classList.remove('active');
    }, 1000);
    
    // Card hover effects with sound feedback (visual only)
    const newsCards = document.querySelectorAll('.news-card');
    
    newsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
    
    // Enhanced click effects for pagination
    const paginationBtns = document.querySelectorAll('.pagination-btn');
    
    paginationBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Smooth section divider hover effect
    const sectionDivider = document.querySelector('.section-divider');
    if (sectionDivider) {
        sectionDivider.addEventListener('mouseenter', function() {
            this.style.width = '100px';
        });
        
        sectionDivider.addEventListener('mouseleave', function() {
            this.style.width = '60px';
        });
    }
    
    // Enhanced image loading with fade-in
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        if (img.complete) {
            img.style.opacity = '1';
        } else {
            img.style.opacity = '0';
            img.addEventListener('load', function() {
                this.style.transition = 'opacity 0.3s ease';
                this.style.opacity = '1';
            });
        }
    });
    
    // Page transition loading
    const links = document.querySelectorAll('a[href^="/"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.target || this.target === '_self') {
                loadingOverlay.classList.add('active');
            }
        });
    });
    
    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(e) {
        // ESC key to close any overlays
        if (e.key === 'Escape') {
            loadingOverlay.classList.remove('active');
        }
        
        // Space or Enter to scroll down
        if (e.key === ' ' || e.key === 'Enter') {
            if (e.target === document.body) {
                e.preventDefault();
                window.scrollBy(0, window.innerHeight * 0.8);
            }
        }
    });
    
    // Touch support for mobile
    let touchStartY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', function(e) {
        const touchEndY = e.changedTouches[0].clientY;
        const diff = touchStartY - touchEndY;
        
        // Swipe up gesture - scroll to top
        if (diff > 100 && window.pageYOffset > 300) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    });
    
    // Search functionality
    const searchInput = document.getElementById('newsSearch');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const newsCards = document.querySelectorAll('.news-card');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // Filter news cards
            newsCards.forEach(card => {
                const title = card.querySelector('h3 a').textContent.toLowerCase();
                const summary = card.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || summary.includes(searchTerm) || searchTerm === '') {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show/hide suggestions
            if (searchTerm.length > 0) {
                searchSuggestions.classList.add('active');
            } else {
                searchSuggestions.classList.remove('active');
            }
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.classList.remove('active');
            }
        });
    }
    
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const category = this.dataset.category;
            
            // Filter news cards based on category
            newsCards.forEach(card => {
                const cardCategory = card.querySelector('.category-tag').textContent.toLowerCase();
                
                if (category === 'all' || cardCategory.includes(category.replace('-', ' '))) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // Search suggestions click
    const suggestionItems = document.querySelectorAll('.search-suggestion-item');
    suggestionItems.forEach(item => {
        item.addEventListener('click', function() {
            searchInput.value = this.textContent;
            searchSuggestions.classList.remove('active');
            // Trigger search
            searchInput.dispatchEvent(new Event('input'));
        });
    });
    
    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush
