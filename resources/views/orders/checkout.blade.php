@extends('layouts.app')

@section('content')
@php
    // Kiểm tra xem giỏ hàng có chỉ ebook hay không
    $hasOnlyEbooks = true;
    $hasPhysicalBooks = false;
    
    // Debug: Log cart items để kiểm tra
    \Log::info('=== CHECKOUT DEBUG ===');
    \Log::info('Cart items count: ' . $cartItems->count());
    
    foreach($cartItems as $item) {
        \Log::info('Item ID: ' . $item->id);
        \Log::info('is_combo value: ' . ($item->is_combo ?? 'null'));
        \Log::info('is_combo type: ' . gettype($item->is_combo));
        \Log::info('collection_id: ' . ($item->collection_id ?? 'null'));
        \Log::info('book_id: ' . ($item->book_id ?? 'null'));
        \Log::info('book_format_id: ' . ($item->book_format_id ?? 'null'));
        
        // Kiểm tra combo - combo luôn là sách vật lý
        if (isset($item->is_combo) && $item->is_combo) {
            \Log::info('Found combo item - setting hasPhysicalBooks = true');
            $hasPhysicalBooks = true;
            $hasOnlyEbooks = false;
            break;
        }
        
        // Kiểm tra sách đơn lẻ
        if ($item->bookFormat) {
            \Log::info('Book format: ' . $item->bookFormat->format_name);
            if (strtolower($item->bookFormat->format_name) !== 'ebook') {
                \Log::info('Found physical book - setting hasPhysicalBooks = true');
                $hasPhysicalBooks = true;
                $hasOnlyEbooks = false;
                break;
            }
        }
    }
    
    \Log::info('Final hasOnlyEbooks: ' . ($hasOnlyEbooks ? 'true' : 'false'));
    \Log::info('Final hasPhysicalBooks: ' . ($hasPhysicalBooks ? 'true' : 'false'));
    \Log::info('=== END CHECKOUT DEBUG ===');
@endphp

