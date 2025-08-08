/**
 * Chat Realtime JavaScript Module
 * 
 * Quản lý các chức năng chat realtime bao gồm:
 * - Gửi/nhận tin nhắn realtime qua Laravel Echo & Pusher
 * - Xử lý UI interactions (scroll, typing, file upload)
 * - Đồng bộ với Livewire components
 * - Quản lý trạng thái conversation
 */

class ChatRealtime {
    constructor() {
        this.currentConversationId = null;
        this.isTyping = false;
        this.typingTimeout = null;
        this.messageContainer = null;
        this.messageInput = null;
        this.fileUploadInput = null;
        this.currentUserId = null;
        this.echoChannels = new Map();

        this.init();
    }

    /**
     * Khởi tạo chat realtime
     */
    init() {
        // Đợi DOM sẵn sàng trước
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeElements();
                this.initializeEcho();
                this.bindEvents();
                this.setupLivewireListeners();
            });
        } else {
            // DOM đã sẵn sàng
            this.initializeElements();
            this.initializeEcho();
            this.bindEvents();
            this.setupLivewireListeners();
        }

        // Đợi Livewire sẵn sàng
        document.addEventListener('livewire:initialized', () => {
            this.setupLivewireListeners();
        });
    }

    /**
     * Khởi tạo các DOM elements
     */
    initializeElements() {
        
        // Các elements chính
        this.messageContainer = document.getElementById('message-container');
        this.messageInput = document.querySelector('#messageInputField') || 
                           document.querySelector('[wire\\:model="message_content"]');
        this.fileUploadInput = document.querySelector('[wire\\:model="fileUpload"]');
        this.chatConversation = document.getElementById('chat-conversation');

        // // Debug log elements found
        // console.log('💬 ChatRealtime: Elements found:', {
        //     messageContainer: !!this.messageContainer,
        //     messageInput: !!this.messageInput,
        //     messageInputId: this.messageInput ? this.messageInput.id : 'NOT_FOUND',
        //     fileUploadInput: !!this.fileUploadInput,
        //     chatConversation: !!this.chatConversation
        // });

        // Check for any existing wire:keydown.enter bindings
        if (this.messageInput) {
            const wireKeydown = this.messageInput.getAttribute('wire:keydown.enter');
            if (wireKeydown) {
                console.warn('💬 ChatRealtime: Found wire:keydown.enter binding:', wireKeydown);
            }
        }

        // Lấy conversation ID từ window hoặc DOM
        this.currentConversationId = window.currentConversationId || 
                                   document.querySelector('[data-conversation-id]')?.dataset.conversationId;

        // Lấy current user ID từ meta tag hoặc global variable
        const userMeta = document.querySelector('meta[name="user-id"]');
        this.currentUserId = userMeta ? userMeta.content : window.currentUserId;

    }

    /**
     * Khởi tạo Laravel Echo connections
     */
    initializeEcho() {
        if (!window.Echo) {
            console.error('💬 ChatRealtime: Laravel Echo not available');
            return;
        }
        
        // Debug Pusher connection như chat.js
        if (window.Echo.connector && window.Echo.connector.pusher) {
            const pusher = window.Echo.connector.pusher;

            pusher.connection.bind('disconnected', () => {
                console.log('💬 ChatRealtime: Pusher disconnected');
            });
            
            pusher.connection.bind('error', (error) => {
                console.error('💬 ChatRealtime: Pusher connection error:', error);
            });
        }

        // Setup global channels first
        this.setupGlobalChannels();

        if (!this.currentConversationId) {
            console.warn('💬 ChatRealtime: No conversation ID found, skipping conversation channels');
            return;
        }

        // Đợi một chút để đảm bảo Echo sẵn sàng như chat.js
        setTimeout(() => {
            this.setupConversationChannel(this.currentConversationId);
        }, 100);
    }

    /**
     * Thiết lập channel cho conversation cụ thể
     */
    setupConversationChannel(conversationId) {
        if (!conversationId) return;

        // Hủy channel cũ nếu có
        this.leaveConversationChannel();

        const channels = [
            `conversations.${conversationId}`,
            `bookbee.${conversationId}`
        ];

        channels.forEach(channelName => {
            try {
                const channel = window.Echo.channel(channelName);
                
                // IMPORTANT: Listen BEFORE subscription callbacks
                channel.listen('MessageSent', (data) => {
                                     
                    this.handleIncomingMessage(data);
                });

                channel.listen('UserTyping', (data) => {
                    console.log('💬 ChatRealtime: Received UserTyping', data);
                    this.handleTypingIndicator(data);
                });

                // Đăng ký callback cho subscription_succeeded SAU khi đã listen
                channel.subscribed(() => {
                    console.log(`💬 ChatRealtime: Successfully subscribed to ${channelName}`);
                });

                // Đăng ký callback cho subscription_error
                channel.error((error) => {
                    console.error(`💬 ChatRealtime: Subscription error for ${channelName}:`, error);
                });

                // BACKUP: Direct Pusher binding như chat.js để đảm bảo callback được đăng ký
                if (window.Echo.connector && window.Echo.connector.pusher) {
                    const pusher = window.Echo.connector.pusher;
                    const pusherChannel = pusher.subscribe(channelName);
                    
                    pusherChannel.bind('MessageSent', (data) => {
                    
                        this.handleIncomingMessage(data);
                    });

                }

                this.echoChannels.set(channelName, channel);

            } catch (error) {
                console.error(`💬 ChatRealtime: Error subscribing to ${channelName}:`, error);
            }
        });

        this.currentConversationId = conversationId;
    }

    /**
     * Thiết lập các channel global
     */
    setupGlobalChannels() {
        // Channel cho user status
        try {
           const userStatusChannel = window.Echo.channel('user-status');
            
            userStatusChannel.listen('UserSessionChanged', (data) => {
                console.log('💬 ChatRealtime: Received UserSessionChanged', data);
                this.handleUserStatusChange(data);
            });
            
            this.echoChannels.set('user-status', userStatusChannel);
        } catch (error) {
            console.error('💬 ChatRealtime: Error setting up user-status channel:', error);
        }

        // Channel global cho bookbee với backup Pusher binding
        try {
            const globalChannel = window.Echo.channel('bookbee.global');
            
            globalChannel.listen('MessageSent', (data) => {
                console.log('💬 ChatRealtime: Received MessageSent on global channel via Echo', data);
                this.handleGlobalMessageUpdate(data);
            });

            // BACKUP: Direct Pusher binding cho global channel
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                const pusherGlobalChannel = pusher.subscribe('bookbee.global');
                
                pusherGlobalChannel.bind('MessageSent', (data) => {
                   this.handleGlobalMessageUpdate(data);
                });

                pusherGlobalChannel.bind('pusher:subscription_succeeded', () => {
                    console.log('💬 ChatRealtime: Direct Pusher subscription successful for bookbee.global');
                });
            }
            
            this.echoChannels.set('bookbee.global', globalChannel);
       } catch (error) {
            console.error('💬 ChatRealtime: Error setting up global channel:', error);
        }
    }

    /**
     * Rời khỏi conversation channel hiện tại
     */
    leaveConversationChannel() {
        if (this.currentConversationId) {
            const channels = [
                `conversations.${this.currentConversationId}`,
                `bookbee.${this.currentConversationId}`
            ];

            channels.forEach(channelName => {
                const channel = this.echoChannels.get(channelName);
                if (channel) {
                    try {
                        window.Echo.leave(channelName);
                        this.echoChannels.delete(channelName);
                        console.log(`💬 ChatRealtime: Left channel ${channelName}`);
                    } catch (error) {
                        console.error(`💬 ChatRealtime: Error leaving ${channelName}:`, error);
                    }
                }
            });
        }
    }

    /**
     * Xử lý tin nhắn đến
     */
    handleIncomingMessage(data) {


        // Bỏ qua tin nhắn từ chính mình
        if (data.sender_id && data.sender_id == this.currentUserId) {
            console.log('💬 ChatRealtime: Skipping own message');
            return;
        }

        // Bỏ qua nếu không phải conversation hiện tại
        if (data.conversation_id && data.conversation_id != this.currentConversationId) {
            // Chỉ refresh conversation list
            this.refreshConversationList();
            return;
        }


        // Thông báo cho Livewire component để refresh (quan trọng!)
        if (window.Livewire) {
            try {
                // Tìm component ChatRealtime và gọi method handleIncomingMessage
                const component = window.Livewire.find(this.findChatRealtimeComponentId());
                if (component) {
                    console.log('💬 ChatRealtime: Calling Livewire handleIncomingMessage');
                    component.call('handleIncomingMessage', data);
                } else {
                    // Fallback: dispatch event
                    console.log('💬 ChatRealtime: Using fallback dispatch');
                    window.Livewire.dispatch('handleIncomingMessage', { payload: data });
                }
            } catch (error) {
                console.error('💬 ChatRealtime: Error notifying Livewire:', error);
            }
        }

        // Scroll to bottom sau khi Livewire update (quan trọng để timing đúng!)
        setTimeout(() => {
            // console.log('💬 ChatRealtime: Scrolling after incoming message');
            this.scrollToBottom();
            this.showMessageNotification(data);
        }, 100); // Giảm delay xuống để responsive hơn
    }

    /**
     * Xử lý typing indicator
     */
    handleTypingIndicator(data) {
        if (data.user_id == this.currentUserId) return;

        const typingIndicator = document.getElementById('typing-indicator');
        if (!typingIndicator) return;

        if (data.is_typing) {
            typingIndicator.style.display = 'block';
            typingIndicator.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2">
                        <div class="avatar-title bg-light rounded-circle">
                            ${data.user_name ? data.user_name.charAt(0).toUpperCase() : 'U'}
                        </div>
                    </div>
                    <div class="typing-animation">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                    <small class="text-muted ms-2">${data.user_name || 'Someone'} đang nhập...</small>
                </div>
            `;
            this.scrollToBottom();
        } else {
            typingIndicator.style.display = 'none';
        }
    }

    /**
     * Xử lý thay đổi trạng thái user
     */
    handleUserStatusChange(data) {
        // Show toast notification
        if (data.message && data.type) {
            this.showToast(data.message, data.type);
        }

        // Update UI elements nếu cần
        this.updateUserStatus(data.user);
    }

    /**
     * Xử lý cập nhật tin nhắn global
     */
    handleGlobalMessageUpdate(data) {
        // Chỉ refresh conversation list, không cần thêm tin nhắn
        this.refreshConversationList();
    }

    /**
     * Bind các events cho UI
     */
    bindEvents() {
        // Enter key để gửi tin nhắn
        if (this.messageInput) {
                   
            // Remove existing event listeners để tránh duplicate
            this.messageInput.removeEventListener('keydown', this.handleKeydownEvent);
            
            // Create bound method to avoid context issues
            this.handleKeydownEvent = (e) => {
                console.log('💬 ChatRealtime: Keydown event triggered:', e.key);
                
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    console.log('💬 ChatRealtime: Enter key detected, calling sendMessage');
                    this.sendMessage();
                } else if (e.key === 'Enter' && e.shiftKey) {
                    // Cho phép xuống dòng với Shift+Enter
                    console.log('💬 ChatRealtime: Shift+Enter detected, allowing new line');
                    return;
                } else {
                    // Trigger typing indicator
                    this.handleTyping();
                }
            };
            
            this.messageInput.addEventListener('keydown', this.handleKeydownEvent);

            // Handle paste events (cho việc paste hình ảnh)
            this.messageInput.addEventListener('paste', (e) => {
                console.log('💬 ChatRealtime: Paste event detected');
                this.handlePaste(e);
            });
        } else {
            console.warn('💬 ChatRealtime: Message input not found for event binding');
        }

        // File upload events
        if (this.fileUploadInput) {
            this.fileUploadInput.addEventListener('change', (e) => {
                console.log('💬 ChatRealtime: File input changed');
                if (e.target.files.length > 0) {
                    this.handleFileUpload(e.target.files[0]);
                }
            });
        }

        // Drag and drop cho file upload
        if (this.chatConversation) {
            this.chatConversation.addEventListener('dragover', (e) => {
                e.preventDefault();
                this.chatConversation.classList.add('drag-over');
            });

            this.chatConversation.addEventListener('dragleave', (e) => {
                e.preventDefault();
                this.chatConversation.classList.remove('drag-over');
            });

            this.chatConversation.addEventListener('drop', (e) => {
                e.preventDefault();
                this.chatConversation.classList.remove('drag-over');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    this.handleFileUpload(files[0]);
                }
            });
        }

        // Copy message events
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('copy-message')) {
                this.copyMessage(e.target.dataset.message);
            }
        });

        // Image modal events
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('message-image')) {
                this.showImageModal(e.target.src);
            }
        });
    }

    /**
     * Thiết lập Livewire listeners
     */
    setupLivewireListeners() {
        if (!window.Livewire) return;

        // Listen for Livewire events
        window.Livewire.on('scroll-to-bottom', () => {
            this.scrollToBottom();
        });

        window.Livewire.on('message-sent', () => {
            this.handleMessageSent();
        });

        window.Livewire.on('conversation-changed', (data) => {
           this.handleConversationChange(data[0]);
        });

        window.Livewire.on('file-uploaded', () => {
            this.handleFileUploaded();
        });

        // Listen for Livewire updates và auto scroll như chat.js
        document.addEventListener('livewire:updated', () => {
            // Đợi một chút để DOM cập nhật xong
            setTimeout(() => {
                this.scrollToBottom();
            }, 50);
        });

        // Listen for message updates specifically
        document.addEventListener('livewire:updated.message-container', () => {
           this.scrollToBottom();
        });

    }

    /**
     * Gửi tin nhắn (hỗ trợ cả text và file) - Enhanced với debug logging
     */
    sendMessage() {
        console.log('💬 ChatRealtime: sendMessage() called');
        
        if (!this.messageInput) {
            console.error('💬 ChatRealtime: Message input not found');
            return;
        }

        const content = this.messageInput.value.trim();
        const hasFile = this.fileUploadInput && this.fileUploadInput.files && this.fileUploadInput.files.length > 0;
        
        console.log('💬 ChatRealtime: Message details:', {
            content: content,
            contentLength: content.length,
            hasFile: hasFile,
            fileCount: hasFile ? this.fileUploadInput.files.length : 0
        });
        
        // Phải có nội dung hoặc file mới được gửi
        if (!content && !hasFile) {
            console.warn('💬 ChatRealtime: No content or file to send');
            return;
        }

        console.log('💬 ChatRealtime: Proceeding to send message', { content, hasFile });
 
        // Clear input ngay lập tức để tránh gửi trùng
        this.messageInput.value = '';

        // Gọi Livewire method
        if (window.Livewire) {
            try {
                const componentId = this.findChatRealtimeComponentId();
                console.log('💬 ChatRealtime: Found component ID:', componentId);
                
                const component = window.Livewire.find(componentId);
                if (component) {
                    console.log('💬 ChatRealtime: Component found, setting data and calling sendMessage');
                    
                    // Set content nếu có
                    if (content) {
                        component.set('message_content', content);
                        console.log('💬 ChatRealtime: Set message_content:', content);
                    }
                    
                    // Gọi sendMessage method - Livewire sẽ tự handle cả text và file
                    console.log('💬 ChatRealtime: Calling component.call("sendMessage")');
                    component.call('sendMessage');
                } else {
                    // Fallback: dispatch event
                    console.log('💬 ChatRealtime: Using fallback dispatch method');
                    window.Livewire.dispatch('sendMessage', { 
                        message_content: content,
                        hasFile: hasFile 
                    });
                }
            } catch (error) {
                console.error('💬 ChatRealtime: Error sending message:', error);
                // Khôi phục nội dung nếu có lỗi
                this.messageInput.value = content;
            }
        } else {
            console.error('💬 ChatRealtime: Livewire not available');
            // Khôi phục nội dung nếu Livewire không có
            this.messageInput.value = content;
        }
    }

    /**
     * Xử lý typing indicator
     */
    handleTyping() {
        if (this.isTyping) return;

        this.isTyping = true;
        
        // Broadcast typing event
        if (window.Echo && this.currentConversationId) {
            // Implementation sẽ cần backend support
        }

        // Reset typing sau 3 giây
        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            this.isTyping = false;
        }, 3000);
    }

    /**
     * Xử lý paste (cho hình ảnh)
     */
    handlePaste(e) {
        const items = e.clipboardData.items;
        
        for (let item of items) {
            if (item.type.indexOf('image') !== -1) {
                e.preventDefault();
                const file = item.getAsFile();
                this.handleFileUpload(file);
                break;
            }
        }
    }

    /**
     * Xử lý file upload
     */
    handleFileUpload(file) {
 
        // Validation
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            this.showToast('File quá lớn (tối đa 10MB)', 'error');
            return;
        }

        // Set file to Livewire component
        if (window.Livewire && this.fileUploadInput) {
            try {
                // Trigger Livewire file upload
                const component = window.Livewire.find(this.findChatRealtimeComponentId());
                if (component) {
                    // Livewire sẽ handle file upload
                    this.fileUploadInput.files = [file];
                    this.fileUploadInput.dispatchEvent(new Event('change'));
                }
            } catch (error) {
                console.error('💬 ChatRealtime: Error uploading file:', error);
                this.showToast('Lỗi khi upload file', 'error');
            }
        }
    }

    /**
     * Scroll to bottom của chat
     */
    scrollToBottom() {
        if (this.chatConversation) {
      // Method từ chat.js - Direct assignment
            this.chatConversation.scrollTop = this.chatConversation.scrollHeight;
            
      } else {
            console.warn('💬 ChatRealtime: Chat conversation element not found for scrolling');
        }
    }

    /**
     * Copy tin nhắn
     */
    copyMessage(message) {
        navigator.clipboard.writeText(message).then(() => {
            this.showCopyAlert();
        }).catch(err => {
            console.error('💬 ChatRealtime: Could not copy message:', err);
        });
    }

    /**
     * Show copy alert
     */
    showCopyAlert() {
        const alert = document.getElementById('copyClipBoard');
        if (alert) {
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 2000);
        }
    }

    /**
     * Show image modal
     */
    showImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        if (modal) {
            const modalBody = modal.querySelector('.modal-content');
            modalBody.innerHTML = `
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${imageSrc}" class="img-fluid" alt="Image">
                </div>
            `;
            
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    /**
     * Show message notification
     */
    showMessageNotification(data) {
        // Có thể implement browser notification ở đây
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(`Tin nhắn mới từ ${data.sender?.name || 'Unknown'}`, {
                body: data.content,
                icon: '/favicon.ico'
            });
        }
    }

    /**
     * Update user status UI
     */
    updateUserStatus(user) {
        if (!user) return;

        // Update conversation list status indicators
        const conversationItem = document.querySelector(`[data-user-id="${user.id}"]`);
        if (conversationItem) {
            const statusIndicator = conversationItem.querySelector('.status-indicator');
            if (statusIndicator) {
                statusIndicator.className = `status-indicator ${user.status === 'online' ? 'online' : 'offline'}`;
            }
        }
    }

    /**
     * Refresh conversation list
     */
    refreshConversationList() {
        if (window.Livewire) {
            // Dispatch event to ConversationList component
            window.Livewire.dispatch('refreshConversations');
        }
    }

    /**
     * Handle conversation change
     */
    handleConversationChange(conversationId) {       
        // Update current conversation
        this.setupConversationChannel(conversationId);
        
        // Update global variable
        window.currentConversationId = conversationId;
        
        // Scroll to bottom
        setTimeout(() => this.scrollToBottom(), 200);
    }

    /**
     * Handle message sent
     */
    handleMessageSent() {
        // console.log('💬 ChatRealtime: Message sent successfully');
        
        // Force clear input multiple ways
        if (this.messageInput) {
            this.messageInput.value = '';
            this.messageInput.dispatchEvent(new Event('input')); // Trigger any input listeners
        }

        // Also clear Livewire model
        if (window.Livewire) {
            try {
                const component = window.Livewire.find(this.findChatRealtimeComponentId());
                if (component) {
                    component.set('message_content', '');
                }
            } catch (error) {
                console.warn('💬 ChatRealtime: Could not clear Livewire model:', error);
            }
        }
        
        // Scroll to bottom với độ trễ để đảm bảo DOM đã cập nhật
        setTimeout(() => {
            this.scrollToBottom();
        }, 100);
        
    }

    /**
     * Handle file uploaded
     */
    handleFileUploaded() {
        console.log('💬 ChatRealtime: File uploaded successfully');
        
        // Clear file input
        if (this.fileUploadInput) {
            this.fileUploadInput.value = '';
        }
        
        // Scroll to bottom
        this.scrollToBottom();
        
        // Show feedback
        this.showToast('File đã được gửi', 'success');
    }

    /**
     * Find ChatRealtime Livewire component ID
     */
    findChatRealtimeComponentId() {
        // Method 1: Tìm element có wire:id trong chat content
        const chatElements = document.querySelectorAll('[wire\\:id]');
        for (let element of chatElements) {
            const wireId = element.getAttribute('wire:id');
            if (wireId) {
                 return wireId;
            }
        }

        // Method 2: Tìm component qua Livewire.all()
        if (window.Livewire && window.Livewire.all) {
            const components = window.Livewire.all();        
            for (let component of components) {
                // Kiểm tra nếu component có method cần thiết
                if (component.name && component.name.includes('ChatRealtime')) {
                    console.log('💬 ChatRealtime: Found ChatRealtime component by name', component.id);
                    return component.id;
                }
            }
            
            // Fallback: lấy component đầu tiên
            if (components.length > 0) {
                console.log('💬 ChatRealtime: Using first available component', components[0].id);
                return components[0].id;
            }
        }

        console.warn('💬 ChatRealtime: No Livewire component found');
        return null;
    }

    /**
     * Switch to conversation
     */
    switchConversation(conversationId) {
      
        if (window.Livewire) {
            const component = window.Livewire.find(this.findChatRealtimeComponentId());
            if (component) {
                component.call('switchConversation', conversationId);
            }
        }
        
        this.handleConversationChange(conversationId);
    }

    /**
     * Debug method để kiểm tra callbacks
     */
    debugChannelCallbacks() {
        if (!window.Echo || !window.Echo.connector || !window.Echo.connector.pusher) {
            console.error('💬 ChatRealtime: Pusher not available for debugging');
            return;
        }

        const pusher = window.Echo.connector.pusher;
        const channels = pusher.channels.channels;
        
        // console.log('💬 ChatRealtime: Debugging channel callbacks');
        
        Object.keys(channels).forEach(channelName => {
            const channel = channels[channelName];
            console.log(`💬 ChatRealtime: Channel ${channelName}:`, {
                callbacks: channel.callbacks,
                MessageSent: channel.callbacks['MessageSent'] || 'NO CALLBACKS'
            });
        });
    }

    /**
     * Force re-setup callbacks nếu bị mất
     */
    forceSetupCallbacks() {
        if (!this.currentConversationId) {
            console.warn('💬 ChatRealtime: No conversation ID to setup callbacks');
            return;
        }

        // console.log('💬 ChatRealtime: Force re-setting up callbacks');
        
        // Re-setup conversation channels
        this.setupConversationChannel(this.currentConversationId);
        
        // Re-setup global channels
        this.setupGlobalChannels();
    }

    /**
     * Setup admin search functionality
     */
    setupAdminSearch() {
        const searchInput = document.getElementById('searchMessage');
        const clearButton = document.getElementById('admin-clear-search');
        const resultsInfo = document.getElementById('admin-search-results-info');
        const instructions = document.getElementById('admin-search-instructions');
        const searchCount = document.getElementById('admin-search-count');
        const searchDropdown = document.getElementById('admin-search-dropdown');
        const searchToggle = document.getElementById('admin-search-toggle');
        
        if (!searchInput) {
            console.warn('💬 ChatRealtime: Search input not found');
            return;
        }
        
       
        let searchTimeout;
        
        // Prevent dropdown from closing when clicking inside
        if (searchDropdown) {
            searchDropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
        
        // Prevent input field from closing dropdown
        searchInput.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        
        // Focus input when dropdown is shown
        if (searchToggle) {
            searchToggle.addEventListener('click', () => {
                setTimeout(() => {
                    searchInput.focus();
                }, 100);
            });
        }
        
        // Handle Bootstrap dropdown events
        if (searchDropdown) {
            // Listen for Bootstrap dropdown show event
            searchDropdown.addEventListener('shown.bs.dropdown', () => {
                console.log('💬 ChatRealtime: Search dropdown shown');
                searchInput.focus();
            });
            
            // Prevent dropdown from hiding when clicking inside search area
            searchDropdown.addEventListener('hide.bs.dropdown', (e) => {
                // Only prevent if clicked inside search area
                if (e.clickEvent && searchDropdown.contains(e.clickEvent.target)) {
                    e.preventDefault();
                    console.log('💬 ChatRealtime: Prevented dropdown close from inside click');
                }
            });
        }
        
        // Search input handler with debounce
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            // Show/hide clear button and instructions
            if (query) {
                clearButton?.classList.remove('d-none');
                instructions?.classList.add('d-none');
            } else {
                clearButton?.classList.add('d-none');
                instructions?.classList.remove('d-none');
                resultsInfo?.classList.add('d-none');
            }
            
            // Debounced search
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.performAdminSearch(query);
            }, 300);
        });
        
        // Enter key to search immediately
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                this.performAdminSearch(e.target.value.trim());
            }
        });
        
        // Clear search handler
        clearButton?.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent dropdown close
            searchInput.value = '';
            clearButton.classList.add('d-none');
            instructions?.classList.remove('d-none');
            resultsInfo?.classList.add('d-none');
            this.clearAdminSearchHighlights();
            this.scrollToBottom();
            searchInput.focus(); // Keep focus on input
        });
        
    }

    /**
     * Perform admin search
     */
    performAdminSearch(query) {
        const resultsInfo = document.getElementById('admin-search-results-info');
        const searchCount = document.getElementById('admin-search-count');
        
        // Clear previous highlights
        this.clearAdminSearchHighlights();
        
        if (!query) {
            resultsInfo?.classList.add('d-none');
            return;
        }
        
        
        // Search in message content
        const chatContainer = document.getElementById('chat-conversation');
        if (!chatContainer) {
            console.warn('💬 ChatRealtime: Chat container not found for search');
            return;
        }
        
        const messageElements = chatContainer.querySelectorAll('.ctext-wrap-content');
        let matchCout = 0;
        let firstMatch = null;
        
        messageElements.forEach(messageEl => {
            const textContent = messageEl.textContent.toLowerCase();
            if (textContent.includes(query.toLowerCase())) {
                messageEl.classList.add('admin-message-highlight');
                matchCount++;
                if (!firstMatch) {
                    firstMatch = messageEl;
                }
            }
        });
        
        // Update results info
        if (searchCount) {
            searchCount.textContent = matchCount;
        }
        resultsInfo?.classList.remove('d-none');
        
        // Scroll to first match
        if (firstMatch) {
            firstMatch.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Add extra highlight to first match
            setTimeout(() => {
                firstMatch.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    firstMatch.style.transform = 'scale(1)';
                }, 500);
            }, 100);
        }
        
    }

    /**
     * Clear admin search highlights
     */
    clearAdminSearchHighlights() {
        document.querySelectorAll('.admin-message-highlight').forEach(el => {
            el.classList.remove('admin-message-highlight');
            el.style.transform = '';
        });
    }

    /**
     * Legacy search function for backward compatibility
     */
    searchMessages() {
        const searchInput = document.getElementById('searchMessage');
        if (searchInput) {
            this.performAdminSearch(searchInput.value.trim());
        }
    }

    /**
     * Cleanup when destroying
     */
    destroy() {
        
        // Leave all channels
        this.echoChannels.forEach((channel, channelName) => {
            try {
                window.Echo.leave(channelName);
            } catch (error) {
                console.error(`💬 ChatRealtime: Error leaving ${channelName}:`, error);
            }
        });
        
        this.echoChannels.clear();
        
        // Clear timeouts
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }
    }
}

// Global functions for backward compatibility
window.handleEnterKey = function(event) {
    
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
         
        if (window.chatRealtime) {
             window.chatRealtime.sendMessage();
        } else {
             // Fallback cho trường hợp chatRealtime chưa khởi tạo
            const messageInput = document.getElementById('messageInputField');
            const fileInput = document.getElementById('fileUpload');
            
            if (messageInput) {
                const content = messageInput.value.trim();
                const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
                
                if (content || hasFile) {
                    // Gọi Livewire trực tiếp
                    if (window.Livewire && window.Livewire.first) {
                        try {
                            const component = window.Livewire.first();
                            if (component) {
                                if (content) {
                                    component.set('message_content', content);
                                }
                                component.call('sendMessage');
                                messageInput.value = '';
                            }
                        } catch (error) {
                            console.error('💬 Fallback handleEnterKey error:', error);
                        }
                    }
                } else {
                    console.log('💬 Global handleEnterKey: No content or file to send');
                }
            }
        }
    }
};

window.switchConversation = function(conversationId) {
    if (window.chatRealtime) {
        window.chatRealtime.switchConversation(conversationId);
    }
};

// Global search functions for backward compatibility
window.setupAdminSearch = function() {
    if (window.chatRealtime) {
        window.chatRealtime.setupAdminSearch();
    }
};

window.performAdminSearch = function(query) {
    if (window.chatRealtime) {
        window.chatRealtime.performAdminSearch(query);
    }
};

window.clearAdminSearchHighlights = function() {
    if (window.chatRealtime) {
        window.chatRealtime.clearAdminSearchHighlights();
    }
};

window.searchMessages = function() {
    if (window.chatRealtime) {
        window.chatRealtime.searchMessages();
    }
};

// Global scrollToBottom for backward compatibility
window.scrollToBottomChat = function() {
    if (window.chatRealtime) {
        window.chatRealtime.scrollToBottom();
    }
};

// Initialize chat realtime when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
       
        // Debug: Check available elements
        // console.log('💬 ChatRealtime: Available elements check:', {
        //     messageInputField: !!document.getElementById('messageInputField'),
        //     wireModelInput: !!document.querySelector('[wire\\:model="message_content"]'),
        //     chatConversation: !!document.getElementById('chat-conversation'),
        //     messageContainer: !!document.getElementById('message-container'),
        //     searchMessage: !!document.getElementById('searchMessage'),
        //     adminSearchToggle: !!document.getElementById('admin-search-toggle'),
        //     adminSearchDropdown: !!document.getElementById('admin-search-dropdown'),
        //     currentConversationId: window.currentConversationId,
        //     livewireAvailable: !!window.Livewire,
        //     echoAvailable: !!window.Echo
        // });
        
        window.chatRealtime = new ChatRealtime();
        
        // Setup admin search after ChatRealtime initialization
        if (window.chatRealtime) {
            window.chatRealtime.setupAdminSearch();
        }
    });
} else {
    
    window.chatRealtime = new ChatRealtime();
    
    // Setup admin search after ChatRealtime initialization
    if (window.chatRealtime) {
        window.chatRealtime.setupAdminSearch();
    }
}

// Also initialize when Livewire is loaded (if not already)
document.addEventListener('livewire:initialized', function() {
    if (!window.chatRealtime) {
            window.chatRealtime = new ChatRealtime();
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.chatRealtime) {
        window.chatRealtime.destroy();
    }
});

console.log('💬 ChatRealtime: Module loaded successfully');