@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách quyền</h3>
                        <div class="card-tools">
                            @permission('permission.create')
                            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm quyền
                            </a>
                            @endpermission
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">{{ ucfirst($module) }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Tên quyền</th>
                                                    <th>Định danh</th>
                                                    <th>Mô tả</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($modulePermissions as $permission)
                                                    <tr>
                                                        <td>{{ $permission->name }}</td>
                                                        <td>{{ $permission->slug }}</td>
                                                        <td>{{ $permission->description }}</td>
                                                        <td>
                                                            @permission('permission.edit')
                                                            <a href="{{ route('admin.permissions.edit', $permission) }}"
                                                                class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @endpermission
                                                            @permission('permission.delete')
                                                            <form
                                                                action="{{ route('admin.permissions.destroy', $permission) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            @endpermission
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
