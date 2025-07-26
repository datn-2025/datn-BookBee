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
            {{-- Form upload bill sau khi chuyển khoản --}}
            <div class="mt-6 p-4 bg-light border rounded">
                <h5 class="mb-3 text-center">Đã chuyển khoản? Upload bill để xác nhận</h5>
                <form action="{{ route('wallet.uploadBill') }}" method="POST" enctype="multipart/form-data" id="billUploadForm">
                    @csrf
                    <input type="hidden" name="transaction_id" value="{{ $pendingTransaction->id }}">
                    
                    <div class="mb-3">
                        <label for="bill_image" class="form-label">
                            Upload ảnh bill chuyển khoản <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               class="form-control" 
                               id="bill_image" 
                               name="bill_image" 
                               accept="image/*"
                               capture="environment"
                               required>
                        <small class="text-muted">Chỉ chấp nhận file ảnh (JPG, PNG, GIF). Tối đa 2MB.</small>
                    </div>
                    
                    {{-- Preview ảnh --}}
                    <div id="image_preview" class="mb-3" style="display: none;">
                        <img id="preview_img" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-upload-line me-1"></i>Xác nhận đã chuyển khoản
                        </button>
                        <a href="{{ route('wallet.deposit.form') }}" class="btn btn-outline-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-danger">Không tìm thấy thông tin ngân hàng. Vui lòng liên hệ quản trị viên.</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Xử lý preview ảnh
    $('#bill_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Kiểm tra kích thước file (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert('File quá lớn! Vui lòng chọn file nhỏ hơn 2MB.');
                $(this).val('');
                $('#image_preview').hide();
                return;
            }
            
            // Kiểm tra định dạng file
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Định dạng file không được hỗ trợ! Vui lòng chọn file JPG, PNG hoặc GIF.');
                $(this).val('');
                $('#image_preview').hide();
                return;
            }
            
            // Hiển thị preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_img').attr('src', e.target.result);
                $('#image_preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image_preview').hide();
        }
    });
    
    // Xử lý submit form
    $('#billUploadForm').on('submit', function(e) {
        const fileInput = $('#bill_image')[0];
        if (!fileInput.files || !fileInput.files[0]) {
            e.preventDefault();
            alert('Vui lòng chọn ảnh bill chuyển khoản!');
            return false;
        }
        
        // Hiển thị loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="ri-loader-line me-1"></i>Đang xử lý...');
        
        // Khôi phục nút sau 10 giây để tránh treo
        setTimeout(function() {
            submitBtn.prop('disabled', false).html(originalText);
        }, 10000);
    });
});
</script>
@endpush
