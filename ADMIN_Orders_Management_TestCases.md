# TEST CASES - QUẢN LÝ ĐƠN HÀNG BÊN ADMIN

**Dự án**: BookBee - Hệ thống bán sách online  
**Module**: Admin - Quản lý đơn hàng (OrderController)  
**Ngày tạo**: 28/07/2025  
**Người tạo**: Admin QA Team  

## 📋 THÔNG TIN CHUNG

**Controller**: `OrderController.php`  
**Routes**: `/admin/orders/*`  
**Model chính**: `Order`  
**Models liên quan**: `OrderItem`, `OrderStatus`, `PaymentStatus`, `User`, `Book`, `RefundRequest`, `Payment`  
**Service**: `OrderService`  

## 🎯 CHỨC NĂNG CHÍNH

1. **Danh sách đơn hàng** (index) - Xem, tìm kiếm, lọc đơn hàng
2. **Chi tiết đơn hàng** (show) - Xem thông tin chi tiết và các sản phẩm
3. **Cập nhật đơn hàng** (edit/update) - Cập nhật trạng thái đơn hàng
4. **Hoàn tiền đơn hàng** (showRefund/processRefund) - Xử lý hoàn tiền
5. **Quản lý yêu cầu hoàn tiền** (refundList/refundDetail/processRefundRequest) - Xử lý các yêu cầu hoàn tiền từ khách hàng

---

## 📊 BẢNG TEST CASES

