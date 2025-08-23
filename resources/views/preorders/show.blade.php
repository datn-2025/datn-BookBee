@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="ri-check-line me-2"></i>Đặt Trước Thành Công
                    </h4>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-success mb-4">
                        <i class="ri-check-double-line me-2"></i>
                        <strong>Cảm ơn bạn đã đặt trước!</strong> 
                        Chúng tôi đã nhận được yêu cầu của bạn và sẽ liên hệ sớm nhất.
                    </div>

                    <!-- Thông tin đơn đặt trước -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img src="{{ $preorder->book->cover_image ? asset('storage/' . $preorder->book->cover_image) : asset('images/default-book.svg') }}" 
                                 alt="{{ $preorder->book->title }}" 
                                 class="img-fluid rounded shadow-sm">
                        </div>
                        <div class="col-md-8">
                            <h5 class="fw-bold text-primary">{{ $preorder->book->title }}</h5>
                            <div class="row g-2 mt-2">
                                <div class="col-sm-6">
                                    <small class="text-muted">Mã đơn:</small><br>
                                    <span class="fw-medium">#{{ substr($preorder->id, 0, 8) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">Trạng thái đơn hàng:</small><br>
                                    <span class="badge bg-warning">{{ $preorder->status_text }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">Định dạng:</small><br>
                                    <span class="fw-medium">{{ $preorder->bookFormat ? $preorder->bookFormat->format_name : 'N/A' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">Số lượng:</small><br>
                                    <span class="fw-medium">{{ $preorder->quantity }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">Ngày ra mắt:</small><br>
                                    <span class="fw-medium text-info">{{ $preorder->expected_delivery_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">Phương thức thanh toán:</small><br>
                                    <span class="fw-medium">{{ $preorder->paymentMethod ? $preorder->paymentMethod->name : 'Chưa xác định' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">Trạng thái thanh toán:</small><br>
                                    @php
                                        $paymentStatusClass = match($preorder->payment_status) {
                                            'paid' => 'bg-success',
                                            'failed' => 'bg-danger',
                                            default => 'bg-warning'
                                        };
                                        $paymentStatusText = match($preorder->payment_status) {
                                            'paid' => 'Đã thanh toán',
                                            'failed' => 'Thanh toán thất bại',
                                            default => 'Chờ thanh toán'
                                        };
                                    @endphp
                                    <span class="badge {{ $paymentStatusClass }}">{{ $paymentStatusText }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">Chi tiết giá:</small><br>
                                    @php
                                        // Tính lại phí thuộc tính từ selected_attributes để khớp với lúc lưu
                                        $attributeExtra = 0;
                                        if (!$preorder->isEbook() && $preorder->selected_attributes && count($preorder->selected_attributes) > 0) {
                                            foreach ($preorder->selected_attributes as $attrName => $attrValue) {
                                                $bav = $preorder->book->bookAttributeValues()
                                                    ->whereHas('attributeValue', function($q) use ($attrValue) {
                                                        $q->where('value', $attrValue);
                                                    })
                                                    ->whereHas('attributeValue.attribute', function($q) use ($attrName) {
                                                        $q->where('name', $attrName);
                                                    })
                                                    ->first();
                                                if ($bav && $bav->extra_price > 0) {
                                                    $attributeExtra += (float)$bav->extra_price;
                                                }
                                            }
                                        }
                                        // Suy ra giá cơ bản từ đơn giá đã lưu
                                        $basePrice = max(0, (float)$preorder->unit_price - $attributeExtra);
                                    @endphp
                                    <div class="mb-1">
                                        <small>Giá cơ bản: <span class="fw-medium">{{ number_format($basePrice, 0, ',', '.') }}đ</span></small>
                                    </div>
                                    @if($attributeExtra > 0)
                                        <div class="mb-1">
                                            <small>Phí thuộc tính: <span class="fw-medium text-info">+{{ number_format($attributeExtra, 0, ',', '.') }}đ</span></small>
                                        </div>
                                    @endif
                                    <div class="mb-1">
                                        <small>Đơn giá: <span class="fw-medium">{{ number_format($preorder->unit_price, 0, ',', '.') }}đ</span></small>
                                    </div>
                                    <div class="mb-1">
                                        <small>Số lượng: <span class="fw-medium">{{ $preorder->quantity }}</span></small>
                                    </div>
                                    @if(!$preorder->isEbook() && $preorder->shipping_fee > 0)
                                        <div class="mb-1">
                                            <small>Phí vận chuyển: <span class="fw-medium text-info">{{ number_format($preorder->shipping_fee, 0, ',', '.') }}đ</span></small>
                                        </div>
                                    @endif
                                    <hr class="my-2">
                                    <span class="fw-bold text-success fs-5">Tổng: {{ number_format($preorder->total_amount, 0, ',', '.') }}đ</span>
                                    @if($preorder->isEbook() || $preorder->shipping_fee == 0)
                                        <div class="mt-1">
                                            <small class="text-success"><i class="ri-truck-line me-1"></i>Miễn phí vận chuyển</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin khách hàng -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="ri-user-line me-1"></i>Thông tin khách hàng</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">Họ và tên:</small><br>
                                    <span class="fw-medium">{{ $preorder->customer_name }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Email:</small><br>
                                    <span class="fw-medium">{{ $preorder->email }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Điện thoại:</small><br>
                                    <span class="fw-medium">{{ $preorder->phone }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Địa chỉ giao hàng (nếu không phải ebook) -->
                    @if(!$preorder->isEbook())
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-map-pin-line me-1"></i>Địa chỉ giao hàng</h6>
                            </div>
                            <div class="card-body">
                                <address class="mb-0">
                                    {{ $preorder->full_address }}
                                </address>
                            </div>
                        </div>
                    @endif

                    <!-- Thuộc tính đã chọn -->
                    @php
                        $selectedAttrs = $preorder->selected_attributes ?? [];
                        if (is_array($selectedAttrs)) {
                            $selectedAttrs = array_filter($selectedAttrs, function($v) { return $v !== null && $v !== ''; });
                        } else {
                            $selectedAttrs = [];
                        }
                    @endphp
                    @if(!empty($selectedAttrs))
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-price-tag-line me-1"></i>Thuộc tính đã chọn</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($selectedAttrs as $attr => $value)
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted">{{ $attr }}:</small><br>
                                            <span class="fw-medium">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Ghi chú -->
                    @if($preorder->notes)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="ri-chat-3-line me-1"></i>Ghi chú của khách hàng</h6>
                            </div>
                            <div class="card-body">
                                {{ $preorder->notes }}
                            </div>
                        </div>
                    @endif

                    <!-- Thông tin bổ sung -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="ri-information-line me-1"></i>Lưu ý quan trọng:</h6>
                        <ul class="mb-0">
                            <li>Bạn sẽ nhận được email xác nhận đơn đặt trước.</li>
                            <li>Chúng tôi sẽ liên hệ với bạn khi sách được phát hành.</li>
                            @if($preorder->isEbook())
                                <li>Link tải sách sẽ được gửi qua email khi sách ra mắt.</li>
                            @else
                                <li>Sách sẽ được giao đến địa chỉ bạn đã cung cấp.</li>
                            @endif
                            <li>Bạn có thể hủy đơn hàng trước khi sách được phát hành.</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('preorders.index') }}" class="btn btn-outline-primary me-md-2">
                            <i class="ri-list-check me-1"></i>Xem đơn đặt trước của tôi
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="ri-home-line me-1"></i>Về trang chủ
                        </a>
                        @if($preorder->canBeCancelled())
                            <form action="{{ route('preorders.cancel', $preorder) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt trước này?')">
                                    <i class="ri-close-line me-1"></i>Hủy đơn
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
