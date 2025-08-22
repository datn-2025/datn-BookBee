@extends('layouts.backend')

@section('title', 'Tạo Đơn Đặt Trước Mới')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tạo Đơn Đặt Trước Mới</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.preorders.index') }}">Đơn đặt trước</a></li>
                    <li class="breadcrumb-item active">Tạo mới</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.preorders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.preorders.store') }}" method="POST" id="preorderForm">
        @csrf
        <div class="row">
            <!-- Thông tin sách -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông Tin Sách</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="book_id" class="form-label">Chọn sách <span class="text-danger">*</span></label>
                                <select class="form-control @error('book_id') is-invalid @enderror" id="book_id" name="book_id" required>
                                    <option value="">-- Chọn sách --</option>
                                    @foreach($preorderBooks as $book)
                                        <option value="{{ $book->id }}" 
                                                data-title="{{ $book->title }}"
                                                data-cover="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default-book.svg') }}"
                                                data-preorder-limit="{{ $book->stock_preorder_limit ?? 'Không giới hạn' }}"
                                                data-current-preorders="{{ $book->preorder_count ?? 0 }}"
                                                data-preorder-price="{{ $book->pre_order_price ?? 0 }}"
                                                data-formats='@json($book->bookFormats->map(function($format) use ($book) {
                                                    // Ưu tiên giá preorder nếu có
                                                    $finalPrice = $book->pre_order_price ? $book->pre_order_price : 
                                                        ($format->discount > 0 ? ($format->price * (100 - $format->discount) / 100) : $format->price);
                                                    return [
                                                        'id' => $format->id,
                                                        'name' => $format->format_name,
                                                        'price' => $format->price,
                                                        'discount' => $format->discount ?? 0,
                                                        'final_price' => $finalPrice
                                                    ];
                                                }))'
                                            data-attributes='@json($book->attributeValues->map(function($attrValue) {
                                                    return [
                                                        'id' => $attrValue->attribute_value_id,
                                                        'attribute_name' => $attrValue->attributeValue->attribute->name,
                                                        'value' => $attrValue->attributeValue->value,
                                                        'extra_price' => $attrValue->extra_price,
                                                        'stock' => $attrValue->stock
                                                    ];
                                                }))'
                                                {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                            {{ $book->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('book_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="book_format_id" class="form-label">Định dạng <span class="text-danger">*</span></label>
                                <select class="form-control @error('book_format_id') is-invalid @enderror" id="book_format_id" name="book_format_id" required disabled>
                                    <option value="">-- Chọn định dạng --</option>
                                </select>
                                @error('book_format_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="10" required>
                                <div class="form-text">
                                    <span id="preorder-limit-info" class="text-muted"></span>
                                </div>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="unit_price" class="form-label">Đơn giá (VNĐ)</label>
                                <input type="number" class="form-control" id="unit_price" name="unit_price" readonly>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="total_amount" class="form-label">Tổng tiền (VNĐ)</label>
                                <input type="number" class="form-control" id="total_amount" name="total_amount" readonly>
                            </div>
                        </div>
                        
                        <!-- Phí vận chuyển -->
                        <div id="shipping-fee-section" class="row mb-3" style="display: none;">
                            <div class="col-md-6">
                                <label for="shipping_fee" class="form-label">Phí vận chuyển</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="shipping_fee" name="shipping_fee" value="0" step="0.01" min="0">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <div class="form-text text-muted">
                                    <small>Phí vận chuyển sẽ được tính theo khu vực giao hàng</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden field for selected attributes -->
                        <input type="hidden" id="selected_attributes" name="selected_attributes" value="">
                        </div>
                        
                        <!-- Preview sách -->
                        <div id="book-preview" class="mt-4" style="display: none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <img id="book-cover" src="" alt="" class="img-fluid rounded shadow-sm">
                                </div>
                                <div class="col-md-9">
                                    <h5 id="book-title" class="fw-bold text-primary mb-3"></h5>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <strong>Định dạng:</strong> <span id="selected-format"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Giá:</strong> <span id="format-price"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Thuộc tính sách -->
                        <div id="book-attributes" class="mt-4" style="display: none;">
                            <h6 class="text-primary mb-3">Thuộc Tính Bổ Sung</h6>
                            <div id="attributes-container" class="row">
                                <!-- Attributes will be populated by JavaScript -->
                            </div>
                            <div class="mt-3">
                                <strong>Tổng giá thuộc tính: </strong>
                                <span id="attributes-total-price" class="text-success fw-bold">0đ</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ghi chú -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ghi Chú</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú đơn hàng</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4" 
                                      placeholder="Ghi chú về đơn đặt trước...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin khách hàng -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông Tin Khách Hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_type" class="form-label">Loại khách hàng</label>
                            <select class="form-control" id="customer_type" name="customer_type">
                                <option value="guest" {{ old('customer_type', 'guest') == 'guest' ? 'selected' : '' }}>Khách lẻ</option>
                                <option value="member" {{ old('customer_type') == 'member' ? 'selected' : '' }}>Thành viên</option>
                            </select>
                        </div>
                        
                        <!-- Chọn thành viên -->
                        <div id="member-selection" style="display: none;">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Chọn thành viên</label>
                                <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                    <option value="">-- Chọn thành viên --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone ?? '' }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Địa chỉ giao hàng -->
                <div class="card shadow mb-4" id="shipping-address">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Địa Chỉ Giao Hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="province_id" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <select class="form-control @error('province_id') is-invalid @enderror" id="province_id" name="province_id">
                                <option value="">-- Chọn tỉnh/thành phố --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('province_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="district_id" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                            <select class="form-control @error('district_id') is-invalid @enderror" id="district_id" name="district_id" disabled>
                                <option value="">-- Chọn quận/huyện --</option>
                            </select>
                            @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="ward_id" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                            <select class="form-control @error('ward_id') is-invalid @enderror" id="ward_id" name="ward_id" disabled>
                                <option value="">-- Chọn phường/xã --</option>
                            </select>
                            @error('ward_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" 
                                      placeholder="Số nhà, tên đường...">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Thông tin đơn hàng -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông Tin Đơn Hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái ban đầu</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="expected_delivery_date" class="form-label">Ngày ra mắt dự kiến</label>
                            <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" 
                                   id="expected_delivery_date" name="expected_delivery_date" 
                                   value="{{ old('expected_delivery_date') }}">
                            @error('expected_delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Nút submit -->
                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Tạo đơn đặt trước
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Khởi tạo biến
    let selectedBook = null;
    let selectedFormat = null;
    let bookFormats = {};
    let bookAttributes = {};
    let selectedAttributes = {};
    let currentUnitPrice = 0;
    let attributesTotalPrice = 0;
    
    // Xử lý thay đổi loại khách hàng
    $('#customer_type').change(function() {
        if ($(this).val() === 'member') {
            $('#member-selection').show();
        } else {
            $('#member-selection').hide();
            $('#user_id').val('');
            clearCustomerInfo();
        }
    });
    
    // Xử lý chọn thành viên
    $('#user_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#customer_name').val(selectedOption.data('name'));
            $('#email').val(selectedOption.data('email'));
            $('#phone').val(selectedOption.data('phone'));
        } else {
            clearCustomerInfo();
        }
    });
    
    function clearCustomerInfo() {
        $('#customer_name').val('');
        $('#email').val('');
        $('#phone').val('');
    }
    
    // Xử lý chọn sách
    $('#book_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const bookFormatSelect = $('#book_format_id');
        
        // Reset format selection
        bookFormatSelect.empty().append('<option value="">-- Chọn định dạng --</option>');
        bookFormatSelect.prop('disabled', true);
        $('#book-preview').hide();
        $('#book-attributes').hide();
        
        if (selectedOption.val()) {
            selectedBook = selectedOption.val();
            const formats = selectedOption.data('formats');
            const attributes = selectedOption.data('attributes');
            
            // Store book data
             bookFormats = {};
             bookAttributes = attributes || [];
             selectedAttributes = {};
             
             // Update preorder limit info
             const preorderLimit = selectedOption.data('preorder-limit') || 'Không giới hạn';
             const currentPreorders = selectedOption.data('current-preorders') || 0;
             $('#preorder-limit-info').html(`
                 Giới hạn đặt trước: <strong>${preorderLimit}</strong> | 
                 Đã đặt trước: <strong>${currentPreorders}</strong>
             `);
             
             // Populate formats
             formats.forEach(function(format) {
                 bookFormats[format.id] = format;
                 const discountText = format.discount > 0 ? ` (Giảm ${format.discount}%)` : '';
                 bookFormatSelect.append(
                     `<option value="${format.id}" data-price="${format.final_price}">${format.name}${discountText}</option>`
                 );
             });
            
            bookFormatSelect.prop('disabled', false);
            
            // Update book preview
            $('#book-cover').attr('src', selectedOption.data('cover'));
            $('#book-title').text(selectedOption.data('title'));
            $('#book-preview').show();
            
            // Show attributes if available
            if (bookAttributes.length > 0) {
                renderAttributes();
                $('#book-attributes').show();
            }
        }
        
        calculateTotal();
    });
    
    // Xử lý chọn định dạng
    $('#book_format_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const formatName = selectedOption.text();
        const formatId = selectedOption.val();
        
        if (formatId) {
            selectedFormat = formatId;
            const format = bookFormats[formatId];
            const price = format.final_price;
            const selectedBookOption = $('#book_id').find('option:selected');
            const preorderPrice = parseFloat(selectedBookOption.data('preorder-price')) || 0;
            
            $('#selected-format').text(formatName);
            
            // Hiển thị thông tin giá với chú thích preorder
            if (preorderPrice > 0) {
                $('#format-price').html(
                    '<span class="text-success fw-bold">' + new Intl.NumberFormat('vi-VN').format(price) + 'đ</span>' +
                    '<br><small class="text-muted"><i class="ri-price-tag-3-line me-1"></i>Giá ưu đãi đặt trước</small>'
                );
            } else {
                $('#format-price').text(new Intl.NumberFormat('vi-VN').format(price) + 'đ');
            }
            
            currentUnitPrice = price;
            
            // Ẩn/hiện địa chỉ giao hàng và phí vận chuyển
            if (formatName.toLowerCase().includes('ebook')) {
                $('#shipping-address').hide();
                $('#shipping-fee-section').hide();
                $('#shipping-address input, #shipping-address select, #shipping-address textarea').prop('required', false);
            } else {
                $('#shipping-address').show();
                $('#shipping-fee-section').show();
                $('#shipping-address input[data-required], #shipping-address select[data-required], #shipping-address textarea[data-required]').prop('required', true);
            }
        } else {
            selectedFormat = null;
            $('#selected-format').text('');
            $('#format-price').text('');
            currentUnitPrice = 0;
            $('#shipping-address').show();
            $('#shipping-fee-section').show();
        }
        
        calculateTotal();
    });
    
    // Xử lý thay đổi số lượng
    $('#quantity').on('input', function() {
        calculateTotal();
    });
    
    // Xử lý thay đổi phí vận chuyển
    $('#shipping_fee').on('input', function() {
        calculateTotal();
    });
    
    // Render attributes
    function renderAttributes() {
        const container = $('#attributes-container');
        container.empty();
        
        // Group attributes by attribute name
        const groupedAttributes = {};
        bookAttributes.forEach(function(attr) {
            if (!groupedAttributes[attr.attribute_name]) {
                groupedAttributes[attr.attribute_name] = [];
            }
            groupedAttributes[attr.attribute_name].push(attr);
        });
        
        // Render each attribute group
        Object.keys(groupedAttributes).forEach(function(attributeName) {
            const attributes = groupedAttributes[attributeName];
            const attributeId = 'attr_' + attributeName.replace(/\s+/g, '_').toLowerCase();
            
            let html = `
                <div class="col-md-6 mb-3">
                    <label class="form-label">${attributeName}</label>
                    <select class="form-control attribute-select" data-attribute="${attributeName}" id="${attributeId}">
                        <option value="">-- Chọn ${attributeName} --</option>
            `;
            
            attributes.forEach(function(attr) {
                const extraPriceText = attr.extra_price > 0 ? ` (+${new Intl.NumberFormat('vi-VN').format(attr.extra_price)}đ)` : '';
                const stockText = attr.stock !== null ? ` (Còn: ${attr.stock})` : '';
                html += `<option value="${attr.id}" data-price="${attr.extra_price}" data-stock="${attr.stock}">${attr.value}${extraPriceText}${stockText}</option>`;
            });
            
            html += `
                    </select>
                </div>
            `;
            
            container.append(html);
        });
        
        // Bind change events
        $('.attribute-select').change(function() {
            const attributeName = $(this).data('attribute');
            const selectedOption = $(this).find('option:selected');
            
            if (selectedOption.val()) {
                selectedAttributes[attributeName] = {
                    id: selectedOption.val(),
                    price: parseFloat(selectedOption.data('price')) || 0,
                    stock: selectedOption.data('stock')
                };
            } else {
                delete selectedAttributes[attributeName];
            }
            
            updateAttributesPrice();
            calculateTotal();
        });
    }
    
    // Update attributes total price
    function updateAttributesPrice() {
        attributesTotalPrice = 0;
        Object.values(selectedAttributes).forEach(function(attr) {
            attributesTotalPrice += attr.price;
        });
        
        $('#attributes-total-price').text(new Intl.NumberFormat('vi-VN').format(attributesTotalPrice) + 'đ');
        
        // Update selected attributes hidden field
        $('#selected_attributes').val(JSON.stringify(selectedAttributes));
    }
    
    function calculateTotal() {
        const quantity = parseInt($('#quantity').val()) || 0;
        const unitPrice = currentUnitPrice + attributesTotalPrice;
        const shippingFee = parseFloat($('#shipping_fee').val()) || 0;
        const total = (quantity * unitPrice) + shippingFee;
        
        $('#unit_price').val(unitPrice);
        $('#total_amount').val(total);
    }
    
    // Xử lý địa chỉ
    $('#province_id').change(function() {
        const provinceId = $(this).val();
        const districtSelect = $('#district_id');
        const wardSelect = $('#ward_id');
        
        // Reset districts and wards
        districtSelect.empty().append('<option value="">-- Chọn quận/huyện --</option>').prop('disabled', true);
        wardSelect.empty().append('<option value="">-- Chọn phường/xã --</option>').prop('disabled', true);
        
        if (provinceId) {
            // Load districts via AJAX
            $.get(`/api/districts/${provinceId}`, function(districts) {
                districts.forEach(function(district) {
                    districtSelect.append(`<option value="${district.id}">${district.name}</option>`);
                });
                districtSelect.prop('disabled', false);
            });
        }
    });
    
    $('#district_id').change(function() {
        const districtId = $(this).val();
        const wardSelect = $('#ward_id');
        
        // Reset wards
        wardSelect.empty().append('<option value="">-- Chọn phường/xã --</option>').prop('disabled', true);
        
        if (districtId) {
            // Load wards via AJAX
            $.get(`/api/wards/${districtId}`, function(wards) {
                wards.forEach(function(ward) {
                    wardSelect.append(`<option value="${ward.id}">${ward.name}</option>`);
                });
                wardSelect.prop('disabled', false);
            });
        }
    });
    
    // Trigger customer type change on page load
    $('#customer_type').trigger('change');
    
    // Form validation before submit
    $('#preorderForm').submit(function(e) {
        // Check if attributes with stock are available
        let hasStockIssue = false;
        Object.values(selectedAttributes).forEach(function(attr) {
            if (attr.stock !== null && attr.stock < parseInt($('#quantity').val())) {
                hasStockIssue = true;
            }
        });
        
        if (hasStockIssue) {
            e.preventDefault();
            alert('Số lượng yêu cầu vượt quá tồn kho của một số thuộc tính đã chọn.');
            return false;
        }
    });
});
</script>
@endpush