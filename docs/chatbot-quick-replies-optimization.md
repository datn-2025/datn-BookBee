# Tối Ưu Hóa Chatbot với Quick Replies

## Mô tả chức năng

Chức năng Quick Replies cho phép chatbot xử lý các câu hỏi phổ biến trực tiếp từ database mà không cần gọi API Gemini, giúp:
- Giảm thời gian phản hồi từ 5-10 giây xuống còn < 1 giây
- Tiết kiệm chi phí API Gemini
- Cải thiện trải nghiệm người dùng
- Đảm bảo độ chính xác cao cho các truy vấn cơ bản

## Các Quick Replies được hỗ trợ

### 1. Sách bán chạy
**Từ khóa:** `sách bán chạy`, `bán chạy nhất`, `bestseller`, `best seller`

**Logic:** Truy vấn sách có số lượng bán cao nhất từ bảng `order_items`

**Phản hồi:** Danh sách 6 cuốn sách bán chạy nhất với thông tin đầy đủ

### 2. Sách mới
**Từ khóa:** `sách mới`, `mới nhất`, `new`, `latest`

**Logic:** Sắp xếp theo `publication_date` giảm dần

**Phản hồi:** Danh sách 6 cuốn sách mới nhất

### 3. Sách giảm giá
**Từ khóa:** `sách giảm giá`, `giảm giá`, `sale`, `discount`, `khuyến mãi`

**Logic:** Lọc sách có `discount > 0` trong bảng `book_formats`

**Phản hồi:** Danh sách 6 cuốn sách đang giảm giá

### 4. Xem danh mục
**Từ khóa:** `xem danh mục`, `danh mục`, `categories`, `menu`

**Logic:** Lấy tất cả danh mục từ bảng `categories`

**Phản hồi:** Danh sách các danh mục sách (type: `categories`)

### 5. Sách theo danh mục
**Từ khóa:** `sách [tên danh mục]` hoặc chỉ `[tên danh mục]`

**Danh mục hỗ trợ:** văn học, kinh tế, kỹ năng sống, thiếu nhi, khoa học, lịch sử, tâm lý, công nghệ

**Logic:** Tìm danh mục theo tên và lấy sách thuộc danh mục đó

## Cách hoạt động

### Luồng xử lý

1. **Nhận tin nhắn từ người dùng**
2. **Kiểm tra Quick Replies** (hàm `handleQuickReplies()`)
   - Nếu match → Trả về kết quả từ database
   - Nếu không match → Tiếp tục với Gemini API
3. **Xử lý encoding UTF-8** để đảm bảo nhận diện đúng tiếng Việt
4. **Trả về phản hồi** với định dạng phù hợp

### Code implementation

```php
// Trong ChatbotController.php
public function processMessage(Request $request)
{
    $userPrompt = $input['message'] ?? $request->input('message', '');
    
    // Kiểm tra quick replies trước
    $quickResponse = $this->handleQuickReplies($userPrompt);
    if ($quickResponse) {
        return response()->json([
            'success' => true,
            'data' => $quickResponse
        ]);
    }
    
    // Tiếp tục với Gemini API nếu không phải quick reply
    // ...
}

private function handleQuickReplies($userPrompt)
{
    $prompt = mb_strtolower(trim($userPrompt), 'UTF-8');
    
    // Xử lý các pattern khác nhau
    if (str_contains($prompt, 'danh mục') || str_contains($prompt, 'danh m?c')) {
        // Trả về danh mục
    }
    
    // Các logic khác...
    return null; // Không phải quick reply
}
```

## Xử lý vấn đề Encoding

### Vấn đề
Khi gửi request từ frontend, ký tự tiếng Việt có thể bị encode sai, ví dụ:
- "danh mục" → "danh m?c"
- Bytes: `64616e68206d3f63`

### Giải pháp
1. Sử dụng `mb_strtolower()` với encoding UTF-8
2. Kiểm tra cả phiên bản gốc và phiên bản bị encode sai
3. Sử dụng `str_contains()` thay vì regex để tránh vấn đề encoding

```php
// Kiểm tra cả hai trường hợp
if (str_contains($prompt, 'danh mục') || str_contains($prompt, 'danh m?c')) {
    // Xử lý logic
}
```

## Kết quả đạt được

### Trước khi tối ưu
- Thời gian phản hồi: 5-10 giây (do timeout API Gemini)
- Tỷ lệ lỗi: Cao (do API Gemini không ổn định)
- Chi phí: Cao (mỗi request đều gọi API)

### Sau khi tối ưu
- Thời gian phản hồi: < 1 giây cho quick replies
- Tỷ lệ lỗi: Gần như 0% cho quick replies
- Chi phí: Giảm đáng kể (chỉ gọi API khi cần thiết)
- Trải nghiệm người dùng: Cải thiện rõ rệt

## Test Cases

### Test 1: Sách bán chạy
```bash
curl -X POST http://localhost:8000/api/chatbot/message \
  -H "Content-Type: application/json" \
  -d '{"message":"sách bán chạy nhất"}'
```

**Kết quả mong đợi:** Type `product_list` với danh sách sách bán chạy

### Test 2: Xem danh mục
```bash
curl -X POST http://localhost:8000/api/chatbot/message \
  -H "Content-Type: application/json" \
  -d '{"message":"danh mục"}'
```

**Kết quả mong đợi:** Type `categories` với danh sách tên danh mục

### Test 3: Sách giảm giá
```bash
curl -X POST http://localhost:8000/api/chatbot/message \
  -H "Content-Type: application/json" \
  -d '{"message":"sách giảm giá"}'
```

**Kết quả mong đợi:** Type `product_list` với sách có discount > 0

## Lưu ý quan trọng

1. **Thứ tự kiểm tra:** Quick replies được kiểm tra trước Gemini API
2. **Fallback:** Nếu không match quick reply, vẫn sử dụng Gemini API
3. **Performance:** Database queries được tối ưu với `take()` và `with()`
4. **Encoding:** Luôn xử lý UTF-8 encoding cho tiếng Việt
5. **Maintenance:** Dễ dàng thêm/sửa quick replies mới

## Cách mở rộng

### Thêm quick reply mới
1. Thêm pattern matching trong `handleQuickReplies()`
2. Viết logic truy vấn database
3. Định nghĩa format phản hồi
4. Test và document

### Ví dụ thêm "Sách theo tác giả"
```php
// Trong handleQuickReplies()
if (preg_match('/sách của (.+)/i', $prompt, $matches)) {
    $authorName = trim($matches[1]);
    $books = Book::whereHas('authors', function($query) use ($authorName) {
        $query->where('name', 'like', '%' . $authorName . '%');
    })->take(6)->get();
    
    return [
        'type' => 'product_list',
        'content' => "📚 Sách của {$authorName}:",
        'products' => $books->map(fn($book) => $this->formatBookCard($book))
    ];
}
```

## Kết luận

Việc implement Quick Replies đã cải thiện đáng kể hiệu suất và trải nghiệm của chatbot BookBee.vn. Hệ thống này có thể dễ dàng mở rộng để hỗ trợ thêm nhiều loại truy vấn phổ biến khác.