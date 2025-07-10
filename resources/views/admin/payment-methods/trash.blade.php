@extends('layouts.backend')

@section('title', 'Thùng rác - Phương thức thanh toán')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Thùng rác - Phương thức thanh toán</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.payment-methods.index') }}">Phương thức thanh toán</a></li>
                            <li class="breadcrumb-item active">Thùng rác</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h4 class="card-title mb-0">Danh sách đã xóa</h4>
                        <div>
                            <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-primary btn-sm">
                                <i class="ri-arrow-left-line me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên phương thức</th>
                                        <th>Ngày xóa</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($paymentMethods as $key => $method)
                                        <tr>
                                            <td>{{ $paymentMethods->firstItem() + $key }}</td>
                                            <td>{{ $method->name }}</td>
                                            <td>{{ $method->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <form action="{{ route('admin.payment-methods.restore', $method->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success" title="Khôi phục">
                                                            <i class="ri-refresh-line"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.payment-methods.force-delete', $method->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn phương thức thanh toán này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa vĩnh viễn">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Thùng rác trống</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $paymentMethods->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection