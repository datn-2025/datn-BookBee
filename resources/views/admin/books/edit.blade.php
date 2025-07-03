@extends('layouts.backend')

@section('title', 'Sửa sách')

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
                            <li class="breadcrumb-item active" aria-current="page">Sửa sách</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <form action="{{ route('admin.books.update', [$book->id, $book->slug]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                                        <input type="text" name="title" class="form-control" id="title"
                                            value="{{ old('title', $book->title) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" id="description" class="form-control"
                                            rows="4">{{ old('description', $book->description) }}</textarea>
                                        <small class="text-muted">Mô tả chi tiết về nội dung sách</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="isbn" class="form-label">ISBN</label>
                                        <input type="text" name="isbn" class="form-control" id="isbn"
                                            value="{{ old('isbn', $book->isbn) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="page_count" class="form-label">Số trang</label>
                                        <input type="number" name="page_count" class="form-control" id="page_count"
                                            value="{{ old('page_count', $book->page_count) }}">
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
                                        name="has_physical" value="1" {{ $physicalFormat ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_physical">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="physical_format" style="{{ $physicalFormat ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                            <input type="number" name="formats[physical][price]"
                                                class="form-control physical-field" min="0" step="1000"
                                                value="{{ old('formats.physical.price', $physicalFormat ? $physicalFormat->price : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giảm giá (%)</label>
                                            <input type="number" name="formats[physical][discount]"
                                                class="form-control physical-field" min="0" max="100"
                                                value="{{ old('formats.physical.discount', $physicalFormat ? $physicalFormat->discount : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="formats[physical][stock]"
                                                class="form-control physical-field" min="0"
                                                value="{{ old('formats.physical.stock', $physicalFormat ? $physicalFormat->stock : '') }}">
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
                                        value="1" {{ $ebookFormat ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_ebook">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="ebook_format" style="{{ $ebookFormat ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                            <input type="number" name="formats[ebook][price]"
                                                class="form-control ebook-field" min="0" step="1000"
                                                value="{{ old('formats.ebook.price', $ebookFormat ? $ebookFormat->price : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giảm giá (%)</label>
                                            <input type="number" name="formats[ebook][discount]"
                                                class="form-control ebook-field" min="0" max="100"
                                                value="{{ old('formats.ebook.discount', $ebookFormat ? $ebookFormat->discount : '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File Ebook</label>
                                    <input type="file" name="formats[ebook][file]" class="form-control ebook-field"
                                        accept=".pdf,.epub">
                                    @if($ebookFormat && $ebookFormat->file_url)
                                    <div class="mt-2">
                                        <span class="badge bg-success">
                                            <i class="ri-file-pdf-line me-1"></i> File hiện tại: {{ basename($ebookFormat->file_url) }}
                                        </span>
                                        <small class="text-muted d-block mt-1">Tải lên file mới để thay thế file hiện tại</small>
                                    </div>
                                    @endif
                                    <small class="text-muted">Hỗ trợ định dạng PDF, EPUB</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="allow_sample_read"
                                        name="formats[ebook][allow_sample_read]" value="1"
                                        {{ $ebookFormat && $ebookFormat->allow_sample_read ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_sample_read">
                                        Cho phép đọc thử
                                    </label>
                                </div>

                                <div class="mb-3" id="sample_file_container" style="{{ ($ebookFormat && $ebookFormat->allow_sample_read) ? '' : 'display: none;' }}">
                                    <label class="form-label">File xem thử</label>
                                    <input type="file" name="formats[ebook][sample_file]"
                                        class="form-control ebook-field" accept=".pdf,.epub">
                                    @if($ebookFormat && $ebookFormat->sample_file_url)
                                    <div class="mt-2">
                                        <span class="badge bg-info">
                                            <i class="ri-file-pdf-line me-1"></i> File hiện tại: {{ basename($ebookFormat->sample_file_url) }}
                                        </span>
                                        <small class="text-muted d-block mt-1">Tải lên file mới để thay thế file hiện tại</small>
                                    </div>
                                    @endif
                                    <small class="text-muted">File xem thử cho khách hàng</small>
                                </div>
                            </div>
                        </div>

                        @error('format')
                            <div class="alert alert-danger mb-3">
                                {{ $message }}
                            </div>
                        @enderror
                        <div id="format_validation_message" class="alert alert-info mb-3" style="display: none;">
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
                                            <div class="selected-values mt-2">
                                                @foreach($book->attributeValues as $attributeValue)
                                                    @if($attributeValue->attribute_id == $attribute->id)
                                                    <div class="selected-value mb-2">
                                                        <span class="badge bg-light text-dark p-2 d-flex align-items-center">
                                                            <span>{{ $attributeValue->value }}</span>
                                                            <span class="ms-2 text-primary">+{{ number_format($attributeValue->pivot->extra_price) }}đ</span>
                                                            <button type="button" class="btn-close ms-2 remove-attribute-value"></button>
                                                            <input type="hidden" name="attribute_values[{{ $attributeValue->id }}][id]" value="{{ $attributeValue->id }}">
                                                            <input type="hidden" name="attribute_values[{{ $attributeValue->id }}][extra_price]" value="{{ $attributeValue->pivot->extra_price }}">
                                                        </span>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
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
                                        <option value="Còn Hàng" {{ $book->status == 'Còn Hàng' ? 'selected' : '' }}>Còn Hàng</option>
                                        <option value="Hết Hàng Tồn Kho" {{ $book->status == 'Hết Hàng Tồn Kho' ? 'selected' : '' }}>Hết Hàng Tồn Kho</option>
                                        <option value="Sắp Ra Mắt" {{ $book->status == 'Sắp Ra Mắt' ? 'selected' : '' }}>Sắp Ra Mắt</option>
                                        <option value="Ngừng Kinh Doanh" {{ $book->status == 'Ngừng Kinh Doanh' ? 'selected' : '' }}>Ngừng Kinh Doanh</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Danh mục <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="author_id" class="form-label">Tác giả <span
                                            class="text-danger">*</span></label>
                                    <select name="author_id" id="author_id" class="form-select" required>
                                        <option value="">-- Chọn tác giả --</option>
                                        @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="brand_id" class="form-label">Thương hiệu <span
                                            class="text-danger">*</span></label>
                                    <select name="brand_id" id="brand_id" class="form-select" required>
                                        <option value="">-- Chọn thương hiệu --</option>
                                        @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $book->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="publication_date" class="form-label">Ngày xuất bản</label>
                                    <input type="date" name="publication_date" class="form-control"
                                        id="publication_date" value="{{ old('publication_date', $book->publication_date ? $book->publication_date->format('Y-m-d') : '') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="release_date" class="form-label">Ngày ra mắt</label>
                                    <input type="date" name="release_date" class="form-control"
                                        id="release_date" value="{{ old('release_date', $book->release_date ? $book->release_date->format('Y-m-d') : '') }}">
                                    <small class="text-muted">Ngày sách chính thức ra mắt thị trường</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hình ảnh -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Hình ảnh bìa</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="cover_image" class="form-label">Ảnh bìa</label>
                                    <input type="file" name="cover_image" class="form-control" id="cover_image"
                                        accept="image/*">
                                    @if($book->cover_image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" 
                                            class="img-thumbnail" style="max-height: 200px;">
                                        <small class="text-muted d-block mt-1">Tải lên ảnh mới để thay thế ảnh hiện tại</small>
                                    </div>
                                    @else
                                    <div id="cover_preview" class="mt-2">
                                        <div class="preview-container" style="max-width: 200px;"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Thư viện ảnh -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Thư viện ảnh</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Thêm ảnh mới</label>
                                    <input type="file" name="images[]" class="form-control" id="images" multiple
                                        accept="image/*">
                                    <div class="mt-2">
                                        <div id="images_preview" class="row g-2"></div>
                                    </div>
                                </div>

                                @if($book->images->count() > 0)
                                <div class="mt-3">
                                    <h6 class="mb-2">Ảnh hiện tại</h6>
                                    <div class="row g-2">
                                        @foreach($book->images as $image)
                                        <div class="col-md-6 col-sm-6 col-6 mb-2">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image->image_url) }}" alt="Ảnh sách" 
                                                    class="img-thumbnail" style="height: 100px; object-fit: cover; width: 100%;">
                                                <div class="form-check position-absolute" style="top: 5px; right: 5px;">
                                                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $image->id }}" id="delete_image_{{ $image->id }}">
                                                    <label class="form-check-label" for="delete_image_{{ $image->id }}">
                                                        <span class="badge bg-danger">Xóa</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">Đánh dấu vào ô để xóa ảnh</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Nút submit -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="ri-save-line align-bottom me-1"></i> Cập nhật sách
                                </button>
                                <a href="{{ route('admin.books.show', [$book->id, $book->slug]) }}" class="btn btn-light w-100">
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

