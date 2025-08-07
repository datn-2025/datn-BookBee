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

        // KHÔNG gọi updateCartTotal ở đây nữa!
    }

    // Flag để chỉ cho phép updateCartTotal khi gọi từ checkbox change
    let allowUpdateCartTotal = false;

    // Cập nhật tổng giỏ hàng: chỉ tính các item có checkbox được chọn
    function updateCartTotal() {
        if (!allowUpdateCartTotal) {
            return;
        }
        let cartTotal = 0;
        const cartItems = document.querySelectorAll('.cart-item');
        const checkedItems = [];
        
        cartItems.forEach(item => {
            const checkbox = item.querySelector('.select-cart-item');
            if (checkbox && checkbox.checked) {
                // Sử dụng data-price đã bao gồm extra price từ variants
                const price = parseFloat(item.dataset.price) || 0;
                const quantityInput = item.querySelector('.quantity-input');
                const quantity = quantityInput ? (parseInt(quantityInput.value) || 0) : 0;
                cartTotal += price * quantity;
                checkedItems.push(item.dataset.bookId || '[no-id]');
            }
        });

        // Cập nhật hiển thị tổng tiền
        const subtotalElement = document.getElementById('subtotal');
        const totalElement = document.getElementById('total-amount');
        const discountElement = document.getElementById('discount-amount');

        let discount = 0;
        if (discountElement && discountElement.textContent) {
            const discountText = discountElement.textContent.trim();
            if (discountText !== '0đ' && discountText !== '') {
                const cleanText = discountText.replace(/^\-\s*/, '').replace(/[^\d]/g, '');
                discount = parseFloat(cleanText) || 0;
            }
        }

        if (subtotalElement) {
            subtotalElement.textContent = formatCurrency(cartTotal);
        }
        if (totalElement) {
            const finalTotal = Math.max(0, cartTotal - discount);
            totalElement.textContent = formatCurrency(finalTotal);
        }
        
        console.log('Cart total updated:', {
            checkedItems: checkedItems.length,
            subtotal: cartTotal,
            discount: discount,
            total: Math.max(0, cartTotal - discount)
        });
    }

    // Make functions available globally for cart_products.js
    window.updateCartTotal = updateCartTotal;
    window.allowUpdateCartTotal = allowUpdateCartTotal;

    // Khi thay đổi checkbox, cập nhật tổng tiền
    $(document).on('change', '.select-cart-item', function() {
        const bookId = $(this).closest('.cart-item').data('book-id');
        window.allowUpdateCartTotal = true;
        updateCartTotal();
        window.allowUpdateCartTotal = false;
    });

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // // Cập nhật số lượng
    // const updateQuantity = debounce(function(cartItem, newQuantity) {
    //     if (!cartItem) return;

    //     const bookId = cartItem.dataset.bookId;
    //     const quantityInput = cartItem.querySelector('.quantity-input');
    //     const increaseBtn = cartItem.querySelector('.increase-quantity');
    //     const decreaseBtn = cartItem.querySelector('.decrease-quantity');
    //     const stockAmount = cartItem.querySelector('.stock-amount');
    //     const currentStock = parseInt(cartItem.dataset.stock) || 0;
    //     const oldQuantity = parseInt(quantityInput?.dataset.lastValue) || 1;
    //     const checkbox = cartItem.querySelector('.select-cart-item');

    //     // Validate quantity
    //     if (newQuantity > currentStock) {
    //         toastr.error(`Số lượng không được vượt quá ${currentStock} sản phẩm (số lượng tồn kho hiện tại)`);
    //         if (quantityInput) quantityInput.value = oldQuantity;
    //         return;
    //     }

    //     // Disable controls
    //     [quantityInput, increaseBtn, decreaseBtn].forEach(el => {
    //         if (el) el.disabled = true;
    //     });

    //     // Thêm loading state
    //     cartItem.classList.add('loading');
    //     // Cập nhật UI trước: chỉ cập nhật giá từng sản phẩm
    //     updateItemPriceDisplay(cartItem, newQuantity);

    //     // Gọi API cập nhật
    //     $.ajax({
    //         url: '/cart/update',
    //         method: 'POST',
    //         data: {
    //             book_id: bookId,
    //             quantity: newQuantity,
    //             _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //         },
    //         success: function(response) {
    //             if (response.success) {
    //                 toastr.success(response.success);
    //                 if (response.data) {
    //                     cartItem.dataset.price = response.data.price;
    //                     cartItem.dataset.stock = response.data.stock;
    //                     if (stockAmount) {
    //                         stockAmount.textContent = response.data.stock;
    //                     }
    //                     if (quantityInput) {
    //                         quantityInput.max = response.data.stock;
    //                         quantityInput.value = response.data.quantity; // Đảm bảo input đúng số lượng thực tế
    //                         quantityInput.dataset.lastValue = response.data.quantity;
    //                     }
    //                     if (increaseBtn) {
    //                         increaseBtn.disabled = response.data.quantity >= response.data.stock;
    //                     }
    //                     if (decreaseBtn) {
    //                         decreaseBtn.disabled = response.data.quantity <= 1;
    //                     }
    //                     // Luôn cập nhật lại giá từng sản phẩm
    //                     updateItemPriceDisplay(cartItem, response.data.quantity);
    //                     // CHỈ trigger change cho checkbox nếu checkbox đang được check
    //                     if (checkbox && checkbox.checked) {
    //                         $(checkbox).trigger('change');
    //                     }
    //                     // Nếu chưa check thì KHÔNG trigger change, KHÔNG gọi updateCartTotal
    //                 }
    //             } else if (response.error) {
    //                 toastr.error(response.error);
    //                 resetToOldValue(cartItem, oldQuantity);
    //             }
    //         },
    //         error: function(xhr) {
    //             const response = xhr.responseJSON;
    //             if (response?.error) {
    //                 if (response.available_stock !== undefined) {
    //                     toastr.error(`${response.error} (Số lượng tồn kho hiện tại: ${response.available_stock} sản phẩm)`);
    //                     if (quantityInput) {
    //                         quantityInput.value = Math.min(response.available_stock, oldQuantity);
    //                         quantityInput.max = response.available_stock;
    //                     }
    //                     if (stockAmount) {
    //                         stockAmount.textContent = response.available_stock;
    //                     }
    //                     cartItem.dataset.stock = response.available_stock;
    //                     updateItemPriceDisplay(cartItem, Math.min(response.available_stock, oldQuantity));
    //                     // KHÔNG gọi updateCartTotal ở đây nữa!
    //                 } else {
    //                     toastr.error(response.error);
    //                     resetToOldValue(cartItem, oldQuantity);
    //                 }
    //             } else {
    //                 toastr.error('Có lỗi xảy ra. Vui lòng thử lại.');
    //                 resetToOldValue(cartItem, oldQuantity);
    //             }
    //         },
    //         complete: function() {
    //             cartItem.classList.remove('loading');
    //             [quantityInput, increaseBtn, decreaseBtn].forEach(el => {
    //                 if (el) el.disabled = false;
    //             });
    //         }
    //     });
    // }, 500);

    // // Reset về giá trị cũ
    // function resetToOldValue(cartItem, oldQuantity) {
    //     const quantityInput = cartItem.querySelector('.quantity-input');
    //     if (quantityInput) {
    //         quantityInput.value = oldQuantity;
    //     }
    //     updateItemPriceDisplay(cartItem, oldQuantity);
    // }

    // // DISABLED - Quantity management handled by cart_products.js
    // // Xử lý sự kiện cho mỗi cart item
    // document.querySelectorAll('.cart-item').forEach(cartItem => {
    //     const quantityInput = cartItem.querySelector('.quantity-input');
    //     const increaseBtn = cartItem.querySelector('.increase-quantity');
    //     const decreaseBtn = cartItem.querySelector('.decrease-quantity');
    //     const currentStock = parseInt(cartItem.dataset.stock) || 0;

    //     if (quantityInput) {
    //         // Lưu giá trị ban đầu
    //         quantityInput.dataset.lastValue = quantityInput.value;

    //         // Xử lý thay đổi input (chỉ validate, không cập nhật tổng tiền)
    //         quantityInput.addEventListener('input', function() {
    //             const newValue = parseInt(this.value) || 0;
    //             const min = parseInt(this.min) || 1;
    //             const max = parseInt(this.max) || currentStock;

    //             if (newValue > currentStock) {
    //                 toastr.error(`Số lượng không được vượt quá ${currentStock} sản phẩm (số lượng tồn kho hiện tại)`);
    //                 this.value = currentStock;
    //                 return;
    //             }

    //             if (newValue < min) {
    //                 this.value = min;
    //                 toastr.warning(`Số lượng tối thiểu là ${min}`);
    //             } else if (newValue > max) {
    //                 this.value = max;
    //                 toastr.warning(`Số lượng tối đa là ${max}`);
    //             }
    //             // KHÔNG gọi updateCartTotal ở đây!
    //         });

    //         // Xử lý khi người dùng hoàn thành việc nhập
    //         quantityInput.addEventListener('change', function() {
    //             const newValue = parseInt(this.value) || 1;
    //             if (newValue > 0) {
    //                 updateQuantity(cartItem, newValue);
    //             }
    //         });
    //     }

    //     // Xử lý nút tăng
    //     if (increaseBtn) {
    //         increaseBtn.addEventListener('click', function() {
    //             if (!quantityInput) return;
    //             const currentValue = parseInt(quantityInput.value) || 1;
    //             const max = parseInt(quantityInput.max) || parseInt(cartItem.dataset.stock) || 1;
    //             if (currentValue < max) {
    //                 quantityInput.value = currentValue + 1;
    //                 updateQuantity(cartItem, currentValue + 1);
    //             }
    //         });
    //     }

    //     // Xử lý nút giảm
    //     if (decreaseBtn) {
    //         decreaseBtn.addEventListener('click', function() {
    //             if (!quantityInput) return;
    //             const currentValue = parseInt(quantityInput.value) || 1;
    //             const min = parseInt(quantityInput.min) || 1;
    //             if (currentValue > min) {
    //                 quantityInput.value = currentValue - 1;
    //                 updateQuantity(cartItem, currentValue - 1);
    //             }
    //         });
    //     }
    // });

    // Xử lý xóa sản phẩm - Đã chuyển sang cart_products.js
    // Bỏ qua phần này để tránh xung đột

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

    // Xóa tất cả sản phẩm trong giỏ hàng - Đã chuyển sang cart_products.js
    // Bỏ qua phần này để tránh xung đột

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