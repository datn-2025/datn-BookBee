@extends('layouts.backend')

@section('title', 'Quản Lý Phương Thức Thanh Toán')

@section('content')
    <div class="container-fluid">
        <!-- Tiêu đề trang -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quản lý phương thức thanh toán</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Quản lý</a></li>
                            <li class="breadcrumb-item active">Phương thức thanh toán</li>
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
                        <h4 class="card-title mb-0">Danh sách phương thức thanh toán</h4>
                    </div>

                    <div class="card-body">
                        <!-- Thanh công cụ -->
                        <div class="row g-4 mb-3">
                            <div class="col-md-6 d-flex align-items-center gap-2">
                                <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-success btn-sm">
                                    <i class="ri-add-line me-1"></i> Thêm phương thức
                                </a>
                                <a href="{{ route('admin.payment-methods.trash') }}" class="btn btn-danger btn-sm px-4">
                                    <i class="ri-delete-bin-line me-1"></i> Thùng rác
                                    @if ($trashCount > 0)
                                        <span class="badge bg-light text-danger ms-1">{{ $trashCount }}</span>
                                    @endif
                                </a>
                            </div>

                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.payment-methods.index') }}" class="d-flex justify-content-md-end align-items-center gap-2">
                                    <input type="text" name="search" value="{{ old('search', $search ?? '') }}" class="form-control" placeholder="Tìm theo tên phương thức thanh toán" value="{{ request('search') }}" style="width: 300px;">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="ri-search-2-line"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary px-4">
                                        <i class="ri-refresh-line"></i> Làm mới
                                    </a>
                                </form>
                            </div>
                        </div>

                        <!-- Bảng dữ liệu -->
                        <div class="table-responsive table-card mt-3 mb-1">
                            @if ($paymentMethods->isEmpty())
                                <div class="noresult text-center py-5">
                                    @if (filled(request()->get('search')))
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-3 text-danger">Không tìm thấy phương thức thanh toán phù hợp</h5>
                                        <p class="text-muted">Không có phương thức nào khớp với từ khóa <strong>"{{ request()->get('search') }}"</strong>.<br>Vui lòng kiểm tra lại từ khóa hoặc thử lại với nội dung khác.</p>
                                    @else
                                        <lord-icon src="https://cdn.lordicon.com/nocovwne.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:100px;height:100px"></lord-icon>
                                        <h5 class="mt-3 text-muted">Danh sách phương thức thanh toán hiện đang trống</h5>
                                        <p class="text-muted">Nhấn <strong>“Thêm phương thức”</strong> để bắt đầu thiết lập hệ thống thanh toán.</p>
                                    @endif
                                </div>
                            @else
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên phương thức</th>
                                            <th>Mô tả</th>
                                            <th>Ngày tạo</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paymentMethods as $key => $method)
                                            <tr>
                                                <td>{{ $paymentMethods->firstItem() + $key }}</td>
                                                <td>{{ $method->name }}</td>
                                                <td class="text-truncate" style="max-width: 260px;" title="{{ $method->description }}">
                                                    {!! $method->description ?: '<span class="text-muted">Không có mô tả</span>' !!}
                                                </td> 
                                                <td>{{ $method->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if ($method->is_active)
                                                        <span class="badge bg-success">Đang hoạt động</span>
                                                    @else
                                                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group gap-2">
                                                        <a href="{{ route('admin.payment-methods.edit', $method) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                            <i class="ri-edit-2-line"></i>
                                                        </a>
                                                        <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa phương thức thanh toán này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa tạm thời">
                                                                <i class="ri-delete-bin-fill"></i>
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
                                        <strong>{{ $paymentMethods->total() }}</strong> phương thức thanh toán
                                    </div>
                                    <div>
                                        {{ $paymentMethods->withQueryString()->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @endif
                        </div> <!-- table-card -->
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div>
        </div>
    </div>
@endsection
