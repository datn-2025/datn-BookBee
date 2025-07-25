@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white py-16 relative overflow-hidden">
    <!-- Background Elements - Adidas Style -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 rounded-none transform rotate-45 translate-x-36 -translate-y-36"></div>
        <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
        <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-6">
        <!-- Header - Adidas Style -->
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-8 h-0.5 bg-black"></div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">
                    REFUND REQUEST
                </span>
            </div>
            <div class="flex items-center justify-between">
                <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tight text-black">
                    YÊU CẦU<br>
                    <span class="text-gray-400">HOÀN TIỀN</span>
                </h1>
                <a href="{{ route('account.orders.show', $order->id) }}" 
                   class="group bg-black text-white px-6 py-3 font-bold text-xs uppercase tracking-[0.1em] hover:bg-gray-800 transition-all duration-300 flex items-center gap-3">
                    <i class="fas fa-arrow-left"></i>
                    <span>QUAY LẠI</span>
                    <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Order Summary Card - Adidas Style -->
        <div class="bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden mb-8 group">
            <!-- Geometric background -->
            <div class="absolute top-0 right-0 w-16 h-16 bg-gray-50 transform rotate-45 translate-x-8 -translate-y-8 group-hover:bg-gray-100 group-hover:scale-110 transition-all duration-500"></div>
            
            <div class="p-8 relative z-10">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-6 h-0.5 bg-black"></div>
                    <h2 class="text-lg font-bold uppercase tracking-wide text-black">
                        ĐƠN HÀNG #{{ $order->code }}
                    </h2>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">NGÀY ĐẶT HÀNG</p>
                        <p class="text-sm font-medium text-black">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">TỔNG TIỀN</p>
                        <div class="flex items-center gap-2">
                            <span class="bg-red-600 text-white px-2 py-1 text-xs font-bold uppercase tracking-wide">
                                {{ number_format($order->total_amount, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">THANH TOÁN</p>
                        <p class="text-sm font-medium text-black">{{ $order->paymentMethod->name ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">TRẠNG THÁI</p>
                        <p class="text-sm font-medium text-black">{{ $order->paymentStatus->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <!-- Progress indicator -->
                <div class="absolute bottom-0 left-0 h-1 bg-black w-0 group-hover:w-full transition-all duration-700"></div>
            </div>
        </div>

        <!-- Refund Request Form -->
        <div class="relative bg-black border border-gray-800 overflow-hidden group hover:border-white transition-all duration-300">
            <!-- Geometric Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-32 h-32 border-r-2 border-t-2 border-white transform rotate-45 translate-x-16 -translate-y-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 border-l-2 border-b-2 border-white transform -rotate-45 -translate-x-12 translate-y-12"></div>
            </div>
            
            <div class="relative p-8">
                <div class="flex items-center mb-8">
                    <div class="w-1 h-8 bg-white mr-4"></div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wider">THÔNG TIN YÊU CẦU HOÀN TIỀN</h2>
                </div>
                
                <form method="POST" action="{{ route('account.orders.refund.request', $order->id) }}" class="space-y-8">
                    @csrf
                    
                    <!-- Reason Selection -->
                    <div class="group">
                        <label for="reason" class="block text-sm font-bold text-white uppercase tracking-wide mb-3">
                            LÝ DO YÊU CẦU HOÀN TIỀN <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="reason" name="reason" required
                                class="w-full bg-gray-900 border-2 border-gray-700 text-white px-4 py-3 focus:border-white focus:outline-none transition-all duration-300 uppercase font-medium">
                                <option value="" class="bg-gray-900">CHỌN LÝ DO</option>
                                <option value="wrong_item" class="bg-gray-900" {{ old('reason') == 'wrong_item' ? 'selected' : '' }}>SẢN PHẨM KHÔNG ĐÚNG MÔ TẢ</option>
                                <option value="quality_issue" class="bg-gray-900" {{ old('reason') == 'quality_issue' ? 'selected' : '' }}>VẤN ĐỀ VỀ CHẤT LƯỢNG</option>
                                <option value="shipping_delay" class="bg-gray-900" {{ old('reason') == 'shipping_delay' ? 'selected' : '' }}>GIAO HÀNG QUÁ CHẬM</option>
                                <option value="wrong_qty" class="bg-gray-900" {{ old('reason') == 'wrong_qty' ? 'selected' : '' }}>SỐ LƯỢNG KHÔNG ĐÚNG</option>
                                <option value="other" class="bg-gray-900" {{ old('reason') == 'other' ? 'selected' : '' }}>LÝ DO KHÁC</option>
                            </select>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <div class="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-white"></div>
                            </div>
                        </div>
                        @error('reason')
                            <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Details -->
                    <div class="group">
                        <label for="details" class="block text-sm font-bold text-white uppercase tracking-wide mb-3">
                            CHI TIẾT LÝ DO <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <textarea id="details" name="details" rows="4" required
                                class="w-full bg-gray-900 border-2 border-gray-700 text-white px-4 py-3 focus:border-white focus:outline-none transition-all duration-300 resize-none"
                                placeholder="VUI LÒNG MÔ TẢ CHI TIẾT LÝ DO YÊU CẦU HOÀN TIỀN (ÍT NHẤT 20 KÝ TỰ)...">{{ old('details') }}</textarea>
                            <div class="absolute bottom-2 right-2 w-3 h-3 border-r-2 border-b-2 border-gray-600"></div>
                        </div>
                        @error('details')
                            <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Refund Method -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white uppercase tracking-wide mb-4">
                            PHƯƠNG THỨC HOÀN TIỀN <span class="text-red-500">*</span>
                        </label>
                        
                        @php
                            $paymentMethod = strtolower($order->paymentMethod->name ?? '');
                            $isVnpayOrder = str_contains($paymentMethod, 'vnpay');
                            $defaultMethod = $isVnpayOrder ? 'vnpay' : 'wallet';
                        @endphp
                        
                        <div class="space-y-4">
                            <!-- Wallet Option -->
                            <div class="relative bg-gray-900 border-2 border-gray-700 p-4 hover:border-white transition-all duration-300 group/option">
                                <div class="flex items-start">
                                    <input type="radio" id="refund_wallet" name="refund_method" value="wallet" 
                                           class="mt-1 h-5 w-5 text-white bg-gray-900 border-2 border-gray-600 focus:ring-white focus:ring-2" 
                                           {{ old('refund_method', $defaultMethod) == 'wallet' ? 'checked' : '' }}>
                                    <label for="refund_wallet" class="ml-4 block cursor-pointer">
                                        <span class="text-white font-bold uppercase tracking-wide">HOÀN TIỀN VÀO VÍ</span>
                                        <p class="text-gray-400 text-sm mt-1 uppercase">TIỀN SẼ ĐƯỢC CỘNG VÀO VÍ CỦA BẠN NGAY LẬP TỨC</p>
                                    </label>
                                </div>
                                <div class="absolute top-2 right-2 w-2 h-2 bg-white opacity-0 group-hover/option:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            
                            <!-- VNPay Option -->
                            @if($isVnpayOrder)
                            <div class="relative bg-gray-900 border-2 border-gray-700 p-4 hover:border-white transition-all duration-300 group/option">
                                <div class="flex items-start">
                                    <input type="radio" id="refund_vnpay" name="refund_method" value="vnpay" 
                                           class="mt-1 h-5 w-5 text-white bg-gray-900 border-2 border-gray-600 focus:ring-white focus:ring-2"
                                           {{ old('refund_method', $defaultMethod) == 'vnpay' ? 'checked' : '' }}>
                                    <label for="refund_vnpay" class="ml-4 block cursor-pointer">
                                        <div class="flex items-center">
                                            <span class="text-white font-bold uppercase tracking-wide">HOÀN TIỀN QUA VNPAY</span>
                                            <span class="ml-2 bg-white text-black px-2 py-1 text-xs font-black uppercase">(KHUYẾN NGHỊ)</span>
                                        </div>
                                        <p class="text-gray-400 text-sm mt-1 uppercase">TIỀN SẼ ĐƯỢC HOÀN VỀ PHƯƠNG THỨC THANH TOÁN BAN ĐẦU TRONG 3-5 NGÀY LÀM VIỆC</p>
                                    </label>
                                </div>
                                <div class="absolute top-2 right-2 w-2 h-2 bg-white opacity-0 group-hover/option:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            @endif
                        </div>
                        
                        @if($isVnpayOrder)
                        <div class="mt-6 bg-gray-900 border-l-4 border-white p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-6 h-6 border-2 border-white flex items-center justify-center">
                                        <div class="w-2 h-2 bg-white"></div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-white font-bold uppercase tracking-wide">THÔNG TIN</h4>
                                    <p class="text-gray-300 text-sm mt-1 uppercase">VÌ ĐƠN HÀNG NÀY ĐƯỢC THANH TOÁN QUA VNPAY, CHÚNG TÔI KHUYẾN NGHỊ HOÀN TIỀN QUA VNPAY ĐỂ ĐẢM BẢO NHANH CHÓNG VÀ AN TOÀN.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    
                        @error('refund_method')
                            <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 pt-8 border-t-2 border-gray-800">
                        <a href="{{ route('account.orders.show', $order->id) }}"
                           class="relative px-8 py-3 bg-gray-800 border-2 border-gray-600 text-white font-bold uppercase tracking-wide hover:bg-gray-700 hover:border-gray-500 transition-all duration-300 group overflow-hidden">
                            <span class="relative z-10">HỦY</span>
                            <div class="absolute inset-0 bg-white transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                            <span class="absolute inset-0 flex items-center justify-center text-black font-bold uppercase tracking-wide opacity-0 group-hover:opacity-100 transition-opacity duration-300">HỦY</span>
                        </a>
                        <button type="submit"
                            class="relative px-8 py-3 bg-white border-2 border-white text-black font-black uppercase tracking-wide hover:bg-black hover:text-white transition-all duration-300 group overflow-hidden">
                            <span class="relative z-10">GỬI YÊU CẦU HOÀN TIỀN</span>
                            <div class="absolute top-0 right-0 w-2 h-2 bg-black group-hover:bg-white transition-colors duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="relative mt-8 bg-black border-2 border-gray-800 overflow-hidden group hover:border-white transition-all duration-300">
            <!-- Geometric Background -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="absolute top-4 right-4 w-16 h-16 border-2 border-white transform rotate-45"></div>
                    <div class="absolute bottom-4 left-4 w-12 h-12 border-2 border-white transform -rotate-45"></div>
                    <div class="absolute top-1/2 left-1/2 w-8 h-8 border border-white transform -translate-x-1/2 -translate-y-1/2 rotate-45"></div>
                </div>
            </div>
            
            <div class="relative p-6">
                <div class="flex items-center mb-6">
                    <div class="w-1 h-6 bg-white mr-3"></div>
                    <h3 class="text-lg font-black text-white uppercase tracking-wider">ĐIỀU KHOẢN VÀ LƯU Ý</h3>
                </div>
                
                <div class="space-y-4 text-gray-300">
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-white mt-2 mr-3 flex-shrink-0"></div>
                        <p class="text-sm uppercase font-medium">YÊU CẦU HOÀN TIỀN CHỈ ĐƯỢC XỬ LÝ CHO ĐƠN HÀNG ĐÃ HOÀN THÀNH THÀNH CÔNG VÀ ĐÃ THANH TOÁN</p>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-white mt-2 mr-3 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm uppercase font-medium mb-2">THỜI GIAN XỬ LÝ HOÀN TIỀN:</p>
                            <div class="ml-4 space-y-2">
                                <div class="flex items-start">
                                    <div class="w-1 h-1 bg-gray-500 mt-2 mr-2 flex-shrink-0"></div>
                                    <p class="text-xs uppercase">HOÀN VÀO VÍ: NGAY LẬP TỨC SAU KHI ĐƯỢC DUYỆT</p>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-1 h-1 bg-gray-500 mt-2 mr-2 flex-shrink-0"></div>
                                    <p class="text-xs uppercase">HOÀN TIỀN VNPAY: 3-5 NGÀY LÀM VIỆC SAU KHI ĐƯỢC DUYỆT</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-white mt-2 mr-3 flex-shrink-0"></div>
                        <p class="text-sm uppercase font-medium">PHƯƠNG THỨC HOÀN TIỀN PHỤ THUỘC VÀO PHƯƠNG THỨC THANH TOÁN BAN ĐẦU</p>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-white mt-2 mr-3 flex-shrink-0"></div>
                        <p class="text-sm uppercase font-medium">MỌI THẮC MẮC VUI LÒNG LIÊN HỆ HOTLINE: <span class="text-white font-black">1900 XXXX</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
