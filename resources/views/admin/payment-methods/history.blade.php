@extends('layouts.backend')

@section('title', 'Lịch Sử Thanh Toán')
@section('content')
<!-- Page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản lý thanh toán</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">admin</a></li>
                    <li class="breadcrumb-item active">Lịch sử thanh toán</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- End page title -->

<div class="container-fluid">

    <!-- Bộ lọc tìm kiếm và trạng thái -->
    <div class="card mb-4">
        <div class="card-body border-bottom py-4">
            <h5 class="mb-4">Lịch Sử Thanh Toán</h5>

            <form method="GET" action="{{ route('admin.payment-methods.history') }}">
                <div class="row g-3 align-items-center">
                    <!-- Ô tìm kiếm -->
                    <div class="col-lg-4">
                        <input type="text" name="search" class="form-control ps-4" placeholder="🔍 Tìm kiếm đơn hàng..."
                            value="{{ request('search') }}">
                    </div>

                    <!-- Trạng thái thanh toán -->
                    <div class="col-lg-auto" style="min-width: 190px;">
                        <select class="form-select" name="payment_status">
                            <option value="">✨ Tất cả trạng thái</option>
                            <option value="Chờ xử lý" {{ request('payment_status') == 'Chờ xử lý' ? 'selected' : '' }}>
                                Chờ xử lý
                            </option>
                            <option value="Chưa thanh toán" {{ request('payment_status') == 'Chưa thanh toán' ? 'selected' : '' }}>
                                Chưa thanh toán
                            </option>
                            <option value="Đã Thanh Toán" {{ request('payment_status') == 'Đã Thanh Toán' ? 'selected' : '' }}>
                                Đã Thanh Toán
                            </option>
                            <option value="Thất bại" {{ request('payment_status') == 'Thất bại' ? 'selected' : '' }}>
                                Thất bại
                            </option>
                        </select>
                    </div>

                    <!-- Nút lọc + đặt lại -->
                    <div class="col-lg-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4" style="min-width: 130px;">
                            <i class="ri-filter-3-line me-1"></i> Lọc
                        </button>
                        <a href="{{ route('admin.payment-methods.history') }}" class="btn btn-outline-secondary px-4"
                            style="min-width: 130px;">
                            <i class="ri-refresh-line me-1"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng lịch sử thanh toán -->
    <div class="card">
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted">
                        <th>STT</th>
                        <th>Order Code</th>
                        <th>Phương thức thanh toán</th>
                        <th>Số tiền</th>
                        <th>Ngày thanh toán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $key => $payment)
                    <tr>
                        <td>{{ $payments->firstItem() + $key }}</td>
                        <td>{{ $payment->order->order_code }}</td>
                        <td>{{ $payment->paymentMethod->name ?? 'Không xác định' }}</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->paid_at }}</td>
                        <td>
                            @if($payment->paymentStatus->name == 'Đã Thanh Toán')
                                <span class="badge bg-success">{{ $payment->paymentStatus->name }}</span>
                            @elseif($payment->paymentStatus->name == 'Chưa thanh toán')
                                <span class="badge bg-warning text-dark">{{ $payment->paymentStatus->name }}</span>
                            @elseif($payment->paymentStatus->name == 'Chờ Xử Lý')
                                <span class="badge bg-primary">{{ $payment->paymentStatus->name }}</span>
                            @else
                                <span class="badge bg-danger">{{ $payment->paymentStatus->name }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Không tìm thấy giao dịch nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="d-flex justify-content-end mt-4">
                <nav>
                    @if ($payments->hasPages())
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            @if ($payments->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Prev</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $payments->previousPageUrl() }}" rel="prev">Prev</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                                @if ($page == $payments->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($payments->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $payments->nextPageUrl() }}" rel="next">Next</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                            @endif
                        </ul>
                    @endif
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection