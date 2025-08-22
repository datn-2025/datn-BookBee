# Chú giải từng dòng: PreorderController (tiếng Việt)

Tài liệu này đi qua từng khối lệnh trong `app/Http/Controllers/PreorderController.php` và giải thích chi tiết mục đích của từng dòng/nhóm dòng. Do số dòng thực tế có thể thay đổi theo commit, phần chú giải bám theo hàm và các dòng lệnh theo thứ tự xuất hiện.

## use/import và khai báo lớp
- `namespace App\Http\Controllers;` — không gian tên của controller.
- `use ...` — import các model, service, facade được dùng trong file (Book, Preorder, Wallet, Mail, DB, Auth, Log, v.v.).
- `class PreorderController extends Controller` — định nghĩa controller.
- `protected $ghnService; protected $paymentService;` — thuộc tính để lưu service GHN và Payment.
- `__construct(GhnService $ghnService, PaymentService $paymentService)` — inject service qua DI, gán vào thuộc tính.

## create(Book $book)
- Kiểm tra `$book->canPreorder()` — nếu false, redirect back với thông báo lỗi.
- Lấy `formats` của sách: `$book->formats()->get()` — phục vụ người dùng chọn định dạng.
- Lấy `attributes` của sách kèm quan hệ `attributeValue.attribute` — hiển thị các tuỳ chọn thuộc tính.
- Lấy danh sách `paymentMethods` cho preorder: thường là VNPay, Ví điện tử (lọc theo is_active nếu có logic).
- Lấy `wallet` của user hiện tại (nếu đăng nhập) để hiển thị số dư.
- Tính `preorderDiscountPercent` nếu có cấu hình trên sách.
- `return view('preorders.create', compact(...))` — trả về view hiển thị form đặt trước.

## store(Request $request)
1) Validate dữ liệu:
- `book_id`, `book_format_id`, `quantity`, `selected_attributes`, `customer_name`, `email`, `phone`, địa chỉ (nếu cần), `payment_method_id`, v.v.

2) Lấy dữ liệu sách/format và tính giá:
- `Book::findOrFail()` và `BookFormat::findOrFail()` — truy vấn đối tượng cần thiết.
- Tính `unit_price` từ format, `totalAmount = unit_price * quantity` (có thể áp dụng giảm giá preorder nếu có).

3) Chuẩn bị dữ liệu địa chỉ nếu không phải ebook:
- Tra cứu Province/District/Ward theo id gửi lên để lấy thêm `*_name`.

4) Tạo preorder:
- `Preorder::create($preorderData)` — chứa các trường: user, book, format, quantity, unit_price, total_amount, customer_name, email, phone, địa chỉ, status mặc định là pending/confirmed tuỳ luồng.

5) Nhánh Thanh toán Ví điện tử (Wallet):
- Kiểm tra đăng nhập: `Auth::user()` và ví: `Wallet::where('user_id', ...)`.
- Kiểm tra số dư `balance >= totalAmount` — nếu không đủ, `DB::rollback()` và trả về lỗi.
- Trừ ví: `$wallet->decrement('balance', $totalAmount)`.
- Ghi lịch sử ví: tạo `WalletTransaction` với số tiền âm, `type = 'preorder_payment'`, tham chiếu `preorder_id`.
- Cập nhật preorder: `payment_status = 'paid'`, lưu `payment_method_id` là ví.
- `DB::commit()` và gửi email `PreorderConfirmation` (thử/catch để log lỗi gửi mail nếu có).
- Redirect `preorders.show` với thông báo thành công.

6) Nhánh Thanh toán VNPay:
- `DB::commit()` sau khi đã tạo preorder để đảm bảo có id.
- Tạo `vnpayData` gồm `preorder_id`, `payment_status_id` (nếu dùng), `payment_method_id`, `order_code` (tiền tố PRE + id + timestamp), `amount`, `order_info`.
- Gọi `vnpay_payment($vnpayData)` để build URL và redirect sang VNPay.

7) Các phương thức khác (nếu không phải ví/VNPay):
- Giữ preorder, gửi email xác nhận (chưa thanh toán), redirect `preorders.show`.

## show(Preorder $preorder)
- Kiểm tra quyền (user hiện tại phải sở hữu preorder nếu là route user-facing).
- Nạp quan hệ `book`, `bookFormat`.
- Trả view `preorders.show` để người dùng xem chi tiết.

## index()
- Lọc theo `Auth::id()`, nạp `book`, `bookFormat`, sắp xếp `created_at desc`, phân trang 10.
- Trả view `preorders.index`.

## cancel(Preorder $preorder)
- Kiểm tra quyền sở hữu: `Auth::id() !== $preorder->user_id` thì 403.
- Kiểm tra `canBeCancelled()` — nếu false, back với lỗi.
- Gọi `$preorder->markAsCancelled()` — cập nhật trạng thái và timestamps liên quan.
- Back với thông báo thành công.

## getBookInfo(Book $book)
- Kiểm tra `canPreorder()`.
- Trả JSON gồm `book`, `formats`, `attributes` (cho UI động ở client).

## vnpay_payment($data)
- Đọc config: `vnp_TmnCode`, `vnp_HashSecret`, `vnp_Url`, `vnp_Returnurl`.
- Tạo tham số: `vnp_TxnRef` (order_code tạm), `vnp_OrderInfo`, `vnp_Amount`, `vnp_CreateDate`, etc.
- Ký `vnp_SecureHash` bằng HMAC SHA512.
- Cập nhật preorder theo `preorder_id`: set `vnpay_transaction_id = order_code` (mã tham chiếu tạm) và `payment_status = 'processing'`.
- `return redirect($vnp_Url)` sang cổng VNPay.

## vnpayReturn(Request $request)
- Lấy `vnp_SecureHash` từ query VNPay.
- Lọc toàn bộ tham số `vnp_*` trừ `vnp_SecureHash` và sắp xếp theo key.
- Tự tính lại hash (`hash_hmac('sha512', query, secret)`) và so sánh để xác thực.
- Tìm `preorder` dựa vào `vnp_TxnRef` (mã đã lưu tạm trước đó).
- Nếu `vnp_ResponseCode === '00'`:
  - Cập nhật `payment_status = 'paid'`, lưu transaction id thực tế nếu cần.
  - Gửi email xác nhận.
- Nếu thất bại:
  - Cập nhật `payment_status = 'failed'`.
- Redirect về `preorders.show` với thông báo.

---
Ghi chú: Với preorder, KHÔNG tạo bản ghi `payments` tại thời điểm thanh toán VNPay để tránh lỗi `order_id` NOT NULL. Thay vào đó lưu trạng thái/tham chiếu ngay trên bảng `preorders`. Khi chuyển preorder → order, khi đó mới ánh xạ sang payment của order.
