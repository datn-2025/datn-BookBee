@extends('layouts.backend')
@section('title', 'Sửa combo sách/tập')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card mb-3">
                <div class="card-body py-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}">Combo sách</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa combo</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <form action="{{ route('admin.collections.update', $collection->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin combo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Tên combo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $collection->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <div class="col-md-9">
                                <label for="date_range" class="form-label">Chọn Khoảng Ngày</label>
                                <input type="text" name="date_range" class="form-control date-range-picker @error('date_range') is-invalid @enderror" placeholder="Chọn khoảng ngày" autocomplete="off"
                                    value="{{ old('date_range', ($startDate && $endDate) ? ($startDate . ' - ' . $endDate) : '') }}">
                                <input type="hidden" name="start_date" class="start-date" value="{{ old('start_date', $collection->startDate) }}">
                                <input type="hidden" name="end_date" class="end-date" value="{{ old('end_date', $collection->endDate) }}">
                                @error('date_range')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="combo_price" class="form-label">Giá combo</label>
                                <input type="number" step="0.01" class="form-control @error('combo_price') is-invalid @enderror" id="combo_price" name="combo_price" value="{{ old('combo_price', $collection->combo_price) }}">
                                @error('combo_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="books" class="form-label">Chọn sách cho combo <span class="text-danger">*</span></label>
                                <select class="form-select @error('books') is-invalid @enderror" id="books" name="books[]" multiple required>
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}" {{ (in_array($book->id, old('books', $selectedBooks ?? $collection->books->pluck('id')->toArray()))) ? 'selected' : '' }}>{{ $book->title }}</option>
                                    @endforeach
                                </select>
                                @error('books')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều sách.</small>
                            </div>
                            <div class="col-12">
                                <label for="cover_image" class="form-label">Ảnh bìa</label>
                                @if($collection->cover_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $collection->cover_image) }}" alt="Ảnh bìa" style="max-width:120px;max-height:120px;object-fit:cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $collection->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary" style="background-color:#405189; border-color: #405189">
                            <i class="ri-save-2-line me-1"></i> Cập nhật
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>
<script>
    flatpickr('.date-range-picker', {
        mode: 'range',
        dateFormat: 'd/m/Y',
        defaultDate: @json(old('date_range', ($collection->start_date && $collection->end_date) ? [$collection->start_date->format('d/m/Y'), $collection->end_date->format('d/m/Y')] : [])),
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                document.querySelector('.start-date').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                document.querySelector('.end-date').value = instance.formatDate(selectedDates[1], 'Y-m-d');
            }
        }
    });
</script>
@endpush
