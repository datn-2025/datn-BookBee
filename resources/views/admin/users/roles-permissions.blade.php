@extends('layouts.backend')

@section('title', 'Phân quyền cho người dùng')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Phân quyền cho người dùng</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người Dùng</a></li>
                            <li class="breadcrumb-item active">Phân quyền</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Họ tên: <strong>{{ $user->name }}</strong> | Email: <strong>{{ $user->email }}</strong></h5>
                        <form action="{{ route('admin.users.roles-permissions.update', $user->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <div class="mb-4">
                                <label class="form-label"><strong>Vai trò</strong></label>
                                <select name="role_id" class="form-select" required>
                                    <option value="">-- Chọn vai trò --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @if ($user->role && $user->role->id == $role->id) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label"><strong>Quyền trực tiếp</strong></label>
                                <input type="text" class="form-control mb-2" id="permission-search"
                                    placeholder="Tìm kiếm quyền...">
                                @php $grouped = collect($permissions)->groupBy('module'); @endphp
                                @php
                                    $allUserPermissionIds = $user->permissions
                                        ->pluck('id')
                                        ->merge($user->role ? $user->role->permissions->pluck('id') : collect())
                                        ->unique();
                                @endphp
                                @foreach ($grouped as $module => $modulePermissions)
                                    <div class="mb-2"><strong>{{ ucfirst($module) }}</strong></div>
                                    <div class="row mb-2 permission-group" data-module="{{ $module }}">
                                        @foreach ($modulePermissions as $permission)
                                            @php
                                                $hasDirect = $user->permissions->contains($permission->id);
                                            @endphp
                                            <div class="col-md-4 permission-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                                        value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                                        @if ($hasDirect) checked @endif>
                                                    <label class="form-check-label permission-label"
                                                        for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                                <small class="text-muted">Các quyền này sẽ được gán trực tiếp cho tài khoản, không phụ thuộc vai trò.<br>
                                    <span class="text-info">Chỉ hiển thị các quyền chưa có qua vai trò hiện tại.</span>
                                </small>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </form>
                        @if (session('success'))
                            <script>
                                toastr.success("{{ session('success') }}");
                            </script>
                        @endif
                        @if (session('error'))
                            <script>
                                toastr.error("{{ session('error') }}");
                            </script>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('permission-search');
            searchInput.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll('.permission-item').forEach(function(item) {
                    const label = item.querySelector('.permission-label').textContent.toLowerCase();
                    if (label.includes(keyword)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
                // Ẩn/hiện group module nếu không có quyền nào hiển thị
                document.querySelectorAll('.permission-group').forEach(function(group) {
                    const visible = group.querySelectorAll(
                        '.permission-item:not([style*="display: none"])').length;
                    group.style.display = visible ? '' : 'none';
                });
            });
        });
    </script>
@endsection
