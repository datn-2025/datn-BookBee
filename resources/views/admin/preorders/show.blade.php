@extends('layouts.backend')

@section('title', 'Chi tiết đặt trước')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chi Tiết Đặt Trước</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: inherit;">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.preorders.index') }}" style="color: inherit;">Đặt trước</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Thông tin đơn đặt trước -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Thông Tin Đơn Đặt Trước</h5>
                    <div>
                        <span class="badge bg-{{ $preorder->status_color }}-subtle text-{{ $preorder->status_color }} px-3 py-2">
                            {{ $preorder->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="150">Mã đơn:</th>
                                <td><code>{{ $preorder->id }}</code></td>
                            </tr>
                            <tr>
                                <th>Ngày đặt:</th>
                                <td>{{ $preorder->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Số lượng:</th>
                                <td><span class="badge bg-primary">{{ $preorder->quantity }} cuốn</span></td>
                            </tr>
                            <tr>
                                <th>Trạng thái sách:</th>
                                <td>
                                    @if($preorder->book)
                                        @php
                                            $statusText = $preorder->book->status ?? 'Không rõ';
                                            $statusClass = 'bg-secondary';
                                            
                                            switch($statusText) {
                                                case 'Còn hàng':
                                                    $statusClass = 'bg-success';
                                                    break;
                                                case 'Hết hàng tồn kho':
                                                    $statusClass = 'bg-danger';
                                                    break;
                                                case 'Sắp ra mắt':
                                                    $statusClass = 'bg-warning text-dark';
                                                    break;
                                                case 'Ngừng kinh doanh':
                                                    $statusClass = 'bg-dark';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                    @else
                                        <span class="text-muted">Không có thông tin sách</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Đơn giá:</th>
                                <td>{{ number_format($preorder->unit_price) }}₫</td>
                            </tr>
                            <tr>
                                <th>Tổng tiền:</th>
                                <td><strong class="text-success">{{ number_format($preorder->total_amount) }}₫</strong></td>
                            </tr>
                            <tr>
                                <th>Thanh toán:</th>
                                <td>
                                    @if($preorder->paymentMethod)
                                        <div class="d-flex align-items-center">
                                            @if(stripos($preorder->paymentMethod->name, 'chuyển khoản') !== false || stripos($preorder->paymentMethod->name, 'ngân hàng') !== false)
                                                <i class="ri-bank-card-line text-success me-2"></i>
                                            @elseif(stripos($preorder->paymentMethod->name, 'vnpay') !== false)
                                                <i class="ri-smartphone-line text-primary me-2"></i>
                                            @elseif(stripos($preorder->paymentMethod->name, 'ví điện tử') !== false)
                                                <i class="ri-wallet-3-line text-warning me-2"></i>
                                            @elseif(stripos($preorder->paymentMethod->name, 'khi nhận hàng') !== false || stripos($preorder->paymentMethod->name, 'cod') !== false || stripos($preorder->paymentMethod->name, 'tiền mặt') !== false)
                                                <i class="ri-hand-coin-line text-success me-2"></i>
                                            @else
                                                <i class="ri-money-dollar-circle-line text-info me-2"></i>
                                            @endif
                                            @if(stripos($preorder->paymentMethod->name, 'khi nhận hàng') !== false || stripos($preorder->paymentMethod->name, 'cod') !== false || stripos($preorder->paymentMethod->name, 'tiền mặt') !== false)
                                                <span class="badge bg-success-subtle text-success">{{ $preorder->paymentMethod->name }}</span>
                                            @else
                                                <span class="badge bg-info-subtle text-info">{{ $preorder->paymentMethod->name }}</span>
                                            @endif
                                        </div>
                                        @if($preorder->paymentMethod->description)
                                            <small class="text-muted d-block mt-1">{{ $preorder->paymentMethod->description }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Chưa xác định</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái TT:</th>
                                <td>
                                    @if($preorder->payment_status)
                                        @if($preorder->payment_status == 'Đã Thanh Toán')
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="ri-check-line me-1"></i>Đã thanh toán
                                            </span>
                                            @if($preorder->confirmed_at)
                                                <br><small class="text-muted">{{ $preorder->confirmed_at->format('d/m/Y H:i') }}</small>
                                            @endif
                                        @elseif($preorder->payment_status == 'Thất Bại')
                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="ri-close-line me-1"></i>Thất bại
                                            </span>
                                            @if($preorder->cancellation_reason)
                                                <br><small class="text-muted">{{ $preorder->cancellation_reason }}</small>
                                            @endif
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
                            @if($preorder->vnpay_transaction_id)
                            <tr>
                                <th>Mã GD VNPay:</th>
                                <td>
                                    <code class="text-primary">{{ $preorder->vnpay_transaction_id }}</code>
                                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $preorder->vnpay_transaction_id }}')"
                                            title="Sao chép mã giao dịch">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                            @if($preorder->preorder_code)
                            <tr>
                                <th>Mã đặt trước:</th>
                                <td>
                                    <code class="text-info">{{ $preorder->preorder_code }}</code>
                                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $preorder->preorder_code }}')"
                                            title="Sao chép mã đặt trước">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless mb-0">
                            @if($preorder->expected_delivery_date)
                            <tr>
                                <th width="150">Dự kiến giao:</th>
                                <td>{{ \Carbon\Carbon::parse($preorder->expected_delivery_date)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($preorder->confirmed_at)
                            <tr>
                                <th>Xác nhận lúc:</th>
                                <td>{{ $preorder->confirmed_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($preorder->shipped_at)
                            <tr>
                                <th>Gửi hàng lúc:</th>
                                <td>{{ $preorder->shipped_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($preorder->delivered_at)
                            <tr>
                                <th>Giao hàng lúc:</th>
                                <td>{{ $preorder->delivered_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($preorder->notes)
                <div class="mt-4">
                    <h6>Ghi chú:</h6>
                    <div class="bg-light p-3 rounded">{{ $preorder->notes }}</div>
                </div>
                @endif

                @if($preorder->selected_attributes)
                <div class="mt-4">
                    <h6>Thuộc tính đã chọn:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @if($preorder->formatted_attributes && count($preorder->formatted_attributes) > 0)
                            @foreach($preorder->formatted_attributes as $attr)
                                <span class="badge bg-info-subtle text-info">{{ $attr['display'] }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">{{ $preorder->selected_attributes_display }}</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Thông tin sách -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông Tin Sách</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        @if($preorder->book->cover_image)
                            <img src="{{ asset('storage/' . $preorder->book->cover_image) }}" 
                                 alt="{{ $preorder->book->title }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 200px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="ri-image-line text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h5>{{ $preorder->book->title }}</h5>
                        <p class="text-muted mb-2">{{ $preorder->book->description }}</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="100">ISBN:</th>
                                        <td>{{ $preorder->book->isbn ?? 'N/A' }}</td>
                                    </tr>
                                    @if($preorder->bookFormat)
                                    <tr>
                                        <th>Định dạng:</th>
                                        <td>{{ $preorder->bookFormat->name }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Giá gốc:</th>
                                        <td>{{ number_format($preorder->book->price) }}₫</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    @if($preorder->book->category)
                                    <tr>
                                        <th width="100">Danh mục:</th>
                                        <td>{{ $preorder->book->category->name }}</td>
                                    </tr>
                                    @endif
                                    @if($preorder->book->publication_date)
                                    <tr>
                                        <th>Ngày xuất bản:</th>
                                        <td>{{ \Carbon\Carbon::parse($preorder->book->publication_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Thông tin khách hàng -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông Tin Khách Hàng</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if($preorder->user)
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                <i class="ri-user-line" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h6 class="mb-1">{{ $preorder->customer_name }}</h6>
                        <span class="badge bg-success">Thành viên</span>
                    @else
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle">
                                <i class="ri-user-line" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h6 class="mb-1">{{ $preorder->customer_name }}</h6>
                        <span class="badge bg-secondary">Khách vãng lai</span>
                    @endif
                </div>

                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="80"><i class="ri-mail-line me-2"></i>Email:</th>
                        <td><a href="mailto:{{ $preorder->email }}">{{ $preorder->email }}</a></td>
                    </tr>
                    <tr>
                        <th><i class="ri-phone-line me-2"></i>SĐT:</th>
                        <td><a href="tel:{{ $preorder->phone }}">{{ $preorder->phone }}</a></td>
                    </tr>
                </table>

                <h6 class="mt-4 mb-2"><i class="ri-map-pin-line me-2"></i>Địa chỉ giao hàng:</h6>
                <div class="bg-light p-3 rounded">
                    <div>{{ $preorder->address }}</div>
                    <div>{{ $preorder->ward_name }}, {{ $preorder->district_name }}</div>
                    <div>{{ $preorder->province_name }}</div>
                </div>
            </div>
        </div>

        <!-- Hành động -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Hành Động</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.preorders.edit', $preorder->id) }}" class="btn btn-primary">
                        <i class="ri-pencil-line me-1"></i> Chỉnh sửa
                    </a>
                    
                    @if($preorder->status === 'pending')
                    <button type="button" class="btn btn-success" onclick="confirmAndCreateOrder()">
                        <i class="ri-check-line me-1"></i> Xác nhận đơn & Tạo Order
                    </button>
                    @endif
                    
                    @if($preorder->status === 'confirmed')
                    <button type="button" class="btn btn-info" onclick="updateStatus('processing')">
                        <i class="ri-settings-3-line me-1"></i> Đang xử lý
                    </button>
                    @endif
                    
                    @if($preorder->status === 'processing')
                    <button type="button" class="btn btn-warning" onclick="updateStatus('shipped')">
                        <i class="ri-truck-line me-1"></i> Đã gửi hàng
                    </button>
                    @endif
                    
                    @if($preorder->status === 'shipped')
                    <button type="button" class="btn btn-success" onclick="updateStatus('delivered')">
                        <i class="ri-check-double-line me-1"></i> Đã giao hàng
                    </button>
                    @endif

                    @if(in_array($preorder->status, ['pending', 'confirmed']))
                    <button type="button" class="btn btn-outline-danger" onclick="updateStatus('cancelled')">
                        <i class="ri-close-line me-1"></i> Hủy đơn
                    </button>
                    @endif

                    <a href="{{ route('admin.preorders.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmAndCreateOrder() {
        if (confirm('Bạn có chắc chắn muốn xác nhận đơn đặt trước này và tạo đơn hàng? Hành động này không thể hoàn tác.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.preorders.confirm', $preorder->id) }}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function updateStatus(status) {
        const statusMessages = {
            'confirmed': 'xác nhận',
            'processing': 'chuyển sang đang xử lý',
            'shipped': 'đánh dấu đã gửi hàng',
            'delivered': 'đánh dấu đã giao hàng',
            'cancelled': 'hủy'
        };

        if (confirm(`Bạn có chắc chắn muốn ${statusMessages[status]} đơn đặt trước này?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.preorders.update', $preorder->id) }}`;
            
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
