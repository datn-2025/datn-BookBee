@extends('layouts.backend')

@section('title', 'dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="h-100">
                    <!-- Breadcrumb -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Tổng quan</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Trang chủ</a></li>
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Header -->
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                <div class="flex-grow-1">
                                    @if (auth()->check() && auth()->user()->isAdmin())
                                        <h4 class="fs-18 mb-1">Xin chào, {{ auth()->user()->name }}!</h4>
                                        <p class="text-muted mb-0">Chào mừng bạn đến với hệ thống quản trị BookBee. Dưới đây
                                            là tổng quan cửa hàng sách hôm nay.</p>
                                    @else
                                        <h4 class="fs-18 mb-1">Chào mừng!</h4>
                                        <p class="text-muted mb-0">Bạn không có quyền truy cập vào dashboard quản trị.</p>
                                    @endif
                                </div>
                                <div class="mt-3 mt-lg-0">
                                    <form action="#">
                                        <div class="row g-3 mb-0 align-items-center">
                                            <div class="col-sm-auto">
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control border-0 shadow dash-filter-picker"
                                                        data-provider="flatpickr" data-range-date="true"
                                                        data-date-format="d M, Y" placeholder="Chọn khoảng thời gian">
                                                    <div class="input-group-text bg-primary text-white border-primary">
                                                        <i class="ri-calendar-2-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="{{ route('admin.books.create') }}" class="btn btn-success">
                                                    <i class="ri-add-circle-line align-middle me-1"></i> Thêm sách
                                                </a>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-info btn-icon layout-rightside-btn">
                                                    <i class="ri-bar-chart-2-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- end header row -->
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- 4 Thẻ thống kê chính -->
                    <livewire:dashboard-stats />

                    <!-- Các báo cáo bổ sung -->
                    <div class="row mt-4">

                        <div class="col-xl-4 col-md-6">
                            <livewire:top-selling-books-report />
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <livewire:inventory-status-report />
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <livewire:revenue-by-author-publisher-report />
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-xl-4 col-md-6">
                            <livewire:book-category-chart />
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <livewire:book-author-chart />
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <livewire:book-brand-chart />
                        </div>
                    </div>

                </div> <!-- end .h-100 -->
            </div>
        </div>
    </div>
@endsection
