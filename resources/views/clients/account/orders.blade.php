@extends('layouts.account.layout')

@section('account_content')
<style>
    .status-tab-active {
        background-color: #000;
        color: #fff;
    }
    .order-card {
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: box-shadow 0.3s ease;
    }
    .order-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .review-form .star-rating label:hover,
    .review-form .star-rating input:checked ~ label {
        color: #f59e0b; /* amber-400 */
    }
</style>

<div>
    <h1 class="text-3xl font-black uppercase tracking-wide text-black mb-8">Quản lý đơn hàng</h1>

    <!-- Navigation Tabs for Order Status -->
    <div class="flex flex-wrap gap-2 mb-8">
        @php
            $statuses = [
                1 => 'Tất cả',
                2 => 'Chờ xác nhận',
                3 => 'Đã xác nhận',
                4 => 'Đang chuẩn bị',
                5 => 'Đang giao hàng',
                6 => 'Đã giao',
                9 => 'Đã hủy',
            ];
        @endphp
        @foreach($statuses as $statusId => $label)
            <a href="{{ route('account.orders.index', ['status' => $statusId]) }}"
               class="px-4 py-2 text-sm font-bold uppercase tracking-wider rounded-full transition-colors duration-200 
                      {{ request('status', '1') == $statusId ? 'status-tab-active' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Order List -->
    <div class="space-y-6">
        @forelse($orders as $order)
            <div class="bg-white order-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-bold text-black">ĐƠN HÀNG #{{ $order->order_code }}</h3>
                        <p class="text-sm text-gray-600">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="status-badge bg-black text-white">{{ $order->orderStatus->name }}</span>
                        <p class="text-lg font-bold text-black mt-1">Tổng tiền: {{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($order->orderItems as $item)
                        <div class="flex flex-col md:flex-row gap-4 py-4 border-b border-gray-200 last:border-b-0">
                            <!-- Product Info -->
                            <div class="flex-grow flex gap-4">
                                <img src="{{ $item->book->image_url ?? 'https://via.placeholder.com/100x140' }}"  class="w-20 h-28 object-cover rounded-md flex-shrink-0">
                                <div>
                                    <p class="text-sm text-gray-600">Số lượng: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-600">Giá: <span class="font-semibold">{{ number_format($item->price, 0, ',', '.') }} đ</span></p>
                                </div>
                            </div>

                            <!-- Review Section -->
                            <div class="md:w-80 flex-shrink-0">
                                @if(in_array($order->order_status_id, [7, 8])) <!-- Delivered or Success -->
                                    @php
                                        $review = $order->reviews()->withTrashed()->where('book_id', $item->book_id)->first();
                                    @endphp

                                    @if($review)
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <h5 class="font-bold text-sm mb-2">Đánh giá của bạn</h5>
                                            <div class="flex items-center text-yellow-400 mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="text-sm text-gray-600 italic">"{{ $review->comment }}"</p>
                                            @if($review->trashed())
                                                <p class="text-xs text-red-500 mt-2">(Đánh giá đã bị xóa)</p>
                                            @endif
                                        </div>
                                    @else
                                        <form action="{{ route('account.reviews.store') }}" method="POST" class="space-y-3 review-form">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <input type="hidden" name="book_id" value="{{ $item->book_id }}">
                                            <div class="star-rating flex flex-row-reverse justify-end items-center">
                                                @for($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="star-{{$item->id}}-{{ $i }}" name="rating" value="{{ $i }}" class="sr-only" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                                    <label for="star-{{$item->id}}-{{ $i }}" class="text-gray-300 text-2xl cursor-pointer transition-colors">★</label>
                                                @endfor
                                            </div>
                                            <textarea name="comment" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-black focus:border-black text-sm" placeholder="Viết nhận xét của bạn..." required>{{ old('comment') }}</textarea>
                                            <button type="submit" class="w-full px-4 py-2 bg-black hover:bg-gray-800 text-white text-sm font-bold uppercase rounded-md transition-colors">Gửi đánh giá</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-16 px-6 bg-gray-50 rounded-lg">
                <i class="fas fa-receipt text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-black mb-2">Bạn chưa có đơn hàng nào</h3>
                <p class="text-gray-500">Tất cả đơn hàng của bạn sẽ được hiển thị ở đây.</p>
            </div>
        @endforelse

        @if($orders->hasPages())
            <div class="mt-8">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
