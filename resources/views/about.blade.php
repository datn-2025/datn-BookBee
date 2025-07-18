@extends('layouts.app')
@section('title', 'Giới thiệu về BookBee')

@push('styles')
    <style>
        /* Optimized hero section */
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
        
        /* Optimized stats counter - no animations */
        .stats-counter {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 900;
            color: #000;
            line-height: 1;
        }
        
        /* Minimal team card hover - ultra light */
        .team-card {
            transition: box-shadow 0.1s ease;
            position: relative;
            background: #fff;
        }
        .team-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        /* Minimal mission card */
        .mission-card {
            background: #f8f9fa;
            border-left: 4px solid #000;
            transition: background-color 0.1s ease;
        }
        .mission-card:hover {
            background: #fff;
        }
        
        /* Optimized timeline */
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
        
        /* Simplified stripes */
        .adidas-stripe {
            position: relative;
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
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        /* Preload critical styles and improve font loading */
        .font-loading {
            font-display: swap;
        }
        
        /* Reduce motion for users who prefer it */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }
        
        /* Minimal button interactions */
        .btn-focus {
            outline: none;
        }
        .btn-focus:focus {
            box-shadow: 0 0 0 2px rgba(0,0,0,0.3);
        }
        
        /* Remove image loading animation */
        .image-placeholder {
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Disable fade-in animations */
        .fade-in {
            opacity: 1;
            transform: none;
        }
        
        /* Static sections for better performance */
        .static-section {
            contain: layout;
        }
        
        /* Responsive text scaling */
        @media (max-width: 768px) {
            .stats-counter {
                font-size: 2rem;
            }
            .timeline-item {
                padding-left: 2rem;
            }
            .timeline-item::before {
                left: 0.5rem;
            }
            .timeline-item::after {
                left: -4px;
                width: 12px;
                height: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section - Adidas Style -->
    <section class="about-hero text-white py-20 md:py-32 relative" id="hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="order-2 md:order-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-8 md:w-12 h-0.5 bg-white"></div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] md:tracking-[0.3em] text-gray-300">
                            VỀ CHÚNG TÔI
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-black uppercase leading-[0.9] tracking-tight mb-6">
                        <span class="block">IMPOSSIBLE</span>
                        <span class="block text-gray-400">IS</span>
                        <span class="block">NOTHING</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-lg">
                        BookBee - Nơi tri thức không có giới hạn. Chúng tôi tin rằng mọi cuốn sách đều có thể thay đổi cuộc sống.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 md:w-16 h-0.5 bg-white"></div>
                        <span class="text-xs md:text-sm font-bold uppercase tracking-wide">EST. 2024</span>
                    </div>
                </div>
                <div class="relative order-1 md:order-2">
                    <div class="aspect-square bg-white/10 p-4 md:p-8">
                        <img src="{{ asset('images/single-image-about.jpg') }}" 
                             alt="BookBee Story" 
                             class="w-full h-full object-cover"
                             loading="lazy"
                             decoding="async">
                    </div>
                    <div class="absolute -bottom-4 md:-bottom-6 -right-4 md:-right-6 bg-white text-black px-4 py-2 md:px-6 md:py-3 font-bold uppercase tracking-wide text-sm md:text-base">
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
                        SỨ MỆNH & TẦM NHÌN
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black mb-6">
                    TẠI SAO CHỌN BOOKBEE?
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-16">
                <div class="space-y-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-black text-white flex items-center justify-center font-bold text-xl">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3 class="text-2xl font-bold uppercase tracking-wide">SỨ MỆNH</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Mang tri thức đến gần hơn với mọi người, tạo ra một cộng đồng yêu sách và học hỏi. Chúng tôi tin rằng mỗi cuốn sách đều có thể thay đổi cuộc sống và mở ra những cơ hội mới.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-0.5 bg-black"></div>
                        <span class="text-sm font-bold uppercase tracking-wide text-gray-500">EST. 2024</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-black text-white flex items-center justify-center font-bold text-xl">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="text-2xl font-bold uppercase tracking-wide">TẦM NHÌN</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Trở thành nền tảng sách hàng đầu tại Việt Nam, nơi mọi người có thể dễ dàng tìm kiếm, khám phá và sở hữu những cuốn sách yêu thích với trải nghiệm tốt nhất.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-0.5 bg-black"></div>
                        <span class="text-sm font-bold uppercase tracking-wide text-gray-500">FUTURE VISION</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="mission-card p-8">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl">
                        01
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4">CHẤT LƯỢNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Chúng tôi cam kết cung cấp những cuốn sách chất lượng cao, được tuyển chọn kỹ càng từ các nhà xuất bản uy tín.
                    </p>
                </div>

                <div class="mission-card p-8">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl">
                        02
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4">ĐA DẠNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Kho sách đa dạng với hàng nghìn đầu sách thuộc mọi thể loại, từ văn học, khoa học đến phát triển bản thân.
                    </p>
                </div>

                <div class="mission-card p-8">
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
                <div class="team-card p-8 text-center">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-user text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2">TEAM LEADER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Founder & CEO</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>

                <div class="team-card p-8 text-center">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center">
                        <i class="fas fa-code text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2">DEVELOPER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Technical Lead</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>

                <div class="team-card p-8 text-center">
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
    <section class="bg-black text-white py-20">
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

    <!-- Contact Info Section -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        LIÊN HỆ
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                    KẾT NỐI VỚI CHÚNG TÔI
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold uppercase tracking-wide">ĐỊA CHỈ</h3>
                    <p class="text-gray-600 leading-relaxed">
                        123 Đường Sách<br>
                        Quận 1, TP.HCM<br>
                        Việt Nam
                    </p>
                </div>

                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold uppercase tracking-wide">ĐIỆN THOẠI</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Hotline: 1900 1234<br>
                        Mobile: 0901 234 567<br>
                        Hỗ trợ 24/7
                    </p>
                </div>

                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold uppercase tracking-wide">EMAIL</h3>
                    <p class="text-gray-600 leading-relaxed">
                        info@bookbee.vn<br>
                        support@bookbee.vn<br>
                        order@bookbee.vn
                    </p>
                </div>

                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold uppercase tracking-wide">GIỜ LÀM VIỆC</h3>
                    <p class="text-gray-600 leading-relaxed">
                        T2-T6: 8:00 - 18:00<br>
                        T7-CN: 9:00 - 17:00<br>
                        Online 24/7
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        CÂU HỎI THƯỜNG GẶP
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black">
                    GIẢI ĐÁP THẮC MẮC
                </h2>
            </div>

            <div class="max-w-4xl mx-auto space-y-6">
                <div class="bg-white p-6 border-l-4 border-black">
                    <h3 class="text-lg font-bold uppercase tracking-wide mb-3">BookBee có những loại sách nào?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Chúng tôi cung cấp đa dạng các thể loại sách từ văn học, khoa học, kỹ thuật, kinh doanh, phát triển bản thân đến sách thiếu nhi. Kho sách của chúng tôi được cập nhật liên tục với những đầu sách mới nhất.
                    </p>
                </div>

                <div class="bg-white p-6 border-l-4 border-black">
                    <h3 class="text-lg font-bold uppercase tracking-wide mb-3">Thời gian giao hàng như thế nào?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Chúng tôi cam kết giao hàng trong vòng 24-48h đối với nội thành và 2-3 ngày đối với các tỉnh thành khác. Đặc biệt, chúng tôi có dịch vụ giao hàng nhanh trong ngày cho những đơn hàng khẩn cấp.
                    </p>
                </div>

                <div class="bg-white p-6 border-l-4 border-black">
                    <h3 class="text-lg font-bold uppercase tracking-wide mb-3">Có chính sách đổi trả không?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Chúng tôi hỗ trợ đổi trả trong vòng 7 ngày kể từ khi nhận hàng nếu sản phẩm có lỗi từ nhà sản xuất hoặc không đúng như mô tả. Khách hàng có thể liên hệ với chúng tôi qua hotline hoặc email.
                    </p>
                </div>

                <div class="bg-white p-6 border-l-4 border-black">
                    <h3 class="text-lg font-bold uppercase tracking-wide mb-3">Làm sao để theo dõi đơn hàng?</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Sau khi đặt hàng thành công, bạn sẽ nhận được mã tracking qua email hoặc SMS. Bạn có thể theo dõi tình trạng đơn hàng trực tiếp trên website hoặc ứng dụng BookBee của chúng tôi.
                    </p>
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
                       class="btn-focus bg-black text-white px-8 py-4 font-bold text-sm uppercase tracking-wider hover:bg-gray-800">
                        XEM SÁCH NGAY
                    </a>
                    <a href="{{ route('contact.form') }}" 
                       class="btn-focus border-2 border-black text-black px-8 py-4 font-bold text-sm uppercase tracking-wider hover:bg-black hover:text-white">
                        LIÊN HỆ
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Minimal, performance-optimized JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Simple image loading optimization
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                if (img.complete) {
                    img.style.opacity = '1';
                }
            });
            
            // Smooth scroll for anchor links (minimal implementation)
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
@endpush
