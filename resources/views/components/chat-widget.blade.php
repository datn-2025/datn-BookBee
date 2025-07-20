<!-- Chat Widget -->
<!-- Nút mở chat -->
<button id="chat-toggle" class="fixed bottom-5 right-5 z-50 transition-transform hover:scale-110 focus:outline-none">
    <div class="relative">
        <img src="{{ asset('images/bookbeee.jpg') }}" alt="Chat Bee"
            class="w-16 h-16 rounded-full object-cover shadow-lg border-2 border-black">
        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
    </div>
</button>

<!-- Hộp chat -->
<div id="chat-box" class="hidden fixed bottom-24 right-5 w-[380px] z-50">
    <div class="bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-black text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <h3 class="font-semibold">Hỗ trợ khách hàng</h3>
            </div>
            <button onclick="document.getElementById('chat-box').style.display='none'" 
                    class="text-white hover:text-gray-300 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Chat content -->
        <div id="chat-content" class="p-4 h-[350px] overflow-y-auto bg-gray-50">
            <!-- Tin nhắn sẽ được render bằng JS -->
        </div>

        <!-- Input area -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <form id="chat-form" class="flex items-center space-x-2" autocomplete="off">
                <input type="text" id="chat-input" 
                    class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:border-black"
                    placeholder="Nhập tin nhắn..." autocomplete="off">
                <button type="submit" 
                    class="bg-black text-white rounded-full p-2 hover:bg-gray-800 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
<!-- Thêm data-user-id và data-user-name cho JS -->
@if(Auth::check())
    <div id="chat-user-info" data-user-id="{{ Auth::id() }}" data-user-name="{{ Auth::user()->name }}" style="display:none;"></div>
@endif