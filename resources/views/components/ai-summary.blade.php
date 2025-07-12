{{-- AI Summary Component --}}
<div id="ai-summary-container" data-book-id="{{ $book->id }}" class="ai-summary-section">
    {{-- Content will be loaded by JavaScript --}}
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="animate-pulse">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                    <div class="w-8 h-8 bg-blue-300 rounded-full"></div>
                </div>
                <div class="h-6 bg-gray-200 rounded-lg w-1/2 mx-auto mb-3"></div>
                <div class="h-4 bg-gray-100 rounded w-3/4 mx-auto mb-6"></div>
                <div class="h-10 bg-gray-200 rounded-xl w-40 mx-auto"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/ai-summary-simple.js') }}"></script>
@endpush

<style>
.glass {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.ai-summary-section .toggle-summary-section:hover {
    color: #2563eb;
}

.ai-summary-section .toggle-summary-section svg {
    transition: transform 0.2s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.ai-summary-section > div {
    animation: fadeIn 0.3s ease;
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease;
}

/* Scrollbar Styling */
.scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: #d1d5db #f3f4f6;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Chat Message Styling */
.chat-message-user {
    color: white !important;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.chat-message-user p {
    color: white !important;
    margin: 0 !important;
}

/* Ensure text visibility in chat bubbles */
#chat-messages .text-white {
    color: white !important;
    -webkit-text-fill-color: white !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

#chat-messages .text-white p {
    color: white !important;
    -webkit-text-fill-color: white !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

/* User message bubble styling */
#chat-messages .bg-gradient-to-r.from-blue-500.to-blue-600 {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
}

#chat-messages .bg-gradient-to-r.from-blue-500.to-blue-600 p {
    color: white !important;
    -webkit-text-fill-color: white !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    font-weight: 500;
}
    scrollbar-color: #d1d5db #f3f4f6;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Hover effects */
.ai-summary-section .toggle-summary-section:hover svg {
    transform: scale(1.1);
}

/* Focus states */
.chat-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button hover animations */
.send-chat-btn:hover {
    transform: scale(1.05) translateY(-1px);
}

.regenerate-summary-btn:hover {
    transform: translateY(-1px);
}

.generate-summary-btn:hover {
    transform: translateY(-2px);
}

/* Card hover effects */
.ai-summary-section .bg-white:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

/* Responsive improvements */
@media (max-width: 640px) {
    .ai-summary-section .max-w-4xl {
        padding: 0 1rem;
    }
    
    .ai-summary-section .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .ai-summary-section .text-2xl {
        font-size: 1.5rem;
    }
    
    .ai-summary-section .p-12 {
        padding: 2rem;
    }
}

/* Loading animation improvements */
@keyframes pulse-soft {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.animate-pulse-soft {
    animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Gradient backgrounds */
.bg-gradient-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-chat {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
</style>
