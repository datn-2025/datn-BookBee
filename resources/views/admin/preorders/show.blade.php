@extends('layouts.backend')

@section('title', 'Chi Tiết Đơn Đặt Trước #' . substr($preorder->id, 0, 8))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chi Tiết Đơn Đặt Trước</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.preorders.index') }}">Đơn đặt trước</a></li>
                    <li class="breadcrumb-item active">#{{ substr($preorder->id, 0, 8) }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.preorders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            @if($preorder->isPending())
                <form action="{{ route('admin.preorders.approve', $preorder) }}" method="POST" class="d-inline">
                    @csrf
                    {{-- <button type="submit" class="btn btn-warning" 
                            onclick="return confirm('Duyệt đơn hàng này?')">
                        <i class="fas fa-check"></i>
                        Duyệt đơn hàng
                    </button> --}}
                </form>
            @elseif($preorder->isApproved() && $preorder->book->isReleased())
                <button type="button" class="btn btn-success" 
                        onclick="showConvertModal('{{ $preorder->id }}', '{{ $preorder->book->title }}', true, '{{ $preorder->book->release_date->format('d/m/Y') }}')">
                    <i class="fas fa-exchange-alt"></i>
                    Chuyển thành đơn hàng
                </button>
            @elseif($preorder->isApproved() && !$preorder->book->isReleased())
                <button type="button" class="btn btn-warning" 
                        onclick="showConvertModal('{{ $preorder->id }}', '{{ $preorder->book->title }}', false, '{{ $preorder->book->release_date->format('d/m/Y') }}')">
                    <i class="fas fa-exclamation-triangle"></i>
                    Chuyển thành đơn hàng (Sớm)
                </button>
            @elseif($preorder->isReadyToConvert())
                <button type="button" class="btn btn-info" 
                        onclick="showConvertModal('{{ $preorder->id }}', '{{ $preorder->book->title }}', true, '{{ $preorder->book->release_date->format('d/m/Y') }}')">
                    <i class="fas fa-rocket"></i>
                    Chuyển thành đơn hàng
                </button>
            @elseif($preorder->isConverted())
                <a href="{{ route('admin.orders.show', $preorder->converted_order_id) }}" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i>
                    Xem đơn hàng đã chuyển đổi
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Đơn Hàng</h6>
                    @php
                        $statusColors = [
                            \App\Models\Preorder::STATUS_CHO_DUYET => 'warning',
                            \App\Models\Preorder::STATUS_DA_DUYET => 'info',
                            \App\Models\Preorder::STATUS_SAN_SANG_CHUYEN_DOI => 'primary',
                            \App\Models\Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG => 'success',
                            \App\Models\Preorder::STATUS_DA_HUY => 'danger',
                            // Tương thích ngược
                            'pending' => 'warning',
                            'confirmed' => 'info',
                            'processing' => 'primary',
                            'shipped' => 'success',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$preorder->status] ?? 'secondary' }} fs-6">
                        {{ $preorder->status_text }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Mã đơn:</strong> #{{ substr($preorder->id, 0, 8) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Ngày đặt:</strong> {{ $preorder->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Ngày ra mắt dự kiến:</strong> {{ $preorder->expected_delivery_date->format('d/m/Y') }}
                        </div>
                        <div class="col-md-6">
                            @if($preorder->confirmed_at)
                                <strong>Ngày xác nhận:</strong> {{ $preorder->confirmed_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                    @if($preorder->converted_at)
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Ngày chuyển đổi:</strong> {{ $preorder->converted_at->format('d/m/Y H:i') }}
                            </div>
                            @if($preorder->converted_order_id)
                                <div class="col-md-6">
                                    <strong>Mã đơn hàng:</strong> 
                                    <a href="{{ route('admin.orders.show', $preorder->converted_order_id) }}" class="text-primary">
                                        {{ Str::limit($preorder->converted_order_id, 8) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thông tin sách -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Sách</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="{{ $preorder->book->cover_image ? asset('storage/' . $preorder->book->cover_image) : asset('images/default-book.svg') }}" 
                                 alt="{{ $preorder->book->title }}" class="img-fluid rounded shadow-sm">
                        </div>
                        <div class="col-md-9">
                            <h5 class="fw-bold text-primary mb-3">{{ $preorder->book->title }}</h5>
                            
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Định dạng:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-{{ $preorder->bookFormat && $preorder->bookFormat->format_name == 'Ebook' ? 'info' : 'secondary' }}">
                                        {{ $preorder->bookFormat ? $preorder->bookFormat->format_name : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Số lượng:</strong></div>
                                <div class="col-sm-8">{{ $preorder->quantity }}</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Đơn giá:</strong></div>
                                <div class="col-sm-8">{{ number_format($preorder->unit_price, 0, ',', '.') }}đ</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Tổng tiền:</strong></div>
                                <div class="col-sm-8">
                                    <span class="fw-bold text-success fs-5">{{ number_format($preorder->total_amount, 0, ',', '.') }}đ</span>
                                </div>
                            </div>

                            @if($preorder->book->isReleased())
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle"></i> Sách đã được phát hành
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-clock"></i> Sách chưa được phát hành
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thuộc tính đã chọn -->
            @php
                $selectedAttrs = $preorder->selected_attributes ?? [];
                if (is_array($selectedAttrs)) {
                    // Loại bỏ các giá trị rỗng/null
                    $selectedAttrs = array_filter($selectedAttrs, function($v) { return $v !== null && $v !== ''; });
                } else {
                    $selectedAttrs = [];
                }
            @endphp
            @if(!empty($selectedAttrs))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thuộc Tính Đã Chọn</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($selectedAttrs as $attr => $value)
                                <div class="col-md-6 mb-2">
                                    <strong>{{ $attr }}:</strong> {{ $value }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ghi chú của khách hàng -->
            @php
                $clientNotes = $preorder->notes;
                // Loại bỏ ghi chú hệ thống đã từng được append khi chuyển đổi sang đơn hàng
                if ($clientNotes) {
                    // Xóa phần "Đã chuyển đổi thành đơn hàng #..." và mọi nội dung sau đó
                    $clientNotes = preg_replace('/\n*\s*Đã chuyển đổi thành đơn hàng\s*#.*$/s', '', $clientNotes);
                    $clientNotes = trim($clientNotes);
                }
            @endphp
            @if(!empty($clientNotes))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ghi chú của khách hàng</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $clientNotes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Thông tin khách hàng -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Khách Hàng</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Họ tên:</strong><br>
                        {{ $preorder->customer_name }}
                        @if($preorder->user)
                            <span class="badge bg-success ms-2">Thành viên</span>
                        @else
                            <span class="badge bg-secondary ms-2">Khách</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $preorder->email }}">{{ $preorder->email }}</a>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Số điện thoại:</strong><br>
                        <a href="tel:{{ $preorder->phone }}">{{ $preorder->phone }}</a>
                    </div>

                    @if($preorder->user)
                        <div class="mb-3">
                            <strong>Tài khoản:</strong><br>
                            <a href="{{ route('admin.users.show', $preorder->user) }}" class="text-decoration-none">
                                Xem thông tin tài khoản
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thông tin thanh toán -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Thanh Toán</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Phương thức thanh toán:</strong><br>
                        @if($preorder->paymentMethod)
                            <span class="badge bg-info">{{ $preorder->paymentMethod->name }}</span>
                        @else
                            <span class="text-muted">Chưa xác định</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Trạng thái thanh toán:</strong><br>
                        @if($preorder->payment_status === 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @elseif($preorder->payment_status === 'failed')
                            <span class="badge bg-danger">Thanh toán thất bại</span>
                        @else
                            <span class="badge bg-warning">Chờ thanh toán</span>
                        @endif
                    </div>
                    
                    @if(!$preorder->isEbook())
                        <div class="mb-3">
                            <strong>Phí vận chuyển:</strong><br>
                            @if($preorder->shipping_fee && $preorder->shipping_fee > 0)
                                <span class="text-success fw-bold">{{ number_format($preorder->shipping_fee, 0, ',', '.') }}đ</span>
                            @else
                                <span class="text-success fw-bold">Miễn phí vận chuyển</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Địa chỉ giao hàng -->
            @if(!$preorder->isEbook())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Địa Chỉ Giao Hàng</h6>
                    </div>
                    <div class="card-body">
                        <address class="mb-0">
                            {{ $preorder->address }}<br>
                            {{ $preorder->ward_name }}, {{ $preorder->district_name }}<br>
                            {{ $preorder->province_name }}
                        </address>
                    </div>
                </div>
            @endif

            <!-- Cập nhật trạng thái -->


            <!-- Timeline trạng thái -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch Sử Trạng Thái</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $preorder->created_at ? 'completed' : '' }}">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Đơn đặt trước được tạo</h6>
                                <p class="timeline-text">{{ $preorder->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($preorder->confirmed_at)
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Đã xác nhận</h6>
                                    <p class="timeline-text">{{ $preorder->confirmed_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($preorder->converted_at)
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Đã chuyển thành đơn hàng</h6>
                                    <p class="timeline-text">{{ $preorder->converted_at->format('d/m/Y H:i') }}</p>
                                    @if($preorder->converted_order_id)
                                        <p class="timeline-text">
                                            <small class="text-muted">Mã đơn hàng: 
                                                <a href="{{ route('admin.orders.show', $preorder->converted_order_id) }}" class="text-primary">
                                                    {{ Str::limit($preorder->converted_order_id, 12) }}
                                                </a>
                                            </small>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Thao tác khác -->
            @if($preorder->status == \App\Models\Preorder::STATUS_DA_HUY)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">Thao Tác Nguy Hiểm</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.preorders.destroy', $preorder) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Xóa vĩnh viễn đơn đặt trước này? Hành động này không thể hoàn tác.')">
                                <i class="fas fa-trash"></i> Xóa đơn đặt trước
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Cảnh Báo Duyệt Đơn -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="warningModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Cảnh Báo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="warningMessage" class="mb-3"></p>
                <div class="alert alert-warning">
                    <strong>Lưu ý:</strong> Việc duyệt đơn trước ngày phát hành có thể ảnh hưởng đến quy trình quản lý kho và giao hàng.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <a href="#" id="confirmConvertBtn" class="btn btn-warning">
                    <i class="fas fa-check"></i> Xác Nhận Duyệt
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-item.completed .timeline-marker {
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Kiểm tra có cảnh báo từ session không
    @if(session('warning'))
        const warningData = @json(session('warning'));
        $('#warningMessage').text(warningData.message);
        $('#confirmConvertBtn').attr('href', warningData.confirm_url);
        $('#warningModal').modal('show');
    @endif

    // Xử lý form cập nhật trạng thái
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Bạn có chắc chắn muốn cập nhật trạng thái này?')) {
            this.submit();
        }
    });

    // Xử lý xác nhận duyệt/chuyển đổi đơn hàng
    $('#confirmConvertBtn').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url.includes('approve')) {
            if (confirm('Xác nhận duyệt đơn hàng này?')) {
                // Tạo form POST để gửi request
                const form = $('<form>', {
                    'method': 'POST',
                    'action': url
                });
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'force_approve',
                    'value': '1'
                }));
                $('body').append(form);
                form.submit();
            }
        } else {
            if (confirm('Xác nhận chuyển đổi đơn hàng thành đơn hàng chính thức?')) {
                // Tạo form POST để gửi request
                const form = $('<form>', {
                    'method': 'POST',
                    'action': url
                });
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));
                $('body').append(form);
                form.submit();
            }
        }
    });

    // Xử lý nút chuyển đổi thành đơn hàng
    $('form[action*="convert-to-order"]').on('submit', function(e) {
        if (!$(this).find('input[name="force_convert"]').length) {
            if (!confirm('Chuyển đổi đơn hàng thành đơn hàng chính thức?')) {
                e.preventDefault();
            }
        }
    });
});

