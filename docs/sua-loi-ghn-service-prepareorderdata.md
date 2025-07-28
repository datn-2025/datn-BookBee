# Sửa lỗi GHN Service prepareOrderData()

## Mô tả lỗi
Lỗi `Too few arguments to function App\Services\GhnService::prepareOrderData(), 1 passed in OrderService.php on line 206 and exactly 2 expected` xảy ra khi gọi hàm `prepareOrderData()` trong `GhnService` mà không truyền đủ tham số.

## Nguyên nhân
- Hàm `prepareOrderData()` trong `GhnService` yêu cầu 2 tham số: `$order` và `$items`
- Nhưng trong `OrderService` chỉ truyền 1 tham số `$order`
- Code cũ không xử lý được combo items và book items khác nhau

## Giải pháp đã áp dụng

### 1. Sửa lời gọi hàm trong OrderService.php
```php
// Trước (lỗi)
$orderData = $this->ghnService->prepareOrderData($order);

// Sau (đã sửa)
// Lấy order items với relationship để chuẩn bị dữ liệu GHN
$orderItems = $order->orderItems()->with(['book', 'collection'])->get();
$orderData = $this->ghnService->prepareOrderData($order, $orderItems);
```

### 2. Cập nhật logic xử lý trong GhnService.php
```php
public function prepareOrderData($order, $items)
{
    $totalWeight = 0;
    $itemsData = [];
    
    foreach ($items as $item) {
        if ($item->is_combo) {
            // Xử lý combo - ước tính trọng lượng combo
            $comboWeight = 1000; // 1kg cho combo
            $totalWeight += $comboWeight * $item->quantity;
            
            $itemsData[] = [
                'name' => $item->collection->name ?? 'Combo',
                'quantity' => $item->quantity,
                'weight' => $comboWeight
            ];
        } else {
            // Xử lý sách lẻ
            $bookWeight = ($item->book->page_count ?? 200) * 5;
            $totalWeight += $bookWeight * $item->quantity;
            
            $itemsData[] = [
                'name' => $item->book->title ?? 'Sách',
                'quantity' => $item->quantity,
                'weight' => $bookWeight
            ];
        }
    }
    
    // ... phần còn lại của hàm
}
```

## Các file đã được sửa
1. `app/Services/OrderService.php` - dòng 206-208
2. `app/Services/GhnService.php` - hàm `prepareOrderData()`

## Lưu ý quan trọng
- Đảm bảo có sản phẩm trong giỏ hàng trước khi đặt hàng
- Hàm đã hỗ trợ cả combo và sách lẻ
- Sử dụng eager loading để tránh N+1 query problem

## Cách tránh lỗi tương tự
1. Luôn kiểm tra signature của hàm trước khi gọi
2. Sử dụng eager loading khi cần truy cập relationship
3. Xử lý cả trường hợp combo và sách lẻ trong logic
4. Validate dữ liệu đầu vào trước khi xử lý