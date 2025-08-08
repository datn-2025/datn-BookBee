# Test Cases - Quản Lý Ví Điện Tử (Admin)

## Thông tin chung
- Module: Quản lý ví điện tử
- Controller: WalletController
- Tester: [Tên người test]
- Ngày tạo: 29/07/2025

---

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|

## 1. HIỂN THỊ DANH SÁCH GIAO DỊCH VÍ

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 1 | Hiển thị danh sách | WL_LIST_001 | Hiển thị danh sách giao dịch ví | Kiểm tra hiển thị danh sách tất cả giao dịch ví | Admin đã đăng nhập, có giao dịch trong DB | 1. Đăng nhập admin<br>2. Truy cập /admin/wallets | Hiển thị danh sách giao dịch với đầy đủ thông tin: user, loại giao dịch, số tiền, trạng thái, thời gian | | High | | | Có ít nhất 5 giao dịch khác nhau | Development | | |
| 2 | Hiển thị danh sách | WL_LIST_002 | Không có giao dịch | Kiểm tra hiển thị khi chưa có giao dịch nào | Admin đã đăng nhập, DB trống giao dịch | 1. Đăng nhập admin<br>2. Truy cập /admin/wallets | Hiển thị thông báo "Không có giao dịch nào" hoặc bảng trống | | Medium | | | DB không có giao dịch | Development | | |
| 3 | Hiển thị danh sách | WL_LIST_003 | Phân trang giao dịch | Kiểm tra chức năng phân trang | Admin đã đăng nhập, có >10 giao dịch | 1. Đăng nhập admin<br>2. Truy cập /admin/wallets<br>3. Kiểm tra nút phân trang | Hiển thị 10 giao dịch/trang, có nút chuyển trang, số trang hiện tại | | High | | | Có >15 giao dịch | Development | | |
| 4 | Hiển thị danh sách | WL_LIST_004 | Hiển thị thống kê tổng quan | Kiểm tra hiển thị số liệu thống kê | Admin đã đăng nhập, có giao dịch | 1. Đăng nhập admin<br>2. Truy cập /admin/wallets<br>3. Kiểm tra phần thống kê | Hiển thị: Tổng ví, Tổng giao dịch, Tổng nạp tiền, Tổng rút tiền | | High | | | Dữ liệu thống kê đa dạng | Development | | |
| 5 | Hiển thị danh sách | WL_LIST_005 | Tính toán số dư trước/sau giao dịch | Kiểm tra tính toán số dư chính xác | Admin đã đăng nhập, có giao dịch | 1. Đăng nhập admin<br>2. Truy cập /admin/wallets<br>3. Kiểm tra cột số dư | Số dư trước và sau giao dịch được tính chính xác theo logic business | | High | | | Giao dịch có số tiền khác nhau | Development | | |

## 2. TÌM KIẾM VÀ LỌC GIAO DỊCH

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 6 | Tìm kiếm | WL_SEARCH_001 | Tìm kiếm theo tên chính xác | Tìm kiếm user theo tên chính xác | Admin đã đăng nhập, có user "Nguyễn Văn A" | 1. Nhập "Nguyễn Văn A" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị giao dịch của user "Nguyễn Văn A" | | High | | | User: "Nguyễn Văn A" | Development | | |
| 7 | Tìm kiếm | WL_SEARCH_002 | Tìm kiếm theo tên gần đúng | Tìm kiếm user theo tên một phần | Admin đã đăng nhập, có user "Nguyễn Văn A" | 1. Nhập "Nguyễn" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị tất cả giao dịch của user có tên chứa "Nguyễn" | | High | | | User có tên chứa "Nguyễn" | Development | | |
| 8 | Tìm kiếm | WL_SEARCH_003 | Tìm kiếm theo email chính xác | Tìm kiếm user theo email | Admin đã đăng nhập, có user với email cụ thể | 1. Nhập email vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị giao dịch của user có email đó | | High | | | Email: "test@example.com" | Development | | |
| 9 | Tìm kiếm | WL_SEARCH_004 | Tìm kiếm user không tồn tại | Tìm kiếm user không có trong hệ thống | Admin đã đăng nhập | 1. Nhập "UserKhongTonTai" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị thông báo "Không tìm thấy kết quả" hoặc danh sách trống | | Medium | | | Tên không tồn tại | Development | | |
| 10 | Tìm kiếm | WL_SEARCH_005 | Giữ từ khóa tìm kiếm khi phân trang | Kiểm tra từ khóa được giữ khi chuyển trang | Admin đã đăng nhập, tìm kiếm có >10 kết quả | 1. Tìm kiếm "Nguyễn"<br>2. Chuyển sang trang 2<br>3. Kiểm tra ô tìm kiếm | Từ khóa "Nguyễn" vẫn được giữ trong ô tìm kiếm | | Medium | | | >10 user có tên chứa "Nguyễn" | Development | | |
| 11 | Tìm kiếm | WL_SEARCH_006 | Nút làm mới tìm kiếm | Kiểm tra nút reset tìm kiếm | Admin đã thực hiện tìm kiếm | 1. Tìm kiếm "Nguyễn"<br>2. Click nút "Làm mới"/"Reset" | Ô tìm kiếm được xóa, hiển thị tất cả giao dịch | | Low | | | Có từ khóa tìm kiếm | Development | | |

