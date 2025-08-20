@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thùng rác - Voucher</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Form -->
                        <form action="{{ route('admin.vouchers.trash') }}" method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Tìm mã hoặc mô tả..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Voucher List -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Mô tả</th>
                                        <th>Giảm giá</th>
                                        <th>Điều kiện</th>
                                        <th>Thời hạn</th>
                                        <th>Số lượng</th>
                                        <th>Ngày xóa</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vouchers as $voucher)
                                        <tr>
                                            <td>{{ $voucher->code }}</td>
                                            <td>{{ Str::limit($voucher->description, 40) }}</td>
                                            <td>
                                                {{ $voucher->discount_percent }}%
                                                @if ($voucher->max_discount)
                                                    <br>
                                                    <small>Tối đa: {{ number_format($voucher->max_discount) }}đ</small>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($voucher->conditions as $condition)
                                                    <div>
                                                        @switch($condition->type)
                                                            @case('all')
                                                                Tất cả sản phẩm
                                                            @break

                                                            @case('category')
                                                                Danh mục:
                                                                {{ $condition->categoryCondition->name ?? 'Không xác định' }}
                                                            @break

                                                            @case('author')
                                                                Tác giả: {{ $condition->authorCondition->name ?? 'Không xác định' }}
                                                            @break

                                                            @case('brand')
                                                                Thương hiệu:
                                                                {{ $condition->brandCondition->name ?? 'Không xác định' }}
                                                            @break

                                                            @case('book')
                                                                Sách: {{ $condition->bookCondition->title ?? 'Không xác định' }}
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
                                            <td>{{ $voucher->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <form action="{{ route('admin.vouchers.restore', $voucher->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn muốn khôi phục?')">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </button>
                                                </form>
                                                {{-- <form action="{{ route('admin.vouchers.force-delete', $voucher->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form> --}}
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Không có voucher nào trong thùng rác</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $vouchers->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
