@extends('layouts.backend')

@section('title', 'Danh Sách Nhân Viên')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Quản Lý Nhân Viên</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                            <li class="breadcrumb-item active">Nhân Viên</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('admin.staff.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm thông tin "
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="Hoạt Động" {{ request('status') == 'Hoạt Động' ? 'selected' : '' }}>Hoạt Động</option>
                                <option value="Bị Khóa" {{ request('status') == 'Bị Khóa' ? 'selected' : '' }}>Bị Khóa</option>
                                <option value="Chưa kích Hoạt" {{ request('status') == 'Chưa kích Hoạt' ? 'selected' : '' }}>Chưa kích Hoạt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Đặt lại</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col">
                <div class="h-100">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Danh sách nhân viên</h4>
                            <a href="{{ route('admin.staff.create') }}" class="btn btn-success btn-sm ms-2">Thêm Nhân Viên</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Họ tên</th>
                                            <th>Email</th>
                                            <th>Số điện thoại</th>
                                            <th>Vai trò</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($staffs as $staff)
                                            <tr>
                                                <td>{{ $staff->id }}</td>
                                                <td>{{ $staff->name }}</td>
                                                <td>{{ $staff->email }}</td>
                                                <td>{{ $staff->phone }}</td>
                                                <td>{{ $staff->role ? $staff->role->name : 'Chưa phân quyền' }}</td>
                                                <td>{{ $staff->status }}</td>
                                                <td>
                                                    <a href="{{ route('admin.staff.show', $staff->id) }}" class="btn btn-info btn-sm">Xem</a>
                                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                                    <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" style="display:inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                                    </form>
                                                    {{-- <a href="{{ route('admin.staff.roles-permissions.edit', $staff->id) }}" class="btn btn-secondary btn-sm">Phân quyền</a> --}}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Không có nhân viên nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                {{ $staffs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
