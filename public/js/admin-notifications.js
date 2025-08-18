// Admin Notification System

// Toggle notification dropdown
function toggleAdminNotificationDropdown() {
    console.log('toggleAdminNotificationDropdown called'); // Debug log
    const dropdown = document.getElementById('admin-notification-dropdown');
    
    if (!dropdown) {
        console.error('Dropdown element not found!');
        return;
    }
    
    const isVisible = dropdown.style.display === 'block';
    console.log('Current visibility:', isVisible); // Debug log
    
    if (isVisible) {
        hideAdminNotificationDropdown();
    } else {
        showAdminNotificationDropdown();
    }
}

// Show notification dropdown
function showAdminNotificationDropdown() {
    console.log('showAdminNotificationDropdown called'); // Debug log
    const dropdown = document.getElementById('admin-notification-dropdown');
    if (dropdown) {
        dropdown.style.display = 'block';
        dropdown.style.opacity = '1';
        dropdown.style.visibility = 'visible';
        dropdown.style.transform = 'translateY(0)';
        dropdown.style.pointerEvents = 'auto';
        console.log('Dropdown shown'); // Debug log
    }
}

// Hide notification dropdown
function hideAdminNotificationDropdown() {
    console.log('hideAdminNotificationDropdown called'); // Debug log
    const dropdown = document.getElementById('admin-notification-dropdown');
    if (dropdown) {
        dropdown.style.display = 'none';
        dropdown.style.opacity = '0';
        dropdown.style.visibility = 'hidden';
        dropdown.style.transform = 'translateY(-8px)';
        dropdown.style.pointerEvents = 'none';
        console.log('Dropdown hidden'); // Debug log
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.admin-notification-dropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        hideAdminNotificationDropdown();
    }
});

// Add notification to admin dropdown
function addAdminNotificationToDropdown(notification) {
    const notificationList = document.getElementById('admin-notification-list');
    const notificationCount = document.getElementById('admin-notification-count');
    const notificationBadge = document.getElementById('admin-notification-badge');
    
    if (!notificationList || !notificationCount || !notificationBadge) {
        return;
    }
    
    // Remove empty state if exists
    const emptyState = notificationList.querySelector('div[style*="text-align: center"]');
    if (emptyState) {
        emptyState.remove();
    }
    
    // Create notification element
    const notificationElement = document.createElement('div');
    notificationElement.className = 'admin-notification-item';
    notificationElement.style.cssText = `
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s ease;
        cursor: pointer;
        opacity: 0;
        transform: translateY(-10px);
    `;

        let url = '#';
        if (notification.type === 'order_status_updated' || notification.type === 'refund_request' || notification.type === 'order_cancelled') {
            url = `/orders/${notification.type_id}`;
        } else if (notification.type === 'wallet_withdrawn' || notification.type === 'wallet_deposited') {
            url = `/wallet`;
        }
    
    notificationElement.innerHTML = `
        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
            <div style="flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 50%; background-color: ${getAdminNotificationColor(notification.type)}; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 1rem; height: 1rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                    ${getAdminNotificationIcon(notification.type)}
                </svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <h6 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 600; color: #111827; line-height: 1.25;">
                    ${notification.title}
                </h6>
                <p style="margin: 0 0 0.5rem 0; font-size: 0.75rem; color: #6b7280; line-height: 1.4;">
                    ${notification.message}
                </p>
                <span style="font-size: 0.75rem; color: #9ca3af;">
                    ${notification.time}
                </span>
            </div>
        </div>
    `;
    
    // Add hover effect
    notificationElement.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#f9fafb';
    });
    
    notificationElement.addEventListener('mouseleave', function() {
        this.style.backgroundColor = 'transparent';
    });
    
    // Insert at the beginning
    notificationList.insertBefore(notificationElement, notificationList.firstChild);
    
    // Animate in
    setTimeout(() => {
        notificationElement.style.opacity = '1';
        notificationElement.style.transform = 'translateY(0)';
    }, 10);
    
    // Limit to 5 notifications for admin
    const notifications = notificationList.querySelectorAll('.admin-notification-item');
    if (notifications.length > 5) {
        notifications[notifications.length - 1].remove();
    }
    
    // Update counter and badge
    updateAdminNotificationCounter();
}

