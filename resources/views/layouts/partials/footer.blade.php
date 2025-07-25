<footer class="bg-white border-t border-gray-100" style="background-color: white !important;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Cột 1: Logo + mô tả -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <h2 class="text-2xl font-black uppercase tracking-tight" style="color: #000000 !important;">BOOK<span style="color: #000000 !important;">BEE</span></h2>
                </div>
                <p class="text-sm leading-relaxed" style="color: #6b7280 !important;">
                    Mang đến trải nghiệm đọc sách hiện đại, tiện lợi và nhanh chóng cho mọi độc giả. IMPOSSIBLE IS NOTHING.
                </p>
                <div class="w-12 h-0.5" style="background-color: #000000 !important;"></div>
            </div>

            <!-- Cột 2: Quick Links -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wide" style="color: #000000 !important;">Liên kết nhanh</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Trang chủ</a></li>
                    <li><a href="{{ route('about') }}" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Giới thiệu</a></li>
                    <li><a href="{{ route('books.index') }}" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Cửa hàng</a></li>
                    <li><a href="{{ route('news.index') }}" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Tin tức</a></li>
                    <li><a href="{{ route('contact.form') }}" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Cột 3: Help & Info -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wide" style="color: #000000 !important;">Hỗ trợ khách hàng</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Theo dõi đơn hàng</a></li>
                    <li><a href="#" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Chính sách đổi trả</a></li>
                    <li><a href="#" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Giao hàng & thanh toán</a></li>
                    <li><a href="#" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Liên hệ với chúng tôi</a></li>
                    <li><a href="#" class="text-sm font-medium transition-colors duration-200" style="color: #6b7280 !important;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Câu hỏi thường gặp</a></li>
                </ul>
            </div>

            <!-- Cột 4: Contact -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold uppercase tracking-wide" style="color: #000000 !important;">Liên hệ</h4>
                <div class="p-4 border" style="background-color: #f9fafb !important; border-color: #f3f4f6 !important;">
                    <p class="text-sm mb-3 font-medium" style="color: #6b7280 !important;">Bạn có thắc mắc hoặc góp ý gì không?</p>
                    <a href="mailto:yourinfo@gmail.com" class="font-bold text-sm block mb-4 pb-2 border-b transition-colors duration-200" style="color: #000000 !important; border-color: #e5e7eb !important;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#000000'">yourinfo@gmail.com</a>
                    <p class="text-sm mb-3 font-medium" style="color: #6b7280 !important;">Nếu bạn cần hỗ trợ? Hãy gọi cho chúng tôi.</p>
                    <a href="tel:123456789" class="font-bold text-sm transition-colors duration-200" style="color: #000000 !important;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#000000'">123456789</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Phần giữa: Đối tác vận chuyển + Hình thức thanh toán -->
    <div class="border-t py-8" style="border-color: #f3f4f6 !important;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4 flex-wrap">
                    <span class="font-bold text-sm uppercase tracking-wide" style="color: #000000 !important;">Đối tác vận chuyển:</span>
                    <div class="flex gap-3 items-center">
                        <img src="{{asset('images/dhl.png')}}" alt="DHL" class="h-8 rounded border p-1" style="border-color: #e5e7eb !important; background-color: white !important;">
                        <img src="{{asset('images/shippingcard.png')}}" alt="Shipping" class="h-8 rounded border p-1" style="border-color: #e5e7eb !important; background-color: white !important;">
                    </div>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <span class="font-bold text-sm uppercase tracking-wide" style="color: #000000 !important;">Hình thức thanh toán:</span>
                    <div class="flex gap-3 items-center">
                        <img src="{{asset('images/visa.jpg')}}" alt="Visa" class="h-8 rounded border" style="border-color: #e5e7eb !important;">
                        <img src="{{asset('images/mastercard.jpg')}}" alt="Mastercard" class="h-8 rounded border" style="border-color: #e5e7eb !important;">
                        <img src="{{asset('images/paypal.jpg')}}" alt="PayPal" class="h-8 rounded border" style="border-color: #e5e7eb !important;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer bottom -->
    <div class="border-t py-6" style="background-color: #f9fafb !important; border-color: #f3f4f6 !important;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex justify-center items-center gap-4 mb-4">
                <div class="h-px w-12" style="background-color: #d1d5db !important;"></div>
                <div class="text-center">
                    <p class="font-medium text-sm" style="color: #6b7280 !important;">© 2025 Bản quyền thuộc về <span class="font-bold uppercase" style="color: #000000 !important;">BookBee</span></p>
                    <p class="text-xs mt-1" style="color: #9ca3af !important;">Thiết kế bởi BookBee Team</p>
                </div>
                <div class="h-px w-12" style="background-color: #d1d5db !important;"></div>
            </div>
            <div class="h-px w-16 mx-auto" style="background-color: #000000 !important;"></div>
        </div>
    </div>
</footer>