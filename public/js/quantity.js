document.addEventListener('DOMContentLoaded', function () {
    // --- Kiểm tra tồn kho cho comboQuantity (combo) ---
    const comboInput = document.getElementById('comboQuantity');
    if (comboInput) {
        // Nút + - cho combo
        const incBtn = document.getElementById('comboIncrementBtn');
        const decBtn = document.getElementById('comboDecrementBtn');
        if (incBtn) {
            incBtn.addEventListener('click', function() {
                let val = parseInt(comboInput.value) || 1;
                const max = parseInt(comboInput.getAttribute('max')) || 1;
                if (val < max) {
                    comboInput.value = val + 1;
                    comboInput.dispatchEvent(new Event('input'));
                }
            });
        }
        if (decBtn) {
            decBtn.addEventListener('click', function() {
                let val = parseInt(comboInput.value) || 1;
                if (val > 1) {
                    comboInput.value = val - 1;
                    comboInput.dispatchEvent(new Event('input'));
                }
            });
        }
        comboInput.addEventListener('input', function() {
            const max = parseInt(comboInput.getAttribute('max')) || 1;
            let val = parseInt(comboInput.value) || 1;
            if (val < 1) val = 1;
            if (val > max) val = max;
            comboInput.value = val;
            // Cập nhật trạng thái nút
            const parent = comboInput.parentElement;
            if (parent) {
                const incBtn = parent.querySelector('button[onclick*="updateComboQty(1)"]');
                if (incBtn) incBtn.disabled = (val >= max);
                const decBtn = parent.querySelector('button[onclick*="updateComboQty(-1)"]');
                if (decBtn) decBtn.disabled = (val <= 1);
            }
        });
        comboInput.addEventListener('blur', function() {
            if (!comboInput.value) comboInput.value = 1;
            comboInput.dispatchEvent(new Event('input'));
        });
        // Khởi tạo trạng thái đúng khi load trang
        comboInput.dispatchEvent(new Event('input'));
    }
    const formatSelect = document.getElementById('bookFormatSelect');
    const priceDisplay = document.getElementById('bookPrice');
    const originalPriceElement = document.getElementById('originalPrice');
    const stockDisplay = document.getElementById('bookStock');
    const quantityInput = document.getElementById('quantity');
    const productQuantityDisplay = document.getElementById('productQuantity');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const incrementBtn = document.getElementById('incrementBtn');
    const decrementBtn = document.getElementById('decrementBtn');
    const discountText = document.getElementById('discountText');
    const discountAmount = document.getElementById('discountAmount');

    const quantityGroup = quantityInput?.closest('.mt-4.flex');
    const attributeGroups = document.querySelectorAll('[id^="attribute_"]');

    function updatePriceAndStock() {
        const selectedOption = formatSelect?.selectedOptions?.[0];
        let basePrice = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
        let discount = parseFloat(selectedOption?.getAttribute('data-discount')) || 0;
        let stock = parseInt(selectedOption?.getAttribute('data-stock')) || 0;
        let isEbook = selectedOption?.textContent?.toLowerCase().includes('ebook');

        let totalExtra = 0;
        document.querySelectorAll('select[id^="attribute_"]').forEach(select => {
            const extra = parseFloat(select.selectedOptions?.[0]?.getAttribute('data-price')) || 0;
            totalExtra += extra;
        });

        const totalBase = basePrice + totalExtra;

        // Giá cuối cùng đã được tính sẵn từ server, chỉ cần hiển thị
        let finalPrice = totalBase;
        if (discount > 0) {
            // Giá đã được tính sẵn, chỉ trừ discount để hiển thị
            finalPrice = totalBase - discount;
            finalPrice = Math.max(0, finalPrice);
        }

        priceDisplay.textContent = `${finalPrice.toLocaleString('vi-VN', { minimumFractionDigits: 0 })}₫`;
        priceDisplay.dataset.basePrice = totalBase;

        if (originalPriceElement) {
            if (discount > 0) {
                originalPriceElement.style.display = 'inline';
                originalPriceElement.textContent = `${totalBase.toLocaleString('vi-VN', { minimumFractionDigits: 0 })}₫`;
            } else {
                originalPriceElement.style.display = 'none';
            }
        }

        if (discountText && discountAmount) {
            if (discount > 0) {
                discountText.style.display = 'inline';
                // Hiển thị discount như số tiền VNĐ với định dạng
                const formattedDiscount = discount.toLocaleString('vi-VN', { minimumFractionDigits: 0 });
                discountAmount.textContent = formattedDiscount;
            } else {
                discountText.style.display = 'none';
            }
        }

        // Badge logic giống combo
        const stockBadge = document.getElementById('stockBadge');
        const stockDot = document.getElementById('stockDot');
        const stockText = document.getElementById('stockText');
        const stockQuantityDisplay = document.getElementById('stockQuantityDisplay');
        const productQuantity = document.getElementById('productQuantity');

        // Reset badge class
        stockBadge.className = 'inline-flex items-center px-3 py-1 text-sm font-semibold border adidas-font uppercase tracking-wider';
        let badgeClass = '', dotClass = '', statusText = '';
        if (isEbook) {
            badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
            dotClass = 'bg-blue-500';
            statusText = 'EBOOK - CÓ SẴN';
        } else if (stock > 0) {
            badgeClass = 'bg-green-50 text-green-700 border-green-200';
            dotClass = 'bg-green-500';
            statusText = 'CÒN HÀNG';
        } else if (stock === 0) {
            badgeClass = 'bg-red-50 text-red-700 border-red-200';
            dotClass = 'bg-red-500';
            statusText = 'HẾT HÀNG';
        } else if (stock === -1) {
            badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
            dotClass = 'bg-yellow-500';
            statusText = 'SẮP RA MẮT';
        } else if (stock === -2) {
            badgeClass = 'bg-gray-100 text-gray-700 border-gray-300';
            dotClass = 'bg-gray-500';
            statusText = 'NGƯNG KINH DOANH';
        } else {
            badgeClass = 'bg-red-50 text-red-700 border-red-200';
            dotClass = 'bg-red-500';
            statusText = 'HẾT HÀNG';
        }
        stockBadge.className += ' ' + badgeClass;
        stockDot.className = 'w-2 h-2 rounded-full mr-2 inline-block ' + dotClass;
        stockText.textContent = statusText;

        // Số lượng còn lại
        if ((stock > 0 || isEbook) && stock !== -1 && stock !== -2) {
            stockQuantityDisplay.style.display = '';
            if (productQuantity) productQuantity.textContent = stock;
        } else {
            stockQuantityDisplay.style.display = 'none';
        }

        // Ẩn input số lượng nếu là ebook hoặc kiểm tra trạng thái không khả dụng cho ebook
        if (isEbook) {
            const isUnavailable = stock === -1 || stock === -2; // Sắp ra mắt (-1) hoặc Ngừng kinh doanh (-2)
            
            if (quantityGroup) quantityGroup.style.display = 'none';
            quantityInput.value = 1;
            quantityInput.disabled = true;

            attributeGroups.forEach(select => {
                const label = document.querySelector(`label[for="${select.id}"]`);
                const isLanguage = label?.textContent.toLowerCase().includes('ngôn ngữ');
                select.closest('.col-span-1').style.display = isLanguage ? 'block' : 'none';
            });

            productQuantityDisplay.textContent = 'Không giới hạn';
            stockDisplay.textContent = isUnavailable ? (stock === -1 ? 'Sắp ra mắt' : 'Ngừng kinh doanh') : 'Có thể mua';
            stockDisplay.className = `font-bold px-3 py-1.5 rounded text-white ${isUnavailable ? 'bg-gray-500' : 'bg-blue-500'}`;
            addToCartBtn.disabled = isUnavailable;
            addToCartBtn.classList.toggle('bg-gray-300', isUnavailable);
            addToCartBtn.classList.toggle('bg-black', !isUnavailable);
            incrementBtn.disabled = true;
            decrementBtn.disabled = true;
        } else {
            if (quantityGroup) quantityGroup.style.display = 'flex';
            quantityInput.disabled = false;

            // Hiện lại tất cả thuộc tính
            document.querySelectorAll('#bookAttributesGroup .space-y-2').forEach(group => {
                group.style.display = '';
            });

            productQuantityDisplay.textContent = stock > 0 ? stock : 0;
            quantityInput.max = stock;
            if (parseInt(quantityInput.value) > stock) {
                quantityInput.value = stock > 0 ? 1 : 0;
            }

            const outOfStock = stock <= 0;
            const isUnavailable = stock === -1 || stock === -2; // Sắp ra mắt (-1) hoặc Ngừng kinh doanh (-2)
            
            addToCartBtn.disabled = outOfStock || isUnavailable;
            addToCartBtn.classList.toggle('bg-gray-300', outOfStock || isUnavailable);
            addToCartBtn.classList.toggle('bg-black', !outOfStock && !isUnavailable);
            
            // Ẩn/hiện nút cộng trừ số lượng dựa trên trạng thái
            const shouldDisableQuantityControls = outOfStock || isUnavailable;
            incrementBtn.disabled = shouldDisableQuantityControls;
            decrementBtn.disabled = shouldDisableQuantityControls;
            
            // Ẩn hoàn toàn quantityGroup nếu sản phẩm không khả dụng
            if (quantityGroup) {
                if (shouldDisableQuantityControls) {
                    quantityGroup.style.display = 'none';
                } else {
                    quantityGroup.style.display = 'flex';
                }
            }

            stockDisplay.textContent = outOfStock ? 'Hết hàng' : 'Còn hàng';
            stockDisplay.className = `font-bold px-3 py-1.5 rounded text-white ${outOfStock ? 'bg-gray-900' : 'bg-green-500'}`;
        }
    }

    incrementBtn?.addEventListener('click', () => {
        if (!quantityInput) return;
        const max = parseInt(quantityInput.max) || 999;
        let val = parseInt(quantityInput.value) || 1;
        if (val < max) {
            quantityInput.value = val + 1;
            quantityInput.dispatchEvent(new Event('input'));
            quantityInput.dispatchEvent(new Event('change'));
        }
    });

    decrementBtn?.addEventListener('click', () => {
        if (!quantityInput) return
        let val = parseInt(quantityInput.value) || 1;
        if (val > 1) {
            quantityInput.value = val - 1;
            quantityInput.dispatchEvent(new Event('input'));
            quantityInput.dispatchEvent(new Event('change'));
        }
    });

    quantityInput?.addEventListener('input', () => {
        let val = parseInt(quantityInput.value) || 0;
        const max = parseInt(quantityInput.max);
        if (val < 1) val = 1;
        if (val > max) val = max;
        quantityInput.value = val;
    });

    quantityInput?.addEventListener('blur', () => {
        if (!quantityInput.value) {
            quantityInput.value = 1;
        }
    });

    formatSelect?.addEventListener('change', updatePriceAndStock);
    document.querySelectorAll('select[id^="attribute_"]').forEach(select => {
        select.addEventListener('change', updatePriceAndStock);
    });

    updatePriceAndStock();
});
