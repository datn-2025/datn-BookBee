/**
 * Real-time Notifications với Laravel Echo và Pusher
 * Xử lý thông báo cho admin và khách hàng
 */

// Đợi Echo được khởi tạo
function waitForEcho(callback, maxAttempts = 10, attempt = 1) {
    if (typeof window.Echo !== 'undefined' && window.Echo) {
        console.log('✅ Laravel Echo đã sẵn sàng cho thông báo realtime');
        callback();
    } else if (attempt < maxAttempts) {
        console.log(`⏳ Đang đợi Echo khởi tạo... (${attempt}/${maxAttempts})`);
        setTimeout(() => waitForEcho(callback, maxAttempts, attempt + 1), 500);
    } else {
        console.error('❌ Laravel Echo không thể khởi tạo sau', maxAttempts, 'lần thử');
    }
}

/**
 * Hiển thị thông báo toast
 * @param {string} type - Loại thông báo (success, info, warning, error)
 * @param {string} title - Tiêu đề thông báo
 * @param {string} message - Nội dung thông báo
 */
function showNotification(type, title, message) {
    // Sử dụng toastr nếu có
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        toastr[type](message, title);
    }
    // Sử dụng SweetAlert2 nếu có
    else if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
            title: title,
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
    }
    // Fallback sử dụng alert
    else {
        alert(title + ': ' + message);
    }
}

/**
 * Phát âm thanh thông báo
 */
function playNotificationSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();

        // oscillator chính
        const osc1 = audioContext.createOscillator();
        osc1.type = "sine";
        osc1.frequency.setValueAtTime(880, audioContext.currentTime); // A5

        // oscillator phụ để tạo hòa âm
        const osc2 = audioContext.createOscillator();
        osc2.type = "triangle";
        osc2.frequency.setValueAtTime(1760, audioContext.currentTime); // A6 (1 octave trên)

        const gainNode = audioContext.createGain();
        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 1);

        // Kết nối
        osc1.connect(gainNode);
        osc2.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Start/Stop
        osc1.start();
        osc2.start();
        osc1.stop(audioContext.currentTime + 1);
        osc2.stop(audioContext.currentTime + 1);
    } catch (error) {
        console.log("Không thể phát âm thanh thông báo:", error);
    }
}


/**
 * Cập nhật số lượng thông báo trên badge
 * @param {number} count - Số lượng thông báo chưa đọc
 */
function updateNotificationBadge(count = null) {
    const badge = document.querySelector('.topbar-badge');
    if (!badge) {
        console.warn('⚠️ Không tìm thấy badge thông báo với selector .topbar-badge');
        return;
    }
    
    if (count !== null) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'block' : 'none';
    } else {
        // Tăng số hiện tại lên 1
        const currentCount = parseInt(badge.textContent) || 0;
        const newCount = currentCount + 1;
        badge.textContent = newCount;
        badge.style.display = 'block';
    }
}

/**
 * Thêm thông báo mới vào dropdown
 * @param {Object} notification - Thông tin thông báo
 * @param {string} notification.title - Tiêu đề thông báo
 * @param {string} notification.message - Nội dung thông báo
 * @param {string} notification.time - Thời gian thông báo
 * @param {string} notification.icon - Icon class (bx-*)
 * @param {string} notification.type - Loại thông báo (success, info, warning, danger)
 */
