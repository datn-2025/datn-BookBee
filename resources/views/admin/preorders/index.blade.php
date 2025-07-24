@extends('layouts.backend')

@section('title', 'Quản lý đặt trước sách')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Quản Lý Đặt Trước Sách</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: inherit;">Dashboard</a></li>
                    <li class="breadcrumb-item active">Đặt trước</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh Sách Đặt Trước</h4>
                    <div>
                        <span class="badge bg-info">{{ $preorders->total() }} tổng cộng</span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Bộ lọc -->
                <form action="{{ route('admin.preorders.index') }}" method="GET" class="mb-4 border-bottom pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-lg-3">
                            <div class="search-box">
                                <input type="text" name="search" class="form-control search"
                                    placeholder="Tìm theo tên, email, SĐT..." value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã gửi hàng</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Từ ngày">
                        </div>
                        <div class="col-lg-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Đến ngày">
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ri-search-line me-1"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('admin.preorders.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-refresh-line me-1"></i> Đặt lại
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Bảng dữ liệu -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col">Khách hàng</th>
                                <th scope="col">Sách đặt trước</th>
                                <th scope="col">Trạng thái sách</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Phương thức thanh toán</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày đặt</th>
                                <th scope="col" style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($preorders as $preorder)
                            <tr>
                                <td>{{ $loop->iteration + ($preorders->currentPage() - 1) * $preorders->perPage() }}</td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ $preorder->customer_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-mail-line me-1"></i>{{ $preorder->email }}<br>
                                            <i class="ri-phone-line me-1"></i>{{ $preorder->phone }}
                                        </p>
                                        @if($preorder->user)
                                            <span class="badge bg-success-subtle text-success mt-1">Thành viên</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary mt-1">Khách vãng lai</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($preorder->book->cover_image)
                                            <img src="{{ asset('storage/' . $preorder->book->cover_image) }}" 
                                                 alt="{{ $preorder->book->title }}" class="rounded me-2" 
                                                 style="width: 40px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <h6 class="mb-1">{{ $preorder->book->title }}</h6>
                                            @if($preorder->bookFormat)
                                                <small class="text-muted">{{ $preorder->bookFormat->format_name }}</small>
                                            @endif
                                            @if($preorder->formatted_attributes && count($preorder->formatted_attributes) > 0)
                                                <div class="mt-1">
                                                    @foreach($preorder->formatted_attributes as $attr)
                                                        <span class="badge bg-light text-dark border me-1 small">{{ $attr['display'] }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
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
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary">{{ $preorder->quantity }}</span>
                                </td>
                                <td>
                                    <strong>{{ number_format($preorder->total_amount) }}₫</strong>
                                    <br>
                                    <small class="text-muted">{{ number_format($preorder->unit_price) }}₫/cuốn</small>
                                </td>
                                <td>
                                    @if($preorder->paymentMethod)
                                        <div class="d-flex align-items-center">
                                            @if(stripos($preorder->paymentMethod->name, 'chuyển khoản') !== false || stripos($preorder->paymentMethod->name, 'ngân hàng') !== false)
                                                <i class="ri-bank-card-line text-success me-2"></i>
                                            @elseif(stripos($preorder->paymentMethod->name, 'vnpay') !== false)
                                                <i class="ri-smartphone-line text-primary me-2"></i>
                                            @elseif(stripos($preorder->paymentMethod->name, 'ví điện tử') !== false)
                                                <i class="ri-wallet-3-line text-warning me-2"></i>
                                            @else
                                                <i class="ri-money-dollar-circle-line text-info me-2"></i>
                                            @endif
                                            <span>{{ $preorder->paymentMethod->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Chưa chọn</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $preorder->status_color }}-subtle text-{{ $preorder->status_color }}">
                                        {{ $preorder->status_label }}
                                    </span>
                                </td>
                                <td>
                                    {{ $preorder->created_at->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ $preorder->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" role="button" id="dropdownMenuLink{{ $preorder->id }}"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink{{ $preorder->id }}">
                                            <li><a href="{{ route('admin.preorders.show', $preorder->id) }}"
                                                    class="dropdown-item">
                                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Xem chi tiết
                                                </a></li>
                                            <li><a href="{{ route('admin.preorders.edit', $preorder->id) }}"
                                                    class="dropdown-item">
                                                    <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Chỉnh sửa
                                                </a></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger"
                                                        onclick="deletePreorder('{{ $preorder->id }}')">
                                                    <i class="ri-delete-bin-fill align-bottom me-2"></i> Xóa
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ri-inbox-line display-4"></i>
                                        <p class="mt-2">Không có đơn đặt trước nào.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $preorders->appends(request()->query())->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function deletePreorder(preorderId) {
        if (confirm('Bạn có chắc chắn muốn xóa đơn đặt trước này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.preorders.index') }}/${preorderId}`;
            
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
