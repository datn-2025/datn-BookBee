@extends('layouts.backend')

@section('title', 'Chi Tiết Tin Tức')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Chi Tiết Tin Tức</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Tin tức</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <!-- Nội dung chính -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <!-- Ảnh đại diện -->
                    <div class="text-center mb-4">
                        @if($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                alt="Ảnh đại diện"
                                class="img-fluid rounded"
                                style="max-height: 400px;">
                        @else
                            <div class="text-muted">Không có ảnh đại diện</div>
                        @endif
                    </div>

                    <!-- Tiêu đề -->
                    <h1 class="mb-3 fs-4">
                        <span class="text-muted">Tiêu đề:</span>
                        {{ $article->title }}
                    </h1>

                    <!-- Thông tin -->
                    <div class="d-flex gap-3 mb-4 text-muted flex-wrap">
                        <div>
                            <i class="ri-calendar-line align-bottom me-1"></i>
                            <strong>Ngày tạo:</strong> {{ $article->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <i class="ri-price-tag-3-line align-bottom me-1"></i>
                            <strong>Danh mục:</strong> {{ $article->category }}
                        </div>
                    </div>

                    <!-- Tóm tắt -->
                    <div class="mb-4">
                        <h5 class="text-muted">Tóm tắt:</h5>
                        <p class="lead mb-0">{{ $article->summary }}</p>
                    </div>

                    <!-- Nội dung -->
                    <div>
                        <h5 class="text-muted mb-2">Nội dung chi tiết:</h5>
                        <div class="article-content">
                            {!! $article->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột bên phải -->
        <div class="col-lg-4">
            <!-- Thông tin chi tiết -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin bài viết</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="ri-hashtag"></i> Mã bài viết (ID):</h6>
                        <p class="text-muted mb-0">{{ $article->id }}</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="ri-time-line"></i> Ngày tạo:</h6>
                        <p class="text-muted mb-0">{{ $article->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="ri-history-line"></i> Cập nhật lần cuối:</h6>
                        <p class="text-muted mb-0">{{ $article->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <h6><i class="ri-star-line"></i> Trạng thái:</h6>
                        @if($article->is_featured)
                            <span class="badge bg-success">Bài viết nổi bật</span>
                        @else
                            <span class="badge bg-secondary">Bài viết thường</span>
                        @endif
                    </div>
                </div>
            </div>

              <!-- Thao tác -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thao tác</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-warning w-100 mb-2">
                        <i class="ri-pencil-line align-bottom me-1"></i> Chỉnh sửa
                    </a>
                    <form action="{{ route('admin.news.destroy', $article) }}"
                          method="POST"
                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin tức này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="ri-delete-bin-line align-bottom me-1"></i> Xóa
                        </button>
                    </form>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-light w-100 mt-2">
                        <i class="ri-arrow-go-back-line align-bottom me-1"></i> Quay lại danh sách
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
