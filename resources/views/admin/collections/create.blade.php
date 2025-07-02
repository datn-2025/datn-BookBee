@extends('layouts.backend')
@section('title', 'Thêm combo sách/tập')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card mb-3">
                <div class="card-body py-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}">Combo sách</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin combo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Tên combo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Tên combo" value="{{ old('name') }}" >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <div class="col-md-9">
                                <label for="">Chọn Khoảng Ngày</label>
                                <input type="text" name="date_range" class="form-control date-range-picker" placeholder="Chọn khoảng ngày" autocomplete="off">
                                <input type="hidden" name="start_date" class="start-date">
                                <input type="hidden" name="end_date" class="end-date">
                            </div>
                            <div class="col-12">
                                <label for="combo_price" class="form-label">Giá combo</label>
                                <input type="number" step="0.01" class="form-control @error('combo_price') is-invalid @enderror" id="combo_price" placeholder="Giá combo" name="combo_price" value="{{ old('combo_price') }}">
                                @error('combo_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="books" class="form-label">Chọn sách cho combo <span class="text-danger">*</span></label>
                                <select class="form-select @error('books') is-invalid @enderror" id="books" name="books[]" multiple >
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}" {{ (collect(old('books'))->contains($book->id)) ? 'selected' : '' }}>{{ $book->title }}</option>
                                    @endforeach
                                </select>
                                @error('books')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều sách.</small>
                            </div>
                            <div class="col-12">
                                <label for="cover_image" class="form-label">Ảnh bìa</label>
                                <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary" style="background-color:#405189; border-color: #405189">
                            <i class="ri-save-2-line me-1"></i> Lưu
                        </button>
                        <a href="{{ route('admin.collections.index') }}" class="btn btn-light">Quay lại</a>
                    </div>
                </div>
            </form>
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
            dateFormat: 'Y-m-d',
            onChange: function(selectedDates, dateStr, instance) {
                // Tìm input hidden cùng cấp
                const startInput = el.closest('.col-md-9').querySelector('.start-date');
                const endInput = el.closest('.col-md-9').querySelector('.end-date');
                if (selectedDates.length === 2) {
                    startInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                    endInput.value = instance.formatDate(selectedDates[1], 'Y-m-d');
                } else {
                    startInput.value = '';
                    endInput.value = '';
                }
            }
        });
    });
}
initFlatpickr();
// Đảm bảo khi submit form luôn lấy lại giá trị nếu user không chọn lại ngày
const form = document.querySelector('form');
if (form) {
    form.addEventListener('submit', function() {
        document.querySelectorAll('.date-range-picker').forEach(function(el) {
            const startInput = el.closest('.col-md-9').querySelector('.start-date');
            const endInput = el.closest('.col-md-9').querySelector('.end-date');
            if (el.value && el.value.includes(' to ')) {
                const parts = el.value.split(' to ');
                startInput.value = parts[0].trim();
                endInput.value = parts[1].trim();
            }
        });
    });
}
</script>
@endpush
