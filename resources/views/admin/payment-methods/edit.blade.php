@extends('layouts.backend')

@section('title', 'Chỉnh sửa Phương Thức Thanh Toán')

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Chỉnh sửa Phương Thức Thanh Toán</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.payment-methods.index') }}">Quản lý thanh toán</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST"
                              onsubmit="return handleSubmit(this)">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Tên phương thức <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $paymentMethod->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3">{{ old('description', $paymentMethod->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <!-- Hidden field để luôn gửi dữ liệu -->
                                    <input type="hidden" name="is_active" value="0">
                                    
                                    <input type="checkbox"
                                           class="form-check-input @error('is_active') is-invalid @enderror"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Kích hoạt</label>

                                    @error('is_active')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary me-2">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
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
        function handleSubmit(form) {
            const btn = form.querySelector("button[type=submit]");
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...`;
            }
            return true;
        }
    </script>
@endpush
