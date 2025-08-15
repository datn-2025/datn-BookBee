@extends('layouts.app')
@section('title', $article->title)

@push('styles')
<style>
    /* Clean Adidas-style design cho trang chi tiết tin tức */
    .news-detail-page {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    /* Smooth page loading animation */
    .fade-in {
        animation: fadeInUp 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Staggered animation delays */
    .fade-in-delay-1 { animation-delay: 0.1s; }
    .fade-in-delay-2 { animation-delay: 0.2s; }
    .fade-in-delay-3 { animation-delay: 0.3s; }
    .fade-in-delay-4 { animation-delay: 0.4s; }
    
    /* Hero section */
    .article-hero {
        position: relative;
        overflow: hidden;
        background: #fff;
        border-bottom: 2px solid #000;
    }
    
    .article-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100%;
        background: #000;
        opacity: 0.05;
        transform: skewX(-15deg);
    }
    
    /* Enhanced parallax effect */
    .parallax-bg {
        will-change: transform;
        transition: transform 0.1s ease-out;
    }
    
    /* Article content styling */
    .article-content {
        line-height: 1.8;
        font-size: 1.1rem;
    }
    
    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5,
    .article-content h6 {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 2rem 0 1rem 0;
        color: #000;
    }
    
    .article-content h1 { font-size: 2.5rem; }
    .article-content h2 { font-size: 2rem; }
    .article-content h3 { font-size: 1.5rem; }
    
    .article-content p {
        margin-bottom: 1.5rem;
        color: #333;
    }
    
    .article-content blockquote {
        border-left: 4px solid #000;
        padding-left: 2rem;
        margin: 2rem 0;
        font-style: italic;
        font-size: 1.2rem;
        color: #666;
        background: #f8f8f8;
        padding: 1.5rem 2rem;
    }
    
    .article-content ul,
    .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    .article-content li {
        margin-bottom: 0.5rem;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        margin: 2rem 0;
        border: 2px solid #f0f0f0;
        transition: border-color 0.3s ease;
    }
    
    .article-content img:hover {
        border-color: #000;
    }
    
    /* Meta info styling */
    .meta-info {
        background: #000;
        color: #fff;
        padding: 8px 16px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.75rem;
        position: relative;
        overflow: hidden;
    }
    
    /* Enhanced news cards for related articles */
    .related-card {
        background: #fff;
        border: 2px solid #f5f5f5;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .related-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: #000;
        transition: left 0.3s ease;
    }
    
    .related-card:hover::before {
        left: 0;
    }
    
    .related-card:hover {
        border-color: #000;
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    /* Navigation buttons */
    .nav-btn {
        background: #000;
        color: #fff;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        border: none;
        transition: all 0.3s ease;
    }
    
    .nav-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .nav-btn:hover::before {
        left: 100%;
    }
    
    .nav-btn:hover {
        background: #333;
        transform: translateX(4px) scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    /* Share buttons */
    .share-btn {
        background: #fff;
        border: 2px solid #000;
        color: #000;
        padding: 12px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .share-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: #000;
        transition: left 0.3s ease;
        z-index: -1;
    }
    
    .share-btn:hover::before {
        left: 0;
    }
    
    .share-btn:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    
    /* Progress reading bar */
    .reading-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #000, #333);
        z-index: 1000;
        transition: width 0.1s ease;
    }
    
    /* Back to top button */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: #000;
        color: #fff;
        border: none;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .back-to-top:hover {
        background: #333;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    
    /* Table of contents */
    .toc {
        background: #f8f8f8;
        border: 2px solid #000;
        padding: 2rem;
        position: sticky;
        top: 2rem;
    }
    
    .toc ul {
        list-style: none;
        padding: 0;
    }
    
    .toc li {
        margin-bottom: 0.5rem;
    }
    
    .toc a {
        color: #000;
        text-decoration: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
        padding: 0.5rem 0;
        display: block;
        border-bottom: 1px solid transparent;
        transition: all 0.3s ease;
    }
    
    .toc a:hover {
        border-bottom-color: #000;
        padding-left: 1rem;
    }
    
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .article-content {
            font-size: 12pt;
            line-height: 1.6;
        }
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .article-content h1 { font-size: 2rem; }
        .article-content h2 { font-size: 1.5rem; }
        .article-content h3 { font-size: 1.25rem; }
        
        .related-card:hover {
            transform: translateY(-4px) scale(1.01);
        }
        
        .back-to-top {
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
        }
        
        .toc {
            position: relative;
            top: 0;
        }
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .loading-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f0f0f0;
        border-top: 3px solid #000;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="news-detail-page">
    <!-- Reading Progress Bar -->
    <div class="reading-progress"></div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Back to Top Button -->
    <button class="back-to-top no-print" id="backToTop" title="Về đầu trang">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Article Hero Section -->
    <section class="article-hero bg-white py-16 md:py-24 relative overflow-hidden fade-in">
        <!-- Background Elements - Adidas Style -->
        <div class="absolute inset-0 pointer-events-none parallax-bg">
            <div class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 transform rotate-45 translate-x-36 -translate-y-36"></div>
            <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
            <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb - Adidas Style -->
            <nav class="flex items-center gap-2 mb-8 text-sm font-semibold uppercase tracking-wide fade-in-delay-1">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-black transition-colors">Trang chủ</a>
                <div class="w-2 h-0.5 bg-black"></div>
                <a href="{{ route('news.index') }}" class="text-gray-600 hover:text-black transition-colors">Tin tức</a>
                <div class="w-2 h-0.5 bg-black"></div>
                <span class="text-black">Chi tiết</span>
            </nav>

            <!-- Article Meta Info -->
            <div class="flex flex-wrap items-center gap-4 mb-8 fade-in-delay-2">
                <span class="meta-info">{{ $article->category ?? 'TIN TỨC' }}</span>
                <div class="flex items-center text-gray-600">
                    <div class="w-2 h-2 bg-black mr-2"></div>
                    {{ $article->created_at->format('d M Y') }}
                </div>
            </div>

            <!-- Article Title -->
            <h1 class="text-3xl md:text-5xl font-black uppercase leading-tight tracking-tight text-black mb-8 fade-in-delay-3">
                {{ $article->title }}
            </h1>

            <!-- Article Summary -->
            @if($article->summary)
            <div class="text-xl text-gray-700 font-medium leading-relaxed mb-8 fade-in-delay-4 p-6 bg-gray-50 border-l-4 border-black">
                {{ $article->summary }}
            </div>
            @endif
        </div>
    </section>

    <!-- Main Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Article Content -->
            <article class="lg:w-2/3">
                <!-- Featured Image -->
                @if($article->thumbnail)
                <div class="mb-12 fade-in">
                    <img src="{{ $article->thumbnail }}" 
                         alt="{{ $article->title }}" 
                         class="w-full h-64 md:h-96 object-cover border-2 border-black">
                </div>
                @endif

                <!-- Article Content -->
                <div class="article-content prose prose-lg max-w-none fade-in-delay-1">
                    {!! $article->content !!}
                </div>

                <!-- Article Footer -->
                <div class="mt-12 pt-8 border-t-2 border-black fade-in-delay-2">
                    <!-- Share Buttons -->
                    <div class="mb-8 no-print">
                        <h3 class="text-lg font-bold uppercase tracking-wide text-black mb-4">CHIA SẺ</h3>
                        <div class="flex gap-4">
                            <button class="share-btn" onclick="shareOnFacebook()" title="Chia sẻ lên Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="share-btn" onclick="shareOnTwitter()" title="Chia sẻ lên Twitter">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="share-btn" onclick="copyLink()" title="Sao chép liên kết">
                                <i class="fas fa-link"></i>
                            </button>
                            <button class="share-btn" onclick="printArticle()" title="In bài viết">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex flex-col sm:flex-row gap-4 no-print">
                        <a href="{{ route('news.index') }}" 
                           class="nav-btn inline-flex items-center justify-center px-6 py-3 text-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Quay lại danh sách
                        </a>
                        
                        @if($related->isNotEmpty())
                        <a href="#related-articles" 
                           class="nav-btn inline-flex items-center justify-center px-6 py-3 text-sm">
                            Bài viết liên quan
                            <i class="fas fa-arrow-down ml-2"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </article>

            <!-- Sidebar -->
            <aside class="lg:w-1/3">
                <!-- Table of Contents (nếu content dài) -->
                <div class="toc mb-8 no-print fade-in-delay-3">
                    <h3 class="text-lg font-bold uppercase tracking-wide text-black mb-4">MỤC LỤC</h3>
                    <div id="tocContent">
                        <!-- TOC sẽ được generate bằng JavaScript -->
                    </div>
                </div>

                <!-- Latest News -->
                <div class="bg-white border-2 border-black p-6 mb-8 fade-in-delay-4">
                    <h3 class="text-lg font-bold uppercase tracking-wide text-black mb-6">TIN MỚI NHẤT</h3>
                    
                    @php
                        $latestNews = App\Models\NewsArticle::where('id', '!=', $article->id)
                            ->orderByDesc('created_at')
                            ->limit(3)
                            ->get();
                    @endphp
                    
                    @foreach($latestNews as $latest)
                    <div class="mb-6 last:mb-0">
                        <div class="flex gap-4">
                            @if($latest->thumbnail)
                            <img src="{{ $latest->thumbnail }}" 
                                 alt="{{ $latest->title }}" 
                                 class="w-16 h-16 object-cover border border-gray-300">
                            @endif
                            <div class="flex-1">
                                <h4 class="font-bold text-sm uppercase leading-tight mb-2">
                                    <a href="{{ route('news.show', $latest->id) }}" 
                                       class="hover:text-gray-600 transition-colors">
                                        {{ Str::limit($latest->title, 60) }}
                                    </a>
                                </h4>
                                <div class="text-xs text-gray-600 uppercase tracking-wide">
                                    {{ $latest->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>

    <!-- Related Articles -->
    @if($related->isNotEmpty())
    <section id="related-articles" class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12 fade-in">
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="w-8 h-0.5 bg-black"></div>
                    <span class="meta-info text-xs uppercase tracking-[0.2em]">
                        BÀI VIẾT LIÊN QUAN
                    </span>
                    <div class="w-8 h-0.5 bg-black"></div>
                </div>
                
                <h2 class="text-3xl md:text-4xl font-black uppercase text-black tracking-tight">
                    ĐỌC THÊM
                </h2>
            </div>

            <!-- Related Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($related as $index => $item)
                <article class="related-card group fade-in fade-in-delay-{{ ($index % 4) + 1 }}">
                    <!-- Image -->
                    <div class="relative overflow-hidden">
                        <a href="{{ route('news.show', $item->id) }}" class="block">
                            <img src="{{ $item->thumbnail ?? '/images/news-default.jpg' }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                        </a>
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="meta-info">{{ $item->category ?? 'TIN TỨC' }}</span>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 mb-3">
                            <div class="flex items-center text-gray-600 text-xs">
                                <div class="w-1 h-1 bg-black mr-2"></div>
                                {{ $item->created_at->format('d M Y') }}
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="mb-3">
                            <a href="{{ route('news.show', $item->id) }}" 
                               class="font-bold text-sm uppercase leading-tight hover:text-gray-600 transition-colors">
                                {{ Str::limit($item->title, 80) }}
                            </a>
                        </h3>
                        
                        <!-- Summary -->
                        @if($item->summary)
                        <p class="text-xs text-gray-600 leading-relaxed">
                            {{ Str::limit($item->summary, 100) }}
                        </p>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reading progress bar
    const progressBar = document.querySelector('.reading-progress');
    
    function updateProgress() {
        const scrollTop = window.pageYOffset;
        const docHeight = document.body.scrollHeight - window.innerHeight;
        const progress = scrollTop / docHeight;
        progressBar.style.width = progress * 100 + '%';
    }
    
    window.addEventListener('scroll', updateProgress);
    
    // Back to top button
    const backToTopButton = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });
    
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Parallax effect for background elements
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        parallaxElements.forEach(function(element) {
            element.style.transform = `translateY(${rate}px)`;
        });
    });
    
    // Generate Table of Contents
    const tocContent = document.getElementById('tocContent');
    const headings = document.querySelectorAll('.article-content h1, .article-content h2, .article-content h3');
    
    if (headings.length > 0) {
        const tocList = document.createElement('ul');
        
        headings.forEach(function(heading, index) {
            // Add ID to heading for anchor links
            const headingId = 'heading-' + index;
            heading.id = headingId;
            
            // Create TOC item
            const listItem = document.createElement('li');
            const link = document.createElement('a');
            link.href = '#' + headingId;
            link.textContent = heading.textContent;
            link.className = 'toc-link';
            
            // Add different styles for different heading levels
            if (heading.tagName === 'H1') {
                link.style.fontWeight = 'bold';
            } else if (heading.tagName === 'H3') {
                link.style.paddingLeft = '1rem';
                link.style.fontSize = '0.8rem';
            }
            
            listItem.appendChild(link);
            tocList.appendChild(listItem);
        });
        
        tocContent.appendChild(tocList);
    } else {
        tocContent.innerHTML = '<p class="text-sm text-gray-600">Không có mục lục</p>';
    }
    
    // Smooth scroll for TOC links
    document.querySelectorAll('.toc-link').forEach(anchor => {
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
    
    // Loading overlay (simulate loading)
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Show loading briefly on page load
    loadingOverlay.classList.add('active');
    setTimeout(() => {
        loadingOverlay.classList.remove('active');
    }, 800);
    
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
    
    // Enhanced image loading with fade-in
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        if (img.complete) {
            img.style.opacity = '1';
        } else {
            img.style.opacity = '0';
            img.addEventListener('load', function() {
                this.style.transition = 'opacity 0.3s ease';
                this.style.opacity = '1';
            });
        }
    });
    
    // Page transition loading
    const links = document.querySelectorAll('a[href^="/"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.target || this.target === '_self') {
                loadingOverlay.classList.add('active');
            }
        });
    });
    
    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(e) {
        // ESC key to close any overlays
        if (e.key === 'Escape') {
            loadingOverlay.classList.remove('active');
        }
        
        // Space or Enter to scroll down
        if (e.key === ' ' || e.key === 'Enter') {
            if (e.target === document.body) {
                e.preventDefault();
                window.scrollBy(0, window.innerHeight * 0.8);
            }
        }
    });
});

// Share functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // Show success message
        const btn = event.target.closest('.share-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = '#28a745';
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

function printArticle() {
    window.print();
}
</script>
@endpush
