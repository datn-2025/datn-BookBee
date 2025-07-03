@extends('layouts.backend')

@section('title', 'Chỉnh Sửa Tin Tức')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Chỉnh Sửa Tin Tức</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Tin tức</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.news.update', $article) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $article->title) }}" >
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="summary" class="form-label">Tóm tắt <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('summary') is-invalid @enderror" 
                                              id="summary" name="summary" rows="3" >{{ old('summary', $article->summary) }}</textarea>
                                    @error('summary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content">{{ old('content', $article->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" >
                                        <option value="">Chọn danh mục</option>
                                        <option value="Sách" {{ old('category', $article->category) == 'Sách' ? 'selected' : '' }}>Sách</option>
                                        <option value="Kinh doanh" {{ old('category', $article->category) == 'Kinh doanh' ? 'selected' : '' }}>Kinh doanh</option>
                                        <option value="Sức khỏe" {{ old('category', $article->category) == 'Sức khỏe' ? 'selected' : '' }}>Sức khỏe</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                           id="thumbnail" name="thumbnail" accept="image/*">
                                    <div class="mt-2">
                                        <img id="thumbnail-preview" 
                                             src="{{ asset('storage/' . $article->thumbnail) }}" 
                                             alt="{{ $article->title }}" 
                                             style="max-width: 100%;">
                                    </div>
                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_featured" value="0">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_featured" name="is_featured" value="1"
                                               {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Bài viết nổi bật</label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line align-bottom me-1"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('admin.news.index') }}" class="btn btn-light">
                                        <i class="ri-arrow-go-back-line align-bottom me-1"></i> Quay lại
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
