@extends('layouts.backend')
@section('title', 'Quản lý ví')

@section('content')
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quản lý ví và giao dịch</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Quản lý ví</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="ri-wallet-3-line me-2 text-success fs-20"></i>
                            Danh sách giao dịch ví
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">Tổng số ví</p>
                                                <h4 class="mb-0">{{ $totalWallets }}</h4>
                                            </div>
                                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                                <span class="avatar-title rounded-circle bg-primary">
                                                    <i class="ri-wallet-line fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">Tổng số giao dịch</p>
                                                <h4 class="mb-0">{{ $totalTransactions }}</h4>
                                            </div>
                                            <div class="avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                                                <span class="avatar-title rounded-circle bg-success">
                                                    <i class="ri-exchange-dollar-line fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">Tổng nạp</p>
                                                <h4 class="mb-0">{{ number_format($totalDeposits, 0, ',', '.') }} đ</h4>
                                            </div>
                                            <div class="avatar-sm rounded-circle bg-info align-self-center mini-stat-icon">
                                                <span class="avatar-title rounded-circle bg-info">
                                                    <i class="ri-arrow-down-circle-line fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">Tổng chi tiêu</p>
                                                <h4 class="mb-0">{{ number_format($totalWithdrawals, 0, ',', '.') }} đ</h4>
                                            </div>
                                            <div class="avatar-sm rounded-circle bg-danger align-self-center mini-stat-icon">
                                                <span class="avatar-title rounded-circle bg-danger">
                                                    <i class="ri-arrow-up-circle-line fs-4"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm theo email người dùng...">
                                    <button class="btn btn-primary" type="button" id="searchButton">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="transactionTypeFilter">
                                    <option value="">Tất cả giao dịch</option>
                                    <option value="deposit">Nạp tiền</option>
                                    <option value="withdrawal">Thanh toán</option>
                                    <option value="refund">Hoàn tiền</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateRangeFilter" placeholder="Chọn khoảng thời gian">
                                    <button class="btn btn-soft-secondary" type="button">
                                        <i class="ri-calendar-2-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-soft-danger" id="resetFilter">
                                    <i class="ri-refresh-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">ID giao dịch</th>
                                        <th scope="col">Người dùng</th>
                                        <th scope="col">Loại giao dịch</th>
                                        <th scope="col">Số tiền</th>
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Đơn hàng liên quan</th>
                                        <th scope="col">Thời gian</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $key => $transaction)
                                    <tr>
                                        <td>
                                            <span class="fw-medium">{{ ++$key }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ substr($transaction->id, 0, 8)  }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        {{ substr($transaction->wallet->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="fs-13 mb-0">{{ $transaction->wallet->user->name }}</h5>
                                                    <p class="fs-12 mb-0 text-muted">{{ $transaction->wallet->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($transaction->type == 'deposit')
                                                <span class="badge bg-success">Nạp tiền</span>
                                            @elseif($transaction->type == 'withdraw')
                                                <span class="badge bg-danger">Rút Tiền</span>
                                            @elseif($transaction->type == 'refund')
                                                <span class="badge bg-info">Hoàn tiền</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $transaction->type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->amount > 0)
                                                <span class="text-success">+{{ number_format($transaction->amount, 0, ',', '.') }} đ</span>
                                            @else
                                                <span class="text-danger">{{ number_format($transaction->amount, 0, ',', '.') }} đ</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $transaction->description }}</span>
                                        </td>
                                        <td>
                                            @if($transaction->related_order_id)
                                                <a href="{{ route('admin.orders.show', $transaction->related_order_id) }}" class="link-primary">
                                                    Đơn #{{ substr($transaction->related_order_id, 0, 8) }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                                <small class="d-block text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.wallets.show', $transaction->id) }}">
                                                            <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Xem chi tiết
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="">
                                                            <i class="ri-printer-fill align-bottom me-2 text-muted"></i> Xuất PDF
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#deleteModal"
                                                           data-transaction-id="{{ $transaction->id }}">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i> Xóa
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="py-4">
                                                <div class="avatar-md mx-auto mb-4">
                                                    <div class="avatar-title bg-light text-secondary rounded-circle fs-24">
                                                        <i class="ri-file-list-3-line"></i>
                                                    </div>
                                                </div>
                                                <h5 class="mb-2">Không có giao dịch nào được tìm thấy</h5>
                                                <p class="text-muted mb-0">Chưa có giao dịch nào được thực hiện trên hệ thống.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $transactions->links('layouts.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar-md mx-auto mb-4">
                    <div class="avatar-title bg-light text-danger rounded-circle h1">
                        <i class="ri-error-warning-line"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <h4 class="mb-3">Bạn có chắc chắn muốn xóa giao dịch này?</h4>
                    <p class="text-muted mb-4">Hành động này không thể hoàn tác và sẽ xóa vĩnh viễn giao dịch khỏi hệ thống.</p>
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="hstack gap-2 justify-content-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy bỏ</button>
                            <button type="submit" class="btn btn-danger">Xóa vĩnh viễn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Xử lý delete modal
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const transactionId = button.getAttribute('data-transaction-id');
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = `/admin/wallets/transactions/${transactionId}`;
            });
        }

        // Khởi tạo date range picker
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#dateRangeFilter", {
                mode: "range",
                dateFormat: "d/m/Y",
                locale: {
                    rangeSeparator: ' đến '
                }
            });
        }

        // Xử lý filter
        document.getElementById('transactionTypeFilter').addEventListener('change', function() {
            applyFilters();
        });

        document.getElementById('searchButton').addEventListener('click', function() {
            applyFilters();
        });

        document.getElementById('resetFilter').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('transactionTypeFilter').selectedIndex = 0;
            if (typeof flatpickr !== 'undefined') {
                const dateRangePicker = document.getElementById('dateRangeFilter')._flatpickr;
                dateRangePicker.clear();
            } else {
                document.getElementById('dateRangeFilter').value = '';
            }
            window.location.href = '{{ route("admin.wallets.index") }}';
        });

        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value;
            const transactionType = document.getElementById('transactionTypeFilter').value;
            const dateRange = document.getElementById('dateRangeFilter').value;

            let url = new URL(window.location.href);
            url.searchParams.delete('search');
            url.searchParams.delete('type');
            url.searchParams.delete('date_range');

            if (searchTerm) {
                url.searchParams.append('search', searchTerm);
            }

            if (transactionType) {
                url.searchParams.append('type', transactionType);
            }

            if (dateRange) {
                url.searchParams.append('date_range', dateRange);
            }

            window.location.href = url.toString();
        }
    });
</script>
@endpush
