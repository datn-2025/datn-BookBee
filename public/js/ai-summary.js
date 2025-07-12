class AISummaryManager {
    constructor() {
        this.apiEndpoints = {
            generate: '/ai-summary/generate/',
            get: '/ai-summary/get/',
            regenerate: '/ai-summary/regenerate/',
            status: '/ai-summary/status/'
        };
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        // Auto-load summary if book detail page
        const bookId = this.getBookIdFromPage();
        if (bookId) {
            this.loadSummary(bookId);
        }

        // Bind event listeners
        this.bindEvents();
    }

    getBookIdFromPage() {
        // Try to get book ID from data attribute or URL
        const bookElement = document.querySelector('[data-book-id]');
        if (bookElement) {
            return bookElement.getAttribute('data-book-id');
        }
        
        // Fallback: extract from URL pattern
        const urlMatch = window.location.pathname.match(/\/books\/([^\/]+)/);
        return urlMatch ? urlMatch[1] : null;
    }

    bindEvents() {
        // Generate summary button
        document.addEventListener('click', (e) => {
            if (e.target.matches('.generate-summary-btn')) {
                e.preventDefault();
                const bookId = e.target.getAttribute('data-book-id');
                this.generateSummary(bookId);
            }
        });

        // Regenerate summary button
        document.addEventListener('click', (e) => {
            if (e.target.matches('.regenerate-summary-btn')) {
                e.preventDefault();
                const bookId = e.target.getAttribute('data-book-id');
                this.regenerateSummary(bookId);
            }
        });

        // Toggle summary sections
        document.addEventListener('click', (e) => {
            if (e.target.matches('.toggle-summary-section')) {
                e.preventDefault();
                this.toggleSummarySection(e.target);
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

        if (button) {
            this.setButtonLoading(button, true);
        }

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
            if (button) {
                this.setButtonLoading(button, false);
            }
        }
    }

    async regenerateSummary(bookId) {
        if (!confirm('Bạn có chắc chắn muốn tạo lại tóm tắt AI? Tóm tắt hiện tại sẽ bị xóa.')) {
            return;
        }

        const button = document.querySelector(`[data-book-id="${bookId}"].regenerate-summary-btn`);
        const container = document.getElementById('ai-summary-container');

        if (button) {
            this.setButtonLoading(button, true);
        }

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
            if (button) {
                this.setButtonLoading(button, false);
            }
        }
    }

    displaySummary(summaryData) {
        const container = document.getElementById('ai-summary-container');
        if (!container) return;

        const summaryHtml = this.buildSummaryHTML(summaryData);
        container.innerHTML = summaryHtml;
        
        // Add animation
        container.style.opacity = '0';
        setTimeout(() => {
            container.style.transition = 'opacity 0.3s ease';
            container.style.opacity = '1';
        }, 100);
    }

    buildSummaryHTML(data) {
        return `
            <div class="glass bg-white rounded-2xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tóm tắt AI
                    </h3>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            ${data.ai_model === 'fallback' ? 'Demo' : 'AI Generated'}
                        </span>
                        <button class="regenerate-summary-btn px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm" 
                                data-book-id="${this.getBookIdFromPage()}">
                            🔄 Tạo lại
                        </button>
                    </div>
                </div>

                <!-- Tóm tắt ngắn -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center cursor-pointer toggle-summary-section" 
                        data-target="summary-short">
                        <svg class="w-5 h-5 mr-2 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Tóm tắt ngắn
                    </h4>
                    <div id="summary-short" class="text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-lg">
                        ${data.summary || 'Chưa có tóm tắt'}
                    </div>
                </div>

                <!-- Tóm tắt chi tiết -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center cursor-pointer toggle-summary-section" 
                        data-target="summary-detailed">
                        <svg class="w-5 h-5 mr-2 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Tóm tắt chi tiết
                    </h4>
                    <div id="summary-detailed" class="text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-lg hidden">
                        ${data.detailed_summary || 'Chưa có tóm tắt chi tiết'}
                    </div>
                </div>

                <!-- Điểm chính -->
                ${data.key_points && data.key_points.length > 0 ? `
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center cursor-pointer toggle-summary-section" 
                        data-target="key-points">
                        <svg class="w-5 h-5 mr-2 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Điểm chính
                    </h4>
                    <div id="key-points" class="hidden">
                        <ul class="space-y-2">
                            ${data.key_points.map(point => `
                                <li class="flex items-start">
                                    <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3"></span>
                                    <span class="text-gray-600">${point}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                </div>
                ` : ''}

                <!-- Chủ đề -->
                ${data.themes && data.themes.length > 0 ? `
                <div class="mb-4">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Chủ đề</h4>
                    <div class="flex flex-wrap gap-2">
                        ${data.themes.map(theme => `
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">${theme}</span>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <div class="text-xs text-gray-400 mt-4">
                    Được tạo bởi AI • Cập nhật: ${new Date(data.updated_at).toLocaleDateString('vi-VN')}
                </div>
            </div>
        `;
    }

    showGenerateButton(bookId) {
        const container = document.getElementById('ai-summary-container');
        if (!container) return;

        container.innerHTML = `
            <div class="glass bg-white rounded-2xl shadow-lg p-8 mb-8 text-center">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tóm tắt AI chưa có</h3>
                    <p class="text-gray-600 mb-6">Sử dụng trí tuệ nhân tạo để tạo tóm tắt chi tiết cho cuốn sách này</p>
                </div>
                <button class="generate-summary-btn bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium" 
                        data-book-id="${bookId}">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                    Tạo tóm tắt AI
                </button>
            </div>
        `;
    }

    showLoadingState(container) {
        if (!container) return;

        container.innerHTML = `
            <div class="glass bg-white rounded-2xl shadow-lg p-8 mb-8 text-center">
                <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Đang tạo tóm tắt AI...</h3>
                <p class="text-gray-500">Vui lòng đợi trong giây lát</p>
            </div>
        `;
    }

    showError(container, message) {
        if (!container) return;

        container.innerHTML = `
            <div class="glass bg-white rounded-2xl shadow-lg p-8 mb-8 text-center">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-red-700 mb-2">Lỗi tạo tóm tắt</h3>
                <p class="text-gray-600 mb-4">${message}</p>
                <button class="generate-summary-btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors" 
                        data-book-id="${this.getBookIdFromPage()}">
                    Thử lại
                </button>
            </div>
        `;
    }

    setButtonLoading(button, loading) {
        if (loading) {
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Đang xử lý...
            `;
        } else {
            button.disabled = false;
            button.innerHTML = button.classList.contains('regenerate-summary-btn') ? '🔄 Tạo lại' : '✨ Tạo tóm tắt AI';
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
        // Check if toastr is available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AISummaryManager();
});
