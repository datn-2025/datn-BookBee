// Enhanced Adidas-style Wishlist JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeAdidasEffects();
    initializeSmoothAnimations();
    initializeParallaxEffects();
});

function initializeAdidasEffects() {
    // Add loading skeletons
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    wishlistItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Add hover sound effect simulation
    wishlistItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Enhanced button interactions
    const adidasButtons = document.querySelectorAll('.adidas-btn');
    adidasButtons.forEach(btn => {
        btn.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        btn.addEventListener('mouseup', function() {
            this.style.transform = 'scale(1)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

function initializeSmoothAnimations() {
    // Smooth scroll for pagination
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href && this.href.includes('#')) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Add ripple effect to buttons
    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
        circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
        circle.classList.add('ripple');

        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
    }

    const rippleButtons = document.querySelectorAll('.adidas-btn');
    rippleButtons.forEach(btn => {
        btn.addEventListener('click', createRipple);
        btn.style.position = 'relative';
        btn.style.overflow = 'hidden';
    });

    // Add CSS for ripple effect
    const style = document.createElement('style');
    style.textContent = `
        .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

function initializeParallaxEffects() {
    const header = document.querySelector('.wishlist-header');
    if (header) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            header.style.transform = `translateY(${rate}px)`;
        });
    }

    // Add floating animation to empty state icon
    const emptyIcon = document.querySelector('.empty-icon');
    if (emptyIcon) {
        emptyIcon.style.animation = 'float 3s ease-in-out infinite';
        
        const floatStyle = document.createElement('style');
        floatStyle.textContent = `
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
        `;
        document.head.appendChild(floatStyle);
    }
}

// Enhanced remove functions with Adidas-style animations
function removeFromWishlist(bookId) {
    Swal.fire({
        title: 'XÁC NHẬN XÓA',
        text: 'Bạn có chắc chắn muốn xóa sách này khỏi danh sách yêu thích?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000000',
        cancelButtonColor: '#767677',
        confirmButtonText: '<i class="fas fa-trash-alt"></i> XÓA NGAY',
        cancelButtonText: '<i class="fas fa-ban"></i> HỦY BỎ',
        background: '#ffffff',
        color: '#000000',
        customClass: {
            popup: 'adidas-swal-popup',
            title: 'adidas-swal-title',
            content: 'adidas-swal-content',
            confirmButton: 'adidas-swal-confirm',
            cancelButton: 'adidas-swal-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Add loading animation
            const item = document.querySelector(`[data-book-id="${bookId}"]`);
            if (item) {
                item.style.transition = 'all 0.5s ease';
                item.style.transform = 'translateX(-100%)';
                item.style.opacity = '0';
            }

            fetch('/wishlist/delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ book_id: bookId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                    
                    Swal.fire({
                        title: 'ĐÃ XÓA!',
                        text: 'Sách đã được xóa khỏi danh sách yêu thích.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#ffffff',
                        color: '#000000',
                        customClass: {
                            popup: 'adidas-swal-popup'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'LỖI!',
                    text: 'Có lỗi xảy ra khi xóa sách.',
                    icon: 'error',
                    confirmButtonColor: '#d32f2f',
                    background: '#ffffff',
                    color: '#000000'
                });
            });
        }
    });
}

function removeAllFromWishlist() {
    Swal.fire({
        title: 'XÓA TẤT CẢ?',
        text: 'Bạn có chắc chắn muốn xóa tất cả sách khỏi danh sách yêu thích? Hành động này không thể hoàn tác!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d32f2f',
        cancelButtonColor: '#767677',
        confirmButtonText: '<i class="fas fa-trash-can"></i> XÓA TẤT CẢ',
        cancelButtonText: '<i class="fas fa-ban"></i> HỦY BỎ',
        background: '#ffffff',
        color: '#000000',
        customClass: {
            popup: 'adidas-swal-popup',
            title: 'adidas-swal-title',
            content: 'adidas-swal-content',
            confirmButton: 'adidas-swal-confirm',
            cancelButton: 'adidas-swal-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Add dramatic loading animation
            const allItems = document.querySelectorAll('.wishlist-item');
            allItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.transform = 'translateY(-50px)';
                    item.style.opacity = '0';
                }, index * 100);
            });

            fetch('/wishlist/delete-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    
                    Swal.fire({
                        title: 'ĐÃ XÓA TẤT CẢ!',
                        text: 'Tất cả sách đã được xóa khỏi danh sách yêu thích.',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#ffffff',
                        color: '#000000',
                        customClass: {
                            popup: 'adidas-swal-popup'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'LỖI!',
                    text: 'Có lỗi xảy ra khi xóa danh sách.',
                    icon: 'error',
                    confirmButtonColor: '#d32f2f',
                    background: '#ffffff',
                    color: '#000000'
                });
            });
        }
    });
}

function toggleShortcutsModal() {
    Swal.fire({
        title: 'PHÍM TẮT',
        html: `
            <div style="text-align: left; font-family: 'Helvetica Neue', Arial, sans-serif;">
                <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                    <strong style="color: #000; text-transform: uppercase; letter-spacing: 1px; min-width: 80px;">G</strong> 
                    <span style="color: #767677;">Chuyển đổi view</span>
                </div>
                <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                    <strong style="color: #000; text-transform: uppercase; letter-spacing: 1px; min-width: 80px;">?</strong> 
                    <span style="color: #767677;">Hiển thị phím tắt</span>
                </div>
                <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                    <strong style="color: #000; text-transform: uppercase; letter-spacing: 1px; min-width: 80px;">Ctrl + D</strong> 
                    <span style="color: #767677;">Xóa tất cả</span>
                </div>
                <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                    <strong style="color: #000; text-transform: uppercase; letter-spacing: 1px; min-width: 80px;">ESC</strong> 
                    <span style="color: #767677;">Đóng modal</span>
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> ĐÓNG',
        confirmButtonColor: '#000000',
        background: '#ffffff',
        color: '#000000',
        customClass: {
            popup: 'adidas-swal-popup',
            title: 'adidas-swal-title',
            content: 'adidas-swal-content',
            confirmButton: 'adidas-swal-confirm'
        }
    });
}

// Add custom SweetAlert2 styles
const swalStyle = document.createElement('style');
swalStyle.textContent = `
    .adidas-swal-popup {
        border: 3px solid #000000 !important;
        border-radius: 0 !important;
        font-family: 'Helvetica Neue', Arial, sans-serif !important;
    }
    
    .adidas-swal-title {
        font-weight: 900 !important;
        text-transform: uppercase !important;
        letter-spacing: -0.02em !important;
        color: #000000 !important;
    }
    
    .adidas-swal-content {
        font-weight: 500 !important;
        color: #767677 !important;
    }
    
    .adidas-swal-confirm {
        background: #000000 !important;
        color: #ffffff !important;
        border: 2px solid #000000 !important;
        border-radius: 0 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 12px 24px !important;
        transition: all 0.2s ease !important;
    }
    
    .adidas-swal-confirm:hover {
        background: #ffffff !important;
        color: #000000 !important;
        transform: translateY(-2px) !important;
    }
    
    .adidas-swal-cancel {
        background: #ffffff !important;
        color: #767677 !important;
        border: 2px solid #767677 !important;
        border-radius: 0 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 12px 24px !important;
        transition: all 0.2s ease !important;
    }
    
    .adidas-swal-cancel:hover {
        background: #767677 !important;
        color: #ffffff !important;
        transform: translateY(-2px) !important;
    }
`;
document.head.appendChild(swalStyle);
