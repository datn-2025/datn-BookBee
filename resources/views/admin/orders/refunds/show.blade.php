@extends('layouts.backend')

@section('title', 'Chi tiết yêu cầu hoàn tiền')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chi tiết yêu cầu hoàn tiền #{{ $refund->order->order_code }}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.refunds.index') }}">Yêu cầu hoàn tiền</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Refund Request Details -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title flex-grow-1 mb-0">Thông tin yêu cầu hoàn tiền</h5>
                    <div class="flex-shrink-0">
                        <span class="badge fs-12 
                            @if($refund->status === 'pending') bg-warning
                            @elseif($refund->status === 'processing') bg-info  
                            @elseif($refund->status === 'approve') bg-success
                            @elseif($refund->status === 'rejected') bg-danger
                            @else bg-secondary @endif">
                            @if($refund->status === 'pending') Chờ xử lý
                            @elseif($refund->status === 'processing') Đang xử lý
                            @elseif($refund->status === 'approve') Đã duyệt
                            @elseif($refund->status === 'rejected') Đã từ chối
                            @else {{ $refund->status }} @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do hoàn tiền:</label>
                            <p class="text-muted mb-0">{{ ucfirst(str_replace('_', ' ', $refund->reason)) }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số tiền hoàn:</label>
                            <p class="text-muted mb-0 fs-16 fw-medium">{{ number_format($refund->amount) }}đ</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Chi tiết lý do:</label>
                    <div class="border rounded p-3 bg-light">
                        <p class="mb-0">{{ $refund->details }}</p>
                    </div>
                </div>

                @if($refund->images && count($refund->images) > 0)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Hình ảnh minh chứng:</label>
                        <div class="row g-3">
                            @foreach($refund->images as $image)
                            <div class="col-md-3 col-sm-4 col-6">
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Hình minh chứng" 
                                         class="img-fluid rounded border cursor-pointer hover-zoom"
                                         style="aspect-ratio: 1; object-fit: cover; transition: transform 0.3s ease;"
                                         onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                    <div class="position-absolute top-50 start-50 translate-middle opacity-0 hover-icon" 
                                         style="pointer-events: none; transition: opacity 0.3s ease;">
                                        <i class="fas fa-search-plus text-white fs-4"></i>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($refund->bank_name || $refund->account_number || $refund->account_holder)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Thông tin tài khoản nhận hoàn tiền:</label>
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-medium" style="width: 150px;">Tên ngân hàng:</td>
                                    <td>{{ $refund->bank_name ?? 'Chưa cập nhật' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Số tài khoản:</td>
                                    <td>{{ $refund->account_number ?? 'Chưa cập nhật' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Tên chủ TK:</td>
                                    <td>{{ $refund->account_holder ?? 'Chưa cập nhật' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif

                @if($refund->admin_note)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ghi chú của Admin:</label>
                        <div class="border rounded p-3 bg-warning-subtle">
                            <p class="mb-0">{{ $refund->admin_note }}</p>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ngày yêu cầu:</label>
                            <p class="text-muted mb-0">{{ $refund->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    @if($refund->processed_at)
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ngày xử lý:</label>
                                <p class="text-muted mb-0">{{ $refund->processed_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Process Refund Form -->
        @if($refund->status === 'pending')
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Xử lý yêu cầu hoàn tiền</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Quyết định <span class="text-danger">*</span></label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" 
                                           id="approve" value="approve" required>
                                    <label class="form-check-label text-success fw-medium" for="approve">
                                        <i class="ri-check-line me-1"></i> Duyệt hoàn tiền
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" 
                                           id="reject" value="rejected" required>
                                    <label class="form-check-label text-danger fw-medium" for="reject">
                                        <i class="ri-close-line me-1"></i> Từ chối hoàn tiền
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="admin_note" class="form-label">Ghi chú <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('admin_note') is-invalid @enderror" 
                                      id="admin_note" name="admin_note" rows="4" required
                                      placeholder="Nhập ghi chú về quyết định của bạn...">{{ old('admin_note') }}</textarea>
                            @error('admin_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary me-2">
                                <i class="ri-arrow-left-line me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Xử lý yêu cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Order Information Sidebar -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-medium">Mã đơn hàng:</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $refund->order->id) }}" 
                                   class="text-primary fw-medium">
                                    #{{ $refund->order->order_code }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Khách hàng:</td>
                            <td>{{ $refund->order->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Email:</td>
                            <td>{{ $refund->order->user->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Tổng tiền:</td>
                            <td class="fw-semibold">{{ number_format($refund->order->total_amount) }}đ</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Trạng thái ĐH:</td>
                            <td>
                                <span class="badge bg-success">{{ $refund->order->orderStatus->name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">TT Thanh toán:</td>
                            <td>
                                <span class="badge 
                                    @if($refund->order->paymentStatus->name === 'Đã Thanh Toán') bg-success
                                    @elseif($refund->order->paymentStatus->name === 'Đang Hoàn Tiền') bg-warning
                                    @elseif($refund->order->paymentStatus->name === 'Đã Hoàn Tiền') bg-info
                                    @else bg-secondary @endif">
                                    {{ $refund->order->paymentStatus->name }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">PT Thanh toán:</td>
                            <td>{{ $refund->order->paymentMethod->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Ngày đặt:</td>
                            <td>{{ $refund->order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thao tác nhanh</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.orders.show', $refund->order->id) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="ri-eye-line me-1"></i> Xem chi tiết đơn hàng
                    </a>
                    <a href="{{ route('admin.users.show', $refund->order->user->id) }}" 
                       class="btn btn-outline-info btn-sm">
                        <i class="ri-user-line me-1"></i> Xem thông tin khách hàng
                    </a>
                    @if($refund->status === 'approve')
                        <button type="button" class="btn btn-outline-success btn-sm">
                            <i class="ri-check-double-line me-1"></i> Đã hoàn tiền
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Confirmation for form submission
    $('form').on('submit', function(e) {
        const selectedStatus = $('input[name="status"]:checked').val();
        const actionText = selectedStatus === 'approve' ? 'duyệt hoàn tiền' : 'từ chối yêu cầu';
        
        if (!confirm(`Bạn có chắc chắn muốn ${actionText} cho yêu cầu này?`)) {
            e.preventDefault();
            return false;
        }
    });
});

// Image modal functions
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.addEventListener('click', function(e) {
    const modal = document.getElementById('imageModal');
    if (e.target === modal) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Hover effects for images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.hover-zoom');
    images.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.nextElementSibling.style.opacity = '1';
        });
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.nextElementSibling.style.opacity = '0';
        });
    });
});
</script>

<!-- Image Modal -->
<div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="position: relative; max-width: 90%; max-height: 90%;">
        <button onclick="closeImageModal()" style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Hình ảnh phóng to" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">
    </div>
</div>

@endpush
