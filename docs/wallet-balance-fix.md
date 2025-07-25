# Sửa Lỗi Hiển Thị Số Dư Ví

## Vấn Đề
Khi người dùng chọn phương thức thanh toán "Ví điện tử", hệ thống hiển thị thông báo "Chưa kích hoạt ví" thay vì hiển thị số dư 0đ cho người dùng chưa có wallet record trong database.

## Nguyên Nhân
1. **Database Structure**: Hệ thống có 2 cách lưu trữ thông tin ví:
   - Bảng `wallets` với cột `balance` (relationship)
   - Bảng `users` với cột `wallet` (direct column)

2. **Missing Wallet Records**: Người dùng mới chưa có record trong bảng `wallets`

3. **Frontend Logic**: View đang kiểm tra `Auth::user()->wallet` và hiển thị "Chưa kích hoạt ví" khi null

## Giải Pháp Đã Áp Dụng

### 1. Cập Nhật OrderController
**File**: `app/Http/Controllers/OrderController.php`

```php
public function checkout(Request $request)
{
    $user = Auth::user();
    
    // Đảm bảo user có wallet
    $user->wallet()->firstOrCreate(
        ['user_id' => $user->id],
        ['balance' => 0]
    );
    
    // ... rest of the method
}
```

**Thêm import**:
```php
use App\Models\Wallet;
```

### 2. Cập Nhật View Template
**File**: `resources/views/orders/checkout.blade.php`

**Thay đổi hiển thị số dư**:
```php
// Trước
@if(Auth::user()->wallet)
    {{ number_format(Auth::user()->wallet->balance) }}đ
@else
    Chưa kích hoạt ví
@endif

// Sau
@if(Auth::user()->wallet)
    {{ number_format(Auth::user()->wallet->balance) }}đ
@else
    0đ
@endif
```

**Cải thiện logic kiểm tra số dư**:
```php
@php
    $walletBalance = Auth::user()->wallet ? Auth::user()->wallet->balance : 0;
    $totalAmount = $subtotal + 20000;
@endphp
@if($walletBalance < $totalAmount)
    <div class="mt-2 text-xs text-red-600 font-medium">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        Số dư không đủ để thanh toán
    </div>
@endif
```

### 3. Cập Nhật OrderService
**File**: `app/Services/OrderService.php`

**Method `checkWalletBalance`**:
```php
public function checkWalletBalance(User $user, $amount)
{
    // Tạo wallet nếu user chưa có
    $wallet = $user->wallet()->firstOrCreate(
        ['user_id' => $user->id],
        ['balance' => 0]
    );

    if ($wallet->balance < $amount) {
        throw new \Exception('Số dư ví không đủ để thanh toán. Số dư hiện tại: ' . number_format($wallet->balance) . 'đ');
    }

    return true;
}
```

**Method `processWalletPayment`**:
```php
// Kiểm tra số dư ví (sẽ tạo wallet nếu chưa có)
$this->checkWalletBalance($user, $order->total_amount);

// Lấy wallet (đã được tạo trong checkWalletBalance)
$wallet = $user->wallet()->first();
```

## Lợi Ích

### 1. **User Experience**
- Hiển thị số dư 0đ thay vì thông báo "Chưa kích hoạt ví"
- Người dùng hiểu rõ tình trạng ví của mình
- Giao diện nhất quán cho tất cả người dùng

### 2. **System Reliability**
- Tự động tạo wallet cho user mới
- Tránh lỗi null reference
- Đảm bảo tính toàn vẹn dữ liệu

### 3. **Maintainability**
- Logic tạo wallet tập trung
- Dễ dàng debug và maintain
- Consistent behavior across the system

## Files Đã Thay Đổi

1. **app/Http/Controllers/OrderController.php**
   - Thêm logic tạo wallet tự động
   - Thêm import Wallet model

2. **resources/views/orders/checkout.blade.php**
   - Cập nhật hiển thị số dư ví
   - Cải thiện logic kiểm tra số dư

3. **app/Services/OrderService.php**
   - Cập nhật `checkWalletBalance()` method
   - Cập nhật `processWalletPayment()` method

## Test Cases

### 1. **User Mới (Chưa có wallet)**
- ✅ Hiển thị "0đ" thay vì "Chưa kích hoạt ví"
- ✅ Tự động tạo wallet record khi truy cập checkout
- ✅ Hiển thị cảnh báo "Số dư không đủ để thanh toán"

### 2. **User Có Wallet (Số dư = 0)**
- ✅ Hiển thị "0đ"
- ✅ Hiển thị cảnh báo "Số dư không đủ để thanh toán"
- ✅ Không thể submit form khi chọn ví điện tử

### 3. **User Có Wallet (Số dư > 0)**
- ✅ Hiển thị số dư chính xác
- ✅ Cho phép thanh toán nếu số dư đủ
- ✅ Hiển thị cảnh báo nếu số dư không đủ

## Kết Luận
Việc sửa lỗi này đảm bảo:
- Tất cả user đều có wallet record
- Hiển thị thông tin ví nhất quán
- Trải nghiệm người dùng tốt hơn
- Hệ thống ổn định và đáng tin cậy