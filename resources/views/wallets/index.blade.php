@extends('layouts.account.layout')

@push('styles')
<style>
    .wallet-card {
        background: #1f2937;
        border-radius: 0.75rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid #374151;
    }
    .balance-display {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0.75rem;
    }
    .action-btn {
        transition: all 0.2s ease;
        border-radius: 0.5rem;
        border: 2px solid transparent;
        font-weight: 500;
        padding: 1rem 1.5rem;
        text-decoration: none;
    }
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .transaction-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .btn-deposit {
        background: #ffffff;
        color: #374151;
        border-color: #d1d5db;
    }
    .btn-deposit:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }
    .btn-withdraw {
        background: #374151;
        color: #ffffff;
        border-color: #374151;
    }
    .btn-withdraw:hover {
        background: #4b5563;
        border-color: #4b5563;
        color: #ffffff;
    }
</style>
@endpush

@section('account_content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4">
        {{-- Wallet Balance Card --}}
        <div class="wallet-card p-8 mb-8 text-white">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-2xl font-semibold mb-2">Ví của tôi</h1>
                    <p class="text-gray-300 text-base">Quản lý tài chính cá nhân</p>
                </div>
                <div class="balance-display px-6 py-4">
                    <p class="text-gray-300 text-sm font-medium mb-1">Số dư hiện tại</p>
                    <p class="text-2xl font-semibold text-white">₫{{ number_format($wallet->balance ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        {{-- Action Buttons --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <a href="{{ route('wallet.deposit.form') }}" class="action-btn btn-deposit text-center block">
                <div class="flex items-center justify-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Nạp tiền</span>
                </div>
            </a>
            <a href="{{ route('wallet.withdraw.form') }}" class="action-btn btn-withdraw text-center block">
                <div class="flex items-center justify-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4"/>
                    </svg>
                    <span>Rút tiền</span>
                </div>
            </a>
        </div>

        {{-- Transaction History --}}
        <div class="transaction-card bg-white">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Lịch sử giao dịch</h2>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Loại</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số tiền</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Phương thức</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ngày</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transactions as $i => $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transactions->firstItem() + $i }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($transaction->type === 'Nap')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Nạp tiền
                                            </span>
                                        @elseif($transaction->type === 'Rut')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-white">
                                                Rút tiền
                                            </span>
                                        @elseif($transaction->type === 'HoanTien')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Hoàn tiền
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium {{ in_array($transaction->type, ['Nap', 'HoanTien']) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ in_array($transaction->type, ['Nap', 'HoanTien']) ? '+' : '-' }}₫{{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        @if ($transaction->payment_method === 'bank_transfer')
                                            Chuyển khoản
                                        @else
                                            VNPay
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $transaction->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($transaction->status === 'pending' || $transaction->status === 'Chờ Xử Lý')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Chờ duyệt
                                            </span>
                                        @elseif($transaction->status === 'success' || $transaction->status === 'Thành Công')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Thành công
                                            </span>
                                        @elseif($transaction->status === 'rejected' || $transaction->status === 'Từ Chối')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Từ chối
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $transaction->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900 mb-1">Chưa có giao dịch nào</p>
                                            <p class="text-sm text-gray-500">Lịch sử giao dịch sẽ hiển thị tại đây</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
