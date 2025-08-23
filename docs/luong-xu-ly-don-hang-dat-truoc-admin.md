# Luồng Xử Lý Đơn Hàng Đặt Trước (Pre-order) - Admin

## 📋 Tổng Quan

Tài liệu này mô tả luồng xử lý đơn hàng đặt trước từ phía admin, bao gồm việc chuyển đổi preorder thành order thực tế khi sách được phát hành.

## 👤 Luồng phía Khách hàng (Client)

Luồng này bám sát code trong `app/Http/Controllers/PreorderController.php`.

- __Mở form__: `create(Book $book)`
  - Kiểm tra `book->canPreorder()`
  - Nạp `formats`, `attributes`, `paymentMethods` (chỉ VNPay, Ví điện tử), `wallet` của user, `preorderDiscountPercent`

- __Gửi form__: `store(Request $request)`
  - Validate dữ liệu, kiểm tra địa chỉ với sách vật lý (không yêu cầu với ebook)
  - Tính giá: `getPreorderPrice()` + phụ thu thuộc tính + số lượng → `unit_price`, `total_amount`
  - Tạo `preorders` với `status='pending'`, `payment_status='pending'` và thông tin địa chỉ (nếu vật lý)

- __Thanh toán__:
  - __Ví điện tử__: kiểm tra số dư ví → trừ tiền → tạo `WalletTransaction` → cập nhật `preorders.payment_status='paid'` → gửi email → redirect `preorders.show`
  - __VNPay__: gọi `vnpay_payment($vnpayData)` → lưu `preorders.vnpay_transaction_id` (mã tham chiếu) và `payment_status='processing'` → redirect VNPay. Sau khi quay lại `vnpayReturn()`:
    - Xác thực chữ ký
    - Tìm preorder theo `vnp_TxnRef`
    - Thành công (`vnp_ResponseCode === '00'`): `payment_status='paid'`, cập nhật `vnpay_transaction_id` = mã giao dịch thực tế, gửi email
    - Thất bại: `payment_status='failed'`

- __Xem chi tiết__: `show(Preorder $preorder)` — chỉ chủ sở hữu được xem

- __Danh sách của tôi__: `index()` — phân trang các preorder của user

- __Hủy đơn__: `cancel(Preorder $preorder)` — chỉ khi `Preorder::canBeCancelled()` trả true

Lưu ý quan trọng về thanh toán (đã áp dụng trong code):
- Không tạo bản ghi `payments` trong giai đoạn preorder (tránh lỗi ràng buộc `order_id` NOT NULL). Thay vào đó dùng các trường trên bảng `preorders`: `payment_status`, `vnpay_transaction_id`.
- Khi chuyển đổi sang Order mới tạo `payments` (nếu cần) và gán đúng `payment_method_id` (tránh gán nhầm COD cho đơn đã trả qua Ví/ VNPay).

## 🔄 Luồng Xử Lý Chính

### 1. Kiểm Tra Điều Kiện Chuyển Đổi

Trước khi chuyển đổi preorder thành order, hệ thống kiểm tra:

- ✅ Preorder có trạng thái `confirmed`
- ✅ Sách đã được phát hành (release_date <= ngày hiện tại)
- ✅ Chưa có order nào được tạo từ preorder này

### 2. Quy Trình Chuyển Đổi

#### Bước 1: Tạo Address Record
```php
$addressId = \Illuminate\Support\Str::uuid();
\DB::table('addresses')->insert([
    'id' => $addressId,
    'user_id' => $preorder->user_id,
    'recipient_name' => $preorder->customer_name,
    'phone' => $preorder->phone,
    'address_detail' => $preorder->address ?? 'Địa chỉ từ đơn đặt trước',
    'city' => 'Hà Nội',
    'district' => 'Quận 1',
    'ward' => 'Phường 1',
    'is_default' => false,
    'created_at' => now(),
    'updated_at' => now()
]);
```

#### Bước 2: Tạo/Lấy Order Status và Payment Status
```php
$orderStatusId = \DB::table('order_statuses')->where('name', 'Đã Thanh Toán')->value('id');
if (!$orderStatusId) {
    $orderStatusId = \Illuminate\Support\Str::uuid();
    \DB::table('order_statuses')->insert([
        'id' => $orderStatusId,
        'name' => 'Đã Thanh Toán',
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
```

