document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 100
    });

    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active classes from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('bg-black', 'text-white');
                btn.classList.add('bg-gray-100', 'text-black', 'hover:bg-gray-200');
            });
            
            // Add active class to clicked button
            this.classList.remove('bg-gray-100', 'text-black', 'hover:bg-gray-200');
            this.classList.add('bg-black', 'text-white');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            
            // Show target tab content
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
            }
        });
    });

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

    // Intersection Observer for counters
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.target);
                if (target) {
                    animateCounter(counter, target);
                }
                counterObserver.unobserve(counter);
            }
        });
    }, { threshold: 0.7 });

    // Observe counter elements
    document.querySelectorAll('.counter-animate').forEach(counter => {
        counterObserver.observe(counter);
    });

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
});

// Swiper initialization
document.addEventListener('DOMContentLoaded', function() {
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
});
