{{-- AI Summary Component - Adidas Style --}}
@php
    $isCombo = isset($combo);
    $item = $isCombo ? $combo : $book;
    $itemType = $isCombo ? 'combo' : 'book';
    $dataAttribute = $isCombo ? 'data-combo-id' : 'data-book-id';
@endphp

<div id="{{ $isCombo ? 'ai-combo-summary-container' : 'ai-summary-container' }}" {{ $dataAttribute }}="{{ $item->id }}" class="ai-summary-section">
    {{-- Content will be loaded by JavaScript --}}
    <div class="text-center">
        <div class="bg-white border-2 border-gray-100 relative overflow-hidden group">
            <!-- Header Badge -->
            <div class="bg-amber-600 text-white px-6 py-3 text-center">
                <span class="text-sm font-bold uppercase tracking-wider">
                    {{ $isCombo ? 'AI SUMMARY - COMBO' : 'AI SUMMARY' }}
                </span>
            </div>
            
            <!-- Loading Content -->
            <div class="p-12">
                <div class="w-20 h-20 bg-amber-600 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-robot text-3xl text-white animate-pulse"></i>
                </div>
                <div class="space-y-4">
                    <div class="h-6 bg-gray-200 w-1/2 mx-auto"></div>
                    <div class="h-4 bg-gray-100 w-3/4 mx-auto"></div>
                    <div class="h-4 bg-gray-100 w-2/3 mx-auto"></div>
                </div>
                <div class="mt-8">
                    <div class="h-12 bg-amber-600 w-48 mx-auto flex items-center justify-center">
                        <span class="text-white text-sm font-bold uppercase tracking-wider">Đang tải...</span>
                    </div>
                </div>
            </div>
            
            <!-- Geometric decorations -->
            <div class="absolute top-0 right-0 w-16 h-16 bg-amber-600 opacity-5 transform rotate-45 translate-x-8 -translate-y-8"></div>
            <div class="absolute bottom-0 left-0 w-24 h-1 bg-amber-600 opacity-10"></div>
        </div>
    </div>
</div>

@push('scripts')
    @if(isset($combo))
        <script src="{{ asset('js/combo-ai-summary.js') }}"></script>
    @else
        <script src="{{ asset('js/ai-summary-adidas.js') }}"></script>
    @endif
@endpush

<style>
/* Adidas-inspired AI Summary Styles */
.ai-summary-section {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* Main Container Styles */
.ai-summary-container {
    background: #fff;
    border: 2px solid #d97706;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.ai-summary-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.15);
}

.ai-summary-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(217, 119, 6, 0.2), transparent);
    transition: left 0.5s;
}

.ai-summary-container:hover::before {
    left: 100%;
}

/* Header Styles */
.ai-header {
    background: #d97706;
    color: #fff;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #d97706;
}

.ai-header-title {
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0;
}

