@extends('layouts.app')
@section('title', 'Trạng thái hoàn tiền')

@push('styles')
<style>
    .status-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .status-card:hover {
        border-color: #000;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .timeline-step {
        transition: all 0.3s ease;
    }
    .timeline-step:hover {
        transform: translateX(4px);
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('account.orders.show', $refund->order_id) }}" 
               class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-gray-300 hover:border-black text-black font-bold uppercase tracking-wide transition-all duration-300 hover:bg-gray-50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                QUAY LẠI ĐƠN HÀNG
            </a>
        </div>

        <!-- Status Header -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 geometric-bg">
            <div class="bg-black text-white px-8 py-6 relative">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8"></div>
                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-1 h-8 bg-white"></div>
                                <h1 class="text-3xl font-black uppercase tracking-wide">TRẠNG THÁI HOÀN TIỀN</h1>
                            </div>
                            <p class="text-gray-300 text-sm uppercase tracking-wider">YÊU CẦU #{{ $refund->id }} - ĐƠN HÀNG: {{ $refund->order->order_code }}</p>
                        </div>
                        <div class="text-right">
                            <div class="px-6 py-3 font-bold text-sm uppercase tracking-wide rounded
                                @if($refund->status === 'pending') bg-yellow-500 text-white
                                @elseif($refund->status === 'processing') bg-blue-600 text-white
                                @elseif($refund->status === 'completed') bg-green-600 text-white
                                @elseif($refund->status === 'rejected') bg-red-600 text-white
                                @endif">
                                @if($refund->status === 'pending') ĐANG CHỜ XỬ LÝ
                                @elseif($refund->status === 'processing') ĐANG XỬ LÝ
                                @elseif($refund->status === 'completed') HOÀN THÀNH
                                @elseif($refund->status === 'rejected') TỪ CHỐI
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Summary Card -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 status-card">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-1 h-6 bg-black"></div>
                    <h2 class="text-xl font-black uppercase tracking-wide text-black">
                        THÔNG TIN YÊU CẦU HOÀN TIỀN
                    </h2>
                </div>
                
                <div class="grid lg:grid-cols-4 gap-6">
                    <div class="bg-gray-50 p-4 border-l-4 border-black">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">MÃ ĐƠN HÀNG</p>
                        <p class="font-black text-black text-lg">{{ $refund->order->order_code }}</p>
                    </div>
                    
                    <div class="bg-black text-white p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-300 mb-1">SỐ TIỀN HOÀN</p>
                        <p class="font-black text-white text-xl">{{ number_format($refund->amount, 0, ',', '.') }}đ</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 border-l-4 border-blue-500">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">PHƯƠNG THỨC</p>
                        <div class="flex items-center gap-2">
                            @if($refund->refund_method === 'wallet')
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span class="font-bold text-black">VÍ ĐIỆN TỬ</span>
                            @else
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2.5 4A1.5 1.5 0 001 5.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-2.185A.75.75 0 0015 5.25V5.5A1.5 1.5 0 0013.5 4h-11zM15 7.586l-6.293 2.293a1.5 1.5 0 01-1.414 0L1 7.586V13.5A1.5 1.5 0 002.5 15h11a1.5 1.5 0 001.5-1.5V7.586z"/>
                                </svg>
                                <span class="font-bold text-black">VNPAY</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 border-l-4 border-purple-500">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">NGÀY YÊU CẦU</p>
                        <p class="font-bold text-black">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                        @if($refund->processed_at)
                        <p class="text-xs text-gray-500 mt-1">Xử lý: {{ $refund->processed_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Details Card -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 mb-8 status-card">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-1 h-6 bg-black"></div>
                    <h3 class="text-xl font-black uppercase tracking-wide text-black">CHI TIẾT YÊU CẦU</h3>
                </div>
                
                <div class="space-y-8">
                    <!-- Reason Section -->
                    <div class="bg-gray-50 p-6 border-l-4 border-red-500">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-3">LÝ DO HOÀN TIỀN</p>
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <p class="font-bold text-black text-lg">
                                @switch($refund->reason)
                                    @case('wrong_item')
                                        Sản phẩm không đúng mô tả
                                        @break
                                    @case('quality_issue')
                                        Vấn đề về chất lượng
                                        @break
                                    @case('shipping_delay')
                                        Giao hàng quá chậm
                                        @break
                                    @case('wrong_qty')
                                        Số lượng không đúng
                                        @break
                                    @default
                                        Lý do khác
                                @endswitch
                            </p>
                        </div>
                    </div>
                    
                    @if($refund->details)
                    <!-- Description Section -->
                    <div class="bg-blue-50 p-6 border-l-4 border-blue-500">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-3">CHI TIẾT MÔ TẢ</p>
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-700 leading-relaxed">{{ $refund->details }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($refund->images && count($refund->images) > 0)
                    <!-- Images Section -->
                    <div class="bg-green-50 p-6 border-l-4 border-green-500">
                        <div class="flex items-center gap-3 mb-4">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-600">HÌNH ẢNH MINH CHỨNG</p>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($refund->images as $image)
                            <div class="relative group cursor-pointer transform hover:scale-105 transition-all duration-300" onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                <img src="{{ asset('storage/' . $image) }}" 
                                     alt="Hình ảnh minh chứng" 
                                     class="w-full h-32 object-cover border-2 border-gray-200 hover:border-black transition-all duration-300 shadow-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                    </svg>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if($refund->admin_note)
                    <!-- Admin Note Section -->
                    <div class="bg-yellow-50 p-6 border-l-4 border-yellow-500">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-2">GHI CHÚ TỪ QUẢN TRỊ VIÊN</p>
                                <p class="text-gray-700 leading-relaxed font-medium">{{ $refund->admin_note }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($refund->processed_at)
                    <!-- Processing Date Section -->
                    <div class="bg-purple-50 p-6 border-l-4 border-purple-500">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-600 mb-1">NGÀY XỬ LÝ</p>
                                <p class="font-bold text-black text-lg">{{ $refund->processed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Timeline Card -->
        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 status-card">
            <div class="bg-black text-white p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-1 h-6 bg-white"></div>
                    <h3 class="text-xl font-black uppercase tracking-wide text-white">
                        TIẾN TRÌNH XỬ LÝ
                    </h3>
                    <div class="ml-auto px-4 py-2 font-bold text-xs uppercase tracking-wide
                        @if($refund->status === 'pending') bg-yellow-500 text-white
                        @elseif($refund->status === 'processing') bg-blue-600 text-white
                        @elseif($refund->status === 'completed') bg-green-600 text-white
                        @elseif($refund->status === 'rejected') bg-red-600 text-white
                        @endif">
                        @if($refund->status === 'pending') ĐANG CHỜ XỬ LÝ
                        @elseif($refund->status === 'processing') ĐANG XỬ LÝ
                        @elseif($refund->status === 'completed') HOÀN THÀNH
                        @elseif($refund->status === 'rejected') TỪ CHỐI
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-8 top-0 bottom-0 w-1 bg-gray-200"></div>
                    
                    <!-- Step 1: Request Submitted -->
                    <div class="relative flex items-start mb-12 timeline-step">
                        <div class="flex-shrink-0 w-16 h-16 bg-green-600 flex items-center justify-center relative z-10 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-8 bg-green-50 p-6 flex-1 border-l-4 border-green-500">
                            <h4 class="text-lg font-black uppercase tracking-wide text-black mb-2">
                                YÊU CẦU ĐÃ ĐƯỢC GỬI
                            </h4>
                            <p class="text-sm font-bold text-green-600 mb-2">
                                {{ $refund->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-gray-700">
                                Yêu cầu hoàn tiền của bạn đã được tiếp nhận và đang chờ xử lý
                            </p>
                        </div>
                    </div>
                    
                    <!-- Step 2: Processing -->
                    <div class="relative flex items-start mb-12 timeline-step">
                        <div class="flex-shrink-0 w-16 h-16 flex items-center justify-center relative z-10 shadow-lg
                            @if(in_array($refund->status, ['processing', 'completed', 'rejected'])) bg-blue-600 @else bg-gray-400 @endif">
                            @if(in_array($refund->status, ['processing', 'completed', 'rejected']))
                                @if($refund->status === 'processing')
                                    <svg class="w-8 h-8 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            @else
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-8 p-6 flex-1 border-l-4
                            @if(in_array($refund->status, ['processing', 'completed', 'rejected'])) bg-blue-50 border-blue-500 @else bg-gray-50 border-gray-300 @endif">
                            <h4 class="text-lg font-black uppercase tracking-wide mb-2
                                @if(in_array($refund->status, ['processing', 'completed', 'rejected'])) text-black @else text-gray-500 @endif">
                                ĐANG XỬ LÝ YÊU CẦU
                            </h4>
                            @if($refund->processed_at)
                                <p class="text-sm font-bold text-blue-600 mb-2">
                                    {{ $refund->processed_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                            <p class="@if(in_array($refund->status, ['processing', 'completed', 'rejected'])) text-gray-700 @else text-gray-500 @endif">
                                @if($refund->status === 'processing')
                                    Yêu cầu đang được xem xét và xử lý bởi đội ngũ hỗ trợ
                                @elseif(in_array($refund->status, ['completed', 'rejected']))
                                    Yêu cầu đã được xử lý thành công
                                @else
                                    Chờ xử lý
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Step 3: Completed/Rejected -->
                    <div class="relative flex items-start timeline-step">
                        <div class="flex-shrink-0 w-16 h-16 flex items-center justify-center relative z-10 shadow-lg
                            @if($refund->status === 'completed') bg-green-600 
                            @elseif($refund->status === 'rejected') bg-red-600 
                            @else bg-gray-400 @endif">
                            @if($refund->status === 'completed')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($refund->status === 'rejected')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-8 p-6 flex-1 border-l-4
                            @if($refund->status === 'completed') bg-green-50 border-green-500
                            @elseif($refund->status === 'rejected') bg-red-50 border-red-500
                            @else bg-gray-50 border-gray-300 @endif">
                            <h4 class="text-lg font-black uppercase tracking-wide mb-2
                                @if(in_array($refund->status, ['completed', 'rejected'])) text-black @else text-gray-500 @endif">
                                @if($refund->status === 'completed')
                                    HOÀN TIỀN THÀNH CÔNG
                                @elseif($refund->status === 'rejected')
                                    YÊU CẦU BỊ TỪ CHỐI
                                @else
                                    CHỜ HOÀN TIỀN
                                @endif
                            </h4>
                            @if(in_array($refund->status, ['completed', 'rejected']) && $refund->processed_at)
                                <p class="text-sm font-bold mb-2
                                    @if($refund->status === 'completed') text-green-600
                                    @elseif($refund->status === 'rejected') text-red-600
                                    @endif">
                                    {{ $refund->processed_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                            <p class="@if(in_array($refund->status, ['completed', 'rejected'])) text-gray-700 @else text-gray-500 @endif">
                                @if($refund->status === 'completed')
                                    Tiền đã được hoàn về tài khoản của bạn thành công
                                @elseif($refund->status === 'rejected')
                                    Yêu cầu hoàn tiền không được chấp nhận
                                @else
                                    Chờ hoàn tiền
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Hình ảnh phóng to" class="max-w-full max-h-full object-contain rounded">
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>

@endsection
