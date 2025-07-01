@extends('layouts.backend')

@section('title' , 'Quản lý tin tức')

@section('content')

    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý tin tức</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">admin</a></li>
                        <li class="breadcrumb-item active">Tin tức</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End page title -->


    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách tin tức</h5>
                        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i> Thêm tin tức
                        </a>
                    </div>

                    <!-- Bộ lọc tin tức -->
                    <!-- Bộ lọc tin tức -->
                    <div class="card-body border-bottom py-4">
                        <form method="GET" action="{{ route('admin.news.index') }}">
                            <div class="row g-3 align-items-center">
                                <!-- Ô tìm kiếm -->
                                <div class="col-lg-5">
                                    <input type="text" name="search" class="form-control ps-4"
                                        placeholder="🔍 Tìm kiếm tin tức..." value="{{ request('search') }}">
                                </div>

                                <!-- Danh mục -->
                                <div class="col-lg-auto" style="min-width: 190px;">
                                    <select class="form-select" name="category">
                                        <option value="">📁 Tất cả danh mục</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Trạng thái -->
                                <div class="col-lg-auto" style="min-width: 170px;">
                                    <select class="form-select" name="is_featured">
                                        <option value="">✨ Tất cả trạng thái</option>
                                        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Bài nổi bật
                                        </option>
                                        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Bài thường
                                        </option>
                                    </select>
                                </div>

                                <!-- Nút lọc + đặt lại -->
                                <div class="col-lg-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4" style="min-width: 130px;">
                                        <i class="ri-filter-3-line me-1"></i> Lọc
                                    </button>
                                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary px-4"
                                        style="min-width: 130px;">
                                        <i class="ri-refresh-line me-1"></i> Đặt lại
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>



                    <div class="card-body">
                        <div class="table-responsive table-card mb-4">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-muted">
                                        <th scope="col" style="width: 50px;">STT</th>
                                        <th scope="col">Ảnh</th>
                                        <th scope="col" style="width: 30%">Tiêu đề</th>
                                        <th scope="col">Danh mục</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col" style="width: 150px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($articles as $index => $article)
                                        <tr>
                                            <td class="text-center">{{ $articles->firstItem() + $index }}</td>
                                            <td>
                                                @if ($article->thumbnail)
                                                    <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                                        alt="{{ $article->title }}"
                                                        class="avatar-sm rounded object-fit-cover"
                                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=No+Image';">
                                                @else
                                                    <span class="text-muted">Không có ảnh</span>
                                                @endif
                                            </td>


                                            <td style="max-width: 250px; white-space: normal; word-break: break-word;">
                                                <h6 class="mb-0">{{ $article->title }}</h6>
                                            </td>
                                            <td>{{ $article->category }}</td>
                                            <td>
                                                @if($article->is_featured)
                                                    <span class="badge bg-success">Bài nổi bật</span>
                                                @else
                                                    <span class="badge bg-secondary">Bài thường</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.news.show', $article->id) }}"
                                                        class="btn btn-sm btn-info" title="Xem">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a href="{{ route('admin.news.edit', $article->id) }}"
                                                        class="btn btn-sm btn-warning" title="Sửa">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                    <form action="{{ route('admin.news.destroy', $article->id) }}" method="POST"
                                                        class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Bạn có chắc muốn xóa tin tức này?')"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                Không có tin tức nào.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <nav>
                                @if ($articles->hasPages())
                                    <ul class="pagination mb-0">
                                        {{-- Previous Page Link --}}
                                        @if ($articles->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">Prev</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $articles->previousPageUrl() }}" rel="prev">Prev</a>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                                            @if ($page == $articles->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($articles->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $articles->nextPageUrl() }}" rel="next">Next</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">Next</span>
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection