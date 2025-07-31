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
                                <div class="row">
                                    @foreach ($roles as $role)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]"
                                                    value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                    {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="role_{{ $role->id }}">{{ $role->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label"><strong>Quyền trực tiếp</strong></label>
                                <input type="text" class="form-control mb-2" id="permission-search"
                                    placeholder="Tìm kiếm quyền...">
                                @php $grouped = collect($permissions)->groupBy('module'); @endphp
                                @php
                                    $allUserPermissionIds = $user->permissions
                                        ->pluck('id')
                                        ->merge($user->roles->flatMap->permissions->pluck('id'))
                                        ->unique();
                                @endphp
                                @foreach ($grouped as $module => $modulePermissions)
                                    <div class="mb-2"><strong>{{ ucfirst($module) }}</strong></div>
                                    <div class="row mb-2 permission-group" data-module="{{ $module }}">
                                        @foreach ($modulePermissions as $permission)
                                            @php
                                                $hasDirect = $user->permissions->contains($permission->id);
                                                $hasViaRole = $user->roles->flatMap->permissions
                                                    ->pluck('id')
                                                    ->contains($permission->id);
                                            @endphp
                                            <div class="col-md-4 permission-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                                        value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                                        @if ($hasDirect || $hasViaRole) checked @endif
                                                        @if (!$hasDirect && $hasViaRole) title="Quyền này được thừa hưởng từ vai trò. Nếu muốn gán trực tiếp, hãy tick lại." @endif>
                                                    <label class="form-check-label permission-label"
                                                        for="perm_{{ $permission->id }}">{{ $permission->name }}
                                                        @if (!$hasDirect && $hasViaRole)
                                                            <span class="text-info"
                                                                title="Quyền này được thừa hưởng từ vai trò. Nếu muốn gán trực tiếp, hãy tick lại.">(qua
                                                                vai trò)</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                                <small class="text-muted">Các quyền này sẽ được gán trực tiếp cho tài khoản, không phụ thuộc
                                    vai trò.</small>
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
