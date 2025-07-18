@extends('layouts.backend')

@section('title', 'Quản lý danh mục sách')

@section('content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12 d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Chỉnh sửa danh mục</h4>
            <nav>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục sách</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Page Title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin danh mục</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category->slug) }}" method="POST" enctype="multipart/form-data" onsubmit="return disableSubmitOnce(this)">
                        @csrf
                        @method('PUT')

                        <!-- Tên danh mục -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $category->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mô tả danh mục -->
                        <div class="mb-3">
                            <label for="description_cat" class="form-label">Mô tả danh mục</label>
                            <textarea id="description_cat" name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ảnh danh mục -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Ảnh danh mục</label>
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            @if ($category->image)
                                <div class="mt-3">
                                    <label class="form-label">Ảnh hiện tại:</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                             class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_image"
                                               id="remove_image" value="1">
                                        <label class="form-check-label" for="remove_image">
                                            Xóa ảnh hiện tại
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <img id="preview" src="#" alt="Xem trước ảnh"
                                 class="mt-3 border rounded"
                                 style="display: none; max-height: 120px;" />
                        </div>

                        <!-- Buttons -->
                        <div class="text-end">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">
                                <i class="ri-arrow-left-line align-bottom"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Cập nhật
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{!! Toastr::message() !!}
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Xem trước ảnh
        const input = document.getElementById("image");
        const preview = document.getElementById("preview");

        input?.addEventListener("change", e => {
            const file = e.target.files?.[0];
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

        // Ngăn submit nhiều lần
        document.querySelectorAll("form[onsubmit]").forEach(form => {
            form.onsubmit = () => {
                const btn = form.querySelector("button[type=submit]");
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...`;
                }
                return true;
            };
        });
    });
</script>
@endpush
