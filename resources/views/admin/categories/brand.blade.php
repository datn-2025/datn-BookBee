@extends('layouts.backend')
@section('title', 'Quản Lý Thương Hiệu')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Quản Lý Thương Hiệu</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Quản lý</a></li>
                            <li class="breadcrumb-item active">Thương Hiệu</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h4 class="card-title mb-0">Danh Sách Thương Hiệu</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.categories.brands.create') }}" class="btn btn-success">
                                <i class="ri-add-line align-bottom me-1"></i> Thêm thương hiệu
                            </a>
                            <a href="{{ route('admin.categories.brands.trash') }}" class="btn btn-outline-danger">
                                <i class="ri-delete-bin-2-line align-bottom me-1"></i> Thùng rác
                            </a>
                        </div>
                    </div>
                    <!-- Bắt đầu Form tìm kiếm -->
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 ms-auto">
                            <form action="" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search_name" class="form-control"
                                        placeholder="Tìm kiếm theo tên thương hiệu" value="{{ request('search_name') }}">
                                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                    <a href="{{ route('admin.categories.brands.brand') }}" class="btn btn-secondary">Đặt lại</a>
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
                                        <th scope="col">ID</th>
                                        <th scope="col">Tên Thương Hiệu</th>
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Ảnh/Logo</th>
                                        <th scope="col">Số Lượng Sách</th>
                                        <th scope="col">Thao tác </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($brands as $key => $brand)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $brand->name }}</td>
                                            <td>{{ $brand->description }}</td>
                                            <td>
                                                @if ($brand->image)
                                                    <img src="{{ asset($brand->image) }}" alt="{{ $brand->name }}"
                                                        style="width: 50px; height: 50px; object-fit: cover;"
                                                        class="rounded">
                                                @else
                                                    <span class="text-muted">Không có ảnh</span>
                                                @endif
                                            </td>
                                            <td>{{ $brand->books_count ?? 0 }}</td>
                                            <td>
                                                <div class="btn-group">

                                                    <a href="{{ route('admin.categories.brands.edit', $brand->id) }}" class="btn btn-sm btn-light" title="Chỉnh sửa">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                    </a>
                                                    <form action="{{ route('admin.categories.brands.destroy', $brand->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm"
                                                            onclick="return confirm('Bạn có chắc muốn xóa tạm thời thương hiệu này?')"
                                                            title="Xóa tạm thời">
                                                            <i class="ri-delete-bin-fill align-bottom me-2"></i>
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Không có thương hiệu nào</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $brands->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
