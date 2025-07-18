<footer class="bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Cột 1: Logo + mô tả -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight">BOOK<span class="text-black">BEE</span></h2>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Mang đến trải nghiệm đọc sách hiện đại, tiện lợi và nhanh chóng cho mọi độc giả. IMPOSSIBLE IS NOTHING.
                </p>
                <div class="w-12 h-0.5 bg-black"></div>
            </div>

            <!-- Cột 2: Quick Links -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-black uppercase tracking-wide">Liên kết nhanh</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Trang chủ</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Giới thiệu</a></li>
                    <li><a href="{{ route('books.index') }}" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Cửa hàng</a></li>
                    <li><a href="{{ route('news.index') }}" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Tin tức</a></li>
                    <li><a href="{{ route('contact.form') }}" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Cột 3: Help & Info -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-black uppercase tracking-wide">Hỗ trợ khách hàng</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Theo dõi đơn hàng</a></li>
                    <li><a href="#" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Chính sách đổi trả</a></li>
                    <li><a href="#" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Giao hàng & thanh toán</a></li>
                    <li><a href="#" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Liên hệ với chúng tôi</a></li>
                    <li><a href="#" class="text-gray-600 text-sm hover:text-black transition-colors duration-200 font-medium">Câu hỏi thường gặp</a></li>
                </ul>
            </div>

            <!-- Cột 4: Contact -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-black uppercase tracking-wide">Liên hệ</h4>
                <div class="bg-gray-50 p-4 border border-gray-100">
                    <p class="text-gray-600 text-sm mb-3 font-medium">Bạn có thắc mắc hoặc góp ý gì không?</p>
                    <a href="mailto:yourinfo@gmail.com" class="text-black font-bold text-sm block mb-4 pb-2 border-b border-gray-200 hover:text-gray-700 transition-colors duration-200">yourinfo@gmail.com</a>
                    <p class="text-gray-600 text-sm mb-3 font-medium">Nếu bạn cần hỗ trợ? Hãy gọi cho chúng tôi.</p>
                    <a href="tel:123456789" class="text-black font-bold text-sm hover:text-gray-700 transition-colors duration-200">123456789</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Phần giữa: Đối tác vận chuyển + Hình thức thanh toán -->
    <div class="border-t border-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4 flex-wrap">
                    <span class="font-bold text-black text-sm uppercase tracking-wide">Đối tác vận chuyển:</span>
                    <div class="flex gap-3 items-center">
                        <img src="{{asset('images/dhl.png')}}" alt="DHL" class="h-8 rounded border border-gray-200 bg-white p-1">
                        <img src="{{asset('images/shippingcard.png')}}" alt="Shipping" class="h-8 rounded border border-gray-200 bg-white p-1">
                    </div>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <span class="font-bold text-black text-sm uppercase tracking-wide">Hình thức thanh toán:</span>
                    <div class="flex gap-3 items-center">
                        <img src="{{asset('images/visa.jpg')}}" alt="Visa" class="h-8 rounded border border-gray-200">
                        <img src="{{asset('images/mastercard.jpg')}}" alt="Mastercard" class="h-8 rounded border border-gray-200">
                        <img src="{{asset('images/paypal.jpg')}}" alt="PayPal" class="h-8 rounded border border-gray-200">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer bottom -->
    <div class="bg-gray-50 border-t border-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex justify-center items-center gap-4 mb-4">
                <div class="h-px w-12 bg-gray-300"></div>
                <div class="text-center">
                    <p class="text-gray-600 font-medium text-sm">© 2025 Bản quyền thuộc về <span class="text-black font-bold uppercase">BookBee</span></p>
                    <p class="text-gray-500 text-xs mt-1">Thiết kế bởi BookBee Team</p>
                </div>
                <div class="h-px w-12 bg-gray-300"></div>
            </div>
            <div class="h-px w-16 bg-black mx-auto"></div>
        </div>
    </div>
</footer>