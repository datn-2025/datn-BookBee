@extends('layouts.backend')

@section('title', 'Chỉnh sửa đặt trước')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chỉnh Sửa Đặt Trước</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: inherit;">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.preorders.index') }}" style="color: inherit;">Đặt trước</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Cập Nhật Đơn Đặt Trước</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.preorders.update', $preorder->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="pending" {{ $preorder->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                    <option value="confirmed" {{ $preorder->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="processing" {{ $preorder->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="shipped" {{ $preorder->status == 'shipped' ? 'selected' : '' }}>Đã gửi hàng</option>
                                    <option value="delivered" {{ $preorder->status == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                    <option value="cancelled" {{ $preorder->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expected_delivery_date" class="form-label">Ngày giao hàng dự kiến</label>
                                <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" 
                                       id="expected_delivery_date" name="expected_delivery_date" 
                                       value="{{ old('expected_delivery_date', $preorder->expected_delivery_date ? $preorder->expected_delivery_date->format('Y-m-d') : '') }}">
                                @error('expected_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" 
                                  placeholder="Nhập ghi chú về đơn đặt trước...">{{ old('notes', $preorder->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.preorders.show', $preorder->id) }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Thông tin hiện tại -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông Tin Hiện Tại</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="120">Mã đơn:</th>
                        <td><code>{{ $preorder->id }}</code></td>
                    </tr>
                    <tr>
                        <th>Khách hàng:</th>
                        <td>{{ $preorder->customer_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $preorder->email }}</td>
                    </tr>
                    <tr>
                        <th>SĐT:</th>
                        <td>{{ $preorder->phone }}</td>
                    </tr>
                    <tr>
                        <th>Sách:</th>
                        <td>{{ $preorder->book->title }}</td>
                    </tr>
                    <tr>
                        <th>Số lượng:</th>
                        <td>{{ $preorder->quantity }} cuốn</td>
                    </tr>
                    <tr>
                        <th>Tổng tiền:</th>
                        <td><strong>{{ number_format($preorder->total_amount) }}₫</strong></td>
                    </tr>
                    <tr>
                        <th>Thanh toán:</th>
                        <td>
                            @if($preorder->payment_status)
                                @if($preorder->payment_status == 'Đã Thanh Toán')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-check-line me-1"></i>Đã thanh toán
                                    </span>
                                @elseif($preorder->payment_status == 'Thất Bại')
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="ri-close-line me-1"></i>Thất bại
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="ri-time-line me-1"></i>{{ $preorder->payment_status }}
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <i class="ri-question-line me-1"></i>Chưa thanh toán
                                </span>
                            @endif
                        </td>
                    </tr>
                    @if($preorder->preorder_code)
                    <tr>
                        <th>Mã đặt trước:</th>
                        <td>
                            <code>{{ $preorder->preorder_code }}</code>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                    onclick="copyToClipboard('{{ $preorder->preorder_code }}')"
                                    title="Sao chép mã đặt trước">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </td>
                    </tr>
                    @endif
                    @if($preorder->vnpay_transaction_id)
                    <tr>
                        <th>Mã GD VNPay:</th>
                        <td>
                            <code>{{ $preorder->vnpay_transaction_id }}</code>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                    onclick="copyToClipboard('{{ $preorder->vnpay_transaction_id }}')"
                                    title="Sao chép mã giao dịch VNPay">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th>Ngày đặt:</th>
                        <td>{{ $preorder->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Lịch sử trạng thái -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lịch Sử Trạng Thái</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Đơn được tạo</h6>
                            <p class="timeline-text text-muted mb-0">{{ $preorder->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($preorder->confirmed_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Đã xác nhận</h6>
                            <p class="timeline-text text-muted mb-0">{{ $preorder->confirmed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($preorder->shipped_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Đã gửi hàng</h6>
                            <p class="timeline-text text-muted mb-0">{{ $preorder->shipped_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($preorder->delivered_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Đã giao hàng</h6>
                            <p class="timeline-text text-muted mb-0">{{ $preorder->delivered_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 15px;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 2px;
}

.timeline-text {
    font-size: 12px;
}
</style>
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success('Đã sao chép: ' + text, 'Thành công!');
            } else {
                alert('Đã sao chép: ' + text);
            }
        }, function(err) {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            if (typeof toastr !== 'undefined') {
                toastr.success('Đã sao chép: ' + text, 'Thành công!');
            } else {
                alert('Đã sao chép: ' + text);
            }
        });
    }
</script>
@endpush
