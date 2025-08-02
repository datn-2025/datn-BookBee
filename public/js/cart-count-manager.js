/**
 * Global Cart Count Manager
 * Handles updating cart badge count across the site
 */

window.CartCountManager = {
    // Cache cart count elements
    countElements: null,
    
    // Initialize cart count manager
    init() {
        this.countElements = document.querySelectorAll('.cart-count, [data-cart-count]');
        this.bindEvents();
    },

    // Bind events for cart updates
    bindEvents() {
        // Listen for cart update events
        document.addEventListener('cartUpdated', (event) => {
            if (event.detail && typeof event.detail.count !== 'undefined') {
                this.updateCount(event.detail.count);
            } else {
                // If no count provided, refresh from server
                this.refreshFromServer();
            }
        });

        // Listen for cart item added events
        document.addEventListener('cartItemAdded', (event) => {
            if (event.detail && typeof event.detail.count !== 'undefined') {
                this.updateCount(event.detail.count);
            } else {
                this.refreshFromServer();
            }
        });

        // Listen for cart item removed events  
        document.addEventListener('cartItemRemoved', (event) => {
            if (event.detail && typeof event.detail.count !== 'undefined') {
                this.updateCount(event.detail.count);
            } else {
                this.refreshFromServer();
            }
        });
    },

    // Update cart count display
    updateCount(newCount) {
        const count = parseInt(newCount) || 0;
        
        // Find cart link element
        const cartLink = document.querySelector('a[href*="/cart"]');
        if (!cartLink) return;

        // Find or create badge element
        let badge = cartLink.querySelector('.cart-badge, span[class*="absolute"]');
        
        if (count > 0) {
            if (!badge) {
                // Create new badge
                badge = document.createElement('span');
                badge.className = 'absolute -top-1 -right-1 h-4 w-4 bg-black text-white text-xs rounded-full flex items-center justify-center cart-badge';
                cartLink.appendChild(badge);
            }
            
            // Update badge text with animation
            badge.style.animation = 'bounce 0.3s ease';
            badge.textContent = count > 99 ? '99+' : count.toString();
            
            // Remove animation after completion
            setTimeout(() => {
                badge.style.animation = '';
            }, 300);
            
            // Make sure badge is visible
            badge.style.display = 'flex';
        } else {
            // Hide badge when count is 0
            if (badge) {
                badge.style.display = 'none';
            }
        }
    },

    // Refresh cart count from server
    async refreshFromServer() {
        try {
            const response = await fetch('/cart/count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (typeof data.count !== 'undefined') {
                    this.updateCount(data.count);
                }
            }
        } catch (error) {
            console.error('Failed to refresh cart count:', error);
        }
    },

    // Get current displayed count
    getCurrentCount() {
        const cartLink = document.querySelector('a[href*="/cart"]');
        if (!cartLink) return 0;
        
        const badge = cartLink.querySelector('.cart-badge, span[class*="absolute"]');
        if (!badge || badge.style.display === 'none') return 0;
        
        const text = badge.textContent.trim();
        if (text === '99+') return 99;
        
        return parseInt(text) || 0;
    },

    // Increment count (for adding items)
    incrementCount(amount = 1) {
        const currentCount = this.getCurrentCount();
        this.updateCount(currentCount + amount);
    },

    // Decrement count (for removing items)
    decrementCount(amount = 1) {
        const currentCount = this.getCurrentCount();
        this.updateCount(Math.max(0, currentCount - amount));
    }
};

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        CartCountManager.init();
    });
} else {
    CartCountManager.init();
}

// Add CSS for bounce animation if not already present
if (!document.querySelector('#cart-count-animations')) {
    const style = document.createElement('style');
    style.id = 'cart-count-animations';
    style.textContent = `
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0,-8px,0);
            }
            70% {
                transform: translate3d(0,-4px,0);
            }
            90% {
                transform: translate3d(0,-2px,0);
            }
        }
    `;
    document.head.appendChild(style);
}
