/**
 * Cart Base Module - Core functionality, utilities, and configuration
 * Provides common functions and configurations used across cart modules
 */

// Global cart configuration and utilities
const CartBase = {
    // Configuration
    config: {
        debounceDelay: 500,
        toastrTimeout: 3000,
    },

    // Initialize base configurations
    init() {
        this.setupToastr();
        this.setupCSRF();
        this.checkDependencies();
    },

    // Setup Toastr configuration
    setupToastr() {
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: this.config.toastrTimeout
            };
        }
    },

    // Setup CSRF token for AJAX requests
    setupCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token && typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.getAttribute('content')
                }
            });
        }
    },

    // Check required dependencies
    checkDependencies() {
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded');
            return false;
        }
        if (typeof toastr === 'undefined') {
            console.error('Toastr is not loaded');
            return false;
        }
        return true;
    },

    // Utility Functions
    utils: {
        // Format currency in Vietnamese format
        formatCurrency(amount) {
            const numAmount = parseFloat(amount) || 0;
            return new Intl.NumberFormat('vi-VN').format(numAmount) + 'đ';
        },

        // Debounce function for performance optimization
        debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        },

        // Get CSRF token
        getCSRFToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        // Parse number from formatted text
        parseNumber(text) {
            if (!text) return 0;
            const cleanText = text.toString().replace(/[^\d]/g, '');
            return parseFloat(cleanText) || 0;
        },

        // Show loading state for element
        showLoading(element, loadingText = 'Đang xử lý...') {
            if (!element) return;
            
            element.disabled = true;
            const originalHTML = element.innerHTML;
            element.dataset.originalHTML = originalHTML;
            element.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${loadingText}`;
        },

        // Hide loading state for element
        hideLoading(element, customText = null) {
            if (!element) return;
            
            element.disabled = false;
            const originalHTML = customText || element.dataset.originalHTML;
            if (originalHTML) {
                element.innerHTML = originalHTML;
                delete element.dataset.originalHTML;
            }
        },

        // Get cart item data
        getCartItemData(cartItem) {
            if (!cartItem) return null;
            
            return {
                bookId: cartItem.dataset.bookId,
                bookFormatId: cartItem.dataset.bookFormatId,
                attributeValueIds: cartItem.dataset.attributeValueIds,
                formatName: cartItem.dataset.formatName,
                price: parseFloat(cartItem.dataset.price) || 0,
                stock: parseInt(cartItem.dataset.stock) || 0,
                element: cartItem
            };
        },

        // Show success message
        showSuccess(message) {
            if (typeof toastr !== 'undefined') {
                toastr.success(message);
            }
        },

        // Show error message
        showError(message) {
            if (typeof toastr !== 'undefined') {
                toastr.error(message);
            }
        },

        // Show warning message
        showWarning(message) {
            if (typeof toastr !== 'undefined') {
                toastr.warning(message);
            }
        },

        // Show info message
        showInfo(message) {
            if (typeof toastr !== 'undefined') {
                toastr.info(message);
            }
        },

        // Show confirmation dialog
        showConfirm(message) {
            return confirm(message);
        }
    },

    // DOM Helpers
    dom: {
        // Get element safely
        get(selector) {
            return document.querySelector(selector);
        },

        // Get all elements safely
        getAll(selector) {
            return document.querySelectorAll(selector);
        },

        // Get element by ID safely
        getById(id) {
            return document.getElementById(id);
        },

        // Add event listener safely
        on(element, event, handler) {
            if (element && typeof handler === 'function') {
                element.addEventListener(event, handler);
            }
        },

        // Remove element safely with animation
        remove(element) {
            if (element && element.parentNode) {
                // Add fade out animation
                element.style.transition = 'opacity 0.3s ease';
                element.style.opacity = '0';
                
                setTimeout(() => {
                    if (element.parentNode) {
                        element.parentNode.removeChild(element);
                    }
                }, 300);
            }
        },

        // Toggle class safely
        toggleClass(element, className) {
            if (element) {
                element.classList.toggle(className);
            }
        },

        // Add class safely
        addClass(element, className) {
            if (element) {
                element.classList.add(className);
            }
        },

        // Remove class safely
        removeClass(element, className) {
            if (element) {
                element.classList.remove(className);
            }
        }
    },

    // AJAX Helpers
    ajax: {
        // Generic AJAX request handler
        request(url, options = {}) {
            const defaults = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': CartBase.utils.getCSRFToken()
                }
            };

            const config = { ...defaults, ...options };
            
            if (config.data && typeof config.data === 'object') {
                config.body = new URLSearchParams(config.data).toString();
                delete config.data;
            }

            return fetch(url, config)
                .then(response => response.json())
                .catch(error => {
                    console.error('AJAX Error:', error);
                    CartBase.utils.showError('Có lỗi xảy ra. Vui lòng thử lại.');
                    throw error;
                });
        },

        // POST request helper
        post(url, data = {}) {
            return this.request(url, {
                method: 'POST',
                data: { ...data, _token: CartBase.utils.getCSRFToken() }
            });
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    CartBase.init();
});

// Export for use in other modules
window.CartBase = CartBase;
