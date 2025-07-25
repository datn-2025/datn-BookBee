@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết Voucher</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px; color: #000;">Mã Voucher</th>
                                    <td style="color: #000;">{{ $voucher->code }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Mô tả</th>
                                    <td style="color: #000;">{{ $voucher->description }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Phần trăm giảm</th>
                                    <td style="color: #000;">{{ $voucher->discount_percent }}%</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Giảm tối đa</th>
                                    <td style="color: #000;">{{ number_format($voucher->max_discount) }} VNĐ</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Đơn tối thiểu</th>
                                    <td style="color: #000;">{{ number_format($voucher->min_order_value) }} VNĐ</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Số lượng phát hành</th>
                                    <td style="color: #000;">{{ $voucher->quantity }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Đã sử dụng</th>
                                    <td style="color: #000;">{{ $voucher->used_count ?? 0 }} lần</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Còn lại</th>
                                    <td style="color: #000;">{{ $voucher->remaining_quantity }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="color: #000;">Ngày bắt đầu</th>
                                    <td style="color: #000;">{{ $voucher->valid_from->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Ngày kết thúc</th>
                                    <td style="color: #000;">{{ $voucher->valid_to->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Trạng thái</th>
                                    <td style="color: #000;">
                                        @if($voucher->status === 'active')
                                            <span class="badge badge-success" style="color: #000 !important;">Hoạt động</span>
                                        @else
                                            <span class="badge badge-warning" style="color: #000 !important;">Không hoạt động</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Ngày tạo</th>
                                    <td style="color: #000;">{{ $voucher->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #000;">Cập nhật lần cuối</th>
                                    <td style="color: #000;">{{ $voucher->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Điều kiện áp dụng</h4>
                            <ul>
                                @foreach($voucher->conditions as $condition)
                                    <li>
                                        @switch($condition->type)
                                            @case('all')
                                                Tất cả sản phẩm
                                                @break
                                            @case('category')
                                                Danh mục: {{ $condition->categoryCondition->name ?? 'Không xác định' }}
                                                @break
                                            @case('author')
                                                Tác giả: {{ $condition->authorCondition->name ?? 'Không xác định' }}
                                                @break
                                            @case('brand')
                                                Thương hiệu: {{ $condition->brandCondition->name ?? 'Không xác định' }}
                                                @break
                                            @case('book')
                                                Sách: {{ $condition->bookCondition->title ?? 'Không xác định' }}
                                                @break
                                            @default
                                                Không xác định
                                        @endswitch
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if($voucher->appliedVouchers->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Lịch sử sử dụng</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Khách hàng</th>
                                            <th>Đơn hàng</th>
                                            <th>Ngày sử dụng</th>
                                            <th>Số lần dùng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($voucher->appliedVouchers as $applied)
                                        <tr>
                                            <td>{{ $applied->user->name ?? 'Ẩn danh' }}</td>
                                            <td>
                                                {{ $applied->order->order_code ?? '-' }}
                                            </td>
                                            <td>{{ $applied->used_at ? $applied->used_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td>{{ $applied->usage_count }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