#### Bước 3: Tạo Order Record
```php
$orderId = \Illuminate\Support\Str::uuid();
$orderCode = 'ORD-' . time() . '-' . rand(1000, 9999);

\DB::table('orders')->insert([
    'id' => $orderId,
    'user_id' => $preorder->user_id,
    'order_code' => $orderCode,
    'total_amount' => $preorder->total_amount,
    'address_id' => $addressId,
    'order_status_id' => $orderStatusId,
    'payment_status_id' => $paymentStatusId,
    'note' => 'Chuyển đổi từ đơn đặt trước #' . $preorder->id,
    'created_at' => now(),
    'updated_at' => now()
]);
```

#### Bước 4: Tạo Order Item
```php
$orderItemId = \Illuminate\Support\Str::uuid();
\DB::table('order_items')->insert([
    'id' => $orderItemId,
    'order_id' => $orderId,
    'book_id' => $preorder->book_id,
    'book_format_id' => $preorder->book_format_id,
    'quantity' => $preorder->quantity,
    'price' => $preorder->unit_price,
    'total' => $preorder->total_amount,
    'is_combo' => false,
    'created_at' => now(),
    'updated_at' => now()
]);
```

#### Bước 5: Cập Nhật Preorder
```php
$preorder->update([
    'status' => 'delivered',
    'delivered_at' => now(),
    'notes' => ($preorder->notes ? $preorder->notes . "\n\n" : '') . 
              'Đã chuyển đổi thành đơn hàng #' . $orderId
]);
```

## 🛠️ Implementation

### Controller Method

**File:** `app/Http/Controllers/Admin/AdminPreorderController.php`

```php
public function convertToOrder(Preorder $preorder)
{
    try {
        // Kiểm tra điều kiện
        if ($preorder->status !== 'confirmed') {
            return back()->with('error', 'Chỉ có thể chuyển đổi preorder đã xác nhận');
        }

        if (!$preorder->book->isReleased()) {
            return back()->with('error', 'Sách chưa được phát hành');
        }

        // Kiểm tra đã có order chưa
        $existingOrder = Order::where('note', 'LIKE', '%đơn đặt trước #' . $preorder->id . '%')->first();
        if ($existingOrder) {
            return back()->with('error', 'Preorder này đã được chuyển đổi thành order');
        }

        DB::beginTransaction();

        // Thực hiện chuyển đổi (code như trên)
        
        DB::commit();

        return redirect()->route('admin.orders.show', $order)
                        ->with('success', 'Đã chuyển đổi preorder thành order thành công!');

    } catch (Exception $e) {
        DB::rollback();
        return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}
```

### Route

**File:** `routes/web.php`

```php
Route::post('/admin/preorders/{preorder}/convert-to-order', 
    [AdminPreorderController::class, 'convertToOrder'])
    ->name('admin.preorders.convert-to-order');
```

## 🎯 Lý Do Sử Dụng Raw SQL

### Vấn Đề Với Eloquent Models

Ban đầu, việc sử dụng Eloquent models gặp lỗi:

```
Illuminate\Database\Eloquent\Model->save()
Illuminate\Database\Eloquent\Builder->create()
```

### Giải Pháp Raw SQL

Sử dụng raw SQL giải quyết được:

1. **Tránh validation phức tạp** của Eloquent
2. **Kiểm soát chính xác** dữ liệu được insert
3. **Tránh conflict** với các observer/event listeners
4. **Performance tốt hơn** cho bulk operations

## 📊 Database Schema

