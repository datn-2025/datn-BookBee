<div wire:key="conversation-list">
    <ul class="list-unstyled chat-list chat-user-list" id="userList">
        @if(count($conversations) > 0)
            @foreach ($conversations as $conversation)
                <li wire:key="{{ $refreshKey }}-{{ $conversation->id }}">
                    <a href="{{ route('admin.chat.show', $conversation->id) }}"
                        class="d-flex align-items-center px-4 py-2 {{ $selectedConversationId && $selectedConversationId == $conversation->id ? 'active' : '' }}"
                        data-user-id="{{ $conversation->customer->id }}">
                        <!-- Avatar khách hàng -->
                        <div class="flex-shrink-0 me-3">
                            <img src="{{ $conversation->customer->avatar ?? asset('images/default-user.png') }}"
                                alt="Avatar" class="rounded-circle avatar-xs">
                        </div>

                        <!-- Nội dung -->
                        <div class="flex-grow-1 overflow-hidden">
                            <h5 class="text-truncate fs-15 mb-1">
                                {{ $conversation->customer->name }}
                            </h5>
                            <p class="text-truncate text-muted fs-13 mb-0">
                                {{ $conversation->messages->first()?->content ?? 'Chưa có tin nhắn' }}
                            </p>
                            <small class="text-muted">
                                @if ($conversation->customer->status === 'online')
                                    <span class="text-success" style="font-size: 1.2em;">●</span> Online
                                @elseif ($conversation->customer->last_seen)
                                    <span class="badge bg-warning">
                                        Hoạt động {{ \Carbon\Carbon::parse($conversation->customer->last_seen)->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-muted">Offline</span>
                                @endif
                            </small>
                        </div>

                        <!-- Thời gian -->
                        <div class="flex-shrink-0 ms-2">
                            <span class="text-muted fs-11">
                                {{ optional($conversation->last_message_at)->diffForHumans() }}
                            </span>
                            @if ($conversation->unread_messages_count > 0)
                                <span class="badge bg-danger rounded-pill ms-1">
                                    {{ $conversation->unread_messages_count }}
                                </span>
                            @endif
                        </div>
                    </a>
                </li>
            @endforeach
        @else
            <li class="text-center text-muted py-3">
                <i class="ri-chat-3-line fs-2"></i>
                <p>Chưa có cuộc trò chuyện nào</p>
            </li>
        @endif
    </ul>

</div>
