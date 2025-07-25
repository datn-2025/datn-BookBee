@extends('layouts.backend')

@section('title', 'Quản Lý Tác Giả')

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
                    <h4 class="mb-sm-0">Quản Lý Danh Mục</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="">Quản lý</a></li>
                            <li class="breadcrumb-item active">Tác Giả</li>
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
                        <h4 class="card-title mb-0">Danh Sách Tác Giả</h4>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('admin.categories.authors.create') }}" class="btn btn-success btn-sm">
                                <i class="ri-add-line me-1"></i> Thêm tác giả
                            </a>
                            <a href="{{ route('admin.categories.authors.trash') }}" class="btn btn-outline-danger">
                               <i class="ri-delete-bin-2-line align-bottom me-1"></i> Thùng rác
                                @if($trashCount > 0)
                                    <span class="badge bg-light text-danger ms-1">{{ $trashCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <!-- Bắt đầu Form tìm kiếm -->
                    <div class="row mb-4">
                        <div class="d-flex justify-content-sm-end">
                            <form action="{{ route('admin.categories.authors.index') }}" method="GET" class="d-flex gap-2">
                                <div class="col-auto">
                                    <input type="text" name="search_name" class="form-control" placeholder="Tìm kiếm theo tên"
                                        value="{{ $searchName }}">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                    <a href="{{ route('admin.categories.authors.index') }}" class="btn btn-secondary">Đặt lại</a>
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
                                        <th scope="col">Hình Ảnh</th>
                                        <th scope="col">Tên Tác Giả</th>
                                        <th scope="col">Số Lượng Sách</th>
                                        <th scope="col">Tiểu Sử</th>
                                        <th scope="col">Trạng Thái</th>
                                        <th scope="col">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($authors as $key => $author)
                                        <tr>
                                            <td>{{ $authors->firstItem() + $key }}</td>
                                            <td>
                                                <img src="{{ $author->image ? asset($author->image) : asset('images/default-author.png') }}" alt="{{ $author->name }}" class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td>{{ $author->name }}</td>
                                            <td>{{ $author->books_count }} cuốn</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 300px;">
                                                    {{ $author->biography }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($author->deleted_at)
                                                    <span class="badge bg-danger">Đã xóa</span>
                                                @else
                                                    <span class="badge bg-success">Đang hoạt động</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    @if ($author->deleted_at)
                                                        <form
                                                            action="{{ route('admin.categories.authors.restore', $author->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                title="Khôi phục">
                                                                <i class="las la-undo"></i>
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('admin.categories.authors.force-delete', $author->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn tác giả này? Nếu tác giả có sách, bạn sẽ không thể xóa vĩnh viễn.')"
                                                                title="Xóa vĩnh viễn">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('admin.categories.authors.edit', $author->id) }}" class="btn btn-sm btn-light" title="Chỉnh sửa">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.categories.authors.destroy', $author->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm"
                                                                onclick="return confirm('Bạn có chắc muốn xóa tạm thời tác giả này?')"
                                                                title="Xóa tạm thời">
                                                                <i class="ri-delete-bin-fill align-bottom me-2"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Không có tác giả nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $authors->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
