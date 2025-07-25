@extends('layouts.backend')

@section('title', 'Chi tiết người dùng')

@section('content')
    <div class="container-fluid">
        <!-- start page title -->

        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Quản Lý Người Dùng</h4>


            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                    <li class="breadcrumb-item active">Người Dùng</li>
                </ol>
            </div>

        </div>

        <!-- end page title -->


        <div class="row">
            <div class="col">

                <div class="h-100">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mb-3">
                        <i class="ri-arrow-left-line"></i> Quay lại danh sách
                    </a>
                    <!-- Striped Rows -->
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Tài khoản người dùng</h4>

                        </div><!-- end card header -->

                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-4 d-flex justify-content-center align-items-center">
                                        <img src="{{ $user->avatar ?? 'https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png' }}"
                                            style="width:80%;border-radius:50%;" alt="Avatar người dùng"
                                            onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png'">
                                    </div>
                                    <div class="col-8">
                                        <table class="table table-borderless">
                                            <tbody style="font-size: large;">
                                                <tr>
                                                    <th>Họ tên:</th>
                                                    <td>{{ $user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email:</th>
                                                    <td>{{ $user->email }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Số điện thoại:</th>
                                                    <td>{{ $user->phone }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Vai trò:</th>
                                                    <td>{{ $user->role ? $user->role->name : 'Chưa phân quyền' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ngày tạo:</th>
                                                    <td>{{ $user->created_at }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ngày cập nhật:</th>
                                                    <td>{{ $user->updated_at }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                        <h2>Lịch sử mua hàng</h2>
                                        <div>
                                            <table id="example1" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Mã đơn hàng</th>
                                                        <th>Tên người nhận</th>
                                                        <th>Ngày đặt</th>
                                                        <th>Tổng Tiền</th>
                                                        <th>Trạng thái </th>
                                                        <th>Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($listDonHang as $key => $donHang)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $donHang->order_code }}</td>
                                                            <td>{{ $donHang->shipping_name }} <br>
                                                                {{ $donHang->shipping_phone }} </td>

                                                            <td>{{ $donHang->created_at }}</td>
                                                            <td>{{ number_format($donHang->total_amount, 0, ',', '.') }}đ
                                                            </td>
                                                            <td>
                                                                @if ($donHang->orderStatus)
                                                                    @php
                                                                        $statusClass = match (
                                                                            $donHang->orderStatus->name
                                                                        ) {
                                                                            'Chờ xác nhận' => 'bg-warning',
                                                                            'Đã xác nhận' => 'bg-info',
                                                                            'Đang chuẩn bị' => 'bg-info',
                                                                            'Đang giao hàng' => 'bg-primary',
                                                                            'Đã giao thành công' => 'bg-success',
                                                                            'Đã nhận hàng' => 'bg-success',
                                                                            'Thành công' => 'bg-success',
                                                                            'Giao thất bại' => 'bg-danger',
                                                                            'Đã hủy' => 'bg-danger',
                                                                            'Đã hoàn tiền' => 'bg-warning',
                                                                            default => 'bg-secondary',
                                                                        };
                                                                    @endphp
                                                                    <span
                                                                        class="badge {{ $statusClass }}">{{ $donHang->orderStatus->name }}</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Không xác định</span>
                                                                @endif
                                                                <br>
                                                                @if ($donHang->paymentStatus)
                                                                    @php
                                                                        $paymentStatusClass = match (
                                                                            $donHang->paymentStatus->name
                                                                        ) {
                                                                            'Chưa thanh toán' => 'bg-warning',
                                                                            'Đã thanh toán' => 'bg-success',
                                                                            'Đã hoàn tiền' => 'bg-info',
                                                                            'Thanh toán thất bại' => 'bg-danger',
                                                                            'Đang xử lý' => 'bg-primary',
                                                                            default => 'bg-secondary',
                                                                        };
                                                                    @endphp
                                                                    <span
                                                                        class="badge {{ $paymentStatusClass }}">{{ $donHang->paymentStatus->name }}</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Không xác định</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <a href="" class="link-success fs-15"><i
                                                                            class="las la-eye"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center">Chưa có đơn hàng nào</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.container-fluid -->
                        </section>
                    </div><!-- end card -->

                </div> <!-- end .h-100-->

            </div> <!-- end col -->
        </div>

    </div>
    <!-- container-fluid -->
                            <div class="h-100">
                               <!-- Striped Rows -->
                               <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Tài khoản người dùng</h4>

                                </div><!-- end card header -->

                                            <section class="content">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-4 d-flex justify-content-center align-items-center">
                                                            <img src="{{ $user->avatar ?? 'https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png' }}"
                                                                style="width:80%;border-radius:50%;" alt="Avatar người dùng"
                                                                onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png'">
                                                        </div>
                                                        <div class="col-8">
                                                            <table class="table table-borderless">
                                                                <tbody style="font-size: large;">
                                                                    <tr>
                                                                        <th>Họ tên:</th>
                                                                        <td>{{ $user->name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Email:</th>
                                                                        <td>{{ $user->email }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Số điện thoại:</th>
                                                                        <td>{{ $user->phone }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Vai trò:</th>
                                                                        <td>{{ $user->role ? $user->role->name : 'Chưa phân quyền' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Ngày tạo:</th>
                                                                        <td>{{ $user->created_at }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Ngày cập nhật:</th>
                                                                        <td>{{ $user->updated_at }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-12">
                                                            <hr>
                                                            <h2>Lịch sử mua hàng</h2>
                                                            <div>
                                                                <table id="example1" class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>STT</th>
                                                                            <th>Mã đơn hàng</th>
                                                                            <th>Tên người nhận</th>
                                                                            <th>Ngày đặt</th>
                                                                            <th>Tổng Tiền</th>
                                                                            <th>Trạng thái </th>
                                                                            <th>Thao tác</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($listDonHang as $key => $donHang)
                                                                            <tr>
                                                                                <td>{{ $key + 1 }}</td>
                                                                                <td>{{ $donHang->order_code }}</td>
                                                                                <td>{{ $donHang->shipping_name }} <br>
                                                                               {{ $donHang->shipping_phone }} </td>
                                                                                
                                                                                <td>{{ $donHang->created_at }}</td>
                                                                                <td>{{ number_format($donHang->total_amount, 0, ',', '.') }}đ</td>
                                                                                <td>
                                                                                    @if($donHang->orderStatus)
                                                                                        @php
                                                                                            $statusClass = match($donHang->orderStatus->name) {
                                                                                                'Chờ xác nhận' => 'bg-warning',
                                                                                                'Đã xác nhận' => 'bg-info',
                                                                                                'Đang chuẩn bị' => 'bg-info',
                                                                                                'Đang giao hàng' => 'bg-primary', 
                                                                                                'Đã giao thành công' => 'bg-success',
                                                                                                'Đã nhận hàng' => 'bg-success',
                                                                                                'Thành công' => 'bg-success',
                                                                                                'Giao thất bại' => 'bg-danger',
                                                                                                'Đã hủy' => 'bg-danger',
                                                                                                'Đã hoàn tiền' => 'bg-warning',
                                                                                                default => 'bg-secondary'
                                                                                            };
                                                                                        @endphp
                                                                                        <span class="badge {{ $statusClass }}">{{ $donHang->orderStatus->name }}</span>
                                                                                    @else
                                                                                        <span class="badge bg-secondary">Không xác định</span>
                                                                                    @endif                                                                                
                                                                                <br>
                                                                                    @if($donHang->paymentStatus)
                                                                                        @php
                                                                                            $paymentStatusClass = match($donHang->paymentStatus->name) {
                                                                                                'Chưa thanh toán' => 'bg-warning',
                                                                                                'Đã Thanh Toán' => 'bg-success',
                                                                                                'Đã hoàn tiền' => 'bg-info',
                                                                                                'Thanh toán thất bại' => 'bg-danger',
                                                                                                'Đang xử lý' => 'bg-primary',
                                                                                                default => 'bg-secondary'
                                                                                            };
                                                                                        @endphp
                                                                                        <span class="badge {{ $paymentStatusClass }}">{{ $donHang->paymentStatus->name }}</span>
                                                                                    @else
                                                                                        <span class="badge bg-secondary">Không xác định</span>
                                                                                    @endif
                                                                                        </td>
                                                                                <td>
                                                                                    <div class="btn-group">
                                                                                        <a href="" class="link-success fs-15"><i class="las la-eye"></i></a>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="8" class="text-center">Chưa có đơn hàng nào</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /.row -->
                                                </div>
                                                <!-- /.container-fluid -->
                                            </section>
                            </div><!-- end card -->

                            </div> <!-- end .h-100-->

                        </div> <!-- end col -->
                    </div>

                </div>
                <!-- container-fluid -->         
@endsection
