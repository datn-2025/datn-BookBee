@extends('layouts.backend')
@section('title', 'Thùng rác combo sách/tập')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Thùng rác Combo Sách / Sách Tập</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}" style="color: inherit;">Combo sách</a></li>
                    <li class="breadcrumb-item active">Thùng rác</li>
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
                    <h4 class="card-title mb-0">Danh Sách Combo Đã Xóa</h4>
                    <a href="{{ route('admin.collections.index') }}" class="btn btn-light btn-sm me-2">
                        <i class="ri-arrow-go-back-line me-1"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th class="text-center">Tên combo</th>
                                <th class="text-center">Ngày bắt đầu</th>
                                <th class="text-center">Ngày kết thúc</th>
                                <th class="text-center">Giá combo</th>
                                <th class="text-center" style="width: 150px;">Tùy chọn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collections as $key => $collection)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>
                                <td class="fw-semibold text-center">{{ $collection->name }}</td>
                                <td class="text-center">{{ $collection->start_date ? date('d/m/Y', strtotime($collection->start_date)) : '-' }}</td>
                                <td class="text-center">{{ $collection->end_date ? date('d/m/Y', strtotime($collection->end_date)) : '-' }}</td>
                                <td class="text-primary fw-bold text-center">{{ $collection->combo_price ? number_format($collection->combo_price, 0, ',', '.') . ' đ' : '-' }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.collections.restore', $collection->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Khôi phục combo này?')">
                                            <i class="ri-reply-line me-1"></i> 
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.collections.forceDelete', $collection->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa vĩnh viễn combo này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="ri-delete-bin-2-line me-1"></i> 
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có combo nào trong thùng rác.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($collections instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-end mt-3">
                    {!! $collections->links('layouts.pagination') !!}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