<div class="min-h-screen bg-white relative overflow-hidden">
    <!-- Adidas-style Background Elements -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-64 h-1 bg-black opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-black opacity-5 transform rotate-45"></div>
        <div class="absolute top-1/2 right-10 w-0.5 h-24 bg-black opacity-30"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 py-12">
        <!-- Adidas-style Header -->
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="w-12 h-0.5 bg-black"></div>
                <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                    BOOKBEE CHECKOUT
                </span>
                <div class="w-12 h-0.5 bg-black"></div>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tight text-black mb-6">
                THANH TOÁN
            </h1>
            
            <p class="text-lg text-gray-700 max-w-2xl mx-auto mb-8">
                Hoàn tất đơn hàng của bạn với quy trình đơn giản và an toàn
            </p>
            
            <!-- Adidas-style Progress Steps -->
            <div class="flex items-center justify-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-black text-white flex items-center justify-center font-bold">
                        ✓
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wide text-black">GIỏ HÀNG</span>
                </div>
                <div class="w-12 h-0.5 bg-black"></div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-600 text-white flex items-center justify-center font-bold">
                        2
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wide text-red-600">THANH TOÁN</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-300"></div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-300 text-gray-500 flex items-center justify-center font-bold">
                        3
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wide text-gray-500">HOÀN TẤT</span>
                </div>
            </div>
        </div>

        {{-- @if(isset($mixedFormatCart) && $mixedFormatCart)
        <div class="bg-red-600 text-white p-6 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
            <div class="relative z-10">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-white text-red-600 flex items-center justify-center font-bold">
                            !
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wide mb-2">LƯU Ý QUAN TRỌNG</h3>
                        <p class="text-sm leading-relaxed mb-3">
                            Giỏ hàng của bạn có cả <span class="font-bold">sách vật lý</span> và <span class="font-bold">sách điện tử (ebook)</span>.
                        </p>
                        <div class="bg-white/10 p-4 rounded">
                            <h4 class="font-bold text-sm mb-2">📦 ĐƠN HÀNG SẼ ĐƯỢC CHIA THÀNH 2 PHẦN:</h4>
                            <ul class="text-sm space-y-1">
                                <li>• <span class="font-semibold">Đơn 1:</span> Chứa các sách vật lý → Giao hàng tận nơi, tính phí ship</li>
                                <li>• <span class="font-semibold">Đơn 2:</span> Chứa các ebook → Gửi email link tải ngay sau khi thanh toán</li>
                            </ul>
                            <p class="text-xs mt-2 opacity-90">* Phương thức thanh toán khi nhận hàng không khả dụng cho đơn hàng này.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($hasOnlyEbooks)
        <div class="bg-blue-600 text-white p-6 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
            <div class="relative z-10">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-white text-blue-600 flex items-center justify-center font-bold">
                            📚
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wide mb-2">ĐƠN HÀNG EBOOK</h3>
                        <p class="text-sm leading-relaxed mb-3">
                            Giỏ hàng của bạn chỉ có <span class="font-bold">sách điện tử (ebook)</span>. 
                            Bạn sẽ nhận link tải ebook qua email sau khi thanh toán thành công.
                        </p>
                        <div class="bg-white/10 p-4 rounded">
                            <h4 class="font-bold text-sm mb-2">💳 PHƯƠNG THỨC THANH TOÁN:</h4>
                            <ul class="text-sm space-y-1">
                                <li>• <span class="font-semibold">Thanh toán online:</span> Nhận link tải ngay sau khi thanh toán thành công</li>
                                <li>• <span class="text-gray-300">Thanh toán khi nhận hàng không áp dụng cho ebook</span></li>
                            </ul>
                            <p class="text-xs mt-2 opacity-90">* Không cần nhập địa chỉ giao hàng cho ebook.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif --}}

        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 xl:gap-12">
                <!-- Form thanh toán bên trái -->
            <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group">
                <!-- Adidas-style header -->
                <div class="bg-black text-white px-8 py-6 relative">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-1 h-8 bg-white"></div>
                            <h2 class="text-xl font-black uppercase tracking-wide">
                                THÔNG TIN THANH TOÁN
                            </h2>
                        </div>
                        <p class="text-gray-300 text-sm uppercase tracking-wider">BƯỚC 2 TRONG QUY TRÌNH ĐẶT HÀNG</p>
                    </div>
                </div>
                
                <div class="p-8">
                    <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
                        @csrf
                        {{-- Hidden fields for form submission --}}
                        <input type="hidden" name="final_total_amount" id="form_hidden_total_amount" value="{{ $hasOnlyEbooks ? $subtotal : $subtotal + 20000 }}">
                        <input type="hidden" name="discount_amount_applied" id="form_hidden_discount_amount" value="0">
                        <input type="hidden" name="applied_voucher_code" id="form_hidden_applied_voucher_code" value="">
                        <input type="hidden" name="shipping_fee_applied" id="form_hidden_shipping_fee" value="{{ $hasOnlyEbooks ? 0 : 20000 }}">
                        <input type="hidden" name="delivery_method" id="form_hidden_delivery_method" value="{{ $hasOnlyEbooks ? 'ebook' : 'delivery' }}">
            <input type="hidden" name="shipping_method" id="form_hidden_shipping_method" value="2">
                        
                        {{-- Hidden fields for GHN --}}
                        <input type="hidden" name="province_id" id="form_hidden_province_id" value="">
                        <input type="hidden" name="district_id" id="form_hidden_district_id" value="">
                        <input type="hidden" name="ward_code" id="form_hidden_ward_code" value="">
                        
                        {{-- Khu vực nhập địa chỉ mới --}}
                        <div id="new-address-form">
                            <div class="mb-8">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-1 h-6 bg-black"></div>
                                    <h3 class="text-lg font-black uppercase tracking-wide text-black">
                                        THÔNG TIN NGƯỜI NHẬN
                                    </h3>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="group">
                                    <label for="new_recipient_name" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                        TÊN NGƯỜI NHẬN *
                                    </label>
                                    <input type="text" name="new_recipient_name" id="new_recipient_name"
                                        class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg"
                                        placeholder="Nhập họ và tên đầy đủ" value="{{ old('new_recipient_name') }}">
                                    @error('new_recipient_name') 
                                        <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="group">
                                    <label for="new_phone" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                        SỐ ĐIỆN THOẠI *
                                    </label>
                                    <input type="text" name="new_phone" id="new_phone"
                                        class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg"
                                        placeholder="Nhập số điện thoại" value="{{ old('new_phone') }}">
                                    @error('new_phone') 
                                        <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-8 group">
                                <label for="new_email" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                    EMAIL (TÙY CHỌN)
                                </label>
                                <input type="email" name="new_email" id="new_email"
                                    class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg"
                                    placeholder="Nhập email để nhận thông báo đơn hàng" value="{{ old('new_email') }}">
                                @error('new_email') 
                                    <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Tab Navigation cho Địa chỉ -->
                            @if(!$hasOnlyEbooks)
                            <div class="mb-8" id="address-section">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-1 h-6 bg-black"></div>
                                    <h3 class="text-lg font-black uppercase tracking-wide text-black">
                                        ĐỊA CHỈ GIAO HÀNG
                                    </h3>
                                </div>
                                
                                <!-- Tab Headers -->
                                <div class="flex border-b border-gray-200 mb-6">
                                    <button type="button" id="existing-address-tab" 
                                            class="address-tab px-6 py-3 font-bold text-sm uppercase tracking-wide border-b-2 border-black text-black bg-gray-50 transition-all duration-300">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            Địa chỉ có sẵn
                                        </div>
                                    </button>
                                    <button type="button" id="new-address-tab" 
                                            class="address-tab px-6 py-3 font-bold text-sm uppercase tracking-wide border-b-2 border-transparent text-gray-500 hover:text-black hover:border-gray-300 transition-all duration-300">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Thêm địa chỉ mới
                                        </div>
                                    </button>
                                </div>
                                
                                <!-- Tab Content -->
                                <div class="tab-content">
                                    <!-- Existing Addresses Tab -->
                                    <div id="existing-address-content" class="address-tab-content">
                                        @if($addresses && count($addresses) > 0)
                                            <!-- Selected Address Display -->
                                            <div id="selected-address-display" class="hidden mb-6 p-4 border-2 border-green-500 bg-green-50 rounded-lg">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <h4 class="font-bold text-green-800">Địa chỉ đã chọn</h4>
                                                        </div>
                                                        <div id="selected-address-info" class="text-sm text-green-700">
                                                            <!-- Address info will be populated here -->
                                                        </div>
                                                    </div>
                                                    <button type="button" onclick="openAddressModal()" 
                                                            class="text-green-600 hover:text-green-800 font-medium text-sm underline">
                                                        Thay đổi
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Choose Address Button -->
                                            <div id="choose-address-btn-container" class="text-center">
                                                <button type="button" onclick="openAddressModal()" 
                                                        class="inline-flex items-center px-6 py-4 bg-black text-white font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-all duration-300 rounded-lg group">
                                                    <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    Chọn địa chỉ có sẵn
                                                    <span class="ml-2 text-xs bg-white/20 px-2 py-1 rounded">
                                                        {{ count($addresses) }} địa chỉ
                                                    </span>
                                                </button>
                                            </div>
                                            
                                            <!-- Hidden input for selected address -->
                                            <input type="hidden" name="address_id" id="selected_address_id" value="">
                                            
                                            @error('address_id')
                                            <p class="text-red-500 text-sm mt-3 font-medium">{{ $message }}</p>
                                            @enderror
                                        @else
                                            <div class="text-center py-8">
                                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <p class="text-gray-500 mb-4">Bạn chưa có địa chỉ nào được lưu</p>
                                                <button type="button" onclick="switchToNewAddressTab()" 
                                                        class="inline-flex items-center px-4 py-2 bg-black text-white font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-colors duration-300">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Thêm địa chỉ mới
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- New Address Tab -->
                                    <div id="new-address-content" class="address-tab-content hidden">
                                        <div class="space-y-6">
                                            <!-- Quick Actions -->
                                            <div class="flex flex-wrap gap-3 mb-6">
                                                <button type="button" id="clear-form-btn" 
                                                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium text-sm rounded-lg hover:bg-gray-700 transition-colors duration-300">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Xóa form
                                                </button>
                                            </div>
                                            
                                            <!-- Address Form -->
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="group">
                                                    <label for="tinh" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                                        TỈNH/THÀNH PHỐ *
                                                    </label>
                                                    <select id="tinh" name="new_address_city_id" required
                                                            class="w-full border-2 border-gray-300 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg">
                                                        <option value="">Chọn Tỉnh/Thành phố</option>
                                                    </select>
                                                    <input type="hidden" name="new_address_city_name" id="ten_tinh">
                                                    @error('new_address_city_id') <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                                                    @error('new_address_city_name') <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                                                </div>
                                                <div class="group">
                                                    <label for="quan" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                                        QUẬN/HUYỆN *
                                                    </label>
                                                    <select id="quan" name="new_address_district_id" required
                                                            class="w-full border-2 border-gray-300 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg">
                                                        <option value="">Chọn Quận/Huyện</option>
                                                    </select>
                                                    <input type="hidden" name="new_address_district_name" id="ten_quan">
                                                    @error('new_address_district_id') <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                                                    @error('new_address_district_name') <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                                                </div>
                                                <div class="group">
                                                    <label for="phuong" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                                        PHƯỜNG/XÃ *
                                                    </label>
                                                    <select id="phuong" name="new_address_ward_id" required
                                                            class="w-full border-2 border-gray-300 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg">
                                                        <option value="">Chọn Phường/Xã</option>
                                                    </select>
                                                    <input type="hidden" name="new_address_ward_name" id="ten_phuong">
                                                     @error('new_address_ward_id') <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                                                     @error('new_address_ward_name') <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                                                 </div>
                                             </div>
                                             
                                             <!-- Địa chỉ cụ thể -->
                                             <div class="group">
                                                 <label for="new_address_detail" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                                     ĐỊA CHỈ CỤ THỂ *
                                                 </label>
                                                 <input type="text" name="new_address_detail" id="new_address_detail"
                                                        class="w-full border-2 border-gray-300 px-4 py-3 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg"
                                                        style="height: 3.5rem; line-height: 1.75; font-size: 14px;"
                                                        placeholder="Ví dụ: Số 123, Đường ABC, Tòa nhà XYZ" value="{{ old('new_address_detail') }}">
                                                 @error('new_address_detail') <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                                             </div>
                                             
                                             <!-- Checkbox lưu địa chỉ -->
                                             <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                                 <input type="checkbox" id="save_address" name="save_address" value="1" 
                                                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                 <div class="flex-1">
                                                     <label for="save_address" class="text-sm font-medium text-blue-900 cursor-pointer">
                                                         Lưu địa chỉ này cho lần mua hàng tiếp theo
                                                     </label>
                                                     <p class="text-xs text-blue-700 mt-1">
                                                         Địa chỉ sẽ được lưu vào tài khoản của bạn để sử dụng cho các đơn hàng sau
                                                     </p>
                                                 </div>
                                             </div>
                                             
                                             <!-- Address Validation Status -->
                                             <div id="address-validation-status" class="hidden">
                                                 <div class="flex items-center gap-2 p-3 rounded-lg">
                                                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                     </svg>
                                                     <span class="text-sm font-medium">Địa chỉ hợp lệ</span>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             @endif
                        
                        <!-- Phương thức vận chuyển - Ẩn ban đầu, hiển thị khi chọn địa chỉ -->
                        @if(!$hasOnlyEbooks)
                        <div class="mb-8 shipping-section" style="display: none;">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-1 h-6 bg-black"></div>
                                <h3 class="text-lg font-black uppercase tracking-wide text-black">
                                    PHƯƠNG THỨC VẬN CHUYỂN
                                </h3>
                            </div>
                            
                            <!-- Loading state -->
                            <div id="shipping-services-loading" class="text-center py-8">
                                <div class="inline-flex items-center gap-3 text-gray-600">
                                    <div class="w-8 h-8 bg-black text-white flex items-center justify-center">
                                        <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                    <span class="font-bold uppercase tracking-wide">Đang tải dịch vụ vận chuyển...</span>
                                </div>
                            </div>
                            
                            <!-- Services container -->
                            <div id="shipping-services-container" class="hidden">
                                <div id="shipping-services-list" class="space-y-4">
                                    <!-- Services will be loaded here -->
                                </div>
                            </div>
                            
                            <!-- Fallback options -->
                            <div id="shipping-services-fallback" class="hidden">
                                <div class="space-y-4">
                                    <!-- Nhận hàng trực tiếp -->
                                    <label class="group cursor-pointer block">
                                        <div class="relative border-2 border-gray-300 hover:border-black transition-all duration-500 bg-white group-hover:shadow-lg overflow-hidden">
                                            <!-- Adidas-style accent line -->
                                            <div class="absolute left-0 top-0 w-1 h-full bg-black opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                            
                                            <div class="p-6 relative">
                                                <!-- Radio button -->
                                                <input type="radio" name="shipping_method" value="pickup"
                                                       class="absolute right-6 top-6 h-5 w-5 text-black focus:ring-black focus:ring-2 border-2 border-gray-400">
                                                
                                                <!-- Content -->
                                                <div class="pr-12">
                                                    <div class="flex items-center gap-3 mb-3">
                                                        <!-- Shipping icon -->
                                                        <div class="w-10 h-10 bg-black text-white flex items-center justify-center">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                            </svg>
                                                        </div>
                                                        
                                                        <!-- Method info -->
                                                        <div>
                                                            <h4 class="text-base font-black uppercase tracking-wide text-black">
                                                                Nhận hàng tại cửa hàng
                                                            </h4>
                                                            <p class="text-sm text-gray-600 mt-1">Miễn phí vận chuyển</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Price display -->
                                                    <div class="mt-4 p-4 bg-gray-50 border-l-4 border-black">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-sm font-bold uppercase tracking-wide text-gray-700">PHÍ VẬN CHUYỂN:</span>
                                                            <span class="text-lg font-black text-green-600">0đ</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Adidas-style corner accent -->
                                                <div class="absolute top-0 right-0 w-8 h-8 bg-black opacity-5 transform rotate-45 translate-x-4 -translate-y-4 group-hover:opacity-10 transition-opacity duration-300"></div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            @error('shipping_method')
                            <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500">
                                <p class="text-sm text-red-700 font-medium">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>
                        @endif
                        
                        <!-- Phương thức thanh toán -->
                        <div class="mb-8">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-1 h-6 bg-black"></div>
                                <h3 class="text-lg font-black uppercase tracking-wide text-black">
                                    PHƯƠNG THỨC THANH TOÁN
                                </h3>
                            </div>
                            
                            <div class="space-y-4">
                                @foreach($paymentMethods as $method)
                                <label class="group cursor-pointer block">
                                    <div class="relative border-2 border-gray-300 hover:border-black transition-all duration-500 bg-white group-hover:shadow-lg overflow-hidden">
                                        <!-- Adidas-style accent line -->
                                        <div class="absolute left-0 top-0 w-1 h-full bg-black opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        
                                        <div class="p-6 relative">
                                            <!-- Radio button -->
                                            <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                                   class="absolute right-6 top-6 h-5 w-5 text-black focus:ring-black focus:ring-2 border-2 border-gray-400" required>
                                            
                                            <!-- Content -->
                                            <div class="pr-12">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <!-- Payment method icon -->
                                                    <div class="w-10 h-10 bg-black text-white flex items-center justify-center">
                                                        @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                            </svg>
                                                        @elseif(str_contains(strtolower($method->name), 'momo'))
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M12 2C6.477 2 2 6.477 2 12c0 5.524 4.477 10 10 10s10-4.476 10-10c0-5.523-4.477-10-10-10z"/>
                                                            </svg>
                                                        @elseif(str_contains(strtolower($method->name), 'vnpay'))
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Method name -->
                                                    <div>
                                                        <h4 class="text-base font-black uppercase tracking-wide text-black">
                                                            {{ $method->name }}
                                                        </h4>
                                                        @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                            <p class="text-sm text-gray-600 mt-1">Thanh toán nhanh chóng với ví điện tử</p>
                                                        @elseif(str_contains(strtolower($method->name), 'momo'))
                                                            <p class="text-sm text-gray-600 mt-1">Thanh toán qua ví MoMo</p>
                                                        @elseif(str_contains(strtolower($method->name), 'vnpay'))
                                                            <p class="text-sm text-gray-600 mt-1">Thanh toán qua cổng VNPay</p>
                                                        @elseif(str_contains(strtolower($method->name), 'khi nhận hàng'))
                                                            <p class="text-sm text-gray-600 mt-1">Thanh toán khi shipper giao hàng</p>
                                                        @else
                                                            <p class="text-sm text-gray-600 mt-1">Phương thức thanh toán an toàn</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <!-- Wallet balance for e-wallet -->
                                                @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                    <div class="mt-4 p-4 bg-gray-50 border-l-4 border-black">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-sm font-bold uppercase tracking-wide text-gray-700">SỐ DƯ VÍ:</span>
                                                            <span class="text-lg font-black text-black">
                                                                @if($wallet)
                                                                    {{ number_format($wallet->balance) }}đ
                                                                @else
                                                                    0đ
                                                                @endif
                                                            </span>
                                                        </div>
                                                        @if(!$wallet || $wallet->balance == 0)
                                                            <p class="text-xs text-red-600 mt-2 font-medium">
                                                                ⚠️ Số dư ví không đủ để thanh toán
                                                            </p>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <!-- Special notes for payment methods -->
                                                @if(str_contains(strtolower($method->name), 'khi nhận hàng') && $hasOnlyEbooks)
                                                    <div class="mt-3 p-3 bg-red-50 border-l-4 border-red-500">
                                                        <p class="text-xs text-red-700 font-medium">
                                                            ❌ Không khả dụng cho đơn hàng ebook
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Adidas-style corner accent -->
                                            <div class="absolute top-0 right-0 w-8 h-8 bg-black opacity-5 transform rotate-45 translate-x-4 -translate-y-4 group-hover:opacity-10 transition-opacity duration-300"></div>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            
                            @error('payment_method_id')
                            <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500">
                                <p class="text-sm text-red-700 font-medium">{{ $message }}</p>
                            </div>
                            @enderror
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-8">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-1 h-6 bg-black"></div>
                                <h3 class="text-lg font-black uppercase tracking-wide text-black">
                                    GHI CHÚ ĐƠN HÀNG
                                </h3>
                            </div>
                            
                            <div class="group">
                                <label class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-3">
                                    THÔNG TIN BỔ SUNG (TÙY CHỌN)
                                </label>
                                <div class="relative">
                                    <textarea name="note" rows="4"
                                        class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg resize-none"
                                        placeholder="Nhập ghi chú cho đơn hàng của bạn (yêu cầu đặc biệt, thời gian giao hàng mong muốn...)">{{ old('note') }}</textarea>
                                    
                                    <!-- Adidas-style accent line -->
                                    <div class="absolute left-0 top-0 w-1 h-full bg-black opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 uppercase tracking-wide">
                                    * Ghi chú sẽ được gửi đến bộ phận xử lý đơn hàng
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-12">
                            <button type="submit"
                                class="w-full bg-black text-white py-6 px-8 text-lg font-black uppercase tracking-wider
                                       hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-400 focus:ring-offset-2 
                                       transform hover:scale-[1.02] transition-all duration-300 relative overflow-hidden group">
                                
                                <!-- Button background effect -->
                                <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-black transform -skew-x-12 -translate-x-full group-hover:translate-x-0 transition-transform duration-500"></div>
                                
                                <!-- Button content -->
                                <div class="relative z-10 flex items-center justify-center gap-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>ĐẶT HÀNG NGAY</span>
                                    <div class="w-1 h-6 bg-white opacity-50"></div>
                                </div>
                                
                                <!-- Adidas-style corner accent -->
                                <div class="absolute top-0 right-0 w-12 h-12 bg-white opacity-10 transform rotate-45 translate-x-6 -translate-y-6"></div>
                            </button>
                            
                            <!-- Security notice -->
                            <div class="mt-4 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">
                                    🔒 Thanh toán an toàn & bảo mật
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

                <!-- Thông tin đơn hàng - Cột bên phải -->
                <div class="order-1 lg:order-2">
            <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group">
                <!-- Adidas-style header -->
                <div class="bg-black text-white px-8 py-6 relative">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-1 h-8 bg-white"></div>
                            <h2 class="text-xl font-black uppercase tracking-wide">
                                THÔNG TIN ĐƠN HÀNG
                            </h2>
                        </div>
                        <p class="text-gray-300 text-sm uppercase tracking-wider">TỔNG QUAN GIỎ HÀNG</p>
                    </div>
                </div>
                
                <div class="p-8">
                    <!-- Danh sách sản phẩm -->
                    <div class="space-y-3 mb-6">
                        @foreach($cartItems as $item)
                        @php
                            // Sử dụng trực tiếp giá từ cart item (đã bao gồm discount và extra price)
                            $finalPrice = $item->price ?? 0;
                        @endphp
                        <div class="group flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:border-black transition-all duration-300 hover:shadow-md relative">
                            @if(isset($item->is_combo) && $item->is_combo)
                                <!-- Hiển thị combo -->
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $item->collection && $item->collection->cover_image ? asset('storage/' . $item->collection->cover_image) : asset('images/default-book.svg') }}"
                                         alt="{{ $item->collection ? $item->collection->name : 'Combo' }}" 
                                         class="w-16 h-20 object-cover rounded shadow-sm">
                                    <div class="absolute -top-1 -right-1">
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-green-600 rounded-full">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                    <!-- Badge combo -->
                                    <div class="absolute -bottom-1 -left-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold bg-green-600 text-white">
                                            COMBO
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-semibold text-gray-900 text-sm truncate">
                                        {{ $item->collection ? $item->collection->name : 'Combo không xác định' }}
                                    </h6>
                                    @if($item->collection && $item->collection->books && $item->collection->books->count() > 0)
                                        <div class="mt-1 space-y-1">
                                            <p class="text-xs text-gray-500">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    {{ $item->collection->books->count() }} cuốn sách
                                                </span>
                                            </p>
                                            <p class="text-xs text-green-600 font-medium">
                                                💰 Tiết kiệm so với mua lẻ
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 text-sm">
                                        {{ number_format($finalPrice * $item->quantity) }}đ
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ number_format($finalPrice) }}đ/combo
                                    </p>
                                </div>
                            @else
                                <!-- Hiển thị sách đơn lẻ -->
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $item->book && $item->book->cover_image ? asset('storage/' . $item->book->cover_image) : asset('images/default-book.svg') }}"
                                         alt="{{ $item->book ? $item->book->title : 'Sách' }}" 
                                         class="w-16 h-20 object-cover rounded shadow-sm">
                                    <div class="absolute -top-1 -right-1">
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-600 rounded-full">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-semibold text-gray-900 text-sm truncate">
                                        {{ $item->book ? $item->book->title : 'Sách không xác định' }}
                                    </h6>
                                    <div class="mt-1 space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->bookFormat && $item->bookFormat->format_name == 'Ebook' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $item->bookFormat ? $item->bookFormat->format_name : 'N/A' }}
                                        </span>
                                        @if($item->book && $item->book->authors && $item->book->authors->count() > 0)
                                            <p class="text-xs text-gray-500">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    {{ $item->book->authors->pluck('name')->join(', ') }}
                                                </span>
                                            </p>
                                        @endif
                                        
                                        <!-- Hiển thị thuộc tính biến thể -->
                                        @if(!$item->isCombo() && $item->attributeValues && $item->attributeValues->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($item->attributeValues as $attributeValue)
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded border">
                                                        {{ $attributeValue->attribute->name ?? 'Thuộc tính' }}: {{ $attributeValue->value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <!-- Hiển thị quà tặng kèm theo -->
                                        @if(isset($item->gifts) && $item->gifts && $item->gifts->count() > 0)
                                            <div class="mt-2 p-2 bg-orange-50 border border-orange-200 rounded">
                                                <div class="flex items-center gap-1 text-xs text-orange-700">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="font-medium">{{ $item->gifts->count() }} quà tặng kèm</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 text-sm">
                                        {{ number_format($finalPrice * $item->quantity) }}đ
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ number_format($finalPrice) }}đ/cuốn
                                    </p>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Mã giảm giá -->
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1 h-5 bg-black"></div>
                            <h3 class="text-base font-bold uppercase tracking-wide text-black">
                                MÃ GIẢM GIÁ
                            </h3>
                        </div>
                        
                        <div class="space-y-3">
                            <!-- Voucher đã chọn (nếu có) -->
                            <div id="selected-voucher-info" class="hidden p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-semibold text-green-800" id="selected-voucher-code"></span>
                                        </div>
                                        <p class="text-sm text-green-700 mt-1" id="selected-voucher-desc"></p>
                                        <p class="text-xs text-green-600" id="selected-voucher-discount"></p>
                                    </div>
                                    <button type="button" id="remove-voucher-btn" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Input và buttons -->
                            <div id="voucher-input-section">
                                <input type="text" name="voucher_code_input" id="voucher_code_input"
                                    class="w-full border border-gray-300 px-3 py-2 rounded focus:border-black focus:ring-0 transition-all duration-300 text-sm"
                                    placeholder="Nhập mã giảm giá">
                                
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <button type="button" id="open-voucher-modal-btn"
                                        class="bg-white border border-black text-black px-3 py-2 text-sm font-medium hover:bg-black hover:text-white transition-all duration-300">
                                        Chọn mã
                                    </button>
                                    <button type="button" id="apply-voucher-btn-new"
                                        class="bg-black text-white px-3 py-2 text-sm font-medium hover:bg-gray-800 transition-all duration-300">
                                        Áp dụng
                                    </button>
                                </div>
                            </div>
                            
                            <div id="voucher-message-new" class="text-sm font-medium"></div>
                        </div>
                    </div>

                    <!-- Tổng kết -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1 h-5 bg-black"></div>
                            <h3 class="text-base font-bold uppercase tracking-wide text-black">
                                TỔNG KẾT
                            </h3>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-600">Tạm tính</span>
                                <span class="font-medium text-gray-900">{{ number_format($subtotal) }}đ</span>
                            </div>
                            
                            <!-- Phí vận chuyển với thông tin chi tiết -->
                            <div class="flex justify-between items-center py-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600">Phí vận chuyển</span>
                                    <div id="shipping-info-icon" class="hidden">
                                        <svg class="w-4 h-4 text-blue-500 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Thông tin vận chuyển">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span id="shipping-fee" class="font-medium text-gray-900">{{ $hasOnlyEbooks ? '0đ' : 'Chưa tính' }}</span>
                                    <div id="shipping-service-name" class="text-xs text-gray-500 hidden"></div>
                                </div>
                            </div>
                            
                            <!-- Thời gian giao hàng dự kiến -->
                            <div id="delivery-time-info" class="flex justify-between items-center py-1 text-xs text-gray-500 hidden">
                                <span>Thời gian giao hàng dự kiến:</span>
                                <span id="delivery-time">-</span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-600">Giảm giá</span>
                                <span id="discount-amount" class="font-medium text-green-600">0đ</span>
                            </div>
                            
                            <hr class="border-gray-200">
                            
                            <!-- Tổng cộng -->
                            <div class="flex justify-between items-center py-3">
                                <span class="text-lg font-bold text-gray-900">TỔNG CỘNG</span>
                                <span id="total-amount" class="text-lg font-bold text-blue-600">
                                    {{ $hasOnlyEbooks ? number_format($subtotal) : number_format($subtotal) }}đ
                                </span>
                            </div>
                            
                            <!-- Thông báo phí ship -->
                            <div id="shipping-notice" class="text-xs text-gray-500 text-center py-2 {{ $hasOnlyEbooks ? 'hidden' : '' }}">
                                💡 Phí vận chuyển sẽ được tính khi bạn chọn địa chỉ giao hàng
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>

<!-- Voucher Modal -->
<div id="voucher-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none">
    <div class="relative mx-auto p-6 w-full max-w-2xl max-h-[80vh] bg-white rounded-lg shadow-xl">
        <div class="flex justify-between items-center pb-4 border-b">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Chọn mã giảm giá</h2>
                <p class="text-sm text-gray-600 mt-1">Chọn voucher phù hợp để tiết kiệm chi phí</p>
            </div>
            <button id="close-voucher-modal-btn" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="voucher-list-modal" class="mt-4 space-y-3 max-h-96 overflow-y-auto">
            @if(isset($vouchers) && count($vouchers))
                @foreach($vouchers as $voucher)
                <div class="voucher-item-modal group border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all duration-200 cursor-pointer" data-code="{{ $voucher->code }}">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Mã voucher -->
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                        {{ $voucher->code }}
                                    </div>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                        -{{ $voucher->discount_percent }}%
                                    </span>
                                </div>
                                
                                <!-- Tên voucher -->
                                <h4 class="font-semibold text-gray-900 mb-2">{{ $voucher->description }}</h4>
                                
                                <!-- Thông tin giảm giá -->
                                <div class="space-y-1 text-sm">
                                    <div class="flex items-center gap-2 text-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <span class="font-medium">
                                            Giảm {{ $voucher->discount_percent }}%
                                            @if($voucher->max_discount)
                                                (tối đa {{ number_format($voucher->max_discount) }}đ)
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if($voucher->min_order_value)
                                        <div class="flex items-center gap-2 text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                            <span>Đơn hàng từ {{ number_format($voucher->min_order_value) }}đ</span>
                                        </div>
                                    @endif
                                    
                                    @if($voucher->valid_to)
                                        <div class="flex items-center gap-2 text-orange-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>HSD: {{ \Carbon\Carbon::parse($voucher->valid_to)->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($voucher->quantity !== null)
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span>Còn lại: {{ $voucher->quantity }} lượt</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Nút chọn -->
                            <div class="ml-4">
                                <button type="button" class="select-voucher-from-modal-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 group-hover:bg-blue-600" data-code="{{ $voucher->code }}">
                                    Chọn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Không có mã giảm giá nào</p>
                    <p class="text-gray-400 text-sm mt-1">Hiện tại chưa có voucher khả dụng</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Address Selection Modal -->
<div id="address-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none">
    <div class="relative mx-auto p-6 w-full max-w-3xl max-h-[85vh] bg-white rounded-lg shadow-xl">
        <div class="flex justify-between items-center pb-4 border-b">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Chọn địa chỉ giao hàng</h2>
                <p class="text-sm text-gray-600 mt-1">Chọn một trong các địa chỉ đã lưu của bạn</p>
            </div>
            <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mt-4 space-y-3 max-h-96 overflow-y-auto">
            @if($addresses && count($addresses) > 0)
                @foreach($addresses as $address)
                <div class="group border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all duration-200 cursor-pointer" 
                     onclick="selectAddress('{{ $address->id }}', '{{ addslashes($address->recipient_name) }}', '{{ $address->phone }}', '{{ addslashes($address->address_detail) }}', '{{ addslashes($address->ward) }}', '{{ addslashes($address->district) }}', '{{ addslashes($address->city) }}')">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Thông tin người nhận -->
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="font-bold text-gray-900">{{ $address->recipient_name }}</h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $address->phone }}
                                    </span>
                                </div>
                                
                                <!-- Địa chỉ chi tiết -->
                                <p class="text-sm text-gray-600 leading-relaxed mb-3">
                                    {{ $address->address_detail }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                                </p>
                                
                                <!-- Thông tin bổ sung -->
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    @if($address->district_id && $address->ward_code)
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-green-600 font-medium">Có thể tính phí ship</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3 h-3 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-orange-600 font-medium">Cần cập nhật thông tin GHN</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>{{ $address->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Nút chọn -->
                            <div class="ml-4">
                                <div class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 group-hover:bg-blue-600">
                                    Chọn địa chỉ này
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Chưa có địa chỉ nào</p>
                    <p class="text-gray-400 text-sm mt-1">Vui lòng thêm địa chỉ mới để tiếp tục</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hàm hỗ trợ định dạng số tiền
let discountValue = 0; // Global discount value

// Hàm hỗ trợ định dạng số tiền
function number_format(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

// Hàm cập nhật tổng tiền hiển thị
function updateTotal() {
    console.log('updateTotal called');
    const subtotalValue = {{ $subtotal }}; // Use the correct subtotal from controller
    const shippingFeeText = document.getElementById('shipping-fee').textContent.trim();
    const shippingFee = parseFloat(shippingFeeText.replace(/\./g, "")) || 0;

    console.log(`Subtotal: ${subtotalValue}, Discount: ${discountValue}, Shipping: ${shippingFee}`);

    let total = subtotalValue - discountValue + shippingFee;
    total = Math.max(0, total); // Ensure total is not negative
    document.getElementById('total-amount').textContent = `${number_format(total)}đ`;

    // Update hidden form fields
    document.getElementById('form_hidden_total_amount').value = total;
    document.getElementById('form_hidden_discount_amount').value = discountValue; // Use global discountValue
    document.getElementById('form_hidden_shipping_fee').value = shippingFee;
    console.log('Hidden fields updated:', {
        total: document.getElementById('form_hidden_total_amount').value,
        discount: document.getElementById('form_hidden_discount_amount').value,
        shipping: document.getElementById('form_hidden_shipping_fee').value,
        voucher_code: document.getElementById('form_hidden_applied_voucher_code').value
    });

// Kiểm tra số dư ví khi chọn phương thức thanh toán
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method_id"]');
    const submitButton = document.querySelector('button[type="submit"]');
    const userWalletBalance = {{ $wallet ? $wallet->balance : 0 }};
    
    function checkWalletBalance() {
        const selectedPaymentMethod = document.querySelector('input[name="payment_method_id"]:checked');
        if (!selectedPaymentMethod) return;
        
        const paymentMethodLabel = selectedPaymentMethod.closest('label');
        const methodName = paymentMethodLabel.querySelector('span').textContent.toLowerCase();
        
        if (methodName.includes('ví điện tử')) {
            const totalAmount = parseFloat(document.getElementById('total-amount').textContent.replace(/[^\d]/g, ''));
            
            if (userWalletBalance < totalAmount) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.textContent = 'SỐ DƯ VÍ KHÔNG ĐỦ';
                
                // Hiển thị thông báo lỗi
                if (typeof toastr !== 'undefined') {
                    toastr.error('Số dư ví không đủ để thanh toán. Vui lòng nạp thêm tiền hoặc chọn phương thức thanh toán khác.');
                }
            } else {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.textContent = 'ĐẶT HÀNG NGAY';
            }
        } else {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            submitButton.textContent = 'ĐẶT HÀNG NGAY';
        }
    }
    
    // Lắng nghe sự kiện thay đổi phương thức thanh toán
    paymentMethodInputs.forEach(input => {
        input.addEventListener('change', checkWalletBalance);
    });
    
    // Kiểm tra ban đầu
    checkWalletBalance();
    
    // Kiểm tra lại khi tổng tiền thay đổi (do voucher)
    const observer = new MutationObserver(checkWalletBalance);
    const totalAmountElement = document.getElementById('total-amount');
    if (totalAmountElement) {
        observer.observe(totalAmountElement, { childList: true, subtree: true });
    }
});
}

// Logic cũ đã được thay thế bằng logic mới ở trên

document.getElementById('apply-voucher-btn-new').addEventListener('click', function() {
    const applyBtn = this;
    const originalBtnText = applyBtn.textContent;
    const voucherCode = document.getElementById('voucher_code_input').value;
    const discountEl = document.getElementById('discount-amount');
    console.log(voucherCode);

    if (!voucherCode) {
        toastr.warning('Vui lòng nhập mã giảm giá.', '⚠️ Lưu ý!');
        discountEl.textContent = '0đ';
        discountValue = 0; // Reset global discount
        updateTotal();
        return;
    }

    applyBtn.disabled = true;
    applyBtn.textContent = 'Đang xử lý...';

    const currentSubtotal = {{ $subtotal }};

    fetch(`{{ route('orders.apply-voucher') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ voucher_code: voucherCode, subtotal: currentSubtotal })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Apply voucher response:', data);

        if (data.success == true) {
            discountValue = parseFloat(data.discount_amount) || 0;
            if (isNaN(discountValue)) {
                console.error('Failed to parse discount_amount from server:', data.discount_amount);
                discountValue = 0;
            }

            const successMessage = data.max_discount_applied_message
                ? data.max_discount_applied_message
                : `Áp dụng mã giảm giá "${data.voucher_code}" thành công. Bạn được giảm ${number_format(discountValue)}đ.`;

            toastr.success(successMessage, '🎉 Thành công!');
            discountEl.textContent = `-${number_format(discountValue)}đ`;
            document.getElementById('form_hidden_applied_voucher_code').value = data.voucher_code;
            document.getElementById('form_hidden_discount_amount').value = discountValue;
            
            // Hiển thị thông tin voucher đã chọn
            if (typeof showSelectedVoucher === 'function') {
                showSelectedVoucher({
                    code: data.voucher_code,
                    name: data.voucher_name || 'Mã giảm giá',
                    discount_amount: discountValue
                });
            }

        } else {
            if (data.errors && Array.isArray(data.errors)) {
                data.errors.forEach(error => toastr.error(error, '❌ Lỗi!'));
            } else {
                toastr.error(data.message || 'Mã giảm giá không hợp lệ hoặc có lỗi xảy ra.', '❌ Lỗi!');
            }
            discountEl.textContent = '0đ';
            document.getElementById('form_hidden_applied_voucher_code').value = '';
            document.getElementById('form_hidden_discount_amount').value = 0;
        }

        updateTotal();
    })
    .catch(error => {
        console.error('Error applying voucher:', error);
        toastr.error('Có lỗi xảy ra khi áp dụng mã giảm giá.', '❌ Lỗi!');
        discountEl.textContent = '0đ';
        document.getElementById('form_hidden_applied_voucher_code').value = '';
        document.getElementById('form_hidden_discount_amount').value = 0;
        updateTotal();
    })
    .finally(() => {
        applyBtn.disabled = false;
        applyBtn.textContent = originalBtnText;
    });
});

// Hàm áp dụng mã giảm giá được gợi ý
function applySuggestedVoucher(code, event) {
    event.preventDefault();
    const input = document.querySelector('input[name="voucher_code"]');
    input.value = code;
    document.getElementById('apply-voucher').click();
}

// Cập nhật tổng tiền lần đầu khi trang load
updateTotal();


document.addEventListener('DOMContentLoaded', function () {
    const voucherModal = document.getElementById('voucher-modal');
    const openModalBtn = document.getElementById('open-voucher-modal-btn');
    const closeModalBtn = document.getElementById('close-voucher-modal-btn');
    const voucherCodeInput = document.getElementById('voucher_code_input');
    const applyVoucherBtnNew = document.getElementById('apply-voucher-btn-new');
    const voucherMessageElNew = document.getElementById('voucher-message-new');
    const hiddenAppliedVoucherCode = document.getElementById('form_hidden_applied_voucher_code');
    const discountAmountEl = document.getElementById('discount-amount');

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            if (voucherModal) {
                voucherModal.classList.remove('opacity-0', 'pointer-events-none');
                voucherModal.classList.add('opacity-100');
            }
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function () {
            if (voucherModal) {
                voucherModal.classList.remove('opacity-100');
                voucherModal.classList.add('opacity-0', 'pointer-events-none');
            }
        });
    }

    if (voucherModal) {
        voucherModal.addEventListener('click', function (event) {
            if (event.target === voucherModal) {
                voucherModal.classList.remove('opacity-100');
                voucherModal.classList.add('opacity-0', 'pointer-events-none');
            }
        });
    }

    const voucherItems = document.querySelectorAll('.voucher-item-modal');
    voucherItems.forEach(item => {
        item.addEventListener('click', function (event) {
            if (event.target.closest('.select-voucher-from-modal-btn')) {
                return;
            }
            const code = this.dataset.code;
            if (voucherCodeInput) voucherCodeInput.value = code;
            if (voucherModal) voucherModal.style.display = 'none';
        });
    });

    const selectVoucherButtonsModal = document.querySelectorAll('.select-voucher-from-modal-btn');
    selectVoucherButtonsModal.forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const code = this.dataset.code;
            if (voucherCodeInput) voucherCodeInput.value = code;
            
            // Đóng modal
            if (voucherModal) {
                voucherModal.classList.remove('opacity-100');
                voucherModal.classList.add('opacity-0', 'pointer-events-none');
            }
            
            // Tự động áp dụng voucher
            setTimeout(() => {
                if (applyVoucherBtnNew) applyVoucherBtnNew.click();
            }, 300);
        });
    });
    
    // Xử lý nút xóa voucher
    const removeVoucherBtn = document.getElementById('remove-voucher-btn');
    if (removeVoucherBtn) {
        removeVoucherBtn.addEventListener('click', function() {
            // Reset voucher
            if (voucherCodeInput) voucherCodeInput.value = '';
            if (hiddenAppliedVoucherCode) hiddenAppliedVoucherCode.value = '';
            if (discountAmountEl) discountAmountEl.textContent = '0đ';
            
            // Ẩn thông tin voucher đã chọn
            const selectedVoucherInfo = document.getElementById('selected-voucher-info');
            const voucherInputSection = document.getElementById('voucher-input-section');
            if (selectedVoucherInfo) selectedVoucherInfo.classList.add('hidden');
            if (voucherInputSection) voucherInputSection.classList.remove('hidden');
            
            // Reset discount
            discountValue = 0;
            updateTotal();
            
            if (typeof toastr !== 'undefined') {
                toastr.info('Đã xóa mã giảm giá');
            }
        });
    }
    
    // Hàm hiển thị thông tin voucher đã chọn
    function showSelectedVoucher(voucherData) {
        const selectedVoucherInfo = document.getElementById('selected-voucher-info');
        const voucherInputSection = document.getElementById('voucher-input-section');
        const selectedVoucherCode = document.getElementById('selected-voucher-code');
        const selectedVoucherDesc = document.getElementById('selected-voucher-desc');
        const selectedVoucherDiscount = document.getElementById('selected-voucher-discount');
        
        if (selectedVoucherCode) selectedVoucherCode.textContent = voucherData.code;
        if (selectedVoucherDesc) selectedVoucherDesc.textContent = voucherData.description || 'Mã giảm giá';
        if (selectedVoucherDiscount) {
            selectedVoucherDiscount.textContent = `Giảm ${number_format(voucherData.discount_amount)}đ`;
        }
        
        if (selectedVoucherInfo) selectedVoucherInfo.classList.remove('hidden');
        if (voucherInputSection) voucherInputSection.classList.add('hidden');
    }
    
    // Đặt function ở global scope
    window.showSelectedVoucher = showSelectedVoucher;

    if (applyVoucherBtnNew && voucherCodeInput && voucherMessageElNew && hiddenAppliedVoucherCode && discountAmountEl) {
        applyVoucherBtnNew.addEventListener('click', function() {
            const voucherCode = voucherCodeInput.value.trim();
            const subtotalForVoucher = {{ $subtotal }};

            if (!voucherCode) {
                voucherMessageElNew.innerHTML = '<p class="text-red-500">Vui lòng nhập mã giảm giá hoặc chọn từ danh sách.</p>';
                if (typeof toastr !== 'undefined') toastr.warning('Vui lòng nhập mã giảm giá hoặc chọn từ danh sách.');
                return;
            }

            applyVoucherBtnNew.disabled = true;
            applyVoucherBtnNew.textContent = 'Đang áp dụng...';
            voucherMessageElNew.innerHTML = '';

            fetch(`{{ route('orders.apply-voucher') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    voucher_code: voucherCode,
                    subtotal: subtotalForVoucher
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw { status: response.status, data: errData };
                    }).catch(() => {
                        throw { status: response.status, data: { message: `Lỗi HTTP: ${response.status}` } };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    discountValue = parseFloat(data.discount_amount || 0);
                    hiddenAppliedVoucherCode.value = data.voucher_code || '';
                    discountAmountEl.textContent = `-${number_format(discountValue)}đ`;

                    // Hiển thị thông tin voucher đã chọn
                    showSelectedVoucher({
                        code: data.voucher_code,
                        description: data.voucher_description || 'Mã giảm giá',
                        discount_amount: discountValue
                    });

                    voucherMessageElNew.innerHTML = `<p class="text-green-500">${data.message || `Áp dụng mã giảm giá "${data.voucher_code}" thành công. Bạn được giảm ${number_format(discountValue)}đ.`}</p>`;
                    if (typeof toastr !== 'undefined') toastr.success(data.message || `Áp dụng mã giảm giá "${data.voucher_code}" thành công. Bạn được giảm ${number_format(discountValue)}đ.`);
                } else {
                    discountValue = 0;
                    hiddenAppliedVoucherCode.value = '';
                    discountAmountEl.textContent = '0đ';

                    let errorMessages = '<ul class="list-disc list-inside">';
                    if (data.errors && typeof data.errors === 'object' && Object.keys(data.errors).length > 0) {
                        for (const key in data.errors) {
                            if (Array.isArray(data.errors[key])) {
                                data.errors[key].forEach(msg => { errorMessages += `<li class="text-red-500">${msg}</li>`; });
                            } else { errorMessages += `<li class="text-red-500">${data.errors[key]}</li>`; }
                        }
                    } else if (data.message) {
                         errorMessages += `<li class="text-red-500">${data.message}</li>`;
                    } else {
                        errorMessages += '<li class="text-red-500">Không thể áp dụng mã giảm giá.</li>';
                    }
                    errorMessages += '</ul>';
                    voucherMessageElNew.innerHTML = errorMessages;
                    if (typeof toastr !== 'undefined') toastr.error(data.message || 'Không thể áp dụng mã giảm giá. Vui lòng kiểm tra lại.');
                }
            })
            .catch(error => {
                console.error('Lỗi khi áp dụng voucher:', error);
                discountValue = 0;
                hiddenAppliedVoucherCode.value = '';
                discountAmountEl.textContent = '0đ';

                let errorMessageText = 'Có lỗi xảy ra khi áp dụng mã giảm giá.';
                let detailedErrorsHtml = '<ul class="list-disc list-inside">';
                let hasDetailedErrors = false;

                if (error && error.data) {
                    if (error.data.message) errorMessageText = error.data.message;
                    if (error.data.errors && typeof error.data.errors === 'object') {
                        for (const key in error.data.errors) {
                            if (Array.isArray(error.data.errors[key])) {
                                error.data.errors[key].forEach(msg => { detailedErrorsHtml += `<li class="text-red-500">${msg}</li>`; hasDetailedErrors = true;});
                            } else { detailedErrorsHtml += `<li class="text-red-500">${error.data.errors[key]}</li>`; hasDetailedErrors = true; }
                        }
                    }
                } else if (error && error.message) {
                    errorMessageText = error.message;
                }
                detailedErrorsHtml += '</ul>';

                voucherMessageElNew.innerHTML = hasDetailedErrors ? detailedErrorsHtml : `<p class="text-red-500">${errorMessageText}</p>`;
                if (typeof toastr !== 'undefined') toastr.error(errorMessageText);
            })
            .finally(() => {
                applyVoucherBtnNew.disabled = false;
                applyVoucherBtnNew.textContent = 'Áp dụng mã giảm giá';
                updateTotal();
            });
        });
    } else {
        console.warn('Một hoặc nhiều phần tử UI cho voucher mới không được tìm thấy. Các chức năng có thể không hoạt động.');
    }
    
    // Phương thức vận chuyển ẩn ban đầu, hiển thị khi chọn địa chỉ
    const shippingSection = document.querySelector('.shipping-section');
    if (shippingSection) {
        shippingSection.style.display = 'none';
    }
    
    // Hiển thị phương thức vận chuyển khi chọn địa chỉ
    function showShippingMethods() {
        if (shippingSection) {
            shippingSection.style.display = 'block';
            // Hiển thị fallback options
            const fallbackOptions = document.getElementById('shipping-services-fallback');
            if (fallbackOptions) {
                fallbackOptions.classList.remove('hidden');
            }
            // Ẩn loading
            const loadingElement = document.getElementById('shipping-services-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        }
    }
    
    // Gắn sự kiện cho việc chọn địa chỉ có sẵn
    document.querySelectorAll('input[name="address_id"]').forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked) {
                loadAddressForShipping(this.value);
            }
        });
    });

    // Hàm lấy thông tin địa chỉ và tính phí ship
    async function loadAddressForShipping(addressId) {
        try {
            const response = await fetch(`/account/addresses/${addressId}/shipping`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.data.district_id && data.data.ward_code) {
                // Cập nhật hidden fields với thông tin địa chỉ
                document.getElementById('form_hidden_district_id').value = data.data.district_id;
                document.getElementById('form_hidden_ward_code').value = data.data.ward_code;
                
                // Hiển thị phần phương thức vận chuyển
                showShippingMethods();
                
                // Load shipping services và tính phí
                await loadShippingServices(data.data.district_id);
                
                // Tính phí ship với service mặc định
                const defaultService = document.querySelector('input[name="shipping_method"]:checked');
                if (defaultService) {
                    await calculateShippingFeeWithService(data.data.district_id, data.data.ward_code);
                }
            } else {
                console.error('Địa chỉ không có thông tin district_id hoặc ward_code');
                resetShippingInfo();
            }
        } catch (error) {
            console.error('Error loading address for shipping:', error);
            resetShippingInfo();
        }
    }
    
    // Gắn sự kiện cho form địa chỉ mới
    function checkNewAddressComplete() {
        const city = document.getElementById('tinh')?.value;
        const district = document.getElementById('quan')?.value;
        const ward = document.getElementById('phuong')?.value;
        const detail = document.getElementById('new_address_detail')?.value?.trim();
        
        if (city && district && ward && detail) {
            showShippingMethods();
        }
    }
    
    // Gắn sự kiện cho các trường địa chỉ mới
    ['tinh', 'quan', 'phuong', 'new_address_detail'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', checkNewAddressComplete);
            element.addEventListener('input', checkNewAddressComplete);
        }
    });
    
    // Xử lý sự kiện chọn phương thức vận chuyển
    document.addEventListener('change', function(e) {
        if (e.target.name === 'delivery_method') {
            document.getElementById('form_hidden_delivery_method').value = e.target.value;
            if (e.target.value === 'pickup') {
                // Ẩn phí ship cho pickup
                document.getElementById('shipping-fee').textContent = '0đ';
                document.getElementById('form_hidden_shipping_fee').value = 0;
            }
            updateTotal();
        }
        
        if (e.target.name === 'shipping_method') {
            console.log('nhận tại của hàng');
            
            document.getElementById('form_hidden_delivery_method').value = e.target.value;
            // Cập nhật delivery_method dựa trên shipping_method được chọn
            if (e.target.value === 'pickup') {
                document.getElementById('form_hidden_delivery_method').value = 'pickup';
                // Ẩn phí ship cho pickup
                document.getElementById('shipping-fee').textContent = '0đ';
                document.getElementById('form_hidden_shipping_fee').value = 0;
                // Ẩn phần địa chỉ giao hàng khi chọn pickup
                // const addressSection = document.getElementById('address-section');
                // if (addressSection) {
                //     addressSection.style.display = 'none';
                // }
            }
            //  else {
            //     document.getElementById('form_hidden_delivery_method').value = 'delivery';
            //     // Hiện phần địa chỉ giao hàng khi chọn delivery
            //     const addressSection = document.getElementById('address-section');
            //     if (addressSection) {
            //         addressSection.style.display = 'block';
            //     }
            // }
            updateTotal();
        }
    });
    
    // ===== TAB NAVIGATION CHO ĐỊA CHỈ =====
    const existingAddressTab = document.getElementById('existing-address-tab');
    const newAddressTab = document.getElementById('new-address-tab');
    const existingAddressContent = document.getElementById('existing-address-content');
    const newAddressContent = document.getElementById('new-address-content');
    
    // Function để switch tab
    function switchToExistingAddressTab() {
        // Update tab styles
        existingAddressTab.classList.add('border-black', 'text-black', 'bg-gray-50');
        existingAddressTab.classList.remove('border-transparent', 'text-gray-500');
        newAddressTab.classList.remove('border-black', 'text-black', 'bg-gray-50');
        newAddressTab.classList.add('border-transparent', 'text-gray-500');
        
        // Show/hide content
        existingAddressContent.classList.remove('hidden');
        newAddressContent.classList.add('hidden');
        
        // Disable required validation cho các trường địa chỉ mới khi ẩn
        const newAddressFields = ['tinh', 'quan', 'phuong', 'new_address_detail'];
        newAddressFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.removeAttribute('required');
            }
        });
        
        // Clear new address form validation
        clearNewAddressValidation();
    }
    
    function switchToNewAddressTab() {
        // Update tab styles
        newAddressTab.classList.add('border-black', 'text-black', 'bg-gray-50');
        newAddressTab.classList.remove('border-transparent', 'text-gray-500');
        existingAddressTab.classList.remove('border-black', 'text-black', 'bg-gray-50');
        existingAddressTab.classList.add('border-transparent', 'text-gray-500');
        
        // Show/hide content
        newAddressContent.classList.remove('hidden');
        existingAddressContent.classList.add('hidden');
        
        // Enable lại required validation cho các trường địa chỉ mới khi hiển thị
        const newAddressFields = [
            { id: 'tinh', name: 'new_address_city_id' },
            { id: 'quan', name: 'new_address_district_id' },
            { id: 'phuong', name: 'new_address_ward_id' },
            { id: 'new_address_detail', name: 'new_address_detail' }
        ];
        newAddressFields.forEach(field => {
            const element = document.getElementById(field.id);
            if (element) {
                element.setAttribute('required', 'required');
            }
        });
        
        // Clear existing address selection
        const existingAddressInputs = document.querySelectorAll('input[name="address_id"]');
        existingAddressInputs.forEach(input => input.checked = false);
    }
    
    // Gắn sự kiện cho tab buttons
    if (existingAddressTab) {
        existingAddressTab.addEventListener('click', switchToExistingAddressTab);
    }
    if (newAddressTab) {
        newAddressTab.addEventListener('click', switchToNewAddressTab);
    }
    
    // Make switchToNewAddressTab available globally
    window.switchToNewAddressTab = switchToNewAddressTab;
    
    // ===== QUICK ACTIONS =====
    const clearFormBtn = document.getElementById('clear-form-btn');
    
    // Clear form functionality
    if (clearFormBtn) {
        clearFormBtn.addEventListener('click', function() {
            // Clear all form fields
            document.getElementById('tinh').selectedIndex = 0;
            document.getElementById('quan').selectedIndex = 0;
            document.getElementById('phuong').selectedIndex = 0;
            document.getElementById('new_address_detail').value = '';
            document.getElementById('save_address').checked = false;
            
            // Clear hidden fields
            document.getElementById('ten_tinh').value = '';
            document.getElementById('ten_quan').value = '';
            document.getElementById('ten_phuong').value = '';
            
            // Clear validation status
            clearNewAddressValidation();
            
            if (typeof toastr !== 'undefined') {
                toastr.info('Đã xóa form địa chỉ');
            }
        });
    }
    
    // ===== REAL-TIME VALIDATION =====
    function clearNewAddressValidation() {
        const validationStatus = document.getElementById('address-validation-status');
        if (validationStatus) {
            validationStatus.classList.add('hidden');
        }
    }
    
    function showAddressValidation(isValid, message) {
        const validationStatus = document.getElementById('address-validation-status');
        if (validationStatus) {
            const statusDiv = validationStatus.querySelector('div');
            if (isValid) {
                statusDiv.className = 'flex items-center gap-2 p-3 rounded-lg bg-green-50 border border-green-200';
                statusDiv.querySelector('svg').className = 'w-5 h-5 text-green-600';
                statusDiv.querySelector('span').className = 'text-sm font-medium text-green-800';
                statusDiv.querySelector('span').textContent = message || 'Địa chỉ hợp lệ';
            } else {
                statusDiv.className = 'flex items-center gap-2 p-3 rounded-lg bg-red-50 border border-red-200';
                statusDiv.querySelector('svg').className = 'w-5 h-5 text-red-600';
                statusDiv.querySelector('span').className = 'text-sm font-medium text-red-800';
                statusDiv.querySelector('span').textContent = message || 'Vui lòng điền đầy đủ thông tin';
            }
            validationStatus.classList.remove('hidden');
        }
    }
    
    // Validate address form on change
    function validateAddressForm() {
        // Bỏ qua validation nếu chọn pickup
        const deliveryMethod = document.getElementById('form_hidden_delivery_method').value;
        if (deliveryMethod === 'pickup') {
            return; // Không cần validate địa chỉ khi pickup
        }
        
        const city = document.getElementById('tinh').value;
        const district = document.getElementById('quan').value;
        const ward = document.getElementById('phuong').value;
        const detail = document.getElementById('new_address_detail').value.trim();
        
        if (city && district && ward && detail) {
            showAddressValidation(true, 'Địa chỉ đã đầy đủ và hợp lệ');
        } else {
            const missing = [];
            if (!city) missing.push('Tỉnh/Thành phố');
            if (!district) missing.push('Quận/Huyện');
            if (!ward) missing.push('Phường/Xã');
            if (!detail) missing.push('Địa chỉ cụ thể');
            
            if (missing.length > 0) {
                showAddressValidation(false, `Thiếu: ${missing.join(', ')}`);
            }
        }
    }
    
    // Gắn sự kiện validation
    ['tinh', 'quan', 'phuong', 'new_address_detail'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', validateAddressForm);
            element.addEventListener('input', validateAddressForm);
        }
    });
    
    // ===== TAB SWITCHING LOGIC =====
    // Auto switch to new address tab if no existing addresses
    const existingAddresses = document.querySelectorAll('input[name="address_id"]');
    if (existingAddresses.length === 0) {
        switchToNewAddressTab();
    }
    
    // Switch to existing address tab when an address is selected
    existingAddresses.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked) {
                switchToExistingAddressTab();
            }
        });
    });
    
    // ===== GHN API INTEGRATION =====
    // Load provinces on page load
    loadProvinces();
    
    // GHN API functions
    async function loadProvinces() {
        try {
            const response = await fetch('/api/ghn/provinces');
            const data = await response.json();
            
            const provinceSelect = document.getElementById('tinh');
            if (provinceSelect && data.success) {
                provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
                data.data.forEach(province => {
                    provinceSelect.innerHTML += `<option value="${province.ProvinceID}" data-name="${province.ProvinceName}">${province.ProvinceName}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }
    
    async function loadDistricts(provinceId) {
        try {
            const response = await fetch('/api/ghn/districts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    province_id: parseInt(provinceId)
                })
            });
            const data = await response.json();
            
            const districtSelect = document.getElementById('quan');
            const wardSelect = document.getElementById('phuong');
            
            if (districtSelect && data.success) {
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                
                data.data.forEach(district => {
                    districtSelect.innerHTML += `<option value="${district.DistrictID}" data-name="${district.DistrictName}">${district.DistrictName}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading districts:', error);
        }
    }
    
    async function loadWards(districtId) {
        try {
            const response = await fetch('/api/ghn/wards', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    district_id: parseInt(districtId)
                })
            });
            const data = await response.json();
            
            const wardSelect = document.getElementById('phuong');
            
            if (wardSelect && data.success) {
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                
                data.data.forEach(ward => {
                    wardSelect.innerHTML += `<option value="${ward.WardCode}" data-name="${ward.WardName}">${ward.WardName}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading wards:', error);
        }
    }
    
    async function calculateShippingFee(districtId, wardCode) {
        try {
            const response = await fetch('/api/ghn/shipping-fee', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    to_district_id: parseInt(districtId),
                    to_ward_code: wardCode,
                    weight: 500 // Default weight 500g
                })
            });
            
            const data = await response.json();
            
            if (data.success && data.data.total) {
                const shippingFee = data.data.total;
                updateShippingFee(shippingFee);
                return shippingFee;
            }
        } catch (error) {
            console.error('Error calculating shipping fee:', error);
            // Fallback to default shipping fee
            updateShippingFee(30000);
        }
    }
    
    function updateShippingFee(fee) {
        // Update hidden field
        document.getElementById('form_hidden_shipping_fee').value = fee;
        
        // Update display in order summary
        const shippingFeeDisplay = document.getElementById('shipping-fee-display');
        if (shippingFeeDisplay) {
            shippingFeeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(fee) + 'đ';
        }
        
        // Recalculate total
        updateTotal();
    }
    
    // Load shipping services from GHN API
    async function loadShippingServices(districtId) {
        const servicesContainer = document.getElementById('shipping-services-container');
        const servicesList = document.getElementById('shipping-services-list');
        const loadingElement = document.getElementById('shipping-services-loading');
        const fallbackElement = document.getElementById('shipping-services-fallback');
        
        if (!districtId) {
            servicesContainer.classList.add('hidden');
            loadingElement.classList.add('hidden');
            fallbackElement.classList.remove('hidden');
            return;
        }
        
        try {
            loadingElement.classList.remove('hidden');
            servicesContainer.classList.add('hidden');
            fallbackElement.classList.add('hidden');
            
            const response = await fetch('/api/ghn/services', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    to_district_id: parseInt(districtId)
                })
            });
            
            const data = await response.json();
            
            if (data.success && data.data && data.data.length > 0) {
                servicesList.innerHTML = '';
                
                // Lọc chỉ lấy 2 dịch vụ: giao hàng nhanh (1) và giao hàng tiết kiệm (2)
                const filteredServices = data.data.filter(service => 
                    service.service_type_id === 2
                );
                
                // Chỉ có giao hàng tiết kiệm
                filteredServices.sort((a, b) => b.service_type_id - a.service_type_id);
                
                if (filteredServices.length > 0) {
                    filteredServices.forEach((service, index) => {
                        const serviceElement = document.createElement('label');
                        serviceElement.className = 'group relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50';
                        
                        // Chỉ có giao hàng tiết kiệm
                        const serviceName = 'Giao hàng tiết kiệm';
                        const serviceDescription = '3-5 ngày làm việc';
                        const serviceIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>';
                        
                        serviceElement.innerHTML = `
                            <input type="radio" name="shipping_method" value="${service.service_type_id}" class="sr-only" ${index === 0 ? 'checked' : ''}>
                            <div class="flex items-center justify-center w-4 h-4 border-2 border-gray-300 rounded-full group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500 mr-3">
                                <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        ${serviceIcon}
                                    </svg>
                                    <span class="font-medium text-gray-900">${serviceName}</span>
                                </div>
                                <p class="text-xs text-gray-600">${serviceDescription}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-blue-600">Tính phí khi chọn địa chỉ</div>
                            </div>
                        `;
                        
                        servicesList.appendChild(serviceElement);
                    });
                } else {
                    throw new Error('No supported services available');
                }
                
                loadingElement.classList.add('hidden');
                servicesContainer.classList.remove('hidden');
            } else {
                throw new Error('No services available');
            }
        } catch (error) {
            console.error('Error loading shipping services:', error);
            loadingElement.classList.add('hidden');
            fallbackElement.classList.remove('hidden');
        }
    }
    
    // Enhanced shipping fee calculation with service info
    async function calculateShippingFeeWithService(districtId, wardCode) {
        try {
            const selectedService = document.querySelector('input[name="shipping_method"]:checked');
            const serviceTypeId = selectedService ? selectedService.value : 2;
            
            // Nếu chọn nhận hàng trực tiếp, phí vận chuyển = 0
            if (serviceTypeId === 'pickup') {
                const serviceName = selectedService?.closest('label').querySelector('.font-medium')?.textContent || 'Nhận hàng trực tiếp';
                updateShippingFeeDisplay(0, serviceName);
                return 0;
            }
            
            const response = await fetch('/api/ghn/shipping-fee', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    to_district_id: parseInt(districtId),
                    to_ward_code: wardCode,
                    weight: 500,
                    service_type_id: parseInt(serviceTypeId)
                })
            });
            
            const data = await response.json();
            
            if (data.success && data.data.total) {
                const shippingFee = data.data.total;
                
                // Lấy tên dịch vụ từ label được chọn
                const serviceName = selectedService?.closest('label').querySelector('.font-medium')?.textContent || 
                                  'Giao hàng tiết kiệm';
                
                updateShippingFeeDisplay(shippingFee, serviceName);
                
                // Get lead time
                getLeadTime(districtId, wardCode, serviceTypeId);
                
                return shippingFee;
            }
        } catch (error) {
            console.error('Error calculating shipping fee:', error);
            const serviceName = 'Giao hàng tiết kiệm';
            updateShippingFeeDisplay(30000, serviceName);
        }
    }
    
    // Get estimated delivery time
    async function getLeadTime(districtId, wardCode, serviceTypeId = 2) {
        try {
            const response = await fetch('/api/ghn/lead-time', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    to_district_id: parseInt(districtId),
                    to_ward_code: wardCode,
                    service_type_id: parseInt(serviceTypeId)
                })
            });
            
            const data = await response.json();
            
            if (data.success && data.data.expected_date) {
                document.getElementById('delivery-time').textContent = data.data.expected_date;
                document.getElementById('delivery-time-info').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error getting lead time:', error);
        }
    }
    
    // Update shipping fee display with service info
    function updateShippingFeeDisplay(fee, serviceName) {
        document.getElementById('shipping-fee').textContent = `${number_format(fee)}đ`;
        document.getElementById('shipping-service-name').textContent = serviceName;
        document.getElementById('shipping-service-name').classList.remove('hidden');
        document.getElementById('shipping-info-icon').classList.remove('hidden');
        document.getElementById('shipping-notice').classList.add('hidden');
        
        // Update hidden field
        document.getElementById('form_hidden_shipping_fee').value = fee;
        
        // Recalculate total
        updateTotal();
    }
    
    // Event listeners for address selects
    document.getElementById('tinh')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const provinceName = selectedOption.getAttribute('data-name') || '';
        const provinceId = this.value;
        
        // Update hidden fields
        document.getElementById('ten_tinh').value = provinceName;
        document.getElementById('form_hidden_province_id').value = provinceId;
        
        // Reset dependent selects
        document.getElementById('quan').innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        document.getElementById('phuong').innerHTML = '<option value="">Chọn Phường/Xã</option>';
        document.getElementById('ten_quan').value = '';
        document.getElementById('ten_phuong').value = '';
        document.getElementById('form_hidden_district_id').value = '';
        document.getElementById('form_hidden_ward_code').value = '';
        
        // Reset shipping info
        resetShippingInfo();
        
        if (provinceId) {
            loadDistricts(provinceId);
        }
        
        validateAddressForm();
    });
    
    document.getElementById('quan')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const districtName = selectedOption.getAttribute('data-name') || '';
        const districtId = this.value;
        
        // Update hidden fields
        document.getElementById('ten_quan').value = districtName;
        document.getElementById('form_hidden_district_id').value = districtId;
        
        // Reset dependent select
        document.getElementById('phuong').innerHTML = '<option value="">Chọn Phường/Xã</option>';
        document.getElementById('ten_phuong').value = '';
        document.getElementById('form_hidden_ward_code').value = '';
        
        // Reset shipping info
        resetShippingInfo();
        
        if (districtId) {
            loadWards(districtId);
            loadShippingServices(districtId);
        }
        
        validateAddressForm();
    });
    
    document.getElementById('phuong')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const wardName = selectedOption.getAttribute('data-name') || '';
        const wardCode = this.value;
        const districtId = document.getElementById('form_hidden_district_id').value;
        
        // Update hidden fields
        document.getElementById('ten_phuong').value = wardName;
        document.getElementById('form_hidden_ward_code').value = wardCode;
        
        if (districtId && wardCode) {
            calculateShippingFeeWithService(districtId, wardCode);
        }
        
        validateAddressForm();
    });
    
    // Reset shipping information
    function resetShippingInfo() {
        document.getElementById('shipping-fee').textContent = 'Chưa tính';
        document.getElementById('shipping-service-name').classList.add('hidden');
        document.getElementById('shipping-info-icon').classList.add('hidden');
        document.getElementById('delivery-time-info').classList.add('hidden');
        document.getElementById('shipping-notice').classList.remove('hidden');
        document.getElementById('form_hidden_shipping_fee').value = 0;
        updateTotal();
    }
    
    // Event listener for shipping method change
    document.addEventListener('change', function(e) {
        if (e.target.name === 'shipping_method') {
            const selectedValue = e.target.value;
            
            // Cập nhật hidden field
            document.getElementById('form_hidden_shipping_method').value = selectedValue;
            
            // Nếu chọn pickup, set phí = 0 ngay lập tức
            if (selectedValue === 'pickup') {
                const serviceName = e.target.closest('label').querySelector('.font-medium')?.textContent || 'Nhận hàng trực tiếp';
                updateShippingFeeDisplay(0, serviceName);
                return;
            }
            
            // Với các phương thức khác, cần có địa chỉ để tính phí
            const districtId = document.getElementById('form_hidden_district_id').value;
            const wardCode = document.getElementById('form_hidden_ward_code').value;
            
            if (districtId && wardCode) {
                calculateShippingFeeWithService(districtId, wardCode);
            } else {
                // Reset về trạng thái chưa tính phí
                resetShippingInfo();
            }
        }
    });
    
    // Address Modal Functions
    window.openAddressModal = function() {
        document.getElementById('address-modal').classList.remove('opacity-0', 'pointer-events-none');
        document.getElementById('address-modal').classList.add('opacity-100');
        document.body.style.overflow = 'hidden';
    }

    window.closeAddressModal = function() {
        document.getElementById('address-modal').classList.add('opacity-0', 'pointer-events-none');
        document.getElementById('address-modal').classList.remove('opacity-100');
        document.body.style.overflow = 'auto';
    }

    window.selectAddress = function(addressId, recipientName, phone, addressDetail, ward, district, city) {
        // Update hidden input
        document.getElementById('selected_address_id').value = addressId;
        
        // Update display
        const addressInfo = `<strong>${recipientName}</strong> - ${phone}<br>${addressDetail}, ${ward}, ${district}, ${city}`;
        document.getElementById('selected-address-info').innerHTML = addressInfo;
        
        // Show selected address display and hide choose button
        document.getElementById('selected-address-display').classList.remove('hidden');
        document.getElementById('choose-address-btn-container').classList.add('hidden');
        
        // Close modal
        closeAddressModal();
        
        // Calculate shipping fee for this address
        fetch(`/account/addresses/${addressId}/shipping`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data && data.data.district_id && data.data.ward_code) {
                    // Update hidden fields
                    document.getElementById('form_hidden_district_id').value = data.data.district_id;
                    document.getElementById('form_hidden_ward_code').value = data.data.ward_code;
                    
                    // Calculate shipping fee
                    calculateShippingFeeWithService(data.data.district_id, data.data.ward_code);
                } else {
                    console.error('Invalid response data:', data);
                    toastr.error('Không thể lấy thông tin địa chỉ để tính phí ship');
                }
            })
            .catch(error => {
                console.error('Error fetching address shipping info:', error);
                toastr.error('Lỗi khi lấy thông tin địa chỉ: ' + error.message);
            });
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.id === 'address-modal') {
            closeAddressModal();
        }
    });

    // ===== KHÔI PHỤC DỮ LIỆU ĐỊA CHỈ CŨ KHI CÓ LỖI VALIDATION =====
    function restoreOldAddressData() {
        // Khôi phục dữ liệu từ Laravel old() helper
        const oldCityName = '{{ old("new_address_city_name") }}';
        const oldDistrictName = '{{ old("new_address_district_name") }}';
        const oldWardName = '{{ old("new_address_ward_name") }}';
        const oldCityId = '{{ old("new_address_city_id") }}';
        const oldDistrictId = '{{ old("new_address_district_id") }}';
        const oldWardId = '{{ old("new_address_ward_id") }}';
        
        // Nếu có dữ liệu cũ, khôi phục chúng
        if (oldCityName || oldDistrictName || oldWardName) {
            console.log('Khôi phục dữ liệu địa chỉ cũ...', {
                city: oldCityName,
                district: oldDistrictName, 
                ward: oldWardName
            });
            
            // Chuyển sang tab địa chỉ mới nếu có dữ liệu validation lỗi
            const newAddressTab = document.getElementById('new-address-tab');
            const existingAddressTab = document.getElementById('existing-address-tab');
            
            if (newAddressTab && existingAddressTab) {
                // Kích hoạt tab địa chỉ mới
                newAddressTab.click();
            }
            
            // Khôi phục hidden fields
            if (oldCityName) document.getElementById('ten_tinh').value = oldCityName;
            if (oldDistrictName) document.getElementById('ten_quan').value = oldDistrictName;
            if (oldWardName) document.getElementById('ten_phuong').value = oldWardName;
            
            // Load lại dữ liệu select boxes nếu cần
            if (oldCityId && oldCityName) {
                setTimeout(() => {
                    loadProvinces().then(() => {
                        const citySelect = document.getElementById('tinh');
                        if (citySelect) {
                            citySelect.value = oldCityId;
                            // Trigger change event để load districts
                            citySelect.dispatchEvent(new Event('change'));
                            
                            setTimeout(() => {
                                if (oldDistrictId && oldDistrictName) {
                                    const districtSelect = document.getElementById('quan');
                                    if (districtSelect) {
                                        districtSelect.value = oldDistrictId;
                                        districtSelect.dispatchEvent(new Event('change'));
                                        
                                        setTimeout(() => {
                                            if (oldWardId && oldWardName) {
                                                const wardSelect = document.getElementById('phuong');
                                                if (wardSelect) {
                                                    wardSelect.value = oldWardId;
                                                    wardSelect.dispatchEvent(new Event('change'));
                                                }
                                            }
                                        }, 500);
                                    }
                                }
                            }, 500);
                        }
                    });
                }, 100);
            }
        }
    }
    
    // Khởi tạo trạng thái form khi trang load
     function initializeFormState() {
         console.log('Initializing form state...');
         
         // Kiểm tra tab nào đang active
         const existingAddressContent = document.getElementById('existing-address-content');
         const newAddressContent = document.getElementById('new-address-content');
         
         console.log('Existing address content hidden:', existingAddressContent?.classList.contains('hidden'));
         console.log('New address content hidden:', newAddressContent?.classList.contains('hidden'));
         
         // Mặc định disable required cho tất cả các trường địa chỉ mới
         // Vì tab "Địa chỉ có sẵn" thường là tab mặc định
         const newAddressFields = ['tinh', 'quan', 'phuong', 'new_address_detail'];
         newAddressFields.forEach(fieldId => {
             const field = document.getElementById(fieldId);
             if (field) {
                 field.removeAttribute('required');
                 console.log(`Removed required from ${fieldId}`);
             }
         });
         
         // Chỉ enable required nếu tab địa chỉ mới đang active
         if (newAddressContent && !newAddressContent.classList.contains('hidden')) {
             console.log('New address tab is active, enabling required validation');
             const newAddressFieldsWithNames = [
                 { id: 'tinh', name: 'new_address_city_id' },
                 { id: 'quan', name: 'new_address_district_id' },
                 { id: 'phuong', name: 'new_address_ward_id' },
                 { id: 'new_address_detail', name: 'new_address_detail' }
             ];
             newAddressFieldsWithNames.forEach(field => {
                 const element = document.getElementById(field.id);
                 if (element) {
                     element.setAttribute('required', 'required');
                     console.log(`Added required to ${field.id}`);
                 }
             });
         }
     }
    
    // Khôi phục dữ liệu khi trang load
     document.addEventListener('DOMContentLoaded', function() {
         initializeFormState();
         restoreOldAddressData();
     });
     
     // Force remove required attributes ngay lập tức để tránh lỗi validation
     (function() {
         const forceRemoveRequired = function() {
             const fields = ['tinh', 'quan', 'phuong'];
             fields.forEach(fieldId => {
                 const field = document.getElementById(fieldId);
                 if (field) {
                     field.removeAttribute('required');
                     console.log(`Force removed required from ${fieldId}`);
                 }
             });
         };
         
         // Chạy ngay lập tức
         forceRemoveRequired();
         
         // Chạy lại sau 100ms để đảm bảo
         setTimeout(forceRemoveRequired, 100);
     })();

    // ...existing code...
});

</script>

<style>
/* Custom styling for select fields to ensure text visibility */
select#tinh, select#quan, select#phuong {
    line-height: 1.75 !important;
    height: 3.5rem !important;
    min-height: 3.5rem !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    color: #374151 !important;
    vertical-align: middle !important;
    display: flex !important;
    align-items: center !important;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E") !important;
    background-position: right 0.75rem center !important;
    background-repeat: no-repeat !important;
    background-size: 1.25em 1.25em !important;
    padding: 0.875rem 2.5rem 0.875rem 1rem !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    box-sizing: border-box !important;
}

select#tinh option, select#quan option, select#phuong option {
    padding: 0.75rem 1rem !important;
    color: #374151 !important;
    background-color: white !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    min-height: 2.5rem !important;
}

/* Ensure proper display for placeholder text */
select#tinh:invalid, select#quan:invalid, select#phuong:invalid {
    color: #9CA3AF !important;
}

select#tinh:valid, select#quan:valid, select#phuong:valid {
    color: #374151 !important;
}

/* Fix for different browsers */
select#tinh, select#quan, select#phuong {
    -webkit-box-sizing: border-box !important;
    -moz-box-sizing: border-box !important;
    box-sizing: border-box !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    overflow: hidden !important;
}

/* Ensure text is visible on different devices */
@media (max-width: 768px) {
    select#tinh, select#quan, select#phuong {
        height: 3.75rem !important;
        min-height: 3.75rem !important;
        font-size: 16px !important;
        padding: 1rem 2.5rem 1rem 1rem !important;
    }
}

/* Additional fixes for text positioning */
select#tinh, select#quan, select#phuong {
    text-align: left !important;
    text-align-last: left !important;
    direction: ltr !important;
}
</style>

@endpush
@endsection
