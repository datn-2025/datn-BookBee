@extends('layouts.backend')
@section('title', 'Quản lý liên hệ')

@section('content')
    <div class="container-fluid py-4">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Chi Tiết Liên Hệ</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.contacts.index') }}">Liên hệ</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="text-muted">Tên:</h5>
                            <p>{{ $contact->name }}</p>
                        </div>

                        <div class="mb-4">
                            <h5 class="text-muted">Email:</h5>
                            <p>{{ $contact->email }}</p>
                        </div>

                        <div class="mb-4">
                            <h5 class="text-muted">Trạng thái:</h5>
                            @php
                                $statusClass = [
                                    'new' => 'badge bg-primary',
                                    'processing' => 'badge bg-warning text-dark',
                                    'replied' => 'badge bg-success',
                                    'closed' => 'badge bg-secondary',
                                ];
                            @endphp
                            <span class="{{ $statusClass[$contact->status] ?? 'badge bg-light text-dark' }}">
                                {{ ucfirst($contact->status) }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <h5 class="text-muted">Ngày gửi:</h5>
                            <p>{{ $contact->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mb-4">
                            <h5 class="text-muted">Nội dung phản hồi:</h5>
                            <p>{{ $contact->note ?? 'Không có ghi chú' }}</p>
                        </div>
                        @if ($contact->admin_reply)
                            <div class="mb-4">
                                <h5 class="text-muted">Phản hồi từ Admin:</h5>
                                <p>{{ $contact->admin_reply }}</p>
                            </div>
                        @endif


                        <div class="mb-4">
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editStatusModal">
                                <i class="ri-pencil-line align-bottom me-1"></i> Sửa trạng thái
                            </button>

                            <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
                                    <i class="ri-delete-bin-line align-bottom me-1"></i> Xóa
                                </button>
                            </form>

                            <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-go-back-line align-bottom me-1"></i> Quay lại danh sách
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin liên hệ</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Địa chỉ:</h6>
                            <p class="text-muted">{{ $contact->address }}</p>
                        </div>
                        <div class="mb-3">
                            <h6>Ngày tạo:</h6>
                            <p class="text-muted">{{ $contact->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal chỉnh sửa trạng thái -->
        <div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStatusModalLabel">Chỉnh sửa trạng thái liên hệ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.contacts.update', $contact->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" name="status" required>
                                    <option value="new" {{ $contact->status == 'new' ? 'selected' : '' }} @if(in_array($contact->status, ['processing', 'replied', 'closed'])) disabled @endif> Mới</option>
                                    <option value="processing" {{ $contact->status == 'processing' ? 'selected' : '' }} @if(in_array($contact->status, ['replied', 'closed'])) disabled @endif> Đang xử lý</option>
                                    <option value="replied" {{ $contact->status == 'replied' ? 'selected' : '' }} @if($contact->status == 'closed') disabled @endif> Đã phản hồi</option>
                                    <option value="closed" {{ $contact->status == 'closed' ? 'selected' : '' }}> Đã đóng</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Nội dung</label>
                                <textarea readonly name="note" id="note" class="form-control"
                                    rows="5">{{ $contact->note }}</textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