## 3. LỌC THEO LOẠI GIAO DỊCH

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 12 | Lọc loại | WL_FILTER_001 | Lọc giao dịch nạp tiền | Lọc chỉ hiển thị giao dịch nạp tiền | Admin đã đăng nhập, có giao dịch "Nap" | 1. Chọn loại giao dịch "Nạp tiền"<br>2. Click lọc | Chỉ hiển thị giao dịch có type = "Nap" | | High | | | Có giao dịch type "Nap" | Development | | |
| 13 | Lọc loại | WL_FILTER_002 | Lọc giao dịch rút tiền | Lọc chỉ hiển thị giao dịch rút tiền | Admin đã đăng nhập, có giao dịch "Rut" | 1. Chọn loại giao dịch "Rút tiền"<br>2. Click lọc | Chỉ hiển thị giao dịch có type = "Rut" | | High | | | Có giao dịch type "Rut" | Development | | |
| 14 | Lọc loại | WL_FILTER_003 | Lọc giao dịch hoàn tiền | Lọc chỉ hiển thị giao dịch hoàn tiền | Admin đã đăng nhập, có giao dịch "HOANTIEN" | 1. Chọn loại giao dịch "Hoàn tiền"<br>2. Click lọc | Chỉ hiển thị giao dịch có type = "HOANTIEN" | | High | | | Có giao dịch type "HOANTIEN" | Development | | |
| 15 | Lọc loại | WL_FILTER_004 | Lọc giao dịch thanh toán | Lọc chỉ hiển thị giao dịch thanh toán | Admin đã đăng nhập, có giao dịch "payment" | 1. Chọn loại giao dịch "Thanh toán"<br>2. Click lọc | Chỉ hiển thị giao dịch có type = "payment" | | High | | | Có giao dịch type "payment" | Development | | |
| 16 | Lọc loại | WL_FILTER_005 | Kết hợp lọc loại và tìm kiếm | Lọc loại giao dịch và tìm kiếm user | Admin đã đăng nhập | 1. Nhập tên user<br>2. Chọn loại giao dịch<br>3. Click lọc | Hiển thị giao dịch của user có loại được chọn | | Medium | | | User có nhiều loại giao dịch | Development | | |

