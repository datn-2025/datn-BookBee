@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="ri-bookmark-line me-2"></i>Đặt Trước Sách
                    </h4>
                </div>
                
                <div class="card-body">
                    <!-- Thông tin sách -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default-book.svg') }}" 
                                 alt="{{ $book->title }}" 
                                 class="img-fluid rounded shadow-sm">
                        </div>
                        <div class="col-md-9">
                            <h5 class="fw-bold text-primary">{{ $book->title }}</h5>
                            <p class="text-muted mb-2">
                                <strong>Tác giả:</strong> 
                                @foreach($book->authors as $author)
                                    {{ $author->name }}@if(!$loop->last), @endif
                                @endforeach
                            </p>
                            <p class="text-muted mb-2">
                                <strong>Thể loại:</strong> {{ $book->category->name }}
                            </p>
                            <p class="text-muted mb-2">
                                <strong>Ngày ra mắt:</strong> 
                                <span class="badge bg-info">{{ $book->release_date->format('d/m/Y') }}</span>
                            </p>
                            @if(($preorderDiscountPercent ?? 0) > 0)
                                <div class="mb-2 p-2 border border-success rounded bg-light">
                                    <span class="me-2"><i class="ri-price-tag-3-line text-success"></i></span>
                                    <span class="text-success fw-bold fs-5">Ưu đãi đặt trước: {{ $preorderDiscountPercent }}%</span>
                                    <div class="mt-1" id="headerPriceInfo" style="display: none;">
                                        <span class="me-2 text-muted">Giá gốc: <span id="headerOriginalPrice" class="text-decoration-line-through fw-semibold"></span></span>
                                        <span class="text-muted">Giá sau giảm: <span id="headerDiscountedPrice" class="text-success fw-bold fs-6"></span></span>
                                        <span class="mx-2 text-muted">/ 1 bản</span>
                                    </div>
                                </div>
                            @endif
                            @if($book->preorder_description)
                                <div class="alert alert-info">
                                    <i class="ri-information-line me-1"></i>
                                    {{ $book->preorder_description }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Form đặt trước -->
                    <form action="{{ route('preorders.store') }}" method="POST" id="preorderForm">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">

                        <div class="row g-3">
                            <!-- Chọn định dạng -->
                            @if($formats->count() > 0)
                                <div class="col-12">
                                    <label class="form-label fw-medium">Định dạng sách <span class="text-danger">*</span></label>
                                    <select name="book_format_id" class="form-select" id="formatSelect" required>
                                        <option value="">-- Chọn định dạng --</option>
                                        @foreach($formats as $format)
                                            <option value="{{ $format->id }}" data-price="{{ $format->price }}" data-is-ebook="{{ strtolower($format->format_name) === 'ebook' ? 'true' : 'false' }}">
                                                @php
                                                    $displayPrice = isset($preorderDiscountPercent) && $preorderDiscountPercent > 0
                                                        ? $book->getPreorderPrice($format)
                                                        : $format->price;
                                                @endphp
                                                {{ $format->format_name }} - {{ number_format($displayPrice, 0, ',', '.') }}đ
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Số lượng (xuống dưới định dạng) -->
                            <div class="col-12" id="quantitySection">
                                <label class="form-label fw-medium">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" max="10" required>
                            </div>

                            <!-- Thuộc tính sách -->
                            @if($attributes->count() > 0)
                                <div class="col-12" id="attributesSection">
                                    <label class="form-label fw-medium">Thuộc tính</label>
                                    <div class="row g-2">
                                        @foreach($attributes->groupBy('attributeValue.attribute.name') as $attributeName => $attrValues)
                                            <div class="col-md-6">
                                                <label class="form-label small">{{ $attributeName }}</label>
                                                <select name="selected_attributes[{{ $attributeName }}]" class="form-select form-select-sm">
                                                    <option value="">-- Chọn {{ $attributeName }} --</option>
                                                    @foreach($attrValues as $attrValue)
                                                        <option value="{{ $attrValue->attributeValue->value }}" 
                                                                data-extra-price="{{ $attrValue->extra_price }}">
                                                            {{ $attrValue->attributeValue->value }}
                                                            @if($attrValue->extra_price > 0)
                                                                (+{{ number_format($attrValue->extra_price, 0, ',', '.') }}đ)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Thông tin khách hàng -->
                        <h6 class="fw-bold mb-3">
                            <i class="ri-user-line me-1"></i>Thông tin khách hàng
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" 
                                       value="{{ Auth::user()->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ Auth::user()->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="{{ Auth::user()->phone }}" required>
                            </div>
                        </div>

                        <!-- Địa chỉ giao hàng (chỉ hiển thị cho sách vật lý) -->
                        <div id="addressSection" style="display: none;">
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">
                                <i class="ri-map-pin-line me-1"></i>Địa chỉ giao hàng
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select name="province_code" class="form-select" id="provinceSelect">
                                        <option value="">-- Chọn tỉnh/thành phố --</option>
                                    </select>
                                    <input type="hidden" name="province_name" id="provinceName">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select name="district_code" class="form-select" id="districtSelect" disabled>
                                        <option value="">-- Chọn quận/huyện --</option>
                                    </select>
                                    <input type="hidden" name="district_name" id="districtName">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Phường/Xã <span class="text-danger">*</span></label>
                                    <select name="ward_code" class="form-select" id="wardSelect" disabled>
                                        <option value="">-- Chọn phường/xã --</option>
                                    </select>
                                    <input type="hidden" name="ward_name" id="wardName">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control" rows="3" 
                                              placeholder="Số nhà, tên đường, khu vực..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Phí vận chuyển -->
                            <div class="mt-3" id="shippingFeeSection" style="display: none;">
                                <div class="alert alert-info d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="ri-truck-line me-1"></i>
                                        Phí vận chuyển: <strong id="shippingFeeText">0đ</strong>
                                    </span>
                                    <small class="text-muted">Miễn phí cho đơn đặt trước</small>
                                </div>
                                <input type="hidden" name="shipping_fee" id="shippingFeeInput" value="0">
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mt-4">
                            <label class="form-label fw-medium">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)"></textarea>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-wallet-line me-1"></i>Phương thức thanh toán
                        </h6>
                        
                        <div class="row g-3">
                            @foreach($paymentMethods as $method)
                                <div class="col-md-6">
                                    <div class="card border payment-method-card" style="cursor: pointer;">
                                        <div class="card-body p-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method_id" 
                                                       value="{{ $method->id }}" id="payment_{{ $method->id }}" required>
                                                <label class="form-check-label w-100" for="payment_{{ $method->id }}">
                                                    <div class="d-flex align-items-center">
                                                        @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                            <i class="ri-wallet-3-line text-success me-2 fs-5"></i>
                                                        @elseif(str_contains(strtolower($method->name), 'vnpay'))
                                                            <i class="ri-bank-card-line text-primary me-2 fs-5"></i>
                                                        @else
                                                            <i class="ri-money-dollar-circle-line me-2 fs-5"></i>
                                                        @endif
                                                        <div class="flex-grow-1">
                                                            <div class="fw-medium">{{ $method->name }}</div>
                                                            @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                                <small class="text-success">
                                                                    Số dư: {{ $wallet ? number_format($wallet->balance) : '0' }}đ
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Tổng tiền -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <div class="row g-2">
                                @if($preorderDiscountPercent > 0)
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span>Giá gốc:</span>
                                        <span class="text-decoration-line-through text-muted" id="originalPrice">0đ</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span>Giảm giá ({{ $preorderDiscountPercent }}%):</span>
                                        <span class="text-success" id="discountAmount">-0đ</span>
                                    </div>
                                </div>
                                @endif
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $preorderDiscountPercent > 0 ? 'Giá sau giảm:' : 'Giá sách:' }}</span>
                                        <span id="bookPrice" class="{{ $preorderDiscountPercent > 0 ? 'text-success fw-bold' : '' }}">0đ</span>
                                    </div>
                                </div>
                                <div class="col-12" id="attributePriceRow" style="display: none;">
                                    <div class="d-flex justify-content-between">
                                        <span>Phí thuộc tính:</span>
                                        <span id="attributePrice">0đ</span>
                                    </div>
                                </div>
                                <div class="col-12" id="shippingPriceRow" style="display: none;">
                                    <div class="d-flex justify-content-between">
                                        <span>Phí vận chuyển:</span>
                                        <span class="text-decoration-line-through text-muted" id="shippingPrice">0đ</span>
                                        <span class="text-success ms-2">Miễn phí</span>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold fs-5">Tổng tiền:</span>
                                        <span class="fw-bold fs-4 text-primary" id="totalAmount">0đ</span>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="ri-information-line me-1"></i>
                                Bạn sẽ thanh toán khi sách được phát hành. Miễn phí vận chuyển cho tất cả đơn đặt trước.
                            </small>
                        </div>

                        <!-- Nút submit -->
                        <div class="mt-4 d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary me-md-2">
                                <i class="ri-arrow-left-line me-1"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                <i class="ri-bookmark-line me-1"></i>Đặt trước ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatSelect = document.getElementById('formatSelect');
    const addressSection = document.getElementById('addressSection');
    const attributesSection = document.getElementById('attributesSection');
    const quantityInput = document.querySelector('input[name="quantity"]');
    const quantitySection = document.getElementById('quantitySection');
    const totalAmountElement = document.getElementById('totalAmount');
    const bookPriceElement = document.getElementById('bookPrice');
    const attributePriceElement = document.getElementById('attributePrice');
    const attributePriceRow = document.getElementById('attributePriceRow');
    const shippingPriceRow = document.getElementById('shippingPriceRow');
    const shippingFeeSection = document.getElementById('shippingFeeSection');
    const shippingFeeText = document.getElementById('shippingFeeText');
    const shippingFeeInput = document.getElementById('shippingFeeInput');
    const headerPriceInfo = document.getElementById('headerPriceInfo');
    const headerOriginalPrice = document.getElementById('headerOriginalPrice');
    const headerDiscountedPrice = document.getElementById('headerDiscountedPrice');
    
    let basePrice = 0;
    let extraPrice = 0;
    let shippingFee = 0;
    let isEbookSelected = false;

    // Xử lý thay đổi định dạng
    if (formatSelect) {
        formatSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            isEbookSelected = selectedOption.dataset.isEbook === 'true';
            const price = parseFloat(selectedOption.dataset.price || 0);
            
            basePrice = price;
            
            // Hiển thị/ẩn phần địa chỉ và thuộc tính
            if (isEbookSelected) {
                addressSection.style.display = 'none';
                attributesSection.style.display = 'none';
                shippingFeeSection.style.display = 'none';
                shippingPriceRow.style.display = 'none';
                shippingFee = 0;
                extraPrice = 0;
                // Ẩn và bỏ required số lượng cho ebook, đặt về 1
                if (quantitySection) quantitySection.style.display = 'none';
                if (quantityInput) {
                    quantityInput.removeAttribute('required');
                    quantityInput.value = 1;
                }
                // Xóa required cho các trường địa chỉ
                addressSection.querySelectorAll('select, textarea').forEach(field => {
                    field.removeAttribute('required');
                });
                // Reset thuộc tính
                attributesSection.querySelectorAll('select').forEach(select => {
                    select.value = '';
                });
            } else {
                addressSection.style.display = 'block';
                attributesSection.style.display = 'block';
                shippingPriceRow.style.display = 'block';
                // Hiện và yêu cầu số lượng với sách vật lý
                if (quantitySection) quantitySection.style.display = 'block';
                if (quantityInput) quantityInput.setAttribute('required', '');
                // Thêm required cho các trường địa chỉ
                document.getElementById('provinceSelect').setAttribute('required', '');
                document.getElementById('districtSelect').setAttribute('required', '');
                document.getElementById('wardSelect').setAttribute('required', '');
                document.querySelector('textarea[name="address"]').setAttribute('required', '');
                loadProvinces();
            }
            
            updateTotalAmount();
        });
    }

    // Xử lý thay đổi số lượng
    if (quantityInput) {
        quantityInput.addEventListener('input', updateTotalAmount);
    }

    // Xử lý thay đổi thuộc tính (extra price)
    document.querySelectorAll('select[name^="selected_attributes"]').forEach(select => {
        select.addEventListener('change', function() {
            extraPrice = 0;
            document.querySelectorAll('select[name^="selected_attributes"] option:checked').forEach(option => {
                extraPrice += parseFloat(option.dataset.extraPrice || 0);
            });
            updateTotalAmount();
        });
    });

    // Khởi tạo trạng thái ngay khi tải trang (ẩn số lượng nếu mặc định là ebook)
    if (formatSelect) {
        const selectedOnLoad = formatSelect.options[formatSelect.selectedIndex];
        if (selectedOnLoad && selectedOnLoad.value) {
            // Gọi sự kiện change để áp dụng tất cả logic ẩn/hiện
            formatSelect.dispatchEvent(new Event('change'));
        } else {
            // Chưa chọn định dạng: đảm bảo số lượng hiển thị và required cho sách vật lý
            if (quantitySection) quantitySection.style.display = 'block';
            if (quantityInput) quantityInput.setAttribute('required', '');
        }
    }

    function updateTotalAmount() {
        const quantity = parseInt(quantityInput.value || 1);
        
        // Áp dụng phần trăm giảm giá preorder nếu có
        const preorderDiscountPercent = {{ $preorderDiscountPercent ?? 0 }};
        let finalPrice = basePrice;
        
        // Cập nhật hiển thị giá gốc và giảm giá
        if (preorderDiscountPercent > 0) {
            const discountAmount = (basePrice * preorderDiscountPercent) / 100;
            finalPrice = Math.max(0, basePrice - discountAmount);
            
            // Hiển thị giá gốc
            const originalPriceElement = document.getElementById('originalPrice');
            if (originalPriceElement) {
                originalPriceElement.textContent = new Intl.NumberFormat('vi-VN').format(basePrice * quantity) + 'đ';
            }
            
            // Hiển thị số tiền giảm
            const discountAmountElement = document.getElementById('discountAmount');
            if (discountAmountElement) {
                discountAmountElement.textContent = '-' + new Intl.NumberFormat('vi-VN').format(discountAmount * quantity) + 'đ';
            }
        }
        
        const bookTotal = finalPrice * quantity;
        const attributeTotal = extraPrice * quantity;
        const total = bookTotal + attributeTotal + (isEbookSelected ? 0 : shippingFee);
        
        // Cập nhật hiển thị từng phần
        bookPriceElement.textContent = new Intl.NumberFormat('vi-VN').format(bookTotal) + 'đ';
        
        if (attributeTotal > 0) {
            attributePriceElement.textContent = new Intl.NumberFormat('vi-VN').format(attributeTotal) + 'đ';
            attributePriceRow.style.display = 'block';
        } else {
            attributePriceRow.style.display = 'none';
        }
        
        totalAmountElement.textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        
        // Cập nhật hiển thị thông tin giá ở phần tiêu đề (theo 1 bản)
        if (headerPriceInfo) {
            if (preorderDiscountPercent > 0 && basePrice > 0) {
                headerPriceInfo.style.display = 'inline';
                if (headerOriginalPrice) {
                    headerOriginalPrice.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + 'đ';
                }
                if (headerDiscountedPrice) {
                    headerDiscountedPrice.textContent = new Intl.NumberFormat('vi-VN').format(finalPrice) + 'đ';
                }
            } else {
                headerPriceInfo.style.display = 'none';
            }
        }
    }

    // Load provinces từ API GHN
    function loadProvinces() {
        fetch('/api/ghn/provinces')
            .then(response => response.json())
            .then(data => {
                const provinceSelect = document.getElementById('provinceSelect');
                provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
                
                if (data.success && data.data) {
                    data.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.ProvinceID;
                        option.textContent = province.ProvinceName;
                        option.dataset.name = province.ProvinceName;
                        provinceSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Lỗi khi tải danh sách tỉnh/thành phố:', error);
            });
    }

    // Xử lý thay đổi tỉnh/thành phố
    document.getElementById('provinceSelect').addEventListener('change', function() {
        const provinceId = this.value;
        const provinceName = this.options[this.selectedIndex].dataset.name;
        
        document.getElementById('provinceName').value = provinceName || '';
        
        const districtSelect = document.getElementById('districtSelect');
        const wardSelect = document.getElementById('wardSelect');
        
        // Reset districts và wards
        districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
        districtSelect.disabled = !provinceId;
        wardSelect.disabled = true;
        
        if (provinceId) {
            loadDistricts(provinceId);
        }
    });

    // Load districts
    function loadDistricts(provinceId) {
        console.log('Loading districts for province:', provinceId);
        
        fetch('/api/ghn/districts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ province_id: parseInt(provinceId) })
        })
        .then(response => {
            console.log('Districts response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Districts data received:', data);
            const districtSelect = document.getElementById('districtSelect');
            
            if (data.success && data.data) {
                data.data.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.DistrictID;
                    option.textContent = district.DistrictName;
                    option.dataset.name = district.DistrictName;
                    districtSelect.appendChild(option);
                });
                districtSelect.disabled = false;
                console.log('Districts loaded successfully:', data.data.length);
            } else {
                console.error('Failed to load districts:', data.message);
            }
        })
        .catch(error => {
            console.error('Lỗi khi tải danh sách quận/huyện:', error);
        });
    }

    // Xử lý thay đổi quận/huyện
    document.getElementById('districtSelect').addEventListener('change', function() {
        const districtId = this.value;
        const districtName = this.options[this.selectedIndex].dataset.name;
        
        document.getElementById('districtName').value = districtName || '';
        
        const wardSelect = document.getElementById('wardSelect');
        
        // Reset wards
        wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
        wardSelect.disabled = !districtId;
        
        if (districtId) {
            loadWards(districtId);
        }
    });

    // Load wards
    function loadWards(districtId) {
        console.log('Loading wards for district:', districtId);
        
        fetch('/api/ghn/wards', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ district_id: parseInt(districtId) })
        })
        .then(response => {
            console.log('Wards response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Wards data received:', data);
            const wardSelect = document.getElementById('wardSelect');
            
            if (data.success && data.data) {
                data.data.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.WardCode;
                    option.textContent = ward.WardName;
                    option.dataset.name = ward.WardName;
                    wardSelect.appendChild(option);
                });
                wardSelect.disabled = false;
                console.log('Wards loaded successfully:', data.data.length);
            } else {
                console.error('Failed to load wards:', data.message);
            }
        })
        .catch(error => {
            console.error('Lỗi khi tải danh sách phường/xã:', error);
        });
    }

    // Xử lý thay đổi phường/xã và tính phí ship
    document.getElementById('wardSelect').addEventListener('change', function() {
        const wardCode = this.value;
        const wardName = this.options[this.selectedIndex].dataset.name;
        
        document.getElementById('wardName').value = wardName || '';
        
        if (wardCode && !isEbookSelected) {
            calculateShippingFee();
        }
    });

    // Tính phí vận chuyển
    function calculateShippingFee() {
        const districtId = document.getElementById('districtSelect').value;
        const wardCode = document.getElementById('wardSelect').value;
        
        if (!districtId || !wardCode) return;
        
        fetch('/api/ghn/calculate-fee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                to_district_id: districtId,
                to_ward_code: wardCode,
                weight: 500, // Trọng lượng mặc định cho sách
                length: 20,
                width: 15,
                height: 3
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                shippingFee = data.fee;
                shippingFeeText.textContent = new Intl.NumberFormat('vi-VN').format(shippingFee) + 'đ';
                shippingFeeInput.value = shippingFee;
                shippingFeeSection.style.display = 'block';
                updateTotalAmount();
            }
        })
        .catch(error => {
            console.error('Lỗi khi tính phí vận chuyển:', error);
        });
    }

    // Form validation
    document.getElementById('preorderForm').addEventListener('submit', function(e) {
        const formatSelect = document.getElementById('formatSelect');
        if (!formatSelect.value) {
            e.preventDefault();
            alert('Vui lòng chọn định dạng sách');
            return;
        }

        const selectedOption = formatSelect.options[formatSelect.selectedIndex];
        const isEbook = selectedOption.dataset.isEbook === 'true';
        
        if (!isEbook) {
            const requiredFields = ['province_code', 'district_code', 'ward_code'];
            for (let field of requiredFields) {
                if (!document.querySelector(`[name="${field}"]`).value) {
                    e.preventDefault();
                    alert('Vui lòng điền đầy đủ thông tin địa chỉ giao hàng');
                    return;
                }
            }
            
            if (!document.querySelector('[name="address"]').value.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập địa chỉ chi tiết');
                return;
            }
        }
    });
});
</script>
@endsection
