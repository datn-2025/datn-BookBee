class ComboAISummaryManager {
    constructor() {
        this.apiEndpoints = {
            generate: '/ai-summary/combo/generate/',
            get: '/ai-summary/combo/get/',
            regenerate: '/ai-summary/combo/regenerate/',
            status: '/ai-summary/combo/status/',
            chat: '/ai-summary/combo/chat/'
        };
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        const comboId = this.getComboIdFromPage();
        if (comboId) {
            this.loadComboSummary(comboId);
        }
        this.bindEvents();
        this.initEnhancedFeatures();
    }

    getComboIdFromPage() {
        const comboElement = document.querySelector('[data-combo-id]');
        if (comboElement) {
            return comboElement.getAttribute('data-combo-id');
        }
        
        const urlMatch = window.location.pathname.match(/\/combos\/([^\/]+)/);
        return urlMatch ? urlMatch[1] : null;
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.generate-combo-summary-btn')) {
                e.preventDefault();
                const comboId = e.target.closest('.generate-combo-summary-btn').getAttribute('data-combo-id');
                this.generateComboSummary(comboId);
            }
            
            if (e.target.closest('.regenerate-combo-summary-btn')) {
                e.preventDefault();
                const comboId = e.target.closest('.regenerate-combo-summary-btn').getAttribute('data-combo-id');
                this.regenerateComboSummary(comboId);
            }
            
            if (e.target.closest('.combo-ai-section-title')) {
                e.preventDefault();
                this.toggleSection(e.target.closest('.combo-ai-section-title'));
            }

            if (e.target.closest('.send-combo-chat-btn')) {
                e.preventDefault();
                const comboId = e.target.closest('.send-combo-chat-btn').getAttribute('data-combo-id');
                const input = document.querySelector('.combo-ai-chat-input');
                if (input && input.value.trim()) {
                    this.sendComboChatMessage(comboId, input.value.trim());
                }
            }
        });

        // Enter key for chat input
        document.addEventListener('keydown', (e) => {
            if (e.target.classList.contains('combo-ai-chat-input') && e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const sendBtn = document.querySelector('.send-combo-chat-btn');
                if (sendBtn) {
                    sendBtn.click();
                }
            }
        });

        // Character count for chat input
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('combo-ai-chat-input')) {
                const charCount = document.querySelector('.combo-char-count');
                if (charCount) {
                    charCount.textContent = `${e.target.value.length}/300`;
                }
            }
        });
    }

    async loadComboSummary(comboId) {
        const container = document.getElementById('ai-combo-summary-container');
        if (!container) return;

        this.showLoading(container);

        try {
            const response = await fetch(`${this.apiEndpoints.get}${comboId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success && result.data) {
                this.displayComboSummary(result.data);
            } else {
                this.showInitialState(container, comboId);
            }
        } catch (error) {
            console.error('Error loading combo summary:', error);
            this.showInitialState(container, comboId);
        }
    }

    async generateComboSummary(comboId) {
        const container = document.getElementById('ai-combo-summary-container');
        const button = document.querySelector(`.generate-combo-summary-btn[data-combo-id="${comboId}"]`);
        
        if (!container || !comboId) return;

        this.setButtonLoading(button, true);
        this.showLoading(container, 'Đang tạo tóm tắt AI cho combo...');

        try {
            const response = await fetch(`${this.apiEndpoints.generate}${comboId}`, {
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
                this.displayComboSummary(result.data);
                this.showNotification('Tạo tóm tắt AI cho combo thành công!', 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error generating combo summary:', error);
            this.showError(container, error.message);
            this.showNotification('Lỗi khi tạo tóm tắt AI combo', 'error');
        } finally {
            if (button) this.setButtonLoading(button, false);
        }
    }

    async regenerateComboSummary(comboId) {
        const container = document.getElementById('ai-combo-summary-container');
        const button = document.querySelector(`.regenerate-combo-summary-btn[data-combo-id="${comboId}"]`);
        
        if (!container || !comboId) return;

        this.setButtonLoading(button, true);
        this.showLoading(container, 'Đang tạo lại tóm tắt AI cho combo...');

        try {
            const response = await fetch(`${this.apiEndpoints.regenerate}${comboId}`, {
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
                this.displayComboSummary(result.data);
                this.showNotification('Tạo lại tóm tắt AI cho combo thành công!', 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error regenerating combo summary:', error);
            this.showError(container, error.message);
            this.showNotification('Lỗi khi tạo lại tóm tắt AI combo', 'error');
        } finally {
            if (button) this.setButtonLoading(button, false);
        }
    }

    displayComboSummary(summaryData) {
        const container = document.getElementById('ai-combo-summary-container');
        if (!container) return;

        container.innerHTML = this.buildComboSummaryHTML(summaryData);
        
        // Animation
        container.style.opacity = '0';
        setTimeout(() => {
            container.style.transition = 'opacity 0.5s ease';
            container.style.opacity = '1';
            
            // Initialize enhanced features after content is loaded
            this.initEnhancedFeatures();
        }, 100);
    }

    buildComboSummaryHTML(data) {
        return `
            <div class="ai-summary-container ai-fade-in">
                <!-- Header -->
                <div class="ai-header">
                    <h3 class="ai-header-title">
                        <i class="fas fa-robot mr-2"></i>
                        Tóm tắt AI - Combo
                    </h3>
                    <span class="ai-header-badge">
                        ${data.ai_model === 'fallback' ? 'DEMO' : 'AI GENERATED'}
                    </span>
                </div>

                <!-- Content -->
                <div class="ai-content">
                    <!-- Summary Section -->
                    <div class="ai-section">
                        <div class="combo-ai-section-title" data-target="combo-summary-content">
                            <i class="fas fa-file-text"></i>
                            TÓM TẮT COMBO
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="combo-summary-content" class="ai-section-content expanded">
                            <p>${data.summary || 'Chưa có tóm tắt chi tiết.'}</p>
                        </div>
                    </div>

                    <!-- Detailed Summary Section -->
                    ${data.detailed_summary ? `
                    <div class="ai-section">
                        <div class="combo-ai-section-title" data-target="combo-detailed-content">
                            <i class="fas fa-list-ul"></i>
                            TÓM TẮT CHI TIẾT
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="combo-detailed-content" class="ai-section-content collapsed">
                            <p>${data.detailed_summary}</p>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Key Points Section -->
                    ${data.key_points ? `
                    <div class="ai-section">
                        <div class="combo-ai-section-title" data-target="combo-keypoints-content">
                            <i class="fas fa-key"></i>
                            ĐIỂM CHÍNH
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="combo-keypoints-content" class="ai-section-content collapsed">
                            <div>${this.formatKeyPoints(data.key_points)}</div>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Benefits Section -->
                    ${data.benefits ? `
                    <div class="ai-section">
                        <div class="combo-ai-section-title" data-target="combo-benefits-content">
                            <i class="fas fa-gift"></i>
                            LỢI ÍCH
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="combo-benefits-content" class="ai-section-content collapsed">
                            <div>${this.formatBenefits(data.benefits)}</div>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Themes Section -->
                    ${data.themes ? `
                    <div class="ai-section">
                        <div class="combo-ai-section-title" data-target="combo-themes-content">
                            <i class="fas fa-tags"></i>
                            CHỦ ĐỀ
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div id="combo-themes-content" class="ai-section-content collapsed">
                            <div>${this.formatThemes(data.themes)}</div>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 mt-6">
                        <button class="ai-btn regenerate-combo-summary-btn" data-combo-id="${this.getComboIdFromPage()}">
                            <i class="fas fa-redo"></i>
                            TẠO LẠI
                        </button>
                        <button class="ai-btn ai-btn-outline" onclick="document.querySelector('.combo-ai-chat-container').classList.toggle('hidden')">
                            <i class="fas fa-comments"></i>
                            CHAT VỚI AI
                        </button>
                    </div>

                    <!-- Chat Section -->
                    <div class="combo-ai-chat-container hidden">
                        <div class="ai-section-title">
                            <i class="fas fa-robot"></i>
                            CHAT VỚI AI VỀ COMBO
                        </div>
                        
                        <div id="combo-chat-messages" class="ai-chat-messages scrollbar-thin">
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-comments text-2xl text-gray-400"></i>
                                </div>
                                <div class="space-y-2">
                                    <h3 class="text-lg font-bold text-black uppercase tracking-wider">BẮT ĐẦU CUỘC TRÒ CHUYỆN</h3>
                                    <p class="text-gray-600 text-sm uppercase tracking-wide font-medium">Hỏi AI về combo sách này</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ai-chat-input-group">
                            <textarea 
                                class="combo-ai-chat-input" 
                                placeholder="Hỏi AI về combo sách này..."
                                maxlength="300"
                                rows="2"
                            ></textarea>
                            <button class="ai-btn ai-chat-send send-combo-chat-btn" data-combo-id="${this.getComboIdFromPage()}">
                                <i class="fas fa-paper-plane"></i>
                                GỬI
                            </button>
                        </div>
                        
                        <div class="text-xs text-gray-500 mt-2 text-center">
                            <span class="combo-char-count">0/300</span> ký tự
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async sendComboChatMessage(comboId, message) {
        const chatMessages = document.getElementById('combo-chat-messages');
        const input = document.querySelector('.combo-ai-chat-input');
        const sendBtn = document.querySelector('.send-combo-chat-btn');
        
        if (!chatMessages || !input || !message.trim()) return;

        // Validate message length
        if (message.length < 3) {
            this.addComboChatMessage('Câu hỏi quá ngắn, vui lòng nhập ít nhất 3 ký tự.', 'error');
            this.showNotification('Tin nhắn quá ngắn', 'error');
            return;
        }

        if (message.length > 300) {
            this.addComboChatMessage('Câu hỏi quá dài, vui lòng nhập tối đa 300 ký tự.', 'error');
            this.showNotification('Tin nhắn quá dài', 'error');
            return;
        }

        // Clear the empty state if it exists
        const emptyState = chatMessages.querySelector('.text-center.py-8');
        if (emptyState) {
            chatMessages.innerHTML = '';
        }

        // Add user message
        this.addComboChatMessage(message, 'user');
        input.value = '';
        
        // Update character count
        const charCount = document.querySelector('.combo-char-count');
        if (charCount) charCount.textContent = '0/300';

        // Disable send button
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        try {
            console.log('Sending combo chat message:', { comboId, message });
            
            const response = await fetch(`${this.apiEndpoints.chat}${comboId}`, {
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
            
            // Handle different HTTP status codes
            if (response.status === 422) {
                const result = await response.json();
                console.log('Validation error:', result);
                
                let errorMessage = 'Tin nhắn không hợp lệ.';
                if (result.errors && result.errors.message) {
                    errorMessage = result.errors.message[0];
                } else if (result.message) {
                    errorMessage = result.message;
                }
                
                this.addComboChatMessage(errorMessage, 'ai');
                this.showNotification('Validation error', 'error');
                return;
            }

            if (response.status === 429) {
                const result = await response.json();
                this.addComboChatMessage(result.message || 'Bạn đã gửi quá nhiều tin nhắn. Vui lòng đợi một chút.', 'ai');
                this.showNotification('Rate limit exceeded', 'warning');
                return;
            }

            if (response.status === 404) {
                const result = await response.json();
                this.addComboChatMessage(result.message || 'Không tìm thấy combo.', 'ai');
                this.showNotification('Combo không tồn tại', 'error');
                return;
            }

            if (response.status === 500) {
                this.addComboChatMessage('Lỗi hệ thống. Vui lòng thử lại sau.', 'ai');
                this.showNotification('Lỗi server', 'error');
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('Combo chat response result:', result);
            
            if (result.success) {
                this.addComboChatMessage(result.response, 'ai');
                
                // Show remaining messages if available
                if (result.remaining_messages !== undefined && result.remaining_messages <= 3) {
                    this.showNotification(`Còn ${result.remaining_messages} tin nhắn trong phút này`, 'warning');
                }
            } else {
                this.addComboChatMessage(result.message || 'Xin lỗi, đã có lỗi xảy ra khi xử lý tin nhắn của bạn.', 'ai');
                this.showNotification(result.message || 'Lỗi khi gửi tin nhắn', 'error');
            }
        } catch (error) {
            console.error('Error sending combo chat message:', error);
            console.error('Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            
            // Check if it's a network error
            if (error instanceof TypeError && error.message.includes('Failed to fetch')) {
                this.addComboChatMessage('Không thể kết nối đến server. Vui lòng kiểm tra kết nối mạng.', 'ai');
                this.showNotification('Lỗi kết nối mạng', 'error');
            } else if (error.name === 'SyntaxError') {
                this.addComboChatMessage('Lỗi dữ liệu từ server. Vui lòng thử lại.', 'ai');
                this.showNotification('Lỗi dữ liệu', 'error');
            } else if (error.message.includes('HTTP')) {
                this.addComboChatMessage(`Lỗi server: ${error.message}`, 'ai');
                this.showNotification('Lỗi server', 'error');
            } else {
                this.addComboChatMessage('Xin lỗi, không thể kết nối đến server.', 'ai');
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

    addComboChatMessage(message, type) {
        const chatMessages = document.getElementById('combo-chat-messages');
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
        } else if (type === 'error') {
            messageDiv.innerHTML = `
                <div class="flex items-start mb-2">
                    <div class="w-8 h-8 bg-red-600 text-white flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-circle text-xs"></i>
                    </div>
                    <div class="bg-red-50 px-4 py-2 max-w-xs border-l-4 border-red-600">
                        <div class="text-xs uppercase tracking-wider font-bold mb-1 text-red-700">LỖI</div>
                        <div class="text-red-800">${message}</div>
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
        if (Array.isArray(keyPoints)) {
            return '<ul class="space-y-2">' + 
                keyPoints.map(point => `<li class="flex items-start"><i class="fas fa-check text-green-600 mt-1 mr-2 flex-shrink-0"></i><span>${point}</span></li>`).join('') +
                '</ul>';
        }
        return '<p>' + keyPoints + '</p>';
    }

    formatBenefits(benefits) {
        if (Array.isArray(benefits)) {
            return '<ul class="space-y-2">' + 
                benefits.map(benefit => `<li class="flex items-start"><i class="fas fa-gift text-blue-600 mt-1 mr-2 flex-shrink-0"></i><span>${benefit}</span></li>`).join('') +
                '</ul>';
        }
        return '<p>' + benefits + '</p>';
    }

    formatThemes(themes) {
        if (Array.isArray(themes)) {
            return '<div class="flex flex-wrap gap-2">' +
                themes.map(theme => `<span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium">${theme}</span>`).join('') +
                '</div>';
        }
        return '<p>' + themes + '</p>';
    }

    toggleSection(titleElement) {
        const targetId = titleElement.getAttribute('data-target');
        const content = document.getElementById(targetId);
        const chevron = titleElement.querySelector('.fa-chevron-down, .fa-chevron-up');
        
        if (content) {
            if (content.classList.contains('collapsed')) {
                content.classList.remove('collapsed');
                content.classList.add('expanded');
                if (chevron) chevron.className = chevron.className.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                content.classList.remove('expanded');
                content.classList.add('collapsed');
                if (chevron) chevron.className = chevron.className.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }
    }

    showInitialState(container, comboId) {
        container.innerHTML = `
            <div class="ai-initial">
                <div class="ai-initial-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ai-initial-text">Tóm tắt AI - Combo</div>
                <div class="ai-initial-description">Tạo tóm tắt AI cho combo sách này</div>
                <button class="ai-btn generate-combo-summary-btn" data-combo-id="${comboId}">
                    <i class="fas fa-magic"></i>
                    TẠO TÓM TẮT AI
                </button>
            </div>
        `;
    }

    showLoading(container, message = 'Đang tải...') {
        container.innerHTML = `
            <div class="ai-loading">
                <div class="ai-loading-icon">
                    <div class="ai-spinner"></div>
                </div>
                <div class="ai-loading-text">${message}</div>
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
                <button class="ai-btn generate-combo-summary-btn" data-combo-id="${this.getComboIdFromPage()}">
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

    initEnhancedFeatures() {
        // Smooth scrolling for chat messages
        const chatMessages = document.getElementById('combo-chat-messages');
        if (chatMessages) {
            chatMessages.style.scrollBehavior = 'smooth';
        }

        // Auto-resize textarea
        const textareas = document.querySelectorAll('.combo-ai-chat-input');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', this.autoResizeTextarea);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Enter to send message
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                const sendBtn = document.querySelector('.send-combo-chat-btn');
                if (sendBtn && !sendBtn.disabled) {
                    sendBtn.click();
                }
            }
            
            // Escape to close chat
            if (e.key === 'Escape') {
                const chatContainer = document.querySelector('.combo-ai-chat-container');
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

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium max-w-md transform translate-x-full transition-transform duration-300`;
        
        // Set colors based on type
        switch (type) {
            case 'success':
                notification.classList.add('bg-green-600');
                break;
            case 'error':
                notification.classList.add('bg-red-600');
                break;
            case 'warning':
                notification.classList.add('bg-yellow-600');
                break;
            default:
                notification.classList.add('bg-blue-600');
        }
        
        notification.textContent = message;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Remove notification
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, type === 'warning' ? 4000 : 3000);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ComboAISummaryManager();
});