## 4. LỌC THEO TRẠNG THÁI

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 17 | Lọc trạng thái | WL_STATUS_001 | Lọc giao dịch thành công | Lọc chỉ hiển thị giao dịch đã duyệt | Admin đã đăng nhập, có giao dịch "success" | 1. Chọn trạng thái "Thành công"<br>2. Click lọc | Chỉ hiển thị giao dịch có status = "success" | | High | | | Có giao dịch status "success" | Development | | |
| 18 | Lọc trạng thái | WL_STATUS_002 | Lọc giao dịch chờ duyệt | Lọc chỉ hiển thị giao dịch chờ duyệt | Admin đã đăng nhập, có giao dịch "pending" | 1. Chọn trạng thái "Chờ duyệt"<br>2. Click lọc | Chỉ hiển thị giao dịch có status = "pending" | | High | | | Có giao dịch status "pending" | Development | | |
| 19 | Lọc trạng thái | WL_STATUS_003 | Lọc giao dịch thất bại | Lọc chỉ hiển thị giao dịch bị từ chối | Admin đã đăng nhập, có giao dịch "failed" | 1. Chọn trạng thái "Thất bại"<br>2. Click lọc | Chỉ hiển thị giao dịch có status = "failed" | | High | | | Có giao dịch status "failed" | Development | | |
| 20 | Lọc trạng thái | WL_STATUS_004 | Kết hợp lọc trạng thái và loại | Lọc cả trạng thái và loại giao dịch | Admin đã đăng nhập | 1. Chọn loại "Nạp tiền"<br>2. Chọn trạng thái "Chờ duyệt"<br>3. Click lọc | Hiển thị giao dịch nạp tiền đang chờ duyệt | | Medium | | | Có giao dịch nạp tiền pending | Development | | |

## 5. LỌC THEO THỜI GIAN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 21 | Lọc thời gian | WL_DATE_001 | Lọc theo khoảng thời gian hợp lệ | Lọc giao dịch trong khoảng thời gian | Admin đã đăng nhập, có giao dịch trong khoảng thời gian | 1. Nhập "01/07/2025 đến 31/07/2025"<br>2. Click lọc | Hiển thị giao dịch từ 01/07 đến 31/07/2025 | | High | | | Giao dịch trong tháng 7/2025 | Development | | |
| 22 | Lọc thời gian | WL_DATE_002 | Lọc ngày bắt đầu = ngày kết thúc | Lọc giao dịch trong 1 ngày | Admin đã đăng nhập | 1. Nhập "29/07/2025 đến 29/07/2025"<br>2. Click lọc | Hiển thị giao dịch trong ngày 29/07/2025 | | Medium | | | Giao dịch ngày 29/07/2025 | Development | | |
| 23 | Lọc thời gian | WL_DATE_003 | Sai định dạng ngày tháng | Nhập sai format ngày tháng | Admin đã đăng nhập | 1. Nhập "2025/07/01 đến 2025/07/31"<br>2. Click lọc | Thông báo lỗi định dạng hoặc không lọc | | Medium | | | Format sai | Development | | |
| 24 | Lọc thời gian | WL_DATE_004 | Ngày bắt đầu > ngày kết thúc | Nhập ngày bắt đầu sau ngày kết thúc | Admin đã đăng nhập | 1. Nhập "31/07/2025 đến 01/07/2025"<br>2. Click lọc | Thông báo lỗi hoặc không có kết quả | | Medium | | | Ngày logic sai | Development | | |
| 25 | Lọc thời gian | WL_DATE_005 | Khoảng thời gian không có giao dịch | Lọc thời gian không có dữ liệu | Admin đã đăng nhập | 1. Nhập khoảng thời gian tương lai<br>2. Click lọc | Hiển thị "Không có giao dịch" | | Low | | | Thời gian tương lai | Development | | |

