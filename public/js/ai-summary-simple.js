class AISummaryManager {
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
            
            if (e.target.closest('.toggle-summary-section')) {
                e.preventDefault();
                this.toggleSummarySection(e.target.closest('.toggle-summary-section'));
            }

            if (e.target.closest('.send-chat-btn')) {
                e.preventDefault();
                const bookId = e.target.closest('.send-chat-btn').getAttribute('data-book-id');
                this.sendChatMessage(bookId);
            }
        });

        // Enter key để gửi tin nhắn
        document.addEventListener('keypress', (e) => {
            if (e.target.matches('.chat-input') && e.key === 'Enter') {
                e.preventDefault();
                const bookId = e.target.getAttribute('data-book-id');
                this.sendChatMessage(bookId);
            }
        });

        // Character counter cho chat input
        document.addEventListener('input', (e) => {
            if (e.target.matches('.chat-input')) {
                const charCount = e.target.value.length;
                const maxLength = e.target.getAttribute('maxlength') || 300;
                const counter = e.target.parentElement.querySelector('.char-count');
                if (counter) {
                    counter.textContent = `${charCount}/${maxLength}`;
                    counter.className = charCount > maxLength * 0.8 ? 'text-orange-500' : 'text-gray-400';
                }
            }
        });
    }

    async loadSummary(bookId) {
        try {
            const response = await fetch(`${this.apiEndpoints.get}${bookId}`, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            if (result.success) {
                this.displaySummary(result.data);
            } else {
                this.showGenerateButton(bookId);
            }
        } catch (error) {
            console.error('Error loading summary:', error);
            this.showGenerateButton(bookId);
        }
    }

    async generateSummary(bookId) {
        const button = document.querySelector(`[data-book-id="${bookId}"].generate-summary-btn`);
        const container = document.getElementById('ai-summary-container');

        if (button) this.setButtonLoading(button, true);
        this.showLoadingState(container);

        try {
            const response = await fetch(`${this.apiEndpoints.generate}${bookId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
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
        if (!confirm('Bạn có chắc chắn muốn tạo lại tóm tắt AI?')) return;

        const button = document.querySelector(`[data-book-id="${bookId}"].regenerate-summary-btn`);
        const container = document.getElementById('ai-summary-container');

        if (button) this.setButtonLoading(button, true);
        this.showLoadingState(container);

        try {
            const response = await fetch(`${this.apiEndpoints.regenerate}${bookId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
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
        
        // Progressive loading animation
        container.style.opacity = '0';
        setTimeout(() => {
            container.style.transition = 'opacity 0.5s ease';
            container.style.opacity = '1';
            
            // Apply additional UX improvements
            this.addProgressiveLoading();
            this.optimizeForMobile();
            this.observeVisibility();
        }, 100);
    }

    buildSummaryHTML(data) {
        return `
            <div class="max-w-4xl mx-auto">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 mb-6 border border-blue-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Tóm tắt AI</h2>
                                <p class="text-gray-600 text-sm">Được tạo bởi trí tuệ nhân tạo</p>
                            </div>
                        </div>
                        <button class="regenerate-summary-btn bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl border border-gray-200 transition-all duration-200 flex items-center space-x-2 shadow-sm hover:shadow-md" 
                                data-book-id="${this.getBookIdFromPage()}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="hidden sm:inline">Tạo lại</span>
                        </button>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid lg:grid-cols-2 gap-6 mb-8">
                    <!-- Tóm tắt ngắn -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center cursor-pointer toggle-summary-section" data-target="summary-short">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                                    </svg>
                                </div>
                                <span>Tóm tắt ngắn</span>
                                <svg class="w-5 h-5 ml-auto transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </h3>
                        </div>
                        <div id="summary-short" class="p-6">
                            <div class="prose prose-gray max-w-none">
                                <p class="text-gray-700 leading-relaxed">${data.summary || 'Chưa có tóm tắt'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tóm tắt chi tiết -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center cursor-pointer toggle-summary-section" data-target="summary-detailed">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                                    </svg>
                                </div>
                                <span>Tóm tắt chi tiết</span>
                                <svg class="w-5 h-5 ml-auto transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </h3>
                        </div>
                        <div id="summary-detailed" class="p-6 hidden">
                            <div class="prose prose-gray max-w-none">
                                <p class="text-gray-700 leading-relaxed">${data.detailed_summary || 'Chưa có tóm tắt chi tiết'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                ${data.key_points && data.key_points.length > 0 ? `
                <!-- Điểm chính & Chủ đề -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center cursor-pointer toggle-summary-section" data-target="key-points">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span>Điểm chính</span>
                                <svg class="w-5 h-5 ml-auto transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </h3>
                        </div>
                        <div id="key-points" class="p-6 hidden">
                            <ul class="space-y-3">
                                ${data.key_points.map(point => `
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0 w-2 h-2 bg-purple-500 rounded-full mt-2.5 mr-3"></div>
                                        <span class="text-gray-700 leading-relaxed">${point}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    </div>
                    
                    ${data.themes && data.themes.length > 0 ? `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Chủ đề</h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            ${data.themes.map(theme => `
                                <span class="px-3 py-1.5 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-full text-sm font-medium border border-blue-100">${theme}</span>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>
                ` : ''}

                <!-- Chat với AI -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <span>Chat với AI</span>
                                <p class="text-sm text-gray-600 font-normal">Hỏi về cuốn sách "${data.book_title || 'này'}"</p>
                            </div>
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div id="chat-messages" class="bg-gray-50 rounded-xl p-4 h-80 overflow-y-auto mb-4 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                                </div>
                                <p class="text-gray-500 text-sm">Bắt đầu cuộc trò chuyện với AI</p>
                                <p class="text-gray-400 text-xs mt-1">Hỏi về nội dung, nhân vật, tác giả...</p>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <div class="flex-1 relative">
                                <input type="text" 
                                       class="chat-input w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Nhập câu hỏi của bạn..."
                                       data-book-id="${this.getBookIdFromPage()}"
                                       maxlength="300">
                                <div class="absolute right-3 top-3 text-xs text-gray-400">
                                    <span class="char-count">0/300</span>
                                </div>
                            </div>
                            <button class="send-chat-btn bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white p-3 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                                    data-book-id="${this.getBookIdFromPage()}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                            <span>💡 Chỉ trả lời câu hỏi liên quan đến sách này</span>
                            <span>⌨️ Enter để gửi</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="text-center text-sm text-gray-400 mt-6 p-4 bg-gray-50 rounded-xl">
                    <p>Được tạo bởi AI • Cập nhật: ${new Date(data.updated_at).toLocaleDateString('vi-VN')}</p>
                </div>
            </div>
        `;
    }

    showGenerateButton(bookId) {
        const container = document.getElementById('ai-summary-container');
        if (!container) return;

        container.innerHTML = `
            <div class="max-w-2xl mx-auto text-center">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
                    <div class="mb-8">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Tạo tóm tắt AI</h3>
                        <p class="text-gray-600 leading-relaxed max-w-md mx-auto">
                            Sử dụng trí tuệ nhân tạo để tạo tóm tắt chi tiết, phân tích điểm chính và chủ đề của cuốn sách này
                        </p>
                    </div>
                    
                    <button class="generate-summary-btn bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold flex items-center mx-auto space-x-3 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105" 
                            data-book-id="${bookId}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        <span>Bắt đầu tạo tóm tắt</span>
                    </button>
                    
                    <div class="mt-6 text-sm text-gray-500">
                        ⏱️ Quá trình này có thể mất vài giây
                    </div>
                </div>
            </div>
        `;
    }

    showLoadingState(container) {
        if (!container) return;
        container.innerHTML = `
            <div class="max-w-2xl mx-auto text-center">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
                    <div class="mb-8">
                        <div class="relative">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl mx-auto mb-6 flex items-center justify-center">
                                <div class="animate-spin w-8 h-8 border-4 border-white border-t-transparent rounded-full"></div>
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Đang tạo tóm tắt AI</h3>
                        <p class="text-gray-600 leading-relaxed max-w-md mx-auto mb-6">
                            AI đang phân tích nội dung sách và tạo tóm tắt chi tiết. Vui lòng đợi trong giây lát...
                        </p>
                        
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                            <span>Đang xử lý</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    showError(container, message) {
        if (!container) return;
        container.innerHTML = `
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8 text-center">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-red-700 mb-2">Lỗi tạo tóm tắt</h3>
                <p class="text-gray-600 mb-4">${message}</p>
                <button class="generate-summary-btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center mx-auto space-x-2" data-book-id="${this.getBookIdFromPage()}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Thử lại</span>
                </button>
            </div>
        `;
    }

    async sendChatMessage(bookId) {
        console.log('Sending chat message for bookId:', bookId);
        
        const input = document.querySelector('.chat-input');
        const message = input.value.trim();
        
        console.log('Input element found:', input);
        console.log('Message content:', message);
        
        if (!message) {
            this.showNotification('Vui lòng nhập câu hỏi', 'error');
            return;
        }

        if (message.length < 3) {
            this.showNotification('Câu hỏi quá ngắn, vui lòng nhập ít nhất 3 ký tự', 'error');
            return;
        }

        if (message.length > 300) {
            this.showNotification('Câu hỏi quá dài, vui lòng nhập tối đa 300 ký tự', 'error');
            return;
        }

        const chatMessages = document.getElementById('chat-messages');
        const sendButton = document.querySelector('.send-chat-btn');
        
        console.log('Chat elements found:', { chatMessages, sendButton });

        // Clear welcome message if this is the first message
        const welcomeMessage = chatMessages.querySelector('.text-center.py-8');
        if (welcomeMessage) {
            welcomeMessage.remove();
            console.log('Welcome message removed');
        }

        // Thêm tin nhắn người dùng
        this.addChatMessage(chatMessages, message, 'user');
        
        // Xóa input và disable button
        input.value = '';
        input.disabled = true;
        
        // Reset character counter
        const counter = input.parentElement.querySelector('.char-count');
        if (counter) {
            counter.textContent = '0/300';
            counter.className = 'text-gray-400';
        }
        
        this.setChatButtonLoading(sendButton, true);

        // Thêm typing indicator
        const typingId = this.addTypingIndicator(chatMessages);

        try {
            const response = await fetch(`${this.apiEndpoints.chat}${bookId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });

            console.log('Chat API response status:', response.status);
            console.log('Chat API response headers:', response.headers);

            // Xử lý response dựa trên status code
            if (response.status === 422) {
                // Validation error
                const result = await response.json();
                console.log('Validation error:', result);
                
                this.removeTypingIndicator(typingId);
                
                let errorMessage = 'Tin nhắn không hợp lệ.';
                if (result.errors && result.errors.message) {
                    errorMessage = result.errors.message[0];
                } else if (result.message) {
                    errorMessage = result.message;
                }
                
                this.addChatMessage(chatMessages, errorMessage, 'error');
                this.showNotification('Validation error', 'error');
                return;
            }

            if (response.status === 429) {
                // Rate limit error
                const result = await response.json();
                console.log('Rate limit error:', result);
                
                this.removeTypingIndicator(typingId);
                this.addChatMessage(chatMessages, result.message || 'Bạn đã gửi quá nhiều tin nhắn. Vui lòng đợi một chút.', 'warning');
                this.showNotification('Rate limit exceeded', 'warning');
                return;
            }

            if (response.status === 404) {
                // Not found error
                const result = await response.json();
                console.log('Book not found error:', result);
                
                this.removeTypingIndicator(typingId);
                this.addChatMessage(chatMessages, result.message || 'Không tìm thấy sách.', 'error');
                this.showNotification('Sách không tồn tại', 'error');
                return;
            }

            if (response.status === 500) {
                // Server error
                this.removeTypingIndicator(typingId);
                this.addChatMessage(chatMessages, 'Lỗi hệ thống. Vui lòng thử lại sau.', 'error');
                this.showNotification('Lỗi server', 'error');
                return;
            }

            if (!response.ok) {
                // Other HTTP errors
                this.removeTypingIndicator(typingId);
                this.addChatMessage(chatMessages, `Lỗi HTTP ${response.status}. Vui lòng thử lại.`, 'error');
                this.showNotification(`HTTP ${response.status} Error`, 'error');
                return;
            }

            const result = await response.json();
            console.log('Chat API result:', result);
            
            // Remove typing indicator
            this.removeTypingIndicator(typingId);

            if (result.success) {
                this.addChatMessage(chatMessages, result.response, 'ai');
                
                // Hiển thị số tin nhắn còn lại nếu có
                if (result.remaining_messages !== undefined) {
                    if (result.remaining_messages <= 3) {
                        this.showNotification(`Còn ${result.remaining_messages} tin nhắn trong phút này`, 'warning');
                    }
                }
            } else {
                console.error('Chat API error:', result);
                this.addChatMessage(chatMessages, result.message || 'Lỗi khi gửi tin nhắn', 'error');
                this.showNotification(result.message || 'Lỗi API', 'error');
            }

        } catch (error) {
            console.error('Error sending chat message:', error);
            console.error('Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack,
                bookId: bookId,
                message: message,
                timestamp: new Date().toISOString()
            });
            
            this.removeTypingIndicator(typingId);
            
            if (error instanceof TypeError && error.message.includes('Failed to fetch')) {
                this.addChatMessage(chatMessages, 'Không thể kết nối đến server. Vui lòng kiểm tra kết nối mạng.', 'error');
                this.showNotification('Lỗi kết nối mạng', 'error');
            } else if (error.name === 'SyntaxError') {
                this.addChatMessage(chatMessages, 'Lỗi dữ liệu từ server. Vui lòng thử lại.', 'error');
                this.showNotification('Lỗi dữ liệu', 'error');
            } else {
                this.addChatMessage(chatMessages, 'Lỗi không xác định. Vui lòng thử lại sau.', 'error');
                this.showNotification('Lỗi hệ thống', 'error');
            }
        } finally {
            // Re-enable input và button
            input.disabled = false;
            this.setChatButtonLoading(sendButton, false);
            input.focus();
        }
    }

    setChatButtonLoading(button, loading) {
        console.log('Setting chat button loading:', { button, loading });
        
        if (!button) {
            console.error('Button is null or undefined');
            return;
        }
        
        button.disabled = loading;
        if (loading) {
            button.innerHTML = '<div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></div>';
        } else {
            button.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                </svg>
            `;
        }
        
        console.log('Chat button updated successfully:', button.innerHTML);
    }

    addChatMessage(container, message, type) {
        console.log('Adding chat message:', { message, type, container });
        
        if (!container) {
            console.error('Container is null');
            return;
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 ${type === 'user' ? 'text-right' : 'text-left'} animate-fadeIn`;
        
        const bubble = document.createElement('div');
        bubble.className = `inline-block max-w-xs lg:max-w-sm xl:max-w-md break-words ${
            type === 'user' 
                ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl rounded-br-md px-4 py-3 user-message-bubble' 
                : type === 'error' 
                    ? 'bg-red-50 text-red-800 border border-red-200 rounded-2xl rounded-bl-md px-4 py-3' 
                    : type === 'warning' 
                        ? 'bg-yellow-50 text-yellow-800 border border-yellow-200 rounded-2xl rounded-bl-md px-4 py-3'
                        : 'bg-white border border-gray-200 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm'
        }`;
        
        if (type === 'ai') {
            bubble.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-700 leading-relaxed m-0">${message}</p>
                    </div>
                </div>
            `;
        } else if (type === 'user') {
            bubble.innerHTML = `
                <div class="flex items-center justify-end">
                    <p class="text-white font-medium m-0" style="color: #ffffff !important; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">${message}</p>
                    <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center ml-2 flex-shrink-0">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            `;
            console.log('User message HTML created:', bubble.innerHTML);
        } else if (type === 'error') {
            bubble.innerHTML = `
                <div class="flex items-start space-x-2">
                    <svg class="w-4 h-4 mt-0.5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm">${message}</span>
                </div>
            `;
        } else if (type === 'warning') {
            bubble.innerHTML = `
                <div class="flex items-start space-x-2">
                    <svg class="w-4 h-4 mt-0.5 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm">${message}</span>
                </div>
            `;
        }

        messageDiv.appendChild(bubble);
        container.appendChild(messageDiv);
        
        console.log('Message div added to container. Total messages:', container.children.length);
        
        // Smooth scroll to bottom
        container.scrollTo({ 
            top: container.scrollHeight, 
            behavior: 'smooth' 
        });
    }

    addTypingIndicator(container) {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'mb-3 text-left typing-indicator';
        typingDiv.innerHTML = `
            <div class="inline-block p-3 rounded-lg bg-gray-200">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                    <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                </div>
            </div>
        `;
        
        container.appendChild(typingDiv);
        container.scrollTop = container.scrollHeight;
        
        return typingDiv;
    }

    removeTypingIndicator(indicator) {
        if (indicator && indicator.parentNode) {
            indicator.parentNode.removeChild(indicator);
        }
    }

    setButtonLoading(button, loading) {
        button.disabled = loading;
        if (loading) {
            if (button.classList.contains('regenerate-summary-btn')) {
                button.innerHTML = '<div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></div>Đang tạo lại...';
            } else {
                button.innerHTML = '<div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></div>Đang xử lý...';
            }
        } else {
            if (button.classList.contains('regenerate-summary-btn')) {
                button.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Tạo lại
                `;
            } else {
                button.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                    Tạo tóm tắt AI
                `;
            }
        }
    }

    toggleSummarySection(trigger) {
        const targetId = trigger.getAttribute('data-target');
        const target = document.getElementById(targetId);
        const icon = trigger.querySelector('svg');

        if (target) {
            target.classList.toggle('hidden');
            icon.style.transform = target.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }

    showNotification(message, type = 'info') {
        if (typeof toastr !== 'undefined') {
            toastr[type === 'warning' ? 'warning' : type](message);
        } else {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' :
                'bg-blue-500'
            } text-white max-w-sm`;
            
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    ${type === 'success' ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' :
                      type === 'error' ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>' :
                      type === 'warning' ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>' :
                      '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'}
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            notification.style.transform = 'translateX(100%)';
            notification.style.transition = 'transform 0.3s ease';
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Auto remove
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, type === 'warning' ? 4000 : 3000);
        }
    }

    // Thêm method để cải thiện UX
    addProgressiveLoading() {
        // Progressive loading cho các sections
        const sections = document.querySelectorAll('.ai-summary-section .bg-white');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            setTimeout(() => {
                section.style.transition = 'all 0.5s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, index * 150);
        });
    }

    addSmoothScrollToChat() {
        // Smooth scroll đến chat khi có tin nhắn mới
        const chatSection = document.querySelector('#chat-messages');
        if (chatSection) {
            chatSection.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest' 
            });
        }
    }

    optimizeForMobile() {
        // Tối ưu cho mobile
        if (window.innerWidth <= 768) {
            const containers = document.querySelectorAll('.max-w-4xl');
            containers.forEach(container => {
                container.classList.add('px-4');
            });
        }
    }

    // Lazy load improvements
    observeVisibility() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeIn');
                }
            });
        });

        document.querySelectorAll('.ai-summary-section .bg-white').forEach(el => {
            observer.observe(el);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AISummaryManager();
});
