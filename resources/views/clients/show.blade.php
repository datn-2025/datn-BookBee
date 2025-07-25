@extends('layouts.app')
@php
    use Carbon\Carbon;
@endphp
@section('title', isset($combo) ? $combo->name : ($book->title ?? 'Chi tiết'))
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=AdihausDIN:wght@400;700&family=TitilliumWeb:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <style>
        /* Scope all styles to product-detail-page only */
        .product-detail-page .adidas-font {
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
        }

        /* Title styling - Optimized for long titles */
        .product-detail-page .product-title {
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            font-weight: 700;
            line-height: 1.25;
            /* Tăng nhẹ để dễ đọc hơn */
            color: #000;
            margin: 0 0 0.75rem 0;
            /* Giảm margin dưới */
            padding: 0.25rem 0;
            /* Thêm padding để dễ đọc */
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 4.5em;
            /* Giới hạn chiều cao tối đa */
        }

        .product-detail-page .product-title.combo-title {
            font-size: 2.125rem;
            /* 34px - Giảm nhẹ từ 36px */
            line-height: 1.2;
            /* Line-height nhỏ hơn cho tiêu đề lớn */
        }

        .product-detail-page .product-title.book-title {
            font-size: 1.875rem;
            /* 30px - Tăng từ 32px */
            line-height: 1.3;
            /* Line-height lớn hơn cho dễ đọc */
        }

        /* Điều chỉnh cho mobile */
        @media (max-width: 768px) {
            .product-detail-page .product-title.combo-title {
                font-size: 1.75rem;
                /* 28px */
                line-height: 1.25;
            }

            .product-detail-page .product-title.book-title {
                font-size: 1.625rem;
                /* 26px */
                line-height: 1.3;
            }
        }

        .product-detail-page .status-coming-soon {
            color: #ff6900;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-detail-page .status-discontinued {
            color: #e74c3c;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-detail-page .status-out-of-stock {
            color: #e74c3c;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-detail-page .status-in-stock {
            color: #27ae60;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Enhanced Ebook Status Styling */
        .product-detail-page .ebook-badge {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-detail-page .ebook-badge::before {
            content: '📱';
            font-size: 1rem;
        }

        /* Hide quantity section for ebooks */
        .product-detail-page .quantity-section.ebook-hidden {
            display: none !important;
        }

        /* Enhanced Image Styling */
        .product-detail-page .product-image-main {
            border-radius: 0;
            background: #fff;
        }

        .product-detail-page .product-image {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-detail-page .thumbnail-container {
            transition: all 0.3s ease;
        }

        .product-detail-page .thumbnail-container.active {
            transform: scale(1.05);
        }

        .product-detail-page .thumbnail-image {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced Buttons */
        .product-detail-page .adidas-btn {
            background: #000;
            color: #fff;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: 2px solid #000;
        }

        .product-detail-page .adidas-btn:hover {
            background: #fff;
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .product-detail-page .adidas-btn-enhanced {
            border: none;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            background: #000;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
            letter-spacing: 2px;
            font-weight: 600;
            text-transform: uppercase;
            border: 2px solid #000;
        }

        .product-detail-page .adidas-btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .product-detail-page .adidas-btn-enhanced:hover {
            background: #333 !important;
            color: #fff !important;
            border-color: #333;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .product-detail-page .adidas-btn-enhanced:hover::before {
            left: 100%;
        }

        .product-detail-page .adidas-btn-enhanced:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .product-detail-page .adidas-btn-enhanced .relative {
            transition: all 0.3s ease;
        }

        .product-detail-page .wishlist-btn {
            border-radius: 0;
            position: relative;
            overflow: hidden;
            background: #fff;
            color: #000;
            border: 2px solid #000;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .product-detail-page .wishlist-btn:hover {
            background: #000 !important;
            color: #fff !important;
            border-color: #000;
        }

        .product-detail-page .wishlist-btn:hover i {
            transform: scale(1.1);
            color: #fff !important;
        }

        /* Enhanced Form Elements */
        .product-detail-page .adidas-select {
            border-radius: 0;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-weight: 600;
            background-image: none;
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            padding: 1rem 1.5rem;
            appearance: none;
            background-color: #fff;
        }

        .product-detail-page .adidas-select:focus {
            border-color: #000;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        .product-detail-page .adidas-input {
            border-radius: 0;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            font-weight: 600;
            background-color: #fff;
        }

        .product-detail-page .adidas-input:focus {
            border-color: #000;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Quantity Controls */
        .product-detail-page .quantity-btn-enhanced {
            border-radius: 0;
            cursor: pointer;
            background: #fff;
            color: #000;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            font-weight: 600;
            width: 3.5rem;
            height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-detail-page .quantity-btn-enhanced:hover {
            background: #000;
            color: #fff;
            border-color: #000;
            transform: translateY(-1px);
        }

        .product-detail-page .quantity-input-enhanced {
            border-radius: 0;
            background: #fff;
            color: #000;
            border: 2px solid #ddd;
            border-left: none;
            border-right: none;
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            font-weight: 600;
            text-align: center;
            width: 5rem;
            height: 3.5rem;
        }

        .product-detail-page .quantity-input-enhanced:focus {
            outline: none;
            border-color: #000;
            border-left: 2px solid #000;
            border-right: 2px solid #000;
        }

        /* Enhanced Share Buttons */
        .product-detail-page .share-btn {
            background: #f5f5f5;
            transition: all 0.3s ease;
        }

        .product-detail-page .share-btn:hover {
            background: #000;
            color: #fff;
            transform: translateY(-2px);
        }

        .product-detail-page .share-btn-enhanced {
            border-radius: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
            color: #666;
            border: 1px solid #ddd;
        }

        .product-detail-page .share-btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: #000;
            border-color: #000;
        }

        .product-detail-page .share-btn-enhanced:hover i {
            color: #fff;
        }

        .product-detail-page .share-btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Navigation */
        .product-detail-page .breadcrumb-item {
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .product-detail-page .breadcrumb-item.active {
            color: #000;
        }

        /* Enhanced Sections */
        .product-detail-page .section-title {
            border-left: 4px solid #000;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .product-detail-page .review-card {
            border-left: 3px solid #000;
            transition: all 0.3s ease;
        }

        .product-detail-page .review-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .product-detail-page .related-product-card {
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }

        .product-detail-page .related-product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-color: #000;
        }

        /* Price Section Enhancement */
        .product-detail-page .price-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 2rem;
            border: 1px solid #e9ecef;
        }

        /* Stock Status Enhancement */
        .product-detail-page .stock-status {
            padding: 1rem;
            background: #f8f9fa;
            border-left: 4px solid #28a745;
        }

        /* Attribute Group Enhancement */
        .product-detail-page .attribute-group {
            background: #f8f9fa;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
        }

        /* Purchase Section Enhancement */
        .product-detail-page .purchase-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 2rem;
            border: 2px solid #e9ecef;
        }

        /* Responsive Enhancements */
        @media (max-width: 768px) {
            .product-detail-page .grid.grid-cols-1.lg\\:grid-cols-2 {
                gap: 2rem;
            }

            .product-detail-page h1 {
                font-size: 2rem;
                line-height: 1.2;
            }

            .product-detail-page .price-section {
                padding: 1.5rem;
            }

            .product-detail-page .purchase-section {
                padding: 1.5rem;
            }

            .product-detail-page .adidas-select {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }

            .product-detail-page .quantity-btn-enhanced {
                width: 3rem;
                height: 3rem;
            }

            .product-detail-page .quantity-input-enhanced {
                width: 4rem;
                height: 3rem;
            }

            .product-detail-page .adidas-btn-enhanced {
                height: 3rem;
                font-size: 0.875rem;
                padding: 0 1rem;
            }
        }

        /* Animation Classes */
        .product-detail-page .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-detail-page .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
@endpush
@section('content')
    <div class="product-detail-page">
        @if(isset($combo))
            {{-- Hiển thị phần thông tin combo ở đầu, giữ nguyên các section chi tiết phía dưới --}}
            <div class="bg-gray-50 py-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <nav class="flex items-center space-x-2 text-sm adidas-font">
                        <a href="/" class="breadcrumb-item hover:text-black transition-colors duration-300 flex items-center">
                            <i class="fas fa-home mr-1"></i>
                            <span>Trang chủ</span>
                        </a>
                        <span class="text-gray-400">/</span>
                        <a href="{{ route('home') }}"
                            class="breadcrumb-item hover:text-black transition-colors duration-300">Combo Sách</a>
                        <span class="text-gray-400">/</span>
                        <span class="breadcrumb-item active">{{ $combo->name }}</span>
                    </nav>
                </div>
            </div>
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                    <!-- Ảnh combo -->
                    <div class="space-y-6">
                        <div class="relative group">
                            <div class="aspect-square bg-white border border-gray-100 overflow-hidden">
                                <img src="{{ $combo->cover_image ? asset('storage/' . $combo->cover_image) : 'https://via.placeholder.com/400x500?text=Combo+Sách' }}"
                                    alt="{{ $combo->name }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </div>
                        </div>
                    </div>
                    <!-- Thông tin combo -->
                    <div class="space-y-8 adidas-font lg:pl-8">
                        <div class="space-y-4 pb-6 border-b border-gray-200">
                            <h1 class="product-title combo-title">{{ $combo->name }}</h1>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">SỐ SÁCH</span>
                                    <span class="text-black font-semibold adidas-font">{{ $combo->books->count() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">NGÀY BẮT
                                        ĐẦU</span>
                                    <span
                                        class="text-black font-semibold adidas-font">{{ optional($combo->start_date)->format('d/m/Y') ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">NGÀY KẾT
                                        THÚC</span>
                                    <span
                                        class="text-black font-semibold adidas-font">{{ optional($combo->end_date)->format('d/m/Y') ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">TRẠNG
                                        THÁI</span>
                                    @php
                                        $statusText = $combo->status === 'active' ? 'Đang mở bán' : 'Ngừng bán';
                                        $statusClass = $combo->status === 'active' ? 'status-in-stock' : 'status-out-of-stock';
                                    @endphp
                                    <span class="font-semibold adidas-font {{ $statusClass }}">{{ $statusText }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">GIÁ
                                        COMBO</span>
                                    <span
                                        class="text-black font-bold text-lg adidas-font">{{ number_format($combo->combo_price, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                        </div>
                        <div class="price-section space-y-4">
                            <div class="flex items-end space-x-4">
                                <span
                                    class="text-4xl font-bold text-black adidas-font">{{ number_format($combo->combo_price, 0, ',', '.') }}₫</span>
                                @php
                                    $now = now();
                                    $startDate = $combo->start_date ? \Carbon\Carbon::parse($combo->start_date) : null;
                                    $endDate = $combo->end_date ? \Carbon\Carbon::parse($combo->end_date) : null;
                                    $isActive = $combo->status === 'active' && 
                                              (!$startDate || $now >= $startDate) && 
                                              (!$endDate || $now <= $endDate);
                                              
                                    if (!$isActive) {
                                        if ($combo->status !== 'active') {
                                            $statusText = 'Ngừng bán';
                                            $statusDot = 'bg-gray-500';
                                            $badgeClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                        } elseif ($startDate && $now < $startDate) {
                                            $statusText = 'Chưa bắt đầu';
                                            $statusDot = 'bg-yellow-500';
                                            $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        } elseif ($endDate && $now > $endDate) {
                                            $statusText = 'Đã kết thúc';
                                            $statusDot = 'bg-red-500';
                                            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                        }
                                    } else {
                                        $statusText = 'Đang mở bán';
                                        $statusDot = 'bg-green-500';
                                        $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold border adidas-font uppercase tracking-wider {{ $badgeClass }}">
                                    <span class="w-2 h-2 rounded-full mr-2 {{ $statusDot }} inline-block"></span>{{ $statusText }}
                                </span>
                                
                                @if($isActive && $startDate)
                                    <span class="text-sm text-gray-600 adidas-font">
                                        (Bắt đầu: {{ $startDate->format('d/m/Y') }})
                                    </span>
                                @endif
                                @if($isActive && $endDate)
                                    <span class="text-sm text-gray-600 adidas-font">
                                        (Kết thúc: {{ $endDate->format('d/m/Y') }})
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- Danh sách sách trong combo -->
                        <div class="combo-books-list bg-white border border-gray-100 p-4 mt-6">
                            <h2
                                class="text-lg font-bold text-black mb-3 flex items-center adidas-font uppercase tracking-wider">
                                <i class="fas fa-book text-base mr-2 text-black"></i>Danh sách sách trong combo
                            </h2>
                            <ul class="space-y-2 list-disc pl-6">
                                @foreach($combo->books as $book)
                                    <li class="flex flex-col md:flex-row md:items-center gap-2">
                                        <a href="{{ route('books.show', $book->slug) }}"
                                            class="text-base text-blue-600 hover:underline font-semibold adidas-font">{{ $book->title }}</a>
                                        <span class="text-gray-500 text-sm adidas-font">@if($book->authors->count()) - Tác giả:
                                        {{ $book->authors->pluck('name')->join(', ') }} @endif</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Form mua combo -->
                        <form action="{{ route('cart.add') }}" method="POST" class="mt-8">
                            @csrf
                            <input type="hidden" name="combo_id" value="{{ $combo->id }}">
                            <input type="hidden" name="type" value="combo">
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-black uppercase tracking-wider mb-3 adidas-font">Số
                                    lượng</label>
                                <div class="flex items-center w-fit">
                                    <button type="button" class="quantity-btn-enhanced" id="comboDecrementBtn">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" name="quantity" id="comboQuantity" value="1" min="1"
                                        class="quantity-input-enhanced adidas-font" />
                                    <button type="button" class="quantity-btn-enhanced" id="comboIncrementBtn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit"
                                class="adidas-btn-enhanced w-full h-16 bg-black text-white font-bold text-lg uppercase tracking-wider transition-all duration-300 flex items-center justify-center adidas-font"
                                @php
                                    $now = now();
                                    $startDate = $combo->start_date ? \Carbon\Carbon::parse($combo->start_date) : null;
                                    $endDate = $combo->end_date ? \Carbon\Carbon::parse($combo->end_date) : null;
                                    $isActive = $combo->status === 'active' && 
                                              (!$startDate || $now >= $startDate) && 
                                              (!$endDate || $now <= $endDate);
                                @endphp
                                @if(!$isActive) disabled style="opacity:0.6;pointer-events:none;" @endif>
                                <i class="fas fa-shopping-bag mr-3"></i>
                                <span>THÊM VÀO GIỎ HÀNG</span>
                            </button>
                            <!-- Wishlist Button -->
                            <button type="button"
                                class="wishlist-btn w-full h-14 border-2 border-black text-black font-bold text-lg uppercase tracking-wider transition-all duration-300 flex items-center justify-center mt-3 adidas-font">
                                <i class="far fa-heart mr-3"></i>
                                <span>YÊU THÍCH</span>
                            </button>
                            <!-- Enhanced Share Section -->
                            <div class="share-section pt-8 border-t border-gray-200 mt-8">
                                <h3 class="text-sm font-bold text-black uppercase tracking-wider mb-6">Chia sẻ sản phẩm</h3>
                                <div class="flex space-x-4">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                        target="_blank" class="share-btn-enhanced w-12 h-12 flex items-center justify-center">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}"
                                        target="_blank" class="share-btn-enhanced w-12 h-12 flex items-center justify-center">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}"
                                        target="_blank" class="share-btn-enhanced w-12 h-12 flex items-center justify-center">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text={{ urlencode(url()->current()) }}"
                                        target="_blank" class="share-btn-enhanced w-12 h-12 flex items-center justify-center">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}" target="_blank"
                                        class="share-btn-enhanced w-12 h-12 flex items-center justify-center">
                                        <i class="fab fa-telegram-plane"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                        <script>
                            function updateComboQty(change) {
                                const input = document.getElementById('comboQuantity');
                                let val = parseInt(input.value) || 1;
                                val += change;
                                if (val < 1) val = 1;
                                input.value = val;
                            }
                        </script>
                    </div>
                </div>
            </section>

        @endif

        {{-- Sau phần combo hoặc book info, luôn render các section chi tiết phía dưới --}}
        @if(isset($relatedCombos) && $relatedCombos->count())
            {{-- Mô tả combo (đồng bộ style sách đơn) --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mt-16 bg-white/90 shadow-sm border border-gray-200 p-6">
                    <h2 class="text-2xl font-semibold mb-4 border-b border-black/10 pb-2 text-black flex items-center">
                        <i class="fas fa-align-left mr-2 text-black"></i>Mô tả combo
                    </h2>
                    @php
                        $comboDesc = strip_tags($combo->description ?? '');
                        $showComboMore = \Illuminate\Support\Str::length($comboDesc) > 200;
                    @endphp
                    <div id="comboDescription" class="text-gray-700 text-base leading-relaxed text-left"
                        data-full="{{ e($comboDesc) }}"
                        data-short="{{ \Illuminate\Support\Str::limit($comboDesc, 200, '...') }}">
                        @if (empty($comboDesc))
                            <div class="text-center"><span class="italic text-gray-400">Không có mô tả nào</span></div>
                        @else
                            {{ $showComboMore ? \Illuminate\Support\Str::limit($comboDesc, 200, '...') : $comboDesc }}
                        @endif
                    </div>
                    @if($showComboMore)
                        <button id="showMoreComboBtn" class="text-blue-500 mt-2 text-sm hover:underline">Xem thêm</button>
                    @endif
                </div>
            </div>

            {{-- AI Summary Section for Combo --}}
            @if(isset($combo))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20 space-y-8">
                    <!-- Section Header with Adidas Style -->
                    <div class="relative">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="w-1 h-12 bg-gradient-to-b from-blue-600 to-purple-600"></div>
                            <div>
                                <h2 class="adidas-font text-3xl font-bold text-black uppercase tracking-wider">
                                    TÓM TẮT AI - COMBO
                                </h2>
                                <p class="text-gray-600 mt-2">Được tạo bởi trí tuệ nhân tạo</p>
                            </div>
                        </div>
                    </div>

                    {{-- AI Summary Component for Combo --}}
                    @include('components.ai-summary', ['combo' => $combo])
                </div>
            @endif

            {{-- Sản phẩm liên quan (đồng bộ style sách đơn, fix ảnh) --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-1 h-12 bg-black"></div>
                    <div>
                        <h2 class="adidas-font text-3xl font-bold text-black uppercase tracking-wider">
                            SẢN PHẨM LIÊN QUAN
                        </h2>
                        <p class="text-sm text-gray-600 uppercase tracking-wide font-medium mt-1">Có thể bạn sẽ thích</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @if(!isset($relatedCombos) || $relatedCombos->count() == 0)
                        <div class="col-span-4 text-center"><span class="italic text-gray-400">Không có sản phẩm liên quan</span>
                        </div>
                    @endif
                    @foreach ($relatedCombos as $related)
                        <div class="bg-white border border-gray-200 overflow-hidden group hover:border-black transition-all duration-300 p-2 cursor-pointer relative"
                            onclick="window.location.href='{{ route('combos.show', $related->slug ?? $related->id) }}'">
                            <div class="relative aspect-square bg-white border border-gray-100 overflow-hidden mb-2">
                                <a href="{{ route('combos.show', $related->slug ?? $related->id) }}" class="block w-full h-full">
                                    <img src="{{ $related->cover_image ? asset('storage/' . $related->cover_image) : asset('images/default.jpg') }}"
                                        alt="{{ $related->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </a>
                                @php $relatedStock = $related->combo_stock ?? 0; @endphp
                                @if($relatedStock <= 0)
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-red-600 text-white text-xs font-bold uppercase tracking-wider px-2 py-0.5">
                                            HẾT HÀNG
                                        </span>
                                    </div>
                                @endif
                                <!-- Wishlist Button -->
                                <div class="absolute top-2 right-2">
                                    <button
                                        class="w-10 h-10 bg-white bg-opacity-90 flex items-center justify-center border border-gray-200 hover:bg-black hover:text-white hover:border-black transition-all duration-300 transform hover:scale-110"
                                        onclick="event.stopPropagation();">
                                        <i class="far fa-heart text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-2">
                                <h3
                                    class="font-bold text-black text-base leading-tight group-hover:text-gray-600 transition-colors duration-300 line-clamp-2 min-h-[40px]">
                                    <span class="hover:underline">{{ $related->name }}</span>
                                </h3>
                                <p class="text-xs text-gray-600 uppercase tracking-wide font-medium min-h-[18px]">
                                    {{ $related->books->pluck('authors')->flatten()->pluck('name')->unique()->join(', ') ?: 'KHÔNG RÕ TÁC GIẢ' }}
                                </p>
                                <div class="flex items-center space-x-2 pt-1">
                                    <span class="text-lg font-bold text-black">
                                        {{ number_format($related->combo_price, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                <div class="pt-1">
                                    <button onclick="event.stopPropagation(); addRelatedToCart('{{ $related->id }}')"
                                        class="adidas-btn-enhanced w-full h-10 bg-black text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-center {{ $relatedStock <= 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-800' }}"
                                        {{ $relatedStock <= 0 ? 'disabled' : '' }}>
                                        <span class="relative flex items-center space-x-1">
                                            <i class="fas fa-shopping-cart text-xs"></i>
                                            <span>{{ $relatedStock <= 0 ? 'HẾT HÀNG' : 'THÊM VÀO GIỎ' }}</span>
                                            <i
                                                class="fas fa-arrow-right text-xs transform group-hover/btn:translate-x-1 transition-transform duration-300"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-center pt-8 mb-8">
                    <a href="{{ route('books.index') }}"
                        class="adidas-btn-enhanced px-8 py-4 bg-white text-black border-2 border-black font-bold uppercase tracking-wider hover:bg-black hover:text-white transition-all duration-300 flex items-center space-x-3">
                        <span>XEM TẤT CẢ COMBO</span>
                    </a>
                </div>
            </div>
        @endif

        @if(!isset($combo))
                {{-- Breadcrumb --}}
                <div class="bg-gray-50 py-4">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <nav class="flex items-center space-x-2 text-sm adidas-font">
                            <a href="/" class="breadcrumb-item hover:text-black transition-colors duration-300 flex items-center">
                                <i class="fas fa-home mr-1"></i>
                                <span>Trang chủ</span>
                            </a>
                            <span class="text-gray-400">/</span>
                            <a href="{{ route('books.index') }}"
                                class="breadcrumb-item hover:text-black transition-colors duration-300">
                                {{ $book->category->name ?? 'Danh mục' }}
                            </a>
                            <span class="text-gray-400">/</span>
                            <span class="breadcrumb-item active">{{ $book->title }}</span>
                        </nav>
                    </div>
                </div>

                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                        {{-- Product Images --}}
                        <div class="space-y-6">
                            <div class="relative group">
                                <div class="aspect-square bg-white border border-gray-100 overflow-hidden">
                                    <img src="{{ $book->images->first() ? asset('storage/' . $book->images->first()->image_url) : ($book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default.jpg')) }}"
                                        alt="{{ $book->title }}" id="mainImage"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
                                @if ($book->images->count() > 1)
                                    <div class="grid grid-cols-5 gap-3 mt-4">
                                        @foreach ($book->images as $index => $image)
                                            <div class="relative group cursor-pointer {{ $index === 0 ? 'ring-2 ring-black' : '' }}"
                                                onclick="changeMainImage('{{ asset('storage/' . $image->image_url) }}', this)">
                                                <div
                                                    class="aspect-square bg-white border border-gray-200 overflow-hidden transition-all duration-300 hover:border-black">
                                                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="{{ $book->title }}"
                                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- Enhanced Product Info --}}
                        <div class="space-y-8 adidas-font lg:pl-8">
                            {{-- Product Header --}}
                            <div class="space-y-4 pb-6 border-b border-gray-200">
                                <h1 class="product-title book-title">{{ $book->title }}</h1>
                            </div>
                            {{-- Quick Info Grid --}}
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 font-medium">TÁC GIẢ</span>
                                        <span class="text-black font-semibold">
                                            @if($book->authors && $book->authors->count())
                                                {{ $book->authors->pluck('name')->join(', ') }}
                                            @else
                                                Không rõ
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 font-medium">THƯƠNG HIỆU</span>
                                        <span class="text-black font-semibold">{{ $book->brand->name ?? 'Không rõ' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 font-medium">ISBN</span>
                                        <span class="text-black font-semibold">{{ $book->isbn }}</span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 font-medium">XUẤT BẢN</span>
                                        <span class="text-black font-semibold">{{ $book->publication_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 font-medium">SỐ TRANG</span>
                                        <span class="text-black font-semibold">{{ $book->page_count }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 font-medium">THỂ LOẠI</span>
                                        <span class="text-black font-semibold">{{ $book->category->name ?? 'Không rõ' }}</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Price Section --}}
                            @php
                                $formats = $book->formats->sortByDesc(fn($f) => $f->format_name === 'Ebook');
                                $defaultFormat = $formats->first();
                                $defaultPrice = $defaultFormat->price ?? $book->price;
                                $defaultStock = $defaultFormat->stock ?? $book->stock;
                                $discount = $defaultFormat->discount ?? 0;
                                $finalPrice = $defaultPrice - ($defaultPrice * ($discount / 100));
                            @endphp
                            <div class="price-section space-y-4">
                                <div class="flex items-end space-x-4">
                                    <span id="bookPrice" data-base-price="{{ $defaultPrice }}"
                                        class="text-4xl font-bold text-black adidas-font">{{ number_format($finalPrice, 0, ',', '.') }}₫</span>
                                    @if ($discount > 0)
                                        <span id="originalPrice"
                                            class="text-xl text-gray-500 line-through adidas-font">{{ number_format($defaultPrice, 0, ',', '.') }}₫</span>
                                        <span id="discountText"
                                            class="bg-red-600 text-white px-3 py-1 text-sm font-bold adidas-font uppercase tracking-wider">-<span
                                                id="discountPercent">{{ $discount }}</span>%</span>
                                    @else
                                        <span id="originalPrice" class="text-xl text-gray-500 line-through adidas-font"
                                            style="display: none;"></span>
                                        <span id="discountText"
                                            class="bg-red-600 text-white px-3 py-1 text-sm font-bold adidas-font uppercase tracking-wider"
                                            style="display: none;">
                                            -<span id="discountPercent">0</span>%
                                        </span>
                                    @endif
                                    <!-- Stock Status with Enhanced Design -->
                                    @php
                                        $isEbook = false;
                                        if (isset($defaultFormat->format_name)) {
                                            $isEbook = stripos($defaultFormat->format_name, 'ebook') !== false;
                                        }
                                        $defaultStock = (int) ($defaultFormat->stock ?? $book->stock ?? 0);

                                        if ($isEbook) {
                                            $statusText = 'EBOOK - CÓ SẴN';
                                            $statusDot = 'bg-blue-500';
                                            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        } elseif ($defaultStock > 0) {
                                            $statusText = 'CÒN HÀNG';
                                            $statusDot = 'bg-green-500';
                                            $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                        } elseif ($defaultStock === 0) {
                                            $statusText = 'HẾT HÀNG';
                                            $statusDot = 'bg-red-500';
                                            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                        } elseif ($defaultStock === -1) {
                                            $statusText = 'SẮP RA MẮT';
                                            $statusDot = 'bg-yellow-500';
                                            $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        } elseif ($defaultStock === -2) {
                                            $statusText = 'NGƯNG KINH DOANH';
                                            $statusDot = 'bg-gray-500';
                                            $badgeClass = 'bg-gray-100 text-gray-700 border-gray-300';
                                        } else {
                                            $statusText = 'HẾT HÀNG';
                                            $statusDot = 'bg-red-500';
                                            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                        }
                                    @endphp
                                    <div class="flex items-end space-x-4 mt-2">
                                        <span id="stockBadge"
                                            class="inline-flex items-center px-3 py-1 text-sm font-semibold border adidas-font uppercase tracking-wider {{ $badgeClass }}">
                                            <span id="stockDot"
                                                class="w-2 h-2 rounded-full mr-2 {{ $statusDot }} inline-block"></span>
                                            <span id="stockText">{{ $statusText }}</span>
                                        </span>
                                        @if(($defaultStock > 0 || $isEbook) && $defaultStock !== -1 && $defaultStock !== -2)
                                            <span id="stockQuantityDisplay" class="text-sm text-gray-600 adidas-font">
                                                (<span class="font-bold text-black" id="productQuantity">{{ $defaultStock }}</span> cuốn
                                                còn lại)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Quà tặng kèm -->
                            @if(isset($bookGifts) && $bookGifts->count())
                                <div class="book-gifts-section mt-8">
                                    <h3
                                        class="text-lg font-bold text-black mb-3 flex items-center adidas-font uppercase tracking-wider">
                                        <i class="fas fa-gift text-base mr-2 text-black"></i>Quà tặng kèm
                                    </h3>
                                    <ul class="space-y-3">
                                        @foreach($bookGifts as $gift)
                                            <li
                                                class="flex items-start gap-4 p-4 bg-white border border-gray-200 hover:border-black transition-all duration-200 shadow-sm">
                                                @if($gift->gift_image)
                                                    <img src="{{ asset('storage/' . $gift->gift_image) }}" alt="{{ $gift->gift_name }}"
                                                        class="w-16 h-16 object-cover shadow border border-gray-200">
                                                @else
                                                    <span
                                                        class="w-16 h-16 flex items-center justify-center bg-gray-100 text-2xl border border-gray-200"><i
                                                            class="fas fa-gift"></i></span>
                                                @endif
                                                <div class="flex-1">
                                                    <div class="font-semibold text-black text-base adidas-font">{{ $gift->gift_name }}</div>
                                                    @if($gift->gift_description)
                                                        <div class="text-sm text-gray-700 mt-1">{{ $gift->gift_description }}</div>
                                                    @endif
                                                    @if($gift->quantity > 0)
                                                        <div class="text-xs text-green-700 mt-1">Số lượng: {{ $gift->quantity }}</div>
                                                    @endif
                                                    @if($gift->start_date || $gift->end_date)
                                                        <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-2">
                                                            @if($gift->start_date)
                                                                <span>Bắt đầu: {{ Carbon::parse($gift->start_date)->format('d/m/Y') }}</span>
                                                            @endif
                                                            @if($gift->end_date)
                                                                <span>Kết thúc: {{ Carbon::parse($gift->end_date)->format('d/m/Y') }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Enhanced Format Selection -->
                            @if ($book->formats->count())
                                <div class="format-selection space-y-3">
                                    <label for="bookFormatSelect"
                                        class="block text-sm font-bold text-black uppercase tracking-wider">Định dạng sách</label>
                                    <div class="relative">
                                        <select id="bookFormatSelect"
                                            class="adidas-select w-full px-6 py-4 text-lg font-semibold appearance-none bg-white border-2 border-gray-300 focus:border-black rounded-none transition-colors duration-300">
                                            @php
                                                $ebookFormat = $book->formats->first(function ($f) {
                                                    return stripos($f->format_name, 'ebook') !== false; });
                                                $otherFormats = $book->formats->filter(function ($f) {
                                                    return stripos($f->format_name, 'ebook') === false; });
                                            @endphp
                                            @if($ebookFormat)
                                                <option value="{{ $ebookFormat->id }}" data-price="{{ $ebookFormat->price }}"
                                                    data-stock="{{ $ebookFormat->stock }}" data-discount="{{ $ebookFormat->discount }}"
                                                    data-format="{{ $ebookFormat->format_name }}" selected>{{ $ebookFormat->format_name }}
                                                </option>
                                            @endif
                                            @foreach($otherFormats as $format)
                                                <option value="{{ $format->id }}" data-price="{{ $format->price }}"
                                                    data-stock="{{ $format->stock }}" data-discount="{{ $format->discount }}"
                                                    data-format="{{ $format->format_name }}">
                                                    {{ $format->format_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                            <i class="fas fa-chevron-down text-black"></i>
                                        </div>
                                    </div>
                                    <!-- Preview Button for Ebook -->
                                    <div id="previewSection" class="@if(!$isEbook) hidden @endif mt-4">
                                        <a href="#"
                                            class="adidas-btn w-full h-12 bg-blue-600 text-white font-bold text-sm uppercase tracking-wider transition-all duration-300 flex items-center justify-center hover:bg-blue-700 adidas-font">
                                            <i class="fas fa-book-reader mr-2"></i>
                                            <span>ĐỌC THỬ</span>
                                        </a>
                                        <p class="text-sm text-gray-600 mt-2 adidas-font">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Bạn có thể đọc thử một phần nội dung của sách
                                        </p>
                                    </div>
                                </div>
                            @endif
                            <!-- Enhanced Attributes -->
                            {{-- Thuộc tính --}}
                            @if($book->attributeValues->count())
                                <div id="bookAttributesGroup" class="attribute-group space-y-4">
                                    <h3 class="text-sm font-bold text-black uppercase tracking-wider adidas-font">Tuỳ chọn sản phẩm</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($book->attributeValues->unique('attribute_id') as $attrVal)
                                            <div class="space-y-2 col-span-1">
                                                <label for="attribute_{{ $attrVal->id }}"
                                                    class="block text-sm font-bold text-black uppercase tracking-wider adidas-font">
                                                    {{ $attrVal->attribute->name ?? 'Không rõ' }}
                                                </label>
                                                @php
                                                    $filteredValues = \App\Models\BookAttributeValue::with('attributeValue')
                                                        ->where('book_id', $book->id)
                                                        ->whereHas('attributeValue', function ($q) use ($attrVal) {
                                                            $q->where('attribute_id', $attrVal->attribute_id);
                                                        })
                                                        ->get();
                                                @endphp
                                                <div class="relative">
                                                    <select name="attributes[{{ $attrVal->id }}]" id="attribute_{{ $attrVal->id }}"
                                                        class="adidas-select w-full appearance-none bg-white">
                                                        @foreach($filteredValues as $bookAttrVal)
                                                            <option value="{{ $bookAttrVal->attribute_value_id }}"
                                                                data-price="{{ $bookAttrVal->extra_price ?? 0 }}">
                                                                {{ $bookAttrVal->attributeValue->value ?? 'Không rõ' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                                        <i class="fas fa-chevron-down text-black"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <!-- Enhanced Quantity & Add to Cart Section -->
                            <div class="purchase-section space-y-6 pt-6">
                                @php
                                    $isEbook = false;
                                    if (isset($defaultFormat->format_name)) {
                                        $isEbook = stripos($defaultFormat->format_name, 'ebook') !== false;
                                    }
                                @endphp
                                <div class="quantity-section space-y-3" @if($isEbook) style="display:none" @endif>
                                    <label for="quantity"
                                        class="block text-sm font-bold text-black uppercase tracking-wider adidas-font">Số
                                        lượng</label>
                                    <div class="flex items-center w-fit">
                                        <button type="button" id="decrementBtn" class="quantity-btn-enhanced">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" id="quantity" value="1" min="1" class="quantity-input-enhanced" />
                                        <button type="button" id="incrementBtn" class="quantity-btn-enhanced">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Enhanced Add to Cart Button -->
                                <div class="space-y-4">
                                    <button id="addToCartBtn"
                                        class="adidas-btn-enhanced w-full h-16 bg-black text-white font-bold text-lg uppercase tracking-wider transition-all duration-300 flex items-center justify-center adidas-font">
                                        <i class="fas fa-shopping-bag mr-3"></i>
                                        <span>THÊM VÀO GIỎ HÀNG</span>
                                    </button>

                                    <!-- Wishlist Button -->
                                    <button
                                        class="wishlist-btn w-full h-14 border-2 border-black text-black font-bold text-lg uppercase tracking-wider transition-all duration-300 flex items-center justify-center adidas-font">
                                        <i class="far fa-heart mr-3"></i>
                                        <span>YÊU THÍCH</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Enhanced Share Section -->
                            <div class="share-section pt-8 border-t border-gray-200">
                                <h3 class="text-sm font-bold text-black uppercase tracking-wider mb-6 adidas-font">Chia sẻ sản phẩm
                                </h3>
                                <div class="flex space-x-4">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                        target="_blank"
                                        class="share-btn-enhanced w-12 h-12 flex items-center justify-center border-2 border-gray-200 hover:border-black transition-all duration-300">
                                        <i class="fab fa-facebook-f text-lg"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}" target="_blank"
                                        class="share-btn-enhanced w-12 h-12 flex items-center justify-center border-2 border-gray-200 hover:border-black transition-all duration-300">
                                        <i class="fab fa-twitter text-lg"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}"
                                        target="_blank"
                                        class="share-btn-enhanced w-12 h-12 flex items-center justify-center border-2 border-gray-200 hover:border-black transition-all duration-300">
                                        <i class="fab fa-linkedin-in text-lg"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text={{ urlencode(url()->current()) }}" target="_blank"
                                        class="share-btn-enhanced w-12 h-12 flex items-center justify-center border-2 border-gray-200 hover:border-black transition-all duration-300">
                                        <i class="fab fa-whatsapp text-lg"></i>
                                    </a>
                                    <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}" target="_blank"
                                        class="share-btn-enhanced w-12 h-12 flex items-center justify-center border-2 border-gray-200 hover:border-black transition-all duration-300">
                                        <i class="fab fa-telegram-plane text-lg"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $bookDesc = strip_tags($book->description ?? '');
                        $showBookMore = \Illuminate\Support\Str::length($bookDesc) > 200;
                    @endphp
                    @if(isset($book))
                        <div class="mt-16 bg-white/90 shadow-sm border border-gray-200 p-6">
                            <h2 class="text-2xl font-bold mb-4 border-b border-black/10 pb-2 text-black flex items-center">
                                <i class="fas fa-align-left mr-2 text-black"></i>Mô tả sách
                            </h2>
                            <div id="bookDescription" class="text-gray-700 text-base leading-relaxed text-left"
                                data-full="{{ e($bookDesc) }}" data-short="{{ \Illuminate\Support\Str::limit($bookDesc, 200, '...') }}">
                                @if (empty($bookDesc))
                                    <div class="text-center"><span class="italic text-gray-400">Không có mô tả nào</span></div>
                                @else
                                    {{ $showBookMore ? \Illuminate\Support\Str::limit($bookDesc, 200, '...') : $bookDesc }}
                                @endif
                            </div>
                            @if($showBookMore)
                                <button id="showMoreBtn" class="text-blue-500 mt-2 text-sm hover:underline">Xem thêm</button>
                            @endif
                        </div>
                    @endif

                    {{-- AI Summary Section --}}
                    @if(!isset($combo))
                    <div class="mt-20 space-y-8">
                        <!-- Section Header with Adidas Style -->
                        <div class="relative">
                            <div class="flex items-center space-x-4 mb-8">
                                <div class="w-1 h-12 bg-gradient-to-b from-blue-600 to-purple-600"></div>
                                <div>
                                    <h2 class="adidas-font text-3xl font-bold text-black uppercase tracking-wider">
                                        TÓM TẮT AI
                                    </h2>
                                    <p class="text-gray-600 mt-2">Được tạo bởi trí tuệ nhân tạo</p>
                                </div>
                            </div>
                        </div>

                        {{-- AI Summary Component --}}
                        @include('components.ai-summary', ['book' => $book])
                    </div>
                    @endif

                    {{-- Enhanced Reviews Section - Adidas Style --}}
                    <div class="mt-20 space-y-8">
                        <!-- Section Header with Adidas Style -->
                        <div class="relative">
                            <div class="flex items-center space-x-4 mb-8">
                                <div class="w-1 h-12 bg-black"></div>
                                <div>
                                    <h2 class="adidas-font text-3xl font-bold text-black uppercase tracking-wider">
                                        ĐÁNH GIÁ KHÁCH HÀNG
                                    </h2>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <div class="flex text-yellow-400 text-lg">
                                            @php
                                                $averageRating = $book->reviews->avg('rating') ?? 0;
                                                $totalReviews = $book->reviews->count();
                                            @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $averageRating)
                                                    ★
                                                @else
                                                    ☆
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-600 font-semibold">{{ number_format($averageRating, 1) }}/5
                                            ({{ $totalReviews }} đánh giá)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Reviews Container -->
                        <div class="space-y-6">
                            @forelse($book->reviews as $review)
                                <div
                                    class="review-card bg-white border-2 border-gray-100 relative overflow-hidden group hover:border-black transition-all duration-300">
                                    <!-- Header Bar -->
                                    <div class="bg-black text-white px-6 py-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-white bg-opacity-20 flex items-center justify-center">
                                                <i class="fas fa-user text-xs"></i>
                                            </div>
                                            <div>
                                                <span
                                                    class="font-bold uppercase tracking-wider text-sm adidas-font">{{ $review->user->name ?? 'KHÁCH HÀNG ẨN DANH' }}</span>
                                                <div class="flex text-yellow-400 text-xs mt-1">
                                                    @for ($i = 0; $i < $review->rating; $i++)
                                                        ★
                                                    @endfor
                                                    @for ($i = $review->rating; $i < 5; $i++)
                                                        ☆
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-gray-300 uppercase tracking-wider">
                                                {{ $review->created_at->diffForHumans() }}</div>
                                            <div class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>

                                    <!-- Content Area -->
                                    <div class="p-6">
                                        <!-- Rating Display -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-black text-white px-3 py-1 text-sm font-bold uppercase tracking-wider">
                                                    {{ $review->rating }}/5
                                                </div>
                                                <div class="flex text-yellow-400 text-lg">
                                                    @for ($i = 0; $i < $review->rating; $i++)
                                                        ★
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="w-2 h-2 bg-black"></div>
                                        </div>

                                        <!-- Comment -->
                                        <div class="relative">
                                            <div
                                                class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-black via-gray-400 to-black">
                                            </div>
                                            <div class="pl-6">
                                                <p class="text-gray-800 leading-relaxed font-medium">{{ $review->comment }}</p>
                                            </div>
                                        </div>

                                        <!-- Bottom Accent -->
                                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                                            <div class="flex items-center space-x-2 text-xs text-gray-500 uppercase tracking-wider">
                                                <i class="fas fa-check-circle w-3"></i>
                                                <span>Đánh giá đã xác thực</span>
                                            </div>
                                            <div class="flex space-x-1">
                                                <div class="w-2 h-2 bg-black"></div>
                                                <div class="w-2 h-2 bg-gray-300"></div>
                                                <div class="w-2 h-2 bg-gray-300"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Side accent -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-black via-gray-600 to-black">
                                    </div>
                                </div>
                            @empty
                                <!-- Enhanced Empty State -->
                                <div class="bg-white border-2 border-gray-100 relative overflow-hidden">
                                    <!-- Header Bar -->
                                    <div class="bg-black text-white px-6 py-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-white bg-opacity-20  flex items-center justify-center">
                                                <i class="fas fa-comments text-xs"></i>
                                            </div>
                                            <span class="font-bold uppercase tracking-wider text-sm adidas-font">CHƯA CÓ ĐÁNH GIÁ</span>
                                        </div>
                                        <div class="w-6 h-6 border border-white border-opacity-30  flex items-center justify-center">
                                            <i class="fas fa-star text-xs"></i>
                                        </div>
                                    </div>

                                    <!-- Content Area -->
                                    <div class="p-12 text-center">
                                        <div class="space-y-6">
                                            <div class="w-16 h-16 bg-gray-100  flex items-center justify-center mx-auto">
                                                <i class="fas fa-star text-2xl text-gray-400"></i>
                                            </div>
                                            <div class="space-y-2 col-span-1">
                                                <h3 class="text-xl font-bold text-black uppercase tracking-wider adidas-font">CHƯA CÓ
                                                    ĐÁNH GIÁ</h3>
                                                <p class="text-gray-600 text-sm adidas-font">Hãy là người đầu tiên đánh giá sản phẩm
                                                    này.</p>
                                            </div>
                                            <div class="flex justify-center space-x-1">
                                                <div class="w-2 h-2 bg-black "></div>
                                                <div class="w-2 h-2 bg-gray-300 "></div>
                                                <div class="w-2 h-2 bg-gray-300 "></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Side accent -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-black via-gray-600 to-black">
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Related Products Section - Matched with Combo Style --}}
                    <div class="mt-20 space-y-8">
                        <!-- Section Header with Adidas Style -->
                        <div class="relative">
                            <div class="flex items-center space-x-4 mb-8">
                                <div class="w-1 h-12 bg-black"></div>
                                <div>
                                    <h2 class="adidas-font text-3xl font-bold text-black uppercase tracking-wider">
                                        SẢN PHẨM LIÊN QUAN
                                    </h2>
                                    <p class="text-sm text-gray-600 uppercase tracking-wide font-medium mt-1">Có thể bạn sẽ thích
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @if(!isset($relatedBooks) || $relatedBooks->count() == 0)
                                <div class="col-span-4 text-center"><span class="italic text-gray-400">Không có sản phẩm liên
                                        quan</span></div>
                            @endif
                            @foreach ($relatedBooks as $related)
                                @php
                                    $defaultFormat = $related->formats ? $related->formats->first() : null;
                                    $stock = $defaultFormat ? $defaultFormat->stock : 0;
                                    $price = $defaultFormat ? $defaultFormat->price : 0;
                                    $discount = $related->discounts ? $related->discounts->where('is_active', true)->first() : null;
                                    $finalPrice = $price;
                                    if ($discount && $price > 0) {
                                        $finalPrice = $price * (1 - ($discount->discount_percent ?? 0) / 100);
                                    }
                                @endphp

                                <div class="bg-white border border-gray-200 overflow-hidden group hover:border-black transition-all duration-300 p-2 cursor-pointer relative"
                                    onclick="window.location.href='{{ route('books.show', $related->slug ?? $related->id) }}'">
                                    <div class="relative aspect-square bg-white border border-gray-100 overflow-hidden mb-2">
                                        <a href="{{ route('books.show', $related->slug ?? $related->id) }}" class="block w-full h-full">
                                            @php
                                                $firstImage = $related->images->first();
                                                $imageUrl = $firstImage ? asset('storage/' . $firstImage->image_url) : ($related->cover_image ? asset('storage/' . $related->cover_image) : asset('images/default.jpg'));
                                            @endphp
                                            <img src="{{ $imageUrl }}" alt="{{ $related->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        </a>
                                        @if($stock <= 0)
                                            <div class="absolute top-2 left-2">
                                                <span class="bg-red-600 text-white text-xs font-bold uppercase tracking-wider px-2 py-0.5">
                                                    HẾT HÀNG
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Wishlist Button -->
                                        <div class="absolute top-2 right-2">
                                            <button
                                                class="w-10 h-10 bg-white bg-opacity-90 flex items-center justify-center border border-gray-200 hover:bg-black hover:text-white hover:border-black transition-all duration-300 transform hover:scale-110"
                                                onclick="event.stopPropagation();">
                                                <i class="far fa-heart text-sm"></i>
                                            </button>
                                        </div>

                                        <!-- Quick View Button -->
                                        <div
                                            class="absolute bottom-2 left-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <a href="{{ route('books.show', $related->slug ?? $related->id) }}"
                                                class="block w-full bg-black text-white text-center py-2 text-xs font-bold uppercase tracking-wider hover:bg-gray-800 transition-colors duration-300">
                                                XEM NHANH
                                            </a>
                                        </div>
                                    </div>

                                    <div class="p-2">
                                        <h3
                                            class="font-bold text-black text-base leading-tight group-hover:text-gray-600 transition-colors duration-300 line-clamp-2 min-h-[40px]">
                                            <a href="{{ route('books.show', $related->slug ?? $related->id) }}" class="hover:underline">
                                                {{ $related->title }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-gray-600 uppercase tracking-wide font-medium min-h-[18px] mt-1">
                                            {{ $related->authors && $related->authors->count() ? $related->authors->pluck('name')->join(', ') : 'KHÔNG RÕ TÁC GIẢ' }}
                                        </p>

                                        <div class="flex items-center justify-between mt-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-bold text-black">
                                                    {{ number_format($finalPrice, 0, ',', '.') }}₫
                                                </span>
                                                @if($discount)
                                                    <span class="text-sm text-gray-500 line-through">
                                                        {{ number_format($price, 0, ',', '.') }}₫
                                                    </span>
                                                    <span class="bg-red-600 text-white text-xs font-bold px-1.5 py-0.5">
                                                        -{{ $discount->discount_percent }}%
                                                    </span>
                                                @endif
                                            </div>

                                            @if($related->reviews->count() > 0)
                                                <div class="flex items-center">
                                                    <div class="text-yellow-400 text-xs">
                                                        @php $avgRating = $related->reviews->avg('rating') @endphp
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $avgRating)
                                                                ★
                                                            @else
                                                                <span class="text-gray-300">☆</span>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span class="text-xs text-gray-500 ml-1">
                                                        ({{ $related->reviews->count() }})
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3">
                                            @if($defaultFormat)
                                                <button onclick="event.stopPropagation(); addRelatedToCart('{{ $related->id }}')"
                                                    class="adidas-btn-enhanced w-full h-10 bg-black text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-center {{ $stock <= 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-800' }}"
                                                    {{ $stock <= 0 ? 'disabled' : '' }}>
                                                    <span class="relative flex items-center space-x-1">
                                                        <i class="fas fa-shopping-cart text-xs"></i>
                                                        <span>{{ $stock > 0 ? 'THÊM VÀO GIỎ' : 'HẾT HÀNG' }}</span>
                                                    </span>
                                                </button>
                                            @else
                                                <button
                                                    class="adidas-btn-enhanced w-full h-10 bg-gray-400 text-white font-bold text-xs uppercase tracking-wider cursor-not-allowed opacity-50"
                                                    disabled>
                                                    <span class="relative flex items-center space-x-1">
                                                        <i class="fas fa-exclamation-circle text-xs"></i>
                                                        <span>KHÔNG KHẢ DỤNG</span>
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- View All Button -->
                        <div class="flex justify-center pt-8">
                            <a href="{{ route('books.index') }}"
                                class="adidas-btn-enhanced px-8 py-4 bg-white text-black border-2 border-black font-bold uppercase tracking-wider hover:bg-black hover:text-white transition-all duration-300 flex items-center space-x-3">
                                <span>XEM TẤT CẢ SẢN PHẨM</span>
                            </a>
                        </div>
                    </div>
            </div>
            </div>
        @endif

    <!-- Modal Đọc Thử Ebook -->
    <div id="previewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
        <div
            class="bg-white shadow-lg max-w-5xl w-[90vw] max-h-[95vh] flex flex-col relative overflow-hidden border-2 border-black">
            <!-- Header -->
            <div class="flex items-center justify-between px-8 py-4 border-b-2 border-black bg-black text-white">
                <h3 class="text-xl font-bold text-white uppercase tracking-wider adidas-font">ĐỌC THỬ SÁCH</h3>
                <button id="closePreviewModal"
                    class="text-white hover:text-gray-300 text-3xl font-bold focus:outline-none adidas-font transition-colors duration-300">&times;</button>
            </div>
            <!-- Nội dung đọc thử -->
            <div id="previewContent" class="flex-1 overflow-y-auto px-0 py-0 relative bg-gray-50"
                style="scroll-behavior:smooth;">
                <div id="previewPages" class="h-full">
                    <!-- Nội dung đọc thử sẽ được load ở đây -->
                    <iframe id="previewIframe" src="{{ asset('storage/book/book_' . $book->id . '.pdf') }}"
                        class="w-full h-[80vh] border-none bg-white"></iframe>
                </div>
                <div id="previewLimitNotice"
                    class="hidden absolute bottom-4 left-4 right-4 text-center bg-black text-white font-bold py-3 px-6 adidas-font uppercase tracking-wider">
                    <i class="fas fa-lock mr-2"></i>
                    HÃY MUA ĐỂ TẬN HƯỞNG TRỌN BỘ!
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Ensure DOM is fully loaded
            document.addEventListener('DOMContentLoaded', function () {
                // Wait for jQuery and toastr to load
                const checkToastr = setInterval(function () {
                    if (typeof $ !== 'undefined' && typeof toastr !== 'undefined') {
                        clearInterval(checkToastr);

                        // Configure toastr options
                        toastr.options = {
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": false,
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "timeOut": "3000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };
                    }
                }, 100);

                // Timeout after 5 seconds
                setTimeout(function () {
                    clearInterval(checkToastr);
                }, 5000);
            });

            function changeMainImage(imageUrl, thumbnailElement) {
                const mainImage = document.getElementById('mainImage');

                // Add fade effect
                mainImage.style.opacity = '0.5';

                setTimeout(() => {
                    mainImage.src = imageUrl;
                    mainImage.style.opacity = '1';
                }, 200);

                // Update thumbnail selection
                if (thumbnailElement) {
                    // Remove active class from all thumbnails
                    document.querySelectorAll('.thumbnail-container').forEach(thumb => {
                        thumb.classList.remove('ring-2', 'ring-black');
                    });

                    // Add active class to selected thumbnail
                    thumbnailElement.classList.add('ring-2', 'ring-black');
                }
            }

            // Update price and stock based on selected format and attributes
            function updatePriceAndStock() {
                const formatSelect = document.getElementById('bookFormatSelect');
                const basePrice = parseFloat(document.getElementById('bookPrice').dataset.basePrice) || 0;
                let finalPrice = basePrice;
                let stock = 0;
                let discount = 0;
                let isEbook = false;

                // Get format data
                if (formatSelect && formatSelect.selectedOptions[0]) {
                    const selectedOption = formatSelect.selectedOptions[0];
                    finalPrice = parseFloat(selectedOption.dataset.price) || basePrice;
                    stock = parseInt(selectedOption.dataset.stock) || 0;
                    discount = parseFloat(selectedOption.dataset.discount) || 0;
                    const selectedText = selectedOption.textContent.trim().toLowerCase();
                    isEbook = selectedText.includes('ebook');
                }
                // Add attribute extra costs
                const attributeSelects = document.querySelectorAll('[name^="attributes["]');
                attributeSelects.forEach(select => {
                    if (select.selectedOptions[0]) {
                        const extraPrice = parseFloat(select.selectedOptions[0].dataset.price) || 0;
                        finalPrice += extraPrice;
                    }
                });
                // Calculate final price with discount
                const discountAmount = finalPrice * (discount / 100);
                const priceAfterDiscount = finalPrice - discountAmount;
                // Update price display
                document.getElementById('bookPrice').textContent = new Intl.NumberFormat('vi-VN').format(priceAfterDiscount) + '₫';
                const originalPriceElement = document.getElementById('originalPrice');
                const discountTextElement = document.getElementById('discountText');
                const discountPercentElement = document.getElementById('discountPercent');
                if (discount > 0) {
                    if (originalPriceElement) {
                        originalPriceElement.textContent = new Intl.NumberFormat('vi-VN').format(finalPrice) + '₫';
                        originalPriceElement.style.display = 'inline';
                    }
                    if (discountTextElement) {
                        discountTextElement.style.display = 'inline';
                    }
                    if (discountPercentElement) {
                        discountPercentElement.textContent = discount;
                    }
                } else {
                    if (originalPriceElement) {
                        originalPriceElement.style.display = 'none';
                    }
                    if (discountTextElement) {
                        discountTextElement.style.display = 'none';
                    }
                }
                // Update stock display
                const bookStockElement = document.getElementById('bookStock');
                const stockQuantityDisplay = document.getElementById('stockQuantityDisplay');

                if (isEbook) {
                    // For eBooks - always available
                    if (bookStockElement) {
                        bookStockElement.innerHTML = 'EBOOK - CÓ SẴN';
                        bookStockElement.className = 'status-in-stock font-semibold';
                    }
                    if (stockQuantityDisplay) {
                        stockQuantityDisplay.style.display = 'none';
                    }
                    // Update status indicator
                    const statusIndicator = bookStockElement?.parentElement?.querySelector('.w-3.h-3.rounded-full');
                    if (statusIndicator) {
                        statusIndicator.className = 'w-3 h-3 rounded-full bg-blue-500';
                    }
                    // Hide quantity section for ebooks
                    const quantitySection = document.querySelector('.quantity-section');
                    if (quantitySection) {
                        quantitySection.style.display = 'none';
                    }
                } else {
                    // For physical books - check stock
                    if (bookStockElement) {
                        let stockText = '';
                        let stockClass = '';
                        if (stock === -1) {
                            stockText = 'SẮP RA MẮT';
                            stockClass = 'status-coming-soon font-semibold';
                        } else if (stock === -2) {
                            stockText = 'NGƯNG KINH DOANH';
                            stockClass = 'status-discontinued font-semibold';
                        } else if (stock === 0) {
                            stockText = 'HẾT HÀNG';
                            stockClass = 'status-out-of-stock font-semibold';
                        } else {
                            stockText = 'CÒN HÀNG';
                            stockClass = 'status-in-stock font-semibold';
                        }
                        bookStockElement.textContent = stockText;
                        bookStockElement.className = stockClass;
                    }
                    if (stockQuantityDisplay) {
                        if (stock > 0) {
                            // Ensure the productQuantity span exists and update it
                            let productQuantitySpan = document.getElementById('productQuantity');
                            if (!productQuantitySpan) {
                                stockQuantityDisplay.innerHTML = `(<span class="font-bold text-black" id="productQuantity">${stock}</span> cuốn còn lại)`;
                            } else {
                                productQuantitySpan.textContent = stock;
                            }
                            stockQuantityDisplay.style.display = 'inline';
                        } else {
                            stockQuantityDisplay.style.display = 'none';
                        }
                    }
                    // Update productQuantityElement reference after potential recreation
                    const refreshedProductQuantityElement = document.getElementById('productQuantity');
                    if (refreshedProductQuantityElement) {
                        refreshedProductQuantityElement.textContent = stock > 0 ? stock : 0;
                    }
                    // Update status indicator
                    const statusIndicator = bookStockElement?.parentElement?.querySelector('.w-3.h-3.rounded-full');
                    if (statusIndicator) {
                        statusIndicator.className = `w-3 h-3 rounded-full ${stock > 0 ? 'bg-green-500' : 'bg-red-500'}`;
                    }
                    // Show quantity section for physical books
                    const quantitySection = document.querySelector('.quantity-section');
                    if (quantitySection) {
                        quantitySection.style.display = 'block';
                    }
                }
                // Update quantity input max value
                const quantityInput = document.getElementById('quantity');
                if (quantityInput) {
                    if (isEbook) {
                        quantityInput.value = 1;
                        quantityInput.max = '';
                        quantityInput.min = 1;
                    } else if (stock > 0) {
                        quantityInput.max = stock;
                        if (parseInt(quantityInput.value) > stock) {
                            quantityInput.value = Math.min(parseInt(quantityInput.value), stock);
                        }
                    }
                }
            }

            // Event listeners
            $(document).ready(function () {
                const formatSelect = document.getElementById('bookFormatSelect');
                if (formatSelect) {
                    formatSelect.addEventListener('change', updatePriceAndStock);
                }

                const attributeSelects = document.querySelectorAll('[name^="attributes["]');
                attributeSelects.forEach(select => {
                    select.addEventListener('change', updatePriceAndStock);
                });


                // Handle add to cart button
                const addToCartBtn = document.getElementById('addToCartBtn');
                if (addToCartBtn) {
                    addToCartBtn.addEventListener('click', function () {
                        addToCart();
                    });
                }

                // Toggle for book description
                const showMoreBtn = document.getElementById('showMoreBtn');
                const bookDescriptionDiv = document.getElementById('bookDescription');
                let isBookExpanded = false;
                if (showMoreBtn && bookDescriptionDiv) {
                    showMoreBtn.addEventListener('click', function () {
                        if (isBookExpanded) {
                            bookDescriptionDiv.innerHTML = bookDescriptionDiv.dataset.short;
                            showMoreBtn.textContent = 'Xem thêm';
                            isBookExpanded = false;
                        } else {
                            bookDescriptionDiv.innerHTML = bookDescriptionDiv.dataset.full;
                            showMoreBtn.textContent = 'Thu gọn';
                            isBookExpanded = true;
                        }
                    });
                }
                // Toggle for combo description
                const showMoreComboBtn = document.getElementById('showMoreComboBtn');
                const comboDescriptionDiv = document.getElementById('comboDescription');
                let isComboExpanded = false;
                if (showMoreComboBtn && comboDescriptionDiv) {
                    showMoreComboBtn.addEventListener('click', function () {
                        if (isComboExpanded) {
                            comboDescriptionDiv.innerHTML = comboDescriptionDiv.dataset.short;
                            showMoreComboBtn.textContent = 'Xem thêm';
                            isComboExpanded = false;
                        } else {
                            comboDescriptionDiv.innerHTML = comboDescriptionDiv.dataset.full;
                            showMoreComboBtn.textContent = 'Thu gọn';
                            isComboExpanded = true;
                        }
                    });
                }

                // Initialize price and stock on page load
                updatePriceAndStock();
            });

            // Add to cart function
            function addToCart() {
                // Check if user is logged in
                @auth
                @else
                        if (typeof toastr !== 'undefined') {
                        toastr.error('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
                    } else {
                        alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
                    }
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 1500);
                    return;
                @endauth

                // Get form data
                const bookId = '{{ $book->id }}';
                const quantity = parseInt(document.getElementById('quantity').value) || 1;

                // Get selected format
                const formatSelect = document.getElementById('bookFormatSelect');
                const bookFormatId = formatSelect ? formatSelect.value : null;
                let isEbook = false;

                if (formatSelect && formatSelect.selectedOptions[0]) {
                    const selectedText = formatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                    isEbook = selectedText.includes('ebook');
                }

                // Get selected attributes
                const attributes = {};
                const attributeValueIds = [];
                const attributeSelects = document.querySelectorAll('[name^="attributes["]');

                attributeSelects.forEach(select => {
                    if (select.value) {
                        attributes[select.name] = select.value;
                        attributeValueIds.push(select.value);
                    }
                });

                // Validate stock (only for physical books)
                if (!isEbook) {
                    // Get stock from format select instead of DOM element for reliability
                    const formatSelect = document.getElementById('bookFormatSelect');
                    let stock = 0;

                    if (formatSelect && formatSelect.selectedOptions[0]) {
                        stock = parseInt(formatSelect.selectedOptions[0].dataset.stock) || 0;
                    }

                    if (stock <= 0 || stock === -1 || stock === -2) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Sản phẩm này hiện không có sẵn để đặt hàng!');
                        } else {
                            alert('Sản phẩm này hiện không có sẵn để đặt hàng!');
                        }
                        addToCartBtn.disabled = false;
                        addToCartBtn.textContent = originalText;
                        return;
                    }

                    if (quantity > stock) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Số lượng vượt quá số lượng tồn kho!');
                        } else {
                            alert('Số lượng vượt quá số lượng tồn kho!');
                        }
                        addToCartBtn.disabled = false;
                        addToCartBtn.textContent = originalText;
                        return;
                    }
                }

                // Disable button and show loading
                const addToCartBtn = document.getElementById('addToCartBtn');
                const originalText = addToCartBtn.textContent;
                addToCartBtn.disabled = true;
                addToCartBtn.textContent = 'Đang thêm...';

                // Send request
                fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        book_id: bookId,
                        quantity: quantity,
                        book_format_id: bookFormatId,
                        attributes: attributes
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Đã thêm sản phẩm vào giỏ hàng!');
                            } else {
                                alert('Đã thêm sản phẩm vào giỏ hàng!');
                            }

                            // Dispatch cart count update event
                            if (typeof data.cart_count !== 'undefined') {
                                document.dispatchEvent(new CustomEvent('cartItemAdded', {
                                    detail: { count: data.cart_count }
                                }));
                            } else {
                                // Fallback: refresh cart count from server
                                if (window.CartCountManager && typeof window.CartCountManager.refreshFromServer === 'function') {
                                    window.CartCountManager.refreshFromServer();
                                }
                            }
                        } else if (data.error) {
                            if (typeof toastr !== 'undefined') {
                                // Kiểm tra nếu là lỗi trộn lẫn loại sản phẩm
                                if (data.cart_type) {
                                    if (data.cart_type === 'physical_books') {
                                        toastr.warning(data.error, 'Giỏ hàng có sách vật lý!', {
                                            timeOut: 6000,
                                            closeButton: true,
                                            progressBar: true,
                                            positionClass: 'toast-top-right'
                                        });
                                    } else if (data.cart_type === 'ebooks') {
                                        toastr.warning(data.error, 'Giỏ hàng có sách điện tử!', {
                                            timeOut: 6000,
                                            closeButton: true,
                                            progressBar: true,
                                            positionClass: 'toast-top-right'
                                        });
                                    }
                                } else {
                                    toastr.error(data.error);
                                }
                            } else {
                                // Fallback alert if toastr is not available
                                alert(data.error);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Đã xảy ra lỗi khi thêm vào giỏ hàng!');
                        } else {
                            alert('Đã xảy ra lỗi khi thêm vào giỏ hàng!');
                        }
                    })
                    .finally(() => {
                        // Restore button
                        addToCartBtn.disabled = false;
                        addToCartBtn.textContent = originalText;
                    });
            }

            // Add related product to cart function
            function addRelatedToCart(bookId) {
                // Check if user is logged in
                @auth
                @else
                        if (typeof toastr !== 'undefined') {
                        toastr.warning('Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng', 'Chưa đăng nhập!', {
                            timeOut: 3000,
                            positionClass: 'toast-top-right',
                            closeButton: true,
                            progressBar: true
                        });
                    } else {
                        alert('Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng');
                    }
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 1500);
                    return;
                @endauth

                // Default quantity for related products
                const quantity = 1;

                // Find the button that was clicked
                const button = event.target.closest('button');
                const originalText = button.innerHTML;

                // Disable button and show loading
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>ĐANG THÊM...';

                // Send request
                fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        book_id: bookId,
                        book_format_id: null, // Use default format
                        quantity: quantity,
                        attribute_value_ids: JSON.stringify([]),
                        attributes: {}
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success notification
                            if (typeof toastr !== 'undefined') {
                                toastr.success(data.success, 'Thành công!', {
                                    timeOut: 3000,
                                    positionClass: 'toast-top-right',
                                    closeButton: true,
                                    progressBar: true
                                });
                            } else {
                                alert(data.success);
                            }

                            // Dispatch cart count update event
                            if (typeof data.cart_count !== 'undefined') {
                                document.dispatchEvent(new CustomEvent('cartItemAdded', {
                                    detail: { count: data.cart_count }
                                }));
                            } else {
                                // Fallback: refresh cart count from server
                                if (window.CartCountManager && typeof window.CartCountManager.refreshFromServer === 'function') {
                                    window.CartCountManager.refreshFromServer();
                                }
                            }

                            // Show cart count update notification
                            setTimeout(() => {
                                if (typeof toastr !== 'undefined') {
                                    toastr.info('Xem giỏ hàng của bạn', 'Tip', {
                                        timeOut: 2000,
                                        onclick: function () {
                                            window.location.href = '{{ route("cart.index") }}';
                                        }
                                    });
                                }
                            }, 1000);

                        } else if (data.error) {
                            // Show error notification
                            if (typeof toastr !== 'undefined') {
                                // Kiểm tra nếu là lỗi trộn lẫn loại sản phẩm
                                if (data.cart_type) {
                                    if (data.cart_type === 'physical_books') {
                                        toastr.warning(data.error, 'Giỏ hàng có sách vật lý!', {
                                            timeOut: 6000,
                                            positionClass: 'toast-top-right',
                                            closeButton: true,
                                            progressBar: true
                                        });
                                    } else if (data.cart_type === 'ebooks') {
                                        toastr.warning(data.error, 'Giỏ hàng có sách điện tử!', {
                                            timeOut: 6000,
                                            positionClass: 'toast-top-right',
                                            closeButton: true,
                                            progressBar: true
                                        });
                                    }
                                } else {
                                    toastr.error(data.error, 'Lỗi!', {
                                        timeOut: 5000,
                                        positionClass: 'toast-top-right',
                                        closeButton: true,
                                        progressBar: true
                                    });
                                }
                            } else {
                                alert(data.error);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Có lỗi xảy ra khi thêm vào giỏ hàng', 'Lỗi mạng!', {
                                timeOut: 5000,
                                positionClass: 'toast-top-right',
                                closeButton: true,
                                progressBar: true
                            });
                        } else {
                            alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
                        }
                    })
                    .finally(() => {
                        // Restore button
                        button.disabled = false;
                        button.innerHTML = originalText;
                    });
            }

            // Xử lý hiển thị nút đọc thử cho ebook
            document.getElementById('bookFormatSelect').addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const formatName = selectedOption.text.toLowerCase();
                const previewSection = document.getElementById('previewSection');

                if (formatName.includes('ebook')) {
                    previewSection.classList.remove('hidden');
                } else {
                    previewSection.classList.add('hidden');
                }
            });

            // Xử lý modal đọc thử lấy đúng file sample_file_url
            const previewBtn = document.querySelector('#previewSection a');
            const previewModal = document.getElementById('previewModal');
            const closePreviewModal = document.getElementById('closePreviewModal');
            const previewContent = document.getElementById('previewContent');
            const previewLimitNotice = document.getElementById('previewLimitNotice');
            const previewIframe = document.getElementById('previewIframe');
            const formatSelect = document.getElementById('bookFormatSelect');

            if (previewBtn && previewModal && closePreviewModal && formatSelect && previewIframe) {
                previewBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const selectedOption = formatSelect.options[formatSelect.selectedIndex];
                    const sampleUrl = selectedOption.getAttribute('data-sample-url');
                    const allowSample = selectedOption.getAttribute('data-allow-sample') === '1';
                    if (allowSample && sampleUrl) {
                        previewIframe.src = sampleUrl;
                        previewModal.classList.remove('hidden');
                        previewLimitNotice.classList.add('hidden');
                        previewContent.scrollTop = 0;
                    } else {
                        alert('Không có file đọc thử cho định dạng này!');
                    }
                });
                closePreviewModal.addEventListener('click', function () {
                    previewModal.classList.add('hidden');
                    previewIframe.src = '';
                });
                previewModal.addEventListener('click', function (e) {
                    if (e.target === previewModal) {
                        previewModal.classList.add('hidden');
                        previewIframe.src = '';
                    }
                });
                previewContent.addEventListener('scroll', function () {
                    const scrollBottom = previewContent.scrollTop + previewContent.clientHeight;
                    const scrollHeight = previewContent.scrollHeight;
                    if (scrollBottom >= scrollHeight - 10) {
                        previewLimitNotice.classList.remove('hidden');
                    } else {
                        previewLimitNotice.classList.add('hidden');
                    }
                });
            }

            // Handle combo quantity controls
            @if(isset($combo))
            const comboQuantityInput = document.getElementById('comboQuantity');
            const comboIncrementBtn = document.getElementById('comboIncrementBtn');
            const comboDecrementBtn = document.getElementById('comboDecrementBtn');

            if (comboQuantityInput && comboIncrementBtn && comboDecrementBtn) {
                // Function to update button states
                function updateComboButtonStates() {
                    const value = parseInt(comboQuantityInput.value) || 1;
                    const min = parseInt(comboQuantityInput.min) || 1;
                    
                    comboDecrementBtn.disabled = value <= min;
                    comboDecrementBtn.style.opacity = value <= min ? '0.5' : '1';
                    
                    // No max limit for combo items - they check date validation instead
                    comboIncrementBtn.disabled = false;
                    comboIncrementBtn.style.opacity = '1';
                }

                // Increment button handler
                comboIncrementBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentValue = parseInt(comboQuantityInput.value) || 1;
                    comboQuantityInput.value = currentValue + 1;
                    updateComboButtonStates();
                });

                // Decrement button handler
                comboDecrementBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentValue = parseInt(comboQuantityInput.value) || 1;
                    const min = parseInt(comboQuantityInput.min) || 1;
                    
                    if (currentValue > min) {
                        comboQuantityInput.value = currentValue - 1;
                        updateComboButtonStates();
                    }
                });

                // Input validation
                comboQuantityInput.addEventListener('input', function() {
                    let value = parseInt(this.value) || 1;
                    const min = parseInt(this.min) || 1;
                    
                    if (value < min) {
                        value = min;
                        this.value = value;
                    }
                    
                    updateComboButtonStates();
                });

                comboQuantityInput.addEventListener('blur', function() {
                    if (!this.value || parseInt(this.value) < 1) {
                        this.value = 1;
                        updateComboButtonStates();
                    }
                });

                // Initialize button states
                updateComboButtonStates();
            }

            // Handle combo form submission
            const comboForm = document.querySelector('form[action="{{ route("cart.add") }}"]');
            if (comboForm) {
                comboForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Check if user is logged in
                    @auth
                    @else
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Vui lòng đăng nhập để thêm combo vào giỏ hàng!');
                        } else {
                            alert('Vui lòng đăng nhập để thêm combo vào giỏ hàng!');
                        }
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 1500);
                        return;
                    @endauth

                    const formData = new FormData(comboForm);
                    const submitBtn = comboForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    // Disable button and show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i><span>Đang thêm...</span>';

                    fetch('{{ route("cart.add") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Combo form response:', data); // Debug logging
                        
                        if (data.success) {
                            if (typeof toastr !== 'undefined') {
                                console.log('Showing toastr success message'); // Debug
                                toastr.success(data.success, 'Thành công!', {
                                    timeOut: 3000,
                                    positionClass: 'toast-top-right',
                                    closeButton: true,
                                    progressBar: true
                                });
                            } else {
                                console.log('Toastr not available, using alert'); // Debug
                                alert(data.success);
                            }

                            // Dispatch cart count update event
                            if (typeof data.cart_count !== 'undefined') {
                                document.dispatchEvent(new CustomEvent('cartItemAdded', {
                                    detail: { count: data.cart_count }
                                }));
                            }

                            // Show cart count update notification
                            setTimeout(() => {
                                if (typeof toastr !== 'undefined') {
                                    toastr.info('Xem giỏ hàng của bạn', 'Tip', {
                                        timeOut: 2000,
                                        onclick: function () {
                                            window.location.href = '{{ route("cart.index") }}';
                                        }
                                    });
                                }
                            }, 1000);

                        } else if (data.error) {
                            console.log('Showing toastr error message:', data.error); // Debug
                            if (typeof toastr !== 'undefined') {
                                toastr.error(data.error, 'Lỗi!', {
                                    timeOut: 5000,
                                    positionClass: 'toast-top-right',
                                    closeButton: true,
                                    progressBar: true
                                });
                            } else {
                                console.log('Toastr not available, using alert'); // Debug
                                alert(data.error);
                            }
                        } else {
                            console.log('Unknown response format:', data); // Debug
                        }
                    })
                    .catch(error => {
                        console.error('Combo form submission error:', error); // Debug
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Có lỗi xảy ra khi thêm combo vào giỏ hàng', 'Lỗi mạng!', {
                                timeOut: 5000,
                                positionClass: 'toast-top-right',
                                closeButton: true,
                                progressBar: true
                            });
                        } else {
                            alert('Có lỗi xảy ra khi thêm combo vào giỏ hàng');
                        }
                    })
                    .finally(() => {
                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
            @endif


        </script>
    @endpush
@endsection