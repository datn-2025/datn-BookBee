document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra jQuery và toastr
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    if (typeof toastr === 'undefined') {
        console.error('Toastr is not loaded');
        return;
    }

    // Cấu hình Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };

    // Format số tiền
    function formatCurrency(amount) {
        // Ensure amount is a valid number
        const numAmount = parseFloat(amount) || 0;
        return new Intl.NumberFormat('vi-VN').format(numAmount) + 'đ';
    }

    // Cập nhật hiển thị giá cho từng item
    function updateItemPriceDisplay(cartItem, quantity) {
        if (!cartItem) return;

        const price = parseFloat(cartItem.dataset.price) || 0;
        const itemTotal = price * quantity;
        
        // Cập nhật tổng tiền của item
        const itemTotalElement = cartItem.querySelector('.item-total');
        if (itemTotalElement) {
            itemTotalElement.textContent = formatCurrency(itemTotal);
        }

        // Tính tổng giỏ hàng
        updateCartTotal();
    }

    // Cập nhật tổng giỏ hàng
    function updateCartTotal() {
        let cartTotal = 0;
        const cartItems = document.querySelectorAll('.cart-item');
        
        if (cartItems.length === 0) {
            console.log('No cart items found');
            return;
        }

        cartItems.forEach(item => {
            const price = parseFloat(item.dataset.price) || 0;
            const quantityInput = item.querySelector('.quantity-input');
            const quantity = quantityInput ? (parseInt(quantityInput.value) || 0) : 0;
            cartTotal += price * quantity;
            
            console.log('Item:', {
                price: price,
                quantity: quantity,
                subtotal: price * quantity
            });
        });

        console.log('Cart total calculated:', cartTotal);

        // Cập nhật hiển thị tổng tiền
        const subtotalElement = document.getElementById('subtotal');
        const totalElement = document.getElementById('total-amount');
        const discountElement = document.getElementById('discount-amount');
        
        // Parse discount amount more carefully
        let discount = 0;
        if (discountElement && discountElement.textContent) {
            const discountText = discountElement.textContent.trim();
            if (discountText !== '0đ' && discountText !== '') {
                // Remove "- " prefix and "đ" suffix, then parse
                const cleanText = discountText.replace(/^-\s*/, '').replace(/[^\d]/g, '');
                discount = parseFloat(cleanText) || 0;
            }
        }

        console.log('Discount amount:', discount);

        if (subtotalElement) {
            subtotalElement.textContent = formatCurrency(cartTotal);
            console.log('Updated subtotal:', subtotalElement.textContent);
        }
        if (totalElement) {
            const finalTotal = Math.max(0, cartTotal - discount);
            totalElement.textContent = formatCurrency(finalTotal);
            console.log('Updated total:', totalElement.textContent);
        }
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Cập nhật số lượng
    const updateQuantity = debounce(function(cartItem, newQuantity) {
        if (!cartItem) return;

        const bookId = cartItem.dataset.bookId;
        const quantityInput = cartItem.querySelector('.quantity-input');
        const increaseBtn = cartItem.querySelector('.increase-quantity');
        const decreaseBtn = cartItem.querySelector('.decrease-quantity');
        const stockAmount = cartItem.querySelector('.stock-amount');
        const currentStock = parseInt(cartItem.dataset.stock) || 0;
        const oldQuantity = parseInt(quantityInput?.dataset.lastValue) || 1;

        // Validate quantity
        if (newQuantity > currentStock) {
            toastr.error(`Số lượng không được vượt quá ${currentStock} sản phẩm (số lượng tồn kho hiện tại)`);
            if (quantityInput) quantityInput.value = oldQuantity;
            return;
        }

        // Disable controls
        [quantityInput, increaseBtn, decreaseBtn].forEach(el => {
            if (el) el.disabled = true;
        });

        // Thêm loading state
        cartItem.classList.add('loading');                // Cập nhật UI trước
                updateItemPriceDisplay(cartItem, newQuantity);

        // Gọi API cập nhật
        $.ajax({
            url: '/cart/update',
            method: 'POST',
            data: {
                book_id: bookId,
                quantity: newQuantity,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.success);
                    
                    // Cập nhật data attributes
                    if (response.data) {
                        cartItem.dataset.price = response.data.price;
                        cartItem.dataset.stock = response.data.stock;
                        
                        // Cập nhật stock display
                        if (stockAmount) {
                            stockAmount.textContent = response.data.stock;
                        }

                        // Cập nhật max của input
                        if (quantityInput) {
                            quantityInput.max = response.data.stock;
                            quantityInput.dataset.lastValue = newQuantity;
                        }

                        // Cập nhật button states
                        if (increaseBtn) {
                            increaseBtn.disabled = newQuantity >= response.data.stock;
                        }
                        if (decreaseBtn) {
                            decreaseBtn.disabled = newQuantity <= 1;
                        }

                        // Cập nhật giá và tổng tiền
                        updateItemPriceDisplay(cartItem, newQuantity);
                    }
                } else if (response.error) {
                    toastr.error(response.error);
                    resetToOldValue(cartItem, oldQuantity);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response?.error) {
                    if (response.available_stock !== undefined) {
                        toastr.error(`${response.error} (Số lượng tồn kho hiện tại: ${response.available_stock} sản phẩm)`);
                        if (quantityInput) {
                            quantityInput.value = Math.min(response.available_stock, oldQuantity);
                            quantityInput.max = response.available_stock;
                        }
                        if (stockAmount) {
                            stockAmount.textContent = response.available_stock;
                        }
                        cartItem.dataset.stock = response.available_stock;
                        updateItemPriceDisplay(cartItem, Math.min(response.available_stock, oldQuantity));
                    } else {
                        toastr.error(response.error);
                        resetToOldValue(cartItem, oldQuantity);
                    }
                } else {
                    toastr.error('Có lỗi xảy ra. Vui lòng thử lại.');
                    resetToOldValue(cartItem, oldQuantity);
                }
            },
            complete: function() {
                // Remove loading state
                cartItem.classList.remove('loading');
                
                // Enable controls
                [quantityInput, increaseBtn, decreaseBtn].forEach(el => {
                    if (el) el.disabled = false;
                });
            }
        });
    }, 500);

    // Reset về giá trị cũ
    function resetToOldValue(cartItem, oldQuantity) {
        const quantityInput = cartItem.querySelector('.quantity-input');
        if (quantityInput) {
            quantityInput.value = oldQuantity;
        }
        updateItemPriceDisplay(cartItem, oldQuantity);
    }

    // Xử lý sự kiện cho mỗi cart item
    document.querySelectorAll('.cart-item').forEach(cartItem => {
        const quantityInput = cartItem.querySelector('.quantity-input');
        const increaseBtn = cartItem.querySelector('.increase-quantity');
        const decreaseBtn = cartItem.querySelector('.decrease-quantity');
        const currentStock = parseInt(cartItem.dataset.stock) || 0;

        if (quantityInput) {
            // Lưu giá trị ban đầu
            quantityInput.dataset.lastValue = quantityInput.value;

            // Xử lý thay đổi input
            quantityInput.addEventListener('input', function() {
                const newValue = parseInt(this.value) || 0;
                const min = parseInt(this.min) || 1;
                const max = parseInt(this.max) || currentStock;

                // Kiểm tra nếu vượt quá tồn kho
                if (newValue > currentStock) {
                    toastr.error(`Số lượng không được vượt quá ${currentStock} sản phẩm (số lượng tồn kho hiện tại)`);
                    this.value = currentStock;
                    return;
                }

                // Validate và cập nhật giá trị
                if (newValue < min) {
                    this.value = min;
                    toastr.warning(`Số lượng tối thiểu là ${min}`);
                } else if (newValue > max) {
                    this.value = max;
                    toastr.warning(`Số lượng tối đa là ${max}`);
                }
            });

            // Xử lý khi người dùng hoàn thành việc nhập
            quantityInput.addEventListener('change', function() {
                const newValue = parseInt(this.value) || 1;
                if (newValue > 0) {
                    updateQuantity(cartItem, newValue);
                }
            });
        }

        // Xử lý nút tăng
        if (increaseBtn) {
            increaseBtn.addEventListener('click', function() {
                if (!quantityInput) return;
                
                const currentValue = parseInt(quantityInput.value) || 1;
                const max = parseInt(quantityInput.max) || parseInt(cartItem.dataset.stock) || 1;
                
                if (currentValue < max) {
                    quantityInput.value = currentValue + 1;
                    updateQuantity(cartItem, currentValue + 1);
                }
            });
        }

        // Xử lý nút giảm
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', function() {
                if (!quantityInput) return;
                
                const currentValue = parseInt(quantityInput.value) || 1;
                const min = parseInt(quantityInput.min) || 1;
                
                if (currentValue > min) {
                    quantityInput.value = currentValue - 1;
                    updateQuantity(cartItem, currentValue - 1);
                }
            });
        }
    });

    // Xử lý xóa sản phẩm
    document.querySelectorAll('.remove-item').forEach(button => {
        if (!button) return;
        
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            if (!cartItem) return;
            
            const bookId = this.dataset.bookId;
            if (!bookId) return;

            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                // Disable item controls
                const controls = cartItem.querySelectorAll('button, input');
                controls.forEach(el => {
                    if (el) el.disabled = true;
                });
                
                // Add loading state
                cartItem.classList.add('loading');

                // Get additional item data for precise removal
                const bookFormatId = cartItem.dataset.bookFormatId || null;
                const attributeValueIds = cartItem.dataset.attributeValueIds || null;

                $.ajax({
                    url: '/cart/remove',
                    method: 'POST',
                    data: {
                        book_id: bookId,
                        book_format_id: bookFormatId,
                        attribute_value_ids: attributeValueIds,
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.success);
                            
                            // Always reload page to update all cart data
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                            
                        } else if (response.error) {
                            toastr.error(response.error);
                            // Enable controls back
                            controls.forEach(el => {
                                if (el) el.disabled = false;
                            });
                            cartItem.classList.remove('loading');
                        }
                    },
                    error: function() {
                        toastr.error('Có lỗi xảy ra. Vui lòng thử lại.');
                        // Enable controls back
                        controls.forEach(el => {
                            if (el) el.disabled = false;
                        });
                        cartItem.classList.remove('loading');
                    }
                });
            }
        });
    });

    // VOUCHER SYSTEM - Logic đơn giản và đáng tin cậy
    function initVoucherSystem() {
        // Event delegation để handle các button voucher
        $(document).on('click', '#apply-voucher', function(e) {
            e.preventDefault();
            applyVoucher();
        });

        $(document).on('click', '#remove-voucher-btn', function(e) {
            e.preventDefault();
            removeVoucher();
        });
    }

    function applyVoucher() {
        const voucherInput = $('#voucher-code');
        const voucherCode = voucherInput.val().trim();
        const applyBtn = $('#apply-voucher');

        if (!voucherCode) {
            toastr.error('Vui lòng nhập mã giảm giá');
            return;
        }

        // Disable input và button, hiển thị loading
        voucherInput.prop('disabled', true);
        applyBtn.prop('disabled', true);
        applyBtn.html('<i class="fas fa-spinner fa-spin me-1"></i><span class="btn-text">Đang áp dụng...</span>');

        const currentTotal = parseFloat($('#subtotal').text().replace(/[^\d]/g, '')) || 0;

        $.ajax({
            url: '/cart/apply-voucher',
            method: 'POST',
            data: {
                code: voucherCode,
                total: currentTotal,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.success);
                    
                    // Cập nhật UI thành chế độ "remove"
                    switchToRemoveMode(voucherCode);
                    
                    // Cập nhật giá
                    updateVoucherPriceDisplay(response.discount || 0);
                    
                    // Hiển thị success indicator
                    showVoucherSuccess();
                } else {
                    toastr.error(response.error || 'Có lỗi xảy ra');
                    resetApplyButton();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.error || 'Có lỗi xảy ra khi áp dụng mã giảm giá');
                resetApplyButton();
            }
        });
    }

    function removeVoucher() {
        const removeBtn = $('#remove-voucher-btn');
        
        // Disable button và hiển thị loading
        removeBtn.prop('disabled', true);
        removeBtn.html('<i class="fas fa-spinner fa-spin me-1"></i><span class="btn-text">Đang xóa...</span>');

        $.ajax({
            url: '/cart/remove-voucher',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.success);
                    
                    // Cập nhật UI thành chế độ "apply"
                    switchToApplyMode();
                    
                    // Reset giá
                    resetVoucherPriceDisplay();
                    
                    // Ẩn success indicator
                    hideVoucherSuccess();
                } else {
                    toastr.error(response.error || 'Có lỗi xảy ra');
                    resetRemoveButton();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.error || 'Có lỗi xảy ra khi xóa mã giảm giá');
                resetRemoveButton();
            }
        });
    }

    function switchToRemoveMode(voucherCode) {
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
    }

    function switchToApplyMode() {
        const voucherInput = $('#voucher-code');
        const buttonContainer = $('.voucher-button-container');
        
        voucherInput.val('');
        voucherInput.prop('disabled', false);
        voucherInput.removeClass('voucher-applied');
        
        buttonContainer.html(`
            <button type="button" id="apply-voucher" class="btn btn-primary voucher-btn apply-voucher-btn">
                <i class="fas fa-check me-1"></i>
                <span class="btn-text">Áp dụng</span>
            </button>
        `);
    }

    function resetApplyButton() {
        const voucherInput = $('#voucher-code');
        const applyBtn = $('#apply-voucher');
        
        voucherInput.prop('disabled', false);
        applyBtn.prop('disabled', false);
        applyBtn.html('<i class="fas fa-check me-1"></i><span class="btn-text">Áp dụng</span>');
    }

    function resetRemoveButton() {
        const removeBtn = $('#remove-voucher-btn');
        removeBtn.prop('disabled', false);
        removeBtn.html('<i class="fas fa-times me-1"></i><span class="btn-text">Xóa</span>');
    }

    function updateVoucherPriceDisplay(discountAmount) {
        const discountElement = $('#discount-amount');
        const totalElement = $('#total-amount');
        const subtotalElement = $('#subtotal');
        
        if (discountElement.length) {
            if (discountAmount > 0) {
                discountElement.text('- ' + formatCurrency(discountAmount));
                discountElement.css('color', '#dc3545');
            } else {
                discountElement.text('0đ');
                discountElement.css('color', '');
            }
        }
        
        if (totalElement.length && subtotalElement.length) {
            const subtotalText = subtotalElement.text().replace(/[^\d]/g, '');
            const subtotal = parseFloat(subtotalText) || 0;
            const newTotal = Math.max(0, subtotal - discountAmount);
            totalElement.text(formatCurrency(newTotal));
        }
    }

    function resetVoucherPriceDisplay() {
        const discountElement = $('#discount-amount');
        const totalElement = $('#total-amount');
        const subtotalElement = $('#subtotal');
        
        if (discountElement.length) {
            discountElement.text('0đ');
            discountElement.css('color', '');
        }
        
        if (totalElement.length && subtotalElement.length) {
            const subtotalText = subtotalElement.text().replace(/[^\d]/g, '');
            const subtotal = parseFloat(subtotalText) || 0;
            totalElement.text(formatCurrency(subtotal));
        }
    }

    function showVoucherSuccess() {
        hideVoucherSuccess(); // Xóa cái cũ trước
        
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
    }

    function hideVoucherSuccess() {
        $('.voucher-success-indicator').remove();
    }

    // Khởi tạo voucher system
    initVoucherSystem();

    // Cập nhật tổng giỏ hàng khi trang load
    updateCartTotal();

    // Xóa tất cả sản phẩm trong giỏ hàng
    const clearCartBtn = document.getElementById('clear-cart-btn');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
                clearCartBtn.disabled = true;
                clearCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xóa...';

                $.ajax({
                    url: '/cart/clear',
                    method: 'POST',
                    data: {
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.success);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response?.error || 'Có lỗi xảy ra khi xóa giỏ hàng');
                        
                        clearCartBtn.disabled = false;
                        clearCartBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Xóa tất cả';
                    }
                });
            }
        });
    }

    // Thêm tất cả từ danh sách yêu thích
    const addWishlistBtn = document.getElementById('add-wishlist-btn');
    if (addWishlistBtn) {
        addWishlistBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (addWishlistBtn.classList.contains('loading')) return;
            const originalHtml = addWishlistBtn.innerHTML;
            addWishlistBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang chuyển...';
            addWishlistBtn.classList.add('loading'); // KHÔNG thêm 'disabled' để tránh bị chặn click
            setTimeout(() => {
                window.location.href = addWishlistBtn.href || '/wishlist';
            }, 500);
        });
    }
});