@extends('layouts.app')
@section('title', 'Giới thiệu về BookBee')

@push('styles')
    <style>
        /* Performance optimizations */
        * {
            box-sizing: border-box;
        }
        
        /* Geometric hero section with sharp design */
        .about-hero {
            background: #000;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
        }
        
        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 40%;
            height: 100%;
            background: linear-gradient(45deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            clip-path: polygon(30% 0, 100% 0, 70% 100%, 0 100%);
            animation: geometricShimmer 6s infinite;
        }
        
        .about-hero::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 100px;
            background: repeating-linear-gradient(
                90deg,
                transparent,
                transparent 10px,
                rgba(255,255,255,0.05) 10px,
                rgba(255,255,255,0.05) 20px
            );
            opacity: 0.3;
        }
        
        @keyframes geometricShimmer {
            0%, 100% { 
                opacity: 0.3; 
                transform: translateX(0);
            }
            50% { 
                opacity: 0.6; 
                transform: translateX(20px);
            }
        }
        
        /* Geometric text styling */
        .geometric-text {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            position: relative;
        }
        
        .geometric-text::before {
            content: '';
            position: absolute;
            top: 0;
            left: -10px;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #fff, transparent);
            animation: textAccent 3s infinite;
        }
        
        @keyframes textAccent {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }
        
        /* Enhanced geometric stats with sharp grid */
        .stats-section {
            background: #fff;
            position: relative;
            overflow: hidden;
        }
        
        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(90deg, rgba(0,0,0,0.05) 1px, transparent 1px),
                linear-gradient(180deg, rgba(0,0,0,0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.3;
        }
        
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
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: #000;
            clip-path: polygon(0 0, 100% 0, 90% 100%, 10% 100%);
        }
        
        .stats-item {
            background: #fff;
            border: 2px solid #000;
            clip-path: polygon(0 0, 95% 0, 100% 100%, 5% 100%);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .stats-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stats-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #000, #333);
            clip-path: polygon(0 0, 100% 0, 95% 100%, 5% 100%);
        }
        
        /* Geometric team cards with sharp edges */
        .team-card {
            transition: all 0.3s ease;
            position: relative;
            background: #fff;
            border: 3px solid #000;
            clip-path: polygon(0 0, 90% 0, 100% 100%, 10% 100%);
            overflow: hidden;
        }
        
        .team-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #000, #333);
            clip-path: polygon(0 0, 100% 0, 90% 100%, 10% 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .team-card:hover::before {
            transform: scaleX(1);
        }
        
        .team-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: repeating-linear-gradient(
                90deg,
                #000,
                #000 5px,
                transparent 5px,
                transparent 10px
            );
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .team-card:hover::after {
            opacity: 1;
        }
        
        /* Enhanced geometric mission cards */
        .mission-card {
            background: #f8f9fa;
            border: 3px solid #000;
            border-radius: 0;
            clip-path: polygon(0 0, 100% 0, 95% 100%, 0 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .mission-card:hover {
            background: #fff;
            transform: translateX(15px) scale(1.02);
            box-shadow: -10px 10px 30px rgba(0,0,0,0.15);
        }
        
        .mission-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #000, #333);
            clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);
        }
        
        .mission-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                -45deg,
                transparent,
                transparent 10px,
                rgba(0,0,0,0.02) 10px,
                rgba(0,0,0,0.02) 20px
            );
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .mission-card:hover::after {
            opacity: 1;
        }
        
        /* Geometric timeline with sharp indicators */
        .timeline-item {
            position: relative;
            padding-left: 4rem;
            opacity: 0;
            transform: translateX(-30px);
            animation: fadeInLeft 0.8s ease forwards;
            margin-bottom: 2rem;
        }
        
        .timeline-item:nth-child(2) {
            animation-delay: 0.3s;
        }
        
        .timeline-item:nth-child(3) {
            animation-delay: 0.6s;
        }
        
        .timeline-item:nth-child(4) {
            animation-delay: 0.9s;
        }
        
        @keyframes fadeInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #000, #333);
            clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            left: -10px;
            top: 10px;
            width: 24px;
            height: 24px;
            background: #000;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #000;
        }
        
        .timeline-content {
            background: #fff;
            border: 2px solid #000;
            clip-path: polygon(0 0, 95% 0, 100% 100%, 5% 100%);
            padding: 1.5rem;
            position: relative;
        }
        
        .timeline-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #000, #333);
        }
        
        /* Geometric FAQ with accordion design */
        .faq-item {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid #000;
            background: #fff;
            clip-path: polygon(0 0, 98% 0, 100% 100%, 2% 100%);
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .faq-item:hover {
            transform: translateX(10px);
            box-shadow: -5px 5px 20px rgba(0,0,0,0.1);
        }
        
        .faq-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #000, #333);
            clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);
        }
        
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            background: rgba(0,0,0,0.02);
        }
        
        .faq-item.active .faq-content {
            max-height: 300px;
        }
        
        .faq-toggle {
            transition: transform 0.3s ease;
            width: 24px;
            height: 24px;
            background: #000;
            color: #fff;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .faq-item.active .faq-toggle {
            transform: rotate(180deg);
        }
        
        .faq-header {
            padding: 1.5rem;
            background: linear-gradient(90deg, rgba(0,0,0,0.02), transparent);
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        /* Geometric buttons with sharp design */
        .btn-primary {
            background: #000;
            color: #fff;
            padding: 16px 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border: none;
            clip-path: polygon(0 0, 90% 0, 100% 100%, 10% 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: 'Courier New', monospace;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            clip-path: polygon(20% 0, 80% 0, 60% 100%, 40% 100%);
            transition: left 0.5s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: repeating-linear-gradient(
                90deg,
                #fff,
                #fff 3px,
                transparent 3px,
                transparent 6px
            );
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .btn-primary:hover::after {
            opacity: 1;
        }
        
        /* Geometric section dividers */
        .section-divider {
            height: 6px;
            background: linear-gradient(90deg, #000, #333, #000);
            clip-path: polygon(5% 0, 95% 0, 100% 100%, 0 100%);
            margin: 4rem 0;
            position: relative;
        }
        
        .section-divider::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-bottom: 15px solid #000;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .btn-secondary {
            border: 2px solid #000;
            color: #000;
            padding: 14px 30px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-radius: 0;
            transition: all 0.3s ease;
            background: transparent;
        }
        
        .btn-secondary:hover {
            background: #000;
            color: #fff;
            transform: translateY(-2px);
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Loading animations */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Staggered animations */
        .fade-in:nth-child(1) { animation-delay: 0.1s; }
        .fade-in:nth-child(2) { animation-delay: 0.2s; }
        .fade-in:nth-child(3) { animation-delay: 0.3s; }
        .fade-in:nth-child(4) { animation-delay: 0.4s; }
        
        /* Enhanced icons */
        .icon-box {
            width: 64px;
            height: 64px;
            background: #000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .icon-box::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50%;
            background: linear-gradient(45deg, #000, #333);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .icon-box:hover::before {
            opacity: 1;
        }
        
        .icon-box:hover {
            transform: scale(1.1);
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
            
            .timeline-item::before {
                left: 0.5rem;
            }
            
            .timeline-item::after {
                left: -4px;
                width: 12px;
                height: 12px;
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
        .btn-focus:focus {
            outline: 2px solid #000;
            outline-offset: 2px;
        }
        
        /* Parallax effect */
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        /* Improved section spacing */
        .section-padding {
            padding: 80px 0;
        }
        
        @media (max-width: 768px) {
            .section-padding {
                padding: 60px 0;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section - Enhanced -->
    <section class="about-hero text-white py-20 md:py-32 relative section-padding" id="hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                <div class="order-2 md:order-1 fade-in">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-8 md:w-12 h-0.5 bg-white"></div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] md:tracking-[0.3em] text-gray-300">
                            VỀ CHÚNG TÔI
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-black uppercase leading-[0.9] tracking-tight mb-6 geometric-text">
                        <span class="block">IMPOSSIBLE</span>
                        <span class="block text-gray-400">IS</span>
                        <span class="block">NOTHING</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-lg">
                        BookBee - Nơi tri thức không có giới hạn. Chúng tôi tin rằng mọi cuốn sách đều có thể thay đổi cuộc sống của bạn.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 md:w-16 h-0.5 bg-white"></div>
                        <span class="text-xs md:text-sm font-bold uppercase tracking-wide">EST. 2024</span>
                    </div>
                </div>
                <div class="relative order-1 md:order-2 fade-in">
                    <div class="aspect-square bg-white/10 p-4 md:p-8 rounded-lg">
                        <img src="{{ asset('images/single-image-about.jpg') }}" 
                             alt="BookBee Story" 
                             class="w-full h-full object-cover rounded-lg"
                             loading="lazy"
                             decoding="async">
                    </div>
                    <div class="absolute -bottom-4 md:-bottom-6 -right-4 md:-right-6 bg-white text-black px-4 py-2 md:px-6 md:py-3 font-bold uppercase tracking-wide text-sm md:text-base rounded">
                        BOOKBEE
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section - Enhanced with Geometric Design -->
    <section class="stats-section section-padding">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="stats-item p-6 geometric-animate">
                    <div class="stats-counter" data-target="10000">0</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold geometric-text">Sách trong kho</div>
                </div>
                <div class="stats-item p-6 geometric-animate">
                    <div class="stats-counter" data-target="5000">0</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold geometric-text">Khách hàng</div>
                </div>
                <div class="stats-item p-6 geometric-animate">
                    <div class="stats-counter" data-target="24">0</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold geometric-text">Giờ hỗ trợ</div>
                </div>
                <div class="stats-item p-6 geometric-animate">
                    <div class="stats-counter" data-target="99">0</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-gray-600 font-bold geometric-text">% Hài lòng</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Geometric Section Divider -->
    <div class="section-divider"></div>

    <!-- Mission Section - Enhanced -->
    <section class="bg-gray-50 section-padding">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        SỨ MỆNH & TẦM NHÌN
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black mb-6 geometric-text">
                    TẠI SAO CHỌN BOOKBEE?
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-16">
                <div class="space-y-6 fade-in">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="icon-box">
                            <i class="fas fa-bullseye text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold uppercase tracking-wide geometric-text">SỨ MỆNH</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Mang tri thức đến gần hơn với mọi người, tạo ra một cộng đồng yêu sách và học hỏi. Chúng tôi tin rằng mỗi cuốn sách đều có thể thay đổi cuộc sống và mở ra những cơ hội mới.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-0.5 bg-black"></div>
                        <span class="text-sm font-bold uppercase tracking-wide text-gray-500">EST. 2024</span>
                    </div>
                </div>

                <div class="space-y-6 fade-in">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="icon-box">
                            <i class="fas fa-eye text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold uppercase tracking-wide geometric-text">TẦM NHÌN</h3>
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 geometric-animate">
                <div class="mission-card p-8 fade-in">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl clip-path: polygon(50% 0%, 0% 100%, 100% 100%)">
                        01
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4 geometric-text">CHẤT LƯỢNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Chúng tôi cam kết cung cấp những cuốn sách chất lượng cao, được tuyển chọn kỹ càng từ các nhà xuất bản uy tín.
                    </p>
                </div>

                <div class="mission-card p-8 fade-in">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl clip-path: polygon(50% 0%, 0% 100%, 100% 100%)">
                        02
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4 geometric-text">ĐA DẠNG</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Kho sách đa dạng với hàng nghìn đầu sách thuộc mọi thể loại, từ văn học, khoa học đến phát triển bản thân.
                    </p>
                </div>

                <div class="mission-card p-8 fade-in">
                    <div class="w-16 h-16 bg-black text-white flex items-center justify-center mb-6 font-bold text-xl clip-path: polygon(50% 0%, 0% 100%, 100% 100%)">
                        03
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-4 geometric-text">TỐC ĐỘ</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Giao hàng nhanh chóng với đội ngũ logistics chuyên nghiệp, đảm bảo sách đến tay bạn trong thời gian sớm nhất.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Timeline - Enhanced -->
    <section class="bg-white section-padding">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        LỊCH SỬ PHÁT TRIỂN
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black geometric-text">
                    HÀNH TRÌNH CỦA CHÚNG TÔI
                </h2>
            </div>

            <div class="max-w-4xl mx-auto geometric-animate">
                <div class="timeline-item mb-12">
                    <div class="timeline-content">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-2xl font-black geometric-text">2024</span>
                            <div class="w-8 h-0.5 bg-black"></div>
                        </div>
                        <h3 class="text-xl font-bold uppercase tracking-wide mb-4 geometric-text">KHỞI ĐẦU</h3>
                        <p class="text-gray-600 leading-relaxed">
                            BookBee được thành lập với mục tiêu mang tri thức đến gần hơn với mọi người. Chúng tôi bắt đầu với một kho sách nhỏ nhưng được tuyển chọn cẩn thận.
                        </p>
                    </div>
                </div>

                <div class="timeline-item mb-12">
                    <div class="timeline-content">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-2xl font-black geometric-text">2024</span>
                            <div class="w-8 h-0.5 bg-black"></div>
                        </div>
                        <h3 class="text-xl font-bold uppercase tracking-wide mb-4 geometric-text">PHÁT TRIỂN</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Mở rộng hệ thống với hàng nghìn đầu sách mới, cải thiện trải nghiệm người dùng và xây dựng đội ngũ giao hàng chuyên nghiệp.
                        </p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-content bg-black text-white">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-2xl font-black geometric-text">NOW</span>
                            <div class="w-8 h-0.5 bg-white"></div>
                        </div>
                        <h3 class="text-xl font-bold uppercase tracking-wide mb-4 geometric-text">TƯƠNG LAI</h3>
                        <p class="text-gray-300 leading-relaxed">
                            Hướng tới trở thành nền tảng sách hàng đầu, không ngừng cải tiến và mang đến những trải nghiệm tuyệt vời nhất cho khách hàng.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section - Enhanced -->
    <section class="bg-gray-50 section-padding">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        ĐỘI NGŨ
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black geometric-text">
                    NHỮNG NGƯỜI THỰC HIỆN
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 geometric-animate">
                <div class="team-card p-8 text-center fade-in">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);">
                        <i class="fas fa-user text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2 geometric-text">TEAM LEADER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Founder & CEO</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>

                <div class="team-card p-8 text-center fade-in">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);">
                        <i class="fas fa-code text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2 geometric-text">DEVELOPER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Technical Lead</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>

                <div class="team-card p-8 text-center fade-in">
                    <div class="w-32 h-32 bg-gray-200 mx-auto mb-6 flex items-center justify-center" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);">
                        <i class="fas fa-palette text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-bold uppercase tracking-wide mb-2 geometric-text">DESIGNER</h3>
                    <p class="text-gray-600 uppercase tracking-wide text-sm mb-4">Creative Director</p>
                    <div class="w-8 h-0.5 bg-black mx-auto"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section - Interactive -->
    <section class="bg-white section-padding">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16 fade-in">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-12 h-0.5 bg-black"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-gray-600">
                        CÂU HỎI THƯỜNG GẶP
                    </span>
                    <div class="w-12 h-0.5 bg-black"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-black geometric-text">
                    GIẢI ĐÁP THẮC MẮC
                </h2>
            </div>

            <div class="max-w-4xl mx-auto space-y-6 geometric-animate">
                <div class="faq-item">
                    <div class="faq-header flex justify-between items-center">
                        <h3 class="text-lg font-bold uppercase tracking-wide geometric-text">BookBee có những loại sách nào?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <div class="faq-content p-6">
                        <p class="text-gray-600 leading-relaxed">
                            Chúng tôi cung cấp đa dạng các thể loại sách từ văn học, khoa học, kỹ thuật, kinh doanh, phát triển bản thân đến sách thiếu nhi. Kho sách của chúng tôi được cập nhật liên tục với những đầu sách mới nhất.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header flex justify-between items-center">
                        <h3 class="text-lg font-bold uppercase tracking-wide geometric-text">Thời gian giao hàng như thế nào?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <div class="faq-content p-6">
                        <p class="text-gray-600 leading-relaxed">
                            Chúng tôi cam kết giao hàng trong vòng 24-48h đối với nội thành và 2-3 ngày đối với các tỉnh thành khác. Đặc biệt, chúng tôi có dịch vụ giao hàng nhanh trong ngày cho những đơn hàng khẩn cấp.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header flex justify-between items-center">
                        <h3 class="text-lg font-bold uppercase tracking-wide geometric-text">Có chính sách đổi trả không?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <div class="faq-content p-6">
                        <p class="text-gray-600 leading-relaxed">
                            Chúng tôi hỗ trợ đổi trả trong vòng 7 ngày kể từ khi nhận hàng nếu sản phẩm có lỗi từ nhà sản xuất hoặc không đúng như mô tả. Khách hàng có thể liên hệ với chúng tôi qua hotline hoặc email.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header flex justify-between items-center">
                        <h3 class="text-lg font-bold uppercase tracking-wide geometric-text">Làm sao để theo dõi đơn hàng?</h3>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <div class="faq-content p-6">
                        <p class="text-gray-600 leading-relaxed">
                            Sau khi đặt hàng thành công, bạn sẽ nhận được mã tracking qua email hoặc SMS. Bạn có thể theo dõi tình trạng đơn hàng trực tiếp trên website hoặc ứng dụng BookBee của chúng tôi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section - Enhanced -->
    <section class="bg-black text-white section-padding parallax-bg">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto fade-in geometric-animate">
                <h2 class="text-3xl md:text-4xl font-black uppercase tracking-tight mb-6 geometric-text">
                    BẮT ĐẦU HÀNH TRÌNH CỦA BẠN
                </h2>
                <p class="text-xl text-gray-300 mb-8">
                    Khám phá kho tàng tri thức cùng BookBee ngay hôm nay
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('books.index') }}" class="btn-primary">
                        XEM SÁCH NGAY
                    </a>
                    <a href="{{ route('contact.form') }}" class="btn-secondary" style="border: 2px solid #fff; color: #fff;">
                        LIÊN HỆ
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced stats counter with geometric animation
            function animateCounters() {
                const counters = document.querySelectorAll('.stats-counter');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const counter = entry.target;
                            const target = parseInt(counter.getAttribute('data-target'));
                            const duration = 2000;
                            const increment = target / (duration / 16);
                            let current = 0;
                            
                            const timer = setInterval(() => {
                                current += increment;
                                if (current >= target) {
                                    current = target;
                                    clearInterval(timer);
                                }
                                counter.textContent = Math.floor(current).toLocaleString();
                            }, 16);
                            
                            observer.unobserve(counter);
                        }
                    });
                }, { threshold: 0.5 });
                
                counters.forEach(counter => observer.observe(counter));
            }
            
            // Geometric mouse follow effect
            function createGeometricCursor() {
                const cursor = document.createElement('div');
                cursor.style.cssText = `
                    position: fixed;
                    width: 20px;
                    height: 20px;
                    background: rgba(0,0,0,0.1);
                    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
                    pointer-events: none;
                    z-index: 9999;
                    transition: transform 0.1s ease;
                `;
                document.body.appendChild(cursor);
                
                document.addEventListener('mousemove', (e) => {
                    cursor.style.left = e.clientX - 10 + 'px';
                    cursor.style.top = e.clientY - 10 + 'px';
                });
                
                // Hide cursor when leaving viewport
                document.addEventListener('mouseleave', () => {
                    cursor.style.opacity = '0';
                });
                
                document.addEventListener('mouseenter', () => {
                    cursor.style.opacity = '1';
                });
            }
            
            // Enhanced FAQ functionality with geometric animations
            function initFAQ() {
                const faqItems = document.querySelectorAll('.faq-item');
                faqItems.forEach(item => {
                    const header = item.querySelector('.faq-header');
                    const content = item.querySelector('.faq-content');
                    
                    header.addEventListener('click', () => {
                        const isActive = item.classList.contains('active');
                        
                        // Close all other FAQ items
                        faqItems.forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.classList.remove('active');
                            }
                        });
                        
                        // Toggle current item
                        if (isActive) {
                            item.classList.remove('active');
                        } else {
                            item.classList.add('active');
                        }
                    });
                });
            }
            
            // Geometric intersection animations
            function initGeometricAnimations() {
                const geometricElements = document.querySelectorAll('.geometric-animate');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0) scale(1)';
                            
                            // Add staggered animation for child elements
                            const children = entry.target.querySelectorAll('[class*="card"], [class*="item"]');
                            children.forEach((child, index) => {
                                setTimeout(() => {
                                    child.style.opacity = '1';
                                    child.style.transform = 'translateY(0) scale(1)';
                                }, index * 150);
                            });
                            
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                
                geometricElements.forEach(element => {
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(30px) scale(0.95)';
                    observer.observe(element);
                });
            }
            
            // Initialize all enhanced functions
            animateCounters();
            createGeometricCursor();
            initFAQ();
            initGeometricAnimations();
            
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
