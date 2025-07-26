@extends('layouts.backend')

@section('title', 'Cập nhật trạng thái đơn hàng')

@section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Cập nhật trạng thái đơn hàng</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                            <li class="breadcrumb-item active">Cập nhật trạng thái</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Cập nhật trạng thái đơn hàng #{{ substr($order->id,
                                0, 8) }}</h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="btn btn-soft-secondary btn-sm">
                                    <i class="ri-arrow-left-line align-middle"></i> Quay lại chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="order_status_id" class="form-label">Trạng thái đơn hàng</label>
                                        <select class="form-select @error('order_status_id') is-invalid @enderror"
                                            id="order_status_id" name="order_status_id">
                                            <option value=""><strong>{{ $order->orderStatus->name }}</strong></option>
                                            @foreach($orderStatuses as $status)
                                            <option value="{{ $status->id }}" {{ old('order_status_id', $order->
                                                order_status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('order_status_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="payment_status_id" class="form-label">Trạng thái thanh toán</label>
                                        <select class="form-select @error('payment_status_id') is-invalid @enderror"
                                            id="payment_status_id" name="payment_status_id">
                                            <option value="">Chọn trạng thái</option>
                                            @foreach($paymentStatuses as $status)
                                            <option value="{{ $status->id }}" {{ old('payment_status_id', $order->
                                                payment_status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('payment_status_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Trường lý do hủy hàng --}}
                            <div class="row mb-4" id="cancellation_reason_row" style="display: none;">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cancellation_reason" class="form-label">Lý do hủy hàng <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('cancellation_reason') is-invalid @enderror"
                                                  id="cancellation_reason" name="cancellation_reason" rows="3"
                                                  placeholder="Vui lòng nhập lý do hủy hàng...">{{ old('cancellation_reason', $order->cancellation_reason) }}</textarea>
                                        @error('cancellation_reason')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info" role="alert">
                                        <i class="ri-information-line me-1 align-middle fs-15"></i>
                                        Việc cập nhật trạng thái đơn hàng sẽ ảnh hưởng đến quá trình xử lý đơn hàng. Vui
                                        lòng kiểm tra kỹ trước khi cập nhật.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" style="background-color:#405189; border-color: #405189"   >
                                            <i class="ri-save-line align-bottom me-1"></i> Cập nhật trạng thái
                                        </button>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-danger">
                                            <i class="ri-close-line align-bottom me-1"></i> Hủy
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 200px;">Mã đơn hàng:</th>
                                            <td>{{ $order->order_code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Khách hàng:</th>
                                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tổng tiền:</th>
                                            <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                        </tr>
                                        <tr>
                                            <th>Ngày đặt hàng:</th>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 200px;">Trạng thái hiện tại:</th>
                                            <td>
                                                <span class="badge rounded-pill fs-12
                                                   @if($order->orderStatus->name == 'Đã giao thành công') bg-success
                                                    @elseif($order->orderStatus->name == 'Đang xử lý') bg-warning text-dark
                                                    @elseif($order->orderStatus->name == 'Đang giao hàng') bg-info
                                                    @elseif($order->orderStatus->name == 'Giao thất bại') bg-danger
                                                    @elseif($order->orderStatus->name == 'Chờ xác nhận') bg-secondary
                                                    @else bg-dark  @endif">
                                                    {{ $order->orderStatus->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Trạng thái thanh toán:</th>
                                            <td>
                                                <span class="badge rounded-pill fs-12
                                                    @if($order->paymentStatus->name == 'Đã Thanh Toán') bg-success
                                                    @elseif($order->paymentStatus->name == 'Chưa Thanh Toán') bg-warning text-dark
                                                    @elseif($order->paymentStatus->name == 'Thất Bại') bg-danger
                                                    @else bg-secondary  @endif">
                                                    {{ $order->paymentStatus->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td>Mã đơn hàng:</td>
                                        <td class="fw-medium">{{ substr($order->id, 0, 8) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Khách hàng:</td>
                                        <td class="fw-medium">{{ $order->user->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tổng tiền:</td>
                                        <td class="fw-medium">{{ number_format($order->total_amount, 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ngày đặt hàng:</td>
                                        <td class="fw-medium">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h5 class="fs-14 mb-3">Trạng thái hiện tại</h5>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 avatar-sm">
                                    <div class="avatar-title bg-light text-primary rounded-circle fs-3">
                                        <i class="ri-truck-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fs-15">Trạng thái đơn hàng:</h6>
                                    <p class="text-muted mb-0">
                                        <span class="badge
                                            @if($order->orderStatus->name == 'Đã giao hàng') bg-success
                                            @elseif($order->orderStatus->name == 'Đang xử lý') bg-warning
                                            @elseif($order->orderStatus->name == 'Đang giao hàng') bg-info
                                            @elseif($order->orderStatus->name == 'Đã hủy') bg-danger
                                            @else bg-secondary @endif">
                                            {{ $order->orderStatus->name ?? 'N/A' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-sm">
                                    <div class="avatar-title bg-light text-primary rounded-circle fs-3">
                                        <i class="ri-bank-card-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fs-15">Trạng thái thanh toán:</h6>
                                    <p class="text-muted mb-0">
                                        <span class="badge
                                            @if($order->paymentStatus->name == 'Đã Thanh Toán') bg-success
                                            @elseif($order->paymentStatus->name == 'Chưa thanh toán') bg-warning
                                            @elseif($order->paymentStatus->name == 'Đã hủy') bg-danger
                                            @else bg-secondary @endif">
                                            {{ $order->paymentStatus->name ?? 'N/A' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 text-center border-top">
                            @if($order->qr_code)
                            <h5 class="fs-14 mb-3">Mã QR đơn hàng</h5>
                            <img src="{{ asset('storage/' . $order->qr_code) }}" alt="QR Code" class="img-fluid rounded"
                                style="max-width: 150px">
                            @else
                            <div class="d-flex justify-content-center">
                                <div class="avatar-md">
                                    <div class="avatar-title bg-light text-secondary rounded-circle fs-2">
                                        <i class="ri-qr-code-line"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mt-2 mb-0">Chưa có mã QR</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderStatusSelect = document.getElementById('order_status_id');
    const cancellationReasonRow = document.getElementById('cancellation_reason_row');
    const cancellationReasonInput = document.getElementById('cancellation_reason');

    function toggleCancellationReason() {
        const selectedOption = orderStatusSelect.options[orderStatusSelect.selectedIndex];
        const selectedStatusName = selectedOption.text.trim();
        
        if (selectedStatusName === 'Đã hủy') {
            cancellationReasonRow.style.display = 'block';
            cancellationReasonInput.required = true;
        } else {
            cancellationReasonRow.style.display = 'none';
            cancellationReasonInput.required = false;
            cancellationReasonInput.value = '';
        }
    }

    // Kiểm tra trạng thái ban đầu
    toggleCancellationReason();

    // Lắng nghe sự kiện thay đổi trạng thái
    orderStatusSelect.addEventListener('change', toggleCancellationReason);
});
</script>
@endsection
