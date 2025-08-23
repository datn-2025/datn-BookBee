@extends('layouts.backend')

@section('title', 'Chat Real Time')

@push('styles')
<link href="{{ asset('css/chat.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <style>
        .chat-wrapper {
            height: 80vh;
            /* chiều cao vừa phải thay vì 100vh */
        }

        /* Đảm bảo chat-content có layout đúng */
        .chat-content {
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
        }

        .user-chat-topbar {
            flex-shrink: 0 !important;
        }

        .chat-conversation-list hr {
            border-color: #ccc;
            opacity: 0.5;
        }

        .chat-list.right .conversation-list {
            justify-content: flex-end;
        }

        .chat-list.left .conversation-list {
            justify-content: flex-start;
        }

        .message {
            max-width: 80%;
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
        }

        .message.sent {
            background-color: #007bff;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 0.25rem;
        }

        .message.received {
            background-color: #f1f1f1;
            color: #333;
            margin-right: auto;
            border-bottom-left-radius: 0.25rem;
        }

        .chat-container {
            height: calc(100vh - 250px);
            overflow-y: auto;
        }

        /* Chat conversation với height đơn giản */
        .chat-conversation {
            flex: 1 !important;
            overflow-y: auto !important;
            overflow-x: hidden;
            padding: 1rem;
            height: 60vh !important;
            /* height cố định đơn giản */
            max-height: 60vh !important;
        }

        /* Đảm bảo chat input không bị che */
        .chat-input-section {
            flex-shrink: 0 !important;
            border-top: 1px solid #dee2e6 !important;
            background: white !important;
            position: sticky !important;
            bottom: 0 !important;
        }



        /* Sidebar đơn giản */
        .chat-leftsidebar {
            height: 80vh;
        }

        .chat-room-list {
            height: 60vh;
            /* đơn giản hóa */
            overflow-y: auto;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        /* Active conversation styling */
        .chat-user-list a.active {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
        }

        .chat-user-list a:hover {
            background-color: #f5f5f5;
        }

        /* Loại bỏ gạch chân cho links trong conversation list */
        .chat-user-list a {
            text-decoration: none !important;
        }

        .chat-user-list a:hover {
            text-decoration: none !important;
        }

        /* Empty state styling */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Contact list styling */
        .contact-item {
            border-bottom: 1px solid #f1f1f1;
            transition: background-color 0.2s ease;
        }

        .contact-item:hover {
            background-color: #f8f9fa;
        }

        .contact-item:last-child {
            border-bottom: none;
        }

        .start-conversation-btn {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        .start-conversation-btn:hover {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        /* Search box styling trong contacts tab */
        #contacts .search-box {
            position: relative;
        }

        #contacts .search-box .search-icon {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
            pointer-events: none;
        }

        #contacts .search-box input {
            padding-right: 40px;
        }

        /* Animation cho contact items */
        .contact-item {
            transition: all 0.2s ease;
        }

        .contact-item.hidden {
            opacity: 0;
            transform: translateX(-10px);
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <div class="chat-wrapper d-lg-flex gap-1">
                <div class="chat-leftsidebar minimal-border">
                    <div class="px-4 pt-4 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="mb-4">Chats</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom"
                                    title="Add Contact">

                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-soft-success btn-sm material-shadow-none">
                                        <i class="ri-add-line align-bottom"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="search-box">
                            <input type="text" class="form-control bg-light border-light" placeholder="Search here...">
                            <i class="ri-search-2-line search-icon"></i>
                        </div>
                    </div> <!-- .p-4 -->

                    <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#chats" role="tab">
                                Chats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#contacts" role="tab">
                                Contacts
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="chats" role="tabpanel">
                            <div class="chat-room-list pt-3" data-simplebar>
                                <div class="d-flex align-items-center px-4 mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fs-11 text-muted text-uppercase">Direct Messages</h6>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom"
                                            title="New Message">

                                            <!-- Button trigger modal -->
                                            <button type="button"
                                                class="btn btn-soft-success btn-sm shadow-none material-shadow"
                                                data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                                <i class="ri-add-line align-bottom"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="chat-message-list">
                                    <livewire:conversation-list :selectedConversationId="$selectedConversation?->id" />
                                </div>

                                <!-- End chat-message-list -->
                            </div>
                        </div>
                        <div class="tab-pane" id="contacts" role="tabpanel">
                            <div class="chat-room-list pt-3" data-simplebar>
                                <div class="d-flex align-items-center px-4 mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fs-11 text-muted text-uppercase">Active Users</h6>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-success-subtle text-success">{{ $activeUsers->count() ?? 0 }}</span>
                                    </div>
                                </div>

                                <!-- Search box for contacts -->
                                <div class="px-4 pb-2">
                                    <div class="search-box">
                                        <input type="text" class="form-control bg-light border-light" 
                                               placeholder="Tìm kiếm người dùng..." 
                                               id="contactSearch">
                                        <i class="ri-search-2-line search-icon"></i>
                                    </div>
                                </div>

                                <div class="contact-list" id="contactList">
                                    @if(isset($activeUsers) && $activeUsers->count() > 0)
                                        @foreach($activeUsers as $user)
                                            <div class="d-flex align-items-center px-4 py-2 contact-item" 
                                                 data-user-id="{{ $user->id }}" 
                                                 data-user-name="{{ $user->name }}"
                                                 data-user-email="{{ $user->email }}"
                                                 data-search-text="{{ strtolower($user->name . ' ' . $user->email) }}"
                                                 style="cursor: pointer;">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="position-relative">
                                                        <img src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff&size=200' }}" 
                                                             alt="{{ $user->name }}" 
                                                             class="rounded-circle avatar-xs">
                                                        @if($user->last_seen && $user->last_seen->diffInMinutes(now()) <= 5)
                                                            <span class="position-absolute bottom-0 end-0 badge border-2 border-white rounded-circle bg-success p-1">
                                                                <span class="visually-hidden">Online</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="text-truncate fs-14 mb-0">{{ $user->name }}</h5>
                                                    <p class="text-truncate text-muted fs-13 mb-0">{{ $user->email }}</p>
                                                    <p class="text-truncate text-muted fs-12 mb-0">
                                                        @if($user->last_seen && $user->last_seen->diffInMinutes(now()) <= 5)
                                                            <i class="bx bxs-circle text-success fs-10 me-1"></i>Online
                                                        @elseif($user->last_seen && $user->last_seen->diffInMinutes(now()) <= 30)
                                                            <i class="bx bxs-circle text-warning fs-10 me-1"></i>Away ({{ $user->last_seen->diffForHumans() }})
                                                        @else
                                                            <i class="bx bxs-circle text-muted fs-10 me-1"></i>Offline
                                                            @if($user->last_seen)
                                                                ({{ $user->last_seen->diffForHumans() }})
                                                            @endif
                                                        @endif
                                                    </p>
                                                </div>
                                                
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <div class="mb-2">
                                                <i class="bx bx-user-x display-6 text-muted"></i>
                                            </div>
                                            <h5 class="fs-16 fw-semibold">Không có người dùng nào</h5>
                                            <p class="text-muted mb-0">Chưa có người dùng hoạt động trong hệ thống</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end tab contact -->
                </div>
                <!-- end chat leftsidebar -->
                <!-- Start User chat -->
                <div class="user-chat w-100 minimal-border">
                    @if ($selectedConversation)
                        <livewire:chat-realtime :selectedConversation="$selectedConversation" />
                    @else
                        <div class="empty-state">
                            <i class="bx bx-message-square-dots"></i>
                            <h4>Không có cuộc trò chuyện nào</h4>
                            <p> Vui lòng chọn cuộc trò chuyện từ danh sách</p>
                        </div>
                    @endif
                </div>
                <!-- end chat-wrapper -->
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <!-- New Conversation Modal -->
        <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newConversationModalLabel">Start New Conversation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customerSearch" class="form-label">Search Customers</label>
                            <input type="text" class="form-control" id="customerSearch"
                                placeholder="Search by name or email...">
                        </div>
                        <div id="customersList" class="list-group">
                            <!-- Customers will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script src="{{ asset('js/chat-realtime.js') }}"></script>
        <script>
            // Xử lý tạo cuộc trò chuyện mới khi click vào contact
            document.addEventListener('DOMContentLoaded', function() {
                // Handle contact search
                const contactSearch = document.getElementById('contactSearch');
                const contactItems = document.querySelectorAll('.contact-item');
                
                if (contactSearch) {
                    contactSearch.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        
                        contactItems.forEach(item => {
                            const searchText = item.dataset.searchText || '';
                            const userName = item.dataset.userName || '';
                            const userEmail = item.dataset.userEmail || '';
                            
                            const isMatch = searchText.includes(searchTerm) || 
                                          userName.toLowerCase().includes(searchTerm) || 
                                          userEmail.toLowerCase().includes(searchTerm);
                            
                            if (isMatch || searchTerm === '') {
                                item.style.display = 'flex';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        
                        // Update count
                        const visibleItems = Array.from(contactItems).filter(item => 
                            item.style.display !== 'none'
                        );
                        const countBadge = document.querySelector('#contacts .badge');
                        if (countBadge) {
                            countBadge.textContent = visibleItems.length;
                        }
                    });
                }

                // Start conversation when clicking a contact item
                document.addEventListener('click', function(e) {
                    const contactItem = e.target.closest('.contact-item');
                    if (!contactItem) return;

                    e.preventDefault();
                    e.stopPropagation();

                    const userId = contactItem.dataset.userId;
                    if (!userId) {
                        console.error('User ID not found');
                        return;
                    }

                    // Visual loading state on the item
                    contactItem.style.opacity = '0.6';
                    contactItem.style.pointerEvents = 'none';

                    // Create conversation
                    fetch('{{ route("admin.chat.create-conversation") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ customer_id: userId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof toastr !== 'undefined') toastr.success(data.message);
                            setTimeout(() => { window.location.href = data.redirect_url; }, 300);
                        } else {
                            throw new Error(data.message || 'Có lỗi xảy ra');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (typeof toastr !== 'undefined') toastr.error(error.message || 'Có lỗi xảy ra khi tạo cuộc trò chuyện');
                        contactItem.style.opacity = '';
                        contactItem.style.pointerEvents = '';
                    });
                });
            });
        </script>
    @endpush
