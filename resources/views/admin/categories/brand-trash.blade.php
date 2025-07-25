@extends('layouts.backend')
@section('title', 'Thùng rác Thương Hiệu')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Thùng rác Thương Hiệu</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Quản lý</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.categories.brands.brand') }}">Thương Hiệu</a></li>
                        <li class="breadcrumb-item active">Thùng rác</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 col-md-6 ms-auto">
            <form action="" method="GET">
                <div class="input-group">
                    <input type="text" name="search_name" class="form-control" placeholder="Tìm kiếm theo tên thương hiệu" value="{{ request('search_name') }}">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    <a href="{{ route('admin.categories.brands.trash') }}" class="btn btn-secondary">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
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
                            <th scope="col">Thao tác</th>
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
                                        <img src="{{ asset($brand->image) }}" alt="{{ $brand->name }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                    @else
                                        <span class="text-muted">Không có ảnh</span>
                                    @endif
                                </td>
                                <td>{{ $brand->books_count ?? 0 }}</td>
                                <td>
                                    <div class="btn-group">
                                        <form action="{{ route('admin.categories.brands.restore', $brand->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success" title="Khôi phục" onclick="return confirm('Khôi phục thương hiệu này?')">
                                                <i class="ri-reply-line align-bottom me-1"></i> 
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.categories.brands.force-delete', $brand->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa vĩnh viễn" onclick="return confirm('Xóa vĩnh viễn thương hiệu này?')">
                                                <i class="ri-delete-bin-2-line align-bottom me-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Không có thương hiệu nào trong thùng rác</td>
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
@endsection
