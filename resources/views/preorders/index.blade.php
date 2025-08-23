@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="ri-bookmark-line me-2"></i>Đơn Đặt Trước Của Tôi</h2>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="ri-arrow-left-line me-1"></i>Về trang chủ
                </a>
            </div>

            @if($preorders->count() > 0)
                <div class="row">
                    @foreach($preorders as $preorder)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <!-- Header với status -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <small class="text-muted">#{{ substr($preorder->id, 0, 8) }}</small>
                                        <span class="badge 
                                            @switch($preorder->status)
                                                @case(\App\Models\Preorder::STATUS_CHO_DUYET) bg-warning @break
                        @case(\App\Models\Preorder::STATUS_DA_DUYET) bg-info @break
                        @case(\App\Models\Preorder::STATUS_SAN_SANG_CHUYEN_DOI) bg-success @break
                        @case(\App\Models\Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG) bg-primary @break
                        @case(\App\Models\Preorder::STATUS_DA_HUY) bg-danger @break
                                                @case('processing') bg-primary @break
                                                @case('shipped') bg-success @break
                                                @case('delivered') bg-success @break
                                                @case('cancelled') bg-danger @break
                                                @default bg-secondary @break
                                            @endswitch
                                        ">
                                            {{ $preorder->status_text }}
                                        </span>
                                    </div>

                                    <!-- Book info -->
                                    <div class="d-flex mb-3">
                                        <img src="{{ $preorder->book->cover_image ? asset('storage/' . $preorder->book->cover_image) : asset('images/default-book.svg') }}" 
                                             alt="{{ $preorder->book->title }}" 
                                             class="me-3 rounded" style="width: 60px; height: 80px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-truncate">{{ $preorder->book->title }}</h6>
                                            <small class="text-muted">
                                                {{ $preorder->bookFormat ? $preorder->bookFormat->format_name : 'N/A' }}
                                            </small>
                                            <div class="mt-1">
                                                <small class="text-muted">Số lượng: {{ $preorder->quantity }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price and date -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold text-success">{{ number_format($preorder->total_amount, 0, ',', '.') }}đ</span>
                                            <small class="text-muted">
                                                <i class="ri-calendar-line me-1"></i>
                                                {{ $preorder->expected_delivery_date->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Type indicator -->
                                    <div class="mb-3">
                                        @if($preorder->isEbook())
                                            <span class="badge bg-primary-subtle text-primary">
                                                <i class="ri-smartphone-line me-1"></i>Ebook
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                <i class="ri-book-line me-1"></i>Sách vật lý
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Order date -->
                                    <small class="text-muted">
                                        Đặt ngày: {{ $preorder->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>

                                <div class="card-footer bg-light">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('preorders.show', $preorder) }}" 
                                           class="btn btn-outline-primary btn-sm flex-grow-1">
                                            <i class="ri-eye-line me-1"></i>Xem chi tiết
                                        </a>
                                        @if($preorder->canBeCancelled())
                                            <form action="{{ route('preorders.cancel', $preorder) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn này?')"
                                                        title="Hủy đơn">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $preorders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="ri-bookmark-line" style="font-size: 4rem; color: #dee2e6;"></i>
                    </div>
                    <h4 class="text-muted">Chưa có đơn đặt trước nào</h4>
                    <p class="text-muted">Bạn chưa đặt trước cuốn sách nào. Hãy khám phá các sách sắp ra mắt!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="ri-search-line me-1"></i>Khám phá sách mới
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    font-size: 0.8rem;
    padding: 0.375rem 0.75rem;
}
</style>
@endsection
