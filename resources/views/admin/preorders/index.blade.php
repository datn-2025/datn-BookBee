@extends('layouts.backend')

@section('title', 'Quản lý sách đặt trước')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý sách đặt trước</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download"></i> Xuất Excel
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đơn đặt trước
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chờ xác nhận
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Đã xác nhận
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['confirmed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đã giao hàng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['delivered'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.preorders.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Tên sách, khách hàng, email...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Tất cả</option>
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="date_from" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="date_to" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.preorders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preorders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn đặt trước</h6>
            @if($preorders->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-tasks"></i> Thao tác hàng loạt
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('confirmed')">Xác nhận tất cả</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('preparing')">Chuẩn bị tất cả</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('shipped')">Gửi hàng tất cả</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('delivered')">Giao hàng tất cả</a></li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body">
            @if($preorders->count() > 0)
                <form id="bulkForm" method="POST" action="{{ route('admin.preorders.bulkUpdateStatus') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Sách</th>
                                    <th>Khách hàng</th>
                                    <th>Liên hệ</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($preorders as $preorder)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="preorder_ids[]" value="{{ $preorder->id }}" class="preorder-checkbox">
                                        </td>
                                        <td>#{{ $preorder->id }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $preorder->book_title }}</div>
                                            @if($preorder->book_format_name)
                                                <small class="text-muted">{{ $preorder->book_format_name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $preorder->customer_name }}</div>
                                            <div class="text-muted small">{{ $preorder->customer_address }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $preorder->customer_email }}</div>
                                            <div class="text-muted small">{{ $preorder->customer_phone }}</div>
                                        </td>
                                        <td class="text-center">{{ $preorder->quantity }}</td>
                                        <td class="text-end">{{ $preorder->formatted_total_price }}</td>
                                        <td>
                                            <span class="badge {{ $preorder->status_color }}">
                                                {{ $preorder->status_text }}
                                            </span>
                                        </td>
                                        <td>{{ $preorder->created_at ? $preorder->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.preorders.show', $preorder) }}" 
                                                   class="btn btn-info btn-sm" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-warning btn-sm" 
                                                        data-bs-toggle="modal" data-bs-target="#statusModal{{ $preorder->id }}" 
                                                        title="Cập nhật trạng thái">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="confirmDelete('{{ $preorder->id }}')" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Status Update Modal -->
                                    <div class="modal fade" id="statusModal{{ $preorder->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.preorders.updateStatus', $preorder) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Cập nhật trạng thái đơn #{{ $preorder->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="status{{ $preorder->id }}" class="form-label">Trạng thái</label>
                                                            <select class="form-control" id="status{{ $preorder->id }}" name="status" required>
                                                                @foreach($statuses as $key => $value)
                                                                    <option value="{{ $key }}" {{ $preorder->status === $key ? 'selected' : '' }}>
                                                                        {{ $value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="admin_notes{{ $preorder->id }}" class="form-label">Ghi chú</label>
                                                            <textarea class="form-control" id="admin_notes{{ $preorder->id }}" 
                                                                    name="admin_notes" rows="3" 
                                                                    placeholder="Ghi chú của admin...">{{ $preorder->admin_notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $preorders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có đơn đặt trước nào</h5>
                    <p class="text-muted">Các đơn đặt trước sẽ xuất hiện tại đây khi khách hàng đặt sách.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('admin.preorders.export') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Xuất dữ liệu Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="export_status" name="status">
                            <option value="">Tất cả</option>
                            @foreach($statuses as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_date_from" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="export_date_from" name="date_from">
                    </div>
                    <div class="mb-3">
                        <label for="export_date_to" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="export_date_to" name="date_to">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Xuất Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Select all checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.preorder-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk update status
    function bulkUpdateStatus(status) {
        const checkedBoxes = document.querySelectorAll('.preorder-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Vui lòng chọn ít nhất một đơn đặt trước!');
            return;
        }

        if (confirm('Bạn có chắc chắn muốn cập nhật trạng thái cho các đơn đã chọn?')) {
            const form = document.getElementById('bulkForm');
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);
            form.submit();
        }
    }

    // Confirm delete
    function confirmDelete(preorderId) {
        if (confirm('Bạn có chắc chắn muốn xóa đơn đặt trước này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.preorders.index') }}/${preorderId}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
