@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Trạng thái hoàn tiền</h1>
            <a href="{{ route('account.orders.show', $refund->order->id) }}" 
               class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại chi tiết đơn hàng
            </a>
        </div>

        <!-- Refund Status Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center mb-4">
                @if($refund->status === 'pending')
                    <div class="w-3 h-3 bg-yellow-400 rounded-full mr-3"></div>
                    <h2 class="text-lg font-semibold text-yellow-700">Đang chờ xử lý</h2>
                @elseif($refund->status === 'processing')
                    <div class="w-3 h-3 bg-blue-400 rounded-full mr-3 animate-pulse"></div>
                    <h2 class="text-lg font-semibold text-blue-700">Đang xử lý</h2>
                @elseif($refund->status === 'completed')
                    <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                    <h2 class="text-lg font-semibold text-green-700">Hoàn thành</h2>
                @else
                    <div class="w-3 h-3 bg-red-400 rounded-full mr-3"></div>
                    <h2 class="text-lg font-semibold text-red-700">Đã từ chối</h2>
                @endif
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Mã đơn hàng</p>
                    <p class="font-medium">{{ $refund->order->code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Số tiền hoàn</p>
                    <p class="font-medium text-lg text-red-600">{{ number_format($refund->amount, 0, ',', '.') }}đ</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Phương thức hoàn tiền</p>
                    <p class="font-medium">
                        @if($refund->refund_method === 'wallet')
                            Hoàn vào ví
                        @else
                            Hoàn qua VNPay
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Ngày yêu cầu</p>
                    <p class="font-medium">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
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
                <div>
                    <p class="text-sm font-medium text-gray-700">Ngày xử lý:</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $refund->processed_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tiến trình xử lý</h3>
            
            <div class="relative">
                <!-- Timeline line -->
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                <!-- Created -->
                <div class="relative flex items-center mb-6">
                    <div class="absolute left-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                    <div class="ml-12">
                        <h4 class="font-medium text-gray-900">Yêu cầu đã được gửi</h4>
                        <p class="text-sm text-gray-500">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                <!-- Processing -->
                <div class="relative flex items-center mb-6">
                    <div class="absolute left-0 w-8 h-8 {{ in_array($refund->status, ['processing', 'completed']) ? 'bg-blue-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                        @if(in_array($refund->status, ['processing', 'completed']))
                            <i class="fas fa-cog text-blue-600 text-sm"></i>
                        @else
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        @endif
                    </div>
                    <div class="ml-12">
                        <h4 class="font-medium {{ in_array($refund->status, ['processing', 'completed']) ? 'text-gray-900' : 'text-gray-400' }}">
                            Đang xử lý yêu cầu
                        </h4>
                        @if($refund->status === 'processing' || $refund->status === 'completed')
                            <p class="text-sm text-gray-500">Yêu cầu đã được duyệt và đang xử lý</p>
                        @else
                            <p class="text-sm text-gray-400">Chờ admin xem xét</p>
                        @endif
                    </div>
                </div>
                
                <!-- Completed -->
                <div class="relative flex items-center">
                    <div class="absolute left-0 w-8 h-8 {{ $refund->status === 'completed' ? 'bg-green-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                        @if($refund->status === 'completed')
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        @else
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        @endif
                    </div>
                    <div class="ml-12">
                        <h4 class="font-medium {{ $refund->status === 'completed' ? 'text-gray-900' : 'text-gray-400' }}">
                            Hoàn tiền thành công
                        </h4>
                        @if($refund->status === 'completed')
                            <p class="text-sm text-gray-500">
                                Tiền đã được hoàn về 
                                {{ $refund->refund_method === 'wallet' ? 'ví của bạn' : 'tài khoản VNPay' }}
                            </p>
                        @else
                            <p class="text-sm text-gray-400">Tiền sẽ được hoàn sau khi xử lý xong</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
