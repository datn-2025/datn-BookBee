@extends('layouts.app')

@section('title', 'Yêu cầu hoàn tiền Ebook')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 mb-8">
            <div class="bg-black text-white px-8 py-6">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-1 h-8 bg-white"></div>
                    <h1 class="text-xl font-black uppercase tracking-wide">
                        YÊU CẦU HOÀN TIỀN EBOOK
                    </h1>
                </div>
                <p class="text-gray-300 text-sm uppercase tracking-wider">ĐƠN HÀNG #{{ $order->order_code }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Thông tin đơn hàng -->
            <div class="bg-white border-2 border-gray-200 p-6">
                <h2 class="text-lg font-bold mb-4 uppercase tracking-wide">THÔNG TIN ĐƠN HÀNG</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="font-medium">Mã đơn hàng:</span>
                        <span class="font-bold">#{{ $order->order_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Ngày đặt:</span>
                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Tổng tiền:</span>
                        <span class="font-bold text-red-600">{{ number_format($order->total_amount) }}đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Trạng thái thanh toán:</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-medium">
                            {{ $order->paymentStatus->name ?? 'Không xác định' }}
                        </span>
                    </div>
                </div>

                <hr class="my-4">

                <h3 class="font-bold mb-3 uppercase tracking-wide">EBOOK TRONG ĐƠN HÀNG</h3>
                <div class="space-y-3">
                    @foreach($refundCalculation['details'] as $detail)
                    <div class="border border-gray-200 p-3 rounded {{ !$detail['can_refund'] ? 'bg-red-50' : '' }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-sm">{{ $detail['book_title'] }}</h4>
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-xs px-2 py-1 rounded 
                                    @if($detail['download_count'] === 0) bg-green-100 text-green-800
                                    @elseif($detail['download_count'] === 1) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $detail['refund_status'] }}
                                </span>
                                @if(!$detail['can_refund'])
                                <span class="text-xs px-2 py-1 bg-red-500 text-white rounded font-bold">
                                    KHÔNG THỂ HOÀN TIỀN
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Giá gốc:</span>
                                <span>{{ number_format($detail['original_amount']) }}đ</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Số lần tải:</span>
                                <span class="font-medium">{{ $detail['download_count'] }} lần</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Hoàn trả ({{ $detail['refund_percentage'] }}%):</span>
                                <span class="font-bold {{ $detail['can_refund'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $detail['can_refund'] ? number_format($detail['refund_amount']) . 'đ' : '0đ' }}
                                </span>
                            </div>
                            @if($detail['download_count'] > 1)
                            <div class="mt-2 p-2 bg-red-100 border border-red-200 rounded">
                                <p class="text-xs text-red-700 font-medium">
                                    ⚠️ Ebook đã được tải {{ $detail['download_count'] }} lần. Theo chính sách, chỉ ebook tải tối đa 1 lần mới được hoàn tiền.
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg">TỔNG HOÀN TRẢ:</span>
                        <span class="font-black text-xl text-green-600">{{ number_format($refundCalculation['total_refund_amount']) }}đ</span>
                    </div>
                </div>
            </div>

            <!-- Form yêu cầu hoàn tiền -->
            <div class="bg-white border-2 border-gray-200 p-6">
                <h2 class="text-lg font-bold mb-4 uppercase tracking-wide">THÔNG TIN YÊU CẦU</h2>
                
                <form action="{{ route('ebook-refund.store', $order->id) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="reason" class="block text-sm font-bold mb-2 uppercase tracking-wide">LÝ DO HOÀN TIỀN *</label>
                        <select name="reason" id="reason" class="w-full border-2 border-gray-200 focus:border-black p-3 transition-all duration-300" required>
                            <option value="">Chọn lý do hoàn tiền</option>
                            <option value="File lỗi không đọc được" {{ old('reason') == 'File lỗi không đọc được' ? 'selected' : '' }}>File lỗi không đọc được</option>
                            <option value="Nội dung không đúng mô tả" {{ old('reason') == 'Nội dung không đúng mô tả' ? 'selected' : '' }}>Nội dung không đúng mô tả</option>
                            <option value="Giao sai sách" {{ old('reason') == 'Giao sai sách' ? 'selected' : '' }}>Giao sai sách</option>
                            <option value="Chất lượng kém" {{ old('reason') == 'Chất lượng kém' ? 'selected' : '' }}>Chất lượng kém</option>
                            <option value="Không hài lòng" {{ old('reason') == 'Không hài lòng' ? 'selected' : '' }}>Không hài lòng</option>
                            <option value="Khác" {{ old('reason') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('reason')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="details" class="block text-sm font-bold mb-2 uppercase tracking-wide">CHI TIẾT (TÙY CHỌN)</label>
                        <textarea name="details" id="details" rows="4" 
                                  class="w-full border-2 border-gray-200 focus:border-black p-3 transition-all duration-300 resize-none" 
                                  placeholder="Mô tả chi tiết vấn đề bạn gặp phải...">{{ old('details') }}</textarea>
                        @error('details')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Tối đa 1000 ký tự</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 p-4 rounded">
                        <h3 class="font-bold text-blue-800 mb-2">CHÍNH SÁCH HOÀN TIỀN EBOOK</h3>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• <strong>Chưa tải file:</strong> Hoàn 100% giá trị ebook</li>
                            <li>• <strong>Đã tải 1 lần:</strong> Hoàn 40% giá trị ebook</li>
                            <li>• <strong class="text-red-600">Đã tải trên 1 lần:</strong> <span class="text-red-600">Không được hoàn tiền</span></li>
                            <li>• Thời hạn yêu cầu: 7 ngày kể từ ngày mua</li>
                            <li>• Tiền sẽ được hoàn về ví điện tử trong 24-48 giờ</li>
                        </ul>
                        <div class="mt-3 p-2 bg-yellow-100 border border-yellow-300 rounded">
                            <p class="text-xs text-yellow-800 font-medium">
                                ⚠️ <strong>Lưu ý:</strong> Chỉ ebook được tải tối đa 1 lần mới có thể yêu cầu hoàn tiền. Ebook đã tải nhiều hơn 1 lần sẽ không được hoàn tiền theo chính sách bảo vệ bản quyền.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" 
                                class="flex-1 bg-black text-white py-3 px-6 font-bold uppercase tracking-wide hover:bg-gray-800 transition-all duration-300">
                            GỬI YÊU CẦU HOÀN TIỀN
                        </button>
                        <a href="{{ route('orders.show', $order->id) }}" 
                           class="flex-1 bg-gray-200 text-black py-3 px-6 font-bold uppercase tracking-wide text-center hover:bg-gray-300 transition-all duration-300">
                            HỦY BỎ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-focus vào textarea khi chọn "Khác"
document.getElementById('reason').addEventListener('change', function() {
    if (this.value === 'Khác') {
        document.getElementById('details').focus();
    }
});

// Character counter cho textarea
const textarea = document.getElementById('details');
const maxLength = 1000;

// Tạo counter element
const counter = document.createElement('div');
counter.className = 'text-xs text-gray-500 mt-1 text-right';
counter.textContent = `0/${maxLength} ký tự`;
textarea.parentNode.appendChild(counter);

textarea.addEventListener('input', function() {
    const currentLength = this.value.length;
    counter.textContent = `${currentLength}/${maxLength} ký tự`;
    
    if (currentLength > maxLength * 0.9) {
        counter.className = 'text-xs text-red-500 mt-1 text-right';
    } else {
        counter.className = 'text-xs text-gray-500 mt-1 text-right';
    }
});
</script>
@endpush