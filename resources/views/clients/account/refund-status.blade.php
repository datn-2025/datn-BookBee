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
                    REFUND STATUS
                </span>
            </div>
            <div class="flex items-center justify-between">
                <h1 class="text-4xl md:text-5xl font-black uppercase tracking-tight text-black">
                    TRẠNG THÁI<br>
                    <span class="text-gray-400">HOÀN TIỀN</span>
                </h1>
                <a href="{{ route('account.orders.show', $refund->order_id) }}" 
                   class="group bg-black text-white px-6 py-3 font-bold text-xs uppercase tracking-[0.1em] hover:bg-gray-800 transition-all duration-300 flex items-center gap-3">
                    <i class="fas fa-arrow-left"></i>
                    <span>QUAY LẠI</span>
                    <div class="w-4 h-0.5 bg-white transform group-hover:w-8 transition-all duration-300"></div>
                </a>
            </div>
        </div>

        <!-- Status Card - Adidas Style -->
        <div class="bg-white border border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500 relative overflow-hidden mb-8 group">
            <!-- Geometric background -->
            <div class="absolute top-0 right-0 w-20 h-20 bg-gray-50 transform rotate-45 translate-x-10 -translate-y-10 group-hover:bg-gray-100 group-hover:scale-110 transition-all duration-500"></div>
            
            <div class="p-8 relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-6 h-0.5 bg-black"></div>
                        <h2 class="text-lg font-bold uppercase tracking-wide text-black">
                            YÊU CẦU #{{ $refund->id }}
                        </h2>
                    </div>
                    <div class="px-4 py-2 font-bold text-xs uppercase tracking-wide
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
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">MÃ ĐƠN HÀNG</p>
                        <p class="text-sm font-medium text-black">{{ $refund->order->order_code }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">SỐ TIỀN HOÀN</p>
                        <div class="flex items-center gap-2">
                            <span class="bg-green-600 text-white px-2 py-1 text-xs font-bold uppercase tracking-wide">
                                {{ number_format($refund->amount, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">PHƯƠNG THỨC</p>
                        <p class="text-sm font-medium text-black">
                            @if($refund->refund_method === 'wallet')
                                VÍ
                            @else
                                VNPAY
                            @endif
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">NGÀY YÊU CẦU</p>
                        <p class="text-sm font-medium text-black">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết yêu cầu hoàn tiền</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-700">Lý do:</p>
                    <p class="text-sm text-gray-600 mt-1">
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
                
                <div>
                    <p class="text-sm font-medium text-gray-700">Chi tiết:</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $refund->details }}</p>
                </div>
                
                @if($refund->admin_note)
                <div>
                    <p class="text-sm font-medium text-gray-700">Ghi chú từ admin:</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $refund->admin_note }}</p>
                </div>
                @endif
                    @if($refund->processed_at)
                    <div class="col-span-2 space-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">NGÀY XỬ LÝ</p>
                        <p class="text-sm font-medium text-black">{{ $refund->processed_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Progress indicator -->
                <div class="absolute bottom-0 left-0 h-1 bg-black w-0 group-hover:w-full transition-all duration-700"></div>
            </div>
        </div>

        <!-- Status Timeline - Adidas Style -->
        <div class="bg-black text-white relative overflow-hidden">
            <!-- Geometric accents -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 transform rotate-45 translate-x-16 -translate-y-16"></div>
            <div class="absolute bottom-0 left-0 w-48 h-1 bg-white opacity-20"></div>
            
            <div class="p-8 relative z-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-6 h-0.5 bg-white"></div>
                    <h3 class="text-lg font-bold uppercase tracking-wide text-white">
                        TIẾN TRÌNH XỬ LÝ
                    </h3>
                </div>
                
                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-white opacity-30"></div>
                    
                    <!-- Step 1: Request Submitted -->
                    <div class="relative flex items-start mb-12">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-600 flex items-center justify-center relative z-10">
                            <i class="fas fa-check text-white text-lg"></i>
                        </div>
                        <div class="ml-6">
                            <h4 class="text-sm font-bold uppercase tracking-wide text-white mb-1">
                                YÊU CẦU ĐÃ ĐƯỢC GỬI
                            </h4>
                            <p class="text-xs text-gray-300 uppercase tracking-wide mb-2">
                                {{ $refund->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">
                                YÊU CẦU HOÀN TIỀN CỦA BẠN ĐÃ ĐƯỢC TIẾP NHẬN
                            </p>
                        </div>
                    </div>
                    
                    <!-- Step 2: Processing -->
                    <div class="relative flex items-start mb-12">
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center relative z-10
                            @if(in_array($refund->status, ['processing', 'completed'])) bg-blue-600 @else bg-gray-600 @endif">
                            @if(in_array($refund->status, ['processing', 'completed']))
                                @if($refund->status === 'processing')
                                    <i class="fas fa-spinner fa-spin text-white text-lg"></i>
                                @else
                                    <i class="fas fa-check text-white text-lg"></i>
                                @endif
                            @else
                                <i class="fas fa-clock text-gray-400 text-lg"></i>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h4 class="text-sm font-bold uppercase tracking-wide mb-1
                                @if(in_array($refund->status, ['processing', 'completed'])) text-white @else text-gray-500 @endif">
                                ĐANG XỬ LÝ YÊU CẦU
                            </h4>
                            @if($refund->processed_at)
                                <p class="text-xs text-gray-300 uppercase tracking-wide mb-2">
                                    {{ $refund->processed_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400 uppercase tracking-wide">
                                @if($refund->status === 'processing')
                                    YÊU CẦU ĐANG ĐƯỢC XEM XÉT VÀ XỬ LÝ
                                @elseif($refund->status === 'completed')
                                    YÊU CẦU ĐÃ ĐƯỢC XỬ LÝ THÀNH CÔNG
                                @else
                                    CHỜ XỬ LÝ
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Step 3: Completed -->
                    <div class="relative flex items-start">
                        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center relative z-10
                            @if($refund->status === 'completed') bg-green-600 @else bg-gray-600 @endif">
                            @if($refund->status === 'completed')
                                <i class="fas fa-check text-white text-lg"></i>
                            @else
                                <i class="fas fa-clock text-gray-400 text-lg"></i>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h4 class="text-sm font-bold uppercase tracking-wide mb-1
                                @if($refund->status === 'completed') text-white @else text-gray-500 @endif">
                                HOÀN TIỀN THÀNH CÔNG
                            </h4>
                            @if($refund->status === 'completed' && $refund->processed_at)
                                <p class="text-xs text-gray-300 uppercase tracking-wide mb-2">
                                    {{ $refund->processed_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400 uppercase tracking-wide">
                                @if($refund->status === 'completed')
                                    TIỀN ĐÃ ĐƯỢC HOÀN VỀ TÀI KHOẢN CỦA BẠN
                                @else
                                    CHỜ HOÀN TIỀN
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom accent line -->
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-white via-gray-300 to-transparent opacity-30"></div>
            </div>
        </div>
    </div>
</div>
@endsection
