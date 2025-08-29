@extends('layouts.backend')

@section('title', 'Quản lý Voucher')

@section('styles')
<link href="{{ asset('css/admin-vouchers.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý Voucher</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Voucher</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Thống kê -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Tổng số voucher</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ $totalVouchers }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-info rounded fs-3">
                                <i class="ri-coupon-line text-info"></i>
                            </span>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Đang hoạt động</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ $activeVouchers }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded fs-3">
                                <i class="ri-checkbox-circle-line text-success"></i>
                            </span>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Không hoạt động</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ $inactiveVouchers }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-warning rounded fs-3">
                                <i class="ri-indeterminate-circle-line text-warning"></i>
                            </span>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Lượt sử dụng</p>
                            <h4 class="fs-22 fw-semibold mb-0">{{ $usedVouchersCount }}</h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-soft-danger rounded fs-3">
                                <i class="ri-history-line text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-tags me-2"></i>Quản lý Voucher</h3>
                    <div class="card-tools d-flex gap-2">
                        @permission('voucher.trash')
                        <a href="{{ route('admin.vouchers.trash') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-trash"></i> Thùng rác
                        </a>
                        @endpermission
                        @permission('voucher.create')
                        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                        @endpermission
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form action="{{ route('admin.vouchers.index') }}" method="GET" class="mb-4" id="filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tìm kiếm</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Mã hoặc mô tả voucher..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Từ ngày</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Đến ngày</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-grid d-md-flex gap-2">
                                <button type="submit" class="btn btn-info w-100"><i class="fas fa-filter me-1"></i>Lọc</button>
                                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Reset</a>
                            </div>
                        </div>
                    </form>

                    <!-- Voucher List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Giảm giá</th>
                                    <th>Điều kiện</th>
                                    <th>Thời hạn</th>
                                    <th>Đã sử dụng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vouchers as $voucher)
                                <tr>
                                    <td class="fw-semibold text-primary">
                                        <i class="fas fa-hashtag me-1"></i>{{ $voucher->code }}
                                    </td>
                                    <td>
                                        @if($voucher->discount_type === 'fixed')
                                            <span class="badge rounded-pill bg-primary"><i class="fas fa-money-bill-wave me-1"></i>Cố định</span>
                                            <div class="mt-1 fw-bold">{{ number_format($voucher->fixed_discount) }}đ</div>
                                        @else
                                            <span class="badge rounded-pill bg-info text-dark"><i class="fas fa-percent me-1"></i>Phần trăm</span>
                                            <div class="mt-1 fw-bold">{{ rtrim(rtrim(number_format($voucher->discount_percent, 2), '0'), '.') }}%</div>
                                            @if(!is_null($voucher->max_discount))
                                                <small class="text-muted">Tối đa: {{ number_format($voucher->max_discount) }}đ</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($voucher->conditions as $condition)
                                            @switch($condition->type)
                                                @case('all')
                                                    <span class="badge rounded-pill bg-secondary me-1 mb-1">Tất cả sản phẩm</span>
                                                    @break
                                                @case('category')
                                                    <span class="badge rounded-pill bg-light text-dark border me-1 mb-1"><i class="fas fa-folder-open me-1"></i>Danh mục: {{ $condition->categoryCondition->name }}</span>
                                                    @break
                                                @case('author')
                                                    <span class="badge rounded-pill bg-light text-dark border me-1 mb-1"><i class="fas fa-user-edit me-1"></i>Tác giả: {{ $condition->authorCondition->name }}</span>
                                                    @break
                                                @case('brand')
                                                    <span class="badge rounded-pill bg-light text-dark border me-1 mb-1"><i class="fas fa-copyright me-1"></i>Thương hiệu: {{ $condition->brandCondition->name }}</span>
                                                    @break
                                            @endswitch
                                        @endforeach
                                    </td>
                                    <td>
                                        <div><span class="badge bg-light text-dark border"><i class="far fa-calendar-check me-1"></i>Từ: {{ $voucher->valid_from->format('d/m/Y') }}</span></div>
                                        <div class="mt-1"><span class="badge bg-light text-dark border"><i class="far fa-calendar-times me-1"></i>Đến: {{ $voucher->valid_to->format('d/m/Y') }}</span></div>
                                    </td>
                                    <td>
                                        @php
                                            // Tính phần trăm sử dụng
                                            $totalQty = max(1, (int) $voucher->quantity);
                                            $used = (int) $voucher->applied_vouchers_count;
                                            $percent = min(100, round($used / $totalQty * 100));
                                        @endphp
                                        <div class="small text-muted mb-1">{{ $used }}/{{ $voucher->quantity }}</div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            // Xác định trạng thái hiển thị theo ngày và cờ status
                                            $now = now();
                                            $isActiveFlag = ($voucher->status === 'active');
                                            $isWithin = $now->between($voucher->valid_from, $voucher->valid_to);
                                            $isFuture = $now->lt($voucher->valid_from);
                                            $isPast = $now->gt($voucher->valid_to);
                                        @endphp
                                        @if($isActiveFlag && $isWithin)
                                            <span class="badge rounded-pill bg-success"><i class="fas fa-check-circle me-1"></i>Đang hoạt động</span>
                                        @elseif($isFuture)
                                            <span class="badge rounded-pill bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Sắp diễn ra</span>
                                        @elseif($isPast)
                                            <span class="badge rounded-pill bg-secondary"><i class="fas fa-history me-1"></i>Hết hạn</span>
                                        @elseif(!$isActiveFlag)
                                            <span class="badge rounded-pill bg-danger"><i class="fas fa-ban me-1"></i>Không hoạt động</span>
                                        @else
                                            <span class="badge rounded-pill bg-light text-dark border">Khác</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @permission('voucher.show')
                                            <a href="{{ route('admin.vouchers.show', $voucher) }}"
                                               class="btn btn-info btn-sm" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endpermission
                                            @permission('voucher.edit')
                                            <a href="{{ route('admin.vouchers.edit', $voucher) }}"
                                               class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endpermission
                                            @permission('voucher.delete')
                                            {{-- <form action="{{ route('admin.vouchers.destroy', $voucher) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa voucher này?')"
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form> --}}
                                            @endpermission
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không có voucher nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $vouchers->withQueryString()->links('layouts.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Lắng nghe click trên các liên kết phân trang bên trong .pagination
        $('.pagination').on('click', 'a.page-link', function(e) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định

            var url = $(this).attr('href');
            var pageMatch = url.match(/page=(\d+)/); // Tìm số trang trong URL

            if (pageMatch && pageMatch[1]) {
                var page = pageMatch[1];
                var form = $('#filter-form'); // Lấy form lọc

                // Thêm hoặc cập nhật input hidden cho số trang
                var pageInput = form.find('input[name="page"]');
                if (pageInput.length === 0) {
                    pageInput = $('<input type="hidden" name="page">');
                    form.append(pageInput);
                }
                pageInput.val(page);

                // Submit form
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection
