// Lắng nghe public channel 'user-status' - chỉ khi có Echo
if (window.Echo) {
    window.Echo.channel('user-status')
        .listen('UserSessionChanged', (e) => {
            // Cập nhật trạng thái user trên giao diện
            if (e.user && e.user.id) {
                // Tìm tất cả phần tử hiển thị trạng thái user này
                document.querySelectorAll(`[data-user-id="${e.user.id}"]`).forEach(el => {
                    if (e.user.status === 'online') {
                        el.innerHTML = '<span class="badge bg-success">Online</span>';
                    } else if (e.user.last_seen) {
                        if (window.moment) {
                            el.innerHTML = `<span class="badge bg-warning">Hoạt động ${window.moment(e.user.last_seen).fromNow()}</span>`;
                        } else {
                            el.innerHTML = `<span class="badge bg-warning">Hoạt động ${e.user.last_seen}</span>`;
                        }
                    } else {
                        el.innerHTML = '<span class="badge bg-secondary">Offline</span>';
                    }
                });
            }
            // Hiện notification
            const notification = document.getElementById('notification');
            if (notification) {
                notification.classList.remove('invisible');
                notification.classList.remove('alert-success');
                notification.classList.remove('alert-danger');
                notification.classList.add(`alert-${e.type}`);
                notification.innerText = e.message;

                setTimeout(() => {
                    notification.classList.remove(`alert-${e.type}`);
                    notification.classList.add('invisible');
                }, 5000);
            }
        });
}

