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
            emojiBtn.className = 'hover:bg-gray-100 p-1 rounded cursor-pointer';
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

    // Debug Echo availability
    console.log('🔍 Checking Echo availability:', !!window.Echo);
    if (window.Echo) {
        console.log('✅ Echo is available');
        console.log('🔧 Echo connector:', window.Echo.connector);
        console.log('🔧 Pusher instance:', window.Echo.connector?.pusher);
        
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
        console.log('🌐 Setting up global listener for user:', globalChannelName);
        
        // Thử cách tiếp cận trực tiếp với Pusher thay vì Echo
        try {
            // Method 1: Sử dụng Echo như bình thường
            const globalChannel = window.Echo.channel(globalChannelName);
            
            // Đăng ký callback cho subscription success
            globalChannel.subscribed(() => {
                console.log('✅ Subscribed to global channel:', globalChannelName);
                isEchoListening = true;
                
                console.log('🎯 Setting up MessageSent listener after subscription');
                globalChannel.listen('MessageSent', (e) => {
                    console.log('� Global message received via Echo:', e);
                    handleIncomingMessage(e);
                });
                console.log('🎯 MessageSent listener registered successfully');
            });
            
            // Method 2: Backup với Pusher trực tiếp
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                console.log('🔧 Setting up backup Pusher listener');
                
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
            
            console.log('🌐 Global listener setup completed');
            
        } catch (error) {
            console.error('❌ Error setting up global listener:', error);
        }
    }
    
    // Function riêng để xử lý tin nhắn đến
    function handleIncomingMessage(e) {
        console.log('📨 Processing incoming message:', e);
        console.log('📨 Event conversation_id:', e.conversation_id, 'Current conversation:', conversationId);
        console.log('📨 Event sender_id:', e.sender_id, 'Current userId:', userId);
        
        // Chỉ xử lý nếu đang ở conversation đúng và không phải tin nhắn của mình
        if (e.conversation_id === conversationId && e.sender_id !== userId) {
            console.log('✅ Processing admin message');
            
            const incomingMessage = {
                id: e.id,
                sender_id: e.sender_id,
                content: e.content,
                created_at: e.created_at,
                sender: e.sender
            };
            
            console.log('📝 Adding message to allMessages:', incomingMessage);
            console.log('📝 Current allMessages before:', [...allMessages]);
            
            // Thêm tin nhắn mới vào danh sách
            const newMessages = [...allMessages, incomingMessage];
            console.log('📝 New messages array:', newMessages);
            renderMessages(newMessages);
            
            // Hiệu ứng thông báo
            if (chatBox.style.display === 'none' || chatBox.classList.contains('hidden')) {
                chatToggle.classList.add('animate-bounce');
                setTimeout(() => chatToggle.classList.remove('animate-bounce'), 3000);
            }
        } else {
            console.log('⏭️ Skipping message - not current conversation or from same user');
            console.log('   - conversation match:', e.conversation_id === conversationId);
            console.log('   - different sender:', e.sender_id !== userId);
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
        
        // Chỉ cập nhật allMessages với tin nhắn thực (không phải tin nhắn tạm)
        const realMessages = messages.filter(m => !m.isTemp);
        allMessages = [...realMessages];
        
        let lastDate = null;
        messages.forEach(msg => {
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
            const name = isMe ? userName : (msg.sender?.name || 'Admin');
            
            // Xử lý nội dung tin nhắn dựa trên type
            let messageContent = '';
            if (msg.type === 'image' && msg.file_path) {
                // Hiển thị ảnh
                const imageUrl = msg.file_path.startsWith('http') ? msg.file_path : `/storage/${msg.file_path}`;
                messageContent = `<img src="${imageUrl}" alt="Hình ảnh" class="max-w-[200px] max-h-[200px] rounded-lg cursor-pointer" onclick="window.open('${imageUrl}', '_blank')" style="object-fit: cover;">`;
            } else {
                // Hiển thị text thông thường
                messageContent = `<p class="text-sm text-gray-600">${msg.content || ''}</p>`;
            }
            
            const html = `<div class="flex ${align} mb-2">
                <div class="${bg} ${opacity} rounded-lg shadow-sm px-4 py-2 max-w-[80%]">
                    <div class="text-xs text-gray-400 mb-1">${name}${msg.isTemp ? ' (đang gửi...)' : ''}</div>
                    ${messageContent}
                    <span class="text-xs text-gray-400">${msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) : ''}</span>
                </div>
            </div>`;
            chatContent.insertAdjacentHTML('beforeend', html);
        });
        chatContent.scrollTop = chatContent.scrollHeight;
    }

    // Hàm thiết lập lắng nghe tin nhắn real-time
    function setupEchoListener() {
        if (!window.Echo) {
            console.error('❌ Echo is not available');
            return;
        }
        
        if (!conversationId) {
            console.error('❌ Conversation ID is not available');
            return;
        }
        
        if (isEchoListening) {
            console.log('⚠️ Echo listener already setup');
            return;
        }
        
        const channelName = 'conversations.' + conversationId;
        console.log('🎯 Setting up realtime listener for:', channelName);
        
        // Đợi một chút để đảm bảo Echo sẵn sàng
        setTimeout(() => {
            try {
                // Sử dụng public channel
                const channel = window.Echo.channel(channelName);
                
                console.log('📡 Channel object created:', channel);
                
                // Đăng ký listener
                channel.listen('MessageSent', (e) => {
                    console.log('📨 Raw message received:', e);
                    console.log('📨 Message sender_id:', e.sender_id, 'Current userId:', userId);
                    
                    if (e.sender_id && e.sender_id != userId) {
                        console.log('✅ Processing admin message from sender:', e.sender_id);
                        
                        // Tạo object message từ data nhận được
                        const incomingMessage = {
                            id: e.id,
                            sender_id: e.sender_id,
                            content: e.content,
                            created_at: e.created_at,
                            sender: e.sender
                        };
                        
                        console.log('📝 Incoming message object:', incomingMessage);
                        console.log('📝 Current allMessages before:', allMessages);
                        
                        // Thêm tin nhắn mới vào danh sách
                        const newMessages = [...allMessages, incomingMessage];
                        console.log('📝 New messages array:', newMessages);
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
                
                console.log('🎯 Echo listener setup completed');
                
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
            console.log('🔍 Looking for admin with email:', email);
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

            console.log('🔄 Loading messages for user:', userId);
            const res = await window.axios.get('/api/messages?customer_id=' + userId);
            const conversations = res.data;
            
            console.log('📋 Conversations received:', conversations);
            
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
            
            console.log('💬 Using conversation:', conversationId, 'with admin:', adminId);
            console.log('📨 Messages in conversation:', conv.messages || []);
            
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
            console.log('🔧 Creating default conversation with admin1@example.com');
            
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
                
                console.log('✅ Created new conversation:', conversationId, 'with admin:', adminId);
                
                // Render tin nhắn chào mừng
                renderMessages([]);
                
            } else {
                console.error('❌ Failed to create conversation:', response.data);
                chatContent.innerHTML = `<div class='text-center text-red-500'>Không thể tạo cuộc trò chuyện mới.</div>`;
            }
            
        } catch (e) {
            console.error('❌ Error creating default conversation:', e);
            
            // Fallback: tìm admin theo email
            console.log('🔄 Attempting fallback - finding admin by email');
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
                    console.log('🔄 Final attempt: finding admin by email');
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
                    isTemp: true
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
                    isTemp: true
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
                
                // Loại bỏ tin nhắn tạm
                const filteredMessages = allMessages.filter(m => !m.isTemp);
                const updatedMessages = [...filteredMessages, realMessage];
                renderMessages(updatedMessages);
                
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
            
            chatContent.innerHTML += `<div class='text-center text-red-500 my-2'>❌ Không gửi được tin nhắn. Vui lòng thử lại.</div>`;
        }
    });
});