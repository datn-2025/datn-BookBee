@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Log;
@endphp

@section('title', 'Giỏ hàng')

@push('styles')
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-black': '#000000',
                        'brand-white': '#ffffff',
                        'brand-gray': '#767677',
                        'brand-light-gray': '#f4f4f4',
                        'brand-red': '#d71921',
                        'brand-green': '#69be28',
                    },
                    fontFamily: {
                        'brand': ['Roboto', 'Arial', 'sans-serif'],
                    },
                    animation: {
                        'slide-in': 'slideIn 0.3s ease-out',
                        'fade-in': 'fadeIn 0.2s ease-in',
                        'bounce-soft': 'bounceSoft 2s infinite',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <style>
        @keyframes slideIn {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes bounceSoft {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
            60% { transform: translateY(-3px); }
        }
        .cart-hover:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-white py-8 md:py-16 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-64 h-1 bg-black opacity-10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-black opacity-5 transform rotate-45"></div>
        <div class="absolute top-1/2 right-10 w-0.5 h-24 bg-black opacity-20"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-12 animate-fade-in">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="w-12 h-0.5 bg-black"></div>
                <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                    BOOKBEE CART
                </span>
                <div class="w-12 h-0.5 bg-black"></div>
            </div>
            <h1 class="text-4xl md:text-6xl font-black uppercase tracking-tight text-black mb-4">
                GIỎ HÀNG CỦA BẠN
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Quản lý các sản phẩm bạn muốn mua một cách thông minh và hiệu quả
            </p>
        </div>

        @if(count($cart) > 0)
            <!-- Action Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8 p-6 bg-gray-50 border-l-4 border-black">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-black text-white flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold uppercase tracking-wide text-black">
                            {{ count($cart) }} SẢN PHẨM TRONG GIỎ
                        </h4>
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
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-600">
                                <i class="fas fa-mobile-alt"></i> Ebooks + <i class="fas fa-book"></i> Sách vật lý
                            </span>
                        @elseif($hasEbooks)
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-600">
                                <i class="fas fa-mobile-alt"></i> Chỉ Ebooks
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-600">
                                <i class="fas fa-book"></i> Chỉ sách vật lý
                            </span>
                        @endif
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1 text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                <i class="fas fa-sync-alt"></i>
                                Giá được tự động cập nhật theo thời gian thực
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('wishlist.index') }}" class="bg-white border-2 border-black text-black px-6 py-3 font-bold text-sm uppercase tracking-wide hover:bg-black hover:text-white transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-heart"></i> THÊM TỪ YÊU THÍCH
                    </a>
                    <button id="clear-cart-btn" class="bg-red-600 border-2 border-red-600 text-white px-6 py-3 font-bold text-sm uppercase tracking-wide hover:bg-white hover:text-red-600 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i> XÓA TẤT CẢ
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Products List -->
                <div class="lg:col-span-2 space-y-6">
                @foreach($cart as $item)
                    @if($item->is_combo)
                        {{-- Combo Item --}}
                        <div class="cart-hover bg-white border-2 border-gray-200 hover:border-black transition-all duration-300 p-6 cart-item combo-item" 
                             data-cart-id="{{ $item->id }}"
                             data-collection-id="{{ $item->collection_id }}" 
                             data-price="{{ $item->price ?? 0 }}"
                             data-is-combo="true">
                            
                            <!-- Checkbox chọn sản phẩm để mua -->
                            <div class="flex items-center mb-2">
                                <input type="checkbox" class="select-cart-item mr-2" 
                                    data-cart-id="{{ $item->id }}"
                                    {{ $item->is_selected ? 'checked' : '' }}>
                                <span class="text-xs text-gray-500">Chọn để mua</span>
                            </div>

                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Product Image -->
                                <div class="relative group">
                                    @if($item->cover_image)
                                        <img class="w-32 h-40 object-cover" 
                                             src="{{ $item->cover_image ? (str_starts_with($item->cover_image, 'http') ? $item->cover_image : asset('storage/' . $item->cover_image)) : asset('images/default-book.svg') }}" 
                                             alt="{{ $item->title ?? 'Combo image' }}">
                                    @else
                                        <div class="w-32 h-40 bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-layer-group text-3xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    <!-- Combo Badge -->
                                    <div class="absolute -top-2 -left-2 bg-green-600 text-white px-3 py-1 text-xs font-bold uppercase rounded-r">
                                        <i class="fas fa-layer-group mr-1"></i>COMBO
                                    </div>
                                    <!-- Quantity Badge -->
                                    <div class="absolute -top-2 -right-2 bg-black text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                                        {{ $item->quantity }}
                                    </div>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-xl font-bold text-black uppercase tracking-wide">
                                            {{ $item->title ?? 'Combo sách' }}
                                        </h3>
                                        <button class="text-red-600 hover:text-red-800 p-2 cart-product-remove" 
                                                data-collection-id="{{ $item->collection_id }}" 
                                                data-is-combo="true" 
                                                title="Xóa combo">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-3 mb-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <i class="fas fa-layer-group"></i>
                                            <span>{{ $item->author_name ?? 'Combo sách' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <i class="fas fa-gift"></i>
                                            <span>{{ $item->format_name ?? 'Combo sách' }}</span>
                                        </div>
                                    </div>

                                    {{-- Combo Books List --}}
                                    @if(isset($item->combo_books) && count($item->combo_books) > 0)
                                        <div class="bg-gray-50 p-4 border-l-4 border-black mb-4">
                                            <div class="flex items-center gap-2 text-sm font-bold text-black mb-2">
                                                <i class="fas fa-list"></i>
                                                <span>Bao gồm {{ count($item->combo_books) }} cuốn sách:</span>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($item->combo_books as $book)
                                                    <span class="bg-white px-3 py-1 text-xs border border-gray-200 font-medium">
                                                        {{ $book->title }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Price and Quantity -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Price -->
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Đơn giá</span>
                                            @php
                                                $hasComboDiscount = isset($item->original_price) && isset($item->price) && $item->original_price > $item->price;
                                                $comboDiscountAmount = $hasComboDiscount ? ($item->original_price - $item->price) : 0;
                                                $comboDiscountPercent = $hasComboDiscount ? round(($comboDiscountAmount / $item->original_price) * 100) : 0;
                                            @endphp
                                            
                                            @if($hasComboDiscount)
                                                <!-- Giá combo cuối cùng (sau giảm giá) -->
                                                <div class="text-lg font-bold text-green-600">
                                                    {{ number_format($item->price) }}đ
                                                </div>
                                                <!-- Giá combo trước khi giảm -->
                                                <div class="text-sm text-gray-500 line-through">
                                                    Giá combo gốc: {{ number_format($item->original_price) }}đ
                                                </div>
                                                <!-- Mức tiết kiệm combo -->
                                                <div class="text-xs text-green-600 font-bold bg-green-50 px-2 py-1 rounded mt-1">
                                                    <i class="fas fa-gift mr-1"></i>
                                                    Tiết kiệm {{ number_format($comboDiscountAmount) }}đ ({{ $comboDiscountPercent }}%)
                                                </div>
                                            @else
                                                <!-- Giá combo cuối cùng (không giảm thêm) -->
                                                <div class="text-lg font-bold text-green-600">
                                                    {{ number_format($item->price) }}đ
                                                </div>
                                                <div class="text-xs text-green-600 mt-1 bg-green-50 px-2 py-1 rounded">
                                                    <i class="fas fa-layer-group mr-1"></i>
                                                    Giá combo đã ưu đãi
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Quantity -->
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Số lượng</span>
                                            <div class="flex items-center mt-1 cart-qty-control" data-collection-id="{{ $item->collection_id }}">
                                                <button type="button" 
                                                        class="w-10 h-10 bg-black text-white hover:bg-gray-800 transition-colors duration-200 decrease-quantity" 
                                                        data-action="decrease" 
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="w-16 h-10 text-center border-t-2 border-b-2 border-black text-black font-bold quantity-input" 
                                                       value="{{ $item->quantity ?? 1 }}" 
                                                       min="1"
                                                       data-cart-id="{{ $item->id }}"
                                                       data-collection-id="{{ $item->collection_id }}"
                                                       data-last-value="{{ $item->quantity ?? 1 }}"
                                                       data-is-combo="true">
                                                <button type="button" 
                                                        class="w-10 h-10 bg-black text-white hover:bg-gray-800 transition-colors duration-200 increase-quantity" 
                                                        data-action="increase">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Total -->
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Thành tiền</span>
                                            <div class="text-xl font-black text-black item-total">
                                                @php
                                                    $itemTotal = $item->price * $item->quantity;
                                                @endphp
                                                {{ number_format($itemTotal) }}đ
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @php
                            // Price is already calculated with attribute extra price in the database/controller
                            // No need to add extra_price again as it causes duplicate pricing
                            $isEbook = isset($item->format_name) && (stripos($item->format_name, 'ebook') !== false);
                        @endphp
                        
                        {{-- Individual Book Item --}}
                        <div class="cart-hover bg-white border-2 border-gray-200 hover:border-black transition-all duration-300 p-6 cart-item" 
                             data-cart-id="{{ $item->id }}"
                             data-book-id="{{ $item->book_id }}" 
                             data-book-format-id="{{ $item->book_format_id }}"
                             data-attribute-value-ids="{{ $item->attribute_value_ids }}"
                             data-price="{{ $item->price }}" 
                             data-base-price="{{ $item->original_price ?? $item->price }}"
                             data-discount="{{ $item->discount ?? 0 }}"
                             data-extra-price="0"
                             data-stock="{{ $item->stock ?? 0 }}"
                             data-format-name="{{ $item->format_name ?? '' }}"
                             data-is-combo="false">
                            
                            <!-- Checkbox chọn sản phẩm để mua -->
                            <div class="flex items-center mb-2">
                                <input type="checkbox" class="select-cart-item mr-2" 
                                    data-cart-id="{{ $item->id }}"
                                    {{ $item->is_selected ? 'checked' : '' }}>
                                <span class="text-xs text-gray-500">Chọn để mua</span>
                            </div>

                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Product Image -->
                                <div class="relative group">
                                    @if($item->image)
                                        <img class="w-32 h-40 object-cover" 
                                             src="{{ $item->image ? (str_starts_with($item->image, 'http') ? $item->image : asset('storage/' . $item->image)) : asset('images/default-book.svg') }}" 
                                             alt="{{ $item->title ?? 'Book image' }}">
                                    @else
                                        <div class="w-32 h-40 bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-book text-3xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    
                                    @if($isEbook)
                                        <div class="absolute -top-2 -left-2 bg-blue-600 text-white px-3 py-1 text-xs font-bold uppercase">
                                            <i class="fas fa-mobile-alt mr-1"></i>EBOOK
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-xl font-bold text-black uppercase tracking-wide">
                                            {{ $item->title ?? 'Không có tiêu đề' }}
                                        </h3>
                                        <button class="text-red-600 hover:text-red-800 p-2 cart-product-remove" 
                                                data-book-id="{{ $item->book_id }}" 
                                                data-is-combo="false" 
                                                title="Xóa sản phẩm">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="space-y-3 mb-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $item->author_name ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <i class="fas fa-bookmark"></i>
                                            <span>{{ $item->format_name ?? 'Chưa cập nhật' }}</span>
                                        </div>
                                    </div>

                                    {{-- Attributes - Hiển thị đầy đủ thông tin biến thể bao gồm stock và SKU --}}
                                    @if($item->attribute_value_ids && $item->attribute_value_ids !== '[]')
                                        @php
                                            $attributeIds = json_decode($item->attribute_value_ids, true);
                                            $attributes = collect();
                                            // Use the already calculated $attributeExtraPrice
                                            if ($attributeIds && is_array($attributeIds) && count($attributeIds) > 0) {
                                                $query = DB::table('attribute_values')
                                                    ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
                                                    ->join('book_attribute_values', function($join) use ($item) {
                                                        $join->on('attribute_values.id', '=', 'book_attribute_values.attribute_value_id')
                                                             ->where('book_attribute_values.book_id', '=', $item->book_id);
                                                    })
                                                    ->whereIn('attribute_values.id', $attributeIds);
                                                
                                                // Nếu là ebook, chỉ lấy thuộc tính ngôn ngữ
                                                if ($isEbook) {
                                                    $query->where(function($q) {
                                                        $q->where('attributes.name', 'LIKE', '%Ngôn Ngữ%')
                                                          ->orWhere('attributes.name', 'LIKE', '%language%')
                                                          ->orWhere('attributes.name', 'LIKE', '%Language%');
                                                    });
                                                }
                                                
                                                $attributes = $query->select(
                                                    'attributes.name as attr_name', 
                                                    'attribute_values.value as attr_value',
                                                    'book_attribute_values.extra_price',
                                                    'book_attribute_values.stock',
                                                    'book_attribute_values.sku'
                                                )->get();
                                            }
                                        @endphp
                                        @if($attributes->count() > 0)
                                            <div class="bg-gray-50 p-3 border-l-4 border-gray-400 mb-4">
                                                <div class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-2">
                                                    <i class="fas fa-tags"></i> 
                                                    @if($isEbook)
                                                        Ngôn ngữ:
                                                    @else
                                                        Thuộc tính sản phẩm:
                                                    @endif
                                                </div>
                                                <div class="space-y-2">
                                                    @foreach($attributes->unique(function($attr) { return $attr->attr_name . ':' . $attr->attr_value; }) as $attr)
                                                        <div class="flex items-center justify-between bg-white p-2 rounded border">
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-sm font-medium text-gray-800">
                                                                    @if($isEbook)
                                                                        {{ $attr->attr_value }}
                                                                    @else
                                                                        {{ $attr->attr_name }}: <span class="font-bold">{{ $attr->attr_value }}</span>
                                                                    @endif
                                                                </span>
                                                                @if($attr->extra_price > 0)
                                                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">
                                                                        +{{ number_format($attr->extra_price) }}đ
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            
                                                            {{-- Hiển thị stock và SKU cho sách vật lý --}}
                                                            @if(!$isEbook)
                                                                <div class="flex items-center gap-3 text-xs">
                                                                    {{-- SKU --}}
                                                                    @if($attr->sku)
                                                                        <div class="flex items-center gap-1">
                                                                            <i class="fas fa-barcode text-gray-500"></i>
                                                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded font-mono">
                                                                                {{ $attr->sku }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    {{-- Stock --}}
                                                                    <div class="flex items-center gap-1">
                                                                        <i class="fas fa-boxes text-gray-500"></i>
                                                                        <span class="
                                                                            @if($attr->stock > 10) 
                                                                                bg-green-100 text-green-700 
                                                                            @elseif($attr->stock > 0) 
                                                                                bg-yellow-100 text-yellow-700 
                                                                            @else 
                                                                                bg-red-100 text-red-700 
                                                                            @endif
                                                                            px-2 py-1 rounded font-medium">
                                                                            @if($attr->stock > 0)
                                                                                {{ $attr->stock }} có sẵn
                                                                            @else
                                                                                Hết hàng
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    {{-- Gifts --}}
                                    @if(isset($item->gifts) && count($item->gifts) > 0)
                                        <div class="bg-green-50 p-3 border-l-4 border-green-500 mb-4">
                                            <div class="text-xs text-green-600 uppercase tracking-wide font-bold mb-2">
                                                <i class="fas fa-gift"></i> Quà tặng đi kèm:
                                            </div>
                                            <div class="space-y-1">
                                                @foreach($item->gifts as $gift)
                                                    <div class="text-sm">
                                                        <span class="font-medium text-green-800">{{ $gift->name }}</span>
                                                        @if($gift->description)
                                                            <div class="text-xs text-green-600">{{ $gift->description }}</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Price and Quantity -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Price -->
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Đơn giá</span>
                                            @php
                                                $hasDiscount = isset($item->original_price) && isset($item->price) && $item->original_price > $item->price;
                                                $discountAmount = $hasDiscount ? ($item->original_price - $item->price) : 0;
                                                $discountPercent = $hasDiscount ? round(($discountAmount / $item->original_price) * 100) : 0;
                                            @endphp
                                            
                                            @if($hasDiscount)
                                                <!-- Giá cuối cùng (đã bao gồm biến thể + giảm giá) -->
                                                <div class="text-lg font-bold text-red-600">
                                                    {{ number_format($item->price) }}đ
                                                </div>
                                                <!-- Giá trước khi giảm -->
                                                <div class="text-sm text-gray-500 line-through">
                                                    Trước giảm: {{ number_format($item->original_price) }}đ
                                                </div>
                                                <!-- Mức tiết kiệm -->
                                                <div class="text-xs text-green-600 font-bold bg-green-50 px-2 py-1 rounded mt-1">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    Tiết kiệm {{ number_format($discountAmount) }}đ ({{ $discountPercent }}%)
                                                </div>
                                            @else
                                                <!-- Giá cuối cùng (đã bao gồm biến thể, không giảm giá) -->
                                                <div class="text-lg font-bold text-black">
                                                    {{ number_format($item->price) }}đ
                                                </div>
                                                <div class="text-xs text-blue-600 mt-1 bg-blue-50 px-2 py-1 rounded">
                                                    <i class="fas fa-calculator mr-1"></i>
                                                    Giá cuối (đã gồm biến thể)
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Quantity -->
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Số lượng</span>
                                            @if($isEbook)
                                                <div class="mt-1">
                                                    <input type="number" 
                                                           class="w-16 h-10 text-center border-2 border-gray-300 bg-gray-100 text-black font-bold quantity-input" 
                                                           value="1" 
                                                           min="1" 
                                                           max="1" 
                                                           data-cart-id="{{ $item->id }}"
                                                           data-book-id="{{ $item->book_id }}"
                                                           data-last-value="1"
                                                           data-is-ebook="true"
                                                           disabled>
                                                    <div class="text-xs text-gray-500 mt-1 ebook-notice">
                                                        <i class="fas fa-info-circle"></i> Sách điện tử (số lượng cố định)
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center mt-1 cart-qty-control" data-book-id="{{ $item->book_id }}">
                                                    <button type="button" 
                                                            class="w-10 h-10 bg-black text-white hover:bg-gray-800 transition-colors duration-200 decrease-quantity" 
                                                            data-action="decrease" 
                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="w-16 h-10 text-center border-t-2 border-b-2 border-black text-black font-bold quantity-input" 
                                                           value="{{ $item->quantity }}" 
                                                           min="1" 
                                                           max="{{ $item->stock ?? 1 }}" 
                                                           data-cart-id="{{ $item->id }}"
                                                           data-book-id="{{ $item->book_id }}" 
                                                           data-last-value="{{ $item->quantity }}"
                                                           data-is-combo="false">
                                                    <button type="button" 
                                                            class="w-10 h-10 bg-black text-white hover:bg-gray-800 transition-colors duration-200 increase-quantity" 
                                                            data-action="increase" 
                                                            {{ $item->quantity >= ($item->stock ?? 1) ? 'disabled' : '' }}>
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @if($item->stock && $item->quantity >= $item->stock)
                                                        <span class="text-red-600"><i class="fas fa-exclamation-triangle"></i> Đã đạt tối đa</span>
                                                    @elseif($item->stock)
                                                        <span><i class="fas fa-boxes"></i> Còn {{ $item->stock }} sản phẩm</span>
                                                    @else
                                                        <span class="text-orange-600"><i class="fas fa-question-circle"></i> Kiểm tra tồn kho</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Total -->
                                        <div>
                                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Thành tiền</span>
                                            <div class="text-xl font-black text-black item-total">
                                                @php
                                                    $itemTotal = $item->price * $item->quantity;
                                                @endphp
                                                {{ number_format($itemTotal) }}đ
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white border-2 border-black p-6 sticky top-8">
                        <!-- Header -->
                        <div class="border-b-2 border-black pb-4 mb-6">
                            <h4 class="text-2xl font-black uppercase tracking-wide text-black">
                                TỔNG KẾT ĐƠN HÀNG
                            </h4>
                        </div>
                        
                        <!-- Summary Details -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold uppercase tracking-wide text-gray-600">
                                    <i class="fas fa-shopping-bag mr-2"></i>Tạm tính:
                                </span>
                                <span id="subtotal" class="text-lg font-bold text-black">
                                    {{ number_format($total) }}đ
                                </span>
                            </div>
                            
                            <div class="border-t-2 border-black pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-black uppercase tracking-wide text-black">
                                        <i class="fas fa-coins mr-2"></i>TỔNG CỘNG:
                                    </span>
                                    <span id="total-amount" class="text-2xl font-black text-black">
                                        {{ number_format($total) }}đ
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Checkout Button -->
                        <a href="{{ route('orders.checkout') }}" 
                           id="checkout-btn"
                           class="w-full bg-black text-white py-4 px-6 font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-all duration-300 flex items-center justify-center gap-3 group">
                            <i class="fas fa-credit-card"></i>
                            <span>TIẾN HÀNH THANH TOÁN</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform duration-300"></i>
                        </a>
                        
                        <!-- Security Notice -->
                        <div class="mt-4 text-center">
                            <div class="flex items-center justify-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-shield-alt"></i>
                                <span>THANH TOÁN AN TOÀN & BẢO MẬT</span>
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart Section -->
            <div class="text-center py-16">
                <div class="mb-8">
                    <div class="w-32 h-32 bg-gray-100 mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-5xl text-gray-400"></i>
                    </div>
                    <h2 class="text-3xl font-black uppercase tracking-tight text-black mb-4">
                        GIỎ HÀNG TRỐNG
                    </h2>
                    <p class="text-lg text-gray-600 max-w-lg mx-auto mb-8">
                        Khám phá hàng ngàn cuốn sách hay và thêm chúng vào giỏ hàng của bạn!
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('books.index') }}" 
                       class="bg-black text-white px-8 py-4 font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-all duration-300 flex items-center justify-center gap-3">
                        <i class="fas fa-book-open"></i>
                        <span>KHÁM PHÁ SÁCH NGAY</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}" 
                       class="bg-white border-2 border-black text-black px-8 py-4 font-bold text-sm uppercase tracking-wide hover:bg-black hover:text-white transition-all duration-300 flex items-center justify-center gap-3">
                        <i class="fas fa-heart"></i>
                        <span>THÊM TỪ YÊU THÍCH</span>
                    </a>
                </div>

                <!-- Suggestions -->
                <div class="mt-12">
                    <h6 class="text-sm font-bold uppercase tracking-wide text-gray-600 mb-6">GỢI Ý CHO BẠN</h6>
                    <div class="flex flex-wrap justify-center gap-4">
                        <div class="bg-gray-50 px-4 py-2 border-l-4 border-red-600">
                            <span class="text-sm font-bold text-black">
                                <i class="fas fa-fire mr-2 text-red-600"></i>SÁCH HOT
                            </span>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-l-4 border-yellow-600">
                            <span class="text-sm font-bold text-black">
                                <i class="fas fa-star mr-2 text-yellow-600"></i>BÁN CHẠY
                            </span>
                        </div>
                        <div class="bg-gray-50 px-4 py-2 border-l-4 border-green-600">
                            <span class="text-sm font-bold text-black">
                                <i class="fas fa-percentage mr-2 text-green-600"></i>GIẢM GIÁ
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Modular Cart JavaScript Files -->
    {{-- <script src="{{ asset('js/cart/cart_base.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/cart/cart_summary.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/cart/cart_quantity.js') }}"></script> --}}
    <script src="{{ asset('js/cart/cart_products.js') }}"></script>
    <script src="{{ asset('js/cart/cart.js') }}"></script>
    <script src="{{ asset('js/cart/cart_stock_validation.js') }}"></script>
    {{-- <script src="{{ asset('js/cart/cart_voucher.js') }}"></script>
    <script src="{{ asset('js/cart/cart_enhanced_ux.js') }}"></script>
    <script src="{{ asset('js/cart/cart_smart_ux.js') }}"></script> --}}
    <!-- Debug scripts -->
    {{-- <script src="{{ asset('js/cart/debug_cart.js') }}"></script>
    @if(config('app.debug'))
        <script src="{{ asset('js/cart/cart_debug_test.js') }}"></script>
    @endif --}}
@endpush
