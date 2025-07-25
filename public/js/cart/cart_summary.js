/**
 * Cart Summary Module - Price calculations and cart totals
 * Handles all cart total calculations, price displays, and order summary
 */

const CartSummary = {
    // Summary state
    state: {
        subtotal: 0,
        discount: 0,
        total: 0
    },

    // Initialize cart summary
    init() {
        this.calculateInitialTotals();
        this.bindSummaryEvents();
    },

    // Calculate initial totals when page loads
    calculateInitialTotals() {
        this.updateCartTotal();
    },

    // Bind summary-related events
    bindSummaryEvents() {
        // Listen for cart changes
        document.addEventListener('cartUpdated', () => {
            this.updateCartTotal();
        });

        // Listen for voucher changes
        document.addEventListener('voucherChanged', (e) => {
            this.handleVoucherChange(e.detail);
        });
    },

    // Update individual item price display
    updateItemPriceDisplay(cartItem, quantity) {
        if (!cartItem) return;

        const itemData = CartBase.utils.getCartItemData(cartItem);
        if (!itemData) return;

        const itemTotal = itemData.price * quantity;
        
        // Update item total display
        const itemTotalElement = cartItem.querySelector('.item-total');
        if (itemTotalElement) {
            itemTotalElement.textContent = CartBase.utils.formatCurrency(itemTotal);
        }

        // Update cart totals
        this.updateCartTotal();
    },

    // Update complete cart total
    updateCartTotal() {
        const cartItems = CartBase.dom.getAll('.cart-item');
        
        if (cartItems.length === 0) {
            console.log('No cart items found');
            return;
        }

        let cartTotal = 0;

        // Calculate subtotal from all items
        cartItems.forEach(item => {
            const itemData = CartBase.utils.getCartItemData(item);
            const quantityInput = item.querySelector('.quantity-input');
            const quantity = quantityInput ? (parseInt(quantityInput.value) || 0) : 0;
            
            if (itemData) {
                cartTotal += itemData.price * quantity;
            }
            
            console.log('Item calculation:', {
                price: itemData?.price || 0,
                quantity: quantity,
                subtotal: (itemData?.price || 0) * quantity
            });
        });

        console.log('Cart total calculated:', cartTotal);

        // Update state
        this.state.subtotal = cartTotal;

        // Get current discount
        const discountElement = CartBase.dom.getById('discount-amount');
        let discount = 0;
        
        if (discountElement && discountElement.textContent) {
            const discountText = discountElement.textContent.trim();
            if (discountText !== '0đ' && discountText !== '') {
                // Remove "- " prefix and parse
                discount = CartBase.utils.parseNumber(discountText.replace(/^-\s*/, ''));
            }
        }

        this.state.discount = discount;
        this.state.total = Math.max(0, cartTotal - discount);

        console.log('Summary state:', this.state);

        // Update display
        this.updateSummaryDisplay();
    },

    // Update summary display elements
    updateSummaryDisplay() {
        // Update subtotal
        const subtotalElement = CartBase.dom.getById('subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = CartBase.utils.formatCurrency(this.state.subtotal);
            console.log('Updated subtotal display:', subtotalElement.textContent);
        }

        // Update total
        const totalElement = CartBase.dom.getById('total-amount');
        if (totalElement) {
            totalElement.textContent = CartBase.utils.formatCurrency(this.state.total);
            console.log('Updated total display:', totalElement.textContent);
        }

        // Dispatch event for other modules
        this.dispatchSummaryUpdateEvent();
    },

    // Handle voucher changes
    handleVoucherChange(voucherData) {
        if (voucherData) {
            this.state.discount = voucherData.discount || 0;
        } else {
            this.state.discount = 0;
        }

        this.state.total = Math.max(0, this.state.subtotal - this.state.discount);
        this.updateSummaryDisplay();
    },

    // Update discount display
    updateDiscountDisplay(discountAmount) {
        const discountElement = CartBase.dom.getById('discount-amount');
        
        if (discountElement) {
            if (discountAmount > 0) {
                discountElement.textContent = '- ' + CartBase.utils.formatCurrency(discountAmount);
                discountElement.style.color = '#dc3545';
            } else {
                discountElement.textContent = '0đ';
                discountElement.style.color = '';
            }
        }

        this.state.discount = discountAmount;
        this.state.total = Math.max(0, this.state.subtotal - discountAmount);
        
        // Update total display
        const totalElement = CartBase.dom.getById('total-amount');
        if (totalElement) {
            totalElement.textContent = CartBase.utils.formatCurrency(this.state.total);
        }
    },

    // Dispatch summary update event
    dispatchSummaryUpdateEvent() {
        const event = new CustomEvent('summaryUpdated', {
            detail: {
                subtotal: this.state.subtotal,
                discount: this.state.discount,
                total: this.state.total
            }
        });
        document.dispatchEvent(event);
    },

    // Price formatting utilities
    formatting: {
        // Format price with currency
        formatPrice(amount) {
            return CartBase.utils.formatCurrency(amount);
        },

        // Parse price from text
        parsePrice(text) {
            return CartBase.utils.parseNumber(text);
        },

        // Format percentage
        formatPercentage(value) {
            return value.toFixed(1) + '%';
        },

        // Calculate percentage discount
        calculateDiscountPercentage(original, discounted) {
            if (original <= 0) return 0;
            return ((original - discounted) / original) * 100;
        }
    },

    // Summary calculations
    calculations: {
        // Calculate item subtotal
        calculateItemSubtotal(price, quantity) {
            return (parseFloat(price) || 0) * (parseInt(quantity) || 0);
        },

        // Calculate discount amount
        calculateDiscountAmount(subtotal, discountPercent) {
            return (subtotal * discountPercent) / 100;
        },

        // Calculate final total
        calculateFinalTotal(subtotal, discountAmount) {
            return Math.max(0, subtotal - discountAmount);
        },

        // Validate cart totals
        validateTotals(subtotal, discount, total) {
            return {
                isValid: total >= 0 && subtotal >= 0 && discount >= 0,
                subtotal: Math.max(0, subtotal),
                discount: Math.max(0, discount),
                total: Math.max(0, total)
            };
        }
    },

    // Animation and visual feedback
    animations: {
        // Animate price change
        animatePriceChange(element, newValue) {
            if (!element) return;
            
            CartBase.dom.addClass(element, 'price-updating');
            
            setTimeout(() => {
                element.textContent = CartBase.utils.formatCurrency(newValue);
                CartBase.dom.removeClass(element, 'price-updating');
                CartBase.dom.addClass(element, 'price-updated');
                
                setTimeout(() => {
                    CartBase.dom.removeClass(element, 'price-updated');
                }, 300);
            }, 150);
        },

        // Highlight savings
        highlightSavings(amount) {
            if (amount <= 0) return;
            
            const savingsElement = CartBase.dom.get('.savings-highlight');
            if (savingsElement) {
                savingsElement.textContent = `Bạn tiết kiệm: ${CartBase.utils.formatCurrency(amount)}`;
                CartBase.dom.addClass(savingsElement, 'highlight-savings');
                
                setTimeout(() => {
                    CartBase.dom.removeClass(savingsElement, 'highlight-savings');
                }, 2000);
            }
        }
    },

    // Summary information getters
    getSummary() {
        return {
            subtotal: this.state.subtotal,
            discount: this.state.discount,
            total: this.state.total,
            itemCount: CartBase.dom.getAll('.cart-item').length,
            hasDiscount: this.state.discount > 0
        };
    },

    // Get formatted summary
    getFormattedSummary() {
        const summary = this.getSummary();
        return {
            subtotal: CartBase.utils.formatCurrency(summary.subtotal),
            discount: CartBase.utils.formatCurrency(summary.discount),
            total: CartBase.utils.formatCurrency(summary.total),
            itemCount: summary.itemCount,
            hasDiscount: summary.hasDiscount
        };
    },

    // Validation methods
    validation: {
        // Validate cart is not empty
        isCartValid() {
            const items = CartBase.dom.getAll('.cart-item');
            return items.length > 0;
        },

        // Validate minimum order amount
        isMinimumAmountMet(minimumAmount = 0) {
            return CartSummary.state.total >= minimumAmount;
        },

        // Check stock availability
        isStockAvailable() {
            const cartItems = CartBase.dom.getAll('.cart-item');
            
            for (const item of cartItems) {
                const quantityInput = item.querySelector('.quantity-input');
                const quantity = parseInt(quantityInput?.value) || 0;
                const stock = parseInt(item.dataset.stock) || 0;
                
                if (quantity > stock) {
                    return false;
                }
            }
            
            return true;
        }
    },

    // Debug utilities
    debug: {
        // Log current state
        logState() {
            console.log('Cart Summary State:', CartSummary.state);
        },

        // Log all cart items
        logCartItems() {
            const items = CartBase.dom.getAll('.cart-item');
            items.forEach((item, index) => {
                const itemData = CartBase.utils.getCartItemData(item);
                const quantityInput = item.querySelector('.quantity-input');
                const quantity = quantityInput ? parseInt(quantityInput.value) || 0 : 0;
                
                console.log(`Item ${index + 1}:`, {
                    ...itemData,
                    quantity: quantity,
                    subtotal: (itemData?.price || 0) * quantity
                });
            });
        },

        // Validate calculations
        validateCalculations() {
            const items = CartBase.dom.getAll('.cart-item');
            let calculatedTotal = 0;
            
            items.forEach(item => {
                const itemData = CartBase.utils.getCartItemData(item);
                const quantityInput = item.querySelector('.quantity-input');
                const quantity = parseInt(quantityInput?.value) || 0;
                calculatedTotal += (itemData?.price || 0) * quantity;
            });
            
            const displayedSubtotal = CartBase.utils.parseNumber(
                CartBase.dom.getById('subtotal')?.textContent || '0'
            );
            
            console.log('Calculation validation:', {
                calculated: calculatedTotal,
                displayed: displayedSubtotal,
                match: Math.abs(calculatedTotal - displayedSubtotal) < 0.01
            });
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (window.CartBase) {
        CartSummary.init();
        
        // Initial total calculation
        setTimeout(() => {
            CartSummary.updateCartTotal();
        }, 100);
    }
});

// Export for use in other modules
window.CartSummary = CartSummary;
