@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Breadcrumb (outside header) -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('preorders.index') }}" class="text-decoration-none">Đơn đặt trước</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
                </ol>
            </nav>

            <!-- Header với style màu đen như create -->
            <div class="mb-4" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border-radius: 0px;">
                <div class="p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-4 order-md-2 d-flex justify-content-md-end">
                            <div class="p-0" style="background: transparent; border-radius: 0px;">
                                <img src="{{ $preorder->book->cover_image ? asset('storage/' . $preorder->book->cover_image) : asset('images/default-book.svg') }}" 
                                     alt="{{ $preorder->book->title }}" 
                                     class="img-fluid shadow-sm" style="max-height: 260px; object-fit: cover; border-radius: 0px; width: 100%;">
                            </div>
                        </div>
                        <div class="col-md-8 order-md-1 pe-md-4">
                            <h1 class="h2 mb-3 text-white">
                                <i class="ri-check-line me-2"></i>ĐẶT TRƯỚC THÀNH CÔNG
                            </h1>
                            <p class="text-light mb-4">CẢM ƠN BẠN ĐÃ ĐẶT TRƯỚC! CHÚNG TÔI ĐÃ NHẬN ĐƯỢC YÊU CẦU CỦA BẠN VÀ SẼ LIÊN HỆ SỚM NHẤT.</p>
                            
                            <div class="row text-light">
                                <div class="col-md-6 order-md-1">
                                    <p class="mb-2"><strong>{{ $preorder->book->title }}</strong></p>
                                    <p class="mb-2">Mã đơn: <span class="badge bg-warning text-dark">#{{ substr($preorder->id, 0, 8) }}</span></p>
                                </div>
                                <div class="col-md-6 order-md-2 text-md-end">
                                    <p class="mb-2"><strong>Trạng thái:</strong> <span class="badge bg-success">{{ $preorder->status_text }}</span></p>
                                    @if ($preorder->notes)
                                        <p class="mb-2"><strong>Lý do:</strong> {{ $preorder->notes }}</p>
                                    @endif
                                    <p class="mb-2"><strong>Tổng tiền:</strong> <span class="text-warning fw-bold fs-5">{{ number_format($preorder->total_amount, 0, ',', '.') }}đ</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Cards -->
            <div class="row g-4">
                <!-- Chi tiết đơn hàng -->
                <div class="col-lg-8">
                    <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 0px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                        <div class="p-4" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                            <h5 class="text-white fw-bold mb-0">
                                <i class="ri-file-list-3-line me-2"></i>CHI TIẾT ĐƠN HÀNG
                            </h5>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted" style="font-size: 14px;">Định dạng:</span>
                                        <span class="fw-bold">{{ $preorder->bookFormat ? $preorder->bookFormat->format_name : 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted" style="font-size: 14px;">Số lượng:</span>
                                        <span class="fw-bold">{{ $preorder->quantity }}</span>
                                    </div>
                                </div>
                                @if($preorder->variant_label || $preorder->variant_sku)
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted" style="font-size: 14px;">Biến thể:</span>
                                        <span class="fw-bold">{{ $preorder->variant_label ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted" style="font-size: 14px;">SKU biến thể:</span>
                                        <span class="fw-bold">{{ $preorder->variant_sku ?? '—' }}</span>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted" style="font-size: 14px;">Ngày ra mắt:</span>
                                        <span class="fw-bold text-info">{{ $preorder->expected_delivery_date->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted" style="font-size: 14px;">Phương thức thanh toán:</span>
                                        <span class="fw-bold">{{ $preorder->paymentMethod ? $preorder->paymentMethod->name : 'Chưa xác định' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #dee2e6;">
                                        <span class="text-muted" style="font-size: 14px;">Trạng thái thanh toán:</span>
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
                                        <span class="badge {{ $paymentStatusClass }}" style="border-radius: 0px;">{{ $paymentStatusText }}</span>
                                    </div>
                                </div>
                                
                                <!-- Chi tiết giá -->
                                <div class="col-12">
                                    <h6 class="fw-bold mt-3 mb-3">Chi tiết giá:</h6>
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
                                        $variantExtra = (float)($preorder->variant_extra_price ?? 0);
                                        // Suy ra giá cơ bản từ đơn giá đã lưu
                                        $basePrice = max(0, (float)$preorder->unit_price - $attributeExtra - $variantExtra);
                                    @endphp
                                    <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #eee;">
                                        <span class="text-muted">Giá cơ bản:</span>
                                        <span class="fw-bold">{{ number_format($basePrice, 0, ',', '.') }}đ</span>
                                    </div>
                                    @if($variantExtra > 0)
                                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #eee;">
                                            <span class="text-muted">Phụ phí biến thể:</span>
                                            <span class="fw-bold text-info">+{{ number_format($variantExtra, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endif
                                    @if($attributeExtra > 0)
                                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #eee;">
                                            <span class="text-muted">Phí thuộc tính:</span>
                                            <span class="fw-bold text-info">+{{ number_format($attributeExtra, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #eee;">
                                        <span class="text-muted">Đơn giá:</span>
                                        <span class="fw-bold">{{ number_format($preorder->unit_price, 0, ',', '.') }}đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #eee;">
                                        <span class="text-muted">Số lượng:</span>
                                        <span class="fw-bold">{{ $preorder->quantity }}</span>
                                    </div>
                                    @if(!$preorder->isEbook() && $preorder->shipping_fee > 0)
                                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #eee;">
                                            <span class="text-muted">Phí vận chuyển:</span>
                                            <span class="fw-bold text-info">{{ number_format($preorder->shipping_fee, 0, ',', '.') }}đ</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between py-3 mt-3" style="background: #f8f9fa; margin: 0 -1rem; padding: 1rem !important;">
                                        <span class="fw-bold fs-5">Tổng cộng:</span>
                                        <span class="fw-bold text-success fs-4">{{ number_format($preorder->total_amount, 0, ',', '.') }}đ</span>
                                    </div>
                                    @if($preorder->isEbook() || $preorder->shipping_fee == 0)
                                        <div class="mt-2 text-center">
                                            <small class="text-success"><i class="ri-truck-line me-1"></i>Miễn phí vận chuyển</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar thông tin -->
                <div class="col-lg-4">
                    <!-- Thông tin khách hàng -->
                    <div class="mb-4" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 0px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                        <div class="p-3" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                            <h6 class="text-white fw-bold mb-0"><i class="ri-user-line me-2"></i>THÔNG TIN KHÁCH HÀNG</h6>
                        </div>
                        <div class="p-3">
                            <div class="mb-3">
                                <small class="text-muted d-block">Họ và tên:</small>
                                <span class="fw-bold">{{ $preorder->customer_name }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Email:</small>
                                <span class="fw-bold">{{ $preorder->email }}</span>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted d-block">Điện thoại:</small>
                                <span class="fw-bold">{{ $preorder->phone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Địa chỉ giao hàng -->
                    @if(!$preorder->isEbook())
                        <div class="mb-4" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 0px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                            <div class="p-3" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                                <h6 class="text-white fw-bold mb-0"><i class="ri-map-pin-line me-2"></i>ĐỊA CHỈ GIAO HÀNG</h6>
                            </div>
                            <div class="p-3">
                                <address class="mb-0">{{ $preorder->full_address }}</address>
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
                        <div class="mb-4" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 0px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                            <div class="p-3" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                                <h6 class="text-white fw-bold mb-0"><i class="ri-price-tag-line me-2"></i>THUỘC TÍNH ĐÃ CHỌN</h6>
                            </div>
                            <div class="p-3">
                                @foreach($selectedAttrs as $attr => $value)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">{{ $attr }}:</small>
                                        <span class="fw-bold">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Ghi chú -->
                    @if($preorder->notes)
                        <div class="mb-4" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 0px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                            <div class="p-3" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                                <h6 class="text-white fw-bold mb-0"><i class="ri-chat-3-line me-2"></i>GHI CHÚ</h6>
                            </div>
                            <div class="p-3">
                                {{ $preorder->notes }}
                            </div>
                        </div>
                    @endif

                    <!-- Lưu ý quan trọng -->
                    <div class="mb-4" style="background: #e3f2fd; border: 1px solid #2196f3; border-radius: 0px;">
                        <div class="p-3">
                            <h6 class="fw-bold mb-3"><i class="ri-information-line me-2 text-primary"></i>Lưu ý quan trọng</h6>
                            <ul class="mb-0 small">
                                <li class="mb-1">Bạn sẽ nhận được email xác nhận đơn đặt trước.</li>
                                <li class="mb-1">Chúng tôi sẽ liên hệ với bạn khi sách được phát hành.</li>
                                @if($preorder->isEbook())
                                    <li class="mb-1">Link tải sách sẽ được gửi qua email khi sách ra mắt.</li>
                                @else
                                    <li class="mb-1">Sách sẽ được giao đến địa chỉ bạn đã cung cấp.</li>
                                @endif
                                <li>Bạn có thể hủy đơn hàng trước khi sách được phát hành.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('preorders.index') }}" class="btn btn-outline-dark" style="border-radius: 0px; font-weight: 600;">
                            <i class="ri-list-check me-2"></i>XEM ĐƠN ĐẶT TRƯỚC CỦA TÔI
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-dark" style="border-radius: 0px; font-weight: 600;">
                            <i class="ri-home-line me-2"></i>VỀ TRANG CHỦ
                        </a>
                        @if($preorder->canBeCancelled())
                            <form action="{{ route('preorders.cancel', $preorder) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100" style="border-radius: 0px; font-weight: 600;"
                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt trước này?')">
                                    <i class="ri-close-line me-2"></i>HỦY ĐƠN
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
