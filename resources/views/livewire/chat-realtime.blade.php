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
    </style>


    <div class="chat-content d-lg-flex">
        <!-- start chat conversation section -->
        <div class="w-100 overflow-hidden position-relative">
            <!-- conversation user -->
            <div class="position-relative">
                <div class="position-relative" id="users-chat">
                    <!-- Topbar -->
                    <div class="p-3 user-chat-topbar">
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

                    <!-- Chat content -->
                    <div class="chat-conversation p-3 p-lg-4" id="chat-conversation" data-simplebar>
                        <div id="elmLoader" class="text-center py-4"></div>
                        @php $previousDate = null; @endphp
                        <ul class="list-unstyled chat-conversation-list" id="message-container">
                            @foreach ($chatMessages as $message)
                                @php
                                    $isMine = $message->sender_id === auth()->id();
                                    $isAdmin = $message->sender->isAdmin();
                                    $side = $isAdmin ? 'right' : 'left';
                                    $bgColor = $isAdmin ? 'bg-primary text-white' : 'bg-light text-dark';
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
                                                <img src="{{ $message->sender->avatar ?? asset('storage/avatars/' . $message->sender->avatar) }}"
                                                    alt="Admin" class="avatar-xs rounded-circle">
                                            </div>
                                        @endif

                                        <div id='message-container' class="user-chat-content">
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
                                                    @if ($message->read_at)
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
                    </div>

                    <div class="alert alert-warning alert-dismissible copyclipboard-alert px-4 fade show"
                        id="copyClipBoard" role="alert">
                        Message copied
                    </div>

                    <!-- chat input -->
                    <div class="chat-input-section p-3 p-lg-4">
                        <!-- Giao diện form -->
                        <form wire:submit.prevent="send" class="message-form">
                            <div class="row g-0 align-items-center">
                                <!-- Emoji -->
                                <div class="col-auto pe-2">
                                    <button type="button" class="btn btn-light rounded-circle p-2">
                                        <i class="bx bx-smile fs-4 text-primary"></i>
                                    </button>
                                </div>

                                <!-- File upload -->
                                <div class="col-auto pe-2">
                                    <label for="uploadFile" class="btn btn-light rounded-circle p-2"
                                        style="cursor: pointer;">
                                        <i class="bx bx-plus fs-4 text-primary"></i>
                                    </label>
                                    <input type="file" id="uploadFile" wire:model="uploadFile" class="d-none"
                                        accept="image/*,application/*">
                                </div>

                                <!-- Nhập tin nhắn + ảnh -->
                                <div class="col px-2">
                                    <div
                                        class="bg-light rounded shadow-sm p-2 d-flex flex-row align-items-center gap-2 position-relative">
                                        {{-- Ảnh preview với wire:key để tránh re-render --}}
                                        @if ($uploadFile)
                                            <div class="position-relative me-2"
                                                style="max-width: 80px; min-width: 60px;"
                                                wire:key="file-preview-{{ $uploadFile->getFilename() }}">
                                                @if (method_exists($uploadFile, 'getMimeType') && Str::startsWith($uploadFile->getMimeType(), 'image/'))
                                                    <img src="{{ $uploadFile->temporaryUrl() }}"
                                                        class="img-thumbnail" alt="Preview"
                                                        style="max-width: 100%; max-height: 60px;">
                                                @else
                                                    <div class="bg-secondary text-white text-center p-2 rounded"
                                                        style="font-size: 12px;">
                                                        📎 {{ $uploadFile->getClientOriginalName() }}
                                                    </div>
                                                @endif
                                                <button type="button"
                                                    class="btn btn-sm btn-light position-absolute top-0 end-0 rounded-circle"
                                                    wire:click="removeFile" style="transform: translate(50%, -50%);">
                                                    &times;
                                                </button>
                                            </div>
                                        @endif
                                        {{-- <input type="text"
                                            class="form-control chat-input bg-white border-0 rounded-pill shadow-sm flex-grow-1"
                                            placeholder="Type your message..." wire:model.defer="messageInput"
                                            autocomplete="off" wire:key="message-input"> --}}
                                        <input type="text"
                                            class="form-control chat-input bg-white border-0 rounded-pill shadow-sm flex-grow-1"
                                            placeholder="Type your message..." wire:model="messageInput"
                                            autocomplete="off">

                                    </div>
                                </div>

                                <!-- Gửi -->
                                <div class="col-auto ps-2">
                                    <button type="submit" class="btn btn-primary rounded-circle p-2">
                                        <i class="bx bx-send text-white"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>


                    <!-- reply preview -->
                    <div class="replyCard">
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

                </div> {{-- END #users-chat --}}
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
                    @if ($selectedConversation)
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
                    @else
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-truncate mb-0 fs-16 text-muted">
                                    Chưa chọn người để trò chuyện
                                </h5>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }
        document.addEventListener('livewire:init', () => {
            // Cuộn xuống cuối khi nhận sự kiện
            Livewire.on('scrollToBottom', () => {
                setTimeout(() => {
                    const container = document.getElementById('chat-conversation');
                    if (container && container.simplebar) {
                        container.simplebar.getScrollElement().scrollTop = container.simplebar
                            .getScrollElement().scrollHeight;
                    }
                }, 100);
            });

            // Lắng nghe sự kiện từ Pusher
            window.Echo.channel('conversation.{{ $selectedConversation->id }}')
                .listen('MessageSent', (e) => {
                    @this.call('onMessageSent', e.message);
                });
        });
    </script>



</div>
