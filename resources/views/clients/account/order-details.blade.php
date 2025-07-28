@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng')

@push('styles')
<style>
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
    .cancel-form {
        display: none;
    }
    .cancel-form.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button - Adidas Style -->
        <div class="mb-8">
            <a href="{{ route('account.orders.unified') }}" 
               class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-gray-300 hover:border-black text-black font-bold uppercase tracking-wide transition-all duration-300 hover:bg-gray-50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                QUAY LẠI DANH SÁCH
            </a>
        </div>

        <!-- Order Header - Adidas Style -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 geometric-bg">
            <div class="bg-black text-white px-8 py-6 relative">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-1 h-8 bg-white"></div>
                                <h1 class="text-3xl font-black uppercase tracking-wide">CHI TIẾT ĐƠN HÀNG</h1>
                            </div>
                            <p class="text-gray-300 text-sm uppercase tracking-wider">MÃ ĐƠN HÀNG: {{ $order->order_code }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="status-badge bg-white text-black">
                                {{ $order->orderStatus->name }}
                            </span>
                            <div class="text-right">
                                <p class="text-sm text-gray-300 uppercase tracking-wide">Tổng tiền</p>
                                <p class="text-2xl font-black text-white">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Content -->
            <div class="p-8">
                <!-- Order Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Order Details -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1 h-5 bg-black"></div>
                            <h4 class="text-base font-bold uppercase tracking-wide text-black">THÔNG TIN ĐƠN HÀNG</h4>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Ngày đặt:</span>
                                <span class="font-bold text-black">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Phương thức thanh toán:</span>
                                <span class="font-bold text-black">{{ $order->paymentMethod->name ?? 'Không xác định' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Trạng thái thanh toán:</span>
                                <span class="font-bold text-black">{{ $order->paymentStatus->name ?? 'Chưa thanh toán' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Phí vận chuyển:</span>
                                <span class="font-bold text-black">{{ number_format($order->shipping_fee ?? 0) }}đ</span>
                            </div>
                            @if($order->ghn_order_code)
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Mã vận đơn GHN:</span>
                                <span class="font-bold text-black">{{ $order->ghn_order_code }}</span>
                            </div>
                            @endif
                            @if($order->expected_delivery_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Ngày giao dự kiến:</span>
                                <span class="font-bold text-black">{{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('d/m/Y') }}</span>
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
                            @if($order->delivery_method === 'ebook')
                                <p><span class="font-bold text-black">Phương thức:</span> Sách điện tử (Ebook)</p>
                                <p><span class="font-bold text-black">Người nhận:</span> {{ $order->recipient_name ?? 'Không có thông tin' }}</p>
                                <p><span class="font-bold text-black">Email:</span> {{ $order->recipient_email ?? '' }}</p>
                                <p class="text-black font-bold bg-blue-100 p-2 border-l-4 border-black">Link tải ebook sẽ được gửi đến email của bạn sau khi đơn hàng được xác nhận.</p>
                            @elseif($order->delivery_method === 'pickup')
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
                                <p class="text-black font-bold bg-yellow-100 p-2 border-l-4 border-black">Vui lòng mang theo mã đơn hàng {{ $order->order_code }} khi đến nhận sách.</p>
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

                <!-- GHN Tracking Section -->
                {{-- @if($order->delivery_method === 'delivery' && $order->ghn_order_code)
                <div class="border-t-2 border-gray-200 pt-8 mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-5 bg-blue-600"></div>
                        <h4 class="text-base font-bold uppercase tracking-wide text-black">THEO DÕI ĐƠN HÀNG GHN</h4>
                    </div>
                    
                    <div class="bg-blue-50 border-2 border-blue-200 p-6 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h5 class="font-bold text-black mb-3">Thông tin vận chuyển</h5>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Mã vận đơn:</span>
                                        <span class="font-bold text-black">{{ $order->ghn_order_code }}</span>
                                    </div>
                                    @if($order->expected_delivery_date)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ngày giao dự kiến:</span>
                                        <span class="font-bold text-black">{{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('d/m/Y') }}</span>
                                    </div>
                                    @endif
                                    @if($order->ghn_service_type_id)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Loại dịch vụ:</span>
                                        <span class="font-bold text-black">{{ $order->ghn_service_type_id == 2 ? 'Giao hàng tiêu chuẩn' : 'Giao hàng nhanh' }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <h5 class="font-bold text-black mb-3">Trạng thái hiện tại</h5>
                                <div id="ghn-tracking-status" class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                        <span class="text-sm font-medium">Đang tải thông tin...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tracking Timeline -->
                        <div id="ghn-tracking-timeline" class="hidden">
                            <h5 class="font-bold text-black mb-4">Lịch sử vận chuyển</h5>
                            <div class="space-y-3">
                                <!-- Timeline items will be loaded here -->
                            </div>
                        </div>
                        
                        <div class="flex gap-3 mt-4">
                            <button id="refresh-tracking-btn" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm uppercase tracking-wide transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Cập nhật
                            </button>
                            <button id="toggle-timeline-btn" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold text-sm uppercase tracking-wide transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                Xem chi tiết
                            </button>
                        </div>
                    </div>
                </div>
                @endif --}}

                <!-- Order Items -->
                <div class="border-t-2 border-gray-200 pt-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-5 bg-black"></div>
                        <h4 class="text-base font-bold uppercase tracking-wide text-black">SẢN PHẨM ĐÃ ĐẶT ({{ $order->orderItems->count() }} sản phẩm)</h4>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                            <div class="flex items-center gap-4 p-4 border-2 border-gray-200 hover:border-black transition-all duration-300">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-20 bg-gray-200 border-2 border-gray-300 overflow-hidden">
                                        @if($item->isCombo())
                                            @if($item->collection && $item->collection->cover_image)
                                                <img src="{{ asset('storage/' . $item->collection->cover_image) }}" 
                                                     alt="{{ $item->collection->name }}" 
                                                     class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full bg-black flex items-center justify-center">
                                                    <span class="text-white text-xs font-bold">COMBO</span>
                                                </div>
                                            @endif
                                        @else
                                            @if($item->book && $item->book->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $item->book->images->first()->path) }}" 
                                                     alt="{{ $item->book->title }}" 
                                                     class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 text-xs">IMG</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1">
                                    @if($item->isCombo())
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-1 bg-black text-white text-xs font-bold uppercase">COMBO</span>
                                        </div>
                                        <h5 class="font-bold text-black text-sm uppercase tracking-wide">
                                            {{ $item->collection->name ?? 'Combo không xác định' }}
                                        </h5>
                                    @else
                                        <h5 class="font-bold text-black text-sm uppercase tracking-wide">
                                            {{ $item->book->title ?? 'Sách không xác định' }}
                                            @if($item->bookFormat)
                                                <span class="text-gray-600">({{ $item->bookFormat->format_name }})</span>
                                            @endif
                                        </h5>
                                    @endif
                                    
                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-600 uppercase tracking-wide">
                                        <span>SL: {{ $item->quantity }}</span>
                                        <span>GIÁ: {{ number_format($item->price) }}đ</span>
                                    </div>
                                </div>
                                
                                <!-- Price -->
                                <div class="text-right">
                                    <p class="text-lg font-black text-black">
                                        {{ number_format($item->price * $item->quantity) }}đ
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mt-8 border-t-2 border-gray-200 pt-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-5 bg-black"></div>
                        <h4 class="text-base font-bold uppercase tracking-wide text-black">TÓM TẮT ĐƠN HÀNG</h4>
                    </div>
                    <div class="bg-gray-50 border-2 border-gray-200 p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Tạm tính</span>
                                <span class="font-bold text-black">{{ number_format($order->total_amount) }}đ</span>
                            </div>
                            @if($order->voucher)
                                @php
                                    $discountAmount = 0;
                                    $discountByPercent = $order->total_amount * ($order->voucher->discount_percent / 100);
                                    $discountAmount = min($discountByPercent, $order->voucher->max_discount);
                                @endphp
                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">
                                        Mã giảm giá ({{ $order->voucher->code }})
                                        @if($order->voucher->discount_percent)
                                            - {{ $order->voucher->discount_percent }}%
                                        @endif
                                    </span>
                                    <span class="text-red-600 font-bold">-{{ number_format($discountAmount) }}đ</span>
                                </div>
                            @elseif($order->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">Giảm giá</span>
                                    <span class="text-red-600 font-bold">-{{ number_format($order->discount_amount) }}đ</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600 uppercase tracking-wide">Phí vận chuyển</span>
                                <span class="font-bold text-black">{{ number_format($order->shipping_fee) }}đ</span>
                            </div>
                            @php
                                $discountTotal = isset($discountAmount) ? $discountAmount : $order->discount_amount;
                                $total = $order->total_amount - $discountTotal + $order->shipping_fee;
                            @endphp
                            <div class="border-t-2 border-black pt-4 flex justify-between">
                                <span class="text-lg font-black text-black uppercase tracking-wide">Tổng cộng</span>
                                <span class="text-2xl font-black text-black">{{ number_format($total) }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ebook Download Section -->
                @if($order->paymentStatus->name === 'Đã Thanh Toán')
                    @php
                        $ebookItems = $order->orderItems->filter(function($item) {
                            // Trường hợp 1: Mua trực tiếp ebook
                            if (!$item->is_combo && $item->bookFormat && $item->bookFormat->format_name === 'Ebook') {
                                return true;
                            }
                            // Trường hợp 2: Mua sách vật lý có ebook kèm theo
                            if (!$item->is_combo && $item->book && $item->book->formats) {
                                return $item->book->formats->contains('format_name', 'Ebook');
                            }
                            return false;
                        });
                    @endphp
                    
                    @if($ebookItems->isNotEmpty())
                        <div class="mt-8 pt-8 border-t-2 border-gray-200">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-1 h-5 bg-green-600"></div>
                                <h4 class="text-base font-bold uppercase tracking-wide text-black">TẢI EBOOK</h4>
                            </div>
                            
                            <div class="bg-green-50 border-2 border-green-200 p-6">
                                <div class="space-y-4">
                                    @foreach($ebookItems as $item)
                                        @if(!$item->is_combo && $item->bookFormat && $item->bookFormat->format_name === 'Ebook')
                                            {{-- Trường hợp 1: Mua trực tiếp ebook --}}
                                            <div class="flex items-center justify-between p-4 bg-white border border-green-300 rounded">
                                                <div>
                                                    <h5 class="font-bold text-black">{{ $item->book->title ?? 'Sách không xác định' }}</h5>
                                                    <p class="text-sm text-gray-600">Định dạng: Ebook (Mua trực tiếp)</p>
                                                    @if($item->book->authors->isNotEmpty())
                                                        <p class="text-sm text-gray-600">Tác giả: {{ $item->book->authors->first()->name }}</p>
                                                    @endif
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('ebook.view', $item->bookFormat->id) }}" 
                                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold uppercase tracking-wide transition-all duration-300"
                                                       target="_blank">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Đọc Online
                                                    </a>
                                                    <a href="{{ route('ebook.download', $item->bookFormat->id) }}" 
                                                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        Tải Xuống
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif(!$item->is_combo && $item->book && $item->book->formats->contains('format_name', 'Ebook'))
                                            {{-- Trường hợp 2: Mua sách vật lý có ebook kèm theo --}}
                                            @php
                                                $ebookFormat = $item->book->formats->where('format_name', 'Ebook')->first();
                                            @endphp
                                            @if($ebookFormat && $ebookFormat->file_url)
                                                <div class="flex items-center justify-between p-4 bg-white border border-green-300 rounded">
                                                    <div>
                                                        <h5 class="font-bold text-black">{{ $item->book->title ?? 'Sách không xác định' }}</h5>
                                                        <p class="text-sm text-gray-600">Định dạng: Ebook (Kèm theo sách vật lý)</p>
                                                        @if($item->book->authors->isNotEmpty())
                                                            <p class="text-sm text-gray-600">Tác giả: {{ $item->book->authors->first()->name }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <a href="{{ route('ebook.view', $ebookFormat->id) }}" 
                                                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold uppercase tracking-wide transition-all duration-300"
                                                           target="_blank">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                            Đọc Online
                                                        </a>
                                                        <a href="{{ route('ebook.download', $ebookFormat->id) }}" 
                                                           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                            Tải Xuống
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                                
                                <div class="mt-4 p-3 bg-green-100 border border-green-300 rounded">
                                    <p class="text-sm text-green-800">
                                        <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <strong>Lưu ý:</strong> Bạn có thể đọc ebook online hoặc tải xuống để đọc offline. File tải xuống sẽ có định dạng PDF.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Order Actions -->
                <div class="mt-8 pt-8 border-t-2 border-gray-200">
                    @if(\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name))
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-5 bg-black"></div>
                            <h4 class="text-base font-bold uppercase tracking-wide text-black">THAO TÁC ĐƠN HÀNG</h4>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Cancel Button -->
                            <button type="button" 
                                    onclick="toggleCancelForm()"
                                    class="inline-flex items-center gap-3 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                HỦY ĐƠN HÀNG
                            </button>
                            
                            <!-- Cancel Form -->
                            <div id="cancelForm" class="cancel-form bg-red-50 border-2 border-red-200 p-6">
                                <form action="{{ route('account.orders.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-red-800 uppercase tracking-wide mb-2">
                                            LÝ DO HỦY ĐƠN HÀNG *
                                        </label>
                                        <textarea name="cancellation_reason" 
                                                  rows="4" 
                                                  required
                                                  placeholder="Vui lòng nhập lý do hủy đơn hàng..."
                                                  class="w-full px-4 py-3 border-2 border-red-300 focus:border-red-500 focus:outline-none text-sm"></textarea>
                                    </div>
                                    
                                    <div class="flex gap-3">
                                        <button type="submit" 
                                                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                            XÁC NHẬN HỦY
                                        </button>
                                        <button type="button" 
                                                onclick="toggleCancelForm()"
                                                class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-black font-bold uppercase tracking-wide transition-all duration-300">
                                            HỦY BỎ
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($order->orderStatus->name === 'Thành công' && $order->paymentStatus->name === 'Đã Thanh Toán')
                        @php
                            $hasRefundRequest = $order->refundRequests()->exists();
                        @endphp
                        
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-5 bg-black"></div>
                            <h4 class="text-base font-bold uppercase tracking-wide text-black">YÊU CẦU HOÀN TIỀN</h4>
                        </div>
                        
                        <div class="flex gap-4">
                            @if(!$hasRefundRequest)
                                <a href="{{ route('account.orders.refund.create', $order->id) }}"
                                   class="inline-flex items-center gap-3 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    YÊU CẦU HOÀN TIỀN
                                </a>
                            @else
                                <a href="{{ route('account.orders.refund.status', $order->id) }}"
                                   class="inline-flex items-center gap-3 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    XEM TRẠNG THÁI HOÀN TIỀN
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Hiển thị thông báo khi đơn hàng đang hoàn tiền hoặc đã hoàn tiền --}}
                    @if($order->orderStatus->name === 'Thành công' && in_array($order->paymentStatus->name, ['Đang Hoàn Tiền']))
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-5 bg-black"></div>
                            <h4 class="text-base font-bold uppercase tracking-wide text-black">TRẠNG THÁI HOÀN TIỀN</h4>
                        </div>
                        
                        <div class="bg-yellow-50 border-2 border-yellow-200 p-6">
                            <div class="flex items-center gap-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-base font-bold text-yellow-800 uppercase tracking-wide">
                                        ĐANG XỬ LÝ HOÀN TIỀN
                                    </h3>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Yêu cầu hoàn tiền của bạn đang được xử lý. Chúng tôi sẽ thông báo khi có kết quả.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('account.orders.refund.status', $order->id) }}"
                                   class="inline-flex items-center gap-3 px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    XEM CHI TIẾT HOÀN TIỀN
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Hiển thị thông báo khi đơn hàng đã hoàn tiền thành công --}}
                    @if($order->orderStatus->name === 'Thành công' && $order->paymentStatus->name === 'Đã Hoàn Tiền')
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-5 bg-black"></div>
                            <h4 class="text-base font-bold uppercase tracking-wide text-black">TRẠNG THÁI HOÀN TIỀN</h4>
                        </div>
                        
                        <div class="bg-green-50 border-2 border-green-200 p-6">
                            <div class="flex items-center gap-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-base font-bold text-green-800 uppercase tracking-wide">
                                        ĐÃ HOÀN TIỀN THÀNH CÔNG
                                    </h3>
                                    <p class="text-sm text-green-700 mt-1">
                                        Tiền đã được hoàn về tài khoản của bạn thành công.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('account.orders.refund.status', $order->id) }}"
                                   class="inline-flex items-center gap-3 px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    XEM CHI TIẾT HOÀN TIỀN
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleCancelForm() {
    const form = document.getElementById('cancelForm');
    form.classList.toggle('active');
}