// Update notification counter and badge
function updateAdminNotificationCounter() {
    const notificationList = document.getElementById('admin-notification-list');
    const notificationCount = document.getElementById('admin-notification-count');
    const notificationBadge = document.getElementById('admin-notification-badge');
    
    if (!notificationList || !notificationCount || !notificationBadge) {
        return;
    }
    
    const count = notificationList.querySelectorAll('.admin-notification-item').length;
    
    if (count > 0) {
        notificationCount.textContent = `${count} thông báo mới`;
        notificationBadge.textContent = count > 99 ? '99+' : count;
        notificationBadge.style.display = 'flex';
    } else {
        notificationCount.textContent = '0 thông báo mới';
        notificationBadge.style.display = 'none';
        
        // Show empty state
        notificationList.innerHTML = `
            <div style="padding: 2rem 1rem; text-align: center; color: #6b7280;">
                <svg style="height: 3rem; width: 3rem; margin: 0 auto 1rem; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p style="margin: 0; font-size: 0.875rem;">Chưa có thông báo nào</p>
            </div>
        `;
    }
}

// Get notification color based on type for admin
function getAdminNotificationColor(type) {
    switch (type) {
        case 'new_order':
            return '#10b981'; // green
        case 'order_cancelled':
            return '#ef4444'; // red
        case 'refund_request':
            return '#f59e0b'; // yellow
        case 'low_stock':
            return '#f97316'; // orange
        case 'new_user':
            return '#3b82f6'; // blue
        case 'system':
            return '#6b7280'; // gray
        case 'payment':
            return '#8b5cf6'; // purple
        default:
            return '#6366f1'; // indigo
    }
}

// Get notification icon based on type for admin
function getAdminNotificationIcon(type) {
    switch (type) {
        case 'new_order':
            return '<path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>';
        case 'order_cancelled':
            return '<path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
        case 'refund_request':
            return '<path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>';
        case 'low_stock':
            return '<path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>';
        case 'new_user':
            return '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>';
        case 'payment':
            return '<path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>';
        case 'system':
            return '<path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
        default:
            return '<path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>';
    }
}

// Listen for admin notifications
// function listenForAdminNotifications() {
//     if (typeof window.Echo !== 'undefined' && window.userRole === 'admin') {
//         // Listen for new orders
//         window.Echo.channel('admin-orders')
//             .listen('NewOrderCreated', (e) => {
//                 console.log('Admin notification - New order:', e);
                
//                 addAdminNotificationToDropdown({
//                     title: 'Đơn hàng mới',
//                     message: `Đơn hàng #${e.order.id} từ ${e.order.customer_name}`,
//                     time: 'Vừa xong',
//                     type: 'new_order'
//                 });
                
//                 // Show browser notification
//                 showAdminNotification(
//                     'Đơn hàng mới',
//                     `Đơn hàng #${e.order.id} từ ${e.order.customer_name}`,
//                     'new_order'
//                 );
                
//                 // Play notification sound
//                 playAdminNotificationSound();
//             });
            
//         // Listen for order cancellations
//         window.Echo.channel('admin-orders')
//             .listen('OrderCancelled', (e) => {
//                 console.log('Admin notification - Order cancelled:', e);
                
//                 addAdminNotificationToDropdown({
//                     title: 'Đơn hàng bị hủy',
//                     message: `Đơn hàng #${e.order.id} đã bị hủy`,
//                     time: 'Vừa xong',
//                     type: 'order_cancelled'
//                 });
                
//                 showAdminNotification(
//                     'Đơn hàng bị hủy',
//                     `Đơn hàng #${e.order.id} đã bị hủy`,
//                     'order_cancelled'
//                 );
                
//                 playAdminNotificationSound();
//             });
            
