<!-- Tmart-style Chatbot Widget -->
<div id="chatbot-widget" class="fixed bottom-24 right-5 z-50 font-sans" role="region" aria-label="BookBee Chatbot Assistant">
    <!-- Floating Action Button - Tmart Style -->
    <button id="chatbot-toggle" 
            class="group relative bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white w-16 h-16 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-teal-300/50"
            aria-label="Mở trợ lý BookBee"
            title="Trợ lý BookBee">
        
        <!-- Robot Avatar -->
        {{-- <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-10 h-10 bg-teal-400 rounded-full flex items-center justify-center">
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                    <div class="w-4 h-4 bg-teal-500 rounded-full flex items-center justify-center">
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                </div>
            </div>
        </div> --}}
        
        <!-- Notification Badge -->
        {{-- <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
            <span class="text-white text-xs font-bold">1</span>
        </div> --}}
        <img style=" border-radius: 50%; object-fit: cover;" src="{{ asset('storage/avtchatbot.jpg') }}" alt="">
    </button>

    <!-- Chat Window - Style -->
    <div id="chatbot-window" 
         class="hidden fixed bottom-44 right-5 rounded-2xl shadow-2xl border border-gray-200 overflow-hidden bg-white transform transition-all duration-300 scale-95 opacity-0"
         style="display: none; width: 350px; height: 500px;"
         role="dialog" 
         aria-labelledby="chatbot-title" 
         aria-modal="true">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-teal-500 to-teal-600 text-white p-4 rounded-t-2xl" 
             style="background: linear-gradient(to right, #14b8a6, #0891b2);">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- Bot Avatar -->
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            <img style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" src="{{ asset('images/avtchatbot.jpg') }}" alt="">
                        </div>
                    </div>
                    
                    <div>
                        <h3 id="chatbot-title" class="font-semibold text-base">BookBee.vn</h3>
                    </div>
                </div>
                
                <!-- Control Buttons -->
                <div class="flex items-center space-x-2">
                    {{-- <button class="w-8 h-8 rounded-full hover:bg-white/20 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button> --}}
                    <button id="chatbot-close" 
                            class="w-8 h-8 rounded-full hover:bg-white/20 flex items-center justify-center transition-colors"
                            aria-label="Đóng chatbot">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" 
             class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
             role="log" 
             aria-label="Lịch sử trò chuyện"
             aria-live="polite">
            
            <!-- Welcome Message -->
            <div class="flex items-start space-x-2">
                <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    <img style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" src="{{ asset('images/avtchatbot.jpg') }}" alt="">
                </div>
                <div class="bg-white rounded-lg rounded-tl-none p-3 shadow-sm max-w-xs">
                    <p class="text-gray-800 text-sm">
                        Xin chào, em là trợ lý ảo của BookBee.VN 👋
                    </p>
                </div>
            </div>
            
            <div class="flex items-start space-x-2">
                <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    <img style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" src="{{ asset('images/avtchatbot.jpg') }}" alt="">
                </div>
                <div class="bg-white rounded-lg rounded-tl-none p-3 shadow-sm max-w-xs">
                    <p class="text-gray-800 text-sm">
                        Em rất sẵn lòng hỗ trợ anh/chị 😊
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="px-4 py-2 bg-white border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <button class="quick-action bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-full px-3 py-1 text-xs font-medium transition-colors" data-message="Sách văn học">
                    Sách văn học
                </button>
                <button class="quick-action bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-full px-3 py-1 text-xs font-medium transition-colors" data-message="Sách kinh tế">
                    Sách kinh tế
                </button>
                <button class="quick-action bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-full px-3 py-1 text-xs font-medium transition-colors" data-message="Sách kỹ năng sống">
                    Kỹ năng sống
                </button>
                <button class="quick-action bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-full px-3 py-1 text-xs font-medium transition-colors" data-message="Sách thiếu nhi">
                    Thiếu nhi
                </button>
                <button class="quick-action bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-full px-3 py-1 text-xs font-medium transition-colors" data-message="Sách bán chạy">
                    Bán chạy nhất
                </button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100 rounded-b-2xl">
            <div class="flex items-center space-x-2">
                {{-- <button class="w-8 h-8 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                </button> --}}
                
                <div class="flex-1 relative">
                    <input type="text" 
                           id="chatbot-input" 
                           placeholder="Nhập tin nhắn..." 
                           class="w-full bg-gray-100 border-0 rounded-full px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all placeholder-gray-500"
                           maxlength="500"
                           autocomplete="off">
                </div>
                
                <button id="chatbot-send" 
                        class="w-8 h-8 bg-teal-500 hover:bg-teal-600 text-white rounded-full flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        aria-label="Gửi tin nhắn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Typing Indicator -->
            <div id="typing-indicator" class="hidden mt-3">
                <div class="flex items-center space-x-2 text-gray-500 text-xs">
                    <div class="flex space-x-1">
                        <div class="w-1 h-1 bg-teal-400 rounded-full animate-bounce"></div>
                        <div class="w-1 h-1 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-1 h-1 bg-teal-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                    <span>BookBee đang nhập...</span>
                </div>
            </div>
            
            <!-- Footer Info -->
            {{-- <div class="mt-2 text-center text-xs text-gray-400">
                Thông tin tham vấn bởi Trí tuệ nhân tạo, vui lòng liên hệ hotline 0899396446
            </div>
            <div class="text-center text-xs text-gray-400 mt-1">
                Powered by 
                <span class="text-teal-500 font-medium">🧠 Easy AI Chat</span>
            </div> --}}
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="chatbot-overlay" class="hidden fixed inset-0 bg-black/50 z-40 backdrop-blur-sm"></div>
</div>

<!-- Custom Styles - Tmart Style -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    #chatbot-widget {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    /* Chat Window Styles */
    #chatbot-window {
        display: none;
        flex-direction: column;
        z-index: 1000;
    }
    
    #chatbot-window.show {
        display: flex !important;
        opacity: 1;
        transform: scale(1);
    }
    
    /* Ensure proper z-index layering */
    #chatbot-overlay {
        z-index: 998;
    }
    
    #chatbot-widget {
        z-index: 999;
    }
    
    #chatbot-window {
        z-index: 1000;
    }
    
    /* Prevent body scroll when mobile chatbot is open */
    body.chatbot-mobile-open {
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
    }
    
    /* Smooth transitions */
    #chatbot-window {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #chatbot-toggle {
        transition: all 0.2s ease;
    }
    
    #chatbot-toggle:active {
        transform: scale(0.95);
    }
    
    /* Scrollbar Styles */
    #chatbot-messages {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db #f9fafb;
    }
    
    #chatbot-messages::-webkit-scrollbar {
        width: 4px;
    }
    
    #chatbot-messages::-webkit-scrollbar-track {
        background: #f9fafb;
    }
    
    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }
    
    #chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Message Bubbles - Tmart Style */
    .message-user {
        background: linear-gradient(135deg, #14b8a6, #0891b2);
        color: white;
        margin-left: auto;
        border-radius: 16px 16px 4px 16px;
        box-shadow: 0 2px 8px rgba(20, 184, 166, 0.3);
        animation: slideInRight 0.3s ease-out;
    }
    
    .message-bot {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px 16px 16px 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        animation: slideInLeft 0.3s ease-out;
    }
    
    /* Animations */
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
    }
    
    /* Animation Classes with Better Performance */
    .animate-slide-up {
        animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    
    .animate-slide-down {
        animation: slideDown 0.2s ease-in forwards;
    }
    
    /* Prevent Animation Conflicts */
    #chatbot-window.show {
        animation-fill-mode: forwards !important;
    }
    
    #chatbot-window.hidden {
        animation-fill-mode: forwards !important;
    }
    
    /* Quick Action Buttons */
    .quick-action {
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .quick-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.15);
    }
    
    /* Mobile Responsive */
    @media (max-width: 640px) {
        #chatbot-widget {
            right: 16px !important;
            bottom: 20px !important;
        }
        
        #chatbot-toggle {
            width: 56px !important;
            height: 56px !important;
        }
        
        #chatbot-window {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            border-radius: 0 !important;
            margin: 0 !important;
            z-index: 9999 !important;
        }
        
        #chatbot-overlay {
            z-index: 9998 !important;
        }
    }
    
    /* Tablet Responsive */
    @media (min-width: 641px) and (max-width: 1024px) {
        #chatbot-window {
            width: 380px !important;
            height: 600px !important;
            bottom: 140px !important;
            right: 20px !important;
        }
    }
    
    /* Notification Badge */
    .notification-badge {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    /* Product Card Styles */
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }
    
    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    
    .product-title {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        line-height: 1.3;
        margin-bottom: 8px;
    }
    
    .product-price {
        font-size: 14px;
        font-weight: 600;
        color: #dc2626;
    }
