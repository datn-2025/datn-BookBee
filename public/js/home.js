document.addEventListener('DOMContentLoaded', function() {
    // Enhanced animations and interactions (home.js handles only animations)

    // Counter animation
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        function updateCounter() {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        }
        updateCounter();
    }

    // Simple fade-in animation using Intersection Observer
    function setupScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    
                    // If it's a counter element, animate it
                    if (entry.target.classList.contains('counter-animate')) {
                        const target = parseInt(entry.target.dataset.target);
                        if (target) {
                            animateCounter(entry.target, target);
                        }
                    }
                    
                    observer.unobserve(entry.target);
                }
            });
        }, { 
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Add scroll animations to elements
        const elementsToAnimate = document.querySelectorAll('.feature-card, .stats-section, .counter-animate');
        elementsToAnimate.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    }

    // Initialize scroll animations
    setupScrollAnimations();

    // Parallax scroll effect
    let ticking = false;
    
    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('[data-scroll-speed]');
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.scrollSpeed;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
        
        ticking = false;
    }

    function requestParallax() {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestParallax);

    // Enhanced hover effects with sound feedback (optional)
    const featureCards = document.querySelectorAll('.group');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Add subtle vibration on mobile
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        });
    });

    // Add click ripple effect
    featureCards.forEach(card => {
        card.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            const rect = card.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: radial-gradient(circle, rgba(0,0,0,0.1) 0%, transparent 70%);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
                z-index: 1000;
            `;
            
            card.style.position = 'relative';
            card.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // TAB SWITCHING FUNCTIONALITY
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    // Enhanced tab click handler
    tabButtons.forEach((button, buttonIndex) => {
        // Add keyboard support
        button.setAttribute('tabindex', '0');
        button.setAttribute('role', 'tab');
        
        // Click handler
        button.addEventListener('click', handleTabClick);
        
        // Keyboard handler
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleTabClick.call(this, e);
            }
        });
        
        function handleTabClick(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active state from all buttons
            tabButtons.forEach((btn, btnIndex) => {
                btn.classList.remove('bg-amber-600', 'bg-black', 'text-white');
                btn.classList.add('bg-gray-100', 'text-black', 'hover:bg-gray-200');
                btn.setAttribute('aria-selected', 'false');
            });
            
            // Activate clicked button
            this.classList.remove('bg-gray-100', 'hover:bg-gray-200', 'text-black');
            this.classList.add('bg-amber-600', 'text-white');
            this.setAttribute('aria-selected', 'true');
            
            // Hide all tab contents
            tabContents.forEach((content, contentIndex) => {
                content.classList.add('hidden');
                content.classList.remove('block');
                content.setAttribute('aria-hidden', 'true');
            });
            
            // Show target tab content
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
                targetContent.setAttribute('aria-hidden', 'false');
                
                // Simple reveal animation
                targetContent.style.opacity = '0';
                targetContent.style.transform = 'translateY(10px)';
                
                requestAnimationFrame(() => {
                    targetContent.style.transition = 'all 0.4s ease';
                    targetContent.style.opacity = '1';
                    targetContent.style.transform = 'translateY(0)';
                });
            }
        }
        
        // Enhanced visual feedback
        button.addEventListener('mouseenter', function() {
            if (!this.classList.contains('bg-black')) {
                this.style.transform = 'translateY(-1px)';
                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            }
        });
        
        button.addEventListener('mouseleave', function() {
            if (!this.classList.contains('bg-black')) {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            }
        });
    });
    
    // Set initial state
    const firstButton = tabButtons[0];
    const firstContent = tabContents[0];
    if (firstButton && firstContent) {
        firstButton.setAttribute('aria-selected', 'true');
        firstContent.setAttribute('aria-hidden', 'false');
    }

    // XEM THÊM / THU GỌN functionality for books
    const showMoreBtn = document.getElementById('showMoreBooks');
    const showLessBtn = document.getElementById('showLessBooks');
    const bookItems = document.querySelectorAll('.book-item');
    
    // Sale books expand/collapse functionality
    const showMoreSaleBtn = document.getElementById('showMoreSaleBooks');
    const showLessSaleBtn = document.getElementById('showLessSaleBooks');
    const saleBookItems = document.querySelectorAll('.sale-book-item');
    
    if (showMoreBtn) {
        showMoreBtn.addEventListener('click', function() {
            // Show all hidden books
            bookItems.forEach(function(item, index) {
                if (index >= 8) {
                    item.classList.remove('hidden');
                }
            });
            
            // Toggle buttons
            showMoreBtn.classList.add('hidden');
            showLessBtn.classList.remove('hidden');
            
            // Smooth scroll to show the newly revealed books
            setTimeout(function() {
                if (bookItems[8]) {
                    bookItems[8].scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                }
            }, 100);
        });
    }
    
    if (showLessBtn) {
        showLessBtn.addEventListener('click', function() {
            // Hide books after index 7 (keep first 8 books)
            bookItems.forEach(function(item, index) {
                if (index >= 8) {
                    item.classList.add('hidden');
                }
            });
            
            // Toggle buttons
            showLessBtn.classList.add('hidden');
            showMoreBtn.classList.remove('hidden');
            
            // Smooth scroll back to the section
            document.getElementById('allBooksGrid').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        });
    }
    
    // Sale books expand functionality
    if (showMoreSaleBtn) {
        showMoreSaleBtn.addEventListener('click', function() {
            // Show all hidden sale books
            saleBookItems.forEach(function(item, index) {
                if (index >= 8) {
                    item.classList.remove('hidden');
                }
            });
            
            // Toggle buttons
            showMoreSaleBtn.classList.add('hidden');
            showLessSaleBtn.classList.remove('hidden');
            
            // Smooth scroll to show the newly revealed books
            setTimeout(function() {
                if (saleBookItems[8]) {
                    saleBookItems[8].scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                }
            }, 100);
        });
    }
    
    // Sale books collapse functionality
    if (showLessSaleBtn) {
        showLessSaleBtn.addEventListener('click', function() {
            // Hide books after index 7 (keep first 8 books)
            saleBookItems.forEach(function(item, index) {
                if (index >= 8) {
                    item.classList.add('hidden');
                }
            });
            
            // Toggle buttons
            showLessSaleBtn.classList.add('hidden');
            showMoreSaleBtn.classList.remove('hidden');
            
            // Smooth scroll back to the section
            document.getElementById('saleBooksGrid').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        });
    }

    // Swiper initialization - consolidated in same DOMContentLoaded
    
    // Check if Swiper is available
    if (typeof Swiper !== 'undefined') {
        const reviewSwiperElement = document.querySelector('.reviewSwiper');
        if (reviewSwiperElement) {
            
            const reviewSwiper = new Swiper('.reviewSwiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination-bullets',
                    clickable: true,
                    bulletClass: 'swiper-pagination-bullet-custom',
                    bulletActiveClass: 'swiper-pagination-bullet-active-custom',
                    renderBullet: function (index, className) {
                        return '<span class="' + className + ' w-3 h-3 bg-white/30 hover:bg-white/60 transition-all duration-300 cursor-pointer"></span>';
                    },
                },
                navigation: {
                    nextEl: '.swiper-next-custom',
                    prevEl: '.swiper-prev-custom',
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 40,
                    },
                },
                effect: 'slide',
                speed: 600,
            });
        }
    }
});