function addNotificationToDropdown(notification) {
    let notificationContainer = document.querySelector('#all-noti-tab [data-simplebar]');
    if (!notificationContainer) {
        console.warn('⚠️ Không tìm thấy container thông báo với selector #all-noti-tab [data-simplebar]');
        console.log('Đang tìm kiếm các selector khác...');
        // Thử selector khác
        const altContainer = document.querySelector('#all-noti-tab');
        if (altContainer) {
            console.log('✅ Tìm thấy container thay thế');
            notificationContainer = altContainer.querySelector('[data-simplebar]') || altContainer;
        } else {
            console.error('❌ Không tìm thấy bất kỳ container nào');
            return;
        }
    }
    
    // Tạo HTML cho thông báo mới
    const notificationHTML = `
        <div class="text-reset notification-item d-block dropdown-item position-relative new-notification">
            <div class="d-flex">
                <div class="avatar-xs me-3 flex-shrink-0">
                    <span class="avatar-title bg-${getNotificationColor(notification.type)}-subtle text-${getNotificationColor(notification.type)} rounded-circle fs-16">
                        <i class="bx ${notification.icon}"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <a href="#!" class="stretched-link">
                        <h6 class="mt-0 mb-2 lh-base">${notification.title}</h6>
                    </a>
                    <p class="mb-1 fs-13 text-muted">${notification.message}</p>
                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                        <i class="mdi mdi-clock-outline"></i> ${notification.time}
                    </p>
                </div>
                <div class="px-2 fs-15">
                    <div class="form-check notification-check">
                        <input class="form-check-input" type="checkbox" id="notification-check-${Date.now()}">
                        <label class="form-check-label" for="notification-check-${Date.now()}"></label>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Thêm thông báo mới vào đầu danh sách
    const viewAllButton = notificationContainer.querySelector('.view-all');
    if (viewAllButton) {
        viewAllButton.insertAdjacentHTML('beforebegin', notificationHTML);
    } else {
        notificationContainer.insertAdjacentHTML('beforeend', notificationHTML);
    }
    
    // Giới hạn tối đa 3 thông báo, xóa thông báo cũ nếu vượt quá
    const allNotifications = notificationContainer.querySelectorAll('.notification-item:not(.view-all)');
    if (allNotifications.length > 3) {
        // Xóa thông báo cũ nhất (không phải view-all button)
        const oldestNotification = allNotifications[allNotifications.length - 1];
        if (oldestNotification && !oldestNotification.classList.contains('view-all')) {
            oldestNotification.remove();
        }
    }
    
    // Thêm hiệu ứng fade-in cho thông báo mới
    const newNotification = notificationContainer.querySelector('.new-notification');
    if (newNotification) {
        newNotification.style.opacity = '0';
        newNotification.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            newNotification.style.transition = 'all 0.3s ease';
            newNotification.style.opacity = '1';
            newNotification.style.transform = 'translateY(0)';
            
            // Xóa class new-notification sau khi hoàn thành animation
            setTimeout(() => {
                newNotification.classList.remove('new-notification');
            }, 300);
        }, 100);
    }
    
    // Cập nhật số lượng thông báo trong header
    updateNotificationHeader();
}

/**
 * Lấy màu sắc cho loại thông báo
 * @param {string} type - Loại thông báo
 * @returns {string} - Màu sắc tương ứng
 */
function getNotificationColor(type) {
    const colors = {
        'success': 'success',
        'info': 'info', 
        'warning': 'warning',
        'danger': 'danger',
        'primary': 'primary'
    };
    return colors[type] || 'info';
}

/**
 * Cập nhật header thông báo (số lượng)
 */
function updateNotificationHeader() {
    const headerBadge = document.querySelector('.dropdown-head .badge');
    const tabBadge = document.querySelector('a[href="#all-noti-tab"]');
    
    if (headerBadge && tabBadge) {
        const currentCount = parseInt(headerBadge.textContent.replace(/\D/g, '')) || 0;
        const newCount = currentCount + 1;
        
        headerBadge.textContent = `${newCount} New`;
        tabBadge.textContent = `All (${newCount})`;
    }
}

/**
 * Lắng nghe thông báo cho Admin
 * Channel: admin-orders
 * Event: order.created
 */
function listenForAdminNotifications() {
    if (!window.Echo) return;
    
    console.log('🔊 Đang lắng nghe thông báo admin trên channel: admin-orders');
    
    // Lắng nghe thông báo đơn hàng
    // if(data.target === 'customer') return null;
    window.Echo.channel('admin-orders')
        .listen('.order.created', (data) => {
            console.log('📦 Đơn hàng mới:', data);
            // Hiển thị thông báo toast
            showNotification(
                'info',
                '🛒 Đơn hàng mới!',
                `Có đơn hàng mới từ khách hàng ${data.user_name} với giá trị ${data.total_amount}`,
                `Đơn hàng #${data.order_code} vừa được tạo lúc ${data.created_at}`
            );
            
            // Thêm thông báo vào dropdown
            addNotificationToDropdown({
                title: 'Đơn hàng mới!',
                message: `Đơn hàng từ ${data.customer_name} - ${data.total_amount}đ`,
                time: 'Vừa xong',
                icon: 'bx-shopping-bag',
                type: 'success'
            });
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            updateNotificationBadge();
        })
        .listen('.order.cancelled', (data) => {
            console.log('❌ Đơn hàng bị hủy:', data);
            // Hiển thị thông báo toast
            showNotification(
                'warning',
                '❌ Đơn hàng bị hủy!',
                `Khách hàng ${data.customer_name} đã hủy đơn hàng #${data.order_code} - Lý do: ${data.cancellation_reason}`
            );
            
            // Thêm thông báo vào dropdown
            addNotificationToDropdown({
                title: 'Đơn hàng bị hủy!',
                message: `${data.customer_name} hủy đơn #${data.order_code} - ${data.refund_amount > 0 ? 'Đã hoàn ' + data.refund_amount + 'đ' : 'Không hoàn tiền'}`,
                time: 'Vừa xong',
                icon: 'bx-x-circle',
                type: 'warning'
            });
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            updateNotificationBadge();
        })
        .listen('.refund.requested', (data) => {
            console.log('💰 Yêu cầu hoàn tiền:', data);
            if(data.target === 'customer') return null;
            // Hiển thị thông báo toast
            showNotification(
                'info',
                '💰 Yêu cầu hoàn tiền!',
                `Khách hàng ${data.customer_name} yêu cầu hoàn tiền đơn hàng #${data.order_code}`
            );
            
            // Thêm thông báo vào dropdown
            addNotificationToDropdown({
                title: 'Yêu cầu hoàn tiền!',
                message: `${data.customer_name} - Đơn #${data.order_code} - ${data.refund_amount}đ`,
                time: 'Vừa xong',
                icon: 'bx-money',
                type: 'info'
            });
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            updateNotificationBadge();
        })
        .error((error) => {
            console.error('❌ Lỗi khi lắng nghe channel admin-orders:', error);
        });
    
    console.log('🔊 Đang lắng nghe thông báo ví admin trên channel: admin-wallets');
    
    // Lắng nghe thông báo ví
    window.Echo.channel('admin-wallets')
        .listen('.wallet.deposited', (data) => {
            console.log('💰 Nạp tiền ví:', data);
            
            // Hiển thị thông báo toast
            showNotification(
                'success',
                '💰 Nạp tiền ví!',
                `${data.user_name} đã nạp ${data.amount}đ vào ví`
            );
            
            // Thêm thông báo vào dropdown
            addNotificationToDropdown({
                title: 'Nạp tiền ví!',
                message: `${data.customer_name} - ${data.amount}đ`,
                time: 'Vừa xong',
                icon: 'bx-wallet',
                type: 'success'
            });
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            updateNotificationBadge();
        })
        .listen('.wallet.withdrawn', (data) => {
            console.log('💸 Rút tiền ví:', data);
            
            // Hiển thị thông báo toast
            showNotification(
                'warning',
                '💸 Rút tiền ví!',
                `${data.user_name} đã tạo yêu cầu rút ${data.amount}đ từ ví`
            );
            
            // Thêm thông báo vào dropdown
            addNotificationToDropdown({
                title: 'Rút tiền ví!',
                message: `${data.customer_name} - ${data.amount}đ`,
                time: 'Vừa xong',
                icon: 'bx-money-withdraw',
                type: 'warning'
            });
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            updateNotificationBadge();
        })
        .error((error) => {
            console.error('❌ Lỗi khi lắng nghe channel admin-wallets:', error);
        });
}