// GHN Tracking functionality
@if($order->delivery_method === 'delivery' && $order->ghn_order_code)
document.addEventListener('DOMContentLoaded', function() {
    const ghnOrderCode = '{{ $order->ghn_order_code }}';
    const refreshBtn = document.getElementById('refresh-tracking-btn');
    const toggleTimelineBtn = document.getElementById('toggle-timeline-btn');
    const trackingStatus = document.getElementById('ghn-tracking-status');
    const trackingTimeline = document.getElementById('ghn-tracking-timeline');
    
    // Load tracking info on page load
    loadTrackingInfo();
    
    // Refresh button event
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Đang cập nhật...
            `;
            
            loadTrackingInfo().finally(() => {
                this.disabled = false;
                this.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Cập nhật
                `;
            });
        });
    }
    
    // Toggle timeline button event
    if (toggleTimelineBtn) {
        toggleTimelineBtn.addEventListener('click', function() {
            const isHidden = trackingTimeline.classList.contains('hidden');
            
            if (isHidden) {
                trackingTimeline.classList.remove('hidden');
                this.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    Ẩn chi tiết
                `;
            } else {
                trackingTimeline.classList.add('hidden');
                this.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Xem chi tiết
                `;
            }
        });
    }
    
    async function loadTrackingInfo() {
        try {
            const response = await fetch(`/api/ghn/tracking/${ghnOrderCode}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.data) {
                updateTrackingStatus(data.data);
                updateTrackingTimeline(data.data.logs || []);
            } else {
                showTrackingError('Không thể tải thông tin theo dõi');
            }
        } catch (error) {
            console.error('Error loading tracking info:', error);
            showTrackingError('Lỗi khi tải thông tin theo dõi');
        }
    }
    
    function updateTrackingStatus(trackingData) {
        const statusElement = trackingStatus;
        const currentStatus = trackingData.status || 'Không xác định';
        const statusColor = getStatusColor(currentStatus);
        
        statusElement.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 ${statusColor} rounded-full"></div>
                <span class="text-sm font-medium">${currentStatus}</span>
            </div>
            ${trackingData.description ? `<p class="text-xs text-gray-600 mt-1">${trackingData.description}</p>` : ''}
        `;
    }
    
    function updateTrackingTimeline(logs) {
        const timelineContainer = trackingTimeline.querySelector('.space-y-3');
        
        if (logs.length === 0) {
            timelineContainer.innerHTML = '<p class="text-sm text-gray-600">Chưa có thông tin lịch sử vận chuyển</p>';
            return;
        }
        
        timelineContainer.innerHTML = logs.map((log, index) => {
            const isLatest = index === 0;
            return `
                <div class="flex items-start gap-3 ${isLatest ? 'bg-blue-100 p-3 rounded' : ''}">
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-3 h-3 ${isLatest ? 'bg-blue-600' : 'bg-gray-400'} rounded-full"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-black">${log.status || 'Cập nhật trạng thái'}</p>
                        <p class="text-xs text-gray-600">${log.updated_date || ''}</p>
                        ${log.description ? `<p class="text-xs text-gray-700 mt-1">${log.description}</p>` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }
    
    function showTrackingError(message) {
        trackingStatus.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                <span class="text-sm font-medium text-red-600">${message}</span>
            </div>
        `;
    }
    
    function getStatusColor(status) {
        const statusLower = status.toLowerCase();
        if (statusLower.includes('giao thành công') || statusLower.includes('delivered')) {
            return 'bg-green-500';
        } else if (statusLower.includes('đang giao') || statusLower.includes('shipping')) {
            return 'bg-blue-500';
        } else if (statusLower.includes('đã lấy') || statusLower.includes('picked')) {
            return 'bg-yellow-500';
        } else if (statusLower.includes('hủy') || statusLower.includes('cancel')) {
            return 'bg-red-500';
        } else {
            return 'bg-gray-500';
        }
    }
});
@endif
</script>
@endpush
@endsection