.ai-header-badge {
    background: #fff;
    color: #d97706;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Content Styles */
.ai-content {
    padding: 2rem;
    background: #fff;
}

.ai-section {
    margin-bottom: 2rem;
    border-left: 4px solid #d97706;
    padding-left: 1.5rem;
}

.ai-section:last-child {
    margin-bottom: 0;
}

.ai-section-title {
    font-size: 1.125rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #d97706;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.ai-section-title:hover {
    color: #b45309;
}

.ai-section-title i {
    margin-right: 0.75rem;
    transition: transform 0.3s ease;
}

.ai-section-title .fa-chevron-down {
    margin-left: auto;
    margin-right: 0;
    font-size: 0.875rem;
}

.ai-section-content {
    color: #333;
    line-height: 1.7;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.ai-section-content.collapsed {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    padding-top: 0;
    padding-bottom: 0;
    margin-top: 0;
}

.ai-section-content.expanded {
    max-height: 1000px;
    opacity: 1;
    padding-top: 1rem;
    padding-bottom: 0;
    margin-top: 1rem;
}

/* Button Styles */
.ai-btn {
    background: #d97706;
    color: #fff;
    border: 2px solid #d97706;
    padding: 0.75rem 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.ai-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.ai-btn:hover {
    background: #b45309;
    border-color: #b45309;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.3);
    color: #fff;
    text-decoration: none;
}

.ai-btn:hover::before {
    left: 100%;
}

.ai-btn:active {
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.2);
}

.ai-btn-outline {
    background: #fff;
    color: #d97706;
    border: 2px solid #d97706;
}

.ai-btn-outline:hover {
    background: #d97706;
    color: #fff;
}

/* Primary Button Style */
.adidas-btn-primary {
    background: #d97706;
    color: #fff;
    border: 2px solid #d97706;
    padding: 1rem 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.adidas-btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.adidas-btn-primary:hover {
    background: #b45309;
    border-color: #b45309;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(217, 119, 6, 0.3);
    color: #fff;
    text-decoration: none;
}

.adidas-btn-primary:hover::before {
    left: 100%;
}

.adidas-btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(217, 119, 6, 0.2);
}

/* Chat Styles */
.ai-chat-container {
    border-top: 2px solid #d97706;
    margin-top: 2rem;
    padding-top: 2rem;
}

.ai-chat-messages {
    max-height: 400px;
    overflow-y: auto;
    padding: 1rem;
    background: #f9f9f9;
    border: 1px solid #ddd;
    margin-bottom: 1rem;
}

.ai-chat-message {
    margin-bottom: 1rem;
    padding: 0.75rem 1rem;
    border-left: 3px solid #d97706;
}

.ai-chat-message.user {
    background: #d97706;
    color: #fff;
    border-left-color: #fff;
    text-align: right;
}

.ai-chat-message.ai {
    background: #f0f0f0;
    color: #333;
    border-left-color: #d97706;
    text-align: left;
}

.ai-chat-input-group {
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
}

.ai-chat-input {
    flex: 1;
    border: 2px solid #d97706;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    resize: none;
    min-height: 48px;
    max-height: 120px;
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.ai-chat-input:focus {
    outline: none;
    border-color: #b45309;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
}

.ai-chat-send {
    padding: 0.75rem 1.5rem;
    min-width: 120px;
    font-size: 0.875rem;
    white-space: nowrap;
}

/* Combo AI Summary Specific Styles */
#ai-combo-summary-container {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.combo-ai-section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border: 2px solid #d97706;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 1px;
    color: #d97706;
}

.combo-ai-section-title:hover {
    background: #d97706;
    color: #fff;
}

.combo-ai-section-title i:first-child {
    margin-right: 0.75rem;
}

.combo-ai-section-title i:last-child {
    transition: transform 0.3s ease;
}

.combo-ai-chat-container {
    border-top: 2px solid #d97706;
    margin-top: 2rem;
    padding-top: 2rem;
}

.combo-ai-chat-input {
    flex: 1;
    border: 2px solid #d97706;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    resize: none;
    min-height: 48px;
    max-height: 120px;
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.combo-ai-chat-input:focus {
    outline: none;
    border-color: #b45309;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
}

.combo-char-count {
    font-size: 0.75rem;
    color: #666;
}

.send-combo-chat-btn {
    padding: 0.75rem 1.5rem;
    min-width: 120px;
    font-size: 0.875rem;
    white-space: nowrap;
}

/* Loading States */
.ai-loading {
    text-align: center;
    padding: 3rem 2rem;
}

.ai-loading-icon {
    width: 80px;
    height: 80px;
    background: #d97706;
    color: #fff;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.ai-loading-text {
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #d97706;
    margin-bottom: 0.5rem;
}

.ai-loading-dots {
    display: flex;
    justify-content: center;
    gap: 0.25rem;
}

.ai-loading-dot {
    width: 0.5rem;
    height: 0.5rem;
    background: #d97706;
    animation: ai-loading-bounce 1.4s ease-in-out infinite both;
}

.ai-loading-dot:nth-child(1) { animation-delay: -0.32s; }
.ai-loading-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes ai-loading-bounce {
    0%, 80%, 100% {
        transform: scale(0);
    }
    40% {
        transform: scale(1);
    }
}

/* Error States */
.ai-error {
    text-align: center;
    padding: 3rem 2rem;
}

.ai-error-icon {
    width: 80px;
    height: 80px;
    background: #dc2626;
    color: #fff;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.ai-error-text {
    font-size: 1.125rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #dc2626;
    margin-bottom: 1rem;
}

.ai-error-message {
    color: #666;
    margin-bottom: 1.5rem;
}

/* Initial State */
.ai-initial {
    text-align: center;
    padding: 3rem 2rem;
}

.ai-initial-icon {
    width: 80px;
    height: 80px;
    background: #d97706;
    color: #fff;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.ai-initial-title {
    font-size: 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #d97706;
    margin-bottom: 1rem;
}

.ai-initial-description {
    color: #666;
    margin-bottom: 2rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
    font-weight: 500;
}

/* Additional Utility Classes */
.geometric-text {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.adidas-font {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* Mobile Optimization */
.mobile-optimized .ai-content {
    padding: 1rem;
}

.mobile-optimized .ai-section {
    padding-left: 0.75rem;
    border-left-width: 2px;
}

.mobile-optimized .ai-section-title {
    font-size: 0.875rem;
}

.mobile-optimized .ai-chat-input-group {
    flex-direction: column;
    gap: 0.75rem;
}

.mobile-optimized .ai-chat-send {
    width: 100%;
}

/* Flex Utilities for older browsers */
.flex {
    display: flex;
}

.flex-wrap {
    flex-wrap: wrap;
}

.gap-3 > * + * {
    margin-left: 0.75rem;
}

.mt-6 {
    margin-top: 1.5rem;
}

.hidden {
    display: none;
}

.text-center {
    text-align: center;
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.mb-4 {
    margin-bottom: 1rem;
}

.space-y-2 > * + * {
    margin-top: 0.5rem;
}

.text-lg {
    font-size: 1.125rem;
}

.text-sm {
    font-size: 0.875rem;
}

.text-xs {
    font-size: 0.75rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

/* Scrollbar Styling */
.ai-summary-section .scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f3f4f6;
}

.ai-summary-section .scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.ai-summary-section .scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.ai-summary-section .scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    transition: background 0.3s ease;
}

.ai-summary-section .scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Animation Classes */
.ai-fade-in {
    animation: ai-fade-in 0.5s ease-out;
}

@keyframes ai-fade-in {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.ai-slide-up {
    animation: ai-slide-up 0.6s ease-out;
}

@keyframes ai-slide-up {
    from { 
        transform: translateY(30px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

/* Geometric Elements */
.ai-geometric-bg::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: #d97706;
    opacity: 0.03;
    transform: rotate(45deg);
    translate: 50px -50px;
}

.ai-geometric-accent {
    position: relative;
}

.ai-geometric-accent::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 2px;
    background: #d97706;
    opacity: 0.2;
}

/* Focus States for Accessibility */
.ai-btn:focus,
.ai-chat-input:focus,
.ai-section-title:focus {
    outline: 2px solid #d97706;
    outline-offset: 2px;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .ai-summary-container,
    .ai-btn,
    .ai-chat-input {
        border-width: 3px;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .ai-summary-section * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Container Responsive */
@media (max-width: 640px) {
    .ai-summary-container {
        margin: 0 -1rem;
    }
    
    .ai-header {
        padding: 1rem;
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .ai-content {
        padding: 1rem;
    }
    
    .ai-section {
        border-left-width: 2px;
        padding-left: 1rem;
    }
    
    .ai-section-title {
        font-size: 1rem;
        flex-wrap: wrap;
    }
    
    .ai-chat-input-group {
        flex-direction: column;
        gap: 1rem;
    }
    
    .ai-chat-send {
        width: 100%;
    }
}

/* Loading States Enhancement */
.ai-loading,
.ai-initial,
.ai-error {
    min-height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Hover Effects */
.ai-summary-container:hover .ai-geometric-accent::after {
    width: 80px;
    opacity: 0.3;
}

/* Focus States Enhancement */
.ai-btn:focus-visible,
.ai-chat-input:focus-visible {
    outline: 2px solid #d97706;
    outline-offset: 2px;
}
</style>