## 6. QUẢN LÝ GIAO DỊCH NẠP TIỀN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 26 | Nạp tiền | WL_DEPOSIT_001 | Hiển thị danh sách nạp tiền | Xem lịch sử nạp tiền | Admin đã đăng nhập | 1. Truy cập /admin/wallets/deposit | Hiển thị danh sách giao dịch nạp tiền (type="Nap") | | High | | | Có giao dịch nạp tiền | Development | | |
| 27 | Nạp tiền | WL_DEPOSIT_002 | Lọc nạp tiền theo user | Lọc giao dịch nạp tiền theo user cụ thể | Admin đã đăng nhập, có nhiều user nạp tiền | 1. Chọn user cụ thể<br>2. Click lọc | Hiển thị giao dịch nạp tiền của user đã chọn | | Medium | | | Nhiều user có nạp tiền | Development | | |
| 28 | Nạp tiền | WL_DEPOSIT_003 | Lọc nạp tiền theo trạng thái | Lọc theo trạng thái giao dịch nạp tiền | Admin đã đăng nhập | 1. Chọn trạng thái "pending"<br>2. Click lọc | Hiển thị giao dịch nạp tiền chờ duyệt | | High | | | Có giao dịch nạp tiền pending | Development | | |
| 29 | Nạp tiền | WL_DEPOSIT_004 | Lọc nạp tiền theo thời gian | Lọc giao dịch nạp tiền theo khoảng thời gian | Admin đã đăng nhập | 1. Nhập khoảng thời gian<br>2. Click lọc | Hiển thị giao dịch nạp tiền trong khoảng thời gian | | Medium | | | Giao dịch nạp tiền theo thời gian | Development | | |
| 30 | Nạp tiền | WL_DEPOSIT_005 | Phân trang danh sách nạp tiền | Kiểm tra phân trang | Admin đã đăng nhập, có >10 giao dịch nạp tiền | 1. Truy cập trang nạp tiền<br>2. Kiểm tra phân trang | Hiển thị 10 giao dịch/trang, có điều hướng trang | | Medium | | | >10 giao dịch nạp tiền | Development | | |

## 7. QUẢN LÝ GIAO DỊCH RÚT TIỀN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 31 | Rút tiền | WL_WITHDRAW_001 | Hiển thị danh sách rút tiền | Xem lịch sử rút tiền | Admin đã đăng nhập | 1. Truy cập /admin/wallets/withdraw | Hiển thị danh sách giao dịch rút tiền (type="Rut") | | High | | | Có giao dịch rút tiền | Development | | |
| 32 | Rút tiền | WL_WITHDRAW_002 | Tìm kiếm rút tiền theo user | Tìm kiếm giao dịch rút tiền theo tên/email | Admin đã đăng nhập | 1. Nhập tên user<br>2. Click tìm kiếm | Hiển thị giao dịch rút tiền của user | | Medium | | | User có rút tiền | Development | | |
| 33 | Rút tiền | WL_WITHDRAW_003 | Lọc rút tiền theo trạng thái | Lọc theo trạng thái giao dịch rút tiền | Admin đã đăng nhập | 1. Chọn trạng thái<br>2. Click lọc | Hiển thị giao dịch rút tiền theo trạng thái | | High | | | Giao dịch rút tiền đa trạng thái | Development | | |
| 34 | Rút tiền | WL_WITHDRAW_004 | Lọc rút tiền theo thời gian | Lọc giao dịch rút tiền theo khoảng thời gian | Admin đã đăng nhập | 1. Nhập khoảng thời gian<br>2. Click lọc | Hiển thị giao dịch rút tiền trong khoảng thời gian | | Medium | | | Giao dịch rút tiền theo thời gian | Development | | |
| 35 | Rút tiền | WL_WITHDRAW_005 | Giữ filter khi phân trang rút tiền | Kiểm tra filter được giữ khi chuyển trang | Admin đã lọc, có >10 kết quả | 1. Lọc theo điều kiện<br>2. Chuyển trang | Filter được giữ nguyên khi chuyển trang | | Medium | | | >10 giao dịch rút tiền | Development | | |

