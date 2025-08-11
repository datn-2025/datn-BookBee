/**
 * Global Wishlist Count Manager
 * Handles updating wishlist badge count across the site
 */

window.WishlistCountManager = {
    // Initialize wishlist count manager
    init() {
        this.bindEvents();
    },

    // Bind events for wishlist updates
    bindEvents() {
        // Listen for wishlist update events
        document.addEventListener('wishlistUpdated', (event) => {
            if (event.detail && typeof event.detail.count !== 'undefined') {
                this.updateCount(event.detail.count);
            } else {
                // If no count provided, refresh from server
                this.refreshFromServer();
            }
        });

        // Listen for wishlist item added events
        document.addEventListener('wishlistItemAdded', (event) => {
            if (event.detail && typeof event.detail.count !== 'undefined') {
                this.updateCount(event.detail.count);
            } else {
                this.refreshFromServer();
            }
        });

        // Listen for wishlist item removed events  
        document.addEventListener('wishlistItemRemoved', (event) => {
            if (event.detail && typeof event.detail.count !== 'undefined') {
                this.updateCount(event.detail.count);
            } else {
                this.refreshFromServer();
            }
        });
    },

    // Update wishlist count display
    updateCount(newCount) {
        const count = parseInt(newCount) || 0;
        
        // Find wishlist link element
        const wishlistLink = document.querySelector('a[href*="/wishlist"]');
        if (!wishlistLink) return;

        // Find or create badge element
        let badge = wishlistLink.querySelector('.wishlist-badge, span[style*="background-color: #ef4444"]');
        
        if (count > 0) {
            if (!badge) {
                // Create new badge
                badge = document.createElement('span');
                badge.className = 'wishlist-badge';
                badge.style.cssText = 'position: absolute; top: -0.25rem; right: -0.25rem; height: 1rem; width: 1rem; background-color: #ef4444; color: white; font-size: 0.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center;';
                wishlistLink.appendChild(badge);
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

    // Refresh wishlist count from server
    async refreshFromServer() {
        try {
            const response = await fetch('/wishlist/count', {
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
            console.error('Failed to refresh wishlist count:', error);
        }
    },

    // Get current displayed count
    getCurrentCount() {
        const wishlistLink = document.querySelector('a[href*="/wishlist"]');
        if (!wishlistLink) return 0;
        
        const badge = wishlistLink.querySelector('.wishlist-badge, span[style*="background-color: #ef4444"]');
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
        WishlistCountManager.init();
    });
} else {
    WishlistCountManager.init();
}
