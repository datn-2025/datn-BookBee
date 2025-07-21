// Chat Real-time JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Scroll to bottom function
    function scrollToBottom() {
        const chatContainer = document.getElementById('chat-conversation');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    // Auto scroll to bottom when page loads
    setTimeout(scrollToBottom, 100);

    // Auto scroll when new message arrives
    window.addEventListener('livewire:update', function() {
        setTimeout(scrollToBottom, 100);
    });

    // Listen for Livewire browser events
    window.addEventListener('scroll-to-bottom', function() {
        setTimeout(scrollToBottom, 100);
    });

    window.addEventListener('message-sent', function() {
        // Focus back to input after sending
        const messageInput = document.querySelector('input[wire\\:model="messageInput"]');
        if (messageInput) {
            messageInput.focus();
        }
        scrollToBottom();
    });

    // Handle image modal
    window.showImageModal = function(src, title) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('imageModalLabel');
        
        if (modal && modalImage) {
            modalImage.src = src;
            modalTitle.textContent = title || 'Image';
            
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
    };

    // Handle message input keydown
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            const messageInput = document.querySelector('input[wire\\:model="messageInput"]');
            if (messageInput && document.activeElement === messageInput && messageInput.value.trim()) {
                e.preventDefault();
                // Let Livewire handle the send
            }
        }
    });

    // Copy message functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.copy-message')) {
            e.preventDefault();
            const messageContainer = e.target.closest('.user-chat-content');
            const messageContent = messageContainer.querySelector('.ctext-content');
            
            if (messageContent) {
                const text = messageContent.textContent || messageContent.innerText;
                navigator.clipboard.writeText(text).then(function() {
                    // Show copy alert
                    const alert = document.getElementById('copyClipBoard');
                    if (alert) {
                        alert.style.display = 'block';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 2000);
                    }
                });
            }
        }
    });

    // Auto focus on message input when conversation loads
    const messageInput = document.querySelector('input[wire\\:model="messageInput"]');
    if (messageInput) {
        messageInput.focus();
    }
});

// Listen for Livewire events
document.addEventListener('livewire:load', function() {
    
    // Listen for message sent event
    Livewire.on('messageSent', function() {
        // Scroll to bottom after message sent
        setTimeout(function() {
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }, 100);
    });

    // Listen for file uploaded event  
    Livewire.on('fileUploaded', function() {
        setTimeout(function() {
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }, 100);
    });
});

// Echo listening (if using Laravel Echo)
if (window.Echo && window.currentConversationId) {
    window.Echo.channel(`bookbee.${window.currentConversationId}`)
        .listen('MessageSent', (e) => {
            console.log('New message received:', e);
            
            // Auto scroll to bottom when receiving new message
            setTimeout(function() {
                const chatContainer = document.getElementById('chat-conversation');
                if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            }, 200);
        });
}
