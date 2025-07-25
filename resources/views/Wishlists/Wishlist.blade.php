@extends('layouts.app')

@section('content')
<link href="{{ asset('css/wishlist-adidas.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="wishlist-container">
    <!-- Header Section -->
    <div class="wishlist-header">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-6xl mx-auto">
                <nav class="wishlist-breadcrumb">
                    <a href="/" class="hover:opacity-70 transition-opacity">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                    <span class="text-white/60">→</span>
                    <span><i class="fas fa-heart"></i> Danh sách yêu thích</span>
                </nav>
                <h1 class="wishlist-title">
                    <i class="fas fa-heart" aria-label="Wishlist icon"></i> Wishlist
                </h1>
                <div class="wishlist-stats">
                    <div class="flex items-center gap-4">
                        <div class="stats-number">{{ $statistics['total'] }}</div>
                        <div class="text-white/90 font-medium">
                            <i class="fas fa-heart"></i> Sản phẩm yêu thích
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Controls Section -->
    <div class="wishlist-controls">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="control-group">
                    <form class="flex items-center gap-4 flex-wrap">
                        <label for="sort-select" class="font-bold text-sm uppercase tracking-wide text-gray-800 flex items-center gap-2">
                            <i class="fas fa-sort"></i> Sắp xếp:
                        </label>
                        <select id="sort-select" class="adidas-select">
                            <option value="date-desc"><i class="fas fa-calendar-alt"></i> Mới nhất trước</option>
                            <option value="date-asc"><i class="fas fa-calendar-alt"></i> Cũ nhất trước</option>
                            <option value="title-asc"><i class="fas fa-sort-alpha-down"></i> Theo tên A-Z</option>
                            <option value="title-desc"><i class="fas fa-sort-alpha-up"></i> Theo tên Z-A</option>
                        </select>
                    </form>
                </div>
                <div class="control-group">
                    <button onclick="toggleShortcutsModal()" class="adidas-btn adidas-btn-outline">
                        <i class="fas fa-keyboard"></i> Phím tắt
                    </button>
                    <button onclick="removeAllFromWishlist()" class="adidas-btn adidas-btn-danger">
                        <i class="fas fa-trash-alt"></i> Xóa tất cả
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="wishlist-content">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                @if($wishlist->isEmpty())
                    <div class="empty-wishlist">
                        <div class="empty-icon">
                            <i class="fas fa-heart-broken" aria-label="Empty wishlist"></i>
                        </div>
                        <h2 class="empty-title">Danh sách trống</h2>
                        <p class="empty-message">
                            Danh sách yêu thích trống. Hãy khám phá và thêm những cuốn sách bạn yêu thích vào đây!
                        </p>
                        <a href="/books" class="adidas-btn">
                            <i class="fas fa-book" aria-hidden="true"></i> Khám phá sách ngay
                        </a>
                    </div>
                @else
                    <div class="wishlist-grid">
                        @foreach($wishlist as $item)
                            <div class="wishlist-item" data-book-id="{{ $item->book_id }}">
                                <div class="item-content">
                                    <h3 class="item-title">{{ $item->title }}</h3>
                                    <p class="item-author">{{ $item->author_name }}</p>
                                    
                                    <div class="item-details">
                                        <div class="item-detail">
                                            <span class="detail-label">
                                                <i class="fas fa-tag"></i> Loại sách:
                                            </span>
                                            <span class="detail-value">{{ $item->category_name ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                        <div class="item-detail">
                                            <span class="detail-label">
                                                <i class="fas fa-building"></i> Nhà xuất bản:
                                            </span>
                                            <span class="detail-value">{{ $item->brand_name ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="item-date">
                                        <i class="fas fa-clock"></i> Đã thêm {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                    </div>
                                    
                                    <div class="item-actions">
                                        <a href="{{ route('books.show', ['slug' => $item->slug]) }}" class="adidas-btn adidas-btn-outline">
                                            <i class="fas fa-eye" aria-hidden="true"></i> Xem chi tiết
                                        </a>
                                        <button onclick="removeFromWishlist('{{ $item->book_id }}')" class="adidas-btn adidas-btn-danger">
                                            <i class="fas fa-times" aria-hidden="true"></i> Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="wishlist-pagination">
                        {{ $wishlist->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Tips Section -->
    <div class="wishlist-tips">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="tips-title">
                    <i class="fas fa-lightbulb"></i> Mẹo hay cho bạn
                </h2>
                <ul class="tips-list">
                    <li>
                        <i class="fas fa-shopping-cart"></i> Thêm sách vào giỏ hàng để mua ngay
                    </li>
                    <li>
                        <i class="fas fa-star"></i> Theo dõi sách yêu thích của bạn
                    </li>
                    <li>
                        <i class="fas fa-search"></i> Dễ dàng tìm lại sách đã thích
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/wishlist.js') }}"></script>
<script src="{{ asset('js/wishlist-adidas.js') }}"></script>
<script src="{{ asset('js/wishlist-icons.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
