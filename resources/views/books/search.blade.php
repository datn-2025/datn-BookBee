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
    
    /* Compact Filter Styles */
    .filter-header {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.75rem;
        margin-bottom: 0;
        transition: all 0.3s ease;
    }
    
    .filter-header:hover {
        background-color: #f8f9fa;
        margin: -1rem -1rem 0 -1rem;
        padding: 1rem 1rem 0.75rem 1rem;
        border-radius: 8px 8px 0 0;
    }
    
    .filter-content {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .filter-select {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
        margin-bottom: 0.5rem;
    }
    
    .filter-select option {
        padding: 0.5rem;
    }
    
    /* Icon rotation animation */
    #filter-toggle-icon {
        transition: transform 0.3s ease;
        color: #6c757d;
    }
    
    /* Active filter indication */
    .filter-select:not([value=""]):valid {
        border-left: 3px solid #28a745;
        background-color: #f8fff9;
    }
    
    /* Button improvements */
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    
    /* Compact spacing */
    .filter-card {
        margin-bottom: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: box-shadow 0.3s ease;
    }
    
    .filter-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    /* Adidas Style Filter Button */
    .adidas-filter-btn {
        background: white;
        border: 1px solid #000;
        color: #000;
        padding: 12px 24px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 180px;
    }

    .adidas-filter-btn:hover {
        background: #000;
        color: white;
    }

    /* Adidas Style Overlay */
    .adidas-filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
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
        max-width: 400px;
        height: 100vh;
        background: white;
        z-index: 9999;
        transition: right 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        display: flex;
        flex-direction: column;
        box-shadow: -2px 0 24px rgba(0, 0, 0, 0.15);
    }

    .adidas-filter-sidebar.show {
        right: 0;
    }

    /* Header */
    .adidas-filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e5e5e5;
        background: #f5f5f5;
    }

    .adidas-filter-title {
        font-size: 18px;
        font-weight: 700;
        color: #000;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    .adidas-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #000;
        cursor: pointer;
        padding: 8px;
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
        border-bottom: 1px solid #e5e5e5;
    }

    .adidas-filter-group-header {
        width: 100%;
        background: none;
        border: none;
        padding: 20px 24px;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s ease;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 14px;
        color: #000;
    }

    .adidas-filter-group-header:hover {
        background: #f5f5f5;
    }

    .adidas-filter-group-header i {
        transition: transform 0.3s ease;
        font-size: 12px;
        color: #666;
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
        max-height: 400px;
    }

    /* Filter Options */
    .adidas-filter-option {
        padding: 16px 48px 16px 24px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        color: #666;
        border-bottom: 1px solid #f0f0f0;
        position: relative;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .adidas-filter-option:hover {
        background: #f0f0f0;
        color: #000;
    }

    .adidas-filter-option.active {
        background: #000;
        color: white;
        font-weight: 600;
    }

    .adidas-filter-option.active::after {
        content: '✓';
        position: absolute;
        right: 24px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: bold;
    }

    /* Price Inputs */
    .adidas-price-inputs {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 24px;
    }

    .adidas-price-input {
        flex: 1;
        border: 1px solid #ddd;
        padding: 12px 16px;
        font-size: 14px;
        text-align: center;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .adidas-price-input:focus {
        border-color: #000;
    }

    .adidas-price-separator {
        color: #666;
        font-weight: bold;
    }

    /* Footer */
    .adidas-filter-footer {
        padding: 24px;
        border-top: 1px solid #e5e5e5;
        background: #f5f5f5;
    }

    .adidas-apply-btn {
        width: 100%;
        background: #000;
        color: white;
        border: none;
        padding: 16px 24px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .adidas-apply-btn:hover {
        background: #333;
        transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .adidas-filter-sidebar {
            max-width: 100%;
        }
        
        .adidas-filter-title {
            font-size: 16px;
        }
        
        .adidas-filter-option {
            padding: 14px 40px 14px 20px;
            font-size: 13px;
        }
        
        .adidas-price-inputs {
            padding: 12px 20px;
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
                <!-- Search Results - Full width -->
                <div class="col-12 mb-4">
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
                            <!-- Filter & Sort Button - Adidas Style -->
                            <button class="adidas-filter-btn" onclick="toggleFilterSidebar()">
                                <span>LỌC & SẮP XẾP</span>
                                <i class="fas fa-bars ms-2"></i>
                            </button>
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
                                <div class="col-md-6 col-lg-3">
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
            </div>
        </div>
    </section>
</div>

<!-- Adidas Style Filter Sidebar -->
<div class="adidas-filter-overlay" onclick="closeFilterSidebar()"></div>
<div class="adidas-filter-sidebar">
    <!-- Header -->
    <div class="adidas-filter-header">
        <h3 class="adidas-filter-title">Lọc & Sắp xếp</h3>
        <button class="adidas-close-btn" onclick="closeFilterSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Content -->
    <div class="adidas-filter-content">
        <!-- Sort By Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-sort')">
                <span>SORT BY</span>
                <i class="fas fa-chevron-down" id="adidas-sort-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-sort">
                <div class="adidas-filter-option {{ $filters['sort'] == 'price_asc' ? 'active' : '' }}" onclick="selectSort('price_asc')">
                    GIÁ (THẤP - CAO)
                </div>
                <div class="adidas-filter-option {{ $filters['sort'] == 'newest' ? 'active' : '' }}" onclick="selectSort('newest')">
                    MỚI NHẤT TRƯỚC
                </div>
                <div class="adidas-filter-option {{ $filters['sort'] == 'oldest' ? 'active' : '' }}" onclick="selectSort('oldest')">
                    BÁN CHẠY NHẤT
                </div>
                <div class="adidas-filter-option {{ $filters['sort'] == 'price_desc' ? 'active' : '' }}" onclick="selectSort('price_desc')">
                    GIÁ (CAO - THẤP)
                </div>
            </div>
        </div>

        <!-- Danh mục Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-category')">
                <span>DANH MỤC</span>
                <i class="fas fa-chevron-down" id="adidas-category-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-category">
                <div class="adidas-filter-option {{ !$filters['category'] ? 'active' : '' }}" onclick="selectFilter('category', '')">
                    TẤT CẢ DANH MỤC
                </div>
                @foreach($categories as $category)
                    <div class="adidas-filter-option {{ $filters['category'] == $category->id ? 'active' : '' }}" onclick="selectFilter('category', '{{ $category->id }}')">
                        {{ strtoupper($category->name) }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tác giả Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-author')">
                <span>TÁC GIẢ</span>
                <i class="fas fa-chevron-down" id="adidas-author-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-author">
                <div class="adidas-filter-option {{ !$filters['author'] ? 'active' : '' }}" onclick="selectFilter('author', '')">
                    TẤT CẢ TÁC GIẢ
                </div>
                @foreach($authors as $author)
                    <div class="adidas-filter-option {{ $filters['author'] == $author->id ? 'active' : '' }}" onclick="selectFilter('author', '{{ $author->id }}')">
                        {{ strtoupper($author->name) }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Nhà xuất bản Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-brand')">
                <span>NHÀ XUẤT BẢN</span>
                <i class="fas fa-chevron-down" id="adidas-brand-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-brand">
                <div class="adidas-filter-option {{ !$filters['brand'] ? 'active' : '' }}" onclick="selectFilter('brand', '')">
                    TẤT CẢ NXB
                </div>
                @foreach($brands as $brand)
                    <div class="adidas-filter-option {{ $filters['brand'] == $brand->id ? 'active' : '' }}" onclick="selectFilter('brand', '{{ $brand->id }}')">
                        {{ strtoupper($brand->name) }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Khoảng giá Section -->
        <div class="adidas-filter-group">
            <button class="adidas-filter-group-header" onclick="toggleAdidasSection('adidas-price')">
                <span>KHOẢNG GIÁ</span>
                <i class="fas fa-chevron-down" id="adidas-price-icon"></i>
            </button>
            <div class="adidas-filter-group-content" id="adidas-price">
                <div class="adidas-price-inputs">
                    <input type="number" 
                           id="adidas-min-price" 
                           class="adidas-price-input" 
                           placeholder="Giá từ"
                           value="{{ $filters['min_price'] }}">
                    <span class="adidas-price-separator">-</span>
                    <input type="number" 
                           id="adidas-max-price" 
                           class="adidas-price-input" 
                           placeholder="Giá đến"
                           value="{{ $filters['max_price'] }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Button -->
    <div class="adidas-filter-footer">
        <button class="adidas-apply-btn" onclick="applyAdidasFilters()">
            ÁP DỤNG ({{ $books->count() }})
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
