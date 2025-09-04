@extends('layouts.backend')

@section('title', 'Quản Lý Đơn Đặt Trước')

@section('content')
    @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const warningData = @json(session('warning'));
                showConvertWarningModal(warningData);
            });
        </script>
    @endif
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản Lý Đơn Đặt Trước</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.preorders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tạo đơn đặt trước
            </a>
            <a href="{{ route('admin.preorders.export', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Xuất Excel
            </a>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đơn</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ duyệt</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['cho_duyet'] ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đã duyệt</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['da_duyet'] ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sẵn sàng chuyển đổi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['san_sang_chuyen_doi'] ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">Đã chuyển đổi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['da_chuyen_doi'] ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Đã hủy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['da_huy'] ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ Lọc</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.preorders.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Tên, email, số điện thoại...">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control"  name="status">
                            <option value="">Tất cả</option>
                            <option value="{{ \App\Models\Preorder::STATUS_CHO_DUYET }}" {{ request('status') == \App\Models\Preorder::STATUS_CHO_DUYET ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="{{ \App\Models\Preorder::STATUS_DA_DUYET }}" {{ request('status') == \App\Models\Preorder::STATUS_DA_DUYET ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="{{ \App\Models\Preorder::STATUS_SAN_SANG_CHUYEN_DOI }}" {{ request('status') == \App\Models\Preorder::STATUS_SAN_SANG_CHUYEN_DOI ? 'selected' : '' }}>Sẵn sàng chuyển đổi</option>
                    <option value="{{ \App\Models\Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG }}" {{ request('status') == \App\Models\Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG ? 'selected' : '' }}>Đã chuyển đổi</option>
                    <option value="{{ \App\Models\Preorder::STATUS_DA_HUY }}" {{ request('status') == \App\Models\Preorder::STATUS_DA_HUY ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="book_id" class="form-label">Sách</label>
                        <select class="form-control" id="book_id" name="book_id">
                            <option value="">Tất cả sách</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ request('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                        <a href="{{ route('admin.preorders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đơn đặt trước -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh Sách Đơn Đặt Trước</h6>
            <div>
                {{-- <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                    <i class="fas fa-edit"></i> Cập nhật hàng loạt
                </button> --}}
            </div>
        </div>
        <div class="card-body">
            @if($preorders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Sách</th>
                                <th>Định dạng</th>
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
                                    <td><input type="checkbox" class="preorder-checkbox" value="{{ $preorder->id }}"></td>
                                    <td>
                                        <a href="{{ route('admin.preorders.show', $preorder) }}" class="text-decoration-none">
                                            #{{ substr($preorder->id, 0, 8) }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $preorder->customer_name }}</strong><br>
                                            <small class="text-muted">{{ $preorder->email }}</small><br>
                                            <small class="text-muted">{{ $preorder->phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $preorder->book->cover_image ? asset('storage/' . $preorder->book->cover_image) : asset('images/default-book.svg') }}" 
                                                 alt="{{ $preorder->book->title }}" class="me-2" style="width: 40px; height: 50px; object-fit: cover;">
                                            <div>
                                                <strong>{{ Str::limit($preorder->book->title, 30) }}</strong><br>
                                                <small class="text-muted">Ngày ra mắt: {{ $preorder->expected_delivery_date ? $preorder->expected_delivery_date->format('d/m/Y') : 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $preorder->bookFormat && $preorder->bookFormat->format_name == 'Ebook' ? 'info' : 'secondary' }}">
                                            {{ $preorder->bookFormat ? $preorder->bookFormat->format_name : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $preorder->quantity }}</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($preorder->total_amount, 0, ',', '.') }}đ</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                \App\Models\Preorder::STATUS_CHO_DUYET => 'warning',
                                                \App\Models\Preorder::STATUS_DA_DUYET => 'info',
                                                \App\Models\Preorder::STATUS_SAN_SANG_CHUYEN_DOI => 'success',
                                                \App\Models\Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG => 'primary',
                                                \App\Models\Preorder::STATUS_DA_HUY => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$preorder->status] ?? 'secondary' }}">
                                            {{ $preorder->status_text }}
                                        </span>
                                    </td>
                                    <td>{{ $preorder->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                Thao tác
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.preorders.show', $preorder) }}">
                                                        <i class="fas fa-eye"></i> Xem chi tiết
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" onclick="showStatusModal('{{ $preorder->id }}', '{{ $preorder->status }}')">
                                                        <i class="fas fa-edit"></i> Cập nhật trạng thái
                                                    </button>
                                                </li>
                                                @if($preorder->isPending())
                                                    <li>
                                                        <form action="{{ route('admin.preorders.approve', $preorder) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            {{-- <button type="submit" class="dropdown-item text-warning" 
                                                                    onclick="return confirm('Duyệt đơn hàng này?')">
                                                                <i class="fas fa-check"></i>
                                                                Duyệt đơn hàng
                                                            </button> --}}
                                                        </form>
                                                    </li>
                                                @elseif($preorder->isApproved())
                                                    <li>
                                                        @if($preorder->book->isReleased())
                                                            <button type="button" class="dropdown-item text-success" 
                                                                    onclick="showConvertModal('{{ $preorder->id }}', '{{ $preorder->book->title }}', true, '{{ $preorder->book->release_date->format('d/m/Y') }}')">
                                                                <i class="fas fa-exchange-alt"></i>
                                                                Chuyển thành đơn hàng
                                                            </button>
                                                        @else
                                                            {{-- <button type="button" class="dropdown-item text-warning" 
                                                                    onclick="showConvertModal('{{ $preorder->id }}', '{{ $preorder->book->title }}', false, '{{ $preorder->book->release_date->format('d/m/Y') }}')">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                Chuyển thành đơn hàng (Sớm)
                                                            </button> --}}
                                                        @endif
                                                    </li>
                                                @elseif($preorder->isReadyToConvert())
                                                    <li>
                                                        <button type="button" class="dropdown-item text-info" 
                                                                onclick="showConvertModal('{{ $preorder->id }}', '{{ $preorder->book->title }}', true, '{{ $preorder->book->release_date->format('d/m/Y') }}')">
                                                            <i class="fas fa-rocket"></i>
                                                            Chuyển thành đơn hàng
                                                        </button>
                                                    </li>
                                                @endif
                                                @if($preorder->isCancelled())
                                                    <li>
                                                        <form action="{{ route('admin.preorders.destroy', $preorder) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Xóa đơn đặt trước này?')">
                                                                <i class="fas fa-trash"></i> Xóa
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($preorder->isConverted())
                                                    <li>
                                                        <a class="dropdown-item text-primary" href="{{ route('admin.orders.show', $preorder->converted_order_id) }}">
                                                            <i class="fas fa-external-link-alt"></i>
                                                            Xem đơn hàng đã chuyển đổi
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $preorders->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có đơn đặt trước nào</h5>
                    <p class="text-muted">Chưa có đơn đặt trước nào được tạo.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal xác nhận chuyển đổi -->
<div class="modal fade" id="convertModal" tabindex="-1" aria-labelledby="convertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="convertModalLabel">
                    <i class="fas fa-exclamation-triangle"></i>
                    Xác nhận chuyển đổi đơn đặt trước
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="fw-bold">Thông tin đơn hàng:</h6>
                    <div class="ps-3">
                        <p class="mb-2">• Tên sách: <span id="bookTitle" class="fw-bold"></span></p>
                        <p class="mb-2">• Ngày phát hành: <span id="releaseDate" class="fw-bold"></span></p>
                        <p class="mb-0" id="releaseStatus"></p>
                    </div>
                </div>

                <div class="alert alert-warning mb-4" id="warningMessage">
                    <!-- Nội dung cảnh báo sẽ được điền bằng JavaScript -->
                </div>
                
                <div class="mb-3">
                    <p class="fw-bold mb-1">Xác nhận chuyển đổi:</p>
                    <p class="text-muted small">Sau khi chuyển đổi thành đơn hàng, thao tác này không thể hoàn tác. 
                    Hãy chắc chắn rằng bạn muốn thực hiện chuyển đổi.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Hủy bỏ
                </button>
                <form id="convertForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning" id="confirmConvertBtn">
                        <i class="fas fa-exchange-alt"></i> Xác nhận chuyển đổi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal cập nhật trạng thái -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Cập Nhật Trạng Thái</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái mới</label>
                        <select class="form-control" id="modalStatus" name="status" required>
                            <option value="{{ \App\Models\Preorder::STATUS_CHO_DUYET }}">Chờ duyệt</option>
                            <option value="{{ \App\Models\Preorder::STATUS_DA_DUYET }}">Đã duyệt</option>
                            <option value="{{ \App\Models\Preorder::STATUS_SAN_SANG_CHUYEN_DOI }}">Sẵn sàng chuyển đổi</option>
                            <option value="{{ \App\Models\Preorder::STATUS_DA_HUY }}">Đã hủy</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="modalNotes" name="notes" rows="3" 
                                  placeholder="Ghi chú về việc cập nhật trạng thái..."></textarea>
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

<!-- Modal cập nhật hàng loạt -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUpdateModalLabel">Cập Nhật Hàng Loạt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.preorders.bulk-update-status') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulkStatus" class="form-label">Trạng thái mới</label>
                        <select class="form-control" id="bulkStatus" name="status" required>
                            <option value="{{ \App\Models\Preorder::STATUS_CHO_DUYET }}">Chờ duyệt</option>
                            <option value="{{ \App\Models\Preorder::STATUS_DA_DUYET }}">Đã duyệt</option>
                            <option value="{{ \App\Models\Preorder::STATUS_SAN_SANG_CHUYEN_DOI }}">Sẵn sàng chuyển đổi</option>
                            <option value="{{ \App\Models\Preorder::STATUS_DA_HUY }}">Đã hủy</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Vui lòng chọn các đơn đặt trước cần cập nhật bằng cách tick vào checkbox.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="bulkUpdateBtn" disabled>Cập nhật</button>
                </div>
            </form>
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

@push('scripts')
<script>
// Xử lý checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.preorder-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkUpdateButton();
});

document.querySelectorAll('.preorder-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkUpdateButton);
});

