# Hệ thống Chat theo Đơn hàng - Phân tích Chi tiết

## 1. Tổng quan Tính năng

### 1.1. Mục tiêu
Cho phép khách hàng liên hệ trực tiếp với admin về một đơn hàng cụ thể thông qua chat realtime (API + Pusher/Laravel Echo).

### 1.2. Quy tắc thời gian
- Chat khả dụng từ khi tạo đơn hàng 
- Kéo dài đến 7 ngày sau khi đơn hàng ở trạng thái "Thành công"
- Tự động gửi thông tin đơn hàng khi bắt đầu chat

## 2. Phân tích Luồng nghiệp vụ Chi tiết

### 2.1. Kiểm tra Điều kiện Hiển thị Chat
**Điều kiện hiển thị nút "Liên hệ với Admin":**

✅ **Cho phép chat khi:**
- Đơn hàng thuộc về user hiện tại
- Trạng thái đơn hàng: "Chờ xác nhận", "Đã xác nhận", "Đang chuẩn bị", "Đang giao", "Thành công"
- Nếu trạng thái = "Thành công": `now() - completed_at <= 7 ngày`

❌ **Không cho phép chat khi:**
- Trạng thái "Thành công" nhưng quá 7 ngày
- Đơn hàng không thuộc về user hiện tại

### 2.2. Luồng Bắt đầu Chat

```mermaid
graph TD
    A[User click "Liên hệ Admin"] --> B[Frontend gọi POST /orders/{id}/start-chat]
    B --> C{Validate điều kiện}
    C -->|Hợp lệ| D[Tạo/Lấy conversation]
    C -->|Không hợp lệ| E[Trả về lỗi 403]
    D --> F[Gửi tin nhắn khởi tạo với thông tin đơn hàng]
    F --> G[Push realtime đến admin]
    G --> H[Trả về conversation_id cho frontend]
    H --> I[Mở cửa sổ chat]
```

### 2.3. Cấu trúc Tin nhắn Khởi tạo
Khi bắt đầu chat, hệ thống tự động gửi:
```
🛒 Thông tin đơn hàng #ORD-123456

📋 Chi tiết:
- Mã đơn hàng: ORD-123456
- Ngày đặt: 01/08/2025
- Trạng thái: Đang giao
- Tổng tiền: 299,000đ
- Sản phẩm: 3 items

Xin chào! Tôi cần hỗ trợ về đơn hàng này.
```

## 3. Thiết kế Database

### 3.1. Bảng Conversations (Cập nhật)
```sql
-- Thêm cột order_id vào bảng conversations hiện tại
ALTER TABLE conversations ADD COLUMN order_id VARCHAR(255) NULL;
ALTER TABLE conversations ADD FOREIGN KEY (order_id) REFERENCES orders(id);
ALTER TABLE conversations ADD INDEX idx_order_user (order_id, customer_id);
```

### 3.2. Bảng Messages (Sử dụng hiện tại)
Bảng `messages` hiện tại đã đủ, chỉ cần thêm type cho tin nhắn hệ thống:
- `type`: 'text', 'image', 'file', 'system_order_info'

### 3.3. Migration mới
```php
// database/migrations/xxxx_add_order_id_to_conversations.php
public function up()
{
    Schema::table('conversations', function (Blueprint $table) {
        $table->string('order_id')->nullable()->after('admin_id');
        $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        $table->index(['order_id', 'customer_id'], 'idx_order_customer');
    });
}
```

## 4. API Endpoints Cần Thêm

### 4.1. Kiểm tra Điều kiện Chat
```
GET /api/orders/{order_id}/can-chat
Response: {
    "can_chat": true,
    "reason": null
}
```

### 4.2. Bắt đầu Chat theo Đơn hàng
```
POST /api/orders/{order_id}/start-chat
Response: {
    "conversation_id": "uuid",
    "messages": [...],
    "order_info": {...}
}
```

