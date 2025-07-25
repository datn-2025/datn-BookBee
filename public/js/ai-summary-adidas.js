// Adidas-style AI Summary Manager
class AdidasAISummaryManager {
    constructor() {
        this.apiEndpoints = {
            generate: '/ai-summary/generate/',
            get: '/ai-summary/get/',
            regenerate: '/ai-summary/regenerate/',
            status: '/ai-summary/status/',
            chat: '/ai-summary/chat/'
        };
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        const bookId = this.getBookIdFromPage();
        if (bookId) {
            this.loadSummary(bookId);
        }
        this.bindEvents();
        this.initEnhancedFeatures();
    }

    getBookIdFromPage() {
        const bookElement = document.querySelector('[data-book-id]');
        if (bookElement) {
            return bookElement.getAttribute('data-book-id');
        }
        
        const urlMatch = window.location.pathname.match(/\/books\/([^\/]+)/);
        return urlMatch ? urlMatch[1] : null;
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.generate-summary-btn')) {
                e.preventDefault();
                const bookId = e.target.closest('.generate-summary-btn').getAttribute('data-book-id');
                this.generateSummary(bookId);
            }
            
            if (e.target.closest('.regenerate-summary-btn')) {
                e.preventDefault();
                const bookId = e.target.closest('.regenerate-summary-btn').getAttribute('data-book-id');
                this.regenerateSummary(bookId);
            }
            
            if (e.target.closest('.ai-section-title')) {
                e.preventDefault();
                this.toggleSection(e.target.closest('.ai-section-title'));
            }

