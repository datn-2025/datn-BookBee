@extends('layouts.app')

@section('title', 'Combo Sách - Tiết Kiệm Hơn')

@section('content')
<div class="bg-white min-h-screen">
    <!-- Hero Section - Adidas Style -->
    <section class="w-full bg-white py-32 md:py-40 relative overflow-hidden">
        <!-- Background Elements - Minimal Adidas Style -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 transform rotate-45 translate-x-36 -translate-y-36"></div>
            <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
            <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 items-center px-6 md:px-10 gap-10 max-w-screen-xl mx-auto">
            <!-- Left text - Adidas Typography Style -->
            <div class="space-y-8 text-gray-900">
                <!-- Pre-title với Adidas style -->
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">
                        BOOKBEE COMBO
                    </span>
                </div>

                <!-- Main headline - Bold Adidas typography -->
                <h1 class="text-5xl md:text-7xl font-black uppercase leading-[0.9] tracking-tight text-black">
                    <span class="block">COMBO</span>
                    <span class="block text-gray-400">SÁCH</span>
                    <span class="block">ĐẶC BIỆT</span>
                </h1>

                <!-- Subtitle -->
                <div class="space-y-4">
                    <p class="text-xl md:text-2xl font-medium text-gray-700 max-w-lg">
                        Bộ sưu tập sách được tuyển chọn với giá ưu đãi đặc biệt
                    </p>

                    <!-- Price highlight - Clean Adidas style -->
                    <div class="flex items-center gap-4">
                        <span class="bg-red-600 text-white px-4 py-2 text-sm font-bold uppercase tracking-wide">
                            TIẾT KIỆM 30%
                        </span>
                        <span class="text-2xl font-bold text-black">{{ $combos->total() }} combo có sẵn!</span>
                    </div>
                </div>

                <!-- CTA Button - Adidas style -->
                <div class="pt-4">
                    <a href="#combos-section"
                        class="group bg-black text-white px-10 py-4 font-bold text-sm uppercase tracking-[0.1em] hover:bg-gray-800 transition-all duration-300 flex items-center gap-3 w-max">
                        <span>XEM COMBO</span>
                        <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                    </a>
                </div>
            </div>

            <!-- Right image - Clean presentation -->
            <div class="flex justify-center">
                <div class="relative group">
                    <!-- Main image với clean style -->
                    <div class="relative">
                        <div class="h-80 md:h-96 w-80 md:w-96 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center transform group-hover:scale-105 transition-transform duration-700">
                            <div class="text-center">
                                <div class="w-24 h-24 bg-black text-white flex items-center justify-center mx-auto mb-4 transform group-hover:rotate-12 transition-transform duration-500">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <p class="text-lg font-bold uppercase tracking-wide text-gray-700">COMBO SÁCH</p>
                            </div>
                        </div>

                        <!-- Clean badge thay vì rounded -->
                        <div class="absolute -top-6 -left-6 bg-black text-white px-6 py-3 transform group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform duration-500">
                            <div class="text-center">
                                <div class="text-sm font-bold uppercase tracking-wide">HOT</div>
                                <div class="text-xs uppercase tracking-wider text-gray-300">Deal</div>
                            </div>
                        </div>

                        <!-- Minimal accent -->
                        <div class="absolute -bottom-4 -right-4 bg-white border-2 border-black px-4 py-2 transform group-hover:translate-x-1 group-hover:translate-y-1 transition-transform duration-500">
                            <span class="text-xs font-bold uppercase tracking-wide text-black">Premium</span>
                        </div>
                    </div>

                    <!-- Background geometric shape -->
                    <div class="absolute inset-0 -z-10 bg-gray-100 transform translate-x-4 translate-y-4 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-700"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Filter Section - Adidas Style -->
    <section id="combos-section" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-12">
                <!-- <div class="flex items-center gap-4">
                    <div class="w-1 h-12 bg-black"></div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">COMBO SÁCH</h2>
                        <p class="text-gray-600 font-medium mt-1">Tìm combo phù hợp với bạn</p>
                    </div>
                <div class="hidden md:flex items-center gap-2">
                    <div class="w-8 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">{{ $combos->total() }} COMBO</span>
                </div> -->
            </div>

            <!-- Compact Filter Section -->
            <div class="bg-white border border-gray-200 p-4 mb-8">
                <form method="GET" action="{{ route('combos.index') }}" id="filterForm">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Filter Label -->
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-black flex items-center justify-center">
                                <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wide text-black">LỌC:</span>
                        </div>

                        <!-- Search -->
                        <div class="flex-1 min-w-48">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Tìm combo..." 
                                   class="w-full px-3 py-2 border border-gray-300 focus:border-black transition-all duration-200 text-sm">
                        </div>

                        <!-- Price Range -->
                        <div class="flex gap-1">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                   placeholder="Từ" 
                                   class="w-20 px-2 py-2 border border-gray-300 focus:border-black transition-all duration-200 text-sm">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                   placeholder="Đến" 
                                   class="w-20 px-2 py-2 border border-gray-300 focus:border-black transition-all duration-200 text-sm">
                        </div>
                        
                        <!-- Discount Filter -->
                        <select name="discount" class="px-3 py-2 border border-gray-300 focus:border-black transition-all duration-200 text-sm">
                            <option value="">Giảm giá</option>
                            <option value="10" {{ request('discount') == '10' ? 'selected' : '' }}>Từ 10%</option>
                            <option value="20" {{ request('discount') == '20' ? 'selected' : '' }}>Từ 20%</option>
                            <option value="30" {{ request('discount') == '30' ? 'selected' : '' }}>Từ 30%</option>
                        </select>
                        
                        <!-- Sort -->
                        <select name="sort" class="px-3 py-2 border border-gray-300 focus:border-black transition-all duration-200 text-sm">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Giá thấp → cao</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Giá cao → thấp</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                        </select>

                        <!-- Clear Button -->
                        <button type="button" id="clearFilters" class="px-3 py-2 bg-gray-100 hover:bg-black hover:text-white transition-all duration-200 text-xs font-bold uppercase">
                            Xóa
                        </button>
                    </div>
            </form>
        </div>
    </div>

    <!-- Results Summary -->
    <!-- <div class="container mx-auto px-4 mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 border border-blue-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Kết quả tìm kiếm</h3>
                        <p class="text-gray-600">Tìm thấy <span class="font-semibold text-blue-600">{{ $combos->total() }}</span> combo phù hợp</p>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'min_price', 'max_price', 'discount', 'sort']))
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500">Đang lọc:</span>
                        @if(request('search'))
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">"{{ request('search') }}"</span>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                {{ request('min_price') ? number_format(request('min_price')) . 'đ' : '0đ' }} - 
                                {{ request('max_price') ? number_format(request('max_price')) . 'đ' : '∞' }}
                            </span>
                        @endif
                        @if(request('discount'))
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full">Giảm ≥{{ request('discount') }}%</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div> -->

    <!-- Combos Grid -->
    <div class="container mx-auto px-4 pb-12">
        @if($combos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($combos as $combo)
                <!-- Adidas Style Combo Card -->
                <div class="group bg-white border-2 border-gray-200 hover:border-black transition-all duration-300 relative overflow-hidden">
                    <!-- Combo Image -->
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        @if($combo->cover_image)
                            <img src="{{ asset('storage/' . $combo->cover_image) }}" 
                                 alt="{{ $combo->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center relative">
                                <!-- Clean geometric background -->
                                <div class="absolute top-0 right-0 w-16 h-16 bg-black opacity-5 transform rotate-45 translate-x-8 -translate-y-8"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-1 bg-black opacity-10"></div>
                                
                                <div class="text-center z-10">
                                    <div class="w-12 h-12 bg-black text-white flex items-center justify-center mx-auto mb-2 group-hover:rotate-12 transition-transform duration-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-600">{{ $combo->books->count() }} CUỐN</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Discount Badge - Clean Style -->
                        @if($combo->combo_price && $combo->books->sum(function($book) { return $book->formats->max('price') ?? 0; }) > 0)
                            @php
                                $originalPrice = $combo->books->sum(function($book) { return $book->formats->max('price') ?? 0; });
                                $discountPercent = round((($originalPrice - $combo->combo_price) / $originalPrice) * 100);
                            @endphp
                            @if($discountPercent > 0)
                                <div class="absolute top-4 right-4 bg-red-600 text-white px-3 py-1 text-xs font-bold uppercase tracking-wide">
                                    -{{ $discountPercent }}%
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Combo Info - Adidas Style -->
                    <div class="p-6">
                        <!-- Combo Name -->
                        <h3 class="text-lg font-black uppercase tracking-tight text-black mb-3 line-clamp-2 group-hover:text-gray-600 transition-colors duration-300">
                            {{ $combo->name }}
                        </h3>
                        
                        <!-- Books Count - Clean Style -->
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-4 bg-black"></div>
                            <span class="text-xs font-bold uppercase tracking-wide text-gray-600">{{ $combo->books->count() }} CUỐN SÁCH</span>
                        </div>
                        
                        <!-- Description -->
                        <!-- @if($combo->description)
                            <p class="text-sm text-gray-600 mb-5 line-clamp-2 leading-relaxed">
                                {{ $combo->description }}
                            </p>
                        @endif -->
                        
                        <!-- Price Section - Clean Style -->
                        <div class="mb-6">
                            @if($combo->combo_price)
                                @php
                                    $originalPrice = $combo->books->sum(function($book) { return $book->formats->max('price') ?? 0; });
                                @endphp
                                @if($originalPrice > $combo->combo_price)
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm text-gray-500 line-through">{{ number_format($originalPrice) }}đ</span>
                                        <span class="bg-red-600 text-white px-2 py-1 text-xs font-bold uppercase tracking-wide">
                                            TIẾT KIỆM {{ number_format($originalPrice - $combo->combo_price) }}Đ
                                        </span>
                                    </div>
                                @endif
                                <div class="text-2xl font-black text-black">
                                    {{ number_format($combo->combo_price) }}đ
                                </div>
                            @else
                                <div class="text-2xl font-black text-black">
                                    {{ number_format($combo->books->sum(function($book) { return $book->formats->max('price') ?? 0; })) }}đ
                                </div>
                            @endif
                        </div>
                        
                        <!-- Action Button - Adidas Style -->
                        <a href="{{ route('combos.show', $combo->slug) }}" 
                           class="group bg-black text-white hover:bg-gray-800 transition-all duration-300 flex items-center justify-center gap-3 w-full py-3 font-bold text-sm uppercase tracking-[0.1em]">
                            <span>XEM CHI TIẾT</span>
                            <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($combos->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="bg-white rounded-lg shadow-sm border">
                        {{ $combos->links() }}
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Chưa có combo nào
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Hiện tại chưa có combo sách nào được tạo. Hãy khám phá các cuốn sách đơn lẻ của chúng tôi.
                    </p>
                    
                    <a href="{{ route('books.index') }}" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition-colors duration-200">
                        Xem sách đơn lẻ
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Benefits Section -->
    <div class="bg-white py-12 border-t">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    Tại sao chọn combo sách?
                </h2>
                <p class="text-gray-600">Những lợi ích khi mua combo sách tại cửa hàng chúng tôi</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Tiết kiệm chi phí</h3>
                    <p class="text-sm text-gray-600">Giá combo luôn rẻ hơn khi mua từng cuốn riêng biệt</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Chất lượng đảm bảo</h3>
                    <p class="text-sm text-gray-600">Các cuốn sách được tuyển chọn kỹ lưỡng</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Tiện lợi mua sắm</h3>
                    <p class="text-sm text-gray-600">Một lần đặt hàng, nhận nhiều cuốn sách hay</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float 3s ease-in-out infinite;
    animation-delay: 1s;
}

/* Gradient text animation */
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradient {
    background: linear-gradient(-45deg, #3B82F6, #8B5CF6, #EC4899, #10B981);
    background-size: 400% 400%;
    animation: gradient 3s ease infinite;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Loading animation */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('input, select');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Add loading state
            document.body.classList.add('loading');
            filterForm.submit();
        });
    });
    
    // Clear filters functionality
    document.getElementById('clearFilters').addEventListener('click', function() {
        // Clear all form inputs
        filterInputs.forEach(input => {
            if (input.type === 'text' || input.type === 'number') {
                input.value = '';
            } else if (input.type === 'select-one') {
                input.selectedIndex = 0;
            }
        });
        
        // Submit form to clear filters
        filterForm.submit();
    });
    
    // Search input with debounce
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500); // 500ms debounce
        });
    }
    
    // Add smooth scroll to results when filters are applied
    if (window.location.search) {
        setTimeout(() => {
            const resultsSection = document.querySelector('.container');
            if (resultsSection) {
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
    
    // Add intersection observer for card animations
    const cards = document.querySelectorAll('.group');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Add price range validation
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');
    
    function validatePriceRange() {
        const minPrice = parseInt(minPriceInput.value) || 0;
        const maxPrice = parseInt(maxPriceInput.value) || Infinity;
        
        if (minPrice > maxPrice && maxPrice !== Infinity) {
            maxPriceInput.setCustomValidity('Giá tối đa phải lớn hơn giá tối thiểu');
        } else {
            maxPriceInput.setCustomValidity('');
        }
    }
    
    if (minPriceInput && maxPriceInput) {
        minPriceInput.addEventListener('input', validatePriceRange);
        maxPriceInput.addEventListener('input', validatePriceRange);
    }
    
    // Add hover effects to filter inputs
    filterInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('scale-105');
            this.parentElement.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('scale-105');
        });
    });
});
</script>
@endsection