### 4.3. Lấy Messages theo Đơn hàng
```
GET /api/orders/{order_id}/messages
Response: {
    "conversation_id": "uuid",
    "messages": [...],
    "order_info": {...}
}
```

## 5. Frontend Implementation

### 5.1. Thêm Button vào Order List/Detail
- Component: `OrderChatButton.vue` hoặc Blade
- Gọi API `can-chat` để hiển thị/ẩn button
- Tích hợp với chat widget hiện tại

### 5.2. Cập nhật Chat Widget
- Thêm mode "order-chat" 
- Hiển thị thông tin đơn hàng ở header
- Load messages theo conversation_id (bao gồm tất cả tin nhắn: order-specific + chat thông thường)
- Khi có order_id, hiển thị context về đơn hàng nhưng vẫn load full conversation history

### 5.3. Realtime Events
- Sử dụng Echo hiện tại
- Channel: `order-chat.{order_id}`
- Event: `OrderMessageSent`

## 6. Backend Implementation Plan

### 6.1. Models cần cập nhật
- `Conversation`: thêm relationship `order()`
- `Order`: thêm relationship `conversations()`
- `Message`: thêm logic xử lý tin nhắn hệ thống

### 6.2. Controllers mới
- `OrderChatController`: xử lý chat theo đơn hàng
- Methods: `canChat()`, `startChat()`, `getMessages()`

### 6.3. Services
- `OrderChatService`: business logic
- `OrderChatValidationService`: validate điều kiện

### 6.4. Events & Jobs
- `OrderChatStarted`: event khi bắt đầu chat
- `CheckExpiredOrderChats`: job cron để ẩn chat quá hạn

## 7. Validation Rules

### 7.1. Kiểm tra quyền truy cập
```php
// Đơn hàng phải thuộc về user hiện tại
$order->user_id === auth()->id()

// Admin có thể truy cập tất cả order chats
auth()->user()->isAdmin()
```

### 7.2. Kiểm tra thời gian
```php
// Đơn hàng thành công trong vòng 7 ngày
if ($order->status === 'Thành công') {
    return $order->completed_at->diffInDays(now()) <= 7;
}

// Đơn hàng chưa hoàn tất
return in_array($order->status, [
    'Chờ xác nhận', 'Đã xác nhận', 
    'Đang chuẩn bị', 'Đang giao', 'Thành công'
]);
```

## 8. UI/UX Considerations

### 8.1. Order List Page
- Thêm icon chat bên cạnh mỗi đơn hàng eligible
- Tooltip: "Liên hệ về đơn hàng này"

### 8.2. Order Detail Page  
- Button "Liên hệ với Admin" prominence
- Hiển thị lịch sử chat nếu đã có

### 8.3. Chat Window
- Header hiển thị: "Chat về đơn hàng #ORD-123456"
- Quick info panel với order details
- Auto-scroll to system message

## 9. Admin Dashboard Integration

### 9.1. Chat Management
- Tab "Chat theo đơn hàng" trong admin
- Filter theo order status, date range
- Quick view order details từ chat

### 9.2. Notifications
- Realtime notification khi có chat mới
- Badge count cho unread order chats

## 10. Testing Strategy

### 10.1. Unit Tests
- OrderChatService methods
- Validation logic
- Time-based conditions

### 10.2. Feature Tests
- API endpoints
- Chat creation flow
- Permission checks

### 10.3. Browser Tests
- E2E chat flow
- Realtime messaging
- UI state management

## 11. Deployment Checklist

- [ ] Database migration
- [ ] Update Pusher config
- [ ] Frontend build & deploy
- [ ] Setup cron job for expired chats
- [ ] Admin training documentation
- [ ] Monitor performance metrics

## 12. Future Enhancements

### 12.1. Phase 2 Features
- File upload trong order chat
- Order status updates trong chat
- Quick actions (track order, cancel, etc.)

### 12.2. Analytics
- Chat usage metrics
- Response time tracking
- Customer satisfaction rating

---

**Prepared by:** System Analyst  
**Date:** August 8, 2025  
**Status:** Ready for Implementation
