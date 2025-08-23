<!-- Order Chat Button Component -->
@if(Auth::check())
<button 
    type="button" 
    id="order-chat-btn-{{ $order->id }}" 
    data-order-id="{{ $order->id }}"
    class="order-chat-button hidden inline-flex items-center px-6 py-3 border-2 border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white font-bold text-sm uppercase tracking-wide transition-all duration-300"
    onclick="startOrderChat('{{ $order->id }}')">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4-8 9-8s9 3.582 9 8z" />
    </svg>
    LIÊN HỆ VỀ ĐƠN HÀNG
</button>
@endif

<script>
// Kiểm tra điều kiện chat cho đơn hàng này khi trang load
document.addEventListener('DOMContentLoaded', function() {
    checkOrderChatAvailability('{{ $order->id }}');
});

async function checkOrderChatAvailability(orderId) {
    try {
        const response = await fetch(`/api/orders/${orderId}/can-chat`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();
        
        const chatButton = document.getElementById(`order-chat-btn-${orderId}`);
        if (chatButton) {
            if (data.can_chat) {
                chatButton.classList.remove('hidden');
            } else {
                chatButton.classList.add('hidden');
                // Optional: show reason in console for debugging
                if (data.reason) {
                    console.log(`Order ${orderId} chat not available: ${data.reason}`);
                }
            }
        }
    } catch (error) {
        console.error('Error checking order chat availability:', error);
    }
}

async function startOrderChat(orderId) {
    const button = document.getElementById(`order-chat-btn-${orderId}`);
    if (!button) {
        console.error('Chat button not found for order:', orderId);
        return;
    }
    
    const originalText = button.innerHTML;
    
    try {
        // Kiểm tra xem chat widget đã có conversation chưa
        const currentConversationId = window.getCurrentConversationId ? window.getCurrentConversationId() : null;
        const currentMessages = window.getCurrentMessages ? window.getCurrentMessages() : [];
        
        console.log('🚀 Starting order chat:', {
            orderId: orderId,
            currentConversationId: currentConversationId,
            currentMessagesCount: currentMessages.length
        });
        
        // Kiểm tra lại điều kiện chat trước khi bắt đầu
        const canChatResponse = await fetch(`/api/orders/${orderId}/can-chat`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });

        const canChatData = await canChatResponse.json();
        
        if (!canChatData.can_chat) {
            // Hiển thị lý do không thể chat cho user
            alert(`Không thể chat về đơn hàng này: ${canChatData.reason || 'Lý do không xác định'}`);
            return;
        }
        
        // Show loading state
        button.disabled = true;
        button.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            ĐANG KHỞI TẠO...
        `;

        const response = await fetch(`/api/orders/${orderId}/start-chat`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();
        
        if (data.success) {
            // Lưu thông tin order chat vào localStorage hoặc global variable
            window.currentOrderChat = {
                orderId: orderId,
                conversationId: data.conversation_id,
                orderInfo: data.order_info
            };
            
            console.log('✅ Order chat response:', {
                success: data.success,
                conversationId: data.conversation_id,
                messagesCount: data.messages?.length || 0,
                sameConversation: currentConversationId === data.conversation_id
            });
            
            // Mở chat widget với order context
            openOrderChatWidget(data);
            
            // Hiển thị thông báo thành công
            console.log('Order chat started successfully for order:', orderId);
        } else {
            alert(data.message || 'Không thể khởi tạo chat');
        }
    } catch (error) {
        console.error('Error starting order chat:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
    } finally {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

function openOrderChatWidget(chatData) {
    // Mở chat widget hiện tại
    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    
    if (chatToggle && chatBox) {
        chatBox.classList.remove('hidden');
        chatBox.style.display = 'block';
        
        // Kiểm tra conversation hiện tại với debug chi tiết
        const currentConversationId = window.getCurrentConversationId ? window.getCurrentConversationId() : null;
        const currentMessages = window.getCurrentMessages ? window.getCurrentMessages() : [];
        
        console.log('� [DETAILED DEBUG] Conversation comparison:', {
            currentConversationId: currentConversationId,
            currentConversationIdType: typeof currentConversationId,
            newConversationId: chatData.conversation_id,
            newConversationIdType: typeof chatData.conversation_id,
            areEqual: currentConversationId === chatData.conversation_id,
            areEqualLoose: currentConversationId == chatData.conversation_id,
            currentMessagesCount: currentMessages.length,
            allMessagesCount: chatData.messages?.length || 0,
            hasNewMessageOnly: !!chatData.new_message_only,
            newMessageContent: chatData.new_message_only?.content?.substring(0, 50) || 'none'
        });
        
        // Set order context TRƯỚC
        if (window.setOrderContext) {
            window.setOrderContext(chatData.order_info);
        }
        
        // Cập nhật header chat với thông tin đơn hàng
        updateChatHeader(chatData.order_info);
        
        // QUAN TRỌNG: Xử lý trường hợp chưa có conversation ID hiện tại
        if (!currentConversationId) {
            console.log('🆕 [NO CURRENT CONVERSATION] First time opening chat or page refresh');
            console.log('🔄 [STRATEGY] Load existing conversation from server if available');
            
            // Chưa có conversation hiện tại - cần load conversation từ server
            // Nhưng TRƯỚC TIÊN load tin nhắn hiện có (nếu có)
            if (window.loadOrderMessages) {
                // Gọi loadMessages từ API trước để khôi phục conversation
                loadExistingConversationFirst(chatData);
            } else {
                // Fallback - load full conversation
                console.log('⚠️ [FALLBACK] Loading full conversation');
                if (window.loadOrderMessages) {
                    window.loadOrderMessages(chatData.conversation_id, chatData.messages);
                } else {
                    loadMessagesDirectly();
                }
            }
        } else if (currentConversationId === chatData.conversation_id || 
                   currentConversationId.toString() === chatData.conversation_id.toString()) {
            console.log('✅ [SAME CONVERSATION] Preserving existing messages - only adding new order info');
            console.log('🔍 [CHECK] Has new_message_only:', !!chatData.new_message_only);
            console.log('🔍 [CHECK] Current messages count:', currentMessages.length);
            
            // Cùng conversation - CHỈ thêm tin nhắn order mới (nếu có)
            if (chatData.new_message_only && window.loadOrderMessages) {
                console.log('➕ [ADD] New order message only:', chatData.new_message_only.content.substring(0, 50) + '...');
                // CHỈ GỬI MESSAGE MỚI - KHÔNG GỬI TẤT CẢ
                window.loadOrderMessages(chatData.conversation_id, [chatData.new_message_only]);
            } else if (!chatData.new_message_only) {
                console.log('ℹ️ [SKIP] Order already discussed - no new message to add');
                // KHÔNG LÀM GÌ - giữ nguyên conversation hiện tại
            } else {
                console.log('⚠️ [WARNING] loadOrderMessages function not available');
            }
        } else {
            console.log('🔄 [DIFFERENT CONVERSATION] Loading full conversation');
            // Khác conversation hoặc chưa có conversation - load full
            if (window.loadOrderMessages) {
                // GỬI TẤT CẢ MESSAGES cho conversation mới
                window.loadOrderMessages(chatData.conversation_id, chatData.messages);
            } else {
                loadMessagesDirectly();
            }
        }
        
        console.log('Chat widget opened with order context:', {
            orderId: chatData.order_info?.id,
            orderCode: chatData.order_info?.order_code,
            conversationId: chatData.conversation_id
        });
    }
}

// Function để load conversation hiện có trước khi xử lý order chat
async function loadExistingConversationFirst(chatData) {
    try {
        console.log('🔄 [RECOVERY] Attempting to load existing conversation first');
        
        // Gọi API để load conversation hiện có của user
        const response = await fetch('/api/messages?customer_id=' + document.getElementById('chat-user-info')?.dataset.userId, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const conversations = await response.json();
        
        if (conversations.length > 0) {
            const existingConv = conversations[0];
            console.log('✅ [RECOVERY] Found existing conversation:', {
                existingConvId: existingConv.id,
                orderConvId: chatData.conversation_id,
                existingMessages: existingConv.messages?.length || 0,
                isSame: existingConv.id === chatData.conversation_id
            });
            
            if (existingConv.id === chatData.conversation_id) {
                console.log('🎯 [RECOVERY] Same conversation - merging with existing messages');
                
                // Load existing messages trước
                if (window.loadOrderMessages) {
                    window.loadOrderMessages(existingConv.id, existingConv.messages || []);
                }
                
                // Sau đó thêm order message nếu có
                if (chatData.new_message_only) {
                    setTimeout(() => {
                        console.log('➕ [RECOVERY] Adding order message after existing messages loaded');
                        window.loadOrderMessages(chatData.conversation_id, [chatData.new_message_only]);
                    }, 100);
                }
            } else {
                console.log('🔄 [RECOVERY] Different conversation - load order conversation');
                if (window.loadOrderMessages) {
                    window.loadOrderMessages(chatData.conversation_id, chatData.messages);
                }
            }
        } else {
            console.log('🆕 [RECOVERY] No existing conversation - load order conversation as new');
            if (window.loadOrderMessages) {
                window.loadOrderMessages(chatData.conversation_id, chatData.messages);
            }
        }
    } catch (error) {
        console.error('❌ [RECOVERY] Error loading existing conversation:', error);
        // Fallback to normal order chat loading
        if (window.loadOrderMessages) {
            window.loadOrderMessages(chatData.conversation_id, chatData.messages);
        }
    }
}

// Function để load messages trực tiếp từ API nếu cần
async function loadMessagesDirectly() {
    try {
        const response = await fetch('/api/messages?customer_id=' + document.getElementById('chat-user-info')?.dataset.userId, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const conversations = await response.json();
        if (conversations.length > 0) {
            const conv = conversations[0];
            if (window.renderMessages) {
                window.renderMessages(conv.messages || []);
            }
        }
    } catch (error) {
        console.error('Error loading messages directly:', error);
    }
}

// Function backup để render messages nếu cần
function renderOrderMessages(messages) {
    const chatContent = document.getElementById('chat-content');
    if (!chatContent) return;
    
    chatContent.innerHTML = '';
    
    if (!messages.length) {
        chatContent.innerHTML = `
            <div class='flex justify-center mb-4'>
                <div class='bg-white rounded-lg shadow-sm px-4 py-3 max-w-[80%] border border-gray-100'>
                    <p class='text-sm text-gray-700 mb-1'>Cuộc hội thoại đã được khởi tạo!</p>
                    <p class='text-sm text-gray-600'>Hãy gửi tin nhắn để bắt đầu chat về đơn hàng của bạn.</p>
                </div>
            </div>
        `;
        return;
    }
    
    messages.forEach(msg => {
        const isMe = msg.sender_id == document.getElementById('chat-user-info')?.dataset.userId;
        const align = isMe ? 'justify-end' : 'justify-start';
        const bg = isMe ? 'bg-green-100 text-right' : 'bg-white text-left';
        const name = msg.sender_name || 'Unknown';
        
        let messageContent = '';
        if (msg.type === 'system_order_info') {
            // Xử lý tin nhắn thông tin đơn hàng với HTML formatting
            let formattedContent = (msg.content || '').replace(/\n/g, '<br>');
            formattedContent = formattedContent
                .replace(/<strong>(.*?)<\/strong>/g, '<span class="font-bold text-gray-800">$1</span>')
                .replace(/<span class="status-badge">(.*?)<\/span>/g, '<span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">$1</span>')
                .replace(/<span class="price-highlight">(.*?)<\/span>/g, '<span class="font-bold text-green-600">$1</span>')
                .replace(/• /g, '<span class="text-blue-500">•</span> ');
            
            messageContent = `<div class="order-info-message">${formattedContent}</div>`;
        } else {
            messageContent = `<p class="text-sm text-gray-600">${(msg.content || '').replace(/\n/g, '<br>')}</p>`;
        }
        
        const html = `<div class="flex ${align} mb-2">
            <div class="${bg} rounded-lg shadow-sm px-4 py-2 max-w-[80%]">
                <div class="text-xs text-gray-400 mb-1">${name}</div>
                ${messageContent}
                <span class="text-xs text-gray-400">${msg.created_at ? new Date(msg.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) : ''}</span>
            </div>
        </div>`;
        chatContent.insertAdjacentHTML('beforeend', html);
    });
    
    chatContent.scrollTop = chatContent.scrollHeight;
}

function updateChatHeader(orderInfo) {
    // Tìm phần header của chat widget
    const chatHeader = document.querySelector('#chat-box .bg-black');
    if (chatHeader && orderInfo) {
        // Thêm thông tin đơn hàng vào header
        const orderInfoEl = chatHeader.querySelector('.order-info') || document.createElement('div');
        orderInfoEl.className = 'order-info text-xs text-gray-300 mt-1';
        orderInfoEl.innerHTML = `📦 Đơn hàng #${orderInfo.order_code} - ${orderInfo.status}`;
        
        if (!chatHeader.querySelector('.order-info')) {
            chatHeader.appendChild(orderInfoEl);
        }
    }
}

// Helper function để lấy auth token (không cần thiết nữa vì dùng session)
// function getAuthToken() {
//     return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
// }
</script>
