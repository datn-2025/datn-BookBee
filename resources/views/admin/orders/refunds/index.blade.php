@extends('layouts.backend')

@section('title', 'Danh sách yêu cầu hoàn tiền')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Danh sách yêu cầu hoàn tiền</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                    <li class="breadcrumb-item active">Yêu cầu hoàn tiền</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Chờ xử lý</p>
                        <h4 class="mb-0">{{ $refunds->where('status', 'pending')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-warning-subtle">
                            <span class="avatar-title rounded-circle fs-2">
                                <i class="bx bx-time text-warning"></i>
                            </span>
                        </div>
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
                        <p class="text-uppercase fw-medium text-muted mb-0">Đã duyệt</p>
                        <h4 class="mb-0">{{ $refunds->where('status', 'approve')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-success-subtle">
                            <span class="avatar-title rounded-circle fs-2">
                                <i class="bx bx-check-circle text-success"></i>
                            </span>
                        </div>
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
                        <p class="text-uppercase fw-medium text-muted mb-0">Đã từ chối</p>
                        <h4 class="mb-0">{{ $refunds->where('status', 'rejected')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-danger-subtle">
                            <span class="avatar-title rounded-circle fs-2">
                                <i class="bx bx-x-circle text-danger"></i>
                            </span>
                        </div>
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
                        <p class="text-uppercase fw-medium text-muted mb-0">Tổng số tiền</p>
                        <h4 class="mb-0">{{ number_format($refunds->where('status', 'approve')->sum('amount')) }}đ</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-info-subtle">
                            <span class="avatar-title rounded-circle fs-2">
                                <i class="bx bx-money text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Requests Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Danh sách yêu cầu hoàn tiền</h5>
                </div>
            </div>
            
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <!-- Filter Form -->
                <form action="{{ route('admin.refunds.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Tìm theo mã đơn hàng..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="approve" {{ request('status') === 'approve' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Lý do</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày yêu cầu</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($refunds as $refund)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $refund->order->id) }}" 
                                           class="fw-medium text-primary">
                                            #{{ $refund->order->order_code }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $refund->order->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $refund->order->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            // Bản đồ lý do sang tiếng Việt
                                            $reasonLabels = [
                                                'wrong_item' => 'Sản phẩm không đúng mô tả',
                                                'quality_issue' => 'Vấn đề về chất lượng',
                                                'shipping_delay' => 'Giao hàng quá chậm',
                                                'wrong_qty' => 'Số lượng không đúng',
                                                'damaged' => 'Sản phẩm hư hỏng',
                                                'other' => 'Lý do khác',
                                            ];
                                            $reasonVN = $reasonLabels[$refund->reason] ?? $refund->reason;
                                        @endphp
                                        <span class="badge bg-secondary">{{ $reasonVN }}</span>
                                    </td>
                                    <td class="fw-medium">{{ number_format($refund->amount) }}đ</td>
                                    <td>
                                        <span class="badge 
                                            @if($refund->status === 'pending') bg-warning
                                            @elseif($refund->status === 'processing') bg-info
                                            @elseif($refund->status === 'completed') bg-success
                                            @elseif($refund->status === 'rejected') bg-danger
                                            @else bg-secondary @endif">
                                            @if($refund->status === 'pending') Chờ xử lý
                                            @elseif($refund->status === 'processing') Đang xử lý
                                            @elseif($refund->status === 'completed') Đã Duyệt
                                            @elseif($refund->status === 'rejected') Đã từ chối
                                            @else {{ $refund->status }} @endif
                                        </span>
                                    </td>
                                    <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ route('admin.refunds.show', $refund->id) }}">
                                                        <i class="ri-eye-fill me-2"></i> Xem chi tiết
                                                    </a>
                                                </li>
                                                @if($refund->status === 'pending')
                                                    <li>
                                                        <button type="button" class="dropdown-item text-success" 
                                                                onclick="processRefund('{{ $refund->id }}', 'approve')">
                                                            <i class="ri-check-line me-2"></i> Duyệt hoàn tiền
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger" 
                                                                onclick="processRefund('{{ $refund->id }}', 'rejected')">
                                                            <i class="ri-close-line me-2"></i> Từ chối
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ri-search-line fs-1 mb-3 d-block"></i>
                                            Không có yêu cầu hoàn tiền nào
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($refunds->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $refunds->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Process Refund Modal -->
<div class="modal fade" id="processRefundModal" tabindex="-1" aria-labelledby="processRefundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processRefundModalLabel">Xử lý yêu cầu hoàn tiền</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="processRefundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_note" class="form-label">Ghi chú <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="admin_note" name="admin_note" rows="4" required
                                  placeholder="Nhập ghi chú về quyết định của bạn..."></textarea>
                    </div>
                    <input type="hidden" name="action" id="refund_status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Xử lý yêu cầu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function processRefund(refundId, status) {
    const form = document.getElementById('processRefundForm');
    const modal = new bootstrap.Modal(document.getElementById('processRefundModal'));
    const modalTitle = document.getElementById('processRefundModalLabel');
    const submitBtn = document.getElementById('submitBtn');
    const statusInput = document.getElementById('refund_status');
    
    // Set form action
    form.action = '{{ route("admin.refunds.process", ":id") }}'.replace(':id', refundId);
    
    // Set status
    statusInput.value = status;
    
    // Update modal title and button text
    if (status === 'approve') {
        modalTitle.textContent = 'Duyệt yêu cầu hoàn tiền';
        submitBtn.textContent = 'Duyệt yêu cầu';
        submitBtn.className = 'btn btn-success';
    } else {
        modalTitle.textContent = 'Từ chối yêu cầu hoàn tiền';
        submitBtn.textContent = 'Từ chối yêu cầu';
        submitBtn.className = 'btn btn-danger';
    }
    
    // Clear previous note
    document.getElementById('admin_note').value = '';
    
    // Show modal
    modal.show();
}
</script>
@endsection
