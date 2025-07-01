@extends('layouts.account.layout')

@section('account_content')
<div class="bg-white border border-black shadow-lg mb-8" style="border-radius:0;">
    <div class="px-8 py-6 border-b border-black bg-black">
        <h1 class="text-2xl font-bold text-white uppercase tracking-wide">Quản lý đơn hàng</h1>
    </div>
    <div class="p-8">
        <!-- Navigation Tabs for Order Status -->
        <div class="flex space-x-1 mb-8 border-b border-black">
            @foreach([
                1 => 'Tất cả đơn hàng',
                2 => 'Chờ xác nhận',
                3 => 'Đã xác nhận',
                4 => 'Đang chuẩn bị',
                5 => 'Đang giao hàng',
                6 => 'Đã giao thành công',
                7 => 'Đã nhận hàng',
                8 => 'Thành công',
                9 => 'Đã hủy',
                10 => 'Giao thất bại',
                11 => 'Đã hoàn tiền'
            ] as $status => $label)
                <a href="{{ route('account.orders.index', ['status' => $status]) }}"
                   class="flex-1 text-center px-6 py-3 text-base font-semibold border-b-2 transition
                       {{ request('status', '1') == $status ? 'border-black text-black bg-white' : 'border-transparent text-gray-500 hover:text-black hover:bg-gray-100' }}"
                   style="border-radius:0;">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Order List -->
        <div class="space-y-6">
            @forelse($orders as $order)
                <div class="bg-white border border-black shadow transition hover:shadow-lg" style="border-radius:0;">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-4 bg-gray-50 border-b border-black">
                        <div>
                            <h3 class="text-lg font-bold text-black">Đơn hàng #{{ $order->id }}</h3>
                            <p class="text-sm text-gray-600">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-black text-white border border-black" style="border-radius:0;">
                            {{ $order->orderStatus->name }}
                        </span>
                    </div>

                    <div class="p-6 space-y-6">
                        @foreach($order->orderItems as $item)
                            <div class="flex flex-col lg:flex-row gap-6 pb-6 border-b border-slate-200 last:border-b-0 last:pb-0">
                                <div class="flex-shrink-0">
                                    <img src="{{ $item->book->image_url ?? 'https://via.placeholder.com/120x160' }}"
                                         alt="{{ $item->book->name }}"
                                         class="w-24 h-32 object-cover rounded-lg shadow-sm">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-medium text-slate-900 mb-3">{{ $item->book->name }}</h4>
                                    <div class="space-y-1">
                                        <p class="text-sm text-slate-600">
                                            <span class="font-medium">Số lượng:</span> {{ $item->quantity }}
                                        </p>
                                        <p class="text-sm text-slate-600">
                                            <span class="font-medium">Giá:</span>
                                            <span class="text-red-600 font-semibold">{{ number_format($item->price, 0, ',', '.') }} đ</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="lg:w-96">
                                    @php
                                        $review = $order->reviews()->withTrashed()->where('book_id', $item->book_id)->first();
                                    @endphp
                                    @if($review && $review->trashed())
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                            <p class="text-center text-red-500">Đánh giá đã bị xóa</p>
                                        </div>
                                    @elseif($review)
                                        <fieldset disabled>
                                            <form action="{{ route('account.reviews.update', $review->id) }}" method="POST" class="space-y-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="book_id" value="{{ $item->book_id }}">

                                                <div class="mb-2">
                                                    <span class="text-xs text-slate-500">Cập nhật lần cuối: {{ $review->updated_at->format('d/m/Y') }}</span>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Đánh giá của bạn:</label>
                                                    <div class="flex items-center justify-center space-x-1">
                                                        @for($i = 5; $i >= 1; $i--)
                                                            <label for="star{{ $i }}_{{ $order->id }}_{{ $item->book_id }}" class="cursor-pointer text-2xl {{ old('rating', $review->rating) >= $i ? 'text-yellow-400' : 'text-slate-300' }} hover:text-yellow-400 transition-colors duration-150">
                                                                <input type="radio"
                                                                       id="star{{ $i }}_{{ $order->id }}_{{ $item->book_id }}"
                                                                       name="rating"
                                                                       value="{{ $i }}"
                                                                       class="sr-only"
                                                                       {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                                                                ★
                                                            </label>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <div>
                                                    <textarea name="comment" rows="3"
                                                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-sm resize-none"
                                                              placeholder="Nhận xét về sản phẩm...">{{ old('comment', $review->comment) }}</textarea>
                                                </div>
                                            </form>
                                        </fieldset>
                                    @else
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                            <form action="{{ route('account.review.store') }}" method="POST" class="space-y-4">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="book_id" value="{{ $item->book_id }}">

                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Đánh giá của bạn:</label>
                                                    <div class="flex items-center justify-center space-x-1">
                                                        @for($i = 5; $i >= 1; $i--)
                                                            <input type="radio" id="star{{ $i }}_{{ $order->id }}_{{ $item->book_id }}" name="rating" value="{{ $i }}" class="sr-only" {{ old('rating') == $i ? 'checked' : ($i == 5 ? 'checked' : '') }}>
                                                            <label for="star{{ $i }}_{{ $order->id }}_{{ $item->book_id }}" class="cursor-pointer text-2xl text-slate-300 hover:text-yellow-400 transition-colors duration-150" title="{{ $i }} sao">★</label>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <div>
                                                    <textarea name="comment" rows="3"
                                                              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-sm resize-none"
                                                              placeholder="Nhận xét về sản phẩm..." required>{{ old('comment') }}</textarea>
                                                </div>

                                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                    Gửi đánh giá
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-black text-white mb-4" style="border-radius:0;">
                        <i class="fas fa-box-open text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-black mb-1">Không có đơn hàng nào</h3>
                    <p class="text-gray-600">Bạn chưa có đơn hàng nào để hiển thị.</p>
                </div>
            @endforelse
            @if($orders->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
