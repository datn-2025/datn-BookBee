# Luồng Đặt Trước & Thanh Toán (Hướng dẫn cho người mới)

Tài liệu này giải thích luồng đặt trước từ tuyến (routes) đến controller, tập trung vào thanh toán Ví điện tử và VNPay, và cách chuyển preorder thành đơn hàng chính thức.

## Tổng quan
- Người dùng mở form đặt trước → gửi biểu mẫu.
- Chọn Ví điện tử: trừ tiền ngay → đánh dấu preorder đã thanh toán.
- Chọn VNPay: chuyển hướng sang VNPay → quay về site → đánh dấu preorder đã thanh toán nếu thành công.
- Admin về sau chuyển các preorder đã xác nhận thành Order; phương thức/trạng thái thanh toán được suy luận từ dữ liệu preorder.

## File quan trọng
- `routes/web.php` — các route đặt trước và callback VNPay.
- `app/Http/Controllers/PreorderController.php` — tạo preorder, xử lý thanh toán Ví/VNPay.
- `app/Http/Controllers/Admin/AdminPreorderController.php` — quản trị và `convertToOrder()`.

## Routes (mức cao)
- `GET  /preorders/{book}` → `PreorderController@create` (hiển thị form).
- `POST /preorders` → `PreorderController@store` (tạo preorder + quyết định thanh toán).
- `GET  /preorders/{preorder}` → `PreorderController@show`.
- `POST /preorders/{preorder}/cancel` → `PreorderController@cancel`.
- `GET  /vnpay/return/preorder` → `PreorderController@vnpayReturn` (xử lý callback VNPay cho preorder).

Xem comment trong `routes/web.php` để hiểu mục đích từng route.

## PreorderController — các bước chính
- `create(Book $book)`
  - Kiểm tra `Book::canPreorder()`.
  - Tải phương thức thanh toán hợp lệ (VNPay, Ví) và số dư ví user cho UI.
- `store(Request $request)`
  - Validate; tính `unit_price`, `total_amount`.
  - Tạo bản ghi `preorders`.
  - Rẽ nhánh theo phương thức thanh toán:
    - Ví điện tử:
      - Kiểm tra số dư, trừ ví, tạo `WalletTransaction`.
      - Cập nhật preorder: `payment_status = 'paid'`, giữ `payment_method_id`.
      - Commit + gửi email xác nhận.
    - VNPay:
      - Commit preorder trước.
      - Tạo URL VNPay và redirect.
      - Lưu tạm `vnpay_transaction_id` (mã ref local) và `payment_status = 'processing'`.
      - KHÔNG tạo bản ghi Payment tại đây (bảng payments yêu cầu `order_id` NOT NULL).
- `vnpay_payment($data)`
  - Tạo URL ký VNPay và redirect user.
  - Cập nhật preorder: đặt `vnpay_transaction_id` tạm và `payment_status = 'processing'`.
- `vnpayReturn(Request $request)`
  - Xác thực chữ ký VNPay.
  - Thành công (`vnp_ResponseCode === '00'`): đặt `payment_status = 'paid'`, cập nhật transaction id nếu cần, gửi email.
  - Thất bại: đặt `payment_status = 'failed'`.

## Vì sao không tạo Payment khi preorder?
- Bảng `payments` yêu cầu `order_id` NOT NULL.
- Preorder chưa có `order_id`.
- Do đó lưu thông tin thanh toán ngay trên preorder: `payment_status`, `vnpay_transaction_id`.
- Khi convert preorder → order, lúc đó mới tạo bản ghi payment cho order (nếu hệ thống dùng ở bước này).

## AdminPreorderController::convertToOrder()
- Điều kiện: preorder đã được xác nhận (`Đã xác nhận` / `confirmed`).
- Các bước:
  1) Tạo địa chỉ giao hàng từ trường của preorder.
  2) Đảm bảo tồn tại trạng thái đơn hàng cơ bản: `Chờ xác nhận`.
  3) Đảm bảo tồn tại trạng thái thanh toán: `Đã Thanh Toán`, `Chưa Thanh Toán`.
  4) Chuẩn bị `orderId` và `orderCode`.
  5) Xác định `payment_method_id` cho Order:
     - Mặc định: dùng `preorder.payment_method_id`.
     - Nếu `preorder.payment_status === 'paid'`:
       - Có `vnpay_transaction_id` → chọn VNPay theo tên (tránh hard-code ID).
       - Không có → chọn Ví điện tử theo tên.
     - Fallback:
       - Đã thanh toán nhưng chưa rõ → ưu tiên Ví.
       - Chưa thanh toán → COD.
  6) Chèn vào `orders` với `payment_status_id` Paid/Unpaid tương ứng.
  7) Chèn `order_items` từ dữ liệu preorder.
  8) Cập nhật preorder sang `delivered` + `delivered_at` (quyết định thiết kế; có thể tùy chỉnh).

## Bug đã khắc phục (Ví hiển thị COD)
- Triệu chứng: Preorder trả bằng Ví nhưng Order sau convert lại hiển thị COD.
- Nguyên nhân: Khi `payment_status = 'paid'` và không có `vnpay_transaction_id`, code cũ fallback sang COD.
- Sửa: Nếu `payment_status = 'paid'` và không có VNPay transaction → chọn Ví theo tên. Bổ sung fallback an toàn.
- Kết quả: Order sau convert hiển thị đúng phương thức thanh toán Ví.

## Lookup theo tên thay vì hard-code ID
- Tránh hard-code UUID phương thức.
- Tra theo pattern tên: `%vnpay%`, `%ví điện tử%`, `%wallet%`, `%cash on delivery%`, v.v.
- Linh hoạt khi ID thay đổi giữa môi trường.

## Checklist kiểm tra nhanh
- Sau preorder bằng Ví:
  - `preorders.payment_status = 'paid'`.
  - `preorders.payment_method_id` trỏ tới Ví.
- Sau VNPay thành công:
  - `preorders.payment_status = 'paid'` và có `vnpay_transaction_id` hợp lệ.
- Sau convertToOrder:
  - `orders.payment_method_id` khớp Ví hoặc VNPay.
  - `orders.payment_status_id` là Paid nếu preorder đã paid.

## Gợi ý xử lý sự cố
- Order hiển thị COD nhưng đáng lẽ là Ví:
  - Kiểm tra preorder: `payment_status`, `payment_method_id`, `vnpay_transaction_id`.
  - Đảm bảo phương thức Ví đang active và tên trong bảng `payment_methods.name` chứa từ khóa.
  - Xem log quanh `convertToOrder` để biết nhánh/fallback đã chọn.

## Hướng phát triển
- Bảng `preorder_payments` riêng để lưu metadata gateway mà không cần `order_id`.
- Chuẩn hóa enum trạng thái và phương thức.
- Tách logic tìm kiếm phương thức thanh toán vào service dùng chung.
