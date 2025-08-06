# Chức năng Chatbot

## Mô tả chức năng
Chatbot giới thiệu sản phẩm sách tự động, hỗ trợ khách hàng tìm hiểu và mua sách thông qua giao diện trò chuyện thân thiện.

## Tính năng chính

### 1. Nhận diện ý định (Intent Detection)
- **Hỏi giá sách**: Trả lời thông tin giá của sách cụ thể
- **Hỏi đánh giá**: Hiển thị đánh giá và rating của sách
- **Tìm sách theo tác giả**: Liệt kê các sách của tác giả
- **Tìm sách theo danh mục**: Hiển thị sách trong danh mục cụ thể
- **Sách bán chạy**: Gợi ý top sách bán chạy nhất
- **Sách mới**: Hiển thị sách mới phát hành
- **Sách giảm giá**: Liệt kê sách đang có khuyến mãi
- **Chào hỏi**: Phản hồi lời chào và giới thiệu dịch vụ
- **Tìm kiếm chung**: Tìm kiếm sách theo từ khóa

### 2. Giao diện người dùng
- **Widget chatbot**: Nút chat floating ở góc phải màn hình
- **Cửa sổ chat**: Giao diện trò chuyện với header, body và input
- **Quick replies**: Các nút trả lời nhanh cho tương tác dễ dàng
- **Product cards**: Hiển thị thông tin sách dạng thẻ với hình ảnh
- **Typing indicator**: Hiệu ứng đang nhập khi bot phản hồi

### 3. Tích hợp dữ liệu
- Kết nối với database sách, danh mục, đánh giá
- Hiển thị thông tin real-time từ hệ thống
- Hỗ trợ tìm kiếm và lọc dữ liệu

## Cách sử dụng

### Cho người dùng cuối
1. Click vào icon chat ở góc phải màn hình
2. Nhập câu hỏi hoặc chọn quick reply
3. Xem phản hồi từ bot với thông tin sách
4. Click vào sản phẩm để xem chi tiết

### Cho admin
- Chatbot tự động hoạt động, không cần cấu hình
- Dữ liệu được lấy từ database hiện có
- Có thể tùy chỉnh phản hồi trong ChatbotController

## Cấu trúc mã nguồn

### Backend (Laravel)
```
app/Http/Controllers/Api/ChatbotController.php
├── processMessage()     # Xử lý tin nhắn từ user
├── detectIntent()       # Nhận diện ý định
├── generateResponse()   # Tạo phản hồi
├── getCategories()      # API lấy danh mục
└── getBooksByCategory() # API lấy sách theo danh mục
```

### Frontend (Blade + JavaScript)
```
resources/views/components/chatbot-widget.blade.php
├── HTML structure       # Cấu trúc giao diện
├── CSS styling         # Thiết kế responsive
└── JavaScript logic    # Xử lý tương tác
```

### API Routes
```
routes/api.php
├── POST /api/chatbot/message           # Xử lý tin nhắn
├── GET /api/chatbot/categories         # Lấy danh mục
└── GET /api/chatbot/books-by-category  # Lấy sách theo danh mục
```

## Tích hợp vào layout

Chatbot được tích hợp vào layout chính:
```blade
{{-- resources/views/layouts/app.blade.php --}}
@include('components.chatbot-widget')
```

## Kết quả mong muốn

### Trải nghiệm người dùng
- Giao diện thân thiện, dễ sử dụng
- Phản hồi nhanh và chính xác
- Hiển thị thông tin sách đầy đủ
- Hỗ trợ tìm kiếm hiệu quả

### Tính năng kỹ thuật
- API RESTful chuẩn
- Responsive design
- Tích hợp seamless với hệ thống
- Xử lý lỗi graceful

### Hiệu suất
- Tải nhanh, không ảnh hưởng trang chính
- Tối ưu database queries
- Cache dữ liệu khi cần thiết

## Mở rộng tương lai

1. **AI/NLP nâng cao**: Tích hợp OpenAI hoặc Google Dialogflow
2. **Đa ngôn ngữ**: Hỗ trợ tiếng Anh và các ngôn ngữ khác
3. **Personalization**: Gợi ý dựa trên lịch sử mua hàng
4. **Voice chat**: Hỗ trợ chat bằng giọng nói
5. **Analytics**: Theo dõi hiệu quả chatbot

## Troubleshooting

### Lỗi thường gặp
1. **Chatbot không hiển thị**: Kiểm tra include trong layout
2. **API không hoạt động**: Kiểm tra routes và CSRF token
3. **Dữ liệu không load**: Kiểm tra database connection
4. **Giao diện bị lỗi**: Kiểm tra CSS conflicts

### Debug
```bash
# Kiểm tra routes
php artisan route:list | grep chatbot

# Clear cache
php artisan cache:clear
php artisan config:clear
```