// Modal xác nhận chuyển đổi
function showConvertModal(preorderId, bookTitle, isReleased, releaseDate) {
    // Cập nhật thông tin trong modal
    document.getElementById('bookTitle').textContent = bookTitle;
    document.getElementById('releaseDate').textContent = releaseDate;
    
    // Hiển thị cảnh báo phù hợp
    const earlyWarning = document.getElementById('earlyWarning');
    const readyInfo = document.getElementById('readyInfo');
    const confirmBtn = document.getElementById('confirmConvertBtn');
    
    if (isReleased) {
        earlyWarning.style.display = 'none';
        readyInfo.style.display = 'block';
        confirmBtn.className = 'btn btn-success';
        confirmBtn.innerHTML = '<i class="fas fa-exchange-alt"></i> Xác nhận chuyển đổi';
    } else {
        earlyWarning.style.display = 'block';
        readyInfo.style.display = 'none';
        confirmBtn.className = 'btn btn-warning';
        confirmBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Xác nhận chuyển đổi sớm';
    }
    
    // Cập nhật action của form
    const form = document.getElementById('convertForm');
    form.action = `/admin/preorders/${preorderId}/convert-to-order`;
    
    // Xóa input force_convert cũ nếu có
    const existingInput = form.querySelector('input[name="force_convert"]');
    if (existingInput) {
        existingInput.remove();
    }
    
    // Thêm input force_convert=1
    const forceConvertInput = document.createElement('input');
    forceConvertInput.type = 'hidden';
    forceConvertInput.name = 'force_convert';
    forceConvertInput.value = '1';
    form.appendChild(forceConvertInput);
    
    // Hiển thị modal
    const modal = new bootstrap.Modal(document.getElementById('convertModal'));
    modal.show();
}
</script>
@endpush

