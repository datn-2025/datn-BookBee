@extends('layouts.backend')

@section('title', 'Thùng rác - Phương thức thanh toán')

@section('content')
    <div class="container-fluid">
        <!-- Tiêu đề -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Thùng rác - Phương thức thanh toán</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.payment-methods.index') }}">Phương thức thanh
                                    toán</a></li>
                            <li class="breadcrumb-item active">Thùng rác</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nội dung -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Danh sách phương thức đã xoá</h4>
                        <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary btn-sm px-4">
                            <i class="ri-arrow-left-line me-1"></i> Quay lại
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Thanh tìm kiếm -->
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.payment-methods.trash') }}"
                                    class="d-flex justify-content-md-start align-items-center gap-2">
                                    <input type="text" name="search" value="{{ old('search', request('search')) }}"
                                        class="form-control" placeholder="Tìm theo tên phương thức" style="width: 300px;">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="ri-search-2-line"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.payment-methods.trash') }}"
                                        class="btn btn-outline-secondary px-4">
                                        <i class="ri-refresh-line"></i> Làm mới
                                    </a>
                                </form>
                            </div>
                        </div>

                        <!-- Bảng dữ liệu -->
                        <div class="table-responsive table-card mt-3 mb-1">
                            @if ($paymentMethods->isEmpty())
                                <div class="noresult text-center py-5">
                                    @if (filled(request('search')))
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#f06548"
                                            style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-3 text-danger">Không tìm thấy phương thức phù hợp</h5>
                                        <p class="text-muted">Không có phương thức nào trong thùng rác khớp với từ khoá:
                                            <strong>"{{ request('search') }}"</strong>.
                                        </p>
                                    @else
                                        <lord-icon src="https://cdn.lordicon.com/jmkrnisz.json" trigger="loop"
                                            style="width:90px;height:90px"></lord-icon>
                                        <h5 class="mt-3 text-muted">Thùng rác đang trống</h5>
                                        <p class="text-muted">Hiện không có phương thức thanh toán nào bị xóa.</p>
                                    @endif
                                </div>
                            @else
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên phương thức</th>
                                            <th>Ngày xoá</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paymentMethods as $key => $method)
                                            <tr>
                                                <td>{{ $paymentMethods->firstItem() + $key }}</td>
                                                <td>{{ $method->name }}</td>
                                                <td>{{ $method->deleted_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group gap-2">
                                                        <form
                                                            action="{{ route('admin.payment-methods.restore', $method->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                title="Khôi phục">
                                                                <i class="ri-refresh-line"></i>
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('admin.payment-methods.force-delete', $method->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn phương thức thanh toán này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Xóa vĩnh viễn" {{ $method->payments_count > 0 ? 'disabled' : '' }}>
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Phân trang -->
                                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                                    <div class="text-muted">
                                        Hiển thị <strong>{{ $paymentMethods->firstItem() }}</strong> đến
                                        <strong>{{ $paymentMethods->lastItem() }}</strong> trong tổng số
                                        <strong>{{ $paymentMethods->total() }}</strong> phương thức bị xoá
                                    </div>
                                    <div>
                                        {{ $paymentMethods->withQueryString()->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div>
        </div>
    </div>
@endsection
