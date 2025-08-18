// Frontend Notification System

// Toggle notification dropdown
function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    const isVisible = dropdown.style.opacity === '1';
    
    if (isVisible) {
        hideNotificationDropdown();
    } else {
        showNotificationDropdown();
    }
}

// Show notification dropdown
function showNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    dropdown.style.opacity = '1';
    dropdown.style.visibility = 'visible';
    dropdown.style.transform = 'translateY(0)';
    dropdown.style.pointerEvents = 'auto';
}

// Hide notification dropdown
function hideNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    dropdown.style.opacity = '0';
    dropdown.style.visibility = 'hidden';
    dropdown.style.transform = 'translateY(-8px)';
    dropdown.style.pointerEvents = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.notification-dropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        hideNotificationDropdown();
    }
});

// Add notification to frontend dropdown
function addFrontendNotificationToDropdown(notification) {
    const notificationList = document.getElementById('notification-list');
    const notificationCount = document.getElementById('notification-count');
    const notificationBadge = document.getElementById('notification-badge');
    
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
    notificationElement.className = 'notification-item';
    notificationElement.style.cssText = `
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s ease;
        cursor: pointer;
        opacity: 0;
        transform: translateY(-10px);
    `;
    
    notificationElement.innerHTML = `
        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
            <div style="flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 50%; background-color: ${getNotificationColor(notification.type)}; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 1rem; height: 1rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                    ${getNotificationIcon(notification.type)}
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
    
    // Limit to 3 notifications
    const notifications = notificationList.querySelectorAll('.notification-item');
    if (notifications.length > 3) {
        notifications[notifications.length - 1].remove();
    }
    
    // Update counter and badge
    updateFrontendNotificationCounter();
}

// Update notification counter and badge
function updateFrontendNotificationCounter() {
    const notificationList = document.getElementById('notification-list');
    const notificationCount = document.getElementById('notification-count');
    const notificationBadge = document.getElementById('notification-badge');
    
    if (!notificationList || !notificationCount || !notificationBadge) {
        return;
    }
    
    const count = notificationList.querySelectorAll('.notification-item').length;
    
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

// Get notification color based on type
function getNotificationColor(type) {
    switch (type) {
        case 'order':
            return '#10b981'; // green
        case 'payment':
            return '#3b82f6'; // blue
        case 'shipping':
            return '#f59e0b'; // yellow
        case 'system':
            return '#6b7280'; // gray
        default:
            return '#8b5cf6'; // purple
    }
}

// Get notification icon based on type
function getNotificationIcon(type) {
    switch (type) {
        case 'order':
            return '<path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>';
        case 'payment':
            return '<path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>';
        case 'shipping':
            return '<path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-6a2 2 0 00-2-2h-8a2 2 0 00-2 2v6a2 2 0 002 2z"/>';
        case 'system':
            return '<path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
        default:
            return '<path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>';
    }
}

// Listen for customer notifications (for logged-in customers)
function listenForCustomerNotifications() {
    if (typeof window.Echo !== 'undefined' && window.userRole === 'user') {
        window.Echo.channel('customer-orders')
            .listen('OrderStatusUpdated', (e) => {
                console.log('Customer notification received:', e);
                
                // Show browser notification
                showNotification(
                    'Cập nhật đơn hàng',
                    `Đơn hàng #${e.order.id} đã được cập nhật trạng thái: ${e.order.status}`,
                    'order'
                );
                
                // Reload notifications from database to get the latest data
                setTimeout(() => {
                    loadNotificationsFromDatabase();
                }, 1000);
                
                // Play notification sound
                playNotificationSound();
            });
    }
}

// Show browser notification
function showNotification(title, message, type = 'info') {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: message,
            icon: '/favicon.ico',
            tag: 'order-notification'
        });
    }
}

