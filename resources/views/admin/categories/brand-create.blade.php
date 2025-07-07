@extends('layouts.backend')
@section('title', 'Thêm Thương Hiệu')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Thêm Thương Hiệu</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.categories.brands.brand') }}">Quản lý thương
                                    hiệu</a></li>
                            <li class="breadcrumb-item active">Thêm mới</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.categories.brands.store') }}" method="POST"
                            enctype="multipart/form-data" onsubmit="return disableSubmitOnce(this)">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên thương hiệu <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Ảnh/Logo</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image" accept="image/*">
                                <small class="text-muted">Cho phép: JPG, JPEG, PNG, GIF. Tối đa 2MB.</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <img id="preview" src="#" alt="Xem trước ảnh" class="mt-2 border rounded"
                                    style="display: none; max-height: 120px;" />

                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.categories.brands.brand') }}"
                                    class="btn btn-secondary me-2">Hủy</a>
                                <button type="submit" class="btn btn-primary">Thêm thương hiệu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Xem trước ảnh
        document.getElementById("image")?.addEventListener("change", e => {
            const file = e.target.files?.[0];
            const preview = document.getElementById("preview");

            if (!preview) return;
            if (file?.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = ev => {
                    preview.src = ev.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = "none";
            }
        });

        // Chống submit nhiều lần
        document.querySelectorAll("form[onsubmit]").forEach(form => {
            form.onsubmit = () => {
                const btn = form.querySelector("button[type=submit]");
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...`;
                }
                return true;
            };
        });
    });
</script>
@endpush    
