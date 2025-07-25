@extends('layouts.backend')
@section('title', 'Lịch sử nạp ví')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Lịch sử nạp ví</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Nạp ví</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-wallet-3-line me-2 text-primary fs-20"></i>Lịch sử nạp ví</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">Người dùng</label>
                <input type="text" name="user" value="{{ request('user') }}" class="form-control" placeholder="Tên hoặc email người dùng">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="success" @selected(request('status')=='success')>Thành công</option>
                    <option value="pending" @selected(request('status')=='pending')>Chờ duyệt</option>
                    <option value="failed" @selected(request('status')=='failed')>Thất bại</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Thời gian</label>
                <input type="text" name="date_range" value="{{ request('date_range') }}" class="form-control" id="dateRangeFilter" placeholder="Chọn khoảng thời gian">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary " type="submit"><i class="ri-search-line"></i> Tìm kiếm</button>
                <a href="{{ route('admin.wallets.depositHistory') }}" class="btn btn-light me-2">
                    <i class="ri-refresh-line"></i> Đặt lại
                </a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle table-nowrap mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Người dùng</th>
                        <th class="text-center">Số tiền nạp</th>
                        <th>Thời gian</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Phương thức</th>
                        <th class="text-center">Duyệt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($depositTransactions as $key => $transaction)
                    <tr>
                        <td class="text-center">{{ $depositTransactions->firstItem() + $key }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs">
                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                        {{ strtoupper(mb_substr($transaction->wallet->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $transaction->wallet->user->name }}</div>
                                    <div class="text-muted small">{{ $transaction->wallet->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-success fw-bold text-center">+{{ number_format($transaction->amount, 0, ',', '.') }} đ</td>
                        <td>
                            <span class="d-block">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                            <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="text-center">
                            @if($transaction->status == 'success')
                                <span class="badge bg-success">Thành công</span>
                            @elseif($transaction->status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @else
                                <span class="badge bg-danger">Thất bại</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->payment_method == 'bank_transfer')
                                <span class="badge bg-info">Chuyển khoản</span>
                            @elseif($transaction->payment_method == 'vnpay')
                                <span class="badge bg-primary">VNPay</span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($transaction->status == 'pending')
                                <div class="d-flex gap-1 justify-content-center">
                                    <form action="{{ route('admin.wallets.approveTransaction', $transaction->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success px-2" title="Duyệt" onclick="return confirm('Duyệt giao dịch này?')">
                                            <i class="ri-check-line"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.wallets.rejectTransaction', $transaction->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger px-2" title="Từ chối" onclick="return confirm('Từ chối giao dịch này?')">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Không có giao dịch nạp ví nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $depositTransactions->appends(request()->query())->links('layouts.pagination') }}
        </div>
    </div>
</div>
@push('scripts')
<script>
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#dateRangeFilter", {
            mode: "range",
            dateFormat: "d/m/Y",
            locale: { rangeSeparator: ' đến ' }
        });
    }
</script>
@endpush
@endsection
