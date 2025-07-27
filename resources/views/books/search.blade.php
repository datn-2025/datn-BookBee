@extends('layouts.app')
@section('title', 'Tìm kiếm sách')

@push('styles')
<!-- Search Page CSS -->
<link rel="stylesheet" href="{{ asset('css/search.css') }}">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'adidas-black': '#000000',
                    'adidas-white': '#ffffff',
                    'adidas-gray': '#6b7280',
                    'adidas-light-gray': '#f3f4f6',
                },
                fontFamily: {
                    'adidas': ['Roboto', 'Arial', 'sans-serif'],
                },
                animation: {
                    'slide-in': 'slideIn 0.3s ease-out',
                    'fade-in': 'fadeIn 0.2s ease-in',
                }
            }
        }
    }
</script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');
    
    * {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    @keyframes slideIn {
        from { transform: translateX(-20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .search-page {
        background: #ffffff;
        min-height: 100vh;
    }
    
    /* Adidas Style Search Tags */
    .search-tag {
        background: #000000;
        color: white;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        display: inline-block;
        margin: 0.25rem 0.25rem 0.25rem 0;
        position: relative;
        padding-right: 2.5rem;
        transition: all 0.2s ease;
    }
    
    .search-tag .remove-filter {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.2);
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: bold;
    }
    
    .search-tag .remove-filter:hover {
        background: rgba(255,255,255,0.4);
        transform: translateY(-50%) scale(1.1);
    }
    
    /* Adidas Style Book Cards */
    .adidas-book-card {
        background: white;
        border: 2px solid #f3f4f6;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .adidas-book-card:hover {
        border-color: #000000;
        transform: translateY(-2px);
    }
    
    .adidas-book-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: #000000;
        transition: left 0.3s ease;
    }
    
    .adidas-book-card:hover::before {
        left: 0;
    }
    
    .book-image-adidas {
        aspect-ratio: 3/4;
        background: #f9fafb;
        overflow: hidden;
        position: relative;
    }
    
    .book-image-adidas img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .adidas-book-card:hover .book-image-adidas img {
        transform: scale(1.05);
    }
    
    /* Status Badge - Adidas Style */
    .status-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 0.25rem 0.5rem;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: white;
    }
    
    .status-available { background: #000000; }
    .status-out-of-stock { background: #dc2626; }
    .status-coming-soon { background: #f59e0b; }
    .status-discontinued { background: #6b7280; }
    
    /* Discount Badge */
    .discount-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #dc2626;
        color: white;
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Price Section - Adidas Style */
    .price-section-adidas {
        border-top: 1px solid #e5e7eb;
        margin-top: 1rem;
        padding-top: 1rem;
    }
    
    .price-current {
        color: #000000;
        font-weight: 900;
        font-size: 1.1rem;
    }
    
    .price-original {
        color: #9ca3af;
        text-decoration: line-through;
        font-size: 0.9rem;
    }
    
    /* No Results - Adidas Style */
    .no-results-adidas {
        text-align: center;
        padding: 6rem 2rem;
        background: #f9fafb;
    }
    
    .no-results-adidas i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 2rem;
    }
    
    .no-results-adidas h4 {
        font-size: 2rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #000000;
        margin-bottom: 1rem;
    }
    
    .no-results-adidas p {
        color: #6b7280;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2rem;
    }
    
    /* Breadcrumb - Adidas Style */
    .breadcrumb-adidas {
        background: transparent;
        padding: 0;
        margin-bottom: 3rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 500;
    }
    
    .breadcrumb-adidas .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #6b7280;
        font-weight: 700;
        margin: 0 0.75rem;
    }
    
    .breadcrumb-adidas .breadcrumb-item a {
        color: #000000;
        text-decoration: none;
        transition: opacity 0.2s;
    }
    
    .breadcrumb-adidas .breadcrumb-item a:hover {
        opacity: 0.7;
    }
    
    .breadcrumb-adidas .breadcrumb-item.active {
        color: #6b7280;
    }
    
    /* Adidas Style Filter Button */
    .adidas-filter-btn {
        background: white;
        border: 2px solid #000000;
        color: #000000;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.8rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-width: 200px;
    }

    .adidas-filter-btn:hover {
        background: #000000;
        color: white;
        transform: translateY(-1px);
    }

    /* Adidas Style Overlay */
    .adidas-filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .adidas-filter-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    /* Adidas Style Sidebar */
    .adidas-filter-sidebar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 100%;
        max-width: 450px;
        height: 100vh;
        background: white;
        z-index: 9999;
        transition: right 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        display: flex;
        flex-direction: column;
        box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
    }

    .adidas-filter-sidebar.show {
        right: 0;
    }

    /* Header */
    .adidas-filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 2px solid #f3f4f6;
        background: #f9fafb;
    }

    .adidas-filter-title {
        font-size: 1.1rem;
        font-weight: 900;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin: 0;
    }

    .adidas-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #000000;
        cursor: pointer;
        padding: 0.5rem;
        transition: transform 0.2s ease;
    }

    .adidas-close-btn:hover {
        transform: scale(1.1);
    }

    /* Content */
    .adidas-filter-content {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    /* Filter Groups */
    .adidas-filter-group {
        border-bottom: 1px solid #e5e7eb;
    }

    .adidas-filter-group-header {
        width: 100%;
        background: none;
        border: none;
        padding: 1.25rem 1.5rem;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s ease;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.85rem;
        color: #000000;
    }

    .adidas-filter-group-header:hover {
        background: #f9fafb;
    }

    .adidas-filter-group-header i {
        transition: transform 0.3s ease;
        font-size: 0.75rem;
        color: #6b7280;
    }

    .adidas-filter-group-header.active i {
        transform: rotate(180deg);
    }

    /* Filter Group Content */
    .adidas-filter-group-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: #fafafa;
    }

    .adidas-filter-group-content.show {
        max-height: 500px;
    }

    /* Filter Options */
    .adidas-filter-option {
        padding: 1rem 3rem 1rem 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.85rem;
        color: #374151;
        border-bottom: 1px solid #f0f0f0;
        position: relative;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 500;
    }

    .adidas-filter-option:hover {
        background: #f0f0f0;
        color: #000000;
    }

    .adidas-filter-option.active {
        background: #000000;
        color: white;
        font-weight: 700;
    }

    .adidas-filter-option.active::after {
        content: '✓';
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        font-weight: bold;
    }

    /* Price Inputs */
    .adidas-price-inputs {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
    }

    .adidas-price-input {
        flex: 1;
        border: 2px solid #e5e7eb;
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
        text-align: center;
        outline: none;
        transition: border-color 0.2s ease;
        font-weight: 600;
        text-transform: uppercase;
    }

    .adidas-price-input:focus {
        border-color: #000000;
    }

    .adidas-price-separator {
        color: #6b7280;
        font-weight: bold;
        font-size: 0.9rem;
    }

    /* Footer */
    .adidas-filter-footer {
        padding: 1.5rem;
        border-top: 2px solid #e5e7eb;
        background: #f9fafb;
    }

    .adidas-apply-btn {
        width: 100%;
        background: #000000;
        color: white;
        border: none;
        padding: 1rem 1.5rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .adidas-apply-btn:hover {
        background: #374151;
        transform: translateY(-1px);
    }

    /* Results Header */
    .results-header-adidas {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 3rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .results-count-adidas h5 {
        font-size: 1.5rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #000000;
        margin: 0;
    }

    .results-count-adidas .search-term {
        color: #6b7280;
        font-weight: 400;
    }

    /* Utility classes for better compatibility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .min-h-screen {
        min-height: 100vh;
    }
    
    .gap-6 {
        gap: 1.5rem;
    }
    
    .gap-4 {
        gap: 1rem;
    }
    
    .gap-2 {
        gap: 0.5rem;
    }
    
    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }
    
    .space-y-1 > * + * {
        margin-top: 0.25rem;
    }
    
    /* Grid system */
    .grid {
        display: grid;
    }
    
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    @media (min-width: 640px) {
        .sm\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    
    @media (min-width: 768px) {
        .md\:grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    
    @media (min-width: 1024px) {
        .lg\:grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
    
    /* Flexbox utilities */
    .flex {
        display: flex;
    }
    
    .items-center {
        align-items: center;
    }
    
    .justify-center {
        justify-content: center;
    }
    
    .justify-between {
        justify-content: space-between;
    }
    
    .flex-wrap {
        flex-wrap: wrap;
    }
    
    /* Position utilities */
    .relative {
        position: relative;
    }
    
    .absolute {
        position: absolute;
    }
    
    .inset-0 {
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    
    /* Z-index */
    .z-10 {
        z-index: 10;
    }
    
    /* Padding and margin */
    .py-20 {
        padding-top: 5rem;
        padding-bottom: 5rem;
    }
    
    .px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .p-4 {
        padding: 1rem;
    }
    
    .mb-8 {
        margin-bottom: 2rem;
    }
    
    .mb-2 {
        margin-bottom: 0.5rem;
    }
    
    .mr-1 {
        margin-right: 0.25rem;
    }
    
    .mr-2 {
        margin-right: 0.5rem;
    }
    
    .mr-4 {
        margin-right: 1rem;
    }
    
    .mt-2 {
        margin-top: 0.5rem;
    }
    
    /* Width and height */
    .w-1 {
        width: 0.25rem;
    }
    
    .w-6 {
        width: 1.5rem;
    }
    
    .w-8 {
        width: 2rem;
    }
    
    .w-16 {
        width: 4rem;
    }
    
    .w-72 {
        width: 18rem;
    }
    
    .w-96 {
        width: 24rem;
    }
    
    .h-0\.5 {
        height: 0.125rem;
    }
    
    .h-1 {
        height: 0.25rem;
    }
    
    .h-12 {
        height: 3rem;
    }
    
    .h-32 {
        height: 8rem;
    }
    
    .max-w-7xl {
        max-width: 80rem;
    }
    
    .mx-auto {
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Text utilities */
    .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
    }
    
    .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }
    
    .font-medium {
        font-weight: 500;
    }
    
    .font-bold {
        font-weight: 700;
    }
    
    .uppercase {
        text-transform: uppercase;
    }
    
    .tracking-wider {
        letter-spacing: 0.05em;
    }
    
    /* Colors */
    .text-gray-500 {
        color: #6b7280;
    }
    
    .text-gray-400 {
        color: #9ca3af;
    }
    
    .text-black {
        color: #000000;
    }
    
    .bg-white {
        background-color: #ffffff;
    }
    
    .bg-black {
        background-color: #000000;
    }
    
    .bg-gray-800 {
        background-color: #1f2937;
    }
    
    /* Hover states */
    .hover\:text-black:hover {
        color: #000000;
    }
    
    .hover\:bg-gray-800:hover {
        background-color: #1f2937;
    }
    
    /* Transitions */
    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    .duration-200 {
        transition-duration: 200ms;
    }
    
    .duration-300 {
        transition-duration: 300ms;
    }
    
    /* Overflow */
    .overflow-hidden {
        overflow: hidden;
    }
    
    .pointer-events-none {
        pointer-events: none;
    }
    
    /* Opacity */
    .opacity-10 {
        opacity: 0.1;
    }
    
    .opacity-5 {
        opacity: 0.05;
    }
    
    .opacity-70 {
        opacity: 0.7;
    }
    
    /* Display */
    .inline-block {
        display: inline-block;
    }
    
    .text-decoration-none {
        text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .results-header-adidas {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="search-page bg-white min-h-screen">
    <!-- Background Elements - Minimal Adidas Style -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 right-0 w-72 h-1 bg-black opacity-10"></div>
        <div class="absolute bottom-0 left-0 w-96 h-0.5 bg-black opacity-5"></div>
        <div class="absolute top-1/2 left-10 w-0.5 h-32 bg-black opacity-10"></div>
    </div>

    <!-- Search Results Section -->
    <section class="relative z-10 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Breadcrumb - Adidas Style -->
            <nav aria-label="breadcrumb" class="mb-8">
                <ol class="breadcrumb-adidas flex items-center">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <i class="fas fa-home"></i>
                            TRANG CHỦ
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('books.index') }}">SÁCH</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">TÌM KIẾM</li>
                </ol>
            </nav>

            <!-- Results Header - Adidas Style -->
            <div class="results-header-adidas">
                <div class="results-count-adidas">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-1 h-12 bg-black"></div>
                        <div>
                            <h5>
                                @if($books->count() > 0)
                                    HIỂN THỊ {{ $books->count() }} KẾT QUẢ
                                @else
                                    KHÔNG TÌM THẤY KẾT QUẢ
                                @endif
                                @if($searchTerm)
                                    <span class="search-term">CHO "{{ strtoupper($searchTerm) }}"</span>
                                @endif
                            </h5>
                            <div class="w-16 h-0.5 bg-black mt-2"></div>
                        </div>
                    </div>
                </div>
                
                <div class="filter-sort-controls">
                    <!-- Filter & Sort Button - Adidas Style -->
                    <button class="adidas-filter-btn" onclick="toggleFilterSidebar()">
                        <span>LỌC & SẮP XẾP</span>
                        <i class="fas fa-sliders-h"></i>
                    </button>
                </div>
            </div>
            
            <!-- Active Filters Display - Adidas Style -->
            @if($filters['category'] || $filters['author'] || $filters['brand'] || ($filters['min_price'] > 0) || ($filters['max_price'] > 0))
                <div class="mb-8">
                    <div class="flex items-center flex-wrap gap-2">
                        <span class="text-gray-500 text-sm uppercase tracking-wider font-medium mr-4">BỘ LỌC ĐANG ÁP DỤNG:</span>
                        @if($filters['category'])
                            <span class="search-tag">
                                DANH MỤC: {{ strtoupper($categories->firstWhere('id', $filters['category'])->name ?? '') }}
                                <a href="{{ route('books.search', array_merge(request()->query(), ['category' => null])) }}" 
                                   class="remove-filter text-decoration-none">×</a>
                            </span>
                        @endif
                        @if($filters['author'])
                            <span class="search-tag">
                                TÁC GIẢ: {{ strtoupper($authors->firstWhere('id', $filters['author'])->name ?? '') }}
                                <a href="{{ route('books.search', array_merge(request()->query(), ['author' => null])) }}" 
                                   class="remove-filter text-decoration-none">×</a>
                            </span>
                        @endif
                        @if($filters['brand'])
                            <span class="search-tag">
                                NXB: {{ strtoupper($brands->firstWhere('id', $filters['brand'])->name ?? '') }}
                                <a href="{{ route('books.search', array_merge(request()->query(), ['brand' => null])) }}" 
                                   class="remove-filter text-decoration-none">×</a>
                            </span>
                        @endif
                        @if($filters['min_price'] > 0 || $filters['max_price'] > 0)
                            <span class="search-tag">
                                GIÁ: 
                                @if($filters['min_price'] > 0 && $filters['max_price'] > 0)
                                    {{ number_format($filters['min_price']) }}Đ - {{ number_format($filters['max_price']) }}Đ
                                @elseif($filters['min_price'] > 0)
                                    TỪ {{ number_format($filters['min_price']) }}Đ
                                @elseif($filters['max_price'] > 0)
                                    ĐẾN {{ number_format($filters['max_price']) }}Đ
                                @endif
                                <a href="{{ route('books.search', array_merge(request()->query(), ['min_price' => null, 'max_price' => null])) }}" 
                                   class="remove-filter text-decoration-none">×</a>
                            </span>
                        @endif
                        @if($filters['category'] || $filters['author'] || $filters['brand'] || ($filters['min_price'] > 0) || ($filters['max_price'] > 0))
                            <a href="{{ route('books.search', ['search' => $searchTerm]) }}" 
                               class="text-gray-500 text-decoration-none hover:text-black transition-colors duration-200 text-xs uppercase tracking-wider font-medium">
                                <i class="fas fa-times mr-1"></i>XÓA TẤT CẢ
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Books Grid - Adidas Style -->
            @if($books->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($books as $book)
                        <div class="adidas-book-card group" onclick="window.location='{{ route('books.show', $book->slug) }}'">
                            <div class="book-image-adidas">
                                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-image.png') }}" 
                                     alt="{{ $book->title }}">
                                
                                @php
                                    // Calculate discount and prices
                                    $hasDiscount = false;
                                    $discountPercent = 0;
                                    if (isset($book->min_sale_price) && isset($book->min_price) && 
                                        $book->min_sale_price > 0 && $book->min_price > 0 && 
                                        $book->min_sale_price < $book->min_price) {
                                        $hasDiscount = true;
                                        $discountPercent = round((($book->min_price - $book->min_sale_price) / $book->min_price) * 100);
                                    }
                                @endphp
                                
                                @if($hasDiscount)
                                    <div class="discount-badge">
                                        -{{ $discountPercent }}%
                                    </div>
                                @endif

                                @php
                                    $statusText = $book->status ?? 'KHÔNG RÕ';
                                    $statusClass = 'status-badge ';
                                    
                                    switch($statusText) {
                                        case 'Còn Hàng':
                                            $statusClass .= 'status-available';
                                            $statusText = 'CÒN HÀNG';
                                            break;
                                        case 'Hết Hàng Tồn Kho':
                                            $statusClass .= 'status-out-of-stock';
                                            $statusText = 'HẾT HÀNG';
                                            break;
                                        case 'Sắp Ra Mắt':
                                            $statusClass .= 'status-coming-soon';
                                            $statusText = 'SẮP RA';
                                            break;
                                        case 'Ngừng Kinh Doanh':
                                            $statusClass .= 'status-discontinued';
                                            $statusText = 'NGỪNG BÁN';
                                            break;
                                        default:
                                            $statusClass .= 'status-discontinued';
                                            $statusText = 'KHÔNG RÕ';
                                    }
                                @endphp
                                <div class="{{ $statusClass }}">{{ $statusText }}</div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-4 space-y-3">
                                <h3 class="font-bold text-black text-sm uppercase tracking-wide group-hover:opacity-70 transition-opacity line-clamp-2">
                                    {{ Str::limit($book->title, 40) }}
                                </h3>
                                
                                @php
                                    // Get author, brand, category info
                                    $authorName = 'CHƯA RÕ TÁC GIẢ';
                                    if (isset($book->author_name) && !empty($book->author_name) && $book->author_name !== 'N/A') {
                                        $authorName = strtoupper($book->author_name);
                                    } elseif (isset($book->author) && $book->author && !empty($book->author->name) && $book->author->name !== 'N/A') {
                                        $authorName = strtoupper($book->author->name);
                                    }
                                    
                                    $brandName = 'CHƯA RÕ NXB';
                                    if (isset($book->brand_name) && !empty($book->brand_name) && $book->brand_name !== 'N/A') {
                                        $brandName = strtoupper($book->brand_name);
                                    } elseif (isset($book->brand) && $book->brand && !empty($book->brand->name) && $book->brand->name !== 'N/A') {
                                        $brandName = strtoupper($book->brand->name);
                                    }
                                @endphp
                                
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">
                                        {{ Str::limit($authorName, 25) }}
                                    </p>
                                    <p class="text-xs text-gray-400 uppercase tracking-wider">
                                        {{ Str::limit($brandName, 25) }}
                                    </p>
                                </div>
                                
                                <!-- Price Section - Adidas Style -->
                                <div class="price-section-adidas">
                                    @php
                                        $displayPrice = null;
                                        $salePrice = null;
                                        $originalPrice = null;
                                        
                                        if (isset($book->min_price) && is_numeric($book->min_price) && $book->min_price > 0) {
                                            if (isset($book->max_price) && is_numeric($book->max_price) && 
                                                $book->max_price > 0 && $book->max_price != $book->min_price) {
                                                $originalPrice = number_format($book->min_price) . 'Đ - ' . number_format($book->max_price) . 'Đ';
                                                $displayPrice = $book->min_price;
                                            } else {
                                                $originalPrice = number_format($book->min_price) . 'Đ';
                                                $displayPrice = $book->min_price;
                                            }
                                        }
                                        
                                        if (isset($book->min_sale_price) && is_numeric($book->min_sale_price) && $book->min_sale_price > 0) {
                                            if ($displayPrice && $book->min_sale_price < $displayPrice) {
                                                $salePrice = number_format($book->min_sale_price) . 'Đ';
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            @if($salePrice && $originalPrice)
                                                <div class="price-current">{{ $salePrice }}</div>
                                                <div class="price-original">{{ $originalPrice }}</div>
                                            @elseif($originalPrice)
                                                <div class="price-current">{{ $originalPrice }}</div>
                                            @else
                                                <div class="price-current text-gray-500">LIÊN HỆ</div>
                                            @endif
                                        </div>
                                        <div class="w-6 h-0.5 bg-black group-hover:w-8 transition-all duration-300"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-results-adidas">
                    <i class="fas fa-search"></i>
                    <h4>KHÔNG TÌM THẤY SÁCH NÀO</h4>
                    <p>HÃY THỬ TÌM KIẾM VỚI TỪ KHÓA KHÁC HOẶC BỎ BỚT BỘ LỌC</p>
                    <a href="{{ route('books.search', ['search' => $searchTerm]) }}" 
                       class="bg-black text-white px-8 py-3 font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors inline-block">
                        <i class="fas fa-times mr-2"></i>XÓA BỘ LỌC
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>

<!-- Adidas Style Filter Sidebar -->
<div class="adidas-filter-overlay" onclick="closeFilterSidebar()"></div>
<div class="adidas-filter-sidebar">
    <!-- Header -->
    <div class="adidas-filter-header">
        <h3 class="adidas-filter-title">LỌC & SẮP XẾP</h3>
        <button class="adidas-close-btn" onclick="closeFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Content -->
    <div class="adidas-filter-content">
        <!-- Sort By Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-sort')">
                SẮP XẾP THEO
                <i class="fas fa-chevron-down" id="adidas-sort-icon"></i>
            </button>
            <div class="adidas-filter-group-content show" id="adidas-sort">
                <div class="adidas-filter-option {{ ($filters['sort'] ?? 'newest') == 'newest' ? 'active' : '' }}" 
                     onclick="selectSort('newest')">
                    MỚI NHẤT
                </div>
                <div class="adidas-filter-option {{ ($filters['sort'] ?? '') == 'price_asc' ? 'active' : '' }}" 
                     onclick="selectSort('price_asc')">
                    GIÁ THẤP ĐẾN CAO
                </div>
                <div class="adidas-filter-option {{ ($filters['sort'] ?? '') == 'price_desc' ? 'active' : '' }}" 
                     onclick="selectSort('price_desc')">
                    GIÁ CAO ĐẾN THẤP
                </div>
                <div class="adidas-filter-option {{ ($filters['sort'] ?? '') == 'name_asc' ? 'active' : '' }}" 
                     onclick="selectSort('name_asc')">
                    TÊN A-Z
                </div>
                <div class="adidas-filter-option {{ ($filters['sort'] ?? '') == 'name_desc' ? 'active' : '' }}" 
                     onclick="selectSort('name_desc')">
                    TÊN Z-A
                </div>
            </div>
        </div>

        <!-- Danh mục Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-category')">
                DANH MỤC
                <i class="fas fa-chevron-down" id="adidas-category-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-category">
                <div class="adidas-filter-option {{ !($filters['category'] ?? '') ? 'active' : '' }}" 
                     onclick="selectFilter('category', '')">
                    TẤT CẢ DANH MỤC
                </div>
                @if(isset($categories))
                    @foreach($categories as $category)
                        <div class="adidas-filter-option {{ ($filters['category'] ?? '') == $category->id ? 'active' : '' }}" 
                             onclick="selectFilter('category', '{{ $category->id }}')">
                            {{ strtoupper($category->name) }}
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Tác giả Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-author')">
                TÁC GIẢ
                <i class="fas fa-chevron-down" id="adidas-author-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-author">
                <div class="adidas-filter-option {{ !($filters['author'] ?? '') ? 'active' : '' }}" 
                     onclick="selectFilter('author', '')">
                    TẤT CẢ TÁC GIẢ
                </div>
                @if(isset($authors))
                    @foreach($authors as $author)
                        <div class="adidas-filter-option {{ ($filters['author'] ?? '') == $author->id ? 'active' : '' }}" 
                             onclick="selectFilter('author', '{{ $author->id }}')">
                            {{ strtoupper($author->name) }}
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Nhà xuất bản Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-brand')">
                NHÀ XUẤT BẢN
                <i class="fas fa-chevron-down" id="adidas-brand-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-brand">
                <div class="adidas-filter-option {{ !($filters['brand'] ?? '') ? 'active' : '' }}" 
                     onclick="selectFilter('brand', '')">
                    TẤT CẢ NXB
                </div>
                @if(isset($brands))
                    @foreach($brands as $brand)
                        <div class="adidas-filter-option {{ ($filters['brand'] ?? '') == $brand->id ? 'active' : '' }}" 
                             onclick="selectFilter('brand', '{{ $brand->id }}')">
                            {{ strtoupper($brand->name) }}
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Khoảng giá Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-price')">
                KHOẢNG GIÁ
                <i class="fas fa-chevron-down" id="adidas-price-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-price">
                <div class="adidas-price-inputs">
                    <input type="number" 
                           class="adidas-price-input" 
                           id="adidas-min-price" 
                           placeholder="TỪ"
                           value="{{ $filters['min_price'] ?? '' }}">
                    <span class="adidas-price-separator">-</span>
                    <input type="number" 
                           class="adidas-price-input" 
                           id="adidas-max-price" 
                           placeholder="ĐẾN"
                           value="{{ $filters['max_price'] ?? '' }}">
                </div>
                <div class="adidas-filter-option" onclick="selectPriceRange('', '')">
                    TẤT CẢ GIÁ
                </div>
                <div class="adidas-filter-option" onclick="selectPriceRange('0', '100000')">
                    DƯỚI 100.000Đ
                </div>
                <div class="adidas-filter-option" onclick="selectPriceRange('100000', '300000')">
                    100.000Đ - 300.000Đ
                </div>
                <div class="adidas-filter-option" onclick="selectPriceRange('300000', '500000')">
                    300.000Đ - 500.000Đ
                </div>
                <div class="adidas-filter-option" onclick="selectPriceRange('500000', '')">
                    TRÊN 500.000Đ
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Button -->
    <div class="adidas-filter-footer">
        <button class="adidas-apply-btn" onclick="applyAdidasFilters()">
            ÁP DỤNG BỘ LỌC
        </button>
    </div>
</div>

<script>
// Adidas Filter State
let adidasFilters = {
    sort: '{{ $filters["sort"] ?? "newest" }}',
    category: '{{ $filters["category"] ?? "" }}',
    author: '{{ $filters["author"] ?? "" }}',
    brand: '{{ $filters["brand"] ?? "" }}',
    min_price: '{{ $filters["min_price"] ?? "" }}',
    max_price: '{{ $filters["max_price"] ?? "" }}'
};

// Toggle Adidas Filter Sidebar
function toggleFilterSidebar() {
    const sidebar = document.querySelector('.adidas-filter-sidebar');
    const overlay = document.querySelector('.adidas-filter-overlay');
    
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    
    // Prevent body scroll when sidebar is open
    if (sidebar.classList.contains('show')) {
        document.body.style.overflow = 'hidden';
        // Auto-open first section (Sort By)
        setTimeout(() => {
            toggleAdidasSection('adidas-sort');
        }, 300);
    } else {
        document.body.style.overflow = '';
    }
}

// Close Adidas Filter Sidebar
function closeFilterSidebar() {
    const sidebar = document.querySelector('.adidas-filter-sidebar');
    const overlay = document.querySelector('.adidas-filter-overlay');
    
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
}

// Toggle Adidas Filter Section
function toggleAdidasSection(sectionId) {
    const content = document.getElementById(sectionId);
    const icon = document.getElementById(sectionId + '-icon');
    const header = content.previousElementSibling;
    
    // Close all other sections
    document.querySelectorAll('.adidas-filter-group-content').forEach(section => {
        if (section.id !== sectionId) {
            section.classList.remove('show');
            const otherIcon = document.getElementById(section.id + '-icon');
            const otherHeader = section.previousElementSibling;
            if (otherIcon) otherIcon.classList.remove('rotate');
            if (otherHeader) otherHeader.classList.remove('active');
        }
    });
    
    // Toggle current section
    const isOpen = content.classList.contains('show');
    content.classList.toggle('show');
    header.classList.toggle('active');
    
    if (icon) {
        if (isOpen) {
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.style.transform = 'rotate(180deg)';
        }
    }
}

// Select Sort Option
function selectSort(sortValue) {
    adidasFilters.sort = sortValue;
    
    // Update active states
    document.querySelectorAll('#adidas-sort .adidas-filter-option').forEach(el => {
        el.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Select Filter Option
function selectFilter(filterType, filterValue) {
    adidasFilters[filterType] = filterValue;
    
    // Update active states
    const sectionId = 'adidas-' + filterType;
    document.querySelectorAll('#' + sectionId + ' .adidas-filter-option').forEach(el => {
        el.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Select Price Range
function selectPriceRange(minPrice, maxPrice) {
    document.getElementById('adidas-min-price').value = minPrice;
    document.getElementById('adidas-max-price').value = maxPrice;
    adidasFilters.min_price = minPrice;
    adidasFilters.max_price = maxPrice;
}

// Apply All Adidas Filters
function applyAdidasFilters() {
    // Get price values
    const minPrice = document.getElementById('adidas-min-price')?.value || '';
    const maxPrice = document.getElementById('adidas-max-price')?.value || '';
    
    adidasFilters.min_price = minPrice;
    adidasFilters.max_price = maxPrice;
    
    // Build URL with all filters
    const params = new URLSearchParams();
    params.append('search', '{{ $searchTerm }}');
    
    Object.keys(adidasFilters).forEach(key => {
        if (adidasFilters[key] && adidasFilters[key] !== '') {
            params.append(key, adidasFilters[key]);
        }
    });
    
    // Redirect to filtered results
    window.location.href = '{{ route("books.search") }}?' + params.toString();
}

// Close sidebar when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFilterSidebar();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial active states based on current filters
    Object.keys(adidasFilters).forEach(filterType => {
        const value = adidasFilters[filterType];
        if (value) {
            if (filterType === 'sort') {
                const sortOption = document.querySelector(`#adidas-sort .adidas-filter-option[onclick*="${value}"]`);
                if (sortOption) sortOption.classList.add('active');
            } else {
                const filterOption = document.querySelector(`#adidas-${filterType} .adidas-filter-option[onclick*="${value}"]`);
                if (filterOption) filterOption.classList.add('active');
            }
        }
    });
});
</script>
@endsection
