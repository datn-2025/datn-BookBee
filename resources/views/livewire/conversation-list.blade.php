<ul class="list-unstyled chat-list chat-user-list" id="userList">
    @foreach ($conversations as $conversation)
        <li>
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
                </div>
            </a>
        </li>
    @endforeach
</ul> 