@extends('layouts.app')
@section('title', 'Giới thiệu về BookBee')

@push('styles')
    <style>
        .about-hero {
            background: linear-gradient(135deg, #000 0%, #333 100%);
            position: relative;
            overflow: hidden;
        }
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 40%;
            height: 100%;
            background: linear-gradient(45deg, transparent 0%, rgba(255,255,255,0.05) 50%, transparent 100%);
            transform: skewX(15deg);
        }
        .stats-counter {
            font-size: 3rem;
            font-weight: 900;
            color: #000;
            line-height: 1;
        }
        .team-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .team-card:hover {
            transform: translateY(-10px);
        }
        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .team-card:hover::before {
            left: 100%;
        }
        .mission-card {
            background: #f8f9fa;
            border-left: 4px solid #000;
            transition: all 0.3s ease;
        }
        .mission-card:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .timeline-item {
            position: relative;
            padding-left: 3rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background: #000;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 18px;
            height: 18px;
            background: #000;
            border-radius: 50%;
        }
        .adidas-stripe {
            position: relative;
            overflow: hidden;
        }
        .adidas-stripe::before,
        .adidas-stripe::after {
            content: '';
            position: absolute;
            top: 0;
            width: 4px;
            height: 100%;
            background: #000;
        }
        .adidas-stripe::before {
            left: 20px;
        }
        .adidas-stripe::after {
            right: 20px;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section - Adidas Style -->
    <section class="about-hero text-white py-32 relative">
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-0.5 bg-white"></div>
                        <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-300">
                            VỀ CHÚNG TÔI
                        </span>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black uppercase leading-[0.9] tracking-tight mb-6">
                        <span class="block">IMPOSSIBLE</span>
                        <span class="block text-gray-400">IS</span>
                        <span class="block">NOTHING</span>
                    </h1>
                    <p class="text-xl text-gray-300 mb-8 max-w-lg">
                        BookBee - Nơi tri thức không có giới hạn. Chúng tôi tin rằng mọi cuốn sách đều có thể thay đổi cuộc sống.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-0.5 bg-white"></div>
                        <span class="text-sm font-bold uppercase tracking-wide">EST. 2024</span>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-square bg-white/10 p-8 transform rotate-3 hover:rotate-6 transition-transform duration-500">
                        <img src="{{ asset('images/single-image-about.jpg') }}" 
                             alt="BookBee Story" 
                             class="w-full h-full object-cover transform -rotate-3">
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-white text-black px-6 py-3 font-bold uppercase tracking-wide">
                        BOOKBEE
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="space-y-4">
                    <div class="stats-counter">10K+</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold">Sách trong kho</div>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>
                <div class="space-y-4">
                    <div class="stats-counter">5K+</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold">Khách hàng</div>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>
                <div class="space-y-4">
                    <div class="stats-counter">24/7</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold">Hỗ trợ</div>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>
                <div class="space-y-4">
                    <div class="stats-counter">99%</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold">Hài lòng</div>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        SỨ MỆNH
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black mb-6">
                    TẠI SAO CHỌN BOOKBEE?
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="mission-card p-8 hover:bg-white transition-colors duration-300">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl">
                        01
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4">CHẤT LƯỢNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Chúng tôi cam kết cung cấp những cuốn sách chất lượng cao, được tuyển chọn kỹ càng từ các nhà xuất bản uy tín.
                    </p>
                </div>

                <div class="mission-card p-8 hover:bg-white transition-colors duration-300">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl">
                        02
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4">ĐA DẠNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Kho sách đa dạng với hàng nghìn đầu sách thuộc mọi thể loại, từ văn học, khoa học đến phát triển bản thân.
                    </p>
                </div>

                <div class="mission-card p-8 hover:bg-white transition-colors duration-300">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl">
                        03
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4">TỐC ĐỘ</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Giao hàng nhanh chóng với đội ngũ logistics chuyên nghiệp, đảm bảo sách đến tay bạn trong thời gian sớm nhất.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Timeline -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        LỊCH SỬ PHÁT TRIỂN
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                    HÀNH TRÌNH CỦA CHÚNG TÔI
                </h2>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="timeline-item mb-12">
                    <div class="bg-gray-50 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-2xl font-black">2024</span>
                            <div class="w-8 h-0.5 bg-black"></div>
                        </div>
                        <h3 class="text-xl font-bold uppercase tracking-wide mb-4">KHỞI ĐẦU</h3>
                        <p class="text-gray-600 leading-relaxed">
                            BookBee được thành lập với mục tiêu mang tri thức đến gần hơn với mọi người. Chúng tôi bắt đầu với một kho sách nhỏ nhưng được tuyển chọn cẩn thận.
                        </p>
                    </div>
                </div>

                <div class="timeline-item mb-12">
                    <div class="bg-gray-50 p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-2xl font-black">2024</span>
                            <div class="w-8 h-0.5 bg-black"></div>
                        </div>
                        <h3 class="text-xl font-bold uppercase tracking-wide mb-4">PHÁT TRIỂN</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Mở rộng hệ thống với hàng nghìn đầu sách mới, cải thiện trải nghiệm người dùng và xây dựng đội ngũ giao hàng chuyên nghiệp.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="bg-black text-white p-8">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-2xl font-black">NOW</span>
                            <div class="w-8 h-0.5 bg-white"></div>
                        </div>
                        <h3 class="text-xl font-bold uppercase tracking-wide mb-4">TƯƠNG LAI</h3>
                        <p class="text-gray-300 leading-relaxed">
                            Hướng tới trở thành nền tảng sách hàng đầu, không ngừng cải tiến và mang đến những trải nghiệm tuyệt vời nhất cho khách hàng.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        ĐỘI NGŨ
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                    NHỮNG NGƯỜI THỰC HIỆN
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="team-card bg-white p-8 text-center">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2">TEAM LEADER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Founder & CEO</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>

                <div class="team-card bg-white p-8 text-center">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-code text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2">DEVELOPER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Technical Lead</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>

                <div class="team-card bg-white p-8 text-center">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-palette text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2">DESIGNER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Creative Director</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="bg-black text-white py-20 adidas-stripe">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-4 mb-8">
                <div class="w-12 h-0.5 bg-white"></div>
                <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-300">
                    GIÁ TRỊ CỐT LÕI
                </span>
                <div class="w-12 h-0.5 bg-white"></div>
            </div>
            <h2 class="text-4xl md:text-6xl font-black uppercase tracking-tight mb-8">
                IMPOSSIBLE IS NOTHING
            </h2>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto mb-12">
                Chúng tôi tin rằng tri thức có thể thay đổi thế giới. Mỗi cuốn sách là một cánh cửa mở ra những khả năng vô hạn.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="space-y-4">
                    <div class="text-6xl font-black">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide">VÔ HẠN</h3>
                    <p class="text-gray-300">Tri thức không có giới hạn</p>
                </div>
                <div class="space-y-4">
                    <div class="text-6xl font-black">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide">NHANH CHÓNG</h3>
                    <p class="text-gray-300">Giao hàng tốc độ ánh sáng</p>
                </div>
                <div class="space-y-4">
                    <div class="text-6xl font-black">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide">GIÁO DỤC</h3>
                    <p class="text-gray-300">Lan tỏa tri thức và học hỏi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black mb-6">
                    BẮT ĐẦU HÀNH TRÌNH CỦA BẠN
                </h2>
                <p class="text-xl text-gray-600 mb-8">
                    Khám phá kho tàng tri thức cùng BookBee ngay hôm nay
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('books.index') }}" 
                       class="bg-black text-white px-8 py-4 font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors">
                        XEM SÁCH NGAY
                    </a>
                    <a href="{{ route('contact.form') }}" 
                       class="border-2 border-black text-black px-8 py-4 font-bold text-sm uppercase tracking-wider hover:bg-black hover:text-white transition-colors">
                        LIÊN HỆ
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Counter animation
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stats-counter');
            
            const animateCounter = (counter) => {
                const target = counter.textContent.replace(/\D/g, '');
                const suffix = counter.textContent.replace(/\d/g, '');
                let current = 0;
                const increment = target / 100;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target + suffix;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current) + suffix;
                    }
                }, 20);
            };
            
            // Intersection Observer for animation trigger
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            counters.forEach(counter => {
                observer.observe(counter);
            });
        });
    </script>
@endpush
