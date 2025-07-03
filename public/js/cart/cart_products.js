/**
 * Cart Products Module - Product management (add/remove items)
 * Handles individual product removal and bulk cart operations
 */

const CartProducts = {
    // Initialize product management
    init() {
        console.log('CartProducts init called');
        this.bindRemoveButtons();
        this.bindBulkActions();
        this.initialized = true;
    },

    // Bind individual product remove buttons using event delegation
    bindRemoveButtons() {
        console.log('Binding remove buttons with event delegation');
        
        // Use event delegation on document body
        document.body.addEventListener('click', (e) => {
            if (e.target.closest('.adidas-cart-product-remove')) {
                e.preventDefault();
                e.stopPropagation();
                
                const button = e.target.closest('.adidas-cart-product-remove');
                console.log('Remove button clicked via delegation:', button);
                this.removeItem(button);
            }
        });
        
        // Also bind directly for existing buttons
        const removeButtons = document.querySelectorAll('.adidas-cart-product-remove');
        console.log(`Found ${removeButtons.length} remove button(s) for direct binding`);
        
        removeButtons.forEach((button, index) => {
            console.log(`Setting up remove button ${index}:`, button);
            this.setupRemoveButton(button);
        });
    },

    // Setup individual remove button
    setupRemoveButton(button) {
        if (!button) return;
        
        console.log('Setting up remove button:', button);
        
        // Simple click handler
        button.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Button clicked directly:', button);
            this.removeItem(button);
        };
    },

    // Remove individual item from cart
    removeItem(button) {
        const cartItem = button.closest('.cart-item');
        const bookId = button.dataset.bookId;
        
        console.log('Remove item called:', { cartItem, bookId, button });
        
        if (!cartItem || !bookId) {
            console.error('Missing cart item or book ID');
            return;
        }

        // Show confirmation
        if (!CartBase.utils.showConfirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            return;
        }

        // Disable item controls
        const controls = cartItem.querySelectorAll('button, input');
        controls.forEach(el => el.disabled = true);
        
        // Add loading state
        CartBase.dom.addClass(cartItem, 'loading');

        // Get additional item data for precise removal
        const bookFormatId = cartItem.dataset.bookFormatId || null;
        const attributeValueIds = cartItem.dataset.attributeValueIds || null;

        console.log('Remove item data:', {
            bookId,
            bookFormatId,
            attributeValueIds
        });

        // Make AJAX request
        $.ajax({
            url: '/cart/remove',
            method: 'POST',
            data: {
                book_id: bookId,
                book_format_id: bookFormatId,
                attribute_value_ids: attributeValueIds,
                _token: CartBase.utils.getCSRFToken()
            },
            success: (response) => {
                console.log('Remove success:', response);
                this.handleRemoveSuccess(response, cartItem);
            },
            error: (xhr) => {
                console.error('Remove error:', xhr);
                this.handleRemoveError(xhr, cartItem, controls);
            }
        });
    },

    // Handle successful item removal
    handleRemoveSuccess(response, cartItem) {
        if (response.success) {
            CartBase.utils.showSuccess(response.success);
            
            // Always reload page after successful removal to update all cart data
            setTimeout(() => {
                location.reload();
            }, 1500); // Delay to show success message
            
        } else if (response.error) {
            CartBase.utils.showError(response.error);
            this.resetItemControls(cartItem);
        }
    },

    // Handle item removal error
    handleRemoveError(xhr, cartItem, controls) {
        const response = xhr.responseJSON;
        CartBase.utils.showError(response?.error || 'Có lỗi xảy ra. Vui lòng thử lại.');
        this.resetItemControls(cartItem);
    },

    // Reset item controls after error
    resetItemControls(cartItem) {
        const controls = cartItem.querySelectorAll('button, input');
        controls.forEach(el => el.disabled = false);
        CartBase.dom.removeClass(cartItem, 'loading');
    },

    // Bind bulk action buttons
    bindBulkActions() {
        this.setupClearCartButton();
        this.setupAddWishlistButton();
    },

    // Setup clear cart button
    setupClearCartButton() {
        const clearCartBtn = CartBase.dom.getById('clear-cart-btn');
        
        if (clearCartBtn) {
            CartBase.dom.on(clearCartBtn, 'click', () => {
                this.clearCart(clearCartBtn);
            });
        }
    },

    // Clear entire cart
    clearCart(button) {
        if (!CartBase.utils.showConfirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
            return;
        }

        // Show loading state
        CartBase.utils.showLoading(button, 'Đang xóa...');

        // Make AJAX request
        $.ajax({
            url: '/cart/clear',
            method: 'POST',
            data: {
                _token: CartBase.utils.getCSRFToken()
            },
            success: (response) => {
                this.handleClearSuccess(response);
            },
            error: (xhr) => {
                this.handleClearError(xhr, button);
            }
        });
    },

    // Handle successful cart clear
    handleClearSuccess(response) {
        if (response.success) {
            CartBase.utils.showSuccess(response.success);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    },

    // Handle cart clear error
    handleClearError(xhr, button) {
        const response = xhr.responseJSON;
        CartBase.utils.showError(response?.error || 'Có lỗi xảy ra khi xóa giỏ hàng');
        
        // Reset button
        CartBase.utils.hideLoading(button, '<i class="fas fa-trash-alt me-2"></i>Xóa tất cả');
    },

    // Setup add wishlist button
    setupAddWishlistButton() {
        const addWishlistBtn = CartBase.dom.getById('add-wishlist-btn');
        
        if (addWishlistBtn) {
            CartBase.dom.on(addWishlistBtn, 'click', () => {
                this.addFromWishlist(addWishlistBtn);
            });
        }
    },

    // Add all items from wishlist to cart
    addFromWishlist(button) {
        // Show loading state
        CartBase.utils.showLoading(button, 'Đang thêm...');

        // Make AJAX request
        $.ajax({
            url: '/cart/add-wishlist',
            method: 'POST',
            data: {
                _token: CartBase.utils.getCSRFToken()
            },
            success: (response) => {
                this.handleWishlistSuccess(response);
            },
            error: (xhr) => {
                this.handleWishlistError(xhr, button);
            }
        });
    },

    // Handle successful wishlist addition
    handleWishlistSuccess(response) {
        if (response.success) {
            CartBase.utils.showSuccess(response.success);
            
            // Show information about skipped items
            if (response.skipped_items && response.skipped_items.length > 0) {
                let skippedMsg = 'Một số sản phẩm không thể thêm:\n';
                response.skipped_items.forEach(item => {
                    skippedMsg += `- ${item.title}: ${item.reason}\n`;
                });
                
                if (typeof toastr !== 'undefined') {
                    toastr.warning(skippedMsg, 'Thông báo', {
                        timeOut: 5000
                    });
                }
            }
            
            // Reload page after delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
    },

    // Handle wishlist addition error
    handleWishlistError(xhr, button) {
        const response = xhr.responseJSON;
        CartBase.utils.showError(response?.error || 'Có lỗi xảy ra khi thêm từ danh sách yêu thích');
        
        // Reset button
        CartBase.utils.hideLoading(button, '<i class="fas fa-heart me-2"></i>Thêm từ yêu thích');
    },

    // Utility methods for product management
    utils: {
        // Get product information from cart item
        getProductInfo(cartItem) {
            if (!cartItem) return null;
            
            const titleElement = cartItem.querySelector('.product-title');
            const priceElement = cartItem.querySelector('.product-price');
            const quantityInput = cartItem.querySelector('.quantity-input');
            
            return {
                id: cartItem.dataset.bookId,
                title: titleElement ? titleElement.textContent.trim() : '',
                price: CartBase.utils.parseNumber(priceElement ? priceElement.textContent : '0'),
                quantity: quantityInput ? parseInt(quantityInput.value) || 1 : 1,
                stock: parseInt(cartItem.dataset.stock) || 0
            };
        },

        // Update product display information
        updateProductDisplay(cartItem, data) {
            if (!cartItem || !data) return;
            
            // Update data attributes
            if (data.price !== undefined) {
                cartItem.dataset.price = data.price;
            }
            if (data.stock !== undefined) {
                cartItem.dataset.stock = data.stock;
            }
            
            // Update stock display
            const stockElement = cartItem.querySelector('.stock-amount');
            if (stockElement && data.stock !== undefined) {
                stockElement.textContent = data.stock;
            }
            
            // Update quantity max
            const quantityInput = cartItem.querySelector('.quantity-input');
            if (quantityInput && data.stock !== undefined) {
                quantityInput.max = data.stock;
            }
        },

        // Check if cart has any items
        hasItems() {
            return CartBase.dom.getAll('.cart-item').length > 0;
        },

        // Get all cart items data
        getAllCartItems() {
            const cartItems = CartBase.dom.getAll('.cart-item');
            return Array.from(cartItems).map(item => this.getProductInfo(item)).filter(item => item);
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking CartBase...');
    if (window.CartBase) {
        console.log('CartBase available, initializing CartProducts...');
        CartProducts.init();
    } else {
        console.error('CartBase not available');
        // Try again after a delay
        setTimeout(() => {
            if (window.CartBase) {
                console.log('CartBase available after delay, initializing CartProducts...');
                CartProducts.init();
            }
        }, 100);
    }
});

// Also try when window loads
window.addEventListener('load', function() {
    console.log('Window loaded, double-checking CartProducts...');
    if (!CartProducts.initialized) {
        console.log('CartProducts not initialized yet, trying again...');
        CartProducts.init();
        CartProducts.initialized = true;
    }
});

// Export for use in other modules
window.CartProducts = CartProducts;
