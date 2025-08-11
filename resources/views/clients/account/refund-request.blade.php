@extends('layouts.app')
@section('title', 'Yêu cầu hoàn tiền')

@push('styles')
<style>
    .refund-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .refund-card:hover {
        border-color: #000;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .geometric-bg::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(0,0,0,0.05);
        transform: rotate(45deg) translate(50px, -50px);
    }
    .upload-zone {
        transition: all 0.3s ease;
    }
    .upload-zone:hover {
        border-color: #000;
        background-color: #f9fafb;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('account.orders.show', $order->id) }}" 
               class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-gray-300 hover:border-black text-black font-bold uppercase tracking-wide transition-all duration-300 hover:bg-gray-50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                QUAY LẠI ĐƠN HÀNG
            </a>
        </div>

        <!-- Refund Header -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 geometric-bg">
            <div class="bg-black text-white px-8 py-6 relative">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-1 h-8 bg-white"></div>
                                <h1 class="text-3xl font-black uppercase tracking-wide">YÊU CẦU HOÀN TIỀN</h1>
                            </div>
                            <p class="text-gray-300 text-sm uppercase tracking-wider">ĐƠN HÀNG: {{ $order->order_code }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-300 uppercase tracking-wide">Tổng tiền đơn hàng</p>
                            <p class="text-2xl font-black text-white">
                                {{ number_format($order->total_amount, 0, ',', '.') }}đ
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Order Summary Card -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 refund-card">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-1 h-6 bg-black"></div>
                    <h2 class="text-xl font-black uppercase tracking-wide text-black">
                        THÔNG TIN ĐƠN HÀNG
                    </h2>
                </div>

                <div class="grid lg:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 border-l-4 border-black">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">MÃ ĐƠN HÀNG</p>
                            <p class="font-black text-black text-lg">{{ $order->order_code }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 border-l-4 border-gray-300">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">NGÀY ĐẶT</p>
                            <p class="font-bold text-black">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 border-l-4 border-green-500">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">TRẠNG THÁI</p>
                            <span class="inline-flex px-3 py-1 bg-green-100 text-green-800 text-xs font-bold uppercase tracking-wide rounded">
                                {{ $order->paymentStatus->name ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="bg-gray-50 p-4 border-l-4 border-blue-500">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">THANH TOÁN</p>
                            <p class="font-bold text-black">{{ $order->paymentMethod->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-black text-white p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-300 mb-1">TỔNG TIỀN</p>
                            <p class="font-black text-white text-2xl">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                        </div>
                        <div class="bg-gray-50 p-4 border-l-4 border-purple-500">
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">SỐ LƯỢNG</p>
                            <p class="font-bold text-black">{{ $order->orderItems->sum('quantity') }} sản phẩm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Request Form -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden refund-card">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-1 h-8 bg-black"></div>
                    <h2 class="text-2xl font-black uppercase tracking-wide text-black">
                        FORM YÊU CẦU HOÀN TIỀN
                    </h2>
                </div>
                
                <form method="POST" action="{{ route('account.orders.refund.request', $order->id) }}" class="space-y-8" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Reason Selection -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-6 bg-black"></div>
                            <label for="reason" class="text-lg font-black uppercase tracking-wide text-black">
                                LÝ DO YÊU CẦU HOÀN TIỀN <span class="text-red-600">*</span>
                            </label>
                        </div>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @php
                                $reasons = [
                                    'wrong_item' => 'SẢN PHẨM KHÔNG ĐÚNG MÔ TẢ',
                                    'quality_issue' => 'VẤN ĐỀ VỀ CHẤT LƯỢNG', 
                                    'shipping_delay' => 'GIAO HÀNG QUÁ CHẬM',
                                    'wrong_qty' => 'SỐ LƯỢNG KHÔNG ĐÚNG',
                                    'damaged' => 'SẢN PHẨM HƯ HỎNG',
                                    'other' => 'LÝ DO KHÁC'
                                ];
                            @endphp
                            @foreach($reasons as $value => $label)
                            <label class="group cursor-pointer">
                                <div class="border-2 border-gray-200 hover:border-black transition-all duration-300 p-4 bg-white hover:bg-gray-50 relative overflow-hidden">
                                    <div class="flex items-center gap-3 relative z-10">
                                        <input type="radio" name="reason" value="{{ $value }}" 
                                               class="w-5 h-5 text-black border-2 border-gray-300 focus:ring-black focus:ring-2" 
                                               {{ old('reason') == $value ? 'checked' : '' }}
                                               required>
                                        <span class="text-sm font-bold uppercase tracking-wide text-black">
                                            {{ $label }}
                                        </span>
                                    </div>
                                    <div class="absolute bottom-0 left-0 h-1 bg-black w-0 group-hover:w-full transition-all duration-300"></div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('reason')
                            <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-red-600 text-sm font-bold uppercase tracking-wide">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>

                    <!-- Additional Details -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-6 bg-black"></div>
                            <label for="details" class="text-lg font-black uppercase tracking-wide text-black">
                                CHI TIẾT LÝ DO <span class="text-red-600">*</span>
                            </label>
                        </div>
                        <div class="relative group bg-white border-2 border-gray-200 hover:border-black transition-all duration-300">
                            <textarea id="details" name="details" rows="6" required
                                class="block w-full border-0 focus:ring-0 bg-transparent px-6 py-4 text-sm font-medium text-black transition-all duration-300 resize-none placeholder-gray-500"
                                placeholder="VUI LÒNG MÔ TẢ CHI TIẾT LÝ DO YÊU CẦU HOÀN TIỀN (ÍT NHẤT 20 KÝ TỰ)...">{{ old('details') }}</textarea>
                            <div class="absolute bottom-0 left-0 w-0 h-1 bg-black transition-all duration-500 group-focus-within:w-full"></div>
                        </div>
                        @error('details')
                            <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-red-600 text-sm font-bold uppercase tracking-wide">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>

                    <!-- Upload Images -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-6 bg-black"></div>
                            <label class="text-lg font-black uppercase tracking-wide text-black">
                                HÌNH ẢNH MINH CHỨNG <span class="text-gray-500 text-sm font-medium">(TỐI ĐA 5 HÌNH)</span>
                            </label>
                        </div>
                        <div class="relative group">
                            <div class="border-2 border-dashed border-gray-200 hover:border-black transition-all duration-300 p-8 text-center bg-white hover:bg-gray-50">
                                <input type="file" id="images" name="images[]" multiple accept="image/*" 
                                       class="hidden" onchange="previewImages(this)">
                                <label for="images" class="cursor-pointer">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center mb-6 group-hover:bg-gray-800 transition-all duration-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-lg font-black uppercase tracking-wide text-black mb-3">
                                            CHỌN HÌNH ẢNH
                                        </p>
                                        <p class="text-sm text-gray-600 uppercase tracking-wide mb-2">
                                            KÉNG THẢ HOẶC CLICK ĐỂ CHỌN HÌNH ẢNH
                                        </p>
                                        <div class="bg-gray-100 px-4 py-2 rounded">
                                            <p class="text-xs text-gray-600 font-medium">
                                                JPG, PNG, GIF - TỐI ĐA 2MB MỖI HÌNH
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Preview Images -->
                            <div id="imagePreview" class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 hidden">
                                <!-- Images will be displayed here -->
                            </div>
                        </div>
                        @error('images')
                            <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-red-600 text-sm font-bold uppercase tracking-wide">{{ $message }}</p>
                            </div>
                        @enderror
                        @error('images.*')
                            <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4">
                                <p class="text-red-600 text-sm font-bold uppercase tracking-wide">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>

                    <!-- Refund Method -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-6 bg-black"></div>
                            <label class="text-lg font-black uppercase tracking-wide text-black">
                                PHƯƠNG THỨC HOÀN TIỀN <span class="text-red-600">*</span>
                            </label>
                        </div>
                    
                    @php
                        $paymentMethod = strtolower($order->paymentMethod->name ?? '');
                        $isVnpayOrder = str_contains($paymentMethod, 'vnpay');
                        $defaultMethod = $isVnpayOrder ? 'vnpay' : 'wallet';
                    @endphp
                    
                        <div class="space-y-4">
                            <!-- Luôn hiển thị tùy chọn ví -->
                            <label class="group flex items-start cursor-pointer hover:bg-gray-50 p-6 border-2 border-gray-200 hover:border-black transition-all duration-300 relative overflow-hidden">
                                <input type="radio" id="refund_wallet" name="refund_method" value="wallet" 
                                       class="w-5 h-5 text-black focus:ring-black border-gray-300 mt-1" 
                                       {{ old('refund_method', $defaultMethod) == 'wallet' ? 'checked' : '' }}>
                                <div class="ml-6 flex-1">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-lg font-black uppercase tracking-wide text-black">
                                                HOÀN TIỀN VÀO VÍ
                                            </div>
                                            <p class="text-sm text-gray-600 uppercase tracking-wide">
                                                TIỀN SẼ ĐƯỢC CỘNG VÀO VÍ CỦA BẠN NGAY LẬP TỨC
                                            </p>
                                        </div>
                                    </div>
                                    <div class="bg-green-50 px-3 py-2 rounded">
                                        <p class="text-xs text-green-600 font-medium uppercase tracking-wide">Thời gian: Ngay lập tức sau khi được duyệt</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 h-1 bg-green-500 w-0 group-hover:w-full transition-all duration-300"></div>
                            </label>
                            
                            <!-- Chỉ hiển thị VNPay nếu đơn hàng được thanh toán qua VNPay -->
                            @if($isVnpayOrder)
                            <label class="group flex items-start cursor-pointer hover:bg-gray-50 p-6 border-2 border-gray-200 hover:border-black transition-all duration-300 relative overflow-hidden">
                                <input type="radio" id="refund_vnpay" name="refund_method" value="vnpay" 
                                       class="w-5 h-5 text-black focus:ring-black border-gray-300 mt-1"
                                       {{ old('refund_method', $defaultMethod) == 'vnpay' ? 'checked' : '' }}>
                                <div class="ml-6 flex-1">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M2.5 4A1.5 1.5 0 001 5.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-2.185A.75.75 0 0015 5.25V5.5A1.5 1.5 0 0013.5 4h-11zM15 7.586l-6.293 2.293a1.5 1.5 0 01-1.414 0L1 7.586V13.5A1.5 1.5 0 002.5 15h11a1.5 1.5 0 001.5-1.5V7.586z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-lg font-black uppercase tracking-wide text-black">
                                                HOÀN TIỀN QUA VNPAY 
                                                <span class="bg-black text-white px-2 py-1 text-xs ml-2">(KHUYẾN NGHỊ)</span>
                                            </div>
                                            <p class="text-sm text-gray-600 uppercase tracking-wide">
                                                TIỀN SẼ ĐƯỢC HOÀN VỀ PHƯƠNG THỨC THANH TOÁN BAN ĐẦU
                                            </p>
                                        </div>
                                    </div>
                                    <div class="bg-blue-50 px-3 py-2 rounded">
                                        <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Thời gian: 3-5 ngày làm việc sau khi được duyệt</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 h-1 bg-blue-500 w-0 group-hover:w-full transition-all duration-300"></div>
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
                    <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-6 pt-8 border-t-2 border-gray-100">
                        <a href="{{ route('account.orders.show', $order->id) }}"
                           class="group bg-white border-2 border-gray-300 hover:border-black text-black px-8 py-4 font-bold text-sm uppercase tracking-[0.1em] transition-all duration-300 flex items-center justify-center gap-3 hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>HỦY BỎ</span>
                            <div class="w-4 h-0.5 bg-gray-400 group-hover:bg-black group-hover:w-8 transition-all duration-300"></div>
                        </a>
                        <button type="submit"
                            class="group bg-black hover:bg-gray-800 text-white px-8 py-4 font-bold text-sm uppercase tracking-[0.1em] transition-all duration-300 flex items-center justify-center gap-3 hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>GỬI YÊU CẦU HOÀN TIỀN</span>
                            <div class="w-4 h-0.5 bg-white group-hover:w-8 transition-all duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden mt-8 refund-card">
            <div class="bg-gray-50 border-b-2 border-gray-200 px-8 py-6">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-8 bg-black"></div>
                    <h3 class="text-xl font-black uppercase tracking-wide text-black">
                        ĐIỀU KHOẢN VÀ LƯU Ý
                    </h3>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 bg-black text-white rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold">1</span>
                            </div>
                            <div>
                                <p class="font-black text-black uppercase tracking-wide text-sm mb-2">THỜI GIAN XỬ LÝ</p>
                                <div class="text-sm text-gray-700 leading-relaxed">
                                    <p class="mb-2">Yêu cầu hoàn tiền sẽ được xử lý:</p>
                                    <div class="ml-4 space-y-1">
                                        <p>• <span class="font-bold text-black">Hoàn vào ví:</span> Ngay lập tức sau khi được duyệt</p>
                                        <p>• <span class="font-bold text-black">Hoàn tiền VNPay:</span> 3-5 ngày làm việc sau khi được duyệt</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 bg-black text-white rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold">2</span>
                            </div>
                            <div>
                                <p class="font-black text-black uppercase tracking-wide text-sm mb-2">ĐIỀU KIỆN HOÀN TIỀN</p>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Yêu cầu hoàn tiền chỉ được xử lý cho đơn hàng <span class="font-bold text-black">đã hoàn thành và đã thanh toán</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 bg-black text-white rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold">3</span>
                            </div>
                            <div>
                                <p class="font-black text-black uppercase tracking-wide text-sm mb-2">PHƯƠNG THỨC HOÀN TIỀN</p>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Phương thức hoàn tiền <span class="font-bold text-black">phụ thuộc vào phương thức thanh toán ban đầu</span> của đơn hàng.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 bg-black text-white rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold">4</span>
                            </div>
                            <div>
                                <p class="font-black text-black uppercase tracking-wide text-sm mb-2">LIÊN HỆ HỖ TRỢ</p>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    Mọi thắc mắc vui lòng liên hệ:<br>
                                    Hotline: <span class="font-bold text-black">1900 XXXX</span><br>
                                    Email: <span class="font-bold text-black">support@example.com</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="font-black text-yellow-800 uppercase tracking-wide text-sm">LƯU Ý QUAN TRỌNG</p>
                    </div>
                    <p class="text-sm text-yellow-700 leading-relaxed">
                        Sau khi gửi yêu cầu, bạn không thể chỉnh sửa thông tin. Vui lòng kiểm tra kỹ trước khi gửi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImages(input) {
    const previewContainer = document.getElementById('imagePreview');
    const files = input.files;
    
    // Giới hạn tối đa 5 hình
    if (files.length > 5) {
        alert('Bạn chỉ có thể chọn tối đa 5 hình ảnh!');
        input.value = '';
        return;
    }
    
    // Xóa preview cũ
    previewContainer.innerHTML = '';
    
    if (files.length > 0) {
        previewContainer.classList.remove('hidden');
        
        Array.from(files).forEach((file, index) => {
            // Kiểm tra kích thước file (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert(`Hình ảnh ${file.name} quá lớn. Vui lòng chọn hình ảnh nhỏ hơn 2MB.`);
                input.value = '';
                previewContainer.innerHTML = '';
                previewContainer.classList.add('hidden');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageDiv = document.createElement('div');
                imageDiv.className = 'relative group';
                imageDiv.innerHTML = `
                    <div class="aspect-square bg-gray-100 rounded border-2 border-gray-200 hover:border-black transition-all duration-300 overflow-hidden">
                        <img src="${e.target.result}" alt="Preview ${index + 1}" 
                             class="w-full h-full object-cover">
                    </div>
                    <button type="button" onclick="removeImage(${index}, this)" 
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-600 text-white rounded-full text-xs font-bold hover:bg-red-700 transition-all duration-300 flex items-center justify-center">
                        ×
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 text-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                `;
                previewContainer.appendChild(imageDiv);
            };
            reader.readAsDataURL(file);
        });
    } else {
        previewContainer.classList.add('hidden');
    }
}

function removeImage(index, button) {
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    const files = input.files;
    
    // Tạo lại danh sách file mới không bao gồm file bị xóa
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    // Cập nhật input với danh sách file mới
    input.files = dt.files;
    
    // Cập nhật preview
    previewImages(input);
}

// Drag and drop functionality
const dropZone = document.querySelector('.border-dashed');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-black', 'bg-gray-50');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-black', 'bg-gray-50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-black', 'bg-gray-50');
    
    const files = e.dataTransfer.files;
    const input = document.getElementById('images');
    input.files = files;
    previewImages(input);
});
</script>

@endsection