//         // Listen for refund requests
//         window.Echo.channel('admin-refunds')
//             .listen('RefundRequested', (e) => {
//                 console.log('Admin notification - Refund request:', e);
                
//                 addAdminNotificationToDropdown({
//                     title: 'Yêu cầu hoàn tiền',
//                     message: `Yêu cầu hoàn tiền cho đơn hàng #${e.order.id}`,
//                     time: 'Vừa xong',
//                     type: 'refund_request'
//                 });
                
//                 showAdminNotification(
//                     'Yêu cầu hoàn tiền',
//                     `Yêu cầu hoàn tiền cho đơn hàng #${e.order.id}`,
//                     'refund_request'
//                 );
                
//                 playAdminNotificationSound();
//             });
            
//         // Listen for low stock alerts
//         window.Echo.channel('admin-inventory')
//             .listen('LowStockAlert', (e) => {
//                 console.log('Admin notification - Low stock:', e);
                
//                 addAdminNotificationToDropdown({
//                     title: 'Cảnh báo tồn kho',
//                     message: `Sách "${e.book.title}" sắp hết hàng (còn ${e.stock} cuốn)`,
//                     time: 'Vừa xong',
//                     type: 'low_stock'
//                 });
                
//                 showAdminNotification(
//                     'Cảnh báo tồn kho',
//                     `Sách "${e.book.title}" sắp hết hàng`,
//                     'low_stock'
//                 );
                
//                 playAdminNotificationSound();
//             });
            
//         // Listen for new user registrations
//         window.Echo.channel('admin-users')
//             .listen('NewUserRegistered', (e) => {
//                 console.log('Admin notification - New user:', e);
                
//                 addAdminNotificationToDropdown({
//                     title: 'Người dùng mới',
//                     message: `${e.user.name} vừa đăng ký tài khoản`,
//                     time: 'Vừa xong',
//                     type: 'new_user'
//                 });
                
//                 showAdminNotification(
//                     'Người dùng mới',
//                     `${e.user.name} vừa đăng ký tài khoản`,
//                     'new_user'
//                 );
                
//                 playAdminNotificationSound();
//             });
//     }
// }

// Show browser notification for admin
function showAdminNotification(title, message, type = 'info') {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: message,
            icon: '/favicon.ico',
            tag: `admin-${type}-notification`
        });
    }
}

// Play notification sound for admin
function playAdminNotificationSound() {
    try {
        const audio = new Audio('/sounds/admin-notification.mp3');
        audio.volume = 0.7;
        audio.play().catch(e => console.log('Could not play admin notification sound:', e));
    } catch (e) {
        console.log('Admin notification sound not available:', e);
    }
}

// Request notification permission for admin
function requestAdminNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Load admin notifications from database
async function loadAdminNotificationsFromDatabase() {
    // try {
        const response = await fetch('/api/admin/notifications/all', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            displayAdminNotificationsInDropdown(result.notifications);
            updateAdminNotificationBadge(result.unread_count);
        } else {
            console.error('Lỗi khi tải thông báo admin:', result.message);
        }
    // } catch (error) {
    //     console.error('Lỗi khi gọi API thông báo admin:', error);
    // }
}

