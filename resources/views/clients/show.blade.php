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
            color: #d97706;
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
            color: #d97706;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Simplified Variant Overview */
        .product-detail-page .variant-overview {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .product-detail-page .variant-item {
            cursor: pointer;
        }

        .product-detail-page .variant-item.out-of-stock {
            background: #f8f8f8;
            border: 1px dashed #ddd;
            cursor: not-allowed;
        }

        /* Simplified Select Options Styling */
        .product-detail-page .adidas-select option {
            padding: 8px 12px;
            font-size: 14px;
        }

        .product-detail-page .adidas-select option:disabled {
            color: #ef4444;
            background-color: #fef2f2;
            font-style: italic;
        }

        /* Enhanced Ebook Status Styling */
        .product-detail-page .ebook-badge {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
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
            background: #d97706;
            color: #fff;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: 2px solid #d97706;
        }

        .product-detail-page .adidas-btn:hover {
            background: #b45309;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(217, 119, 6, 0.3);
            border-color: #b45309;
        }

        .product-detail-page .adidas-btn-enhanced {
            border: none;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            background: #d97706;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
            letter-spacing: 2px;
            font-weight: 600;
            text-transform: uppercase;
            border: 2px solid #d97706;
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
            background: #b45309 !important;
            color: #fff !important;
            border-color: #b45309;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(217, 119, 6, 0.4);
        }

        .product-detail-page .adidas-btn-enhanced:hover::before {
            left: 100%;
        }

        .product-detail-page .adidas-btn-enhanced:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(217, 119, 6, 0.2);
        }

        .product-detail-page .adidas-btn-enhanced .relative {
            transition: all 0.3s ease;
        }

        .product-detail-page .wishlist-btn {
            border-radius: 0;
            position: relative;
            overflow: hidden;
            background: #fff;
            color: #d97706;
            border: 2px solid #d97706;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .product-detail-page .wishlist-btn:hover {
            background: #d97706 !important;
            color: #fff !important;
            border-color: #d97706;
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
            border-color: #d97706;
            outline: none;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
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
            border-color: #d97706;
            outline: none;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
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
            background: #d97706;
            color: #fff;
            border-color: #d97706;
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
            border-color: #d97706;
            border-left: 2px solid #d97706;
            border-right: 2px solid #d97706;
        }

        /* Enhanced Share Buttons */
        .product-detail-page .share-btn {
            background: #f5f5f5;
            transition: all 0.3s ease;
        }

        .product-detail-page .share-btn:hover {
            background: #d97706;
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
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);
            background: #d97706;
            border-color: #d97706;
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
            color: #d97706;
        }

        /* Enhanced Sections */
        .product-detail-page .section-title {
            border-left: 4px solid #d97706;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .product-detail-page .review-card {
            border-left: 3px solid #d97706;
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
            box-shadow: 0 15px 35px rgba(217, 119, 6, 0.1);
            border-color: #d97706;
        }

        /* Price Section Enhancement */
        .product-detail-page .price-section {
            background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
            padding: 2rem;
            border: 1px solid #fbbf24;
        }

        /* Stock Status Enhancement */
        .product-detail-page .stock-status {
            padding: 1rem;
            background: #fffbeb;
            border-left: 4px solid #d97706;
        }

        /* Attribute Group Enhancement */
        .product-detail-page .attribute-group {
            background: #fffbeb;
            padding: 1.5rem;
            border: 1px solid #fbbf24;
        }

        /* Purchase Section Enhancement */
        .product-detail-page .purchase-section {
            background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
            padding: 2rem;
            border: 2px solid #fbbf24;
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

        /* Enhanced Variant Information Styling */
        .product-detail-page .variant-info-card {
            border-radius: 0;
            overflow: hidden;
            background: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-detail-page .variant-info-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .product-detail-page .variant-info-header {
            background: #d97706 !important;
            border-bottom: 2px solid #b45309;
        }

        .product-detail-page .variant-info-item {
            border-radius: 0;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
            transition: all 0.2s ease;
        }

        .product-detail-page .variant-info-item:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .product-detail-page .icon-container {
            border-radius: 2px;
            transition: all 0.2s ease;
        }

        .product-detail-page .variant-info-item:hover .icon-container {
            transform: scale(1.05);
        }

        .product-detail-page .variant-info-value {
            border-radius: 0;
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            transition: all 0.2s ease;
        }

        .product-detail-page .variant-info-badge {
            border-radius: 0;
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            transition: all 0.2s ease;
        }

        /* Responsive adjustments for variant info */
        @media (max-width: 768px) {
            .product-detail-page .variant-info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .product-detail-page .variant-info-item>div:first-child {
                width: 100%;
            }

            .product-detail-page .variant-info-value,
            .product-detail-page .variant-info-badge {
                align-self: flex-end;
            }

            .product-detail-page .icon-container {
                width: 1.75rem;
                height: 1.75rem;
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

        /* Enhanced Admin Response Styling */
        .product-detail-page .admin-response {
            border-radius: 0;
            overflow: hidden;
            background: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 1rem;
        }

        .product-detail-page .admin-response:hover {
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        .product-detail-page .admin-response-text {
            font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
            color: #1f2937;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Responsive adjustments for admin response */
        @media (max-width: 768px) {
            .product-detail-page .admin-response {
                margin-top: 0.75rem;
            }
            
            .product-detail-page .admin-response-text {
                font-size: 0.9rem;
                line-height: 1.5;
            }
        }

        /* Enhanced Variant Attributes Styling */
        .product-detail-page .variant-option-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border-radius: 0;
        }

        .product-detail-page .variant-option-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);
            border-color: #d97706;
        }

        .product-detail-page .variant-option-card:has(input:checked) {
            border-color: #d97706 !important;
            background-color: #fff7ed !important;
            box-shadow: 0 0 0 1px #d97706;
        }

        .product-detail-page .variant-option-card input[type="radio"] {
            accent-color: #d97706;
            scale: 1.2;
        }

        .product-detail-page .variant-option-card input[type="radio"]:focus {
            outline: 2px solid #d97706;
            outline-offset: 2px;
        }

        .product-detail-page .attribute-group-item {
            border-left: 4px solid #d97706;
            padding-left: 1rem;
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            padding: 1rem;
            margin: 0.5rem 0;
        }

        .product-detail-page #attributesSummary {
            animation: slideUp 0.3s ease-out;
            border-radius: 0;
        }

        .product-detail-page #attributesSummary .grid > div {
            transition: all 0.2s ease;
            border-radius: 0;
        }

        .product-detail-page #attributesSummary .grid > div:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
        }

        /* Mobile responsive for variant options */
        @media (max-width: 768px) {
            .product-detail-page .variant-option-card {
                padding: 0.75rem;
            }

            .product-detail-page .variant-option-card .flex {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .product-detail-page .variant-option-card .text-right {
                text-align: left;
                align-self: stretch;
            }

            .product-detail-page .attribute-group-item {
                padding: 0.75rem;
                margin: 0.25rem 0;
            }

            .product-detail-page #attributesSummary .grid {
                grid-template-columns: 1fr;
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
                        @php
                            // CENTRALIZED COMBO STATUS LOGIC - ONLY DEFINE ONCE
                            $now = now();
                            $startDate = $combo->start_date ? \Carbon\Carbon::parse($combo->start_date) : null;
                            $endDate = $combo->end_date ? \Carbon\Carbon::parse($combo->end_date) : null;

                            // Determine combo status with proper priority
                            $comboStatus = [
                                'isActive' => false,
                                'canPurchase' => false,
                                'statusText' => '',
                                'statusDot' => '',
                                'badgeClass' => '',
                                'buttonText' => '',
                                'showQuantity' => false,
                                'showStock' => false
                            ];

                            // Priority 1: Check if combo is disabled
                            if ($combo->status !== 'active') {
                                $comboStatus['statusText'] = 'Ngừng bán';
                                $comboStatus['statusDot'] = 'bg-gray-500';
                                $comboStatus['badgeClass'] = 'bg-gray-50 text-gray-700 border-gray-200';
                                $comboStatus['buttonText'] = 'NGỪNG BÁN';
                            }
                            // Priority 2: Check stock
                            elseif ($combo->combo_stock <= 0) {
                                $comboStatus['statusText'] = 'Hết hàng';
                                $comboStatus['statusDot'] = 'bg-red-500';
                                $comboStatus['badgeClass'] = 'bg-red-50 text-red-700 border-red-200';
                                $comboStatus['buttonText'] = 'HẾT HÀNG';
                            }
                            // Priority 3: Check time constraints
                            elseif ($startDate && $now < $startDate) {
                                $comboStatus['statusText'] = 'Chưa bắt đầu';
                                $comboStatus['statusDot'] = 'bg-yellow-500';
                                $comboStatus['badgeClass'] = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                $comboStatus['buttonText'] = 'CHƯA BẮT ĐẦU';
                            } elseif ($endDate && $now > $endDate) {
                                $comboStatus['statusText'] = 'Đã kết thúc';
                                $comboStatus['statusDot'] = 'bg-red-500';
                                $comboStatus['badgeClass'] = 'bg-red-50 text-red-700 border-red-200';
                                $comboStatus['buttonText'] = 'ĐÃ KẾT THÚC';
                            }
                            // All conditions met - combo is active
                            else {
                                $comboStatus['isActive'] = true;
                                $comboStatus['canPurchase'] = true;
                                $comboStatus['statusText'] = 'Đang mở bán';
                                $comboStatus['statusDot'] = 'bg-green-500';
                                $comboStatus['badgeClass'] = 'bg-green-50 text-green-700 border-green-200';
                                $comboStatus['buttonText'] = 'THÊM VÀO GIỎ HÀNG';
                                $comboStatus['showQuantity'] = true;
                                $comboStatus['showStock'] = true;
                            }
                        @endphp

                        <div class="space-y-4 pb-6 border-b border-gray-200">
                            <h1 class="product-title combo-title">{{ $combo->name }}</h1>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">SỐ SÁCH</span>
                                    <span class="text-black font-semibold adidas-font">{{ $combo->books->count() }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">NGÀY BẮT
                                        ĐẦU</span>
                                    <span class="text-black font-semibold adidas-font truncate">
                                        @if($combo->start_date)
                                            {{ $startDate->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">Không giới hạn</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">NGÀY KẾT
                                        THÚC</span>
                                    <span class="text-black font-semibold adidas-font truncate">
                                        @if($combo->end_date)
                                            {{ $endDate->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">Không giới hạn</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">TRẠNG
                                        THÁI</span>
                                    <span
                                        class="inline-flex items-center px-3 py-1 text-xs sm:text-sm font-semibold border adidas-font uppercase tracking-wider {{ $comboStatus['badgeClass'] }} whitespace-nowrap">
                                        <span class="truncate">{{ $comboStatus['statusText'] }}</span>
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                    <span class="text-gray-600 font-medium adidas-font uppercase tracking-wider">GIÁ
                                        COMBO</span>
                                    <span
                                        class="text-black font-bold text-lg adidas-font truncate">{{ number_format($combo->combo_price, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                        </div>
                        <div class="price-section space-y-4">
                            <!-- Price and main status -->
                            <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-4">
                                <span
                                    class="text-4xl font-bold text-black adidas-font">{{ number_format($combo->combo_price, 0, ',', '.') }}₫</span>
                                <span
                                    class="inline-flex items-center px-3 py-1 text-xs sm:text-sm font-semibold border adidas-font uppercase tracking-wider {{ $comboStatus['badgeClass'] }} whitespace-nowrap">
                                    <span
                                        class="w-2 h-2 rounded-full mr-2 {{ $comboStatus['statusDot'] }} inline-block flex-shrink-0"></span>
                                    <span class="truncate">{{ $comboStatus['statusText'] }}</span>
                                </span>
                                @if($comboStatus['showStock'])
                                    <span class="text-sm text-gray-600 adidas-font">(<span
                                            class="font-bold text-black">{{ $combo->combo_stock }}</span> combo còn lại)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Enhanced Time Information Section -->
                        @if($combo->start_date || $combo->end_date)
                            <div
                                class="time-info-section bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 p-4 rounded-r-lg mt-6">
                                <h3
                                    class="text-sm font-bold text-amber-800 uppercase tracking-wider mb-3 adidas-font flex items-center">
                                    <i class="fas fa-clock mr-2"></i>Thông tin thời gian
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($combo->start_date)
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-green-100 flex items-center justify-center rounded-full">
                                                <i class="fas fa-play text-green-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Bắt đầu</div>
                                                <div class="text-sm font-bold text-gray-800">
                                                    {{ \Carbon\Carbon::parse($combo->start_date)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($combo->start_date)->format('H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($combo->end_date)
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-red-100 flex items-center justify-center rounded-full">
                                                <i class="fas fa-stop text-red-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Kết thúc</div>
                                                <div class="text-sm font-bold text-gray-800">
                                                    {{ \Carbon\Carbon::parse($combo->end_date)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($combo->end_date)->format('H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Countdown Timer for Active Combo -->
                                @if($comboStatus['isActive'] && $combo->end_date)
                                    @php
                                        $diff = $now->diff($endDate);
                                        $totalDays = $now->diffInDays($endDate);
                                        $isUrgent = $totalDays <= 3;
                                    @endphp
                                    <div
                                        class="mt-4 p-3 {{ $isUrgent ? 'bg-red-100 border border-red-200' : 'bg-blue-100 border border-blue-200' }} rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-hourglass-half {{ $isUrgent ? 'text-red-600' : 'text-blue-600' }}"></i>
                                                <span
                                                    class="text-sm font-semibold {{ $isUrgent ? 'text-red-800' : 'text-blue-800' }} uppercase tracking-wider">
                                                    {{ $isUrgent ? 'Sắp kết thúc!' : 'Thời gian còn lại' }}
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold {{ $isUrgent ? 'text-red-600' : 'text-blue-600' }}">
                                                    @if($diff->days > 0)
                                                        {{ $diff->days }} ngày
                                                    @endif
                                                    @if($diff->h > 0)
                                                        {{ $diff->h }} giờ
                                                    @endif
                                                    @if($diff->i > 0 && $diff->days == 0)
                                                        {{ $diff->i }} phút
                                                    @endif
                                                </div>
                                                <div class="text-xs {{ $isUrgent ? 'text-red-500' : 'text-blue-500' }}">
                                                    {{ $endDate->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(!$comboStatus['isActive'] && $combo->start_date && $now < $startDate)
                                    @php
                                        $diff = $now->diff($startDate);
                                    @endphp
                                    <div class="mt-4 p-3 bg-yellow-100 border border-yellow-200 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-clock text-yellow-600"></i>
                                                <span class="text-sm font-semibold text-yellow-800 uppercase tracking-wider">
                                                    Chưa bắt đầu
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-yellow-600">
                                                    @if($diff->days > 0)
                                                        {{ $diff->days }} ngày
                                                    @endif
                                                    @if($diff->h > 0)
                                                        {{ $diff->h }} giờ
                                                    @endif
                                                    @if($diff->i > 0 && $diff->days == 0)
                                                        {{ $diff->i }} phút
                                                    @endif
                                                </div>
                                                <div class="text-xs text-yellow-500">
                                                    {{ $startDate->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Danh sách sách trong combo -->
                        <div class="combo-books-list bg-white border border-gray-100 p-6 mt-6 rounded-lg">
                            <h2
                                class="text-lg font-bold text-black mb-4 flex items-center adidas-font uppercase tracking-wider border-b pb-3">
                                <i class="fas fa-book text-base mr-2 text-black"></i>Danh sách sách trong combo
                            </h2>
                            <div class="grid grid-cols-1 gap-4 mt-4">
                                @foreach($combo->books as $book)
                                                <div class="flex items-start border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                                    <div
                                                        class="flex-shrink-0 w-12 h-16 bg-gray-100 mr-4 flex items-center justify-center overflow-hidden">

                                                        <img src="{{ $book->cover_image
                                    ? asset('storage/' . $book->cover_image)
                                    : ($book->images->first()
                                        ? asset('storage/' . $book->images->first()->image_url)
                                        : asset('images/default.jpg')) }}" alt="{{ $book->title }}" id="mainImage"
                                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <a href="{{ route('books.show', $book->slug) }}"
                                                            class="text-base font-semibold text-gray-900 hover:text-blue-600 transition-colors duration-200 line-clamp-2"
                                                            title="{{ $book->title }}">
                                                            {{ $book->title }}
                                                        </a>
                                                        @if($book->authors->count())
                                                            <p class="text-sm text-gray-500 mt-1">
                                                                <span class="font-medium">Tác giả:</span>
                                                                {{ $book->authors->pluck('name')->join(', ') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Form mua combo -->
                        <form action="{{ route('cart.add') }}" method="POST" class="mt-8">
                            @csrf
                            <input type="hidden" name="combo_id" value="{{ $combo->id }}">
                            <input type="hidden" name="type" value="combo">
                            @if($comboStatus['showQuantity'])
                                <div class="mb-6">
                                    <label class="block text-sm font-bold text-black uppercase tracking-wider mb-3 adidas-font">Số
                                        lượng</label>
                                    <div class="flex items-center w-fit">
                                        <button type="button" class="quantity-btn-enhanced" id="comboDecrementBtn">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" name="quantity" id="comboQuantity" value="1" min="1"
                                            max="{{ $combo->combo_stock }}" class="quantity-input-enhanced adidas-font" />
                                        <button type="button" class="quantity-btn-enhanced" id="comboIncrementBtn">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="quantity" value="1">
                            @endif
                            <button type="submit"
                                class="adidas-btn-enhanced w-full h-16 bg-black text-white font-bold text-lg uppercase tracking-wider transition-all duration-300 flex items-center justify-center adidas-font"
                                @if(!$comboStatus['canPurchase']) disabled style="opacity:0.6;pointer-events:none;" @endif>
                                <i class="fas fa-shopping-bag mr-3"></i>
                                <span>{{ $comboStatus['buttonText'] }}</span>
                            </button>
                            <!-- Enhanced Share Section -->
                            <div class="share-section pt-8 border-t border-gray-200 mt-8">
                                <h3 class="text-sm font-bold text-amber-600 uppercase tracking-wider mb-6">Chia sẻ sản phẩm</h3>
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
                        {{--
                        <script>
                            function updateComboQty(change) {
                                const input = document.getElementById('comboQuantity');
                                let val = parseInt(input.value) || 1;
                                val += change;
                                if (val < 1) val = 1;
                                input.value = val;
                            }
                        </script> --}}
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
                        // Giải mã HTML entity nếu trong DB lưu dạng &lt;p&gt;
                        $decodedDesc = html_entity_decode($combo->description ?? '');
                        // Loại bỏ thẻ HTML
                        $comboDesc = strip_tags($decodedDesc);
                        $showComboMore = \Illuminate\Support\Str::length($comboDesc) > 200;
                    @endphp
                    <div id="comboDescription" class="text-gray-700 text-base leading-relaxed text-left"
                        data-full="{{ $comboDesc }}" data-short="{{ \Illuminate\Support\Str::limit($comboDesc, 200, '...') }}">
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

            {{-- Enhanced Reviews Section for Combo - Adidas Style --}}
            @if(isset($combo))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20 space-y-8">
                    <!-- Section Header with Adidas Style -->
                    <div class="relative">
                        <div class="flex items-center space-x-4 mb-8">
                            <div class="w-1 h-12 bg-amber-600"></div>
                            <div>
                                <h2 class="adidas-font text-3xl font-bold text-amber-600 uppercase tracking-wider">
                                    ĐÁNH GIÁ KHÁCH HÀNG - COMBO
                                </h2>
                                <div class="flex items-center space-x-2 mt-1">
                                    <div class="flex text-yellow-400 text-lg">
                                        @php
                                            $averageRating = $combo->reviews->avg('rating') ?? 0;
                                            $totalReviews = $combo->reviews->count();
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
                        @forelse($combo->reviews as $review)
                            <div
                                class="review-card bg-white border-2 border-gray-100 relative overflow-hidden group hover:border-amber-600 transition-all duration-300">
                                <!-- Header Bar -->
                                <div class="bg-amber-600 text-white px-6 py-3 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-opacity-20 flex items-center justify-center">
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
                                            {{ $review->created_at->diffForHumans() }}
                                        </div>
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
                                        {{-- <div
                                            class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-black via-gray-400 to-black">
                                        </div> --}}
                                        <div class="pl-6">
                                            <p class="text-gray-800 leading-relaxed font-medium">
                                                <i class="fas fa-share text-gray-500"></i>
                                                {{ $review->comment }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Review Images -->
                                        @if($review->images && count($review->images) > 0)
                                            <div class="mt-5">
                                                <div class="text-sm text-gray-700 mb-3 uppercase tracking-wider font-bold flex items-center">
                                                    <i class="fas fa-camera mr-2 text-amber-600"></i>
                                                    ẢNH ĐÁNH GIÁ ({{ count($review->images) }})
                                                </div>
                                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3 max-w-2xl">
                                                    @foreach($review->images as $imagePath)
                                                        <div class="relative group cursor-pointer review-image rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300"
                                                            onclick="showReviewImageModal('{{ asset('storage/' . $imagePath) }}')">
                                                            <img src="{{ asset('storage/' . $imagePath) }}" alt="Review Image"
                                                                class="w-full h-32 sm:h-36 md:h-40 object-cover group-hover:scale-110 transition-transform duration-300">
                                                            <div
                                                                class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center">
                                                                <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                                                                    <i class="fas fa-search-plus text-2xl mb-1"></i>
                                                                    <div class="text-xs font-medium">Xem lớn</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                    <!-- Admin Response -->
                                    @if($review->admin_response)
                                        <div
                                            class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-r-lg admin-response">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center">
                                                    <i class="fas fa-reply text-xs"></i>
                                                </div>
                                                <span class="text-xs text-blue-700 uppercase tracking-wider font-bold">
                                                    PHẢN HỒI TỪ BOOKBEE
                                                </span>
                                            </div>
                                            <div class="pl-8">
                                                <p class="text-gray-700 leading-relaxed font-medium italic">{{ $review->admin_response }}
                                                </p>
                                                <div class="mt-2 text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $review->updated_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- <!-- Admin Response -->
                                    @if($review->admin_response)
                                    <div
                                        class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-r-lg admin-response">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center">
                                                <i class="fas fa-reply text-xs"></i>
                                            </div>
                                            <span class="text-xs text-blue-700 uppercase tracking-wider font-bold">
                                                PHẢN HỒI TỪ BOOKBEE
                                            </span>
                                        </div>
                                        <div class="pl-8">
                                            <p class="text-gray-700 leading-relaxed font-medium italic">{{ $review->admin_response }}
                                            </p>
                                            <div class="mt-2 text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $review->updated_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif --}}

                                    <!-- Product Info & Format -->
                                    <div class="mt-4 p-3 bg-gray-50 border-l-4 border-black product-info">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-xs text-gray-600 uppercase tracking-wider font-semibold">
                                                    {{ $review->product_type }}: {{ $review->product_name }}
                                                </span>
                                                <span
                                                    class="px-2 py-1 text-xs font-bold uppercase tracking-wider bg-green-100 text-green-800">
                                                    COMBO
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Accent -->
                                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                                        <div
                                            class="flex items-center space-x-2 text-xs text-black uppercase tracking-wider bg-gray-200">
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
                                <div class="bg-amber-600 text-white px-6 py-3 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-opacity-20 flex items-center justify-center">
                                            <i class="fas fa-comments text-xs"></i>
                                        </div>
                                        <span class="font-bold uppercase tracking-wider text-sm adidas-font">CHƯA CÓ ĐÁNH GIÁ</span>
                                    </div>
                                    <div class="w-6 h-6 border border-white border-opacity-30 flex items-center justify-center">
                                        <i class="fas fa-star text-xs"></i>
                                    </div>
                                </div>

                                <!-- Content Area -->
                                <div class="p-12 text-center">
                                    <div class="space-y-6">
                                        <div class="w-16 h-16 bg-amber-50 flex items-center justify-center mx-auto">
                                            <i class="fas fa-star text-2xl text-amber-400"></i>
                                        </div>
                                        <div class="space-y-2 col-span-1">
                                            <h3 class="text-xl font-bold text-amber-600 uppercase tracking-wider adidas-font">CHƯA CÓ
                                                ĐÁNH GIÁ</h3>
                                            <p class="text-gray-600 text-sm adidas-font">Hãy là người đầu tiên đánh giá combo
                                                này.</p>
                                        </div>
                                        <div class="flex justify-center space-x-1">
                                            <div class="w-2 h-2 bg-amber-600"></div>
                                            <div class="w-2 h-2 bg-amber-300"></div>
                                            <div class="w-2 h-2 bg-amber-300"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Side accent -->
                                <div
                                    class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600">
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Form đánh giá combo --}}
                    @auth
                        @php
                            // Kiểm tra xem user đã mua combo này chưa và đã hoàn thành đơn hàng chưa
                            $userPurchasedCombo = Auth::user()->orders()
                                ->whereHas('orderStatus', function ($q) {
                                    $q->where('name', 'Thành công');
                                })
                                ->whereHas('orderItems', function ($q) use ($combo) {
                                    $q->where('collection_id', $combo->id);
                                })
                                ->exists();

                            // Kiểm tra xem user đã đánh giá combo này chưa
                            $userReviewed = Auth::user()->reviews()
                                ->where('collection_id', $combo->id)
                                ->exists();
                        @endphp

                        @if($userPurchasedCombo && !$userReviewed)
                            <div class="mt-12">
                                <div class="bg-white border-2 border-gray-100 relative overflow-hidden">
                                    <!-- Header Bar -->
                                    <div class="bg-black text-white px-6 py-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-white bg-opacity-20 flex items-center justify-center">
                                                <i class="fas fa-star text-xs"></i>
                                            </div>
                                            <span class="font-bold uppercase tracking-wider text-sm adidas-font">ĐÁNH GIÁ COMBO</span>
                                        </div>
                                        <div class="w-6 h-6 border border-white border-opacity-30 flex items-center justify-center">
                                            <i class="fas fa-edit text-xs"></i>
                                        </div>
                                    </div>

                                    <!-- Content Area -->
                                    <div class="p-6">
                                        <form action="{{ route('account.reviews.store') }}" method="POST" class="space-y-6">
                                            @csrf
                                            <input type="hidden" name="collection_id" value="{{ $combo->id }}">
                                            @php
                                                // Tìm order_id của đơn hàng đã hoàn thành có chứa combo này
                                                $completedOrder = Auth::user()->orders()
                                                    ->whereHas('orderStatus', function ($q) {
                                                        $q->where('name', 'Thành công');
                                                    })
                                                    ->whereHas('orderItems', function ($q) use ($combo) {
                                                        $q->where('collection_id', $combo->id);
                                                    })
                                                    ->first();
                                             @endphp
                                            @if($completedOrder)
                                                <input type="hidden" name="order_id" value="{{ $completedOrder->id }}">
                                            @endif

                                            <!-- Rating Section -->
                                            <div class="space-y-3">
                                                <label class="block text-sm font-bold text-black uppercase tracking-wider adidas-font">
                                                    Đánh giá của bạn
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex space-x-1 rating-stars">
                                                        @for($i = 5; $i >= 1; $i--)
                                                            <input type="radio" id="combo-star-{{ $i }}" name="rating" value="{{ $i }}"
                                                                class="sr-only" {{ $i == 5 ? 'checked' : '' }}>
                                                            <label for="combo-star-{{ $i }}"
                                                                class="text-gray-300 text-2xl cursor-pointer transition-all duration-200 hover:text-yellow-400 hover:scale-110 star-label"
                                                                data-star="{{ $i }}">★</label>
                                                        @endfor
                                                    </div>
                                                    <span class="text-sm text-gray-600 ml-3 rating-text">Tuyệt vời</span>
                                                </div>
                                            </div>

                                            <!-- Comment Section -->
                                            <div class="space-y-3">
                                                <label for="combo-comment"
                                                    class="block text-sm font-bold text-black uppercase tracking-wider adidas-font">
                                                    Chia sẻ trải nghiệm của bạn về combo này
                                                </label>
                                                <textarea id="combo-comment" name="comment" rows="4"
                                                    class="w-full px-4 py-3 border-2 border-gray-300 focus:border-black focus:ring-0 text-sm resize-none transition-all duration-200"
                                                    placeholder="Chia sẻ trải nghiệm của bạn về combo này...">{{ old('comment') }}</textarea>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                                <div class="flex items-center space-x-2 text-xs text-gray-500 uppercase tracking-wider">
                                                    <i class="fas fa-info-circle w-3"></i>
                                                    <span>Đánh giá sẽ được hiển thị sau khi duyệt</span>
                                                </div>
                                                <button type="submit"
                                                    class="px-8 py-3 bg-black hover:bg-gray-800 text-white text-sm font-bold uppercase tracking-wider transition-all duration-300 flex items-center space-x-2">
                                                    <i class="fas fa-paper-plane text-xs"></i>
                                                    <span>GỬI ĐÁNH GIÁ</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Side accent -->
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            @endif

            {{-- Sản phẩm liên quan (đồng bộ style sách đơn, fix ảnh) --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="w-1 h-12 bg-amber-600"></div>
                    <div>
                        <h2 class="adidas-font text-3xl font-bold text-amber-600 uppercase tracking-wider">
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
                        <div class="bg-white border border-gray-200 overflow-hidden group hover:border-amber-600 transition-all duration-300 p-2 cursor-pointer relative"
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
                                {{-- <!-- Wishlist Button -->
                                <div class="absolute top-2 right-2">
                                    <button
                                        class="w-10 h-10 bg-white bg-opacity-90 flex items-center justify-center border border-gray-200 hover:bg-amber-600 hover:text-white hover:border-amber-600 transition-all duration-300 transform hover:scale-110"
                                        onclick="event.stopPropagation();">
                                        <i class="far fa-heart text-sm"></i>
                                    </button>
                                </div> --}}
                            </div>
                            <div class="p-2">
                                <h3
                                    class="font-bold text-black text-base leading-tight group-hover:text-gray-600 transition-colors duration-300 line-clamp-2 min-h-[40px]">
                                    <span class="hover:underline">{{ $related->name }}</span>
                                </h3>
                                <p class="text-xs text-gray-600 uppercase tracking-wide font-medium min-h-[18px] truncate">
                                    {{ $related->books->pluck('authors')->flatten()->pluck('name')->unique()->join(', ') ?: 'KHÔNG RÕ TÁC GIẢ' }}
                                </p>
                                <div class="flex items-center space-x-2 pt-1">
                                    <span class="text-lg font-bold text-black">
                                        {{ number_format($related->combo_price, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                <div class="pt-1">
                                    <a href="{{ route('combos.show', $related->slug ?? $related->id) }}"
                                        onclick="event.stopPropagation();"
                                        class="adidas-btn-enhanced w-full h-10 bg-black text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-center hover:bg-gray-800">
                                        <span class="relative flex items-center space-x-1">
                                            <i class="fas fa-eye text-xs"></i>
                                            <span>XEM CHI TIẾT</span>
                                            <i
                                                class="fas fa-arrow-right text-xs transform group-hover/btn:translate-x-1 transition-transform duration-300"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-center pt-8 mb-8">
                    <a href="{{ route('combos.index') }}"
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
                                    <img src="{{ $book->cover_image
                    ? asset('storage/' . $book->cover_image)
                    : ($book->images->first()
                        ? asset('storage/' . $book->images->first()->image_url)
                        : asset('images/default.jpg')) }}" alt="{{ $book->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                        <span class="text-gray-600 font-medium">TÁC GIẢ</span>
                                        <span class="text-black font-semibold truncate">
                                            @if($book->authors && $book->authors->count())
                                                {{ $book->authors->pluck('name')->join(', ') }}
                                            @else
                                                Không rõ
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                        <span class="text-gray-600 font-medium">THƯƠNG HIỆU</span>
                                        <span
                                            class="text-black font-semibold truncate">{{ $book->brand->name ?? 'Không rõ' }}</span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                        <span class="text-gray-600 font-medium">ISBN</span>
                                        <span class="text-black font-semibold truncate">{{ $book->isbn }}</span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                        <span class="text-gray-600 font-medium">XUẤT BẢN</span>
                                        <span class="text-black font-semibold">{{ $book->publication_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                        <span class="text-gray-600 font-medium">SỐ TRANG</span>
                                        <span class="text-black font-semibold">{{ $book->page_count }}</span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:justify-between text-sm gap-1 sm:gap-0">
                                        <span class="text-gray-600 font-medium">THỂ LOẠI</span>
                                        <span
                                            class="text-black font-semibold truncate">{{ $book->category->name ?? 'Không rõ' }}</span>
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
                                $finalPrice = $defaultPrice - $discount;
                            @endphp
                            <div class="price-section space-y-4">
                                <div class="flex items-end space-x-4">
                                    <span id="bookPrice" data-base-price="{{ $defaultPrice }}"
                                        data-book-status="{{ $book->status }}"
                                        data-preorder-percent="{{ (float) ($book->preorder_discount_percent ?? 0) }}"
                                        class="text-4xl font-bold text-black adidas-font">{{ number_format($finalPrice, 0, ',', '.') }}₫</span>
                                    @if ($discount > 0)
                                        <span id="originalPrice"
                                            class="text-xl text-gray-500 line-through adidas-font">{{ number_format($defaultPrice, 0, ',', '.') }}₫</span>
                                        <span id="discountText"
                                            class="bg-red-600 text-white px-3 py-1 text-sm font-bold adidas-font uppercase tracking-wider">-<span
                                                id="discountAmount">{{ number_format($discount, 0, ',', '.') }}</span>₫</span>
                                    @else
                                        <span id="originalPrice" class="text-xl text-gray-500 line-through adidas-font"
                                            style="display: none;"></span>
                                        <span id="discountText"
                                            class="bg-red-600 text-white px-3 py-1 text-sm font-bold adidas-font uppercase tracking-wider"
                                            style="display: none;">
                                            -<span id="discountAmount">0</span>₫
                                        </span>
                                    @endif
                                </div>
                                @php
                                    $isEbook = false;
                                    if (isset($defaultFormat->format_name)) {
                                        $isEbook = stripos($defaultFormat->format_name, 'ebook') !== false;
                                    }
                                    $defaultStock = (int) ($defaultFormat->stock ?? $book->stock ?? 0);

                                    // Priority 1: Check book.status first 
                                    switch ($book->status) {
                                        case 'Ngừng Kinh Doanh':
                                            $statusText = 'NGƯNG KINH DOANH';
                                            $statusDot = 'bg-gray-500';
                                            $badgeClass = 'bg-gray-100 text-gray-700 border-gray-300';
                                            break;
                                        case 'Sắp Ra Mắt':
                                            $statusText = 'SẮP RA MẮT';
                                            $statusDot = 'bg-yellow-500';
                                            $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                            break;
                                        case 'Hết Hàng Tồn Kho':
                                            $statusText = 'HẾT HÀNG TỒN KHO';
                                            $statusDot = 'bg-red-500';
                                            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                            break;
                                        case 'Còn Hàng':
                                        default:
                                            // Priority 2: Only when status = 'Còn Hàng', check if ebook or stock levels
                                            if ($isEbook) {
                                                $statusText = 'EBOOK - CÓ SẴN';
                                                $statusDot = 'bg-blue-500';
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                            } elseif ($defaultStock == 0) {
                                                $statusText = 'HẾT HÀNG';
                                                $statusDot = 'bg-red-500';
                                                $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                            } elseif ($defaultStock >= 1 && $defaultStock <= 9) {
                                                $statusText = 'SẮP HẾT HÀNG';
                                                $statusDot = 'bg-yellow-500';
                                                $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                            } elseif ($defaultStock >= 10) {
                                                $statusText = 'CÒN HÀNG';
                                                $statusDot = 'bg-green-500';
                                                $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                            } else {
                                                $statusText = 'HẾT HÀNG';
                                                $statusDot = 'bg-red-500';
                                                $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                            }
                                            break;
                                    }
                                @endphp
                                <div class="flex flex-col sm:flex-row sm:items-end gap-2 sm:gap-4 mt-2">
                                    <span id="stockBadge"
                                        class="inline-flex items-center px-3 py-1 text-xs sm:text-sm font-semibold border adidas-font uppercase tracking-wider {{ $badgeClass }} whitespace-nowrap w-fit">
                                        <span id="stockDot"
                                            class="w-2 h-2 rounded-full mr-2 {{ $statusDot }} inline-block flex-shrink-0"></span>
                                        <span id="stockText" class="truncate">{{ $statusText }}</span>
                                    </span>
                                    @if(
                                            ($book->status === 'Còn Hàng' && $defaultStock > 0) ||
                                            $isEbook
                                        )
                                        @if(!in_array($book->status, ['Ngừng Kinh Doanh', 'Sắp Ra Mắt', 'Hết Hàng Tồn Kho']))
                                            <span id="stockQuantityDisplay"
                                                class="text-xs sm:text-sm text-gray-600 adidas-font whitespace-nowrap">
                                                (<span class="font-bold text-black" id="productQuantity">{{ $defaultStock }}</span> cuốn
                                                còn lại)
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Quà tặng kèm - Chỉ hiển thị khi chọn định dạng sách vật lý -->
                            @if(!in_array($book->status, ['Ngừng Kinh Doanh', 'Sắp Ra Mắt', 'Hết Hàng Tồn Kho']))
                                @php
                                    $selectedFormat = $book->formats->first(); // Lấy định dạng mặc định hoặc đầu tiên

                                    // Kiểm tra xem định dạng đang chọn có phải là sách vật lý không
                                    $isPhysicalFormatSelected = $selectedFormat && str_contains($selectedFormat->format_name, 'Sách Vật Lý');
                                @endphp
                                @if(isset($bookGifts) && $bookGifts->count())
                                    <div id="giftsSection" class="book-gifts-section mt-8" @if(!$isPhysicalFormatSelected)
                                    style="display: none;" @endif>
                                        <!-- Enhanced Gift Section - Adidas Style -->
                                        <div class="bg-white border-2 border-gray-100 relative overflow-hidden">
                                            <!-- Header Bar -->
                                            <div class="bg-amber-600 text-white px-6 py-4 flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-white bg-opacity-20 flex items-center justify-center">
                                                        <i class="fas fa-gift text-sm"></i>
                                                    </div>
                                                    <h3 class="font-bold uppercase tracking-wider text-lg adidas-font">QUÀ TẶNG KÈM</h3>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="bg-black bg-opacity-20 px-2 py-1 text-xs font-bold uppercase tracking-wider">
                                                        {{ $bookGifts->count() }} món
                                                    </span>
                                                    <div class="w-6 h-6 border border-white border-opacity-30 flex items-center justify-center">
                                                        <i class="fas fa-star text-xs"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Content Area -->
                                            <div class="p-6">
                                                <ul class="space-y-4">
                                                    @foreach($bookGifts as $gift)
                                                        <li class="gift-item bg-white border border-gray-200 relative overflow-hidden hover:border-amber-600 transition-all duration-300 {{ $gift->quantity <= 0 ? 'opacity-60 bg-gray-50' : '' }}">
                                                            <!-- Gift Item Header -->
                                                            <div class="flex items-start gap-4 p-4">
                                                                <!-- Gift Image/Icon -->
                                                                <div class="flex-shrink-0">
                                                                    @if($gift->gift_image)
                                                                        <img src="{{ asset('storage/' . $gift->gift_image) }}" alt="{{ $gift->gift_name }}"
                                                                            class="w-16 h-16 object-cover border border-gray-200 {{ $gift->quantity <= 0 ? 'grayscale' : '' }}">
                                                                    @else
                                                                        <div class="w-16 h-16 flex items-center justify-center bg-amber-50 border border-amber-200 {{ $gift->quantity <= 0 ? 'text-gray-400' : 'text-amber-600' }}">
                                                                            <i class="fas fa-gift text-2xl"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <!-- Gift Info -->
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="flex items-start justify-between">
                                                                        <div class="flex-1">
                                                                            <h4 class="font-bold text-black text-base adidas-font uppercase tracking-wider">{{ $gift->gift_name }}</h4>
                                                                            @if($gift->gift_description)
                                                                                <p class="text-sm text-gray-700 mt-1 font-medium">{{ $gift->gift_description }}</p>
                                                                            @endif
                                                                        </div>
                                                                        
                                                                        <!-- Status Badge -->
                                                                        <div class="flex-shrink-0 ml-4">
                                                                            @if($gift->quantity > 0)
                                                                                <span class="bg-green-100 text-green-800 px-3 py-1 text-xs font-bold uppercase tracking-wider border border-green-200">
                                                                                    Còn {{ $gift->quantity }}
                                                                                </span>
                                                                            @else
                                                                                <span class="bg-red-100 text-red-800 px-3 py-1 text-xs font-bold uppercase tracking-wider border border-red-200">
                                                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Hết hàng
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <!-- Gift Dates -->
                                                                    @if($gift->start_date || $gift->end_date)
                                                                        <div class="mt-3 p-2 bg-amber-50 border border-amber-200">
                                                                            <div class="flex items-center space-x-4 text-xs text-amber-800 font-semibold uppercase tracking-wider">
                                                                                <i class="fas fa-calendar-alt text-amber-600"></i>
                                                                                @if($gift->start_date)
                                                                                    <span>Từ: {{ Carbon::parse($gift->start_date)->format('d/m/Y') }}</span>
                                                                                @endif
                                                                                @if($gift->end_date)
                                                                                    <span>Đến: {{ Carbon::parse($gift->end_date)->format('d/m/Y') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Gift Item Side Accent -->
                                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600"></div>
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                <!-- Bottom Section -->
                                                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                                                    <div class="flex items-center space-x-2 text-xs text-gray-500 uppercase tracking-wider">
                                                        <i class="fas fa-info-circle"></i>
                                                        <span class="font-semibold">Chỉ áp dụng cho sách vật lý</span>
                                                    </div>
                                                    <div class="flex space-x-1">
                                                        <div class="w-2 h-2 bg-amber-600"></div>
                                                        <div class="w-2 h-2 bg-amber-300"></div>
                                                        <div class="w-2 h-2 bg-amber-300"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section Side Accent -->
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600"></div>
                                        </div>
                                    </div>
                                @endif
                            @endif



                            <!-- Enhanced Format Selection -->
                            @if ($book->formats->count())
                                <div class="format-selection space-y-3" @if(in_array($book->status, ['Ngừng Kinh Doanh', 'Sắp Ra Mắt', 'Hết Hàng Tồn Kho'])) style="display: none;" @endif>
                                    <label for="bookFormatSelect"
                                        class="block text-sm font-bold text-black uppercase tracking-wider">Định dạng sách</label>
                                    <div class="relative">
                                        <select id="bookFormatSelect" data-gifts-section="giftsSection"
                                            class="adidas-select w-full px-6 py-4 text-lg font-semibold appearance-none bg-white border-2 border-gray-300 focus:border-black rounded-none transition-colors duration-300">
                                            @php
                                                // Prioritize physical books first, then ebook
                                                $physicalFormats = $book->formats->filter(function ($f) {
                                                    return stripos($f->format_name, 'ebook') === false;
                                                });
                                                $ebookFormat = $book->formats->first(function ($f) {
                                                    return stripos($f->format_name, 'ebook') !== false;
                                                });
                                            @endphp
                                            {{-- Show physical formats first (selected by default) --}}
                                            @foreach($physicalFormats as $index => $format)
                                                <option value="{{ $format->id }}" data-price="{{ $format->price }}"
                                                    data-stock="{{ $format->stock }}" data-discount="{{ $format->discount }}"
                                                    data-format="{{ $format->format_name }}"
                                                    {{ $index === 0 ? 'selected' : '' }}>
                                                    {{ strtoupper($format->format_name) }} - {{ number_format($format->price, 0, ',', '.') }}₫
                                                    @if($format->discount > 0)
                                                        <span class="text-red-600">(-{{ number_format($format->discount, 0, ',', '.') }}₫)</span>
                                                    @endif
                                                </option>
                                            @endforeach
                                            {{-- Show ebook format last --}}
                                            @if($ebookFormat)
                                                <option value="{{ $ebookFormat->id }}" data-price="{{ $ebookFormat->price }}"
                                                    data-stock="{{ $ebookFormat->stock }}" data-discount="{{ $ebookFormat->discount }}"
                                                    data-format="{{ $ebookFormat->format_name }}"
                                                    data-sample-url="{{ $ebookFormat->sample_file_url ? route('ebook.sample.view', $ebookFormat->id) : '' }}"
                                                    data-allow-sample="{{ $ebookFormat->allow_sample_read ? '1' : '0' }}"
                                                    {{ $physicalFormats->count() === 0 ? 'selected' : '' }}>
                                                    {{ $ebookFormat->format_name }}
                                                </option>
                                            @endif
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                            <i class="fas fa-chevron-down text-black"></i>
                                        </div>
                                    </div>
                                    <!-- Preview Button for Ebook -->
                                    <div id="previewSection" class="@if(!$isEbook) hidden @endif mt-4">
                                        <a href="#" id="previewBtn"
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
                            <!-- Enhanced Attributes - Improved UI -->
                            {{-- Thuộc tính & Biến thể --}}
                            @php
                                // Kiểm tra xem sách có biến thể không (cả cách cũ và mới)
                                $hasBookAttributeValues = $book->bookAttributeValues && $book->bookAttributeValues->count() > 0;
                                $hasVariants = $book->variants && $book->variants->count() > 0;
                                $hasAnyVariants = $hasBookAttributeValues || $hasVariants;
                            @endphp
                            
                            @if($hasAnyVariants)
                                <div class="mb-4">
                                    <label for="variantCombinationSelect" class="block font-semibold mb-2">Chọn tổ hợp biến thể:</label>
                                    <select id="variantCombinationSelect" name="variant_id" class="adidas-select w-full">
                                        @foreach($book->variants as $variant)
                                            <option value="{{ $variant->id }}"
                                                data-price="{{ $variant->price }}"
                                                data-discount="{{ $variant->discount ?? 0 }}"
                                                data-stock="{{ $variant->stock ?? 0 }}"
                                            >
                                                {{ $variant->attributeValues->pluck('value')->implode(' - ') }}
                                                @if($variant->stock === 0) (Hết hàng)@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                                    <span class="text-xs font-medium text-gray-600">Tồn kho:</span>
                                                    <span id="selectedVariantStock" class="font-bold text-green-600 text-sm">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- Enhanced Quantity & Add to Cart Section -->
                            <div class="purchase-section space-y-6 pt-6">
                                @php
                                    // Only hide for special statuses, let JavaScript handle ebook/physical logic
                                    $isSpecialStatus = in_array($book->status, ['Ngừng Kinh Doanh', 'Sắp Ra Mắt', 'Hết Hàng Tồn Kho']);
                                @endphp
                                <div class="quantity-section space-y-3" @if($isSpecialStatus) style="display:none" @else style="display:block" @endif>
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
                                    @if(isset($book) && $book->canPreorder())
                                        <!-- Preorder Button -->
                                        <div class="preorder-section bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 p-4 rounded-lg mb-4">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="bg-blue-600 text-white px-2 py-1 text-xs font-bold uppercase rounded">
                                                    ĐẶT TRƯỚC
                                                </div>
                                                <span class="text-blue-900 font-bold text-sm">
                                                    Ra mắt: {{ $book->release_date ? $book->release_date->format('d/m/Y') : 'TBD' }}
                                                </span>
                                            </div>
                                            @php
                                                $preorderPrice = $book->getPreorderPrice();
                                                $originalPrice = $book->formats->first()->price ?? 0;
                                            @endphp
                                            @if($preorderPrice && $preorderPrice < $originalPrice)
                                                <div class="mb-3">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-xl font-bold text-blue-600">
                                                            {{ number_format($preorderPrice, 0, ',', '.') }}₫
                                                        </span>
                                                        <span class="text-sm text-gray-500 line-through">
                                                            {{ number_format($originalPrice, 0, ',', '.') }}₫
                                                        </span>
                                                    </div>
                                                    <div class="bg-red-100 text-red-600 px-2 py-1 text-xs font-bold rounded inline-block">
                                                        TIẾT KIỆM {{ number_format($originalPrice - $preorderPrice, 0, ',', '.') }}₫
                                                    </div>
                                                </div>
                                            @endif
                                            <button onclick="window.location.href='{{ route('preorders.create', $book) }}'"
                                                    class="adidas-btn-enhanced w-full h-16 bg-black text-white font-bold text-lg uppercase tracking-wider transition-all duration-300 flex items-center justify-center adidas-font">
                                                <i class="ri-bookmark-line mr-3 text-xl"></i>
                                                <span>ĐẶT TRƯỚC NGAY</span>
                                            </button>
                                        </div>
                                    @else
                                        <!-- Regular Add to Cart Button -->
                                        <button id="addToCartBtn"
                                            class="adidas-btn-enhanced w-full h-16 bg-black text-white font-bold text-lg uppercase tracking-wider transition-all duration-300 flex items-center justify-center adidas-font">
                                            <i class="fas fa-shopping-bag mr-3"></i>
                                            <span>THÊM VÀO GIỎ HÀNG</span>
                                        </button>
                                    @endif

                                    <!-- Wishlist Button -->
                                    <button id="wishlistBtn" data-book-id="{{ $book->id }}"
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
                        // Decode nếu mô tả trong DB lưu dạng HTML entity
                        $decodedDesc = html_entity_decode($book->description ?? '');
                        // Xóa toàn bộ thẻ HTML
                        $bookDesc = strip_tags($decodedDesc);
                        $showBookMore = \Illuminate\Support\Str::length($bookDesc) > 200;
                    @endphp
                    @if(isset($book))
                        <!-- Enhanced Description Section - Adidas Style -->
                        <div class="mt-16 bg-white border-2 border-gray-100 relative overflow-hidden">
                            <!-- Header Bar -->
                            <div class="bg-amber-600 text-white px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-opacity-20 flex items-center justify-center">
                                        <i class="fas fa-align-left text-sm"></i>
                                    </div>
                                    <h2 class="font-bold uppercase tracking-wider text-lg adidas-font">MÔ TẢ SÁCH</h2>
                                </div>
                                <div class="w-6 h-6 border border-white border-opacity-30 flex items-center justify-center">
                                    <i class="fas fa-book text-xs"></i>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="p-6">
                                <div class="relative">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600"></div>
                                    <div class="pl-6">
                                        <div id="bookDescription" class="text-gray-800 text-base leading-relaxed font-medium"
                                            data-full="{{ $bookDesc }}" data-short="{{ \Illuminate\Support\Str::limit($bookDesc, 200, '...') }}">
                                            @if (empty($bookDesc))
                                                <div class="text-center py-8">
                                                    <div class="w-16 h-16 bg-amber-50 flex items-center justify-center mx-auto mb-4">
                                                        <i class="fas fa-file-alt text-2xl text-amber-400"></i>
                                                    </div>
                                                    <span class="italic text-gray-400 text-lg">Không có mô tả nào</span>
                                                </div>
                                            @else
                                                {{ $showBookMore ? \Illuminate\Support\Str::limit($bookDesc, 200, '...') : $bookDesc }}
                                            @endif
                                        </div>
                                        @if($showBookMore)
                                            <button id="showMoreBtn" 
                                                class="mt-4 px-4 py-2 bg-amber-600 text-white font-bold uppercase tracking-wider text-sm hover:bg-amber-700 transition-all duration-300 border-none adidas-font">
                                                Xem thêm
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Bottom Accent -->
                                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-info-circle"></i>
                                        <span class="font-semibold">Thông tin chi tiết</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <div class="w-2 h-2 bg-amber-600"></div>
                                        <div class="w-2 h-2 bg-amber-300"></div>
                                        <div class="w-2 h-2 bg-amber-300"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Side accent -->
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600"></div>
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
                                <div class="w-1 h-12 bg-amber-600"></div>
                                <div>
                                    <h2 class="adidas-font text-3xl font-bold text-amber-600 uppercase tracking-wider">
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
                                    class="review-card bg-white border-2 border-gray-100 relative overflow-hidden group hover:border-amber-600 transition-all duration-300">
                                    <!-- Header Bar -->
                                    <div class="bg-amber-600 text-white px-6 py-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-opacity-20 flex items-center justify-center">
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
                                                {{ $review->created_at->diffForHumans() }}
                                            </div>
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
                                                <div class="flex text-yellow-400 text-lg star-rating">
                                                    @for ($i = 0; $i < $review->rating; $i++)
                                                        ★
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="w-2 h-2 bg-black"></div>
                                        </div>

                                        <!-- Comment -->
                                        <div class="relative">
                                            {{-- <div
                                                class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-black via-gray-400 to-black">
                                            </div> --}}
                                            <div class="pl-6">
                                                <p class="text-gray-800 leading-relaxed font-medium">
                                                    <i class="fas fa-share text-gray-500"></i>
                                                    {{ $review->comment }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Review Images -->
                                        @if($review->images && count($review->images) > 0)
                                            <div class="mt-5">
                                                <div class="text-sm text-gray-700 mb-3 uppercase tracking-wider font-bold flex items-center">
                                                    <i class="fas fa-camera mr-2 text-amber-600"></i>
                                                    ẢNH ĐÁNH GIÁ ({{ count($review->images) }})
                                                </div>
                                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3 max-w-2xl">
                                                    @foreach($review->images as $imagePath)
                                                        <div class="relative group cursor-pointer review-image rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300"
                                                            onclick="showReviewImageModal('{{ asset('storage/' . $imagePath) }}')">
                                                            <img src="{{ asset('storage/' . $imagePath) }}" alt="Review Image"
                                                                class="w-full h-32 sm:h-36 md:h-40 object-cover group-hover:scale-110 transition-transform duration-300">
                                                            <div
                                                                class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center">
                                                                <div class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                                                                    <i class="fas fa-search-plus text-2xl mb-1"></i>
                                                                    <div class="text-xs font-medium">Xem lớn</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Product Info & Format -->
                                        <div class="mt-4 p-3 bg-gray-50 border-l-4 border-amber-600 product-info">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-xs text-gray-600 uppercase tracking-wider font-semibold">
                                                        {{ $review->product_type }}: {{ $review->product_name }}
                                                    </span>
                                                    @php
                                                        $orderItem = $review->order->orderItems->firstWhere('book_id', $review->book_id);

                                                    @endphp
                                                    @if($orderItem && $orderItem->bookFormat)
                                                        {{-- @php
                                                        dd($orderItem->bookFormat->format_name);
                                                        @endphp --}}
                                                        <span
                                                            class="px-2 py-1 text-xs font-bold uppercase tracking-wider rounded-none {{ strtolower($orderItem->bookFormat->format_name) === 'ebook' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                            {{ $orderItem->bookFormat->format_name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Admin Response Section -->
                                        @if($review->admin_response)
                                            <div class="mt-4 bg-blue-50 border-l-4 border-blue-600 admin-response">
                                                <!-- Admin Response Header -->
                                                <div class="bg-blue-600 text-white px-4 py-2 flex items-center space-x-3">
                                                    <div class="w-6 h-6 bg-white bg-opacity-20 flex items-center justify-center">
                                                        <i class="fas fa-user-tie text-xs"></i>
                                                    </div>
                                                    <span class="font-bold uppercase tracking-wider text-sm adidas-font">PHẢN HỒI TỪ BOOKBEE</span>
                                                    <div class="ml-auto">
                                                        <div class="w-2 h-2 bg-white bg-opacity-60"></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Admin Response Content -->
                                                <div class="p-4">
                                                    <div class="relative">
                                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-600 via-blue-400 to-blue-600"></div>
                                                        <div class="pl-4">
                                                            <p class="text-gray-800 leading-relaxed font-medium admin-response-text">
                                                                {{ $review->admin_response }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Admin Response Footer -->
                                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-blue-200">
                                                        <div class="flex items-center space-x-2 text-xs text-blue-600 uppercase tracking-wider">
                                                            <i class="fas fa-shield-alt"></i>
                                                            <span class="font-semibold">Phản hồi chính thức</span>
                                                        </div>
                                                        <div class="flex space-x-1">
                                                            <div class="w-2 h-2 bg-blue-600"></div>
                                                            <div class="w-2 h-2 bg-blue-300"></div>
                                                            <div class="w-2 h-2 bg-blue-300"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Bottom Accent -->
                                        <div class="flex items-center justify-between mt-6 pt-4 border-t">
                                            <div
                                                class="flex items-center space-x-2 text-xs text-gray-500 uppercase tracking-wider bg-gray-200">
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
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600">
                                    </div>
                                </div>
                            @empty
                                <!-- Enhanced Empty State -->
                                <div class="bg-white border-2 border-gray-100 relative overflow-hidden">
                                    <!-- Header Bar -->
                                    <div class="bg-amber-600 text-white px-6 py-3 flex items-center justify-between">
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
                                            <div class="w-16 h-16 bg-amber-50  flex items-center justify-center mx-auto">
                                                <i class="fas fa-star text-2xl text-amber-400"></i>
                                            </div>
                                            <div class="space-y-2 col-span-1">
                                                <h3 class="text-xl font-bold text-amber-600 uppercase tracking-wider adidas-font">CHƯA
                                                    CÓ
                                                    ĐÁNH GIÁ</h3>
                                                <p class="text-gray-600 text-sm adidas-font">Hãy là người đầu tiên đánh giá sản phẩm
                                                    này.</p>
                                            </div>
                                            <div class="flex justify-center space-x-1">
                                                <div class="w-2 h-2 bg-amber-600 "></div>
                                                <div class="w-2 h-2 bg-amber-300 "></div>
                                                <div class="w-2 h-2 bg-amber-300 "></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Side accent -->
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-amber-600 via-amber-400 to-amber-600">
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
                                <div class="w-1 h-12 bg-amber-600"></div>
                                <div>
                                    <h2 class="adidas-font text-3xl font-bold text-amber-600 uppercase tracking-wider">
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
                                        $finalPrice = $price - ($discount->discount_amount ?? 0);
                                    }

                                    // Áp dụng logic status priority như trang chi tiết chính
                                    $isEbook = $defaultFormat && stripos($defaultFormat->format_name, 'ebook') !== false;
                                    $shouldShowOutOfStock = false;
                                    $outOfStockText = '';

                                    // Priority 1: Check book.status first 
                                    switch ($related->status) {
                                        case 'Ngừng Kinh Doanh':
                                            $shouldShowOutOfStock = true;
                                            $outOfStockText = 'NGỪNG KINH DOANH';
                                            break;
                                        case 'Sắp Ra Mắt':
                                            $shouldShowOutOfStock = true;
                                            $outOfStockText = 'SẮP RA MẮT';
                                            break;
                                        case 'Hết Hàng Tồn Kho':
                                            $shouldShowOutOfStock = true;
                                            $outOfStockText = 'HẾT HÀNG TỒN KHO';
                                            break;
                                        case 'Hết Hàng':
                                            $shouldShowOutOfStock = true;
                                            $outOfStockText = 'HẾT HÀNG';
                                            break;
                                        case 'Còn Hàng':
                                        default:
                                            // Priority 2: Only when status = 'Còn Hàng', check stock levels for physical books
                                            if (!$isEbook && $stock <= 0) {
                                                $shouldShowOutOfStock = true;
                                                $outOfStockText = 'HẾT HÀNG';
                                            }
                                            break;
                                    }
                                @endphp

                                <div class="bg-white border border-gray-200 overflow-hidden group hover:border-amber-600 transition-all duration-300 p-2 cursor-pointer relative"
                                    onclick="window.location.href='{{ route('books.show', $related->slug ?? $related->id) }}'">
                                    <div class="relative aspect-square bg-white border border-gray-100 overflow-hidden mb-2">
                                        <a href="{{ route('books.show', $related->slug ?? $related->id) }}" class="block w-full h-full">
                                            @php
                                                $imageUrl = $related->cover_image
                                                    ? asset('storage/' . $related->cover_image)
                                                    : ($related->images->first()
                                                        ? asset('storage/' . $related->images->first()->image_url)
                                                        : asset('images/default.jpg'));
                                            @endphp
                                            <img src="{{ $imageUrl }}" alt="{{ $related->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        </a>
                                        @if($shouldShowOutOfStock)
                                            <div class="absolute top-2 left-2">
                                                <span class="bg-red-600 text-white text-xs font-bold uppercase tracking-wider px-2 py-0.5">
                                                    {{ $outOfStockText }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- <!-- Wishlist Button -->
                                        <div class="absolute top-2 right-2">
                                            <button
                                                class="w-10 h-10 bg-white bg-opacity-90 flex items-center justify-center border border-gray-200 hover:bg-amber-600 hover:text-white hover:border-amber-600 transition-all duration-300 transform hover:scale-110"
                                                onclick="event.stopPropagation();">
                                                <i class="far fa-heart text-sm"></i>
                                            </button>
                                        </div> --}}

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
                                                        -{{ number_format($discount->discount_amount ?? 0, 0, ',', '.') }}₫
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
                                            <a href="{{ route('books.show', $related->slug ?? $related->id) }}"
                                                onclick="event.stopPropagation();"
                                                class="adidas-btn-enhanced w-full h-10 bg-black text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 flex items-center justify-center hover:bg-gray-800">
                                                <span class="relative flex items-center space-x-1">
                                                    <i class="fas fa-eye text-xs"></i>
                                                    <span>XEM CHI TIẾT</span>
                                                </span>
                                            </a>
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

    <!-- Modal Đọc Thử Ebook - Enhanced Modern Design -->
    <div id="previewModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 hidden backdrop-blur-sm">
        <div
            class="bg-white w-full max-w-7xl h-full max-h-[95vh] flex flex-col shadow-2xl border border-gray-200 rounded-lg overflow-hidden">
            <!-- Enhanced Header -->
            <div
                class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-gray-900 to-black text-white border-b">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-book-open text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold uppercase tracking-wider adidas-font">Đọc thử sách</h3>
                        <p class="text-sm text-gray-300 adidas-font">{{ $book->title ?? '' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- PDF Controls -->
                    <div class="flex items-center space-x-2 bg-white/10 rounded-lg px-3 py-2">
                        <button id="zoomOut" class="text-white hover:text-blue-300 transition-colors p-1" title="Thu nhỏ">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <span id="zoomLevel" class="text-white text-sm font-medium min-w-[50px] text-center">100%</span>
                        <button id="zoomIn" class="text-white hover:text-blue-300 transition-colors p-1" title="Phóng to">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                    <div class="flex items-center space-x-2 bg-white/10 rounded-lg px-3 py-2">
                        <button id="prevPage" class="text-white hover:text-blue-300 transition-colors p-1"
                            title="Trang trước">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="pageInfo" class="text-white text-sm font-medium min-w-[60px] text-center">1 / 1</span>
                        <button id="nextPage" class="text-white hover:text-blue-300 transition-colors p-1"
                            title="Trang sau">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <button id="fullscreenBtn" class="text-white hover:text-blue-300 transition-colors p-2"
                        title="Toàn màn hình">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button id="closePreviewModal"
                        class="text-white hover:text-red-400 text-2xl font-bold focus:outline-none transition-colors duration-300 p-2"
                        title="Đóng">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Enhanced Content Area -->
            <div id="previewContent" class="flex-1 relative bg-gray-100 overflow-hidden">
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="absolute inset-0 flex items-center justify-center bg-white z-10">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        <p class="text-gray-600 font-medium adidas-font">Đang tải nội dung...</p>
                    </div>
                </div>

                <!-- PDF Viewer Container -->
                <div id="pdfViewerContainer" class="w-full h-full flex flex-col items-center overflow-auto bg-gray-200 p-4"
                    style="scroll-behavior: smooth;">
                    <div id="pdfCanvas" class="bg-white shadow-lg border border-gray-300 rounded-lg overflow-hidden">
                        <!-- PDF will be rendered here -->
                    </div>

                </div>

                <!-- Fallback iframe for compatibility -->
                <iframe id="previewIframe" src="{{ asset('storage/book/book_' . $book->id . '.pdf') }}"
                    class="w-full h-full border-none bg-white hidden"></iframe>

                <!-- Enhanced Limit Notice -->
                <div id="previewLimitNotice"
                    class="hidden absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black/90 to-transparent p-6">
                    <div class="text-center text-white">
                        <div
                            class="inline-flex items-center space-x-3 bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 border border-white/20">
                            <i class="fas fa-lock text-yellow-400 text-lg"></i>
                            <span class="font-bold text-lg adidas-font uppercase tracking-wider">Mua sách để đọc toàn bộ nội
                                dung</span>
                            <i class="fas fa-arrow-right text-yellow-400"></i>
                        </div>
                        <p class="text-sm text-gray-300 mt-2 adidas-font">Bạn đang xem phiên bản giới hạn</p>
                    </div>
                </div>
            </div>

            <!-- Enhanced Footer -->
            <div class="bg-gray-50 border-t px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span class="flex items-center space-x-1">
                            <i class="fas fa-eye text-blue-600"></i>
                            <span class="adidas-font">Chế độ xem thử</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="adidas-font">Định dạng PDF</span>
                        </span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button id="downloadSample"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors adidas-font flex items-center space-x-2">
                            <i class="fas fa-download"></i>
                            <span>Tải mẫu</span>
                        </button>
                        <button id="buyNowFromPreview"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold transition-colors adidas-font flex items-center space-x-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Mua ngay</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const formatSelect = document.getElementById('bookFormatSelect');
                const giftsSection = document.getElementById('giftsSection');

                if (formatSelect && giftsSection) {
                    // Kiểm tra ngay khi trang tải
                    const checkFormat = () => {
                        const selectedOption = formatSelect.options[formatSelect.selectedIndex];
                        const isPhysicalFormat = selectedOption.text.includes('Sách Vật Lý');
                        giftsSection.style.display = isPhysicalFormat ? 'block' : 'none';
                    };

                    // Chạy kiểm tra lần đầu
                    checkFormat();

                    // Lắng nghe sự kiện thay đổi
                    formatSelect.addEventListener('change', checkFormat);
                }
            });
        </script>
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

            // Helper function to get CSS classes for status badges
            function getStatusClasses(type, value) {
                const classMap = {
                    stock: {
                        green: 'font-bold text-green-600 bg-green-100 px-2 py-1 rounded text-sm',
                        yellow: 'font-bold text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-sm',
                        red: 'font-bold text-red-600 bg-red-100 px-2 py-1 rounded text-sm'
                    },
                    badge: {
                        gray: 'bg-gray-100 text-gray-700 border-gray-300',
                        yellow: 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        red: 'bg-red-50 text-red-700 border-red-200',
                        green: 'bg-green-50 text-green-700 border-green-200',
                        blue: 'bg-blue-50 text-blue-700 border-blue-200'
                    },
                    dot: {
                        gray: 'bg-gray-500',
                        yellow: 'bg-yellow-500',
                        red: 'bg-red-500',
                        green: 'bg-green-500',
                        blue: 'bg-blue-500'
                    }
                };
                return classMap[type] && classMap[type][value] ? classMap[type][value] : '';
            }

            // Helper function to get stock status configuration
            function getStockStatusConfig(bookStatus, stock = 0, isEbook = false) {
                const statusMap = {
                    'Ngừng Kinh Doanh': {
                        text: 'NGƯNG KINH DOANH',
                        badge: 'gray',
                        dot: 'gray'
                    },
                    'Sắp Ra Mắt': {
                        text: 'SẮP RA MẮT',
                        badge: 'yellow',
                        dot: 'yellow'
                    },
                    'Hết Hàng Tồn Kho': {
                        text: 'HẾT HÀNG TỒN KHO',
                        badge: 'red',
                        dot: 'red'
                    },
                    'Còn Hàng': isEbook ? {
                        text: 'EBOOK - CÓ SẴN',
                        badge: 'blue',
                        dot: 'blue'
                    } : stock == 0 ? {
                        text: 'HẾT HÀNG',
                        badge: 'red',
                        dot: 'red'
                    } : stock >= 1 && stock <= 9 ? {
                        text: 'SẮP HẾT HÀNG',
                        badge: 'yellow',
                        dot: 'yellow'
                    } : stock >= 10 ? {
                        text: 'CÒN HÀNG',
                        badge: 'green',
                        dot: 'green'
                    } : {
                        text: 'HẾT HÀNG',
                        badge: 'red',
                        dot: 'red'
                    }
                };

                return statusMap[bookStatus] || statusMap['Còn Hàng'];
            }

            // Helper function to update stock display elements
            function updateStockDisplay(stockConfig, stockTextElement, stockBadgeElement, stockDotElement) {
                if (stockTextElement) {
                    stockTextElement.textContent = stockConfig.text;
                }
                if (stockBadgeElement) {
                    const badgeClass = getStatusClasses('badge', stockConfig.badge);
                    stockBadgeElement.className = 'inline-flex items-center px-3 py-1 text-xs sm:text-sm font-semibold border adidas-font uppercase tracking-wider ' + badgeClass + ' whitespace-nowrap w-fit';
                }
                if (stockDotElement) {
                    const dotClass = getStatusClasses('dot', stockConfig.dot);
                    stockDotElement.className = 'w-2 h-2 rounded-full mr-2 ' + dotClass + ' inline-block flex-shrink-0';
                }
            }

            // Helper function to format price with Vietnamese formatting
            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN').format(price) + '₫';
            }

            // Helper function to create toggle description functionality
            function createDescriptionToggle(btnId, divId, expandedVar) {
                const btn = document.getElementById(btnId);
                const div = document.getElementById(divId);
                let isExpanded = false;

                if (btn && div) {
                    btn.addEventListener('click', function () {
                        if (isExpanded) {
                            div.innerHTML = div.dataset.short;
                            btn.textContent = 'Xem thêm';
                            btn.classList.remove('bg-amber-700');
                            btn.classList.add('bg-amber-600');
                            isExpanded = false;
                        } else {
                            div.innerHTML = div.dataset.full;
                            btn.textContent = 'Thu gọn';
                            btn.classList.remove('bg-amber-600');
                            btn.classList.add('bg-amber-700');
                            isExpanded = true;
                        }
                    });
                }
            }

            // Helper function to show toastr notifications with consistent styling
            function showToastr(type, message, title = '', options = {}) {
                if (typeof toastr !== 'undefined') {
                    const defaultOptions = {
                        timeOut: type === 'error' ? 5000 : 4000,
                        positionClass: 'toast-top-right',
                        closeButton: true,
                        progressBar: true
                    };
                    const finalOptions = { ...defaultOptions, ...options };
                    toastr[type](message, title, finalOptions);
                } else {
                    alert(message);
                }
            }

            // Helper function to setup PDF viewer controls
            function setupPDFControls() {
                const zoomInBtn = document.getElementById('zoomIn');
                const zoomOutBtn = document.getElementById('zoomOut');
                const prevPageBtn = document.getElementById('prevPage');
                const nextPageBtn = document.getElementById('nextPage');
                const fullscreenBtn = document.getElementById('fullscreenBtn');
                const downloadSampleBtn = document.getElementById('downloadSample');
                const buyNowBtn = document.getElementById('buyNowFromPreview');
                const zoomLevel = document.getElementById('zoomLevel');
                const formatSelect = document.getElementById('bookFormatSelect');
                const previewModal = document.getElementById('previewModal');

                // Zoom controls
                if (zoomInBtn) {
                    zoomInBtn.addEventListener('click', function () {
                        if (pdfDoc && scale < 3.0) {
                            scale += 0.25;
                            zoomLevel.textContent = Math.round(scale * 100) + '%';
                            queueRenderPage(pageNum);
                        }
                    });
                }

                if (zoomOutBtn) {
                    zoomOutBtn.addEventListener('click', function () {
                        if (pdfDoc && scale > 0.5) {
                            scale -= 0.25;
                            zoomLevel.textContent = Math.round(scale * 100) + '%';
                            queueRenderPage(pageNum);
                        }
                    });
                }

                // Page navigation
                if (prevPageBtn) {
                    prevPageBtn.addEventListener('click', function () {
                        if (pdfDoc && pageNum > 1) {
                            pageNum--;
                            queueRenderPage(pageNum);
                        }
                    });
                }

                if (nextPageBtn) {
                    nextPageBtn.addEventListener('click', function () {
                        if (pdfDoc && pageNum < pdfDoc.numPages) {
                            pageNum++;
                            queueRenderPage(pageNum);
                        }
                    });
                }

                // Fullscreen
                if (fullscreenBtn) {
                    fullscreenBtn.addEventListener('click', function () {
                        if (previewModal.requestFullscreen) {
                            previewModal.requestFullscreen();
                        } else if (previewModal.webkitRequestFullscreen) {
                            previewModal.webkitRequestFullscreen();
                        } else if (previewModal.msRequestFullscreen) {
                            previewModal.msRequestFullscreen();
                        }
                    });
                }

                // Download sample
                if (downloadSampleBtn) {
                    downloadSampleBtn.addEventListener('click', function () {
                        const selectedOption = formatSelect.options[formatSelect.selectedIndex];
                        const sampleUrl = selectedOption.getAttribute('data-sample-url');
                        if (sampleUrl) {
                            const link = document.createElement('a');
                            link.href = sampleUrl;
                            link.download = 'sample.pdf';
                            link.click();
                        }
                    });
                }

                // Buy now action
                if (buyNowBtn) {
                    buyNowBtn.addEventListener('click', function () {
                        previewModal.classList.add('hidden');
                        const addToCartSection = document.querySelector('.add-to-cart-section, #addToCartSection');
                        if (addToCartSection) {
                            addToCartSection.scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                }
            }

            // Helper function to setup keyboard navigation for PDF viewer
            function setupPDFKeyboardNavigation() {
                const previewModal = document.getElementById('previewModal');
                const zoomLevel = document.getElementById('zoomLevel');

                document.addEventListener('keydown', function (e) {
                    if (!previewModal.classList.contains('hidden')) {
                        switch (e.key) {
                            case 'Escape':
                                previewModal.classList.add('hidden');
                                break;
                            case 'ArrowLeft':
                                if (pdfDoc && pageNum > 1) {
                                    pageNum--;
                                    queueRenderPage(pageNum);
                                }
                                break;
                            case 'ArrowRight':
                                if (pdfDoc && pageNum < pdfDoc.numPages) {
                                    pageNum++;
                                    queueRenderPage(pageNum);
                                }
                                break;
                            case '+':
                            case '=':
                                if (pdfDoc && scale < 3.0) {
                                    scale += 0.25;
                                    zoomLevel.textContent = Math.round(scale * 100) + '%';
                                    queueRenderPage(pageNum);
                                }
                                break;
                            case '-':
                                if (pdfDoc && scale > 0.5) {
                                    scale -= 0.25;
                                    zoomLevel.textContent = Math.round(scale * 100) + '%';
                                    queueRenderPage(pageNum);
                                }
                                break;
                        }
                    }
                });
            }

            // Helper function to setup star rating interaction
            function setupStarRating(containerSelector, textSelector, ratingTexts) {
                const ratingStars = document.querySelectorAll(`${containerSelector} .star-label`);
                const ratingText = document.querySelector(textSelector);

                if (!ratingStars.length) return;

                ratingStars.forEach(star => {
                    star.addEventListener('mouseenter', function () {
                        const rating = parseInt(this.dataset.star);
                        updateStarDisplay(ratingStars, rating);
                        if (ratingText && ratingTexts[rating]) {
                            ratingText.textContent = ratingTexts[rating];
                        }
                    });

                    star.addEventListener('click', function () {
                        const rating = parseInt(this.dataset.star);
                        const inputId = containerSelector.includes('combo') ? `combo-star-${rating}` : `star-${rating}`;
                        const input = document.querySelector(`#${inputId}`);
                        if (input) {
                            input.checked = true;
                        }
                        updateStarDisplay(ratingStars, rating);
                        if (ratingText && ratingTexts[rating]) {
                            ratingText.textContent = ratingTexts[rating];
                        }
                    });
                });

                // Reset stars on container mouse leave
                const ratingContainer = document.querySelector(containerSelector);
                if (ratingContainer) {
                    ratingContainer.addEventListener('mouseleave', function () {
                        const checkedStar = document.querySelector(`${containerSelector} input[name="rating"]:checked`);
                        if (checkedStar) {
                            const rating = parseInt(checkedStar.value);
                            updateStarDisplay(ratingStars, rating);
                            if (ratingText && ratingTexts[rating]) {
                                ratingText.textContent = ratingTexts[rating];
                            }
                        }
                    });
                }

                // Initialize star display
                const checkedStar = document.querySelector(`${containerSelector} input[name="rating"]:checked`);
                if (checkedStar) {
                    updateStarDisplay(ratingStars, parseInt(checkedStar.value));
                }
            }

            // Helper function to setup quantity controls - COMMENTED OUT TO USE QUANTITY.JS VERSION
            /* COMMENTED OUT - USING QUANTITY.JS VERSION INSTEAD
            function setupQuantityControls(decrementId, incrementId, inputId, maxStock = null) {
                const decrementBtn = document.getElementById(decrementId);
                const incrementBtn = document.getElementById(incrementId);
                const quantityInput = document.getElementById(inputId);

                if (!quantityInput) return;

                // Decrement button
                if (decrementBtn) {
                    decrementBtn.addEventListener('click', function() {
                        let val = parseInt(quantityInput.value) || 1;
                        if (val > 1) {
                            quantityInput.value = val - 1;
                            quantityInput.dispatchEvent(new Event('input'));
                        }
                    });
                }

                // Increment button
                if (incrementBtn) {
                    incrementBtn.addEventListener('click', function() {
                        let val = parseInt(quantityInput.value) || 1;
                        const max = maxStock || parseInt(quantityInput.getAttribute('max')) || parseInt(quantityInput.max);
                        if (val < max) {
                            quantityInput.value = val + 1;
                            quantityInput.dispatchEvent(new Event('input'));
                        }
                    });
                }

                // Input validation
                quantityInput.addEventListener('input', function() {
                    let val = parseInt(this.value) || 0;
                    const max = maxStock || parseInt(this.getAttribute('max')) || parseInt(this.max);
                    const min = parseInt(this.getAttribute('min')) || 1;

                    if (val < min) val = min;
                    if (val > max) val = max;
                    this.value = val;
                });

                quantityInput.addEventListener('blur', function() {
                    if (!this.value || parseInt(this.value) < 1) {
                        this.value = 1;
                    }
                });
            }
            */ // END COMMENTED SETUP QUANTITY CONTROLS

            // Helper function to update star display
            function updateStarDisplay(stars, rating) {
                stars.forEach((star) => {
                    const starValue = parseInt(star.dataset.star);
                    if (starValue <= rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            }

            // Helper function to handle cart response and update cart count
            function handleCartResponse(data, isEbook = false) {
                if (data.success) {
                    const productType = isEbook ? 'sách điện tử' : 'sách vật lý';
                    showToastr('success', `Đã thêm ${productType} vào giỏ hàng thành công!`, 'Thêm thành công', { timeOut: 3000 });

                    // Update cart count
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
                    // Check for specific cart type conflicts
                    if (data.cart_type) {
                        const cartTypeMessages = {
                            'physical_books': { msg: data.error, title: 'Giỏ hàng có sách vật lý!' },
                            'ebooks': { msg: data.error, title: 'Giỏ hàng có sách điện tử!' }
                        };
                        const cartInfo = cartTypeMessages[data.cart_type];
                        if (cartInfo) {
                            showToastr('warning', cartInfo.msg, cartInfo.title, { timeOut: 6000 });
                        }
                    } else {
                        // Standard error handling
                        const errorTitles = {
                            'hết hàng': 'Hết hàng',
                            'vượt quá tồn kho': 'Vượt quá tồn kho',
                            'định dạng': 'Lỗi định dạng sách',
                            'thuộc tính': 'Lỗi thuộc tính sách',
                            'biến thể': 'Lỗi thuộc tính sách'
                        };

                        let errorTitle = 'Lỗi thêm vào giỏ hàng';
                        let timeOut = 5000;

                        for (const [keyword, title] of Object.entries(errorTitles)) {
                            if (data.error.includes(keyword)) {
                                errorTitle = title;
                                timeOut = keyword.includes('tồn kho') || keyword.includes('thuộc tính') ? 6000 : 5000;
                                break;
                            }
                        }

                        showToastr('error', data.error, errorTitle, { timeOut });
                    }
                }
            }

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
                const bookPriceElement = document.getElementById('bookPrice');

                // Kiểm tra nếu không phải trang book thì return
                if (!bookPriceElement) {
                    return;
                }

                const basePrice = parseFloat(bookPriceElement.dataset.basePrice) || 0;
                const preorderPercent = parseFloat(bookPriceElement.dataset.preorderPercent) || 0; // % đặt trước
                let finalPrice = basePrice;
                let stock = 0;
                let discount = 0;
                let isEbook = false;
                let variantStock = 0;
                let selectedVariantInfo = [];

                if (formatSelect && formatSelect.selectedOptions[0]) {
                    const selectedOption = formatSelect.selectedOptions[0];
                    finalPrice = parseFloat(selectedOption.dataset.price) || basePrice; // Giá theo định dạng
                    stock = parseInt(selectedOption.dataset.stock) || 0;
                    discount = parseFloat(selectedOption.dataset.discount) || 0; // Giảm theo định dạng (VND, cố định)
                    const selectedText = selectedOption.textContent.trim().toLowerCase();
                    isEbook = selectedText.includes('ebook');
                    console.log('updatePriceAndStock - Format selected:', selectedText, 'isEbook:', isEbook);
                }

                // Add attribute extra costs and get variant stock (only for physical books)
                const attributeSelects = document.querySelectorAll('[name^="attributes["]');
                let totalVariantStock = stock; // Start with format stock
                let lowestVariantStock = stock;
                let totalExtraPrice = 0; // Track total extra price from variants

                if (!isEbook) {
                    attributeSelects.forEach(select => {
                        if (select.selectedOptions[0] && select.value) {
                            const selectedOption = select.selectedOptions[0];
                            const extraPrice = parseFloat(selectedOption.dataset.price) || 0;
                            const attributeStock = parseInt(selectedOption.dataset.stock) || 0;
                            const attributeSku = selectedOption.dataset.sku || '';

                            // Add extra price for physical books
                            finalPrice += extraPrice;
                            totalExtraPrice += extraPrice;

                            // Use the minimum stock among variants for physical books
                            if (attributeStock >= 0) {
                                lowestVariantStock = Math.min(lowestVariantStock, attributeStock);
                                selectedVariantInfo.push({
                                    selectId: select.id,
                                    stock: attributeStock,
                                    sku: attributeSku,
                                    extraPrice: extraPrice
                                });
                            }

                            // Update variant info display for physical books
                            const attributeId = select.id.replace('attribute_', '');
                            const skuElement = document.getElementById(`selected_sku_${attributeId}`);
                            const stockElement = document.getElementById(`selected_stock_${attributeId}`);
                            const extraPriceElement = document.getElementById(`selected_extra_price_${attributeId}`);
                            const infoElement = document.getElementById(`variant_info_${attributeId}`);
                            const physicalInfoElement = document.getElementById(`physical_variant_info_${attributeId}`);
                            const ebookInfoElement = document.getElementById(`ebook_variant_info_${attributeId}`);

                            if (infoElement) {
                                infoElement.classList.remove('hidden');

                                // Show physical info, hide ebook info
                                if (physicalInfoElement) {
                                    physicalInfoElement.classList.remove('hidden');
                                }
                                if (ebookInfoElement) {
                                    ebookInfoElement.classList.add('hidden');
                                }

                                // Update SKU and stock for physical books
                                if (skuElement) {
                                    const displaySku = attributeSku || 'N/A';
                                    skuElement.textContent = displaySku;
                                }

                                if (stockElement) {
                                    stockElement.textContent = `${attributeStock}`;
                                    // Update stock color based on availability
                                    stockElement.className = getStatusClasses('stock', attributeStock > 0 ? 'green' : 'red');
                                }

                                // Update extra price display
                                if (extraPriceElement) {
                                    let displayPrice = extraPrice > 0 ? formatPrice(extraPrice) : 'Miễn phí';
                                    let displayClass = getStatusClasses('stock', extraPrice > 0 ? 'yellow' : 'green');
                                    extraPriceElement.textContent = displayPrice;
                                    extraPriceElement.className = displayClass;
                                }
                            }
                        }
                    });

                    // Use the lowest variant stock for physical books - apply hierarchical stock logic
                    if (selectedVariantInfo.length > 0) {
                        // Apply hierarchical stock: Math.min(format_stock, lowest_variant_stock)
                        stock = Math.min(stock, lowestVariantStock);
                    }
                }

                // Giá cuối cùng đã được tính sẵn từ server
                const priceAfterDiscount = finalPrice - discount;
                // Update price display
                bookPriceElement.textContent = formatPrice(priceAfterDiscount);
                const originalPriceElement = document.getElementById('originalPrice');
                const discountTextElement = document.getElementById('discountText');
                const discountAmountElement = document.getElementById('discountAmount');

                if (discount > 0) {
                    if (originalPriceElement) {
                        originalPriceElement.textContent = formatPrice(finalPrice);
                        originalPriceElement.style.display = 'inline';
                    }
                    if (discountTextElement) {
                        discountTextElement.style.display = 'inline';
                    }
                    if (discountAmountElement) {
                        const formattedDiscount = new Intl.NumberFormat('vi-VN').format(discount);
                        discountAmountElement.textContent = formattedDiscount;
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
                const stockBadgeElement = document.getElementById('stockBadge');
                const stockDotElement = document.getElementById('stockDot');
                const stockTextElement = document.getElementById('stockText');
                const stockQuantityDisplay = document.getElementById('stockQuantityDisplay');

                if (isEbook) {
                    // For eBooks - apply status priority logic too
                    const bookStatus = bookPriceElement.dataset.bookStatus || 'Còn Hàng';
                    const stockConfig = getStockStatusConfig(bookStatus, stock, true);

                    updateStockDisplay(stockConfig, stockTextElement, stockBadgeElement, stockDotElement);

                    if (stockQuantityDisplay) {
                        stockQuantityDisplay.style.display = 'none';
                    }
                    // Always hide quantity section for ebooks (regardless of status)
                    const quantitySection = document.querySelector('.quantity-section');
                    if (quantitySection) {
                        quantitySection.style.display = 'none';
                    }
                } else {
                    // For physical books - apply status priority logic
                    const bookStatus = bookPriceElement.dataset.bookStatus || 'Còn Hàng';
                    const stockConfig = getStockStatusConfig(bookStatus, stock, false);

                    updateStockDisplay(stockConfig, stockTextElement, stockBadgeElement, stockDotElement);

                    if (stockQuantityDisplay) {
                        if (stock > 0 && bookStatus === 'Còn Hàng') {
                            // Ensure the productQuantity span exists and update it
                            let productQuantitySpan = document.getElementById('productQuantity');
                            if (!productQuantitySpan) {
                                let stockText = stock;
                                // Add variant info if available
                                if (selectedVariantInfo.length > 0) {
                                    const variantSkus = selectedVariantInfo.map(v => v.sku).filter(sku => sku).join(', ');
                                    stockText = `${stock} ${variantSkus ? `(${variantSkus})` : ''}`;
                                }
                                stockQuantityDisplay.innerHTML = `(<span class="font-bold text-black" id="productQuantity">${stockText}</span> cuốn còn lại)`;
                            } else {
                                let stockText = stock;
                                if (selectedVariantInfo.length > 0) {
                                    const variantSkus = selectedVariantInfo.map(v => v.sku).filter(sku => sku).join(', ');
                                    stockText = `${stock} ${variantSkus ? `(${variantSkus})` : ''}`;
                                }
                                productQuantitySpan.innerHTML = stockText;
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

                    // Show quantity section for physical books
                    const quantitySection = document.querySelector('.quantity-section');
                    if (quantitySection) {
                        quantitySection.style.display = 'block';
                    }
                }
                // Update quantity input max value based on stock
                const quantityInput = document.getElementById('quantity');
                if (quantityInput) {
                    if (isEbook) {
                        quantityInput.value = 1;
                        quantityInput.max = '';
                        quantityInput.min = 1;
                    } else if (stock > 0) {
                        quantityInput.max = stock;
                        // Adjust current value if it exceeds new max
                        const currentValue = parseInt(quantityInput.value) || 1;
                        if (currentValue > stock) {
                            quantityInput.value = Math.min(currentValue, stock);
                        }

                        // Update min value appropriately
                        quantityInput.min = 1;

                        // Log variant stock info for debugging
                        if (selectedVariantInfo.length > 0) {
                        }
                    } else {
                        // Out of stock
                        quantityInput.max = 0;
                        quantityInput.value = 0;
                        quantityInput.min = 0;
                    }
                }

                // Update attributes summary
                const attributesSummary = document.getElementById('attributesSummary');
                const totalExtraPriceElement = document.getElementById('totalExtraPrice');
                const minStockSummary = document.getElementById('minStockSummary');
                const minStockValue = document.getElementById('minStockValue');

                // Show summary if any attributes are selected (only for physical books)
                if (!isEbook && attributeSelects.length > 0 && Array.from(attributeSelects).some(s => s.value)) {
                    if (attributesSummary) {
                        attributesSummary.classList.remove('hidden');

                        // Update total extra price
                        if (totalExtraPriceElement) {
                            let displayTotalExtra, displayTotalClass;
                            const extraPriceLabelElement = document.getElementById('extraPriceLabel');

                            // For physical books, show actual total extra price
                            displayTotalExtra = totalExtraPrice > 0
                                ? new Intl.NumberFormat('vi-VN').format(totalExtraPrice) + '₫'
                                : 'Miễn phí';
                            displayTotalClass = totalExtraPrice > 0
                                ? 'font-bold text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-sm'
                                : 'font-bold text-green-600 bg-green-100 px-2 py-1 rounded text-sm';
                            if (extraPriceLabelElement) {
                                extraPriceLabelElement.textContent = 'Tổng phí cộng thêm:';
                            }

                            totalExtraPriceElement.textContent = displayTotalExtra;
                            totalExtraPriceElement.className = displayTotalClass;
                        }

                        // Update min stock (for physical books only)
                        if (selectedVariantInfo.length > 0) {
                            if (minStockSummary) {
                                minStockSummary.classList.remove('hidden');
                            }
                            if (minStockValue) {
                                minStockValue.textContent = `${stock} cuốn`;
                                minStockValue.className = stock > 0
                                    ? 'font-bold text-green-600 bg-green-100 px-2 py-1 rounded text-sm'
                                    : 'font-bold text-red-600 bg-red-100 px-2 py-1 rounded text-sm';
                            }
                        } else {
                            if (minStockSummary) {
                                minStockSummary.classList.add('hidden');
                            }
                        }
                    }
                } else {
                    if (attributesSummary) {
                        attributesSummary.classList.add('hidden');
                    }
                }

                // Show/hide attributes based on format type
                const attributesGroup = document.getElementById('bookAttributesGroup');
                const quantitySection = document.querySelector('.quantity-section');
                const bookStatus = bookPriceElement.dataset.bookStatus || 'Còn Hàng';

                // Kiểm tra trạng thái đặc biệt
                const isSpecialStatus = bookStatus === 'Ngừng Kinh Doanh' ||
                    bookStatus === 'Sắp Ra Mắt' ||
                    bookStatus === 'Hết Hàng Tồn Kho';

                // Re-check isEbook value here to ensure consistency
                let currentIsEbook = false;
                if (formatSelect && formatSelect.selectedOptions[0]) {
                    const currentSelectedText = formatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                    currentIsEbook = currentSelectedText.includes('ebook');
                }

                if (attributesGroup) {
                    const attributeItems = attributesGroup.querySelectorAll('.attribute-item');

                    if (isSpecialStatus) {
                        // Ẩn toàn bộ phần thuộc tính
                        attributesGroup.style.display = 'none';

                        // Ẩn tất cả các dropdown chọn thuộc tính
                        document.querySelectorAll('[name^="attributes["]').forEach(select => {
                            select.style.display = 'none';
                            const parent = select.closest('.attribute-item');
                            if (parent) parent.style.display = 'none';
                        });

                        // Ẩn nhãn thuộc tính
                        const attributeLabels = document.querySelectorAll('.attribute-item label');
                        attributeLabels.forEach(label => {
                            label.style.display = 'none';
                        });

                        // Ẩn thông tin biến thể nếu có
                        document.querySelectorAll('[id^="variant_info_"]').forEach(el => {
                            el.style.display = 'none';
                        });
                    } else if (currentIsEbook) {
                        // For ebooks, always hide attributes/variants
                        attributesGroup.style.display = 'none';
                    } else {
                        attributesGroup.style.display = 'block';
                        // Update group title for physical books
                        const groupTitle = attributesGroup.querySelector('h3');
                        if (groupTitle) {
                            groupTitle.textContent = 'Tuỳ chọn sản phẩm';
                        }
                        // Update dropdown options display for physical books
                        updateAttributeOptionsDisplay(isEbook);
                    }
                }

                // Show/hide Add to Cart button and quantity controls based on product status
                const addToCartBtn = document.getElementById('addToCartBtn');

                if (addToCartBtn) {
                    // Hide button for discontinued, coming soon, or out of stock products
                    if (isSpecialStatus) {
                        addToCartBtn.style.display = 'none';
                    } else {
                        addToCartBtn.style.display = 'block';
                    }
                }

                // Show/hide quantity controls based on product status and stock
                if (quantitySection) {
                    if (isSpecialStatus) {
                        quantitySection.style.display = 'none';
                    } else if (currentIsEbook) {
                        // For ebooks, always hide quantity section
                        quantitySection.style.display = 'none';
                        console.log('updatePriceAndStock: Hidden quantity for ebook');
                    } else {
                        // For physical books that are available
                        quantitySection.style.display = 'block';
                        console.log('updatePriceAndStock: Shown quantity for physical book');
                    }
                }

                // Update preview section visibility after stock/status changes
                updatePreviewSectionVisibility();
            }

            // Function to update attribute dropdown options based on format (only for physical books)
            function updateAttributeOptionsDisplay(isEbook) {
                // Only update for physical books
                if (isEbook) return;

                const attributeSelects = document.querySelectorAll('[name^="attributes["]');
                let hiddenAttributesCount = 0;

                attributeSelects.forEach(select => {
                    const options = select.querySelectorAll('option');
                    let hasAvailableOptions = false;

                    options.forEach(option => {
                        if (option.value) { // Skip empty option
                            const originalText = option.dataset.originalText || option.textContent;
                            const extraPrice = parseFloat(option.dataset.price) || 0;
                            const variantStock = parseInt(option.dataset.stock) || 0;

                            // Store original text if not stored
                            if (!option.dataset.originalText) {
                                // Extract base text (everything before " (+")
                                const baseText = originalText.split(' (+')[0].split(' - ')[0];
                                option.dataset.originalText = baseText;
                            }

                            const baseText = option.dataset.originalText;
                            let newText = baseText;

                            // For physical books: show actual extra price and stock info
                            if (extraPrice > 0) {
                                newText += ' (+' + new Intl.NumberFormat('vi-VN').format(extraPrice) + '₫)';
                            }

                            // Add stock info with better formatting
                            if (variantStock <= 0) {
                                newText += ' - Hết hàng';
                            } else if (variantStock <= 5) {
                                newText += ' - Còn ' + variantStock + ' cuốn';
                            } else if (variantStock <= 10) {
                                newText += ' - Còn ' + variantStock + ' cuốn';
                            }

                            // Update disabled state for physical books only
                            option.disabled = variantStock === 0;

                            // Check if this option is available (has stock)
                            if (variantStock > 0) {
                                hasAvailableOptions = true;
                            }

                            option.textContent = newText;
                        }
                    });

                    // Hide/show the entire attribute group based on availability
                    const attributeItem = select.closest('.attribute-item');
                    if (attributeItem) {
                        const attributeName = select.name || 'Unknown';
                        const label = attributeItem.querySelector('label');
                        const displayName = label ? label.textContent.trim() : attributeName;

                        if (hasAvailableOptions) {
                            attributeItem.style.display = 'block';
                        } else {
                            attributeItem.style.display = 'none';
                            hiddenAttributesCount++;
                            // Reset select value if hiding this attribute
                            if (select.value) {
                                select.value = '';
                                // Trigger change event to update price calculations
                                select.dispatchEvent(new Event('change'));
                            }
                        }
                    }
                });

                // Check if any attribute groups are visible and hide the entire attributes section if none
                const attributesGroup = document.getElementById('bookAttributesGroup');
                if (attributesGroup) {
                    const allAttributeItems = attributesGroup.querySelectorAll('.attribute-item');
                    const totalAttributes = allAttributeItems.length;

                    // Count actually visible items (not hidden by display:none)
                    let visibleCount = 0;
                    allAttributeItems.forEach(item => {
                        const computedStyle = window.getComputedStyle(item);
                        if (computedStyle.display !== 'none') {
                            visibleCount++;
                        }
                    });

                    if (visibleCount === 0) {
                        attributesGroup.style.display = 'none';
                    } else {
                        attributesGroup.style.display = 'block';
                    }
                }
            }

            // Helper function to get real-time available stock based on current selections
            function getCurrentAvailableStock() {
                const formatSelect = document.getElementById('bookFormatSelect');
                const attributeSelects = document.querySelectorAll('[name^="attributes["]');

                if (!formatSelect || !formatSelect.selectedOptions[0]) {
                    return 0;
                }

                const selectedText = formatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                const isEbook = selectedText.includes('ebook');

                // For ebooks, return unlimited
                if (isEbook) {
                    return Infinity;
                }

                let formatStock = parseInt(formatSelect.selectedOptions[0].dataset.stock) || 0;
                let minVariantStock = Infinity;
                let hasSelectedVariants = false;

                // Check if any attributes are selected
                attributeSelects.forEach(select => {
                    if (select.value && select.selectedOptions[0]) {
                        hasSelectedVariants = true;
                        const variantStock = parseInt(select.selectedOptions[0].dataset.stock) || 0;
                        if (variantStock < minVariantStock) {
                            minVariantStock = variantStock;
                        }
                    }
                });

                // Apply hierarchical stock logic
                if (hasSelectedVariants && minVariantStock !== Infinity) {
                    return Math.min(formatStock, minVariantStock);
                }

                return formatStock;
            }

            // Helper function to update stock display in real-time
            function updateRealTimeStockDisplay() {
                const currentStock = getCurrentAvailableStock();
                const stockQuantityDisplay = document.getElementById('stockQuantityDisplay');
                const productQuantityElement = document.getElementById('productQuantity');
                const quantityInput = document.getElementById('quantity');

                // Update stock display
                if (stockQuantityDisplay && currentStock !== Infinity) {
                    if (currentStock > 0) {
                        stockQuantityDisplay.style.display = 'inline';
                        if (productQuantityElement) {
                            productQuantityElement.textContent = currentStock;
                        }
                    } else {
                        stockQuantityDisplay.style.display = 'none';
                    }
                }

                // Update quantity input constraints
                if (quantityInput && currentStock !== Infinity) {
                    quantityInput.max = Math.max(0, currentStock);

                    // Adjust current value if it exceeds new max
                    const currentValue = parseInt(quantityInput.value) || 1;
                    if (currentValue > currentStock) {
                        quantityInput.value = Math.max(1, Math.min(currentValue, currentStock));
                    }
                }
            }

            // Initialize variant overview interactions
            function initializeVariantOverview() {
                const variantItems = document.querySelectorAll('.variant-item:not(.out-of-stock)');

                variantItems.forEach(item => {
                    // Add click interaction to select variant
                    item.addEventListener('click', function () {
                        const variantValue = this.dataset.variantValue;
                        const attributeName = this.dataset.attributeName;
                        const stock = this.dataset.stock;
                        const sku = this.dataset.sku;

                        // Find corresponding select by matching attribute name
                        const attributeSelects = document.querySelectorAll('[name^="attributes["]');
                        attributeSelects.forEach(select => {
                            // Get the label text to match with attribute name
                            const label = select.closest('.attribute-item').querySelector('label');
                            if (label && label.textContent.trim().toLowerCase().includes(attributeName.toLowerCase())) {
                                const options = select.querySelectorAll('option');
                                options.forEach(option => {
                                    const optionText = option.textContent.trim();
                                    // Match by variant value (before any stock info)
                                    if (optionText.includes(variantValue) || optionText.startsWith(variantValue)) {
                                        select.value = option.value;
                                        // Trigger change event to update price and stock
                                        select.dispatchEvent(new Event('change'));

                                        // Add visual feedback
                                        item.style.transform = 'scale(0.95)';
                                        setTimeout(() => {
                                            item.style.transform = '';
                                        }, 150);

                                        // Show simple feedback
                                    }
                                });
                            }
                        });
                    });
                });
            }

            // Event listeners
            $(document).ready(function () {
                const formatSelect = document.getElementById('bookFormatSelect');
                if (formatSelect) {
                    formatSelect.addEventListener('change', function () {
                        console.log('formatSelect change event triggered');
                        updatePriceAndStock();
                        updateRealTimeStockDisplay(); // Update real-time stock display

                        // Hide/show variants based on format type
                        const selectedOption = formatSelect.selectedOptions[0];
                        if (selectedOption) {
                            const selectedText = selectedOption.textContent.trim().toLowerCase();
                            const isEbook = selectedText.includes('ebook');
                            const attributesGroup = document.getElementById('bookAttributesGroup');
                            const quantitySection = document.querySelector('.quantity-section');
                            
                            console.log('Format change handler:', {
                                selectedText: selectedText,
                                isEbook: isEbook,
                                attributesGroup: !!attributesGroup,
                                quantitySection: !!quantitySection
                            });
                            
                            if (attributesGroup) {
                                if (isEbook) {
                                    attributesGroup.style.display = 'none';
                                    console.log('Format change: Hidden attributes for ebook');
                                } else {
                                    attributesGroup.style.display = 'block';
                                    console.log('Format change: Shown attributes for physical book');
                                }
                            }
                            
                            if (quantitySection) {
                                if (isEbook) {
                                    quantitySection.style.display = 'none';
                                    console.log('Format change: Hidden quantity for ebook');
                                } else {
                                    quantitySection.style.display = 'block';
                                    console.log('Format change: Shown quantity for physical book');
                                }
                            }
                            
                            if (!isEbook) {
                                // Force re-check of attribute visibility based on stock
                                setTimeout(() => {
                                    updateAttributeOptionsDisplay(isEbook);
                                }, 50);
                            }
                        }
                    });
                }

                const attributeSelects = document.querySelectorAll('[name^="attributes["]');
                attributeSelects.forEach(select => {
                    select.addEventListener('change', function () {
                        updatePriceAndStock();
                        updateRealTimeStockDisplay(); // Update real-time stock display

                        // Re-check attribute visibility after any attribute change
                        const formatSelect = document.getElementById('bookFormatSelect');
                        if (formatSelect && formatSelect.selectedOptions[0]) {
                            const selectedText = formatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                            const isEbook = selectedText.includes('ebook');
                            if (!isEbook) {
                                setTimeout(() => {
                                    updateAttributeOptionsDisplay(isEbook);
                                }, 50);
                            }
                        }
                    });
                });

                // Initialize price and stock on page load
                updatePriceAndStock();

                // Initialize attribute visibility and dropdown display on page load
                const initialFormatSelect = document.getElementById('bookFormatSelect');
                if (initialFormatSelect && initialFormatSelect.selectedOptions[0]) {
                    const initialSelectedText = initialFormatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                    const initialIsEbook = initialSelectedText.includes('ebook');
                    if (!initialIsEbook) {
                        // Force check attribute visibility on page load
                        setTimeout(() => {
                            updateAttributeOptionsDisplay(initialIsEbook);
                        }, 100);
                    }
                }

                // Initialize variant overview interactions
                initializeVariantOverview();

                // Handle add to cart button
                const addToCartBtn = document.getElementById('addToCartBtn');
                if (addToCartBtn) {
                    addToCartBtn.addEventListener('click', function () {
                        addToCart();
                    });
                }

                // Toggle for book and combo descriptions using helper function
                createDescriptionToggle('showMoreBtn', 'bookDescription');
                createDescriptionToggle('showMoreComboBtn', 'comboDescription');

                // Setup quantity controls using helper function - COMMENTED OUT TO USE QUANTITY.JS
                /* COMMENTED OUT - USING QUANTITY.JS VERSION INSTEAD
                setupQuantityControls('decrementBtn', 'incrementBtn', 'quantity');
                setupQuantityControls('comboDecrementBtn', 'comboIncrementBtn', 'comboQuantity');
                */ // END COMMENTED SETUP CALLS

                // Initialize price and stock on page load
                updatePriceAndStock();
                updateRealTimeStockDisplay(); // Initialize real-time stock display

                // Check initial format and hide/show variants accordingly - with delay
                setTimeout(() => {
                    const initialFormatSelect = document.getElementById('bookFormatSelect');
                    const initialAttributesGroup = document.getElementById('bookAttributesGroup');
                    
                    console.log('Initial format check:', {
                        formatSelect: !!initialFormatSelect,
                        attributesGroup: !!initialAttributesGroup,
                        selectedOption: initialFormatSelect?.selectedOptions[0]?.textContent
                    });
                    
                    if (initialFormatSelect && initialFormatSelect.selectedOptions[0] && initialAttributesGroup) {
                        const selectedText = initialFormatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                        const isEbook = selectedText.includes('ebook');
                        
                        console.log('Initial format logic:', {
                            selectedText: selectedText,
                            isEbook: isEbook,
                            currentDisplay: initialAttributesGroup.style.display
                        });
                        
                        if (isEbook) {
                            initialAttributesGroup.style.display = 'none';
                            console.log('Hidden attributes for ebook');
                        } else {
                            initialAttributesGroup.style.display = 'block';
                            console.log('Shown attributes for physical book - initial display set to:', initialAttributesGroup.style.display);
                        }
                    }
                }, 100);

                // Enhanced Radio Button Variant Selection
                const variantRadios = document.querySelectorAll('input[name^="attributes["]');
                const attributesSummary = document.getElementById('attributesSummary');
                
                variantRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.checked) {
                            updateVariantSummary();
                            updatePriceAndStock();
                            updateRealTimeStockDisplay();
                        }
                    });
                });

                function updateVariantSummary() {
                    const selectedRadios = document.querySelectorAll('input[name^="attributes["]:checked');
                    const summaryLabel = document.getElementById('selectedVariantLabel');
                    const totalPriceElement = document.getElementById('totalExtraPrice');
                    const stockElement = document.getElementById('selectedVariantStock');
                    
                    if (selectedRadios.length > 0) {
                        // Build variant label theo format: AttributeName: ValueName | AttributeName: ValueName
                        const labelParts = [];
                        let totalExtraPrice = 0;
                        let minStock = Infinity;
                        
                        selectedRadios.forEach(radio => {
                            const label = radio.dataset.label || '';
                            const price = parseFloat(radio.dataset.price || 0);
                            const stock = parseInt(radio.dataset.stock || 0);
                            
                            if (label) labelParts.push(label);
                            totalExtraPrice += price;
                            if (stock < minStock) minStock = stock;
                        });
                        
                        // Update summary display
                        if (summaryLabel) {
                            summaryLabel.textContent = labelParts.join(' | ') || 'Đã chọn biến thể';
                        }
                        
                        if (totalPriceElement) {
                            if (totalExtraPrice > 0) {
                                totalPriceElement.textContent = '+' + new Intl.NumberFormat('vi-VN').format(totalExtraPrice) + '₫';
                                totalPriceElement.className = 'font-bold text-orange-600 text-sm';
                            } else {
                                totalPriceElement.textContent = 'Miễn phí';
                                totalPriceElement.className = 'font-bold text-green-600 text-sm';
                            }
                        }
                        
                        if (stockElement) {
                            if (minStock === Infinity) {
                                stockElement.textContent = '-';
                            } else if (minStock <= 0) {
                                stockElement.textContent = 'Hết hàng';
                                stockElement.className = 'font-bold text-red-600 text-sm';
                            } else if (minStock <= 5) {
                                stockElement.textContent = minStock + ' cuốn';
                                stockElement.className = 'font-bold text-yellow-600 text-sm';
                            } else {
                                stockElement.textContent = minStock + ' cuốn';
                                stockElement.className = 'font-bold text-green-600 text-sm';
                            }
                        }
                        
                        // Show summary
                        if (attributesSummary) {
                            attributesSummary.classList.remove('hidden');
                        }
                    } else {
                        // Hide summary if no selection
                        if (attributesSummary) {
                            attributesSummary.classList.add('hidden');
                        }
                    }
                }

                // Initialize attribute visibility on page load - Double check after DOM fully loaded
                setTimeout(() => {
                    updatePriceAndStock(); // Gọi lại để đảm bảo thuộc tính được ẩn/hiện đúng
                    updateRealTimeStockDisplay(); // Update real-time stock display again

                    // Final check for attribute visibility
                    const finalFormatSelect = document.getElementById('bookFormatSelect');
                    if (finalFormatSelect && finalFormatSelect.selectedOptions[0]) {
                        const finalSelectedText = finalFormatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                        const finalIsEbook = finalSelectedText.includes('ebook');
                        if (!finalIsEbook) {
                            updateAttributeOptionsDisplay(finalIsEbook);
                        }
                    }
                }, 200);
            });

            // Add to cart function - optimized
            function addToCart() {
                @if(!isset($combo) && isset($book))
                    @guest
                        showToastr('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 1500);
                        return;
                    @endguest

                                                                                const addToCartBtn = document.getElementById('addToCartBtn');
                    const originalText = addToCartBtn.textContent;
                    const bookId = '{{ $book->id }}';
                    const quantity = parseInt(document.getElementById('quantity')?.value) || 1;
                    const formatSelect = document.getElementById('bookFormatSelect');
                    const bookFormatId = formatSelect?.value || null;

                    let isEbook = false;
                    if (formatSelect?.selectedOptions[0]) {
                        const selectedText = formatSelect.selectedOptions[0].textContent.trim().toLowerCase();
                        isEbook = selectedText.includes('ebook');
                    }

                    const attributes = {};
                    const attributeValueIds = [];
                    const attributeSelects = document.querySelectorAll('[name^="attributes["]');

                    // Only collect attributes for physical books
                    if (!isEbook) {
                        attributeSelects.forEach(select => {
                            if (select.value) {
                                attributes[select.name] = select.value;
                                attributeValueIds.push(select.value);
                            }
                        });
                    }

                    // Frontend validation for quantity - check before sending request
                    if (!isEbook) {
                        // Get current stock information from DOM - use hierarchical stock logic
                        let currentStock = 0;
                        const formatSelect = document.getElementById('bookFormatSelect');

                        if (formatSelect && formatSelect.selectedOptions[0]) {
                            currentStock = parseInt(formatSelect.selectedOptions[0].dataset.stock) || 0;

                            // If attributes are selected, get minimum variant stock and apply hierarchical logic
                            if (attributeValueIds.length > 0) {
                                let minVariantStock = Infinity;

                                attributeSelects.forEach(select => {
                                    if (select.value && select.selectedOptions[0]) {
                                        const variantStock = parseInt(select.selectedOptions[0].dataset.stock) || 0;
                                        if (variantStock < minVariantStock) {
                                            minVariantStock = variantStock;
                                        }
                                    }
                                });

                                if (minVariantStock !== Infinity) {
                                    // Apply hierarchical stock: Math.min(format_stock, min_variant_stock)
                                    currentStock = Math.min(currentStock, minVariantStock);
                                }
                            }
                        }

                        // Check quantity against current stock
                        if (currentStock <= 0) {
                            showToastr('error', 'Sản phẩm này hiện đã hết hàng!', 'Hết hàng', { timeOut: 4000 });
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = originalText;
                            return;
                        }

                        if (quantity > currentStock) {
                            showToastr('error', `Số lượng vượt quá tồn kho! Chỉ còn ${currentStock} cuốn khả dụng.`, 'Vượt quá tồn kho');
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = originalText;
                            return;
                        }
                    }

                    // Validate based on book status (for both ebooks and physical books)
                    const bookPriceElement = document.getElementById('bookPrice');
                    const bookStatus = bookPriceElement?.dataset.bookStatus || 'Còn Hàng';

                    // Priority 1: Check books.status first (applies to both ebooks and physical books)
                    if (bookStatus === 'Ngừng Kinh Doanh' || bookStatus === 'Sắp Ra Mắt' || bookStatus === 'Hết Hàng Tồn Kho') {
                        const statusMessages = {
                            'Ngừng Kinh Doanh': { msg: 'Sản phẩm này hiện đã ngừng kinh doanh!', title: 'Ngừng kinh doanh' },
                            'Sắp Ra Mắt': { msg: 'Sản phẩm này hiện chưa ra mắt!', title: 'Sắp ra mắt' },
                            'Hết Hàng Tồn Kho': { msg: 'Sản phẩm này hiện hết hàng tồn kho!', title: 'Hết hàng tồn kho' }
                        };

                        const statusInfo = statusMessages[bookStatus];
                        if (statusInfo) {
                            showToastr('error', statusInfo.msg, statusInfo.title);
                        }

                        addToCartBtn.disabled = false;
                        addToCartBtn.textContent = originalText;
                        return;
                    }

                    // Additional stock validation for physical books only
                    if (!isEbook) {
                        // Step 1: Get format stock from book_formats table
                        const formatSelect = document.getElementById('bookFormatSelect');
                        let formatStock = 0;
                        let formatName = 'Không xác định';

                        if (formatSelect && formatSelect.selectedOptions[0]) {
                            formatStock = parseInt(formatSelect.selectedOptions[0].dataset.stock) || 0;
                            formatName = formatSelect.selectedOptions[0].textContent.trim();
                        }

                        // Step 2: Check format stock first (book_formats.stock)
                        if (bookStatus === 'Còn Hàng' && formatStock <= 0) {
                            showToastr('error', `Định dạng "${formatName}" hiện đã hết hàng!`, 'Hết hàng định dạng');
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = originalText;
                            return;
                        }

                        if (quantity > formatStock) {
                            showToastr('error', `Số lượng vượt quá tồn kho định dạng! "${formatName}" chỉ còn ${formatStock} cuốn.`, 'Vượt quá tồn kho định dạng');
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = originalText;
                            return;
                        }

                        // Step 3: Check variant stock if attributes are selected (book_attribute_values.stock)
                        let finalStock = formatStock; // Start with format stock

                        if (attributeValueIds.length > 0) {
                            let minVariantStock = Infinity;
                            let hasOutOfStockVariant = false;
                            let outOfStockVariantDetails = [];
                            let validVariants = [];

                            attributeSelects.forEach(select => {
                                if (select.value && select.selectedOptions[0]) {
                                    const variantStock = parseInt(select.selectedOptions[0].dataset.stock) || 0;
                                    const variantSku = select.selectedOptions[0].dataset.sku || '';
                                    const variantName = select.selectedOptions[0].textContent.split(' - ')[0].trim();

                                    const variantInfo = {
                                        name: variantName,
                                        sku: variantSku,
                                        stock: variantStock
                                    };

                                    if (variantStock <= 0) {
                                        hasOutOfStockVariant = true;
                                        outOfStockVariantDetails.push(variantInfo);
                                    } else {
                                        validVariants.push(variantInfo);
                                        if (variantStock < minVariantStock) {
                                            minVariantStock = variantStock;
                                        }
                                    }
                                }
                            });

                            // Check if any variant is out of stock
                            if (hasOutOfStockVariant) {
                                const outOfStockNames = outOfStockVariantDetails.map(v => v.name).join(', ');
                                showToastr('error', `Các thuộc tính sau đã hết hàng: ${outOfStockNames}. Vui lòng chọn thuộc tính khác!`, 'Hết hàng thuộc tính', { timeOut: 6000 });
                                addToCartBtn.disabled = false;
                                addToCartBtn.textContent = originalText;
                                return;
                            }

                            // Apply hierarchical stock: Math.min(format_stock, min_variant_stock)
                            if (minVariantStock !== Infinity) {
                                finalStock = Math.min(formatStock, minVariantStock);

                                // Check if quantity exceeds variant stock
                                if (quantity > minVariantStock) {
                                    const limitingVariant = validVariants.find(v => v.stock === minVariantStock);
                                    showToastr('error', `Số lượng vượt quá tồn kho thuộc tính! "${limitingVariant?.name || 'Không xác định'}" chỉ còn ${minVariantStock} cuốn${limitingVariant?.sku ? ` (SKU: ${limitingVariant.sku})` : ''}.`, 'Vượt quá tồn kho thuộc tính', { timeOut: 6000 });
                                    addToCartBtn.disabled = false;
                                    addToCartBtn.textContent = originalText;
                                    return;
                                }
                            }
                        }

                        // Final validation with the lowest stock
                        if (bookStatus === 'Còn Hàng' && finalStock <= 0) {
                            showToastr('error', 'Sản phẩm này hiện đã hết hàng!', 'Hết hàng', { timeOut: 4000 });
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = originalText;
                            return;
                        }

                        if (quantity > finalStock) {
                            showToastr('error', `Số lượng vượt quá tồn kho! Chỉ còn ${finalStock} cuốn khả dụng.`, 'Vượt quá tồn kho');
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = originalText;
                            return;
                        }
                    }

                    // Disable button and show loading
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Đang thêm...';

                    // Debug request data
                    const requestData = {
                        book_id: bookId,
                        quantity: quantity,
                        book_format_id: bookFormatId
                    };

                    // Only add attributes for physical books
                    if (!isEbook) {
                        requestData.attribute_value_ids = JSON.stringify(attributeValueIds);
                        requestData.attributes = attributes;
                    }

                    // Send request
                    fetch('{{ route("cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => {
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                return response.text().then(text => {
                                    throw new Error('Server returned non-JSON response');
                                });
                            }

                            // Parse JSON for both success and error responses
                            return response.json().then(data => {
                                if (!response.ok) {
                                    // For 422 and other HTTP errors, we want to handle the error data
                                    console.error('HTTP Error:', response.status, response.statusText, data);
                                    return { error: data.error || data.message || `HTTP ${response.status}: ${response.statusText}`, httpStatus: response.status };
                                }
                                return data;
                            });
                        })
                        .then(data => {
                            // Check if this is an HTTP error response
                            if (data.httpStatus && data.httpStatus !== 200) {
                                // Handle HTTP error responses (like 422)
                                handleCartResponse(data);
                                return; // Exit early for error responses
                            }

                            // Handle success/error responses
                            handleCartResponse(data, isEbook);
                        })
                        .catch(error => {
                            console.error('Fetch Error Details:', error);

                            // Error message mapping for better user experience
                            const errorMap = {
                                'HTTP error! status: 422': { msg: 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại thông tin!', title: 'Dữ liệu không hợp lệ' },
                                'HTTP error! status: 500': { msg: 'Lỗi server nội bộ. Vui lòng thử lại sau!', title: 'Lỗi server' },
                                'HTTP error! status: 419': { msg: 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang!', title: 'Phiên hết hạn' },
                                'non-JSON response': { msg: 'Server trả về dữ liệu không hợp lệ. Vui lòng thử lại!', title: 'Lỗi dữ liệu' },
                                'NetworkError': { msg: 'Lỗi mạng. Vui lòng kiểm tra kết nối internet và thử lại!', title: 'Lỗi mạng' },
                                'Failed to fetch': { msg: 'Lỗi mạng. Vui lòng kiểm tra kết nối internet và thử lại!', title: 'Lỗi mạng' }
                            };

                            let errorInfo = { msg: 'Có lỗi xảy ra khi thêm vào giỏ hàng', title: 'Lỗi thêm vào giỏ hàng' };

                            // Find matching error type
                            for (const [key, value] of Object.entries(errorMap)) {
                                if (error.message && error.message.includes(key)) {
                                    errorInfo = value;
                                    break;
                                }
                            }

                            // Fallback for HTTP errors
                            if (error.message && error.message.includes('HTTP error') && errorInfo.title === 'Lỗi thêm vào giỏ hàng') {
                                errorInfo = { msg: 'Lỗi kết nối server. Vui lòng thử lại sau!', title: 'Lỗi kết nối' };
                            }

                            showToastr('error', errorInfo.msg, errorInfo.title, { timeOut: 6000 });
                        })
                        .finally(() => {
                            // Restore button
                            addToCartBtn.disabled = false;
                            addToCartBtn.textContent = originalText;
                        });
                @else
                    // This is combo page, addToCart function should not be called
                    console.warn('addToCart function called on combo page');
                    showToastr('warning', 'Chức năng này chỉ khả dụng trên trang sách đơn');
                @endif
                                                                    }

            // Add related product to cart function - optimized  
            function addRelatedToCart(bookId) {
                @guest
                    showToastr('warning', 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng', 'Chưa đăng nhập!', { timeOut: 3000 });
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 1500);
                    return;
                @endguest

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
                        quantity: 1,
                        attribute_value_ids: JSON.stringify([]),
                        attributes: {}
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            return response.text().then(text => {
                                throw new Error('Server returned non-JSON response');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Use helper function to handle response
                        handleCartResponse(data);

                        // Show additional tip if successful
                        if (data.success) {
                            setTimeout(() => {
                                showToastr('info', 'Xem giỏ hàng của bạn', 'Tip', {
                                    timeOut: 2000,
                                    onclick: function () {
                                        window.location.href = '{{ route("cart.index") }}';
                                    }
                                });
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Use error mapping like in addToCart
                        const errorMap = {
                            'HTTP error': 'Lỗi kết nối server. Vui lòng thử lại sau!',
                            'non-JSON response': 'Server trả về dữ liệu không hợp lệ. Vui lòng thử lại!',
                            'NetworkError': 'Lỗi mạng. Vui lòng kiểm tra kết nối internet và thử lại!',
                            'Failed to fetch': 'Lỗi mạng. Vui lòng kiểm tra kết nối internet và thử lại!'
                        };

                        let errorMessage = 'Có lỗi xảy ra khi thêm sách liên quan vào giỏ hàng';
                        for (const [key, value] of Object.entries(errorMap)) {
                            if (error.message && error.message.includes(key)) {
                                errorMessage = value;
                                break;
                            }
                        }

                        showToastr('error', errorMessage, 'Lỗi thêm sách liên quan', { timeOut: 6000 });
                    })
                    .finally(() => {
                        // Restore button
                        button.disabled = false;
                        button.innerHTML = originalText;
                    });
            }

            // Helper function to check and update preview section visibility
            function updatePreviewSectionVisibility() {
                const bookFormatSelectElement = document.getElementById('bookFormatSelect');
                const previewSection = document.getElementById('previewSection');

                if (!bookFormatSelectElement || !previewSection) return;

                const selectedOption = bookFormatSelectElement.options[bookFormatSelectElement.selectedIndex];
                if (!selectedOption) return;

                const formatName = selectedOption.text.toLowerCase();

                // Kiểm tra xem có phải ebook không
                if (formatName.includes('ebook')) {
                    // Kiểm tra trạng thái sản phẩm và stock để quyết định hiển thị nút đọc thử
                    const bookPriceElement = document.getElementById('bookPrice');
                    const bookStatus = bookPriceElement?.dataset.bookStatus || 'Còn Hàng';
                    const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

                    // Ẩn nút đọc thử nếu sản phẩm có trạng thái không khả dụng
                    const isUnavailable =
                        bookStatus === 'Ngừng Kinh Doanh' ||
                        bookStatus === 'Sắp Ra Mắt' ||
                        bookStatus === 'Hết Hàng Tồn Kho' ||
                        stock === -1 || // Sắp ra mắt  
                        stock === -2;   // Ngừng kinh doanh

                    if (isUnavailable) {
                        previewSection.classList.add('hidden');
                    } else {
                        previewSection.classList.remove('hidden');
                    }
                } else {
                    previewSection.classList.add('hidden');
                }
            }

            // Xử lý hiển thị nút đọc thử cho ebook
            const bookFormatSelectElement = document.getElementById('bookFormatSelect');
            if (bookFormatSelectElement) {
                bookFormatSelectElement.addEventListener('change', function () {
                    updatePreviewSectionVisibility();
                });

                // Kiểm tra trạng thái nút đọc thử khi trang load lần đầu
                updatePreviewSectionVisibility();
            }

            // Enhanced PDF Preview Modal with Modern Features
            const previewBtn = document.querySelector('#previewBtn');
            const previewModal = document.getElementById('previewModal');
            const closePreviewModal = document.getElementById('closePreviewModal');
            const previewContent = document.getElementById('previewContent');
            const previewLimitNotice = document.getElementById('previewLimitNotice');
            const previewIframe = document.getElementById('previewIframe');
            const formatSelect = document.getElementById('bookFormatSelect');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const pdfViewerContainer = document.getElementById('pdfViewerContainer');
            const pdfCanvas = document.getElementById('pdfCanvas');

            // PDF.js variables
            let pdfDoc = null;
            let pageNum = 1;
            let pageRendering = false;
            let pageNumPending = null;
            let scale = 1.0;
            let canvas = null;
            let ctx = null;

            // PDF Controls
            const zoomInBtn = document.getElementById('zoomIn');
            const zoomOutBtn = document.getElementById('zoomOut');
            const zoomLevel = document.getElementById('zoomLevel');
            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const pageInfo = document.getElementById('pageInfo');
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const downloadSampleBtn = document.getElementById('downloadSample');
            const buyNowBtn = document.getElementById('buyNowFromPreview');

            // Initialize PDF viewer
            function initPDFViewer() {
                if (!canvas) {
                    canvas = document.createElement('canvas');
                    ctx = canvas.getContext('2d');
                    pdfCanvas.appendChild(canvas);
                }
            }

            // Render a page
            function renderPage(num) {
                pageRendering = true;

                pdfDoc.getPage(num).then(function (page) {
                    const viewport = page.getViewport({ scale: scale });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    const renderTask = page.render(renderContext);

                    renderTask.promise.then(function () {
                        pageRendering = false;
                        if (pageNumPending !== null) {
                            renderPage(pageNumPending);
                            pageNumPending = null;
                        }

                        // Update page info
                        pageInfo.textContent = `${num} / ${pdfDoc.numPages}`;

                        // Update navigation buttons
                        prevPageBtn.disabled = (num <= 1);
                        nextPageBtn.disabled = (num >= pdfDoc.numPages);

                        // Show limit notice after a few pages
                        if (num >= 3) {
                            previewLimitNotice.classList.remove('hidden');
                        }
                    });
                });
            }

            // Queue page rendering
            function queueRenderPage(num) {
                if (pageRendering) {
                    pageNumPending = num;
                } else {
                    renderPage(num);
                }
            }

            // Load PDF document
            function loadPDF(url) {
                loadingSpinner.classList.remove('hidden');
                pdfViewerContainer.classList.add('hidden');
                previewIframe.classList.add('hidden');

                // Try to load with PDF.js first
                if (typeof pdfjsLib !== 'undefined') {
                    pdfjsLib.getDocument(url).promise.then(function (pdf) {
                        pdfDoc = pdf;
                        pageNum = 1;

                        initPDFViewer();
                        renderPage(pageNum);

                        loadingSpinner.classList.add('hidden');
                        pdfViewerContainer.classList.remove('hidden');

                        // Update zoom level display
                        zoomLevel.textContent = Math.round(scale * 100) + '%';

                    }).catch(function (error) {
                        fallbackToIframe(url);
                    });
                } else {
                    // Fallback to iframe if PDF.js is not available
                    fallbackToIframe(url);
                }
            }

            // Fallback to iframe
            function fallbackToIframe(url) {
                previewIframe.src = url;
                loadingSpinner.classList.add('hidden');
                pdfViewerContainer.classList.add('hidden');
                previewIframe.classList.remove('hidden');

                // Hide PDF controls for iframe mode
                document.querySelectorAll('.bg-white\/10').forEach(el => el.style.display = 'none');
            }

            if (previewBtn && previewModal && closePreviewModal && formatSelect) {
                // Open preview modal
                previewBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const selectedOption = formatSelect.options[formatSelect.selectedIndex];
                    const sampleUrl = selectedOption.getAttribute('data-sample-url');
                    const allowSample = selectedOption.getAttribute('data-allow-sample') === '1';

                    if (allowSample && sampleUrl) {
                        previewModal.classList.remove('hidden');
                        previewLimitNotice.classList.add('hidden');
                        loadPDF(sampleUrl);
                    } else {
                        showToastr('warning', 'Không có file đọc thử cho định dạng này!');
                    }
                });

                // Close modal
                closePreviewModal.addEventListener('click', function () {
                    previewModal.classList.add('hidden');
                    previewIframe.src = '';
                    if (pdfDoc) {
                        pdfDoc = null;
                        pageNum = 1;
                        scale = 1.0;
                    }
                });

                // Close on backdrop click
                previewModal.addEventListener('click', function (e) {
                    if (e.target === previewModal) {
                        previewModal.classList.add('hidden');
                        previewIframe.src = '';
                        if (pdfDoc) {
                            pdfDoc = null;
                            pageNum = 1;
                            scale = 1.0;
                        }
                    }
                });

                // Setup PDF controls using helper function
                setupPDFControls();

                // Setup keyboard navigation using helper function
                setupPDFKeyboardNavigation();
            }

            // Setup star rating for combo review using helper function
            const ratingTexts = {
                5: 'Tuyệt vời',
                4: 'Tốt',
                3: 'Bình thường',
                2: 'Không tốt',
                1: 'Rất tệ'
            };
            setupStarRating('.rating-stars', '.rating-text', ratingTexts);

            const comboForm = document.querySelector('form[action="{{ route("cart.add") }}"]');
            if (comboForm) {
                comboForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    @guest
                        showToastr('error', 'Vui lòng đăng nhập để thêm combo vào giỏ hàng!');
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 1500);
                        return;
                    @endguest

                                                                    const formData = new FormData(comboForm);
                    const urlParams = new URLSearchParams();

                    // Convert FormData to URLSearchParams
                    for (let pair of formData.entries()) {
                        urlParams.append(pair[0], pair[1]);
                    }

                    const submitBtn = comboForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Check CSRF token
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

                    if (!csrfToken) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Lỗi bảo mật: Không tìm thấy CSRF token. Vui lòng tải lại trang!', 'Lỗi!');
                        } else {
                            alert('Lỗi bảo mật: Không tìm thấy CSRF token. Vui lòng tải lại trang!');
                        }
                        return;
                    }

                    // Disable button and show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i><span>Đang thêm...</span>';

                    // Use fetch with URLSearchParams body (simpler debugging)
                    fetch('{{ route("cart.add") }}', {
                        method: 'POST',
                        body: urlParams,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                        .then(response => {
                            // Check if response is OK
                            if (!response.ok) {
                                // For non-200 responses, get the text to see what's wrong
                                return response.text().then(text => {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                });
                            }

                            // Check content type
                            const contentType = response.headers.get('content-type');

                            if (!contentType || !contentType.includes('application/json')) {
                                return response.text().then(text => {
                                    // Try to extract Laravel error information
                                    if (text.includes('validation') || text.includes('ValidationException')) {
                                        throw new Error('Validation Error: Dữ liệu gửi lên không hợp lệ');
                                    } else if (text.includes('500 | Server Error') || text.includes('ErrorException')) {
                                        throw new Error('Server Error: Lỗi server nội bộ');
                                    } else if (text.includes('419 | Page Expired') || text.includes('CSRF')) {
                                        throw new Error('CSRF Error: Token đã hết hạn, vui lòng tải lại trang');
                                    } else {
                                        throw new Error('Server trả về HTML thay vì JSON');
                                    }
                                });
                            }

                            return response.json();
                        })
                        .then(data => {
                            // Use helper function to handle combo response
                            handleCartResponse(data);

                            // Show additional tip if successful
                            if (data.success) {
                                setTimeout(() => {
                                    showToastr('info', 'Xem giỏ hàng của bạn', 'Tip', {
                                        timeOut: 2000,
                                        onclick: function () {
                                            window.location.href = '{{ route("cart.index") }}';
                                        }
                                    });
                                }, 1000);
                            }
                        })
                        .catch(error => {
                            console.error('Combo form submission error:', error);

                            // Error mapping for combo form
                            const comboErrorMap = {
                                'non-JSON response': 'Lỗi server: Server trả về HTML thay vì JSON. Có thể có lỗi validation hoặc server error.',
                                'HTTP error! status: 422': 'Lỗi validation: Dữ liệu gửi lên không hợp lệ',
                                'HTTP error! status: 500': 'Lỗi server nội bộ: Vui lòng thử lại sau',
                                'HTTP error': 'Lỗi kết nối server'
                            };

                            let errorMessage = 'Có lỗi xảy ra khi thêm combo vào giỏ hàng';
                            for (const [key, value] of Object.entries(comboErrorMap)) {
                                if (error.message && error.message.includes(key)) {
                                    errorMessage = value;
                                    break;
                                }
                            }

                            showToastr('error', errorMessage, 'Lỗi!', { timeOut: 5000 });
                        })
                        .finally(() => {
                            // Re-enable button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                });
            }

            // Wishlist functionality for book page
            const wishlistBtn = document.getElementById('wishlistBtn');
            if (wishlistBtn) {
                wishlistBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    @guest
                        showToastr('warning', 'Bạn cần đăng nhập để thêm sản phẩm vào danh sách yêu thích', 'Chưa đăng nhập!', { timeOut: 3000 });
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 1500);
                        return;
                    @endguest

                                                            if (this.disabled) return;

                    const button = this;
                    const bookId = button.dataset.bookId;
                    const originalHTML = button.innerHTML;

                    // Visual feedback
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i><span>ĐANG THÊM...</span>';

                    fetch('{{ route("wishlist.add") }}', {
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
                                button.classList.remove('border-black', 'text-black');
                                button.classList.add('bg-red-500', 'text-white', 'border-red-500');
                                button.innerHTML = '<i class="fas fa-heart mr-3"></i><span>ĐÃ YÊU THÍCH</span>';
                                showToastr('success', 'Đã thêm vào danh sách yêu thích!', 'Thành công', { timeOut: 3000 });

                                // Dispatch wishlist update event
                                if (typeof data.wishlist_count !== 'undefined') {
                                    document.dispatchEvent(new CustomEvent('wishlistItemAdded', {
                                        detail: { count: data.wishlist_count }
                                    }));
                                } else {
                                    // Fallback: refresh wishlist count from server
                                    if (window.WishlistCountManager && typeof window.WishlistCountManager.refreshFromServer === 'function') {
                                        window.WishlistCountManager.refreshFromServer();
                                    }
                                }

                                button.disabled = false;
                            } else {
                                button.innerHTML = originalHTML;
                                button.disabled = false;
                                showToastr('warning', data.message || 'Lỗi khi thêm vào danh sách yêu thích!', 'Thông báo', { timeOut: 4000 });
                            }
                        })
                        .catch(error => {
                            console.error('Wishlist error:', error);
                            button.innerHTML = originalHTML;
                            button.disabled = false;
                            showToastr('error', 'Lỗi kết nối! Vui lòng thử lại.', 'Lỗi mạng', { timeOut: 5000 });
                        });
                });
            }

        </script>
        <!-- Enhanced Review Image Modal -->
        <div id="reviewImageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden backdrop-blur-sm">
            <div class="relative max-w-4xl max-h-[85vh] p-6 w-full flex items-center justify-center">
                <!-- Close Button -->
                <button onclick="closeReviewImageModal()"
                    class="absolute top-4 right-4 text-white hover:text-gray-300 text-3xl z-20 bg-black bg-opacity-50 rounded-full w-12 h-12 flex items-center justify-center transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
                
                <!-- Loading Spinner -->
                <div id="imageLoadingSpinner" class="absolute inset-0 flex items-center justify-center z-10">
                    <div class="flex flex-col items-center text-white">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
                        <p class="text-sm">Đang tải ảnh...</p>
                    </div>
                </div>
                
                <!-- Error Message -->
                <div id="imageErrorMessage" class="absolute inset-0 flex items-center justify-center z-10 hidden">
                    <div class="flex flex-col items-center text-white text-center">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4 text-yellow-400"></i>
                        <p class="text-lg mb-2">Không thể tải ảnh</p>
                        <p class="text-sm text-gray-300">Vui lòng thử lại sau</p>
                        <button onclick="retryLoadImage()" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white transition-colors">
                            <i class="fas fa-redo mr-2"></i>Thử lại
                        </button>
                    </div>
                </div>
                
                <!-- Main Image -->
                <img id="reviewModalImage" src="" alt="Review Image" 
                    class="max-w-[90%] max-h-[70vh] object-contain rounded-lg shadow-2xl hidden"
                    onload="handleImageLoad()" 
                    onerror="handleImageError()">
            </div>
        </div>

        <script>
            let currentImageSrc = '';
            
            function showReviewImageModal(imageSrc) {
                const modal = document.getElementById('reviewImageModal');
                const modalImage = document.getElementById('reviewModalImage');
                const loadingSpinner = document.getElementById('imageLoadingSpinner');
                const errorMessage = document.getElementById('imageErrorMessage');
                
                // Store current image source
                currentImageSrc = imageSrc;
                
                // Reset states
                modalImage.classList.add('hidden');
                loadingSpinner.classList.remove('hidden');
                errorMessage.classList.add('hidden');
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Load image
                modalImage.src = imageSrc;
                
                // Add body scroll lock
                document.body.style.overflow = 'hidden';
            }
            
            function handleImageLoad() {
                const modalImage = document.getElementById('reviewModalImage');
                const loadingSpinner = document.getElementById('imageLoadingSpinner');
                const errorMessage = document.getElementById('imageErrorMessage');
                
                // Hide loading, show image
                loadingSpinner.classList.add('hidden');
                errorMessage.classList.add('hidden');
                modalImage.classList.remove('hidden');
            }
            
            function handleImageError() {
                const modalImage = document.getElementById('reviewModalImage');
                const loadingSpinner = document.getElementById('imageLoadingSpinner');
                const errorMessage = document.getElementById('imageErrorMessage');
                
                // Hide loading and image, show error
                loadingSpinner.classList.add('hidden');
                modalImage.classList.add('hidden');
                errorMessage.classList.remove('hidden');
                
                console.error('Failed to load image:', currentImageSrc);
            }
            
            function retryLoadImage() {
                if (currentImageSrc) {
                    const modalImage = document.getElementById('reviewModalImage');
                    const loadingSpinner = document.getElementById('imageLoadingSpinner');
                    const errorMessage = document.getElementById('imageErrorMessage');
                    
                    // Reset states
                    modalImage.classList.add('hidden');
                    errorMessage.classList.add('hidden');
                    loadingSpinner.classList.remove('hidden');
                    
                    // Try to load image again with cache busting
                    const cacheBuster = '?t=' + new Date().getTime();
                    modalImage.src = currentImageSrc + cacheBuster;
                }
            }

            function closeReviewImageModal() {
                const modal = document.getElementById('reviewImageModal');
                const modalImage = document.getElementById('reviewModalImage');
                
                modal.classList.add('hidden');
                modalImage.src = ''; // Clear source to stop loading
                currentImageSrc = '';
                
                // Remove body scroll lock
                document.body.style.overflow = '';
            }

            // Close modal when clicking outside the image
            document.getElementById('reviewImageModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeReviewImageModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeReviewImageModal();
                }
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Enhanced Review Styles */
            .review-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border-radius: 12px;
                overflow: hidden;
                background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            }

            .review-card:hover {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                transform: translateY(-4px) scale(1.02);
            }

            .review-card .bg-black {
                background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
                position: relative;
            }

            .review-card .bg-black::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
                animation: shimmer 3s infinite;
            }

            @keyframes shimmer {
                0% {
                    transform: translateX(-100%);
                }

                100% {
                    transform: translateX(100%);
                }
            }

            /* Admin Response Animation */
            .admin-response {
                animation: slideInFromLeft 0.6s ease-out;
                background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 50%, #dbeafe 100%);
                border-left: 4px solid #3b82f6;
                box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
            }

            @keyframes slideInFromLeft {
                0% {
                    transform: translateX(-30px);
                    opacity: 0;
                }

                100% {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            /* Star Rating Enhancement */
            .star-rating {
                filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.3));
                animation: starGlow 2s ease-in-out infinite alternate;
            }

            @keyframes starGlow {
                0% {
                    filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.3));
                }

                100% {
                    filter: drop-shadow(0 4px 8px rgba(251, 191, 36, 0.5));
                }
            }

            /* Image Gallery Enhancement */
            .review-image {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 8px;
                overflow: hidden;
                position: relative;
            }

            .review-image::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.2) 50%, transparent 70%);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .review-image:hover {
                transform: scale(1.08) rotate(1deg);
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            }

            .review-image:hover::before {
                opacity: 1;
                animation: imageShimmer 0.6s ease-out;
            }

            @keyframes imageShimmer {
                0% {
                    transform: translateX(-100%);
                }

                100% {
                    transform: translateX(100%);
                }
            }

            /* Enhanced Typography */
            .review-comment {
                line-height: 1.8;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                position: relative;
            }

            .review-comment::before {
                content: '"';
                position: absolute;
                left: -20px;
                top: -10px;
                font-size: 3rem;
                color: #e5e7eb;
                font-family: serif;
            }

            /* Product Info Enhancement */
            .product-info {
                background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
                border-left: 4px solid #111827;
                position: relative;
                overflow: hidden;
            }

            .product-info::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 2px;
                background: linear-gradient(90deg, #111827 0%, #6b7280 50%, #111827 100%);
            }

            /* Responsive Improvements */
            @media (max-width: 768px) {
                .review-card {
                    margin-bottom: 1.5rem;
                    border-radius: 8px;
                }

                .review-card .p-6 {
                    padding: 1.25rem;
                }

                .review-card:hover {
                    transform: translateY(-2px) scale(1.01);
                }
            }

            /* Loading Animation for Images */
            .review-image img {
                transition: opacity 0.3s ease;
            }

            .review-image img:not([src]) {
                opacity: 0;
            }

            /* Enhanced Verified Badge */
            .verified-badge {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                animation: pulse 2s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.8;
                }
            }

            /* Enhanced Description Section Styles */
            .description-section {
                position: relative;
                overflow: hidden;
                border-radius: 16px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .description-section:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
            }

            .description-header {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #f59e0b 100%);
                position: relative;
                overflow: hidden;
            }

            .description-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.2) 50%, transparent 100%);
                animation: headerShine 3s infinite;
            }

            @keyframes headerShine {
                0% {
                    left: -100%;
                }
                100% {
                    left: 100%;
                }
            }

            .description-content {
                position: relative;
                background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            }

            .description-gradient-line {
                height: 3px;
                background: linear-gradient(90deg, #f59e0b 0%, #d97706 50%, #f59e0b 100%);
                animation: gradientFlow 2s ease-in-out infinite alternate;
            }

            @keyframes gradientFlow {
                0% {
                    background: linear-gradient(90deg, #f59e0b 0%, #d97706 50%, #f59e0b 100%);
                }
                100% {
                    background: linear-gradient(90deg, #d97706 0%, #f59e0b 50%, #d97706 100%);
                }
            }

            .description-empty-state {
                position: relative;
                background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%);
                border: 2px dashed #d1d5db;
                border-radius: 12px;
                transition: all 0.3s ease;
            }

            .description-empty-state:hover {
                border-color: #f59e0b;
                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                transform: scale(1.02);
            }

            .description-toggle-btn {
                position: relative;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            }

            .description-toggle-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
                background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            }

            .description-toggle-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
                transition: left 0.5s ease;
            }

            .description-toggle-btn:hover::before {
                left: 100%;
            }

            /* Enhanced Gift Section Styles */
            .gift-section {
                position: relative;
                overflow: hidden;
                border-radius: 16px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .gift-section:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
            }

            .gift-header {
                background: linear-gradient(135deg, #10b981 0%, #059669 50%, #10b981 100%);
                position: relative;
                overflow: hidden;
            }

            .gift-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.2) 50%, transparent 100%);
                animation: giftHeaderShine 4s infinite;
            }

            @keyframes giftHeaderShine {
                0% {
                    left: -100%;
                }
                100% {
                    left: 100%;
                }
            }

            .gift-card {
                position: relative;
                background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
                border-radius: 12px;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border: 2px solid transparent;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            .gift-card:hover {
                transform: translateY(-4px) scale(1.02);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                border-color: #10b981;
            }

            .gift-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, transparent 30%, rgba(16, 185, 129, 0.1) 50%, transparent 70%);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .gift-card:hover::before {
                opacity: 1;
                animation: cardShimmer 0.6s ease-out;
            }

            @keyframes cardShimmer {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(100%);
                }
            }

            .gift-image-container {
                position: relative;
                overflow: hidden;
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .gift-image-container:hover {
                transform: scale(1.05);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            }

            .gift-status-badge {
                position: relative;
                overflow: hidden;
                animation: badgePulse 2s infinite;
            }

            .gift-status-available {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            }

            .gift-status-unavailable {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            }

            @keyframes badgePulse {
                0%, 100% {
                    transform: scale(1);
                    opacity: 1;
                }
                50% {
                    transform: scale(1.05);
                    opacity: 0.9;
                }
            }

            .gift-date-display {
                background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
                border-left: 4px solid #6366f1;
                position: relative;
                overflow: hidden;
            }

            .gift-date-display::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 2px;
                background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #6366f1 100%);
                animation: dateGlow 2s ease-in-out infinite alternate;
            }

            @keyframes dateGlow {
                0% {
                    opacity: 0.6;
                }
                100% {
                    opacity: 1;
                }
            }

            /* Enhanced Button Animations */
            .adidas-btn-enhanced {
                position: relative;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            }

            .adidas-btn-enhanced::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.2) 50%, transparent 100%);
                transition: left 0.5s ease;
            }

            .adidas-btn-enhanced:hover::before {
                left: 100%;
            }

            .adidas-btn-enhanced:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            }

            /* Loading States */
            .gift-loading {
                background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
                background-size: 200% 100%;
                animation: loadingShimmer 1.5s infinite;
            }

            @keyframes loadingShimmer {
                0% {
                    background-position: 200% 0;
                }
                100% {
                    background-position: -200% 0;
                }
            }

            /* Mobile Responsive Enhancements */
            @media (max-width: 768px) {
                .description-section,
                .gift-section {
                    border-radius: 12px;
                    margin-bottom: 1.5rem;
                }

                .gift-card {
                    margin-bottom: 1rem;
                }

                .gift-card:hover {
                    transform: translateY(-2px) scale(1.01);
                }

                .description-toggle-btn,
                .adidas-btn-enhanced {
                    padding: 0.75rem 1.5rem;
                    font-size: 0.875rem;
                }
            }

            /* High Performance Animations */
            @media (prefers-reduced-motion: reduce) {
                .description-section,
                .gift-section,
                .gift-card,
                .description-toggle-btn,
                .adidas-btn-enhanced {
                    animation: none;
                    transition: none;
                }

                .description-section:hover,
                .gift-section:hover,
                .gift-card:hover {
                    transform: none;
                }
            }
        </style>
    @endpush

    @push('styles')
    <style>
        .variant-option-card {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .variant-option-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .variant-option-card input[type="radio"]:checked + div {
            background-color: #fed7aa;
        }
        
        .variant-option-card:has(input[type="radio"]:checked) {
            border-color: #ea580c;
            background-color: #fff7ed;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle variant selection
            const variantRadios = document.querySelectorAll('input[type="radio"][name^="attributes"]');
            const summaryDiv = document.getElementById('attributesSummary');
            const selectedLabel = document.getElementById('selectedVariantLabel');
            const totalExtraPrice = document.getElementById('totalExtraPrice');
            const selectedStock = document.getElementById('selectedVariantStock');

            if (variantRadios.length > 0) {
                variantRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        updateVariantSummary();
                    });
                });
            }

            function updateVariantSummary() {
                const selectedVariants = [];
                let totalExtra = 0;
                let minStock = Infinity;

                variantRadios.forEach(radio => {
                    if (radio.checked) {
                        const label = radio.getAttribute('data-label');
                        const price = parseFloat(radio.getAttribute('data-price')) || 0;
                        const stock = parseInt(radio.getAttribute('data-stock')) || 0;
                        
                        selectedVariants.push(label);
                        totalExtra += price;
                        minStock = Math.min(minStock, stock);
                    }
                });

                if (selectedVariants.length > 0) {
                    summaryDiv.classList.remove('hidden');
                    selectedLabel.textContent = selectedVariants.join(' | ');
                    totalExtraPrice.textContent = new Intl.NumberFormat('vi-VN').format(totalExtra) + '₫';
                    selectedStock.textContent = minStock === Infinity ? '-' : minStock;
                } else {
                    summaryDiv.classList.add('hidden');
                }
            }
        });
    </script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formatSelect = document.getElementById('bookFormatSelect');
    const attributesGroup = document.getElementById('bookAttributesGroup');
    // Số lượng
    const quantitySection = document.querySelector('.quantity-section');
    if (formatSelect && attributesGroup) {
        function updateAttributesGroupDisplay() {
            const selectedOption = formatSelect.options[formatSelect.selectedIndex];
            const isEbook = selectedOption && selectedOption.text.toLowerCase().includes('ebook');
            // Chỉ ẩn biến thể khi là ebook, còn lại luôn hiện
            attributesGroup.style.display = isEbook ? 'none' : 'block';
            // Số lượng: LUÔN hiển thị, chỉ ẩn nếu là trạng thái đặc biệt (đã xử lý ở PHP)
            if (quantitySection) quantitySection.style.display = 'block';
        }
        // Gọi khi load trang và khi đổi định dạng
        updateAttributesGroupDisplay();
        formatSelect.addEventListener('change', updateAttributesGroupDisplay);
    }
});
</script>
@endpush
@endsection