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
    const formatSelect = document.getElementById('bookFormatSelect'); // ✅ FIXED: Use correct ID
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

    // 🎯 HELPER FUNCTION: Lấy stock hiện tại từ biến thể đã chọn
    function getCurrentVariantStock() {
        const selectedVariantRadio = document.querySelector('input[name="selected_variant"]:checked');
        if (selectedVariantRadio && selectedVariantRadio.dataset.stock) {
            return parseInt(selectedVariantRadio.dataset.stock) || 0;
        }
        
        // Fallback về stock của format nếu không có biến thể
        const selectedOption = formatSelect?.selectedOptions?.[0];
        return parseInt(selectedOption?.getAttribute('data-stock')) || 0;
    }

    function updatePriceAndStock() {
        const selectedOption = formatSelect?.selectedOptions?.[0];
        let basePrice = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
        let discount = parseFloat(selectedOption?.getAttribute('data-discount')) || 0;
        let stock = parseInt(selectedOption?.getAttribute('data-stock')) || 0;
        let isEbook = selectedOption?.textContent?.toLowerCase().includes('ebook');

        let totalExtra = 0;
        let variantStock = null; // Biến để lưu stock của biến thể
        
        // Updated to work with new variant combination system - using radio buttons
        const selectedVariantRadio = document.querySelector('input[name="selected_variant"]:checked');
        if (selectedVariantRadio && selectedVariantRadio.value) {
            const extra = parseFloat(selectedVariantRadio.dataset.extraPrice) || 0;
            totalExtra += extra;
            
            // 🎯 LẤY STOCK TỪ BIẾN THỂ ĐÃ CHỌN
            variantStock = parseInt(selectedVariantRadio.dataset.stock) || 0;
            console.log('🔢 quantity.js: Using variant stock:', variantStock, 'for variant:', selectedVariantRadio.value);
        }
        
        // 📊 SỬ DỤNG STOCK CỦA BIẾN THỂ NẾU CÓ, KHÔNG THÌ DÙNG STOCK CỦA FORMAT
        const effectiveStock = variantStock !== null ? variantStock : stock;
        console.log('📊 quantity.js: Effective stock calculation:', {
            formatStock: stock,
            variantStock: variantStock,
            effectiveStock: effectiveStock,
            isEbook: isEbook
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
        } else if (effectiveStock > 0) {
            badgeClass = 'bg-green-50 text-green-700 border-green-200';
            dotClass = 'bg-green-500';
            statusText = 'CÒN HÀNG';
        } else if (effectiveStock === 0) {
            badgeClass = 'bg-red-50 text-red-700 border-red-200';
            dotClass = 'bg-red-500';
            statusText = 'HẾT HÀNG';
        } else if (effectiveStock === -1) {
            badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
            dotClass = 'bg-yellow-500';
            statusText = 'SẮP RA MẮT';
        } else if (effectiveStock === -2) {
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
        if ((effectiveStock > 0 || isEbook) && effectiveStock !== -1 && effectiveStock !== -2) {
            stockQuantityDisplay.style.display = '';
            if (productQuantity) productQuantity.textContent = effectiveStock;
        } else {
            stockQuantityDisplay.style.display = 'none';
        }

        // Ẩn input số lượng nếu là ebook hoặc kiểm tra trạng thái không khả dụng cho ebook
        if (isEbook) {
            console.log('📱 quantity.js: Processing EBOOK format');
            const isUnavailable = effectiveStock === -1 || effectiveStock === -2; // Sắp ra mắt (-1) hoặc Ngừng kinh doanh (-2)
            if (quantityGroup) quantityGroup.style.display = 'none';
            quantityInput.value = 1;
            quantityInput.disabled = true;

            // Ẩn bookAttributesGroup (hệ thống biến thể mới) cho ebook
            const bookAttributesGroup = document.getElementById('bookAttributesGroup');
            if (bookAttributesGroup) {
                console.log('🔴 quantity.js: Hiding bookAttributesGroup for ebook');
                bookAttributesGroup.style.display = 'none';
            }

            // Ẩn tất cả attribute group (hệ thống cũ) trừ thuộc tính ngôn ngữ (nếu có)
            attributeGroups.forEach(select => {
                const attributeItem = select.closest('.attribute-item');
                if (attributeItem) {
                    const label = attributeItem.querySelector('label');
                    const isLanguage = label?.textContent.toLowerCase().includes('ngôn ngữ');
                    attributeItem.style.display = isLanguage ? 'block' : 'none';
                }
            });

            productQuantityDisplay.textContent = 'Không giới hạn';
            stockDisplay.textContent = isUnavailable ? (effectiveStock === -1 ? 'Sắp ra mắt' : 'Ngừng kinh doanh') : 'Có thể mua';
            stockDisplay.className = `font-bold px-3 py-1.5 rounded text-white ${isUnavailable ? 'bg-gray-500' : 'bg-blue-500'}`;
            addToCartBtn.disabled = isUnavailable;
            addToCartBtn.classList.toggle('bg-gray-300', isUnavailable);
            addToCartBtn.classList.toggle('bg-black', !isUnavailable);
            incrementBtn.disabled = true;
            decrementBtn.disabled = true;
        } else {
            console.log('📚 quantity.js: Processing PHYSICAL book format');
            if (quantityGroup) quantityGroup.style.display = 'flex';
            quantityInput.disabled = false;

            // Hiện lại bookAttributesGroup (hệ thống biến thể mới) cho sách vật lý
            const bookAttributesGroup = document.getElementById('bookAttributesGroup');
            if (bookAttributesGroup) {
                // 🔄 CẢI THIỆN: Kiểm tra xem có bị ẩn bởi server hay không
                const serverStyle = bookAttributesGroup.getAttribute('style');
                const isHiddenByServer = serverStyle && serverStyle.includes('display: none') && !serverStyle.includes('display:none');
                const isCurrentlyHidden = bookAttributesGroup.style.display === 'none';
                
                console.log('📚 quantity.js: Physical book attributes check:', {
                    serverStyle: serverStyle,
                    isHiddenByServer: isHiddenByServer,
                    isCurrentlyHidden: isCurrentlyHidden,
                    shouldShow: !isHiddenByServer
                });
                
                // Chỉ hiện lại nếu không bị server ẩn, hoặc đang ẩn do ebook trước đó
                if (!isHiddenByServer) {
                    console.log('✅ quantity.js: Showing bookAttributesGroup for physical book');
                    bookAttributesGroup.style.display = 'block';
                } else {
                    console.log('❌ quantity.js: Attributes hidden by server for physical book');
                }
            }

            // LUÔN hiện lại tất cả thuộc tính (biến thể cũ) khi là sách vật lý
            attributeGroups.forEach(select => {
                const attributeItem = select.closest('.attribute-item');
                if (attributeItem) {
                    attributeItem.style.display = '';
                }
            });

            // 🎯 CẬP NHẬT LOGIC DỰA VÀO STOCK CỦA BIẾN THỂ
            productQuantityDisplay.textContent = effectiveStock > 0 ? effectiveStock : 0;
            quantityInput.max = effectiveStock;
            if (parseInt(quantityInput.value) > effectiveStock) {
                quantityInput.value = effectiveStock > 0 ? 1 : 0;
            }

            const outOfStock = effectiveStock <= 0;
            const isUnavailable = effectiveStock === -1 || effectiveStock === -2; // Sắp ra mắt (-1) hoặc Ngừng kinh doanh (-2)
            addToCartBtn.disabled = outOfStock || isUnavailable;
            addToCartBtn.classList.toggle('bg-gray-300', outOfStock || isUnavailable);
            addToCartBtn.classList.toggle('bg-black', !outOfStock && !isUnavailable);
            
            // 🔢 CẬP NHẬT NÚT +/- DỰA VÀO STOCK BIẾN THỂ
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
            
            console.log('📚 quantity.js: Updated quantity controls for variant stock:', effectiveStock);
        }

        // 🔄 ĐỒNG BỘ: Trigger custom event để thông báo cho các script khác
        setTimeout(() => {
            const formatChangeEvent = new CustomEvent('quantityJsFormatProcessed', {
                detail: { isEbook, stock: effectiveStock, formatSelect }
            });
            document.dispatchEvent(formatChangeEvent);
            console.log('🔄 quantity.js: Dispatched quantityJsFormatProcessed event', { isEbook, stock: effectiveStock });
        }, 10);
    }

    incrementBtn?.addEventListener('click', () => {
        // 🎯 SỬ DỤNG HELPER FUNCTION ĐỂ LẤY STOCK HIỆN TẠI
        const maxStock = getCurrentVariantStock();
        let val = parseInt(quantityInput.value) || 1;
        
        if (val < maxStock && maxStock > 0) {
            quantityInput.value = val + 1;
            // Cập nhật max attribute của input element
            quantityInput.max = maxStock;
            console.log('➕ quantity.js: Incremented to', val + 1, 'variant stock:', maxStock);
        } else {
            console.log('➕ quantity.js: Cannot increment - reached max variant stock:', maxStock);
        }
    });

    decrementBtn?.addEventListener('click', () => {
        let val = parseInt(quantityInput.value) || 1;
        if (val > 1) {
            quantityInput.value = val - 1;
            console.log('➖ quantity.js: Decremented to', val - 1);
        }
    });

    quantityInput?.addEventListener('input', () => {
        let val = parseInt(quantityInput.value) || 0;
        
        // 🎯 SỬ DỤNG HELPER FUNCTION ĐỂ LẤY STOCK HIỆN TẠI
        const maxStock = getCurrentVariantStock();
        
        if (val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        quantityInput.value = val;
        
        // Cập nhật max attribute của input element
        quantityInput.max = maxStock;
        
        console.log('✏️ quantity.js: Input validated', val, 'variant stock:', maxStock);
    });

    quantityInput?.addEventListener('blur', () => {
        if (!quantityInput.value) {
            quantityInput.value = 1;
        }
    });

    formatSelect?.addEventListener('change', updatePriceAndStock);
    // Updated to work with new variant combination system - using radio buttons
    document.addEventListener('change', function(e) {
        if (e.target.matches('input[name="selected_variant"]')) {
            updatePriceAndStock();
            
            // 🔄 VALIDATE VÀ ĐIỀU CHỈNH QUANTITY KHI THAY ĐỔI BIẾN THỂ
            const newStock = parseInt(e.target.dataset.stock) || 0;
            const currentQuantity = parseInt(quantityInput.value) || 1;
            
            // Nếu quantity hiện tại vượt quá stock của biến thể mới, điều chỉnh
            if (currentQuantity > newStock && newStock > 0) {
                quantityInput.value = Math.min(currentQuantity, newStock);
                console.log('🔄 quantity.js: Adjusted quantity from', currentQuantity, 'to', quantityInput.value, 'due to variant stock limit:', newStock);
            } else if (newStock <= 0 && currentQuantity > 0) {
                quantityInput.value = 1; // Reset về 1 cho biến thể hết hàng
                console.log('🔄 quantity.js: Reset quantity to 1 for out-of-stock variant');
            }
            
            // Add visual feedback for selection
            const card = e.target.closest('.variant-option-card');
            if (card) {
                card.classList.add('selecting');
                setTimeout(() => {
                    card.classList.remove('selecting');
                }, 600);
            }
        }
    });

    updatePriceAndStock();
});
