@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Yêu cầu hoàn tiền</h1>
            <a href="{{ route('account.orders.show', $order->id) }}" 
               class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại chi tiết đơn hàng
            </a>
        </div>

        <!-- Order Summary Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Thông tin đơn hàng #{{ $order->code }}</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Ngày đặt hàng</p>
                    <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng tiền</p>
                    <p class="font-medium text-lg text-red-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Phương thức thanh toán</p>
                    <p class="font-medium">{{ $order->paymentMethod->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Trạng thái thanh toán</p>
                    <p class="font-medium">{{ $order->paymentStatus->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Refund Request Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Thông tin yêu cầu hoàn tiền</h2>
            
            <form method="POST" action="{{ route('account.orders.refund.request', $order->id) }}" class="space-y-6">
                @csrf
                
                <!-- Reason Selection -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                        Lý do yêu cầu hoàn tiền <span class="text-red-600">*</span>
                    </label>
                    <select id="reason" name="reason" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Chọn lý do</option>
                        <option value="wrong_item" {{ old('reason') == 'wrong_item' ? 'selected' : '' }}>Sản phẩm không đúng mô tả</option>
                        <option value="quality_issue" {{ old('reason') == 'quality_issue' ? 'selected' : '' }}>Vấn đề về chất lượng</option>
                        <option value="shipping_delay" {{ old('reason') == 'shipping_delay' ? 'selected' : '' }}>Giao hàng quá chậm</option>
                        <option value="wrong_qty" {{ old('reason') == 'wrong_qty' ? 'selected' : '' }}>Số lượng không đúng</option>
                        <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Lý do khác</option>
                    </select>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Details -->
                <div>
                    <label for="details" class="block text-sm font-medium text-gray-700 mb-1">
                        Chi tiết lý do <span class="text-red-600">*</span>
                    </label>
                    <textarea id="details" name="details" rows="4" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Vui lòng mô tả chi tiết lý do yêu cầu hoàn tiền (ít nhất 20 ký tự)...">{{ old('details') }}</textarea>
                    @error('details')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Refund Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Phương thức hoàn tiền <span class="text-red-600">*</span>
                    </label>
                    
                    @php
                        $paymentMethod = strtolower($order->paymentMethod->name ?? '');
                        $isVnpayOrder = str_contains($paymentMethod, 'vnpay');
                        $defaultMethod = $isVnpayOrder ? 'vnpay' : 'wallet';
                    @endphp
                    
                    <div class="space-y-3">
                        <!-- Luôn hiển thị tùy chọn ví -->
                        <div class="flex items-center">
                            <input type="radio" id="refund_wallet" name="refund_method" value="wallet" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                                   {{ old('refund_method', $defaultMethod) == 'wallet' ? 'checked' : '' }}>
                            <label for="refund_wallet" class="ml-3 block text-sm font-medium text-gray-700">
                                Hoàn tiền vào ví
                                <p class="text-xs text-gray-500 mt-1">Tiền sẽ được cộng vào ví của bạn ngay lập tức</p>
                            </label>
                        </div>
                        
                        <!-- Chỉ hiển thị VNPay nếu đơn hàng được thanh toán qua VNPay -->
                        @if($isVnpayOrder)
                        <div class="flex items-center">
                            <input type="radio" id="refund_vnpay" name="refund_method" value="vnpay" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                   {{ old('refund_method', $defaultMethod) == 'vnpay' ? 'checked' : '' }}>
                            <label for="refund_vnpay" class="ml-3 block text-sm font-medium text-gray-700">
                                Hoàn tiền qua VNPay 
                                <span class="text-blue-600 font-medium">(Khuyến nghị)</span>
                                <p class="text-xs text-gray-500 mt-1">Tiền sẽ được hoàn về phương thức thanh toán ban đầu trong 3-5 ngày làm việc</p>
                            </label>
                        </div>
                        @endif
                    </div>
                    
                    @if($isVnpayOrder)
                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Thông tin</h4>
                                <p class="text-sm text-blue-700 mt-1">Vì đơn hàng này được thanh toán qua VNPay, chúng tôi khuyến nghị hoàn tiền qua VNPay để đảm bảo nhanh chóng và an toàn.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @error('refund_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('account.orders.show', $order->id) }}"
                       class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Hủy
                    </a>
                    <button type="submit"
                        class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Gửi yêu cầu hoàn tiền
                    </button>
                </div>
            </form>
        </div>

        <!-- Important Notes -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-800 mb-2">Điều khoản và lưu ý:</h3>
            <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                <li>Yêu cầu hoàn tiền chỉ được xử lý cho đơn hàng đã hoàn thành thành công và đã thanh toán</li>
                <li>Thời gian xử lý hoàn tiền:</li>
                <ul class="list-disc list-inside ml-4 space-y-1">
                    <li>Hoàn vào ví: Ngay lập tức sau khi được duyệt</li>
                    <li>Hoàn tiền VNPay: 3-5 ngày làm việc sau khi được duyệt</li>
                </ul>
                <li>Phương thức hoàn tiền phụ thuộc vào phương thức thanh toán ban đầu</li>
                <li>Mọi thắc mắc vui lòng liên hệ hotline: 1900 xxxx</li>
            </ul>
        </div>
    </div>
</div>
@endsection
