<div>
    <style>
        .message-image img {
            transition: transform 0.2s;
        }

        .message-image img:hover {
            transform: scale(1.05);
        }

        .message-file {
            border: 1px solid #e0e0e0;
            border-radius: 8px;      
        }

        .message-file:hover {
            background-color: #f8f9fa !important;
        }

        /* Typing animation */
        .typing-animation {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #6c757d;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typing {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Chat introduction styles */
        .chat-introduction {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 1rem;
            text-align: center;
            color: white;
            border-radius: 15px;
            margin: 1rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .chat-introduction::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shine 3s infinite;
        }

        .chat-introduction .celebration-icons {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
            z-index: 2;
            position: relative;
        }

        .chat-introduction .user-avatar {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(255,255,255,0.3);
            margin: 0 auto 1rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            z-index: 2;
            position: relative;
            transition: transform 0.3s ease;
        }

        .chat-introduction .user-avatar:hover {
            transform: scale(1.1);
        }

        .chat-introduction .intro-text {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            opacity: 0.9;
            z-index: 2;
            position: relative;
        }

        .chat-introduction .start-chat-btn {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 2;
            position: relative;
        }

        .chat-introduction .start-chat-btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .quick-sticker {
            padding: 0.5rem;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            margin: 0 0.25rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .quick-sticker:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.2) rotate(10deg);
        }

        .quick-sticker:active {
            transform: scale(1.5) rotate(0deg);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        @keyframes shine {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .chat-introduction {
                margin: 0.5rem;
                padding: 1.5rem 0.75rem;
            }
            
            .chat-introduction .user-avatar {
                width: 60px;
                height: 60px;
            }
            
            .chat-introduction .intro-text {
                font-size: 1rem;
            }
            
            .chat-introduction .celebration-icons {
                font-size: 1.2rem;
            }
        }

        /* Message day separator */
        .message-date-separator {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .message-date-separator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e5e5;
        }

        .message-date-separator .date-badge {
            background: #f8f9fa;
            padding: 0.25rem 1rem;
            border-radius: 15px;
            font-size: 0.8rem;
            color: #6c757d;
            position: relative;
            display: inline-block;
        }

        /* Auto scroll */
        .chat-conversation {
            scroll-behavior: smooth;
        }

        /* Message transitions */
        .chat-list {
            animation: fadeInUp 0.3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading state */
        .btn[wire\:loading] {
            pointer-events: none;
        }

        /* Admin Emoji Picker Styles */
        #emoji-picker {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
        }

        #emoji-picker.hidden {
            display: none !important;
        }

        #emoji-picker .grid button {
            transition: all 0.2s ease;
        }

        #emoji-picker .grid button:hover {
            background-color: #f3f4f6 !important;
            transform: scale(1.1);
        }

        #emoji-picker .grid button:active {
            transform: scale(0.95);
        }

        /* Responsive emoji picker */
        @media (max-width: 768px) {
            #emoji-picker {
                width: 240px !important;
                max-height: 160px !important;
            }
            
            #emoji-picker .grid {
                grid-template-columns: repeat(6, 1fr) !important;
            }
        }
    </style>

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
                                                aria-expanded="false">
                                                <i data-feather="search" class="icon-sm"></i>
                                            </button>
                                            <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                                                <div class="p-2">
                                                    <div class="search-box">
                                                        <input type="text" class="form-control bg-light border-light"
                                                            placeholder="Search here..." onkeyup="searchMessages()"
                                                            id="searchMessage">
                                                        <i class="ri-search-2-line search-icon"></i>
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
                                    <li class="list-inline-item m-0">
                                        <div class="dropdown">
                                            <button class="btn btn-ghost-secondary btn-icon material-shadow-none"
                                                type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i data-feather="more-vertical" class="icon-sm"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item d-block d-lg-none user-profile-show"
                                                    href="#"><i
                                                        class="ri-user-2-fill align-bottom text-muted me-2"></i>View
                                                    Profile</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="ri-inbox-archive-line align-bottom text-muted me-2"></i>Archive</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="ri-mic-off-line align-bottom text-muted me-2"></i>Muted</a>
                                                <a class="dropdown-item" href="#"><i
                                                        class="ri-delete-bin-5-line align-bottom text-muted me-2"></i>Delete</a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
        </div>

        <!-- Chat Body -->
        <div class="chat-body">
            <div class="chat-conversation" id="chat-conversation" style="background-color: #f8f9fa;">
                <div id="elmLoader" class="text-center py-4"></div>
            
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

                                                    {{-- Hiển thị theo loại tin nhắn --}}
                                                    @if ($message->type === 'image')
                                                        @if ($message->file_path)
                                                            <div class="message-image"
                                                                style="cursor:pointer; max-width:180px;"
                                                                onclick="openImageModal('{{ asset('storage/' . $message->file_path) }}')">
                                                                <img src="{{ asset('storage/' . $message->file_path) }}"
                                                                    alt="Image" class="img-fluid rounded"
                                                                    style="max-width:100%; max-height:180px;" />
                                                            </div>
                                                        @endif
                                                        @if ($message->content)
                                                            <p class="mb-0 ctext-content">{{ $message->content }}</p>
                                                        @endif
                                                    @elseif($message->type === 'file')
                                                        {{-- Hiển thị file --}}
                                                        @if ($message->file_path)
                                                            <div class="message-file p-2 mb-1">
                                                                <a href="{{ asset('storage/' . $message->file_path) }}"
                                                                    target="_blank">
                                                                    📎 {{ basename($message->file_path) }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if ($message->content)
                                                            <p class="mb-0 ctext-content">{{ $message->content }}</p>
                                                        @endif
                                                    @else
                                                        {{-- Tin nhắn text thông thường --}}
                                                        <p class="mb-0 ctext-content">{{ $message->content }}</p>
                                                    @endif
                                                </div>

                                                <div class="dropdown align-self-start message-box-drop">
                                                    <a class="dropdown-toggle" href="#" role="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i>
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item reply-message" href="#"><i
                                                                class="ri-reply-line me-2 text-muted align-bottom"></i>Reply</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-share-line me-2 text-muted align-bottom"></i>Forward</a>
                                                        <a class="dropdown-item copy-message" href="#"><i
                                                                class="ri-file-copy-line me-2 text-muted align-bottom"></i>Copy</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-bookmark-line me-2 text-muted align-bottom"></i>Bookmark</a>
                                                        @if ($message->type === 'file' || $message->type === 'image')
                                                            <a class="dropdown-item"
                                                                href="{{ asset('storage/' . $message->file_path) }}"
                                                                download><i
                                                                    class="ri-download-line me-2 text-muted align-bottom"></i>Download</a>
                                                        @endif
                                                        <a class="dropdown-item delete-item" href="#"><i
                                                                class="ri-delete-bin-5-line me-2 text-muted align-bottom"></i>Delete</a>
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
            <!-- Giao diện form -->
            <form wire:submit.prevent="sendMessage">
                <div class="row g-0 align-items-center">
                    <!-- Emoji -->
                    <div class="col-auto pe-2">
                        <button type="button" class="btn btn-light rounded-circle p-2" id="emoji-toggle">
                            <i class="bx bx-smile fs-4 text-primary"></i>
                        </button>
                        
                        <!-- Emoji Picker -->
                        <div id="emoji-picker" class="position-absolute bg-white border rounded shadow-lg p-3 hidden" style="bottom: 60px; left: 0; width: 280px; max-height: 200px; overflow-y: auto; z-index: 1000;">
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
                                placeholder="Type your message..." 
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

                <!-- Upload file button khi có file -->
                @if ($fileUpload)
                    <div class="mt-2 text-center">
                        <button type="button" wire:click="uploadFile" class="btn btn-sm btn-success">
                            <i class="bx bx-upload"></i> Send File
                        </button>
                    </div>
                @endif
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
        console.log('💬 Chat realtime Blade loaded, conversation ID:', window.currentConversationId);
        
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
        
        // Initialize admin emoji picker
        function initAdminEmojiPicker() {
            const emojiPicker = document.getElementById('emoji-picker');
            const emojiGrid = emojiPicker.querySelector('.grid');
            
            if (!emojiGrid) return;
            
            emojiGrid.innerHTML = '';
            
            adminEmojis.forEach(emoji => {
                const emojiBtn = document.createElement('button');
                emojiBtn.type = 'button';
                emojiBtn.textContent = emoji;
                emojiBtn.className = 'btn btn-sm btn-outline-light hover:bg-gray-100 p-1 rounded cursor-pointer';
                emojiBtn.style.cssText = 'border: none; font-size: 1.2rem; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;';
                
                emojiBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    insertEmojiToAdminInput(emoji);
                    emojiPicker.classList.add('hidden');
                });
                
                emojiGrid.appendChild(emojiBtn);
            });
        }
        
        // Insert emoji to admin input field
        function insertEmojiToAdminInput(emoji) {
            const messageInput = document.getElementById('messageInputField');
            if (messageInput) {
                const currentValue = messageInput.value || '';
                const newValue = currentValue + emoji;
                messageInput.value = newValue;
                
                // Trigger Livewire update
                if (window.Livewire && @this) {
                    @this.set('message_content', newValue);
                }
                
                messageInput.focus();
            }
        }
        
        // Setup admin emoji toggle
        function setupAdminEmojiToggle() {
            const emojiToggle = document.getElementById('emoji-toggle');
            const emojiPicker = document.getElementById('emoji-picker');
            
            if (!emojiToggle || !emojiPicker) return;
            
            emojiToggle.addEventListener('click', (e) => {
                e.preventDefault();
                emojiPicker.classList.toggle('hidden');
            });
            
            // Close emoji picker when clicking outside
            document.addEventListener('click', (e) => {
                if (!emojiToggle.contains(e.target) && !emojiPicker.contains(e.target)) {
                    emojiPicker.classList.add('hidden');
                }
            });
        }
        
        // Handle Enter key for sending messages
        function handleEnterKey(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                
                // Trigger Livewire sendMessage method
                if (window.Livewire && @this) {
                    @this.call('sendMessage');
                }
            }
        }
        
        // Send quick message (sticker/emoji)
        function sendQuickMessage(content) {
            if (window.Livewire && @this) {
                // Set the message content
                @this.set('message_content', content);
                // Send the message
                @this.call('sendMessage');
            }
        }
        
        // Scroll to bottom function - Optimized
        function scrollToBottom() {
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                // Simple and reliable scroll
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }
        
        // Test scroll function
        function testScroll() {
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                console.log('✅ Chat container found');
                console.log('📏 ScrollHeight:', chatContainer.scrollHeight);
                console.log('📏 ClientHeight:', chatContainer.clientHeight);
                console.log('📍 ScrollTop:', chatContainer.scrollTop);
                console.log('🔄 Can scroll:', chatContainer.scrollHeight > chatContainer.clientHeight);
                
                // Test scroll capability
                const maxScroll = chatContainer.scrollHeight - chatContainer.clientHeight;
                console.log('🎯 Max scroll position:', maxScroll);
            } else {
                console.log('❌ Chat container not found');
            }
        }
        
        // Force scroll function
        function forceScrollEnable() {
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                // Force CSS properties via JavaScript
                chatContainer.style.overflowY = 'scroll';
                chatContainer.style.height = '450px';
                chatContainer.style.maxHeight = '450px';
                chatContainer.style.minHeight = '450px';
                chatContainer.style.display = 'block';
                chatContainer.style.position = 'relative';
                
                console.log('🔧 Force scroll applied');
                console.log('📏 Final height:', chatContainer.offsetHeight);
                console.log('📏 Scroll height:', chatContainer.scrollHeight);
                console.log('💡 Should scroll:', chatContainer.scrollHeight > chatContainer.offsetHeight);
                
                return chatContainer.scrollHeight > chatContainer.offsetHeight;
            }
            return false;
        }
        
        // Auto scroll to bottom when new messages arrive
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Chat scroll setup initialized');
            
            // Initialize admin emoji picker
            initAdminEmojiPicker();
            setupAdminEmojiToggle();
            
            // Test scroll after page load
            setTimeout(() => {
                forceScrollEnable();
                testScroll();
                scrollToBottom();
            }, 500);
            
            // Listen for Livewire updates
            if (window.Livewire) {
                window.Livewire.on('messageProcessed', function() {
                    setTimeout(scrollToBottom, 100);
                });
            }
            
            // Simple scroll monitoring
            const chatContainer = document.getElementById('chat-conversation');
            if (chatContainer) {
                // Log scroll events for debugging
                chatContainer.addEventListener('scroll', function() {
                    const scrollPercent = (this.scrollTop / (this.scrollHeight - this.clientHeight) * 100).toFixed(1);
                    console.log(`📍 Scroll: ${scrollPercent}%`);
                });
                
                // Ensure scrolling works
                console.log('✅ Scroll events attached');
            }
        });
        
        // Add some animation effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate celebration icons
            const celebrationIcons = document.querySelector('.celebration-icons');
            if (celebrationIcons) {
                celebrationIcons.style.animation = 'bounce 2s infinite';
            }
            
            // Add hover effects to quick stickers
            document.querySelectorAll('.quick-sticker').forEach(sticker => {
                sticker.addEventListener('click', function() {
                    // Add click animation
                    this.style.transform = 'scale(1.5)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });
    </script>

</div>
