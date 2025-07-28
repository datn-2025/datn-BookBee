@extends('layouts.account.layout')
@section('title', 'Nạp Ví')
@push('styles')
<style>
    .deposit-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
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
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">Nạp tiền vào ví</h1>
            <p class="text-gray-600">Thêm tiền vào tài khoản của bạn</p>
        </div>

        {{-- Deposit Form --}}
        <div class="deposit-card p-6">
            <form action="{{ route('wallet.deposit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="amount" class="form-label">
                        Số tiền nạp <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">₫</span>
                        <input type="number" 
                               min="1000" 
                               class="form-control" 
                               id="amount" 
                               name="amount" 
                               required 
                               placeholder="Nhập số tiền tối thiểu 1,000 VNĐ"
                               value="{{ old('amount') }}">
                    </div>
                    <small class="text-gray-500 mt-1">Số tiền tối thiểu: 1,000 VNĐ</small>
                    @error('amount')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_method" class="form-label">
                        Phương thức thanh toán <span class="text-red-500">*</span>
                    </label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="">-- Chọn phương thức thanh toán --</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                            Chuyển khoản ngân hàng
                        </option>
                        <option value="vnpay" {{ old('payment_method') == 'vnpay' ? 'selected' : '' }}>
                            VNPay
                        </option>
                    </select>
                    @error('payment_method')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>



                <div class="space-y-4">
                    <button type="submit" class="submit-btn">
                        Tiến hành nạp tiền
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
            <h3 class="text-sm font-medium text-gray-900 mb-2">Lưu ý</h3>
            <ul class="space-y-1 text-gray-700 text-sm">
                <li>• Số tiền nạp tối thiểu là 1,000 VNĐ</li>
                <li>• Giao dịch sẽ được xử lý trong vòng 24 giờ</li>
                <li>• Vui lòng kiểm tra kỹ thông tin trước khi xác nhận</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        

    });
</script>
@endpush
