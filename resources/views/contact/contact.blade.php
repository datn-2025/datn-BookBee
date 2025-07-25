@extends('layouts.app')
@section('title', 'Liên hệ')

@push('styles')
<style>
/* Clean Adidas-style contact design */
.contact-page {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #fff;
}

/* Hero Section */
.contact-hero {
    position: relative;
    overflow: hidden;
    background: #fff;
    border-bottom: 2px solid #000;
    padding: 80px 0;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100%;
    background: #000;
    opacity: 0.05;
    transform: skewX(-15deg);
}

.hero-badge {
    background: #000;
    color: #fff;
    padding: 8px 16px;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 1px;
    font-size: 0.75rem;
}

/* Form Container */
.contact-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 20px;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: start;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

/* Contact Info Section */
.contact-info {
    position: relative;
}

.section-badge {
    background: #000;
    color: #fff;
    padding: 6px 12px;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
    font-size: 0.7rem;
    margin-bottom: 20px;
    display: inline-block;
}

.contact-info h2 {
    font-size: 2.5rem;
    font-weight: 900;
    text-transform: uppercase;
    color: #000;
    line-height: 1;
    margin-bottom: 20px;
    letter-spacing: -1px;
}

.contact-info p {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 40px;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 24px;
    padding: 20px 0;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.contact-item:hover {
    transform: translateX(4px);
    border-bottom-color: #000;
}

.contact-icon {
    width: 50px;
    height: 50px;
    background: #000;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    font-size: 18px;
    transition: all 0.3s ease;
}

.contact-item:hover .contact-icon {
    background: #333;
    transform: scale(1.1);
}

.contact-details h4 {
    font-weight: 700;
    text-transform: uppercase;
    color: #000;
    margin-bottom: 4px;
    letter-spacing: 0.5px;
}

.contact-details span {
    color: #666;
    font-weight: 500;
}

/* Form Section */
.form-container {
    background: #fff;
    border: 2px solid #f5f5f5;
    position: relative;
    overflow: hidden;
}

.form-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 2px;
    background: #000;
    transition: left 0.3s ease;
}

.form-container:hover::before {
    left: 0;
}

.form-header {
    padding: 40px 40px 20px;
    border-bottom: 1px solid #f0f0f0;
}

.form-header h3 {
    font-size: 1.8rem;
    font-weight: 900;
    text-transform: uppercase;
    color: #000;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.form-header p {
    color: #666;
    font-weight: 500;
}

.form-body {
    padding: 40px;
}

/* Form Fields */
.field-group {
    margin-bottom: 30px;
}

