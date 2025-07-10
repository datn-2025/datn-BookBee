@extends('layouts.backend')

@section('title', 'Quản lý hóa đơn')

@section('content')
    <div class="container-fluid">
        <!-- Tiêu đề trang -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quản lý hóa đơn</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Hóa đơn</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nội dung -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Danh sách hóa đơn</h4>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <!-- Bộ lọc tìm kiếm -->
                        <!-- Bộ lọc tìm kiếm -->
                        <form method="GET" action="{{ route('admin.invoices.index') }}" autocomplete="off">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <!-- Mã đơn hàng -->
                                        <div class="col-md-6">
                                            <label for="search_order_code" class="form-label">Mã đơn hàng</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-hashtag"></i></span>
                                                <input type="text" name="search_order_code" id="search_order_code"
                                                    class="form-control" placeholder="Nhập mã đơn hàng"
                                                    value="{{ request()->get('search_order_code') }}">
                                            </div>
                                        </div>

                                        <!-- Ngày tạo -->
                                        <div class="col-md-6">
                                            <label class="form-label">Ngày tạo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                                                <input type="date" class="form-control" name="start_date"
                                                    placeholder="Từ ngày" value="{{ request()->get('start_date') }}">
                                                <input type="date" class="form-control" name="end_date"
                                                    placeholder="Đến ngày" value="{{ request()->get('end_date') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <!-- Thông tin khách hàng -->
                                        <div class="col-md-6">
                                            <label for="customer_name" class="form-label">Thông tin khách hàng</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                <input type="text" name="customer_name" id="customer_name"
                                                    class="form-control" placeholder="Tên khách hàng"
                                                    value="{{ request()->get('customer_name') }}">
                                            </div>
                                            <div class="input-group mt-2">
                                                <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                                <input type="text" name="customer_email" id="customer_email"
                                                    class="form-control" placeholder="Email khách hàng"
                                                    value="{{ request()->get('customer_email') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <!-- Phương thức thanh toán -->
                                                <div class="col-12">
                                                    <label for="payment_method" class="form-label">Phương thức thanh
                                                        toán</label>
                                                    <select name="payment_method" id="payment_method" class="form-select">
                                                        <option value="">Tất cả phương thức</option>
                                                        @foreach ($paymentMethods as $method)
                                                            <option value="{{ $method->id }}"
                                                                {{ request()->get('payment_method') == $method->id ? 'selected' : '' }}>
                                                                {{ $method->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút tìm kiếm -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-search-2-line me-1"></i> Tìm kiếm
                                        </button>
                                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-light">
                                            <i class="ri-refresh-line me-1"></i> Làm mới bộ lọc
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Bảng hóa đơn -->
                        <div class="table-responsive table-card mt-3">
                            @if ($invoices->isEmpty())
                                <div class="noresult text-center py-5">
                                    @if (filled(request()->get('search_invoice_code')))
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                        </lord-icon>
                                        <h5 class="mt-3 text-danger">Không tìm thấy hóa đơn phù hợp</h5>
                                        <p class="text-muted">
                                            Không có hóa đơn nào khớp với từ khóa
                                            <strong>"{{ request()->get('search_invoice_code') }}"</strong>.<br>
                                            Vui lòng kiểm tra lại từ khóa hoặc thử tìm kiếm khác.
                                        </p>
                                    @else
                                        <lord-icon src="https://cdn.lordicon.com/nocovwne.json" trigger="loop"
                                            colors="primary:#405189,secondary:#0ab39c" style="width:100px;height:100px">
                                        </lord-icon>
                                        <h5 class="mt-3 text-muted">Danh sách hóa đơn hiện đang trống</h5>
                                        <p class="text-muted">Hãy nhấn <strong>“Tạo hóa đơn”</strong> để bắt đầu quản lý
                                            hóa đơn.</p>
                                    @endif
                                </div>
                            @else
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Mã đơn hàng</th>
                                            <th>Khách hàng</th>
                                            <th>Phương thức</th>
                                            <th>Tổng tiền</th>
                                            <th>Ngày tạo</th>
                                            <th class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoices as $key => $invoice)
                                            <tr>
                                                <td>{{ $invoices->firstItem() + $key }}</td>
                                                <td class="fw-medium text-primary">#{{ $invoice->order->order_code }}</td>
                                                <td>
                                                    <div class="fw-medium">{{ $invoice->order->user->name }}</div>
                                                    <small class="text-muted"><i
                                                            class="fas fa-envelope-open me-1"></i>{{ $invoice->order->user->email }}</small>
                                                </td>
                                                <td>{{ $invoice->order->paymentMethod->name ?? 'Không xác định' }}</td>
                                                <td>{{ number_format($invoice->total_amount) }}đ</td>
                                                <td>{{ $invoice->created_at->format('H:i:s d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                                            class="btn btn-sm btn-info" title="Xem chi tiết">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <a href="{{ route('admin.invoices.generate-pdf', $invoice->id) }}"
                                                            target="_blank" class="btn btn-sm btn-primary"
                                                            title="Tải PDF">
                                                            <i class="ri-file-pdf-line"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Phân trang -->
                                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                                    <div class="text-muted">
                                        Hiển thị <strong>{{ $invoices->firstItem() }}</strong> đến
                                        <strong>{{ $invoices->lastItem() }}</strong> trên tổng số
                                        <strong>{{ $invoices->total() }}</strong> hóa đơn
                                    </div>
                                    <div>
                                        {{ $invoices->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @endif
                        </div> <!-- table-card -->
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div>
        </div>
    </div>
@endsection
