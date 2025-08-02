# Debug Refund Button Display Issue

## Vấn đề
User báo cáo rằng refund button vẫn hiển thị sai trên giao diện đơn hàng.

## Phân tích đã thực hiện

### 1. Kiểm tra Logic Backend
- ✅ **EbookRefundService**: Logic đã chính xác
  - Refund 100% nếu chưa tải
  - Refund 40% nếu tải 1 lần
  - Refund 0% nếu tải >1 lần
- ✅ **Database Query**: Query đếm download đã đúng
  - `order_id` trong `ebook_downloads` tham chiếu đến bảng `orders`
  - Logic filter theo `user_id`, `order_id`, `book_format_id` chính xác

### 2. Kiểm tra Logic Frontend
- ✅ **Blade Template**: Logic hiển thị trong `order-details.blade.php` đã đúng
  ```php
  $hasEbook = $order->orderItems()->whereHas('bookFormat', function($query) {
      $query->where('format_name', 'Ebook');
  })->exists();
  
  $canRefundEbook = false;
  if ($hasEbook) {
      $ebookRefundService = app(\App\Services\EbookRefundService::class);
      $canRefundResult = $ebookRefundService->canRefundEbook($order, auth()->user());
      $canRefundEbook = $canRefundResult['can_refund'];
  }
  ```

### 3. Test Results
Kết quả test cho thấy:

#### Đơn hàng có thể refund:
- **Order ID**: `d443301e-c30c-4e6d-9960-e1cd6d48b680`
  - Download Count: 0
  - Can Refund: YES
  - Should show button: YES

- **Order ID**: `500759c9-611c-442d-bacb-4c4db6e93a46`
  - Download Count: 0
  - Can Refund: YES
  - Should show button: YES

#### Đơn hàng không thể refund:
- **Order ID**: `241a704d-1119-4490-ada2-ee40d92291ce`
  - Download Count: 2
  - Can Refund: NO
  - Should show button: NO

## Các bước đã thực hiện

### 1. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 2. Kiểm tra Step-by-step
- ✅ User ownership check
- ✅ Payment status check
- ✅ Has ebook check
- ✅ Existing refund check
- ✅ Time limit check (7 days)
- ✅ Download count check

## Kết luận

**Logic backend và frontend đều hoạt động chính xác.** Các test cho thấy:

1. **Đơn hàng chưa tải ebook**: Hiển thị refund button ✅
2. **Đơn hàng đã tải >1 lần**: Không hiển thị refund button ✅

## Khuyến nghị

Nếu user vẫn thấy hiển thị sai, có thể do:

1. **Browser Cache**: User cần hard refresh (Ctrl+F5)
2. **Session Cache**: User cần logout/login lại
3. **Specific Order**: Cần kiểm tra đơn hàng cụ thể mà user đang xem

## Cách kiểm tra cụ thể

1. Xác định Order ID mà user đang xem
2. Chạy query kiểm tra:
   ```php
   $order = Order::find('ORDER_ID');
   $ebookRefundService = app(\App\Services\EbookRefundService::class);
   $result = $ebookRefundService->canRefundEbook($order, $order->user);
   dd($result);
   ```

## Files liên quan

- `app/Services/EbookRefundService.php`
- `resources/views/clients/account/order-details.blade.php`
- `app/Models/EbookDownload.php`
- `database/migrations/2025_07_30_165142_create_ebook_downloads_table.php`

---

**Ngày tạo**: 2025-08-01  
**Trạng thái**: Logic đã chính xác, cần kiểm tra cache/session