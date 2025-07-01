@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Log;
@endphp

@section('title', 'Giỏ hàng')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="/css/cart-adidas.css">
@endpush

@section('content')
<div class="adidas-cart-container">
    <!-- Modern Page Header -->
    <div class="adidas-cart-header">
        <div class="adidas-cart-header-content">
            <div class="adidas-cart-header-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
                <h1 class="adidas-cart-title">Giỏ hàng của bạn</h1>
                <p class="adidas-cart-desc">Quản lý các sản phẩm bạn muốn mua</p>
            </div>
        </div>
        @if(count($cart) > 0)
            <div class="adidas-cart-actions">
                <a id="add-wishlist-btn" class="adidas-btn adidas-btn-secondary" href="{{ route('wishlist.index') }}">
                    <i class="fas fa-heart"></i> Thêm từ yêu thích
                </a>
                <button id="clear-cart-btn" class="adidas-btn adidas-btn-danger">
                    <i class="fas fa-trash-alt"></i> Xóa tất cả
                </button>
            </div>
        @endif
    </div>

    @if(count($cart) > 0)
        <div class="adidas-cart-main">
            <!-- Danh sách sản phẩm -->
            <div class="adidas-cart-products">
                <div class="adidas-cart-products-header">
                    <i class="fas fa-shopping-cart"></i>
                    <h4>Sản phẩm trong giỏ ({{ count($cart) }})</h4>
                    @php
                        $hasEbooks = false;
                        $hasPhysical = false;
                        foreach($cart as $item) {
                            $isEbook = isset($item->format_name) && (stripos($item->format_name, 'ebook') !== false);
                            if ($isEbook) {
                                $hasEbooks = true;
                            } else {
                                $hasPhysical = true;
                            }
                        }
                    @endphp
                    @if($hasEbooks && $hasPhysical)
                        <div class="cart-type-indicator mixed">
                            <span class="badge badge-mixed">
                                <i class="fas fa-mobile-alt"></i> Ebooks
                                <span class="mx-2">+</span>
                                <i class="fas fa-book"></i> Sách vật lý
                            </span>
                        </div>
                    @elseif($hasEbooks)
                        <div class="cart-type-indicator ebook">
                            <span class="badge badge-ebook">
                                <i class="fas fa-mobile-alt"></i> Chỉ Ebooks
                            </span>
                        </div>
                    @else
                        <div class="cart-type-indicator physical">
                            <span class="badge badge-physical">
                                <i class="fas fa-book"></i> Chỉ sách vật lý
                            </span>
                        </div>
                    @endif
                </div>
                <div class="adidas-cart-products-list">
                @foreach($cart as $item)
                    <div class="adidas-cart-product-card cart-item" 
                         data-book-id="{{ $item->book_id }}" 
                         data-book-format-id="{{ $item->book_format_id }}"
                         data-attribute-value-ids="{{ $item->attribute_value_ids }}"
                         data-price="{{ $item->price ?? 0 }}" 
                         data-stock="{{ $item->stock ?? 0 }}"
                         data-format-name="{{ $item->format_name ?? '' }}">
                        <div class="adidas-cart-product-img-wrap">
                            @if($item->image)
                                <img class="adidas-cart-product-img" src="{{ asset($item->image) }}" alt="{{ $item->title ?? 'Book image' }}">
                            @else
                                <div class="adidas-cart-product-img-placeholder">
                                    <i class="fas fa-book"></i>
                                </div>
                            @endif
                        </div>
                        <div class="adidas-cart-product-info">
                            <div class="adidas-cart-product-header">
                                <h5 class="adidas-cart-product-title">{{ $item->title ?? 'Không có tiêu đề' }}</h5>
                                <button class="adidas-cart-product-remove" data-book-id="{{ $item->book_id }}" title="Xóa sản phẩm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="adidas-cart-product-meta">
                                <span><i class="fas fa-user"></i> {{ $item->author_name ?? 'Chưa cập nhật' }}</span>
                                <span class="format-name"><i class="fas fa-bookmark"></i> {{ $item->format_name ?? 'Chưa cập nhật' }}</span>
                            </div>
                            @if($item->attribute_value_ids && $item->attribute_value_ids !== '[]')
                                @php
                                    $attributeIds = json_decode($item->attribute_value_ids, true);
                                    $attributes = collect();
                                    if ($attributeIds && is_array($attributeIds) && count($attributeIds) > 0) {
                                        $attributes = DB::table('attribute_values')
                                            ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
                                            ->whereIn('attribute_values.id', $attributeIds)
                                            ->select('attributes.name as attr_name', 'attribute_values.value as attr_value')
                                            ->get();
                                    }
                                @endphp
                                @if($attributes->count() > 0)
                                    <div class="adidas-cart-product-attrs">
                                        <small><i class="fas fa-tags"></i> Thuộc tính:</small>
                                        <div>
                                            @foreach($attributes->unique(function($attr) { return $attr->attr_name . ':' . $attr->attr_value; }) as $attr)
                                                <span>{{ $attr->attr_name }}: {{ $attr->attr_value }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <div class="adidas-cart-product-footer">
                                <div class="adidas-cart-product-price">
                                    <small>Đơn giá</small>
                                    <div><i class="fas fa-tag"></i> {{ number_format($item->price ?? 0) }}đ</div>
                                </div>
                                <div class="adidas-cart-product-qty">
                                    <small><i class="fas fa-calculator"></i> Số lượng</small>
                                    @php
                                        $isEbook = isset($item->format_name) && (stripos($item->format_name, 'ebook') !== false);
                                    @endphp
                                    <div class="adidas-cart-qty-control" data-book-id="{{ $item->book_id }}">
                                        @if($isEbook)
                                            <input type="number" class="quantity-input" value="1" min="1" max="1" data-book-id="{{ $item->book_id }}" disabled style="background:#f5f5f5;cursor:not-allowed;width:60px;text-align:center;border:1px solid #ddd;">
                                            <small class="ebook-notice" style="color:#666;font-size:11px;margin-top:2px;display:block;">
                                                <i class="fas fa-info-circle"></i> Sách điện tử
                                            </small>
                                        @else
                                            <button type="button" class="decrease-quantity" data-action="decrease" title="Giảm số lượng" aria-label="Giảm số lượng sản phẩm {{ $item->title ?? 'Sách' }}" {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="quantity-input" value="{{ $item->quantity }}" min="1" max="{{ $item->stock ?? 1 }}" data-book-id="{{ $item->book_id }}" data-original-value="{{ $item->quantity }}" aria-label="Số lượng sản phẩm {{ $item->title ?? 'Sách' }}" autocomplete="off">
                                            <button type="button" class="increase-quantity" data-action="increase" title="Tăng số lượng" aria-label="Tăng số lượng sản phẩm {{ $item->title ?? 'Sách' }}" {{ $item->quantity >= ($item->stock ?? 1) ? 'disabled' : '' }}>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="adidas-cart-product-stock">
                                        <small>
                                            @if($isEbook)
                                                <span><i class="fas fa-infinity"></i> Ebook - Có sẵn</span>
                                            @else
                                                <span><i class="fas fa-boxes"></i> Còn <span>{{ $item->stock ?? 0 }}</span> sản phẩm</span>
                                                @if($item->quantity >= ($item->stock ?? 1))
                                                    <span class="adidas-cart-product-max"><i class="fas fa-exclamation-triangle"></i> Đã đạt tối đa</span>
                                                @endif
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="adidas-cart-product-total">
                                    <small>Thành tiền</small>
                                    <div class="item-total">{{ number_format(($item->price ?? 0) * $item->quantity) }}đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
            <!-- Tổng kết đơn hàng -->
            <div class="adidas-cart-summary">
                <div class="adidas-cart-summary-header">
                    <i class="fas fa-calculator"></i>
                    <h4>Tổng kết đơn hàng</h4>
                </div>
                <div class="adidas-cart-summary-details">
                    <h6>Chi tiết thanh toán</h6>
                    <div class="adidas-cart-summary-row">
                        <span><i class="fas fa-shopping-bag"></i> Tạm tính:</span>
                        <span id="subtotal">{{ number_format($total) }}đ</span>
                    </div>
                    <div class="adidas-cart-summary-row adidas-cart-summary-total">
                        <span><i class="fas fa-coins"></i> Tổng cộng:</span>
                        <span id="total-amount">{{ number_format($total) }}đ</span>
                    </div>
                </div>
                <div class="adidas-cart-summary-checkout">
                    <a href="{{route('orders.checkout')}}" class="adidas-btn adidas-btn-primary">
                        <i class="fas fa-credit-card"></i>
                        <span>Tiến hành thanh toán</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="adidas-cart-summary-safe">
                    <i class="fas fa-shield-alt"></i>
                    <small>Thanh toán an toàn & bảo mật</small>
                    <i class="fas fa-lock"></i>
                </div>
            </div>
        </div>
    @else
        <!-- Modern Empty Cart Section -->
        <div class="adidas-cart-empty">
            <div class="adidas-cart-empty-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2>Giỏ hàng của bạn đang trống</h2>
            <p>Khám phá hàng ngàn cuốn sách hay và thêm chúng vào giỏ hàng của bạn!</p>
            <div class="adidas-cart-empty-actions">
                <a href="{{ route('books.index') }}" class="adidas-btn adidas-btn-primary">
                    <i class="fas fa-book-open"></i> Khám phá sách ngay
                </a>
                <a href="{{ route('wishlist.index') }}" class="adidas-btn adidas-btn-secondary">
                    <i class="fas fa-heart"></i> Thêm từ yêu thích
                </a>
            </div>
            <div class="adidas-cart-empty-suggest">
                <h6>Gợi ý cho bạn</h6>
                <div class="adidas-cart-empty-suggest-list">
                    <div><span><i class="fas fa-fire"></i> Sách hot</span></div>
                    <div><span><i class="fas fa-star"></i> Bán chạy</span></div>
                    <div><span><i class="fas fa-percentage"></i> Giảm giá</span></div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Modular Cart JavaScript Files -->
    <script src="{{ asset('js/cart/cart_base.js') }}"></script>
    <script src="{{ asset('js/cart/cart_summary.js') }}"></script>
    <script src="{{ asset('js/cart/cart_quantity.js') }}"></script>
    <script src="{{ asset('js/cart/cart_products.js') }}"></script>
    <script src="{{ asset('js/cart/cart_voucher.js') }}"></script>
    <script src="{{ asset('js/cart/cart_enhanced_ux.js') }}"></script>
    <script src="{{ asset('js/cart/cart_smart_ux.js') }}"></script>
    <!-- Debug script - remove in production -->
    <script src="{{ asset('js/cart/debug_cart.js') }}"></script>
@endpush