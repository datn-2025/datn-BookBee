@extends('layouts.backend')

@section('title', 'Thùng Rác - Danh mục loại sách')

@section('content')
    <div class="container-fluid">
        <!-- Tiêu đề -->
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Thùng rác - Danh mục loại sách</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                    <li class="breadcrumb-item active">Thùng rác</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <!-- Header -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách danh mục đã xoá</h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-primary btn-sm">
                    <i class="ri-arrow-go-back-line me-1"></i> Quay lại
                </a>
            </div>

            <div class="card-body">
                <!-- Thông báo -->
                @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
                    @if (session($key))
                        <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                            {{ session($key) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endforeach

                <!-- Tìm kiếm -->
                <form method="GET" action="{{ route('admin.categories.trash') }}"
                    class="d-flex justify-content-end gap-2 mb-3">
                    <input type="text" name="search_name_category" class="form-control"
                        placeholder="Tìm theo tên danh mục" value="{{ request('search_name_category') }}"
                        style="width: 220px;">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ri-search-2-line"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.categories.trash') }}" class="btn btn-outline-secondary px-4">
                        <i class="ri-refresh-line"></i> Làm mới
                    </a>
                </form>

                <!-- Danh sách -->
                @if ($deletedCategories->isEmpty())
                    <div class="text-center py-5">
                        @if (request()->filled('search_name_category'))
                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                style="width:75px;height:75px"></lord-icon>
                            <h5 class="mt-3 text-danger">Không tìm thấy danh mục</h5>
                            <p class="text-muted">Không có danh mục nào khớp với
                                <strong>"{{ request('search_name_category') }}"</strong>.
                            </p>
                        @else
                            <lord-icon src="https://cdn.lordicon.com/jmkrnisz.json" trigger="loop"
                                style="width:90px;height:90px"></lord-icon>
                            <h5 class="mt-3 text-muted">Thùng rác trống</h5>
                            <p class="text-muted">Không có danh mục nào bị xoá.</p>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên</th>
                                    <th>Ảnh</th>
                                    <th class="text-center">Số lượng sách</th>
                                    <th>Ngày xoá</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deletedCategories as $key => $category)
                                    <tr>
                                        <td>{{ $deletedCategories->firstItem() + $key }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            @if ($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                    alt="{{ $category->name }}" width="50" height="50"
                                                    class="rounded object-fit-cover">
                                            @else
                                                <span class="text-muted">Không có ảnh</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $category->books_count }}</td>
                                        <td>{{ $category->deleted_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <!-- Khôi phục -->
                                            <form action="{{ route('admin.categories.restore', $category->slug) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <button class="btn btn-sm btn-success" title="Khôi phục">
                                                    <i class="ri-reply-line"></i>
                                                </button>
                                            </form>

                                            <!-- Xoá vĩnh viễn -->
                                            <form action="{{ route('admin.categories.force-delete', $category->slug) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Xoá vĩnh viễn"
                                                    onclick="return confirm('Xoá vĩnh viễn danh mục này?')"
                                                    {{ $category->books_count > 0 ? 'disabled' : '' }}>
                                                    <i class="ri-delete-bin-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                        <small class="text-muted">
                            Hiển thị <strong>{{ $deletedCategories->firstItem() }}</strong> đến
                            <strong>{{ $deletedCategories->lastItem() }}</strong> trong tổng số
                            <strong>{{ $deletedCategories->total() }}</strong> danh mục
                        </small>
                        {{ $deletedCategories->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