## 8. DUYỆT GIAO DỊCH

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 36 | Duyệt GD | WL_APPROVE_001 | Duyệt giao dịch nạp tiền thành công | Duyệt giao dịch nạp tiền pending | Admin đã đăng nhập, có GD nạp tiền pending | 1. Tìm GD nạp tiền pending<br>2. Click "Duyệt" | Status = "success", số dư ví tăng, thông báo thành công | | Critical | | | GD nạp tiền 100k pending | Development | | |
| 37 | Duyệt GD | WL_APPROVE_002 | Duyệt giao dịch rút tiền thành công | Duyệt giao dịch rút tiền pending | Admin đã đăng nhập, có GD rút tiền pending | 1. Tìm GD rút tiền pending<br>2. Click "Duyệt" | Status = "success", wallet_lock giảm, gửi email hóa đơn | | Critical | | | GD rút tiền 50k pending | Development | | |
| 38 | Duyệt GD | WL_APPROVE_003 | Duyệt giao dịch đã được duyệt | Thử duyệt giao dịch đã success | Admin đã đăng nhập, có GD đã success | 1. Tìm GD đã success<br>2. Click "Duyệt" | Thông báo lỗi "Chỉ có thể duyệt giao dịch đang chờ duyệt" | | High | | | GD đã success | Development | | |
| 39 | Duyệt GD | WL_APPROVE_004 | Duyệt giao dịch thất bại | Thử duyệt giao dịch đã failed | Admin đã đăng nhập, có GD failed | 1. Tìm GD failed<br>2. Click "Duyệt" | Thông báo lỗi "Chỉ có thể duyệt giao dịch đang chờ duyệt" | | High | | | GD failed | Development | | |
| 40 | Duyệt GD | WL_APPROVE_005 | Duyệt giao dịch không tồn tại | Duyệt giao dịch với ID không hợp lệ | Admin đã đăng nhập | 1. Truy cập URL /admin/wallets/approve/99999 | Lỗi 404 hoặc thông báo "Giao dịch không tồn tại" | | Medium | | | ID không tồn tại: 99999 | Development | | |

## 9. TỪ CHỐI GIAO DỊCH

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 41 | Từ chối GD | WL_REJECT_001 | Từ chối giao dịch nạp tiền | Từ chối giao dịch nạp tiền pending | Admin đã đăng nhập, có GD nạp tiền pending | 1. Tìm GD nạp tiền pending<br>2. Click "Từ chối" | Status = "failed", số dư ví không thay đổi | | Critical | | | GD nạp tiền pending | Development | | |
| 42 | Từ chối GD | WL_REJECT_002 | Từ chối giao dịch rút tiền | Từ chối giao dịch rút tiền pending | Admin đã đăng nhập, có GD rút tiền pending | 1. Tìm GD rút tiền pending<br>2. Click "Từ chối" | Status = "failed", wallet_lock giảm, số dư ví tăng lại | | Critical | | | GD rút tiền pending | Development | | |
| 43 | Từ chối GD | WL_REJECT_003 | Từ chối giao dịch đã được duyệt | Thử từ chối giao dịch đã success | Admin đã đăng nhập, có GD success | 1. Tìm GD success<br>2. Click "Từ chối" | Thông báo lỗi "Chỉ có thể từ chối giao dịch đang chờ duyệt" | | High | | | GD success | Development | | |
| 44 | Từ chối GD | WL_REJECT_004 | Từ chối giao dịch đã thất bại | Thử từ chối giao dịch đã failed | Admin đã đăng nhập, có GD failed | 1. Tìm GD failed<br>2. Click "Từ chối" | Thông báo lỗi "Chỉ có thể từ chối giao dịch đang chờ duyệt" | | High | | | GD failed | Development | | |
| 45 | Từ chối GD | WL_REJECT_005 | Từ chối giao dịch không tồn tại | Từ chối giao dịch với ID không hợp lệ | Admin đã đăng nhập | 1. Truy cập URL /admin/wallets/reject/99999 | Lỗi 404 hoặc thông báo "Giao dịch không tồn tại" | | Medium | | | ID không tồn tại: 99999 | Development | | |

