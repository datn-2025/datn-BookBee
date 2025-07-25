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

        <!-- Refund Request Form - Adidas Style -->
        <div class="bg-white border border-gray-100 hover:border-black transition-all duration-500 relative overflow-hidden">
            <!-- Geometric accent -->
            <div class="absolute top-0 left-0 w-2 h-full bg-black"></div>
            
            <div class="pl-8 pr-8 py-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-6 h-0.5 bg-black"></div>
                    <h2 class="text-lg font-bold uppercase tracking-wide text-black">
                        THÔNG TIN YÊU CẦU HOÀN TIỀN
                    </h2>
                </div>
                
                <form method="POST" action="{{ route('account.orders.refund.request', $order->id) }}" class="space-y-8">
                    @csrf
                    
                    <!-- Reason Selection -->
                    <div>
                        <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-4">
                            LÝ DO YÊU CẦU HOÀN TIỀN <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <select id="reason" name="reason" required
                                class="block w-full border-2 border-gray-200 focus:border-black focus:ring-0 bg-white px-4 py-3 text-sm font-medium text-black uppercase tracking-wide transition-all duration-300">
                                <option value="">CHỌN LÝ DO</option>
                                <option value="wrong_item" {{ old('reason') == 'wrong_item' ? 'selected' : '' }}>SẢN PHẨM KHÔNG ĐÚNG MÔ TẢ</option>
                                <option value="quality_issue" {{ old('reason') == 'quality_issue' ? 'selected' : '' }}>VẤN ĐỀ VỀ CHẤT LƯỢNG</option>
                                <option value="shipping_delay" {{ old('reason') == 'shipping_delay' ? 'selected' : '' }}>GIAO HÀNG QUÁ CHẬM</option>
                                <option value="wrong_qty" {{ old('reason') == 'wrong_qty' ? 'selected' : '' }}>SỐ LƯỢNG KHÔNG ĐÚNG</option>
                                <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>LÝ DO KHÁC</option>
                            </select>
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-black transition-all duration-500 group-focus-within:w-full"></div>
                        </div>
                        @error('reason')
                            <p class="mt-2 text-xs font-bold uppercase tracking-wider text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Details -->
                    <div>
                        <label for="details" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-4">
                            CHI TIẾT LÝ DO <span class="text-red-600">*</span>
                        </label>
                        <div class="relative group">
                            <textarea id="details" name="details" rows="4" required
                                class="block w-full border-2 border-gray-200 focus:border-black focus:ring-0 bg-white px-4 py-3 text-sm font-medium text-black transition-all duration-300 resize-none"
                                placeholder="VUI LÒNG MÔ TẢ CHI TIẾT LÝ DO YÊU CẦU HOÀN TIỀN (ÍT NHẤT 20 KÝ TỰ)...">{{ old('details') }}</textarea>
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-black transition-all duration-500 group-focus-within:w-full"></div>
                        </div>
                        @error('details')
                            <p class="mt-2 text-xs font-bold uppercase tracking-wider text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Refund Method -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-6">
                            PHƯƠNG THỨC HOÀN TIỀN <span class="text-red-600">*</span>
                        </label>
                    
                    @php
                        $paymentMethod = strtolower($order->paymentMethod->name ?? '');
                        $isVnpayOrder = str_contains($paymentMethod, 'vnpay');
                        $defaultMethod = $isVnpayOrder ? 'vnpay' : 'wallet';
                    @endphp
                    
                        <div class="space-y-4">
                            <!-- Luôn hiển thị tùy chọn ví -->
                            <label class="group flex items-start cursor-pointer hover:bg-gray-50 p-4 border border-gray-200 hover:border-black transition-all duration-300">
                                <input type="radio" id="refund_wallet" name="refund_method" value="wallet" 
                                       class="w-4 h-4 text-black focus:ring-black border-gray-300 mt-1" 
                                       {{ old('refund_method', $defaultMethod) == 'wallet' ? 'checked' : '' }}>
                                <div class="ml-4 flex-1">
                                    <div class="text-sm font-bold uppercase tracking-wide text-black">
                                        HOÀN TIỀN VÀO VÍ
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1 uppercase tracking-wide">
                                        TIỀN SẼ ĐƯỢC CỘNG VÀO VÍ CỦA BẠN NGAY LẬP TỨC
                                    </p>
                                </div>
                                <div class="w-0 h-0.5 bg-black group-hover:w-4 transition-all duration-300 mt-2"></div>
                            </label>
                            
                            <!-- Chỉ hiển thị VNPay nếu đơn hàng được thanh toán qua VNPay -->
                            @if($isVnpayOrder)
                            <label class="group flex items-start cursor-pointer hover:bg-gray-50 p-4 border border-gray-200 hover:border-black transition-all duration-300">
                                <input type="radio" id="refund_vnpay" name="refund_method" value="vnpay" 
                                       class="w-4 h-4 text-black focus:ring-black border-gray-300 mt-1"
                                       {{ old('refund_method', $defaultMethod) == 'vnpay' ? 'checked' : '' }}>
                                <div class="ml-4 flex-1">
                                    <div class="text-sm font-bold uppercase tracking-wide text-black">
                                        HOÀN TIỀN QUA VNPAY 
                                        <span class="bg-black text-white px-2 py-1 text-xs ml-2">(KHUYẾN NGHỊ)</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1 uppercase tracking-wide">
                                        TIỀN SẼ ĐƯỢC HOÀN VỀ PHƯƠNG THỨC THANH TOÁN BAN ĐẦU TRONG 3-5 NGÀY LÀM VIỆC
                                    </p>
                                </div>
                                <div class="w-0 h-0.5 bg-black group-hover:w-4 transition-all duration-300 mt-2"></div>
                            </label>
                            @endif
                        </div>
                    
                        @if($isVnpayOrder)
                        <div class="mt-4 bg-gray-50 border-l-4 border-black p-4">
                            <div class="flex items-start">
                                <div class="w-2 h-2 bg-black rounded-full mt-2 mr-4 flex-shrink-0"></div>
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-black mb-2">THÔNG TIN</h4>
                                    <p class="text-xs text-gray-700 uppercase tracking-wide leading-relaxed">
                                        VÌ ĐƠN HÀNG NÀY ĐƯỢC THANH TOÁN QUA VNPAY, CHÚNG TÔI KHUYẾN NGHỊ HOÀN TIỀN QUA VNPAY ĐỂ ĐẢM BẢO NHANH CHÓNG VÀ AN TOÀN.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    
                        @error('refund_method')
                            <p class="mt-2 text-xs font-bold uppercase tracking-wider text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-6 pt-8 border-t-2 border-gray-100">
                        <a href="{{ route('account.orders.show', $order->id) }}"
                           class="group bg-white border-2 border-gray-300 hover:border-black text-black px-8 py-3 font-bold text-xs uppercase tracking-[0.1em] transition-all duration-300 flex items-center gap-3">
                            <span>HỦY</span>
                            <div class="w-4 h-0.5 bg-gray-400 group-hover:bg-black group-hover:w-8 transition-all duration-300"></div>
                        </a>
                        <button type="submit"
                            class="group bg-black hover:bg-gray-800 text-white px-8 py-3 font-bold text-xs uppercase tracking-[0.1em] transition-all duration-300 flex items-center gap-3">
                            <span>GỬI YÊU CẦU HOÀN TIỀN</span>
                            <div class="w-4 h-0.5 bg-white group-hover:w-8 transition-all duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Important Notes - Adidas Style -->
        <div class="bg-black text-white relative overflow-hidden mt-8">
            <!-- Geometric accent -->
            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 transform rotate-45 translate-x-12 -translate-y-12"></div>
            <div class="absolute bottom-0 left-0 w-32 h-1 bg-white opacity-30"></div>
            
            <div class="p-8 relative z-10">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-6 h-0.5 bg-white"></div>
                    <h3 class="text-lg font-bold uppercase tracking-wide text-white">
                        ĐIỀU KHOẢN VÀ LƯU Ý
                    </h3>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-white rounded-full mt-2 flex-shrink-0"></div>
                            <p class="text-sm font-medium text-white uppercase tracking-wide leading-relaxed">
                                YÊU CẦU HOÀN TIỀN CHỈ ĐƯỢC XỬ LÝ CHO ĐƠN HÀNG ĐÃ HOÀN THÀNH VÀ ĐÃ THANH TOÁN
                            </p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-white rounded-full mt-2 flex-shrink-0"></div>
                            <div class="text-sm font-medium text-white uppercase tracking-wide leading-relaxed">
                                <p class="mb-2">THỜI GIAN XỬ LÝ HOÀN TIỀN:</p>
                                <div class="ml-4 space-y-1">
                                    <p>• HOÀN VÀO VÍ: NGAY LẬP TỨC SAU KHI ĐƯỢC DUYỆT</p>
                                    <p>• HOÀN TIỀN VNPAY: 3-5 NGÀY LÀM VIỆC SAU KHI ĐƯỢC DUYỆT</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-white rounded-full mt-2 flex-shrink-0"></div>
                            <p class="text-sm font-medium text-white uppercase tracking-wide leading-relaxed">
                                PHƯƠNG THỨC HOÀN TIỀN PHỤ THUỘC VÀO PHƯƠNG THỨC THANH TOÁN BAN ĐẦU
                            </p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-white rounded-full mt-2 flex-shrink-0"></div>
                            <p class="text-sm font-medium text-white uppercase tracking-wide leading-relaxed">
                                MỌI THẮC MẮC VUI LÒNG LIÊN HỆ HOTLINE: 1900 XXXX
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom accent line -->
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-white via-gray-300 to-transparent opacity-50"></div>
            </div>
        </div>
    </div>
</div>
@endsection
