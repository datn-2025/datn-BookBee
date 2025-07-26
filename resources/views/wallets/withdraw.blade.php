@extends('layouts.account.layout')

@section('title', 'Rút Ví')

@push('styles')
<style>
    .withdraw-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }
    .balance-display {
        background: #1f2937;
        border-radius: 0.75rem;
        color: white;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-control, .form-select {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        transition: border-color 0.2s ease;
        width: 100%;
    }
    .form-control:focus, .form-select:focus {
        border-color: #374151;
        box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
        outline: none;
    }
    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-group-text {
        position: absolute;
        left: 1rem;
        z-index: 10;
        background: none;
        border: none;
        color: #6b7280;
        font-weight: 500;
    }
    .input-group .form-control {
        padding-left: 2.5rem;
    }
    .bank-info-section {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
    }
    .submit-btn {
        background: #374151;
        border: none;
        color: white;
        padding: 0.875rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
        width: 100%;
    }
    .submit-btn:hover {
        background: #4b5563;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('account_content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-lg mx-auto px-4">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">Rút tiền từ ví</h1>
            <p class="text-gray-600">Chuyển tiền về tài khoản ngân hàng</p>
        </div>

        {{-- Balance Display --}}
        <div class="balance-display">
            <p class="text-gray-300 text-sm font-medium mb-1">Số dư hiện tại</p>
            <p class="text-xl font-semibold">₫{{ number_format($wallet->balance ?? 0, 0, ',', '.') }}</p>
        </div>

        {{-- Withdraw Form --}}
        <div class="withdraw-card p-6">
            <form action="{{ route('wallet.withdraw') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="amount" class="form-label">
                        Số tiền rút <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">₫</span>
                        <input type="number" 
                               min="1000" 
                               max="{{ $wallet->balance ?? 0 }}"
                               class="form-control" 
                               id="amount" 
                               name="amount" 
                               required 
                               placeholder="Nhập số tiền muốn rút"
                               value="{{ old('amount') }}">
                    </div>
                    <small class="text-gray-500 mt-1">Số tiền tối thiểu: 1,000 VNĐ</small>
                    @error('amount')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Thông tin tài khoản ngân hàng <span class="text-red-500">*</span>
                    </label>
                    <div class="bank-info-section">
                        <div class="grid grid-cols-1 gap-3 mb-3">
                            <div>
                                <input type="text" 
                                       class="form-control" 
                                       name="customer_name" 
                                       placeholder="Tên chủ tài khoản" 
                                       value="{{ old('customer_name', $userBankList['customer_name'] ?? '') }}" 
                                       required>
                                @error('customer_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <input type="text" 
                                       class="form-control" 
                                       name="bank_number" 
                                       placeholder="Số tài khoản" 
                                       value="{{ old('bank_number', $userBankList['bank_number'] ?? '') }}" 
                                       required>
                                @error('bank_number')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <input type="text" 
                                       class="form-control" 
                                       name="bank_name" 
                                       placeholder="Tên ngân hàng" 
                                       value="{{ old('bank_name', $userBankList['bank_name'] ?? '') }}" 
                                       required>
                                @error('bank_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <p class="text-xs text-gray-600">
                            Vui lòng kiểm tra kỹ thông tin tài khoản ngân hàng trước khi xác nhận.
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Ghi chú (không bắt buộc)</label>
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              rows="3" 
                              placeholder="Thêm ghi chú nếu cần...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-4">
                    <button type="submit" class="submit-btn">
                        Xác nhận rút tiền
                    </button>
                    <a href="{{ route('wallet.index') }}" 
                       class="block text-center text-gray-600 hover:text-gray-800 font-medium transition-colors">
                        ← Quay lại trang ví
                    </a>
                </div>
            </form>
        </div>

        {{-- Info Section --}}
        <div class="mt-6 bg-gray-100 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Lưu ý quan trọng</h3>
            <ul class="space-y-1 text-gray-700 text-sm">
                <li>• Số tiền rút tối thiểu là 1,000 VNĐ</li>
                <li>• Giao dịch rút tiền sẽ được xử lý trong 1-3 ngày làm việc</li>
                <li>• Không thể hủy giao dịch sau khi đã xác nhận</li>
            </ul>
        </div>
    </div>
</div>
@endsection
