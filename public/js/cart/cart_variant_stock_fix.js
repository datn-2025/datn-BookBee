/**
 * Cart Variant Stock Management
 * Fixes quantity display and stock validation based on variant system
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Update data-stock attributes for all cart items based on variant information
    function updateCartItemStockAttributes() {
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(cartItem => {
            const variantId = cartItem.dataset.variantId;
            const attributeValueIds = cartItem.dataset.attributeValueIds;
            const currentStock = parseInt(cartItem.dataset.stock) || 0;
        
            });
            
            // Update quantity input max attribute to match the current stock
            const quantityInput = cartItem.querySelector('.quantity-input');
            if (quantityInput) {
                const oldMax = quantityInput.getAttribute('max');
                quantityInput.setAttribute('max', currentStock);
                quantityInput.setAttribute('data-variant-stock', currentStock);
                
                
                
                // Validate current quantity against new max
                const currentQuantity = parseInt(quantityInput.value) || 1;
                if (currentQuantity > currentStock) {
                    console.warn('⚠️ Current quantity exceeds stock, adjusting:', {
                        currentQuantity,
                        maxStock: currentStock
                    });
                    quantityInput.value = Math.max(1, currentStock);
                }
            }
            
            // Update button states
            const increaseBtn = cartItem.querySelector('.increase-quantity');
            const decreaseBtn = cartItem.querySelector('.decrease-quantity');
            
            if (increaseBtn && quantityInput) {
                const currentQuantity = parseInt(quantityInput.value) || 1;
                increaseBtn.disabled = currentQuantity >= currentStock;
            }
            
            if (decreaseBtn && quantityInput) {
                const currentQuantity = parseInt(quantityInput.value) || 1;
                decreaseBtn.disabled = currentQuantity <= 1;
            }
        });
    }
    
    // Validate quantity changes against variant stock
    function validateQuantityAgainstVariantStock(cartItem, newQuantity) {
        const variantStock = parseInt(cartItem.dataset.variantStock) || parseInt(cartItem.dataset.stock) || 0;
        
        if (newQuantity > variantStock) {
            
            if (typeof toastr !== 'undefined') {
                toastr.error(`Số lượng không được vượt quá ${variantStock} sản phẩm (tồn kho biến thể hiện tại)`);
            }
            
            return false;
        }
        
        return true;
    }
    
    // Enhanced quantity input change handler
    function handleQuantityInputChange(event) {
        const input = event.target;
        const cartItem = input.closest('.cart-item');
        
        if (!cartItem) return;
        
        const newQuantity = parseInt(input.value) || 1;
        const variantStock = parseInt(cartItem.dataset.variantStock) || parseInt(cartItem.dataset.stock) || 0;
        
        
        if (!validateQuantityAgainstVariantStock(cartItem, newQuantity)) {
            // Reset to previous valid value
            const lastValue = parseInt(input.dataset.lastValue) || 1;
            input.value = Math.min(lastValue, variantStock);
            return;
        }
        
        // Update last value
        input.dataset.lastValue = newQuantity;
    }
    
    // Initialize the fix
    setTimeout(() => {
        updateCartItemStockAttributes();
        
        // Add event listeners for quantity inputs
        const quantityInputs = document.querySelectorAll('.cart-item .quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', handleQuantityInputChange);
            input.addEventListener('blur', handleQuantityInputChange);
        });
        
       
    }, 500);
    
    // Re-run when cart is updated (for dynamic content)
    window.addEventListener('cartUpdated', function() {
      
        updateCartItemStockAttributes();
    });
});
