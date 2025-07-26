@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back button -->
        <div class="mb-6">
            <a href="{{ route('account.orders.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại danh sách đơn hàng
            </a>
        </div>

        <!-- Order header -->
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Chi tiết đơn hàng</h1>
                        <p class="mt-1 text-blue-100">Mã đơn hàng: {{ $order->code }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span class="px-4 py-2 text-sm font-semibold rounded-full 
                            {{ $order->orderStatus->name === 'Đã hủy' ? 'bg-red-100 text-red-800' : 
                               ($order->orderStatus->name === 'Thành công' ? 'bg-green-100 text-green-800' : 
                               'bg-blue-100 text-blue-800') }}">
                            {{ $order->orderStatus->name }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <!-- Order info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Thông tin đơn hàng</h2>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Ngày đặt:</span> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p><span class="font-medium">Phương thức thanh toán:</span> {{ $order->paymentMethod->name ?? 'Không xác định' }}</p>
                            <p><span class="font-medium">Trạng thái thanh toán:</span> {{ $order->paymentStatus->name ?? 'Chưa thanh toán' }}</p>
                            <p><span class="font-medium">Tổng tiền:</span> {{ number_format($order->total_amount) }} đ</p>
                        </div>
                    </div>
                    
                    <div>
                        @if($order->delivery_method === 'pickup')
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Thông tin nhận hàng</h2>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Phương thức:</span> Nhận tại cửa hàng</p>
                            <p><span class="font-medium">Người nhận:</span> {{ $order->recipient_name ?? $order->address->recipient_name ?? 'Không có thông tin' }}</p>
                            <p><span class="font-medium">Số điện thoại:</span> {{ $order->recipient_phone ?? $order->address->phone ?? '' }}</p>
                            <p><span class="font-medium">Địa chỉ cửa hàng:</span> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
                            <p><span class="font-medium">Giờ mở cửa:</span> 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>
                            <p class="text-blue-600 font-medium">Vui lòng mang theo mã đơn hàng {{ $order->order_code }} khi đến nhận sách.</p>
                        </div>
                        @else
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Địa chỉ giao hàng</h2>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Phương thức:</span> Giao hàng tận nơi</p>
                            <p class="font-medium">{{ $order->recipient_name ?? $order->address->recipient_name ?? 'Không có thông tin' }}</p>
                            <p>{{ $order->recipient_phone ?? $order->address->phone ?? '' }}</p>
                            <p>{{ $order->address->address_detail ?? '' }}</p>
                            <p>{{ $order->address->ward ?? '' }}, 
                               {{ $order->address->district ?? '' }}, 
                               {{ $order->address->city ?? '' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order items -->
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Sản phẩm đã đặt</h2>
                    <div class="space-y-6">
                        @foreach($order->orderItems as $item)
                            <div class="flex items-start border-b border-gray-100 pb-6">
                                <div class="flex-shrink-0 h-24 w-24 rounded-md overflow-hidden bg-gray-200">
                                    @if($item->isCombo())
                                        {{-- Hiển thị ảnh combo --}}
                                        @if($item->collection && $item->collection->cover_image)
                                            <img src="{{ asset('storage/' . $item->collection->cover_image) }}" 
                                                 alt="{{ $item->collection->name }}" 
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                                                <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            </div>
                                        @endif
                                    @else
                                        {{-- Hiển thị ảnh sách --}}
                                        @if($item->book && $item->book->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $item->book->images->first()->path) }}" 
                                                 alt="{{ $item->book->title }}" 
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full bg-gray-300 flex items-center justify-center">
                                                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    @if($item->isCombo())
                                        {{-- Hiển thị thông tin combo --}}
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                COMBO
                                            </span>
                                        </div>
                                        <h3 class="text-base font-medium text-gray-900">
                                            {{ $item->collection->name ?? 'Combo không xác định' }}
                                        </h3>
                                        @if($item->collection && $item->collection->description)
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ Str::limit($item->collection->description, 100) }}
                                            </p>
                                        @endif
                                    @else
                                        {{-- Hiển thị thông tin sách --}}
                                        <h3 class="text-base font-medium text-gray-900">
                                            @if($item->is_combo)
                                            {{ $item->collection->name ?? 'Combo không xác định' }}
                                            <small class="text-muted">(Combo)</small>
                                        @else
                                            {{ $item->book->title ?? 'Sách không xác định' }}
                                            @if($item->bookFormat)
                                                <small class="text-muted">({{ $item->bookFormat->format_name }})</small>
                                            @endif
                                        @endif
                                        </h3>
                                        @if($item->bookFormat)
                                            <p class="mt-1 text-sm text-gray-500">
                                                Định dạng: {{ $item->bookFormat->format_name }}
                                            </p>
                                        @endif
                                    @endif
                                    
                                    <p class="mt-1 text-sm text-gray-500">
                                        Số lượng: {{ $item->quantity }}
                                    </p>
                                    <p class="mt-1 text-sm font-medium text-gray-900">
                                        {{ number_format($item->price) }} đ
                                    </p>
                                    
                                    {{-- Chỉ hiển thị đánh giá cho sách lẻ --}}
                                    @if(!$item->isCombo() && $item->review)
                                        <div class="mt-2 text-sm text-gray-600">
                                            <p>Đánh giá: {{ $item->review->rating }} sao</p>
                                            <p class="mt-1">{{ $item->review->comment }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="text-base font-medium text-gray-900">
                                        {{ number_format($item->price * $item->quantity) }} đ
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order summary -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Tóm tắt đơn hàng</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tạm tính</span>
                                <span class="text-gray-900">{{ number_format($order->total_amount) }} đ</span>
                            </div>
                            @if($order->voucher)
                                @php
                                    $discountAmount = 0;
                                    // Tính toán số tiền giảm giá dựa trên phần trăm
                                    $discountByPercent = $order->total_amount * ($order->voucher->discount_percent / 100);
                                    // So sánh với mức giảm giá tối đa được phép
                                    $discountAmount = min($discountByPercent, $order->voucher->max_discount);
                                @endphp
                                <div class="flex justify-between">
                                    <span class="text-gray-600">
                                        Mã giảm giá ({{ $order->voucher->code }})
                                        @if($order->voucher->discount_percent)
                                            - {{ $order->voucher->discount_percent }}%
                                        @endif
                                    </span>
                                    <span class="text-red-600">-{{ number_format($discountAmount) }} đ</span>
                                </div>
                            @elseif($order->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Giảm giá</span>
                                    <span class="text-red-600">-{{ number_format($order->discount_amount) }} đ</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phí vận chuyển</span>
                                <span class="text-gray-900">{{ number_format($order->shipping_fee) }} đ</span>
                            </div>
                            @php
                                $discountTotal = isset($discountAmount) ? $discountAmount : $order->discount_amount;
                                $total = $order->total_amount - $discountTotal + $order->shipping_fee;
                            @endphp
                            <div class="border-t border-gray-200 pt-4 flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Tổng cộng</span>
                                <span class="text-lg font-bold text-gray-900">{{ number_format($total) }} đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order actions -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    @if(in_array($order->orderStatus->name, ['Chờ xác nhận', 'Đã xác nhận', 'Đang chuẩn bị']))
                        <div class="flex justify-end">
                            <form action="{{ route('account.orders.update', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                    Hủy đơn hàng
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($order->orderStatus->name === 'Thành công' && $order->paymentStatus->name === 'Đã Thanh Toán' && !in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền']))
                        @php
                            $hasRefundRequest = $order->refundRequests()->whereIn('status', ['pending', 'processing'])->exists();
                        @endphp
                        
                        <div class="flex justify-end space-x-4">
                            @if(!$hasRefundRequest)
                                <!-- Nút yêu cầu hoàn tiền -->
                                <a href="{{ route('account.orders.refund.create', $order->id) }}"
                                   class="inline-flex items-center px-4 py-2 border border-orange-300 text-sm font-medium rounded-md shadow-sm text-orange-700 bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    Yêu cầu hoàn tiền
                                </a>
                            @else
                                <!-- Nút xem trạng thái hoàn tiền -->
                                <a href="{{ route('account.orders.refund.status', $order->id) }}"
                                   class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Xem trạng thái hoàn tiền
                                </a>
                            @endif
                        </div>
                    @endif

                    @if(in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền']))
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-800">
                                        @if($order->paymentStatus->name === 'Đang Hoàn Tiền')
                                            Đang xử lý hoàn tiền
                                        @else
                                            Đã hoàn tiền thành công
                                        @endif
                                    </h3>
                                    <p class="text-sm text-blue-700 mt-1">
                                        @if($order->paymentStatus->name === 'Đang Hoàn Tiền')
                                            Yêu cầu hoàn tiền của bạn đang được xử lý. Chúng tôi sẽ thông báo khi có kết quả.
                                        @else
                                            Tiền đã được hoàn về tài khoản của bạn.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection