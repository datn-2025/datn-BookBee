// Enhanced Icon Interactions for Adidas Wishlist
document.addEventListener('DOMContentLoaded', function() {
    initializeIconEffects();
    addTooltipsToIcons();
    initializeIconAnimations();
});

function initializeIconEffects() {
    // Add ripple effect to icon buttons
    const iconButtons = document.querySelectorAll('.adidas-btn');
    iconButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            createIconRipple(e, this);
        });
    });

    // Add hover sound effects (visual feedback)
    const allIcons = document.querySelectorAll('.fas, .far, .fab');
    allIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.filter = 'brightness(1.2) drop-shadow(0 0 5px rgba(0,0,0,0.3))';
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.filter = 'none';
        });
    });
}

function createIconRipple(event, element) {
    const icon = element.querySelector('i');
    if (icon) {
        icon.style.transform = 'scale(1.3)';
        setTimeout(() => {
            icon.style.transform = 'scale(1)';
        }, 75);
    }
}

function addTooltipsToIcons() {
    // Add tooltips for better UX
    const iconTooltips = {
        'fa-heart': 'Yêu thích',
        'fa-heart-broken': 'Danh sách trống',
        'fa-eye': 'Xem chi tiết',
        'fa-times': 'Xóa khỏi danh sách',
        'fa-trash-alt': 'Xóa tất cả',
        'fa-trash-can': 'Xóa tất cả',
        'fa-keyboard': 'Xem phím tắt',
        'fa-home': 'Về trang chủ',
        'fa-book': 'Khám phá sách',
        'fa-tag': 'Danh mục',
        'fa-building': 'Nhà xuất bản',
        'fa-clock': 'Thời gian thêm',
        'fa-sort': 'Sắp xếp',
        'fa-lightbulb': 'Mẹo hữu ích',
        'fa-shopping-cart': 'Mua hàng',
        'fa-star': 'Theo dõi',
        'fa-search': 'Tìm kiếm',
        'fa-ban': 'Hủy bỏ'
    };

    Object.keys(iconTooltips).forEach(iconClass => {
        const icons = document.querySelectorAll(`.${iconClass}`);
        icons.forEach(icon => {
            if (!icon.hasAttribute('title')) {
                icon.setAttribute('title', iconTooltips[iconClass]);
                icon.setAttribute('aria-label', iconTooltips[iconClass]);
            }
        });
    });
}

function initializeIconAnimations() {
    // Stagger animation for wishlist items
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    
                    // Animate icons within the item
                    const icons = entry.target.querySelectorAll('.fas, .far, .fab');
                    icons.forEach((icon, iconIndex) => {
                        setTimeout(() => {
                            icon.style.opacity = '1';
                            icon.style.transform = 'scale(1)';
                        }, iconIndex * 50);
                    });
                }, index * 100);
            }
        });
    }, {
        threshold: 0.1
    });

    wishlistItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        
        // Initially hide icons
        const icons = item.querySelectorAll('.fas, .far, .fab');
        icons.forEach(icon => {
            icon.style.opacity = '0';
            icon.style.transform = 'scale(0.8)';
            icon.style.transition = 'all 0.3s ease';
        });
        
        observer.observe(item);
    });

    // Animate empty state icon
    const emptyIcon = document.querySelector('.empty-icon .fa-heart-broken');
    if (emptyIcon) {
        let direction = 1;
        setInterval(() => {
            emptyIcon.style.transform = `scale(${1 + direction * 0.1})`;
            direction *= -1;
        }, 2000);
    }

    // Animate stats number with heartbeat effect
    const statsIcon = document.querySelector('.wishlist-stats .fa-heart');
    if (statsIcon) {
        setInterval(() => {
            statsIcon.style.color = '#ff6b6b';
            setTimeout(() => {
                statsIcon.style.color = 'var(--adidas-yellow)';
            }, 200);
        }, 3000);
    }
}

// Icon-specific interactions
function initializeSpecialIconEffects() {
    // Trash can shake effect
    const deleteButtons = document.querySelectorAll('.adidas-btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.fa-trash-alt, .fa-trash-can, .fa-times');
            if (icon) {
                icon.style.animation = 'shake 0.5s ease-in-out';
            }
        });
        
        button.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.fa-trash-alt, .fa-trash-can, .fa-times');
            if (icon) {
                icon.style.animation = 'none';
            }
        });
    });

    // Eye icon blink effect
    const viewButtons = document.querySelectorAll('.adidas-btn-outline');
    viewButtons.forEach(button => {
        const eyeIcon = button.querySelector('.fa-eye');
        if (eyeIcon) {
            button.addEventListener('mouseenter', function() {
                eyeIcon.style.animation = 'blink 1s ease-in-out';
            });
            
            button.addEventListener('mouseleave', function() {
                eyeIcon.style.animation = 'none';
            });
        }
    });

    // Heart pulse effect for favorites
    const heartIcons = document.querySelectorAll('.fa-heart');
    heartIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            this.style.color = '#ff6b6b';
            this.style.animation = 'heartPulse 0.6s ease-in-out';
            
            setTimeout(() => {
                this.style.animation = 'none';
                this.style.color = '';
            }, 600);
        });
    });
}

// Add custom CSS animations
const iconAnimationStyles = document.createElement('style');
iconAnimationStyles.textContent = `
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    @keyframes heartPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.3); }
    }
    
    @keyframes iconFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }
    
    .icon-hover-effect {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .icon-hover-effect:hover {
        transform: scale(1.2) rotate(5deg);
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }
`;
document.head.appendChild(iconAnimationStyles);

// Initialize special effects
document.addEventListener('DOMContentLoaded', function() {
    initializeSpecialIconEffects();
});

// Keyboard shortcuts for icon interactions
document.addEventListener('keydown', function(e) {
    // Ctrl + I for icon info
    if (e.ctrlKey && e.key.toLowerCase() === 'i') {
        e.preventDefault();
        showIconLegend();
    }
});

function showIconLegend() {
    const iconLegend = `
        <div style="text-align: left; font-family: 'Helvetica Neue', Arial, sans-serif;">
            <h3 style="margin-bottom: 20px; text-transform: uppercase; font-weight: 900;">Ý nghĩa các icon</h3>
            <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px 20px;">
                <i class="fas fa-heart" style="color: #ffdd00;"></i><span>Yêu thích</span>
                <i class="fas fa-eye" style="color: #000;"></i><span>Xem chi tiết</span>
                <i class="fas fa-times" style="color: #d32f2f;"></i><span>Xóa</span>
                <i class="fas fa-trash-alt" style="color: #d32f2f;"></i><span>Xóa tất cả</span>
                <i class="fas fa-keyboard" style="color: #000;"></i><span>Phím tắt</span>
                <i class="fas fa-tag" style="color: #767677;"></i><span>Danh mục</span>
                <i class="fas fa-building" style="color: #767677;"></i><span>Nhà xuất bản</span>
                <i class="fas fa-clock" style="color: #767677;"></i><span>Thời gian</span>
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'HƯỚNG DẪN ICON',
        html: iconLegend,
        confirmButtonText: '<i class="fas fa-check"></i> HIỂU RỒI',
        confirmButtonColor: '#000000',
        background: '#ffffff',
        customClass: {
            popup: 'adidas-swal-popup',
            title: 'adidas-swal-title',
            confirmButton: 'adidas-swal-confirm'
        }
    });
}
