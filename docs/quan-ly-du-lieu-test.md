# Quản Lý Dữ Liệu Test - Hệ Thống Đơn Hàng

## Tổng Quan
Tài liệu này giải thích nguồn gốc và cách quản lý dữ liệu test trong hệ thống, đặc biệt là dữ liệu đơn hàng hiển thị "10 sản phẩm" hoặc số lượng lớn khác.

## Nguồn Gốc Dữ Liệu

### 1. Seeder Tạo Dữ Liệu Test
**File:** `database/seeders/ROrderItemSeeder.php`

```php
// Tạo ngẫu nhiên 1-3 sản phẩm cho mỗi đơn hàng
$numOfItems = rand(1, 3);

// Số lượng mỗi sản phẩm từ 1-5
$quantity = rand(1, 5);
```

**Kết quả:** Một đơn hàng có thể có tổng số lượng sản phẩm từ 1 đến 15 (3 sản phẩm × 5 số lượng).

### 2. Hiển Thị Trong Giao Diện
**File:** `resources/views/clients/account/order-details.blade.php` (dòng 342)

```blade
<h4>SẢN PHẨM ĐÃ ĐẶT ({{ $order->orderItems->sum('quantity') }} sản phẩm)</h4>
```

**Giải thích:** `sum('quantity')` tính tổng số lượng của tất cả sản phẩm trong đơn hàng.

## Dữ Liệu Test Hiện Tại

Dựa trên kiểm tra database:
- **Order 1:** BBE-PHYSICAL-1753800342 - 3 sản phẩm
- **Order 2:** ORDFLCDYYMY - 6 sản phẩm  
- **Order 3:** ORDVYDQOQZS - 16 sản phẩm

## Cách Quản Lý Dữ Liệu Test

### 1. Xóa Dữ Liệu Test Cũ
```bash
# Xóa tất cả đơn hàng test
php artisan tinker
>>> \App\Models\Order::truncate();
>>> \App\Models\OrderItem::truncate();
```

### 2. Tạo Dữ Liệu Test Thực Tế
**File:** `database/seeders/RealisticOrderSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Book;
use App\Models\BookFormat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealisticOrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('role', function($q) {
            $q->where('name', 'User');
        })->take(5)->get();

        foreach ($users as $user) {
            // Tạo 2-3 đơn hàng cho mỗi user
            for ($i = 0; $i < rand(2, 3); $i++) {
                $order = Order::create([
                    'id' => (string) Str::uuid(),
                    'order_code' => 'ORD' . date('Ymd') . strtoupper(Str::random(6)),
                    'user_id' => $user->id,
                    'total_amount' => 0, // Sẽ tính sau
                    // ... các field khác
                ]);

                // Tạo 1-3 sản phẩm cho mỗi đơn hàng (thực tế hơn)
                $numItems = rand(1, 3);
                $totalAmount = 0;

                for ($j = 0; $j < $numItems; $j++) {
                    $book = Book::inRandomOrder()->first();
                    $bookFormat = BookFormat::where('book_id', $book->id)->first();
                    
                    // Số lượng thực tế: 1-2 cuốn (không quá 5)
                    $quantity = rand(1, 2);
                    $price = $bookFormat->price;
                    $itemTotal = $price * $quantity;
                    
                    OrderItem::create([
                        'id' => (string) Str::uuid(),
                        'order_id' => $order->id,
                        'book_id' => $book->id,
                        'book_format_id' => $bookFormat->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                    ]);
                    
                    $totalAmount += $itemTotal;
                }

                // Cập nhật tổng tiền đơn hàng
                $order->update(['total_amount' => $totalAmount]);
            }
        }
    }
}
```

### 3. Chạy Seeder Mới
```bash
php artisan db:seed --class=RealisticOrderSeeder
```

## Kiểm Tra Dữ Liệu

### 1. Kiểm Tra Qua Tinker
```bash
php artisan tinker
>>> $orders = \App\Models\Order::with('orderItems')->take(5)->get();
>>> foreach($orders as $order) { echo $order->order_code . ' - ' . $order->orderItems->sum('quantity') . " sản phẩm\n"; }
```

### 2. Kiểm Tra Qua Web Interface
- Truy cập: `http://127.0.0.1:8000/account/orders`
- Đăng nhập với tài khoản user
- Kiểm tra số lượng sản phẩm hiển thị

## Khuyến Nghị

### 1. Dữ Liệu Production
- Mỗi đơn hàng nên có 1-3 sản phẩm
- Mỗi sản phẩm có số lượng 1-2 (tối đa 3)
- Tổng số lượng sản phẩm/đơn hàng: 1-6 sản phẩm

### 2. Validation Trong Code
```php
// Trong OrderService hoặc Controller
public function validateOrderItems($items)
{
    $totalQuantity = collect($items)->sum('quantity');
    
    if ($totalQuantity > 10) {
        throw new \Exception('Đơn hàng không thể có quá 10 sản phẩm');
    }
    
    foreach ($items as $item) {
        if ($item['quantity'] > 5) {
            throw new \Exception('Mỗi sản phẩm không thể có quá 5 cuốn');
        }
    }
}
```

### 3. Hiển Thị Thân Thiện
```blade
@php
    $totalItems = $order->orderItems->sum('quantity');
    $itemText = $totalItems == 1 ? 'sản phẩm' : 'sản phẩm';
@endphp

<h4>SẢN PHẨM ĐÃ ĐẶT ({{ $totalItems }} {{ $itemText }})</h4>
```

## Kết Luận

Dữ liệu "10 sản phẩm" là kết quả của seeder test tạo ra số lượng ngẫu nhiên. Để có dữ liệu thực tế hơn:

1. **Xóa dữ liệu test cũ**
2. **Tạo seeder mới với số lượng hợp lý**
3. **Thêm validation để kiểm soát số lượng**
4. **Test lại giao diện với dữ liệu mới**

Việc này sẽ đảm bảo hệ thống hiển thị dữ liệu chính xác và thực tế hơn cho người dùng cuối.