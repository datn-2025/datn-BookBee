@extends('layouts.backend')

@section('title', 'Chi tiết đơn hàng')

@section('styles')
<link href="{{ asset('css/admin-orders.css') }}" rel="stylesheet">
@endsection

@section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Chi tiết đơn hàng</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                            @if($order->parent_order_id)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.orders.show', $order->parent_order_id) }}">
                                        <i class="ri-parent-line me-1"></i>{{ $order->parentOrder->order_code ?? 'Đơn cha' }}
                                    </a>
                                </li>
                            @endif
                            <li class="breadcrumb-item active">{{ $order->order_code }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title flex-grow-1 mb-0">
                                 Mã đơn hàng: #{{ $order->order_code }}
                                 @if($order->delivery_method === 'mixed')
                                     <span class="badge bg-warning text-dark ms-2">📦📱 ĐƠN HÀNG HỖN HỢP</span>
                                 @endif
                                 @if($order->parent_order_id)
                                     <br><small class="text-muted">Đơn hàng con thuộc: 
                                         <a href="{{ route('admin.orders.show', $order->parent_order_id) }}" class="text-primary">
                                             <i class="ri-parent-line"></i> {{ $order->parentOrder->order_code ?? 'N/A' }}
                                         </a>
                                     </small>
                                 @endif
                             </h5>
                            <div class="flex-shrink-0">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-success btn-sm">
                                        <i class="ri-pencil-fill align-middle me-1"></i> Cập nhật trạng thái
                                    </a>
                                    <!-- @if($order->orderStatus->name === 'Thành công' && $order->paymentStatus->name === 'Đã Thanh Toán' && !in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền']))
                                        <a href="{{ route('admin.refunds.show', $order->id) }}" class="btn btn-warning btn-sm">
                                            <i class="ri-refund-2-line align-middle me-1"></i> Hoàn tiền
                                        </a>
                                    @endif -->
                                    <a href="{{ route('admin.invoices.generate-pdf', $order->invoice->id) }}">
                                    <button type="button" class="btn btn-primary btn-sm">
                                        <i class="ri-printer-fill align-middle me-1"></i> In hóa đơn
                                    </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <h5 class="text-muted mb-3">Thông tin khách hàng</h5>
                                    <div class="card border shadow-none mb-2">
                                        <div class="card-body">
                                           <a href="{{route('admin.users.show' , $order->user_id)}}" style="text-decoration: none; color: #0a0c0d">
                                               <div class="d-flex mb-3">
                                                   <div class="flex-shrink-0">
                                                       <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="" class="avatar-sm rounded">
                                                   </div>
                                                   <div class="flex-grow-1 ms-3">
                                                       <h6 class="fs-15 mb-1">{{ $order->user->name ?? 'N/A' }}</h6>
                                                       <p class="text-muted mb-0">Người Đặt</p>
                                                   </div>
                                               </div>
                                               <ul class="list-unstyled mb-0 vstack gap-2">
                                                   <li>
                                                       <div class="d-flex">
                                                           <div class="flex-shrink-0 text-muted">
                                                               <i class="ri-mail-line me-1 fs-16 align-middle"></i>
                                                           </div>
                                                           <div class="flex-grow-1">
                                                               <span>{{ $order->user->email ?? 'N/A' }}</span>
                                                           </div>
                                                       </div>
                                                   </li>
                                               </ul>
                                           </a>
                                        </div>
                                    </div>
                                    <div class="card border shadow-none mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between ">
                                                <div>
                                                    <p class="text-muted mb-2">Người nhận</p>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa-solid fa-signature"></i>
                                                        <h6 class="fs-15 mb-0 ms-2">{{ $order->recipient_name ?? 'N/A' }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-phone-line me-2 fs-16 text-muted"></i>
                                                <span>{{ $order->recipient_phone ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <h5 class="text-muted mb-3">Thông tin giao hàng</h5>
                                    <div class="card border shadow-none mb-2">
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0 vstack gap-2">
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 text-muted">
                                                            <i class="ri-map-pin-line me-1 fs-16 align-middle"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fs-14">Địa chỉ giao hàng:</h6>
                                                            <p class="text-muted mb-0">
                                                                @if($order->delivery_method === 'ebook')
                                                                    Ebook - Không cần địa chỉ giao hàng
                                                                @elseif($order->address)
                                                                    {{ $order->address->address_detail }}
                                                                @else
                                                                    Không có thông tin
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </li>
                                                @if($order->address && $order->delivery_method !== 'ebook')
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 text-muted">
                                                            <i class="ri-mail-send-line me-1 fs-16 align-middle"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fs-14">Huyện:</h6>
                                                            <p class="text-muted mb-0">{{ $order->address->district . ', ' .  $order->address->ward }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endif
                                                @if($order->delivery_method !== 'ebook')
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 text-muted">
                                                            <i class="ri-building-line me-1 fs-16 align-middle"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fs-14">Thành phố:</h6>
                                                            <p class="text-muted mb-0">{{ $order->address->city ?? 'không có địa chỉ' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endif
                                                <li>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 text-muted">
                                                            <i class="ri-truck-line me-1 fs-16 align-middle"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fs-14">Phương thức nhận hàng:</h6>
                                                            <p class="text-muted mb-0">
                                                                @if($order->delivery_method === 'pickup')
                                                                    <span class="badge bg-info">Nhận tại cửa hàng</span>
                                                                @elseif($order->delivery_method === 'ebook')
                                                                    <span class="badge bg-success">Nhận qua email</span>
                                                                @else
                                                                    <span class="badge bg-primary">Giao hàng tận nơi</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="text-muted mb-0 flex-grow-1">Trạng thái đơn hàng</h5>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <div class="d-flex mb-2">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-success rounded-circle shadow fs-3">
                                                        <i class="ri-truck-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Trạng thái đơn hàng</h6>
                                                @php
                                                    $orderName = trim($order->orderStatus->name ?? '');
                                                    $statusIcon = match($orderName) {
                                                        'Chờ xác nhận' => 'ri-time-line',
                                                        'Đã Xác Nhận', 'Đang xử lý', 'Đang chuẩn bị' => 'ri-settings-3-line',
                                                        'Đang giao hàng', 'Đang giao' => 'ri-truck-line',
                                                        'Đã giao thành công', 'Thành công', 'Đã nhận hàng' => 'ri-check-double-line',
                                                        'Đang Hoàn Tiền', 'Đã Hoàn Tiền' => 'ri-refund-2-line',
                                                        'Đã Hủy' => 'ri-close-line',
                                                        'Giao thất bại' => 'ri-error-warning-line',
                                                        default => 'ri-question-line'
                                                    };
                                                    $statusBg = match($orderName) {
                                                        'Chờ xác nhận' => 'bg-warning text-dark',
                                                        'Đã Xác Nhận', 'Đang xử lý', 'Đang chuẩn bị' => 'bg-primary',
                                                        'Đang giao hàng', 'Đang giao' => 'bg-info text-dark',
                                                        'Đã giao thành công', 'Thành công', 'Đã nhận hàng' => 'bg-success',
                                                        'Đang Hoàn Tiền', 'Đã Hoàn Tiền' => 'bg-info text-dark',
                                                        'Đã Hủy', 'Giao thất bại' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <p class="text-muted mb-0">
                                                    <span class="badge rounded-pill {{ $statusBg }}">
                                                        <i class="{{ $statusIcon }} me-1"></i>
                                                        {{ $order->orderStatus->name ?? 'N/A' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <div class="d-flex mb-2">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-success rounded-circle shadow fs-3">
                                                        <i class="ri-bank-card-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Trạng thái thanh toán</h6>
                                                @php
                                                    $paymentName = trim($order->paymentStatus->name ?? '');
                                                    $paymentIcon = match($paymentName) {
                                                        'Đã Thanh Toán', 'Thanh toán thành công' => 'ri-money-dollar-circle-line',
                                                        'Chưa Thanh Toán', 'Chờ thanh toán', 'Chờ Xử Lý' => 'ri-time-line',
                                                        'Đã Hoàn Tiền', 'Hoàn tiền', 'Đang Hoàn Tiền' => 'ri-refund-2-line',
                                                        'Thất Bại' => 'ri-close-circle-line',
                                                        default => 'ri-question-line'
                                                    };
                                                    $paymentBg = match($paymentName) {
                                                        'Đã Thanh Toán', 'Thanh toán thành công' => 'bg-success',
                                                        'Chưa Thanh Toán', 'Chờ thanh toán', 'Chờ Xử Lý' => 'bg-secondary',
                                                        'Đã Hoàn Tiền', 'Hoàn tiền', 'Đang Hoàn Tiền' => 'bg-info',
                                                        'Thất Bại' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <p class="text-muted mb-0">
                                                    <span class="badge rounded-pill {{ $paymentBg }}">
                                                        <i class="{{ $paymentIcon }} me-1"></i>
                                                        {{ $order->paymentStatus->name ?? 'N/A' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($order->delivery_method === 'mixed' && $order->isParentOrder())
                        <div class="mt-4">
                            <h5 class="text-muted mb-3">
                                <i class="ri-git-branch-line me-2"></i>Thông tin đơn hàng con ({{ $order->childOrders->count() }})
                            </h5>
                            <div class="row">
                                @foreach($order->childOrders as $childOrder)
                                <div class="col-md-6 mb-3">
                                     <div class="child-order-card">
                                        <div class="child-order-header">
                                            @if($childOrder->delivery_method === 'delivery')
                                                <div class="child-order-icon order-type-physical">
                                                    <i class="ri-truck-line"></i>
                                                </div>
                                            @else
                                                <div class="child-order-icon order-type-ebook">
                                                    <i class="ri-smartphone-line"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $childOrder->order_code }}</h6>
                                                <small class="text-muted">
                                                    @if($childOrder->delivery_method === 'delivery')
                                                        Đơn hàng sách vật lý
                                                    @else
                                                        Đơn hàng ebook
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Tổng tiền:</small>
                                                <span class="fw-bold text-primary">{{ number_format($childOrder->total_amount, 0, ',', '.') }}đ</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Trạng thái:</small>
                                                @php
                                                    $statusClass = match($childOrder->orderStatus->name) {
                                                        'Chờ Xác Nhận' => 'status-pending',
                                                        'Đã Xác Nhận' => 'status-confirmed',
                                                        'Đang Giao Hàng' => 'status-shipping',
                                                        'Đã Giao Thành Công' => 'status-delivered',
                                                        'Thành công' => 'status-delivered',
                                                        'Đã Hủy' => 'status-cancelled',
                                                        default => 'status-pending'
                                                    };
                                                    $statusIcon = match($childOrder->orderStatus->name) {
                                                        'Chờ Xác Nhận' => 'ri-time-line',
                                                        'Đã Xác Nhận' => 'ri-check-line',
                                                        'Đang Giao Hàng' => 'ri-truck-line',
                                                        'Đã Giao Thành Công' => 'ri-check-double-line',
                                                        'Thành công' => 'ri-check-double-line',
                                                        'Đã Hủy' => 'ri-close-line',
                                                        default => 'ri-question-line'
                                                    };
                                                @endphp
                                                <span class="order-status-badge {{ $statusClass }} small">
                                                    <i class="{{ $statusIcon }} me-1"></i>
                                                    {{ $childOrder->orderStatus->name }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('admin.orders.show', $childOrder->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ri-eye-line me-1"></i> Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($order->delivery_method !== 'mixed' && !$order->isParentOrder())
                        <div class="mt-4">
                            <h5 class="text-muted mb-3">Chi tiết đơn hàng</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0 order-items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Sản phẩm</th>
                                            <th>Định dạng</th>
                                            <th>Thuộc tính</th>
                                            <th>Giá</th>
                                            <th>Số lượng</th>
                                            <th>Lượt tải Ebook</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @if(isset($orderItems) && $orderItems->count() > 0)
                                            @foreach($orderItems as $index => $item)
                                                @php
                                                    $subtotal = $item->price * $item->quantity;
                                                    $total += $subtotal;
                                                    $book = $item->book;
                                                    $format = $item->bookFormat;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                       @if($book)
                                                           <a href="{{route('admin.books.show', [$book->id, $book->slug])}}" style="text-decoration: none; color: #0a0c0d">
                                                               <div class="d-flex align-items-center">
                                                                   @if($book->cover_image)
                                                                       <img src="{{ asset('storage/' . $book->cover_image) }}"
                                                                            alt="{{ $book->title }}" class="me-2"
                                                                            style="width: 40px; height: 50px; object-fit: cover;">
                                                                   @endif
                                                                   <div>
                                                                       <h6 class="mb-0">{{ $book->title }}</h6>
                                                                       <small class="text-muted">ISBN: {{ $book->isbn ?? 'N/A' }}</small>
                                                                   </div>
                                                               </div>
                                                           </a>
                                                       @elseif($item->collection_id)
                                                           @php $collection = $item->collection; @endphp
                                                           @if($collection)
                                                               <div class="d-flex align-items-center">
                                                                   @if($collection->image)
                                                                       <img src="{{ asset('storage/' . $collection->image) }}"
                                                                            alt="{{ $collection->name }}" class="me-2"
                                                                            style="width: 40px; height: 50px; object-fit: cover;">
                                                                   @endif
                                                                   <div>
                                                                       <h6 class="mb-0">{{ $collection->name }} (Combo)</h6>
                                                                       <small class="text-muted">Mã: {{ $collection->id }}</small>
                                                                   </div>
                                                               </div>
                                                           @else
                                                               <span class="text-muted">Combo không tồn tại (ID: {{ $item->collection_id }})</span>
                                                           @endif
                                                       @else
                                                           <span class="text-muted">Sản phẩm không tồn tại (Book ID: {{ $item->book_id }})</span>
                                                       @endif
                                                    </td>
                                                    <td>
                                                        @if($item->bookFormat)
                                                             <span class="badge bg-primary">{{ $item->bookFormat->format_name }}</span>
                                                         @elseif($item->collection_id)
                                                             <span class="badge bg-primary combo">Combo</span>
                                                         @else
                                                             <span class="text-muted">N/A</span>
                                                         @endif
                                                    </td>
                                                    <td>
                                                        @if($item->attributeValues && $item->attributeValues->count() > 0)
                                                            @foreach($item->attributeValues as $attrValue)
                                                                <span class="attribute-badge">{{ $attrValue->attribute->name }}: {{ $attrValue->value }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">Không có</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td class="ebook-download-cell">
                                                        @if($item->bookFormat && $item->bookFormat->format_name === 'Ebook')
                                                            @php
                                                                $downloadCount = \App\Models\EbookDownload::where('user_id', $order->user_id)
                                                                    ->where('order_id', $order->id)
                                                                    ->where('book_format_id', $item->bookFormat->id)
                                                                    ->count();
                                                                $maxDownloads = $item->bookFormat->max_downloads ?? 5;
                                                                $drmEnabled = $item->bookFormat->drm_enabled ?? true;
                                                            @endphp
                                                            <div class="text-center">
                                                                <div class="mb-1">
                                                                    <span class="ebook-download-badge {{ $downloadCount > 0 ? 'ebook-download-count' : 'ebook-download-zero' }}">
                                                                        <i class="ri-download-line"></i>{{ $downloadCount }}
                                                                    </span>
                                                                </div>
                                                                @if($drmEnabled)
                                                                    <div class="mb-1">
                                                                        <small class="text-muted fw-bold">Max: {{ $maxDownloads }}</small>
                                                                        @if($downloadCount >= $maxDownloads)
                                                                            <div class="mt-1">
                                                                                <span class="ebook-download-badge ebook-download-limit" style="font-size: 0.6rem;">
                                                                                    <i class="ri-forbid-line"></i>Hết lượt
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <div class="mb-1">
                                                                        <span class="ebook-unlimited" style="font-size: 0.8rem;">∞</span>
                                                                    </div>
                                                                @endif
                                                                @if($downloadCount > 0)
                                                                    <div class="ebook-last-download" style="font-size: 0.65rem;">
                                                                        <i class="ri-time-line"></i>
                                                                        <span>{{ \App\Models\EbookDownload::where('user_id', $order->user_id)->where('order_id', $order->id)->where('book_format_id', $item->bookFormat->id)->latest()->first()?->downloaded_at?->format('d/m H:i') ?? 'N/A' }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="text-center text-muted">
                                                                <i class="ri-subtract-line"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                                                </tr>
                                            @endforeach
                                        @elseif(isset($order->invoice) && $order->invoice->items->count() > 0)
                                            @foreach($order->invoice->items as $index => $item)
                                                @php
                                                    $subtotal = $item->price * $item->quantity;
                                                    $total += $subtotal;
                                                    $book = $item->book;
                                                    $format = $book ? $book->formats->first() : null;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if($book)
                                                            <div class="d-flex align-items-center">
                                                                @if($book->cover_image)
                                                                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                                                                        alt="{{ $book->title }}" class="me-2"
                                                                        style="width: 40px; height: 50px; object-fit: cover;">
                                                                @endif
                                                                <div>
                                                                    <h6 class="mb-0">{{ $book->title }}</h6>
                                                                    <small class="text-muted">ISBN: {{ $book->isbn ?? 'N/A' }}</small>
                                                                </div>
                                                            </div>
                                                        @elseif($item->collection_id)
                                                            @php $collection = $item->collection; @endphp
                                                            @if($collection)
                                                                <div class="d-flex align-items-center">
                                                                    @if($collection->image)
                                                                        <img src="{{ asset('storage/' . $collection->image) }}"
                                                                             alt="{{ $collection->name }}" class="me-2"
                                                                             style="width: 40px; height: 50px; object-fit: cover;">
                                                                    @endif
                                                                    <div>
                                                                        <h6 class="mb-0">{{ $collection->name }} (Combo)</h6>
                                                                        <small class="text-muted">Mã: {{ $collection->id }}</small>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">Combo không tồn tại (ID: {{ $item->collection_id }})</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Sản phẩm không tồn tại (Book ID: {{ $item->book_id }})</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($format)
                                                            <span class="badge bg-info">{{ $format->format_name }}</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">Không có</span>
                                                    </td>
                                                    <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>
                                                        @if($format && $format->format_name === 'Ebook')
                                                            @php
                                                                $downloadCount = \App\Models\EbookDownload::where('user_id', $order->user_id)
                                                                    ->where('order_id', $order->id)
                                                                    ->where('book_format_id', $format->id)
                                                                    ->count();
                                                                $maxDownloads = $format->max_downloads ?? 5;
                                                                $drmEnabled = $format->drm_enabled ?? true;
                                                            @endphp
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-{{ $downloadCount > 0 ? 'success' : 'secondary' }} me-2">
                                                                    <i class="ri-download-line me-1"></i>{{ $downloadCount }}
                                                                </span>
                                                                @if($drmEnabled)
                                                                    <small class="text-muted">/ {{ $maxDownloads }}</small>
                                                                    @if($downloadCount >= $maxDownloads)
                                                                        <span class="badge bg-danger ms-2">Hết lượt</span>
                                                                    @endif
                                                                @else
                                                                    <small class="text-success">Không giới hạn</small>
                                                                @endif
                                                            </div>
                                                            @if($downloadCount > 0)
                                                                <small class="text-muted d-block mt-1">
                                                                    Lần cuối: {{ \App\Models\EbookDownload::where('user_id', $order->user_id)->where('order_id', $order->id)->where('book_format_id', $format->id)->latest()->first()?->downloaded_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                                                </small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8" class="text-center">Không có chi tiết đơn hàng</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="7" class="text-end">Tổng tiền sản phẩm:</th>
                                            <td>{{ number_format($total ?? $order->total_amount, 0, ',', '.') }}đ</td>
                                        </tr>
                                        @if($order->voucher)
                                            <tr>
                                                <th colspan="7" class="text-end">Giảm giá ({{ $order->voucher->code }}):</th>
                                                <td> {{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="7" class="text-end">Vận Chuyển ({{ $order->shipping_fee == 20000 ? 'Giao Hàng Tiết Kiệm' : 'Giao Hàng Nhanh' }}):</th>
                                            <td> {{ number_format($order->shipping_fee, 0, ',', '.') }}đ</td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <th colspan="7" class="text-end">Tổng thanh toán:</th>
                                            <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex mb-2">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                            <i class="ri-bank-card-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Phương thức thanh toán</h6>
                                    <p class="text-muted mb-0">
                                        @if(isset($order->payments) && count($order->payments) > 0)
                                            @php
                                                $payment = $order->payments[0];
                                                $paymentMethod = $payment->paymentMethod;
                                            @endphp
                                            @if($paymentMethod)
                                                <span class="badge rounded-pill fs-12 badge-soft-primary text-dark">
                                                    <i class="ri-bank-card-line me-1 align-bottom "></i>
                                                    {{ $paymentMethod->name }}
                                                </span>
                                            @else
                                                {{ $payment->payment_method_id ?? 'N/A' }}
                                            @endif
                                        @else
                                            Chưa có thông tin
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if(isset($order->payments) && count($order->payments) > 0 && $order->paymentStatus->name == 'Đã Thanh Toán')
                        <div class="mb-4">
                            <h6 class="mb-2">Lịch sử thanh toán</h6>
                            <div class="border rounded p-3">
                                @foreach($order->payments as $payment)
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1">
                                        <p class="text-muted mb-0">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                                        <h6 class="mb-0">{{ number_format($payment->amount, 0, ',', '.') }}đ</h6>
                                        @if($payment->paymentMethod)
                                            <small class="text-muted">{{ $payment->paymentMethod->name }}</small>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge
                                            @if($payment->status == 'Thành công') bg-success
                                            @elseif($payment->status == 'Đang xử lý') bg-warning
                                            @elseif($payment->status == 'Thất bại') bg-danger
                                            @else bg-secondary @endif">
                                            {{ $payment->status }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($order->invoice)
                        <div class="card mb-4">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="ri-file-text-line me-2"></i>THÔNG TIN HÓA ĐƠN
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="fw-bold text-primary mb-1">
                                        <i class="ri-hashtag me-2"></i>Mã hóa đơn
                                    </div>
                                    <div class="ps-3">
                                        <p class="mb-0">{{ strtoupper( 'INV-' . explode('-', $order->invoice->id)[0]) }}</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="fw-bold text-primary mb-1">
                                        <i class="ri-calendar-line me-2"></i>Ngày xuất hóa đơn
                                    </div>
                                    <div class="ps-3">
                                        <p class="mb-0">{{ $order->invoice->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="fw-bold text-primary mb-1">
                                        <i class="ri-money-dollar-circle-line me-2"></i>Tổng tiền hóa đơn
                                    </div>
                                    <div class="ps-3">
                                        <p class="mb-0 fw-bold text-success">{{ number_format($order->invoice->total_amount) }}đ</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="fw-bold text-primary mb-1">
                                        <i class="ri-information-line me-2"></i>Trạng thái
                                    </div>
                                    <div class="ps-3">
                                        @if($order->invoice->status == 'paid')
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @elseif($order->invoice->status == 'pending')
                                            <span class="badge bg-warning">Chờ thanh toán</span>
                                        @elseif($order->invoice->status == 'cancelled')
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $order->invoice->status }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.invoices.show', $order->invoice->id) }}" class="btn btn-primary btn-sm">
                                        <i class="ri-eye-line me-1"></i> Xem chi tiết hóa đơn
                                    </a>
                                    <a href="{{ route('admin.invoices.generate-pdf', $order->invoice->id) }}" target="_blank" class="btn btn-success btn-sm">
                                        <i class="ri-file-pdf-line me-1"></i> Tải PDF hóa đơn
                                    </a>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card mb-4">
                            <div class="card-header bg-light py-3">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="ri-file-text-line me-2"></i>THÔNG TIN HÓA ĐƠN
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-light text-muted rounded-circle fs-1">
                                        <i class="ri-file-text-line"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-3">Chưa có hóa đơn cho đơn hàng này</p>
                                @if($order->paymentStatus->name === 'Đã Thanh Toán')
                                    <p class="text-info small">Hóa đơn sẽ được tạo tự động sau khi thanh toán thành công</p>
                                @else
                                    <p class="text-warning small">Hóa đơn sẽ được tạo sau khi đơn hàng được thanh toán</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <div class="d-flex mb-2">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                            <i class="ri-calendar-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Ngày đặt hàng</h6>
                                    <p class="text-muted mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Thông tin hủy hàng --}}
                        @if($order->cancelled_at && in_array($order->orderStatus->name, ['Đã hủy', 'Đã Hủy']))
                        <div class="mb-4">
                            <div class="d-flex mb-2">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-light text-danger rounded-circle shadow fs-3">
                                            <i class="ri-close-circle-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Ngày hủy hàng</h6>
                                    <p class="text-muted mb-0">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex mb-2">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-light text-warning rounded-circle shadow fs-3">
                                            <i class="ri-information-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Lý do hủy hàng</h6>
                                    <p class="text-muted mb-0">{{ $order->cancellation_reason ?? 'Không có lý do cụ thể' }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Ebook Downloads Statistics --}}
                @php
                    $ebookItems = $order->orderItems->filter(function($item) {
                        return $item->bookFormat && $item->bookFormat->format_name === 'Ebook';
                    });
                    // dd($ebookItems);
                    $totalEbookDownloads = 0;
                    $ebookStats = [];
                    foreach($ebookItems as $item) {
                        // dd($order->user_id, $order->id);
                        $downloadCount = \App\Models\EbookDownload::where('user_id', $order->user_id)
                            // ->where('order_id', $order->id)
                            ->where('book_format_id', $item->bookFormat->id)
                            ->count();
                        $totalEbookDownloads += $downloadCount;
                        $ebookStats[] = [
                            'item' => $item,
                            'downloads' => $downloadCount,
                            'max_downloads' => $item->bookFormat->max_downloads ?? 5,
                            'drm_enabled' => $item->bookFormat->drm_enabled ?? true
                        ];
                    }
                @endphp
                
                @if($ebookItems->count() > 0)
                <div class="card ebook-stats-card">
                    <div class="card-header bg-gradient-primary">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 text-gray">
                                <i class="ri-download-line me-2"></i>Thống kê Ebook Downloads
                            </h5>
                            <span class="ebook-download-badge ebook-download-count">
                                <i class="ri-download-cloud-line"></i>
                                {{ $totalEbookDownloads }} lượt tải
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($ebookStats as $stat)
                            <div class="ebook-download-item mb-3 p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="ebook-title mb-0 text-truncate" style="max-width: 200px;" title="{{ $stat['item']->book->title ?? 'N/A' }}">
                                        <i class="ri-book-line me-1 text-primary"></i>
                                        {{ Str::limit($stat['item']->book->title ?? 'N/A', 25) }}
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="ebook-download-badge {{ $stat['downloads'] > 0 ? 'ebook-download-count' : 'ebook-download-zero' }}">
                                            {{ $stat['downloads'] }}
                                        </span>
                                        @if($stat['drm_enabled'])
                                            <small class="text-muted fw-bold">/ {{ $stat['max_downloads'] }}</small>
                                        @else
                                            <span class="ebook-unlimited">∞</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($stat['drm_enabled'])
                                    <div class="ebook-progress-bar mb-2">
                                        @php
                                            $percentage = ($stat['downloads'] / $stat['max_downloads']) * 100;
                                            $progressClass = $percentage >= 100 ? 'ebook-progress-danger' : ($percentage >= 80 ? 'ebook-progress-warning' : 'ebook-progress-success');
                                        @endphp
                                        <div class="ebook-progress-fill {{ $progressClass }}" 
                                             style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                @endif
                                
                                @if($stat['downloads'] > 0)
                                    @php
                                        $lastDownload = \App\Models\EbookDownload::where('user_id', $order->user_id)
                                            // ->where('order_id', $order->id)
                                            ->where('book_format_id', $stat['item']->bookFormat->id)
                                            ->latest()
                                            ->first();
                                    @endphp
                                    <div class="ebook-last-download">
                                        <i class="ri-time-line"></i>
                                        <span>Lần cuối: {{ $lastDownload?->downloaded_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                    </div>
                                @else
                                    <div class="ebook-last-download">
                                        <i class="ri-information-line"></i>
                                        <span>Chưa có lượt tải nào</span>
                                    </div>
                                @endif
                                
                                @if($stat['drm_enabled'] && $stat['downloads'] >= $stat['max_downloads'])
                                    <div class="mt-2">
                                        <span class="ebook-download-badge ebook-download-limit">
                                            <i class="ri-forbid-line"></i>Đã hết lượt tải
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        {{-- @if($totalEbookDownloads > 0)
                            <div class="ebook-stats-summary">
                                <i class="ri-bar-chart-line me-2"></i>
                                <strong>Tổng cộng {{ $totalEbookDownloads }} lượt tải từ {{ $ebookItems->count() }} ebook</strong>
                            </div>
                        @endif --}}
                    </div>
                </div>
                @endif

                {{-- GHN Shipping Information --}}
                @if($order->delivery_method === 'delivery')
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Thông tin vận chuyển GHN</h5>
                            @if($order->ghn_order_code)
                                <span class="badge bg-success">Đã tạo vận đơn</span>
                            @else
                                <span class="badge bg-warning">Chưa tạo vận đơn</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($order->ghn_order_code)
                            <div class="mb-3">
                                <div class="d-flex mb-2">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                                <i class="ri-truck-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Mã vận đơn GHN</h6>
                                        <p class="text-muted mb-0">{{ $order->ghn_order_code }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($order->ghn_service_type_id)
                            <div class="mb-3">
                                <div class="d-flex mb-2">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-info rounded-circle shadow fs-3">
                                                <i class="ri-service-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Loại dịch vụ</h6>
                                        <p class="text-muted mb-0">ID: {{ $order->ghn_service_type_id }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($order->expected_delivery_date)
                            <div class="mb-3">
                                <div class="d-flex mb-2">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-warning rounded-circle shadow fs-3">
                                                <i class="ri-calendar-check-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Ngày giao dự kiến</h6>
                                        <p class="text-muted mb-0">{{ $order->expected_delivery_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($order->ghn_tracking_data)
                            <div class="mb-3">
                                <h6 class="mb-2">Trạng thái vận chuyển</h6>
                                <div class="border rounded p-3">
                                    @php
                                        $trackingData = is_string($order->ghn_tracking_data) ? json_decode($order->ghn_tracking_data, true) : $order->ghn_tracking_data;
                                    @endphp
                                    @if($trackingData && isset($trackingData['status']))
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="w-3 h-3 bg-primary rounded-circle me-2"></div>
                                            <span class="fw-medium">{{ $trackingData['status'] }}</span>
                                        </div>
                                        @if(isset($trackingData['description']))
                                            <p class="text-muted mb-0 small">{{ $trackingData['description'] }}</p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">Chưa có thông tin theo dõi</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <div class="d-grid gap-2">
                                <form action="{{ route('admin.orders.ghn.update-tracking', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info btn-sm w-100">
                                        <i class="ri-refresh-line me-1"></i> Cập nhật theo dõi
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.orders.ghn.cancel', $order->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy liên kết với đơn hàng GHN?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="ri-close-line me-1"></i> Hủy liên kết GHN
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-light text-muted rounded-circle fs-1">
                                        <i class="ri-truck-line"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-3">Chưa tạo đơn hàng GHN cho đơn hàng này</p>
                                
                                @if(in_array($order->orderStatus->name, ['Chờ Xác Nhận', 'Đã Xác Nhận']))
                                <form action="{{ route('admin.orders.ghn.create', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line me-1"></i> Tạo đơn hàng GHN
                                    </button>
                                </form>
                                @else
                                <p class="text-muted small">Chỉ có thể tạo đơn GHN khi đơn hàng ở trạng thái "Chờ Xác Nhận" hoặc "Đã Xác Nhận"</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
@endsection