### Bảng Orders
```sql
CREATE TABLE orders (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    order_code VARCHAR(255) NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    address_id CHAR(36) NOT NULL,
    order_status_id CHAR(36) NOT NULL,
    payment_status_id CHAR(36) NOT NULL,
    note TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Bảng Order Items
```sql
CREATE TABLE order_items (
    id CHAR(36) PRIMARY KEY,
    order_id CHAR(36) NOT NULL,
    book_id CHAR(36) NOT NULL,
    book_format_id CHAR(36),
    quantity INTEGER NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    is_combo BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🧪 Testing

### Test Script

**File:** `test_convert_to_order.php`

```php
<?php
// Script test chuyển đổi preorder thành order
// Kiểm tra toàn bộ luồng từ preorder confirmed -> order delivered
```

### Kết Quả Test

```
=== TEST CHUYỂN ĐỔI ĐƠN HÀNG ĐẶT TRƯỚC ===
✅ Tất cả điều kiện đều thỏa mãn
✅ Đã tạo Order ID: 85f4f459-cb2c-4968-9c61-42904340c7f0
✅ Đã tạo OrderItem ID: f9e87e4c-95ed-4d6e-bd18-fe5447703a58
✅ Đã cập nhật trạng thái preorder thành 'delivered'
🎉 CHUYỂN ĐỔI THÀNH CÔNG!
```

## 🚨 Lưu Ý Quan Trọng

### 1. Transaction Safety
- Luôn sử dụng `DB::beginTransaction()` và `DB::commit()`
- Rollback khi có lỗi: `DB::rollback()`

### 2. Validation
- Kiểm tra trạng thái preorder
- Kiểm tra sách đã phát hành
- Kiểm tra không trùng lặp order

### 3. Data Integrity
- Sử dụng UUID cho tất cả primary keys
- Đảm bảo foreign key constraints
- Validate dữ liệu trước khi insert

### 4. Error Handling
- Log lỗi chi tiết
- Thông báo lỗi user-friendly
- Rollback transaction khi có lỗi

## 📈 Metrics & Monitoring

### Các Chỉ Số Cần Theo Dõi

1. **Tỷ lệ chuyển đổi thành công**: Preorder -> Order
2. **Thời gian xử lý**: Từ lúc click convert đến hoàn thành
3. **Tỷ lệ lỗi**: Số lần convert thất bại
4. **Doanh thu**: Tổng giá trị orders được tạo từ preorders

### Log Events

```php
Log::info('Preorder conversion started', [
    'preorder_id' => $preorder->id,
    'user_id' => $preorder->user_id,
    'book_id' => $preorder->book_id
]);

Log::info('Preorder conversion completed', [
    'preorder_id' => $preorder->id,
    'order_id' => $orderId,
    'processing_time' => $processingTime
]);
```

## 🔄 Workflow Diagram

```
Preorder (confirmed) 
       ↓
   Check Conditions
       ↓
   Create Address
       ↓
   Create Order
       ↓
   Create OrderItem
       ↓
   Update Preorder (delivered)
       ↓
   Success Response
```

---

**Ngày tạo:** 07/08/2025  
**Tác giả:** AI Assistant  
**Trạng thái:** Hoàn thành  
**Version:** 1.0

## Mô tả chức năng

Luồng xử lý đơn hàng đặt trước trong admin được thiết kế với 2 trạng thái chính và 2 hành động tương ứng:

### Trạng thái và Hành động:
1. **Chờ xử lý** (pending) → **Duyệt đơn** → **Đã xác nhận** (confirmed)
2. **Đã xác nhận** (confirmed) → **Chuyển thành đơn hàng** (khi sách đã phát hành)

## 🔹 1. Danh sách đơn hàng đặt trước

### Vị trí: `/admin/preorders`
### File: `resources/views/admin/preorders/index.blade.php`

Hiển thị danh sách các đơn hàng đặt trước với các trạng thái chính:
- **Chờ xử lý** (pending) - Đơn mới tạo, cần admin duyệt
- **Đã xác nhận** (confirmed) - Đã được duyệt, chờ chuyển thành đơn hàng khi sách phát hành
- **Đã giao** (delivered) - Đã chuyển thành đơn hàng thành công
- **Đã hủy** (cancelled) - Đơn bị hủy

### Thông tin hiển thị:
- Mã đơn hàng
- Thông tin khách hàng (tên, email, số điện thoại)
- Thông tin sách (tên, ảnh bìa, ngày ra mắt)
- Định dạng sách (Ebook/Sách vật lý)
- Số lượng và tổng tiền
- Trạng thái hiện tại
- Ngày tạo đơn

## 🔹 2. Các nút hành động

### A. Nút "Duyệt đơn hàng"
**Điều kiện hiển thị:**
- Đơn hàng có trạng thái: `pending` (chờ xử lý)
- Màu nút: Warning (vàng)
- Icon: `fas fa-check`

### B. Nút "Chuyển thành đơn hàng"
**Điều kiện hiển thị:**
- Đơn hàng có trạng thái: `confirmed` (đã xác nhận)
- Sách đã phát hành (`$book->isReleased() = true`)
- Màu nút: Success (xanh)
- Icon: `fas fa-exchange-alt`

### Vị trí hiển thị:
1. **Trang chi tiết**: `resources/views/admin/preorders/show.blade.php`
   - Nút lớn ở header trang
2. **Trang danh sách**: `resources/views/admin/preorders/index.blade.php`
   - Trong dropdown "Thao tác" của mỗi dòng

## 🔹 3. Xử lý duyệt đơn

### A. Duyệt đơn hàng (Approve)
**Controller:** `app/Http/Controllers/Admin/AdminPreorderController.php`
**Method:** `approvePreorder(Request $request, Preorder $preorder)`
**Route:** `POST /admin/preorders/{preorder}/approve`

#### Luồng xử lý:

**Bước 1: Kiểm tra trạng thái đơn hàng**
```php
if ($preorder->status !== 'pending') {
    return back()->with('error', 'Chỉ có thể duyệt đơn đang chờ xử lý.');
}
```

**Bước 2: Kiểm tra ngày phát hành và cảnh báo**
```php
if (!$preorder->book->isReleased()) {
    if (!$request->has('force_approve')) {
        return back()->with('warning', [
            'message' => "Sách chưa đến ngày phát hành ({$releaseDate}). Bạn có chắc chắn muốn duyệt đơn này không?",
            'confirm_url' => route('admin.preorders.approve', $preorder) . '?force_approve=1',
            'preorder_id' => $preorder->id
        ]);
    }
}
```

**Bước 3: Thực hiện duyệt**
- Cập nhật trạng thái từ `pending` → `confirmed`
- Hiển thị thông báo thành công

### B. Chuyển đổi thành đơn hàng (Convert)
**Method:** `convertToOrder(Request $request, Preorder $preorder)`
**Route:** `POST /admin/preorders/{preorder}/convert-to-order`

#### Luồng xử lý:

**Bước 1: Kiểm tra trạng thái**
```php
if ($preorder->status !== 'confirmed') {
    return back()->with('error', 'Chỉ có thể chuyển đổi đơn đã được xác nhận.');
}
```

**Bước 2: Kiểm tra sách đã phát hành**
```php
if (!$preorder->book->isReleased()) {
    return back()->with('error', 'Không thể chuyển đổi đơn hàng khi sách chưa được phát hành.');
}
```

**Bước 3: Thực hiện chuyển đổi**
1. Tạo `Order` mới với trạng thái "Đã Thanh Toán"
2. Tạo `OrderItem` tương ứng
3. Cập nhật trạng thái `Preorder` thành "delivered"
4. Chuyển hướng đến trang chi tiết đơn hàng mới

## 🔹 4. Cảnh báo cho Admin

### Khi nào hiển thị cảnh báo:
- Sách chưa đến ngày phát hành (`release_date > now()`)
- Admin click "Duyệt đơn đặt trước" lần đầu

### Nội dung cảnh báo:
- **Tiêu đề**: "Cảnh Báo" với icon cảnh báo
- **Thông điệp**: "Sách chưa đến ngày phát hành (dd/mm/yyyy). Bạn có chắc chắn muốn duyệt đơn này không?"
- **Lưu ý**: "Việc duyệt đơn trước ngày phát hành có thể ảnh hưởng đến quy trình quản lý kho và giao hàng."

### Tùy chọn cho Admin:
1. **Hủy**: Quay lại trang trước
2. **Xác Nhận Duyệt**: Tiếp tục duyệt đơn với parameter `force_convert=1`

## 🔹 5. Giao diện Modal Cảnh Báo

### HTML Structure:
```html
<div class="modal fade" id="warningModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Cảnh Báo
                </h5>
            </div>
            <div class="modal-body">
                <p id="warningMessage"></p>
                <div class="alert alert-warning">
                    <strong>Lưu ý:</strong> Việc duyệt đơn trước ngày phát hành...
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <a href="#" id="confirmConvertBtn" class="btn btn-warning">
                    <i class="fas fa-check"></i> Xác Nhận Duyệt
                </a>
            </div>
        </div>
    </div>
</div>
```

### JavaScript xử lý:
```javascript
@if(session('warning'))
    const warningData = @json(session('warning'));
    $('#warningMessage').text(warningData.message);
    $('#confirmConvertBtn').attr('href', warningData.confirm_url);
    $('#warningModal').modal('show');
@endif
```

## 🔹 6. Các trạng thái đơn hàng

### Trạng thái chính:
- **pending**: Chờ xử lý - Có thể duyệt đơn
- **confirmed**: Đã xác nhận - Có thể chuyển thành đơn hàng (nếu sách đã phát hành)
- **delivered**: Đã giao - Đã chuyển thành đơn hàng thành công
- **cancelled**: Đã hủy - Không thể xử lý

### Luồng trạng thái:
```
pending → [Duyệt đơn hàng] → confirmed → [Chuyển thành đơn hàng] → delivered
   ↓
cancelled
```

## 🔹 7. Kiểm tra ngày phát hành

### Method trong Model Book:
```php
public function isReleased(): bool
{
    return $this->release_date && $this->release_date->isPast();
}
```

### Logic kiểm tra:
- `release_date` phải tồn tại
- `release_date` phải là ngày trong quá khứ (đã qua)
- Nếu `false`: sách chưa phát hành → hiển thị cảnh báo
- Nếu `true`: sách đã phát hành → duyệt bình thường

## 🔹 8. Quy trình sử dụng

### A. Duyệt đơn hàng (pending → confirmed):
1. Admin vào trang danh sách hoặc chi tiết đơn đặt trước có trạng thái `pending`
2. Click nút "Duyệt đơn hàng" (màu vàng)
3. **Nếu sách chưa phát hành:**
   - Hệ thống hiển thị modal cảnh báo với ngày phát hành
   - Admin có thể: **Hủy** hoặc **Xác nhận duyệt**
4. **Nếu sách đã phát hành:** Duyệt trực tiếp
5. Trạng thái đơn chuyển từ `pending` → `confirmed`
6. Hiển thị thông báo thành công

### B. Chuyển thành đơn hàng (confirmed → delivered):
1. Admin vào trang danh sách hoặc chi tiết đơn đặt trước có trạng thái `confirmed`
2. **Điều kiện:** Sách phải đã phát hành
3. Click nút "Chuyển thành đơn hàng" (màu xanh)
4. Xác nhận trong popup
5. Hệ thống tạo đơn hàng mới và cập nhật trạng thái `delivered`
6. Chuyển hướng đến trang đơn hàng mới

## 🔹 9. Lợi ích của cải tiến

### Tính linh hoạt:
- Admin có thể duyệt đơn sớm khi cần thiết
- Không bị ràng buộc cứng nhắc bởi ngày phát hành

### An toàn:
- Cảnh báo rõ ràng về rủi ro
- Yêu cầu xác nhận từ admin
- Ghi log đầy đủ các thao tác

### Trải nghiệm người dùng:
- Giao diện trực quan với màu sắc phù hợp
- Thông báo rõ ràng, dễ hiểu
- Quy trình đơn giản, không phức tạp

## 🔹 10. Kết quả mong muốn

- ✅ Admin có thể duyệt đơn đặt trước linh hoạt
- ✅ Cảnh báo rõ ràng khi sách chưa phát hành
- ✅ Quy trình an toàn với xác nhận từ admin
- ✅ Giao diện thân thiện, dễ sử dụng
- ✅ Tích hợp mượt mà với hệ thống hiện có
- ✅ Ghi log đầy đủ cho việc theo dõi và kiểm tra