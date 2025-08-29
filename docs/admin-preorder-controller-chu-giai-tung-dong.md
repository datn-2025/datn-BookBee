# Chú giải từng dòng: AdminPreorderController (tiếng Việt)

Tài liệu này giải thích chi tiết các phần quan trọng trong `app/Http/Controllers/Admin/AdminPreorderController.php`, đặc biệt là luồng chuyển đổi preorder thành order (`convertToOrder`). Do số dòng thay đổi theo commit, phần chú giải bám theo các phương thức và khối lệnh theo thứ tự xuất hiện.

## use/import và khai báo lớp
- `namespace App\Http\Controllers\Admin;` — không gian tên cho controller phía admin.
- `use App\Http\Controllers\Controller;` — kế thừa Controller gốc.
- `use App\Models\...` — import các model: `Preorder`, `Book`, `BookFormat`, `User`, `Order`, `Province`, `District`, `Ward`, ...
- `use Illuminate\Support\Facades\{DB, Log, Auth, Mail};` — Facade cho DB, ghi log, xác thực, gửi mail.
- `class AdminPreorderController extends Controller` — định nghĩa controller admin.

## index(Request $request)
- Tạo `$query = Preorder::with(['user', 'book', 'bookFormat']);` — eager-load để giảm N+1.
- Áp dụng bộ lọc theo `status`, `book_id`, và chuỗi tìm kiếm (`customer_name`, `email`, `phone`).
- Áp dụng filter ngày `date_from`, `date_to` theo `created_at`.
- Phân trang 20; đồng thời tính `stats` thống kê theo scope: `pending()`, `confirmed()`, ...
- Lấy danh sách sách có `pre_order = true` để filter UI.
- Trả view `admin.preorders.index` với dữ liệu.

## create()
- Lấy danh sách sách pre-order kèm `bookFormats` để chọn nhanh.
- Lấy danh sách user để gán preorder cho user cụ thể (nếu cần).
- Lấy `provinces` để đổ dropdown địa chỉ.
- Trả view `admin.preorders.create`.

## store(Request $request)
1) Validate dữ liệu đầu vào: `book_id`, `book_format_id`, `quantity`, thông tin khách hàng và địa chỉ, `status` (pending/confirmed), `expected_delivery_date`.
2) Bắt đầu `DB::beginTransaction()`.
3) Truy xuất `Book`, `BookFormat` và kiểm tra `pre_order`.
4) Tính `unitPrice` từ format và `totalAmount = unitPrice * quantity`.
5) Nếu không phải ebook, tra `Province/District/Ward` để lấy `*_name`.
6) `Preorder::create([...])` với đầy đủ trường; nếu `status = confirmed` thì set `confirmed_at = now()`.
7) Tăng `book->preorder_count`.
8) `DB::commit()`; redirect về trang chi tiết với thông báo thành công.
9) Bắt lỗi: `DB::rollback()`, `Log::error(...)`, back với thông báo lỗi.

## show(Preorder $preorder)
- Nạp `user`, `book`, `bookFormat` và trả view `admin.preorders.show`.

## updateStatus(Request $request, Preorder $preorder)
- Validate `status` trong tập cho phép (song ngữ Việt/Anh).
- Lưu `oldStatus` để so sánh sau.
- `DB::beginTransaction()`; cập nhật `status`, `notes`.
- Cập nhật các timestamp tương ứng: `confirmed_at`, `shipped_at`, `delivered_at` theo trạng thái.
- `DB::commit()`.
- Nếu trạng thái thay đổi, gửi email `PreorderStatusUpdate` (bắt lỗi gửi mail và ghi log nếu có).
- Bắt lỗi cập nhật: rollback + log + trả thông báo lỗi.

## approvePreorder(Request $request, Preorder $preorder)
- Ghi log thông tin gọi.
- Chỉ cho phép duyệt nếu đang ở trạng thái chờ (pending/"Chờ xác nhận").
- Nếu sách chưa phát hành, yêu cầu xác nhận lại (hiển thị warning + link xác nhận `force_approve`).
- Cập nhật `status = 'Đã xác nhận'` và `confirmed_at = now()`; ghi log và trả thông báo thành công.
- Bắt lỗi: log và trả thông báo lỗi.

