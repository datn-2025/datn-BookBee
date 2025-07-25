@extends('layouts.backend')

@section('title', 'Danh Sách Người Dùng')

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Quản Lý Người Dùng</h4>


                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                            <li class="breadcrumb-item active">Người Dùng</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <!-- Bắt đầu Form tìm kiếm -->
        <div class="row">
            <div class="col">
                <form action="{{ route('admin.users.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm thông tin"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="Hoạt Động" {{ request('status') == 'Hoạt Động' ? 'selected' : '' }}>Hoạt Động
                                </option>
                                <option value="Bị Khóa" {{ request('status') == 'Bị Khóa' ? 'selected' : '' }}>Bị Khóa
                                </option>
                                <option value="Chưa kích Hoạt"
                                    {{ request('status') == 'Chưa kích Hoạt' ? 'selected' : '' }}>Chưa kích Hoạt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Đặt lại</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        <!-- Kết thúc Form tìm kiếm -->


        <div class="row">
            <div class="col">

                <div class="h-100">
                    <!-- Striped Rows -->
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Tài khoản người dùng</h4>
                            <!-- <a href="?act=form-them-danh-muc" type="button" class="btn btn-soft-success material-shadow-none"><i class="ri-add-circle-line align-middle me-1"></i>Thêm Người Dùng</a> -->

                        </div><!-- end card header -->

                        <div class="card-body">

                            <div class="live-preview">
                                <div class="table-responsive">
                                    <table class="table table-striped table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">STT</th>
                                                <th scope="col">Tên Người Dùng</th>
                                                <th scope="col">Ảnh Đại Diện</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Số Điện Thoại</th>
                                                <th scope="col">Vai trò</th>
                                                <th scope="col">Trạng thái</th>
                                                <th scope="col">Thao Tác</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $key => $user)
                                                <tr>
                                                    <td class="fw-medium">{{ $key + 1 }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>
                                                        <img style="border-radius:50%;"
                                                            src="{{ $user->avatar ?? 'https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png' }}"
                                                            width="100px" height="100px" alt=""
                                                            onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png'">
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->phone }}</td>
                                                    <td>{{ $user->role ? $user->role->name : 'Chưa phân quyền' }}</td>
                                                    <td>
                                                        @if ($user->status === 'Hoạt Động')
                                                            <span class="badge bg-success">Hoạt Động</span>
                                                        @elseif($user->status === 'Bị Khóa')
                                                            <span class="badge bg-danger">Bị Khóa</span>
                                                        @elseif($user->status === 'Chưa kích Hoạt')
                                                            <span class="badge bg-warning">Chưa kích Hoạt</span>
                                                        @else
                                                            <span class="badge bg-secondary">Không xác định</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.users.show', ['id' => $user->id]) }}"
                                                            class="link-success fs-15">
                                                            <i class="las la-eye"></i>
                                                        </a>

                                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                                            class="link-success fs-15 ">
                                                            <i class="ri-edit-2-line align-bottom"></i>
                                                        </a>


                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div><!-- end card-body -->
                    </div><!-- end card -->

                </div> <!-- end .h-100-->

            </div> <!-- end col -->
        </div>

    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
@endsection
