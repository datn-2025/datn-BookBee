@extends('layouts.app')
@section('title', 'Thông báo')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="bg-blue-500 text-white p-6 rounded-t-lg">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Notifications</h1>
            <div class="bg-white text-blue-500 px-3 py-1 rounded-full text-sm font-semibold">
                {{ $unreadCount }} New
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-blue-500 border-t border-blue-400">
        <div class="flex">
            <button class="tab-button active px-6 py-3 text-white font-medium border-b-2 border-white" data-tab="all">
                All ({{ $notifications->total() }})
            </button>
            <button class="tab-button px-6 py-3 text-blue-200 font-medium hover:text-white transition-colors" data-tab="messages">
                Messages
            </button>
            <button class="tab-button px-6 py-3 text-blue-200 font-medium hover:text-white transition-colors" data-tab="alerts">
                Alerts
            </button>
        </div>
    </div>

    <!-- Notification List -->
    <div class="bg-white rounded-b-lg shadow-lg">
        <div id="all-notifications" class="tab-content active">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="notification-item border-b border-gray-100 p-4 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-blue-50 border-l-4 border-l-blue-500' }}" 
                         data-id="{{ $notification->id }}">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    @switch($notification->type)
                                        @case('order')
                                            <i class="fas fa-shopping-bag text-green-600"></i>
                                            @break
                                        @case('payment')
                                            <i class="fas fa-credit-card text-blue-600"></i>
                                            @break
                                        @case('wallet')
                                            <i class="fas fa-wallet text-yellow-600"></i>
                                            @break
                                        @case('system')
                                            <i class="fas fa-cog text-gray-600"></i>
                                            @break
                                        @default
                                            <i class="fas fa-bell text-blue-600"></i>
                                    @endswitch
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">
                                            @if($notification->type_id && $notification->type == 'order')
                                                <a href="{{ route('orders.show', $notification->type_id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                    {{ $notification->title }}
                                                </a>
                                            @elseif($notification->type_id && $notification->type == 'wallet')
                                                <a href="{{ route('account.wallet') }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                    {{ $notification->title }}
                                                </a>
                                            @else
                                                {{ $notification->title }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">{{ $notification->message }}</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <i class="far fa-clock mr-1"></i>
                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if(!$notification->read_at)
                                            <button class="mark-as-read-btn text-xs bg-blue-500 text-white px-3 py-1 rounded-full hover:bg-blue-600 transition-colors" 
                                                    data-id="{{ $notification->id }}">
                                                Đánh dấu đã đọc
                                            </button>
                                        @else
                                            <span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                                Đã đọc
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $notifications->links() }}
                    </div>
                @endif
                
                <!-- Mark All as Read Button -->
                @if($unreadCount > 0)
                    <div class="p-4 border-t border-gray-200 text-center">
                        <button id="mark-all-read-btn" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            Đánh dấu tất cả đã đọc ({{ $unreadCount }})
                        </button>
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bell text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có thông báo nào</h3>
                    <p class="text-gray-500">Các thông báo mới sẽ xuất hiện ở đây</p>
                </div>
            @endif
        </div>
        
        <!-- Messages Tab Content -->
        <div id="messages-notifications" class="tab-content hidden">
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có tin nhắn nào</h3>
                <p class="text-gray-500">Tin nhắn sẽ xuất hiện ở đây</p>
            </div>
        </div>
        
        <!-- Alerts Tab Content -->
        <div id="alerts-notifications" class="tab-content hidden">
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có cảnh báo nào</h3>
                <p class="text-gray-500">Cảnh báo sẽ xuất hiện ở đây</p>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tab switching
    $('.tab-button').click(function() {
        const tabName = $(this).data('tab');
        
        // Update active tab button
        $('.tab-button').removeClass('active border-b-2 border-white text-white').addClass('text-blue-200');
        $(this).addClass('active border-b-2 border-white text-white').removeClass('text-blue-200');
        
        // Show corresponding content
        $('.tab-content').removeClass('active').addClass('hidden');
        $(`#${tabName}-notifications`).removeClass('hidden').addClass('active');
    });
    
    // Mark single notification as read
    $('.mark-as-read-btn').click(function(e) {
        e.preventDefault();
        const notificationId = $(this).data('id');
        const button = $(this);
        const notificationItem = button.closest('.notification-item');
        
        $.ajax({
            url: `/notifications/${notificationId}/read`,
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update UI
                    notificationItem.removeClass('bg-blue-50 border-l-4 border-l-blue-500');
                    button.replaceWith('<span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">Đã đọc</span>');
                    
                    // Update counters
                    updateNotificationCounters();
                    
                    showToast('Đã đánh dấu thông báo đã đọc', 'success');
                }
            },
            error: function() {
                showToast('Có lỗi xảy ra khi cập nhật thông báo', 'error');
            }
        });
    });
    
    // Mark all notifications as read
    $('#mark-all-read-btn').click(function(e) {
        e.preventDefault();
        const button = $(this);
        
        $.ajax({
            url: '/notifications/mark-all-read',
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update UI
                    $('.notification-item').removeClass('bg-blue-50 border-l-4 border-l-blue-500');
                    $('.mark-as-read-btn').replaceWith('<span class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded-full">Đã đọc</span>');
                    button.hide();
                    
                    // Update header
                    $('.bg-blue-500 .bg-white').text('0 New');
                    
                    showToast('Đã đánh dấu tất cả thông báo đã đọc', 'success');
                }
            },
            error: function() {
                showToast('Có lỗi xảy ra khi cập nhật thông báo', 'error');
            }
        });
    });
    
    // Update notification counters
    function updateNotificationCounters() {
        const unreadCount = $('.notification-item.bg-blue-50').length;
        $('.bg-blue-500 .bg-white').text(unreadCount + ' New');
        
        if (unreadCount === 0) {
            $('#mark-all-read-btn').hide();
        }
    }
    
    // Show toast notification
    function showToast(message, type = 'info') {
        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        const toast = $(`
            <div id="${toastId}" class="${bgColor} text-white px-6 py-3 rounded-lg shadow-lg mb-2 transform translate-x-full transition-transform duration-300">
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button class="ml-4 text-white hover:text-gray-200" onclick="$('#${toastId}').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `);
        
        $('#toast-container').append(toast);
        
        // Animate in
        setTimeout(() => {
            toast.removeClass('translate-x-full');
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.addClass('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
});
</script>
@endpush