@extends('layouts.backend')
@section('title', 'Chi tiết hóa đơn')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="card shadow mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <h1 class="h3 text-gray-800 mb-0">HÓA ĐƠN BÁN HÀNG</h1>
                            <span class="badge bg-success ms-3">
                                {{ $invoice->order->orderStatus->name }}
                            </span>
                        </div>
                        <div class="text-muted">
                            <span class="me-3"><i class="fas fa-hashtag me-1"></i>#{{ $invoice->order->order_code }}</span>
                            Ngày thanh toán: <span class="me-3"><i
                                    class="far fa-calendar-alt me-1"></i>{{ $invoice->created_at->format('H:i:s d/m/Y') }}</span>
                            Ngày in hóa đơn: <span class="me-3"><i
                                    class="far fa-calendar-alt me-1"></i>{{ now()->format('H:i:s d/m/Y') }}</span>
                            <span class="badge bg-success">
                                {{ $invoice->order->paymentStatus->name }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3 mt-sm-0">
                        <a href="{{ route('admin.invoices.generate-pdf', $invoice->id) }}" class="btn btn-primary">
                            <i class="fas fa-file-pdf me-2"></i>Xuất PDF
                        </a>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Thông tin khách hàng -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light py-3">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-user me-2"></i>THÔNG TIN KHÁCH HÀNG
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="fw-bold text-primary mb-1">
                                <i class="fas fa-user-circle me-2"></i>Tài khoản khách hàng
                            </div>
                            <div class="ps-3">
                                <h5 class="mb-1">{{ $invoice->order->user->name }}</h5>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-envelope me-2"></i>{{ $invoice->order->user->email }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-phone me-2"></i>{{ $invoice->order->user->phone ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="fw-bold text-primary mb-1">
                                <i class="fas fa-user-friends me-2"></i>Người nhận hàng
                            </div>
                            <div class="ps-3">
                                <h5 class="mb-1">{{ $invoice->order->recipient_name }}</h5>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-envelope me-2"></i>{{ $invoice->order->recipient_email ?? 'N/A' }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-phone me-2"></i>{{ $invoice->order->recipient_phone ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold text-primary mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng
                            </div>
                            <div class="ps-3">
                                <p class="mb-0">
                                    {{ $invoice->order->address->full_address ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin thanh toán -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light py-3">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-credit-card me-2"></i>THANH TOÁN
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1">
                                <span class="text-muted">Phương thức:</span>
                                <span class="float-end">{{ $invoice->order->paymentMethod->name ?? 'N/A' }}</span>
                            </p>
                            <p class="mb-1">
                                <span class="text-muted">Trạng thái:</span>
                                <span
                                    class="float-end badge bg-{{ $invoice->order->paymentStatus->name == 'Đã Thanh Toán' ? 'success' : 'warning' }}">
                                    {{ $invoice->order->paymentStatus->name }}
                                </span>
                            </p>
                            @if ($invoice->order->paid_at)
                                <p class="mb-0">
                                    <span class="text-muted">Ngày thanh toán:</span>
                                    <span class="float-end">{{ $invoice->order->paid_at->format('H:i:s d/m/Y') }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng quan đơn hàng -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light py-3">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-receipt me-2"></i>TỔNG QUAN
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            @php
                                $totalQuantity = 0;
                            @endphp
                            @foreach ($invoice->items as $item)
                                @php
                                    $totalQuantity += $item->quantity;
                                @endphp
                            @endforeach
                            <tr>
                                <td class="border-0 ps-0">Tổng số lượng:</td>
                                <td class="border-0 text-end">{{ number_format($totalQuantity) }}</td>
                            </tr>
                            <tr>
                                <td class="border-0 ps-0">Tạm tính:</td>
                                <td class="border-0 text-end">{{ number_format($invoice->subtotal) }}đ</td>
                            </tr>
                            @if ($invoice->order->discount_amount > 0)
                                <tr>
                                    <td class="border-0 ps-0">Giảm giá:</td>
                                    <td class="border-0 text-end text-danger">
                                        -{{ number_format($invoice->order->discount_amount) }}đ
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="border-0 ps-0">Phí vận chuyển:</td>
                                <td class="border-0 text-end">{{ number_format($invoice->order->shipping_fee) }}đ</td>
                            </tr>
                            <tr class="table-active">
                                <th class="border-0 ps-0">Tổng cộng:</th>
                                <th class="border-0 text-end">{{ number_format($invoice->total_amount) }}đ</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết sản phẩm -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-shopping-cart me-2"></i>CHI TIẾT SẢN PHẨM
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Sản phẩm</th>
                                <th class="text-end pe-4">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end pe-4">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->book->thumbnail ?? 'https://via.placeholder.com/60x80' }}"
                                                alt="{{ $item->book->title }}" class="me-3"
                                                style="width: 60px; height: 80px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ $item->book->title }}</h6>
                                                <p class="text-muted small mb-0">
                                                    Tác giả: {{ $item->book->author->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end align-middle pe-4">{{ number_format($item->price) }}đ</td>
                                    <td class="text-center align-middle">{{ $item->quantity }}</td>
                                    <td class="text-end align-middle pe-4">
                                        {{ number_format($item->price * $item->quantity) }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ghi chú đơn hàng -->
        @if ($invoice->note)
            <div class="card border-left-info shadow mb-4">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="icon-circle bg-info text-white me-3">
                            <i class="fas fa-info"></i>
                        </div>
                        <div>
                            <h6 class="text-info mb-1">Ghi chú đơn hàng</h6>
                            <p class="mb-0">{{ $invoice->note }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
