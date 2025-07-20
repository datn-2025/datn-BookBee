@extends('layouts.backend')

@section('title', 'Chat Real Time')

@section('content')
    <style>
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

        .message-input {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: white;
            padding: 1rem;
            border-top: 1px solid #dee2e6;
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
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
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
                                    <livewire:conversation-list />
                                </div>

                                <div class="d-flex align-items-center px-4 mt-4 pt-2 mb-2">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase">Channels</h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom"
                                            title="Create group">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-soft-success btn-sm">
                                                <i class="ri-add-line align-bottom"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="chat-message-list">

                                    <ul class="list-unstyled chat-list chat-user-list mb-0" id="channelList">
                                    </ul>
                                </div>
                                <!-- End chat-message-list -->
                            </div>
                        </div>
                        <div class="tab-pane" id="contacts" role="tabpanel">
                            <div class="chat-room-list pt-3" data-simplebar>
                                <div class="sort-contact">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end tab contact -->
                </div>
                <!-- end chat leftsidebar -->
                <!-- Start User chat -->
                <div  class="user-chat w-100 overflow-hidden minimal-border">
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
        <script>
            window.addEventListener('scrollToBottom', () => {
                const container = document.getElementById('message-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        </script>
    @endpush
