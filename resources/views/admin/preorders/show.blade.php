@extends('layouts.backend')

@section('title', 'Chi tiết đơn đặt trước')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Chi tiết đơn đặt trước #{{ $preorder->id }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.preorders.index') }}">Sách đặt trước</a></li>
                    <li class="breadcrumb-item active">Chi tiết #{{ $preorder->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.preorders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal">
                <i class="fas fa-edit"></i> Cập nhật trạng thái
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Order Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đơn đặt trước</h6>
                    <span class="badge {{ $preorder->status_color }} badge-lg">
                        {{ $preorder->status_text }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-gray-600" width="40%">ID đơn hàng:</td>
                                    <td>#{{ $preorder->id }}</td>
                                </tr>                                <tr>
                                    <td class="font-weight-bold text-gray-600">Ngày đặt:</td>
                                    <td>{{ $preorder->created_at ? $preorder->created_at->format('d/m/Y H:i:s') : 'Chưa có thông tin' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-600">Cập nhật lần cuối:</td>
                                    <td>{{ $preorder->updated_at ? $preorder->updated_at->format('d/m/Y H:i:s') : 'Chưa có thông tin' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-600">Trạng thái:</td>
                                    <td>
                                        <span class="badge {{ $preorder->status_color }}">
                                            {{ $preorder->status_text }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-gray-600" width="40%">Số lượng:</td>
                                    <td>{{ $preorder->quantity }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-600">Đơn giá:</td>
                                    <td>{{ $preorder->formatted_unit_price }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-600">Phí vận chuyển:</td>
                                    <td>{{ $preorder->formatted_shipping_cost }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-gray-600">Tổng tiền:</td>
                                    <td class="h5 text-success font-weight-bold">{{ $preorder->formatted_total_price }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($preorder->admin_notes)
                        <div class="mt-4">
                            <h6 class="font-weight-bold text-gray-600">Ghi chú của admin:</h6>
                            <div class="alert alert-info">
                                {{ $preorder->admin_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Book Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sách</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @if($preorder->book && $preorder->book->image)
                                <img src="{{ asset('storage/images/' . $preorder->book->image) }}" 
                                     alt="{{ $preorder->book_title }}" class="img-fluid rounded">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h5 class="font-weight-bold text-gray-800 mb-3">{{ $preorder->book_title }}</h5>
                            
                            <table class="table table-borderless">
                                @if($preorder->book_format_name)
                                    <tr>
                                        <td class="font-weight-bold text-gray-600" width="30%">Định dạng:</td>
                                        <td>{{ $preorder->book_format_name }}</td>
                                    </tr>
                                @endif
                                
                                @if($preorder->book)
                                    <tr>
                                        <td class="font-weight-bold text-gray-600">Tác giả:</td>
                                        <td>{{ $preorder->book->author->name ?? 'Không rõ' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-gray-600">Danh mục:</td>
                                        <td>{{ $preorder->book->category->name ?? 'Không rõ' }}</td>
                                    </tr>
                                    @if($preorder->book->publisher)
                                        <tr>
                                            <td class="font-weight-bold text-gray-600">Nhà xuất bản:</td>
                                            <td>{{ $preorder->book->publisher->name }}</td>
                                        </tr>
                                    @endif
                                @endif
                            </table>

                            @if($preorder->attributes_display && count($preorder->attributes_display) > 0)
                                <div class="mt-3">
                                    <h6 class="font-weight-bold text-gray-600 mb-2">Thuộc tính đã chọn:</h6>
                                    <div class="row">
                                        @foreach($preorder->attributes_display as $attribute => $value)
                                            <div class="col-md-6 mb-2">
                                                <span class="badge badge-light">
                                                    <strong>{{ $attribute }}:</strong> {{ $value }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="col-lg-4">
            <!-- Customer Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; font-size: 24px;">
                            {{ strtoupper(substr($preorder->customer_name, 0, 1)) }}
                        </div>
                        <h6 class="font-weight-bold text-gray-800 mt-2 mb-0">{{ $preorder->customer_name }}</h6>
                    </div>

                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="font-weight-bold text-gray-600" width="30%">
                                <i class="fas fa-envelope text-muted mr-1"></i> Email:
                            </td>
                            <td>
                                <a href="mailto:{{ $preorder->customer_email }}" class="text-decoration-none">
                                    {{ $preorder->customer_email }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">
                                <i class="fas fa-phone text-muted mr-1"></i> Điện thoại:
                            </td>
                            <td>
                                <a href="tel:{{ $preorder->customer_phone }}" class="text-decoration-none">
                                    {{ $preorder->customer_phone }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">
                                <i class="fas fa-map-marker-alt text-muted mr-1"></i> Địa chỉ:
                            </td>
                            <td>{{ $preorder->customer_address }}</td>
                        </tr>
                    </table>

                    <div class="mt-3 pt-3 border-top">
                        <a href="mailto:{{ $preorder->customer_email }}" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-envelope"></i> Gửi email
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($preorder->status === 'pending')
                            <button type="button" class="btn btn-success btn-sm" onclick="quickUpdateStatus('confirmed')">
                                <i class="fas fa-check"></i> Xác nhận đơn hàng
                            </button>
                        @elseif($preorder->status === 'confirmed')
                            <button type="button" class="btn btn-info btn-sm" onclick="quickUpdateStatus('preparing')">
                                <i class="fas fa-box"></i> Chuẩn bị hàng
                            </button>
                        @elseif($preorder->status === 'preparing')
                            <button type="button" class="btn btn-warning btn-sm" onclick="quickUpdateStatus('shipped')">
                                <i class="fas fa-shipping-fast"></i> Gửi hàng
                            </button>
                        @elseif($preorder->status === 'shipped')
                            <button type="button" class="btn btn-success btn-sm" onclick="quickUpdateStatus('delivered')">
                                <i class="fas fa-check-circle"></i> Đã giao hàng
                            </button>
                        @endif

                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
                            <i class="fas fa-edit"></i> Cập nhật trạng thái
                        </button>

                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Xóa đơn hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">            <form method="POST" action="{{ route('admin.preorders.updateStatus', $preorder) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái đơn #{{ $preorder->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" {{ $preorder->status === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ $preorder->status === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="preparing" {{ $preorder->status === 'preparing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                            <option value="shipped" {{ $preorder->status === 'shipped' ? 'selected' : '' }}>Đã gửi hàng</option>
                            <option value="delivered" {{ $preorder->status === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                            <option value="cancelled" {{ $preorder->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                placeholder="Ghi chú của admin...">{{ $preorder->admin_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
    
    .badge-lg {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .table-borderless td {
        border: none;
        padding: 0.5rem 0;
    }
</style>
@endpush

@push('scripts')
<script>
    function quickUpdateStatus(status) {
        if (confirm('Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng?')) {
            const form = document.createElement('form');
            form.method = 'POST';            form.action = '{{ route("admin.preorders.updateStatus", $preorder) }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function confirmDelete() {
        if (confirm('Bạn có chắc chắn muốn xóa đơn đặt trước này? Hành động này không thể hoàn tác!')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.preorders.destroy", $preorder) }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