/**
 * Lắng nghe thông báo cho Khách hàng
 * Channel: customer-{userId}
 * Event: order.status.updated
 * @param {string|number} userId - ID của khách hàng
 */
function listenForCustomerNotifications(userId) {
    if (!window.Echo || !userId) return;
    
    const channelName = `customer-${userId}`;
    console.log(`🔊 Đang lắng nghe thông báo khách hàng trên channel: ${channelName}`);
    
    window.Echo.channel(channelName)
        .listen('.order.status.updated', (data) => {
            console.log('📋 Cập nhật trạng thái đơn hàng:', data);
            
            // Hiển thị thông báo
            showNotification(
                'success',
                '📦 Cập nhật đơn hàng',
                `Đơn hàng #${data.order_code} đã chuyển từ "${data.old_status}" sang "${data.new_status}" lúc ${data.updated_at}`
            );
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            // updateNotificationBadge(newCount);
        })
        .listen('.wallet.deposited', (data) => {
            console.log('💰 Nạp tiền vào ví:', data);
            
            // Hiển thị thông báo
            showNotification(
                'success',
                '💰 Nạp tiền thành công',
                `Bạn đã nạp thành công ${new Intl.NumberFormat('vi-VN').format(data.amount)}đ vào ví lúc ${data.deposited_at}`
            );
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            // updateNotificationBadge(newCount);
        })
        .listen('.wallet.withdrawn', (data) => {
            console.log('💸 Rút tiền từ ví:', data);
            
            // Hiển thị thông báo
            showNotification(
                'info',
                '💸 Rút tiền thành công',
                `Bạn đã rút thành công ${new Intl.NumberFormat('vi-VN').format(data.amount)}đ từ ví lúc ${data.withdrawn_at}`
            );
            
            // Phát âm thanh
            playNotificationSound();
            
            // Cập nhật badge
            // updateNotificationBadge(newCount);
        })
        .error((error) => {
            console.error(`❌ Lỗi khi lắng nghe channel ${channelName}:`, error);
        });
}

