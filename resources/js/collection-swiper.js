import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

document.addEventListener('DOMContentLoaded', () => {

    const swiperMap={}; // lưu instance swiper

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
     // ✅ Hàm cập nhật nút điều hướng
    function updateNavButtons(swiper, parent) {
        const prev = parent.querySelector('.swiper-prev');
        const next = parent.querySelector('.swiper-next');

        prev?.classList.toggle('hidden', swiper.isBeginning);
        next?.classList.toggle('hidden', swiper.isEnd);
    }
    // ✅ Tab switching
    const buttons = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            const tabId = `tab-${tab}`;

            contents.forEach(c => c.classList.add('hidden'));
            const activeTab = document.getElementById(tabId);
            activeTab.classList.remove('hidden');

            // buttons.forEach(b => b.classList.remove('bg-black', 'text-white'));
            // btn.classList.add('bg-black', 'text-white');

            // ✅ Reset slide về đầu khi chuyển tab
            const swiper = swiperMap[tabId];
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
});