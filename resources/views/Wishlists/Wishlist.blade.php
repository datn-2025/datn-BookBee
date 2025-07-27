@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@push('styles')
<style>
/* Home-style design for wishlist */
.wishlist-page {
    font-family: 'Arial Black', 'Helvetica Neue', sans-serif;
    background: white;
}

.wishlist-hero {
    position: relative;
    overflow: hidden;
    background: white;
    padding: 8rem 0 4rem;
}

.wishlist-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 18rem;
    height: 18rem;
    background: black;
    opacity: 0.03;
    transform: rotate(45deg) translate(9rem, -9rem);
}

.wishlist-hero::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 24rem;
    height: 0.5rem;
    background: black;
    opacity: 0.1;
}

.hero-accent-line {
    position: absolute;
    top: 50%;
    left: 2.5rem;
    width: 0.25rem;
    height: 8rem;
    background: black;
    opacity: 0.2;
}

.breadcrumb-home {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: #6b7280;
}

.breadcrumb-home a {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-home a:hover {
    color: black;
}

.title-section {
    margin-bottom: 3rem;
}

.pre-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.pre-title-line {
    width: 2rem;
    height: 0.125rem;
    background: black;
}

.pre-title-text {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    color: #6b7280;
}

.main-title {
    font-size: 3rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -0.05em;
    color: black;
    line-height: 0.9;
    margin-bottom: 1rem;
}

.stats-display {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 900;
    color: black;
}

.stats-text {
    font-size: 1rem;
    font-weight: 500;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions for all elements */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
.group:hover .group-hover\:w-8 {
    width: 2rem;
}

.group:hover .group-hover\:w-16 {
    width: 4rem;
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:bg-red-500 {
    background-color: #ef4444;
}

.group:hover .group-hover\:bg-yellow-500 {
    background-color: #eab308;
}

.group:hover .group-hover\:bg-blue-500 {
    background-color: #3b82f6;
}

.group:hover .group-hover\:text-red-600 {
    color: #dc2626;
}

.group:hover .group-hover\:text-yellow-600 {
    color: #d97706;
}

.group:hover .group-hover\:text-blue-600 {
    color: #2563eb;
}

/* Responsive breakpoints */
@media (min-width: 768px) {
    .main-title {
        font-size: 4rem;
    }
    .stats-number {
        font-size: 3rem;
    }
    .wishlist-hero {
        padding: 10rem 0 6rem;
    }
}

@media (max-width: 768px) {
    .hero-accent-line {
        left: 1rem;
        width: 0.125rem;
        height: 6rem;
    }
    
    .wishlist-hero {
        padding: 6rem 0 3rem;
    }
    
    .breadcrumb-home {
        font-size: 0.625rem;
    }
}

/* Animation keyframes */
@keyframes slideIn {
    from { transform: scaleX(0); }
    to { transform: scaleX(1); }
}

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

/* Loading and interaction animations */
.group {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.group:hover {
    transform: translateY(-2px);
}

/* Custom focus styles */
select:focus, button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
}
</style>
@endpush

<div class="wishlist-page">
    <!-- Hero Section - Home Style -->
    <section class="wishlist-hero">
        <!-- Background decorative elements -->
        <div class="hero-accent-line"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-6">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-home">
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <span>→</span>
                <span><i class="fas fa-heart"></i> Danh sách yêu thích</span>
            </nav>

            <!-- Title Section -->
            <div class="title-section">
                <div class="pre-title">
                    <div class="pre-title-line"></div>
                    <span class="pre-title-text">YOUR FAVORITES</span>
                    <div class="pre-title-line"></div>
                </div>
                
                <h1 class="main-title">
                    <span class="block">IMPOSSIBLE</span>
                    <span class="block text-gray-400">TO</span>
                    <span class="block">FORGET</span>
                </h1>

                <!-- Stats Display -->
                <div class="stats-display">
                    <div class="stats-number">{{ $statistics['total'] }}</div>
                    <div class="stats-text">
                        <i class="fas fa-heart"></i> Sách yêu thích
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Controls Section - Home Style -->
    <section class="bg-gray-50 py-6 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <!-- Sort Section -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-8 bg-black"></div>
                        <label class="font-bold text-sm uppercase tracking-wide text-black">
                            <i class="fas fa-sort"></i> Sắp xếp:
                        </label>
                    </div>
                    <select id="sort-select" class="bg-white border border-gray-300 px-4 py-2 font-medium text-sm uppercase tracking-wide focus:border-black focus:outline-none transition-colors">
                        <option value="date-desc">MỚI NHẤT TRƯỚC</option>
                        <option value="date-asc">CŨ NHẤT TRƯỚC</option>
                        <option value="title-asc">THEO TÊN A-Z</option>
                        <option value="title-desc">THEO TÊN Z-A</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button onclick="toggleShortcutsModal()" class="bg-gray-100 hover:bg-gray-200 text-black px-6 py-3 font-bold text-sm uppercase tracking-wide transition-colors">
                        <i class="fas fa-keyboard"></i> Phím tắt
                    </button>
                    <button onclick="removeAllFromWishlist()" class="bg-black hover:bg-gray-800 text-white px-6 py-3 font-bold text-sm uppercase tracking-wide transition-colors">
                        <i class="fas fa-trash-alt"></i> Xóa tất cả
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- Main Content Section -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            @if($wishlist->isEmpty())
                <!-- Empty State - Home Style -->
                <div class="text-center py-20">
                    <!-- Decorative Elements -->
                    <div class="relative inline-block mb-8">
                        <div class="w-24 h-24 bg-gray-100 flex items-center justify-center mx-auto relative">
                            <i class="fas fa-heart-broken text-4xl text-gray-400"></i>
                            <!-- Geometric accent -->
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-black transform rotate-45"></div>
                        </div>
                    </div>

                    <div class="max-w-2xl mx-auto space-y-6">
                        <!-- Title -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-center gap-4">
                                <div class="w-12 h-0.5 bg-black opacity-20"></div>
                                <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-400">EMPTY STATE</span>
                                <div class="w-12 h-0.5 bg-black opacity-20"></div>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                                IMPOSSIBLE TO FIND
                            </h2>
                        </div>

                        <!-- Description -->
                        <p class="text-lg text-gray-600 max-w-lg mx-auto">
                            Danh sách yêu thích trống. Hãy khám phá và thêm những cuốn sách bạn yêu thích vào đây!
                        </p>

                        <!-- CTA Button -->
                        <div class="pt-6">
                            <a href="{{ route('books.index') }}" class="group bg-black text-white px-10 py-4 font-bold text-sm uppercase tracking-[0.1em] hover:bg-gray-800 transition-all duration-300 inline-flex items-center gap-3">
                                <i class="fas fa-book"></i>
                                <span>KHÁM PHÁ SÁCH NGAY</span>
                                <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Content Header -->
                <div class="flex items-center justify-between mb-12">
                    <div class="flex items-center gap-4">
                        <div class="w-1 h-12 bg-black"></div>
                        <div>
                            <h2 class="text-2xl md:text-3xl font-black uppercase tracking-tight text-black">
                                SẢN PHẨM YÊU THÍCH
                            </h2>
                            <div class="w-16 h-0.5 bg-black mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($wishlist as $item)
                        <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden" data-book-id="{{ $item->book_id }}">
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-gradient-to-br from-black/0 to-black/0 group-hover:from-black/5 group-hover:to-transparent transition-all duration-500"></div>
                            
                            <!-- Content -->
                            <div class="p-6 relative z-10">
                                <!-- Book Info -->
                                <div class="space-y-4 mb-6">
                                    <div class="space-y-2">
                                        <h3 class="text-lg font-bold text-black group-hover:text-gray-800 transition-colors line-clamp-2">
                                            {{ $item->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 font-medium uppercase tracking-wide">
                                            {{ $item->author_name }}
                                        </p>
                                    </div>

                                    <!-- Details Grid -->
                                    <div class="space-y-3 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500 uppercase tracking-wide">
                                                <i class="fas fa-tag w-4"></i> Loại sách:
                                            </span>
                                            <span class="text-black font-medium">{{ $item->category_name ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-500 uppercase tracking-wide">
                                                <i class="fas fa-building w-4"></i> NXB:
                                            </span>
                                            <span class="text-black font-medium">{{ $item->brand_name ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>

                                    <!-- Date Added -->
                                    <div class="flex items-center gap-2 text-xs text-gray-500 pt-2 border-t border-gray-100">
                                        <i class="fas fa-clock"></i>
                                        <span class="uppercase tracking-wide">
                                            Đã thêm {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-3">
                                    <a href="{{ route('books.show', ['slug' => $item->slug]) }}" 
                                       class="w-full bg-gray-100 hover:bg-gray-200 text-black py-3 px-4 font-bold text-sm uppercase tracking-wide transition-colors text-center block">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    <button onclick="removeFromWishlist('{{ $item->book_id }}')" 
                                            class="w-full bg-black hover:bg-gray-800 text-white py-3 px-4 font-bold text-sm uppercase tracking-wide transition-colors">
                                        <i class="fas fa-times"></i> Xóa khỏi danh sách
                                    </button>
                                </div>

                                <!-- Progress indicator -->
                                <div class="absolute bottom-0 left-0 h-1 bg-black w-0 group-hover:w-full transition-all duration-700"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($wishlist->hasPages())
                <div class="mt-16 pt-12 border-t border-gray-100">
                    <div class="flex justify-center">
                        {{ $wishlist->links('pagination::tailwind') }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </section>
    <!-- Tips Section - Home Style -->
    <section class="bg-gray-50 py-20 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        HELPFUL TIPS
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-2xl md:text-3xl font-black uppercase tracking-tight text-black">
                    MẸO HAY CHO BẠN
                </h2>
            </div>

            <!-- Tips Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Tip 1 -->
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-12 h-12 bg-red-50 transform rotate-45 translate-x-6 -translate-y-6 group-hover:bg-red-100 transition-colors duration-500"></div>
                    
                    <div class="p-8 text-center relative z-10">
                        <div class="w-12 h-12 bg-black text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-red-500 transition-colors duration-500">
                            <i class="fas fa-shopping-cart text-lg"></i>
                        </div>
                        
                        <h3 class="text-sm font-bold uppercase tracking-wide text-black mb-4 group-hover:text-red-600 transition-colors duration-300">
                            THÊM VÀO GIỎ HÀNG
                        </h3>
                        
                        <div class="w-8 h-0.5 bg-black mx-auto mb-4 group-hover:w-16 group-hover:bg-red-500 transition-all duration-500"></div>
                        
                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                            Thêm sách vào giỏ hàng để mua ngay
                        </p>

                        <div class="absolute bottom-0 left-0 h-1 bg-red-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>

                <!-- Tip 2 -->
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-12 h-12 bg-yellow-50 transform rotate-45 translate-x-6 -translate-y-6 group-hover:bg-yellow-100 transition-colors duration-500"></div>
                    
                    <div class="p-8 text-center relative z-10">
                        <div class="w-12 h-12 bg-black text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-yellow-500 transition-colors duration-500">
                            <i class="fas fa-star text-lg"></i>
                        </div>
                        
                        <h3 class="text-sm font-bold uppercase tracking-wide text-black mb-4 group-hover:text-yellow-600 transition-colors duration-300">
                            THEO DÕI YÊU THÍCH
                        </h3>
                        
                        <div class="w-8 h-0.5 bg-black mx-auto mb-4 group-hover:w-16 group-hover:bg-yellow-500 transition-all duration-500"></div>
                        
                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                            Theo dõi sách yêu thích của bạn
                        </p>

                        <div class="absolute bottom-0 left-0 h-1 bg-yellow-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>

                <!-- Tip 3 -->
                <div class="group bg-white border border-gray-100 hover:border-black hover:shadow-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-12 h-12 bg-blue-50 transform rotate-45 translate-x-6 -translate-y-6 group-hover:bg-blue-100 transition-colors duration-500"></div>
                    
                    <div class="p-8 text-center relative z-10">
                        <div class="w-12 h-12 bg-black text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-blue-500 transition-colors duration-500">
                            <i class="fas fa-search text-lg"></i>
                        </div>
                        
                        <h3 class="text-sm font-bold uppercase tracking-wide text-black mb-4 group-hover:text-blue-600 transition-colors duration-300">
                            TÌM KIẾM DỄ DÀNG
                        </h3>
                        
                        <div class="w-8 h-0.5 bg-black mx-auto mb-4 group-hover:w-16 group-hover:bg-blue-500 transition-all duration-500"></div>
                        
                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                            Dễ dàng tìm lại sách đã thích
                        </p>

                        <div class="absolute bottom-0 left-0 h-1 bg-blue-500 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="{{ asset('js/wishlist.js') }}"></script>
<script src="{{ asset('js/wishlist-adidas.js') }}"></script>
<script src="{{ asset('js/wishlist-icons.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
