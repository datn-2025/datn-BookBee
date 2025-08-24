@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Alert messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <!-- Breadcrumb (moved outside the dark header) -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('books.show', $book) }}" class="text-decoration-none">{{ $book->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Đặt trước</li>
                </ol>
            </nav>

            <!-- Header với style màu đen như orders -->
            <div class="mb-4" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border-radius: 0px;">
                <div class="p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-4 order-md-2 d-flex justify-content-md-end">
                            <div class="p-0" style="background: transparent; border-radius: 0px;">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" 
                                         class="img-fluid shadow-sm" style="max-height: 260px; object-fit: cover; border-radius: 0px; width: 100%;"
                                         onerror="this.src='https://via.placeholder.com/200x250/cccccc/666666?text=No+Image'">
                                @elseif($book->image)
                                    <img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}" 
                                         class="img-fluid shadow-sm" style="max-height: 260px; object-fit: cover; border-radius: 0px; width: 100%;"
                                         onerror="this.src='https://via.placeholder.com/200x250/cccccc/666666?text=No+Image'">
                                @elseif(isset($book->image_url) && $book->image_url)
                                    <img src="{{ $book->image_url }}" alt="{{ $book->title }}" 
                                         class="img-fluid shadow-sm" style="max-height: 260px; object-fit: cover; border-radius: 0px; width: 100%;"
                                         onerror="this.src='https://via.placeholder.com/200x250/cccccc/666666?text=No+Image'">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 260px; border-radius: 0px;">
                                        <i class="ri-image-line" style="font-size: 3rem; color: #ccc;"></i>
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                        <div class="col-md-8 order-md-1 pe-md-4">
                            <h1 class="h2 mb-3 text-white">
                                <i class="ri-book-2-line me-2"></i>ĐẶT TRƯỚC SÁCH
                            </h1>
                            <p class="text-light mb-4">THEO DÕI VÀ QUẢN LÝ TẤT CẢ ĐƠN ĐẶT TRƯỚC CỦA BẠN</p>
                            
                            <div class="text-light">
                                <div class="text-md-start">
                                    <p class="mb-2">
                                        <strong class="me-2">Ngày ra mắt:</strong>
                                        <span class="badge bg-warning text-dark">{{ $book->release_date ? \Carbon\Carbon::parse($book->release_date)->format('d/m/Y') : '29/08/2025' }}</span>
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <p class="mb-2"><strong>{{ $book->title }}</strong></p>
                                    <p class="mb-2">Tác giả: 
                                        @if($book->authors && $book->authors->count() > 0)
                                            {{ $book->authors->pluck('name')->join(', ') }}
                                        @else
                                            Paulo Coelho, Nguyễn Nhật Ánh, Dale Carnegie
                                        @endif
                                    </p>
                                    <p class="mb-2">Thể loại: {{ $book->category ? $book->category->name : 'Sách Văn Học' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                            @if(($preorderDiscountPercent ?? 0) > 0)
                                <div class="mt-3 p-3" style="background: rgba(255, 193, 7, 0.1); border: 1px solid #ffc107; border-radius: 0px;">
                                    <span class="me-2"><i class="ri-price-tag-3-line text-warning"></i></span>
                                    <span class="text-warning fw-bold fs-5">Ưu đãi đặt trước: {{ $preorderDiscountPercent }}%</span>
                                    <div class="mt-1" id="headerPriceInfo" style="display: none;">
                                        <span class="me-2 text-light opacity-75">Giá gốc: <span id="headerOriginalPrice" class="text-decoration-line-through fw-semibold"></span></span>
                                        <span class="text-light">Giá sau giảm: <span id="headerDiscountedPrice" class="text-warning fw-bold fs-6"></span></span>
                                        <span class="mx-2 text-light opacity-75">/ 1 bản</span>
                                    </div>
                                </div>
                            @endif
                            @if($book->preorder_description)
                                <div class="mt-3 p-3" style="background: rgba(13, 202, 240, 0.1); border: 1px solid #0dcaf0; border-radius: 0px;">
                                    <i class="ri-information-line me-1 text-info"></i>
                                    <span class="text-light">{{ $book->preorder_description }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bố cục 2 cột: Trái (Form) - Phải (Summary) -->
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <!-- Form đặt trước với style màu đen -->
                            <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 0px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                                <div class="p-4" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                                    <h5 class="text-white fw-bold mb-0">
                                        <i class="ri-shopping-cart-line me-2"></i>THÔNG TIN ĐẶT TRƯỚC
                                    </h5>
                                </div>
                                <div class="p-4">
                                    <form action="{{ route('preorders.store') }}" method="POST" id="preorderForm">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">

                                        <div class="row g-3">
                            <!-- Chọn định dạng -->
                           @php
    $availableFormats = $formats->filter(function($format) use ($book) {
        // Ebook luôn có sẵn (không cần stock vật lý)
        if (strtolower($format->format_name) === 'ebook') {
            return true;
        }
        
        // Sách vật lý: kiểm tra stock_preorder_limit từ bảng books
        // Tính số lượng còn lại = giới hạn đặt trước - số đã đặt trước
        $remainingStock = ($book->stock_preorder_limit ?? 0) - ($book->preorder_count ?? 0);
        return $remainingStock > 0;
    });
@endphp

@if($availableFormats->count() > 0)
    <div class="col-12">
        <label class="form-label fw-medium">Định dạng sách <span class="text-danger">*</span></label>
        <select name="book_format_id" class="form-select" id="formatSelect" required>
            <option value="">-- Chọn định dạng --</option>
            @foreach($availableFormats as $format)
                <option value="{{ $format->id }}" 
                        data-price="{{ $format->price }}" 
                        data-is-ebook="{{ strtolower($format->format_name) === 'ebook' ? 'true' : 'false' }}">
                    @php
                        $displayPrice = isset($preorderDiscountPercent) && $preorderDiscountPercent > 0
                            ? $book->getPreorderPrice($format)
                            : $format->price;
                        
                        // Hiển thị thông tin stock cho sách vật lý
                        $stockInfo = '';
                        if (strtolower($format->format_name) !== 'ebook') {
                            $remainingStock = ($book->stock_preorder_limit ?? 0) - ($book->preorder_count ?? 0);
                            $stockInfo = ' (Còn ' . $remainingStock . ' suất đặt trước)';
                        }
                    @endphp
                    {{ $format->format_name }} - {{ number_format($displayPrice, 0, ',', '.') }}đ{{ $stockInfo }}
                </option>
            @endforeach
        </select>
    </div>
@else
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="ri-information-line me-2"></i>
            @if(($book->stock_preorder_limit ?? 0) <= ($book->preorder_count ?? 0))
                Đã hết suất đặt trước cho sách này. Hiện tại đã có {{ $book->preorder_count ?? 0 }}/{{ $book->stock_preorder_limit ?? 0 }} đơn đặt trước.
            @else
                Hiện tại không có định dạng nào có sẵn cho đặt trước.
            @endif
        </div>
    </div>
@endif

                            <!-- Số lượng (xuống dưới định dạng) -->
                            <div class="col-12" id="quantitySection">
                                <label class="form-label fw-medium">Số lượng <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center" style="max-width: 200px; border: 1px solid #dee2e6; border-radius: 0px;">
                                    <button type="button" class="btn btn-link p-3 text-dark border-0" id="decreaseQuantity" style="border-radius: 0px; min-width: 50px;">
                                        <strong>−</strong>
                                    </button>
                                    <input type="number" name="quantity" id="quantityInput" class="form-control text-center border-0" 
                                           value="1" min="1" max="10" required style="border-radius: 0px; box-shadow: none;">
                                    <button type="button" class="btn btn-link p-3 text-dark border-0" id="increaseQuantity" style="border-radius: 0px; min-width: 50px;">
                                        <strong>+</strong>
                                    </button>
                                </div>
                            </div>

                            <!-- Thuộc tính sách -->
                            @php
                                $hasAnyAvailableAttributes = false;
                                foreach($attributes->groupBy('attributeValue.attribute.name') as $attrValues) {
                                    if($attrValues->where('stock', '>', 0)->count() > 0) {
                                        $hasAnyAvailableAttributes = true;
                                        break;
                                    }
                                }
                            @endphp
                            @if($hasAnyAvailableAttributes)
                                <div class="col-12" id="attributesSection">
                                    <label class="form-label fw-medium">Thuộc tính</label>
                                    <div class="row g-2">
                                        @foreach($attributes->groupBy('attributeValue.attribute.name') as $attributeName => $attrValues)
                                            @php
                                                $hasAvailableValues = $attrValues->where('stock', '>', 0)->count() > 0;
                                            @endphp
                                            @if($hasAvailableValues)
                                                <div class="col-md-6">
                                                    <label class="form-label small">{{ $attributeName }}</label>
                                                    <select name="selected_attributes[{{ $attributeName }}]" class="form-select form-select-sm">
                                                        <option value="">-- Chọn {{ $attributeName }} --</option>
                                                        @foreach($attrValues as $attrValue)
                                                            @if($attrValue->stock > 0)
                                                                <option value="{{ $attrValue->attributeValue->value }}" 
                                                                        data-extra-price="{{ $attrValue->extra_price }}">
                                                                    {{ $attrValue->attributeValue->value }}
                                                                    @if($attrValue->extra_price > 0)
                                                                        (+{{ number_format($attrValue->extra_price, 0, ',', '.') }}đ)
                                                                    @endif
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
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

                        <!-- Email nhận ebook (chỉ hiển thị khi chọn ebook) -->
                        <div id="ebookEmailSection" class="mt-4" style="display: none;">
                            <hr class="my-4">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-1 h-6 bg-black"></div>
                                <h6 class="text-lg font-black uppercase tracking-wide text-black mb-0">
                                    <i class="ri-mail-line me-2"></i>EMAIL NHẬN EBOOK
                                </h6>
                            </div>
                            
                            <div class="mb-4 group">
                                <label for="ebook_delivery_email" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                    EMAIL NHẬN FILE EBOOK <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="ebook_delivery_email" id="ebook_delivery_email"
                                    class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg form-control"
                                    placeholder="Nhập email để nhận thông báo và file ebook" 
                                    value="{{ Auth::user()->email }}">
                                <small class="text-muted mt-2 d-block">
                                    <i class="ri-information-line me-1"></i>
                                    File ebook sẽ được gửi đến email này sau khi đơn hàng được xử lý. Có thể khác với email đăng ký tài khoản.
                                </small>
                            </div>
                        </div>

                        <!-- Địa chỉ giao hàng (chỉ hiển thị cho sách vật lý) -->
                        <div id="addressSection">
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
                        Phí vận chuyển GHN: <span id="shippingFeeText">0đ</span>
                    </span>
                    <small class="text-muted">Phí vận chuyển từ GHN</small>
                </div>
                <input type="hidden" name="shipping_fee" id="shippingFeeInput" value="0">
            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mt-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="form-label fw-medium">Ghi chú</label>
                                    <textarea name="notes" class="form-control" rows="3" 
                                              placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)"></textarea>
                                </div>
                            </div>
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
                        <!-- Tổng tiền được chuyển sang cột phải -->
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 0px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); position: sticky; top: 90px;">
                                <div class="p-4" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                                    <h5 class="text-white fw-bold mb-0">
                                        <i class="ri-receipt-line me-2"></i>TÓM TẮT ĐỚN HÀNG
                                    </h5>
                                </div>
                                <div class="p-4">
                                    <h6 class="fw-bold mb-3">Tóm tắt đơn</h6>
                                    <div class="mb-3 p-3 bg-light rounded">
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
                                                    <span id="shippingPrice">0đ</span>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold fs-6">Tổng tiền:</span>
                                                    <span class="fw-bold fs-4 text-primary" id="totalAmount">0đ</span>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="ri-information-line me-1"></i>
                                            Bạn sẽ thanh toán khi sách được phát hành. Phí vận chuyển được tính theo khu vực giao hàng.
                                        </small>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" form="preorderForm" class="btn btn-dark" id="submitBtn">
                                            <i class="ri-bookmark-line me-1"></i>Đặt trước ngay
                                        </button>
                                        {{-- <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary">
                                            <i class="ri-arrow-left-line me-1"></i>Quay lại
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0px; border: 3px solid #dc2626;">
            <!-- Header với style màu đen như trong hình -->
            <div class="modal-header text-white p-4" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); border-radius: 0px; border-bottom: none;">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <!-- Laravel Logo như trong hình -->
                            <div class="p-3" style="background: white; border-radius: 0px;">
                                <svg width="100" height="100" viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg">
                                    <title>Laravel</title>
                                    <path d="m49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302v10.509c0 .286-.152.55-.4.694l-19.223 11.066c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054l-19.219-11.066a.801.801 0 0 1-.402-.694v-32.916c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.050.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.050.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216zm-36.84-31.068v31.068l17.618 10.143v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.030-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09v-21.483l-4.645-2.676-3.363-1.934zm8.81-5.994-8.007 4.609 8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764 4.645-2.674v-20.096l-3.363 1.936-4.646 2.675v20.096zm24.667-23.325-8.006 4.609 8.006 4.609 8.005-4.61zm-.801 10.605-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937zm-18.422 20.561 11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833z" fill="#FF2D20"/>
                                </svg>
                                
                                <!-- Ảnh phụ nhỏ bên dưới -->
                                <div class="row g-1 mt-2">
                                    <div class="col-4">
                                        <img src="{{ asset('images/avtchatbot.jpg') }}" alt="Flag" class="img-fluid" style="height: 30px; object-fit: cover; border-radius: 0px; width: 100%; opacity: 0.8;">
                                    </div>
                                    <div class="col-4">
                                        <img src="{{ asset('images/banner-image-bg.jpg') }}" alt="Books" class="img-fluid" style="height: 30px; object-fit: cover; border-radius: 0px; width: 100%; opacity: 0.6;">
                                    </div>
                                    <div class="col-4">
                                        <img src="{{ asset('images/banner-image-bg-1.jpg') }}" alt="More" class="img-fluid" style="height: 30px; object-fit: cover; border-radius: 0px; width: 100%; opacity: 0.4;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="mb-2">
                                <small class="text-warning">/ Đặt trước</small>
                            </div>
                            <h2 class="text-white fw-bold mb-3">ĐẶT TRƯỚC SÁCH</h2>
                            <p class="text-light mb-3">THEO DÕI VÀ QUẢN LÝ TẤT CẢ ĐƠN ĐẶT TRƯỚC CỦA BẠN</p>
                            
                            <div class="row text-light">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong id="modalBookTitle">{{ $book->title }}</strong></p>
                                    <p class="mb-1">Tác giả: <span id="modalBookAuthor">
                                        @if($book->authors && $book->authors->count() > 0)
                                            {{ $book->authors->pluck('name')->join(', ') }}
                                        @else
                                            Paulo Coelho, Nguyễn Nhật Ánh, Dale Carnegie
                                        @endif
                                    </span></p>
                                    <p class="mb-1">Thể loại: <span id="modalBookCategory">{{ $book->category ? $book->category->name : 'Sách Văn Học' }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Ngày ra mắt:</strong> <span class="badge bg-warning text-dark" id="modalReleaseDate">{{ $book->release_date ? \Carbon\Carbon::parse($book->release_date)->format('d/m/Y') : '20/08/2025' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="modal-body p-4" style="background: #f8f9fa;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100" style="border-radius: 0px; border: 1px solid #dee2e6;">
                            <div class="card-header" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                                <h6 class="text-white fw-bold mb-0">
                                    <i class="ri-file-list-3-line me-2"></i>THÔNG TIN ĐẶT TRƯỚC
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Định dạng sách:</small><br>
                                    <span class="fw-bold" id="modalFormat">-</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Số lượng:</small><br>
                                    <span class="fw-bold" id="modalQuantity">-</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Tổng tiền:</small><br>
                                    <span class="fw-bold text-success fs-5" id="modalTotalAmount">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100" style="border-radius: 0px; border: 1px solid #dee2e6;">
                            <div class="card-header" style="background: linear-gradient(135deg, #343a40 0%, #495057 100%); border-radius: 0px;">
                                <h6 class="text-white fw-bold mb-0">
                                    <i class="ri-receipt-line me-2"></i>TÓM TẮT ĐƠN HÀNG
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success" style="border-radius: 0px;">
                                    <h6 class="alert-heading fw-bold">
                                        <i class="ri-check-line me-2"></i>Đặt trước thành công!
                                    </h6>
                                    <p class="mb-2">Cảm ơn bạn đã đặt trước! Chúng tôi đã nhận được yêu cầu của bạn và sẽ liên hệ sớm nhất.</p>
                                    <hr>
                                    <p class="mb-0">
                                        <small>
                                            <i class="ri-information-line me-1"></i>
                                            Bạn sẽ nhận được email xác nhận đơn đặt trước trong vài phút tới.
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer" style="background: #ffffff; border-top: 1px solid #dee2e6; border-radius: 0px;">
                <button type="button" class="btn btn-dark px-4" style="border-radius: 0px; font-weight: 600;" onclick="redirectToPreorderList()">
                    <i class="ri-list-check me-2"></i>XEM ĐƠN ĐẶT TRƯỚC
                </button>
                <button type="button" class="btn btn-outline-dark px-4" style="border-radius: 0px; font-weight: 600;" onclick="redirectToHome()">
                    <i class="ri-home-line me-2"></i>VỀ TRANG CHỦ
                </button>
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
    const preorderPriceNotice = document.querySelector('.preorder-price-notice') || document.createElement('div');
    // Thêm vào phần đầu script, sau các biến khai báo
    const bookStockPreorderLimit = {{ $book->stock_preorder_limit ?? 0 }};
    const bookPreorderCount = {{ $book->preorder_count ?? 0 }};
    const remainingPreorderStock = bookStockPreorderLimit - bookPreorderCount;
    
    let basePrice = 0;
    let extraPrice = 0;
    let shippingFee = 0;
    let isEbookSelected = false;
    const bookPreorderPrice = {{ $book->pre_order_price ?? 'null' }};

    // Khởi tạo trạng thái ban đầu
    function initializeFormState() {
        if (formatSelect && formatSelect.selectedIndex >= 0) {
            const selectedOption = formatSelect.options[formatSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                isEbookSelected = selectedOption.dataset.isEbook === 'true';
                if (isEbookSelected) {
                    addressSection.style.display = 'none';
                    attributesSection.style.display = 'none';
                    if (shippingFeeSection) shippingFeeSection.style.display = 'none';
                    if (shippingPriceRow) shippingPriceRow.style.display = 'none';
                }
            }
        }
    }
    
    // Gọi khởi tạo ngay khi DOM load
    initializeFormState();

    // Quantity +/- buttons functionality
    document.getElementById('decreaseQuantity').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantityInput');
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateTotalAmount();
        }
    });

    document.getElementById('increaseQuantity').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantityInput');
        let currentValue = parseInt(quantityInput.value);
        const maxValue = parseInt(quantityInput.getAttribute('max'));
        if (currentValue < maxValue) {
            quantityInput.value = currentValue + 1;
            updateTotalAmount();
        }
    });

    // Also allow manual input change to trigger total update
    document.getElementById('quantityInput').addEventListener('input', function() {
        updateTotalAmount();
    });

    // Xử lý thay đổi định dạng
    if (formatSelect) {
    formatSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        isEbookSelected = selectedOption.dataset.isEbook === 'true';
        const price = parseFloat(selectedOption.dataset.price || 0);
        
        basePrice = price;
        
        // Hiển thị thông báo giá ưu đãi preorder
        if (bookPreorderPrice && selectedOption.textContent.includes('(Giá ưu đãi)')) {
            preorderPriceNotice.style.display = 'block';
        } else {
            preorderPriceNotice.style.display = 'none';
        }
        
        // Hiển thị/ẩn phần địa chỉ và thuộc tính
        if (isEbookSelected) {
            addressSection.style.display = 'none';
            attributesSection.style.display = 'none';
            shippingFeeSection.style.display = 'none';
            shippingPriceRow.style.display = 'none';
            shippingFee = 0;
            extraPrice = 0;
            // Hiển thị form email nhận ebook
            document.getElementById('ebookEmailSection').style.display = 'block';
            // Ẩn và bỏ required số lượng cho ebook, đặt về 1
            if (quantitySection) quantitySection.style.display = 'none';
            if (quantityInput) {
                quantityInput.removeAttribute('required');
                quantityInput.value = 1;
                quantityInput.max = 1; // Ebook chỉ mua 1 bản
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
            // Ẩn form email nhận ebook
            document.getElementById('ebookEmailSection').style.display = 'none';
            // Hiện và yêu cầu số lượng với sách vật lý
            if (quantitySection) quantitySection.style.display = 'block';
            if (quantityInput) {
                quantityInput.setAttribute('required', '');
                // Set max quantity cho sách vật lý dựa trên remaining stock
                quantityInput.max = Math.min(10, remainingPreorderStock);
                if (parseInt(quantityInput.value) > remainingPreorderStock) {
                    quantityInput.value = remainingPreorderStock;
                }
            }
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
            // Chưa chọn định dạng: đảm bảo hiển thị các phần cho sách vật lý
            if (quantitySection) quantitySection.style.display = 'block';
            if (quantityInput) quantityInput.setAttribute('required', '');
            if (addressSection) addressSection.style.display = 'block';
            if (attributesSection) attributesSection.style.display = 'block';
            if (shippingPriceRow) shippingPriceRow.style.display = 'block';
            // Thêm required cho các trường địa chỉ
            document.getElementById('provinceSelect').setAttribute('required', '');
            document.getElementById('districtSelect').setAttribute('required', '');
            document.getElementById('wardSelect').setAttribute('required', '');
            document.querySelector('textarea[name="address"]').setAttribute('required', '');
            loadProvinces();
        }
    }

function updateTotalAmount() {
    const quantity = parseInt(quantityInput.value || 1);
    
    // Kiểm tra giới hạn số lượng cho sách vật lý
    if (!isEbookSelected && quantity > remainingPreorderStock) {
        alert(`Chỉ còn ${remainingPreorderStock} suất đặt trước. Vui lòng giảm số lượng.`);
        quantityInput.value = Math.min(quantity, remainingPreorderStock);
        return;
    }
    
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
    
    // Cập nhật hiển thị phí vận chuyển
    const shippingPriceElement = document.getElementById('shippingPrice');
    if (shippingPriceElement) {
        shippingPriceElement.textContent = new Intl.NumberFormat('vi-VN').format(isEbookSelected ? 0 : shippingFee) + 'đ';
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
        
        fetch('/api/ghn/shipping-fee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                to_district_id: parseInt(districtId),
                to_ward_code: wardCode,
                weight: 500, // Trọng lượng mặc định cho sách
                service_type_id: 2 // Giao hàng tiết kiệm
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.total) {
                // Hiển thị và áp dụng phí vận chuyển thực tế từ GHN
                const actualShippingFee = data.data.total;
                shippingFeeText.textContent = new Intl.NumberFormat('vi-VN').format(actualShippingFee) + 'đ';
                
                // Áp dụng phí vận chuyển thực tế
                shippingFee = actualShippingFee;
                shippingFeeInput.value = actualShippingFee;
                shippingFeeSection.style.display = 'block';
                updateTotalAmount();
                
                console.log('Phí vận chuyển GHN:', actualShippingFee, 'Phí áp dụng:', shippingFee);
            } else {
                console.error('Không thể tính phí vận chuyển:', data.message);
                // Fallback: phí vận chuyển mặc định
                shippingFee = 30000;
                shippingFeeText.textContent = '30,000đ';
                shippingFeeInput.value = 30000;
                shippingFeeSection.style.display = 'block';
                updateTotalAmount();
            }
        })
        .catch(error => {
            console.error('Lỗi khi tính phí vận chuyển:', error);
            // Fallback: phí vận chuyển mặc định
            shippingFee = 30000;
            shippingFeeText.textContent = '30,000đ';
            shippingFeeInput.value = 30000;
            shippingFeeSection.style.display = 'block';
            updateTotalAmount();
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
        
        if (isEbook) {
            // Validate ebook delivery email
            const ebookEmail = document.querySelector('[name="ebook_delivery_email"]');
            if (!ebookEmail.value.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập email để nhận file ebook');
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(ebookEmail.value)) {
                e.preventDefault();
                alert('Vui lòng nhập email hợp lệ để nhận file ebook');
                return;
            }
        } else {
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

    // Handle form submission success
    const preorderForm = document.getElementById('preorderForm');
    if (preorderForm) {
        preorderForm.addEventListener('submit', function(e) {
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-loader-4-line me-2" style="animation: spin 1s linear infinite;"></i>Đang xử lý...';
            
            // Let the form submit normally, but prepare modal data
            setTimeout(() => {
                // Get form data for modal
                const formatSelect = document.getElementById('formatSelect');
                const quantityInput = document.getElementById('quantityInput');
                const totalAmount = document.getElementById('totalAmount');
                
                if (formatSelect && quantityInput && totalAmount) {
                    const selectedFormat = formatSelect.options[formatSelect.selectedIndex];
                    
                    // Update modal content
                    document.getElementById('modalFormat').textContent = selectedFormat ? selectedFormat.text : '-';
                    document.getElementById('modalQuantity').textContent = quantityInput.value || '-';
                    document.getElementById('modalTotalAmount').textContent = totalAmount.textContent || '-';
                }
            }, 100);
        });
    }

    // Check for success session and show modal
    @if(session('success'))
        setTimeout(() => {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        }, 500);
    @endif
});

// Modal redirect functions
function redirectToPreorderList() {
    window.location.href = "{{ route('preorders.index') }}";
}

function redirectToHome() {
    window.location.href = "{{ route('home') }}";
}
</script>
@endsection
