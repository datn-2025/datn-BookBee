<!-- Chat Widget -->
<!-- Nút mở chat -->
<button id="chat-toggle" class="fixed bottom-5 right-5 z-50 transition-transform hover:scale-110 focus:outline-none">
    <div class="relative">
        <img src="{{ asset('images/bookbeee.jpg') }}" alt="Chat Bee"
            class="w-16 h-16 rounded-full object-cover shadow-lg border-2 border-black">
        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
    </div>
</button>

<!-- Hộp chat -->
<div id="chat-box" class="hidden fixed bottom-24 right-5 w-[380px] z-50">
    <div class="bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-black text-white px-4 py-3">
            <!-- Main header row -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <h3 class="font-semibold">Hỗ trợ khách hàng</h3>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Search toggle button -->
                    <button id="search-toggle" onclick="toggleChatSearch()" 
                            class="text-white hover:text-gray-300 focus:outline-none transition-colors" title="Tìm kiếm tin nhắn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <!-- Close button -->
                    <button onclick="document.getElementById('chat-box').style.display='none'" 
                            class="text-white hover:text-gray-300 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Search bar (hidden by default) -->
            <div id="chat-search-bar" class="hidden mt-3 pt-3 border-t border-gray-600">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 relative">
                        <input type="text" id="chat-search-input" 
                               class="w-full bg-gray-700 text-white placeholder-gray-300 border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-gray-400"
                               placeholder="Tìm kiếm tin nhắn..." autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button id="clear-search" onclick="clearChatSearch()" 
                            class="text-gray-300 hover:text-white focus:outline-none transition-colors" title="Xóa tìm kiếm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <!-- Search results info -->
                <div id="search-results-info" class="hidden mt-2 text-xs text-gray-300">
                    <span id="search-count">0</span> kết quả tìm thấy
                </div>
            </div>
        </div>
        
        <!-- Chat content -->
        <div id="chat-content" class="p-4 h-[350px] overflow-y-auto bg-gray-50">
            <!-- Tin nhắn sẽ được render bằng JS -->
        </div>

        <!-- Input area -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <!-- File preview area -->
            <div id="file-preview" class="hidden mb-3 p-2 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <img id="preview-image" class="w-12 h-12 rounded object-cover" style="display: none;">
                        <span id="preview-filename" class="text-sm text-gray-600"></span>
                    </div>
                    <button type="button" id="remove-file" class="text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="chat-form" class="flex items-center space-x-2" autocomplete="off">
                <!-- Emoji button -->
                <button type="button" id="emoji-toggle" 
                    class="text-gray-500 hover:text-gray-700 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>

                <!-- File upload button -->
                <label for="file-input" class="cursor-pointer text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                </label>
                <input type="file" id="file-input" class="hidden" accept="image/*">

                <input type="text" id="chat-input" 
                    class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:border-black"
                    placeholder="Nhập tin nhắn..." autocomplete="off">
                
                <button type="submit" 
                    class="bg-black text-white rounded-full p-2 hover:bg-gray-800 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>

            <!-- Emoji picker -->
            <div id="emoji-picker" class="hidden mt-2 p-3 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                <div class="grid grid-cols-8 gap-2 text-xl">
                    <!-- Emoji sẽ được populate bằng JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Thêm data-user-id và data-user-name cho JS -->
@if(Auth::check())
    <div id="chat-user-info" data-user-id="{{ Auth::id() }}" data-user-name="{{ Auth::user()->name }}" style="display:none;"></div>
@endif

<script>
// Chat search functionality
function toggleChatSearch() {
    const searchBar = document.getElementById('chat-search-bar');
    const searchInput = document.getElementById('chat-search-input');
    
    if (searchBar.classList.contains('hidden')) {
        searchBar.classList.remove('hidden');
        searchInput.focus();
    } else {
        searchBar.classList.add('hidden');
        clearChatSearch();
    }
}

function clearChatSearch() {
    const searchInput = document.getElementById('chat-search-input');
    const resultsInfo = document.getElementById('search-results-info');
    
    searchInput.value = '';
    resultsInfo.classList.add('hidden');
    
    // Remove highlight from all messages
    document.querySelectorAll('.chat-message-highlight').forEach(el => {
        el.classList.remove('chat-message-highlight');
    });
    
    // Reset scroll to bottom
    const chatContent = document.getElementById('chat-content');
    if (chatContent) {
        chatContent.scrollTop = chatContent.scrollHeight;
    }
}

// Add search functionality when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('chat-search-input');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performChatSearch(e.target.value);
            }, 300); // Debounce 300ms
        });
        
        // Enter key to search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performChatSearch(e.target.value);
            }
        });
    }
});

function performChatSearch(query) {
    const resultsInfo = document.getElementById('search-results-info');
    const searchCount = document.getElementById('search-count');
    
    // Remove previous highlights
    document.querySelectorAll('.chat-message-highlight').forEach(el => {
        el.classList.remove('chat-message-highlight');
    });
    
    if (!query.trim()) {
        resultsInfo.classList.add('hidden');
        return;
    }
    
    // Search in message content
    const chatContent = document.getElementById('chat-content');
    const messageElements = chatContent.querySelectorAll('[class*="bg-"][class*="rounded-lg"]');
    let matchCount = 0;
    let firstMatch = null;
    
    messageElements.forEach(messageEl => {
        const textContent = messageEl.textContent.toLowerCase();
        if (textContent.includes(query.toLowerCase())) {
            messageEl.classList.add('chat-message-highlight');
            matchCount++;
            if (!firstMatch) {
                firstMatch = messageEl;
            }
        }
    });
    
    // Update results info
    searchCount.textContent = matchCount;
    resultsInfo.classList.remove('hidden');
    
    // Scroll to first match
    if (firstMatch) {
        firstMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>

<style>
.chat-message-highlight {
    background-color: #fef3cd !important;
    border: 2px solid #f59e0b !important;
    animation: highlight-pulse 2s ease-in-out;
}

@keyframes highlight-pulse {
    0%, 100% { 
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
    }
    50% { 
        box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
    }
}

/* Dark mode search input styling */
#chat-search-input:focus {
    box-shadow: 0 0 0 2px rgba(156, 163, 175, 0.5);
}

/* Order info message styling */
.order-info-message {
    font-size: 14px;
    line-height: 1.5;
    color: #374151;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 12px;
    margin: 4px 0;
}

.order-info-message .font-bold {
    font-weight: 600;
}

.order-info-message .text-blue-500 {
    color: #3b82f6;
    font-weight: 500;
}
</style>

<script src="{{ asset('js/chat.js') }}"></script>