</style>

<!-- JavaScript Logic -->
<script>
class ChatbotWidget {
    constructor() {
        this.isOpen = false;
        this.isTyping = false;
        this.messages = [];
        this.apiUrl = '/api/chatbot/message';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Cleanup any existing instances
        this.cleanup();
        
        if (!this.initializeElements()) {
            console.error('Failed to initialize chatbot elements');
            return;
        }
        
        this.attachEventListeners();
        this.setupMobileHandler();
        this.setupCharacterCounter();
        
        console.log('BookBee ChatBot initialized successfully! 🤖');
    }
    
    cleanup() {
        // Reset any existing states
        const existingWindow = document.getElementById('chatbot-window');
        if (existingWindow) {
            existingWindow.style.display = 'none';
            existingWindow.classList.add('hidden');
            existingWindow.classList.remove('show', 'animate-slide-up', 'animate-slide-down');
        }
        
        const existingOverlay = document.getElementById('chatbot-overlay');
        if (existingOverlay) {
            existingOverlay.classList.add('hidden');
        }
        
        // Reset body overflow and classes
        document.body.classList.remove('chatbot-mobile-open');
        document.body.style.overflow = '';
    }
    
    initializeElements() {
        this.widget = document.getElementById('chatbot-widget');
        this.toggleBtn = document.getElementById('chatbot-toggle');
        this.closeBtn = document.getElementById('chatbot-close');
        this.window = document.getElementById('chatbot-window');
        this.overlay = document.getElementById('chatbot-overlay');
        this.messagesContainer = document.getElementById('chatbot-messages');
        this.input = document.getElementById('chatbot-input');
        this.sendBtn = document.getElementById('chatbot-send');
        this.typingIndicator = document.getElementById('typing-indicator');
        
        if (!this.widget) {
            console.error('Chatbot widget not found!');
            return false;
        }
        
        if (!this.window) {
            console.error('Chatbot window not found!');
            return false;
        }
        
        return true;
    }
    
