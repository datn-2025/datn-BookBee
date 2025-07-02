@extends('layouts.account.layout')

@section('account_content')
    <div class="bg-white border border-black shadow mb-8" style="border-radius:0;">
        <div class="px-8 py-6 border-b border-black bg-black">
            <h1 class="text-2xl font-bold text-white uppercase tracking-wide">Đánh giá của tôi</h1>
        </div>
        <div class="p-8">
            <!-- Tabs -->
            <div class="flex space-x-1 mb-8 border-b border-black">
                @foreach ([1 => 'Tất cả đánh giá', 2 => 'Chưa đánh giá', 3 => 'Đã đánh giá'] as $type => $label)
                    <a href="{{ route('account.purchase', ['type' => $type]) }}"
                        class="flex-1 text-center px-6 py-3 text-base font-semibold border-b-2 transition
                       {{ request('type', '1') == $type ? 'border-black text-black bg-white' : 'border-transparent text-gray-500 hover:text-black hover:bg-gray-100' }}"
                    >{{ $label }}</a>
                @endforeach
            </div>
            <!-- Orders List -->
            <div class="space-y-6">
                @forelse($orders as $order)
                    <div class="bg-white border border-black shadow transition hover:shadow-lg" style="border-radius:0;">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-4 bg-gray-50 border-b border-black">
                            <div>
                                <h3 class="text-lg font-bold text-black">Đơn hàng #{{ $order->order_code }}</h3>
                                <p class="text-sm text-gray-600">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-black text-white border border-black mb-1" style="border-radius:0;">Đã hoàn thành</span>
                                <span class="text-base font-semibold text-black">Tổng tiền: <span class="text-red-600">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span></span>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            @foreach ($order->orderItems as $item)
                                @php
                                    $review = $order->reviews()->withTrashed()->where('book_id', $item->book_id)->first();
                                @endphp
                                <div class="flex flex-col lg:flex-row gap-6 pb-6 border-b border-slate-200 last:border-b-0 last:pb-0">
                                    <div class="lg:w-32 flex-shrink-0 flex items-center justify-center bg-gray-100 border border-black" style="border-radius:0; min-height: 120px;">
                                        <img src="{{ $item->book->cover_image_url }}" alt="{{ $item->book->title }}" class="w-20 h-28 object-cover shadow-sm border border-slate-300" style="border-radius:0;">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-lg font-bold text-black mb-1">{{ $item->book->title }}</h4>
                                        <div class="text-sm text-gray-700 mb-1"><span class="font-medium">Tác giả:</span> {{ $item->book->author->name ?? 'Không rõ' }}</div>
                                        <div class="text-sm text-gray-700 mb-1"><span class="font-medium">Nhà xuất bản:</span> {{ $item->book->brand->name ?? 'Không rõ' }}</div>
                                        <div class="text-sm text-gray-700 mb-1"><span class="font-medium">Số lượng:</span> {{ $item->quantity }}</div>
                                    </div>
                                    <div class="lg:w-96 flex flex-col gap-2">
                                        @if ($review && !$review->trashed())
                                            <div class="bg-blue-50 border border-blue-200 p-4" style="border-radius:0;">
                                                <div class="mb-2">
                                                    <span class="text-xs text-slate-500">Cập nhật lần cuối: {{ $review->updated_at->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="flex items-center mb-2">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-300' }} text-xl"></i>
                                                    @endfor
                                                </div>
                                                <div class="text-sm text-slate-700 mb-2">{{ $review->comment ?? 'Không có nhận xét' }}</div>
                                                <div class="flex gap-2">
                                                    @if ($review->user_id === auth()->id())
                                                        <a href="{{ route('account.reviews.edit', $review->id) }}" class="px-3 py-1 bg-black text-white text-xs font-medium rounded-none hover:bg-gray-900 transition-colors duration-150">Sửa đánh giá</a>
                                                        <form action="{{ route('account.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-none hover:bg-red-700 transition-colors duration-150">Xóa</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <form action="{{ route('account.review.store') }}" method="POST" class="flex items-center gap-2 mb-2 quick-review-form">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="book_id" value="{{ $item->book_id }}">
                                                <div class="flex items-center space-x-1 quick-star-group" data-order="{{ $order->id }}" data-book="{{ $item->book_id }}">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <input type="radio" id="quick_star{{ $order->id }}_{{ $item->book_id }}_{{ $i }}" name="rating" value="{{ $i }}" class="sr-only" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                                        <label for="quick_star{{ $order->id }}_{{ $item->book_id }}_{{ $i }}" class="quick-star-label cursor-pointer text-2xl text-slate-300 hover:text-yellow-400 transition-colors duration-150" data-star="{{ $i }}">★</label>
                                                    @endfor
                                                </div>
                                                <button type="submit" class="px-3 py-1 bg-black text-white text-xs font-medium rounded-none hover:bg-gray-900 transition-colors duration-150">Gửi nhanh</button>
                                            </form>
                                            <a href="{{ route('account.review.create', ['orderId' => $order->id, 'bookId' => $item->book_id]) }}" class="px-3 py-1 bg-gray-200 text-black text-xs font-medium rounded-none hover:bg-gray-300 transition-colors duration-150">Đánh giá chi tiết</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-black text-white mb-4" style="border-radius:0;">
                            <i class="fas fa-box-open text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-black mb-1">Không có đơn hàng nào</h3>
                        <p class="text-gray-600">Bạn chưa có đơn hàng nào để hiển thị.</p>
                    </div>
                @endforelse
                @if ($orders->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
