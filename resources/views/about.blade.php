@extends('layouts.app')
@section('title', 'Giới thiệu về BookBee')

@push('styles')
    <style>
        /* Performance optimizations */
        * {
            box-sizing: border-box;
        }
        
        /* Geometric hero section with sharp lines */
        .about-hero {
            background: #000;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }
        
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.03) 100%);
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
        }
        
        .about-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #fff 0%, transparent 100%);
        }
        
        /* Geometric stats with sharp edges */
        .stats-counter {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 900;
            color: #000;
            line-height: 1;
            position: relative;
            font-family: 'Courier New', monospace;
        }
        
        .stats-counter::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #000;
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }
        
        .stats-counter.animated::after {
            transform: scaleX(1);
        }
        
        /* Sharp geometric team cards */
        .team-card {
            background: #fff;
            border: 2px solid #000;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: #000;
            transition: left 0.3s ease;
            z-index: 1;
        }
        
        .team-card:hover::before {
            left: 0;
        }
        
        .team-card:hover .team-content {
            color: #fff;
            z-index: 2;
            position: relative;
        }
        
        .team-card:hover .team-icon {
            color: #fff;
            z-index: 2;
            position: relative;
        }
        
        /* Geometric mission cards */
        .mission-card {
            background: #fff;
            border: 2px solid #000;
            transition: all 0.3s ease;
            position: relative;
            clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 0 100%);
        }
        
        .mission-card:hover {
            background: #000;
            color: #fff;
            transform: translateY(-5px);
        }
        
        .mission-card:hover .mission-number {
            background: #fff;
            color: #000;
        }
        
        /* Sharp timeline design */
        .timeline-item {
            position: relative;
            padding-left: 4rem;
            margin-bottom: 3rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            width: 2px;
            height: 100%;
            background: #000;
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 1rem;
            width: 3rem;
            height: 2px;
            background: #000;
        }
        
        .timeline-year {
            position: absolute;
            left: -2rem;
            top: 0;
            width: 4rem;
            height: 2rem;
            background: #000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 0.8rem;
            clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 100%, 10px 100%);
        }
        
        /* Geometric FAQ design */
        .faq-item {
            background: #fff;
            border: 2px solid #000;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            margin-bottom: 2px;
        }
        
        .faq-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: #000;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .faq-item:hover::before,
        .faq-item.active::before {
            transform: scaleY(1);
        }
        
        .faq-item:hover {
            background: #f8f9fa;
        }
        
        .faq-item.active {
            background: #000;
            color: #fff;
        }
        
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            border-top: 2px solid transparent;
        }
        
        .faq-item.active .faq-content {
            max-height: 200px;
            border-top-color: #333;
            padding-top: 1rem;
        }
        
        .faq-toggle {
            transition: transform 0.3s ease;
            width: 20px;
            height: 20px;
            border: 2px solid currentColor;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .faq-item.active .faq-toggle {
            transform: rotate(180deg);
            background: #fff;
            color: #000;
        }
        
        /* Sharp geometric buttons */
        .btn-primary {
            background: #000;
            color: #fff;
            padding: 16px 32px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            border: 2px solid #000;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 0 100%);
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: #333;
            transition: left 0.3s ease;
        }
        
        .btn-primary:hover::before {
            left: 0;
        }
        
        .btn-primary:hover {
            transform: translateX(5px);
        }
        
        .btn-secondary {
            background: #fff;
            color: #000;
            padding: 16px 32px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            border: 2px solid #000;
            transition: all 0.3s ease;
            position: relative;
            clip-path: polygon(10px 0, 100% 0, 100% 100%, 0 100%, 0 10px);
        }
        
        .btn-secondary:hover {
            background: #000;
            color: #fff;
            transform: translateX(-5px);
        }
        
        /* Geometric loading animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Sharp geometric icons */
        .icon-box {
            width: 60px;
            height: 60px;
            background: #000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 15px, 100% 100%, 0 100%);
        }
        
        .icon-box:hover {
            background: #333;
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Section dividers */
        .section-divider {
            height: 2px;
            background: #000;
            margin: 2rem 0;
            position: relative;
        }
        
        .section-divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 100px;
            height: 100%;
            background: #000;
            clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 100%, 10px 100%);
        }
        
        /* Geometric grid patterns */
        .grid-pattern {
            position: relative;
        }
        
        .grid-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(to right, rgba(0,0,0,0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0,0,0,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
            opacity: 0.3;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .about-hero {
                min-height: 80vh;
            }
            
            .stats-counter {
                font-size: 2rem;
            }
            
            .timeline-item {
                padding-left: 2rem;
            }
            
            .timeline-year {
                left: -1.5rem;
                width: 3rem;
                font-size: 0.7rem;
            }
            
            .btn-primary,
            .btn-secondary {
                padding: 12px 24px;
                font-size: 0.9rem;
            }
            
            .icon-box {
                width: 50px;
                height: 50px;
            }
        }
        
        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Focus states */
        .btn-focus:focus,
        .faq-item:focus {
            outline: 2px solid #000;
            outline-offset: 2px;
        }
        
        /* Geometric section spacing */
        .section-padding {
            padding: 80px 0;
            position: relative;
        }
        
        .section-padding::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #000, transparent);
        }
        
        @media (max-width: 768px) {
            .section-padding {
                padding: 60px 0;
            }
        }
        
        /* Typography improvements */
        .geometric-text {
            font-family: 'Courier New', monospace;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
        }
        
        /* Sharp corners everywhere */
        .sharp-corner {
            border-radius: 0 !important;
        }
        
        /* Geometric backgrounds */
        .geometric-bg {
            position: relative;
            overflow: hidden;
        }
        
        .geometric-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, transparent 45%, rgba(0,0,0,0.02) 50%, transparent 55%),
                linear-gradient(-45deg, transparent 45%, rgba(0,0,0,0.02) 50%, transparent 55%);
            background-size: 30px 30px;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section - Geometric Design -->
    <section class="about-hero text-white py-20 md:py-32 relative section-padding geometric-bg" id="hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="order-2 md:order-1 fade-in">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-0.5 bg-white"></div>
                        <span class="geometric-text text-xs text-gray-300">
                            VỀ CHÚNG TÔI
                        </span>
                        <div class="w-12 h-0.5 bg-white"></div>
                    </div>
                    <h1 class="text-5xl md:text-6xl lg:text-8xl font-black uppercase leading-[0.8] tracking-tight mb-8 geometric-text">
                        <span class="block">BOOK</span>
                        <span class="block text-gray-400">BEE</span>
                        <span class="block text-2xl md:text-3xl font-normal">2024</span>
                    </h1>
                    <div class="border-l-4 border-white pl-6 mb-8">
                        <p class="text-lg md:text-xl text-gray-300 leading-relaxed">
                            Nền tảng sách điện tử hiện đại với thiết kế tối giản và trải nghiệm người dùng tối ưu. Khám phá tri thức không giới hạn.
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-1 bg-white"></div>
                        <span class="geometric-text text-sm text-gray-300">THÀNH LẬP 2024</span>
                    </div>
                </div>
                <div class="relative order-1 md:order-2 fade-in">
                    <div class="aspect-square bg-white/5 border-2 border-white/20 p-8">
                        <div class="w-full h-full bg-white/10 border border-white/30 p-4">
                            <img src="{{ asset('images/single-image-about.jpg') }}" 
                                 alt="BookBee Story" 
                                 class="w-full h-full object-cover sharp-corner"
                                 loading="lazy"
                                 decoding="async">
                        </div>
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-white text-black px-6 py-3 geometric-text text-sm">
                        BOOKBEE
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section - Geometric Grid -->
    <section class="bg-white section-padding grid-pattern">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-0 border-2 border-black">
                <div class="p-8 text-center border-r-2 border-b-2 border-black md:border-b-0 fade-in">
                    <div class="stats-counter geometric-text mb-4" data-target="10000">0</div>
                    <div class="text-xs geometric-text text-gray-600">SÁCH TRONG KHO</div>
                </div>
                <div class="p-8 text-center border-b-2 border-black md:border-b-0 md:border-r-2 fade-in">
                    <div class="stats-counter geometric-text mb-4" data-target="5000">0</div>
                    <div class="text-xs geometric-text text-gray-600">KHÁCH HÀNG</div>
                </div>
                <div class="p-8 text-center border-r-2 border-black fade-in">
                    <div class="stats-counter geometric-text mb-4" data-target="24">0</div>
                    <div class="text-xs geometric-text text-gray-600">GIỜ HỖ TRỢ</div>
                </div>
                <div class="p-8 text-center fade-in">
                    <div class="stats-counter geometric-text mb-4" data-target="99">0</div>
                    <div class="text-xs geometric-text text-gray-600">% HÀI LÒNG</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section - Geometric Layout -->
    <section class="bg-gray-50 section-padding geometric-bg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="section-divider mb-8"></div>
                <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tight text-black mb-6 geometric-text">
                    TẠI SAO CHỌN BOOKBEE?
                </h2>
                <div class="section-divider"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 mb-16 border-2 border-black">
                <div class="p-12 border-r-2 border-black fade-in">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="icon-box">
                            <i class="fas fa-bullseye text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black geometric-text">SỨ MỆNH</h3>
                    </div>
                    <div class="border-l-4 border-black pl-6">
                        <p class="text-gray-700 leading-relaxed text-lg mb-6">
                            Dân chủ hóa việc tiếp cận tri thức thông qua nền tảng kỹ thuật số tiên tiến. Tạo ra trải nghiệm đọc sách liền mạch cho người dùng hiện đại.
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-1 bg-black"></div>
                            <span class="geometric-text text-sm text-gray-500">SỨ MỆNH</span>
                        </div>
                    </div>
                </div>

                <div class="p-12 fade-in">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="icon-box">
                            <i class="fas fa-eye text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-black geometric-text">TẦM NHÌN</h3>
                    </div>
                    <div class="border-l-4 border-black pl-6">
                        <p class="text-gray-700 leading-relaxed text-lg mb-6">
                            Trở thành nền tảng sách kỹ thuật số hàng đầu Việt Nam, đặt ra tiêu chuẩn mới cho trải nghiệm người dùng và khả năng tiếp cận nội dung.
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-1 bg-black"></div>
                            <span class="geometric-text text-sm text-gray-500">TẦM NHÌN</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="mission-card p-8 fade-in">
                    <div class="mission-number w-16 h-16 bg-black text-white flex items-center justify-center mb-6 geometric-text text-xl">
                        01
                    </div>
                    <h3 class="text-xl font-black geometric-text mb-4">CHẤT LƯỢNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Bộ sưu tập được tuyển chọn kỹ càng những cuốn sách cao cấp từ các nhà xuất bản uy tín trên toàn thế giới.
                    </p>
                </div>

                <div class="mission-card p-8 fade-in">
                    <div class="mission-number w-16 h-16 bg-black text-white flex items-center justify-center mb-6 geometric-text text-xl">
                        02
                    </div>
                    <h3 class="text-xl font-black geometric-text mb-4">ĐA DẠNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Thư viện toàn diện bao gồm tất cả các thể loại và chủ đề dành cho mọi độc giả.
                    </p>
                </div>

                <div class="mission-card p-8 fade-in">
                    <div class="mission-number w-16 h-16 bg-black text-white flex items-center justify-center mb-6 geometric-text text-xl">
                        03
                    </div>
                    <h3 class="text-xl font-black geometric-text mb-4">TỐC ĐỘ</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Giao hàng nhanh như chớp và truy cập kỹ thuật số tức thì đến những cuốn sách yêu thích của bạn.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section - Geometric Design -->
    <section class="bg-white section-padding">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="section-divider mb-8"></div>
                <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tight text-black geometric-text">
                    LỊCH SỬ PHÁT TRIỂN
                </h2>
                <div class="section-divider"></div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="timeline-item">
                    <div class="timeline-year">2024</div>
                    <div class="bg-gray-50 border-2 border-black p-8 ml-4">
                        <h3 class="text-2xl font-black geometric-text mb-4">KHỞI ĐẦU</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Nền tảng BookBee ra mắt với trọng tâm là thiết kế tối giản và trải nghiệm người dùng tối ưu. Bắt đầu với bộ sưu tập được tuyển chọn kỹ càng.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2024</div>
                    <div class="bg-gray-50 border-2 border-black p-8 ml-4">
                        <h3 class="text-2xl font-black geometric-text mb-4">PHÁT TRIỂN</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Mở rộng thư viện với hàng ngàn đầu sách mới. Cải tiến UX với các nguyên tắc thiết kế hình học và mạng lưới giao hàng chuyên nghiệp.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">HIỆN TẠI</div>
                    <div class="bg-black text-white border-2 border-black p-8 ml-4">
                        <h3 class="text-2xl font-black geometric-text mb-4">TƯƠNG LAI</h3>
                        <p class="text-gray-300 leading-relaxed">
                            Tiên phong trong tương lai của việc đọc sách kỹ thuật số với các tính năng tiên tiến và sự hài lòng người dùng vô song.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section - Geometric Grid -->
    <section class="bg-gray-50 section-padding geometric-bg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="section-divider mb-8"></div>
                <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tight text-black geometric-text">
                    ĐỘI NGŨ
                </h2>
                <div class="section-divider"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="team-card p-8 text-center fade-in">
                    <div class="team-content">
                        <div class="team-icon w-24 h-24 bg-gray-200 border-2 border-black mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-black geometric-text mb-2">TRƯỞNG NHÓM</h3>
                        <p class="text-gray-600 geometric-text text-sm mb-4">NGƯỜI SÁNG LẬP & GIÁM ĐỐC ĐIỀU HÀNH</p>
                        <div class="w-full h-1 bg-black"></div>
                    </div>
                </div>

                <div class="team-card p-8 text-center fade-in">
                    <div class="team-content">
                        <div class="team-icon w-24 h-24 bg-gray-200 border-2 border-black mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-code text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-black geometric-text mb-2">LẬP TRÌNH VIÊN</h3>
                        <p class="text-gray-600 geometric-text text-sm mb-4">TRƯỞNG PHÒNG KỸ THUẬT</p>
                        <div class="w-full h-1 bg-black"></div>
                    </div>
                </div>

                <div class="team-card p-8 text-center fade-in">
                    <div class="team-content">
                        <div class="team-icon w-24 h-24 bg-gray-200 border-2 border-black mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-palette text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-black geometric-text mb-2">THIẾT KẾ</h3>
                        <p class="text-gray-600 geometric-text text-sm mb-4">GIÁM ĐỐC SÁNG TẠO</p>
                        <div class="w-full h-1 bg-black"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section - Geometric Accordion -->
    <section class="bg-white section-padding">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="section-divider mb-8"></div>
                <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tight text-black geometric-text">
                    CÂU HỎI THƯỜNG GẶP
                </h2>
                <div class="section-divider"></div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="faq-item mb-2">
                    <div class="flex justify-between items-center p-6">
                        <h3 class="text-lg font-black geometric-text">BOOKBEE CÓ NHỮNG LOẠI SÁCH NÀO?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="faq-content px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            Thư viện kỹ thuật số toàn diện bao gồm văn học, khoa học, công nghệ, kinh doanh, phát triển bản thân và sách thiếu nhi. Được cập nhật liên tục với những phát hành mới nhất từ các nhà xuất bản uy tín.
                        </p>
                    </div>
                </div>

                <div class="faq-item mb-2">
                    <div class="flex justify-between items-center p-6">
                        <h3 class="text-lg font-black geometric-text">THỜI GIAN GIAO HÀNG NHƯ THẾ NÀO?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="faq-content px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            Giao hàng 24-48 giờ trong phạm vi thành phố, 2-3 ngày toàn quốc. Có sẵn giao hàng cấp tốc trong ngày cho đơn hàng khẩn cấp. Sách kỹ thuật số có sẵn ngay lập tức.
                        </p>
                    </div>
                </div>

                <div class="faq-item mb-2">
                    <div class="flex justify-between items-center p-6">
                        <h3 class="text-lg font-black geometric-text">CÓ CHÍNH SÁCH ĐỔI TRẢ KHÔNG?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="faq-content px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            Chính sách đổi trả 7 ngày cho các mặt hàng bị lỗi hoặc đơn hàng không đúng. Liên hệ hỗ trợ qua đường dây nóng hoặc email để được hỗ trợ. Việc mua sách kỹ thuật số tuân theo điều khoản của nền tảng.
                        </p>
                    </div>
                </div>

                <div class="faq-item mb-2">
                    <div class="flex justify-between items-center p-6">
                        <h3 class="text-lg font-black geometric-text">LÀM SAO ĐỂ THEO DÕI ĐƠN HÀNG?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    <div class="faq-content px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">
                            Nhận mã theo dõi qua email/SMS sau khi đặt hàng thành công. Theo dõi trạng thái đơn hàng trực tiếp trên trang web BookBee hoặc ứng dụng di động. Có sẵn cập nhật theo thời gian thực.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section - Geometric Design -->
    <section class="bg-black text-white section-padding geometric-bg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center fade-in">
                <div class="border-4 border-white p-16 relative">
                    <div class="absolute top-0 left-0 w-8 h-8 border-l-4 border-t-4 border-white"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-r-4 border-t-4 border-white"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-l-4 border-b-4 border-white"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-r-4 border-b-4 border-white"></div>
                    
                    <h2 class="text-4xl md:text-6xl font-black uppercase tracking-tight mb-8 geometric-text">
                        BẮT ĐẦU HÀNH TRÌNH CỦA BẠN
                    </h2>
                    
                    <div class="w-24 h-1 bg-white mx-auto mb-8"></div>
                    
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto mb-12 leading-relaxed">
                        Khám phá tri thức không giới hạn với nền tảng hiện đại của BookBee. Trải nghiệm tương lai của việc đọc sách kỹ thuật số.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('books.index') }}" class="btn-primary">
                            XEM SÁCH NGAY
                        </a>
                        <a href="{{ route('contact.form') }}" class="btn-secondary">
                            LIÊN HỆ NGAY
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animated counter
            function animateCounter(element, target, duration = 2000) {
                let start = 0;
                const increment = target / (duration / 16);
                const timer = setInterval(() => {
                    start += increment;
                    element.textContent = Math.floor(start).toLocaleString();
                    if (start >= target) {
                        element.textContent = target.toLocaleString();
                        clearInterval(timer);
                    }
                }, 16);
            }

            // Initialize counters when in view
            const counters = document.querySelectorAll('.stats-counter');
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px 0px -50px 0px'
            };

            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseInt(entry.target.dataset.target);
                        animateCounter(entry.target, target);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            counters.forEach(counter => {
                counterObserver.observe(counter);
            });

            // FAQ toggle functionality
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                item.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    
                    // Close all FAQ items
                    faqItems.forEach(faq => {
                        faq.classList.remove('active');
                    });
                    
                    // Open clicked item if it wasn't active
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });

            // Smooth scroll for anchor links
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

            // Fade in animations
            const fadeElements = document.querySelectorAll('.fade-in');
            const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        fadeObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            fadeElements.forEach(element => {
                fadeObserver.observe(element);
            });

            // Parallax effect for hero section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const parallaxElements = document.querySelectorAll('.parallax-bg');
                
                parallaxElements.forEach(element => {
                    const speed = 0.5;
                    element.style.transform = `translateY(${scrolled * speed}px)`;
                });
            });

            // Loading optimization
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.addEventListener('load', () => {
                    img.style.opacity = '1';
                });
            });
        });
    </script>
@endpush
