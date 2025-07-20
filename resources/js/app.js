
import './bootstrap';
import './echo';
import './clock';
import './description-toggle';
import './collection-swiper';
import './review-swiper';
import './quantity';



import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Enable Pusher logging for debugging
Pusher.logToConsole = true;

window.Pusher = Pusher;

// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    encrypted: true,
    forceTLS: true
    // Removed auth configuration since we're using public channels
});

// Debug Echo initialization
console.log('Echo initialized with:', {
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER
});

// Lắng nghe public channel 'user-status'
window.Echo.channel('user-status')
    .listen('UserSessionChanged', (e) => {
        // Cập nhật trạng thái user trên giao diện
        if (e.user && e.user.id) {
            // Tìm tất cả phần tử hiển thị trạng thái user này (cần thêm data-user-id="{{ $user->id }}" ở view)
            document.querySelectorAll(`[data-user-id="${e.user.id}"]`).forEach(el => {
                if (e.user.status === 'online') {
                    el.innerHTML = '<span class="badge bg-success">Online</span>';
                } else if (e.user.last_seen) {
                    // Nếu dùng moment.js để format thời gian
                    if (window.moment) {
                        el.innerHTML = `<span class=\"badge bg-warning\">Hoạt động ${window.moment(e.user.last_seen).fromNow()}</span>`;
                    } else {
                        el.innerHTML = `<span class=\"badge bg-warning\">Hoạt động ${e.user.last_seen}</span>`;
                    }
                } else {
                    el.innerHTML = '<span class="badge bg-secondary">Offline</span>';
                }
            });
        }
        // Hiện notification như cũ
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

// --- CHAT WIDGET LOGIC ---
document.addEventListener('DOMContentLoaded', function () {
    const chatUserInfo = document.getElementById('chat-user-info');
    if (!chatUserInfo) return; // Không đăng nhập thì không làm gì

    const userId = chatUserInfo.dataset.userId;
    const userName = chatUserInfo.dataset.userName;
    let adminId = null;
    let conversationId = null;

    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatContent = document.getElementById('chat-content');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');

    // Setup axios headers
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    window.axios.defaults.headers.common = {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    };

    // Hàm render tin nhắn
    function renderMessages(messages) {
        chatContent.innerHTML = '';
        if (!messages.length) {
            chatContent.innerHTML = `<div class='flex justify-center'><div class='bg-white rounded-lg shadow-sm px-4 py-2 max-w-[80%]'><p class='text-sm text-gray-600'>Xin chào! 👋</p><p class='text-sm text-gray-600'>BookBee có thể giúp gì cho bạn?</p></div></div>`;
            return;
        }
        let lastDate = null;
        messages.forEach(msg => {
            const msgDate = msg.created_at ? new Date(msg.created_at).toLocaleDateString('vi-VN') : '';
            if (msgDate !== lastDate) {
                // Thêm dòng phân cách ngày
                chatContent.insertAdjacentHTML('beforeend', `<div class="text-center my-2 text-xs text-gray-400">${msgDate}</div>`);
                lastDate = msgDate;
            }
            const isMe = msg.sender_id === userId;
            const align = isMe ? 'justify-end' : 'justify-start';
            const bg = isMe ? 'bg-green-100 text-right' : 'bg-white text-left';
            const name = isMe ? userName : (msg.sender?.name || 'Admin');
            const html = `<div class="flex ${align} mb-2">
                <div class="${bg} rounded-lg shadow-sm px-4 py-2 max-w-[80%]">
                    <div class="text-xs text-gray-400 mb-1">${name}</div>
                    <p class="text-sm text-gray-600">${msg.content}</p>
                    <span class="text-xs text-gray-400">${msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) : ''}</span>
                </div>
            </div>`;
            chatContent.insertAdjacentHTML('beforeend', html);
        });
        chatContent.scrollTop = chatContent.scrollHeight;
    }

    // Hàm lấy adminId (lấy admin đầu tiên trong conversation)
    function getAdminIdFromConversations(convs) {
        if (!convs.length) return null;
        // Ưu tiên lấy admin_id khác userId
        for (const conv of convs) {
            if (conv.admin_id && conv.admin_id !== userId) return conv.admin_id;
        }
        // fallback: lấy admin đầu tiên
        return convs[0].admin_id;
    }

    // Kiểm tra auth status
    async function checkAuthStatus() {
        try {
            await window.axios.get('/api/user');
            return true;
        } catch (e) {
            console.error('Auth check failed:', e);
            return false;
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
                renderMessages([]);
                return;
            }
            // Lấy cuộc trò chuyện gần nhất với admin
            const conv = conversations[0];
            conversationId = conv.id;
            adminId = conv.admin_id;
            renderMessages(conv.messages || []);
            // Đăng ký realtime sau khi biết conversationId
            if (window.Echo && conversationId && userId && !window._echoChatBound) {
                window.Echo.private('conversations.' + conversationId)
                    .listen('MessageSent', (e) => {
                        if (e.message && e.message.sender_id !== userId) {
                            const lastMsgs = chatContent._lastMessages || [];
                            lastMsgs.push(e.message);
                            renderMessages(lastMsgs);
                            chatContent._lastMessages = lastMsgs;
                        }
                    });
                window._echoChatBound = true;
            }
        } catch (e) {
            chatContent.innerHTML = `<div class='text-center text-red-500'>Không thể tải tin nhắn.`;
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
        if (!msg) return;
        if (!adminId) {
            // Nếu chưa có adminId, load lại
            await loadMessages();
            if (!adminId) {
                chatContent.innerHTML += `<div class='text-center text-red-500'>Không tìm thấy admin để gửi tin nhắn.</div>`;
                return;
            }
        }
        try {
            // Thêm tin nhắn tạm thời vào UI
            const tempMsg = {
                sender_id: userId,
                content: msg,
                created_at: new Date().toISOString()
            };
            renderMessages([...(chatContent._lastMessages || []), tempMsg]);

            const payload = {
                conversation_id: conversationId,
                customer_id: userId,
                admin_id: adminId,
                sender_id: userId,
                content: msg,
                type: 'text'
            };

            // Gửi request với timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            const response = await window.axios.post('/api/messages', payload, {
                signal: controller.signal,
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            clearTimeout(timeoutId);
            chatInput.value = '';

            if (response.data && response.data.new_message) {
                // Cập nhật UI với tin nhắn từ server
                const messages = [...(chatContent._lastMessages || [])];
                messages.pop(); // Xóa tin nhắn tạm
                messages.push(response.data.new_message);
                chatContent._lastMessages = messages;
                renderMessages(messages);
            } else {
                // Nếu không có tin nhắn mới, load lại toàn bộ
                await loadMessages();
            }
        } catch (e) {
            console.error('Error sending message:', e);
            if (e.response) {
                console.error('Response data:', e.response.data);
                console.error('Response status:', e.response.status);
            }
            chatContent.innerHTML += `<div class='text-center text-red-500'>Không gửi được tin nhắn: ${e.message}</div>`;
        }
    });

    // Lưu lại messages lần cuối để append nhanh
    chatContent._lastMessages = [];
});

