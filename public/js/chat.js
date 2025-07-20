// Lắng nghe sự kiện realtime cho chat admin bằng JS (chỉ lắng nghe, không gửi/hiển thị)
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.conversationId !== 'undefined' && window.conversationId) {
        if (window.Echo) {
            window.Echo.channel(`bookbee.${window.conversationId}`)
                .listen('MessageSent', function (e) {
                    if (window.Livewire) {
                        window.Livewire.emit('refreshChat');
                    }
                });
        }
    }
    // Lắng nghe user-status (nếu cần)
    if (window.Echo) {
        window.Echo.channel('user-status')
            .listen('UserSessionChanged', function (e) {
                // Có thể cập nhật trạng thái user ở đây nếu muốn
                // Ví dụ: updateUserStatus(e.user);
            });
    }
});
