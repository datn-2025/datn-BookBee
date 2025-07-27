@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sửa vai trò</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Tên vai trò</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description', $role->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Quyền</label>
                                @foreach ($permissions as $module => $modulePermissions)
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input module-checkbox"
                                                    id="module_{{ $module }}" data-module="{{ $module }}">
                                                <label class="custom-control-label" for="module_{{ $module }}">
                                                    {{ ucfirst($module) }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($modulePermissions as $permission)
                                                    <div class="col-md-4">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input permission-checkbox"
                                                                id="permission_{{ $permission->id }}" name="permissions[]"
                                                                value="{{ $permission->id }}"
                                                                data-module="{{ $module }}"
                                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="permission_{{ $permission->id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @error('permissions')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Xử lý checkbox module
                $('.module-checkbox').change(function() {
                    var module = $(this).data('module');
                    var isChecked = $(this).prop('checked');
                    $('.permission-checkbox[data-module="' + module + '"]').prop('checked', isChecked);
                });

                // Xử lý checkbox permission
                $('.permission-checkbox').change(function() {
                    var module = $(this).data('module');
                    var moduleCheckbox = $('#module_' + module);
                    var totalPermissions = $('.permission-checkbox[data-module="' + module + '"]').length;
                    var checkedPermissions = $('.permission-checkbox[data-module="' + module + '"]:checked')
                        .length;

                    if (checkedPermissions === 0) {
                        moduleCheckbox.prop('checked', false);
                    } else if (checkedPermissions === totalPermissions) {
                        moduleCheckbox.prop('checked', true);
                    }
                });

                // Kiểm tra trạng thái checkbox module khi load trang
                $('.module-checkbox').each(function() {
                    var module = $(this).data('module');
                    var totalPermissions = $('.permission-checkbox[data-module="' + module + '"]').length;
                    var checkedPermissions = $('.permission-checkbox[data-module="' + module + '"]:checked')
                        .length;

                    if (checkedPermissions === totalPermissions) {
                        $(this).prop('checked', true);
                    }
                });
            });
        </script>
    @endpush
@endsection