<!-- Modal xác nhận chuyển đổi -->
<div class="modal fade" id="convertModal" tabindex="-1" aria-labelledby="convertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convertModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Xác nhận chuyển đổi đơn đặt trước
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="convertInfo">
                    <strong>Thông tin đơn hàng:</strong>
                    <br>• Sách: <span id="bookTitle"></span>
                    <br>• Ngày phát hành: <span id="releaseDate"></span>
                </div>
                <div class="alert alert-warning" id="earlyWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Cảnh báo:</strong> Sách chưa đến ngày phát hành chính thức. 
                    Việc chuyển đổi sớm có thể ảnh hưởng đến quy trình giao hàng.
                </div>
                <div class="alert alert-success" id="readyInfo" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                    Sách đã được phát hành và sẵn sàng để chuyển đổi thành đơn hàng.
                </div>
                <p><strong>Bạn có chắc chắn muốn chuyển đổi đơn đặt trước này thành đơn hàng chính thức không?</strong></p>
                <p class="text-muted small">Sau khi chuyển đổi, đơn đặt trước sẽ trở thành đơn hàng và không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Hủy bỏ
                </button>
                <form id="convertForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="confirmConvertBtn">
                        <i class="fas fa-exchange-alt"></i> Xác nhận chuyển đổi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>