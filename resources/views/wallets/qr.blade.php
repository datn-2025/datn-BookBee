@extends('layouts.account.layout')
@section('account_content')
<div class="bg-white border border-black shadow mb-8" style="border-radius: 0;">
    <div class="px-8 py-6 border-b border-black bg-black flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white uppercase tracking-wide mb-0">Chuyển khoản qua ngân hàng</h1>
    </div>
    <div class="p-8">
        @if($bankInfo)
            <div class="mb-6 text-center">
                <div class="font-semibold mb-2">Quét mã QR để chuyển khoản nhanh</div>
                <img src="{{ $qrUrl }}" alt="QR chuyển khoản" class="mx-auto rounded shadow" style="max-width: 320px;">
                <div class="mt-4">
                    <div class="mb-1"><b>Ngân hàng:</b> {{ $bankInfo['bank_name'] }}</div>
                    <div class="mb-1"><b>Số tài khoản:</b> <span class="text-primary fw-bold">{{ $bankInfo['bank_number'] }}</span></div>
                    <div class="mb-1"><b>Tên chủ tài khoản:</b> {{ $bankInfo['customer_name'] }}</div>
                    <div class="mb-1"><b>Số tiền:</b> <span class="text-success fw-bold">₫{{ number_format($amount, 0, ',', '.') }}</span></div>
                    <div class="mb-1"><b>Nội dung chuyển khoản:</b> <span class="text-danger fw-bold">{{ $description ?? 'Nap tien vi' }}</span></div>
                </div>
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('wallet.deposit.form') }}" class="btn btn-outline-dark">Quay lại</a>
            </div>
        @else
            <div class="alert alert-danger">Không tìm thấy thông tin ngân hàng. Vui lòng liên hệ quản trị viên.</div>
        @endif
    </div>
</div>
@endsection
