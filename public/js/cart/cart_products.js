/**
 * Module Quản Lý Sản Phẩm Giỏ Hàng - Đã sửa lỗi
 * Quản lý việc xóa sản phẩm riêng lẻ và các thao tác hàng loạt trên giỏ hàng
 */

const CartProducts = {
    initialized: false,
    
    // Khởi tạo module quản lý sản phẩm
    init() {
        // Chỉ khởi tạo một lần
        if (this.initialized) {
            return;
        }
        
        this.bindRemoveButtons();
        this.bindQuantityButtons();
        this.bindBulkActions();
        this.bindSelectCheckboxes();
        this.initialized = true;
    },

    // Gắn sự kiện cho các nút xóa sản phẩm riêng lẻ
    bindRemoveButtons() {
        // Kiểm tra và ngăn chặn gắn nhiều lần
        if (document.body.hasAttribute('data-cart-remove-bound')) {
            return;
        }
        
        // Sử dụng event delegation trên document
        document.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.cart-product-remove');
            if (removeBtn) {
                e.preventDefault();
                e.stopPropagation();
                this.removeItem(removeBtn);
            }
        });
        
        document.body.setAttribute('data-cart-remove-bound', 'true');
        
        // Gắn trực tiếp cho các nút hiện có để đảm bảo
        const removeButtons = document.querySelectorAll('.cart-product-remove');
        
        removeButtons.forEach((button, index) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.removeItem(button);
            });
        });
    },

    // Xóa sản phẩm riêng lẻ khỏi giỏ hàng
    removeItem(button) {
        const cartItem = button.closest('.cart-item');
        const isCombo = button.dataset.isCombo === 'true';
        const bookId = button.dataset.bookId || button.getAttribute('data-book-id');
        const collectionId = button.dataset.collectionId || button.getAttribute('data-collection-id');
        
        if (!cartItem || (!bookId && !collectionId)) {
            return;
        }

        // Hiển thị xác nhận với thông báo phù hợp
        const confirmMessage = isCombo 
            ? 'Bạn có chắc muốn xóa combo này khỏi giỏ hàng?' 
            : 'Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?';
            
        if (!confirm(confirmMessage)) {
            return;
        }

        // Vô hiệu hóa các điều khiển của sản phẩm
        const controls = cartItem.querySelectorAll('button, input');
        controls.forEach(el => el.disabled = true);
        
        // Thêm trạng thái đang tải
        cartItem.classList.add('loading');

        let requestData;
        
        if (isCombo) {
            // Xóa combo
            requestData = {
                collection_id: collectionId,
                is_combo: true,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };
        } else {
            // Xóa sách riêng lẻ - lấy dữ liệu từ cart item hoặc button
            const bookFormatId = cartItem.dataset.bookFormatId || cartItem.getAttribute('data-book-format-id') || null;
            const attributeValueIds = cartItem.dataset.attributeValueIds || cartItem.getAttribute('data-attribute-value-ids') || null;

            requestData = {
                book_id: bookId,
                book_format_id: bookFormatId,
                attribute_value_ids: attributeValueIds,
                is_combo: false,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };
        }

        // Gửi yêu cầu AJAX
        $.ajax({
            url: '/cart/remove',
            method: 'POST',
            data: requestData,
            success: (response) => {
                this.handleRemoveSuccess(response, cartItem);
            },
            error: (xhr) => {
                this.handleRemoveError(xhr, cartItem);
            }
        });
    },

    // Handle successful item removal
    handleRemoveSuccess(response, cartItem) {
        if (response.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(response.success);
            } else {
                alert(response.success);
            }
            
            // Dispatch cart update event with count
            if (typeof response.cart_count !== 'undefined') {
                document.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: response.cart_count }
                }));
            }
            
            // Reload page after delay to show success message
            setTimeout(() => {
                location.reload();
            }, 1500);
            
        } else if (response.error) {
            if (typeof toastr !== 'undefined') {
                toastr.error(response.error);
            } else {
                alert(response.error);
            }
            this.resetItemControls(cartItem);
        }
    },

    // Handle item removal error
    handleRemoveError(xhr, cartItem) {
        const response = xhr.responseJSON;
        const errorMsg = response?.error || 'Có lỗi xảy ra. Vui lòng thử lại.';
        
        if (typeof toastr !== 'undefined') {
            toastr.error(errorMsg);
        } else {
            alert(errorMsg);
        }
        
        this.resetItemControls(cartItem);
    },

    // Reset item controls after error
    resetItemControls(cartItem) {
        const controls = cartItem.querySelectorAll('button, input');
        controls.forEach(el => el.disabled = false);
        cartItem.classList.remove('loading');
    },

    // Bind bulk action buttons
    bindBulkActions() {
        this.setupClearCartButton();
    },

    // Setup clear cart button
    setupClearCartButton() {
        const clearCartBtn = document.getElementById('clear-cart-btn');
        
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.clearCart(clearCartBtn);
            });
        }
    },

    // Clear entire cart
    clearCart(button) {
        if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
            return;
        }

        // Show loading state
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xóa...';

        // Make AJAX request
        $.ajax({
            url: '/cart/clear',
            method: 'POST',
            data: {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            if (typeof toastr !== 'undefined') {
                toastr.success(response.success);
            } else {
                alert(response.success);
            }
            
            // Dispatch cart clear event
            if (typeof response.cart_count !== 'undefined') {
                document.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: response.cart_count }
                }));
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    },

    // Handle cart clear error
    handleClearError(xhr, button) {
        const response = xhr.responseJSON;
        const errorMsg = response?.error || 'Có lỗi xảy ra khi xóa giỏ hàng';
        
        if (typeof toastr !== 'undefined') {
            toastr.error(errorMsg);
        } else {
            alert(errorMsg);
        }
        
        // Reset button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-trash-alt me-2"></i>XÓA TẤT CẢ';
    },

    // Gắn sự kiện cho các nút cộng trừ số lượng
    bindQuantityButtons() {
        // Kiểm tra và ngăn chặn gắn nhiều lần
        if (document.body.hasAttribute('data-cart-quantity-bound')) {
            return;
        }

        // Event delegation cho nút tăng số lượng
        document.addEventListener('click', (e) => {
            const increaseBtn = e.target.closest('.increase-quantity');
            if (increaseBtn) {
                e.preventDefault();
                e.stopPropagation();
                this.increaseQuantity(increaseBtn);
            }
        });

        // Event delegation cho nút giảm số lượng
        document.addEventListener('click', (e) => {
            const decreaseBtn = e.target.closest('.decrease-quantity');
            if (decreaseBtn) {
                e.preventDefault();
                e.stopPropagation();
                this.decreaseQuantity(decreaseBtn);
            }
        });

        // Event cho input số lượng
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const input = e.target;
                const newValue = parseInt(input.value) || 1;
                this.updateQuantity(input, newValue);
            }
        });

        document.body.setAttribute('data-cart-quantity-bound', 'true');

        // Gắn trực tiếp cho các nút hiện có
        const increaseButtons = document.querySelectorAll('.increase-quantity');
        const decreaseButtons = document.querySelectorAll('.decrease-quantity');
        const quantityInputs = document.querySelectorAll('.quantity-input');

        increaseButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.increaseQuantity(button);
            });
        });

        decreaseButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.decreaseQuantity(button);
            });
        });

        quantityInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const newValue = parseInt(input.value) || 1;
                this.updateQuantity(input, newValue);
            });
        });
    },

    // Tăng số lượng
    increaseQuantity(button) {
        const cartItem = button.closest('.cart-item');
        const quantityInput = cartItem.querySelector('.quantity-input');
        
        if (!quantityInput) {
            return;
        }

        const currentValue = parseInt(quantityInput.value) || 1;
        const maxValue = parseInt(quantityInput.max) || parseInt(cartItem.dataset.stock) || 999;

        if (currentValue < maxValue) {
            const newValue = currentValue + 1;
            quantityInput.value = newValue;
            this.updateQuantity(quantityInput, newValue);
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.warning(`Số lượng tối đa là ${maxValue}`);
            } else {
                alert(`Số lượng tối đa là ${maxValue}`);
            }
        }
    },

    // Giảm số lượng
    decreaseQuantity(button) {
        const cartItem = button.closest('.cart-item');
        const quantityInput = cartItem.querySelector('.quantity-input');
        
        if (!quantityInput) {
            return;
        }

        const currentValue = parseInt(quantityInput.value) || 1;
        const minValue = parseInt(quantityInput.min) || 1;

        if (currentValue > minValue) {
            const newValue = currentValue - 1;
            quantityInput.value = newValue;
            this.updateQuantity(quantityInput, newValue);
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.warning(`Số lượng tối thiểu là ${minValue}`);
            } else {
                alert(`Số lượng tối thiểu là ${minValue}`);
            }
        }
    },

    // Cập nhật số lượng sản phẩm
    updateQuantity(input, newQuantity) {
        const cartItem = input.closest('.cart-item');
        if (!cartItem) return;

        const isCombo = input.dataset.isCombo === 'true' || cartItem.dataset.isCombo === 'true';
        // Ưu tiên lấy cart_id từ cart item, sau đó từ input
        const cartId = cartItem.dataset.cartId || input.dataset.cartId;
        const bookId = cartItem.dataset.bookId || input.dataset.bookId;
        const collectionId = cartItem.dataset.collectionId || input.dataset.collectionId;
        const oldQuantity = parseInt(input.dataset.lastValue) || 1;


        // Validate quantity
        const minValue = parseInt(input.min) || 1;
        
        // For variant stock validation, use the minimum stock available
        let maxValue = parseInt(input.max) || parseInt(cartItem.dataset.stock) || 999;
        
        // If this item has attributes (variants), we need to consider variant stock
        const attributeValueIds = cartItem.dataset.attributeValueIds;
        const isEbook = cartItem.dataset.formatName && cartItem.dataset.formatName.toLowerCase().includes('ebook');
        
        // For physical books with variants, validate against variant stock
        if (!isEbook && attributeValueIds && attributeValueIds !== '[]' && attributeValueIds !== 'null') {
            // The max value should already be set correctly from the hierarchical stock validation
            // but we double-check here
            console.log('Validating variant stock for quantity update:', {
                cartId: cartId,
                bookId: bookId,
                attributeValueIds: attributeValueIds,
                requestedQuantity: newQuantity,
                maxAllowed: maxValue
            });
        }

        if (newQuantity < minValue) {
            input.value = oldQuantity;
            if (typeof toastr !== 'undefined') {
                toastr.warning(`Số lượng tối thiểu là ${minValue}`);
            }
            return;
        }

        if (newQuantity > maxValue) {
            input.value = oldQuantity;
            if (typeof toastr !== 'undefined') {
                toastr.warning(`Số lượng tối đa là ${maxValue}. ${attributeValueIds && attributeValueIds !== '[]' ? 'Giới hạn bởi tồn kho biến thể.' : 'Giới hạn bởi tồn kho.'}`);
            }
            return;
        }

        // Disable controls
        const controls = cartItem.querySelectorAll('button, input');
        controls.forEach(el => el.disabled = true);
        cartItem.classList.add('loading');

        // Prepare request data
        let requestData = {
            quantity: newQuantity,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        // Thêm cart_id nếu có - đây là cách đáng tin cậy nhất
        if (cartId) {
            requestData.cart_id = cartId;
        }

        if (isCombo) {
            requestData.collection_id = collectionId;
            requestData.is_combo = true;
        } else {
            // Fallback data cho trường hợp không có cart_id
            requestData.book_id = bookId;
            requestData.book_format_id = cartItem.dataset.bookFormatId || null;
            requestData.attribute_value_ids = cartItem.dataset.attributeValueIds || null;
            requestData.variant_id = cartItem.dataset.variantId || null; // Thêm variant_id support
            requestData.is_combo = false;
        }


        // Send AJAX request
        $.ajax({
            url: '/cart/update',
            method: 'POST',
            data: requestData,
            success: (response) => {
                this.handleQuantityUpdateSuccess(response, cartItem, input, newQuantity, oldQuantity);
            },
            error: (xhr) => {
                this.handleQuantityUpdateError(xhr, cartItem, input, oldQuantity);
            }
        });
    },

    // Handle successful quantity update
    handleQuantityUpdateSuccess(response, cartItem, input, newQuantity, oldQuantity) {
        if (response.success) {
            // Update input last value
            input.dataset.lastValue = newQuantity;

            // Update item total display
            this.updateItemTotal(cartItem, newQuantity);

            // Chỉ update tổng tiền nếu sản phẩm này đang được check
            const checkbox = cartItem.querySelector('.select-cart-item');
            if (checkbox && checkbox.checked) {
                this.updateCartTotals();
            }
            // Nếu chưa check thì KHÔNG update tổng tiền

            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success(response.success);
            }

            // Update button states
            this.updateQuantityButtonStates(cartItem);

            // Dispatch cart update event
            if (typeof response.cart_count !== 'undefined') {
                document.dispatchEvent(new CustomEvent('cartUpdated', {
                    detail: { count: response.cart_count }
                }));
            }
        } else if (response.error) {
            input.value = oldQuantity;
            if (typeof toastr !== 'undefined') {
                toastr.error(response.error);
            } else {
                alert(response.error);
            }
        }

        // Re-enable controls
        const controls = cartItem.querySelectorAll('button, input');
        controls.forEach(el => el.disabled = false);
        cartItem.classList.remove('loading');
    },

    // Handle quantity update error
    handleQuantityUpdateError(xhr, cartItem, input, oldQuantity) {
        const response = xhr.responseJSON;
        const errorMsg = response?.error || 'Có lỗi xảy ra khi cập nhật số lượng';

        input.value = oldQuantity;
        
        if (typeof toastr !== 'undefined') {
            toastr.error(errorMsg);
        } else {
            alert(errorMsg);
        }

        // Re-enable controls
        const controls = cartItem.querySelectorAll('button, input');
        controls.forEach(el => el.disabled = false);
        cartItem.classList.remove('loading');
    },

    // Update item total display
    updateItemTotal(cartItem, quantity) {
        // Sử dụng data-price đã bao gồm extra price từ variants
        const price = parseFloat(cartItem.dataset.price) || 0;
        const total = price * quantity;
        const itemTotalElement = cartItem.querySelector('.item-total');
        
        if (itemTotalElement) {
            itemTotalElement.textContent = this.formatCurrency(total);
        }
        
        // Cập nhật data-price trong trường hợp cần thiết
        cartItem.dataset.price = price;
    },

    // Update quantity button states
    updateQuantityButtonStates(cartItem) {
        const quantityInput = cartItem.querySelector('.quantity-input');
        const increaseBtn = cartItem.querySelector('.increase-quantity');
        const decreaseBtn = cartItem.querySelector('.decrease-quantity');

        if (!quantityInput) return;

        const currentValue = parseInt(quantityInput.value) || 1;
        const minValue = parseInt(quantityInput.min) || 1;
        const maxValue = parseInt(quantityInput.max) || parseInt(cartItem.dataset.stock) || 999;

        if (increaseBtn) {
            increaseBtn.disabled = currentValue >= maxValue;
        }

        if (decreaseBtn) {
            decreaseBtn.disabled = currentValue <= minValue;
        }
    },

    // Update cart totals
    updateCartTotals() {
        let cartTotal = 0;
        const cartItems = document.querySelectorAll('.cart-item');

        cartItems.forEach(item => {
            // Chỉ tính các item có checkbox được chọn
            const checkbox = item.querySelector('.select-cart-item');
            if (checkbox && checkbox.checked) {
                // Sử dụng data-price đã bao gồm extra price từ variants
                const price = parseFloat(item.dataset.price) || 0;
                const quantityInput = item.querySelector('.quantity-input');
                const quantity = quantityInput ? (parseInt(quantityInput.value) || 0) : 0;
                cartTotal += price * quantity;
            }
        });

        // Update subtotal
        const subtotalElement = document.getElementById('subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = this.formatCurrency(cartTotal);
        }

        // Update total (considering discount)
        const discountElement = document.getElementById('discount-amount');
        const totalElement = document.getElementById('total-amount');
        
        if (totalElement) {
            let finalTotal = cartTotal;
            if (discountElement) {
                const discountText = discountElement.textContent.replace(/[^\d]/g, '');
                const discount = parseFloat(discountText) || 0;
                finalTotal = cartTotal - discount;
            }
            totalElement.textContent = this.formatCurrency(Math.max(0, finalTotal));
        }
    },

    // Format currency
    formatCurrency(amount) {
        const numAmount = parseFloat(amount) || 0;
        return new Intl.NumberFormat('vi-VN').format(numAmount) + 'đ';
    },

    // Gắn sự kiện cho checkbox chọn sản phẩm để mua
    bindSelectCheckboxes() {
        // Sử dụng event delegation để lắng nghe thay đổi trên tất cả checkbox
        document.addEventListener('change', (e) => {
            const checkbox = e.target.closest('.select-cart-item');
            if (checkbox) {
                const cartId = checkbox.dataset.cartId;
                const isSelected = checkbox.checked ? 1 : 0;
                
                // Disable checkbox temporarily
                checkbox.disabled = true;
                
                $.ajax({
                    url: '/cart/update-selected',
                    method: 'POST',
                    data: {
                        cart_id: cartId,
                        is_selected: isSelected,
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    success: (response) => {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.success);
                            }
                            
                            // Cập nhật tổng tiền ngay lập tức mà không reload trang
                            this.updateCartTotals();
                            
                            // Trigger cart total update in cart.js
                            if (typeof window.updateCartTotal === 'function') {
                                window.allowUpdateCartTotal = true;
                                window.updateCartTotal();
                                window.allowUpdateCartTotal = false;
                            }
                        }
                        
                        // Re-enable checkbox
                        checkbox.disabled = false;
                    },
                    error: (xhr) => {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(xhr.responseJSON?.error || 'Có lỗi khi cập nhật lựa chọn');
                        }
                        // Revert checkbox state
                        checkbox.checked = !checkbox.checked;
                        checkbox.disabled = false;
                    }
                });
            }
        });
    },
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Đợi một chút để đảm bảo tất cả các thư viện đã load
    setTimeout(() => {
        CartProducts.init();
    }, 100);
});

// Also try when window loads
window.addEventListener('load', function() {
    if (!CartProducts.initialized) {
        CartProducts.init();
    }
});

// Export for use in other modules
window.CartProducts = CartProducts;