## 10. KIỂM TRA TÍNH TOÁN SỐ DƯ

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 46 | Tính toán | WL_CALC_001 | Tính số dư sau nạp tiền | Kiểm tra tính toán số dư khi nạp tiền | User có số dư ban đầu 100k | 1. Duyệt GD nạp 50k<br>2. Kiểm tra số dư hiển thị | Số dư trước: 100k, Số dư sau: 150k | | Critical | | | Số dư ban đầu: 100k, Nạp: 50k | Development | | |
| 47 | Tính toán | WL_CALC_002 | Tính số dư sau rút tiền | Kiểm tra tính toán số dư khi rút tiền | User có số dư 100k, rút 30k | 1. Duyệt GD rút 30k<br>2. Kiểm tra số dư hiển thị | Số dư trước: 100k, Số dư sau: 100k (đã trừ khi tạo yêu cầu) | | Critical | | | Số dư: 100k, Rút: 30k | Development | | |
| 48 | Tính toán | WL_CALC_003 | Tính số dư sau hoàn tiền | Kiểm tra tính toán số dư khi hoàn tiền | User có lịch sử mua hàng | 1. Có GD hoàn tiền 20k<br>2. Kiểm tra số dư hiển thị | Số dư tăng thêm 20k sau khi hoàn tiền | | High | | | Hoàn tiền: 20k | Development | | |
| 49 | Tính toán | WL_CALC_004 | Tính số dư sau thanh toán | Kiểm tra tính toán số dư khi thanh toán | User có số dư 100k, thanh toán 25k | 1. Có GD thanh toán 25k<br>2. Kiểm tra số dư hiển thị | Số dư giảm 25k sau thanh toán | | High | | | Số dư: 100k, Thanh toán: 25k | Development | | |
| 50 | Tính toán | WL_CALC_005 | Tính số dư với nhiều giao dịch | Kiểm tra tính toán với chuỗi giao dịch phức tạp | User có nhiều GD liên tiếp | 1. Có GD: Nạp 100k -> Thanh toán 30k -> Hoàn tiền 10k<br>2. Kiểm tra từng bước | Số dư được tính chính xác qua từng giao dịch | | Critical | | | Chuỗi GD phức tạp | Development | | |

## 11. KIỂM TRA BẢO MẬT VÀ QUYỀN HẠN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 51 | Bảo mật | WL_SECURITY_001 | Truy cập không có quyền admin | User thường truy cập trang admin ví | User thường đã đăng nhập | 1. Đăng nhập user thường<br>2. Truy cập /admin/wallets | Chuyển hướng đến trang login hoặc thông báo "Không có quyền" | | Critical | | | User thường: user@test.com | Development | | |
| 52 | Bảo mật | WL_SECURITY_002 | Truy cập khi chưa đăng nhập | Truy cập trang admin ví khi chưa login | Chưa đăng nhập | 1. Không đăng nhập<br>2. Truy cập /admin/wallets | Chuyển hướng đến trang đăng nhập | | Critical | | | Chưa đăng nhập | Development | | |
| 53 | Bảo mật | WL_SECURITY_003 | Duyệt GD bằng POST trực tiếp | Gửi POST request trực tiếp đến endpoint | Admin đã đăng nhập | 1. Gửi POST đến /admin/wallets/approve/{id}<br>2. Không qua form | Yêu cầu có CSRF token hợp lệ | | High | | | CSRF protection | Development | | |
| 54 | Bảo mật | WL_SECURITY_004 | Thao tác với session hết hạn | Thực hiện thao tác khi session hết hạn | Admin đã đăng nhập, session timeout | 1. Đăng nhập admin<br>2. Chờ session hết hạn<br>3. Thực hiện duyệt GD | Chuyển hướng đến trang đăng nhập | | Medium | | | Session timeout | Development | | |
| 55 | Bảo mật | WL_SECURITY_005 | SQL Injection trong tìm kiếm | Thử SQL injection qua ô tìm kiếm | Admin đã đăng nhập | 1. Nhập "'; DROP TABLE users; --" vào ô tìm kiếm<br>2. Click tìm kiếm | Không thực hiện câu lệnh SQL, tìm kiếm bình thường | | High | | | SQL injection payload | Development | | |

