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

    <form action="{{ route('admin.books.update', [$book->id, $book->slug]) }}" method="POST" enctype="multipart/form-data" id="bookForm">
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
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label fw-medium">Mô tả sách</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Nhập mô tả sách...">{{ old('description', $book->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="page_count" class="form-label fw-medium">Số trang</label>
                                <input type="number" class="form-control @error('page_count') is-invalid @enderror" 
                                       id="page_count" name="page_count" value="{{ old('page_count', $book->page_count) }}" 
                                       placeholder="Số trang..." min="1">
                                @error('page_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="publication_date" class="form-label fw-medium">Ngày xuất bản</label>
                                <input type="date" class="form-control @error('publication_date') is-invalid @enderror" 
                                       id="publication_date" name="publication_date" 
                                       value="{{ old('publication_date', $book->publication_date ? $book->publication_date->format('Y-m-d') : '') }}">
                                @error('publication_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                            <div class="invalid-feedback">{{ $message }}</div>
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
                                        <label class="form-label fw-medium">Tên quà tặng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('gift_name') is-invalid @enderror" 
                                               name="gift_name" value="{{ old('gift_name', $currentGift->gift_name ?? '') }}" 
                                               placeholder="Ví dụ: Bookmark đặc biệt, Postcard...">
                                        @error('gift_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Số lượng quà tặng</label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                               name="quantity" value="{{ old('quantity', $currentGift->quantity ?? 1) }}" 
                                               placeholder="1" min="1">
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Mô tả quà tặng</label>
                                        <textarea class="form-control @error('gift_description') is-invalid @enderror" 
                                                  name="gift_description" rows="3" 
                                                  placeholder="Mô tả chi tiết về quà tặng...">{{ old('gift_description', $currentGift->gift_description ?? '') }}</textarea>
                                        @error('gift_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Ngày bắt đầu</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                               name="start_date" value="{{ old('start_date', $currentGift && $currentGift->start_date ? $currentGift->start_date->format('Y-m-d') : '') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Ngày kết thúc</label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                               name="end_date" value="{{ old('end_date', $currentGift && $currentGift->end_date ? $currentGift->end_date->format('Y-m-d') : '') }}">
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                            <div class="invalid-feedback">{{ $message }}</div>
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
                                                   value="{{ old('formats.physical.price', $physicalFormat->price ?? '') }}" 
                                                   placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Giảm giá (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[physical][discount]" 
                                                   value="{{ old('formats.physical.discount', $physicalFormat->discount ?? '') }}" 
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
                                                        // Lấy thuộc tính hiện có của sách cho attribute này
                                                        $bookAttributes = $book->attributeValues->filter(function($attributeValue) use ($attribute) {
                                                            return $attributeValue->attribute_id == $attribute->id;
                                                        });
                                                    @endphp
                                                    
                                                    @if($bookAttributes->count() > 0)
                                                        <div class="mb-3">
                                                            <h6 class="text-success mb-2">Thuộc tính hiện có:</h6>
                                                            @foreach($bookAttributes as $bookAttr)
                                                                <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                                                                    <div>
                                                                        <span class="badge bg-primary me-2">{{ $bookAttr->value ?? 'N/A' }}</span>
                                                                        <small class="text-muted">
                                                                            Giá thêm: {{ number_format($bookAttr->pivot->extra_price ?? 0) }}đ | 
                                                                            Tồn kho: {{ $bookAttr->pivot->stock ?? 0 }}
                                                                        </small>
                                                                    </div>
                                                                    <div>
                                                                        <input type="hidden" name="existing_attributes[{{ $bookAttr->id }}][attribute_value_id]" value="{{ $bookAttr->id }}">
                                                                        <input type="hidden" name="existing_attributes[{{ $bookAttr->id }}][keep]" value="1" class="keep-attribute">
                                                                        <input type="number" name="existing_attributes[{{ $bookAttr->id }}][extra_price]" 
                                                                               value="{{ $bookAttr->pivot->extra_price ?? 0 }}" class="form-control form-control-sm d-inline-block me-2" 
                                                                               style="width: 100px;" placeholder="Giá thêm" min="0">
                                                                        <input type="number" name="existing_attributes[{{ $bookAttr->id }}][stock]" 
                                                                               value="{{ $bookAttr->pivot->stock ?? 0 }}" class="form-control form-control-sm d-inline-block me-2" 
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
                                                   value="{{ old('formats.ebook.price', $ebookFormat->price ?? '') }}" 
                                                   placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Giảm giá (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[ebook][discount]" 
                                                   value="{{ old('formats.ebook.discount', $ebookFormat->discount ?? '') }}" 
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
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không muốn thay đổi ảnh bìa</div>
                            <div id="cover_preview" class="mt-2"></div>
                        </div>
                        
                        <!-- Ảnh phụ -->
                        <div>
                            <label for="images" class="form-label fw-medium">Ảnh phụ</label>
                            @if($book->images->count() > 0)
                                <div class="mb-2">
                                    <div class="row">
                                        @foreach($book->images as $image)
                                            <div class="col-6 mb-2">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                     class="img-thumbnail w-100" style="height: 80px; object-fit: cover;" 
                                                     alt="Ảnh phụ">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="small text-muted">Ảnh phụ hiện tại</div>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                            <div class="invalid-feedback">{{ $message }}</div>
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
                                    <div class="fw-bold text-success">{{ $book->attributeValues->count() }}</div>
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
                            <a href="{{ route('admin.books.show', ['id' => $book->id, 'slug' => $book->slug]) }}" 
                               class="btn btn-outline-info btn-lg">
                                <i class="ri-eye-line me-2"></i>Xem chi tiết
                            </a>
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
});
</script>
@endpush
@endsection
        // Step 3: Media
        if ($('#cover_image').val() || $('#images').val()) {
            $('#step-3').addClass('completed');
        } else if ($('#step-2').hasClass('completed')) {
            $('#step-3').addClass('active');
        }
        
        // Step 4: Publishing
        if ($('#publication_date').val() || $('#publisher').val()) {
            $('#step-4').addClass('completed');
        } else if ($('#step-3').hasClass('completed')) {
            $('#step-4').addClass('active');
        }
    }

    // Enhanced file upload with drag & drop
    function initializeFileUpload() {
        // Upload area click handlers
        $('.upload-area').on('click', function(e) {
            e.preventDefault();
            const targetInput = $(this).data('target');
            $(targetInput).click();
        });

        // Drag & Drop functionality
        $('.upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('border-primary').css('background-color', '#f8f9ff');
        });

        $('.upload-area').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('border-primary').css('background-color', '');
        });

        $('.upload-area').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('border-primary').css('background-color', '');
            
            const files = e.originalEvent.dataTransfer.files;
            const targetInput = $(this).data('target');
            $(targetInput)[0].files = files;
            $(targetInput).trigger('change');
        });

        // Enhanced preview functionality
        $('#cover_image').on('change', function() {
            const file = this.files[0];
            const preview = $('#cover_preview');
            preview.empty();
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.html(`
                        <div class="text-center">
                            <img src="${e.target.result}" class="img-fluid rounded shadow-sm" 
                                 style="max-height: 200px;" alt="Cover preview">
                            <div class="mt-2">
                                <span class="badge bg-success">Ảnh bìa mới</span>
                                <p class="mt-1 mb-0 text-muted small">${file.name}</p>
                            </div>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#images').on('change', function() {
            const files = this.files;
            const preview = $('#images_preview');
            preview.empty();
            
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.append(`
                            <div class="col-6 mb-2">
                                <div class="existing-image">
                                    <img src="${e.target.result}" class="img-fluid rounded shadow-sm" 
                                         style="height: 100px; width: 100%; object-fit: cover;" alt="New image">
                                    <div class="text-center mt-1">
                                        <span class="badge bg-warning text-dark">Mới</span>
                                    </div>
                                </div>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    }

    // Form validation enhancement
    function enhanceFormValidation() {
        $('#bookForm').on('submit', function(e) {
            let isValid = true;
            const requiredFields = ['title', 'category_id'];
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            
            requiredFields.forEach(function(field) {
                const input = $(`#${field}`);
                if (!input.val()) {
                    isValid = false;
                    input.addClass('is-invalid');
                    
                    if (!input.next('.invalid-feedback').length) {
                        input.after('<div class="invalid-feedback">Trường này là bắt buộc</div>');
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
                
                if (typeof toastr !== 'undefined') {
                    toastr.error('Vui lòng điền đầy đủ thông tin bắt buộc', 'Lỗi');
                }
            } else {
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-2"></i>Đang cập nhật...');
                
                // Reset after delay (fallback)
                setTimeout(() => {
                    submitBtn.prop('disabled', false).html(originalText);
                }, 5000);
            }
        });
    }

    // Enhanced hover effects for existing images
    function initializeImageHovers() {
        $('.existing-image').hover(
            function() {
                $(this).find('img').css('transform', 'scale(1.05)');
            },
            function() {
                $(this).find('img').css('transform', 'scale(1)');
            }
        );
    }

    // Auto-resize textareas
    function initializeAutoResize() {
        $('textarea').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Price formatting
    function initializePriceFormatting() {
        $('#price, .attribute-extra-price').on('input', function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(parseInt(value).toLocaleString('vi-VN'));
            }
        });

        $('#price, .attribute-extra-price').on('blur', function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            $(this).val(value);
        });
    }

    // Initialize all enhancements
    updateProgressSteps();
    initializeFileUpload();
    enhanceFormValidation();
    initializeImageHovers();
    initializeAutoResize();
    initializePriceFormatting();

    // Update progress on form changes
    $('input, textarea, select').on('input change', function() {
        updateProgressSteps();
    });

    // Smooth scroll for step navigation
    $('.step').on('click', function() {
        const stepNumber = $(this).attr('id').split('-')[1];
        let targetSection;
        
        switch(stepNumber) {
            case '1':
                targetSection = $('.card').first();
                break;
            case '2':
                targetSection = $('.card').eq(1);
                break;
            case '3':
                targetSection = $('.card').eq(2);
                break;
            case '4':
                targetSection = $('.card').eq(3);
                break;
        }
        
        if (targetSection) {
            $('html, body').animate({
                scrollTop: targetSection.offset().top - 20
            }, 500);
        }
    });

    // Unsaved changes warning
    let hasChanges = false;
    $('input, textarea, select').on('change', function() {
        if (!hasChanges) {
            hasChanges = true;
            $(window).on('beforeunload', function() {
                return 'Bạn có thay đổi chưa được lưu. Bạn có chắc muốn rời khỏi trang?';
            });
        }
    });

    $('#bookForm').on('submit', function() {
        $(window).off('beforeunload');
    });
});

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
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Preview images
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
    
    // Handle attribute value addition
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-attribute-btn')) {
            const button = e.target.closest('.add-attribute-btn');
            const attributeGroup = button.closest('.attribute-group');
            
            if (!attributeGroup) {
                console.error('Không tìm thấy attribute-group');
                return;
            }
            
            const select = attributeGroup.querySelector('.attribute-select');
            const extraPriceInput = attributeGroup.querySelector('.attribute-extra-price');
            const stockInput = attributeGroup.querySelector('.attribute-stock');
            const selectedValuesContainer = attributeGroup.querySelector('.selected-variants-container');
            
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
            const existingValue = attributeGroup.querySelector(`input[name="attribute_values[${valueId}][id]"]`);
            if (existingValue) {
                alert(`Thuộc tính ${valueName} đã được thêm`);
                return;
            }
            
            // Tạo element hiển thị thuộc tính đã chọn
            const selectedDiv = document.createElement('div');
            selectedDiv.className = 'selected-attribute-value mb-2 p-3 border rounded bg-white shadow-sm';
            selectedDiv.innerHTML = `
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
            `;
            
            selectedValuesContainer.appendChild(selectedDiv);
            
            // Reset form
            select.selectedIndex = 0;
            extraPriceInput.value = '0';
            stockInput.value = '0';
        }
        
        // Handle attribute value removal
        if (e.target.closest('.remove-attribute-value')) {
            const button = e.target.closest('.remove-attribute-value');
            const selectedDiv = button.closest('.selected-attribute-value');
            if (selectedDiv) {
                selectedDiv.remove();
            }
        }
        
        // Handle existing attribute removal
        if (e.target.closest('.remove-existing-attribute')) {
            const button = e.target.closest('.remove-existing-attribute');
            const attributeDiv = button.closest('.d-flex');
            if (attributeDiv && confirm('Bạn có chắc chắn muốn xóa thuộc tính này?')) {
                // Thay vì xóa element, đánh dấu để xóa bằng cách set keep = 0
                const keepInput = attributeDiv.querySelector('.keep-attribute');
                if (keepInput) {
                    keepInput.value = '0';
                }
                // Ẩn element thay vì xóa hoàn toàn
                attributeDiv.style.display = 'none';
            }
        }
    });
});

// Initialize Select2
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-authors').select2({
            theme: 'bootstrap-5',
            placeholder: 'Tìm kiếm và chọn tác giả...',
            allowClear: true,
            width: '100%'
        });
    }
});
</script>
@endpush
@endsection