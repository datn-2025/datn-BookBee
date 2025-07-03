@extends('layouts.backend')

@section('title', 'Thêm sách mới')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Breadcrumb -->
            <div class="card mb-3">
                <div class="card-body py-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.books.index') }}">Sách</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Thông tin cơ bản -->
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                            </div>
                            <div class="card-body">

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="title" class="form-label">Tiêu đề sách <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="title"
                                            value="{{ old('title') }}" >
                                        @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" id="description" class="form-control"
                                            id="description" rows="4">{{ old('description') }}</textarea>
                                        <small class="text-muted">Mô tả chi tiết về nội dung sách</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="isbn" class="form-label">ISBN</label>
                                        <input type="text" name="isbn" class="form-control" id="isbn"
                                            value="{{ old('isbn') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="page_count" class="form-label">Số trang</label>
                                        <input type="number" name="page_count" class="form-control" id="page_count"
                                            value="{{ old('page_count') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Định dạng sách vật lý -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Sách vật lý</h5>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="has_physical"
                                        name="has_physical" value="1">
                                    <label class="form-check-label" for="has_physical">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="physical_format" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                            <input type="number" name="formats[physical][price]"
                                                class="form-control physical-field @error('formats.physical.price') is-invalid @enderror" min="0" step="1000" value="{{ old('formats.physical.price') }}">
                                            @error('formats.physical.price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giảm giá (%)</label>
                                            <input type="number" name="formats[physical][discount]"
                                                class="form-control physical-field" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="formats[physical][stock]"
                                                class="form-control physical-field @error('formats.physical.stock') is-invalid @enderror" min="0" value="{{ old('formats.physical.stock') }}">
                                            @error('formats.physical.stock')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Định dạng Ebook -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Ebook</h5>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="has_ebook" name="has_ebook"
                                        value="1">
                                    <label class="form-check-label" for="has_ebook">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="ebook_format" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                            <input type="number" name="formats[ebook][price]"
                                                class="form-control ebook-field @error('formats.ebook.price') is-invalid @enderror" min="0" step="1000" value="{{ old('formats.ebook.price') }}">
                                            @error('formats.ebook.price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giảm giá (%)</label>
                                            <input type="number" name="formats[ebook][discount]"
                                                class="form-control ebook-field" min="0" max="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File Ebook <span class="text-danger">*</span></label>
                                    <input type="file" name="formats[ebook][file]" class="form-control ebook-field @error('formats.ebook.file') is-invalid @enderror"
                                        accept=".pdf,.epub">
                                    @error('formats.ebook.file')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="text-muted">Hỗ trợ định dạng PDF, EPUB</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="allow_sample_read"
                                        name="formats[ebook][allow_sample_read]" value="1">
                                    <label class="form-check-label" for="allow_sample_read">
                                        Cho phép đọc thử
                                    </label>
                                </div>

                                <div class="mb-3" id="sample_file_container" style="display: none;">
                                    <label class="form-label">File xem thử</label>
                                    <input type="file" name="formats[ebook][sample_file]"
                                        class="form-control ebook-field" accept=".pdf,.epub">
                                    <small class="text-muted">File xem thử cho khách hàng</small>
                                </div>
                            </div>
                        </div>

                        @error('format')
                            <div class="alert alert-danger mb-3">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="alert alert-info mb-3" id="format_validation_message" style="display: none;">
                            Vui lòng kích hoạt ít nhất một định dạng sách.
                        </div>
                        <!-- Thuộc tính sản phẩm -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Thuộc tính sản phẩm</h5>
                            </div>
                            <div class="card-body">

                                <div class="row g-3">
                                    @foreach($attributes as $attribute)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ $attribute->name }}</label>
                                        <div class="attribute-group">
                                            <div class="input-group mb-2">
                                                <select class="form-select attribute-select"
                                                    data-attribute-name="{{ $attribute->name }}"
                                                    data-attribute-id="{{ $attribute->id }}">
                                                    <option value="">-- Chọn {{ strtolower($attribute->name) }} --
                                                    </option>
                                                    @foreach($attribute->values as $value)
                                                    <option value="{{ $value->id }}"
                                                        data-value-name="{{ $value->value }}">{{ $value->value }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <input type="number" class="form-control attribute-extra-price"
                                                    placeholder="Giá thêm" min="0" value="0">
                                                <button type="button"
                                                    class="btn btn-primary add-attribute-value">Thêm</button>
                                            </div>
                                            <div class="selected-values mt-2"></div>
                                            <!-- Hidden inputs will be added here by JavaScript -->
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Trạng thái và phân loại -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Trạng thái & Phân loại</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="Còn Hàng">Còn Hàng</option>
                                        <option value="Hết Hàng Tồn Kho">Hết Hàng Tồn Kho</option>
                                        <option value="Sắp Ra Mắt">Sắp Ra Mắt</option>
                                        <option value="Ngừng Kinh Doanh">Ngừng Kinh Doanh</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Danh mục <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" >
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id')==$category->id ?
                                            'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="author_id" class="form-label">Tác giả <span
                                            class="text-danger">*</span></label>
                                    <select name="author_id" id="author_id" class="form-select @error('author_id') is-invalid @enderror" >
                                        <option value="">-- Chọn tác giả --</option>
                                        @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id')==$author->id ? 'selected'
                                            : '' }}>{{ $author->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('author_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="brand_id" class="form-label">Thương hiệu <span
                                            class="text-danger">*</span></label>
                                    <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror" >
                                        <option value="">-- Chọn thương hiệu --</option>
                                        @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id')==$brand->id ? 'selected' :
                                            '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="publication_date" class="form-label">Ngày xuất bản</label>
                                    <input type="date" name="publication_date" class="form-control"
                                        id="publication_date" value="{{ old('publication_date') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="release_date" class="form-label">Ngày ra mắt</label>
                                    <input type="date" name="release_date" class="form-control"
                                        id="release_date" value="{{ old('release_date') }}">
                                    <small class="text-muted">Ngày sách chính thức ra mắt thị trường</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hình ảnh -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Hình ảnh</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="cover_image" class="form-label">Ảnh bìa <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image"
                                        accept="image/*" >
                                    @error('cover_image')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div id="cover_preview" class="mt-2">
                                        <div class="preview-container" style="max-width: 200px;"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="images" class="form-label">Ảnh phụ</label>
                                    <input type="file" name="images[]" class="form-control" id="images" multiple
                                        accept="image/*">
                                    <div class="mt-2">
                                        <div id="images_preview" class="row g-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút submit -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="ri-save-line align-bottom me-1"></i> Lưu sách
                                </button>
                                <a href="{{ route('admin.books.index') }}" class="btn btn-light w-100">
                                    <i class="ri-arrow-left-line align-bottom me-1"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection