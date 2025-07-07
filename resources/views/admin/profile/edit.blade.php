@extends('layouts.backend')

@section('title', 'Sửa Thông Tin Cá Nhân')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- PHẦN PROFILE TOP -->
                <div class="position-relative mx-n4 mt-n4 d-flex flex-column align-items-center">
                    <div class="profile-wid-bg profile-setting-img w-100" style="height: 220px; overflow: hidden;">
                        <img src="{{ Auth::user()->background_image ?? asset('assets/images/profile-bg.jpg') }}" 
                            class="profile-wid-img w-100" alt="Background">
                    </div>
                </div>

                <!-- PHẦN AVATAR + TÊN -->
                <div class="container">
                    <div class="bg-white rounded shadow p-4 w-100 d-flex flex-column align-items-center"
                        style="margin-top: -80px; z-index: 10; position: relative; width: 100%; max-width: 100%;">

                        <!-- Avatar -->
                        <div class="profile-user position-relative d-inline-block mb-3">
                            <img src="{{ Auth::user()->avatar ?? 'https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png' }}" 
                                class="rounded-circle img-thumbnail border border-white border-3 shadow" 
                                style="width: 120px; height: 120px; object-fit: cover;" 
                                alt="user-profile-image">
                        </div>

                        <!-- Tên và vai trò -->
                        <h5 class="fs-16 mb-1 text-center">{{ Auth::user()->name }}</h5>
                        <p class="text-muted mb-0 text-center">{{ Auth::user()->role->name ?? 'Chưa phân quyền' }}</p>
                    </div>
                </div>


            <div class="row justify-content-center mt-4">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                        <i class="fas fa-home"></i> Personal Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                        <i class="far fa-user"></i> Change Password
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content">
                                <div class="tab-pane active" id="personalDetails" role="tabpanel">
                                    <form action="{{ route('admin.profile.update') }}" method="POST" onsubmit="return disableSubmitOnce(this)">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="nameInput" class="form-label">Full Name</label>
                                                    <input type="text" class="form-control" id="nameInput" name="name"
                                                        placeholder="Enter your full name" value="{{ Auth::user()->name }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="phonenumberInput" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" id="phonenumberInput"
                                                        name="phone" placeholder="Enter your phone number"
                                                        value="{{ Auth::user()->phone }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="emailInput" class="form-label">Email Address</label>
                                                    <input type="email" class="form-control" id="emailInput"
                                                        name="email" placeholder="Enter your email"
                                                        value="{{ Auth::user()->email }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="addressInput" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="addressInput"
                                                        name="address" placeholder="Enter your address"
                                                        value="{{ Auth::user()->address }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-3 pb-2">
                                                    <label for="descriptionInput" class="form-label">About Me</label>
                                                    <textarea class="form-control" id="descriptionInput" name="description" placeholder="Write something about yourself"
                                                        rows="3">{{ Auth::user()->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                                    <button type="reset" class="btn btn-soft-success">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!--end tab-pane-->
                                <div class="tab-pane" id="changePassword" role="tabpanel">
                                    <form action="{{ route('admin.profile.change-password') }}" method="POST" onsubmit="return disableSubmitOnce(this)">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-2">
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="oldpasswordInput" class="form-label">Old Password*</label>
                                                    <input type="password" class="form-control" name="current_password"
                                                        id="oldpasswordInput" placeholder="Enter current password"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="newpasswordInput" class="form-label">New Password*</label>
                                                    <input type="password" class="form-control" name="password"
                                                        id="newpasswordInput" placeholder="Enter new password" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="confirmpasswordInput" class="form-label">Confirm
                                                        Password*</label>
                                                    <input type="password" class="form-control"
                                                        name="password_confirmation" id="confirmpasswordInput"
                                                        placeholder="Confirm password" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">Change
                                                        Password</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!--end tab-pane-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Chống submit nhiều lần
        document.querySelectorAll("form[onsubmit]").forEach(form => {
            form.onsubmit = () => {
                const btn = form.querySelector("button[type=submit]");
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...`;
                }
                return true;
            };
        });
    });
</script>
@endpush  

