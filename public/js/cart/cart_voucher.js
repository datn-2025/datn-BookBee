/**
 * Cart Voucher Module - Voucher system functionality
 * Handles voucher application, removal, and UI state management
 */

const CartVoucher = {
    // Current voucher state
    state: {
        isApplied: false,
        currentCode: '',
        discountAmount: 0
    },

    // Initialize voucher system
    init() {
        // this.bindVoucherEvents();
        // this.checkInitialState();
    },

    // Bind voucher-related events using event delegation
    bindVoucherEvents() {
        // Use event delegation for dynamic buttons
        // $(document).on('click', '#apply-voucher', (e) => {
        //     e.preventDefault();
        //     this.applyVoucher();
        // });

        // $(document).on('click', '#remove-voucher-btn', (e) => {
        //     e.preventDefault();
        //     this.removeVoucher();
        // });

        // Bind voucher input events
        // this.bindVoucherInput();
    },

    // Bind voucher input events
    bindVoucherInput() {
        const voucherInput = CartBase.dom.getById('voucher-code');
        
        if (voucherInput) {
            // Enter key to apply voucher
            CartBase.dom.on(voucherInput, 'keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.applyVoucher();
                }
            });

            // Clear error states on input
            CartBase.dom.on(voucherInput, 'input', () => {
                this.clearInputError(voucherInput);
            });
        }
    },

    // Check initial voucher state
    checkInitialState() {
        const voucherInput = CartBase.dom.getById('voucher-code');
        const removeBtn = CartBase.dom.getById('remove-voucher-btn');
        
        // If remove button exists, voucher is already applied
        if (removeBtn && voucherInput) {
            this.state.isApplied = true;
            this.state.currentCode = voucherInput.value;
            
            // Get current discount from display
            const discountElement = CartBase.dom.getById('discount-amount');
            if (discountElement) {
                this.state.discountAmount = CartBase.utils.parseNumber(discountElement.textContent);
            }
        }
    },

    // Apply voucher
    applyVoucher() {
        const voucherInput = $('#voucher-code');
        const voucherCode = voucherInput.val().trim();
        const applyBtn = $('#apply-voucher');

        // Validate input
        if (!voucherCode) {
            CartBase.utils.showError('Vui lòng nhập mã giảm giá');
            this.showInputError(voucherInput[0], 'Vui lòng nhập mã giảm giá');
            return;
        }

        // Show loading state
        this.showApplyLoading(voucherInput, applyBtn);

        // Get current total
        const currentTotal = this.getCurrentTotal();

        // Make AJAX request
        $.ajax({
            url: '/cart/apply-voucher',
            method: 'POST',
            data: {
                code: voucherCode,
                total: currentTotal,
                _token: CartBase.utils.getCSRFToken()
            },
            success: (response) => {
                this.handleApplySuccess(response, voucherCode);
            },
            error: (xhr) => {
                this.handleApplyError(xhr);
            }
        });
    },

    // Handle successful voucher application
    handleApplySuccess(response, voucherCode) {
        if (response.success) {
            CartBase.utils.showSuccess(response.success);
            
            // Update state
            this.state.isApplied = true;
            this.state.currentCode = voucherCode;
            this.state.discountAmount = response.discount || 0;
            
            // Update UI
            this.switchToRemoveMode(voucherCode);
            this.updateVoucherPriceDisplay(response.discount || 0);
            this.showVoucherSuccess();
            
        } else {
            CartBase.utils.showError(response.error || 'Có lỗi xảy ra');
            this.resetApplyButton();
        }
    },

    // Handle voucher application error
    handleApplyError(xhr) {
        const response = xhr.responseJSON;
        const errorMessage = response?.error || 'Có lỗi xảy ra khi áp dụng mã giảm giá';
        
        CartBase.utils.showError(errorMessage);
        this.showInputError($('#voucher-code')[0], errorMessage);
        this.resetApplyButton();
    },

    // Remove applied voucher
    removeVoucher() {
        const removeBtn = $('#remove-voucher-btn');
        
        // Show loading state
        this.showRemoveLoading(removeBtn);

        // Make AJAX request
        $.ajax({
            url: '/cart/remove-voucher',
            method: 'POST',
            data: {
                _token: CartBase.utils.getCSRFToken()
            },
            success: (response) => {
                this.handleRemoveSuccess(response);
            },
            error: (xhr) => {
                this.handleRemoveError(xhr);
            }
        });
    },

    // Handle successful voucher removal
    handleRemoveSuccess(response) {
        if (response.success) {
            CartBase.utils.showSuccess(response.success);
            
            // Update state
            this.state.isApplied = false;
            this.state.currentCode = '';
            this.state.discountAmount = 0;
            
            // Update UI
            this.switchToApplyMode();
            this.resetVoucherPriceDisplay();
            this.hideVoucherSuccess();
            
        } else {
            CartBase.utils.showError(response.error || 'Có lỗi xảy ra');
            this.resetRemoveButton();
        }
    },

    // Handle voucher removal error
    handleRemoveError(xhr) {
        const response = xhr.responseJSON;
        CartBase.utils.showError(response?.error || 'Có lỗi xảy ra khi xóa mã giảm giá');
        this.resetRemoveButton();
    },

    // UI State Management Methods

    // Switch to remove mode (voucher applied)
    switchToRemoveMode(voucherCode) {
        const voucherInput = $('#voucher-code');
        const buttonContainer = $('.voucher-button-container');
        
        voucherInput.val(voucherCode);
        voucherInput.prop('disabled', true);
        voucherInput.addClass('voucher-applied');
        
        buttonContainer.html(`
            <button type="button" id="remove-voucher-btn" class="btn btn-danger voucher-btn remove-voucher-btn">
                <i class="fas fa-times me-1"></i>
                <span class="btn-text">Xóa</span>
            </button>
        `);
    },

    // Switch to apply mode (no voucher)
    switchToApplyMode() {
        const voucherInput = $('#voucher-code');
        const buttonContainer = $('.voucher-button-container');
        
        voucherInput.val('');
        voucherInput.prop('disabled', false);
        voucherInput.removeClass('voucher-applied');
        this.clearInputError(voucherInput[0]);
        
        buttonContainer.html(`
            <button type="button" id="apply-voucher" class="btn btn-primary voucher-btn apply-voucher-btn">
                <i class="fas fa-check me-1"></i>
                <span class="btn-text">Áp dụng</span>
            </button>
        `);
    },

    // Show loading state for apply button
    showApplyLoading(voucherInput, applyBtn) {
        voucherInput.prop('disabled', true);
        applyBtn.prop('disabled', true);
        applyBtn.html('<i class="fas fa-spinner fa-spin me-1"></i><span class="btn-text">Đang áp dụng...</span>');
    },

    // Show loading state for remove button
    showRemoveLoading(removeBtn) {
        removeBtn.prop('disabled', true);
        removeBtn.html('<i class="fas fa-spinner fa-spin me-1"></i><span class="btn-text">Đang xóa...</span>');
    },

    // Reset apply button to normal state
    resetApplyButton() {
        const voucherInput = $('#voucher-code');
        const applyBtn = $('#apply-voucher');
        
        voucherInput.prop('disabled', false);
        applyBtn.prop('disabled', false);
        applyBtn.html('<i class="fas fa-check me-1"></i><span class="btn-text">Áp dụng</span>');
    },

    // Reset remove button to normal state
    resetRemoveButton() {
        const removeBtn = $('#remove-voucher-btn');
        removeBtn.prop('disabled', false);
        removeBtn.html('<i class="fas fa-times me-1"></i><span class="btn-text">Xóa</span>');
    },

    // Price Display Methods

    // Update voucher price display
    updateVoucherPriceDisplay(discountAmount) {
        const discountElement = $('#discount-amount');
        const totalElement = $('#total-amount');
        const subtotalElement = $('#subtotal');
        
        if (discountElement.length) {
            if (discountAmount > 0) {
                discountElement.text('- ' + CartBase.utils.formatCurrency(discountAmount));
                discountElement.css('color', '#dc3545');
            } else {
                discountElement.text('0đ');
                discountElement.css('color', '');
            }
        }
        
        // Update total
        this.updateTotalWithDiscount(discountAmount);
    },

    // Reset voucher price display
    resetVoucherPriceDisplay() {
        const discountElement = $('#discount-amount');
        
        if (discountElement.length) {
            discountElement.text('0đ');
            discountElement.css('color', '');
        }
        
        this.updateTotalWithDiscount(0);
    },

    // Update total with discount
    updateTotalWithDiscount(discountAmount) {
        const totalElement = $('#total-amount');
        const subtotalElement = $('#subtotal');
        
        if (totalElement.length && subtotalElement.length) {
            const subtotal = this.getCurrentTotal();
            const newTotal = Math.max(0, subtotal - discountAmount);
            totalElement.text(CartBase.utils.formatCurrency(newTotal));
        }
    },

    // Feedback Methods

    // Show voucher success indicator
    showVoucherSuccess() {
        this.hideVoucherSuccess(); // Remove existing one
        
        const voucherContainer = $('.voucher-input-container');
        if (voucherContainer.length) {
            const successIndicator = $(`
                <div class="voucher-success-indicator mt-2">
                    <small class="text-success fw-medium">
                        <i class="fas fa-check-circle me-1"></i>
                        Mã giảm giá đã được áp dụng thành công!
                    </small>
                </div>
            `);
            voucherContainer.append(successIndicator);
        }
    },

    // Hide voucher success indicator
    hideVoucherSuccess() {
        $('.voucher-success-indicator').remove();
    },

    // Show input error
    showInputError(input, message) {
        if (!input) return;
        
        CartBase.dom.addClass(input, 'is-invalid');
        
        // Remove existing error message
        this.clearInputError(input);
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback voucher-error';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    },

    // Clear input error
    clearInputError(input) {
        if (!input) return;
        
        CartBase.dom.removeClass(input, 'is-invalid');
        
        // Remove error message
        const errorElement = input.parentNode.querySelector('.voucher-error');
        if (errorElement) {
            CartBase.dom.remove(errorElement);
        }
    },

    // Utility Methods

    // Get current cart total
    getCurrentTotal() {
        const subtotalElement = $('#subtotal');
        if (subtotalElement.length) {
            return CartBase.utils.parseNumber(subtotalElement.text());
        }
        return 0;
    },

    // Check if voucher is currently applied
    isVoucherApplied() {
        return this.state.isApplied;
    },

    // Get current voucher code
    getCurrentVoucherCode() {
        return this.state.currentCode;
    },

    // Get current discount amount
    getCurrentDiscount() {
        return this.state.discountAmount;
    },

    // Validate voucher code format
    isValidVoucherCode(code) {
        return code && typeof code === 'string' && code.trim().length > 0;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (window.CartBase) {
        CartVoucher.init();
    }
});

// Export for use in other modules
window.CartVoucher = CartVoucher;