// Display admin notifications in dropdown
function displayAdminNotificationsInDropdown(notifications) {
    const notificationList = document.getElementById('admin-notification-list');
    const notificationCount = document.getElementById('admin-notification-count');
    
    if (!notificationList || !notificationCount) {
        return;
    }

    // Clear existing notifications
    notificationList.innerHTML = '';

    if (notifications.length === 0) {
        // Show empty state
        notificationList.innerHTML = `
            <div style="padding: 2rem 1rem; text-align: center; color: #6b7280;">
                <svg style="height: 3rem; width: 3rem; margin: 0 auto 1rem; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p style="margin: 0; font-size: 0.875rem;">Chưa có thông báo nào</p>
            </div>
        `;
        notificationCount.textContent = '0 thông báo mới';
        return;
    }

    // Display notifications (max 5 for admin)
    notifications.forEach((notification, index) => {
        const notificationElement = document.createElement('div');
        notificationElement.className = 'admin-notification-item';
        notificationElement.style.cssText = `
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s ease;
            cursor: pointer;
            ${!notification.is_read ? 'background-color: #fef3c7;' : ''}
        `;
        
        let url = '#';
        if (notification.type === 'order_created' || notification.type === 'order_cancelled') {
            url = `/admin/orders/show/${notification.type_id}`;
        } else if (notification.type === 'refund_request') {
            url = `/admin/refunds/${notification.type_id}`;
        } else if (notification.type === 'product_low_stock') {
            url = `/admin/books/${notification.type_id}`;
        } else if (notification.type === 'new_user') {
            url = `/admin/users/${notification.type_id}`;
        }
        
        notificationElement.innerHTML = `
            <a href="${url}" style="text-decoration: none; color: inherit; display: block;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 50%; background-color: ${getAdminNotificationColor(notification.type)}; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1rem; height: 1rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                            ${getAdminNotificationIcon(notification.type)}
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h6 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 600; color: #111827; line-height: 1.25;">
                            ${notification.title}
                        </h6>
                        <p style="margin: 0 0 0.5rem 0; font-size: 0.75rem; color: #6b7280; line-height: 1.4;">
                            ${notification.message}
                        </p>
                        <span style="font-size: 0.75rem; color: #9ca3af;">
                            ${notification.time_ago}
                        </span>
                    </div>
                    ${!notification.is_read ? '<div style="width: 8px; height: 8px; background-color: #f59e0b; border-radius: 50%; flex-shrink: 0; margin-top: 0.5rem;"></div>' : ''}
                </div>
            </a>
        `;
        
        // Add hover effect
        notificationElement.addEventListener('mouseenter', function() {
            this.style.backgroundColor = notification.is_read ? '#f9fafb' : '#fef3c7';
        });
        
        notificationElement.addEventListener('mouseleave', function() {
            this.style.backgroundColor = notification.is_read ? 'transparent' : '#fef3c7';
        });
        
        // Mark as read when clicked
        notificationElement.addEventListener('click', function() {
            if (!notification.is_read) {
                markAdminNotificationAsRead(notification.id);
            }
        });
        
        notificationList.appendChild(notificationElement);
    });

    // Update counter
    const totalCount = notifications.length;
    notificationCount.textContent = `${totalCount} thông báo${totalCount > 1 ? '' : ''}`;
}

// Update admin notification badge
function updateAdminNotificationBadge(unreadCount) {
    const notificationBadge = document.getElementById('admin-notification-badge');
    
    if (!notificationBadge) {
        return;
    }

    if (unreadCount > 0) {
        notificationBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
        notificationBadge.style.display = 'flex';
    } else {
        notificationBadge.style.display = 'none';
    }
}

// Mark admin notification as read
async function markAdminNotificationAsRead(notificationId) {
    try {
        const response = await fetch(`/api/admin/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (response.ok) {
            // Reload notifications to update UI
            loadAdminNotificationsFromDatabase();
        }
    } catch (error) {
        console.error('Lỗi khi đánh dấu thông báo admin đã đọc:', error);
    }
}

// Initialize admin notifications
function initializeAdminNotifications() {
    // Request notification permission
    requestAdminNotificationPermission();
    
    // Load notifications from database
    loadAdminNotificationsFromDatabase();
    
    // Listen for admin notifications if user is admin
    if (typeof window.userRole !== 'undefined' && window.userRole === 'admin') {
        listenForAdminNotifications();
    }
    
    // Reload notifications every 30 seconds
    setInterval(loadAdminNotificationsFromDatabase, 30000);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're in admin panel
    if (window.location.pathname.startsWith('/admin')) {
        initializeAdminNotifications();
    }
});

// Export functions for global access
window.toggleAdminNotificationDropdown = toggleAdminNotificationDropdown;
window.addAdminNotificationToDropdown = addAdminNotificationToDropdown;
window.loadAdminNotificationsFromDatabase = loadAdminNotificationsFromDatabase;