 TÀI LIỆU HỆ THỐNG SÁCH ĐIỆN TỬ (EBOOK) TRONG WEBSITE BÁN SÁCH — LARAVEL

1. Quản lý sách từ Admin

Quản trị viên có thể tạo mới hoặc chỉnh sửa sách.

Với sách điện tử, quản trị viên sẽ:

Đánh dấu đây là sách điện tử (ebook).

Tải lên file PDF gốc của sách.

Tải lên bản đọc thử (ví dụ vài trang đầu).

Các file PDF này được lưu ở thư mục an toàn, không công khai.

2. Quy trình mua sách điện tử

Bước 1: Người dùng chọn mua sách

Nếu là sách điện tử:

Không cần nhập địa chỉ giao hàng.

Bước 2: Thanh toán

Người dùng thanh toán qua các phương thức hỗ trợ.

Sau khi thanh toán thành công, hệ thống tạo đơn hàng và gắn trạng thái là "đã thanh toán".

Bước 3: Nhận sách

Người dùng nhận link tải sách thông qua:

Email xác nhận giao sách.

Hoặc truy cập trang "Đơn hàng của tôi" để tải sách.

Link tải được bảo vệ, chỉ người đã mua mới truy cập được.

3. Xử lý tải sách điện tử

Link tải chỉ hoạt động nếu:

Người dùng đã đăng nhập.

Đơn hàng thuộc về người dùng đó.

Trạng thái đơn hàng là "đã thanh toán".

File PDF không được lưu ở thư mục public để tránh bị chia sẻ trái phép.

4. Đọc thử sách

Mỗi sách có thể có một bản PDF dùng để "đọc thử".

Bản đọc thử sẽ hiển thị trực tiếp trên trang chi tiết sách dưới dạng xem trước.

Đây là cách giúp người dùng trải nghiệm nội dung trước khi mua.

5. Hủy đơn và hoàn tiền

Hủy đơn:

Người dùng chỉ có thể hủy đơn nếu:

Đơn chưa thanh toán.

Hoặc đã thanh toán nhưng chưa tải sách.

Hoàn tiền:

Hoàn tiền chỉ áp dụng trong một số trường hợp đặc biệt:

File lỗi hoặc không đọc được.

Giao sai sách.

Admin sẽ xác minh và cập nhật trạng thái đơn hàng thành "đã hoàn tiền".

6. Bảo mật file sách

Tất cả file PDF đều được lưu ở nơi không công khai.

Hệ thống tạo đường dẫn tải riêng biệt cho từng đơn hàng.

Link có thể giới hạn thời gian hoặc lượt tải.