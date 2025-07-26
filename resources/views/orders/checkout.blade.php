@extends('layouts.app')

@section('content')
@php
    // Kiểm tra xem giỏ hàng có chỉ ebook hay không
    $hasOnlyEbooks = true;
    $hasPhysicalBooks = false;
    
    foreach($cartItems as $item) {
        if ($item->bookFormat) {
            if (strtolower($item->bookFormat->format_name) !== 'ebook') {
                $hasPhysicalBooks = true;
                $hasOnlyEbooks = false;
                break;
            }
        }
    }
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

        @if(isset($mixedFormatCart) && $mixedFormatCart)
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
                        <p class="text-sm leading-relaxed">
                            Giỏ hàng của bạn có cả <span class="font-bold">sách vật lý</span> và <span class="font-bold">sách điện tử (ebook)</span>. 
                            Phương thức thanh toán khi nhận hàng không khả dụng cho đơn hàng này.
                        </p>
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
                        <p class="text-sm leading-relaxed">
                            Giỏ hàng của bạn chỉ có <span class="font-bold">sách điện tử (ebook)</span>. 
                            Bạn không cần nhập địa chỉ giao hàng và sẽ nhận link tải ebook qua email sau khi thanh toán thành công.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                            <div class="mb-8">
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
                                            <div class="space-y-3">
                                                @foreach($addresses as $address)
                                                <label class="group relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-black hover:bg-gray-50 transition-all duration-300 has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                                    <input type="radio" name="address_id" value="{{ $address->id }}" class="sr-only">
                                                    <div class="flex items-center justify-center w-4 h-4 border-2 border-gray-300 rounded-full group-has-[:checked]:border-black group-has-[:checked]:bg-black mr-4 mt-1 flex-shrink-0">
                                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <h4 class="font-bold text-gray-900">{{ $address->recipient_name }}</h4>
                                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ $address->phone }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-gray-600 leading-relaxed">
                                                            {{ $address->address_detail }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity ml-3">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                </label>
                                                @endforeach
                                            </div>
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
                                                <button type="button" id="detect-location-btn" 
                                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 transition-colors duration-300">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    Phát hiện vị trí
                                                </button>
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
                                                    <select id="tinh" name="new_address_city_id"
                                                            class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg">
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
                                                    <select id="quan" name="new_address_district_id"
                                                            class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg">
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
                                                    <select id="phuong" name="new_address_ward_id"
                                                            class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg">
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
                                                        class="w-full border-2 border-gray-300 px-4 py-4 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white group-hover:shadow-lg"
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
                        
                        <!-- Phương thức nhận hàng -->
                        @if(!$hasOnlyEbooks)
                        <div class="mt-8 mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Phương thức nhận hàng</h3>
                                    <p class="text-sm text-gray-600">Chọn cách thức nhận sách</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="group relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                    <input type="radio" name="delivery_method" value="delivery" class="sr-only" checked>
                                    <div class="flex items-center justify-center w-4 h-4 border-2 border-gray-300 rounded-full group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500 mr-3">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            {{-- <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg> --}}
                                            <span class="font-medium text-gray-900">Giao hàng tận nơi</span>
                                        </div>
                                        <p class="text-xs text-gray-600">3-5 ngày làm việc</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-blue-600">Có phí</div>
                                    </div>
                                </label>
                                <label class="group relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 hover:bg-green-50 transition-all duration-200 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                    <input type="radio" name="delivery_method" value="pickup" class="sr-only">
                                    <div class="flex items-center justify-center w-4 h-4 border-2 border-gray-300 rounded-full group-has-[:checked]:border-green-500 group-has-[:checked]:bg-green-500 mr-3">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            {{-- <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg> --}}
                                            <span class="font-medium text-gray-900">Nhận tại cửa hàng</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">FREE</span>
                                        </div>
                                        <p class="text-xs text-gray-600">Đến cửa hàng nhận</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-green-600">0đ</div>
                                    </div>
                                </label>
                            </div>
                            @error('delivery_method')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                            
                            <!-- Thông tin địa chỉ cửa hàng khi chọn nhận tại cửa hàng -->
                            <div id="store-address-info" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg" style="display: none;">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="font-medium text-green-800 mb-2">Địa chỉ cửa hàng:</h4>
                                        <p class="text-sm text-green-700 mb-1">
                                            @if($storeSettings && $storeSettings->address)
                                                {{ $storeSettings->address }}
                                            @else
                                                Số 1, Đường ABC, Quận 1, TP.HCM
                                            @endif
                                        </p>
                                        <p class="text-sm text-green-700 mb-1">
                                            <strong>Điện thoại:</strong> 
                                            @if($storeSettings && $storeSettings->phone)
                                                {{ $storeSettings->phone }}
                                            @else
                                                1900 1234
                                            @endif
                                        </p>
                                        <p class="text-sm text-green-700">
                                            <strong>Giờ mở cửa:</strong> 8:00 - 22:00 (Thứ 2 - Chủ nhật)
                                        </p>
                                        <p class="text-xs text-green-600 mt-2 font-medium">
                                            💡 Vui lòng mang theo mã đơn hàng khi đến nhận sách
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Phương thức vận chuyển -->
                        <div class="mt-6 mb-6 shipping-section">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Phương thức vận chuyển</h3>
                                    <p class="text-sm text-gray-600">Chọn tốc độ giao hàng</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="group relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-orange-300 hover:bg-orange-50 transition-all duration-200 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                    <input type="radio" name="shipping_method" value="standard" class="sr-only" checked>
                                    <div class="flex items-center justify-center w-4 h-4 border-2 border-gray-300 rounded-full group-has-[:checked]:border-orange-500 group-has-[:checked]:bg-orange-500 mr-3">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h1.586a1 1 0 01.707.293l1.414 1.414a1 1 0 00.707.293H15a2 2 0 012 2v2M5 8v8a2 2 0 002 2h8a2 2 0 002-2v-8m0 0V9a2 2 0 012-2h2a1 1 0 011 1v1a2 2 0 01-2 2h-2m-4 0h4"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Tiêu chuẩn</span>
                                        </div>
                                        <p class="text-xs text-gray-600">3-5 ngày làm việc</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-orange-600">20.000đ</div>
                                    </div>
                                </label>
                                <label class="group relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 hover:bg-red-50 transition-all duration-200 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                    <input type="radio" name="shipping_method" value="express" class="sr-only">
                                    <div class="flex items-center justify-center w-4 h-4 border-2 border-gray-300 rounded-full group-has-[:checked]:border-red-500 group-has-[:checked]:bg-red-500 mr-3">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Nhanh</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">HOT</span>
                                        </div>
                                        <p class="text-xs text-gray-600">1-2 ngày làm việc</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-red-600">40.000đ</div>
                                    </div>
                                </label>
                            </div>
                            @error('shipping_method')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                        
                        <!-- Phương thức thanh toán -->
                        <div class="mt-6 mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Phương thức thanh toán</h3>
                                    <p class="text-sm text-gray-600">Chọn cách thanh toán</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($paymentMethods as $method)
                                <label class="group cursor-pointer">
                                    <div class="relative border-2 border-gray-200 rounded-lg p-4 transition-all duration-300 group-hover:border-black group-hover:shadow-md">
                                        <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                               class="absolute right-3 top-3 h-4 w-4 accent-black" required>
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                    </svg>
                                                @elseif(str_contains(strtolower($method->name), 'momo'))
                                                    <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.477 2 2 6.477 2 12c0 5.524 4.477 10 10 10s10-4.476 10-10c0-5.523-4.477-10-10-10z"/>
                                                    </svg>
                                                @elseif(str_contains(strtolower($method->name), 'vnpay'))
                                                    <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                    </svg>
                                                @endif
                                                <span class="font-medium text-base">{{ $method->name }}</span>
                                            </div>
                                            @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-medium text-green-700">Số dư:</span>
                                                        <span class="text-xs font-bold text-green-800">
                                                            @if($wallet)
                                                                {{ number_format($wallet->balance) }}đ
                                                            @else
                                                                0đ
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('payment_method_id')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ghi chú -->
                        <div class="mt-12 mb-8">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-6 h-0.5 bg-black"></div>
                                <h6 class="text-xl font-bold uppercase tracking-wider">Ghi chú đơn hàng</h6>
                            </div>
                            <div class="relative">
                                <textarea name="note" rows="4"
                                    class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-black focus:ring-0 transition-colors"
                                    placeholder="Nhập ghi chú cho đơn hàng của bạn (nếu có)"></textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-black text-white py-4 px-8 text-lg font-bold uppercase tracking-wider
                                   hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 
                                   transform hover:scale-[1.02] transition-all duration-300">
                            ĐẶT HÀNG NGAY
                        </button>
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
                        <div class="group flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:border-black transition-all duration-300">
                            @if(isset($item->is_combo) && $item->is_combo)
                                <!-- Hiển thị combo -->
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $item->collection && $item->collection->cover_image ? asset('storage/' . $item->collection->cover_image) : asset('images/default-book.svg') }}"
                                         alt="{{ $item->collection ? $item->collection->name : 'Combo' }}" 
                                         class="w-16 h-20 object-cover rounded shadow-sm">
                                    <div class="absolute -top-1 -right-1">
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-black rounded-full">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-semibold text-gray-900 text-sm truncate">
                                        {{ $item->collection ? $item->collection->name : 'Combo không xác định' }}
                                    </h6>
                                    @if($item->collection && $item->collection->books && $item->collection->books->count() > 0)
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                                {{ $item->collection->books->count() }} cuốn
                                            </span>
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 text-sm">
                                        {{ number_format($item->price * $item->quantity) }}đ
                                    </p>
                                </div>
                            @else
                                <!-- Hiển thị sách đơn lẻ -->
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $item->book && $item->book->cover_image ? asset('storage/' . $item->book->cover_image) : asset('images/default-book.svg') }}"
                                         alt="{{ $item->book ? $item->book->title : 'Sách' }}" 
                                         class="w-16 h-20 object-cover rounded shadow-sm">
                                    <div class="absolute -top-1 -right-1">
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-black rounded-full">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-semibold text-gray-900 text-sm truncate">
                                        {{ $item->book ? $item->book->title : 'Sách không xác định' }}
                                    </h6>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->bookFormat && $item->bookFormat->format_name == 'Ebook' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $item->bookFormat ? $item->bookFormat->format_name : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 text-sm">
                                        {{ number_format($item->price * $item->quantity) }}đ
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
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-600">Phí vận chuyển</span>
                                <span id="shipping-fee" class="font-medium text-gray-900">{{ $hasOnlyEbooks ? '0đ' : '20.000đ' }}</span>
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
                                    {{ $hasOnlyEbooks ? number_format($subtotal) : number_format($subtotal + 20000) }}đ
                                </span>
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

// Xử lý delivery method
document.querySelectorAll('input[name="delivery_method"]').forEach(input => {
    input.addEventListener('change', function() {
        const shippingSection = document.querySelector('.shipping-section');
        // Cập nhật hidden field
        document.getElementById('form_hidden_delivery_method').value = this.value;
        
        if (this.value === 'pickup') {
            // Ẩn section vận chuyển và set phí ship = 0
            shippingSection.style.display = 'none';
            document.getElementById('shipping-fee').textContent = '0đ';
            document.getElementById('form_hidden_shipping_fee').value = 0;
        } else {
            // Hiện section vận chuyển và set phí ship mặc định
            shippingSection.style.display = 'block';
            const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
            const shippingFee = selectedShipping ? (selectedShipping.value === 'standard' ? 20000 : 40000) : 20000;
            document.getElementById('shipping-fee').textContent = `${number_format(shippingFee)}đ`;
            document.getElementById('form_hidden_shipping_fee').value = shippingFee;
        }
        updateTotal();
    });
});

// Cập nhật phí vận chuyển khi thay đổi phương thức
document.querySelectorAll('input[name="shipping_method"]').forEach(input => {
    input.addEventListener('change', function() {
        const shippingFee = this.value === 'standard' ? 20000 : 40000;
        document.getElementById('shipping-fee').textContent = `${number_format(shippingFee)}đ`;
        updateTotal();
    });
});