// --- CHAT WIDGET LOGIC ---
document.addEventListener('DOMContentLoaded', function () {
    const chatUserInfo = document.getElementById('chat-user-info');
    if (!chatUserInfo) return; // Không đăng nhập thì không làm gì

    const userId = chatUserInfo.dataset.userId;
    const userName = chatUserInfo.dataset.userName;
    let adminId = null;
    let conversationId = null;
    let allMessages = []; // Lưu trữ tất cả tin nhắn
    let isEchoListening = false; // Đảm bảo chỉ lắng nghe 1 lần
    let currentOrderContext = null; // Thêm biến để lưu context đơn hàng

    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatContent = document.getElementById('chat-content');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');
    const previewImage = document.getElementById('preview-image');
    const previewFilename = document.getElementById('preview-filename');
    const removeFileBtn = document.getElementById('remove-file');
    const emojiToggle = document.getElementById('emoji-toggle');
    const emojiPicker = document.getElementById('emoji-picker');

    let selectedFile = null;
    let replyToMessage = null; // Tin nhắn đang reply

    // Helper function để chuẩn hóa thời gian
    function normalizeDateTime(dateTime) {
        if (!dateTime) return new Date(0);
        
        // Nếu format không có timezone (YYYY-MM-DD HH:mm:ss), coi như là UTC
        if (typeof dateTime === 'string' && dateTime.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/)) {
            // Thêm 'T' và timezone UTC để chuẩn hóa
            return new Date(dateTime.replace(' ', 'T') + 'Z');
        }
        
        // Các format khác, parse bình thường
        return new Date(dateTime);
    }

    // Emoji list
    const emojis = [
        '😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣',
        '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰',
        '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜',
        '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏',
        '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣',
        '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠',
        '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨',
        '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥',
        '👍', '👎', '👌', '🤌', '🤏', '✌️', '🤞', '🤟',
        '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️',
        '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✍️', '💅',
        '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍',
        '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖',
        '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️'
    ];

    // Initialize emoji picker
    function initEmojiPicker() {
        const emojiGrid = emojiPicker.querySelector('.grid');
        emojiGrid.innerHTML = '';
        
        emojis.forEach(emoji => {
            const emojiBtn = document.createElement('button');
            emojiBtn.type = 'button';
            emojiBtn.textContent = emoji;
            emojiBtn.className = 'p-1 rounded cursor-pointer';
            emojiBtn.addEventListener('click', () => {
                chatInput.value += emoji;
                chatInput.focus();
                emojiPicker.classList.add('hidden');
            });
            emojiGrid.appendChild(emojiBtn);
        });
    }

    // Initialize emoji picker
    initEmojiPicker();

    // Emoji toggle handler
    emojiToggle.addEventListener('click', () => {
        emojiPicker.classList.toggle('hidden');
    });

    // Close emoji picker when clicking outside
    document.addEventListener('click', (e) => {
        if (!emojiToggle.contains(e.target) && !emojiPicker.contains(e.target)) {
            emojiPicker.classList.add('hidden');
        }
    });

    // File upload handler
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Chỉ có thể gửi file ảnh!');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File quá lớn! Vui lòng chọn file nhỏ hơn 5MB.');
            return;
        }

        selectedFile = file;
        showFilePreview(file);
    });

    // Show file preview
    function showFilePreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
            previewFilename.textContent = file.name;
            filePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // Remove file handler
    removeFileBtn.addEventListener('click', () => {
        selectedFile = null;
        fileInput.value = '';
        filePreview.classList.add('hidden');
        previewImage.style.display = 'none';
    });

    if (window.Echo) {
        
        // Setup global listener cho tất cả conversation của user này
        setupGlobalEchoListener();
    } else {
        console.error('❌ Echo is not available');
    }

    // Setup axios headers
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    window.axios.defaults.headers.common = {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    };

    // Setup global listener để bắt tất cả tin nhắn
    function setupGlobalEchoListener() {
        if (!window.Echo || isEchoListening) return;
        
        // Listen trên channel global cho user này
        const globalChannelName = 'user.' + userId;
        
        // Thử cách tiếp cận trực tiếp với Pusher thay vì Echo
        try {
            // Method 1: Sử dụng Echo như bình thường
            const globalChannel = window.Echo.channel(globalChannelName);
            
            // Đăng ký callback cho subscription success
            globalChannel.subscribed(() => {
                console.log('✅ Subscribed to global channel:', globalChannelName);
                isEchoListening = true;
                
                globalChannel.listen('MessageSent', (e) => {
                    console.log('� Global message received via Echo:', e);
                    handleIncomingMessage(e);
                });
                console.log('🎯 MessageSent listener registered successfully');
            });
            
            // Method 2: Backup với Pusher trực tiếp
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                
                const pusherChannel = pusher.subscribe(globalChannelName);
                pusherChannel.bind('MessageSent', (data) => {
                    console.log('🌐 Global message received via direct Pusher:', data);
                    handleIncomingMessage(data);
                });
                
                pusherChannel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Direct Pusher subscription successful for:', globalChannelName);
                });
            }
            
            // Đăng ký callback cho lỗi
            globalChannel.error((error) => {
                console.error('❌ Global channel subscription error:', error);
            });
            
            
        } catch (error) {
            console.error('❌ Error setting up global listener:', error);
        }
    }
    
    // Function riêng để xử lý tin nhắn đến
    function handleIncomingMessage(e) {

        
        // Chỉ xử lý nếu đang ở conversation đúng
        if (e.conversation_id === conversationId) {
            // Kiểm tra nếu tin nhắn từ người khác (admin)
            if (e.sender_id !== userId) {

                
                const incomingMessage = {
                    id: e.id,
                    sender_id: e.sender_id,
                    content: e.content,
                    created_at: e.created_at,
                    sender: e.sender,
                    type: e.type || 'text',
                    file_path: e.file_path,
                    reply_to_message_id: e.reply_to_message_id,
                    reply_to_message: e.reply_to_message,
                    replyToMessage: e.replyToMessage
                };
                
                // Nếu có reply_to_message_id nhưng không có thông tin reply, tìm trong allMessages
                if (e.reply_to_message_id && !e.reply_to_message && !e.replyToMessage) {
                    // Thử tìm kiếm với cả string và exact match
                    const originalMessage = allMessages.find(msg => 
                        msg.id === e.reply_to_message_id || 
                        msg.id.toString() === e.reply_to_message_id.toString()
                    );
                    
                    if (originalMessage) {
                        console.log('✅ Found original message:', originalMessage);
                        incomingMessage.reply_to_message = {
                            id: originalMessage.id,
                            content: originalMessage.content,
                            sender: originalMessage.sender,
                            sender_id: originalMessage.sender_id
                        };
                    } else {
                        console.warn('⚠️ Could not find original message with ID:', e.reply_to_message_id);
                        console.warn('⚠️ Tried searching in allMessages:', allMessages.length, 'messages');
                        
                        // Log all available messages for debugging
                        allMessages.forEach((msg, index) => {
                            console.log(`  Message ${index}: ID=${msg.id}, content="${msg.content?.substring(0, 20)}"`);
                        });
                    }
                } else if (e.reply_to_message_id) {
                    console.log('✅ Reply data already provided by server');
                }
                
                // console.log('📝 Final incoming message with reply info:', {
                //     id: incomingMessage.id,
                //     content: incomingMessage.content,
                //     reply_to_message_id: incomingMessage.reply_to_message_id,
                //     reply_to_message: incomingMessage.reply_to_message,
                //     replyToMessage: incomingMessage.replyToMessage
                // });
                
                
                // Thêm tin nhắn mới vào danh sách và sắp xếp theo thời gian
                const newMessages = [...allMessages, incomingMessage].sort((a, b) => {
                    const timeA = normalizeDateTime(a.created_at).getTime();
                    const timeB = normalizeDateTime(b.created_at).getTime();
                    return timeA - timeB;
                });
                renderMessages(newMessages);
                
                // Hiệu ứng thông báo
                if (chatBox.style.display === 'none' || chatBox.classList.contains('hidden')) {
                    chatToggle.classList.add('animate-bounce');
                    setTimeout(() => chatToggle.classList.remove('animate-bounce'), 3000);
                }
            } else {
                console.log('⏭️ Skipping message - from same user (self)');
            }
        } else {
            console.log('⏭️ Skipping message - different conversation');
            console.log('   - conversation match:', e.conversation_id === conversationId);
        }
    }

    // Hàm render tin nhắn
    function renderMessages(messages) {
        chatContent.innerHTML = '';
        
        if (!messages.length) {
            // Tin nhắn chào mừng đẹp hơn cho user mới
            chatContent.innerHTML = `
                <div class='flex justify-center mb-4'>
                    <div class='bg-white rounded-lg shadow-sm px-4 py-3 max-w-[80%] border border-gray-100'>
                        <div class="flex items-center mb-2">
                            <img src="${window.location.origin}/images/bookbeee.jpg" alt="BookBee" class="w-6 h-6 rounded-full mr-2">
                            <span class="text-xs text-gray-500 font-medium">BookBee Support</span>
                        </div>
                        <p class='text-sm text-gray-700 mb-1'>Xin chào! 👋</p>
                        <p class='text-sm text-gray-700 mb-1'>Chào mừng bạn đến với BookBee!</p>
                        <p class='text-sm text-gray-600'>Chúng tôi có thể giúp gì cho bạn hôm nay? 📚</p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            <span class="inline-block bg-blue-50 text-blue-600 text-xs px-2 py-1 rounded-full">Tìm sách</span>
                            <span class="inline-block bg-green-50 text-green-600 text-xs px-2 py-1 rounded-full">Đặt hàng</span>
                            <span class="inline-block bg-purple-50 text-purple-600 text-xs px-2 py-1 rounded-full">Hỗ trợ</span>
                        </div>
                    </div>
                </div>
            `;
            return;
        }
        
        // Sắp xếp tin nhắn theo thời gian trước khi render
        const sortedMessages = [...messages].sort((a, b) => {
            // Xử lý tin nhắn tạm thời trước - luôn đặt cuối cùng
            if (a.isTemp && !b.isTemp) return 1;
            if (!a.isTemp && b.isTemp) return -1;
            if (a.isTemp && b.isTemp) return 0; // Cả hai đều tạm thời, giữ nguyên thứ tự
            
            // Với tin nhắn thực, sắp xếp theo thời gian
            const timeA = normalizeDateTime(a.created_at).getTime();
            const timeB = normalizeDateTime(b.created_at).getTime();
            
            // Sắp xếp theo timestamp
            if (timeA !== timeB) {
                return timeA - timeB;
            }
            
            // Nếu cùng thời gian, sắp xếp theo ID (tin nhắn có ID lớn hơn sẽ hiển thị sau)
            const idA = parseInt(a.id) || 0;
            const idB = parseInt(b.id) || 0;
            return idA - idB;
        });
    
        
        // Chỉ cập nhật allMessages với tin nhắn thực (không phải tin nhắn tạm)
        const realMessages = sortedMessages.filter(m => !m.isTemp);
        allMessages = [...realMessages];
        
        let lastDate = null;
        sortedMessages.forEach(msg => {
            const msgDate = msg.created_at ? new Date(msg.created_at).toLocaleDateString('vi-VN') : '';
            if (msgDate !== lastDate) {
                // Thêm dòng phân cách ngày
                chatContent.insertAdjacentHTML('beforeend', `<div class="text-center my-2 text-xs text-gray-400">${msgDate}</div>`);
                lastDate = msgDate;
            }
            const isMe = msg.sender_id == userId; // Dùng == để so sánh cả string và number
            const align = isMe ? 'justify-end' : 'justify-start';
            // Thêm class để phân biệt tin nhắn tạm
            const bg = isMe ? 'bg-green-100 text-right' : 'bg-white text-left';
            const opacity = msg.isTemp ? 'opacity-70' : '';
            // Ưu tiên dùng sender_name từ backend, fallback sang sender.name hoặc userName/Admin
            const name = msg.sender_name || (isMe ? userName : (msg.sender?.name || 'Admin'));
            
            // Xử lý nội dung tin nhắn dựa trên type
            let messageContent = '';
            
            // Hiển thị reply nếu có
            let replyContent = '';
            if (msg.reply_to_message_id && (msg.reply_to_message || msg.replyToMessage)) {
                const replyMsg = msg.reply_to_message || msg.replyToMessage;
                
                const replyText = replyMsg.content ? (replyMsg.content.length > 30 ? replyMsg.content.substring(0, 30) + '...' : replyMsg.content) : 'Tin nhắn đã bị xóa';
                const replySenderName = replyMsg.sender?.name || 'Unknown';
                
                replyContent = `
                    <div class="bg-gray-100 border-l-4 border-blue-400 pl-2 py-1 mb-2 text-xs rounded-r">
                        <div class="text-blue-600 font-medium">↳ Trả lời ${replySenderName}</div>
                        <div class="text-gray-600">${replyText}</div>
                    </div>
                `;
            }
            
            if (msg.type === 'image' && msg.file_path) {
                // Hiển thị ảnh với preview
                const imageUrl = msg.file_path.startsWith('http') ? msg.file_path : `/storage/${msg.file_path}`;
                messageContent = `<img src="${imageUrl}" alt="Hình ảnh" class="max-w-[200px] max-h-[200px] rounded-lg cursor-pointer hover:opacity-80 transition-opacity" onclick="openImagePreview('${imageUrl}')" style="object-fit: cover;">`;
                if (msg.content) {
                    messageContent += `<p class="text-sm text-gray-600 mt-1">${msg.content}</p>`;
                }
            } else if (msg.type === 'system_order_info') {
                // Hiển thị tin nhắn thông tin đơn hàng với HTML formatting
                let formattedContent = (msg.content || '').replace(/\n/g, '<br>');
                
                // Xử lý HTML formatting
                formattedContent = formattedContent
                    .replace(/<strong>(.*?)<\/strong>/g, '<span class="font-bold text-gray-800">$1</span>')
                    .replace(/<span class="status-badge">(.*?)<\/span>/g, '<span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">$1</span>')
                    .replace(/<span class="price-highlight">(.*?)<\/span>/g, '<span class="font-bold text-green-600">$1</span>')
                    .replace(/• /g, '<span class="text-blue-500">•</span> ');
                
                messageContent = `<div class="order-info-message">${formattedContent}</div>`;
            } else {
                // Hiển thị text thông thường với line breaks
                const formattedText = (msg.content || '').replace(/\n/g, '<br>');
                messageContent = `<p class="text-sm text-gray-600">${formattedText}</p>`;
            }
            
            // Kiểm tra nếu tin nhắn có thể xóa (trong 2 phút và là tin nhắn của mình)
            const canDelete = isMe && msg.created_at && !msg.isTemp && 
                (Date.now() - new Date(msg.created_at).getTime()) < 2 * 60 * 1000;
            
            // Action buttons
            let actionButtons = '';
            if (!msg.isTemp) {
                // Properly escape content for JavaScript
                const escapedContent = (msg.content || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
                const escapedName = name.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
                const escapedSenderId = String(msg.sender_id); // Đảm bảo là string
                
                actionButtons = `
                    <div class="message-actions hidden group-hover:flex absolute ${isMe ? 'left-0' : 'right-0'} top-0 bg-white rounded shadow-md border">
                        <button onclick="replyToMessageHandler('${msg.id}', '${escapedName}', '${escapedContent}', '${escapedSenderId}')" 
                                class="p-1 text-gray-500" title="Trả lời">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                        </button>
                        ${canDelete ? `
                            <button onclick="deleteMessageHandler('${msg.id}')" 
                                    class="p-1 text-gray-500" title="Xóa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        ` : ''}
                    </div>
                `;
            }
            
            const html = `<div class="flex ${align} mb-2">
                <div class="${bg} ${opacity} rounded-lg shadow-sm px-4 py-2 max-w-[80%] relative group">
                    <div class="text-xs text-gray-400 mb-1">${name}${msg.isTemp ? ' (đang gửi...)' : ''}</div>
                    ${replyContent}
                    ${messageContent}
                    <span class="text-xs text-gray-400">${msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) : ''}</span>
                    ${actionButtons}
                </div>
            </div>`;
            chatContent.insertAdjacentHTML('beforeend', html);
        });
        chatContent.scrollTop = chatContent.scrollHeight;
        
    }

    // Function để xử lý reply
    window.replyToMessageHandler = function(messageId, senderName, content, senderId) {
        console.log('🔄 Reply button clicked:', { messageId, senderName, content, senderId });
        
        replyToMessage = {
            id: messageId,
            senderName: senderName,
            content: content,
            senderId: senderId
        };
        
        // Hiển thị reply preview
        showReplyPreview(senderName, content);
        
        // Focus vào input
        chatInput.focus();
        chatInput.placeholder = `Trả lời ${senderName}...`;
    };

    // Function để hiển thị reply preview
    function showReplyPreview(senderName, content) { 
        const previewText = content.length > 50 ? content.substring(0, 50) + '...' : content; 
        // Tìm hoặc tạo reply preview element
        let replyPreview = document.getElementById('reply-preview');    
        if (!replyPreview) {
            replyPreview = document.createElement('div');
            replyPreview.id = 'reply-preview';
            replyPreview.className = 'bg-blue-50 border-l-4 border-blue-400 p-3 mb-2 rounded-r-lg shadow-sm';
            
            // Chèn trước form chat
            const chatFormParent = chatForm.parentNode;
            
            if (chatFormParent && chatForm) {
                chatFormParent.insertBefore(replyPreview, chatForm);
            } else {
                console.error('❌ Cannot find chat form or its parent');
                return;
            }
        }
        
        replyPreview.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="text-xs text-blue-600 font-medium mb-1">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Trả lời ${senderName}
                    </div>
                    <div class="text-xs text-gray-600 pl-4">${previewText}</div>
                </div>
                <button onclick="cancelReply()" class="text-gray-400 hover:text-red-500 ml-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        // Hiển thị element
        replyPreview.style.display = 'block';
        replyPreview.classList.remove('hidden');
        
        
        // Scroll để đảm bảo preview hiển thị
        replyPreview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Function để hủy reply
    window.cancelReply = function() {
        replyToMessage = null;
        
        const replyPreview = document.getElementById('reply-preview');
        if (replyPreview) {
            replyPreview.style.display = 'none';
            replyPreview.classList.add('hidden');
        }
        
        // Reset placeholder
        if (chatInput) {
            chatInput.placeholder = 'Nhập tin nhắn...';
            chatInput.focus();
        }
    };

    // Function để xóa tin nhắn
    window.deleteMessageHandler = async function(messageId) {
        
        if (!confirm('Bạn có chắc chắn muốn xóa tin nhắn này?')) {
            return;
        }
        
        try {            
            // Check authentication first
            try {
                await window.axios.get('/api/user');
                console.log('✅ User is authenticated');
            } catch (authError) {
                console.error('❌ Authentication check failed:', authError);
                showToast('🔒 Vui lòng đăng nhập lại để thực hiện hành động này', 'error');
                return;
            }
            
            const response = await window.axios.delete(`/api/messages/${messageId}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            
            if (response.data && response.data.success) {
                // Xóa tin nhắn khỏi allMessages và re-render
                allMessages = allMessages.filter(msg => msg.id != messageId);
                renderMessages(allMessages);
                
                // Hiển thị toast thành công
                showToast('✅ Tin nhắn đã được xóa thành công', 'success');
                
            } else {
                console.warn('⚠️ Unexpected response format:', response.data);
                showToast('⚠️ Phản hồi từ server không mong đợi. Vui lòng thử lại', 'warning');
            }
        } catch (error) {
            
            // Detailed error handling
            if (error.response) {
                console.error('❌ Response status:', error.response.status);
                console.error('❌ Response data:', error.response.data);
                
                switch (error.response.status) {
                    case 401:
                        showToast('🔒 Vui lòng đăng nhập lại để thực hiện hành động này', 'error');
                        break;
                    case 403:
                        const message = error.response.data?.message || 'Bạn không có quyền xóa tin nhắn này hoặc đã quá thời gian cho phép.';
                        showToast(`🚫 ${message}`, 'warning');
                        break;
                    case 404:
                        showToast('📭 Tin nhắn không tồn tại hoặc đã bị xóa', 'warning');
                        // Remove from UI anyway
                        allMessages = allMessages.filter(msg => msg.id != messageId);
                        renderMessages(allMessages);
                        break;
                    case 500:
                        showToast('💥 Có lỗi server. Vui lòng thử lại sau', 'error');
                        break;
                    default:
                        showToast(`❌ Có lỗi xảy ra (${error.response.status}). Vui lòng thử lại`, 'error');
                }
            } else if (error.request) {
                console.error('❌ No response received:', error.request);
                showToast('🌐 Không thể kết nối đến server. Vui lòng kiểm tra kết nối internet', 'error');
            } else {
                console.error('❌ Request setup error:', error.message);
                showToast('⚙️ Có lỗi xảy ra khi thiết lập request. Vui lòng thử lại', 'error');
            }
        }
    };

    // Function để preview ảnh
    window.openImagePreview = function(imageUrl) {
        // Tạo modal preview nếu chưa có
        let modal = document.getElementById('image-preview-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'image-preview-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden';
            modal.innerHTML = `
                <div class="relative max-w-4xl max-h-full p-4">
                    <button onclick="closeImagePreview()" 
                            class="absolute top-2 right-2 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <img id="preview-modal-image" src="" alt="Preview" 
                         class="max-w-full max-h-full object-contain rounded-lg">
                </div>
            `;
            document.body.appendChild(modal);
            
            // Đóng modal khi click ra ngoài
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImagePreview();
                }
            });
        }
        
        // Hiển thị ảnh
        const modalImage = document.getElementById('preview-modal-image');
        modalImage.src = imageUrl;
        modal.classList.remove('hidden');
        
        // Ngăn scroll trang
        document.body.style.overflow = 'hidden';
    };

    // Function để đóng preview ảnh
    window.closeImagePreview = function() {
        const modal = document.getElementById('image-preview-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
        
        // Khôi phục scroll trang
        document.body.style.overflow = 'auto';
    };

    // Function để hiển thị toast notification
    function showToast(message, type = 'info') {
        // Tạo toast container nếu chưa có
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(toastContainer);
        }

        // Tạo toast element
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast-item transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
        
        // Xác định màu sắc dựa trên type
        let bgColor, textColor, iconColor;
        switch (type) {
            case 'success':
                bgColor = 'bg-green-500';
                textColor = 'text-white';
                iconColor = 'text-green-200';
                break;
            case 'error':
                bgColor = 'bg-red-500';
                textColor = 'text-white';
                iconColor = 'text-red-200';
                break;
            case 'warning':
                bgColor = 'bg-yellow-500';
                textColor = 'text-white';
                iconColor = 'text-yellow-200';
                break;
            default:
                bgColor = 'bg-blue-500';
                textColor = 'text-white';
                iconColor = 'text-blue-200';
                break;
        }

        toast.innerHTML = `
            <div class="${bgColor} ${textColor} px-4 py-3 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] max-w-[400px]">
                <div class="${iconColor}">
                    ${type === 'success' ? `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    ` : type === 'error' ? `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    ` : type === 'warning' ? `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    ` : `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    `}
                </div>
                <div class="flex-1 text-sm font-medium">${message}</div>
                <button onclick="hideToast('${toastId}')" class="${iconColor} hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        // Thêm toast vào container
        toastContainer.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 10);

        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            hideToast(toastId);
        }, 5000);
    }

    // Function để ẩn toast
    window.hideToast = function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            
            // Xóa element sau khi animation hoàn thành
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    };

    // Đóng preview khi nhấn ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImagePreview();
        }
    });

    // Hàm thiết lập lắng nghe tin nhắn real-time
    function setupEchoListener() {
        
        const channelName = 'conversations.' + conversationId;
        
        // Đợi một chút để đảm bảo Echo sẵn sàng
        setTimeout(() => {
            try {
                // Sử dụng public channel
                const channel = window.Echo.channel(channelName);
                
                // Đăng ký listener
                channel.listen('MessageSent', (e) => {
                    
                    if (e.sender_id && e.sender_id != userId) {
                        
                        // Tạo object message từ data nhận được
                        const incomingMessage = {
                            id: e.id,
                            sender_id: e.sender_id,
                            content: e.content,
                            created_at: e.created_at,
                            sender: e.sender,
                            type: e.type || 'text',
                            file_path: e.file_path,
                            reply_to_message_id: e.reply_to_message_id,
                            reply_to_message: e.reply_to_message,
                            replyToMessage: e.replyToMessage
                        };
                        
                        // Nếu có reply_to_message_id nhưng không có thông tin reply, tìm trong allMessages
                        if (e.reply_to_message_id && !e.reply_to_message && !e.replyToMessage) {
                            const originalMessage = allMessages.find(msg => msg.id === e.reply_to_message_id);
                            if (originalMessage) {
                                incomingMessage.reply_to_message = {
                                    id: originalMessage.id,
                                    content: originalMessage.content,
                                    sender: originalMessage.sender,
                                    sender_id: originalMessage.sender_id
                                };
                            } else {
                                console.warn('⚠️ [SetupEcho] Could not find original message with ID:', e.reply_to_message_id);
                            }
                        }
                        
                        
                        // Thêm tin nhắn mới vào danh sách và sắp xếp theo thời gian
                        const newMessages = [...allMessages, incomingMessage].sort((a, b) => {
                            const timeA = normalizeDateTime(a.created_at).getTime();
                            const timeB = normalizeDateTime(b.created_at).getTime();
                            return timeA - timeB;
                        });
                        renderMessages(newMessages);
                        
                        // Hiệu ứng thông báo có tin nhắn mới
                        if (chatBox.style.display === 'none' || chatBox.classList.contains('hidden')) {
                            const chatToggle = document.getElementById('chat-toggle');
                            if (chatToggle) {
                                chatToggle.classList.add('animate-bounce');
                                setTimeout(() => {
                                    chatToggle.classList.remove('animate-bounce');
                                }, 3000);
                            }
                        }
                    } else {
                        console.log('⏭️ Skipping message from same user or invalid sender');
                    }
                });
                
                // Đăng ký callback cho subscription success
                channel.subscribed(() => {
                    console.log('✅ Successfully subscribed to channel:', channelName);
                    isEchoListening = true;
                });
                
                // Đăng ký callback cho lỗi
                channel.error((error) => {
                    console.error('❌ Channel subscription error:', error);
                });
                
                
            } catch (error) {
                console.error('❌ Error setting up Echo listener:', error);
            }
        }, 100); // Đợi 100ms
    }

    // Kiểm tra auth status
    async function checkAuthStatus() {
        try {
            await window.axios.get('/api/user');
            return true;
        } catch (e) {
            return false;
        }
    }

    // Hàm lấy admin ID từ email
    async function getAdminIdByEmail(email) {
        try {
            const response = await window.axios.get(`/api/admin/find-by-email?email=${encodeURIComponent(email)}`);
            
            if (response.data && response.data.admin && response.data.admin.id) {
                console.log('✅ Found admin:', response.data.admin);
                return response.data.admin.id;
            } else {
                console.error('❌ Admin not found with email:', email);
                return null;
            }
        } catch (error) {
            console.error('❌ Error finding admin by email:', error);
            return null;
        }
    }

    // Hàm load tin nhắn
    async function loadMessages() {
        try {
            // Kiểm tra auth trước khi load
            const isAuthenticated = await checkAuthStatus();
            if (!isAuthenticated) {
                chatContent.innerHTML = `<div class='text-center text-red-500'>Vui lòng đăng nhập lại để tiếp tục.</div>`;
                return;
            }

            const res = await window.axios.get('/api/messages?customer_id=' + userId);
            const conversations = res.data;

            
            if (!conversations.length) {
                // Tự động tạo conversation mới với admin mặc định
                console.log('🆕 No conversation found, creating new one with default admin');
                await createDefaultConversation();
                return;
            }
            
            // Lấy cuộc trò chuyện gần nhất với admin
            const conv = conversations[0];
            conversationId = conv.id;
            adminId = conv.admin_id;
            
            
            // Render tin nhắn
            renderMessages(conv.messages || []);
            
            // Không gọi setupEchoListener ở đây nữa - sẽ được gọi từ click handler
            
        } catch (e) {
            console.error('❌ Error loading messages:', e);
            chatContent.innerHTML = `<div class='text-center text-red-500'>Không thể tải tin nhắn: ${e.message}</div>`;
        }
    }

    // Hàm tạo conversation mặc định với admin
    async function createDefaultConversation() {
        try {
            
            // Tìm admin theo email thay vì dùng ID cố định
            const defaultAdminEmail = 'admin1@example.com';
            const defaultAdminId = await getAdminIdByEmail(defaultAdminEmail);
            
            if (!defaultAdminId) {
                console.error('❌ Cannot find admin with email:', defaultAdminEmail);
                chatContent.innerHTML = `<div class='text-center text-red-500'>Không tìm thấy admin để tạo cuộc trò chuyện.</div>`;
                return;
            }
            
            const payload = {
                customer_id: userId,
                admin_id: defaultAdminId
            };

            const response = await window.axios.post('/api/conversations', payload, {
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.data && response.data.conversation) {
                const newConv = response.data.conversation;
                conversationId = newConv.id;
                adminId = newConv.admin_id;
                
                
                // Render tin nhắn chào mừng
                renderMessages([]);
                
            } else {
                console.error('❌ Failed to create conversation:', response.data);
                chatContent.innerHTML = `<div class='text-center text-red-500'>Không thể tạo cuộc trò chuyện mới.</div>`;
            }
            
        } catch (e) {
            console.error('❌ Error creating default conversation:', e);
            
            const fallbackAdminEmail = 'admin1@example.com';
            const fallbackAdminId = await getAdminIdByEmail(fallbackAdminEmail);
            
            if (fallbackAdminId) {
                adminId = fallbackAdminId;
                console.log('✅ Using fallback admin ID:', adminId);
                // Render tin nhắn chào mừng
                renderMessages([]);
            } else {
                console.error('❌ Fallback failed - cannot find admin');
                chatContent.innerHTML = `<div class='text-center text-red-500'>Không thể tìm thấy admin để tạo cuộc trò chuyện.</div>`;
            }
        }
    }

    // Khi mở chat, load tin nhắn
    chatToggle.addEventListener('click', function () {
        if (chatBox.style.display === 'none' || chatBox.classList.contains('hidden')) {
            chatBox.style.display = 'block';
            chatBox.classList.remove('hidden');
            loadMessages();
        } else {
            chatBox.style.display = 'none';
        }
    });

    // Gửi tin nhắn
    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const msg = chatInput.value.trim();
        
        if (!msg && !selectedFile) return;
        
        if (!adminId) {
            // Nếu chưa có adminId, load lại hoặc tạo mới
            await loadMessages();
            if (!adminId) {
                // Nếu vẫn chưa có, tạo conversation mặc định
                await createDefaultConversation();
                if (!adminId) {
                    // Thử tìm admin theo email như một giải pháp cuối cùng
                    const fallbackAdminEmail = 'admin1@example.com';
                    const fallbackAdminId = await getAdminIdByEmail(fallbackAdminEmail);
                    
                    if (fallbackAdminId) {
                        adminId = fallbackAdminId;
                        console.log('✅ Found admin for messaging:', adminId);
                    } else {
                        chatContent.innerHTML += `<div class='text-center text-red-500'>Không tìm thấy admin để gửi tin nhắn.</div>`;
                        return;
                    }
                }
            }
        }
        
        try {
            // Save replyToMessage before any operations
            const savedReplyToMessage = replyToMessage;
            
            // Tạo tin nhắn tạm thời với ID tạm để phân biệt
            const tempId = 'temp_' + Date.now();
            
            let tempMsg;
            if (selectedFile) {
                // Tin nhắn ảnh tạm thời
                tempMsg = {
                    id: tempId,
                    sender_id: userId,
                    content: msg || 'Đang gửi ảnh...',
                    type: 'image',
                    file_path: URL.createObjectURL(selectedFile), // Tạo preview URL tạm thời
                    created_at: new Date().toISOString(),
                    sender: { name: userName },
                    isTemp: true,
                    reply_to_message_id: savedReplyToMessage ? savedReplyToMessage.id : null,
                    reply_to_message: savedReplyToMessage ? {
                        id: savedReplyToMessage.id,
                        content: savedReplyToMessage.content,
                        sender: { name: savedReplyToMessage.senderName }
                    } : null
                };
            } else {
                // Tin nhắn text tạm thời
                tempMsg = {
                    id: tempId,
                    sender_id: userId,
                    content: msg,
                    type: 'text',
                    created_at: new Date().toISOString(),
                    sender: { name: userName },
                    isTemp: true,
                    reply_to_message_id: savedReplyToMessage ? savedReplyToMessage.id : null,
                    reply_to_message: savedReplyToMessage ? {
                        id: savedReplyToMessage.id,
                        content: savedReplyToMessage.content,
                        sender: { name: savedReplyToMessage.senderName }
                    } : null
                };
            }
            
            // Thêm vào danh sách hiện tại và render
            const messagesWithTemp = [...allMessages, tempMsg];
            renderMessages(messagesWithTemp);
            
            // Xóa input và file ngay lập tức
            chatInput.value = '';
            
            // Chuẩn bị payload
            const formData = new FormData();
            if (conversationId) {
                formData.append('conversation_id', conversationId);
            }
            formData.append('customer_id', userId);
            formData.append('admin_id', adminId);
            formData.append('sender_id', userId);
            
            // Thêm reply_to_message_id nếu đang reply - SỬ DỤNG savedReplyToMessage
            if (savedReplyToMessage) {
                formData.append('reply_to_message_id', savedReplyToMessage.id);
                
                // Debug FormData content
                for (let [key, value] of formData.entries()) {
                    console.log('📤 FormData entry:', key, value);
                }
            }
            
            if (selectedFile) {
                formData.append('image', selectedFile);
                formData.append('type', 'image');
                if (msg) {
                    formData.append('content', msg);
                }
            } else {
                formData.append('content', msg);
                formData.append('type', 'text');
            }

            // Gửi request
            const response = await window.axios.post('/api/messages', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (response.data && response.data.new_message) {
                // Loại bỏ tin nhắn tạm và thay bằng tin nhắn thực từ server
                const realMessage = response.data.new_message;
                
                
                // Nếu có conversation_id mới từ response, cập nhật
                if (response.data.conversation_id && !conversationId) {
                    conversationId = response.data.conversation_id;
                    console.log('🆕 Updated conversation ID:', conversationId);
                }
                
                // Loại bỏ tin nhắn tạm và thêm tin nhắn thực từ server, sau đó sắp xếp
                const filteredMessages = allMessages.filter(m => !m.isTemp);
                const updatedMessages = [...filteredMessages, realMessage].sort((a, b) => {
                    const timeA = normalizeDateTime(a.created_at).getTime();
                    const timeB = normalizeDateTime(b.created_at).getTime();
                    return timeA - timeB;
                });
                renderMessages(updatedMessages);
                
                // Ẩn reply preview nếu có - sử dụng savedReplyToMessage
                if (savedReplyToMessage) {
                    cancelReply();
                }
                
                // Xóa file đã chọn
                if (selectedFile) {
                    selectedFile = null;
                    fileInput.value = '';
                    filePreview.classList.add('hidden');
                    previewImage.style.display = 'none';
                }
            } else {
                // Nếu không có tin nhắn mới, load lại toàn bộ
                await loadMessages();
            }
            
        } catch (e) {
            console.error('❌ Error sending message:', e);
            
            // Loại bỏ tin nhắn tạm khi gửi thất bại
            const filteredMessages = allMessages.filter(m => !m.isTemp);
            renderMessages(filteredMessages);
            
            // Khôi phục lại nội dung input
            chatInput.value = msg;
            
            showToast('❌ Không gửi được tin nhắn. Vui lòng thử lại', 'error');
        }
    });
    
    // Thêm styles cho message actions và preview modal
    if (!document.getElementById('chat-custom-styles')) {
        const styles = document.createElement('style');
        styles.id = 'chat-custom-styles';
        styles.textContent = `
            .message-actions {
                transform: translateX(-5px);
                transition: opacity 0.2s;
            }
            .message-actions button {
                transition: all 0.2s;
            }
            #image-preview-modal {
                backdrop-filter: blur(4px);
                transition: all 0.3s ease;
            }
            #preview-modal-image {
                transition: transform 0.3s ease;
            }
            
            .border-l-3 {
                border-left-width: 3px;
            }
            .border-l-4 {
                border-left-width: 4px;
            }
            .group:hover .message-actions {
                display: flex !important;
            }
            #reply-preview {
                animation: slideDown 0.3s ease-out;
            }
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Toast styles */
            #toast-container {
                pointer-events: none;
            }
            #toast-container .toast-item {
                pointer-events: auto;
            }
            .toast-item {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            .toast-item:hover {
                transform: translateX(-5px) !important;
            }
        `;
        document.head.appendChild(styles);
    }

    // Hàm global để load order messages (cho order chat)
    window.loadOrderMessages = function(orderConversationId, messages) {
        
        // Nếu cùng conversation ID - TUYỆT ĐỐI KHÔNG OVERRIDE
        if (conversationId === orderConversationId) {
            // CHẮC CHẮN KHÔNG OVERRIDE - chỉ merge nếu có tin nhắn thật sự mới
            if (messages && messages.length > 0) {
                const existingIds = new Set(allMessages.map(m => m.id));
                const newMessages = messages.filter(m => !existingIds.has(m.id));
               
                if (newMessages.length > 0) {                
                    // Backup current messages để đảm bảo
                    const backupMessages = [...allMessages];
                    
                    // MERGE và sort
                    allMessages = [...allMessages, ...newMessages].sort((a, b) => {
                        const timeA = normalizeDateTime(a.created_at).getTime();
                        const timeB = normalizeDateTime(b.created_at).getTime();
                        return timeA - timeB;
                    });
                    
                    console.log('📋 [SUCCESS] Final count after merge:', allMessages.length, '(was', backupMessages.length, ')');
                    renderMessages(allMessages);
                } else {
                    console.log('ℹ️ [SKIP] No new messages - keeping all', allMessages.length, 'existing messages intact');
                    // KHÔNG LÀM GÌ CẢ - giữ nguyên allMessages
                }
            } else {
                console.log('ℹ️ [SKIP] No incoming messages - keeping existing conversation untouched');
                // KHÔNG LÀM GÌ CẢ
            }
        } else {
            // Khác conversation ID - load conversation mới
            conversationId = orderConversationId;
            
            if (messages && messages.length > 0) {
                allMessages = messages;
                renderMessages(allMessages);
            } else {
                loadMessages();
            }
            
            // Setup listener cho conversation mới
            if (!isEchoListening) {
                setupEchoListener();
            }
        }
    };

    // Hàm global để set order context
    window.setOrderContext = function(orderInfo) {
        currentOrderContext = orderInfo;
        updateChatHeaderWithOrder(orderInfo);
    };

    // Hàm global để lấy conversation ID hiện tại
    window.getCurrentConversationId = function() {
        return conversationId;
    };

    // Hàm global để lấy tất cả messages hiện tại
    window.getCurrentMessages = function() {
        return allMessages;
    };

    // Hàm cập nhật header chat với thông tin đơn hàng
    function updateChatHeaderWithOrder(orderInfo) {
        const chatHeader = document.querySelector('#chat-box .bg-black');
        if (chatHeader && orderInfo) {
            let orderInfoEl = chatHeader.querySelector('.order-info');
            if (!orderInfoEl) {
                orderInfoEl = document.createElement('div');
                orderInfoEl.className = 'order-info text-xs text-gray-300 mt-1';
                chatHeader.appendChild(orderInfoEl);
            }
            orderInfoEl.innerHTML = `📦 Đơn hàng #${orderInfo.order_code} - ${orderInfo.status}`;
        }
    }
});