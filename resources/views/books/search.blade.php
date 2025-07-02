@extends('layouts.app')
@section('title', 'Tìm kiếm sách')

@push('styles')
<style>
    .search-page {
        font-family: 'Roboto', sans-serif;
    }
    
    .filter-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .filter-title {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .filter-select {
        border: 1px solid #d1d5db;
        border-radius: 0;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        width: 100%;
        background: white;
    }
    
    .filter-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 1px #6366f1;
        outline: none;
    }
    
    .book-card {
        background: white;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
    }
    
    .book-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }
    
    .book-image {
        position: relative;
        overflow: hidden;
        height: 250px;
    }
    
    .book-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .book-card:hover .book-image img {
        transform: scale(1.1);
    }
    
    .book-price {
        color: #e74c3c;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .book-info {
        font-size: 0.85rem;
        line-height: 1.4;
    }
    
    .book-info strong {
        color: #555;
        font-weight: 600;
    }
    
    .price-section {
        border-top: 1px solid #f0f0f0;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }
    
    .book-rating {
        color: #f39c12;
    }
    
    .result-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #dee2e6;
    }
    
    .search-tag {
        background: #111827;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-block;
        margin: 0.2rem 0.2rem 0.2rem 0;
        position: relative;
        padding-right: 2rem;
    }
    
    .search-tag .remove-filter {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .search-tag .remove-filter:hover {
        background: rgba(255,255,255,0.5);
    }
    
    .no-results {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }
    
    .no-results i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #dee2e6;
    }
    
    /* Button styles - đổi tất cả button thành màu đen */
    .btn-primary {
        background-color: #111827 !important;
        border-color: #111827 !important;
        border-radius: 0 !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 0.5rem 1rem !important;
    }
    
    .btn-primary:hover {
        background-color: #374151 !important;
        border-color: #374151 !important;
    }
    
    .btn-outline-secondary {
        border-color: #111827 !important;
        color: #111827 !important;
        background-color: transparent !important;
        border-radius: 5px !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 0.5rem 1rem !important;
    }
    
    .btn-outline-secondary:hover {
        background-color: #111827 !important;
        border-color: #111827 !important;
        color: #ffffff !important;
    }
    
    .btn-light {
        border-radius: 5px !important;
    }
    
    .btn-sm {
        border-radius: 5px !important;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 2rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #6c757d;
        font-weight: 600;
    }
    
    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    
    .breadcrumb-item.active {
        color: #6c757d;
        font-weight: 600;
    }
    
    /* Mobile responsive styles cho search page */
    @media (max-width: 768px) {
        .filter-card {
            margin-bottom: 1rem;
            padding: 1rem;
        }
        
        .book-card .book-image {
            height: 200px;
        }
        
        .result-info {
            padding: 1rem;
        }
        
        .search-tag {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
        }
        
        .breadcrumb {
            font-size: 0.85rem;
        }
        
        /* Trên mobile, filter sẽ hiển thị trước results */
        .col-lg-3 {
            order: 1;
            margin-bottom: 2rem;
        }
        
        .col-lg-9 {
            order: 2;
        }
        
        .sticky-top {
            position: relative !important;
            top: auto !important;
        }
    }
    
    @media (max-width: 576px) {
        .book-card .p-3 {
            padding: 1rem !important;
        }
        
        .result-info .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .filter-select {
            font-size: 0.9rem;
        }
    }
    
    /* Animation cho search results */
    .book-card {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .book-card:nth-child(1) { animation-delay: 0.1s; }
    .book-card:nth-child(2) { animation-delay: 0.2s; }
    .book-card:nth-child(3) { animation-delay: 0.3s; }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Filter Sort Button Styles - Adidas Style */
    .filter-sort-btn {
        background: white;
        border: 1px solid #ddd;
        border-radius: 0;
        padding: 12px 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 14px;
        color: #333;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .filter-sort-btn:hover {
        background: #f5f5f5;
        border-color: #333;
        color: #333;
    }
    
    .filter-sort-btn.active {
        background: #333;
        color: white;
        border-color: #333;
    }
    
    .filter-sort-btn i {
        font-size: 16px;
    }
    
    /* Filter sidebar toggle for mobile */
    .filter-sidebar-mobile {
        position: fixed;
        top: 0;
        right: -100%;
        width: 100%;
        max-width: 450px;
        height: 100vh;
        background: white;
        z-index: 9999;
        transition: right 0.3s ease;
        overflow-y: auto;
        box-shadow: -5px 0 25px rgba(0,0,0,0.15);
    }
    
    .filter-sidebar-mobile.show {
        right: 0;
    }
    
    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .filter-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    .filter-sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9fafb;
    }
    
    .filter-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #6b7280;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 50%;
        transition: all 0.2s;
    }
    
    .filter-close-btn:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    /* Mobile filter sections */
    .mobile-filter-section {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .mobile-filter-header {
        padding: 1rem 1.5rem;
        background: white;
        border: none;
        width: 100%;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #111827;
    }
    
    .mobile-filter-header:hover {
        background: #f9fafb;
    }
    
    .mobile-filter-header.active {
        background: #f3f4f6;
    }
    
    .mobile-filter-content {
        padding: 0 1.5rem 1rem 1.5rem;
        background: #f9fafb;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }
    
    .mobile-filter-content.show {
        max-height: 500px;
        padding: 1rem 1.5rem;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from { 
            opacity: 0; 
            transform: translateY(-10px);
            max-height: 0;
        }
        to { 
            opacity: 1; 
            transform: translateY(0);
            max-height: 500px;
        }
    }
    
    .mobile-filter-option {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: color 0.2s;
        font-size: 0.9rem;
        color: #374151;
        opacity: 0;
        animation: fadeInOption 0.3s ease forwards;
    }
    
    .mobile-filter-content.show .mobile-filter-option {
        animation: fadeInOption 0.3s ease forwards;
    }
    
    .mobile-filter-content.show .mobile-filter-option:nth-child(1) { animation-delay: 0.1s; }
    .mobile-filter-content.show .mobile-filter-option:nth-child(2) { animation-delay: 0.15s; }
    .mobile-filter-content.show .mobile-filter-option:nth-child(3) { animation-delay: 0.2s; }
    .mobile-filter-content.show .mobile-filter-option:nth-child(4) { animation-delay: 0.25s; }
    .mobile-filter-content.show .mobile-filter-option:nth-child(5) { animation-delay: 0.3s; }
    
    @keyframes fadeInOption {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    .mobile-filter-option:last-child {
        border-bottom: none;
    }
    
    .mobile-filter-option:hover {
        color: #111827;
    }
    
    .mobile-filter-option.active {
        color: #6366f1;
        font-weight: 600;
    }
    
    .mobile-apply-btn {
        position: sticky;
        bottom: 0;
        background: #111827;
        color: white;
        border: none;
        padding: 1rem 1.5rem;
        width: 100%;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .mobile-apply-btn:hover {
        background: #374151;
    }
    
    .chevron-icon {
        transition: transform 0.3s ease;
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    .chevron-icon.rotate {
        transform: rotate(180deg);
    }
    
    /* Results header with filter button */
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .results-count {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-sort-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    @media (min-width: 992px) {
        .filter-sort-btn {
            display: none !important;
        }
    }
    
    @media (max-width: 991px) {
        .desktop-filters {
            display: none !important;
        }
        
        .results-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-sort-controls {
            justify-content: space-between;
        }
    }
</style>
@endpush

@section('content')
<div class="search-page">
    <!-- Search Results -->
    <section class="py-5">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>
                            Trang chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('books.index') }}">Sách</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
                </ol>
            </nav>

            <div class="row">
                <!-- Search Results - Di chuyển sang bên trái -->
                <div class="col-lg-9 mb-4">
                    <!-- Results Header with Filter Button -->
                    <div class="results-header">
                        <div class="results-count">
                            <h5 class="mb-1 fw-bold">
                                @if($books->count() > 0)
                                    Hiển thị tất cả {{ $books->count() }} kết quả
                                @else
                                    Không tìm thấy kết quả nào
                                @endif
                                @if($searchTerm)
                                    cho từ khóa "<span class="text-primary">{{ $searchTerm }}</span>"
                                @endif
                            </h5>
                        </div>
                        
                        <div class="filter-sort-controls">
                            <!-- Filter & Sort Button for Mobile/Tablet -->
                            <button class="filter-sort-btn" onclick="toggleFilterSidebar()">
                                <i class="fas fa-sliders-h"></i>
                                LỌC & SẮP XẾP
                            </button>
                            
                            <!-- Sort Options for Desktop -->
                            <div class="desktop-sort d-none d-lg-block">
                                <form method="GET" action="{{ route('books.search') }}" class="d-inline">
                                    <input type="hidden" name="search" value="{{ $searchTerm }}">
                                    <input type="hidden" name="category" value="{{ $filters['category'] }}">
                                    <input type="hidden" name="author" value="{{ $filters['author'] }}">
                                    <input type="hidden" name="brand" value="{{ $filters['brand'] }}">
                                    <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                                    <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
                                    
                                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto; display: inline-block;">
                                        <option value="newest" {{ $filters['sort'] == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                        <option value="oldest" {{ $filters['sort'] == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                        <option value="price_asc" {{ $filters['sort'] == 'price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                                        <option value="price_desc" {{ $filters['sort'] == 'price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
                                        <option value="name_asc" {{ $filters['sort'] == 'name_asc' ? 'selected' : '' }}>Tên A → Z</option>
                                        <option value="name_desc" {{ $filters['sort'] == 'name_desc' ? 'selected' : '' }}>Tên Z → A</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Active Filters Display -->
                    @if($filters['category'] || $filters['author'] || $filters['brand'] || ($filters['min_price'] > 0) || ($filters['max_price'] > 0))
                        <div class="mb-3">
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="text-muted me-2" style="font-size: 0.9rem;">Đang lọc theo:</span>
                                @if($filters['category'])
                                    <span class="search-tag">
                                        Danh mục: {{ $categories->firstWhere('id', $filters['category'])->name ?? '' }}
                                        <a href="{{ route('books.search', array_merge(request()->query(), ['category' => null])) }}" 
                                           class="remove-filter text-decoration-none">×</a>
                                    </span>
                                @endif
                                @if($filters['author'])
                                    <span class="search-tag">
                                        Tác giả: {{ $authors->firstWhere('id', $filters['author'])->name ?? '' }}
                                        <a href="{{ route('books.search', array_merge(request()->query(), ['author' => null])) }}" 
                                           class="remove-filter text-decoration-none">×</a>
                                    </span>
                                @endif
                                @if($filters['brand'])
                                    <span class="search-tag">
                                        Thương hiệu: {{ $brands->firstWhere('id', $filters['brand'])->name ?? '' }}
                                        <a href="{{ route('books.search', array_merge(request()->query(), ['brand' => null])) }}" 
                                           class="remove-filter text-decoration-none">×</a>
                                    </span>
                                @endif
                                @if($filters['min_price'] > 0 || $filters['max_price'] > 0)
                                    <span class="search-tag">
                                        Giá: 
                                        @if($filters['min_price'] > 0 && $filters['max_price'] > 0)
                                            {{ number_format($filters['min_price']) }}đ - {{ number_format($filters['max_price']) }}đ
                                        @elseif($filters['min_price'] > 0)
                                            Từ {{ number_format($filters['min_price']) }}đ
                                        @elseif($filters['max_price'] > 0)
                                            Đến {{ number_format($filters['max_price']) }}đ
                                        @endif
                                        <a href="{{ route('books.search', array_merge(request()->query(), ['min_price' => null, 'max_price' => null])) }}" 
                                           class="remove-filter text-decoration-none">×</a>
                                    </span>
                                @endif
                                @if($filters['category'] || $filters['author'] || $filters['brand'] || ($filters['min_price'] > 0) || ($filters['max_price'] > 0))
                                    <a href="{{ route('books.search', ['search' => $searchTerm]) }}" 
                                       class="text-muted text-decoration-none ms-2" 
                                       style="font-size: 0.85rem;">
                                        <i class="fas fa-times me-1"></i>Xóa tất cả
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Books Grid -->
                    @if($books->count() > 0)
                        <div class="row g-4">
                            @foreach($books as $book)
                                <div class="col-md-6 col-lg-4">
                                    <div class="book-card">
                                        <div class="book-image">
                                            <a href="{{ route('books.show', $book->slug) }}">
                                                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-image.png') }}" 
                                                     alt="{{ $book->title }}" 
                                                     class="img-fluid">
                                            </a>
                                        </div>
                                        <div class="p-3">
                                            <h6 class="book-title mb-2">
                                                <a href="{{ route('books.show', $book->slug) }}" 
                                                   class="text-decoration-none text-dark">
                                                    {{ Str::limit($book->title, 50) }}
                                                </a>
                                            </h6>
                                            @php
                                                // Lấy tên tác giả
                                                $authorName = 'Chưa rõ tác giả';
                                                if (isset($book->author_name) && !empty($book->author_name) && $book->author_name !== 'N/A') {
                                                    $authorName = $book->author_name;
                                                } elseif (isset($book->author) && $book->author && !empty($book->author->name) && $book->author->name !== 'N/A') {
                                                    $authorName = $book->author->name;
                                                }
                                                
                                                // Lấy tên nhà xuất bản/thương hiệu
                                                $brandName = 'Chưa rõ NXB';
                                                if (isset($book->brand_name) && !empty($book->brand_name) && $book->brand_name !== 'N/A') {
                                                    $brandName = $book->brand_name;
                                                } elseif (isset($book->brand) && $book->brand && !empty($book->brand->name) && $book->brand->name !== 'N/A') {
                                                    $brandName = $book->brand->name;
                                                }
                                                
                                                // Lấy tên danh mục
                                                $categoryName = 'Chưa phân loại';
                                                if (isset($book->category_name) && !empty($book->category_name) && $book->category_name !== 'N/A') {
                                                    $categoryName = $book->category_name;
                                                } elseif (isset($book->category) && $book->category && !empty($book->category->name) && $book->category->name !== 'N/A') {
                                                    $categoryName = $book->category->name;
                                                }
                                            @endphp
                                            
                                            <!-- Hiển thị tác giả -->
                                            <p class="text-muted book-info mb-2">
                                                <i class="fas fa-user me-1 text-primary"></i>
                                                <strong>Tác giả:</strong> {{ $authorName }}
                                            </p>
                                            
                                            <!-- Hiển thị nhà xuất bản -->
                                            <p class="text-muted book-info mb-2">
                                                <i class="fas fa-building me-1 text-success"></i>
                                                <strong>NXB:</strong> {{ $brandName }}
                                            </p>
                                            
                                            <!-- Hiển thị danh mục -->
                                            <p class="text-muted book-info mb-3">
                                                <i class="fas fa-folder me-1 text-warning"></i>
                                                <strong>Danh mục:</strong> {{ $categoryName }}
                                            </p>
                                            
                                            <!-- Phần giá và trạng thái -->
                                            <div class="price-section">
                                                <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @php
                                                        $displayPrice = null;
                                                        $salePrice = null;
                                                        $originalPrice = null;
                                                        
                                                        // Lấy giá gốc từ min_price/max_price
                                                        if (isset($book->min_price) && is_numeric($book->min_price) && $book->min_price > 0) {
                                                            if (isset($book->max_price) && is_numeric($book->max_price) && 
                                                                $book->max_price > 0 && $book->max_price != $book->min_price) {
                                                                $originalPrice = number_format($book->min_price) . 'đ - ' . number_format($book->max_price) . 'đ';
                                                                $displayPrice = $book->min_price; // Để so sánh với sale price
                                                            } else {
                                                                $originalPrice = number_format($book->min_price) . 'đ';
                                                                $displayPrice = $book->min_price;
                                                            }
                                                        }
                                                        
                                                        // Lấy giá khuyến mãi từ min_sale_price
                                                        if (isset($book->min_sale_price) && is_numeric($book->min_sale_price) && $book->min_sale_price > 0) {
                                                            // Chỉ hiển thị sale price nếu nó thấp hơn giá gốc
                                                            if ($displayPrice && $book->min_sale_price < $displayPrice) {
                                                                $salePrice = number_format($book->min_sale_price) . 'đ';
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    <div class="mb-1">
                                                        <small class="text-muted"><strong>Giá:</strong></small>
                                                    </div>
                                                    
                                                    @if($salePrice && $originalPrice)
                                                        <div class="book-price text-danger fw-bold">{{ $salePrice }}</div>
                                                        <small class="text-muted text-decoration-line-through">{{ $originalPrice }}</small>
                                                    @elseif($originalPrice)
                                                        <div class="book-price fw-bold text-primary">{{ $originalPrice }}</div>
                                                    @else
                                                        <div class="book-price fw-bold text-warning">Liên hệ</div>
                                                    @endif
                                                </div>
                                                @php
                                                    $stock = 0;
                                                    if (isset($book->stock) && is_numeric($book->stock)) {
                                                        $stock = $book->stock;
                                                    } elseif (isset($book->physical_stock) && is_numeric($book->physical_stock)) {
                                                        $stock = $book->physical_stock;
                                                    }
                                                    
                                                    // Get status based on actual book status, not just stock
                                                    $statusText = $book->status ?? 'Không rõ';
                                                    $statusClass = 'bg-secondary';
                                                    
                                                    switch($statusText) {
                                                        case 'Còn Hàng':
                                                            $statusClass = 'bg-success';
                                                            break;
                                                        case 'Hết Hàng Tồn Kho':
                                                            $statusClass = 'bg-danger';
                                                            break;
                                                        case 'Sắp Ra Mắt':
                                                            $statusClass = 'bg-warning text-dark';
                                                            break;
                                                        case 'Ngừng Kinh Doanh':
                                                            $statusClass = 'bg-dark';
                                                            break;
                                                        default:
                                                            $statusClass = 'bg-secondary';
                                                    }
                                                @endphp
                                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('books.show', $book->slug) }}" 
                                                   class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-eye me-1"></i>
                                                    Xem chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-results text-center py-5">
                            <i class="fas fa-search"></i>
                            <h4 class="mt-3">Không tìm thấy sách nào</h4>
                            <p class="text-muted">Hãy thử tìm kiếm với từ khóa khác hoặc bỏ bớt bộ lọc</p>
                            <a href="{{ route('books.search', ['search' => $searchTerm]) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Xóa bộ lọc
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Filters Sidebar - Di chuyển sang bên phải -->
                <div class="col-lg-3 mb-4 desktop-filters">
                    <div class="sticky-top" style="top: 100px;">
                        <h5 class="fw-bold mb-3" style="font-size: 1rem; color: #111827;">
                            <i class="fas fa-filter me-2" style="font-size: 0.9rem;"></i>
                            BỘ LỌC TÌM KIẾM
                        </h5>

                        <!-- Category Filter -->
                        <div class="filter-card">
                            <h6 class="filter-title">Danh mục</h6>
                            <form method="GET" action="{{ route('books.search') }}">
                                <input type="hidden" name="search" value="{{ $searchTerm }}">
                                <input type="hidden" name="author" value="{{ $filters['author'] }}">
                                <input type="hidden" name="brand" value="{{ $filters['brand'] }}">
                                <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                                <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
                                <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                                
                                <select name="category" class="filter-select" onchange="this.form.submit()">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ $filters['category'] == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <!-- Author Filter -->
                        <div class="filter-card">
                            <h6 class="filter-title">Tác giả</h6>
                            <form method="GET" action="{{ route('books.search') }}">
                                <input type="hidden" name="search" value="{{ $searchTerm }}">
                                <input type="hidden" name="category" value="{{ $filters['category'] }}">
                                <input type="hidden" name="brand" value="{{ $filters['brand'] }}">
                                <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                                <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
                                <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                                
                                <select name="author" class="filter-select" onchange="this.form.submit()">
                                    <option value="">Tất cả tác giả</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" 
                                                {{ $filters['author'] == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <!-- Brand Filter -->
                        <div class="filter-card">
                            <h6 class="filter-title">Thương hiệu</h6>
                            <form method="GET" action="{{ route('books.search') }}">
                                <input type="hidden" name="search" value="{{ $searchTerm }}">
                                <input type="hidden" name="category" value="{{ $filters['category'] }}">
                                <input type="hidden" name="author" value="{{ $filters['author'] }}">
                                <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                                <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
                                <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                                
                                <select name="brand" class="filter-select" onchange="this.form.submit()">
                                    <option value="">Tất cả thương hiệu</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" 
                                                {{ $filters['brand'] == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-card">
                            <h6 class="filter-title">Khoảng giá</h6>
                            <form method="GET" action="{{ route('books.search') }}">
                                <input type="hidden" name="search" value="{{ $searchTerm }}">
                                <input type="hidden" name="category" value="{{ $filters['category'] }}">
                                <input type="hidden" name="author" value="{{ $filters['author'] }}">
                                <input type="hidden" name="brand" value="{{ $filters['brand'] }}">
                                <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                                
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" 
                                               name="min_price" 
                                               class="filter-select" 
                                               placeholder="Giá từ"
                                               value="{{ $filters['min_price'] }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" 
                                               name="max_price" 
                                               class="filter-select" 
                                               placeholder="Giá đến"
                                               value="{{ $filters['max_price'] }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mt-2 w-100">
                                    <i class="fas fa-filter me-1"></i>
                                    Áp dụng
                                </button>
                            </form>
                        </div>

                        <!-- Clear Filters -->
                        <div class="filter-card">
                            <a href="{{ route('books.search', ['search' => $searchTerm]) }}" 
                               class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-2"></i>
                                Xóa bộ lọc
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Mobile Filter Sidebar -->
<div class="filter-overlay" onclick="closeFilterSidebar()"></div>
<div class="filter-sidebar-mobile">
    <div class="filter-sidebar-header">
        <h5 class="mb-0 fw-bold" style="font-size: 1rem;">
            Lọc & Sắp xếp
        </h5>
        <button class="filter-close-btn" onclick="closeFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Sort By Section -->
    <div class="mobile-filter-section">
        <button class="mobile-filter-header" onclick="toggleFilterSection('sort-section')">
            SORT BY
            <i class="fas fa-chevron-down chevron-icon" id="sort-chevron"></i>
        </button>
        <div class="mobile-filter-content" id="sort-section">
            <div class="mobile-filter-option {{ $filters['sort'] == 'price_asc' ? 'active' : '' }}" onclick="applySort('price_asc')">
                GIÁ (THẤP - CAO)
            </div>
            <div class="mobile-filter-option {{ $filters['sort'] == 'newest' ? 'active' : '' }}" onclick="applySort('newest')">
                MỚI NHẤT TRƯỚC
            </div>
            <div class="mobile-filter-option {{ $filters['sort'] == 'oldest' ? 'active' : '' }}" onclick="applySort('oldest')">
                BÁN CHẠY NHẤT
            </div>
            <div class="mobile-filter-option {{ $filters['sort'] == 'price_desc' ? 'active' : '' }}" onclick="applySort('price_desc')">
                GIÁ (CAO - THẤP)
            </div>
        </div>
    </div>

    <!-- Category Section -->
    <div class="mobile-filter-section">
        <button class="mobile-filter-header" onclick="toggleFilterSection('category-section')">
            DANH MỤC
            <i class="fas fa-chevron-down chevron-icon" id="category-chevron"></i>
        </button>
        <div class="mobile-filter-content" id="category-section">
            <div class="mobile-filter-option {{ !$filters['category'] ? 'active' : '' }}" onclick="applyFilter('category', '')">
                Tất cả danh mục
            </div>
            @foreach($categories as $category)
                <div class="mobile-filter-option {{ $filters['category'] == $category->id ? 'active' : '' }}" onclick="applyFilter('category', '{{ $category->id }}')">
                    {{ strtoupper($category->name) }}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Author Section -->
    <div class="mobile-filter-section">
        <button class="mobile-filter-header" onclick="toggleFilterSection('author-section')">
            TÁC GIẢ
            <i class="fas fa-chevron-down chevron-icon" id="author-chevron"></i>
        </button>
        <div class="mobile-filter-content" id="author-section">
            <div class="mobile-filter-option {{ !$filters['author'] ? 'active' : '' }}" onclick="applyFilter('author', '')">
                Tất cả tác giả
            </div>
            @foreach($authors as $author)
                <div class="mobile-filter-option {{ $filters['author'] == $author->id ? 'active' : '' }}" onclick="applyFilter('author', '{{ $author->id }}')">
                    {{ strtoupper($author->name) }}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Brand Section -->
    <div class="mobile-filter-section">
        <button class="mobile-filter-header" onclick="toggleFilterSection('brand-section')">
            THƯƠNG HIỆU
            <i class="fas fa-chevron-down chevron-icon" id="brand-chevron"></i>
        </button>
        <div class="mobile-filter-content" id="brand-section">
            <div class="mobile-filter-option {{ !$filters['brand'] ? 'active' : '' }}" onclick="applyFilter('brand', '')">
                Tất cả thương hiệu
            </div>
            @foreach($brands as $brand)
                <div class="mobile-filter-option {{ $filters['brand'] == $brand->id ? 'active' : '' }}" onclick="applyFilter('brand', '{{ $brand->id }}')">
                    {{ strtoupper($brand->name) }}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Price Range Section -->
    <div class="mobile-filter-section">
        <button class="mobile-filter-header" onclick="toggleFilterSection('price-section')">
            KHOẢNG GIÁ
            <i class="fas fa-chevron-down chevron-icon" id="price-chevron"></i>
        </button>
        <div class="mobile-filter-content" id="price-section">
            <div class="row g-2">
                <div class="col-6">
                    <input type="number" 
                           id="mobile-min-price" 
                           class="filter-select" 
                           placeholder="Giá từ"
                           value="{{ $filters['min_price'] }}">
                </div>
                <div class="col-6">
                    <input type="number" 
                           id="mobile-max-price" 
                           class="filter-select" 
                           placeholder="Giá đến"
                           value="{{ $filters['max_price'] }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Button -->
    <button class="mobile-apply-btn" onclick="applyAllFilters()">
        ÁP DỤNG ({{ $books->count() }})
    </button>
</div>

<script>
// Global variables để lưu filter state
let tempFilters = {
    sort: '{{ $filters["sort"] ?? "newest" }}',
    category: '{{ $filters["category"] ?? "" }}',
    author: '{{ $filters["author"] ?? "" }}',
    brand: '{{ $filters["brand"] ?? "" }}',
    min_price: '{{ $filters["min_price"] ?? "" }}',
    max_price: '{{ $filters["max_price"] ?? "" }}'
};

// Auto-open first section on load and when sidebar opens
document.addEventListener('DOMContentLoaded', function() {
    // Do nothing on page load
});

// Modify the toggleFilterSidebar function to auto-open first section
function toggleFilterSidebar() {
    const sidebar = document.querySelector('.filter-sidebar-mobile');
    const overlay = document.querySelector('.filter-overlay');
    const btn = document.querySelector('.filter-sort-btn');
    
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    btn.classList.toggle('active');
    
    // Prevent body scroll when sidebar is open
    if (sidebar.classList.contains('show')) {
        document.body.style.overflow = 'hidden';
        // Auto-open first section when sidebar opens
        setTimeout(() => {
            const firstSection = document.getElementById('sort-section');
            const firstChevron = document.getElementById('sort-chevron');
            const firstHeader = firstSection.previousElementSibling;
            
            firstSection.classList.add('show');
            firstChevron.classList.add('rotate');
            firstHeader.classList.add('active');
        }, 100);
    } else {
        document.body.style.overflow = '';
    }
}

function closeFilterSidebar() {
    const sidebar = document.querySelector('.filter-sidebar-mobile');
    const overlay = document.querySelector('.filter-overlay');
    const btn = document.querySelector('.filter-sort-btn');
    
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
    btn.classList.remove('active');
    document.body.style.overflow = '';
}

function toggleFilterSection(sectionId) {
    const content = document.getElementById(sectionId);
    const chevronId = sectionId.replace('-section', '-chevron');
    const chevron = document.getElementById(chevronId);
    const header = content.previousElementSibling;
    
    // Close all other sections first
    document.querySelectorAll('.mobile-filter-content').forEach(section => {
        if (section.id !== sectionId) {
            section.classList.remove('show');
            const otherChevronId = section.id.replace('-section', '-chevron');
            const otherChevron = document.getElementById(otherChevronId);
            const otherHeader = section.previousElementSibling;
            if (otherChevron) otherChevron.classList.remove('rotate');
            if (otherHeader) otherHeader.classList.remove('active');
        }
    });
    
    // Toggle current section
    content.classList.toggle('show');
    if (chevron) chevron.classList.toggle('rotate');
    if (header) header.classList.toggle('active');
}

function applySort(sortValue) {
    tempFilters.sort = sortValue;
    
    // Update active state
    document.querySelectorAll('#sort-section .mobile-filter-option').forEach(el => {
        el.classList.remove('active');
    });
    event.target.classList.add('active');
}

function applyFilter(filterType, filterValue) {
    tempFilters[filterType] = filterValue;
    
    // Update active state
    const sectionId = filterType + '-section';
    document.querySelectorAll('#' + sectionId + ' .mobile-filter-option').forEach(el => {
        el.classList.remove('active');
    });
    event.target.classList.add('active');
}

function applyAllFilters() {
    // Get price values
    const minPrice = document.getElementById('mobile-min-price').value;
    const maxPrice = document.getElementById('mobile-max-price').value;
    
    tempFilters.min_price = minPrice;
    tempFilters.max_price = maxPrice;
    
    // Build URL with all filters
    const params = new URLSearchParams();
    params.append('search', '{{ $searchTerm }}');
    
    Object.keys(tempFilters).forEach(key => {
        if (tempFilters[key] && tempFilters[key] !== '') {
            params.append(key, tempFilters[key]);
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
</script>
@endsection
