@extends('layouts.backend')

@section('title', 'Danh sách thông báo Admin')

@section('content')
{{-- <div class="page-content"> --}}
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Thông báo Admin</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Thông báo</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-bell me-2"></i>Danh sách thông báo
                            @if($unreadCount > 0)
                                <span class="badge bg-danger ms-2">{{ $unreadCount }} chưa đọc</span>
                            @endif
                        </h5>
                        @if($unreadCount > 0)
                            <button type="button" class="btn btn-sm btn-success" onclick="markAllAsRead()">
                                <i class="bx bx-check-double me-1"></i>Đánh dấu tất cả đã đọc
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($notifications->count() > 0)
                            <div class="notification-list">
                                @foreach($notifications as $data)
                                    @php
                                        // $data = json_decode($notification->data, true);
                                        $isRead = !is_null($data->read_at);
                                    @endphp
                                    <div class="notification-item {{ $isRead ? 'read' : 'unread' }}" data-id="{{ $data->id }}">
                                        <div class="d-flex align-items-start">
                                            <!-- Icon thông báo -->
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title rounded-circle {{ getNotificationIconClass($data->type) }}">
                                                        <i class="{{ getNotificationIcon($data->type) }} fs-16"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Nội dung thông báo -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="notification-title {{ $isRead ? 'text-muted' : 'text-dark' }}">
                                                            {{ $data->title ?? 'Thông báo' }}
                                                        </h6>
                                                        <p class="notification-message text-muted mb-1">
                                                            {{ $data->message ?? $data['body'] ?? 'Nội dung thông báo' }}
                                                        </p>
                                                        <small class="text-muted">
                                                            <i class="bx bx-time-five me-1"></i>
                                                            {{ \Carbon\Carbon::parse($data->created_at)->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Actions -->
                                                    <div class="notification-actions">
                                                        @if(!$isRead)
                                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                                    onclick="markAsRead('{{ $data->id }}')" 
                                                                    title="Đánh dấu đã đọc">
                                                                <i class="bx bx-check"></i>
                                                            </button>
                                                        @else
                                                            <span class="badge bg-success-subtle text-success">
                                                                <i class="bx bx-check me-1"></i>Đã đọc
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <!-- Empty state -->
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bx bx-bell-off display-4 text-muted"></i>
                                </div>
                                <h5 class="text-muted">Chưa có thông báo nào</h5>
                                <p class="text-muted">Các thông báo mới sẽ xuất hiện tại đây</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- </div> --}}
@endsection

@push('styles')
<style>
.notification-item {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f8f9ff;
    border-left: 3px solid #3b82f6;
}

.notification-item.read {
    opacity: 0.8;
}

.notification-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.notification-message {
    font-size: 0.875rem;
    line-height: 1.4;
}

.notification-actions {
    min-width: 80px;
}

.notification-list {
    max-height: 70vh;
    overflow-y: auto;
}
</style>
@endpush

@push('scripts')
<script>
// Đánh dấu một thông báo đã đọc
function markAsRead(notificationId) {
    fetch(`/admin/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật giao diện
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                notificationItem.classList.add('read');
                
                // Cập nhật nút action
                const actionBtn = notificationItem.querySelector('.notification-actions button');
                if (actionBtn) {
                    actionBtn.outerHTML = '<span class="badge bg-success-subtle text-success"><i class="bx bx-check me-1"></i>Đã đọc</span>';
                }
            }
            
            // Hiển thị thông báo thành công
            toastr.success(data.message);
            
            // Reload trang để cập nhật số lượng
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Có lỗi xảy ra khi đánh dấu thông báo');
    });
}

// Đánh dấu tất cả thông báo đã đọc
function markAllAsRead() {
    if (!confirm('Bạn có chắc chắn muốn đánh dấu tất cả thông báo là đã đọc?')) {
        return;
    }
    
    fetch('/admin/notifications/mark-all-read', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            
            // Reload trang để cập nhật
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Có lỗi xảy ra khi đánh dấu thông báo');
    });
}
</script>
@endpush

@php
// Helper functions để lấy icon và class cho từng loại thông báo
function getNotificationIcon($type) {
    switch($type) {
        case 'order_created':
            return 'bx bx-shopping-bag';
        case 'order_cancelled':
            return 'bx bx-x-circle';
        case 'refund_request_admin':
            return 'bx bx-undo';
        case 'product_low_stock':
            return 'bx bx-error';
        default:
            return 'bx bx-bell';
    }
}

function getNotificationIconClass($type) {
    switch($type) {
        case 'order_created':
            return 'bg-success-subtle text-success';
        case 'order_cancelled':
            return 'bg-danger-subtle text-danger';
        case 'refund_request_admin':
            return 'bg-warning-subtle text-warning';
        case 'product_low_stock':
            return 'bg-danger-subtle text-danger';
        case 'new_user':
            return 'bg-info-subtle text-info';
        default:
            return 'bg-primary-subtle text-primary';
    }
}
@endphp