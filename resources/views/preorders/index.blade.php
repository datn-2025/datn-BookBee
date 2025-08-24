@extends('layouts.app')
@section('title', 'Đơn đặt trước')

@push('styles')
<style>
    .order-card { transition: all 0.3s ease; border: 2px solid transparent; }
    .order-card:hover { border-color: #000; box-shadow: 0 8px 32px rgba(0,0,0,0.12); transform: translateY(-2px); }
    .status-badge { display: inline-block; padding: 0.5rem 1rem; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .geometric-bg::before { content: ''; position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: rgba(0,0,0,0.05); transform: rotate(45deg) translate(50px, -50px); }
    .btn-outline-black { border: 2px solid #000; color: #000; background: #fff; }
    .btn-outline-black:hover { background:#000; color:#fff; }
    .badge-chip { padding: 0.25rem 0.5rem; border:1px solid #000; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .thumb { width: 64px; height: 84px; object-fit: cover; border:2px solid #e5e7eb; background:#f3f4f6; }
    .label { text-transform: uppercase; letter-spacing: .06em; font-size: .75rem; color:#6b7280; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 geometric-bg">
            <div class="bg-black text-white px-8 py-6 relative">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-1 h-8 bg-white"></div>
                        <h1 class="text-3xl font-black uppercase tracking-wide">ĐƠN ĐẶT TRƯỚC</h1>
                    </div>
                    <p class="text-gray-300 text-sm uppercase tracking-wider">THEO DÕI TẤT CẢ ĐƠN ĐẶT TRƯỚC CỦA BẠN</p>
                </div>
            </div>
        </div>

        @if($preorders->count() > 0)
            <div class="space-y-6">
                @foreach($preorders as $preorder)
                    <div class="bg-white order-card border-2 border-gray-200 relative overflow-hidden">
                        <!-- Card Header -->
                        <div class="bg-gray-50 border-b-2 border-gray-200 px-8 py-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-1 h-6 bg-black"></div>
                                        <h3 class="text-xl font-black uppercase tracking-wide text-black">
                                            ĐƠN ĐẶT TRƯỚC #{{ substr($preorder->id, 0, 8) }}
                                        </h3>
                                    </div>
                                    <p class="text-sm text-gray-600 uppercase tracking-wide">Ngày đặt: {{ $preorder->created_at->format('d/m/Y H:i') }}</p>
                                </div>

                                @php
                                    $statusName = $preorder->status_text;
                                    $statusClass = match($statusName) {
                                        'Chờ duyệt' => 'bg-yellow-500 text-white',
                                        'Đã duyệt' => 'bg-blue-500 text-white',
                                        'Sẵn sàng chuyển đổi' => 'bg-green-600 text-white',
                                        'Đã chuyển thành đơn hàng' => 'bg-green-600 text-white',
                                        'Đã hủy' => 'bg-red-600 text-white',
                                        default => 'bg-gray-500 text-white'
                                    };
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                                    <div class="text-right">
                                        <p class="label">Tổng tiền</p>
                                        <p class="text-2xl font-black text-black">{{ number_format($preorder->total_amount, 0, ',', '.') }}đ</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-8">
                            <div class="flex flex-col lg:flex-row gap-6">
                                <!-- Book -->
                                <div class="flex items-start gap-4 flex-1">
                                    <img class="thumb" src="{{ $preorder->book->cover_image ? asset('storage/' . $preorder->book->cover_image) : asset('images/default-book.svg') }}" alt="{{ $preorder->book->title }}">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            @if($preorder->isEbook())
                                                <span class="badge-chip">EBOOK</span>
                                            @else
                                                <span class="badge-chip">SÁCH VẬT LÝ</span>
                                            @endif
                                        </div>
                                        <h5 class="text-lg font-bold text-black mb-2">{{ $preorder->book->title }}</h5>
                                        @if($preorder->bookFormat)
                                            <p class="text-sm text-gray-600 uppercase tracking-wide mb-2">Định dạng: {{ $preorder->bookFormat->format_name }}</p>
                                        @endif
                                        <div class="flex items-center gap-6 text-sm">
                                            <span class="text-gray-600 uppercase tracking-wide">Số lượng: <span class="font-bold text-black">{{ $preorder->quantity }}</span></span>
                                            <span class="text-gray-600 uppercase tracking-wide">Đơn giá: <span class="font-bold text-black">{{ number_format($preorder->unit_price, 0, ',', '.') }}đ</span></span>
                                            <span class="text-gray-600 uppercase tracking-wide">Dự kiến giao: <span class="font-bold text-black">{{ optional($preorder->expected_delivery_date)->format('d/m/Y') }}</span></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex-shrink-0 flex items-start gap-2">
                                    <a href="{{ route('preorders.show', $preorder) }}" class="px-4 py-2 btn-outline-black font-bold text-xs uppercase tracking-wide transition-all duration-300">
                                        XEM CHI TIẾT
                                    </a>
                                    @if($preorder->canBeCancelled())
                                        <form action="{{ route('preorders.cancel', $preorder) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-4 py-2 border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white font-bold text-xs uppercase tracking-wide transition-all duration-300">
                                                HỦY ĐƠN
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                {{ $preorders->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="mb-6">
                    <i class="ri-bookmark-line" style="font-size: 4rem; color: #e5e7eb;"></i>
                </div>
                <h4 class="text-gray-600 font-bold mb-2">Chưa có đơn đặt trước nào</h4>
                <p class="text-gray-500 mb-4">Bạn chưa đặt trước cuốn sách nào. Hãy khám phá các sách sắp ra mắt!</p>
                <a href="{{ route('home') }}" class="px-5 py-2 btn-outline-black font-bold text-xs uppercase tracking-wide transition-all duration-300">
                    KHÁM PHÁ SÁCH MỚI
                </a>
            </div>
        @endif
    </div>
    
</div>
@endsection
