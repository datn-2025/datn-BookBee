@extends('layouts.backend')

@section('title', 'Chi tiết nhân viên')

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Quản Lý Nhân Viên</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Nhân Viên</li>
                </ol>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col">
                <div class="h-100">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Tài khoản nhân viên</h4>
                        </div>
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-4 d-flex justify-content-center align-items-center">
                                        <img src="{{ $staff->avatar ?? 'https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png' }}"
                                            style="width:80%;border-radius:50%;" alt="Avatar nhân viên"
                                            onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png'">
                                    </div>
                                    <div class="col-8">
                                        <table class="table table-borderless">
                                            <tbody style="font-size: large;">
                                                <tr>
                                                    <th>Họ tên:</th>
                                                    <td>{{ $staff->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email:</th>
                                                    <td>{{ $staff->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Số điện thoại:</th>
                                                    <td>{{ $staff->phone }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Vai trò:</th>
                                                    <td>{{ $staff->role ? $staff->role->name : 'Chưa phân quyền' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ngày tạo:</th>
                                                    <td>{{ $staff->created_at }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ngày cập nhật:</th>
                                                    <td>{{ $staff->updated_at }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