// Play notification sound
function playNotificationSound() {
    try {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.5;
        audio.play().catch(e => console.log('Could not play notification sound:', e));
    } catch (e) {
        console.log('Notification sound not available:', e);
    }
}

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Load notifications from database
async function loadNotificationsFromDatabase() {
    try {
        const response = await fetch('/api/notifications', {
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
            displayNotificationsInDropdown(result.data.notifications);
            updateNotificationBadge(result.data.unread_count);
        } else {
            console.error('Lỗi khi tải thông báo:', result.message);
        }
    } catch (error) {
        console.error('Lỗi khi gọi API thông báo:', error);
    }
}

// Display notifications in dropdown (max 3 with scroll)
function displayNotificationsInDropdown(notifications) {
    const notificationList = document.getElementById('notification-list');
    const notificationCount = document.getElementById('notification-count');
    
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

    // Display notifications (max 3)
    notifications.forEach((notification, index) => {
        console.log(notification);
        
        const notificationElement = document.createElement('div');
        notificationElement.className = 'notification-item';
        notificationElement.style.cssText = `
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s ease;
            cursor: pointer;
            ${!notification.is_read ? 'background-color: #f0f9ff;' : ''}
        `;
        let url = '#';
        if (notification.type === 'order_status_updated' || notification.type === 'refund_request' || notification.type === 'order_cancelled') {
            url = `/orders/${notification.type_id}`;
        } else if (notification.type === 'wallet_withdrawn' || notification.type === 'wallet_deposited') {
            url = `/wallet`;
        }
        
        notificationElement.innerHTML = `
            <a href="${url}" style="text-decoration: none; color: inherit; display: block; padding: 0.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <div style="flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 50%; background-color: ${getNotificationColor(notification.type)}; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1rem; height: 1rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                        ${getNotificationIcon(notification.type_id)}
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
                ${!notification.is_read ? '<div style="width: 8px; height: 8px; background-color: #3b82f6; border-radius: 50%; flex-shrink: 0; margin-top: 0.5rem;"></div>' : ''}
            </div>
        </a>
    `;
        
        // Add hover effect
        notificationElement.addEventListener('mouseenter', function() {
            this.style.backgroundColor = notification.is_read ? '#f9fafb' : '#e0f2fe';
        });
        
        notificationElement.addEventListener('mouseleave', function() {
            this.style.backgroundColor = notification.is_read ? 'transparent' : '#f0f9ff';
        });
        
        // Mark as read when clicked
        notificationElement.addEventListener('click', function() {
            if (!notification.is_read) {
                markNotificationAsRead(notification.id);
            }
        });
        
        notificationList.appendChild(notificationElement);
    });

    // Add "View All" link if there are notifications
    // if (notifications.length > 0) {
    //     const viewAllElement = document.createElement('div');
    //     viewAllElement.className = 'view-all-notifications';
    //     viewAllElement.style.cssText = `
    //         padding: 0.75rem 1rem;
    //         border-top: 1px solid #e5e7eb;
    //         background-color: #f9fafb;
    //         text-align: center;
    //     `;
        
    //     viewAllElement.innerHTML = `
    //         <a href="/notifications" style="
    //             color: #3b82f6;
    //             text-decoration: none;
    //             font-size: 0.875rem;
    //             font-weight: 500;
    //             display: inline-flex;
    //             align-items: center;
    //             gap: 0.5rem;
    //             transition: color 0.2s ease;
    //         " onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#3b82f6'">
    //             <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    //                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    //                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    //             </svg>
    //             Xem tất cả thông báo
    //         </a>
    //     `;
        
    //     notificationList.appendChild(viewAllElement);
    // }

    // Update counter
    const totalCount = notifications.length;
    notificationCount.textContent = `${totalCount} thông báo${totalCount > 1 ? '' : ''}`;
}

// Update notification badge
function updateNotificationBadge(unreadCount) {
    const notificationBadge = document.getElementById('notification-badge');
    
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

// Mark notification as read
async function markNotificationAsRead(notificationId) {
    try {
        const response = await fetch(`/api/notifications/${notificationId}/read`, {
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
            loadNotificationsFromDatabase();
        }
    } catch (error) {
        console.error('Lỗi khi đánh dấu thông báo đã đọc:', error);
    }
}

// Initialize frontend notifications
function initializeFrontendNotifications() {
    // Request notification permission
    requestNotificationPermission();
    
    // Load notifications from database
    loadNotificationsFromDatabase();
    
    // Listen for customer notifications if user is logged in
    if (typeof window.userRole !== 'undefined') {
        listenForCustomerNotifications();
    }
    
    // Reload notifications every 30 seconds
    setInterval(loadNotificationsFromDatabase, 30000);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeFrontendNotifications();
});