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
                            'shipping' => 'Đang giao hàng',
                            'delivered' => 'Đã giao',
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
                                    </h3>
                                    </div>
                                    <p class="text-sm text-gray-600 uppercase tracking-wide">
                                        Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <span class="status-badge bg-black text-white">
                                    {{ $order->orderStatus->name }}
                                </span>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 uppercase tracking-wide">Tổng tiền</p>
                                    <p class="text-2xl font-black text-black">
                                        {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                    </p>
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
                                        <span class="font-bold text-black">{{ $order->paymentStatus->name ?? 'Chưa thanh toán' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 uppercase tracking-wide">Phí vận chuyển:</span>
                                        <span class="font-bold text-black">{{ number_format($order->shipping_fee ?? 0) }}đ</span>
                                    </div>
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
                        @if($order->orderItems->count() > 0)
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
                                                    @if($item->book && $item->book->images->isNotEmpty())
                                                        <img src="{{ asset('storage/' . $item->book->images->first()->path) }}" 
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
                                            @endif
                                            
                                            <div class="flex items-center gap-6 text-sm">
                                                <span class="text-gray-600 uppercase tracking-wide">Số lượng: <span class="font-bold text-black">{{ $item->quantity }}</span></span>
                                                <span class="text-gray-600 uppercase tracking-wide">Đơn giá: <span class="font-bold text-black">{{ number_format($item->price) }}đ</span></span>
                                                <span class="text-gray-600 uppercase tracking-wide">Thành tiền: <span class="font-bold text-black">{{ number_format($item->price * $item->quantity) }}đ</span></span>
                                            </div>
                                        </div>

                                        <!-- Review Section -->
                                        @if(!$item->isCombo() && in_array($order->orderStatus->name, ['Đã giao', 'Thành công']))
                                            <div class="lg:w-80 flex-shrink-0">
                                                @php
                                                    $review = $order->reviews()->where('book_id', $item->book_id)->first();
                                                @endphp

                                                @if($review)
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
                                                        <p class="text-sm text-gray-600 italic">"{{ $review->comment }}"</p>
                                                        @if($review->admin_response)
                                                            <div class="mt-3 pt-3 border-t border-gray-300">
                                                                <p class="text-xs font-bold uppercase tracking-wide text-black mb-1">Phản hồi từ BookBee:</p>
                                                                <p class="text-sm text-gray-600">{{ $review->admin_response }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <form action="{{ route('account.reviews.store') }}" method="POST" class="space-y-4 review-form bg-gray-50 border-2 border-gray-200 p-4">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="book_id" value="{{ $item->book_id }}">
                                                        
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-1 h-4 bg-blue-500"></div>
                                                            <h6 class="font-bold text-sm uppercase tracking-wide text-black">ĐÁNH GIÁ SẢN PHẨM</h6>
                                                        </div>
                                                        
                                                        <div class="star-rating flex flex-row-reverse justify-end items-center gap-1">
                                                            @for($i = 5; $i >= 1; $i--)
                                                                <input type="radio" id="star-{{$item->id}}-{{ $i }}" name="rating" value="{{ $i }}" class="sr-only" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                                                <label for="star-{{$item->id}}-{{ $i }}" class="text-gray-300 text-2xl cursor-pointer transition-colors hover:text-yellow-400">★</label>
                                                            @endfor
                                                        </div>
                                                        
                                                        <textarea name="comment" rows="3" 
                                                                  class="w-full px-3 py-2 border-2 border-gray-300 focus:border-black focus:ring-0 text-sm" 
                                                                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." 
                                                                  required>{{ old('comment') }}</textarea>
                                                        
                                                        <button type="submit" 
                                                                class="w-full px-4 py-3 bg-black hover:bg-gray-800 text-white text-sm font-bold uppercase tracking-wide transition-colors duration-300">
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
                                
                                @if(\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name))
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

@push('scripts')
<script>
// Enhanced star rating interaction
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endpush