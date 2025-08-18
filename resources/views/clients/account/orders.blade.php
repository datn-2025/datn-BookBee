@extends('layouts.app')
@section('title', 'Quản lý đơn hàng')

@push('styles')
<style>
    .order-tab-active {
        background-color: #000;
        color: #fff;
        border-bottom: 3px solid #000;
    }
    .order-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .order-card:hover {
        border-color: #000;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .review-form .star-rating label:hover,
    .review-form .star-rating input:checked ~ label {
        color: #f59e0b;
    }
    .geometric-bg::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(0,0,0,0.05);
        transform: rotate(45deg) translate(50px, -50px);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section - Adidas Style -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 geometric-bg">
            <div class="bg-black text-white px-8 py-6 relative">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-1 h-8 bg-white"></div>
                        <h1 class="text-3xl font-black uppercase tracking-wide">QUẢN LÝ ĐƠN HÀNG</h1>
                    </div>
                    <p class="text-gray-300 text-sm uppercase tracking-wider">THEO DÕI VÀ QUẢN LÝ TẤT CẢ ĐƠN HÀNG CỦA BẠN</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs - Enhanced Adidas Style -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 mb-8">
            <div class="px-8 py-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-1 h-6 bg-black"></div>
                    <h2 class="text-lg font-black uppercase tracking-wide text-black">LỌC THEO TRẠNG THÁI</h2>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @php
                        $statuses = [
                            'all' => 'Tất cả',
                            'pending' => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'preparing' => 'Đang chuẩn bị',
                            'shipping' => 'Đang giao / Đã giao',
                            'delivered' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ];
                    @endphp
                    @foreach($statuses as $statusKey => $label)
                        <a href="{{ route('account.orders.unified', ['status' => $statusKey]) }}"
                           class="px-6 py-3 text-sm font-bold uppercase tracking-wider border-2 transition-all duration-300 
                                  {{ request('status', 'all') == $statusKey ? 'order-tab-active border-black' : 'border-gray-300 text-gray-700 hover:border-black hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="space-y-6">
            @forelse($orders as $order)
                <div class="bg-white order-card border-2 border-gray-200 relative overflow-hidden">
                    <!-- Order Header -->
                    <div class="bg-gray-50 border-b-2 border-gray-200 px-8 py-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex items-center gap-6">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-1 h-6 bg-black"></div>
                                        <h3 class="text-xl font-black uppercase tracking-wide text-black">
                                        ĐƠN HÀNG #{{ $order->order_code }}
                                        @if($order->delivery_method === 'mixed')
                                        <span class="ml-2 px-2 py-1 bg-yellow-500 text-black text-xs font-bold uppercase tracking-wide rounded">
                                            HỖN HỢP
                                        </span>
                                        @endif
                                        @if($order->isParentOrder())
                                        <span class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs font-bold uppercase tracking-wide rounded">
                                            ĐƠN HÀNG CHA
                                        </span>
                                        @endif
                                    </h3>
                                    </div>
                                    <p class="text-sm text-gray-600 uppercase tracking-wide">
                                        Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                @if($order->refundRequests->isNotEmpty())
                                    @php 
                                        $latestRefund = $order->refundRequests->sortByDesc('created_at')->first();
                                        $refundStatusClass = match($latestRefund->status) {
                                            'pending' => 'bg-yellow-500 text-white',
                                            'processing' => 'bg-blue-500 text-white',
                                            'completed' => 'bg-green-500 text-white',
                                            'rejected' => 'bg-red-500 text-white',
                                            default => 'bg-gray-500 text-white'
                                        };
                                        $refundStatusText = match($latestRefund->status) {
                                            'pending' => 'CHỜ HOÀN TIỀN',
                                            'processing' => 'ĐANG HOÀN TIỀN',
                                            'completed' => 'ĐÃ HOÀN TIỀN',
                                            'rejected' => 'TỪ CHỐI HOÀN TIỀN',
                                            default => 'HOÀN TIỀN'
                                        };
                                    @endphp
                                    <span class="status-badge {{ $refundStatusClass }}">
                                        {{ $refundStatusText }}
                                    </span>
                                @else
                                    @php
                                        $orderStatusName = $order->orderStatus->name ?? '';
                                        $orderStatusClass = match($orderStatusName) {
                                            'Chờ xác nhận' => 'bg-yellow-500 text-white',
                                            'Đã xác nhận' => 'bg-blue-500 text-white',
                                            'Đang chuẩn bị' => 'bg-indigo-500 text-white',
                                            'Đang đóng gói' => 'bg-orange-500 text-white',
                                            'Đang giao hàng' => 'bg-purple-500 text-white',
                                            'Đã giao hàng' => 'bg-green-500 text-white',
                                            'Đã giao thành công' => 'bg-green-500 text-white',
                                            'Đã giao', 'Thành công' => 'bg-green-500 text-white',
                                            'Đã hủy' => 'bg-red-500 text-white',
                                            'Hoàn trả' => 'bg-gray-500 text-white',
                                            default => 'bg-gray-500 text-white'
                                        };
                                    @endphp
                                        @if ($order->orderStatus->name !== "Đã giao thành công")
                                        <span class="status-badge {{ $orderStatusClass }}">
                                                {{ $order->orderStatus->name }}
                                            </span>
                                        @endif
                                    @if($order->orderStatus->name === 'Đã giao thành công')
<form action="{{ route('account.orders.confirm-received', $order->id) }}" method="POST" class="inline-block ml-2">
    @csrf
    <button type="submit" 
            class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded hover:bg-blue-600 transition-colors"
            onclick="return confirm('Bạn có chắc chắn đã nhận được hàng? Hành động này không thể hoàn tác.')">
        ĐÃ NHẬN HÀNG
    </button>
</form>
                                    @endif
                                @endif
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 uppercase tracking-wide">Tổng tiền</p>
                                    <p class="text-2xl font-black text-black">
                                        {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                    </p>
                                    @if($order->isParentOrder())
                                    <button type="button" class="mt-2 px-3 py-1 bg-black text-white text-xs font-bold uppercase tracking-wide rounded hover:bg-gray-800 transition-colors" onclick="toggleChildOrders('{{ $order->id }}')">
                                        <span id="toggle-text-{{ $order->id }}">XEM CHI TIẾT</span>
                                        <svg id="toggle-icon-{{ $order->id }}" class="inline-block w-3 h-3 ml-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Content -->
                    <div class="p-8">
                        @if($order->isParentOrder())
                            <!-- Child Orders Section -->
                            <div id="child-orders-{{ $order->id }}" class="hidden mb-8">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-1 h-5 bg-blue-500"></div>
                                    <h4 class="text-base font-bold uppercase tracking-wide text-black">ĐƠN HÀNG CON</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    @foreach($order->childOrders as $childOrder)
                                    <div class="bg-gray-50 border-2 border-gray-200 p-6">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                                            <div>
                                                <h5 class="text-lg font-bold text-black mb-2">
                                                    #{{ $childOrder->order_code }}
                                                    <span class="ml-2 px-2 py-1 bg-gray-500 text-white text-xs font-bold uppercase tracking-wide rounded">
                                                        {{ $childOrder->delivery_method === 'pickup' ? 'NHẬN TẠI CỬA HÀNG' : ($childOrder->delivery_method === 'ebook' ? 'EBOOK' : 'GIAO HÀNG') }}
                                                    </span>
                                                </h5>
                                                <p class="text-sm text-gray-600">{{ $childOrder->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                @php
                                                    $childOrderStatusName = $childOrder->orderStatus->name ?? '';
                                                    $childOrderStatusClass = match($childOrderStatusName) {
                                                        'Chờ xác nhận' => 'bg-yellow-500 text-white',
                                                        'Đã xác nhận' => 'bg-blue-500 text-white',
                                                        'Đang chuẩn bị' => 'bg-indigo-500 text-white',
                                                        'Đang đóng gói' => 'bg-orange-500 text-white',
                                                        'Đang giao hàng' => 'bg-purple-500 text-white',
                                                        'Đã giao hàng' => 'bg-green-500 text-white',
                                                        'Đã giao thành công' => 'bg-green-500 text-white',
                                                        'Đã giao', 'Thành công' => 'bg-green-500 text-white',
                                                        'Đã hủy' => 'bg-red-500 text-white',
                                                        'Hoàn trả' => 'bg-gray-500 text-white',
                                                        default => 'bg-gray-500 text-white'
                                                    };
                                                @endphp
                                                <span class="status-badge {{ $childOrderStatusClass }}">
                                                    {{ $childOrder->orderStatus->name }}
                                                </span>
                                                @if($childOrder->orderStatus->name === 'Đã giao thành công')
                                                    <form action="{{ route('account.orders.confirm-received', $childOrder->id) }}" method="POST" class="inline-block ml-2">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-4 py-2 border-2 border-black text-black hover:bg-black hover:text-white font-bold text-xs uppercase tracking-wide transition-all duration-300"
                                                                onclick="return confirm('Bạn có chắc chắn đã nhận được hàng? Hành động này không thể hoàn tác.')">
                                                            ĐÃ NHẬN HÀNG
                                                        </button>
                                                    </form>
                                                @endif
                                                <div class="text-right">
                                                    <p class="text-lg font-bold text-black">
                                                        {{ number_format($childOrder->total_amount, 0, ',', '.') }}đ
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Child Order Products -->
                                        @if($childOrder->orderItems->count() > 0)
                                        <div class="mt-6 border-t border-gray-300 pt-4">
                                            <h6 class="text-sm font-bold uppercase tracking-wide text-black mb-4">SẢN PHẨM ({{ $childOrder->orderItems->sum('quantity') }} sản phẩm)</h6>
                                            <div class="space-y-6">
                                                @foreach($childOrder->orderItems as $item)
                                                <div class="flex flex-col lg:flex-row gap-6 p-6 border-2 border-gray-200 hover:border-black transition-all duration-300">
                                                    <!-- Product Image -->
                                                    <div class="flex-shrink-0">
                                                        <div class="w-24 h-32 bg-gray-200 border-2 border-gray-300 overflow-hidden">
                                                            @if($item->isCombo())
                                                                @if($item->collection && $item->collection->cover_image)
                                                                    <img src="{{ asset('storage/' . $item->collection->cover_image) }}" 
                                                                         alt="{{ $item->collection->name }}" 
                                                                         class="w-full h-full object-cover">
                                                                @else
                                                                    <div class="w-full h-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                                                                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                @if($item->book && $item->book->cover_image)
                                                                    <img src="{{ asset('storage/' . $item->book->cover_image) }}" 
                                                                         alt="{{ $item->book->title }}" 
                                                                         class="w-full h-full object-cover">
                                                                @else
                                                                    <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Product Info -->
                                                    <div class="flex-1">
                                                        @if($item->isCombo())
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase tracking-wide bg-purple-100 text-purple-800 border border-purple-200">
                                                                    COMBO
                                                                </span>
                                                            </div>
                                                            <h5 class="text-lg font-bold text-black mb-2">
                                                                {{ $item->collection->name ?? 'Combo không xác định' }}
                                                            </h5>
                                                        @else
                                                            <h5 class="text-lg font-bold text-black mb-2">
                                                                {{ $item->book->title ?? 'Sách không xác định' }}
                                                            </h5>
                                                            @if($item->bookFormat)
                                                                <p class="text-sm text-gray-600 uppercase tracking-wide mb-2">
                                                                    Định dạng: {{ $item->bookFormat->format_name }}
                                                                </p>
                                                            @endif
                                                            
                                                            @if(!$item->isCombo() && $item->attributeValues && $item->attributeValues->count() > 0)
                                                                <div class="space-y-1 mb-2">
                                                                    @foreach($item->attributeValues as $attributeValue)
                                                                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                                                                            <span class="font-semibold">{{ $attributeValue->attribute->name ?? 'Thuộc tính' }}:</span> 
                                                                            {{ $attributeValue->value }}
                                                                        </p>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                            
                                                            <!-- Hiển thị quà tặng -->
                                                            @if(!$item->isCombo() && $item->book && $item->book->gifts && $item->book->gifts->count() > 0 && $item->bookFormat && $item->bookFormat->format_name !== 'Ebook')
                                                                <div class="mb-2">
                                                                    <div class="flex items-center gap-2 mb-1">
                                                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                                                        </svg>
                                                                        <span class="text-sm font-bold text-red-600 uppercase tracking-wide">Quà tặng:</span>
                                                                    </div>
                                                                    <div class="space-y-1">
                                                                        @foreach($item->book->gifts as $gift)
                                                                            <div class="flex items-center gap-2 p-2 bg-red-50 border border-red-200 rounded text-sm">
                                                                                @if($gift->gift_image)
                                                                                    <img src="{{ asset('storage/' . $gift->gift_image) }}" 
                                                                                         alt="{{ $gift->gift_name }}" 
                                                                                         class="w-6 h-6 object-cover rounded">
                                                                                @endif
                                                                                <span class="text-red-800 font-medium">{{ $gift->gift_name }}</span>
                                                                                <span class="text-red-600 text-xs">(x{{ $item->quantity }})</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                        
                                                        <div class="flex items-center gap-6 text-sm">
                                                            <span class="text-gray-600 uppercase tracking-wide">Số lượng: <span class="font-bold text-black">{{ $item->quantity }}</span></span>
                                                            <span class="text-gray-600 uppercase tracking-wide">Đơn giá: <span class="font-bold text-black">{{ number_format($item->price) }}đ</span></span>
                                                            <span class="text-gray-600 uppercase tracking-wide">Thành tiền: <span class="font-bold text-black">{{ number_format($item->price * $item->quantity) }}đ</span></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Review Section for Child Order -->
                                                    @if(in_array($childOrder->orderStatus->name, ['Đã giao', 'Thành công']))
                                                        <div class="lg:w-80 flex-shrink-0">
                                                            @php
                                                                // Kiểm tra đánh giá cho combo hoặc sách trong đơn hàng con
                                                                if ($item->isCombo()) {
                                                                    $review = $childOrder->reviews()->withTrashed()->where('collection_id', $item->collection_id)->first();
                                                                } else {
                                                                    $review = $childOrder->reviews()->withTrashed()->where('book_id', $item->book_id)->first();
                                                                }
                                                            @endphp

                                                            @if($review && !$review->trashed())
                                                                <div class="bg-gray-50 border-2 border-gray-200 p-4">
                                                                    <div class="flex items-center gap-2 mb-2">
                                                                        <div class="w-1 h-4 bg-green-500"></div>
                                                                        <h6 class="font-bold text-sm uppercase tracking-wide text-black">ĐÁNH GIÁ CỦA BẠN</h6>
                                                                    </div>
                                                                    <div class="flex items-center text-yellow-400 mb-2">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <p class="text-sm text-gray-600 italic">"{{ $review->comment ?? 'Không có nhận xét' }}"</p>
                                                                    @if($review->admin_response)
                                                                        <div class="mt-3 pt-3 border-t border-gray-300">
                                                                            <p class="text-xs font-bold uppercase tracking-wide text-black mb-1">Phản hồi từ BookBee:</p>
                                                                            <p class="text-sm text-gray-600">{{ $review->admin_response }}</p>
                                                                        </div>
                                                                    @endif
                                                                    <div class="flex gap-2 mt-3">
                                                                        @if ($review->user_id === auth()->id())
                                                                            <a href="{{ route('account.reviews.edit', $review->id) }}" 
                                                                               class="px-3 py-1 bg-black text-white text-xs font-medium hover:bg-gray-900 transition-colors duration-150">
                                                                                Sửa đánh giá
                                                                            </a>
                                                                            <form action="{{ route('account.reviews.destroy', $review->id) }}" method="POST" 
                                                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');" class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" 
                                                                                        class="px-3 py-1 bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition-colors duration-150">
                                                                                    Xóa
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <form action="{{ route('account.reviews.store') }}" method="POST" class="space-y-4 review-form bg-gray-50 border-2 border-gray-200 p-4 quick-review-form">
                                                                    @csrf
                                                                    <input type="hidden" name="order_id" value="{{ $childOrder->id }}">
                                                                    @if($item->isCombo())
                                                                        <input type="hidden" name="collection_id" value="{{ $item->collection_id }}">
                                                                    @else
                                                                        <input type="hidden" name="book_id" value="{{ $item->book_id }}">
                                                                    @endif
                                                                    
                                                                    <div class="flex items-center gap-2 mb-3">
                                                                        <div class="w-1 h-4 bg-blue-500"></div>
                                                                        <h6 class="font-bold text-sm uppercase tracking-wide text-black">
                                                                            ĐÁNH GIÁ {{ $item->isCombo() ? 'COMBO' : 'SẢN PHẨM' }}
                                                                        </h6>
                                                                    </div>
                                                                    
                                                                    <div class="star-rating flex flex-row-reverse justify-end items-center gap-1 quick-star-group" data-order="{{ $childOrder->id }}" data-item="{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}">
                                                                        @for($i = 5; $i >= 1; $i--)
                                                                            <input type="radio" id="child-star-{{$childOrder->id}}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}-{{ $i }}" name="rating" value="{{ $i }}" class="sr-only" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                                                            <label for="child-star-{{$childOrder->id}}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}-{{ $i }}" class="text-gray-300 text-2xl cursor-pointer transition-colors hover:text-yellow-400 quick-star-label" data-star="{{ $i }}">★</label>
                                                                        @endfor
                                                                    </div>
                                                                    
                                                                    <div>
                                                                        <textarea name="comment" rows="4" 
                                                                                  class="w-full px-4 py-3 border-2 border-gray-300 focus:border-black focus:outline-none text-sm" 
                                                                                  placeholder="Chia sẻ cảm nhận của bạn về {{ $item->isCombo() ? 'combo' : 'sản phẩm' }} này...">{{ old('comment') }}</textarea>
                                                                    </div>
                                                                    
                                                                    <button type="submit" 
                                                                            class="w-full px-6 py-3 bg-black hover:bg-gray-800 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                                                        GỬI ĐÁNH GIÁ
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <!-- Child Order Actions -->
                                        <div class="flex gap-2 mt-4">
                                            <a href="{{ route('orders.show', $childOrder->id) }}" 
                                               class="inline-flex items-center px-4 py-2 border-2 border-black text-black hover:bg-black hover:text-white font-bold text-xs uppercase tracking-wide transition-all duration-300">
                                                XEM CHI TIẾT
                                            </a>
                                            
                                            @if(\App\Helpers\OrderStatusHelper::canBeCancelled($childOrder->orderStatus->name))
                                                <form action="{{ route('account.orders.cancel', $childOrder->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-4 py-2 border-2 border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold text-xs uppercase tracking-wide transition-all duration-300"
                                                            onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                                        HỦY ĐƠN HÀNG
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- Order Info Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            <!-- Order Details -->
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-1 h-5 bg-black"></div>
                                    <h4 class="text-base font-bold uppercase tracking-wide text-black">THÔNG TIN ĐƠN HÀNG</h4>
                                </div>
                                
                                @if($order->delivery_method === 'mixed')
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4">
                                    <h5 class="font-bold text-blue-800 text-xs mb-1">📦 ĐƠN HÀNG ĐÃ ĐƯỢC CHIA THÀNH 2 PHẦN</h5>
                                    <p class="text-xs text-blue-600">Sách vật lý sẽ được giao hàng, ebook sẽ được gửi qua email</p>
                                </div>
                                @endif
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 uppercase tracking-wide">Phương thức thanh toán:</span>
                                        <span class="font-bold text-black">{{ $order->paymentMethod->name ?? 'Không xác định' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 uppercase tracking-wide">Trạng thái thanh toán:</span>
                                        @if($order->refundRequests->isNotEmpty())
                                            @php $latestRefund = $order->refundRequests->sortByDesc('created_at')->first(); @endphp
                                            <span class="font-bold 
                                                @if($latestRefund->status === 'pending') text-yellow-600
                                                @elseif($latestRefund->status === 'processing') text-blue-600
                                                @elseif($latestRefund->status === 'completed') text-green-600
                                                @elseif($latestRefund->status === 'rejected') text-red-600
                                                @else text-black
                                                @endif">
                                                @if($latestRefund->status === 'pending') ĐANG CHỜ HOÀN TIỀN
                                                @elseif($latestRefund->status === 'processing') ĐANG XỬ LÝ HOÀN TIỀN
                                                @elseif($latestRefund->status === 'completed') ĐÃ HOÀN TIỀN
                                                @elseif($latestRefund->status === 'rejected') TỪ CHỐI HOÀN TIỀN
                                                @endif
                                            </span>
                                        @else
                                            @php
                                                $paymentStatusName = $order->paymentStatus->name ?? 'Chưa thanh toán';
                                                $paymentStatusClass = match($paymentStatusName) {
                                                    'Đã Thanh Toán' => 'text-green-600 font-bold',
                                                    'Chờ Thanh Toán', 'Chờ Xử Lý' => 'text-yellow-600 font-bold',
                                                    'Đang Xử Lý' => 'text-blue-600 font-bold',
                                                    'Thất Bại' => 'text-red-600 font-bold',
                                                    'Chưa thanh toán' => 'text-gray-600 font-bold',
                                                    default => 'text-black font-bold'
                                                };
                                            @endphp
                                            <span class="{{ $paymentStatusClass }}">{{ $paymentStatusName }}</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 uppercase tracking-wide">Phí vận chuyển:</span>
                                        <span class="font-bold text-black">{{ number_format($order->shipping_fee ?? 0) }}đ</span>
                                    </div>
                                    
                                    @if($order->refundRequests->isNotEmpty())
                                        @php $latestRefund = $order->refundRequests->sortByDesc('created_at')->first(); @endphp
                                        <div class="mt-4 p-3 border-l-4 
                                            @if($latestRefund->status === 'pending') border-yellow-500 bg-yellow-50
                                            @elseif($latestRefund->status === 'processing') border-blue-500 bg-blue-50
                                            @elseif($latestRefund->status === 'completed') border-green-500 bg-green-50
                                            @elseif($latestRefund->status === 'rejected') border-red-500 bg-red-50
                                            @endif">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-1 h-4 
                                                    @if($latestRefund->status === 'pending') bg-yellow-500
                                                    @elseif($latestRefund->status === 'processing') bg-blue-500
                                                    @elseif($latestRefund->status === 'completed') bg-green-500
                                                    @elseif($latestRefund->status === 'rejected') bg-red-500
                                                    @endif"></div>
                                                <h5 class="font-bold text-xs uppercase tracking-wide 
                                                    @if($latestRefund->status === 'pending') text-yellow-800
                                                    @elseif($latestRefund->status === 'processing') text-blue-800
                                                    @elseif($latestRefund->status === 'completed') text-green-800
                                                    @elseif($latestRefund->status === 'rejected') text-red-800
                                                    @endif">YÊU CẦU HOÀN TIỀN</h5>
                                            </div>
                                            <div class="space-y-1 text-xs">
                                                <p><span class="font-bold">Số tiền:</span> {{ number_format($latestRefund->amount, 0, ',', '.') }}đ</p>
                                                <p><span class="font-bold">Ngày yêu cầu:</span> {{ $latestRefund->created_at->format('d/m/Y H:i') }}</p>
                                                @if($latestRefund->processed_at)
                                                    <p><span class="font-bold">Ngày xử lý:</span> {{ $latestRefund->processed_at->format('d/m/Y H:i') }}</p>
                                                @endif
                                                @if($latestRefund->admin_note)
                                                    <p><span class="font-bold">Ghi chú:</span> {{ $latestRefund->admin_note }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-1 h-5 bg-black"></div>
                                    <h4 class="text-base font-bold uppercase tracking-wide text-black">
                                        {{ $order->delivery_method === 'pickup' ? 'THÔNG TIN NHẬN HÀNG' : 'ĐỊA CHỈ GIAO HÀNG' }}
                                    </h4>
                                </div>
                                <div class="space-y-2 text-sm">
                                    @if($order->delivery_method === 'pickup')
                                        <p><span class="font-bold text-black">Phương thức:</span> Nhận tại cửa hàng</p>
                                        <p><span class="font-bold text-black">Người nhận:</span> {{ $order->recipient_name ?? 'Không có thông tin' }}</p>
                                        <p><span class="font-bold text-black">Số điện thoại:</span> {{ $order->recipient_phone ?? '' }}</p>
                                        <p><span class="font-bold text-black">Địa chỉ cửa hàng:</span> 
                                            @if(isset($storeSettings) && $storeSettings->address)
                                                {{ $storeSettings->address }}
                                            @else
                                                123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                                            @endif
                                        </p>
                                    @elseif($order->delivery_method === 'ebook')
                                        <p><span class="font-bold text-black">Phương thức:</span> Sách điện tử (Ebook)</p>
                                        <p class="font-bold text-black">{{ $order->recipient_name ?? 'Không có thông tin' }}</p>
                                        <p>{{ $order->recipient_email ?? '' }}</p>
                                        <p class="text-sm text-gray-600">Link tải ebook đã được gửi qua email</p>
                                    @else
                                        <p><span class="font-bold text-black">Phương thức:</span> Giao hàng tận nơi</p>
                                        <p class="font-bold text-black">{{ $order->recipient_name ?? 'Không có thông tin' }}</p>
                                        <p>{{ $order->recipient_phone ?? '' }}</p>
                                        @if($order->address)
                                        <p>{{ $order->address->address_detail ?? '' }}</p>
                                        <p>{{ $order->address->ward ?? '' }}, {{ $order->address->district ?? '' }}, {{ $order->address->city ?? '' }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        @if(!$order->isParentOrder() && $order->orderItems->count() > 0)
                        <div class="border-t-2 border-gray-200 pt-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-1 h-5 bg-black"></div>
                                <h4 class="text-base font-bold uppercase tracking-wide text-black">SẢN PHẨM ĐÃ ĐẶT ({{ $order->orderItems->sum('quantity') }} sản phẩm)</h4>
                            </div>
                            
                                <div class="space-y-6">
                                    @foreach($order->orderItems as $item)
                                    <div class="flex flex-col lg:flex-row gap-6 p-6 border-2 border-gray-200 hover:border-black transition-all duration-300">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <div class="w-24 h-32 bg-gray-200 border-2 border-gray-300 overflow-hidden">
                                                @if($item->isCombo())
                                                    @php
                                                        $comboImageUrl = asset('images/default-book.jpg');
                                                        if ($item->collection && $item->collection->cover_image) {
                                                            $comboImageUrl = asset('storage/' . $item->collection->cover_image);
                                                        }
                                                    @endphp
                                                    <img src="{{ $comboImageUrl }}" 
                                                         alt="{{ $item->collection ? $item->collection->name : 'Combo không tồn tại' }}" 
                                                         class="w-full h-full object-cover"
                                                         onerror="this.src='{{ asset('images/default-book.jpg') }}'; this.onerror=null;">
                                                @else
                                                    @php
                                                        $bookImageUrl = asset('images/default-book.jpg');
                                                        if ($item->book && $item->book->cover_image) {
                                                            $bookImageUrl = asset('storage/' . $item->book->cover_image);
                                                        } elseif ($item->book && $item->book->images && $item->book->images->isNotEmpty()) {
                                                            $bookImageUrl = asset('storage/' . $item->book->images->first()->image_url);
                                                        }
                                                    @endphp
                                                    <img src="{{ $bookImageUrl }}" 
                                                         alt="{{ $item->book ? $item->book->title : 'Sản phẩm không tồn tại' }}" 
                                                         class="w-full h-full object-cover"
                                                         onerror="this.src='{{ asset('images/default-book.jpg') }}'; this.onerror=null;">
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1">
                                            @if($item->isCombo())
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase tracking-wide bg-purple-100 text-purple-800 border border-purple-200">
                                                        COMBO
                                                    </span>
                                                </div>
                                                <h5 class="text-lg font-bold text-black mb-2">
                                                    {{ $item->collection->name ?? 'Combo không xác định' }}
                                                </h5>
                                            @else
                                                <h5 class="text-lg font-bold text-black mb-2">
                                                    {{ $item->book->title ?? 'Sách không xác định' }}
                                                </h5>
                                                @if($item->bookFormat)
                                                    <p class="text-sm text-gray-600 uppercase tracking-wide mb-2">
                                                        Định dạng: {{ $item->bookFormat->format_name }}
                                                    </p>
                                                @endif
                                                
                                                @if(!$item->isCombo() && $item->attributeValues && $item->attributeValues->count() > 0)
                                                    <div class="space-y-1 mb-2">
                                                        @foreach($item->attributeValues as $attributeValue)
                                                            <p class="text-sm text-gray-600 uppercase tracking-wide">
                                                                <span class="font-semibold">{{ $attributeValue->attribute->name ?? 'Thuộc tính' }}:</span> 
                                                                {{ $attributeValue->value }}
                                                            </p>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                
                                                <!-- Hiển thị quà tặng -->
                                                @if(!$item->isCombo() && $item->book && $item->book->gifts && $item->book->gifts->count() > 0 && $item->bookFormat && $item->bookFormat->format_name !== 'Ebook')
                                                    <div class="mb-2">
                                                        <p class="text-sm font-semibold text-red-600 uppercase tracking-wide mb-1">🎁 Quà tặng kèm:</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach($item->book->gifts as $gift)
                                                                <div class="flex items-center gap-2 px-2 py-1 bg-red-50 text-red-700 text-xs font-medium rounded border border-red-200">
                                                                    @if($gift->image)
                                                                        <img src="{{ asset('storage/' . $gift->image) }}" 
                                                                             alt="{{ $gift->name }}" 
                                                                             class="w-4 h-4 object-cover rounded">
                                                                    @endif
                                                                    <span>{{ $gift->name }}</span>
                                                                    <span class="text-red-500">x{{ $gift->pivot->quantity ?? 1 }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            
                                            <div class="flex items-center gap-6 text-sm">
                                                <span class="text-gray-600 uppercase tracking-wide">Số lượng: <span class="font-bold text-black">{{ $item->quantity }}</span></span>
                                                <span class="text-gray-600 uppercase tracking-wide">Đơn giá: <span class="font-bold text-black">{{ number_format($item->price) }}đ</span></span>
                                                <span class="text-gray-600 uppercase tracking-wide">Thành tiền: <span class="font-bold text-black">{{ number_format($item->price * $item->quantity) }}đ</span></span>
                                            </div>
                                        </div>

                                        <!-- Review Section -->
                                        @if(in_array($order->orderStatus->name, ['Đã giao', 'Thành công']))
                                            <div class="lg:w-80 flex-shrink-0">
                                                @php
                                                    // Kiểm tra đánh giá cho combo hoặc sách
                                                    if ($item->isCombo()) {
                                                        $review = $order->reviews()->withTrashed()->where('collection_id', $item->collection_id)->first();
                                                    } else {
                                                        $review = $order->reviews()->withTrashed()->where('book_id', $item->book_id)->first();
                                                    }
                                                    
                                                    // Kiểm tra xem đơn hàng có yêu cầu hoàn tiền không
                                                    $hasRefundRequest = $order->refundRequests()->whereIn('status', ['pending', 'processing'])->exists();
                                                @endphp

                                                @if($review && !$review->trashed())
                                                    <div class="bg-gray-50 border-2 border-gray-200 p-4">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <div class="w-1 h-4 bg-green-500"></div>
                                                            <h6 class="font-bold text-sm uppercase tracking-wide text-black">ĐÁNH GIÁ CỦA BẠN</h6>
                                                        </div>
                                                        <div class="flex items-center text-yellow-400 mb-2">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <p class="text-sm text-gray-600 italic">"{{ $review->comment ?? 'Không có nhận xét' }}"</p>
                                                        
                                                        @php
                                                            $reviewImages = $review->images;
                                                            if (is_string($reviewImages)) {
                                                                $reviewImages = json_decode($reviewImages, true) ?? [];
                                                            }
                                                            $reviewImages = is_array($reviewImages) ? $reviewImages : [];
                                                        @endphp
                                                        @if(!empty($reviewImages))
                                                            <div class="mt-3">
                                                                <p class="text-xs font-bold uppercase tracking-wide text-black mb-2">Hình ảnh đánh giá:</p>
                                                                <div class="grid grid-cols-3 gap-2">
                                                                    @foreach($reviewImages as $imagePath)
                                                                        <img src="{{ asset('storage/' . $imagePath) }}" alt="Review Image" class="w-full h-16 object-cover border border-gray-300 rounded cursor-pointer hover:opacity-80 transition-opacity" onclick="openImageModal('{{ asset('storage/' . $imagePath) }}')">
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        @if($review->admin_response)
                                                            <div class="mt-3 pt-3 border-t border-gray-300">
                                                                <p class="text-xs font-bold uppercase tracking-wide text-black mb-1">Phản hồi từ BookBee:</p>
                                                                <p class="text-sm text-gray-600">{{ $review->admin_response }}</p>
                                                            </div>
                                                        @endif
                                                        <div class="flex gap-2 mt-3">
                                                            @if ($review->user_id === auth()->id())
                                                                <a href="{{ route('account.reviews.edit', $review->id) }}" 
                                                                   class="px-3 py-1 bg-black text-white text-xs font-medium hover:bg-gray-900 transition-colors duration-150">
                                                                    Sửa đánh giá
                                                                </a>
                                                                <form action="{{ route('account.reviews.destroy', $review->id) }}" method="POST" 
                                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');" class="inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="px-3 py-1 bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition-colors duration-150">
                                                                        Xóa
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif(!$hasRefundRequest)
                                                    <form action="{{ route('account.reviews.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 review-form bg-gray-50 border-2 border-gray-200 p-4 quick-review-form">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        @if($item->isCombo())
                                                            <input type="hidden" name="collection_id" value="{{ $item->collection_id }}">
                                                        @else
                                                            <input type="hidden" name="book_id" value="{{ $item->book_id }}">
                                                        @endif
                                                        
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-1 h-4 bg-blue-500"></div>
                                                            <h6 class="font-bold text-sm uppercase tracking-wide text-black">
                                                                ĐÁNH GIÁ {{ $item->isCombo() ? 'COMBO' : 'SẢN PHẨM' }}
                                                            </h6>
                                                        </div>
                                                        
                                                        <div class="star-rating flex flex-row-reverse justify-end items-center gap-1 quick-star-group" data-order="{{ $order->id }}" data-item="{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}">
                                                            @for($i = 5; $i >= 1; $i--)
                                                                <input type="radio" id="star-{{$order->id}}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}-{{ $i }}" name="rating" value="{{ $i }}" class="sr-only" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                                                <label for="star-{{$order->id}}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}-{{ $i }}" class="text-gray-300 text-2xl cursor-pointer transition-colors hover:text-yellow-400 quick-star-label" data-star="{{ $i }}">★</label>
                                                            @endfor
                                                        </div>
                                                        
                                                        <textarea name="comment" rows="3" 
                                                                  class="w-full px-3 py-2 border-2 border-gray-300 focus:border-black focus:ring-0 text-sm" 
                                                                  placeholder="Chia sẻ trải nghiệm của bạn về {{ $item->isCombo() ? 'combo' : 'sản phẩm' }} này...">{{ old('comment') }}</textarea>
                                                        
                                                        <!-- Upload hình ảnh -->
                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-medium text-gray-700">Hình ảnh đánh giá (tùy chọn)</label>
                                                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors" 
                                                                 id="quick-drop-zone-{{ $order->id }}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}">
                                                                <input type="file" name="images[]" multiple accept="image/*" class="hidden" 
                                                                       id="quick-images-{{ $order->id }}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}" 
                                                                       onchange="previewQuickImages(this, '{{ $order->id }}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}')"> 
                                                                <div class="text-gray-500">
                                                                    <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                    <p class="mt-1 text-xs">Kéo thả hoặc <button type="button" class="text-blue-600 hover:text-blue-500" onclick="document.getElementById('quick-images-{{ $order->id }}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}').click()">chọn ảnh</button></p>
                                                                    <p class="text-xs text-gray-400">Tối đa 5 ảnh, mỗi ảnh < 2MB</p>
                                                                </div>
                                                            </div>
                                                            <div id="quick-preview-{{ $order->id }}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}" class="grid grid-cols-5 gap-2 mt-2"></div>
                                                            <div id="quick-error-{{ $order->id }}-{{ $item->isCombo() ? 'combo_'.$item->collection_id : 'book_'.$item->book_id }}" class="text-red-500 text-xs mt-1"></div>
                                                        </div>
                                                        
                                                        <div class="flex gap-2">
                                                            <button type="submit" 
                                                                    class="flex-1 px-4 py-3 bg-black hover:bg-gray-800 text-white text-sm font-bold uppercase tracking-wide transition-colors duration-300">
                                                                GỬI ĐÁNH GIÁ
                                                            </button>
                                                            @if($item->isCombo())
                                                                <a href="{{ route('combos.show', $item->collection->slug) }}" 
                                                                   class="px-3 py-3 bg-gray-200 text-black text-xs font-medium hover:bg-gray-300 transition-colors duration-150">
                                                                    Chi tiết
                                                                </a>
                                                            @else
                                                                <a href="{{ route('books.show', $item->book->slug) }}" 
                                                                   class="px-3 py-3 bg-gray-200 text-black text-xs font-medium hover:bg-gray-300 transition-colors duration-150">
                                                                    Chi tiết
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                        </div>
                        @endif

                        <!-- Order Actions -->
                        <div class="border-t-2 border-gray-200 pt-6 mt-8">
                            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                                <div>
                                    <a href="{{ route('orders.show', $order->id) }}" 
                                       class="inline-flex items-center px-6 py-3 border-2 border-black text-black hover:bg-black hover:text-white font-bold text-sm uppercase tracking-wide transition-all duration-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        XEM CHI TIẾT
                                    </a>
                                </div>
                                
                                <!-- Order Chat Button -->
                                @include('components.order-chat-button', ['order' => $order])
                                
                                @if(!$order->isParentOrder() && \App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name))
                                    <form action="{{ route('account.orders.cancel', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="inline-flex items-center px-6 py-3 border-2 border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold text-sm uppercase tracking-wide transition-all duration-300"
                                                onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            HỦY ĐƠN HÀNG
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border-2 border-gray-200 text-center py-16 px-6">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gray-100 border-2 border-gray-300 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black uppercase tracking-wide text-black mb-4">CHƯA CÓ ĐƠN HÀNG NÀO</h3>
                        <p class="text-gray-600 uppercase tracking-wide mb-6">Tất cả đơn hàng của bạn sẽ được hiển thị ở đây</p>
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-8 py-4 bg-black text-white font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-colors duration-300">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            MUA SẮM NGAY
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="mt-12">
                <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 p-6">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }
    
    .child-order-item:hover {
        transform: translateY(-1px);
    }
    
    .product-image-container:hover img {
        transform: scale(1.05);
    }
    
    .review-section {
        backdrop-filter: blur(10px);
    }
    
    .star-hover:hover {
        transform: scale(1.1);
    }
    
    .gradient-border {
        background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
        padding: 1px;
        border-radius: 12px;
    }
    
    .gradient-border-inner {
        background: white;
        border-radius: 11px;
    }
</style>
@endpush

@push('scripts')
<script>
// Enhanced star rating interaction and quick review functionality
document.addEventListener('DOMContentLoaded', function() {
    // Toggle child orders visibility
    window.toggleChildOrders = function(orderId) {
        const childOrdersDiv = document.getElementById('child-orders-' + orderId);
        const toggleText = document.getElementById('toggle-text-' + orderId);
        const toggleIcon = document.getElementById('toggle-icon-' + orderId);
        
        if (childOrdersDiv.classList.contains('hidden')) {
            childOrdersDiv.classList.remove('hidden');
            toggleText.textContent = 'ẨN CHI TIẾT';
            toggleIcon.style.transform = 'rotate(180deg)';
        } else {
            childOrdersDiv.classList.add('hidden');
            toggleText.textContent = 'XEM CHI TIẾT';
            toggleIcon.style.transform = 'rotate(0deg)';
        }
    };
    // Star rating interaction
    const starRatings = document.querySelectorAll('.star-rating');
    
    starRatings.forEach(rating => {
        const stars = rating.querySelectorAll('label');
        const inputs = rating.querySelectorAll('input');
        
        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                stars.forEach((s, i) => {
                    if (i >= index) {
                        s.style.color = '#f59e0b';
                    } else {
                        s.style.color = '#d1d5db';
                    }
                });
            });
            
            star.addEventListener('mouseleave', () => {
                const checkedInput = rating.querySelector('input:checked');
                if (checkedInput) {
                    const checkedIndex = Array.from(inputs).indexOf(checkedInput);
                    stars.forEach((s, i) => {
                        if (i >= checkedIndex) {
                            s.style.color = '#f59e0b';
                        } else {
                            s.style.color = '#d1d5db';
                        }
                    });
                } else {
                    stars.forEach(s => s.style.color = '#d1d5db');
                }
            });
        });
    });

    // Quick review form submission
    const quickReviewForms = document.querySelectorAll('.quick-review-form');
    
    quickReviewForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Disable button and show loading
            submitButton.disabled = true;
            submitButton.textContent = 'ĐANG GỬI...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
                    successDiv.innerHTML = '<strong>Thành công!</strong> ' + data.message;
                    
                    this.parentNode.insertBefore(successDiv, this);
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
                    errorDiv.innerHTML = '<strong>Lỗi!</strong> ' + (data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    
                    this.parentNode.insertBefore(errorDiv, this);
                    
                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
                errorDiv.innerHTML = '<strong>Lỗi!</strong> Có lỗi xảy ra, vui lòng thử lại.';
                
                this.parentNode.insertBefore(errorDiv, this);
                
                // Re-enable button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    });

    // Enhanced star interaction for quick review
    const quickStarGroups = document.querySelectorAll('.quick-star-group');
    
    quickStarGroups.forEach(group => {
        const stars = group.querySelectorAll('.quick-star-label');
        const inputs = group.querySelectorAll('input[type="radio"]');
        
        stars.forEach(star => {
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.star);
                highlightStars(stars, rating);
            });
            
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.star);
                const input = group.querySelector(`input[value="${rating}"]`);
                if (input) {
                    input.checked = true;
                    highlightStars(stars, rating);
                }
            });
        });
        
        group.addEventListener('mouseleave', function() {
            const checkedInput = group.querySelector('input:checked');
            if (checkedInput) {
                const rating = parseInt(checkedInput.value);
                highlightStars(stars, rating);
            } else {
                resetStars(stars);
            }
        });
    });
    
    function highlightStars(stars, rating) {
        stars.forEach(star => {
            const starValue = parseInt(star.dataset.star);
            if (starValue <= rating) {
                star.style.color = '#f59e0b'; // yellow
            } else {
                star.style.color = '#d1d5db'; // gray
            }
        });
    }
    
    function resetStars(stars) {
        stars.forEach(star => {
            star.style.color = '#d1d5db'; // gray
        });
    }
});

// Image Modal Functions
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeImageModal();
            }
        });
    }
});