            if (e.target.closest('.send-chat-btn')) {
                e.preventDefault();
                const bookId = e.target.closest('.send-chat-btn').getAttribute('data-book-id');
                const input = document.querySelector('.ai-chat-input');
                if (input && input.value.trim()) {
                    this.sendChatMessage(bookId, input.value.trim());
                }
            }
        });

        // Enter key for chat input
        document.addEventListener('keydown', (e) => {
            if (e.target.classList.contains('ai-chat-input') && e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const sendBtn = document.querySelector('.send-chat-btn');
                if (sendBtn) {
                    sendBtn.click();
                }
            }
        });

        // Character count for chat input
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('ai-chat-input')) {
                const charCount = document.querySelector('.char-count');
                if (charCount) {
                    charCount.textContent = `${e.target.value.length}/300`;
                }
            }
        });
    }

    async loadSummary(bookId) {
        const container = document.getElementById('ai-summary-container');
        if (!container) return;

        this.showLoading(container);

        try {
            const response = await fetch(`${this.apiEndpoints.get}${bookId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success && result.data) {
                this.displaySummary(result.data);
            } else {
                this.showInitialState(container, bookId);
            }
        } catch (error) {
            console.error('Error loading summary:', error);
            this.showInitialState(container, bookId);
        }
    }

    async generateSummary(bookId) {
        const container = document.getElementById('ai-summary-container');
        const button = document.querySelector(`.generate-summary-btn[data-book-id="${bookId}"]`);
        
        if (!container || !bookId) return;

        this.setButtonLoading(button, true);
        this.showLoading(container, 'Đang tạo tóm tắt AI...');

        try {
            const response = await fetch(`${this.apiEndpoints.generate}${bookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success) {
                this.displaySummary(result.data);
                this.showNotification('Tạo tóm tắt AI thành công!', 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error generating summary:', error);
            this.showError(container, error.message);
            this.showNotification('Lỗi khi tạo tóm tắt AI', 'error');
        } finally {
            if (button) this.setButtonLoading(button, false);
        }
    }

    async regenerateSummary(bookId) {
        const container = document.getElementById('ai-summary-container');
        const button = document.querySelector(`.regenerate-summary-btn[data-book-id="${bookId}"]`);
        
        if (!container || !bookId) return;

        this.setButtonLoading(button, true);
        this.showLoading(container, 'Đang tạo lại tóm tắt AI...');

        try {
            const response = await fetch(`${this.apiEndpoints.regenerate}${bookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success) {
                this.displaySummary(result.data);
                this.showNotification('Tạo lại tóm tắt AI thành công!', 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error regenerating summary:', error);
            this.showError(container, error.message);
            this.showNotification('Lỗi khi tạo lại tóm tắt AI', 'error');
        } finally {
            if (button) this.setButtonLoading(button, false);
        }
    }

    displaySummary(summaryData) {
        const container = document.getElementById('ai-summary-container');
        if (!container) return;

        container.innerHTML = this.buildSummaryHTML(summaryData);
        
        // Animation
        container.style.opacity = '0';
        setTimeout(() => {
            container.style.transition = 'opacity 0.5s ease';
            container.style.opacity = '1';
            
            // Initialize enhanced features after content is loaded
            this.initEnhancedFeatures();
        }, 100);
    }

    buildSummaryHTML(data) {
        return `
            <div class="ai-summary-container ai-fade-in">
                <!-- Header -->
                <div class="ai-header">
                    <h3 class="ai-header-title">
                        <i class="fas fa-robot mr-2"></i>
                        Tóm tắt AI
                    </h3>
                    <span class="ai-header-badge">
                        ${data.ai_model === 'fallback' ? 'DEMO' : 'AI GENERATED'}
                    </span>
                </div>

                <!-- Content -->
                <div class="ai-content">
                    <!-- Summary Section -->
                    <div class="ai-section">
                        <div class="ai-section-title" data-target="summary-content">
                            <i class="fas fa-file-text"></i>
                            TÓM TẮT CHÍNH
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="summary-content" class="ai-section-content expanded">
                            <p>${data.summary || 'Chưa có tóm tắt chi tiết.'}</p>
                        </div>
                    </div>

                    <!-- Detailed Summary Section -->
                    ${data.detailed_summary ? `
                    <div class="ai-section">
                        <div class="ai-section-title" data-target="detailed-content">
                            <i class="fas fa-list-ul"></i>
                            TÓM TẮT CHI TIẾT
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="detailed-content" class="ai-section-content collapsed">
                            <p>${data.detailed_summary}</p>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Key Points Section -->
                    ${data.key_points ? `
                    <div class="ai-section">
                        <div class="ai-section-title" data-target="keypoints-content">
                            <i class="fas fa-key"></i>
                            ĐIỂM CHÍNH
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="keypoints-content" class="ai-section-content collapsed">
                            <div>${this.formatKeyPoints(data.key_points)}</div>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Themes Section -->
                    ${data.themes ? `
                    <div class="ai-section">
                        <div class="ai-section-title" data-target="themes-content">
                            <i class="fas fa-tags"></i>
                            CHỦ ĐỀ
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="themes-content" class="ai-section-content collapsed">
                            <div>${this.formatThemes(data.themes)}</div>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 mt-6">
                        <button class="ai-btn regenerate-summary-btn" data-book-id="${this.getBookIdFromPage()}">
                            <i class="fas fa-redo"></i>
                            TẠO LẠI
                        </button>
                        <button class="ai-btn ai-btn-outline" onclick="document.querySelector('.ai-chat-container').classList.toggle('hidden')">
                            <i class="fas fa-comments"></i>
                            CHAT VỚI AI
                        </button>
                    </div>

                    <!-- Chat Section -->
                    <div class="ai-chat-container hidden">
                        <div class="ai-section-title">
                            <i class="fas fa-robot"></i>
                            CHAT VỚI AI VỀ CUỐN SÁCH
                        </div>
                        
                        <div id="chat-messages" class="ai-chat-messages scrollbar-thin">
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-comments text-2xl text-gray-400"></i>
                                </div>
                                <div class="space-y-2">
                                    <h3 class="text-lg font-bold text-black uppercase tracking-wider">BẮT ĐẦU CUỘC TRÒ CHUYỆN</h3>
                                    <p class="text-gray-600 text-sm uppercase tracking-wide font-medium">Hỏi AI về nội dung cuốn sách này</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ai-chat-input-group">
                            <textarea 
                                class="ai-chat-input" 
                                placeholder="Hỏi AI về cuốn sách này..."
                                maxlength="300"
                                rows="2"
                            ></textarea>
                            <button class="ai-btn ai-chat-send send-chat-btn" data-book-id="${this.getBookIdFromPage()}">
                                <i class="fas fa-paper-plane"></i>
                                GỬI
                            </button>
                        </div>
                        
                        <div class="text-xs text-gray-500 mt-2 text-center">
                            <span class="char-count">0/300</span> ký tự
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async sendChatMessage(bookId, message) {
        const chatMessages = document.getElementById('chat-messages');
        const input = document.querySelector('.ai-chat-input');
        const sendBtn = document.querySelector('.send-chat-btn');
        
        if (!chatMessages || !input || !message.trim()) return;

        // Clear the empty state if it exists
        const emptyState = chatMessages.querySelector('.text-center.py-8');
        if (emptyState) {
            chatMessages.innerHTML = '';
        }

        // Add user message
        this.addChatMessage(message, 'user');
        input.value = '';
        
        // Update character count
        const charCount = document.querySelector('.char-count');
        if (charCount) charCount.textContent = '0/300';

        // Disable send button
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        try {
            console.log('Sending chat message:', { bookId, message });
            
            const response = await fetch(`${this.apiEndpoints.chat}${bookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: message })
            });

            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('Chat response result:', result);
            
            if (result.success) {
                this.addChatMessage(result.response, 'ai');
            } else {
                this.addChatMessage('Xin lỗi, đã có lỗi xảy ra khi xử lý tin nhắn của bạn.', 'ai');
                this.showNotification(result.message || 'Lỗi khi gửi tin nhắn', 'error');
            }
        } catch (error) {
            console.error('Error sending chat message:', error);
            console.error('Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            
            // Check if it's a network error
            if (error instanceof TypeError && error.message.includes('Failed to fetch')) {
                this.addChatMessage('Không thể kết nối đến server. Vui lòng kiểm tra kết nối mạng.', 'ai');
                this.showNotification('Lỗi kết nối mạng', 'error');
            } else if (error.message.includes('HTTP')) {
                this.addChatMessage(`Lỗi server: ${error.message}`, 'ai');
                this.showNotification('Lỗi server', 'error');
            } else {
                this.addChatMessage('Xin lỗi, không thể kết nối đến server.', 'ai');
                this.showNotification('Lỗi kết nối', 'error');
            }
        } finally {
            // Re-enable send button
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> GỬI';
            }
        }
    }

    addChatMessage(message, type) {
        const chatMessages = document.getElementById('chat-messages');
        if (!chatMessages) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `ai-chat-message ${type} ai-fade-in`;
        
        if (type === 'user') {
            messageDiv.innerHTML = `
                <div class="flex items-center justify-end mb-2">
                    <div class="bg-black text-white px-4 py-2 max-w-xs">
                        <div class="text-xs uppercase tracking-wider font-bold mb-1">BẠN</div>
                        <div>${message}</div>
                    </div>
                    <div class="w-8 h-8 bg-black text-white flex items-center justify-center ml-3">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="flex items-start mb-2">
                    <div class="w-8 h-8 bg-black text-white flex items-center justify-center mr-3">
                        <i class="fas fa-robot text-xs"></i>
                    </div>
                    <div class="bg-gray-100 px-4 py-2 max-w-xs border-l-4 border-black">
                        <div class="text-xs uppercase tracking-wider font-bold mb-1 text-gray-600">AI ASSISTANT</div>
                        <div class="text-gray-800">${message}</div>
                    </div>
                </div>
            `;
        }

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    formatKeyPoints(keyPoints) {
        if (typeof keyPoints === 'string') {
            return keyPoints.split('\n').map(point => 
                point.trim() ? `<div class="flex items-start mb-2"><div class="w-2 h-2 bg-black mt-2 mr-3"></div><div>${point.trim()}</div></div>` : ''
            ).join('');
        }
        return keyPoints;
    }

    formatThemes(themes) {
        if (typeof themes === 'string') {
            return themes.split(',').map(theme => 
                `<span class="inline-block bg-black text-white px-3 py-1 text-xs font-bold uppercase tracking-wider mr-2 mb-2">${theme.trim()}</span>`
            ).join('');
        }
        return themes;
    }

    toggleSection(titleElement) {
        const target = titleElement.getAttribute('data-target');
        const content = document.getElementById(target);
        const chevron = titleElement.querySelector('.fa-chevron-down');
        
        if (content) {
            content.classList.toggle('collapsed');
            content.classList.toggle('expanded');
            
            if (chevron) {
                chevron.style.transform = content.classList.contains('expanded') ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }
    }

    showInitialState(container, bookId) {
        container.innerHTML = `
            <div class="ai-initial">
                <div class="ai-initial-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="ai-initial-title">Tóm tắt AI</h3>
                <p class="ai-initial-description">
                    Tạo tóm tắt thông minh cho cuốn sách này bằng công nghệ AI
                </p>
                <button class="ai-btn generate-summary-btn" data-book-id="${bookId}">
                    <i class="fas fa-plus"></i>
                    TẠO TÓM TẮT AI
                </button>
            </div>
        `;
    }

    showLoading(container, message = 'Đang tải...') {
        container.innerHTML = `
            <div class="ai-loading">
                <div class="ai-loading-icon">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="ai-loading-text">${message}</div>
                <div class="ai-loading-dots">
                    <div class="ai-loading-dot"></div>
                    <div class="ai-loading-dot"></div>
                    <div class="ai-loading-dot"></div>
                </div>
            </div>
        `;
    }

    showError(container, message) {
        container.innerHTML = `
            <div class="ai-error">
                <div class="ai-error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="ai-error-text">Lỗi</div>
                <div class="ai-error-message">${message}</div>
                <button class="ai-btn generate-summary-btn" data-book-id="${this.getBookIdFromPage()}">
                    <i class="fas fa-redo"></i>
                    THỬ LẠI
                </button>
            </div>
        `;
    }

    setButtonLoading(button, loading) {
        if (!button) return;
        
        if (loading) {
            button.disabled = true;
            const originalContent = button.innerHTML;
            button.setAttribute('data-original-content', originalContent);
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG XỬ LÝ...';
        } else {
            button.disabled = false;
            const originalContent = button.getAttribute('data-original-content');
            if (originalContent) {
                button.innerHTML = originalContent;
                button.removeAttribute('data-original-content');
            }
        }
    }

    // Enhanced UI interactions
    addEnhancedInteractions() {
        // Smooth scrolling for chat messages
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.style.scrollBehavior = 'smooth';
        }

        // Auto-resize textarea
        const textareas = document.querySelectorAll('.ai-chat-input');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', this.autoResizeTextarea);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Enter to send message
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const sendBtn = document.querySelector('.send-chat-btn');
                if (sendBtn && !sendBtn.disabled) {
                    sendBtn.click();
                }
            }
            
            // Escape to close chat
            if (e.key === 'Escape') {
                const chatContainer = document.querySelector('.ai-chat-container');
                if (chatContainer && !chatContainer.classList.contains('hidden')) {
                    chatContainer.classList.add('hidden');
                }
            }
        });
    }

    autoResizeTextarea(event) {
        const textarea = event.target;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    }

    // Enhanced notification with better positioning
    showNotificationEnhanced(message, type = 'info', duration = 5000) {
        // Remove existing notifications
        const existing = document.querySelectorAll('.ai-notification');
        existing.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `ai-notification fixed top-20 right-4 p-4 shadow-lg z-50 max-w-sm transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-600' : 
            type === 'error' ? 'bg-red-600' : 
            type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600'
        } text-white border-l-4 border-white`;
        
        notification.innerHTML = `
            <div class="flex items-start space-x-3">
                <i class="fas ${
                    type === 'success' ? 'fa-check-circle' : 
                    type === 'error' ? 'fa-exclamation-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'
                } mt-1"></i>
                <div class="flex-1">
                    <div class="font-bold text-sm uppercase tracking-wide mb-1">
                        ${type === 'success' ? 'THÀNH CÔNG' : 
                          type === 'error' ? 'LỖI' : 
                          type === 'warning' ? 'CẢNH BÁO' : 'THÔNG TIN'}
                    </div>
                    <div class="text-sm">${message}</div>
                </div>
                <button class="text-white hover:text-gray-200 ml-2" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Slide in animation
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }

    // Initialize enhanced features
    initEnhancedFeatures() {
        this.addEnhancedInteractions();
        
        // Add loading states for better UX
        this.addLoadingStates();
        
        // Initialize intersection observer for animations
        this.initIntersectionObserver();
    }

    addLoadingStates() {
        const buttons = document.querySelectorAll('.ai-btn');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                if (!button.disabled) {
                    button.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        button.style.transform = '';
                    }, 150);
                }
            });
        });
    }

    initIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('ai-fade-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '20px'
        });

        // Observe AI summary sections
        const sections = document.querySelectorAll('.ai-section, .ai-chat-container');
        sections.forEach(section => observer.observe(section));
    }

    showNotification(message, type = 'info') {
        this.showNotificationEnhanced(message, type);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AdidasAISummaryManager();
});