## 12. KIỂM TRA HIỆU SUẤT VÀ GIỚI HẠN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 56 | Hiệu suất | WL_PERF_001 | Tải trang với nhiều giao dịch | Kiểm tra thời gian tải trang với DB lớn | DB có >1000 giao dịch | 1. Truy cập /admin/wallets<br>2. Đo thời gian tải | Trang tải trong <3 giây | | Medium | | | >1000 giao dịch | Development | | |
| 57 | Hiệu suất | WL_PERF_002 | Tìm kiếm với nhiều kết quả | Tìm kiếm trả về nhiều kết quả | Có từ khóa trả về >100 kết quả | 1. Tìm kiếm từ khóa phổ biến<br>2. Đo thời gian response | Kết quả trả về trong <2 giây | | Medium | | | Từ khóa phổ biến | Development | | |
| 58 | Hiệu suất | WL_PERF_003 | Duyệt nhiều giao dịch liên tiếp | Duyệt liên tiếp nhiều giao dịch | Có >5 GD pending | 1. Duyệt 5 GD liên tiếp<br>2. Kiểm tra thời gian xử lý | Mỗi lần duyệt <1 giây | | Low | | | 5 GD pending | Development | | |
| 59 | Hiệu suất | WL_PERF_004 | Filter phức tạp | Áp dụng nhiều filter cùng lúc | Admin đã đăng nhập | 1. Áp dụng tất cả filter cùng lúc<br>2. Đo thời gian response | Kết quả trả về trong <3 giây | | Low | | | Filter đầy đủ | Development | | |
| 60 | Hiệu suất | WL_PERF_005 | Phân trang trang cuối | Truy cập trang cuối của phân trang | DB có >100 giao dịch | 1. Truy cập trang cuối phân trang<br>2. Đo thời gian tải | Trang tải trong <2 giây | | Low | | | >100 giao dịch | Development | | |

## 13. KIỂM TRA GIAO DIỆN VÀ TRẢI NGHIỆM NGƯỜI DÙNG

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 61 | UI/UX | WL_UI_001 | Responsive trên mobile | Kiểm tra giao diện trên thiết bị di động | Admin truy cập bằng mobile | 1. Truy cập bằng mobile<br>2. Kiểm tra layout | Giao diện hiển thị tốt, không bị vỡ layout | | Medium | | | Mobile device | Development | | |
| 62 | UI/UX | WL_UI_002 | Responsive trên tablet | Kiểm tra giao diện trên tablet | Admin truy cập bằng tablet | 1. Truy cập bằng tablet<br>2. Kiểm tra layout | Giao diện điều chỉnh phù hợp với màn hình tablet | | Medium | | | Tablet device | Development | | |
| 63 | UI/UX | WL_UI_003 | Hiển thị số tiền đúng format | Kiểm tra format hiển thị số tiền | Có giao dịch với số tiền lớn | 1. Xem giao dịch có số tiền 1,234,567 VND<br>2. Kiểm tra hiển thị | Số tiền hiển thị đúng format: 1,234,567 VND | | Medium | | | Số tiền lớn | Development | | |
| 64 | UI/UX | WL_UI_004 | Màu sắc trạng thái giao dịch | Kiểm tra màu sắc cho các trạng thái | Có GD đa trạng thái | 1. Xem danh sách giao dịch<br>2. Kiểm tra màu sắc | Success: xanh, Pending: vàng, Failed: đỏ | | Low | | | GD đa trạng thái | Development | | |
| 65 | UI/UX | WL_UI_005 | Loading indicator | Kiểm tra hiển thị loading khi xử lý | Admin thực hiện thao tác | 1. Click duyệt giao dịch<br>2. Quan sát màn hình | Hiển thị loading indicator trong quá trình xử lý | | Low | | | Thao tác duyệt GD | Development | | |

## 14. KIỂM TRA TÍCH HỢP VÀ LIÊN KẾT

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 66 | Tích hợp | WL_INT_001 | Gửi email hóa đơn rút tiền | Kiểm tra tính năng gửi email khi duyệt rút tiền | Admin duyệt GD rút tiền, email service hoạt động | 1. Duyệt GD rút tiền<br>2. Kiểm tra email user | User nhận được email hóa đơn rút tiền | | High | | | GD rút tiền + email config | Development | | |
| 67 | Tích hợp | WL_INT_002 | Log giao dịch đúng format | Kiểm tra log được ghi khi duyệt GD | Admin duyệt GD | 1. Duyệt GD<br>2. Kiểm tra log file | Log ghi đầy đủ: transaction_id, user_id, amount, timestamp | | Medium | | | Log monitoring | Development | | |
| 68 | Tích hợp | WL_INT_003 | Database transaction rollback | Kiểm tra rollback khi có lỗi | Tạo lỗi giả trong quá trình duyệt | 1. Tạo lỗi trong quá trình duyệt<br>2. Kiểm tra DB | DB không bị thay đổi khi có lỗi (rollback thành công) | | High | | | Error simulation | Development | | |
| 69 | Tích hợp | WL_INT_004 | Liên kết với module User | Kiểm tra hiển thị thông tin user | Có GD của user cụ thể | 1. Xem GD trong danh sách<br>2. Kiểm tra thông tin user | Hiển thị đúng tên, email của user | | Medium | | | User data | Development | | |
| 70 | Tích hợp | WL_INT_005 | Đồng bộ số dư với Wallet model | Kiểm tra sync số dư giữa transaction và wallet | Duyệt GD nạp tiền | 1. Duyệt GD nạp 100k<br>2. Kiểm tra bảng wallets | Số dư trong bảng wallets tăng đúng 100k | | Critical | | | Sync wallet balance | Development | | |

