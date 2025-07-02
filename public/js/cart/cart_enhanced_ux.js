/**
 * Cart Enhanced UX Module
 * Provides modern UX features like swipe gestures, animations, and improved interactions
 */

class CartEnhancedUX {
    constructor() {
        this.init();
        this.setupSwipeGestures();
        this.setupToastNotifications();
        this.setupAnimations();
        this.setupKeyboardNavigation();
        this.setupAccessibility();
    }

    init() {
        this.isLoading = false;
        this.undoTimeout = null;
        this.lastRemovedItem = null;
        this.toastContainer = this.createToastContainer();
        
        // Bind events
        this.bindEvents();
        
        console.log('Cart Enhanced UX initialized');
    }

    /**
     * Setup touch/swipe gestures for mobile
     */
    setupSwipeGestures() {
        const cartItems = document.querySelectorAll('.cart-item-card');
        
        cartItems.forEach(item => {
            let startX = 0;
            let currentX = 0;
            let isDragging = false;
            
            // Touch events
            item.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
                item.style.transition = 'none';
            });
            
            item.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                
                currentX = e.touches[0].clientX;
                const deltaX = currentX - startX;
                
                // Only allow left swipe (negative delta)
                if (deltaX < 0) {
                    const swipeAmount = Math.min(Math.abs(deltaX), 100);
                    item.style.transform = `translateX(-${swipeAmount}px)`;
                    
                    // Add visual feedback
                    if (swipeAmount > 50) {
                        item.classList.add('swipe-delete');
                    } else {
                        item.classList.remove('swipe-delete');
                    }
                }
            });
            
            item.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                
                isDragging = false;
                item.style.transition = 'all 0.3s ease';
                
                const deltaX = currentX - startX;
                
                if (Math.abs(deltaX) > 80) {
                    // Trigger delete
                    this.showSwipeDeleteConfirmation(item);
                } else {
                    // Reset position
                    item.style.transform = 'translateX(0)';
                    item.classList.remove('swipe-delete');
                }
            });
        });
    }

    /**
     * Show confirmation for swipe delete
     */
    showSwipeDeleteConfirmation(item) {
        const bookId = item.dataset.bookId;
        const productTitle = item.querySelector('.product-title').textContent;
        
        this.showToast({
            type: 'warning',
            title: 'Xác nhận xóa',
            message: `Bạn có chắc muốn xóa "${productTitle}" khỏi giỏ hàng?`,
            actions: [
                {
                    text: 'Xóa',
                    class: 'btn-danger',
                    callback: () => {
                        this.removeItemWithUndo(bookId, item);
                    }
                },
                {
                    text: 'Hủy',
                    class: 'btn-secondary',
                    callback: () => {
                        item.style.transform = 'translateX(0)';
                        item.classList.remove('swipe-delete');
                    }
                }
            ]
        });
    }

    /**
     * Remove item with undo functionality
     */
    removeItemWithUndo(bookId, itemElement) {
        const productTitle = itemElement.querySelector('.product-title').textContent;
        const quantity = itemElement.querySelector('.quantity-input').value;
        
        // Store item data for undo
        this.lastRemovedItem = {
            bookId: bookId,
            element: itemElement.cloneNode(true),
            title: productTitle,
            quantity: quantity,
            position: Array.from(itemElement.parentNode.children).indexOf(itemElement)
        };
        
        // Add loading state
        this.showLoadingOverlay(itemElement);
        
        // Remove item with animation
        itemElement.style.transition = 'all 0.5s ease';
        itemElement.style.transform = 'translateX(-100%)';
        itemElement.style.opacity = '0';
        
        setTimeout(() => {
            // Call original remove function using the proper CartProducts method
            if (window.CartProducts && typeof window.CartProducts.removeItem === 'function') {
                // Find the remove button for this item
                const removeButton = itemElement.querySelector('.adidas-cart-product-remove');
                if (removeButton) {
                    window.CartProducts.removeItem(removeButton);
                }
            } else {
                // Fallback: direct API call with proper data
                const bookFormatId = itemElement.dataset.bookFormatId || null;
                const attributeValueIds = itemElement.dataset.attributeValueIds || null;
                
                $.ajax({
                    url: '/cart/remove',
                    method: 'POST',
                    data: {
                        book_id: bookId,
                        book_format_id: bookFormatId,
                        attribute_value_ids: attributeValueIds,
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    success: (response) => {
                        if (response.success) {
                            // Show success message
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.success);
                            }
                            
                            // Reload page to update all cart data
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: (xhr) => {
                        console.error('Remove error:', xhr);
                        const response = xhr.responseJSON;
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response?.error || 'Có lỗi xảy ra khi xóa sản phẩm');
                        }
                        // Reset item state on error
                        itemElement.style.transform = '';
                        itemElement.style.opacity = '';
                    }
                });
            }
            
            // Show undo toast
            this.showUndoToast(productTitle);
        }, 500);
    }

    /**
     * Show undo toast notification
     */
    showUndoToast(productTitle) {
        this.showToast({
            type: 'success',
            title: 'Đã xóa sản phẩm',
            message: `"${productTitle}" đã được xóa khỏi giỏ hàng`,
            duration: 5000,
            actions: [
                {
                    text: 'Hoàn tác',
                    class: 'btn-primary',
                    callback: () => {
                        this.undoRemoveItem();
                    }
                }
            ]
        });
    }

    /**
     * Undo remove item
     */
    undoRemoveItem() {
        if (!this.lastRemovedItem) return;
        
        // Re-add item to cart
        if (window.cartProducts && window.cartProducts.addToCart) {
            window.cartProducts.addToCart(
                this.lastRemovedItem.bookId, 
                this.lastRemovedItem.quantity
            );
        }
        
        this.showToast({
            type: 'info',
            title: 'Đã hoàn tác',
            message: `"${this.lastRemovedItem.title}" đã được thêm lại vào giỏ hàng`
        });
        
        this.lastRemovedItem = null;
    }

    /**
     * Setup toast notification system
     */
    setupToastNotifications() {
        // Enhanced toastr configuration
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                closeButton: true,
                debug: false,
                newestOnTop: true,
                progressBar: true,
                positionClass: 'toast-bottom-right',
                preventDuplicates: true,
                onclick: null,
                showDuration: '300',
                hideDuration: '1000',
                timeOut: '3000',
                extendedTimeOut: '1000',
                showEasing: 'swing',
                hideEasing: 'linear',
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut'
            };
        }
    }

    /**
     * Create custom toast container
     */
    createToastContainer() {
        let container = document.querySelector('.custom-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'custom-toast-container';
            container.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Show custom toast notification
     */
    showToast({ type = 'info', title, message, duration = 3000, actions = [] }) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        
        const iconMap = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        const colorMap = {
            success: '#48bb78',
            error: '#e53e3e',
            warning: '#ed8936',
            info: '#667eea'
        };
        
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-header">
                    <i class="${iconMap[type]}" style="color: ${colorMap[type]}"></i>
                    <span class="toast-title">${title}</span>
                    <button class="toast-close" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="toast-body">${message}</div>
                ${actions.length > 0 ? `
                    <div class="toast-actions">
                        ${actions.map(action => `
                            <button class="btn btn-sm ${action.class}" onclick="(${action.callback})(); this.closest('.toast-notification').remove();">
                                ${action.text}
                            </button>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
            <div class="toast-progress"></div>
        `;
        
        // Add styles
        toast.style.cssText = `
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            margin-bottom: 10px;
            overflow: hidden;
            transform: translateX(100%);
            transition: all 0.3s ease;
            border-left: 4px solid ${colorMap[type]};
        `;
        
        this.toastContainer.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto remove
        if (duration > 0) {
            const progressBar = toast.querySelector('.toast-progress');
            if (progressBar) {
                progressBar.style.cssText = `
                    height: 3px;
                    background: ${colorMap[type]};
                    width: 100%;
                    animation: toast-progress ${duration}ms linear forwards;
                `;
            }
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, duration);
        }
        
        // Add CSS for progress animation
        if (!document.querySelector('#toast-progress-styles')) {
            const styles = document.createElement('style');
            styles.id = 'toast-progress-styles';
            styles.textContent = `
                @keyframes toast-progress {
                    from { width: 100%; }
                    to { width: 0%; }
                }
                .toast-content { padding: 1rem; }
                .toast-header { display: flex; align-items: center; margin-bottom: 0.5rem; }
                .toast-header i { margin-right: 0.5rem; }
                .toast-title { font-weight: 600; flex: 1; }
                .toast-close { background: none; border: none; padding: 0; margin-left: 0.5rem; cursor: pointer; }
                .toast-body { color: #4a5568; font-size: 0.9rem; margin-bottom: 0.5rem; }
                .toast-actions { display: flex; gap: 0.5rem; margin-top: 0.75rem; }
                .toast-actions .btn { font-size: 0.8rem; padding: 0.25rem 0.75rem; }
            `;
            document.head.appendChild(styles);
        }
    }

    /**
     * Setup enhanced animations
     */
    setupAnimations() {
        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'slideInUp 0.6s ease forwards';
                }
            });
        }, { threshold: 0.1 });
        
        // Observe cart items
        document.querySelectorAll('.cart-item-card').forEach(item => {
            observer.observe(item);
        });
        
        // Add animation CSS
        if (!document.querySelector('#cart-animations')) {
            const styles = document.createElement('style');
            styles.id = 'cart-animations';
            styles.textContent = `
                @keyframes slideInUp {
                    from {
                        transform: translateY(50px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
                
                @keyframes bounce {
                    0%, 20%, 53%, 80%, 100% {
                        transform: translate3d(0,0,0);
                    }
                    40%, 43% {
                        transform: translate3d(0, -10px, 0);
                    }
                    70% {
                        transform: translate3d(0, -5px, 0);
                    }
                    90% {
                        transform: translate3d(0, -2px, 0);
                    }
                }
                
                .cart-item-card {
                    opacity: 0;
                    transform: translateY(50px);
                }
                
                .quantity-btn:active {
                    animation: bounce 0.6s ease;
                }
                
                .product-image {
                    transition: transform 0.3s ease;
                }
                
                .cart-item-card:hover .product-image {
                    transform: scale(1.05);
                }
            `;
            document.head.appendChild(styles);
        }
    }

    /**
     * Setup keyboard navigation
     */
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // ESC to close modals/toasts
            if (e.key === 'Escape') {
                document.querySelectorAll('.toast-notification').forEach(toast => {
                    toast.remove();
                });
            }
            
            // Ctrl+Z for undo
            if (e.ctrlKey && e.key === 'z') {
                e.preventDefault();
                this.undoRemoveItem();
            }
        });
    }

    /**
     * Setup accessibility features
     */
    setupAccessibility() {
        // Add ARIA labels
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            if (btn.textContent.includes('+')) {
                btn.setAttribute('aria-label', 'Tăng số lượng');
            } else if (btn.textContent.includes('-')) {
                btn.setAttribute('aria-label', 'Giảm số lượng');
            }
        });
        
        document.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.setAttribute('aria-label', 'Xóa sản phẩm khỏi giỏ hàng');
        });
        
        // Add focus management
        this.setupFocusManagement();
    }

    /**
     * Setup focus management
     */
    setupFocusManagement() {
        const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const focusableContent = document.querySelectorAll(focusableElements);
                const firstFocusableElement = focusableContent[0];
                const lastFocusableElement = focusableContent[focusableContent.length - 1];
                
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusableElement) {
                        lastFocusableElement.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusableElement) {
                        firstFocusableElement.focus();
                        e.preventDefault();
                    }
                }
            }
        });
    }

    /**
     * Show loading overlay
     */
    showLoadingOverlay(element) {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="loading-spinner"></div>';
        
        element.style.position = 'relative';
        element.appendChild(overlay);
    }

    /**
     * Hide loading overlay
     */
    hideLoadingOverlay(element) {
        const overlay = element.querySelector('.loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    /**
     * Bind events
     */
    bindEvents() {
        // Enhanced quantity controls
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quantity-btn')) {
                e.target.style.animation = 'bounce 0.6s ease';
                setTimeout(() => {
                    e.target.style.animation = '';
                }, 600);
            }
        });
        
        // Enhanced remove button
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item-btn') || e.target.closest('.remove-item-btn')) {
                e.preventDefault();
                const itemCard = e.target.closest('.cart-item-card');
                const bookId = itemCard.dataset.bookId;
                this.showSwipeDeleteConfirmation(itemCard);
            }
        });
    }

    /**
     * Update cart item count with animation
     */
    updateCartCountWithAnimation(newCount) {
        const countElements = document.querySelectorAll('.cart-count, .cartitem-badge');
        
        countElements.forEach(element => {
            element.style.animation = 'bounce 0.6s ease';
            element.textContent = newCount;
            
            setTimeout(() => {
                element.style.animation = '';
            }, 600);
        });
    }

    /**
     * Smooth scroll to element
     */
    smoothScrollTo(element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.cartEnhancedUX = new CartEnhancedUX();
});

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CartEnhancedUX;
}
