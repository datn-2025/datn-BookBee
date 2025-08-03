@extends('layouts.backend')

@section('title', 'Chỉnh Sửa Nhân Viên')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Chỉnh Sửa Nhân Viên</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Nhân Viên</a></li>
                        <li class="breadcrumb-item active">Chỉnh Sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Tên</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $staff->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staff->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Số điện thoại</label>
                            <div class="col-sm-10">
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $staff->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Vai trò</label>
                            <div class="col-sm-10">
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror">
                                    <option value="">-- Chọn vai trò --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $staff->role && $staff->role->id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Trạng thái</label>
                            <div class="col-sm-10">
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="Hoạt Động" {{ $staff->status == 'Hoạt Động' ? 'selected' : '' }}>Hoạt Động</option>
                                    <option value="Bị Khóa" {{ $staff->status == 'Bị Khóa' ? 'selected' : '' }}>Bị Khóa</option>
                                    <option value="Chưa kích Hoạt" {{ $staff->status == 'Chưa kích Hoạt' ? 'selected' : '' }}>Chưa kích Hoạt</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Cập Nhật</button>
                                <a href="{{ route('admin.staff.show', $staff->id) }}" class="btn btn-secondary">Quay Lại</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
