@extends('layouts.backend')
@section('title', 'Danh sách combo sách/tập')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Danh Sách Combo Sách / Sách Tập</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}" style="color: inherit;">Combo sách</a></li>
                    <li class="breadcrumb-item active">Danh sách</li>
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
                    <h4 class="card-title mb-0">Danh Sách Combo</h4>
                    <div class="button-group">
                        <a href="{{ route('admin.collections.create') }}" class="btn btn-primary btn-sm me-2" style="background-color:#405189; border-color: #405189">
                            <i class="ri-add-line me-1"></i> Thêm combo mới
                        </a>
                        <a href="{{ route('admin.collections.trash') }}" class="btn btn-outline-danger btn-sm me-2">
                            <i class="ri-delete-bin-2-line me-1"></i> Thùng rác
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="mb-4 border-bottom pb-4 pt-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Tên combo</label>
                            <input type="text" name="name" class="form-control" value="{{ request('name') }}" placeholder="Nhập tên combo">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tên sách</label>
                            <input type="text" name="book" class="form-control" value="{{ request('book') }}" placeholder="Nhập tên sách">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Giá từ</label>
                            <input type="number" name="price_from" class="form-control" value="{{ request('price_from') }}" placeholder="Từ">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Giá đến</label>
                            <input type="number" name="price_to" class="form-control" value="{{ request('price_to') }}" placeholder="Đến">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Khoảng ngày</label>
                            <input type="text" name="date_range" class="form-control date-range-picker" value="{{ request('date_range') }}" placeholder="yyyy-mm-dd to yyyy-mm-dd" autocomplete="off">
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-primary me-2" style="background-color:#405189; border-color: #405189">
                                <i class="ri-search-2-line"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('admin.collections.index') }}" class="btn btn-light me-2">
                                <i class="ri-refresh-line"></i> Đặt lại
                            </a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th class="text-center">Tên combo</th>
                                <th class="text-center">Tên sách</th>
                                <th class="text-center">Ảnh combo</th>
                                <th class="text-center">Ngày bắt đầu</th>
                                <th class="text-center">Ngày kết thúc</th>
                                <th class="text-center">Giá combo</th>
                                <th class="text-center" style="width: 100px;">Tùy chọn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collections as $key => $collection)
                            <tr>
                                <td class="text-center">{{ $collections instanceof \Illuminate\Pagination\LengthAwarePaginator ? $collections->firstItem() + $key : $key + 1 }}</td>
                                <td class="fw-semibold">{{ $collection->name }}</td>
                                <td class="fw-normal" >
                                     @if(isset($collection->books) && count($collection->books))
                                        @foreach($collection->books->take(3) as $book)
                                            - {{ $book->title }}<br>
                                        @endforeach
                                        @if($collection->books->count() > 3)
                                            ...
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($collection->cover_image)
                                        <img src="{{ asset('storage/' . $collection->cover_image) }}" alt="{{ $collection->name }}" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        <span class="text-muted">Chưa có ảnh</span>
                                    @endif
                                <td class="text-center">{{ $collection->start_date ? date('d/m/Y', strtotime($collection->start_date)) : '-' }}</td>
                                <td class="text-center">{{ $collection->end_date ? date('d/m/Y', strtotime($collection->end_date)) : '-' }}</td>
                                <td class="text-primary fw-bold text-center">{{ $collection->combo_price ? number_format($collection->combo_price, 0, ',', '.') . ' đ' : '-' }}</td>
                                <td class="text-center">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="{{ route('admin.collections.edit', $collection->id) }}" class="dropdown-item">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Sửa
                                            </a></li>
                                            @if($collection->deleted_at)
                                                <li>
                                                    <form action="{{ route('admin.collections.forceDelete', $collection->id) }}" method="POST" onsubmit="return confirm('Xóa vĩnh viễn combo này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-2-fill align-bottom me-2"></i> Xóa cứng
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('admin.collections.destroy', $collection->id) }}" method="POST" onsubmit="return confirm('Xóa mềm combo này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-warning">
                                                            <i class="ri-delete-bin-fill align-bottom me-2"></i> Xóa mềm
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Chưa có combo nào.</td>
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

@push('scripts')
<script>
 function initFlatpickr() {
        document.querySelectorAll('.date-range-picker').forEach(function(el) {
            flatpickr(el, {
                mode: 'range',
                dateFormat: 'd-m-Y',
                onChange: function(selectedDates, dateStr, instance) {
                    const parent = el.closest('.gift-row');
                    if (selectedDates.length === 2) {
                        const start = selectedDates[0];
                        const end = selectedDates[1];
                        parent.querySelector('.start-date').value = instance.formatDate(start, 'Y-m-d');
                        parent.querySelector('.end-date').value = instance.formatDate(end, 'Y-m-d');
                    } else {
                        parent.querySelector('.start-date').value = '';
                        parent.querySelector('.end-date').value = '';
                    }
                }
            });
        });
    }
    initFlatpickr();
</script>
@endpush