## 15. KIỂM TRA LỖI VÀ NGOẠI LỆ

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 71 | Lỗi | WL_ERROR_001 | Database connection error | Kiểm tra xử lý khi mất kết nối DB | Admin đã đăng nhập | 1. Ngắt kết nối DB<br>2. Truy cập trang ví | Hiển thị thông báo lỗi thân thiện, không crash | | High | | | DB disconnect | Development | | |
| 72 | Lỗi | WL_ERROR_002 | Email service error | Kiểm tra khi email service bị lỗi | Email service bị disable | 1. Duyệt GD rút tiền<br>2. Kiểm tra kết quả | GD vẫn được duyệt, log ghi lỗi email | | Medium | | | Email service down | Development | | |
| 73 | Lỗi | WL_ERROR_003 | Concurrent transaction approval | Kiểm tra duyệt GD đồng thời | 2 admin cùng duyệt 1 GD | 1. Admin A click duyệt<br>2. Admin B click duyệt cùng lúc | Chỉ 1 lần duyệt thành công, 1 lần báo lỗi | | High | | | Concurrent access | Development | | |
| 74 | Lỗi | WL_ERROR_004 | Large dataset timeout | Kiểm tra timeout với dữ liệu lớn | DB có >10,000 giao dịch | 1. Tải trang với filter phức tạp<br>2. Đợi response | Không timeout, có thông báo nếu quá lâu | | Medium | | | Large dataset | Development | | |
| 75 | Lỗi | WL_ERROR_005 | Invalid transaction ID | Truy cập với ID giao dịch không hợp lệ | Admin đã đăng nhập | 1. Truy cập /admin/wallets/approve/abc<br>2. Kiểm tra response | Thông báo lỗi 404 hoặc "ID không hợp lệ" | | Medium | | | Invalid ID format | Development | | |

---

## Ghi chú về Test Cases:

### Mức độ ưu tiên:
- **Critical**: Các chức năng cốt lõi, ảnh hưởng trực tiếp đến business logic
- **High**: Các chức năng quan trọng, ảnh hưởng đến trải nghiệm người dùng  
- **Medium**: Các chức năng hỗ trợ, cần thiết nhưng không critical
- **Low**: Các chức năng tối ưu, cải thiện UX

### Dữ liệu test cần chuẩn bị:
1. **Users**: Ít nhất 10 user với tên và email khác nhau
2. **Wallets**: Mỗi user có 1 wallet với số dư khác nhau
3. **Transactions**: 
   - Nạp tiền (Nap): pending, success, failed
   - Rút tiền (Rut): pending, success, failed  
   - Hoàn tiền (HOANTIEN): success
   - Thanh toán (payment): success
4. **Thời gian**: Giao dịch ở các mốc thời gian khác nhau để test filter

### Môi trường test:
- **Development**: Môi trường phát triển với dữ liệu test
- **Staging**: Môi trường giống production để test cuối cùng
- **Production**: Chỉ test các case không ảnh hưởng đến dữ liệu thật

### Lưu ý thực hiện:
1. Chạy test theo thứ tự từ Critical -> High -> Medium -> Low
2. Backup dữ liệu trước khi test các case có thể thay đổi DB
3. Test trên nhiều browser khác nhau (Chrome, Firefox, Safari)
4. Ghi lại screenshot cho các case fail để debug
5. Kiểm tra log sau mỗi test case quan trọng