.field-group-title {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.group-icon {
    width: 30px;
    height: 30px;
    background: #000;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 14px;
}

.field-group-title {
    font-weight: 700;
    text-transform: uppercase;
    color: #000;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-field.full-width {
    grid-column: span 2;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-field.full-width {
        grid-column: span 1;
    }
}

.form-label {
    display: flex;
    align-items: center;
    font-weight: 700;
    text-transform: uppercase;
    color: #000;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
}

.label-icon {
    margin-right: 8px;
    font-size: 12px;
}

.input-wrapper {
    position: relative;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 16px;
    border: 2px solid #f0f0f0;
    background: #fff;
    font-size: 16px;
    font-weight: 500;
    color: #000;
    transition: all 0.3s ease;
    outline: none;
}

.form-input:focus,
.form-textarea:focus {
    border-color: #000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-input::placeholder,
.form-textarea::placeholder {
    color: #999;
    font-weight: 400;
}

/* Submit Button */
.submit-button {
    background: #000;
    color: #fff;
    border: none;
    padding: 20px 40px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    width: 100%;
    margin-top: 20px;
}

.submit-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.submit-button:hover::before {
    left: 100%;
}

.submit-button:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.button-icon {
    margin-right: 10px;
    font-size: 16px;
}

/* Success Alert */
.success-alert {
    background: #000;
    color: #fff;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.success-alert i {
    margin-right: 10px;
    font-size: 18px;
}

/* Loading State */
.submit-button.loading {
    opacity: 0.7;
    pointer-events: none;
}

.submit-button.loading .button-text {
    opacity: 0;
}

.submit-button.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid #fff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Error States */
.form-input.error,
.form-textarea.error {
    border-color: #e74c3c;
    background: #fdf2f2;
}

.error-message {
    color: #e74c3c;
    font-size: 0.8rem;
    margin-top: 6px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Skeleton Loader */
.form-skeleton {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.skeleton-progress {
    height: 4px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.skeleton-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.skeleton-title {
    height: 20px;
    width: 60%;
    background: #f0f0f0;
    border-radius: 4px;
}

.skeleton-fields {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.skeleton-field {
    height: 40px;
    background: #f0f0f0;
    border-radius: 4px;
}

.skeleton-field-full {
    height: 40px;
    width: 100%;
    background: #f0f0f0;
    border-radius: 4px;
}

.skeleton-textarea {
    height: 80px;
    background: #f0f0f0;
    border-radius: 4px;
}

.skeleton-button {
    height: 50px;
    background: #000;
    border-radius: 4px;
    animation: pulse 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: 0 0; }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>
@endpush

@section('content')
<div class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-72 h-72 bg-black opacity-3 transform rotate-45 translate-x-36 -translate-y-36"></div>
            <div class="absolute bottom-0 left-0 w-96 h-2 bg-black opacity-10"></div>
            <div class="absolute top-1/2 left-10 w-1 h-32 bg-black opacity-20"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="w-8 h-0.5 bg-black"></div>
                <span class="hero-badge">BOOKBEE CONTACT</span>
                <div class="w-8 h-0.5 bg-black"></div>
            </div>
            
            <h1 class="text-4xl md:text-6xl font-black uppercase leading-[0.9] tracking-tight text-black mb-8">
                <span class="block">LIÊN HỆ</span>
                <span class="block text-gray-400">&</span>
                <span class="block">HỖ TRỢ</span>
            </h1>
            
            <p class="text-lg md:text-xl font-medium text-gray-700 max-w-2xl mx-auto">
                Hãy liên hệ với chúng tôi để được hỗ trợ tốt nhất
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="contact-container">
        @if(session('success'))
            <div class="success-alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="contact-grid">
            <!-- Contact Information -->
            <div class="contact-info">
                <span class="section-badge">THÔNG TIN</span>
                <h2>Kết nối<br>với chúng tôi</h2>
                <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy liên hệ qua các kênh dưới đây.</p>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Địa chỉ</h4>
                        <span>137 Nguyễn Thị Thập, Hòa Minh, Liên Chiểu, Đà Nẵng</span>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Điện thoại</h4>
                        <span>+84 123 456 789</span>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Email</h4>
                        <span>contact@bookbee.vn</span>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Giờ làm việc</h4>
                        <span>Thứ 2 - Thứ 6: 8:00 - 17:00</span>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="form-container">
                <div class="form-header">
                    <h3>Gửi tin nhắn</h3>
                    <p>Điền thông tin và chúng tôi sẽ phản hồi sớm nhất</p>
                </div>

                <!-- Thêm skeleton loader -->
                <div id="formSkeleton" class="form-skeleton">
                    <div class="skeleton-progress"></div>
                    <div class="skeleton-group">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-fields">
                            <div class="skeleton-field"></div>
                            <div class="skeleton-field"></div>
                        </div>
                    </div>
                    <div class="skeleton-group">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-fields">
                            <div class="skeleton-field-full"></div>
                            <div class="skeleton-field-full"></div>
                        </div>
                    </div>
                    <div class="skeleton-group">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-fields">
                            <div class="skeleton-textarea"></div>
                        </div>
                    </div>
                    <div class="skeleton-button"></div>
                </div>

                <!-- Form thực tế -->
                <div class="form-body" style="display: none;" id="actualForm">
                    <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
                        @csrf
        
        <!-- Thông tin cá nhân -->
        <div class="field-group" data-group="personal">
          <div class="field-group-title">
            <div class="group-icon">
              <i class="fas fa-user"></i>
            </div>
            Thông tin cá nhân
          </div>
          
          <div class="form-grid">
            <div class="form-field">
              <label for="name" class="form-label">
                <i class="fas fa-user label-icon"></i>
                Họ tên *
              </label>
              <div class="input-wrapper">
                <input type="text" name="name" id="name" 
                       class="form-input"
                       placeholder="Nhập họ tên của bạn" 
                       value="{{ old('name') }}" 
                       required>
              </div>
            </div>

            <div class="form-field">
              <label for="phone" class="form-label">
                <i class="fas fa-phone label-icon"></i>
                Số điện thoại
              </label>
              <div class="input-wrapper">
                <input type="tel" name="phone" id="phone" 
                       class="form-input"
                       placeholder="Nhập số điện thoại" 
                       value="{{ old('phone') }}">
              </div>
            </div>
          </div>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="field-group" data-group="contact">
          <div class="field-group-title">
            <div class="group-icon">
              <i class="fas fa-envelope"></i>
            </div>
            Thông tin liên hệ
          </div>

          <div class="form-grid">
            <div class="form-field full-width">
              <label for="email" class="form-label">
                <i class="fas fa-envelope label-icon"></i>
                Email *
              </label>
              <div class="input-wrapper">
                <input type="email" name="email" id="email" 
                       class="form-input"
                       placeholder="Nhập địa chỉ email" 
                       value="{{ old('email') }}" 
                       required>
              </div>
            </div>

            <div class="form-field full-width">
              <label for="subject" class="form-label">
                <i class="fas fa-tag label-icon"></i>
                Chủ đề *
              </label>
              <div class="input-wrapper">
                <input type="text" name="subject" id="subject"
                       class="form-input"
                       placeholder="Nhập chủ đề tin nhắn" 
                       value="{{ old('subject') }}" 
                       required>
              </div>
            </div>

            <div class="form-field full-width">
              <label for="address" class="form-label">
                <i class="fas fa-map-marker-alt label-icon"></i>
                Địa chỉ
              </label>
              <div class="input-wrapper">
                <input type="text" name="address" id="address"
                       class="form-input"
                       placeholder="Nhập địa chỉ của bạn" value="{{ old('address') }}">
              </div>
            </div>
          </div>
        </div>

        <!-- Nội dung tin nhắn -->
        <div class="field-group" data-group="message">
          <div class="field-group-title">
            <div class="group-icon">
              <i class="fas fa-comment"></i>
            </div>
            Nội dung tin nhắn
          </div>

          <div class="form-grid">
            <div class="form-field full-width">
              <label for="message" class="form-label">
                <i class="fas fa-comment-alt label-icon"></i>
                Tin nhắn *
              </label>
              <div class="input-wrapper">
                <textarea name="message" id="message" 
                          class="form-textarea"
                          placeholder="Nhập nội dung tin nhắn..." 
                          required>{{ old('message') }}</textarea>
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="submit-button" id="submitBtn">
          <i class="fas fa-paper-plane button-icon"></i>
          <span class="button-text">Gửi tin nhắn</span>
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<!-- Enhanced Contact Form Styles -->
<link rel="stylesheet" href="{{ asset('css/contact/contact-enhanced.css') }}">
@endpush

@push('scripts')
<!-- Enhanced JavaScript với UX nâng cao -->
<script src="{{ asset('js/contact/contact-enhanced.js') }}"></script>
@endpush
