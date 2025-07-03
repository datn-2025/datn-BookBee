/**
 * Cart Quantity Module - Quantity management and controls
 * Handles quantity input, increase/decrease buttons, and quantity validation
 */

const CartQuantity = {
    // Initialize quantity controls
    init() {
        this.bindQuantityControls();
        this.initQuantityValidation();
    },

    // Bind all quantity control events
    bindQuantityControls() {
        const cartItems = CartBase.dom.getAll('.cart-item');
        
        cartItems.forEach(cartItem => {
            this.setupQuantityControls(cartItem);
        });
    },

    // Setup quantity controls for a single cart item
    setupQuantityControls(cartItem) {
        const quantityInput = cartItem.querySelector('.quantity-input');
        const increaseBtn = cartItem.querySelector('.increase-quantity');
        const decreaseBtn = cartItem.querySelector('.decrease-quantity');
        const itemData = CartBase.utils.getCartItemData(cartItem);

        if (!itemData) return;

        // Check if this is an ebook
        const isEbook = this.isEbook(cartItem);
        
        // Store initial value
        if (quantityInput) {
            quantityInput.dataset.lastValue = quantityInput.value;
            
            // For ebooks, disable quantity changes
            if (isEbook) {
                quantityInput.disabled = true;
                quantityInput.value = 1;
                quantityInput.style.backgroundColor = '#f5f5f5';
                quantityInput.style.cursor = 'not-allowed';
            } else {
                this.setupQuantityInput(quantityInput, cartItem);
            }
        }

        // Setup buttons only for physical books
        if (!isEbook) {
            if (increaseBtn) {
                this.setupIncreaseButton(increaseBtn, quantityInput, cartItem);
            }
            if (decreaseBtn) {
                this.setupDecreaseButton(decreaseBtn, quantityInput, cartItem);
            }
        } else {
            // Disable buttons for ebooks
            if (increaseBtn) {
                increaseBtn.disabled = true;
                increaseBtn.style.display = 'none';
            }
            if (decreaseBtn) {
                decreaseBtn.disabled = true; 
                decreaseBtn.style.display = 'none';
            }
        }
    },

    // Check if item is an ebook
    isEbook(cartItem) {
        // Method 1: Check format name in cart item data
        const formatElement = cartItem.querySelector('.format-name, [data-format]');
        if (formatElement) {
            const formatText = formatElement.textContent || formatElement.dataset.format || '';
            if (formatText.toLowerCase().includes('ebook')) {
                return true;
            }
        }

        // Method 2: Check if quantity input is disabled (typical for ebooks)
        const quantityInput = cartItem.querySelector('.quantity-input');
        if (quantityInput && quantityInput.disabled && quantityInput.max == 1) {
            return true;
        }

        // Method 3: Check for ebook notice element
        const ebookNotice = cartItem.querySelector('.ebook-notice');
        if (ebookNotice) {
            return true;
        }

        // Method 4: Check meta data in the cart item element
        const formatName = cartItem.dataset.formatName;
        if (formatName && formatName.toLowerCase().includes('ebook')) {
            return true;
        }

        return false;
    },

    // Setup quantity input events
    setupQuantityInput(input, cartItem) {
        const itemData = CartBase.utils.getCartItemData(cartItem);
        
        // Handle input validation
        CartBase.dom.on(input, 'input', (e) => {
            this.validateQuantityInput(e.target, itemData);
        });

        // Handle quantity change
        CartBase.dom.on(input, 'change', (e) => {
            const newValue = parseInt(e.target.value) || 1;
            if (newValue > 0) {
                this.updateQuantity(cartItem, newValue);
            }
        });
    },

    // Setup increase button
    setupIncreaseButton(button, input, cartItem) {
        CartBase.dom.on(button, 'click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            if (!input || button.disabled) return;
            
            const currentValue = parseInt(input.value) || 1;
            const max = parseInt(input.max) || parseInt(cartItem.dataset.stock) || 1;
            
            if (currentValue < max) {
                const newValue = currentValue + 1;
                input.value = newValue;
                
                // Add visual feedback
                CartBase.dom.addClass(button, 'active');
                setTimeout(() => CartBase.dom.removeClass(button, 'active'), 150);
                
                this.updateQuantity(cartItem, newValue);
                this.updateButtonStates(cartItem);
            } else {
                // Show feedback when max reached
                CartBase.utils.showWarning(`Số lượng tối đa là ${max} sản phẩm`);
                this.addErrorFeedback(cartItem);
            }
        });

        // Add keyboard support
        CartBase.dom.on(button, 'keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                button.click();
            }
        });
    },

    // Setup decrease button
    setupDecreaseButton(button, input, cartItem) {
        CartBase.dom.on(button, 'click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            if (!input || button.disabled) return;
            
            const currentValue = parseInt(input.value) || 1;
            const min = parseInt(input.min) || 1;
            
            if (currentValue > min) {
                const newValue = currentValue - 1;
                input.value = newValue;
                
                // Add visual feedback
                CartBase.dom.addClass(button, 'active');
                setTimeout(() => CartBase.dom.removeClass(button, 'active'), 150);
                
                this.updateQuantity(cartItem, newValue);
                this.updateButtonStates(cartItem);
            } else {
                // Show feedback when min reached
                CartBase.utils.showWarning(`Số lượng tối thiểu là ${min} sản phẩm`);
                this.addErrorFeedback(cartItem);
            }
        });

        // Add keyboard support
        CartBase.dom.on(button, 'keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                button.click();
            }
        });
    },

    // Validate quantity input
    validateQuantityInput(input, itemData) {
        const newValue = parseInt(input.value) || 0;
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || itemData.stock;
        const cartItem = input.closest('.cart-item');

        // Skip validation for ebooks
        if (this.isEbook(cartItem)) {
            input.value = 1;
            return;
        }

        // Check stock limit for physical books
        if (newValue > itemData.stock) {
            CartBase.utils.showError(`Số lượng không được vượt quá ${itemData.stock} sản phẩm (số lượng tồn kho hiện tại)`);
            input.value = itemData.stock;
            return;
        }

        // Validate min/max
        if (newValue < min) {
            input.value = min;
            CartBase.utils.showWarning(`Số lượng tối thiểu là ${min}`);
        } else if (newValue > max) {
            input.value = max;
            CartBase.utils.showWarning(`Số lượng tối đa là ${max}`);
        }
    },

    // Initialize quantity validation
    initQuantityValidation() {
        // Set up initial button states
        const cartItems = CartBase.dom.getAll('.cart-item');
        
        cartItems.forEach(cartItem => {
            this.updateButtonStates(cartItem);
        });
    },

    // Update button states based on current quantity
    updateButtonStates(cartItem) {
        const quantityInput = cartItem.querySelector('.quantity-input');
        const increaseBtn = cartItem.querySelector('.increase-quantity');
        const decreaseBtn = cartItem.querySelector('.decrease-quantity');
        const itemData = CartBase.utils.getCartItemData(cartItem);
        const quantityControls = cartItem.querySelector('.quantity-controls');

        if (!quantityInput || !itemData) return;

        const currentValue = parseInt(quantityInput.value) || 1;
        const min = parseInt(quantityInput.min) || 1;
        const max = parseInt(quantityInput.max) || itemData.stock;

        // Update increase button
        if (increaseBtn) {
            const shouldDisable = currentValue >= max;
            increaseBtn.disabled = shouldDisable;
            increaseBtn.setAttribute('aria-disabled', shouldDisable);
            
            if (shouldDisable) {
                increaseBtn.title = `Đã đạt số lượng tối đa (${max})`;
            } else {
                increaseBtn.title = 'Tăng số lượng';
            }
        }

        // Update decrease button
        if (decreaseBtn) {
            const shouldDisable = currentValue <= min;
            decreaseBtn.disabled = shouldDisable;
            decreaseBtn.setAttribute('aria-disabled', shouldDisable);
            
            if (shouldDisable) {
                decreaseBtn.title = `Đã đạt số lượng tối thiểu (${min})`;
            } else {
                decreaseBtn.title = 'Giảm số lượng';
            }
        }

        // Update quantity feedback
        this.updateQuantityFeedback(cartItem, currentValue, max);
        
        // Remove error states
        if (quantityControls) {
            CartBase.dom.removeClass(quantityControls, 'error');
        }
    },

    // Update quantity feedback display
    updateQuantityFeedback(cartItem, currentValue, maxValue) {
        const feedbackEl = cartItem.querySelector('.quantity-feedback small');
        const stockAmount = cartItem.querySelector('.stock-amount');
        
        if (stockAmount) {
            stockAmount.textContent = maxValue;
        }
        
        // Add warning if near maximum
        if (feedbackEl && currentValue >= maxValue) {
            const warningSpan = feedbackEl.querySelector('.text-warning') || document.createElement('span');
            if (!feedbackEl.querySelector('.text-warning')) {
                warningSpan.className = 'text-warning';
                warningSpan.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Đã đạt tối đa';
                feedbackEl.appendChild(warningSpan);
            }
        } else {
            // Remove warning if exists
            const warningSpan = feedbackEl?.querySelector('.text-warning');
            if (warningSpan) {
                warningSpan.remove();
            }
        }
    },

    // Add error feedback animation
    addErrorFeedback(cartItem) {
        const quantityControls = cartItem.querySelector('.quantity-controls');
        if (quantityControls) {
            CartBase.dom.addClass(quantityControls, 'error');
            
            // Shake animation
            quantityControls.style.animation = 'shake 0.5s ease-in-out';
            
            setTimeout(() => {
                CartBase.dom.removeClass(quantityControls, 'error');
                quantityControls.style.animation = '';
            }, 2000);
        }
    },

    // Update quantity with debouncing
    updateQuantity: CartBase.utils.debounce(function(cartItem, newQuantity) {
        const itemData = CartBase.utils.getCartItemData(cartItem);
        const quantityInput = cartItem.querySelector('.quantity-input');
        const increaseBtn = cartItem.querySelector('.increase-quantity');
        const decreaseBtn = cartItem.querySelector('.decrease-quantity');
        const stockAmount = cartItem.querySelector('.stock-amount');
        const oldQuantity = parseInt(quantityInput?.dataset.lastValue) || 1;

        if (!itemData) return;

        // Skip validation for ebooks - always quantity = 1
        const isEbook = this.isEbook(cartItem);
        if (isEbook) {
            if (quantityInput) {
                quantityInput.value = 1;
                quantityInput.dataset.lastValue = 1;
            }
            CartBase.utils.showInfo('Sách điện tử luôn có số lượng cố định là 1');
            return;
        }

        // Validate quantity against stock for physical books
        if (newQuantity > itemData.stock) {
            CartBase.utils.showError(`Số lượng không được vượt quá ${itemData.stock} sản phẩm (số lượng tồn kho hiện tại)`);
            if (quantityInput) quantityInput.value = oldQuantity;
            return;
        }

        // Disable controls during update
        const controls = [quantityInput, increaseBtn, decreaseBtn].filter(el => el);
        controls.forEach(el => el.disabled = true);

        // Add loading state
        CartBase.dom.addClass(cartItem, 'loading');

        // Update UI immediately for better UX
        if (window.CartSummary && typeof window.CartSummary.updateItemPriceDisplay === 'function') {
            window.CartSummary.updateItemPriceDisplay(cartItem, newQuantity);
        }

        // Prepare data for update - include all identifying information
        const updateData = {
            book_id: itemData.bookId,
            book_format_id: cartItem.dataset.bookFormatId || null,
            attribute_value_ids: cartItem.dataset.attributeValueIds || null,
            quantity: newQuantity,
            _token: CartBase.utils.getCSRFToken()
        };

        // Make AJAX request
        $.ajax({
            url: '/cart/update',
            method: 'POST',
            data: updateData,
            success: (response) => {
                this.handleUpdateSuccess(response, cartItem, newQuantity, controls, stockAmount, quantityInput, increaseBtn, decreaseBtn);
            },
            error: (xhr) => {
                this.handleUpdateError(xhr, cartItem, oldQuantity, controls);
            },
            complete: () => {
                // Remove loading state
                CartBase.dom.removeClass(cartItem, 'loading');
                
                // Re-enable controls
                controls.forEach(el => el.disabled = false);
            }
        });
    }, CartBase.config.debounceDelay),

    // Handle successful quantity update
    handleUpdateSuccess(response, cartItem, newQuantity, controls, stockAmount, quantityInput, increaseBtn, decreaseBtn) {
        if (response.success) {
            CartBase.utils.showSuccess(response.success);
            
            // Update item data based on response
            if (response.data) {
                const isEbook = response.data.is_ebook || false;
                
                if (isEbook) {
                    // For ebooks: ensure quantity is always 1 and disable controls
                    cartItem.dataset.price = response.data.price;
                    cartItem.dataset.stock = 999; // Ebook unlimited stock
                    
                    if (quantityInput) {
                        quantityInput.value = 1;
                        quantityInput.max = 1;
                        quantityInput.disabled = true;
                        quantityInput.dataset.lastValue = 1;
                        quantityInput.style.backgroundColor = '#f5f5f5';
                        quantityInput.style.cursor = 'not-allowed';
                    }

                    // Hide/disable increase/decrease buttons for ebooks
                    if (increaseBtn) {
                        increaseBtn.disabled = true;
                        increaseBtn.style.display = 'none';
                    }
                    if (decreaseBtn) {
                        decreaseBtn.disabled = true;
                        decreaseBtn.style.display = 'none';
                    }

                    // Update stock display for ebook
                    if (stockAmount) {
                        const stockContainer = stockAmount.closest('.adidas-cart-product-stock');
                        if (stockContainer) {
                            stockContainer.innerHTML = `
                                <small>
                                    <span><i class="fas fa-infinity"></i> Ebook - Có sẵn</span>
                                </small>
                            `;
                        }
                    }
                } else {
                    // For physical books: normal quantity management
                    cartItem.dataset.price = response.data.price;
                    cartItem.dataset.stock = response.data.stock;
                    
                    // Update stock display
                    if (stockAmount) {
                        stockAmount.textContent = response.data.stock;
                    }

                    // Update input attributes
                    if (quantityInput) {
                        quantityInput.max = response.data.stock;
                        quantityInput.dataset.lastValue = newQuantity;
                        quantityInput.disabled = false;
                        quantityInput.style.backgroundColor = '';
                        quantityInput.style.cursor = '';
                    }

                    // Update button states for physical books
                    this.updateButtonStates(cartItem);
                }

                // Update price display for both types
                if (window.CartSummary && typeof window.CartSummary.updateItemPriceDisplay === 'function') {
                    window.CartSummary.updateItemPriceDisplay(cartItem, isEbook ? 1 : newQuantity);
                }
            }
        } else if (response.error) {
            CartBase.utils.showError(response.error);
            this.resetToOldValue(cartItem);
        }
    },

    // Handle quantity update error
    handleUpdateError(xhr, cartItem, oldQuantity, controls) {
        const response = xhr.responseJSON;
        
        if (response?.error) {
            if (response.available_stock !== undefined) {
                const quantityInput = cartItem.querySelector('.quantity-input');
                const stockAmount = cartItem.querySelector('.stock-amount');
                
                CartBase.utils.showError(`${response.error} (Số lượng tồn kho hiện tại: ${response.available_stock} sản phẩm)`);
                
                if (quantityInput) {
                    quantityInput.value = Math.min(response.available_stock, oldQuantity);
                    quantityInput.max = response.available_stock;
                }
                
                if (stockAmount) {
                    stockAmount.textContent = response.available_stock;
                }
                
                cartItem.dataset.stock = response.available_stock;
                
                if (window.CartSummary && typeof window.CartSummary.updateItemPriceDisplay === 'function') {
                    window.CartSummary.updateItemPriceDisplay(cartItem, Math.min(response.available_stock, oldQuantity));
                }
            } else {
                CartBase.utils.showError(response.error);
                this.resetToOldValue(cartItem);
            }
        } else {
            CartBase.utils.showError('Có lỗi xảy ra. Vui lòng thử lại.');
            this.resetToOldValue(cartItem);
        }
    },

    // Reset quantity to previous value
    resetToOldValue(cartItem) {
        const quantityInput = cartItem.querySelector('.quantity-input');
        const oldQuantity = parseInt(quantityInput?.dataset.lastValue) || 1;
        
        if (quantityInput) {
            quantityInput.value = oldQuantity;
        }
        
        if (window.CartSummary && typeof window.CartSummary.updateItemPriceDisplay === 'function') {
            window.CartSummary.updateItemPriceDisplay(cartItem, oldQuantity);
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (window.CartBase) {
        CartQuantity.init();
    }
});

// Export for use in other modules
window.CartQuantity = CartQuantity;
