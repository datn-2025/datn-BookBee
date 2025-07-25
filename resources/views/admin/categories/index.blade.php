@extends('layouts.backend')

@section('title', 'Quản lý danh mục sách')

@section('content')
    <div class="container-fluid">
        <!-- Tiêu đề trang -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quản lý danh mục</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Quản lý</a></li>
                            <li class="breadcrumb-item active">Danh mục loại sách</li>
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
                        <h4 class="card-title mb-0">Quản lý danh mục loại sách</h4>
                    </div>

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <!-- Thanh công cụ -->
                        <div class="row g-4 mb-3">
                            <div class="col-md-6 d-flex align-items-center gap-2">
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-sm">
                                    <i class="ri-add-line me-1"></i> Thêm danh mục
                                </a>
                                <a href="{{ route('admin.categories.trash') }}" class="btn btn-danger btn-sm px-4">
                                    <i class="ri-delete-bin-line me-1"></i> Thùng rác
                                    @if ($trashCount)
                                        <span class="badge bg-light text-danger ms-1">{{ $trashCount }}</span>
                                    @endif
                                </a>
                            </div>

                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.categories.index') }}"
                                    class="d-flex justify-content-md-end align-items-center gap-2">
                                    <input type="text" name="search_name_category" class="form-control"
                                        placeholder="Tìm theo tên danh mục" value="{{ $searchName ?? '' }}"
                                        style="width: 220px;">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="ri-search-2-line"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">
                                        <i class="ri-refresh-line"></i> Làm mới
                                    </a>
                                </form>
                            </div>
                        </div>

                        <!-- Bảng danh mục -->
                        <div class="table-responsive table-card mt-3 mb-1">
                            @if ($categories->isEmpty())
                                <div class="noresult text-center py-5">
                                    @if (filled(request()->get('search_name_category')))
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                        </lord-icon>
                                        <h5 class="mt-3 text-danger">Không tìm thấy danh mục phù hợp</h5>
                                        <p class="text-muted">
                                            Không có danh mục nào khớp với từ khóa
                                            <strong>"{{ request()->get('search_name_category') }}"</strong>.<br>
                                            Vui lòng kiểm tra lại từ khóa hoặc thử tìm kiếm khác.
                                        </p>
                                    @else
                                        <lord-icon src="https://cdn.lordicon.com/nocovwne.json" trigger="loop"
                                            colors="primary:#405189,secondary:#0ab39c" style="width:100px;height:100px">
                                        </lord-icon>
                                        <h5 class="mt-3 text-muted">Danh sách danh mục loại sách hiện đang trống</h5>
                                        <p class="text-muted">Hãy nhấn <strong>“Thêm danh mục”</strong> để bắt đầu quản lý
                                            danh mục loại sách.</p>
                                    @endif
                                </div>
                            @else
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên danh mục</th>
                                            <th>Ảnh</th>
                                            <th class="text-center">Số lượng sách</th>
                                            <th>Ngày tạo</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $key => $category)
                                            <tr>
                                                <td>{{ $categories->firstItem() + $key }}</td>
                                                <td>{{ $category->name }}</td>
                                                <td>
                                                    @if ($category->image)
                                                        <img src="{{ asset('storage/' . $category->image) }}"
                                                            alt="{{ $category->name }}" width="50"
                                                            class="rounded object-fit-cover">
                                                    @else
                                                        <span class="text-muted">Không có ảnh</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $category->books_count }} quyển</td>
                                                <td>{{ $category->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="btn-group gap-2">
                                                        <a href="{{ route('admin.categories.edit', $category->slug) }}"
                                                            class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                            <i class="ri-edit-2-line"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.categories.destroy', $category->slug) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('{{ $category->books_count > 0 ? 'Danh mục này đang chứa sách!! Bạn có chắc muốn xoá tạm thời không?' : 'Bạn có chắc muốn xoá tạm thời danh mục này?' }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Xóa tạm thời">
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
                                        Hiển thị <strong>{{ $categories->firstItem() }}</strong> đến
                                        <strong>{{ $categories->lastItem() }}</strong> trong tổng số
                                        <strong>{{ $categories->total() }}</strong> danh mục
                                    </div>
                                    <div>
                                        {{ $categories->links('pagination::bootstrap-4') }}
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
