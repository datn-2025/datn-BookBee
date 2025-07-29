@extends('layouts.backend')

@section('title', 'Quản lý đơn hàng')

@section('styles')
<link href="{{ asset('css/admin-orders.css') }}" rel="stylesheet">
@endsection

@section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quản lý đơn hàng</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Đơn hàng</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Order summary cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Tổng đơn hàng</p>
                                <h4 class="fs-22 fw-semibold mb-0">{{ $orders->total() }}</h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary rounded fs-3">
                                    <i class="ri-shopping-bag-line text-primary"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Chờ Xác Nhận</p>
                                <h4 class="fs-22 fw-semibold mb-0">{{ $orderCounts['Chờ Xác Nhận'] }}</h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-warning rounded fs-3">
                                    <i class="ri-time-line text-warning"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Đã giao hàng</p>
                                <h4 class="fs-22 fw-semibold mb-0">{{ $orderCounts['Đã Giao Thành Công'] }}</h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-success rounded fs-3">
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Đã hủy</p>
                                <h4 class="fs-22 fw-semibold mb-0">{{ $orderCounts['Đã Hủy'] }}</h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-danger rounded fs-3">
                                    <i class="ri-close-circle-line text-danger"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Danh sách đơn hàng</h5>
                            <div class="flex-shrink-0">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" disabled class="btn btn-soft-primary btn-sm">
                                        <i class="ri-file-list-3-line align-middle"></i> Xuất Excel
                                    </button>
                                    <button type="button" disabled class="btn btn-soft-secondary btn-sm">
                                        <i class="ri-file-pdf-line align-middle"></i> Xuất PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border border-dashed border-end-0 border-start-0">
                        <form action="{{ route('admin.orders.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-xxl-3 col-sm-6">
                                    <div class="search-box">
                                        <input type="text" class="form-control search"
                                            placeholder="Tìm kiếm theo mã đơn hàng, tên khách hàng..."
                                            name="search" value="{{ request('search') }}">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <div class="col-xxl-2 col-sm-6">
                                    <div>
                                        <select class="form-control" data-choices data-choices-search-false
                                            name="status">
                                            <option value="">Trạng thái đơn hàng</option>
                                                @foreach ($orderStatuses as $status)
                                                    <option value="{{ $status->name }}" {{ request('status') == $status->name ? 'selected' : '' }}>{{ $status->name }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-2 col-sm-6">
                                    <div>
                                        <select class="form-control" data-choices data-choices-search-false
                                            name="payment">
                                            <option value="">Trạng thái thanh toán</option>
                                            @foreach ($paymentStatuses as $status)
                                                <option value="{{ $status->name }}" {{ request('payment') == $status->name ? 'selected' : '' }}>{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-2 col-sm-6">
                                    <div>
                                        <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 d-flex">
                                    <button type="submit" class="btn btn-primary me-2" style="background-color:#405189; border-color: #405189">
                                        <i class="ri-search-2-line"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light me-2">
                                        <i class="ri-refresh-line"></i> Đặt lại
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-nowrap mb-0">
                                <thead class="text-muted table-light">
                                    <tr>
                                        <th scope="col">Mã đơn hàng</th>
                                        <th scope="col">QR Code</th>
                                        <th scope="col">Khách hàng</th>
                                        <th scope="col">Địa chỉ</th>
                                        <th scope="col">Phương thức nhận hàng</th>
                                        <th scope="col">Tổng tiền</th>
                                        <th scope="col">Trạng thái đơn hàng</th>
                                        <th scope="col">Ngày Tạo</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="fw-medium">{{$order->order_code }}</span>
                                                    @if($order->delivery_method === 'mixed')
                                                        <span class="badge bg-warning text-dark ms-2">📦📱 HỖN HỢP</span>
                                                    @endif
                                                </div>
                                                @if($order->parent_order_id)
                                                    <div class="d-flex align-items-center">
                                                        <small class="text-muted me-2">Thuộc đơn hàng:</small>
                                                        <a href="{{ route('admin.orders.show', $order->parent_order_id) }}" 
                                                           class="parent-order-link">
                                                            <i class="ri-parent-line me-1"></i>{{ $order->parentOrder->order_code ?? 'N/A' }}
                                                        </a>
                                                    </div>
                                                @elseif($order->childOrders && $order->childOrders->count() > 0)
                                                    <div class="d-flex align-items-center">
                                                        <small class="text-muted me-2">Có {{ $order->childOrders->count() }} đơn con:</small>
                                                        @foreach($order->childOrders as $child)
                                                            <a href="{{ route('admin.orders.show', $child->id) }}" 
                                                               class="child-order-link">
                                                                <i class="ri-git-branch-line me-1"></i>{{ $child->order_code }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($order->qr_code)
                                            <img src="{{ url('storage/private/' . $order->qr_code) }}" alt="QR Code"
                                                class="avatar-sm rounded">
                                            @else
                                            <span class="badge bg-light text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    @if ($order->user->avatar)
                                                    <img src="{{ asset('storage/' . $order->user->avatar) }} " alt=""
                                                        class="avatar-xs rounded-circle">
                                                    @else
                                                    <img src="{{ asset('assets/images/users/avatar-1.jpg') }} " alt=""
                                                        class="avatar-xs rounded-circle">
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    {{ $order->user->name ?? 'N/A' }}
                                                    <span class="text-muted d-block">{{ $order->user->email ?? ''
                                                        }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                @if($order->delivery_method === 'ebook')
                                                    Ebook
                                                @elseif($order->address)
                                                    {{ $order->address->district ?? 'N/A' }},
                                                    {{ $order->address->city ?? '' }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                @if($order->delivery_method === 'ebook')
                                                    <span class="order-type-icon order-type-ebook">
                                                        <i class="ri-smartphone-line"></i>
                                                    </span>
                                                    <span>Ebook</span>
                                                @elseif($order->delivery_method === 'pickup')
                                                    <span class="order-type-icon order-type-pickup">
                                                        <i class="ri-store-2-line"></i>
                                                    </span>
                                                    <span>Nhận tại cửa hàng</span>
                                                @elseif($order->delivery_method === 'mixed')
                                                    <span class="order-type-icon order-type-mixed">
                                                        <i class="ri-shuffle-line"></i>
                                                    </span>
                                                    <span>Hỗn hợp</span>
                                                @else
                                                    <span class="order-type-icon order-type-physical">
                                                        <i class="ri-truck-line"></i>
                                                    </span>
                                                    <span>Giao hàng tận nơi</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="fw-medium">{{ number_format($order->total_amount, 0, ',', '.') }}đ
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $paymentClass = match($order->paymentStatus->name) {
                                                    'Đã Thanh Toán' => 'status-delivered',
                                                    'Chưa Thanh Toán' => 'status-pending',
                                                    'Đã Hoàn Tiền' => 'status-refunded',
                                                    'Thất Bại' => 'status-cancelled',
                                                    default => 'status-pending'
                                                };
                                                $paymentIcon = match($order->paymentStatus->name) {
                                                    'Đã Thanh Toán' => 'ri-money-dollar-circle-line',
                                                    'Chưa Thanh Toán' => 'ri-time-line',
                                                    'Đã Hoàn Tiền' => 'ri-refund-2-line',
                                                    'Thất Bại' => 'ri-close-circle-line',
                                                    default => 'ri-question-line'
                                                };
                                                $statusClass = match($order->orderStatus->name) {
                                                    'Chờ xác nhận' => 'status-pending',
                                                    'Đã Xác Nhận' => 'status-confirmed',
                                                    'Đang xử lý' => 'status-confirmed',
                                                    'Đang giao hàng' => 'status-shipping',
                                                    'Đã giao thành công' => 'status-delivered',
                                                    'Đã Hủy' => 'status-cancelled',
                                                    'Giao thất bại' => 'status-cancelled',
                                                    default => 'status-pending'
                                                };
                                                $statusIcon = match($order->orderStatus->name) {
                                                    'Chờ xác nhận' => 'ri-time-line',
                                                    'Đã Xác Nhận' => 'ri-check-line',
                                                    'Đang xử lý' => 'ri-settings-3-line',
                                                    'Đang giao hàng' => 'ri-truck-line',
                                                    'Đã giao thành công' => 'ri-check-double-line',
                                                    'Đã Hủy' => 'ri-close-line',
                                                    'Giao thất bại' => 'ri-error-warning-line',
                                                    default => 'ri-question-line'
                                                };
                                            @endphp
                                            <span class="order-status-badge {{ $paymentClass }}">
                                                <i class="{{ $paymentIcon }} me-1"></i>
                                                {{ $order->paymentStatus->name ?? 'N/A' }}
                                            </span>
                                            <br>
                                            <span class="order-status-badge {{ $statusClass }}">
                                                <i class="{{ $statusIcon }} me-1"></i>
                                                {{ $order->orderStatus->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                                            class="dropdown-item">
                                                            <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Chi
                                                            tiết
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                                                            class="dropdown-item">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                            Cập nhật
                                                        </a>
                                                    </li>
                                                    <!-- <li class="dropdown-divider"></li>
                                                    <li>
                                                        <a href="{{ route('admin.invoices.show', $order->id) }}" class="dropdown-item">
                                                            <i class="ri-printer-fill align-bottom me-2 text-muted"></i>
                                                            In hóa đơn
                                                        </a>
                                                    </li> -->
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="py-4">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                    colors="primary:#405189,secondary:#0ab39c"
                                                    style="width:72px;height:72px"></lord-icon>
                                                <h5 class="mt-4">Không có đơn hàng nào</h5>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $orders->links(('layouts.pagination')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
<script src="https://cdn.lordicon.com/bhenfmcm.js"></script>
@endsection