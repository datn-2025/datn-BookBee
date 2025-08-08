# ADMIN - Quản lý liên hệ - Test Cases

## Thông tin chung
- **Module**: Quản lý liên hệ (Contact Management)
- **Controller**: App\Http\Controllers\Admin\ContactController
- **Model**: App\Models\Contact
- **Routes**: /admin/contacts/*
- **Views**: resources/views/admin/contacts/*

## Chi tiết Test Cases

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|-------|---------------------|-------------------|----------------|-----------------|----------------|------------|-----------|-------------|----------------|------------|---------|
| 1 | Xem danh sách liên hệ | TC_CONTACT_01 | Hiển thị danh sách liên hệ | Kiểm tra việc hiển thị danh sách tất cả liên hệ với phân trang | Đã đăng nhập admin, có ít nhất 1 liên hệ trong DB | B1: Truy cập `/admin/contacts` → B2: Quan sát danh sách hiển thị | Hiển thị bảng liên hệ với các cột: STT, Tên, Email, Nội dung phản hồi, Trạng thái, Ngày gửi, Hành động. Có phân trang 15 records/page |  | Cao | | | Contact có đầy đủ field: name, email, phone, address, note, status | Local | | |
| 2 | | TC_CONTACT_02 | Tìm kiếm liên hệ theo tên | Tìm kiếm liên hệ bằng tên người gửi | Có nhiều liên hệ với tên khác nhau | B1: Vào `/admin/contacts` → B2: Nhập tên vào ô "Tìm kiếm liên hệ..." → B3: Nhấn nút "Lọc" | Hiển thị chỉ những liên hệ có tên chứa từ khóa tìm kiếm, không phân biệt hoa thường |  | Cao | | | Tên test: "Nguyễn Văn A", "Trần Thị B" | Local | | |
| 3 | | TC_CONTACT_03 | Tìm kiếm liên hệ theo email | Tìm kiếm liên hệ bằng địa chỉ email | Có nhiều liên hệ với email khác nhau | B1: Vào `/admin/contacts` → B2: Nhập email vào ô tìm kiếm → B3: Nhấn "Lọc" | Hiển thị chỉ những liên hệ có email chứa từ khóa tìm kiếm |  | Cao | | | Email test: "test@example.com", "admin@bookbee.com" | Local | | |
| 4 | | TC_CONTACT_04 | Lọc theo trạng thái | Lọc liên hệ theo trạng thái cụ thể | Có liên hệ với các trạng thái khác nhau | B1: Vào `/admin/contacts` → B2: Chọn trạng thái từ dropdown "Tất cả trạng thái" → B3: Nhấn "Lọc" | Hiển thị chỉ liên hệ có trạng thái được chọn (new/processing/replied/closed) |  | Cao | | | Các trạng thái: new, processing, replied, closed | Local | | |
| 5 | | TC_CONTACT_05 | Đặt lại bộ lọc | Reset tất cả bộ lọc về trạng thái ban đầu | Đã áp dụng bộ lọc hoặc tìm kiếm | B1: Sau khi lọc/tìm kiếm → B2: Nhấn nút "Đặt lại" | Trở về trang danh sách ban đầu, hiển thị tất cả liên hệ, các ô lọc được reset |  | Trung bình | | | | Local | | |
| 6 | Xem chi tiết liên hệ | TC_CONTACT_06 | Xem thông tin chi tiết | Kiểm tra việc hiển thị đầy đủ thông tin chi tiết liên hệ | Có ít nhất 1 liên hệ | B1: Vào danh sách liên hệ → B2: Nhấn nút "Xem" (icon mắt) → B3: Quan sát trang chi tiết | Hiển thị đầy đủ: Tên, Email, Số điện thoại, Địa chỉ, Nội dung, Trạng thái, Ngày tạo, Phản hồi admin (nếu có) |  | Cao | | | Contact đầy đủ thông tin | Local | | |
| 7 | Cập nhật trạng thái | TC_CONTACT_07 | Thay đổi trạng thái từ "Mới" sang "Đang xử lý" | Cập nhật trạng thái liên hệ | Contact có trạng thái "new" | B1: Tại danh sách → B2: Nhấn nút "Sửa" (icon bút chì) → B3: Chọn trạng thái "Đang xử lý" → B4: Nhấn "Lưu thay đổi" | Trạng thái chuyển sang "processing", hiển thị thông báo "Cập nhật trạng thái thành công!" |  | Cao | | | Contact status = "new" | Local | | |
| 8 | | TC_CONTACT_08 | Thay đổi trạng thái từ "Đang xử lý" sang "Đã đóng" | Đóng liên hệ đã xử lý xong | Contact có trạng thái "processing" | B1: Nhấn "Sửa" → B2: Chọn "Đã đóng" → B3: Thêm ghi chú → B4: Lưu | Trạng thái chuyển sang "closed", ghi chú được lưu, thông báo thành công |  | Cao | | | Contact status = "processing" | Local | | |
| 9 | | TC_CONTACT_09 | Cập nhật ghi chú admin | Thêm/sửa ghi chú nội bộ cho liên hệ | Có contact bất kỳ | B1: Nhấn "Sửa" → B2: Nhập/sửa nội dung trong "Ghi chú" → B3: Lưu | Ghi chú được cập nhật và lưu vào DB, hiển thị thông báo thành công |  | Trung bình | | | Ghi chú test: "Đã liên hệ khách hàng qua điện thoại" | Local | | |
| 10 | Gửi phản hồi email | TC_CONTACT_10 | Gửi email phản hồi khách hàng | Gửi email trả lời đến khách hàng | SMTP đã cấu hình, contact có email hợp lệ | B1: Nhấn nút "Gửi phản hồi" (icon thư) → B2: Nhập nội dung phản hồi → B3: Nhấn "Gửi" | Email được gửi đến khách hàng, trạng thái tự động chuyển sang "replied", admin_reply được lưu |  | Cao | | | Email SMTP config, content: "Cảm ơn bạn đã liên hệ..." | Local | | |
| 11 | | TC_CONTACT_11 | Validation form gửi phản hồi | Kiểm tra validate khi gửi phản hồi trống | Contact bất kỳ | B1: Nhấn "Gửi phản hồi" → B2: Để trống nội dung → B3: Nhấn "Gửi" | Hiển thị lỗi validation "The message field is required", không gửi email |  | Trung bình | | | | Local | | |
| 12 | | TC_CONTACT_12 | Gửi phản hồi khi SMTP lỗi | Xử lý lỗi khi không gửi được email | SMTP config sai hoặc không khả dụng | B1: Nhấn "Gửi phản hồi" → B2: Nhập nội dung → B3: Gửi | Hiển thị thông báo lỗi, trạng thái contact không thay đổi |  | Trung bình | | | Cấu hình SMTP sai | Local | | |
| 13 | Xóa liên hệ | TC_CONTACT_13 | Xóa liên hệ thành công | Xóa liên hệ khỏi hệ thống | Có ít nhất 1 contact | B1: Tại danh sách → B2: Nhấn nút "Xóa" (icon thùng rác) → B3: Confirm "OK" trong dialog | Contact bị xóa khỏi DB, hiển thị thông báo "Đã xóa liên hệ thành công!" |  | Cao | | | Contact ID hợp lệ | Local | | |
| 14 | | TC_CONTACT_14 | Hủy xóa liên hệ | Hủy thao tác xóa | Có contact muốn xóa | B1: Nhấn "Xóa" → B2: Nhấn "Cancel" trong confirm dialog | Contact không bị xóa, ở lại trang danh sách |  | Trung bình | | | | Local | | |
| 15 | | TC_CONTACT_15 | Xóa contact không tồn tại | Xử lý khi contact đã bị xóa bởi người khác | Contact ID không tồn tại | B1: Truy cập URL delete với ID không hợp lệ | Hiển thị lỗi 404 hoặc thông báo "Contact not found" |  | Thấp | | | ID không tồn tại: 999999 | Local | | |
| 16 | Phân trang | TC_CONTACT_16 | Kiểm tra phân trang | Kiểm tra hoạt động của phân trang | Có >15 contacts trong DB | B1: Vào danh sách contacts → B2: Quan sát phân trang → B3: Nhấn trang 2, 3... | Mỗi trang hiển thị tối đa 15 records, navigation phân trang hoạt động đúng |  | Trung bình | | | Tạo 50+ contact records | Local | | |
| 17 | Sắp xếp | TC_CONTACT_17 | Sắp xếp theo ngày tạo | Kiểm tra thứ tự hiển thị | Có nhiều contacts với thời gian tạo khác nhau | B1: Vào danh sách contacts → B2: Quan sát thứ tự | Contacts được sắp xếp theo created_at DESC (mới nhất lên đầu) |  | Trung bình | | | Contacts có thời gian tạo khác nhau | Local | | |
| 18 | Responsive UI | TC_CONTACT_18 | Hiển thị trên mobile | Kiểm tra giao diện responsive | Truy cập từ thiết bị mobile | B1: Mở admin panel trên mobile → B2: Vào quản lý contacts → B3: Thực hiện các thao tác | Giao diện hiển thị đúng, các nút bấm hoạt động bình thường trên mobile |  | Thấp | | | Mobile device/responsive mode | Local | | |
| 19 | Security | TC_CONTACT_19 | Truy cập không có quyền | Kiểm tra phân quyền admin | User không phải admin | B1: Logout admin → B2: Login user thường → B3: Truy cập `/admin/contacts` | Redirect về trang login hoặc hiển thị lỗi 403 Forbidden |  | Cao | | | User role = "user" | Local | | |
| 20 | | TC_CONTACT_20 | CSRF Protection | Kiểm tra bảo mật CSRF token | Admin đã login | B1: Gửi request cập nhật/xóa không có CSRF token | Request bị từ chối với lỗi 419 CSRF Token Mismatch |  | Cao | | | Request without _token | Local | | |
| 21 | Performance | TC_CONTACT_21 | Load trang với nhiều dữ liệu | Kiểm tra hiệu suất khi có nhiều contact | DB có >1000 contacts | B1: Vào danh sách contacts → B2: Đo thời gian load | Trang load trong <3 giây, phân trang hoạt động mượt mà |  | Trung bình | | | 1000+ contact records | Local | | |
| 22 | Integration | TC_CONTACT_22 | Tích hợp với email system | Kiểm tra email template và delivery | Email config đầy đủ | B1: Gửi phản hồi → B2: Kiểm tra email nhận được | Email có format đúng với template `emails.contact-reply`, nội dung hiển thị chính xác |  | Cao | | | SMTP hoạt động, email template exists | Local | | |

## Lưu ý về Test Environment

### Database Schema cần thiết:
```sql
contacts table:
- id (uuid, primary key)
- name (varchar)
- email (varchar) 
- phone (varchar, nullable)
- address (text, nullable)
- note (text) -- message từ customer
- status (enum: new, processing, replied, closed)
- admin_reply (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Test Data Setup:
```php
// Tạo contacts với các trạng thái khác nhau
Contact::factory()->create(['status' => 'new', 'name' => 'Nguyễn Văn A']);
Contact::factory()->create(['status' => 'processing', 'name' => 'Trần Thị B']);
Contact::factory()->create(['status' => 'replied', 'name' => 'Lê Văn C']);
Contact::factory()->create(['status' => 'closed', 'name' => 'Phạm Thị D']);
```

### Environment Config:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## Automated Test Commands:
```bash
# Chạy contact tests
php artisan test --filter ContactTest

# Seed test data
php artisan db:seed --class=ContactSeeder

# Clear cache
php artisan cache:clear
php artisan config:clear
```
