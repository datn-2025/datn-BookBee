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
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="isbn" class="form-label">ISBN<span
                                            class="text-danger">*</span></label>
                                        <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" id="isbn" value="{{ old('isbn') }}">
                                        @error('isbn')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="page_count" class="form-label">Số trang<span
                                            class="text-danger">*</span></label>
                                        <input type="number" name="page_count" class="form-control @error('page_count') is-invalid @enderror" id="page_count" value="{{ old('page_count') }}">
                                        @error('page_count')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
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
                                        name="has_physical" value="1" {{ old('has_physical') ? 'checked' : '' }}>
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
                                        value="1" {{ old('has_ebook') ? 'checked' : '' }}>
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
                                        name="formats[ebook][allow_sample_read]" value="1" {{ old('formats.ebook.allow_sample_read') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_sample_read">
                                        Cho phép đọc thử
                                    </label>
                                </div>

                                <div class="mb-3" id="sample_file_container" style="display: none;">
                                    <label class="form-label">File xem thử</label>
                                    <input type="file" name="formats[ebook][sample_file]"
                                        class="form-control ebook-field @error('formats.ebook.sample_file') is-invalid @enderror" accept=".pdf,.epub">
                                    @error('formats.ebook.sample_file')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="text-muted">File xem thử cho khách hàng</small>
                                </div>
                            </div>
                        </div>
                        <!-- Thông báo lỗi định dạng sách -->
                        @if($errors->has('format'))
                            <div class="alert alert-danger mb-2">
                                {{ $errors->first('format') }}
                            </div>
                        @endif
                        <div class="mb-2">
                            <small class="text-muted">Bạn phải chọn ít nhất một định dạng sách (Sách vật lý hoặc Ebook).</small>
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
                                            <div class="selected-values mt-2">
                                                @if(old('attribute_values'))
                                                    @foreach(old('attribute_values') as $value)
                                                        @if(isset($value['id']) && in_array($value['id'], $attribute->values->pluck('id')->toArray()))
                                                            <div class="selected-attribute-value d-inline-flex align-items-center mb-1" style="background:#1677ff;color:#fff;border-radius:6px;padding:4px 12px 4px 12px;font-size:15px;max-width:100%;">
                                                                <input type="hidden" name="attribute_values[{{ $value['id'] }}][id]" value="{{ $value['id'] }}">
                                                                <input type="hidden" name="attribute_values[{{ $value['id'] }}][extra_price]" value="{{ $value['extra_price'] ?? 0 }}">
                                                                <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:120px;">{{ $attribute->values->firstWhere('id', $value['id'])->value }}</span>
                                                                <span style="margin-left:4px;">(+{{ number_format($value['extra_price'] ?? 0) }}đ)</span>
                                                                <button type="button" class="btn btn-link p-0 ms-2 remove-attribute-value" style="color:#86b7fe;font-size:18px;line-height:1;"><i class="fa-solid fa-xmark"></i></button>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            <!-- Hidden inputs will be added here by JavaScript -->
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Quà tặng -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Quà tặng kèm theo</h5>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggle-gift-section">
                                    <label class="form-check-label" for="toggle-gift-section">Kích hoạt</label>
                                </div>
                            </div>
                            <div class="card-body" id="gift-section" style="display:none;">
                                <div class="mb-3 row">
                                    <div id="book-gifts-list">
                                        <div class="row mb-2 align-items-end gift-row">
                                            <div class="col-md-3 d-flex align-items-center">
                                                <p style="margin-left: 30px">Thời gian tặng quà</p>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="date_range" class="form-control date-range-picker" placeholder="Chọn khoảng ngày" autocomplete="off">
                                                <input type="hidden" name="start_date" class="start-date">
                                                <input type="hidden" name="end_date" class="end-date">
                                            </div>
                                        </div>
                                        <div class="row mb-2 mt-4 align-items-end gift-row">
                                            <div class="col-md-3">
                                                <select name="book_id" class="form-select" >
                                                    <option value="">-- Chọn sách --</option>
                                                    @foreach($allBooks as $book)
                                                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="gift_name" class="form-control" placeholder="Tên quà tặng">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="gift_description" class="form-control" placeholder="Mô tả (tuỳ chọn)">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="quantity" class="form-control" placeholder="Số lượng" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-gift-btn">+ Thêm quà tặng</button> --}}
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
                                    <label for="status" class="form-label">Trạng thái<span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                                        <option>--Chọn trạng thái--</option>
                                        <option value="Còn Hàng" {{ old('status') == 'Còn Hàng' ? 'selected' : '' }}>Còn Hàng</option>
                                        <option value="Hết Hàng Tồn Kho" {{ old('status') == 'Hết Hàng Tồn Kho' ? 'selected' : '' }}>Hết Hàng Tồn Kho</option>
                                        <option value="Sắp Ra Mắt" {{ old('status') == 'Sắp Ra Mắt' ? 'selected' : '' }}>Sắp Ra Mắt</option>
                                        <option value="Ngừng Kinh Doanh" {{ old('status') == 'Ngừng Kinh Doanh' ? 'selected' : '' }}>Ngừng Kinh Doanh</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Danh mục <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" >
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="author_id" class="form-label">Tác giả <span class="text-danger">*</span></label>
                                    <select name="author_ids[]" id="author_id" class="form-select aiz-selectpicker" data-live-search="true" multiple data-selected-text-format="count" title="Tìm tác giả...">
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}" {{ (collect(old('author_ids'))->contains($author->id)) ? 'selected' : '' }}>{{ $author->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                                <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
                                <script>
                                    $(document).ready(function () {
                                        $('.selectpicker').selectpicker();
                                    });
                                </script> --}}
                                <div class="mb-3">
                                    <label for="brand_id" class="form-label">Thương hiệu <span
                                            class="text-danger">*</span></label>
                                    <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror" >
                                        <option value="">-- Chọn thương hiệu --</option>
                                        @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id')==$brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="publication_date" class="form-label">Ngày xuất bản<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="publication_date" class="form-control @error('publication_date') is-invalid @enderror"
                                        id="publication_date" value="{{ old('publication_date') }}">
                                    @error('publication_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
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
                                    <small class="text-warning">Nếu form báo lỗi, bạn cần chọn lại ảnh bìa.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="images" class="form-label">Ảnh phụ</label>
                                    <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" id="images" multiple
                                        accept="image/*">
                                    @error('images.*')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="mt-2">
                                        <div id="images_preview" class="row g-2"></div>
                                    </div>
                                    <small class="text-warning">Nếu form báo lỗi, bạn cần chọn lại ảnh phụ.</small>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    document.getElementById('toggle-gift-section').addEventListener('change', function() {
        document.getElementById('gift-section').style.display = this.checked ? '' : 'none';
    });
    function initFlatpickr() {
        document.querySelectorAll('.date-range-picker').forEach(function(el) {
            flatpickr(el, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                onChange: function(selectedDates, dateStr, instance) {
                    const parent = el.closest('.gift-row');
                    if (selectedDates.length === 2) {
                        const start = selectedDates[0];
                        const end = selectedDates[1];
                        parent.querySelector('.start-date').value = instance.formatDate(start, 'Y-m-d');
                        parent.querySelector('.end-date').value = instance.formatDate(end, 'Y-m-d');
                    } else {
                        parent.querySelector('.start-date').value = '';
                        parent.querySelector('.end-date').value = '';
                    }
                }
            });
        });
    }
    initFlatpickr();

    // Xử lý xóa thuộc tính đã chọn (cả khi render từ server)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-attribute-value')) {
            const row = e.target.closest('.selected-attribute-value');
            if (row) row.remove();
        }
    });

    // Ẩn/hiện phần định dạng sách theo checkbox
    function toggleFormatSections() {
        document.getElementById('physical_format').style.display = document.getElementById('has_physical').checked ? '' : 'none';
        document.getElementById('ebook_format').style.display = document.getElementById('has_ebook').checked ? '' : 'none';
    }
    document.getElementById('has_physical').addEventListener('change', toggleFormatSections);
    document.getElementById('has_ebook').addEventListener('change', toggleFormatSections);
    toggleFormatSections(); // Gọi khi load trang để giữ trạng thái

    // Khi submit, kiểm tra phải chọn ít nhất 1 định dạng sách
    document.querySelector('form').addEventListener('submit', function(e) {
        var hasPhysical = document.getElementById('has_physical').checked;
        var hasEbook = document.getElementById('has_ebook').checked;
        var formatMsg = document.getElementById('format_validation_message');
        if (!hasPhysical && !hasEbook) {
            e.preventDefault();
            formatMsg.style.display = '';
            formatMsg.textContent = 'Vui lòng kích hoạt ít nhất một định dạng sách.';
            window.scrollTo({top: formatMsg.offsetTop - 100, behavior: 'smooth'});
        } else {
            formatMsg.style.display = 'none';
        }
    });

    // Nếu có lỗi validate liên quan định dạng, KHÔNG tự động bật phần định dạng nữa
    // Đã có thông báo lỗi phía trên phần định dạng sách
    AIZ.plugins = {
    bootstrapSelect: function (refresh = "") {
            $(".aiz-selectpicker").each(function (el) {
                var $this = $(this);
                if(!$this.parent().hasClass('bootstrap-select')){
                    var selected = $this.data('selected');
                    if( typeof selected !== 'undefined' ){
                        $this.val(selected);
                    }
                    $this.selectpicker({
                        size: 5,
                        noneSelectedText: AIZ.local.nothing_selected,
                        virtualScroll: false
                    });
                }
                if (refresh === "refresh") {
                    $this.selectpicker("refresh");
                }
                if (refresh === "destroy") {
                    $this.selectpicker("destroy");
                }
            });
        },}
</script>
@endpush