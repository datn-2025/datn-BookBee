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
                                            rows="4">{!! nl2br(e(old('description', $book->description))) !!}</textarea>
                                        <small class="text-muted">Mô tả chi tiết về nội dung sách</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                                        <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" id="isbn"
                                            value="{{ old('isbn', $book->isbn) }}">
                                        @error('isbn')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="page_count" class="form-label">Số trang <span class="text-danger">*</span></label>
                                        <input type="number" name="page_count" class="form-control @error('page_count') is-invalid @enderror" id="page_count"
                                            value="{{ old('page_count', $book->page_count) }}">
                                        @error('page_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                        name="has_physical" value="1" {{ old('has_physical', $physicalFormat ? 1 : 0) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_physical">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="physical_format" style="{{ old('has_physical', $physicalFormat ? 1 : 0) ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                            <input type="number" name="formats[physical][price]"
                                                class="form-control physical-field @error('formats.physical.price') is-invalid @enderror" min="0" step="1000"
                                                value="{{ old('formats.physical.price', $physicalFormat ? $physicalFormat->price : '') }}">
                                            @error('formats.physical.price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giảm giá (%)</label>
                                            <input type="number" name="formats[physical][discount]"
                                                class="form-control physical-field @error('formats.physical.discount') is-invalid @enderror" min="0" max="100"
                                                value="{{ old('formats.physical.discount', $physicalFormat ? $physicalFormat->discount : '') }}">
                                            @error('formats.physical.discount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                            <input type="number" name="formats[physical][stock]"
                                                class="form-control physical-field @error('formats.physical.stock') is-invalid @enderror" min="0"
                                                value="{{ old('formats.physical.stock', $physicalFormat ? $physicalFormat->stock : '') }}">
                                            @error('formats.physical.stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
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
                                        value="1" {{ old('has_ebook', $ebookFormat ? 1 : 0) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_ebook">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="ebook_format" style="{{ old('has_ebook', $ebookFormat ? 1 : 0) ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                            <input type="number" name="formats[ebook][price]"
                                                class="form-control ebook-field @error('formats.ebook.price') is-invalid @enderror" min="0" step="1000"
                                                value="{{ old('formats.ebook.price', $ebookFormat ? $ebookFormat->price : '') }}">
                                            @error('formats.ebook.price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giảm giá (%)</label>
                                            <input type="number" name="formats[ebook][discount]"
                                                class="form-control ebook-field @error('formats.ebook.discount') is-invalid @enderror" min="0" max="100"
                                                value="{{ old('formats.ebook.discount', $ebookFormat ? $ebookFormat->discount : '') }}">
                                            @error('formats.ebook.discount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File Ebook</label>
                                    <input type="file" name="formats[ebook][file]" class="form-control ebook-field @error('formats.ebook.file') is-invalid @enderror"
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
                                    @error('formats.ebook.file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="allow_sample_read"
                                        name="formats[ebook][allow_sample_read]" value="1"
                                        {{ old('formats.ebook.allow_sample_read', $ebookFormat && $ebookFormat->allow_sample_read ? 1 : 0) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_sample_read">
                                        Cho phép đọc thử
                                    </label>
                                </div>

                                <div class="mb-3" id="sample_file_container" style="{{ (old('formats.ebook.allow_sample_read', $ebookFormat && $ebookFormat->allow_sample_read ? 1 : 0)) ? '' : 'display: none;' }}">
                                    <label class="form-label">File xem thử</label>
                                    <input type="file" name="formats[ebook][sample_file]"
                                        class="form-control ebook-field @error('formats.ebook.sample_file') is-invalid @enderror" accept=".pdf,.epub">
                                    @if($ebookFormat && $ebookFormat->sample_file_url)
                                    <div class="mt-2">
                                        <span class="badge bg-info">
                                            <i class="ri-file-pdf-line me-1"></i> File hiện tại: {{ basename($ebookFormat->sample_file_url) }}
                                        </span>
                                        <small class="text-muted d-block mt-1">Tải lên file mới để thay thế file hiện tại</small>
                                    </div>
                                    @endif
                                    <small class="text-muted">File xem thử cho khách hàng</small>
                                    @error('formats.ebook.sample_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                        
                        {{-- Hiển thị thuộc tính sản phẩm đã chọn --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Thuộc tính sản phẩm</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @php
                                        $oldAttributeValues = old('attribute_values');
                                    @endphp
                                    @foreach($attributes as $attribute)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ $attribute->name }}</label>
                                        <div class="attribute-group">
                                            <div class="input-group mb-2">
                                                <select class="form-select attribute-select"
                                                    data-attribute-name="{{ $attribute->name }}"
                                                    data-attribute-id="{{ $attribute->id }}">
                                                    <option value="">-- Chọn {{ strtolower($attribute->name) }} --</option>
                                                    @foreach($attribute->values as $value)
                                                        <option value="{{ $value->id }}">{{ $value->value }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" class="form-control attribute-extra-price"
                                                    placeholder="Giá thêm" min="0" value="0">
                                                <button type="button" class="btn btn-primary add-attribute-value">Thêm</button>
                                            </div>
                                            <div class="selected-values mt-2">
                                                {{-- Ưu tiên render lại từ old input nếu có, nếu không thì lấy từ model --}}
                                                @if($oldAttributeValues)
                                                    @foreach($oldAttributeValues as $attrId => $data)
                                                        @php
                                                            $attrValue = \App\Models\AttributeValue::find($data['id']);
                                                        @endphp
                                                        @if($attrValue && $attrValue->attribute_id == $attribute->id)
                                                        <div class="selected-attribute-value d-inline-flex align-items-center mb-1" style="background:#1677ff;color:#fff;border-radius:6px;padding:4px 12px 4px 12px;font-size:15px;max-width:100%;">
                                                            <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:120px;">{{ $attrValue->value }}</span>
                                                            <span style="margin-left:4px;">+{{ number_format($data['extra_price'] ?? 0) }}đ</span>
                                                            <button type="button" class="btn btn-link p-0 ms-2 remove-attribute-value" style="color:#86b7fe;font-size:18px;line-height:1;"><i class="fa-solid fa-xmark"></i></button>
                                                            <input type="hidden" name="attribute_values[{{ $attrValue->id }}][id]" value="{{ $attrValue->id }}">
                                                            <input type="hidden" name="attribute_values[{{ $attrValue->id }}][extra_price]" value="{{ $data['extra_price'] ?? 0 }}">
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach($book->attributeValues as $attributeValue)
                                                        @if($attributeValue->attribute_id == $attribute->id)
                                                        <div class="selected-attribute-value d-inline-flex align-items-center mb-1" style="background:#1677ff;color:#fff;border-radius:6px;padding:4px 12px 4px 12px;font-size:15px;max-width:100%;">
                                                            <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:120px;">{{ $attributeValue->value }}</span>
                                                            <span style="margin-left:4px;">+{{ number_format($attributeValue->pivot->extra_price) }}đ</span>
                                                            <button type="button" class="btn btn-link p-0 ms-2 remove-attribute-value" style="color:#86b7fe;font-size:18px;line-height:1;"><i class="fa-solid fa-xmark"></i></button>
                                                            <input type="hidden" name="attribute_values[{{ $attributeValue->id }}][id]" value="{{ $attributeValue->id }}">
                                                            <input type="hidden" name="attribute_values[{{ $attributeValue->id }}][extra_price]" value="{{ $attributeValue->pivot->extra_price }}">
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                @endif
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
                                    <label for="author_id" class="form-label">Tác giả <span class="text-danger">*</span></label>
                                    {{-- Hiển thị tác giả đã chọn --}}
                                    @php
                                        // Lấy danh sách tác giả đã chọn: ưu tiên old input, nếu không thì lấy từ model
                                        $selectedAuthors = old('author_ids');
                                        if ($selectedAuthors === null) {
                                            $selectedAuthors = ($book->author && $book->author->count()) ? $book->author->pluck('id')->toArray() : [];
                                        }
                                    @endphp
                                    {{-- <pre>{{ var_export($selectedAuthors, true) }}</pre> <!-- Bỏ comment để kiểm tra giá trị khi cần --> --}}
                                    <select name="author_ids[]" id="author_id" class="form-select selectpicker @error('author_ids') is-invalid @enderror" data-live-search="true" multiple title="Tìm tác giả...">
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}" {{ in_array($author->id, $selectedAuthors) ? 'selected' : '' }}>{{ $author->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('author_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <label for="publication_date" class="form-label">Ngày xuất bản <span class="text-danger">*</span></label>
                                    <input type="date" name="publication_date" class="form-control @error('publication_date') is-invalid @enderror"
                                        id="publication_date" value="{{ old('publication_date', $book->publication_date ? $book->publication_date->format('Y-m-d') : '') }}">
                                    @error('publication_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

                        <!-- Quà tặng -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Quà tặng kèm theo</h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggle-gift-section" {{ $book->gifts->count() ? 'checked' : '' }}>
                                    <label class="form-check-label" for="toggle-gift-section">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="gift-section" style="display:{{ $book->gifts->count() ? '' : 'none' }};">
                                <div class="mb-3 row">
                                    @php $gift = $book->gifts->first(); @endphp
                                    <div class="col-md-2">
                                        <input type="text" name="gift_name" class="form-control" value="{{ $gift?->gift_name }}" placeholder="Tên quà tặng">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="gift_description" class="form-control" value="{{ $gift?->gift_description }}" placeholder="Mô tả (tuỳ chọn)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="file" name="gift_image" class="form-control">
                                        @if($gift && $gift->gift_image)
                                            <img src="{{ asset('storage/' . $gift->gift_image) }}" alt="gift" width="40" class="mt-1 rounded border">
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="quantity" class="form-control" value="{{ $gift?->quantity }}" placeholder="Số lượng" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="start_date" class="form-control datepicker" value="{{ $gift?->start_date }}" placeholder="Ngày bắt đầu">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="end_date" class="form-control datepicker" value="{{ $gift?->end_date }}" placeholder="Ngày kết thúc">
                                    </div>
                                </div>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.getElementById('toggle-gift-section').addEventListener('change', function() {
        document.getElementById('gift-section').style.display = this.checked ? '' : 'none';
    });
    function initFlatpickr() {
        flatpickr('.datepicker', {dateFormat: 'Y-m-d'});
    }
    initFlatpickr();

    function checkFormatValidation() {
        const hasPhysical = document.getElementById('has_physical').checked;
        const hasEbook = document.getElementById('has_ebook').checked;
        if (!hasPhysical && !hasEbook) {
            document.getElementById('format_validation_message').style.display = '';
            return false;
        } else {
            document.getElementById('format_validation_message').style.display = 'none';
            return true;
        }
    }
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!checkFormatValidation()) {
            e.preventDefault();
        }
    });
    document.getElementById('has_physical').addEventListener('change', checkFormatValidation);
    document.getElementById('has_ebook').addEventListener('change', checkFormatValidation);

    // 3. JS preview ảnh bìa/ảnh phụ, lưu base64 vào localStorage, tự động hiển thị lại sau validate lỗi
    function previewImage(input, previewContainer, storageKey) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `<img src="${e.target.result}" class='img-thumbnail' style='max-height:200px;'>`;
                localStorage.setItem(storageKey, e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    const coverInput = document.getElementById('cover_image');
    const coverPreview = document.getElementById('cover_preview')?.querySelector('.preview-container');
    if (coverInput && coverPreview) {
        coverInput.addEventListener('change', function() {
            previewImage(this, coverPreview, 'edit_cover_image');
        });
        // Hiển thị lại preview nếu có trong localStorage
        const saved = localStorage.getItem('edit_cover_image');
        if (saved) {
            coverPreview.innerHTML = `<img src="${saved}" class='img-thumbnail' style='max-height:200px;'>`;
        }
    }
    // Preview ảnh phụ
    const imagesInput = document.getElementById('images');
    const imagesPreview = document.getElementById('images_preview');
    if (imagesInput && imagesPreview) {
        imagesInput.addEventListener('change', function() {
            imagesPreview.innerHTML = '';
            Array.from(this.files).forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagesPreview.innerHTML += `<div class='col-4'><img src='${e.target.result}' class='img-thumbnail' style='height:100px;object-fit:cover;width:100%;'></div>`;
                    let arr = JSON.parse(localStorage.getItem('edit_images') || '[]');
                    arr[idx] = e.target.result;
                    localStorage.setItem('edit_images', JSON.stringify(arr));
                };
                reader.readAsDataURL(file);
            });
        });
        // Hiển thị lại preview nếu có trong localStorage
        const savedArr = JSON.parse(localStorage.getItem('edit_images') || '[]');
        if (savedArr.length) {
            imagesPreview.innerHTML = '';
            savedArr.forEach(src => {
                imagesPreview.innerHTML += `<div class='col-4'><img src='${src}' class='img-thumbnail' style='height:100px;object-fit:cover;width:100%;'></div>`;
            });
        }
    }
    // Xóa preview khi submit thành công
    document.querySelector('form').addEventListener('submit', function() {
        localStorage.removeItem('edit_cover_image');
        localStorage.removeItem('edit_images');
    });
    // 4. JS xóa thuộc tính sản phẩm (badge xanh, nút xóa)
    document.querySelectorAll('.selected-values').forEach(function(container) {
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-attribute-value')) {
                e.target.closest('.selected-attribute-value').remove();
            }
        });
    });
</script>
@endpush

{{-- 5. Thông báo dưới input ảnh --}}
<div class="form-text text-danger">Nếu form báo lỗi, bạn cần chọn lại ảnh bìa/ảnh phụ.</div>

