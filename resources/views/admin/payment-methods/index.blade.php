@extends('layouts.backend')

@section('title', 'Quản Lý Phương Thức Thanh Toán')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Quản Lý Thanh Toán</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="">Quản lý</a></li>
                            <li class="breadcrumb-item active">Phương Thức Thanh Toán</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title --> 
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h4 class="card-title mb-0">Danh Sách Phương Thức Thanh Toán</h4>
                        <div class="d-flex align-items-center gap-2">
                            @permission('payment-method.create')
                            <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-success btn-sm">
                                <i class="ri-add-line me-1"></i> Thêm mới
                            </a>
                            @endpermission
                            @permission('payment-method.trash')
                            <a href="{{ route('admin.payment-methods.trash') }}" class="btn btn-danger btn-sm">
                                <i class="ri-delete-bin-line me-1"></i> Thùng rác
                                @if($trashCount > 0)
                                    <span class="badge bg-light text-danger ms-1">{{ $trashCount }}</span>
                                @endif
                            </a>
                            @endpermission
                        </div>
                    </div>
                    <!-- Form tìm kiếm -->
                    <div class="row mb-4">
                        <div class="d-flex justify-content-end">
                            <form action="{{ route('admin.payment-methods.index') }}" method="GET" class="d-flex gap-2">
                                <div class="col-auto">
                                    <input type="text" name="search" class="form-control form-control-sm" 
                                           placeholder="Tìm kiếm theo tên" value="{{ request('search') }}">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="ri-search-line"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary btn-sm">
                                        Đặt lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Kết thúc Form tìm kiếm -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Tên Phương Thức</th>
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Ngày Tạo</th>
                                        <th scope="col">Trạng Thái</th>
                                        <th scope="col">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paymentMethods as $key => $method)
                                        <tr>
                                            <td>{{ $paymentMethods->firstItem() + $key }}</td>
                                            <td>{{ $method->name }}</td>
                                            <td class="text-truncate" style="max-width: 300px;" title="{{ $method->description }}">
                                                <!-- {{ $method->description ?: 'Không có mô tả' }} -->
                                                {!! $method->description ?: '<span class="text-muted">Không có mô tả</span>' !!}
                                            </td>
                                            <td>{{ $method->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($method->is_active)
                                                    <span class="badge bg-success">Đang hoạt động</span>
                                                @else
                                                    <span class="badge bg-secondary">Ngừng hoạt động</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    @permission('payment-method.edit')
                                                    <a href="{{ route('admin.payment-methods.edit', $method) }}" 
                                                       class="btn btn-sm btn-light" title="Chỉnh sửa">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                    </a>
                                                    @endpermission
                                                    @permission('payment-method.delete')
                                                    <form action="{{ route('admin.payment-methods.destroy', $method) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm"
                                                                onclick="return confirm('Bạn có chắc muốn xóa phương thức thanh toán này?')"
                                                                title="Xóa">
                                                            <i class="ri-delete-bin-fill align-bottom me-2"></i>
                                                        </button>
                                                    </form>
                                                    @endpermission
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Không có phương thức thanh toán nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $paymentMethods->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection