@extends('layouts.app')

@section('content')
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
        @endif

        <div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Form thanh toán bên trái -->
        <div class="w-full">
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
                        <input type="hidden" name="final_total_amount" id="form_hidden_total_amount" value="{{ $subtotal + 20000 }}">
                        <input type="hidden" name="discount_amount_applied" id="form_hidden_discount_amount" value="0">
                        <input type="hidden" name="applied_voucher_code" id="form_hidden_applied_voucher_code" value="">
                        <input type="hidden" name="shipping_fee_applied" id="form_hidden_shipping_fee" value="20000">
                        <input type="hidden" name="delivery_method" id="form_hidden_delivery_method" value="delivery">
                        
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
                            
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Địa chỉ giao hàng</label>
                                <select name="address_id" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:border-black focus:ring-0">
                                    <option value="">Chọn địa chỉ</option>
                                    @foreach($addresses as $address)
                                    <option value="{{ $address->id }}">
                                        {{ $address->recipient_name }} - {{ $address->phone }} - {{ $address->address_detail }},
                                        {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('address_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <label class="text-md font-semibold text-gray-800 mb-4 block">Hoặc nhập địa chỉ giao hàng mới:</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="mb-4">
                                    <label for="tinh" class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành phố:</label>
                                    <select id="tinh" name="new_address_city_id"
                                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 bg-white focus:border-black focus:ring-0">
                                        <option value="">Chọn Tỉnh/Thành phố</option>
                                    </select>
                                    <input type="hidden" name="new_address_city_name" id="ten_tinh">
                                    @error('new_address_city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    @error('new_address_city_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="quan" class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện:</label>
                                    <select id="quan" name="new_address_district_id"
                                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 bg-white focus:border-black focus:ring-0">
                                        <option value="">Chọn Quận/Huyện</option>
                                    </select>
                                    <input type="hidden" name="new_address_district_name" id="ten_quan">
                                    @error('new_address_district_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    @error('new_address_district_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="phuong" class="block text-sm font-medium text-gray-700 mb-1">Phường/Xã:</label>
                                    <select id="phuong" name="new_address_ward_id"
                                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 bg-white focus:border-black focus:ring-0">
                                        <option value="">Chọn Phường/Xã</option>
                                    </select>
                                    <input type="hidden" name="new_address_ward_name" id="ten_phuong">
                                    @error('new_address_ward_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    @error('new_address_ward_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="new_address_detail" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ cụ thể (Số nhà, tên đường):</label>
                                <input type="text" name="new_address_detail" id="new_address_detail"
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 bg-white focus:border-black focus:ring-0"
                                    placeholder="Ví dụ: Số 123, Đường ABC" value="{{ old('new_address_detail') }}">
                                @error('new_address_detail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        <!-- Phương thức nhận hàng -->
                        <div class="mt-12 mb-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Phương thức nhận hàng</h3>
                                    <p class="text-gray-600 mt-1">Chọn cách thức nhận sách phù hợp với bạn</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                    <input type="radio" name="delivery_method" value="delivery" class="sr-only" checked>
                                    <div class="flex items-center justify-center w-5 h-5 border-2 border-gray-300 rounded-full group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500 mr-4">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            <span class="font-semibold text-gray-900">Giao hàng tận nơi</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Nhận sách tại địa chỉ của bạn</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-blue-600">Có phí ship</div>
                                        <div class="text-xs text-gray-500">Tùy khu vực</div>
                                    </div>
                                </label>
                                <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-300 hover:bg-green-50 transition-all duration-200 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                    <input type="radio" name="delivery_method" value="pickup" class="sr-only">
                                    <div class="flex items-center justify-center w-5 h-5 border-2 border-gray-300 rounded-full group-has-[:checked]:border-green-500 group-has-[:checked]:bg-green-500 mr-4">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span class="font-semibold text-gray-900">Nhận tại cửa hàng</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">MIỄN PHÍ</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Đến cửa hàng để nhận sách</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-green-600">0đ</div>
                                        <div class="text-xs text-gray-500">Không phí ship</div>
                                    </div>
                                </label>
                            </div>
                            @error('delivery_method')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Phương thức vận chuyển -->
                        <div class="mt-12 mb-8 shipping-section">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Phương thức vận chuyển</h3>
                                    <p class="text-gray-600 mt-1">Chọn cách thức giao hàng phù hợp với bạn</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 hover:bg-orange-50 transition-all duration-200 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                    <input type="radio" name="shipping_method" value="standard" class="sr-only" checked>
                                    <div class="flex items-center justify-center w-5 h-5 border-2 border-gray-300 rounded-full group-has-[:checked]:border-orange-500 group-has-[:checked]:bg-orange-500 mr-4">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h1.586a1 1 0 01.707.293l1.414 1.414a1 1 0 00.707.293H15a2 2 0 012 2v2M5 8v8a2 2 0 002 2h8a2 2 0 002-2v-8m0 0V9a2 2 0 012-2h2a1 1 0 011 1v1a2 2 0 01-2 2h-2m-4 0h4"></path>
                                            </svg>
                                            <span class="font-semibold text-gray-900">Giao hàng tiêu chuẩn</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Giao hàng trong 3-5 ngày làm việc</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-orange-600">20.000đ</div>
                                        <div class="text-xs text-gray-500">Phí vận chuyển</div>
                                    </div>
                                </label>
                                <label class="group relative flex items-center p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-300 hover:bg-red-50 transition-all duration-200 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                    <input type="radio" name="shipping_method" value="express" class="sr-only">
                                    <div class="flex items-center justify-center w-5 h-5 border-2 border-gray-300 rounded-full group-has-[:checked]:border-red-500 group-has-[:checked]:bg-red-500 mr-4">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            <span class="font-semibold text-gray-900">Giao hàng nhanh</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">HOT</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Giao hàng trong 1-2 ngày làm việc</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-red-600">40.000đ</div>
                                        <div class="text-xs text-gray-500">Phí vận chuyển</div>
                                    </div>
                                </label>
                            </div>
                            @error('shipping_method')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Phương thức thanh toán -->
                        <div class="mt-12 mb-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Phương thức thanh toán</h3>
                                    <p class="text-gray-600 mt-1">Chọn cách thức thanh toán an toàn và tiện lợi</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($paymentMethods as $method)
                                <label class="group cursor-pointer">
                                    <div class="relative border-2 border-gray-200 rounded-lg p-6 transition-all duration-300 group-hover:border-black group-hover:shadow-lg">
                                        <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                               class="absolute right-4 top-4 h-5 w-5 accent-black" required>
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-3">
                                                @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                    </svg>
                                                @elseif(str_contains(strtolower($method->name), 'momo'))
                                                    <svg class="w-8 h-8 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.477 2 2 6.477 2 12c0 5.524 4.477 10 10 10s10-4.476 10-10c0-5.523-4.477-10-10-10z"/>
                                                    </svg>
                                                @elseif(str_contains(strtolower($method->name), 'vnpay'))
                                                    <svg class="w-8 h-8 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                    </svg>
                                                @endif
                                                <span class="font-bold text-lg">{{ $method->name }}</span>
                                            </div>
                                            @if(str_contains(strtolower($method->name), 'ví điện tử'))
                                                <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm font-medium text-green-700">Số dư ví:</span>
                                                        <span class="text-sm font-bold text-green-800">
                                                            @if($wallet)
                                                                {{ number_format($wallet->balance) }}đ
                                                            @else
                                                                0đ
                                                            @endif
                                                        </span>
                                                    </div>
                                                    @php
                                                        $walletBalance = Auth::user()->wallet ? Auth::user()->wallet->balance : 0;
                                                        $totalAmount = $subtotal + 20000;
                                                    @endphp
                                                    {{-- @if($walletBalance < $totalAmount)
                                                        <div class="mt-2 text-xs text-red-600 font-medium">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            Số dư không đủ để thanh toán
                                                        </div>
                                                    @endif --}}
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
        <div class="w-full">
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
                    <div class="space-y-4 mb-8">
                        @foreach($cartItems as $item)
                        <div class="group flex items-start gap-4 p-4 border-2 border-gray-100 hover:border-black transition-all duration-300">
                            @if(isset($item->is_combo) && $item->is_combo)
                                <!-- Hiển thị combo -->
                                <div class="relative">
                                    <img src="{{ $item->cover_image ? asset('storage/' . $item->cover_image) : asset('images/no-image.png') }}"
                                         alt="{{ $item->name ?? 'Combo' }}" 
                                         class="w-24 h-32 object-cover rounded-lg shadow-md group-hover:shadow-xl transition-all duration-300">
                                    <div class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-black rounded-full">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-bold text-gray-900 text-lg tracking-tight group-hover:text-black transition-colors">
                                        {{ $item->name }}
                                    </h6>
                                    @if(isset($item->books) && $item->books->count() > 0)
                                        <div class="mt-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-600">{{ $item->books->count() }} cuốn sách</span>
                                        </div>
                                    @endif
                                    <div class="mt-4 text-lg font-bold text-green-600">
                                        {{ number_format($item->price * $item->quantity) }}đ
                                    </div>
                                </div>
                            @else
                                <!-- Hiển thị sách đơn lẻ -->
                                <div class="relative">
                                    <img src="{{ $item->book && $item->book->cover_image ? asset('storage/' . $item->book->cover_image) : asset('images/no-image.png') }}"
                                         alt="{{ $item->book ? $item->book->title : 'Sách' }}" 
                                         class="w-24 h-32 object-cover rounded-lg shadow-md group-hover:shadow-xl transition-all duration-300">
                                    <div class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-black rounded-full">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-bold text-gray-900 text-lg tracking-tight group-hover:text-black transition-colors">
                                        {{ $item->book ? $item->book->title : 'Sách không xác định' }}
                                    </h6>
                                    <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item->bookFormat && $item->bookFormat->format_name == 'Ebook' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $item->bookFormat ? $item->bookFormat->format_name : 'N/A' }}
                                    </div>
                                    <div class="mt-4 text-lg font-bold text-green-600">
                                        {{ number_format($item->price * $item->quantity) }}đ
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Mã giảm giá - Adidas style -->
                    <div class="mb-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-1 h-6 bg-black"></div>
                            <h3 class="text-lg font-black uppercase tracking-wide text-black">
                                MÃ GIẢM GIÁ
                            </h3>
                        </div>
                        
                        <div class="space-y-3">
                            <input type="text" name="voucher_code_input" id="voucher_code_input"
                                class="w-full border-2 border-gray-300 px-4 py-3 focus:border-black focus:ring-0 transition-all duration-300 hover:border-gray-400 bg-white"
                                placeholder="NHẬP MÃ GIẢM GIÁ">
                            
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" id="open-voucher-modal-btn"
                                    class="bg-white border-2 border-black text-black px-4 py-3 font-bold uppercase tracking-wide hover:bg-black hover:text-white transition-all duration-300">
                                    CHỌN MÃ
                                </button>
                                <button type="button" id="apply-voucher-btn-new"
                                    class="bg-black text-white px-4 py-3 font-bold uppercase tracking-wide hover:bg-gray-800 transition-all duration-300">
                                    ÁP DỤNG
                                </button>
                            </div>
                            
                            <div id="voucher-message-new" class="text-sm font-medium"></div>
                        </div>
                    </div>

                    <!-- Tổng tiền - Adidas style -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-1 h-6 bg-black"></div>
                            <h3 class="text-lg font-black uppercase tracking-wide text-black">
                                TỔNG KẾT
                            </h3>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-3 px-4 border-2 border-gray-200">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-700">TẠM TÍNH</span>
                                <span class="font-bold text-black">{{ number_format($subtotal) }}đ</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-4 border-2 border-gray-200">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-700">PHÍ VẬN CHUYỂN</span>
                                <span id="shipping-fee" class="font-bold text-black">20.000đ</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-4 border-2 border-gray-200">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-700">GIẢM GIÁ</span>
                                <span id="discount-amount" class="font-bold text-red-600">0đ</span>
                            </div>
                            
                            <!-- Tổng cộng -->
                            <div class="mt-6 flex justify-between items-center p-6 bg-black relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                                <div class="relative z-10 flex justify-between items-center w-full">
                                    <span class="text-white font-black text-lg uppercase tracking-wide">TỔNG CỘNG</span>
                                    <span id="total-amount" class="text-white font-black text-2xl">
                                        {{ number_format($subtotal + 20000) }}đ
                                    </span>
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
</div>

<!-- Voucher Modal -->
<div id="voucher-modal" class="fixed inset-0 backdrop-blur bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none">
    <div class="relative mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <p class="text-2xl font-bold text-gray-700">Chọn mã giảm giá</p>
            <button id="close-voucher-modal-btn" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="voucher-list-modal" class="mt-3 space-y-3 max-h-96 overflow-y-auto">
            <!-- Voucher items will be populated here by JavaScript -->
            @if(isset($vouchers) && count($vouchers))
                @foreach($vouchers as $voucher)
                <div class="voucher-item-modal p-4 border rounded-lg hover:bg-gray-50 cursor-pointer" data-code="{{ $voucher->code }}">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-indigo-600 voucher-code-modal">{{ $voucher->code }}</p>
                            <p class="text-sm text-gray-600">{{ $voucher->name }}</p>
                            <p class="text-xs text-gray-500">
                                @if($voucher->discount_type === 'percentage')
                                    Giảm {{ $voucher->discount_value }}%
                                    @if($voucher->max_discount_amount)
                                        (tối đa {{ number_format($voucher->max_discount_amount) }}đ)
                                    @endif
                                @elseif($voucher->discount_type === 'fixed')
                                    Giảm {{ number_format($voucher->discount_value) }}đ
                                @endif
                            </p>
                            @if($voucher->min_purchase_amount)
                                <p class="text-xs text-gray-500">Đơn tối thiểu: {{ number_format($voucher->min_purchase_amount) }}đ</p>
                            @endif
                        </div>
                        <button type="button" class="select-voucher-from-modal-btn bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600" data-code="{{ $voucher->code }}">Chọn</button>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-gray-500">Không có mã giảm giá nào.</p>
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
    const voucherCode = document.querySelector('input[name="voucher_code"]').value;
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
            if (voucherModal) voucherModal.style.display = 'none';
        });
    });

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
                    discountAmountEl.textContent = `${number_format(discountValue)}đ`;

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
});
</script>
@endpush
@endsection