## convertToOrder(Request $request, Preorder $preorder)
Phần cốt lõi để chuyển đổi preorder (đặt trước) sang order (đơn chính thức).

1) Kiểm tra điều kiện:
- Chỉ convert khi preorder đã được xác nhận (`Đã xác nhận` hoặc `confirmed`).
- Nếu sách chưa phát hành vẫn cho phép convert, nhưng ghi `Log::info` cảnh báo (ghi `converted_by`).

2) Bắt đầu transaction DB.

3) Tạo địa chỉ giao hàng:
- Sinh `$addressId = Str::uuid()`.
- `DB::table('addresses')->insert([...])` từ trường của preorder: `recipient_name`, `phone`, `address_detail`.
- Các trường `city/district/ward` đặt tạm (có thể thay bằng dữ liệu thực tế nếu đã lưu trong preorder).

4) Đảm bảo trạng thái Order tồn tại:
- Tìm `order_statuses` có `name = 'Chờ xác nhận'`; nếu chưa có thì insert.

5) Đảm bảo trạng thái Payment tồn tại:
- Tìm hoặc thêm `payment_statuses` cho `Đã Thanh Toán` và `Chưa Thanh Toán`.

6) Chuẩn bị ID và mã đơn:
- `$orderId = Str::uuid()` và `$orderCode = 'ORD-' . time() . '-' . rand(1000, 9999)`.

7) Xác định phương thức thanh toán cho Order (`payment_method_id`):
- Mặc định: dùng `preorder->payment_method_id` (tránh mất lựa chọn ban đầu).
- Nếu `preorder->payment_status === 'paid'`:
  - Có `vnpay_transaction_id` → tìm `payment_methods` theo tên chứa: `%vnpay%`, `%vn pay%`, `%vn-pay%`.
  - Không có `vnpay_transaction_id` → tìm theo tên chứa: `%ví điện tử%`, `%vi dien tu%`, `%wallet%`, `%e-wallet%`, `%momo%`.
- Fallback cuối:
  - Nếu đã `paid` nhưng vẫn chưa tìm ra → ưu tiên ví (lookup như trên).
  - Nếu chưa `paid` → fallback sang COD (lookup tên: `%thanh toán khi nhận hàng%`, `%cash on delivery%`, `%cod%`).
- Ghi `Log::info(...)` để debug đường rẽ nhánh được chọn.

8) Tạo Order:
- `DB::table('orders')->insert([...])` với: `order_status_id`, `payment_status_id` (paid/unpaid theo `preorder.payment_status`), `payment_method_id` đã xác định, `note` ghi rõ nguồn từ preorder.
- Lấy `$order = Order::find($orderId)` dùng tiếp cho items.

9) Tạo OrderItem:
- Sinh `$orderItemId = Str::uuid()`.
- `DB::table('order_items')->insert([...])` từ dữ liệu preorder: `book_id`, `book_format_id`, `quantity`, `price = unit_price`, `total = total_amount`, `is_combo = false`.

10) Cập nhật Preorder sau khi convert:
- `status = 'delivered'`, `delivered_at = now()` (theo quyết định thiết kế hiện tại; có thể đổi sang `processing/shipped` tuỳ nghiệp vụ).

11) Commit và redirect:
- `DB::commit()` rồi `redirect()->route('admin.orders.show', $order)` với thông báo thành công.

12) Bắt lỗi:
- `DB::rollback()`; `Log::error('Lỗi chuyển đổi preorder thành order: ...')`; back với thông báo lỗi.

### Ghi chú về lỗi “Ví điện tử hiển thị COD” (đã khắc phục)
- Trước đây: Nếu `payment_status = 'paid'` nhưng không có `vnpay_transaction_id`, code đã rơi vào fallback COD.
- Khắc phục: Trong nhánh `paid` không có VNPay transaction, chọn đúng phương thức Ví điện tử (lookup theo tên). Đồng thời thêm fallback an toàn: `paid` → ví; `unpaid` → COD.

### Ghi chú chung
- Không hard-code UUID phương thức thanh toán; tìm theo pattern tên để linh hoạt giữa các môi trường.
- Ghi log tại các nhánh chính để debug nhanh khi hiển thị sai phương thức.
- Có thể trích xuất logic "tìm phương thức thanh toán theo pattern" vào một service dùng chung nếu muốn tái sử dụng.
