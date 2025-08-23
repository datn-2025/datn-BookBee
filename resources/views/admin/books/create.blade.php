@extends('layouts.backend')

@section('title', 'Thêm sách mới')

@section('content')
<div class="container-fluid">
    <!-- Clean Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-semibold">
                <i class="ri-book-add-line me-2 text-primary"></i>Thêm sách mới
            </h1>
            <p class="text-muted mb-0">Tạo sản phẩm sách mới cho cửa hàng</p>
        </div>
        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-2"></i>Quay lại
        </a>
    </div>

    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" id="bookForm">
        @csrf
        <div class="row ">
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
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Nhập tên sách...">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                                          placeholder="Nhập mô tả sách...">{{ old('description') }}</textarea>
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
                                <label for="isbn" class="form-label fw-medium">Mã ISBN</label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" name="isbn" value="{{ old('isbn') }}" 
                                       placeholder="Mã ISBN...">
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="page_count" class="form-label fw-medium">Số trang</label>
                                <input type="number" class="form-control @error('page_count') is-invalid @enderror" 
                                       id="page_count" name="page_count" value="{{ old('page_count') }}" 
                                       placeholder="Số trang..." min="1">
                                @error('page_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="publication_date" class="form-label fw-medium">Ngày xuất bản</label>
                                <input type="date" class="form-control @error('publication_date') is-invalid @enderror" 
                                       id="publication_date" name="publication_date" value="{{ old('publication_date') }}">
                                @error('publication_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="language" class="form-label fw-medium">Ngôn ngữ</label>
                                <select class="form-select @error('language') is-invalid @enderror" 
                                        id="language" name="language">
                                    <option value="">-- Chọn ngôn ngữ --</option>
                                    <option value="Tiếng Việt" {{ old('language') == 'Tiếng Việt' ? 'selected' : '' }}>Tiếng Việt</option>
                                    <option value="Tiếng Anh" {{ old('language') == 'Tiếng Anh' ? 'selected' : '' }}>Tiếng Anh</option>
                                    <option value="Tiếng Trung" {{ old('language') == 'Tiếng Trung' ? 'selected' : '' }}>Tiếng Trung</option>
                                    <option value="Tiếng Nhật" {{ old('language') == 'Tiếng Nhật' ? 'selected' : '' }}>Tiếng Nhật</option>
                                    <option value="Tiếng Hàn" {{ old('language') == 'Tiếng Hàn' ? 'selected' : '' }}>Tiếng Hàn</option>
                                </select>
                                @error('language')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Preorder Section -->
                            <div class="col-12">
                                <div class="card bg-light border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="ri-bookmark-line me-2"></i>Cấu hình đặt trước sách
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Allow Preorder -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="pre_order" 
                                                   name="pre_order" value="1" {{ old('pre_order') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="pre_order">
                                                Cho phép đặt trước sách
                                            </label>
                                            <div class="form-text">
                                                Kích hoạt để khách hàng có thể đặt trước sách này trước ngày ra mắt
                                            </div>
                                        </div>
                                        
                                        <div id="preorder_section" style="display: none;">
                                            <style>
                                                #preorder_section .col-md-6 {
                                                    width: 50% !important;
                                                    flex: 0 0 auto !important;
                                                }
                                                #preorder_section .row {
                                                    display: flex !important;
                                                    flex-wrap: wrap !important;
                                                }
                                            </style>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="release_date" class="form-label fw-medium">Ngày ra mắt <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control @error('release_date') is-invalid @enderror" 
                                                           id="release_date" name="release_date" value="{{ old('release_date') }}">
                                                    @error('release_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="stock_preorder_limit" class="form-label fw-medium">Số lượng cho phép đặt trước <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('stock_preorder_limit') is-invalid @enderror" 
                                                           id="stock_preorder_limit" name="stock_preorder_limit" value="{{ old('stock_preorder_limit') }}" 
                                                           placeholder="Ví dụ: 100" min="1" step="1">
                                                    @error('stock_preorder_limit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="pre_order_price" class="form-label fw-medium">Giá ưu đãi đặt trước</label>
                                                    <input type="number" class="form-control @error('pre_order_price') is-invalid @enderror" 
                                                           id="pre_order_price" name="pre_order_price" value="{{ old('pre_order_price') }}" 
                                                           min="0" step="1000" placeholder="Ví dụ: 150000">
                                                    @error('pre_order_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="preorder_count" class="form-label fw-medium">Số lượng đã đặt trước</label>
                                                    <input type="number" class="form-control" 
                                                           id="preorder_count" name="preorder_count" value="0" 
                                                           placeholder="0" readonly>
                                                </div>
                                                <div class="col-12">
                                                    <label for="preorder_description" class="form-label fw-medium">Mô tả ưu đãi đặt trước</label>
                                                    <textarea class="form-control @error('preorder_description') is-invalid @enderror" 
                                                              id="preorder_description" name="preorder_description" rows="3" 
                                                              placeholder="Mô tả về ưu đãi hoặc thông tin đặc biệt cho khách đặt trước (ví dụ: Tặng kèm bookmark, giảm 20%, giao hàng miễn phí...)">{{ old('preorder_description') }}</textarea>
                                                    @error('preorder_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    {{ in_array($author->id, old('author_ids', [])) ? 'selected' : '' }}>
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
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="has_gift" name="has_gift" 
                                   value="1" {{ old('has_gift') ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="has_gift">
                                Sách có kèm quà tặng
                            </label>
                        </div>
                        
                        <div id="gift_section" style="display: none;">
                            <div class="p-3 bg-light rounded border">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Chọn sách nhận quà tặng</label>
                                        <select class="form-select @error('gift_book_id') is-invalid @enderror" 
                                                name="gift_book_id">
                                            <option value="" selected>Sách hiện tại (đang tạo)</option>
                                            @foreach($books ?? [] as $book)
                                                <option value="{{ $book->id }}" 
                                                    {{ old('gift_book_id') == $book->id ? 'selected' : '' }}>
                                                    {{ $book->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('gift_book_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Để trống để tạo quà tặng cho sách hiện tại</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Tên quà tặng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('gift_name') is-invalid @enderror" 
                                               name="gift_name" value="{{ old('gift_name') }}" 
                                               placeholder="Ví dụ: Bookmark đặc biệt, Postcard...">
                                        @error('gift_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Số lượng quà tặng</label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                               name="quantity" value="{{ old('quantity', 1) }}" 
                                               placeholder="1" min="1">
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Mô tả quà tặng</label>
                                        <textarea class="form-control @error('gift_description') is-invalid @enderror" 
                                                  name="gift_description" rows="3" 
                                                  placeholder="Mô tả chi tiết về quà tặng...">{{ old('gift_description') }}</textarea>
                                        @error('gift_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Thời gian khuyến mãi quà tặng</label>
                                        <input type="text" class="form-control @error('gift_date_range') is-invalid @enderror" 
                                               id="gift_date_range" name="gift_date_range" 
                                               placeholder="Chọn khoảng thời gian khuyến mãi..." 
                                               value="{{ old('gift_date_range') }}">
                                        
                                        <!-- Hidden inputs để lưu giá trị ngày -->
                                        <input type="hidden" id="gift_start_date" name="gift_start_date" value="{{ old('gift_start_date') }}">
                                        <input type="hidden" id="gift_end_date" name="gift_end_date" value="{{ old('gift_end_date') }}">
                                        
                                        @error('gift_date_range')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('gift_start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('gift_end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        
                                        <div class="form-text">
                                            <i class="ri-information-line me-1"></i>
                                            Chọn khoảng thời gian áp dụng khuyến mãi quà tặng. Nhấp vào ô để chọn ngày bắt đầu và kết thúc.
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Hình ảnh quà tặng</label>
                                        <input type="file" class="form-control @error('gift_image') is-invalid @enderror" 
                                               name="gift_image" accept="image/*">
                                        @error('gift_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Chấp nhận file ảnh JPG, PNG, GIF. Tối đa 2MB</div>
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
                            <i class="ri-book-open-line me-2 text-primary"></i>Định dạng & Giá bán
                        </h5>
                    </div>
                    <div class="card-body">
                        @error('format_required')
                            <div class="alert alert-danger mb-3">
                                <i class="ri-error-warning-line me-2"></i>{{ $message }}
                            </div>
                        @enderror
                        
                        <!-- Sách vật lý -->
                        <div class="mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="has_physical" name="has_physical" 
                                       value="1" {{ old('has_physical') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="has_physical">
                                    <i class="ri-book-line me-1"></i>Sách vật lý
                                </label>
                            </div>
                            
                            <div id="physical_format" style="display: none;">
                                <div class="border rounded p-3 bg-light">
                                    <!-- Thông tin cơ bản sách vật lý -->
                                    <div class="row g-3 mb-4" id="physical_price_section">
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Giá bán (VNĐ)</label>
                                            <input type="number" class="form-control format-price" name="formats[physical][price]" 
                                                   value="{{ old('formats.physical.price') }}" placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Giảm giá (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[physical][discount]" 
                                                   id="physical_discount" value="{{ old('formats.physical.discount') }}" placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Số lượng</label>
                                            <input type="number" class="form-control" name="formats[physical][stock]" 
                                                   value="{{ old('formats.physical.stock') }}" placeholder="0" min="0">
                                        </div>
                                    </div>
                                    
                                    <!-- Thông báo khi bật preorder -->
                                    <div class="alert alert-info preorder-price-notice" style="display: none;">
                                        <i class="ri-information-line me-2"></i>
                                        <strong>Chế độ đặt trước:</strong> Khi bật chế độ đặt trước, giá bán sẽ được sử dụng từ "Giá ưu đãi đặt trước" đã cấu hình ở phần trên thay vì giá định dạng này.
                                    </div>
                                    
                                    <!-- Thuộc tính sách vật lý -->
                                    <div class="border-top pt-4">
                                        <h6 class="fw-bold text-purple mb-3">
                                            <i class="ri-price-tag-3-line me-2"></i>Thuộc tính sách vật lý
                                        </h6>
                                        
                                        <div class="mb-3">
                                            <div class="alert alert-info border-0" style="background-color: #e3f2fd; border-left: 4px solid #2196f3 !important;">
                                                <div class="d-flex align-items-start">
                                                    <i class="ri-information-line me-2 mt-1" style="color: #1976d2; font-size: 18px;"></i>
                                                    <div>
                                                        <h6 class="mb-1" style="color: #1976d2; font-weight: 600;">Thuộc tính biến thể</h6>
                                                        <p class="mb-0" style="color: #1976d2; font-size: 13px;">
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
                                                    
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-medium">Chọn giá trị</label>
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
                                                    
                                                    <!-- Container hiển thị các thuộc tính đã chọn -->
                                                    <div class="selected-variants-container" data-attribute-id="{{ $attribute->id }}">
                                                        <!-- Các thuộc tính đã chọn sẽ hiển thị ở đây -->
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
                                       value="1" {{ old('has_ebook') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="has_ebook">
                                    <i class="ri-file-text-line me-1"></i>Sách điện tử (Ebook)
                                </label>
                            </div>
                            
                            <div id="ebook_format" style="display: none;">
                                <div class="border rounded p-3 bg-light">
                                    <div class="row g-3" id="ebook_price_section">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Giá bán (VNĐ)</label>
                                            <input type="number" class="form-control format-price" name="formats[ebook][price]" 
                                                   value="{{ old('formats.ebook.price') }}" placeholder="0" min="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Giảm giá (VNĐ)</label>
                                            <input type="number" class="form-control" name="formats[ebook][discount]" 
                                                   value="{{ old('formats.ebook.discount') }}" placeholder="0" min="0">
                                        </div>
                                    </div>
                                    
                                    <!-- Thông báo khi bật preorder -->
                                    <div class="alert alert-info preorder-price-notice" style="display: none;">
                                        <i class="ri-information-line me-2"></i>
                                        <strong>Chế độ đặt trước:</strong> Khi bật chế độ đặt trước, giá bán sẽ được sử dụng từ "Giá ưu đãi đặt trước" đã cấu hình ở phần trên thay vì giá định dạng này.
                                    </div>
                                        <div class="col-12">
                                            <label class="form-label fw-medium">File Ebook</label>
                                            <input type="file" class="form-control" name="formats[ebook][file]" 
                                                   accept=".pdf,.epub">
                                            <div class="form-text">Chấp nhận file PDF hoặc EPUB, tối đa 50MB</div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label class="form-label fw-medium">File đọc thử</label>
                                            <input type="file" class="form-control" name="formats[ebook][sample_file]" 
                                                   accept=".pdf,.epub">
                                            <div class="form-text">File đọc thử cho khách hàng. Chấp nhận file PDF hoặc EPUB, tối đa 10MB.</div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="allow_sample_read_create" 
                                                       name="formats[ebook][allow_sample_read]" value="1" 
                                                       {{ old('formats.ebook.allow_sample_read') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_sample_read_create">
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
                            <label for="cover_image" class="form-label fw-medium">
                                Ảnh bìa <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                   id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="cover_preview" class="mt-3"></div>
                        </div>
                        
                        <!-- Ảnh phụ -->
                        <div>
                            <label for="images" class="form-label fw-medium">Ảnh phụ</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="images_preview" class="row mt-3"></div>
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
                        <select class="form-select @error('status') is-invalid @enderror"  name="status">
                            <option value="Còn Hàng" {{ old('status') == 'Còn Hàng' ? 'selected' : '' }}>Còn Hàng</option>
                            {{-- <option value="Hết Hàng Tồn Kho" {{ old('status') == 'Hết Hàng Tồn Kho' ? 'selected' : '' }}>Hết Hàng Tồn Kho</option> --}}
                            <option value="Sắp Ra Mắt" {{ old('status') == 'Sắp Ra Mắt' ? 'selected' : '' }}>Sắp Ra Mắt</option>
                            <option value="Ngừng Kinh Doanh" {{ old('status') == 'Ngừng Kinh Doanh' ? 'selected' : '' }}>Ngừng Kinh Doanh</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ri-save-line me-2"></i>Lưu sách mới
                            </button>
                            <button type="button" class="btn btn-outline-info btn-lg">
                                <i class="ri-eye-line me-2"></i>Xem trước
                            </button>
                            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="ri-close-line me-2"></i>Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Clean and Simple Styling */
    .card {
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-radius: 0.35rem;
    }
    
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .form-label.fw-medium {
        color: #5a5c69;
        font-weight: 600;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    #cover_preview img, #images_preview img {
        max-width: 100%;
        height: auto;
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
    }
    
    .btn {
        border-radius: 0.35rem;
        font-weight: 500;
    }
    
    .text-primary { color: #4e73df !important; }
    .text-info { color: #36b9cc !important; }
    .text-success { color: #1cc88a !important; }
    .text-warning { color: #f6c23e !important; }
    .text-secondary { color: #858796 !important; }
</style>
@endpush

@push('scripts')
<script>
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
    
    // Call preorder toggle to update price sections
    togglePreorderSection();
}

// Toggle gift section
function toggleGiftSection() {
    const giftCheckbox = document.getElementById('has_gift');
    const giftSection = document.getElementById('gift_section');
    
    if (giftCheckbox && giftSection) {
        giftSection.style.display = giftCheckbox.checked ? 'block' : 'none';
    }
}

// Preorder section toggle
function togglePreorderSection() {
    const preOrderCheckbox = document.getElementById('pre_order');
    const preorderSection = document.getElementById('preorder_section');
    const releaseDateInput = document.getElementById('release_date');
    const stockPreorderLimitInput = document.getElementById('stock_preorder_limit');
    const physicalPriceSection = document.getElementById('physical_price_section');
    const ebookPriceSection = document.getElementById('ebook_price_section');
    const preorderPriceNotices = document.querySelectorAll('.preorder-price-notice');
    const physicalCheckbox = document.getElementById('has_physical');
    const ebookCheckbox = document.getElementById('has_ebook');
    
    if (!preOrderCheckbox || !preorderSection) {
        return;
    }
    
    if (preOrderCheckbox.checked) {
        preorderSection.style.display = 'block';
        
        // Make required fields required
        if (releaseDateInput) releaseDateInput.required = true;
        if (stockPreorderLimitInput) stockPreorderLimitInput.required = true;
        
        // Set minimum date to today
        if (releaseDateInput) {
            const today = new Date().toISOString().split('T')[0];
            releaseDateInput.min = today;
        }
        
        // Hide price sections and show notices only for enabled formats
        if (physicalPriceSection && physicalCheckbox && physicalCheckbox.checked) {
            physicalPriceSection.style.display = 'none';
        }
        if (ebookPriceSection && ebookCheckbox && ebookCheckbox.checked) {
            ebookPriceSection.style.display = 'none';
        }
        
        // Show preorder notices for enabled formats
        preorderPriceNotices.forEach(notice => {
            const parentCard = notice.closest('.border');
            if (parentCard) {
                // Check if this is physical format notice
                if (parentCard.closest('#physical_format') && physicalCheckbox && physicalCheckbox.checked) {
                    notice.style.display = 'block';
                }
                // Check if this is ebook format notice
                else if (parentCard.closest('#ebook_format') && ebookCheckbox && ebookCheckbox.checked) {
                    notice.style.display = 'block';
                }
            }
        });
        
        // Set default stock limit if empty
        if (stockPreorderLimitInput && !stockPreorderLimitInput.value) {
            stockPreorderLimitInput.value = 100;
        }
    } else {
        preorderSection.style.display = 'none';
        
        // Remove required from fields
        if (releaseDateInput) {
            releaseDateInput.required = false;
            releaseDateInput.value = '';
        }
        if (stockPreorderLimitInput) {
            stockPreorderLimitInput.required = false;
            stockPreorderLimitInput.value = '';
        }
        
        // Show price sections and hide notices for enabled formats
        if (physicalPriceSection && physicalCheckbox && physicalCheckbox.checked) {
            physicalPriceSection.style.display = 'block';
        }
        if (ebookPriceSection && ebookCheckbox && ebookCheckbox.checked) {
            ebookPriceSection.style.display = 'block';
        }
        
        preorderPriceNotices.forEach(notice => {
            notice.style.display = 'none';
        });
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const physicalCheckbox = document.getElementById('has_physical');
    const ebookCheckbox = document.getElementById('has_ebook');
    const giftCheckbox = document.getElementById('has_gift');
    const preOrderCheckbox = document.getElementById('pre_order');
    
    // Add event listeners
    if (physicalCheckbox) {
        physicalCheckbox.addEventListener('change', toggleFormatSections);
    }
    
    if (ebookCheckbox) {
        ebookCheckbox.addEventListener('change', toggleFormatSections);
    }
    
    if (giftCheckbox) {
        giftCheckbox.addEventListener('change', toggleGiftSection);
    }
    
    if (preOrderCheckbox) {
        preOrderCheckbox.addEventListener('change', togglePreorderSection);
    }
    
    // Initial toggle
    toggleFormatSections();
    toggleGiftSection();
    togglePreorderSection();
    
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
    });

    // Initialize gift date range picker
    const giftDateRangePicker = document.getElementById('gift_date_range');
    if (giftDateRangePicker && typeof flatpickr !== 'undefined') {
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
});

// Initialize Select2 and other jQuery-dependent code
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
});

</script>
@endpush
@endsection