// Preview images for quick review form
function previewQuickImages(input, formId) {
    const previewContainer = document.getElementById(`quick-preview-${formId}`);
    const errorContainer = document.getElementById(`quick-error-${formId}`);
    
    // Clear previous previews and errors
    previewContainer.innerHTML = '';
    errorContainer.innerHTML = '';
    
    const files = Array.from(input.files);
    
    // Validate number of files
    if (files.length > 5) {
        errorContainer.innerHTML = 'Chỉ được chọn tối đa 5 hình ảnh';
        input.value = '';
        return;
    }
    
    // Validate each file
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            errorContainer.innerHTML = `Hình ảnh "${file.name}" vượt quá 2MB`;
            input.value = '';
            previewContainer.innerHTML = '';
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            errorContainer.innerHTML = `File "${file.name}" không phải là hình ảnh`;
            input.value = '';
            previewContainer.innerHTML = '';
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'relative';
            previewDiv.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="w-full h-20 object-cover rounded border">
                <button type="button" onclick="removeQuickImage(this, '${formId}', ${i})" 
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                    ×
                </button>
            `;
            previewContainer.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    }
}

// Remove image from quick review form
function removeQuickImage(button, formId, index) {
    const input = document.getElementById(`quick-images-${formId}`);
    const previewContainer = document.getElementById(`quick-preview-${formId}`);
    
    // Remove preview element
    button.parentElement.remove();
    
    // Create new FileList without the removed file
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    
    // Re-render previews with correct indices
    previewQuickImages(input, formId);
}
</script>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="Review Image" class="max-w-full max-h-full object-contain">
    </div>
</div>

@endpush