| STT | Chức năng | Test Case ID | Mô tả | Dữ liệu đầu vào | Kết quả mong đợi | Độ ưu tiên | Loại test |
|-----|-----------|--------------|-------|----------------|------------------|------------|-----------|
| **DANH SÁCH ĐƠN HÀNG** | | | | | | | |
| 1 | Hiển thị danh sách | TC_ORDERS_001 | Hiển thị tất cả đơn hàng có phân trang | URL: `/admin/orders` | Hiển thị danh sách 10 đơn hàng/trang với thông tin đầy đủ | Cao | Functional |
| 2 | Tìm kiếm theo mã đơn | TC_ORDERS_002 | Tìm kiếm đơn hàng theo mã đơn hàng chính xác | search="ORD12345" | Hiển thị đơn hàng có mã "ORD12345" | Cao | Functional |
| 3 | Tìm kiếm theo mã đơn gần đúng | TC_ORDERS_003 | Tìm kiếm đơn hàng theo mã đơn hàng gần đúng | search="12345" | Hiển thị đơn hàng có mã chứa "12345" | Cao | Functional |
| 4 | Tìm kiếm theo tên khách hàng chính xác | TC_ORDERS_004 | Tìm kiếm đơn hàng theo tên người dùng chính xác | search="Nguyễn Văn A" | Hiển thị đơn hàng của "Nguyễn Văn A" | Cao | Functional |
| 5 | Tìm kiếm theo tên khách hàng gần đúng | TC_ORDERS_005 | Tìm kiếm đơn hàng theo tên người dùng gần đúng | search="Văn A" | Hiển thị đơn hàng của tất cả khách hàng có tên chứa "Văn A" | Cao | Functional |
| 6 | Tìm kiếm theo email | TC_ORDERS_006 | Tìm kiếm đơn hàng theo email khách hàng | search="example@gmail.com" | Hiển thị đơn hàng của người dùng có email tương ứng | Cao | Functional |
| 7 | Tìm kiếm không có kết quả | TC_ORDERS_007 | Tìm kiếm với từ khóa không tồn tại | search="không_tồn_tại_xyz" | Hiển thị thông báo "Không tìm thấy đơn hàng nào" | Cao | Functional |
| 8 | Phân trang danh sách | TC_ORDERS_008 | Kiểm tra phân trang hoạt động chính xác | Click sang trang 2 | Hiển thị 10 đơn hàng tiếp theo | Cao | Functional |
| 9 | Trạng thái phân trang giữ từ khóa tìm kiếm | TC_ORDERS_009 | Kiểm tra từ khóa tìm kiếm được giữ khi chuyển trang | search="Nguyễn", click sang trang 2 | URL và kết quả vẫn giữ tham số search="Nguyễn" | Trung bình | Functional |
| 10 | Giữ lại từ khóa sau tìm kiếm | TC_ORDERS_010 | Kiểm tra từ khóa được giữ lại trong form tìm kiếm | search="ORD123" | Input tìm kiếm vẫn hiển thị "ORD123" | Trung bình | Functional |
| 11 | Nút làm mới tìm kiếm | TC_ORDERS_011 | Kiểm tra nút Reset xóa các điều kiện tìm kiếm | Click nút "Làm mới" | Form tìm kiếm được reset, hiển thị tất cả đơn hàng | Trung bình | Functional |
| 12 | Lọc theo trạng thái đơn hàng | TC_ORDERS_012 | Lọc đơn hàng theo trạng thái | status="Chờ Xác Nhận" | Chỉ hiển thị đơn hàng có trạng thái "Chờ Xác Nhận" | Cao | Functional |
| 13 | Lọc theo trạng thái thanh toán | TC_ORDERS_013 | Lọc đơn hàng theo trạng thái thanh toán | payment="Đã Thanh Toán" | Chỉ hiển thị đơn hàng có trạng thái thanh toán "Đã Thanh Toán" | Cao | Functional |
| 14 | Lọc theo ngày đặt hàng | TC_ORDERS_014 | Lọc đơn hàng theo ngày tạo | date="2025-07-28" | Chỉ hiển thị đơn hàng tạo vào ngày 28/07/2025 | Cao | Functional |
| 15 | Kết hợp nhiều điều kiện lọc | TC_ORDERS_015 | Lọc đơn hàng với nhiều điều kiện cùng lúc | status="Đã Giao Hàng" & payment="Đã Thanh Toán" | Hiển thị đơn hàng thỏa mãn cả hai điều kiện | Trung bình | Functional |
| 16 | Thống kê số lượng đơn hàng | TC_ORDERS_016 | Kiểm tra hiển thị thống kê đơn hàng theo trạng thái | Truy cập trang danh sách | Hiển thị chính xác số lượng đơn hàng theo các trạng thái | Trung bình | Functional |
| 17 | Hiển thị khi không có đơn hàng | TC_ORDERS_017 | Kiểm tra hiển thị khi không có đơn hàng nào | Database không có đơn hàng | Hiển thị thông báo "Không có đơn hàng nào" | Thấp | Functional |
| **CHI TIẾT ĐƠN HÀNG** | | | | | | | |
| 18 | Xem chi tiết đơn hàng | TC_ORDERS_018 | Xem thông tin chi tiết đơn hàng | URL: `/admin/orders/{id}` | Hiển thị đầy đủ thông tin đơn hàng, khách hàng, địa chỉ, sản phẩm | Cao | Functional |
| 19 | Hiển thị danh sách sản phẩm | TC_ORDERS_019 | Kiểm tra hiển thị sản phẩm trong đơn hàng | URL: `/admin/orders/{id}` | Hiển thị đầy đủ danh sách sản phẩm với giá, số lượng, tổng tiền | Cao | Functional |
| 20 | Hiển thị thuộc tính sản phẩm | TC_ORDERS_020 | Kiểm tra hiển thị thuộc tính của sản phẩm | Sản phẩm có nhiều thuộc tính | Hiển thị đầy đủ các thuộc tính đã chọn | Cao | Functional |
| 21 | Hiển thị định dạng sách | TC_ORDERS_021 | Kiểm tra hiển thị định dạng sách (vật lý/ebook) | Đơn hàng có sách vật lý và ebook | Hiển thị chính xác định dạng của sách | Cao | Functional |
| 22 | Hiển thị thông tin khách hàng | TC_ORDERS_022 | Kiểm tra hiển thị thông tin khách hàng | URL: `/admin/orders/{id}` | Hiển thị đầy đủ tên, email, số điện thoại khách hàng | Cao | Functional |
| 23 | Hiển thị thông tin giao hàng | TC_ORDERS_023 | Kiểm tra hiển thị địa chỉ giao hàng | URL: `/admin/orders/{id}` | Hiển thị đầy đủ địa chỉ nhận hàng | Cao | Functional |
| 24 | Hiển thị thông tin thanh toán | TC_ORDERS_024 | Kiểm tra hiển thị thông tin thanh toán | URL: `/admin/orders/{id}` | Hiển thị phương thức thanh toán, trạng thái thanh toán | Cao | Functional |
| 25 | Hiển thị lịch sử thanh toán | TC_ORDERS_025 | Kiểm tra hiển thị lịch sử các giao dịch thanh toán | Đơn hàng có nhiều giao dịch | Hiển thị đầy đủ các giao dịch theo thời gian | Trung bình | Functional |
| 26 | Hiển thị thông tin voucher | TC_ORDERS_026 | Kiểm tra hiển thị thông tin voucher nếu có | Đơn hàng có áp dụng voucher | Hiển thị mã voucher, giá trị giảm | Trung bình | Functional |
| 27 | Hiển thị hóa đơn | TC_ORDERS_027 | Kiểm tra hiển thị thông tin hóa đơn | Đơn hàng có hóa đơn | Hiển thị thông tin hóa đơn, nút tải hóa đơn | Trung bình | Functional |
| 28 | Đơn hàng không tồn tại | TC_ORDERS_028 | Kiểm tra hiển thị khi xem đơn hàng không tồn tại | URL: `/admin/orders/invalid-id` | Hiển thị lỗi 404 Not Found | Cao | Exception |
| **CẬP NHẬT ĐƠN HÀNG** | | | | | | | |
| 29 | Hiển thị form cập nhật | TC_ORDERS_029 | Kiểm tra hiển thị form cập nhật đơn hàng | URL: `/admin/orders/{id}/edit` | Hiển thị form với thông tin hiện tại và các trạng thái có thể chuyển đổi | Cao | Functional |
| 30 | Cập nhật trạng thái đơn hàng hợp lệ | TC_ORDERS_030 | Cập nhật trạng thái đơn hàng sang trạng thái hợp lệ tiếp theo | Chuyển từ "Chờ Xác Nhận" sang "Đã Xác Nhận" | Trạng thái được cập nhật thành công, hiển thị thông báo thành công | Cao | Functional |
| 31 | Cập nhật sang trạng thái không liền kề | TC_ORDERS_031 | Cập nhật trạng thái đơn hàng sang trạng thái không liền kề không hợp lệ | Chuyển từ "Chờ Xác Nhận" sang "Đã Giao Hàng" | Hiển thị lỗi "Trạng thái mới không hợp lệ với trạng thái hiện tại" | Cao | Validation |
| 32 | Cập nhật trạng thái thanh toán | TC_ORDERS_032 | Cập nhật trạng thái thanh toán đơn hàng | Chuyển từ "Chưa Thanh Toán" sang "Đã Thanh Toán" | Trạng thái thanh toán được cập nhật thành công | Cao | Functional |
| 33 | Hủy đơn hàng không lý do | TC_ORDERS_033 | Hủy đơn hàng nhưng không nhập lý do hủy | Chuyển sang "Đã Hủy", cancellation_reason="" | Hiển thị lỗi "Vui lòng nhập lý do hủy hàng khi đổi trạng thái thành 'Đã Hủy'" | Cao | Validation |
| 34 | Hủy đơn hàng có lý do | TC_ORDERS_034 | Hủy đơn hàng và nhập lý do hủy | Chuyển sang "Đã Hủy", có lý do | Đơn hàng được hủy thành công, ghi nhận lý do và ngày hủy | Cao | Functional |
| 35 | Cập nhật với dữ liệu không hợp lệ | TC_ORDERS_035 | Cập nhật với trạng thái không tồn tại | order_status_id="invalid" | Hiển thị lỗi validation | Cao | Validation |
| 36 | Gửi mail thông báo | TC_ORDERS_036 | Kiểm tra việc gửi mail thông báo khi cập nhật trạng thái | Cập nhật trạng thái thành công | Mail thông báo được gửi qua queue | Trung bình | Integration |
| 37 | Ghi log khi cập nhật | TC_ORDERS_037 | Kiểm tra việc ghi log khi cập nhật trạng thái | Cập nhật trạng thái thành công | Log được ghi nhận với thông tin chính xác | Trung bình | Integration |
| 38 | Xử lý transaction | TC_ORDERS_038 | Kiểm tra xử lý transaction khi cập nhật lỗi | Gây lỗi giữa quá trình cập nhật | Transaction được rollback, không có thay đổi dữ liệu | Cao | Exception |
| **HOÀN TIỀN ĐƠN HÀNG** | | | | | | | |
| 39 | Hiển thị form hoàn tiền | TC_ORDERS_039 | Kiểm tra hiển thị form hoàn tiền | URL: `/admin/orders/{id}/refund` | Hiển thị form hoàn tiền với thông tin đơn hàng | Cao | Functional |
| 40 | Điều kiện hoàn tiền - Chưa thành công | TC_ORDERS_040 | Kiểm tra điều kiện hoàn tiền khi đơn hàng chưa thành công | Đơn hàng có trạng thái khác "Thành công" | Hiển thị lỗi "Chỉ có thể hoàn tiền cho đơn hàng đã hoàn thành thành công" | Cao | Validation |
| 41 | Điều kiện hoàn tiền - Đã hoàn tiền | TC_ORDERS_041 | Kiểm tra điều kiện hoàn tiền khi đơn đã được hoàn tiền | Đơn hàng có trạng thái thanh toán "Đã Hoàn Tiền" | Hiển thị lỗi "Đơn hàng đã được hoàn tiền..." | Cao | Validation |
| 42 | Điều kiện hoàn tiền - Chưa thanh toán | TC_ORDERS_042 | Kiểm tra điều kiện hoàn tiền khi đơn chưa thanh toán | Đơn hàng có trạng thái thanh toán khác "Đã Thanh Toán" | Hiển thị lỗi "Chỉ có thể hoàn tiền cho đơn hàng đã thanh toán" | Cao | Validation |
| 43 | Hoàn tiền qua ví điện tử | TC_ORDERS_043 | Hoàn tiền qua ví điện tử của khách hàng | refund_method="wallet", số tiền hợp lệ | Hoàn tiền thành công, cập nhật số dư ví, cập nhật trạng thái thanh toán | Cao | Functional |
| 44 | Hoàn tiền qua VNPay | TC_ORDERS_044 | Hoàn tiền qua cổng thanh toán VNPay | refund_method="vnpay", số tiền hợp lệ | Hoàn tiền thành công, cập nhật trạng thái thanh toán | Cao | Functional |
| 45 | Hoàn tiền số tiền không hợp lệ | TC_ORDERS_045 | Hoàn tiền với số tiền lớn hơn tổng đơn hàng | refund_amount > total_amount | Hiển thị lỗi "Số tiền hoàn không được vượt quá tổng tiền đơn hàng" | Cao | Validation |
| 46 | Hoàn tiền thiếu lý do | TC_ORDERS_046 | Hoàn tiền không nhập lý do | refund_reason="" | Hiển thị lỗi validation "Vui lòng nhập lý do hoàn tiền" | Cao | Validation |
| 47 | Lý do hoàn tiền quá dài | TC_ORDERS_047 | Hoàn tiền với lý do quá dài | refund_reason > 1000 ký tự | Hiển thị lỗi validation "Lý do không được vượt quá 1000 ký tự" | Trung bình | Validation |
| 48 | Ghi log hoàn tiền | TC_ORDERS_048 | Kiểm tra việc ghi log khi hoàn tiền | Hoàn tiền thành công | Log được ghi nhận với thông tin chính xác | Trung bình | Integration |
| 49 | Xử lý transaction hoàn tiền | TC_ORDERS_049 | Kiểm tra xử lý transaction khi hoàn tiền lỗi | Gây lỗi giữa quá trình hoàn tiền | Transaction được rollback, không có thay đổi dữ liệu | Cao | Exception |
| 50 | Kiểm tra trạng thái hoàn tiền | TC_ORDERS_050 | Kiểm tra API trạng thái hoàn tiền | GET `/admin/orders/{id}/refund-status` | Trả về thông tin chính xác về trạng thái, số tiền, thời gian | Trung bình | API |
| **QUẢN LÝ YÊU CẦU HOÀN TIỀN** | | | | | | | |
| 51 | Danh sách yêu cầu hoàn tiền | TC_ORDERS_051 | Kiểm tra hiển thị danh sách yêu cầu hoàn tiền | URL: `/admin/refunds` | Hiển thị danh sách yêu cầu hoàn tiền | Cao | Functional |
| 52 | Tìm kiếm yêu cầu theo mã đơn | TC_ORDERS_052 | Tìm kiếm yêu cầu hoàn tiền theo mã đơn hàng | search="ORD12345" | Hiển thị yêu cầu hoàn tiền của đơn hàng "ORD12345" | Cao | Functional |
| 53 | Lọc yêu cầu theo trạng thái | TC_ORDERS_053 | Lọc yêu cầu hoàn tiền theo trạng thái | status="pending" | Hiển thị các yêu cầu có trạng thái "pending" | Cao | Functional |
| 54 | Chi tiết yêu cầu hoàn tiền | TC_ORDERS_054 | Xem chi tiết yêu cầu hoàn tiền | URL: `/admin/refunds/{id}` | Hiển thị đầy đủ thông tin yêu cầu hoàn tiền và đơn hàng | Cao | Functional |
| 55 | Phê duyệt yêu cầu hoàn tiền | TC_ORDERS_055 | Phê duyệt yêu cầu hoàn tiền | status="completed", có ghi chú | Yêu cầu được phê duyệt, tiền được hoàn vào phương thức đã chọn | Cao | Functional |
| 56 | Từ chối yêu cầu hoàn tiền | TC_ORDERS_056 | Từ chối yêu cầu hoàn tiền | status="rejected", có ghi chú | Yêu cầu bị từ chối, không hoàn tiền | Cao | Functional |
| 57 | Phê duyệt thiếu ghi chú | TC_ORDERS_057 | Phê duyệt yêu cầu hoàn tiền không có ghi chú | status="completed", admin_note="" | Hiển thị lỗi validation "Vui lòng nhập ghi chú" | Cao | Validation |
| 58 | Từ chối thiếu ghi chú | TC_ORDERS_058 | Từ chối yêu cầu hoàn tiền không có ghi chú | status="rejected", admin_note="" | Hiển thị lỗi validation "Vui lòng nhập ghi chú" | Cao | Validation |
| 59 | Ghi chú quá dài | TC_ORDERS_059 | Phê duyệt/từ chối với ghi chú quá dài | admin_note > 1000 ký tự | Hiển thị lỗi validation "Ghi chú không được vượt quá 1000 ký tự" | Trung bình | Validation |
| 60 | Phê duyệt hoàn tiền qua ví | TC_ORDERS_060 | Phê duyệt yêu cầu hoàn tiền qua ví | refund_method="wallet" | Tiền được hoàn vào ví, trạng thái cập nhật thành "Đã Hoàn Tiền" | Cao | Functional |
| 61 | Phê duyệt hoàn tiền qua VNPay | TC_ORDERS_061 | Phê duyệt yêu cầu hoàn tiền qua VNPay | refund_method="vnpay" | Yêu cầu hoàn tiền được gửi đến VNPay, trạng thái cập nhật | Cao | Functional |
| 62 | Xử lý lỗi hoàn tiền | TC_ORDERS_062 | Kiểm tra xử lý khi hoàn tiền gặp lỗi | Gây lỗi trong quá trình hoàn tiền | Log warning được ghi nhận, quá trình vẫn tiếp tục | Trung bình | Exception |
| 63 | Cập nhật trạng thái hoàn tiền | TC_ORDERS_063 | Kiểm tra cập nhật trạng thái hoàn tiền của đơn hàng | Phê duyệt yêu cầu hoàn tiền | Trạng thái thanh toán đơn hàng được cập nhật thành "Đã Hoàn Tiền" | Cao | Functional |
| **BẢO MẬT & PHÂN QUYỀN** | | | | | | | |
| 64 | Truy cập không đăng nhập | TC_ORDERS_064 | Truy cập quản lý đơn hàng khi chưa đăng nhập | Không có session | Chuyển hướng đến trang đăng nhập | Cao | Security |
| 65 | Truy cập không phải admin | TC_ORDERS_065 | User thường truy cập quản lý đơn hàng | role="user" | Hiển thị lỗi 403 Forbidden | Cao | Security |
| 66 | CSRF Protection | TC_ORDERS_066 | Gửi form không có CSRF token | _token="" | Hiển thị lỗi 419 Page Expired | Cao | Security |
| **HIỆU SUẤT** | | | | | | | |
| 67 | Tải trang với nhiều đơn hàng | TC_ORDERS_067 | Kiểm tra hiệu suất trang danh sách với nhiều đơn hàng | Database có 1000+ đơn hàng | Trang tải < 3 giây với pagination | Trung bình | Performance |
| 68 | Eager loading relationships | TC_ORDERS_068 | Kiểm tra eager loading các relationship | Truy cập trang danh sách và chi tiết | Không xảy ra N+1 query problem | Trung bình | Performance |
| 69 | Xử lý hoàn tiền số lượng lớn | TC_ORDERS_069 | Kiểm tra hiệu suất khi xử lý nhiều yêu cầu hoàn tiền | Nhiều yêu cầu hoàn tiền cùng lúc | Hệ thống xử lý mượt mà không bị treo | Trung bình | Performance |
| **TÍCH HỢP** | | | | | | | |
| 70 | Tích hợp Queue | TC_ORDERS_070 | Kiểm tra tích hợp Queue khi gửi mail | Cập nhật trạng thái đơn hàng | Job SendOrderStatusUpdatedMail được dispatch | Cao | Integration |
| 71 | Tích hợp VNPay | TC_ORDERS_071 | Kiểm tra tích hợp cổng thanh toán VNPay | Hoàn tiền qua VNPay | API VNPay được gọi với tham số chính xác | Cao | Integration |
| 72 | Tích hợp Wallet | TC_ORDERS_072 | Kiểm tra tích hợp ví điện tử | Hoàn tiền qua ví | Số dư ví được cập nhật chính xác | Cao | Integration |
| 73 | Tích hợp Toastr | TC_ORDERS_073 | Kiểm tra hiển thị thông báo Toastr | Thực hiện các hành động thành công/lỗi | Hiển thị thông báo Toastr tương ứng | Thấp | Integration |
| 74 | Tích hợp Logging | TC_ORDERS_074 | Kiểm tra hệ thống log | Thực hiện các hành động quan trọng | Log được ghi nhận đầy đủ và chính xác | Trung bình | Integration |