// Khởi tạo trạng thái ban đầu
document.addEventListener('DOMContentLoaded', function() {
    const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked');
    if (deliveryMethod && deliveryMethod.value === 'pickup') {
        document.querySelector('.shipping-section').style.display = 'none';
        document.getElementById('shipping-fee').textContent = '0đ';
        updateTotal();
    }
});

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
    
    // Xử lý hiển thị địa chỉ cửa hàng khi chọn nhận tại cửa hàng
    const deliveryMethodInputs = document.querySelectorAll('input[name="delivery_method"]');
    const storeAddressInfo = document.getElementById('store-address-info');
    const shippingSection = document.querySelector('.shipping-section');
    
    function toggleStoreAddress() {
        const selectedDeliveryMethod = document.querySelector('input[name="delivery_method"]:checked');
        if (selectedDeliveryMethod && selectedDeliveryMethod.value === 'pickup') {
            // Hiển thị thông tin địa chỉ cửa hàng
            if (storeAddressInfo) {
                storeAddressInfo.style.display = 'block';
            }
            // Ẩn phần phương thức vận chuyển
            if (shippingSection) {
                shippingSection.style.display = 'none';
            }
        } else {
            // Ẩn thông tin địa chỉ cửa hàng
            if (storeAddressInfo) {
                storeAddressInfo.style.display = 'none';
            }
            // Hiển thị phần phương thức vận chuyển
            if (shippingSection) {
                shippingSection.style.display = 'block';
            }
        }
    }
    
    // Gắn sự kiện cho các radio button phương thức nhận hàng
    deliveryMethodInputs.forEach(input => {
        input.addEventListener('change', toggleStoreAddress);
    });
    
    // Kiểm tra trạng thái ban đầu
    toggleStoreAddress();
    
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
    const detectLocationBtn = document.getElementById('detect-location-btn');
    const clearFormBtn = document.getElementById('clear-form-btn');
    
    // Detect location functionality
    if (detectLocationBtn) {
        detectLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                this.disabled = true;
                this.innerHTML = `
                    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Đang phát hiện...
                `;
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        // Simulate address detection (in real app, use reverse geocoding API)
                        setTimeout(() => {
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Đã phát hiện vị trí! Vui lòng chọn tỉnh/thành phố từ danh sách.');
                            }
                            // Focus on city select
                            document.getElementById('tinh')?.focus();
                            
                            this.disabled = false;
                            this.innerHTML = `
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Phát hiện vị trí
                            `;
                        }, 1500);
                    },
                    (error) => {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Không thể phát hiện vị trí. Vui lòng nhập thủ công.');
                        }
                        this.disabled = false;
                        this.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Phát hiện vị trí
                        `;
                    }
                );
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Trình duyệt không hỗ trợ định vị.');
                }
            }
        });
    }
    
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
});
</script>
@endpush
@endsection
