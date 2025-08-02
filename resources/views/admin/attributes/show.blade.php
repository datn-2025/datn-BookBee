@extends('layouts.backend')

@section('title', 'Chi tiết thuộc tính')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Chi Tiết Thuộc Tính</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}"
                                style="color: inherit;">Thuộc tính</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Danh sách giá trị -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách giá trị thuộc tính</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-light text-primary rounded fs-3">
                                            <i class="ri-price-tag-3-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fs-16 mb-1">{{ $attribute->name }}</h5>
                                    <p class="text-muted mb-0">Tạo ngày: {{ $attribute->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="text-muted mb-0">Cập nhật lần cuối: {{ $attribute->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if ($attribute->values->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th>Giá trị</th>
                                        <th>Sử dụng trong sách</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attribute->values as $index => $value)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><span
                                                    class="badge bg-light text-dark fs-6 fw-normal">{{ $value->value }}</span>
                                            </td>
                                            <td>
                                                @if ($value->books->count() > 0)
                                                    <span class="badge bg-success">{{ $value->books->count() }} sách</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Chưa sử dụng</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Thuộc tính này chưa có giá trị nào.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Thông tin thuộc tính -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin thuộc tính</h5>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên thuộc tính <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $attribute->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá trị thuộc tính <span class="text-danger">*</span></label>
                            <div id="attribute-values-container">
                                @foreach ($attribute->values as $index => $value)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="values[]"
                                            value="{{ $value->value }}" required>
                                        <input type="hidden" name="value_ids[]" value="{{ $value->id }}">
                                        <button type="button" class="btn btn-danger remove-value"
                                            {{ $attribute->values->count() <= 1 ? 'disabled' : '' }}>
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-value-btn">
                                <i class="ri-add-line"></i> Thêm giá trị
                            </button>
                            @error('values')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('values.*')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line align-bottom me-1"></i> Cập nhật thuộc tính
                            </button>
                            <a href="{{ route('admin.attributes.index') }}" class="btn btn-light">
                                <i class="ri-arrow-left-line align-bottom me-1"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- <div class="card mt-4">
            <div class="card-body">
                <form action="{{ route('admin.attributes.destroy', $attribute->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thuộc tính này?');">
                    @csrf
                    @method('DELETE')
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-delete-bin-line align-bottom me-1"></i> Xóa thuộc tính
                        </button>
                    </div>
                </form>
            </div>
        </div> --}}
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('attribute-values-container');
        const addBtn = document.getElementById('add-value-btn');

        if (container && addBtn) {
            // Add new value field
            addBtn.addEventListener('click', function() {
                const valueField = document.createElement('div');
                valueField.className = 'input-group mb-2';
                valueField.innerHTML = `
                    <input type="text" class="form-control" name="values[]" placeholder="Nhập giá trị thuộc tính" required>
                    <button type="button" class="btn btn-danger remove-value"><i class="ri-delete-bin-line"></i></button>
                `;
                container.appendChild(valueField);

                // Enable all remove buttons if there's more than one value field
                if (container.querySelectorAll('.input-group').length > 1) {
                    container.querySelectorAll('.remove-value').forEach(btn => {
                        btn.disabled = false;
                    });
                }
            });

            // Remove value field
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-value') || e.target.closest('.remove-value')) {
                    const button = e.target.classList.contains('remove-value') ? e.target : e.target
                        .closest('.remove-value');
                    const inputGroup = button.closest('.input-group');

                    if (container.querySelectorAll('.input-group').length > 1) {
                        inputGroup.remove();

                        // Disable the remove button if only one field remains
                        if (container.querySelectorAll('.input-group').length === 1) {
                            container.querySelector('.remove-value').disabled = true;
                        }
                    }
                }
            });

            // Initialize: disable remove button if only one field exists
            if (container.querySelectorAll('.input-group').length === 1) {
                const removeBtn = container.querySelector('.remove-value');
                if (removeBtn) {
                    removeBtn.disabled = true;
                }
            }
        }
    });
</script>
