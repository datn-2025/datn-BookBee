@extends('layouts.backend')
@section('title', 'Quản lý liên hệ')

@section('content')
    <div class="container-fluid py-4">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quản lý liên hệ</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">admin</a></li>
                            <li class="breadcrumb-item active">Liên hệ</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách liên hệ</h5>
                    </div>

                    <!-- Bộ lọc liên hệ -->
                    <div class="card-body border-bottom py-4">
                        <form method="GET" action="{{ route('admin.contacts.index') }}">
                            <div class="row g-3 align-items-center">
                                <!-- Tìm kiếm -->
                                <div class="col-lg-4">
                                    <input type="text" name="search" class="form-control ps-4"
                                        placeholder="🔍 Tìm kiếm liên hệ..." value="{{ request('search') }}">
                                </div>

                                <!-- Trạng thái -->
                                <div class="col-lg-auto" style="min-width: 190px;">
                                    <select class="form-select" name="status">
                                        <option value="">📁 Tất cả trạng thái</option>
                                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Mới</option>
                                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                            Đang xử lý</option>
                                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Đã phản
                                            hồi</option>
                                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng
                                        </option>
                                    </select>
                                </div>

                                <!-- Nút lọc + đặt lại -->
                                <div class="col-lg-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4" style="min-width: 130px;">
                                        <i class="ri-filter-3-line me-1"></i> Lọc
                                    </button>
                                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary px-4"
                                        style="min-width: 130px;">
                                        <i class="ri-refresh-line me-1"></i> Đặt lại
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Bảng danh sách liên hệ -->
                    <div class="card-body">
                        <div class="table-responsive table-card mb-4">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-muted">
                                        <th scope="col" style="width: 50px;">STT</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Nội dung phản hồi</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col">Ngày gửi</th>
                                        <th scope="col" style="width: 150px;">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contacts as $index => $contact)
                                        <tr>
                                            <td class="text-center">{{ $contacts->firstItem() + $index }}</td>
                                            <td class="fw-semibold">{{ $contact->name }}</td>
                                            <td>{{ $contact->email }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;">
                                                    {{ $contact->note ?? 'Không có ghi chú' }}
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusTranslations = [
                                                        'new' => 'Mới',
                                                        'processing' => 'Đang xử lý',
                                                        'replied' => 'Đã phản hồi',
                                                        'closed' => 'Đã đóng',
                                                    ];
                                                @endphp
                                                <span class="badge {{ 
                                                    $contact->status == 'new' ? 'bg-primary' :
                                                    ($contact->status == 'processing' ? 'bg-warning text-dark' :
                                                    ($contact->status == 'replied' ? 'bg-success' : 'bg-secondary')) }}">
                                                    {{ $statusTranslations[$contact->status] ?? ucfirst($contact->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-sm btn-info" title="Xem">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStatusModal{{ $contact->id }}" title="Sửa">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal{{ $contact->id }}" title="Gửi phản hồi">
                                                        <i class="ri-mail-send-line"></i>
                                                    </button>
                                                    <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa liên hệ này?')" title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal chỉnh sửa trạng thái -->
                                        <div class="modal fade" id="editStatusModal{{ $contact->id }}" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Chỉnh sửa trạng thái liên hệ</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
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
                                                                <textarea readonly name="note" id="note" class="form-control" rows="5">{{ $contact->note }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-success submit-btn">Lưu thay đổi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal gửi email phản hồi -->
                                        <div class="modal fade" id="replyModal{{ $contact->id }}" tabindex="-1" aria-labelledby="replyModalLabel{{ $contact->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.contacts.reply', $contact->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Gửi email phản hồi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label>Email người nhận</label>
                                                                <input type="email" class="form-control" name="email" value="{{ $contact->email }}" readonly>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label>Nội dung phản hồi</label>
                                                                <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success submit-btn">Gửi</button>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                Không có liên hệ nào.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Phân trang -->
                        <div class="d-flex justify-content-end mt-4">
                            {{ $contacts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Ưu tiên dùng event.submitter nếu trình duyệt hỗ trợ
            var btn = e.submitter || form.querySelector('.submit-btn');
            if (btn) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...';
                setTimeout(function() {
                    btn.disabled = true;
                }, 10); // Đảm bảo innerHTML cập nhật trước khi disable
            }
        });
    });
});
</script>
@endpush