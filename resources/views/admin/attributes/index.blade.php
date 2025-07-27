@extends('layouts.backend')

@section('title', 'Quản lý thuộc tính')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản Lý Thuộc Tính</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        @permission('attribute.view')
                            <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}"
                                    style="color: inherit;">Thuộc tính</a></li>
                        @endpermission
                        <li class="breadcrumb-item active">Danh sách</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Danh sách thuộc tính -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách thuộc tính</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th>Tên thuộc tính</th>
                                    <th>Giá trị</th>
                                    <th style="width: 120px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attributes as $index => $attribute)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $attribute->name }}</td>
                                        <td>
                                            @if ($attribute->values->count() > 0)
                                                @foreach ($attribute->values as $value)
                                                    <span
                                                        class="badge bg-light text-dark me-1 mb-1">{{ $value->value }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Chưa có giá trị</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @permission('attribute.view')
                                                        <li>
                                                            <a href="{{ route('admin.attributes.show', $attribute->id) }}"
                                                                class="dropdown-item">
                                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Xem
                                                            </a>
                                                        </li>
                                                    @endpermission
                                                    @permission('attribute.delete')
                                                        <li>
                                                            <form
                                                                action="{{ route('admin.attributes.destroy', $attribute->id) }}"
                                                                method="POST" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger delete-item"
                                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa thuộc tính này?');">
                                                                    <i class="ri-delete-bin-fill align-bottom me-2"></i> Xóa
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endpermission
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có thuộc tính nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form thêm thuộc tính mới -->
        @permission('attribute.create')
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thêm thuộc tính mới</h5>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.attributes.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên thuộc tính <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Ví dụ: Màu sắc, Kích thước, Chất liệu</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Giá trị thuộc tính <span class="text-danger">*</span></label>
                                <div id="attribute-values-container">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="values[]"
                                            placeholder="Nhập giá trị thuộc tính" required>
                                        <button type="button" class="btn btn-danger remove-value" disabled><i
                                                class="ri-delete-bin-line"></i></button>
                                    </div>
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
                                <small class="text-muted d-block mt-1">Ví dụ: Đỏ, Xanh, Vàng (nếu thuộc tính là Màu sắc)</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line align-bottom me-1"></i> Lưu thuộc tính
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endpermission
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
