@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách vai trò</h3>
                        @permission('role.create')
                        <div class="card-tools">
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm vai trò
                            </a>
                        </div>
                        @endpermission
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tên vai trò</th>
                                        <th>Mô tả</th>
                                        <th>Số quyền</th>
                                        <th>Số người dùng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $role->name }}</td>
                                           <td>{!! $role->description !!}</td>
                                            <td>{{ $role->permissions->count() }}</td>
                                            <td>{{ $role->users->count() }}</td>
                                            <td>
                                                @permission('role.edit')
                                                <a href="{{ route('admin.roles.edit', $role) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endpermission
                                                @permission('role.delete')
                                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                                    class="d-inline"
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
            </div>
        </div>
    </div>
@endsection