---

## 🔧 THIẾT LẬP TESTING

### Môi trường test:
```bash
# Tạo database test
php artisan migrate --env=testing

# Seed dữ liệu test
php artisan db:seed --env=testing

# Chạy test
php artisan test --filter OrderControllerTest
```

### Dữ liệu test cần chuẩn bị:
- **Orders**: Ít nhất 50 đơn hàng với các trạng thái khác nhau
- **OrderStatuses**: Đầy đủ các trạng thái (Chờ Xác Nhận, Đã Xác Nhận, Đang Chuẩn Bị, Đang Giao Hàng, Đã Giao Hàng, Đã Hủy, Thành công)
- **PaymentStatuses**: Đầy đủ các trạng thái (Chưa Thanh Toán, Đã Thanh Toán, Đang Hoàn Tiền, Đã Hoàn Tiền)
- **Users**: Ít nhất 20 khách hàng
- **OrderItems**: Đơn hàng với nhiều loại sản phẩm (sách vật lý, ebook)
- **RefundRequests**: Các yêu cầu hoàn tiền với các trạng thái khác nhau

### Commands để chạy automated tests:
```bash
# Test cơ bản
php artisan test tests/Feature/Admin/OrderControllerTest.php

# Test với coverage
php artisan test --coverage tests/Feature/Admin/

# Test performance  
php artisan test tests/Performance/AdminOrderPerformanceTest.php
```