    attachEventListeners() {
        // Toggle chatbot - ensure only one listener
        if (this.toggleBtn) {
            this.toggleBtn.removeEventListener('click', this.handleToggle);
            this.handleToggle = () => {
                if (this.isOpen) {
                    this.closeChat();
                } else {
                    this.openChat();
                }
            };
            this.toggleBtn.addEventListener('click', this.handleToggle);
        }
        
        // Close chatbot - ensure only one listener
        if (this.closeBtn) {
            this.closeBtn.removeEventListener('click', this.handleClose);
            this.handleClose = () => this.closeChat();
            this.closeBtn.addEventListener('click', this.handleClose);
        }
        
        // Overlay click to close
        if (this.overlay) {
            this.overlay.removeEventListener('click', this.handleOverlayClick);
            this.handleOverlayClick = () => this.closeChat();
            this.overlay.addEventListener('click', this.handleOverlayClick);
        }
        
        // Send message
        if (this.sendBtn) {
            this.sendBtn.removeEventListener('click', this.handleSend);
            this.handleSend = () => this.sendMessage();
            this.sendBtn.addEventListener('click', this.handleSend);
        }
        
        if (this.input) {
            this.input.removeEventListener('keypress', this.handleKeypress);
            this.handleKeypress = (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            };
            this.input.addEventListener('keypress', this.handleKeypress);
        }
        
        // Quick suggestions and actions
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-suggestion') || e.target.classList.contains('quick-action')) {
                const message = e.target.getAttribute('data-message');
                if (message) {
                    this.input.value = message;
                    this.sendMessage();
                }
            }
        });
        
        // ESC key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeChat();
            }
        });
    }
    
    setupMobileHandler() {
        if (window.innerWidth <= 640) {
            document.body.style.setProperty('--chatbot-mobile', 'true');
        }
        
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 640) {
                document.body.style.setProperty('--chatbot-mobile', 'true');
            } else {
                document.body.style.removeProperty('--chatbot-mobile');
            }
        });
    }
    
    setupCharacterCounter() {
        // Character counter not available in this design
        if (this.input) {
            this.input.addEventListener('input', () => {
                const length = this.input.value.length;
                const maxLength = this.input.getAttribute('maxlength') || 500;
                
                // Optional: Show warning for long messages
                if (length > maxLength * 0.9) {
                    this.input.style.borderColor = '#ef4444';
                } else {
                    this.input.style.borderColor = '';
                }
            });
        }
    }
    
    openChat() {
        if (this.isOpen) return; // Prevent multiple calls
        
        this.isOpen = true;
        this.window.style.display = 'flex';
        this.window.classList.remove('hidden', 'scale-95', 'opacity-0');
        this.window.classList.add('show', 'animate-slide-up');
        
        if (window.innerWidth <= 640) {
            this.overlay?.classList.remove('hidden');
            document.body.classList.add('chatbot-mobile-open');
            document.body.style.overflow = 'hidden';
        }
        
        // Focus on input after animation
        setTimeout(() => {
            this.input?.focus();
        }, 100);
        
        this.scrollToBottom();
        this.trackEvent('chatbot_opened');
    }
    
    closeChat() {
        if (!this.isOpen) return; // Prevent multiple calls
        
        this.isOpen = false;
        this.window.classList.add('animate-slide-down');
        this.window.classList.remove('animate-slide-up', 'show');
        
        // Remove mobile overlay immediately
        this.overlay?.classList.add('hidden');
        document.body.classList.remove('chatbot-mobile-open');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            this.window.style.display = 'none';
            this.window.classList.remove('animate-slide-down');
            this.window.classList.add('hidden', 'scale-95', 'opacity-0');
        }, 200);
        
        this.trackEvent('chatbot_closed');
    }
    
    async sendMessage() {
        const message = this.input.value.trim();
        if (!message) return;
        
        // Disable input
        this.input.disabled = true;
        this.sendBtn.disabled = true;
        this.input.value = '';
        
        // Add user message
        this.addMessage(message, 'user');
        
        // Show typing indicator
        this.showTyping();
        
        try {
            const response = await this.callAPI(message);
            this.hideTyping();
            
            // Handle response from new Gemini-powered API
            console.log(response);
            if (response.success && response.data) {
                const botResponse = response.data;
                
                // Handle different message types
                if (botResponse.type === 'greeting') {
                    this.addMessage(botResponse.content, 'bot');
                    if (botResponse.quick_replies) {
                        this.addQuickReplies(botResponse.quick_replies);
                    }
                } else if (botResponse.type === 'categories') {
                    this.addMessage(botResponse.content, 'bot');
                    if (botResponse.categories) {
                        this.addQuickReplies(botResponse.categories);
                    }
                } else if (botResponse.type === 'product_list' && botResponse.products) {
                    this.addMessage(botResponse.content, 'bot', botResponse.products);
                } else if (botResponse.type === 'text') {
                    this.addMessage(botResponse.content, 'bot');
                    if (botResponse.quick_replies) {
                        this.addQuickReplies(botResponse.quick_replies);
                    }
                } else {
                    // Fallback for plain text responses
                    this.addMessage(botResponse.content || botResponse, 'bot');
                }
            } else {
                this.addMessage(response.message || 'Xin lỗi, tôi không hiểu câu hỏi của bạn.', 'bot');
            }
        } catch (error) {
            this.hideTyping();
            this.addMessage('Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại sau.', 'bot');
            console.error('Chatbot API Error:', error);
        } finally {
            // Re-enable input
            this.input.disabled = false;
            this.sendBtn.disabled = false;
            this.input.focus();
        }
        
        this.trackEvent('message_sent', { message_length: message.length });
    }
    
    addMessage(text, sender, products = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex items-start space-x-2 ${sender === 'user' ? 'flex-row-reverse space-x-reverse' : ''}`;
        
        const avatar = sender === 'user' ? 
            `<div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">
                U
            </div>` :
            `<div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">
                <img style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" src="{{ asset('images/avtchatbot.jpg') }}" alt="">
            </div>`;
            
        const messageClass = sender === 'user' ? 'message-user' : 'message-bot';
        const textColor = sender === 'user' ? 'text-white' : 'text-gray-800';
        
        let messageContent = `
            ${avatar}
            <div class="${messageClass} p-3 max-w-xs shadow-sm">
                <p class="${textColor} text-sm leading-relaxed">${this.formatMessage(text)}</p>
            </div>
        `;
        
        // Add products if provided
        if (products && products.length > 0) {
            messageContent = `
                ${avatar}
                <div class="max-w-xs">
                    <div class="${messageClass} p-3 shadow-sm mb-3">
                        <p class="${textColor} text-sm leading-relaxed">${this.formatMessage(text)}</p>
                    </div>
                    ${this.renderProducts(products)}
                </div>
            `;
        }
        
        messageDiv.innerHTML = messageContent;
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Store message
        this.messages.push({ text, sender, products, timestamp: new Date() });
    }
    
    renderProducts(products) {
        const productsHtml = products.map(product => `
            <div class="product-card mb-2 cursor-pointer" onclick="window.open('${product.url || '#'}', '_blank')">
                <img src="${product.image}" alt="${product.title}" class="product-image">
                <div class="p-3">
                    <h4 class="product-title">${product.title}</h4>
                    <p class="text-xs text-gray-600 mb-2">${product.author}</p>
                    <div class="flex items-center justify-between">
                        <p class="product-price">${this.formatPrice(product.discount_price || product.price)}</p>
                        ${product.rating > 0 ? `<div class="flex items-center text-xs text-yellow-500">
                            <span>⭐</span>
                            <span class="ml-1">${product.rating}</span>
                        </div>` : ''}
                    </div>
                </div>
            </div>
        `).join('');
        
        return `<div class="grid grid-cols-2 gap-2">${productsHtml}</div>`;
    }
    
    formatPrice(price) {
        if (typeof price === 'number') {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(price).replace('₫', 'đ');
        }
        return price;
    }
    
    addQuickReplies(replies) {
        const quickRepliesDiv = document.createElement('div');
        quickRepliesDiv.className = 'flex flex-wrap gap-2 mt-3 px-2';
        
        replies.forEach(reply => {
            const button = document.createElement('button');
            button.className = 'quick-suggestion bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-full px-3 py-1 text-xs font-medium transition-colors';
            button.setAttribute('data-message', reply);
            button.textContent = reply;
            quickRepliesDiv.appendChild(button);
        });
        
        this.messagesContainer.appendChild(quickRepliesDiv);
        this.scrollToBottom();
    }
    
    formatMessage(text) {
        // Convert URLs to links
        text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="underline hover:text-blue-600">$1</a>');
        
        // Convert newlines to br
        text = text.replace(/\n/g, '<br>');
        
        // Add emoji support
        text = text.replace(/:\)/g, '😊').replace(/:\(/g, '😢').replace(/:D/g, '😄');
        
        return text;
    }
    
    showTyping() {
        this.isTyping = true;
        this.typingIndicator.classList.remove('hidden');
        this.scrollToBottom();
    }
    
    hideTyping() {
        this.isTyping = false;
        this.typingIndicator.classList.add('hidden');
    }
    
    scrollToBottom() {
        setTimeout(() => {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }, 100);
    }
    
    async callAPI(message) {
        const response = await fetch(this.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                message: message,
                context: this.getContext()
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        // Return the full response to handle products
        return data;
    }
    
    getContext() {
        return {
            url: window.location.href,
            page: window.location.pathname,
            timestamp: new Date().toISOString(),
            user_agent: navigator.userAgent,
            messages_count: this.messages.length
        };
    }
    
    handleQuickAction(action) {
        const actions = {
            'bestsellers': 'Cho tôi xem những cuốn sách bán chạy nhất',
            'categories': 'Hiển thị tất cả danh mục sách',
            'deals': 'Có những deal sách nào hot hiện tại?',
            'new': 'Sách mới phát hành gần đây'
        };
        
        if (actions[action]) {
            this.input.value = actions[action];
            this.sendMessage();
        }
    }
    
    trackEvent(eventName, properties = {}) {
        // Google Analytics or other tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, {
                event_category: 'chatbot',
                ...properties
            });
        }
        
        console.log(`📊 Chatbot Event: ${eventName}`, properties);
    }
    
    // Public methods
    open() {
        this.openChat();
    }
    
    close() {
        this.closeChat();
    }
    
    toggle() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }
    
    sendQuickMessage(message) {
        if (this.input) {
            this.input.value = message;
            this.sendMessage();
        }
    }
    
    // Destroy method for cleanup
    destroy() {
        this.cleanup();
        if (this.toggleBtn && this.handleToggle) {
            this.toggleBtn.removeEventListener('click', this.handleToggle);
        }
        if (this.closeBtn && this.handleClose) {
            this.closeBtn.removeEventListener('click', this.handleClose);
        }
        if (this.overlay && this.handleOverlayClick) {
            this.overlay.removeEventListener('click', this.handleOverlayClick);
        }
        if (this.sendBtn && this.handleSend) {
            this.sendBtn.removeEventListener('click', this.handleSend);
        }
        if (this.input && this.handleKeypress) {
            this.input.removeEventListener('keypress', this.handleKeypress);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Destroy existing instance if any
    if (window.bookbeeChatbot) {
        window.bookbeeChatbot.destroy();
    }
    
    // Create new instance
    window.bookbeeChatbot = new ChatbotWidget();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.bookbeeChatbot) {
        window.bookbeeChatbot.destroy();
    }
});

// Global API for external access
window.BookBeeChat = {
    open: () => window.bookbeeChatbot?.open(),
    close: () => window.bookbeeChatbot?.close(),
    toggle: () => window.bookbeeChatbot?.toggle(),
    send: (message) => window.bookbeeChatbot?.sendQuickMessage(message)
};
</script>