/**
 * Khởi tạo thông báo realtime
 * @param {Object} config - Cấu hình
 * @param {string} config.userRole - Vai trò người dùng (admin, customer)
 * @param {string|number} config.userId - ID người dùng
 * @param {boolean} config.enableSound - Bật âm thanh thông báo
 * @param {boolean} config.enableBadge - Bật badge thông báo
 */
function initializeNotifications(config = {}) {
    if (!window.Echo) {
        console.error('❌ Laravel Echo chưa sẵn sàng');
        return;
    }
    
    console.log('🚀 Khởi tạo hệ thống thông báo realtime...');
    
    // Lắng nghe thông báo admin (so sánh không phân biệt hoa thường)
    if (config.userRole && config.userRole.toLowerCase() === 'admin') {
        listenForAdminNotifications();
    }
    
    // Lắng nghe thông báo khách hàng (so sánh không phân biệt hoa thường)
    if (config.userId && config.userRole && config.userRole.toLowerCase() === 'user') {
        listenForCustomerNotifications(config.userId);
    }
    
    console.log('✅ Hệ thống thông báo realtime đã được khởi tạo');
}

/**
 * Ngắt kết nối thông báo
 */
function disconnectNotifications() {
    if (window.Echo) {
        window.Echo.disconnect();
        console.log('🔌 Đã ngắt kết nối thông báo realtime');
    }
}

// Export functions để sử dụng ở nơi khác
window.NotificationSystem = {
    initialize: initializeNotifications,
    disconnect: disconnectNotifications,
    showNotification: showNotification,
    updateBadge: updateNotificationBadge
};

// Khởi tạo hệ thống thông báo khi DOM đã sẵn sàng
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM đã sẵn sàng, đang kiểm tra thông tin user...');
    
    // Lấy thông tin user từ meta tags
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    const userRole = document.querySelector('meta[name="user-role"]')?.content;
    
    console.log('📋 Thông tin user:', { userId, userRole });
    
    if (userId && userRole && userId !== '') {
        console.log(`🚀 Chuẩn bị khởi tạo thông báo cho ${userRole} (ID: ${userId})`);
        
        const config = {
            userId: userId,
            userRole: userRole,
            enableSound: true,
            enableBadge: true
        };
        
        // Đợi Echo được khởi tạo trước khi khởi tạo thông báo
        waitForEcho(() => {
            console.log(`🚀 Khởi tạo thông báo cho ${userRole} (ID: ${userId})`);
            initializeNotifications(config);
        });
        
    } else {
        console.log('ℹ️ Người dùng chưa đăng nhập - không khởi tạo thông báo');
        console.log('🔍 Debug: userId ="' + userId + '", userRole ="' + userRole + '"');
    }
});