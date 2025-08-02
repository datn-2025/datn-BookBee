<footer style="background-color: white !important; border-top: 1px solid #f3f4f6;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 4rem 1rem;">
        <div class="footer-grid" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            <style>
                @media (min-width: 768px) {
                    .footer-grid {
                        grid-template-columns: repeat(2, 1fr) !important;
                    }
                }
                @media (min-width: 1024px) {
                    .footer-grid {
                        grid-template-columns: repeat(4, 1fr) !important;
                    }
                }
                @media (min-width: 768px) {
                    .footer-shipping-payment {
                        flex-direction: row !important;
                    }
                }
            </style>
            <!-- Cột 1: Logo + mô tả -->
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: -0.025em; color: #000000 !important; margin: 0;">BOOK<span style="color: #000000 !important;">BEE</span></h2>
                </div>
                <p style="font-size: 0.875rem; line-height: 1.625; color: #6b7280 !important; margin-bottom: 1rem;">
                    Mang đến trải nghiệm đọc sách hiện đại, tiện lợi và nhanh chóng cho mọi độc giả. IMPOSSIBLE IS NOTHING.
                </p>
                <div style="width: 3rem; height: 0.125rem; background-color: #000000 !important;"></div>
            </div>

            <!-- Cột 2: Quick Links -->
            <div style="margin-bottom: 2rem;">
                <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #000000 !important; margin-bottom: 1rem;">Liên kết nhanh</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.5rem;"><a href="{{ route('home') }}" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Trang chủ</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="{{ route('about') }}" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Giới thiệu</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="{{ route('books.index') }}" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Cửa hàng</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="{{ route('news.index') }}" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Tin tức</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="{{ route('contact.form') }}" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Cột 3: Help & Info -->
            <div style="margin-bottom: 2rem;">
                <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #000000 !important; margin-bottom: 1rem;">Hỗ trợ khách hàng</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 0.5rem;"><a href="#" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Theo dõi đơn hàng</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="#" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Chính sách đổi trả</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="#" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Giao hàng & thanh toán</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="#" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Liên hệ với chúng tôi</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="#" style="font-size: 0.875rem; font-weight: 500; color: #6b7280 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6b7280'">Câu hỏi thường gặp</a></li>
                </ul>
            </div>

            <!-- Cột 4: Contact -->
            <div style="margin-bottom: 2rem;">
                <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #000000 !important; margin-bottom: 1rem;">Liên hệ</h4>
                <div style="padding: 1rem; border: 1px solid #f3f4f6; background-color: #f9fafb !important;">
                    <p style="font-size: 0.875rem; margin-bottom: 0.75rem; font-weight: 500; color: #6b7280 !important;">Bạn có thắc mắc hoặc góp ý gì không?</p>
                    <a href="mailto:yourinfo@gmail.com" style="font-weight: 700; font-size: 0.875rem; display: block; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e5e7eb; color: #000000 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#000000'">yourinfo@gmail.com</a>
                    <p style="font-size: 0.875rem; margin-bottom: 0.75rem; font-weight: 500; color: #6b7280 !important;">Nếu bạn cần hỗ trợ? Hãy gọi cho chúng tôi.</p>
                    <a href="tel:123456789" style="font-weight: 700; font-size: 0.875rem; color: #000000 !important; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#000000'">123456789</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Phần giữa: Đối tác vận chuyển + Hình thức thanh toán -->
    <div style="border-top: 1px solid #f3f4f6; padding: 2rem 0;">
        <div style="max-width: 1280px; margin: 0 auto; padding: 0 1rem;">
            <div class="footer-shipping-payment" style="display: flex; flex-direction: column; justify-content: space-between; align-items: center; gap: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <span style="font-weight: 700; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #000000 !important;">Đối tác vận chuyển:</span>
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <img src="{{asset('images/dhl.png')}}" alt="DHL" style="height: 2rem; border-radius: 0.25rem; border: 1px solid #e5e7eb; padding: 0.25rem; background-color: white;">
                        <img src="{{asset('images/shippingcard.png')}}" alt="Shipping" style="height: 2rem; border-radius: 0.25rem; border: 1px solid #e5e7eb; padding: 0.25rem; background-color: white;">
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <span style="font-weight: 700; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #000000 !important;">Hình thức thanh toán:</span>
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <img src="{{asset('images/visa.jpg')}}" alt="Visa" style="height: 2rem; border-radius: 0.25rem; border: 1px solid #e5e7eb;">
                        <img src="{{asset('images/mastercard.jpg')}}" alt="Mastercard" style="height: 2rem; border-radius: 0.25rem; border: 1px solid #e5e7eb;">
                        <img src="{{asset('images/paypal.jpg')}}" alt="PayPal" style="height: 2rem; border-radius: 0.25rem; border: 1px solid #e5e7eb;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer bottom -->
    <div style="border-top: 1px solid #f3f4f6; padding: 1.5rem 0; background-color: #f9fafb !important;">
        <div style="max-width: 1280px; margin: 0 auto; padding: 0 1rem; text-align: center;">
            <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div style="height: 1px; width: 3rem; background-color: #d1d5db;"></div>
                <div style="text-align: center;">
                    <p style="font-weight: 500; font-size: 0.875rem; color: #6b7280 !important; margin: 0;">© 2025 Bản quyền thuộc về <span style="font-weight: 700; text-transform: uppercase; color: #000000 !important;">BookBee</span></p>
                    <p style="font-size: 0.75rem; margin-top: 0.25rem; color: #9ca3af !important;">Thiết kế bởi BookBee Team</p>
                </div>
                <div style="height: 1px; width: 3rem; background-color: #d1d5db;"></div>
            </div>
            <div style="height: 1px; width: 4rem; margin: 0 auto; background-color: #000000;"></div>
        </div>
    </div>
</footer>