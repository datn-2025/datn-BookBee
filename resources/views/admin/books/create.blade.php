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

    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" id="bookForm" novalidate>
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
                                    <div class="text-danger small mt-1">{{ $message }}</div>
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
                                    <div class="text-danger small mt-1">{{ $message }}</div>
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
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label fw-medium">Mô tả sách</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4"
                                          placeholder="Nhập mô tả sách...">{{ old('description') }}</textarea>
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
                                <label for="isbn" class="form-label fw-medium">Mã ISBN</label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" name="isbn" value="{{ old('isbn') }}" 
                                       placeholder="Mã ISBN...">
                                @error('isbn')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="page_count" class="form-label fw-medium">Số trang</label>
                                <input type="number" class="form-control @error('page_count') is-invalid @enderror" 
                                       id="page_count" name="page_count" value="{{ old('page_count') }}" 
                                       placeholder="Số trang..." min="1">
                                @error('page_count')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="publication_date" class="form-label fw-medium">Ngày xuất bản</label>
                                <input type="date" class="form-control @error('publication_date') is-invalid @enderror" 
                                       id="publication_date" name="publication_date" value="{{ old('publication_date') }}">
                                @error('publication_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
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
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('language')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="author_ids" class="form-label fw-medium mb-0">Chọn tác giả</label>
                            <span class="badge bg-primary-subtle text-primary" id="author_selected_badge" style="display:none;">
                                0 đã chọn
                            </span>
                        </div>
                        <select class="form-select select2-authors @error('author_ids') is-invalid @enderror" 
                                id="author_ids" name="author_ids[]" multiple>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" 
                                    {{ in_array($author->id, old('author_ids', [])) ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text mt-2">
                            Gõ để tìm tác giả, Enter để chọn. Có thể chọn nhiều tác giả.
                        </div>
                        @error('author_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('author_ids.*')
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
                                            <option value="" {{ old('gift_book_id') == '' ? 'selected' : '' }}>Sách hiện tại (đang tạo)</option>
                                            @foreach($books ?? [] as $book)
                                                <option value="{{ $book->id }}" 
                                                    {{ old('gift_book_id') == $book->id ? 'selected' : '' }}>
                                                    {{ $book->title }}
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
                                               name="gift_name" value="{{ old('gift_name') }}" 
                                               placeholder="Ví dụ: Bookmark đặc biệt, Postcard...">
                                        @error('gift_name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Số lượng quà tặng</label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                               name="quantity" value="{{ old('quantity') }}" 
                                               placeholder="0" min="1" step="1">
                                        @error('quantity')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Mô tả quà tặng</label>
                                        <textarea class="form-control @error('gift_description') is-invalid @enderror" 
                                                  name="gift_description" rows="3" 
                                                  placeholder="Mô tả chi tiết về quà tặng...">{{ old('gift_description') }}</textarea>
                                        @error('gift_description')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Thời gian khuyến mãi quà tặng</label>
                                        <input type="text" class="form-control @error('gift_date_range') is-invalid @enderror @error('gift_start_date') is-invalid @enderror @error('gift_end_date') is-invalid @enderror" 
                                               id="gift_date_range" name="gift_date_range" 
                                               placeholder="Chọn khoảng thời gian khuyến mãi..." 
                                               value="{{ old('gift_date_range') }}">
                                        
                                        <!-- Hidden inputs để lưu giá trị ngày -->
                                        <input type="hidden" id="gift_start_date" name="gift_start_date" value="{{ old('gift_start_date') }}">
                                        <input type="hidden" id="gift_end_date" name="gift_end_date" value="{{ old('gift_end_date') }}">
                                        
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
                                        <input type="file" class="form-control @error('gift_image') is-invalid @enderror" 
                                               name="gift_image" accept="image/*">
                                        @error('gift_image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
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
                            <div class="text-danger small mt-1">
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
                                            @error('formats.physical.discount')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Số lượng (tự động)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-archive-line"></i></span>
                                                <input type="text" class="form-control" id="total_variant_stock_display" value="0" readonly>
                                            </div>
                                            <div class="form-text">Tổng số lượng = tổng tồn kho của tất cả biến thể</div>
                                            <input type="hidden" name="formats[physical][stock]" id="total_variant_stock_hidden" value="0">
                                        </div>
                                    </div>
                                    
                                    <!-- Thuộc tính sách vật lý -->
                                    <div class="border-top pt-4">
                                        <h6 class="fw-bold text-purple mb-3">
                                            <i class="ri-price-tag-3-line me-2"></i>Thuộc tính sách vật lý
                                        </h6>
                                        
                                        <div class="mb-2">
                                            <div class="alert alert-info py-2 mb-2 border-0" style="background-color: #e9f5ff; border-left: 3px solid #2196f3 !important;">
                                                <div class="d-flex align-items-start">
                                                    <i class="ri-information-line me-2 mt-1" style="color: #1976d2; font-size: 16px;"></i>
                                                    <div class="small" style="color: #1976d2;">
                                                        <div class="fw-semibold">Thuộc tính biến thể</div>
                                                        <div>Các thuộc tính như màu sắc, kích thước, loại bìa sẽ tạo ra biến thể với giá và tồn kho riêng.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($attributes->count() > 0)
                                        <div class="row g-3 mb-4">
                                            @foreach($attributes as $attribute)
                                                <div class="attribute-group mb-4 p-3 border col-md-4 rounde">
                                                    <h6 class="fw-bold text-primary mb-3">
                                                        <i class="ri-bookmark-line me-1"></i>{{ $attribute->name }}
                                                    </h6>
                                                    
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-md-10">
                                                            {{-- <label class="form-label fw-medium">Chọn giá trị</label> --}}
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
                                                        
                                                        <div class="col-md-2">
                                                            {{-- <label class="form-label fw-medium">&nbsp;</label> --}}
                                                            <button type="button" class="btn btn-primary d-block add-attribute-btn btn-sm">
                                                                <i class="ri-add-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Container hiển thị các thuộc tính đã chọn -->
                                                    <div class="selected-variants-container d-flex flex-wrap gap-2" data-attribute-id="{{ $attribute->id }}">
                                                        <!-- Các thuộc tính đã chọn sẽ hiển thị ở đây -->
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="ri-price-tag-3-line text-muted" style="font-size: 48px;"></i>
                                                <p class="text-muted mt-2">Chưa có thuộc tính nào được tạo.</p>
                                                <small class="text-muted">Vui lòng tạo thuộc tính trong phần quản lý thuộc tính trước.</small>
                                            </div>
                                        @endif
                                        <!-- Biến thể (tổ hợp thuộc tính) -->
                                        <div class="border-top pt-4 mt-3">
                                            <h6 class="fw-bold text-purple mb-2">
                                                <i class="ri-shape-line me-2"></i>Biến thể (tổ hợp thuộc tính)
                                            </h6>
                                            <p class="text-muted small mb-3">
                                                Dựa trên các giá trị thuộc tính đã chọn ở trên, nhấn
                                                <span class="fw-semibold">"Tạo tổ hợp biến thể"</span> để sinh các biến thể với SKU, giá thêm và tồn kho riêng.
                                            </p>
                                            <button type="button" class="btn btn-outline-primary" id="generate_variants_btn">
                                                <i class="ri-magic-line me-1"></i>Tạo tổ hợp biến thể
                                            </button>
                                            <div class="table-responsive mt-3" id="variants_section" style="display: none;">
                                                <table class="table table-bordered align-middle" id="variants_table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 45%">Thuộc tính</th>
                                                            <th style="width: 20%">SKU</th>
                                                            <th style="width: 15%">Giá+</th>
                                                            <th style="width: 15%">SL</th>
                                                            <th style="width: 5%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="variants_tbody">
                                                        <!-- Hàng biến thể sẽ được thêm bằng JS -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
                                            @error('formats.ebook.file')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Chấp nhận file PDF hoặc EPUB, tối đa 50MB</div>
                                        </div>
                                        
                                        <div class="col-12" id="ebook_sample_section" style="display: none;">
                                            <label class="form-label fw-medium">File đọc thử</label>
                                            <input type="file" class="form-control" name="formats[ebook][sample_file]" 
                                                   accept=".pdf,.epub">
                                            @error('formats.ebook.sample_file')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
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
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div id="cover_preview" class="mt-3"></div>
                        </div>
                        
                        <!-- Ảnh phụ -->
                        <div>
                            <label for="images" class="form-label fw-medium">Ảnh phụ</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            @error('images')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="text-danger small mt-1">{{ $message }}</div>
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
                            <div class="text-danger small mt-1">{{ $message }}</div>
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
    /* Select2 authors - look & feel giống ảnh mẫu */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px;
        border-color: #cfd8e3;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 42px;
        padding: 2px 2px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        padding: 2px 6px;
    }
    /* Chips đơn giản, nhẹ */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: #eef2f7;
        border: 1px solid #dbe2ea;
        color: #1f2937;
        border-radius: 6px;
        padding: 0.15rem 0.5rem;
        margin-top: 4px;
        line-height: 1.4;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: #526581;
        margin-right: 6px;
    }
    .select2-container--bootstrap-5 .select2-search--inline .select2-search__field { 
        margin-top: 6px; 
    }
    /* Dropdown bo góc + đổ bóng nhẹ */
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 8px;
        border-color: #d1d9e6;
        box-shadow: 0 6px 18px rgba(16, 24, 40, 0.08);
        overflow: hidden;
    }
    /* Item hover/highlight xanh đậm như ảnh */
    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background-color: #2c4775 !important; /* xanh đậm */
        color: #fff !important;
    }
    /* Item đã chọn nhấn nhẹ */
    .select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
        background-color: #f1f5f9;
        color: #334155;
    }
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
            const selectedValuesContainer = attributeGroup.querySelector('.selected-variants-container');
            
            if (!select || !selectedValuesContainer) {
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
            
            // Kiểm tra xem thuộc tính này đã được thêm chưa
            const existingValue = attributeGroup.querySelector(`input[name="attribute_values[${valueId}][id]"]`);
            if (existingValue) {
                alert(`Thuộc tính ${valueName} đã được thêm`);
                return;
            }
            
            // Tạo element hiển thị thuộc tính đã chọn
            const selectedDiv = document.createElement('div');
            selectedDiv.className = 'selected-attribute-value mb-2 p-3 border rounded bg-white shadow-sm';
            // Lưu dữ liệu để sinh tổ hợp biến thể
            selectedDiv.dataset.attributeId = attributeId;
            selectedDiv.dataset.valueId = valueId;
            selectedDiv.dataset.valueName = valueName;
            // Không còn lưu giá thêm & số lượng ở cấp giá trị đơn lẻ
            selectedDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="fw-medium text-dark mb-1">
                            <i class="ri-bookmark-line me-1 text-primary"></i>${valueName}
                        </div>
                        <div class="small text-muted">
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
            `;
            
            selectedValuesContainer.appendChild(selectedDiv);
            
            // Reset form
            select.selectedIndex = 0;
        }
        
        // Handle attribute value removal
        if (e.target.closest('.remove-attribute-value')) {
            const button = e.target.closest('.remove-attribute-value');
            const selectedDiv = button.closest('.selected-attribute-value');
            if (selectedDiv) {
                selectedDiv.remove();
            }
        }

        // Xóa một hàng biến thể
        if (e.target.closest('.remove-variant-row')) {
            const btn = e.target.closest('.remove-variant-row');
            const tr = btn.closest('tr');
            if (tr) tr.remove();
            updateTotalVariantStock();
        }
    });
    
    // Sự kiện tạo tổ hợp biến thể
    const generateBtn = document.getElementById('generate_variants_btn');
    if (generateBtn) {
        generateBtn.addEventListener('click', generateVariantsFromSelected);
    }
    
    // Sinh tổ hợp biến thể từ các thuộc tính đã chọn
    function generateVariantsFromSelected() {
        const attributeGroups = Array.from(document.querySelectorAll('.attribute-group'));
        const attributeSets = [];
        const attributeLabels = []; // Lưu tên thuộc tính để hiển thị
        
        attributeGroups.forEach(group => {
            const attributeId = group.querySelector('.attribute-select')?.getAttribute('data-attribute-id');
            const attributeName = group.querySelector('.attribute-select')?.getAttribute('data-attribute-name');
            const selected = Array.from(group.querySelectorAll('.selected-attribute-value'));
            if (selected.length > 0 && attributeId) {
                attributeLabels.push(attributeName || 'Thuộc tính');
                attributeSets.push(selected.map(el => ({
                    attributeId: el.dataset.attributeId,
                    valueId: el.dataset.valueId,
                    valueName: el.dataset.valueName,
                })));
            }
        });
        
        if (attributeSets.length === 0) {
            alert('Vui lòng chọn ít nhất 1 giá trị thuộc tính trước khi tạo tổ hợp biến thể.');
            return;
        }
        
        const combos = cartesian(attributeSets);
        const tbody = document.getElementById('variants_tbody');
        const section = document.getElementById('variants_section');
        if (!tbody || !section) return;
        
        // Xóa bảng cũ và hiển thị bảng
        tbody.innerHTML = '';
        section.style.display = '';
        
        combos.forEach((combo, idx) => {
            // Nhãn hiển thị: "Thuộc tính: Giá trị | ..."
            const label = combo.map((v, i) => `${attributeLabels[i]}: ${v.valueName}`).join(' | ');
            const sumExtra = 0; // Không dùng giá thêm ở cấp giá trị đơn lẻ
            const stockDefault = 0; // Không dùng tồn kho ở cấp giá trị đơn lẻ
            const skuSuffix = combo.map(v => slugifyForSku(v.valueName)).join('-').toUpperCase();
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-medium">${label}</div>
                    ${combo.map(v => `<input type=\"hidden\" name=\"variants[${idx}][attribute_value_ids][]\" value=\"${v.valueId}\">`).join('')}
                </td>
                <td>
                    <input type="text" class="form-control" name="variants[${idx}][sku]" placeholder="SKU tùy chọn" value="${skuSuffix}">
                </td>
                <td>
                    <input type="number" class="form-control" name="variants[${idx}][extra_price]" min="0" value="${sumExtra}">
                </td>
                <td>
                    <input type="number" class="form-control" name="variants[${idx}][stock]" min="0" value="${stockDefault}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-row"><i class="ri-delete-bin-line"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        // Cập nhật tổng tồn kho sau khi sinh bảng biến thể
        updateTotalVariantStock();
    }
    
    // Lắng nghe thay đổi số lượng của từng biến thể để cập nhật tổng
    document.addEventListener('input', function(e) {
        const target = e.target;
        if (target.matches('input[name^="variants"][name$="[stock]"]')) {
            updateTotalVariantStock();
        }
    });
    
    // Helper: Cartesian product
    function cartesian(arr) {
        return arr.reduce((a, b) => a.flatMap(d => b.map(e => [].concat(d, e))));
    }
    
    // Helper: Cập nhật tổng tồn kho từ các biến thể
    function updateTotalVariantStock() {
        const stockInputs = document.querySelectorAll('input[name^="variants"][name$="[stock]"]');
        let total = 0;
        stockInputs.forEach(inp => {
            const v = parseInt(inp.value, 10);
            if (!isNaN(v)) total += v;
        });
        const totalDisplay = document.getElementById('total_variant_stock_display');
        if (totalDisplay) totalDisplay.value = total;
        const totalHidden = document.getElementById('total_variant_stock_hidden');
        if (totalHidden) totalHidden.value = total;
    }
    
    // Sinh suffix SKU từ value name (đơn giản hoá)
    function slugifyForSku(str) {
        return (str || '')
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // bỏ dấu tiếng Việt
            .replace(/[^a-zA-Z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .slice(0, 20);
    }
    
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
        const $authorSelect = $('.select2-authors');
        $authorSelect.select2({
            theme: 'bootstrap-5',
            placeholder: 'Tìm kiếm và chọn tác giả...',
            allowClear: true,
            closeOnSelect: false,
            width: '100%',
            maximumSelectionLength: 5,
            language: {
                maximumSelected: function (e) {
                    return 'Bạn chỉ có thể chọn tối đa ' + e.maximum + ' tác giả';
                }
            },
            templateSelection: function (data, container) {
                if (!data.id) { return data.text; }
                $(container).addClass('d-flex align-items-center');
                return data.text;
            }
        });

        // Badge đếm số tác giả đã chọn
        const $badge = $('#author_selected_badge');
        function updateAuthorBadge() {
            const count = ($authorSelect.val() || []).length;
            if (count > 0) {
                $badge.text(count + ' đã chọn').show();
            } else {
                $badge.hide();
            }
        }
        $authorSelect.on('change', updateAuthorBadge);
        // Khởi tạo theo old()
        updateAuthorBadge();
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
<script>
// Toggle hiển thị 'File đọc thử' theo checkbox 'Cho phép đọc thử'
document.addEventListener('DOMContentLoaded', function() {
    const allowSample = document.getElementById('allow_sample_read_create');
    const sampleSection = document.getElementById('ebook_sample_section');

    function toggleSampleSection() {
        if (!sampleSection) return;
        sampleSection.style.display = (allowSample && allowSample.checked) ? '' : 'none';
    }

    // Khởi tạo theo old() và khi user thay đổi
    toggleSampleSection();
    if (allowSample) {
        allowSample.addEventListener('change', toggleSampleSection);
    }
});
</script>
@endpush
@endsection