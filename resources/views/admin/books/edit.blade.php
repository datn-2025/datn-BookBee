@extends('layouts.backend')

@section('title', 'Chỉnh sửa sách')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-semibold">
                <i class="ri-book-edit-line me-2 text-primary"></i>Chỉnh sửa sách
            </h1>
            <p class="text-muted mb-0">Cập nhật thông tin sách: <strong>{{ $book->title }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.books.show', [$book->id, $book->slug]) }}" 
               class="btn btn-outline-info">
                <i class="ri-eye-line me-2"></i>Xem chi tiết
            </a>
            <a href="{{ route('admin.books.index') }}" 
               class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <form action="{{ route('admin.books.update', [$book->id, $book->slug]) }}" method="POST" enctype="multipart/form-data" id="bookForm" novalidate>
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Thông tin cơ bản -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-information-line me-2 text-primary"></i>Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="title" class="form-label fw-medium">Tên sách <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $book->title) }}" 
                                       placeholder="Nhập tên sách..." required>
                                @error('title')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="brand_id" class="form-label fw-medium">Thương hiệu</label>
                                <select class="form-select @error('brand_id') is-invalid @enderror" 
                                        id="brand_id" name="brand_id">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" 
                                            {{ old('brand_id', $book->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label fw-medium">Mô tả sách</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Nhập mô tả sách...">{{ old('description', $book->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin chi tiết -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-file-text-line me-2 text-info"></i>Chi tiết sách
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="isbn" class="form-label fw-medium">ISBN</label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" 
                                       placeholder="Mã ISBN...">
                                @error('isbn')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="page_count" class="form-label fw-medium">Số trang</label>
                                <input type="number" class="form-control @error('page_count') is-invalid @enderror" 
                                       id="page_count" name="page_count" value="{{ old('page_count', $book->page_count) }}" 
                                       placeholder="Số trang..." min="1">
                                @error('page_count')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="publication_date" class="form-label fw-medium">Ngày xuất bản</label>
                                <input type="date" class="form-control @error('publication_date') is-invalid @enderror" 
                                       id="publication_date" name="publication_date" 
                                       value="{{ old('publication_date', $book->publication_date ? $book->publication_date->format('Y-m-d') : '') }}">
                                @error('publication_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- <div class="col-md-6">
                                <label for="language" class="form-label fw-medium">Ngôn ngữ</label>
                                <select class="form-select @error('language') is-invalid @enderror" 
                                        id="language" name="language">
                                    <option value="">-- Chọn ngôn ngữ --</option>
                                    <option value="Tiếng Việt" {{ old('language', $book->language) == 'Tiếng Việt' ? 'selected' : '' }}>Tiếng Việt</option>
                                    <option value="Tiếng Anh" {{ old('language', $book->language) == 'Tiếng Anh' ? 'selected' : '' }}>Tiếng Anh</option>
                                    <option value="Tiếng Trung" {{ old('language', $book->language) == 'Tiếng Trung' ? 'selected' : '' }}>Tiếng Trung</option>
                                    <option value="Tiếng Nhật" {{ old('language', $book->language) == 'Tiếng Nhật' ? 'selected' : '' }}>Tiếng Nhật</option>
                                    <option value="Tiếng Hàn" {{ old('language', $book->language) == 'Tiếng Hàn' ? 'selected' : '' }}>Tiếng Hàn</option>
                                </select>
                                @error('language')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Tác giả -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-user-line me-2 text-success"></i>Tác giả
                        </h5>
                    </div>
                    <div class="card-body">
                        <label for="author_ids" class="form-label fw-medium">Chọn tác giả</label>
                        <select class="form-select select2-authors @error('author_ids') is-invalid @enderror" 
                                id="author_ids" name="author_ids[]" multiple>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" 
                                    {{ in_array($author->id, old('author_ids', $book->authors->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('author_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Quà tặng -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-gift-line me-2 text-warning"></i>Quà tặng kèm theo
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $currentGift = $book->gifts->first();
                        @endphp
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="has_gift" name="has_gift" 
                                   value="1" {{ old('has_gift', $currentGift ? '1' : '') ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="has_gift">
                                Sách có kèm quà tặng
                            </label>
                        </div>
                        
                        <div id="gift_section" style="display: {{ $currentGift ? 'block' : 'none' }};">
                            <div class="p-3 bg-light rounded border">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Chọn sách nhận quà tặng</label>
                                        <select class="form-select @error('gift_book_id') is-invalid @enderror" 
                                                name="gift_book_id">
                                            <option value="" {{ old('gift_book_id', $currentGift->book_id ?? '') == '' ? 'selected' : '' }}>Sách hiện tại</option>
                                            @foreach($books ?? [] as $bookOption)
                                                <option value="{{ $bookOption->id }}" 
                                                    {{ old('gift_book_id', $currentGift->book_id ?? '') == $bookOption->id ? 'selected' : '' }}>
                                                    {{ $bookOption->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('gift_book_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Để trống để tạo quà tặng cho sách hiện tại</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Tên quà tặng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('gift_name') is-invalid @enderror" 
                                               name="gift_name" value="{{ old('gift_name', $currentGift->gift_name ?? '') }}" 
                                               placeholder="Ví dụ: Bookmark đặc biệt, Postcard...">
                                        @error('gift_name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Số lượng quà tặng</label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                               name="quantity" value="{{ old('quantity', $currentGift->quantity ?? '') }}" 
                                               placeholder="0" min="1" step="1">
                                        @error('quantity')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Mô tả quà tặng</label>
                                        <textarea class="form-control @error('gift_description') is-invalid @enderror" 
                                                  name="gift_description" rows="3" 
                                                  placeholder="Mô tả chi tiết về quà tặng...">{{ old('gift_description', $currentGift->gift_description ?? '') }}</textarea>
                                        @error('gift_description')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Thời gian khuyến mãi quà tặng</label>
                                        <input type="text" class="form-control @error('gift_date_range') is-invalid @enderror" 
                                               id="gift_date_range" name="gift_date_range" 
                                               placeholder="Chọn khoảng thời gian khuyến mãi..." 
                                               value="{{ old('gift_date_range', ($currentGift && $currentGift->start_date && $currentGift->end_date) ? $currentGift->start_date . ' to ' . $currentGift->end_date : '') }}">
                                        
                                        <!-- Hidden inputs để lưu giá trị ngày -->
                                        <input type="hidden" id="gift_start_date" name="gift_start_date" 
                                               value="{{ old('gift_start_date', $currentGift && $currentGift->start_date ? $currentGift->start_date : '') }}">
                                        <input type="hidden" id="gift_end_date" name="gift_end_date" 
                                               value="{{ old('gift_end_date', $currentGift && $currentGift->end_date ? $currentGift->end_date : '') }}">
                                        
                                         @error('gift_date_range')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @elseif($errors->has('gift_start_date'))
                                            <div class="text-danger small mt-1">{{ $errors->first('gift_start_date') }}</div>
                                        @elseif($errors->has('gift_end_date'))
                                            <div class="text-danger small mt-1">{{ $errors->first('gift_end_date') }}</div>
                                        @enderror
                                        
                                        <div class="form-text">
                                            <i class="ri-information-line me-1"></i>
                                            Chọn khoảng thời gian áp dụng khuyến mãi quà tặng. Nhấp vào ô để chọn ngày bắt đầu và kết thúc.
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Hình ảnh quà tặng</label>
                                        @if($currentGift && $currentGift->gift_image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $currentGift->gift_image) }}" 
                                                     class="img-thumbnail" style="max-height: 150px;" alt="Ảnh quà tặng hiện tại">
                                                <div class="small text-muted mt-1">Ảnh quà tặng hiện tại</div>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('gift_image') is-invalid @enderror" 
                                               name="gift_image" accept="image/*">
                                        @error('gift_image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Chấp nhận file ảnh JPG, PNG, GIF. Tối đa 2MB. Để trống nếu không muốn thay đổi.</div>
                                        <div id="gift_image_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Định dạng sách -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-book-open-line me-2 text-warning"></i>Định dạng & Giá bán
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $physicalFormat = $book->formats->where('format_name', 'Sách Vật Lý')->first();
                            $ebookFormat = $book->formats->where('format_name', 'Ebook')->first();
                        @endphp
                        
                        @error('format_required')
                            <div class="alert alert-danger mb-3">
                                <i class="ri-error-warning-line me-2"></i>{{ $message }}
                            </div>
                        @enderror
                        
                        <!-- Sách vật lý -->
                        <div class="mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="has_physical" name="has_physical" 
                                       value="1" {{ old('has_physical', $physicalFormat ? '1' : '') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="has_physical">
                                    <i class="ri-book-line me-1"></i>Sách vật lý
                                </label>
                            </div>
                            
                            <div id="physical_format" style="display: {{ $physicalFormat ? 'block' : 'none' }};">
                                <div class="border rounded p-3 bg-light">
                                    <!-- Thông tin cơ bản sách vật lý -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Giá bán (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[physical][price]" 
                                                   id="physical_price" value="{{ old('formats.physical.price', $physicalFormat->price ?? '') }}" 
                                                   placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Giảm giá (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[physical][discount]" 
                                                   id="physical_discount" value="{{ old('formats.physical.discount', $physicalFormat->discount ?? '') }}" 
                                                   placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Số lượng</label>
                                            <input type="number" class="form-control" name="formats[physical][stock]" 
                                                   value="{{ old('formats.physical.stock', $physicalFormat->stock ?? '') }}" 
                                                   placeholder="0" min="0">
                                        </div>
                                    </div>
                                    
                                    <!-- Thuộc tính sách vật lý -->
                                    <div class="border-top pt-4">
                                        <h6 class="fw-bold text-dark mb-3">
                                            <i class="ri-price-tag-3-line me-2 text-primary"></i>Thuộc tính sách vật lý
                                        </h6>
                                        
                                        <div class="mb-3">
                                            <div class="alert alert-info border-0 bg-light">
                                                <div class="d-flex align-items-start">
                                                    <i class="ri-information-line me-2 mt-1 text-info"></i>
                                                    <div>
                                                        <h6 class="mb-1 text-dark fw-semibold">Thuộc tính biến thể</h6>
                                                        <p class="mb-0 text-muted small">
                                                            Các thuộc tính như màu sắc, kích thước, loại bìa sẽ tạo ra các biến thể khác nhau với giá và tồn kho riêng biệt.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($attributes->count() > 0)
                                            @foreach($attributes as $attribute)
                                                <div class="attribute-group mb-4 p-3 border rounded bg-white">
                                                    <h6 class="fw-bold text-primary mb-3">
                                                        <i class="ri-bookmark-line me-1"></i>{{ $attribute->name }}
                                                    </h6>
                                                    
                                    <!-- Hiển thị thuộc tính hiện có -->
                                    @php
                                        // Lấy BookAttributeValue records cho attribute này với debug
                                        $bookAttributes = $book->bookAttributeValues->filter(function($bookAttributeValue) use ($attribute) {
                                            return $bookAttributeValue->attributeValue && 
                                                   $bookAttributeValue->attributeValue->attribute &&
                                                   $bookAttributeValue->attributeValue->attribute->id == $attribute->id;
                                        });
                                        
                                        // Debug thông tin - hiển thị trong môi trường debug
                                    @endphp
                                    
                                                   @if($bookAttributes->count() > 0)
                                                        <div class="mb-3">
                                                            <h6 class="text-success mb-2">Thuộc tính hiện có:</h6>
                                                            @foreach($bookAttributes as $bookAttr)
                                                                <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                                                                    <div>
                                                                        <span class="badge bg-primary me-2">{{ $bookAttr->attributeValue ? $bookAttr->attributeValue->value : 'N/A' }}</span>
                                                                        <small class="text-muted">
                                                                            Giá thêm: {{ number_format($bookAttr->extra_price  ?? 0) }}đ | 
                                                                            Tồn kho: {{ $bookAttr->stock ?? 0 }} |
                                                                            SKU: {{ $bookAttr->sku ?? 'Chưa có' }}
                                                                        </small>
                                                                    </div>
                                                                    <div>
                                                                        <input type="hidden" name="existing_attributes[{{ $bookAttr->id }}][attribute_value_id]" value="{{ $bookAttr->attribute_value_id }}">
                                                                        <input type="hidden" name="existing_attributes[{{ $bookAttr->id }}][keep]" value="1" class="keep-attribute">
                                                                        <input type="number" name="existing_attributes[{{ $bookAttr->id }}][extra_price]" 
                                                                               value="{{ $bookAttr->extra_price ?? 0 }}" class="form-control form-control-sm d-inline-block me-2" 
                                                                               style="width: 100px;" placeholder="Giá thêm" min="0" step="1000">
                                                                        <input type="number" name="existing_attributes[{{ $bookAttr->id }}][stock]" 
                                                                               value="{{ $bookAttr->stock ?? 0 }}" class="form-control form-control-sm d-inline-block me-2" 
                                                                               style="width: 80px;" placeholder="Tồn kho" min="0">
                                                                        <button type="button" class="btn btn-sm btn-danger remove-existing-attribute" 
                                                                                data-attribute-id="{{ $bookAttr->id }}">
                                                                            <i class="ri-delete-bin-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Form thêm thuộc tính mới -->
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-medium">Thêm giá trị mới</label>
                                                            <select class="form-select attribute-select" 
                                                                    data-attribute-id="{{ $attribute->id }}" 
                                                                    data-attribute-name="{{ $attribute->name }}">
                                                                <option value="">-- Chọn {{ $attribute->name }} --</option>
                                                                @foreach($attribute->values as $value)
                                                                    <option value="{{ $value->id }}" data-value-name="{{ $value->value }}">
                                                                        {{ $value->value }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-medium">Giá thêm (VNĐ)</label>
                                                            <input type="number" class="form-control attribute-extra-price" 
                                                                   placeholder="0" min="0">
                                                        </div>
                                                        
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-medium">Số lượng</label>
                                                            <input type="number" class="form-control attribute-stock" 
                                                                   placeholder="0" min="0">
                                                        </div>
                                                        
                                                        <div class="col-md-2">
                                                            <label class="form-label fw-medium">&nbsp;</label>
                                                            <button type="button" class="btn btn-primary d-block add-attribute-btn">
                                                                <i class="ri-add-line"></i> Thêm
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Container hiển thị các thuộc tính mới đã chọn -->
                                                    <div class="selected-variants-container" data-attribute-id="{{ $attribute->id }}">
                                                        <!-- Các thuộc tính mới đã chọn sẽ hiển thị ở đây -->
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-price-tag-3-line text-muted" style="font-size: 48px;"></i>
                                                <p class="text-muted mt-2">Chưa có thuộc tính nào được tạo.</p>
                                                <small class="text-muted">Vui lòng tạo thuộc tính trong phần quản lý thuộc tính trước.</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ebook -->
                        <div class="mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="has_ebook" name="has_ebook" 
                                       value="1" {{ old('has_ebook', $ebookFormat ? '1' : '') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="has_ebook">
                                    <i class="ri-file-text-line me-1"></i>Sách điện tử (Ebook)
                                </label>
                            </div>
                            
                            <div id="ebook_format" style="display: {{ $ebookFormat ? 'block' : 'none' }};">
                                <div class="border rounded p-3 bg-light">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Giá bán (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[ebook][price]" 
                                                   id="ebook_price" value="{{ old('formats.ebook.price', $ebookFormat->price ?? '') }}" 
                                                   placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Giảm giá (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[ebook][discount]" 
                                                   id="ebook_discount" value="{{ old('formats.ebook.discount', $ebookFormat->discount ?? '') }}" 
                                                   placeholder="0" min="0">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-medium">File Ebook</label>
                                            @if($ebookFormat && $ebookFormat->file_url)
                                                <div class="mb-2">
                                                    <small class="text-success">
                                                        <i class="ri-file-check-line me-1"></i>
                                                        File hiện tại: {{ basename($ebookFormat->file_url) }}
                                                    </small>
                                                </div>
                                            @endif
                                            <input type="file" class="form-control" name="formats[ebook][file]" 
                                                   accept=".pdf,.epub">
                                            <div class="form-text">Chấp nhận file PDF hoặc EPUB, tối đa 50MB. Để trống nếu không muốn thay đổi.</div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label class="form-label fw-medium">File đọc thử</label>
                                            @if($ebookFormat && $ebookFormat->sample_file_url)
                                                <div class="mb-2">
                                                    <small class="text-success">
                                                        <i class="ri-file-check-line me-1"></i>
                                                        File đọc thử hiện tại: {{ basename($ebookFormat->sample_file_url) }}
                                                    </small>
                                                </div>
                                            @endif
                                            <input type="file" class="form-control" name="formats[ebook][sample_file]" 
                                                   accept=".pdf,.epub">
                                            <div class="form-text">File đọc thử cho khách hàng. Chấp nhận file PDF hoặc EPUB, tối đa 10MB. Để trống nếu không muốn thay đổi.</div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="allow_sample_read" 
                                                       name="formats[ebook][allow_sample_read]" value="1" 
                                                       {{ old('formats.ebook.allow_sample_read', $ebookFormat->allow_sample_read ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_sample_read">
                                                    <i class="ri-eye-line me-1"></i>Cho phép đọc thử trực tuyến
                                                </label>
                                            </div>
                                            <div class="form-text">Khách hàng có thể đọc thử một phần nội dung sách trước khi mua.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('format')
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Hình ảnh -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-image-line me-2 text-primary"></i>Hình ảnh
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Ảnh bìa -->
                        <div class="mb-4">
                            <label for="cover_image" class="form-label fw-medium">Ảnh bìa</label>
                            @if($book->cover_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                         class="img-thumbnail" style="max-height: 200px;" alt="Ảnh bìa hiện tại">
                                    <div class="small text-muted mt-1">Ảnh bìa hiện tại</div>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                   id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không muốn thay đổi ảnh bìa</div>
                            <div id="cover_preview" class="mt-2"></div>
                        </div>
                        
                        <!-- Ảnh phụ -->
                        <div>
                            <label for="images" class="form-label fw-medium">Ảnh phụ</label>
                            @if($book->images->count() > 0)
                                <div class="mb-2">
                                    <div class="row" id="existing-images">
                                        @foreach($book->images as $image)
                                            <div class="col-6 mb-2" id="image-item-{{ $image->id }}">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                                                             class="card-img-top" style="height: 100px; object-fit: cover;" 
                                                             alt="Ảnh phụ">
                                                        <div class="position-absolute top-0 end-0 p-1">
                                                            <button type="button" class="btn btn-danger btn-sm rounded-circle delete-image-btn" 
                                                                    data-image-id="{{ $image->id }}"
                                                                    style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                                <i class="ri-delete-bin-line" style="font-size: 14px;"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <small class="text-muted">Ảnh {{ $loop->iteration }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="small text-muted">Ảnh phụ hiện tại</div>
                                    <!-- Hidden inputs to track deleted images -->
                                    <div id="deleted-images-inputs"></div>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            @error('images')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Có thể chọn nhiều ảnh. Ảnh mới sẽ thay thế ảnh cũ.</div>
                            <div id="images_preview" class="row mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-settings-line me-2 text-secondary"></i>Trạng thái
                        </h5>
                    </div>
                    <div class="card-body">
                        <label for="status" class="form-label fw-medium">Trạng thái sách</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="Còn Hàng" {{ old('status', $book->status) == 'Còn Hàng' ? 'selected' : '' }}>Còn Hàng</option>
                            <option value="Hết Hàng Tồn Kho" {{ old('status', $book->status) == 'Hết Hàng Tồn Kho' ? 'selected' : '' }}>Hết Hàng Tồn Kho</option>
                            <option value="Sắp Ra Mắt" {{ old('status', $book->status) == 'Sắp Ra Mắt' ? 'selected' : '' }}>Sắp Ra Mắt</option>
                            <option value="Ngừng Kinh Doanh" {{ old('status', $book->status) == 'Ngừng Kinh Doanh' ? 'selected' : '' }}>Ngừng Kinh Doanh</option>
                        </select>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Thống kê -->
                <div class="card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="ri-bar-chart-line me-2 text-info"></i>Thống kê
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <div class="fw-bold text-primary">{{ $book->images->count() }}</div>
                                    <small class="text-muted">Ảnh phụ</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <div class="fw-bold text-success">{{ $book->bookAttributeValues->count() }}</div>
                                    <small class="text-muted">Biến thể</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ri-save-line me-2"></i>Cập nhật sách
                            </button>
                            {{-- <a href="{{ route('admin.books.show', ['id' => $book->id, 'slug' => $book->slug]) }}" 
                               class="btn btn-outline-info btn-lg">
                                <i class="ri-eye-line me-2"></i>Xem chi tiết
                            </a> --}}
                            <a href="{{ route('admin.books.index') }}" 
                               class="btn btn-outline-secondary btn-lg">
                                <i class="ri-arrow-left-line me-2"></i>Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('jQuery is loaded and ready!'); // Debug log
    
    // Test if delete buttons exist
    console.log('Delete buttons found:', $('.delete-existing-image').length);
    
    // Initialize Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-authors').select2({
            theme: 'bootstrap-5',
            placeholder: 'Tìm kiếm và chọn tác giả...',
            allowClear: true,
            width: '100%'
        });
    }

    // Toggle format sections
    function toggleFormatSections() {
        const physicalCheckbox = document.getElementById('has_physical');
        const physicalForm = document.getElementById('physical_format');
        const ebookCheckbox = document.getElementById('has_ebook');
        const ebookForm = document.getElementById('ebook_format');
        
        if (physicalCheckbox && physicalForm) {
            physicalForm.style.display = physicalCheckbox.checked ? 'block' : 'none';
        }
        
        if (ebookCheckbox && ebookForm) {
            ebookForm.style.display = ebookCheckbox.checked ? 'block' : 'none';
        }
    }

    // Toggle gift section
    function toggleGiftSection() {
        const giftCheckbox = document.getElementById('has_gift');
        const giftSection = document.getElementById('gift_section');
        
        if (giftCheckbox && giftSection) {
            giftSection.style.display = giftCheckbox.checked ? 'block' : 'none';
        }
    }

    // Event listeners
    const physicalCheckbox = document.getElementById('has_physical');
    const ebookCheckbox = document.getElementById('has_ebook');
    const giftCheckbox = document.getElementById('has_gift');
    
    if (physicalCheckbox) {
        physicalCheckbox.addEventListener('change', toggleFormatSections);
    }
    
    if (ebookCheckbox) {
        ebookCheckbox.addEventListener('change', toggleFormatSections);
    }
    
    if (giftCheckbox) {
        giftCheckbox.addEventListener('change', toggleGiftSection);
    }
    
    // Initial toggle
    toggleFormatSections();
    toggleGiftSection();

    // Image preview
    const coverInput = document.getElementById('cover_image');
    const imagesInput = document.getElementById('images');
    const giftImageInput = document.querySelector('input[name="gift_image"]');
    
    if (coverInput) {
        coverInput.addEventListener('change', function(e) {
            const preview = document.getElementById('cover_preview');
            preview.innerHTML = '';
            
            if (e.target.files[0]) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(e.target.files[0]);
                img.className = 'img-thumbnail';
                img.style.maxHeight = '200px';
                preview.appendChild(img);
            }
        });
    }
    
    if (imagesInput) {
        imagesInput.addEventListener('change', function(e) {
            const preview = document.getElementById('images_preview');
            preview.innerHTML = '';
            
            Array.from(e.target.files).forEach(file => {
                const col = document.createElement('div');
                col.className = 'col-6 mb-2';
                
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-thumbnail w-100';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                
                col.appendChild(img);
                preview.appendChild(col);
            });
        });
    }

    if (giftImageInput) {
        giftImageInput.addEventListener('change', function(e) {
            const preview = document.getElementById('gift_image_preview');
            preview.innerHTML = '';
            
            if (e.target.files[0]) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(e.target.files[0]);
                img.className = 'img-thumbnail';
                img.style.maxHeight = '150px';
                preview.appendChild(img);
            }
        });
    }

    // Handle attribute value addition with event delegation
    $(document).on('click', '.add-attribute-btn', function(e) {
        e.preventDefault();
        console.log('Add attribute button clicked');
        
        const button = $(this);
        const attributeGroup = button.closest('.attribute-group');
        
        if (attributeGroup.length === 0) {
            console.error('Không tìm thấy attribute-group');
            return;
        }
        
        const select = attributeGroup.find('.attribute-select')[0];
        const extraPriceInput = attributeGroup.find('.attribute-extra-price')[0];
        const stockInput = attributeGroup.find('.attribute-stock')[0];
        const selectedValuesContainer = attributeGroup.find('.selected-variants-container')[0];
        
        if (!select || !extraPriceInput || !stockInput || !selectedValuesContainer) {
            console.error('Không tìm thấy các element cần thiết');
            return;
        }
        
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption.value) {
            alert('Vui lòng chọn một giá trị thuộc tính');
            return;
        }
        
        const attributeId = select.getAttribute('data-attribute-id');
        const attributeName = select.getAttribute('data-attribute-name');
        const valueId = selectedOption.value;
        const valueName = selectedOption.getAttribute('data-value-name');
        const extraPrice = parseFloat(extraPriceInput.value) || 0;
        const stock = parseInt(stockInput.value) || 0;
        
        // Kiểm tra xem thuộc tính này đã được thêm chưa
        const existingValue = attributeGroup.find(`input[name="attribute_values[${valueId}][id]"]`);
        if (existingValue.length > 0) {
            alert(`Thuộc tính ${valueName} đã được thêm`);
            return;
        }
        
        // Tạo element hiển thị thuộc tính đã chọn
        const selectedDiv = $(`
            <div class="selected-attribute-value mb-2 p-3 border rounded bg-white shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="fw-medium text-dark mb-1">
                            <i class="ri-bookmark-line me-1 text-primary"></i>${valueName}
                        </div>
                        <div class="small text-muted">
                            <span class="badge bg-success-subtle text-success me-2">
                                <i class="ri-money-dollar-circle-line me-1"></i>+${extraPrice.toLocaleString('vi-VN')}đ
                            </span>
                            <span class="badge bg-info-subtle text-info me-2">
                                <i class="ri-archive-line me-1"></i>${stock} sp
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary">
                                <i class="ri-barcode-line me-1"></i>SKU: Tự động tạo
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-attribute-value">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
                <input type="hidden" name="attribute_values[${valueId}][id]" value="${valueId}">
                <input type="hidden" name="attribute_values[${valueId}][extra_price]" value="${extraPrice}">
                <input type="hidden" name="attribute_values[${valueId}][stock]" value="${stock}">
            </div>
        `);
        
        $(selectedValuesContainer).append(selectedDiv);
        
        // Reset form
        select.selectedIndex = 0;
        extraPriceInput.value = '0';
        stockInput.value = '0';
        
        console.log('Attribute added successfully');
    });
    
    // Handle attribute value removal
    $(document).on('click', '.remove-attribute-value', function(e) {
        e.preventDefault();
        const selectedDiv = $(this).closest('.selected-attribute-value');
        if (selectedDiv.length > 0) {
            selectedDiv.remove();
        }
    });
    
    // Handle existing attribute removal
    $(document).on('click', '.remove-existing-attribute', function(e) {
        e.preventDefault();
        const button = $(this);
        const attributeDiv = button.closest('.d-flex');
        
        if (attributeDiv.length > 0 && confirm('Bạn có chắc chắn muốn xóa thuộc tính này?')) {
            // Thay vì xóa element, đánh dấu để xóa bằng cách set keep = 0
            const keepInput = attributeDiv.find('.keep-attribute');
            if (keepInput.length > 0) {
                keepInput.val('0');
            }
            // Ẩn element thay vì xóa hoàn toàn
            attributeDiv.hide();
        }
    });
    
    // Initialize gift date range picker
    const giftDateRangePicker = document.getElementById('gift_date_range');
    if (giftDateRangePicker) {
        flatpickr(giftDateRangePicker, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            onChange: function(selectedDates, dateStr, instance) {
                const startInput = document.getElementById('gift_start_date');
                const endInput = document.getElementById('gift_end_date');
                if (selectedDates.length === 2) {
                    startInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                    endInput.value = instance.formatDate(selectedDates[1], 'Y-m-d');
                } else {
                    startInput.value = '';
                    endInput.value = '';
                }
            }
        });
    }
    
    // Đảm bảo khi submit form luôn lấy lại giá trị nếu user không chọn lại ngày
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const giftDateRange = document.getElementById('gift_date_range');
            if (giftDateRange && giftDateRange.value && giftDateRange.value.includes(' to ')) {
                const parts = giftDateRange.value.split(' to ');
                document.getElementById('gift_start_date').value = parts[0].trim();
                document.getElementById('gift_end_date').value = parts[1].trim();
            }
        });
    }

    // Image preview functionality
    $('#cover_image').on('change', function() {
        const file = this.files[0];
        const preview = $('#cover_preview');
        preview.empty();
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.html(`<img src="${e.target.result}" class="img-thumbnail mt-2" style="max-height: 200px;">`);
            };
            reader.readAsDataURL(file);
        }
    });

    // Store selected files for manipulation
    let selectedFiles = [];

    $('#images').on('change', function() {
        const files = Array.from(this.files);
        selectedFiles = files;
        updateImagePreview();
    });

    function updateImagePreview() {
        const preview = $('#images_preview');
        preview.empty();
        
        if (selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.append(`
                        <div class="col-6 col-md-4 mb-2" data-index="${index}">
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-image" 
                                        data-index="${index}" style="transform: translate(25%, -25%); border-radius: 50%; width: 25px; height: 25px; padding: 0;">
                                    <i class="ri-close-line" style="font-size: 12px;"></i>
                                </button>
                                <div class="text-center mt-1">
                                    <small class="text-muted">${file.name}</small>
                                </div>
                            </div>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // Handle image removal
    $(document).on('click', '.remove-image', function() {
        const index = parseInt($(this).data('index'));
        selectedFiles.splice(index, 1);
        
        // Update the file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        document.getElementById('images').files = dt.files;
        
        updateImagePreview();
    });

    // New enhanced image deletion logic
    $(document).on('click', '.delete-image-btn', function() {
        const button = $(this);
        const imageId = button.data('image-id');
        const imageItem = $(`#image-item-${imageId}`);
        
        // Show confirmation with SweetAlert2 style (if available) or default confirm
        const confirmMessage = 'Bạn có chắc chắn muốn xóa ảnh này?\nHành động này không thể hoàn tác!';
        
        if (confirm(confirmMessage)) {
            // Disable button and show loading
            button.prop('disabled', true);
            button.html('<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i>');
            
            // Add loading overlay to image item
            imageItem.css('opacity', '0.6');
            
            // Make AJAX request
            $.ajax({
                url: `/admin/books/delete-image/${imageId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Success animation
                        imageItem.addClass('border-success');
                        imageItem.fadeOut(400, function() {
                            $(this).remove();
                            
                            // Show success notification
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Ảnh đã được xóa thành công!');
                            }
                            
                            // Check if no images left
                            if ($('#existing-images .col-6').length === 0) {
                                $('#existing-images').parent().append(
                                    '<div class="text-center text-muted py-3"><i class="ri-image-line me-2"></i>Không có ảnh phụ nào</div>'
                                );
                            }
                        });
                    } else {
                        // Handle error
                        handleDeleteError(button, imageItem, response.message || 'Có lỗi xảy ra khi xóa ảnh');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    handleDeleteError(button, imageItem, 'Không thể kết nối đến server. Vui lòng thử lại!');
                }
            });
        }
    });
    
    // Helper function to handle delete errors
    function handleDeleteError(button, imageItem, message) {
        // Restore button state
        button.prop('disabled', false);
        button.html('<i class="ri-delete-bin-line"></i>');
        
        // Restore image opacity
        imageItem.css('opacity', '1');
        imageItem.addClass('border-danger');
        
        // Show error message
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            alert('Lỗi: ' + message);
        }
        
        // Remove error border after 3 seconds
        setTimeout(() => {
            imageItem.removeClass('border-danger');
        }, 3000);
    }

    // Price discount validation
    function validateDiscountPrice() {
        // Physical format validation
        const physicalPriceInput = document.getElementById('physical_price');
        const physicalDiscountInput = document.getElementById('physical_discount');
        
        if (physicalPriceInput && physicalDiscountInput) {
            function validatePhysicalDiscount() {
                const price = parseFloat(physicalPriceInput.value) || 0;
                const discount = parseFloat(physicalDiscountInput.value) || 0;
                
                if (discount > price) {
                    physicalDiscountInput.setCustomValidity('Giá giảm không được lớn hơn giá bán');
                    physicalDiscountInput.classList.add('is-invalid');
                } else {
                    physicalDiscountInput.setCustomValidity('');
                    physicalDiscountInput.classList.remove('is-invalid');
                }
            }
            
            physicalPriceInput.addEventListener('input', validatePhysicalDiscount);
            physicalDiscountInput.addEventListener('input', validatePhysicalDiscount);
        }
        
        // Ebook format validation
        const ebookPriceInput = document.getElementById('ebook_price');
        const ebookDiscountInput = document.getElementById('ebook_discount');
        
        if (ebookPriceInput && ebookDiscountInput) {
            function validateEbookDiscount() {
                const price = parseFloat(ebookPriceInput.value) || 0;
                const discount = parseFloat(ebookDiscountInput.value) || 0;
                
                if (discount > price) {
                    ebookDiscountInput.setCustomValidity('Giá giảm không được lớn hơn giá bán');
                    ebookDiscountInput.classList.add('is-invalid');
                } else {
                    ebookDiscountInput.setCustomValidity('');
                    ebookDiscountInput.classList.remove('is-invalid');
                }
            }
            
            ebookPriceInput.addEventListener('input', validateEbookDiscount);
            ebookDiscountInput.addEventListener('input', validateEbookDiscount);
        }
    }
    
    // Initialize price validation
    validateDiscountPrice();
});
</script>
@endpush
@endsection