function updateBulkUpdateButton() {
    const checkedBoxes = document.querySelectorAll('.preorder-checkbox:checked');
    const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
    bulkUpdateBtn.disabled = checkedBoxes.length === 0;
}

// Hiển thị modal cảnh báo từ controller
function showConvertWarningModal(warningData) {
    document.getElementById('bookTitle').textContent = warningData.bookTitle || '';
    document.getElementById('releaseDate').textContent = warningData.releaseDate || '';
    
    // Cập nhật nội dung cảnh báo
    const warningMessage = document.getElementById('warningMessage');
    warningMessage.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i>
        ${warningData.message}
    `;
    warningMessage.className = 'alert alert-warning mb-4';

    // Cập nhật form action
    const form = document.getElementById('convertForm');
    form.action = warningData.confirm_url;

    // Hiển thị modal
    const modal = new bootstrap.Modal(document.getElementById('convertModal'));
    modal.show();
}

// Modal xác nhận chuyển đổi ban đầu
function showConvertModal(preorderId, bookTitle, isReleased, releaseDate) {
    // Tạo request đến controller để lấy cảnh báo
    fetch(`/admin/preorders/${preorderId}/convert-to-order`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.toast) {
            toastr[data.toast.type](data.toast.message);
            return;
        }
        if (data.warning) {
            showConvertWarningModal(data.warning);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Modal cập nhật trạng thái
function showStatusModal(preorderId, currentStatus) {
    const form = document.getElementById('statusForm');
    form.action = `/admin/preorders/${preorderId}/status`;
    document.getElementById('modalStatus').value = currentStatus;
    document.getElementById('modalNotes').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

// Xử lý form bulk update
document.querySelector('#bulkUpdateModal form').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.preorder-checkbox:checked');
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất một đơn đặt trước.');
        return;
    }
    
    // Thêm các ID đã chọn vào form
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'preorder_ids[]';
        input.value = checkbox.value;
        this.appendChild(input);
    });

// Xử lý cảnh báo duyệt đơn
@if(session('warning'))
    const warningData = @json(session('warning'));
    $('#warningMessage').text(warningData.message);
    $('#confirmConvertBtn').attr('href', warningData.confirm_url);
    $('#warningModal').modal('show');
@endif
});
</script>
@endpush