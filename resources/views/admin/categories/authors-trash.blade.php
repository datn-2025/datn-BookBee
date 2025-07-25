@extends('layouts.backend')

@section('title', 'Thùng Rác - Tác Giả')

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
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Thùng Rác - Tác Giả</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.categories.authors.index') }}">Quản lý tác giả</a>
                            </li>
                            <li class="breadcrumb-item active">Thùng rác</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h4 class="card-title mb-0">Danh Sách Tác Giả Đã Xóa</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.categories.authors.index') }}" class="btn btn-primary btn-sm">
                                <i class="las la-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>

                    <!-- Thanh tìm kiếm tác giả đã xóa -->
                    <div class="row mb-4 mt-2">
                        <div class="d-flex justify-content-sm-end">
                            <form action="{{ route('admin.categories.authors.trash') }}" method="GET" class="d-flex gap-2">
                                <div class="col-auto">
                                    <input type="text" name="search_name" class="form-control"
                                        placeholder="Tìm kiếm theo tên tác giả đã xóa" value="{{ $searchName }}">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                    <a href="{{ route('admin.categories.authors.trash') }}" class="btn btn-secondary">Đặt lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

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
                                        <th scope="col">Ngày Xóa</th>
                                        <th scope="col">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deletedAuthors as $key => $author)
                                        <tr>
                                            <td>{{ $deletedAuthors->firstItem() + $key }}</td>
                                            <td>
                                                <img src="{{ $author->image }}" alt="{{ $author->name }}" class="rounded"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td>{{ $author->name }}</td>
                                            <td>{{ $author->books_count }} cuốn</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 300px;">
                                                    {{ $author->biography }}
                                                </span>
                                            </td>
                                            <td>{{ $author->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <form action="{{ route('admin.categories.authors.restore', $author->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success" title="Khôi phục" onclick="return confirm('Khôi phục tác giả này?')">
                                                            <i class="ri-reply-line align-bottom me-1"></i> 
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.categories.authors.force-delete', $author->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa vĩnh viễn" onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn tác giả này? Hành động này không thể hoàn tác.')">
                                                            <i class="ri-delete-bin-2-line align-bottom me-1"></i> 
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Không có tác giả nào trong thùng rác</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $deletedAuthors->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
