@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <!-- Thống kê -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalVouchers }}</h3>
                    <p>Tổng số voucher</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $activeVouchers }}</h3>
                    <p>Voucher đang hoạt động</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $inactiveVouchers }}</h3>
                    <p>Voucher Không hoạt động</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $usedVouchersCount }}</h3>
                    <p>Lượt sử dụng</p>
                </div>
                <div class="icon">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản lý Voucher</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                        <a href="{{ route('admin.vouchers.trash') }}" class="btn btn-secondary">
                            <i class="fas fa-trash"></i> Thùng rác
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form action="{{ route('admin.vouchers.index') }}" method="GET" class="mb-4" id="filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="Tìm theo mã hoặc mô tả..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="date_from" class="form-control"
                                           placeholder="Từ ngày"
                                           value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="date_to" class="form-control"
                                           placeholder="Đến ngày"
                                           value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Làm mới
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Voucher List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
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
                                    <td>{{ $voucher->code }}</td>
                                    <td>
                                        {{ $voucher->discount_percent }}%
                                        @if($voucher->max_discount)
                                            <br>
                                            <small>Tối đa: {{ number_format($voucher->max_discount) }}đ</small>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($voucher->conditions as $condition)
                                            <div>
                                                @switch($condition->type)
                                                    @case('all')
                                                        Tất cả sản phẩm
                                                        @break
                                                    @case('category')
                                                        Danh mục: {{ $condition->categoryCondition->name }}
                                                        @break
                                                    @case('author')
                                                        Tác giả: {{ $condition->authorCondition->name }}
                                                        @break
                                                    @case('brand')
                                                        Thương hiệu: {{ $condition->brandCondition->name }}
                                                        @break
                                                    @case('book')
                                                        Sách: {{ $condition->bookCondition->title }}
                                                        @break
                                                @endswitch
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div>Từ: {{ $voucher->valid_from->format('d/m/Y') }}</div>
                                        <div>Đến: {{ $voucher->valid_to->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        {{ $voucher->applied_vouchers_count }}/{{ $voucher->quantity }}
                                    </td>
                                    <td>
                                        @if($voucher->status === 'active')
                                            <span class="badge badge-success text-dark">Hoạt động</span>
                                        @else
                                            <span class="badge badge-warning text-dark">Không hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.vouchers.show', $voucher) }}"
                                               class="btn btn-info btn-sm" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.vouchers.edit', $voucher) }}"
                                               class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa voucher này?')"
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
