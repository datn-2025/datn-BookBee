import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

document.addEventListener('DOMContentLoaded', () => {

    const swiperMap={}; // lưu instance swiper

    // Chỉ khởi tạo swiper nếu có element .categorySwiper
    document.querySelectorAll('.categorySwiper').forEach(swiperEl => {
        const parent = swiperEl.closest('.tab-content');
        const tabId = parent?.id ;

        const swiperInstance= new Swiper(swiperEl, {
            spaceBetween: 16,
            slidesPerView: 1,
            slidesPerGroup: 1,

            breakpoints: {
                640: { slidesPerView: 2, slidesPerGroup: 2 },
                768: { slidesPerView: 3, slidesPerGroup: 3 },
                1024: { slidesPerView: 4, slidesPerGroup: 4 },
            },

            navigation: {
                nextEl: parent.querySelector('.swiper-next'),
                prevEl: parent.querySelector('.swiper-prev'),
            },

            scrollbar: {
                el: parent.querySelector('.swiper-scrollbar'),
                draggable: true,
            },
            // ⬇ Thêm xử lý khi init hoặc chuyển slide
            on: {
                init: function () {
                    updateNavButtons(this, parent);
                },
                slideChange: function () {
                    updateNavButtons(this, parent);
                }
            }
        });
        swiperMap[tabId] = swiperInstance; // lưu instance swiper vào map
    });
     
    // ✅ Hàm cập nhật nút điều hướng (chỉ cho swiper)
    function updateNavButtons(swiper, parent) {
        const prev = parent.querySelector('.swiper-prev');
        const next = parent.querySelector('.swiper-next');

        prev?.classList.toggle('hidden', swiper.isBeginning);
        next?.classList.toggle('hidden', swiper.isEnd);
    }
    
    // ✅ Tab switching - hoạt động cho cả grid layout và swiper
    const buttons = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    if (buttons.length > 0 && contents.length > 0) {
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.dataset.tab; // data-tab đã có format "tab-{id}" rồi
                console.log('Switching to tab:', tabId); // Debug log

                // Hide all tab contents
                contents.forEach(c => {
                    c.classList.add('hidden');
                    c.classList.remove('block');
                });
                
                // Show active tab
                const activeTab = document.getElementById(tabId);
                if (activeTab) {
                    activeTab.classList.remove('hidden');
                    activeTab.classList.add('block');
                    console.log('Active tab found and shown:', tabId); // Debug log
                } else {
                    console.error('Tab not found:', tabId); // Debug log
                }

                // Update button styles
                buttons.forEach(b => {
                    b.classList.remove('bg-amber-600', 'text-white');
                    b.classList.add('bg-gray-100', 'text-black', 'hover:bg-gray-200');
                });
                btn.classList.remove('bg-gray-100', 'text-black', 'hover:bg-gray-200');
                btn.classList.add('bg-amber-600', 'text-white');

                // ✅ Reset slide về đầu khi chuyển tab (chỉ nếu có swiper)
                const swiper = swiperMap[tabId]; // tabId đã đúng format rồi
                if(swiper){
                    swiper.slideTo(0); // Trở về slide đầu tiên
                    setTimeout(()=>{
                        swiper.update();
                        swiper.navigation.update();
                        swiper.scrollbar?.updateSize();
                        updateNavButtons(swiper, activeTab);
                    },100);
                }
            });
        });
    }
});