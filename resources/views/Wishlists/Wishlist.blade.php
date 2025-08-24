@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@push('styles')
<style>
/* Enhanced Home-style design với amber theme */
.wishlist-page {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: white;
}

/* Typography classes để consistent với home page */
.hero-title {
    font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.body-text {
    font-family: 'TitilliumWeb', sans-serif;
    font-weight: 400;
    line-height: 1.6;
}

.button-text {
    font-family: 'AdihausDIN', 'TitilliumWeb', sans-serif;
    font-weight: 600;
    letter-spacing: 0.15em;
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

/* Enhanced hover effects với amber theme */
.group:hover .group-hover\:w-8 {
    width: 2rem;
}

.group:hover .group-hover\:w-16 {
    width: 4rem;
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:bg-amber-500 {
    background-color: #f59e0b;
}

.group:hover .group-hover\:bg-amber-600 {
    background-color: #d97706;
}

.group:hover .group-hover\:text-amber-600 {
    color: #d97706;
}

.group:hover .group-hover\:text-amber-700 {
    color: #b45309;
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

/* Custom focus styles với amber theme */
select:focus, button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .hero-title {
        font-size: 3rem !important;
    }
    
    .body-text {
        font-size: 1.125rem !important;
    }
}

/* Enhanced animations cho amber theme */
@keyframes amberGlow {
    0%, 100% { 
        box-shadow: 0 0 5px rgba(245, 158, 11, 0.3);
    }
    50% { 
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.6);
    }
}

.amber-glow:hover {
    animation: amberGlow 2s infinite;
}
</style>
@endpush

<div class="wishlist-page">
    <!-- Enhanced Hero Section - Home Style với amber theme -->
    <section class="w-full bg-white py-32 md:py-40 relative overflow-hidden">
        <!-- Background Elements - Giống trang chủ -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 rounded-none transform rotate-45 translate-x-36 -translate-y-36"></div>
            <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
            <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 items-center px-6 md:px-10 gap-10 max-w-screen-xl mx-auto">
            {{-- Left text - Enhanced Home Style --}}
            <div class="space-y-8 text-gray-900">
                <!-- Breadcrumb - Enhanced với amber -->
                <nav class="flex items-center gap-3 text-sm uppercase tracking-wider">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-amber-600 transition-colors duration-300 hover:underline font-medium">
                        <i class="fas fa-home mr-1"></i>Trang chủ
                    </a>
                    <div class="w-4 h-0.5 bg-amber-400"></div>
                    <span class="text-amber-600 font-bold"><i class="fas fa-heart mr-1"></i>Danh sách yêu thích</span>
                </nav>

                <!-- Pre-title với amber theme -->
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-8 h-0.5 bg-gradient-to-r from-amber-600 to-orange-600"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600 flex items-center gap-2">
                        <i class="fas fa-heart text-amber-500"></i>
                        DANH SÁCH YÊU THÍCH BOOKBEE
                    </span>
                </div>

                <!-- Main title với amber accent -->
                <h1 class="hero-title text-5xl md:text-7xl font-black uppercase leading-[0.9] tracking-tight text-black">
                    <span class="block">YÊU THÍCH</span>
                    <span class="block text-amber-600">KHÔNG</span>
                    <span class="block">GIỚI HẠN</span>
                </h1>

                <!-- Subtitle -->
                <div class="space-y-4">
                    <p class="body-text text-xl md:text-2xl font-medium text-gray-700 max-w-lg">
                        Bộ sưu tập sách yêu thích của bạn - Nơi lưu giữ những cuốn sách đặc biệt
                    </p>

                    <!-- Stats highlight với amber theme -->
                    <div class="flex items-center gap-4">
                        <span class="bg-amber-600 text-white px-4 py-2 text-sm font-bold uppercase tracking-wide">
                            <i class="fas fa-heart mr-1"></i>
                            {{ $statistics['total'] }} SÁCH
                        </span>
                        <span class="text-2xl font-bold text-amber-600">Yêu thích nhất!</span>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4">
                    <a href="{{ route('books.index') }}"
                        class="group bg-amber-600 text-white px-10 py-4 font-bold text-sm uppercase tracking-[0.1em] hover:bg-amber-700 transition-all duration-300 flex items-center gap-3 w-max button-text">
                        <i class="fas fa-search text-white"></i>
                        <span>KHÁM PHÁ THÊM</span>
                        <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                    </a>
                </div>
            </div>

            {{-- Right image - Background style như trang chủ --}}
            <div class="flex justify-center">
                <div class="relative group">
                    <!-- Background image style -->
                    <div class="relative h-80 md:h-96 w-80 md:w-96 bg-cover bg-center bg-no-repeat transform group-hover:scale-105 transition-transform duration-700"
                         style="background-image: url('{{ asset('storage/images/banner-image2.png') }}');">
                        
                        <!-- Badge với amber theme -->
                        <div class="absolute -top-6 -left-6 bg-amber-600 text-white px-6 py-3 transform group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform duration-500 shadow-lg">
                            <div class="text-center">
                                <div class="text-sm font-bold uppercase tracking-wide">{{ $statistics['total'] }}</div>
                                <div class="text-xs uppercase tracking-wider text-amber-100">YÊU THÍCH</div>
                            </div>
                        </div>

                        <!-- Accent với amber theme -->
                        <div class="absolute -bottom-4 -right-4 bg-white border-2 border-amber-600 px-4 py-2 transform group-hover:translate-x-1 group-hover:translate-y-1 transition-transform duration-500 shadow-lg">
                            <span class="text-xs font-bold uppercase tracking-wide text-amber-600">Favorites</span>
                        </div>
                    </div>

                    <!-- Background geometric shape -->
                    <div class="absolute inset-0 -z-10 bg-amber-100 transform translate-x-4 translate-y-4 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-700"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Controls Section với amber theme -->
    <section class="bg-gray-50 py-6 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <!-- Sort Section -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-8 bg-amber-600"></div>
                        <label class="font-bold text-sm uppercase tracking-wide text-black">
                            <i class="fas fa-sort"></i> Sắp xếp:
                        </label>
                    </div>
                    <select id="sort-select" class="bg-white border border-gray-300 px-4 py-2 font-medium text-sm uppercase tracking-wide focus:border-amber-600 focus:outline-none transition-colors">
                        <option value="date-desc">MỚI NHẤT TRƯỚC</option>
                        <option value="date-asc">CŨ NHẤT TRƯỚC</option>
                        <option value="title-asc">THEO TÊN A-Z</option>
                        <option value="title-desc">THEO TÊN Z-A</option>
                    </select>
                </div>

                <!-- Action Buttons với amber theme -->
                <div class="flex gap-3">
                    <button onclick="toggleShortcutsModal()" class="bg-amber-100 hover:bg-amber-200 text-amber-800 px-6 py-3 font-bold text-sm uppercase tracking-wide transition-colors">
                        <i class="fas fa-keyboard"></i> Phím tắt
                    </button>
                    <button onclick="removeAllFromWishlist()" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 font-bold text-sm uppercase tracking-wide transition-colors">
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
                    <!-- Decorative Elements với amber theme -->
                    <div class="relative inline-block mb-8">
                        <div class="w-24 h-24 bg-amber-50 flex items-center justify-center mx-auto relative">
                            <i class="fas fa-heart-broken text-4xl text-amber-400"></i>
                            <!-- Geometric accent với amber -->
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-amber-600 transform rotate-45"></div>
                        </div>
                    </div>

                    <div class="max-w-2xl mx-auto space-y-6">
                        <!-- Title với amber theme -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-center gap-4">
                                <div class="w-12 h-0.5 bg-amber-600 opacity-20"></div>
                                <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-400">EMPTY STATE</span>
                                <div class="w-12 h-0.5 bg-amber-600 opacity-20"></div>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                                CHƯA CÓ YÊU THÍCH
                            </h2>
                        </div>

                        <!-- Description -->
                        <p class="text-lg text-gray-600 max-w-lg mx-auto">
                            Danh sách yêu thích trống. Hãy khám phá và thêm những cuốn sách bạn yêu thích vào đây!
                        </p>

                        <!-- CTA Button với amber theme -->
                        <div class="pt-6">
                            <a href="{{ route('books.index') }}" class="group bg-amber-600 text-white px-10 py-4 font-bold text-sm uppercase tracking-[0.1em] hover:bg-amber-700 transition-all duration-300 inline-flex items-center gap-3">
                                <i class="fas fa-search"></i>
                                <span>KHÁM PHÁ SÁCH NGAY</span>
                                <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Content Header với amber theme -->
                <div class="flex items-center justify-between mb-12">
                    <div class="flex items-center gap-4">
                        <div class="w-1 h-12 bg-amber-600"></div>
                        <div>
                            <h2 class="text-2xl md:text-3xl font-black uppercase tracking-tight text-black">
                                SẢN PHẨM YÊU THÍCH
                            </h2>
                            <div class="w-16 h-0.5 bg-amber-600 mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($wishlist as $item)
                        <div class="group bg-white border border-gray-100 hover:border-amber-600 hover:shadow-xl transition-all duration-500 relative overflow-hidden" data-book-id="{{ $item->book_id }}">
                            <!-- Hover overlay với amber tint -->
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-600/0 to-amber-600/0 group-hover:from-amber-600/5 group-hover:to-transparent transition-all duration-500"></div>
                            
                            <!-- Book Cover Image -->
                            <div clclass="aspect-[3/4] overflow-hidden bg-gray-50">
                                @if($item->cover_image)
                                    <img src="{{ asset('storage/' . $item->cover_image) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="text-gray-400">
                                        <i class="fas fa-book text-5xl"></i>
                                    </div>
                                @endif
                            </div>
                            
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

                                <!-- Action Buttons với amber theme -->
                                <div class="space-y-3">
                                    <a href="{{ route('books.show', ['slug' => $item->slug]) }}" 
                                       class="w-full bg-amber-100 hover:bg-amber-200 text-amber-800 py-3 px-4 font-bold text-sm uppercase tracking-wide transition-colors text-center block">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    <button onclick="removeFromWishlist('{{ $item->book_id }}')" 
                                            class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 px-4 font-bold text-sm uppercase tracking-wide transition-colors">
                                        <i class="fas fa-times"></i> Xóa khỏi danh sách
                                    </button>
                                </div>

                                <!-- Progress indicator với amber -->
                                <div class="absolute bottom-0 left-0 h-1 bg-amber-600 w-0 group-hover:w-full transition-all duration-700"></div>
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
            <!-- Header với amber theme -->
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-amber-600"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        HELPFUL TIPS
                    </span>
                    <div class="w-12 h-0.5 bg-amber-600"></div>
                </div>
                <h2 class="text-2xl md:text-3xl font-black uppercase tracking-tight text-black">
                    MẸO HAY CHO BẠN
                </h2>
            </div>

            <!-- Tips Grid với amber theme -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Tip 1 với amber theme -->
                <div class="group bg-white border border-gray-100 hover:border-amber-600 hover:shadow-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-12 h-12 bg-amber-50 transform rotate-45 translate-x-6 -translate-y-6 group-hover:bg-amber-100 transition-colors duration-500"></div>
                    
                    <div class="p-8 text-center relative z-10">
                        <div class="w-12 h-12 bg-amber-600 text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-amber-700 transition-colors duration-500">
                            <i class="fas fa-shopping-cart text-lg"></i>
                        </div>
                        
                        <h3 class="text-sm font-bold uppercase tracking-wide text-black mb-4 group-hover:text-amber-600 transition-colors duration-300">
                            THÊM VÀO GIỎ HÀNG
                        </h3>
                        
                        <div class="w-8 h-0.5 bg-amber-600 mx-auto mb-4 group-hover:w-16 transition-all duration-500"></div>
                        
                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                            Thêm sách vào giỏ hàng để mua ngay
                        </p>

                        <div class="absolute bottom-0 left-0 h-1 bg-amber-600 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>

                <!-- Tip 2 với amber theme -->
                <div class="group bg-white border border-gray-100 hover:border-amber-600 hover:shadow-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-12 h-12 bg-amber-50 transform rotate-45 translate-x-6 -translate-y-6 group-hover:bg-amber-100 transition-colors duration-500"></div>
                    
                    <div class="p-8 text-center relative z-10">
                        <div class="w-12 h-12 bg-amber-600 text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-amber-700 transition-colors duration-500">
                            <i class="fas fa-star text-lg"></i>
                        </div>
                        
                        <h3 class="text-sm font-bold uppercase tracking-wide text-black mb-4 group-hover:text-amber-600 transition-colors duration-300">
                            THEO DÕI YÊU THÍCH
                        </h3>
                        
                        <div class="w-8 h-0.5 bg-amber-600 mx-auto mb-4 group-hover:w-16 transition-all duration-500"></div>
                        
                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                            Theo dõi sách yêu thích của bạn
                        </p>

                        <div class="absolute bottom-0 left-0 h-1 bg-amber-600 w-0 group-hover:w-full transition-all duration-700"></div>
                    </div>
                </div>

                <!-- Tip 3 với amber theme -->
                <div class="group bg-white border border-gray-100 hover:border-amber-600 hover:shadow-lg transition-all duration-500 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-12 h-12 bg-amber-50 transform rotate-45 translate-x-6 -translate-y-6 group-hover:bg-amber-100 transition-colors duration-500"></div>
                    
                    <div class="p-8 text-center relative z-10">
                        <div class="w-12 h-12 bg-amber-600 text-white flex items-center justify-center mb-6 mx-auto group-hover:bg-amber-700 transition-colors duration-500">
                            <i class="fas fa-search text-lg"></i>
                        </div>
                        
                        <h3 class="text-sm font-bold uppercase tracking-wide text-black mb-4 group-hover:text-amber-600 transition-colors duration-300">
                            TÌM KIẾM DỄ DÀNG
                        </h3>
                        
                        <div class="w-8 h-0.5 bg-amber-600 mx-auto mb-4 group-hover:w-16 transition-all duration-500"></div>
                        
                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                            Dễ dàng tìm lại sách đã thích
                        </p>

                        <div class="absolute bottom-0 left-0 h-1 bg-amber-600 w-0 group-hover:w-full transition-all duration-700"></div>
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
