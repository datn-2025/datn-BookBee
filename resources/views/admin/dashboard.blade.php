@extends('layouts.backend')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="h-100">
                <!-- Header -->
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                            {{-- @if (auth()->check() && auth()->user()->isAdmin())
                                <h4 class="fs-18 mb-1">Xin chào, {{auth()->user()->name}} !</h4>
                                <p class="text-muted mb-0">Chào mừng bạn đến với hệ thống quản trị BookBee. Dưới đây là tổng quan cửa hàng sách hôm nay.</p>
                            @else --}}
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="#">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control border-0 shadow dash-filter-picker"
                                                       data-provider="flatpickr"
                                                       data-range-date="true"
                                                       data-date-format="d M, Y"
                                                       placeholder="Chọn khoảng thời gian">
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
                        </div> <!-- end card header -->
                    </div>
                </div>
                <!-- End Header -->

                {{-- Có thể include thêm các component dashboard như: --}}
                {{-- @include('admin.dashboard.statistics') --}}
                {{-- @include('admin.dashboard.sales-graph') --}}
                {{-- @include('admin.dashboard.top-books') --}}

            </div> <!-- end .h-100 -->
        </div>
    </div>
</div>

@endsection
