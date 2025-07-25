@extends('layouts.backend')

@section('title', 'Chi Tiết Ví Người Dùng')

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Chi Tiết Ví Người Dùng</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.wallets.index') }}">Quản lý ví</a></li>
                            <li class="breadcrumb-item active">Chi tiết ví</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin ví -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Thông tin ví người dùng</h5>
                        <a href="{{ route('admin.wallets.index') }}" class="btn btn-sm btn-light">
                            <i class="ri-arrow-go-back-line me-1"></i> Quay lại danh sách
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3"><strong>Người dùng:</strong> {{ $wallet->user->name ?? 'Không rõ' }}</div>
                                <div class="mb-3"><strong>Email:</strong> {{ $wallet->user->email ?? '-' }}</div>
                                <div class="mb-3"><strong>Số dư:</strong> ₫{{ number_format($wallet->balance, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3"><strong>Lần cập nhật cuối:</strong> {{ $wallet->updated_at->format('d/m/Y H:i') }}</div>
                                <div class="mb-3"><strong>Số giao dịch:</strong> {{ $transactions->total() }}</div>
                                <div class="mb-3"><strong>Ngày tạo:</strong> {{ $wallet->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng lịch sử giao dịch -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Lịch sử giao dịch</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Loại</th>
                                        <th>Số tiền</th>
                                        <th>Mô tả</th>
                                        <th>Đơn liên quan</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $index => $transaction)
                                        <tr>
                                            <td>{{ $transactions->firstItem() + $index }}</td>
                                            <td>
                                                @if($transaction->type === 'deposit')
                                                    <span class="badge bg-success">Nạp</span>
                                                @elseif($transaction->type === 'withdraw')
                                                    <span class="badge bg-danger">Rút</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($transaction->type) }}</span>
                                                @endif
                                            </td>
                                            <td>₫{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                            <td>{{ $transaction->description ?? '-' }}</td>
                                            <td>{{ $transaction->related_order_id ?? '-' }}</td>
                                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Chưa có giao dịch nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- PHÂN TRANG -->
                        <div class="d-flex justify-content-end mt-4">
                            <nav>
                                @if ($transactions->hasPages())
                                    <ul class="pagination mb-0">
                                        {{-- Previous Page Link --}}
                                        @if ($transactions->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">Prev</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $transactions->previousPageUrl() }}" rel="prev">Prev</a>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                                            @if ($page == $transactions->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($transactions->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $transactions->nextPageUrl() }}" rel="next">Next</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">Next</span>
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