---

## ✅ KIỂM TRA VALIDATION

### Cập nhật đơn hàng:
- ✅ order_status_id (required|exists:order_statuses,id)
- ✅ payment_status_id (required|exists:payment_statuses,id)
- ✅ cancellation_reason (nullable|string|max:1000) - Required khi đổi trạng thái thành "Đã Hủy"

### Hoàn tiền đơn hàng:
- ✅ refund_method (required|in:wallet,vnpay)
- ✅ refund_amount (required|numeric|min:0) - Không được lớn hơn tổng tiền đơn hàng
- ✅ refund_reason (required|string|max:1000)

### Xử lý yêu cầu hoàn tiền:
- ✅ status (required|in:completed,rejected)
- ✅ admin_note (required|string|max:1000)

### Business logic validation:
- ✅ Kiểm tra trạng thái đơn hàng có hợp lệ để chuyển tiếp không
- ✅ Kiểm tra điều kiện hoàn tiền (đã thanh toán, chưa hoàn tiền, đã hoàn thành)
- ✅ Kiểm tra số tiền hoàn có hợp lệ không

---

**Tổng số test cases: 74**  
**Ưu tiên Cao: 45 | Trung bình: 26 | Thấp: 3**  
**Functional: 49 | Validation: 12 | Security: 3 | Performance: 3 | Integration: 5 | Exception: 3 | API: 1**
