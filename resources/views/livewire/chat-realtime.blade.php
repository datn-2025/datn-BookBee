<div>
    @if ($selectedConversation)
    <div class="chat-container">
        <!-- Chat Header -->
        <div class="chat-header">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-8">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 d-block d-lg-none me-3">
                                        <a href="javascript: void(0);" class="user-chat-remove fs-18 p-1">
                                            <i class="ri-arrow-left-s-line align-bottom"></i>
                                        </a>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $selectedConversation->customer->avatar ? asset('storage/avatars' . $selectedConversation->customer->avatar) : asset('images/default-user.png') }}"
                                                alt="Avatar" class="rounded-circle avatar-xs me-2">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="text-truncate mb-0 fs-16">
                                                    {{ $selectedConversation->customer->name }}
                                                </h5>
                                                <p class="text-truncate text-muted fs-14 mb-0">
                                                    <small>
                                                        @if ($selectedConversation->customer->status === 'online')
                                                            <span class="badge bg-success">Online</span>
                                                        @elseif ($selectedConversation->customer->last_seen)
                                                            <span class="badge bg-warning">
                                                                Hoạt động
                                                                {{ \Carbon\Carbon::parse($selectedConversation->customer->last_seen)->diffForHumans() }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Offline</span>
                                                        @endif
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8 col-4">
                                <ul class="list-inline user-chat-nav text-end mb-0">
                                    <li class="list-inline-item m-0">
                                        <div class="dropdown">
                                            <button class="btn btn-ghost-secondary btn-icon material-shadow-none"
                                                type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" id="admin-search-toggle">
                                                <i data-feather="search" class="icon-sm"></i>
                                            </button>
                                            <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" id="admin-search-dropdown">
                                                <div class="p-3">
                                                    <div class="search-box position-relative">
                                                        <input type="text" class="form-control bg-light border-light pe-5"
                                                            placeholder="Tìm kiếm tin nhắn..." 
                                                            id="searchMessage"
                                                            autocomplete="off">
                                                        <i class="ri-search-2-line search-icon position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                                                        <button type="button" id="admin-clear-search" 
                                                                class="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y me-1 d-none" 
                                                                style="z-index: 10;">
                                                            <i class="ri-close-line text-muted"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Search results info -->
                                                    <div id="admin-search-results-info" class="mt-2 d-none">
                                                        <small class="text-muted">
                                                            <i class="ri-search-line me-1"></i>
                                                            <span id="admin-search-count">0</span> kết quả tìm thấy
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Search instructions -->
                                                    <div id="admin-search-instructions" class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="ri-information-line me-1"></i>
                                                            Nhập từ khóa để tìm kiếm tin nhắn
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-inline-item d-none d-lg-inline-block m-0">
                                        <button type="button"
                                            class="btn btn-ghost-secondary btn-icon material-shadow-none"
                                            data-bs-toggle="offcanvas" data-bs-target="#userProfileCanvasExample"
                                            aria-controls="userProfileCanvasExample">
                                            <i data-feather="info" class="icon-sm"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
        </div>

        <!-- Chat Body -->
        <div class="chat-body">
            <div class="chat-conversation" id="chat-conversation" style="background-color: #f8f9fa;">
                <div id="elmLoader" class="text-center py-4"></div>
            {{-- khi tạo cuộc trò chuyện mới sẽ hiển thị phần này  --}}
            @if($this->isNewConversation())
                <!-- Chat Introduction -->
                <div class="chat-introduction">
                    <div class="celebration-icons">
                        🎉 ✨ 💬 🌟 🎊
                    </div>
                    
                    <div class="user-avatar">
                        <img src="{{ $selectedConversation->customer->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($selectedConversation->customer->name) . '&background=random&color=fff&size=200' }}" 
                             alt="{{ $selectedConversation->customer->name }}" 
                             class="rounded-circle w-100 h-100 object-fit-cover">
                    </div>
                    
                    <h4 class="mb-2">🎉 Chào mừng {{ $selectedConversation->customer->name }}!</h4>
                    
                    <div class="intro-text">
                        <p class="mb-2">Bạn và {{ $selectedConversation->customer->name }} đã kết nối thành công!</p>
                        <div class="user-info mb-3">
                            <p class="mb-1">
                                <i class="bx bx-envelope me-1"></i>{{ $selectedConversation->customer->email }}
                            </p>
                            @if($selectedConversation->customer->phone)
                                <p class="mb-1">
                                    <i class="bx bx-phone me-1"></i>{{ $selectedConversation->customer->phone }}
                                </p>
                            @endif
                            <p class="small mb-0 opacity-75">
                                <i class="bx bx-calendar me-1"></i>
                                Khách hàng từ: {{ $selectedConversation->customer->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <p class="small mb-0 opacity-75">
                            💬 Cuộc trò chuyện này được tạo để hỗ trợ bạn tốt nhất
                        </p>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn start-chat-btn" onclick="scrollToBottom()">
                            <i class="bx bx-message-square-detail me-2"></i>
                            Bắt đầu trò chuyện
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <small class="opacity-75">
                            Chọn một sticker dưới đây để bắt đầu trò chuyện
                        </small>
                    </div>
                    
                    <!-- Quick stickers -->
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <span class="quick-sticker" onclick="sendQuickMessage('👋')" style="font-size: 2rem; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">👋</span>
                        <span class="quick-sticker" onclick="sendQuickMessage('😊')" style="font-size: 2rem; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">😊</span>
                        <span class="quick-sticker" onclick="sendQuickMessage('🤝')" style="font-size: 2rem; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🤝</span>
                        <span class="quick-sticker" onclick="sendQuickMessage('👍')" style="font-size: 2rem; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">👍</span>
                    </div>
                </div>
                
                <!-- Date separator for new conversation -->
                <div class="message-date-separator mt-4">
                    <span class="date-badge">
                        {{ now()->format('d/m/Y') }} - Cuộc trò chuyện bắt đầu
                    </span>
                </div>
            @endif
            
            @php $previousDate = null; @endphp
            <ul class="list-unstyled chat-conversation-list" id="message-container" wire:key="messages-{{ count($chatMessages) }}">
                            @foreach ($chatMessages as $message)
                                @php
                                    $currentUser = Auth::guard('admin')->user() ?: Auth::user();
                                    $currentUserId = $currentUser ? $currentUser->id : null;
                                    $isMine = $message->sender_id === $currentUserId;
                                    $isAdmin = $message->sender && method_exists($message->sender, 'isAdmin') ? $message->sender->isAdmin() : false;
                                    $side = $isMine ? 'right' : 'left';
                                    $bgColor = $isMine ? 'bg-primary text-white' : 'bg-light text-dark';
                                    $date = $message->created_at->format('Y-m-d');
                                @endphp
                                {{-- Hiển thị ngày nếu khác ngày trước đó --}}
                                @if ($date !== $previousDate)
                                    <li class="my-4">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <hr class="flex-grow-1 border-top border-light mx-2" />
                                            <span class="badge bg-light text-dark shadow-sm px-3 py-1">
                                                {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : (\Carbon\Carbon::parse($date)->isYesterday() ? 'Yesterday' : \Carbon\Carbon::parse($date)->translatedFormat('d F Y')) }}
                                            </span>
                                            <hr class="flex-grow-1 border-top border-light mx-2" />
                                        </div>
                                    </li>
                                    @php $previousDate = $date; @endphp
                                @endif

                                <li class="chat-list {{ $side }}" id="message-{{ $message->id }}">
                                    <div class="conversation-list">
                                        @if (!$isMine)
                                            <div class="chat-avatar">
                                                <img src="{{ $message->sender->avatar ? asset('storage/avatars/' . $message->sender->avatar) : asset('images/default-user.png') }}"
                                                    alt="User" class="avatar-xs rounded-circle">
                                            </div>
                                        @endif

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content {{ $bgColor }}"
                                                    id="{{ $message->id }}"
                                                    style="border-radius: 10px; padding: 10px;">

                                                    {{-- Hiển thị reply nếu có --}}
                                                    @if ($message->replyToMessage)
                                                        <div class="reply-reference mb-2 p-2 rounded" style="background-color: rgba(255,255,255,0.1); border-left: 3px solid {{ $isMine ? '#fff' : '#007bff' }};">
                                                            <small class="text-muted d-block mb-1">
                                                                <i class="ri-reply-line me-1"></i>
                                                                Trả lời {{ $message->replyToMessage->sender->name }}
                                                            </small>
                                                            <div class="text-muted small" style="font-style: italic;">
                                                                {{ Str::limit($message->replyToMessage->content, 50) }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Hiển thị theo loại tin nhắn --}}
                                                    @if ($message->type === 'image')
                                                        @if ($message->file_path)
                                                            <div class="message-image mb-2"
                                                                style="cursor:pointer; max-width:200px; position: relative;">
                                                                <img src="{{ asset('storage/' . $message->file_path) }}"
                                                                    alt="Image" class="img-fluid rounded shadow-sm"
                                                                    style="max-width:100%; max-height:200px; object-fit: cover; transition: transform 0.2s;"
                                                                    onclick="openImageModal('{{ asset('storage/' . $message->file_path) }}', '{{ $message->content ?? 'Image' }}')"
                                                                    onmouseover="this.style.transform='scale(1.02)'"
                                                                    onmouseout="this.style.transform='scale(1)'">
                                                                
                                                                <!-- Zoom icon overlay -->
                                                                <div class="position-absolute top-0 end-0 m-1 bg-dark bg-opacity-50 rounded-circle p-1" 
                                                                     style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                                                    <i class="bx bx-expand-alt text-white" style="font-size: 12px;"></i>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($message->content)
                                                            <p class="mb-0 ctext-content">{{ $message->content }}</p>
                                                        @endif
                                                    @elseif($message->type === 'file')
                                                        {{-- Hiển thị file --}}
                                                        @if ($message->file_path)
                                                            <div class="message-file d-flex align-items-center p-2 mb-2 rounded border" 
                                                                 style="background-color: rgba(255,255,255,0.1); max-width: 250px;">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <i class="bx bx-file fs-3 text-primary"></i>
                                                                </div>
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <p class="mb-0 fw-medium text-truncate">{{ $message->content }}</p>
                                                                    <small class="text-muted">Click to download</small>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    <a href="{{ asset('storage/' . $message->file_path) }}" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-outline-primary rounded-circle"
                                                                       title="Download file">
                                                                        <i class="bx bx-download"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($message->content && $message->content !== basename($message->file_path))
                                                            <p class="mb-0 ctext-content">{{ $message->content }}</p>
                                                        @endif
                                                    @elseif($message->type === 'system_order_info')
                                                        {{-- Tin nhắn thông tin đơn hàng với styling đặc biệt --}}
                                                        <div class="order-info-message" style="background: linear-gradient(135deg, #81ecec 0%, #74b9ff 50%, #0984e3 100%); border-radius: 15px; padding: 20px; color: white; box-shadow: 0 8px 32px rgba(116, 185, 255, 0.25); border: 1px solid rgba(255, 255, 255, 0.1);">
                                                            @php
                                                                $lines = explode("\n", $message->content);
                                                                $header = array_shift($lines); // Lấy dòng đầu tiên
                                                            @endphp
                                                            
                                                            {{-- Header với icon --}}
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="me-3" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                                                    <i class="bx bx-package" style="font-size: 24px; color: white;"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 text-white fw-bold">{{ $header }}</h6>
                                                                    <small style="color: rgba(255,255,255,0.85);">Yêu cầu hỗ trợ đơn hàng</small>
                                                                </div>
                                                            </div>
                                                            
                                                            {{-- Nội dung đơn hàng --}}
                                                            <div class="order-details" style="background: rgba(255,255,255,0.15); border-radius: 12px; padding: 16px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(20px);">
                                                                @foreach($lines as $line)
                                                                    @if(trim($line))
                                                                        @if(str_contains($line, '🛒'))
                                                                            <div class="order-code mb-3">
                                                                                <h6 class="text-white fw-bold mb-0" style="font-size: 18px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">{{ trim($line) }}</h6>
                                                                            </div>
                                                                        @elseif(str_contains($line, '📋'))
                                                                            <div class="order-details-header mb-2">
                                                                                <strong style="color: rgba(255,255,255,0.95); font-size: 14px;">{{ trim($line) }}</strong>
                                                                            </div>
                                                                        @elseif(str_contains($line, '•'))
                                                                            <div class="order-detail-item d-flex align-items-center mb-2">
                                                                                <span class="me-2" style="color: #ffeaa7; font-size: 10px; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">●</span>
                                                                                <span style="color: rgba(255,255,255,0.95); font-size: 13px;">{{ trim(str_replace('•', '', $line)) }}</span>
                                                                            </div>
                                                                        @elseif(str_contains($line, '🙏'))
                                                                            <div class="order-help-request mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.25);">
                                                                                <div class="d-flex align-items-center">
                                                                                    <span style="font-size: 20px; margin-right: 8px;">🙏</span>
                                                                                    <span style="color: rgba(255,255,255,0.95); font-weight: 500;">{{ trim(str_replace('🙏', '', $line)) }}</span>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                            
                                                            {{-- Footer với timestamp --}}
                                                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                                                <small style="color: rgba(255,255,255,0.8);">
                                                                    <i class="bx bx-time-five me-1"></i>
                                                                    {{ $message->created_at->format('H:i - d/m/Y') }}
                                                                </small>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="badge" style="background: rgba(255,255,255,0.25); color: white; font-size: 11px; backdrop-filter: blur(10px);">
                                                                        <i class="bx bx-support me-1"></i>Cần hỗ trợ
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- Tin nhắn text thông thường --}}
                                                        <p class="mb-0 ctext-content">{{ $message->content }}</p>
                                                    @endif
                                                </div>
                                                {{-- Dropdown menu cho tin nhắn --}}
                                                <div class="dropdown align-self-start message-box-drop">
                                                    <a class="dropdown-toggle" href="#" role="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i>
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item reply-message" href="#" 
                                                           onclick="event.preventDefault(); replyToMessageAndSet('{{ $message->id }}', '{{ $message->sender->name }}', '{{ addslashes($message->content) }}');">
                                                            <i class="ri-reply-line me-2 text-muted align-bottom"></i>Reply
                                                        </a>
                                                        <a class="dropdown-item delete-item" href="#" 
                                                           onclick="deleteMessage('{{ $message->id }}')">
                                                            <i class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-name">
                                                <span class="d-none name">{{ $message->sender->name }}</span>
                                                <small
                                                    class="text-muted time">{{ $message->created_at->format('h:i A') }}</small>

                                                @if ($isMine)
                                                    @if ($message->reads->where('user_id', '!=', $currentUserId)->count() > 0)
                                                        {{-- Đã đọc: 2 dấu check màu xanh --}}
                                                        <span class="text-success" title="Đã xem"><i
                                                                class="bx bx-check-double"></i></span>
                                                    @else
                                                        {{-- Chưa đọc: 1 dấu check màu xám --}}
                                                        <span class="text-muted" title="Đã gửi"><i
                                                                class="bx bx-check"></i></span>
                                                    @endif
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div id="chat-end" class="text-center mt-4" style="display:none;">
                            <p class="text-muted mb-0">End of conversation</p>
                        </div>
                        
                        <!-- Typing Indicator -->
                        <div id="typing-indicator" class="typing-indicator-container px-3" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs me-2">
                                    <img src="{{ $selectedConversation->customer->avatar ? asset('storage/avatars/' . $selectedConversation->customer->avatar) : asset('images/default-user.png') }}" 
                                         alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="typing-animation">
                                    <span class="typing-dot"></span>
                                    <span class="typing-dot"></span>
                                    <span class="typing-dot"></span>
                                </div>
                                <small class="text-muted ms-2">đang soạn tin nhắn...</small>
                            </div>
                        </div>
            </div>
        </div>

        <!-- Chat Footer -->
        <div class="chat-footer">
            <!-- Reply Preview -->
            <div id="reply-preview" class="reply-preview d-none" style="margin: 8px;">
                <div class="reply-to" id="reply-to-name"></div>
                <div class="reply-content" id="reply-content"></div>
                <span class="close-reply" onclick="cancelReply()">&times;</span>
            </div>
            
            <!-- Giao diện form -->
            <form wire:submit.prevent="sendMessage">
                <div class="row g-0 align-items-center">
                    <!-- Emoji -->
                    <div class="col-auto pe-2" style="position: relative;">
                        <button type="button" class="btn btn-light rounded-circle p-2" id="emoji-toggle">
                            <i class="bx bx-smile fs-4 text-primary"></i>
                        </button>
                        
                        <!-- Emoji Picker -->
                        <div id="emoji-picker" class="hidden" style="position: absolute; bottom: 50px; left: 0; width: 280px; max-height: 200px; overflow-y: auto; z-index: 99999; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); padding: 12px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Choose an emoji</small>
                                <button type="button" class="btn btn-sm btn-link p-0" onclick="document.getElementById('emoji-picker').classList.add('hidden')">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                            <div class="grid" style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 4px;">
                                <!-- Emojis will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- File upload -->
                    <div class="col-auto pe-2">
                        <label for="fileUpload" class="btn btn-light rounded-circle p-2" style="cursor: pointer;">
                            <i class="bx bx-plus fs-4 text-primary"></i>
                        </label>
                        <input type="file" id="fileUpload" wire:model="fileUpload" class="d-none"
                            accept="image/*,application/*">
                    </div>

                    <!-- Nhập tin nhắn + ảnh -->
                    <div class="col px-2">
                        <div class="bg-light rounded shadow-sm p-2 d-flex flex-row align-items-center gap-2 position-relative">
                            {{-- File preview --}}
                            @if ($fileUpload)
                                <div class="position-relative me-2" style="max-width: 80px; min-width: 60px;">
                                    @if (method_exists($fileUpload, 'getMimeType') && Str::startsWith($fileUpload->getMimeType(), 'image/'))
                                        <img src="{{ $fileUpload->temporaryUrl() }}" class="img-thumbnail" alt="Preview"
                                            style="max-width: 100%; max-height: 60px;">
                                    @else
                                        <div class="bg-secondary text-white text-center p-2 rounded" style="font-size: 12px;">
                                            📎 {{ Str::limit($fileUpload->getClientOriginalName(), 10) }}
                                        </div>
                                    @endif
                                    <button type="button"
                                        class="btn btn-sm btn-light position-absolute top-0 end-0 rounded-circle"
                                        wire:click="$set('fileUpload', null)" style="transform: translate(50%, -50%);">
                                        &times;
                                    </button>
                                </div>
                            @endif

                            <input type="text"
                                class="form-control chat-input bg-white border-0 rounded-pill shadow-sm flex-grow-1"
                                placeholder="{{ $fileUpload ? 'Nhấn Enter để gửi file...' : 'Type your message...' }}" 
                                wire:model="message_content"
                                autocomplete="off"
                                id="messageInputField"
                                wire:keydown.enter="sendMessage"
                                onkeydown="handleEnterKey(event)">
                        </div>
                    </div>

                    <!-- Gửi -->
                    <div class="col-auto ps-2">
                        <button type="submit" class="btn btn-primary rounded-circle p-2" 
                                wire:loading.attr="disabled">
                            <i class="bx bx-send text-white" wire:loading.remove></i>
                            <i class="bx bx-loader-alt bx-spin text-white" wire:loading></i>
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- reply preview -->
            <div class="replyCard" style="display: none;">
                <div class="card mb-0">
                    <div class="card-body py-3">
                        <div class="replymessage-block mb-0 d-flex align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="conversation-name"></h5>
                                <p class="mb-0"></p>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="button" id="close_toggle"
                                    class="btn btn-sm btn-link mt-n2 me-n3 fs-18">
                                    <i class="bx bx-x align-middle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Offcanvas for user profile --}}
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="userProfileCanvasExample" aria-modal="true"
        role="dialog" style="width: 400px;">
        <div class="offcanvas-body profile-offcanvas p-0">
            <!-- Header với background image -->
            <div class="position-relative">
                <div class="profile-cover position-relative"
                    style="height: 200px; background-image: url('{{ asset('images/golden-gate.jpg') }}'); background-size: cover; background-position: center;">
                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3);"></div>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="offcanvas" aria-label="Close"></button>

                <!-- Avatar centered between cover and white background -->
                <div class="text-center"
                    style="position: absolute; left: 50%; bottom: 0; transform: translate(-50%, 50%);">
                    <div class="position-relative d-inline-block">
                        <img src="{{ $selectedConversation->customer->avatar ?? asset('images/default-user.png') }}"
                            alt="Avatar" class="avatar-lg img-thumbnail rounded-circle mx-auto profile-img"`
                            style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                    </div>
                </div>
            </div>

            <!-- White background content -->
            <div class="bg-white" style="padding-top: 75px;">
                <!-- Name and Status -->
                <div class="text-center mb-4">
                    <h4 class="mb-1">{{ $selectedConversation->customer->name }}</h4>
                    <p class="text-success mb-0">
                        <small>
                            @if ($selectedConversation->customer->status === 'online')
                                <span class="badge bg-success">Online</span>
                            @elseif ($selectedConversation->customer->last_seen)
                                <span class="badge bg-warning">
                                    Hoạt động
                                    {{ \Carbon\Carbon::parse($selectedConversation->customer->last_seen)->diffForHumans() }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Offline</span>
                            @endif
                        </small>
                    </p>
                        <!-- Contact Information -->
                        <div class="px-4">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="ri-phone-line fs-4 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-muted mb-0">Phone Number</p>
                                            <h6 class="mb-0">{{ $selectedConversation->customer->phone }}</h6>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="ri-mail-line fs-4 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-muted mb-0">Email Address</p>
                                            <h6 class="mb-0">{{ $selectedConversation->customer->email }}</h6>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-map-pin-line fs-4 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="text-muted mb-0">Location</p>
                                            <h6 class="mb-0">{{ $selectedConversation->customer->location }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="d-flex gap-2 mb-4">
                                <button class="btn btn-primary flex-grow-1">
                                    <i class="ri-message-3-line me-1"></i> Message
                                </button>
                                <button class="btn btn-light flex-grow-1">
                                    <i class="ri-user-follow-line me-1"></i> Follow
                                </button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="d-flex align-items-center justify-content-center h-100">
            <div class="text-center">
                <i class="bx bx-message-square-dots" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                <h4 class="text-muted">Chưa chọn cuộc trò chuyện</h4>
                <p class="text-muted">Vui lòng chọn cuộc trò chuyện từ danh sách bên trái</p>
            </div>
        </div>
    @endif

    <!-- Đặt modal duy nhất ở cuối file, ngoài vòng lặp -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="Image"
                        style="max-width:100%; max-height:70vh; object-fit:contain;" />
                </div>
            </div>
        </div>
    </div>

    <!-- Script section - di chuyển vào trong div root -->
    <script>
        // Setup biến cho chat-realtime.js
        window.currentConversationId = '{{ $selectedConversation->id ?? null }}';
        
        // Custom CSS cho order info messages
        const orderInfoStyles = `
            <style>
                .order-info-message {
                    position: relative;
                    overflow: hidden;
                    animation: orderMessageSlideIn 0.5s ease-out;
                    transform-origin: left center;
                }
                
                .order-info-message::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                    animation: shimmer 2s infinite;
                }
                
                @keyframes orderMessageSlideIn {
                    from {
                        opacity: 0;
                        transform: translateX(-20px) scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0) scale(1);
                    }
                }
                
                @keyframes shimmer {
                    from {
                        left: -100%;
                    }
                    to {
                        left: 100%;
                    }
                }
                
                .order-info-message:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 12px 40px rgba(116, 185, 255, 0.4) !important;
                    transition: all 0.3s ease;
                }
                
                .order-detail-item {
                    transition: all 0.2s ease;
                }
                
                .order-detail-item:hover {
                    transform: translateX(5px);
                    background: rgba(255,255,255,0.1);
                    border-radius: 6px;
                    padding: 4px 8px;
                    margin: 2px 0;
                }
                
                .order-details {
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                }
                
                /* Pulse animation cho badge cần hỗ trợ */
                @keyframes pulse {
                    0% {
                        box-shadow: 0 0 0 0 rgba(116, 185, 255, 0.4);
                    }
                    70% {
                        box-shadow: 0 0 0 10px rgba(116, 185, 255, 0);
                    }
                    100% {
                        box-shadow: 0 0 0 0 rgba(116, 185, 255, 0);
                    }
                }
                
                .order-info-message .badge {
                    animation: pulse 2s infinite;
                    background: rgba(116, 185, 255, 0.3) !important;
                }
                
                /* Responsive cho mobile */
                @media (max-width: 768px) {
                    .order-info-message {
                        padding: 15px !important;
                        margin: 10px !important;
                    }
                    
                    .order-info-message h6 {
                        font-size: 16px !important;
                    }
                }
            </style>
        `;
        
        // Inject CSS vào head
        if (!document.querySelector('#order-info-styles')) {
            const styleElement = document.createElement('div');
            styleElement.id = 'order-info-styles';
            styleElement.innerHTML = orderInfoStyles;
            document.head.appendChild(styleElement);
        }
        
        // Emoji list for admin chat
        const adminEmojis = [
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
        
        // Global auto-scroll function - Improved
        function scrollToBottom() {
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                // Scroll với smooth behavior
                chatContainer.scrollTo({
                    top: chatContainer.scrollHeight,
                    behavior: 'smooth'
                });
            }
        }
        
        // Force scroll without smooth behavior (for immediate actions)
        function forceScrollToBottom() {
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;            }
        }
        
        // Handle Enter key for sending messages (Enhanced for file support)
        function handleEnterKey(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();              
                
                // Check if there's a file or message content
                const messageContent = document.getElementById('messageInputField')?.value.trim();
                const fileInput = document.getElementById('fileUpload');
                const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
                
                // Send if there's content or file
                if (messageContent || hasFile) {
                    
                    if (window.Livewire && @this) {
                        @this.call('sendMessage').then(() => {
                            // Force scroll after sending
                            setTimeout(forceScrollToBottom, 100);
                        });
                    }
                } else {
                    console.log('❌ No content or file to send');
                }
            }
        }
        
        // Send quick message (sticker/emoji)
        function sendQuickMessage(content) {
            if (window.Livewire && @this) {
                @this.set('message_content', content);
                @this.call('sendMessage').then(() => {
                    setTimeout(forceScrollToBottom, 100);
                });
            }
        }
        
        // Reply to message function
        function replyToMessage(messageId, senderName, content) {
            const replyPreview = document.getElementById('reply-preview');
            const replyToName = document.getElementById('reply-to-name');
            const replyContent = document.getElementById('reply-content');
            
            if (replyPreview && replyToName && replyContent) {
                replyToName.textContent = `Trả lời ${senderName}`;
                replyContent.textContent = content.length > 50 ? content.substring(0, 50) + '...' : content;
                
                replyPreview.classList.remove('d-none');
                replyPreview.classList.add('show');
                replyPreview.style.display = 'block';
                               
                // Focus input
                const messageInput = document.getElementById('messageInputField');
                if (messageInput) {
                    messageInput.focus();
                }
            }
        }
        
        // Combined function to handle both UI and Livewire
        function replyToMessageAndSet(messageId, senderName, content) {
            // Store reply data in localStorage
            const replyData = {
                messageId: messageId,
                senderName: senderName,
                content: content,
                timestamp: Date.now()
            };
            localStorage.setItem('chatReplyData', JSON.stringify(replyData));
            
            // Show UI immediately
            replyToMessage(messageId, senderName, content);
            
            // Set Livewire data
            if (window.Livewire && @this) {
                try {
                    @this.call('setReplyTo', messageId);
                } catch (error) {
                    console.error('❌ Livewire setReplyTo error:', error);
                }
            }
        }
        
        // Cancel reply function
        function cancelReply() {
            localStorage.removeItem('chatReplyData');
            
            const replyPreview = document.getElementById('reply-preview');
            if (replyPreview) {
                replyPreview.classList.add('d-none');
                replyPreview.classList.remove('show');
                replyPreview.style.display = 'none';
            }
            
            if (window.Livewire && @this) {
                @this.call('cancelReply');
            }
        }
        
        // Delete message function
        function deleteMessage(messageId) {
            if (confirm('Bạn có chắc chắn muốn xóa tin nhắn này?')) {
                try {
                    @this.deleteMessage(messageId);
                } catch (error) {
                    console.error('❌ Livewire deleteMessage error:', error);
                }
            }
        }
        
        // Open image modal for preview
        function openImageModal(imageSrc, title = 'Image Preview') {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('imageModalLabel');
            
            if (modal && modalImage && modalTitle) {
                modalImage.src = imageSrc;
                modalTitle.textContent = title;
                
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            }
        }
        
        // Alternative function name for compatibility
        function showImageModal(imageSrc, title = 'Image Preview') {
            openImageModal(imageSrc, title);
        }
        
        // Restore reply preview from localStorage
        function restoreReplyPreview() {
            const storedReply = localStorage.getItem('chatReplyData');
            if (storedReply) {
                try {
                    const replyData = JSON.parse(storedReply);
                    if (Date.now() - replyData.timestamp < 30 * 60 * 1000) {
                        replyToMessage(replyData.messageId, replyData.senderName, replyData.content);
                    } else {
                        localStorage.removeItem('chatReplyData');
                    }
                } catch (error) {
                    localStorage.removeItem('chatReplyData');
                }
            }
        }
        
        // Emoji Picker functionality
        let emojiPickerInitialized = false;
        
        // Enhanced initializeEmojiPicker with better DOM handling
        function initializeEmojiPicker() {
            
            if (emojiPickerInitialized) {
                 return;
            }
            
            const emojiToggle = document.getElementById('emoji-toggle');
            const emojiPicker = document.getElementById('emoji-picker');
            const emojiGrid = emojiPicker?.querySelector('.grid');
            const messageInput = document.getElementById('messageInputField');
            
            if (!emojiToggle || !emojiPicker || !emojiGrid) {
                console.log('❌ Emoji picker elements not found');
                return;
            }
            
            // Clear any existing onclick handlers
            emojiToggle.onclick = null;
            emojiToggle.removeAttribute('onclick');
            
            // Populate emoji grid if empty
            if (emojiGrid.children.length === 0) {
                adminEmojis.forEach(emoji => {
                    // Use SPAN instead of BUTTON to avoid any input-like behavior
                    const emojiSpan = document.createElement('span');
                    emojiSpan.className = 'emoji-btn';
                    emojiSpan.textContent = emoji;
                    emojiSpan.setAttribute('title', emoji);
                    emojiSpan.setAttribute('role', 'button');
                    emojiSpan.setAttribute('tabindex', '0');
                    
                    // Complete prevention attributes
                    emojiSpan.setAttribute('data-emoji', emoji);
                    emojiSpan.setAttribute('unselectable', 'on');
                    emojiSpan.setAttribute('onselectstart', 'return false;');
                    emojiSpan.setAttribute('ondragstart', 'return false;');
                    
                    // CSS styles for emoji-like button appearance
                    emojiSpan.style.cssText = `
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 30px;
                        height: 30px;
                        border-radius: 4px;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        background-color: transparent;
                        border: 1px solid transparent;
                        font-size: 1.2rem;
                        line-height: 1;
                        user-select: none;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        -webkit-touch-callout: none;
                        -webkit-tap-highlight-color: transparent;
                        pointer-events: auto;
                    `;
                    
                    // SIMPLE CLICK HANDLER - No complex event prevention
                    emojiSpan.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        if (messageInput) {
                            const currentValue = messageInput.value || '';
                            const cursorPos = messageInput.selectionStart || currentValue.length;
                            const newValue = currentValue.slice(0, cursorPos) + emoji + currentValue.slice(cursorPos);
                            
                            messageInput.value = newValue;
                            messageInput.focus();
                            
                            setTimeout(() => {
                                messageInput.setSelectionRange(cursorPos + emoji.length, cursorPos + emoji.length);
                            }, 10);
                            
                            if (window.Livewire && @this) {
                                @this.set('message_content', newValue);
                            }
                        }
                        
                        emojiPicker.classList.add('hidden');
                    };
                    
                    // Hover effects
                    emojiSpan.onmouseenter = function() {
                        this.style.backgroundColor = '#f3f4f6';
                        this.style.transform = 'scale(1.1)';
                        this.style.borderColor = '#e5e7eb';
                    };
                    
                    emojiSpan.onmouseleave = function() {
                        this.style.backgroundColor = 'transparent';
                        this.style.transform = 'scale(1)';
                        this.style.borderColor = 'transparent';
                    };
                    
                    // Active effect
                    emojiSpan.onmousedown = function() {
                        this.style.transform = 'scale(0.95)';
                        this.style.backgroundColor = '#e5e7eb';
                    };
                    
                    emojiSpan.onmouseup = function() {
                        this.style.transform = 'scale(1.1)';
                        this.style.backgroundColor = '#f3f4f6';
                    };
                    
                    emojiGrid.appendChild(emojiSpan);
                });
            }
            
            // Create new toggle handler using proper event attachment
            const toggleHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isHidden = emojiPicker.classList.contains('hidden');
                
                if (isHidden) {
                    emojiPicker.classList.remove('hidden');
                    emojiPicker.style.display = 'block';
                    emojiPicker.style.visibility = 'visible';
                    emojiPicker.style.opacity = '1';
                } else {
                    emojiPicker.classList.add('hidden');
                    emojiPicker.style.display = 'none';
               }
            };
            
            // Attach event listener properly
            emojiToggle.addEventListener('click', toggleHandler, true);
            
            // Store reference to handler for cleanup
            emojiToggle._emojiToggleHandler = toggleHandler;
            
            // Close on outside click
            if (!window.emojiPickerOutsideClickAdded) {
                document.addEventListener('click', function(e) {
                    const currentEmojiToggle = document.getElementById('emoji-toggle');
                    const currentEmojiPicker = document.getElementById('emoji-picker');
                    
                    if (currentEmojiToggle && currentEmojiPicker && 
                        !currentEmojiToggle.contains(e.target) && !currentEmojiPicker.contains(e.target)) {
                        if (!currentEmojiPicker.classList.contains('hidden')) {
                            currentEmojiPicker.classList.add('hidden');
                        }
                    }
                }, true);
                window.emojiPickerOutsideClickAdded = true;
            }
            
            emojiPickerInitialized = true;
        }
        
        // Enhanced reset function with proper cleanup
        function resetEmojiPicker() {
            emojiPickerInitialized = false;
            
            // Clean up existing handlers
            const emojiToggle = document.getElementById('emoji-toggle');
            if (emojiToggle) {
                // Remove existing event listener if exists
                if (emojiToggle._emojiToggleHandler) {
                    emojiToggle.removeEventListener('click', emojiToggle._emojiToggleHandler, true);
                    delete emojiToggle._emojiToggleHandler;
                }
                emojiToggle.onclick = null;
                emojiToggle.removeAttribute('onclick');
            }
            
            // Re-initialize after cleanup
            setTimeout(() => {
                initializeEmojiPicker();
            }, 50);
        }
        
        // DOM Observer to watch for changes and re-initialize emoji picker
        let domObserver = null;
        
        function setupDOMObserver() {
            if (domObserver) {
                domObserver.disconnect();
            }
            
            const targetNode = document.querySelector('.chat-footer');
            if (!targetNode) return;
            
            domObserver = new MutationObserver(function(mutations) {
                let needsReinit = false;
                
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' || mutation.type === 'subtree') {
                        // Check if emoji picker elements still exist and have proper handlers
                        const emojiToggle = document.getElementById('emoji-toggle');
                        const emojiPicker = document.getElementById('emoji-picker');
                        
                        if (emojiToggle && emojiPicker) {
                            // Check if onclick handler is missing (sign of DOM update)
                            if (!emojiToggle.onclick || typeof emojiToggle.onclick !== 'function') {
                                needsReinit = true;
                            }
                        }
                    }
                });
                
                if (needsReinit) {
                    console.log('🔄 DOM changed, re-initializing emoji picker...');
                    setTimeout(() => {
                        emojiPickerInitialized = false;
                        initializeEmojiPicker();
                    }, 100);
                }
            });
            
            domObserver.observe(targetNode, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class', 'style']
            });
        }
        
        // Enhanced reset function
        function resetEmojiPicker() {
             emojiPickerInitialized = false;
            window.emojiPickerOutsideClickAdded = false;
            
            // Clear any existing handlers
            const emojiToggle = document.getElementById('emoji-toggle');
            if (emojiToggle) {
                emojiToggle.onclick = null;
                emojiToggle.removeAttribute('onclick');
            }
            
            // Re-initialize
            setTimeout(() => {
                initializeEmojiPicker();
            }, 50);
        }
        
        // Main initialization - SINGLE DOMContentLoaded listener
        document.addEventListener('DOMContentLoaded', function() {
            
            // Initialize emoji picker
            initializeEmojiPicker();
            
            // Setup DOM observer
            setupDOMObserver();
            
            // Restore reply preview
            restoreReplyPreview();
            
            // Auto scroll on page load
            setTimeout(() => {
                forceScrollToBottom();
            }, 500);
            
            // Setup Livewire listeners
            if (window.Livewire) {
                // Auto scroll after message sent
                window.Livewire.on('messageProcessed', function() {
                    localStorage.removeItem('chatReplyData');
                    setTimeout(forceScrollToBottom, 200);
                });
                
                // Auto scroll after messages updated + FORCE emoji picker reset
                window.Livewire.hook('component.updated', (component) => {
                     
                    // ALWAYS reset emoji picker after Livewire update
                    setTimeout(() => {
                        resetEmojiPicker();
                    }, 100);
                    
                    // Auto scroll after DOM update
                    setTimeout(scrollToBottom, 300);
                });
                
                // Handle reply preview events
                window.Livewire.on('showReplyPreview', function(event) {
                    const data = Array.isArray(event) ? event[0] : event;
                    if (data?.messageId && data?.senderName && data?.content) {
                        replyToMessage(data.messageId, data.senderName, data.content);
                    }
                });
                
                window.Livewire.on('hideReplyPreview', function() {
                    localStorage.removeItem('chatReplyData');
                    const replyPreview = document.getElementById('reply-preview');
                    if (replyPreview) {
                        replyPreview.classList.add('d-none');
                        replyPreview.style.display = 'none';
                    }
                });
                
                // Handle alerts
                window.Livewire.on('showAlert', function(data) {
                    if (data.type === 'success') {
                        toastr.success(data.message);
                    } else if (data.type === 'error') {
                        toastr.error(data.message);
                    }
                });
            }
        });
        
        // Handle quick sticker animations
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.quick-sticker').forEach(sticker => {
                sticker.addEventListener('click', function() {
                    this.style.transform = 'scale(1.5)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });
        
        // Make functions globally available
        window.scrollToBottom = scrollToBottom;
        window.forceScrollToBottom = forceScrollToBottom;
        window.sendQuickMessage = sendQuickMessage;
        window.replyToMessageAndSet = replyToMessageAndSet;
        window.cancelReply = cancelReply;
        window.deleteMessage = deleteMessage;
        window.openImageModal = openImageModal;
        window.showImageModal = showImageModal;
        window.resetEmojiPicker = resetEmojiPicker;
        
        console.log('✅ Chat Realtime Scripts Loaded');
    </script>

</div>
