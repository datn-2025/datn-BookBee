# Test Cases - Quản Lý Danh Mục Thương Hiệu (Admin)

## Thông tin chung
- Module: Quản lý danh mục thương hiệu
- Controller: CategoryController (Brand methods)
- Tester: [Tên người test]
- Ngày tạo: 30/07/2025

---

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|

## 1. HIỂN THỊ DANH SÁCH THƯƠNG HIỆU

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 1 | Hiển thị danh sách | BR_LIST_001 | Hiển thị danh sách thương hiệu | Kiểm tra hiển thị danh sách tất cả thương hiệu | Admin đã đăng nhập, có thương hiệu trong DB | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/brands | Hiển thị danh sách thương hiệu với: tên, ảnh, mô tả, số sách, thời gian tạo, sắp xếp theo mới nhất | | High | | | Có ít nhất 5 thương hiệu | Development | | |
| 2 | Hiển thị danh sách | BR_LIST_002 | Không có thương hiệu | Kiểm tra hiển thị khi chưa có thương hiệu nào | Admin đã đăng nhập, DB trống thương hiệu | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/brands | Hiển thị thông báo "Không có thương hiệu nào" hoặc bảng trống | | Medium | | | DB không có thương hiệu | Development | | |
| 3 | Hiển thị danh sách | BR_LIST_003 | Phân trang thương hiệu | Kiểm tra chức năng phân trang | Admin đã đăng nhập, có >10 thương hiệu | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/brands<br>3. Kiểm tra nút phân trang | Hiển thị 10 thương hiệu/trang, có nút chuyển trang, số trang hiện tại | | High | | | Có >15 thương hiệu | Development | | |
| 4 | Hiển thị danh sách | BR_LIST_004 | Hiển thị số lượng sách của thương hiệu | Kiểm tra hiển thị số sách của từng thương hiệu | Admin đã đăng nhập, thương hiệu có sách | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/brands<br>3. Kiểm tra cột số sách | Hiển thị chính xác số lượng sách của từng thương hiệu | | High | | | Thương hiệu có số sách khác nhau | Development | | |
| 5 | Hiển thị danh sách | BR_LIST_005 | Sắp xếp theo thời gian tạo | Kiểm tra sắp xếp từ mới đến cũ | Admin đã đăng nhập, có nhiều thương hiệu | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/brands<br>3. Kiểm tra thứ tự hiển thị | Thương hiệu được sắp xếp từ mới nhất đến cũ nhất theo created_at | | Medium | | | Thương hiệu có thời gian tạo khác nhau | Development | | |

## 2. TÌM KIẾM THƯƠNG HIỆU

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 6 | Tìm kiếm | BR_SEARCH_001 | Tìm kiếm theo tên chính xác | Tìm kiếm thương hiệu theo tên chính xác | Admin đã đăng nhập, có thương hiệu "Samsung" | 1. Nhập "Samsung" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị thương hiệu "Samsung" | | High | | | Thương hiệu: "Samsung" | Development | | |
| 7 | Tìm kiếm | BR_SEARCH_002 | Tìm kiếm theo tên gần đúng | Tìm kiếm thương hiệu theo tên một phần | Admin đã đăng nhập, có thương hiệu "Samsung" | 1. Nhập "Sam" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị tất cả thương hiệu có tên chứa "Sam" | | High | | | Thương hiệu có tên chứa "Sam" | Development | | |
| 8 | Tìm kiếm | BR_SEARCH_003 | Tìm kiếm thương hiệu không tồn tại | Tìm kiếm thương hiệu không có trong hệ thống | Admin đã đăng nhập | 1. Nhập "ThuongHieuKhongTonTai" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị thông báo "Không tìm thấy kết quả" hoặc danh sách trống | | Medium | | | Tên không tồn tại | Development | | |
| 9 | Tìm kiếm | BR_SEARCH_004 | Trạng thái phân trang giữ từ khóa tìm kiếm | Kiểm tra từ khóa được giữ khi chuyển trang | Admin đã đăng nhập, tìm kiếm có >10 kết quả | 1. Tìm kiếm "Apple"<br>2. Chuyển sang trang 2<br>3. Kiểm tra ô tìm kiếm | Từ khóa "Apple" vẫn được giữ trong ô tìm kiếm | | Medium | | | >10 thương hiệu có tên chứa "Apple" | Development | | |
| 10 | Tìm kiếm | BR_SEARCH_005 | Nút làm mới tìm kiếm | Kiểm tra nút reset tìm kiếm | Admin đã thực hiện tìm kiếm | 1. Tìm kiếm "Apple"<br>2. Click nút "Làm mới"/"Reset" | Ô tìm kiếm được xóa, hiển thị tất cả thương hiệu | | Low | | | Có từ khóa tìm kiếm | Development | | |
| 11 | Tìm kiếm | BR_SEARCH_006 | Giữ lại từ khóa sau khi tìm kiếm | Kiểm tra từ khóa được giữ trong form | Admin thực hiện tìm kiếm | 1. Tìm kiếm "Microsoft"<br>2. Kiểm tra ô input | Ô tìm kiếm vẫn hiển thị "Microsoft" | | Low | | | Từ khóa tìm kiếm | Development | | |

## 3. THÊM THƯƠNG HIỆU MỚI

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 12 | Thêm thương hiệu | BR_ADD_001 | Thêm thành công | Thêm thương hiệu mới với đầy đủ thông tin hợp lệ | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên "Nike"<br>3. Nhập mô tả<br>4. Upload ảnh hợp lệ<br>5. Click "Lưu" | Thương hiệu được thêm thành công, thông báo "Thêm thương hiệu mới thành công", chuyển về danh sách | | Critical | | | Tên: "Nike", Ảnh: JPG 1MB | Development | | |
| 13 | Thêm thương hiệu | BR_ADD_002 | Thiếu tên thương hiệu | Thêm thương hiệu không nhập tên | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Để trống tên<br>3. Nhập mô tả<br>4. Click "Lưu" | Thông báo lỗi "Vui lòng nhập tên thương hiệu", không lưu | | High | | | Tên trống | Development | | |
| 14 | Thêm thương hiệu | BR_ADD_003 | Tên bị trùng | Thêm thương hiệu với tên đã tồn tại | Admin đã đăng nhập, có thương hiệu "Samsung" | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên "Samsung"<br>3. Click "Lưu" | Thông báo lỗi "Tên thương hiệu đã tồn tại", không lưu | | High | | | Tên trùng: "Samsung" | Development | | |
| 15 | Thêm thương hiệu | BR_ADD_004 | Tên vượt quá 255 ký tự | Thêm thương hiệu với tên quá dài | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên 300 ký tự<br>3. Click "Lưu" | Thông báo lỗi "Tên thương hiệu không được vượt quá 255 ký tự", không lưu | | Medium | | | Tên 300 ký tự | Development | | |
| 16 | Thêm thương hiệu | BR_ADD_005 | Tên có chứa mã HTML | Thêm thương hiệu với tên chứa HTML | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên "&lt;script&gt;alert('XSS')&lt;/script&gt;"<br>3. Click "Lưu" | Tên được escape hoặc filter, không thực thi mã HTML | | High | | | Tên chứa HTML/JS | Development | | |
| 17 | Thêm thương hiệu | BR_ADD_006 | Mô tả có chứa HTML bị strip | Thêm thương hiệu với mô tả chứa HTML tags | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên hợp lệ<br>3. Nhập mô tả "&lt;b&gt;Bold text&lt;/b&gt;"<br>4. Click "Lưu" | Mô tả được strip tags, chỉ giữ lại text thuần, thương hiệu được tạo thành công | | Medium | | | Mô tả chứa HTML | Development | | |
| 18 | Thêm thương hiệu | BR_ADD_007 | Không nhập mô tả | Thêm thương hiệu không có mô tả | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên hợp lệ<br>3. Để trống mô tả<br>4. Click "Lưu" | Thương hiệu được thêm thành công, mô tả null | | Low | | | Mô tả trống | Development | | |
| 19 | Thêm thương hiệu | BR_ADD_008 | Sai định dạng ảnh | Upload file không phải ảnh | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên hợp lệ<br>3. Upload file .txt<br>4. Click "Lưu" | Thông báo lỗi "File phải là hình ảnh", không lưu | | Medium | | | File .txt | Development | | |
| 20 | Thêm thương hiệu | BR_ADD_009 | Không upload ảnh | Thêm thương hiệu không có ảnh | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Nhập tên hợp lệ<br>3. Không upload ảnh<br>4. Click "Lưu" | Thương hiệu được thêm thành công, ảnh null | | Low | | | Không có ảnh | Development | | |
| 21 | Thêm thương hiệu | BR_ADD_010 | Upload file giả ảnh có đuôi .jpg | Upload file txt đổi đuôi thành jpg | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Upload file .txt đổi thành .jpg<br>3. Click "Lưu" | Thông báo lỗi "File phải là hình ảnh", không lưu | | Medium | | | File giả .jpg | Development | | |
| 22 | Thêm thương hiệu | BR_ADD_011 | Ảnh quá 2MB | Upload ảnh vượt giới hạn kích thước | Admin đã đăng nhập | 1. Truy cập trang thêm thương hiệu<br>2. Upload ảnh 3MB<br>3. Click "Lưu" | Thông báo lỗi "Kích thước hình ảnh không được vượt quá 2MB", không lưu | | Medium | | | Ảnh 3MB | Development | | |
| 23 | Thêm thương hiệu | BR_ADD_012 | Giữ lại dữ liệu khi lỗi | Dữ liệu được giữ khi validation fail | Admin nhập dữ liệu hợp lệ + 1 field lỗi | 1. Nhập đầy đủ form<br>2. Upload ảnh sai định dạng<br>3. Click "Lưu" | Các field hợp lệ vẫn giữ nguyên giá trị đã nhập | | Medium | | | Dữ liệu mixed hợp lệ/lỗi | Development | | |

## 4. SỬA THƯƠNG HIỆU

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 24 | Sửa thương hiệu | BR_EDIT_001 | Truy cập trang sửa thương hiệu | Kiểm tra mở trang chỉnh sửa | Admin đã đăng nhập, có thương hiệu ID=1 | 1. Truy cập danh sách thương hiệu<br>2. Click "Sửa" thương hiệu ID=1 | Mở trang sửa với dữ liệu thương hiệu được load sẵn | | High | | | Thương hiệu ID=1 tồn tại | Development | | |
| 25 | Sửa thương hiệu | BR_EDIT_002 | Để trống tên thương hiệu | Cập nhật thương hiệu với tên trống | Admin đang ở trang sửa thương hiệu | 1. Xóa hết tên thương hiệu<br>2. Click "Cập nhật" | Thông báo lỗi "Vui lòng nhập tên thương hiệu", không cập nhật | | High | | | Tên trống | Development | | |
| 26 | Sửa thương hiệu | BR_EDIT_003 | Cập nhật tên hợp lệ | Cập nhật tên thương hiệu mới hợp lệ | Admin đang ở trang sửa thương hiệu | 1. Đổi tên thành "Adidas"<br>2. Click "Cập nhật" | Thương hiệu được cập nhật thành công, thông báo "Cập nhật thương hiệu thành công!" | | Critical | | | Tên mới: "Adidas" | Development | | |
| 27 | Sửa thương hiệu | BR_EDIT_004 | Cập nhật tên bị trùng | Cập nhật tên trùng với thương hiệu khác | Admin sửa thương hiệu A, có thương hiệu B | 1. Đổi tên thương hiệu A thành tên của thương hiệu B<br>2. Click "Cập nhật" | Thông báo lỗi "Tên thương hiệu đã tồn tại", không cập nhật | | High | | | Tên trùng với thương hiệu khác | Development | | |
| 28 | Sửa thương hiệu | BR_EDIT_005 | Cập nhật tên quá dài | Cập nhật tên vượt quá 255 ký tự | Admin đang ở trang sửa thương hiệu | 1. Nhập tên 300 ký tự<br>2. Click "Cập nhật" | Thông báo lỗi "Tên thương hiệu không được vượt quá 255 ký tự", không cập nhật | | Medium | | | Tên 300 ký tự | Development | | |
| 29 | Sửa thương hiệu | BR_EDIT_006 | Cập nhật mô tả hợp lệ | Cập nhật mô tả mới | Admin đang ở trang sửa thương hiệu | 1. Cập nhật mô tả mới<br>2. Click "Cập nhật" | Mô tả được cập nhật thành công, HTML tags bị strip | | Medium | | | Mô tả mới | Development | | |
| 30 | Sửa thương hiệu | BR_EDIT_007 | Cập nhật ảnh mới | Upload ảnh mới thay thế ảnh cũ | Admin đang ở trang sửa thương hiệu có ảnh | 1. Upload ảnh mới hợp lệ<br>2. Click "Cập nhật" | Ảnh cũ bị xóa, ảnh mới được lưu, cập nhật thành công | | Medium | | | Ảnh mới hợp lệ | Development | | |
| 31 | Sửa thương hiệu | BR_EDIT_008 | Không upload ảnh | Cập nhật mà không đổi ảnh | Admin đang ở trang sửa thương hiệu | 1. Chỉ sửa tên<br>2. Không chọn ảnh mới<br>3. Click "Cập nhật" | Tên được cập nhật, ảnh cũ được giữ nguyên | | Medium | | | Giữ ảnh cũ | Development | | |
| 32 | Sửa thương hiệu | BR_EDIT_009 | Cập nhật không thay đổi gì | Submit form mà không sửa gì | Admin đang ở trang sửa thương hiệu | 1. Không thay đổi gì<br>2. Click "Cập nhật" | Thông báo cập nhật thành công, dữ liệu không đổi | | Low | | | Không thay đổi | Development | | |
| 33 | Sửa thương hiệu | BR_EDIT_010 | Truy cập bằng ID không tồn tại | Sửa thương hiệu với ID không hợp lệ | Admin đã đăng nhập | 1. Truy cập /admin/categories/brands/99999/edit | Lỗi 404 hoặc thông báo "Thương hiệu không tồn tại" | | Medium | | | ID không tồn tại: 99999 | Development | | |

## 5. XÓA THƯƠNG HIỆU

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 34 | Xóa thương hiệu | BR_DEL_001 | Xóa mềm thành công | Xóa mềm thương hiệu không có sách | Admin đã đăng nhập, thương hiệu không có sách | 1. Tìm thương hiệu không có sách<br>2. Click "Xóa"<br>3. Xác nhận xóa | Thương hiệu được xóa mềm, thông báo "Đã chuyển thương hiệu vào thùng rác!" | | Critical | | | Thương hiệu không có sách | Development | | |
| 35 | Xóa thương hiệu | BR_DEL_002 | Không cho xóa mềm thương hiệu có sách | Thử xóa thương hiệu đang có sách | Admin đã đăng nhập, thương hiệu có sách | 1. Tìm thương hiệu có sách<br>2. Click "Xóa" | Thông báo lỗi "Không thể xóa thương hiệu vì vẫn còn sách thuộc thương hiệu này!", không xóa | | Critical | | | Thương hiệu có ít nhất 1 sách | Development | | |
| 36 | Xóa thương hiệu | BR_DEL_003 | Khôi phục thương hiệu đã xóa mềm | Khôi phục thương hiệu từ thùng rác | Admin đã đăng nhập, có thương hiệu trong thùng rác | 1. Vào thùng rác<br>2. Tìm thương hiệu đã xóa<br>3. Click "Khôi phục" | Thương hiệu được khôi phục, thông báo "Khôi phục thương hiệu thành công!", xuất hiện lại trong danh sách chính | | High | | | Thương hiệu đã xóa mềm | Development | | |
| 37 | Xóa thương hiệu | BR_DEL_004 | Xóa vĩnh viễn thương hiệu không có sách | Xóa vĩnh viễn từ thùng rác | Admin đã đăng nhập, thương hiệu trong thùng rác không có sách | 1. Vào thùng rác<br>2. Tìm thương hiệu không có sách<br>3. Click "Xóa vĩnh viễn" | Thương hiệu được xóa vĩnh viễn khỏi DB, file ảnh bị xóa, thông báo "Đã xóa vĩnh viễn thương hiệu!" | | High | | | Thương hiệu đã xóa mềm, không có sách | Development | | |
| 38 | Xóa thương hiệu | BR_DEL_005 | Không cho xóa vĩnh viễn nếu thương hiệu có sách | Thử xóa vĩnh viễn thương hiệu có sách | Admin đã đăng nhập, thương hiệu trong thùng rác có sách | 1. Vào thùng rác<br>2. Tìm thương hiệu có sách<br>3. Click "Xóa vĩnh viễn" | Thông báo lỗi "Không thể xóa vĩnh viễn thương hiệu vì vẫn còn sách thuộc thương hiệu này!" | | Critical | | | Thương hiệu đã xóa mềm nhưng có sách | Development | | |

## 6. QUẢN LÝ THÙNG RÁC

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 39 | Thùng rác | BR_TRASH_001 | Hiển thị thương hiệu đã xóa mềm | Xem danh sách thương hiệu trong thùng rác | Admin đã đăng nhập, có thương hiệu đã xóa mềm | 1. Click vào "Thùng rác"<br>2. Kiểm tra danh sách | Hiển thị danh sách thương hiệu đã xóa mềm với thông tin đầy đủ | | High | | | Thương hiệu đã xóa mềm | Development | | |
| 40 | Thùng rác | BR_TRASH_002 | Tìm kiếm thương hiệu đã xóa mềm | Tìm kiếm trong thùng rác | Admin đã đăng nhập, có thương hiệu đã xóa trong thùng rác | 1. Vào thùng rác<br>2. Nhập tên thương hiệu vào ô tìm kiếm<br>3. Click tìm kiếm | Hiển thị thương hiệu đã xóa khớp với từ khóa tìm kiếm | | Medium | | | Thương hiệu đã xóa có tên cụ thể | Development | | |
| 41 | Thùng rác | BR_TRASH_003 | Tìm kiếm không có kết quả | Tìm kiếm thương hiệu không tồn tại trong thùng rác | Admin đã đăng nhập, trong thùng rác | 1. Vào thùng rác<br>2. Tìm kiếm tên không tồn tại | Hiển thị "Không tìm thấy kết quả" hoặc danh sách trống | | Low | | | Tên không tồn tại trong thùng rác | Development | | |
| 42 | Thùng rác | BR_TRASH_004 | Thùng rác trống | Kiểm tra thùng rác khi không có thương hiệu nào bị xóa | Admin đã đăng nhập, chưa xóa thương hiệu nào | 1. Click vào thùng rác | Hiển thị thông báo "Thùng rác trống" hoặc bảng trống | | Low | | | Không có thương hiệu bị xóa | Development | | |
| 43 | Thùng rác | BR_TRASH_005 | Kiểm tra phân trang thùng rác | Phân trang trong thùng rác | Admin đã đăng nhập, có >10 thương hiệu trong thùng rác | 1. Vào thùng rác<br>2. Kiểm tra phân trang | Hiển thị 10 thương hiệu/trang, có điều hướng trang | | Medium | | | >10 thương hiệu trong thùng rác | Development | | |

## 7. KIỂM TRA BẢO MẬT VÀ QUYỀN HẠN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 44 | Bảo mật | BR_SEC_001 | Truy cập không có quyền admin | User thường truy cập trang admin thương hiệu | User thường đã đăng nhập | 1. Đăng nhập user thường<br>2. Truy cập /admin/categories/brands | Chuyển hướng đến trang login hoặc thông báo "Không có quyền" | | Critical | | | User thường: user@test.com | Development | | |
| 45 | Bảo mật | BR_SEC_002 | Truy cập khi chưa đăng nhập | Truy cập trang admin thương hiệu khi chưa login | Chưa đăng nhập | 1. Không đăng nhập<br>2. Truy cập /admin/categories/brands | Chuyển hướng đến trang đăng nhập | | Critical | | | Chưa đăng nhập | Development | | |
| 46 | Bảo mật | BR_SEC_003 | SQL Injection trong tìm kiếm | Thử SQL injection qua ô tìm kiếm | Admin đã đăng nhập | 1. Nhập "'; DROP TABLE brands; --" vào ô tìm kiếm<br>2. Click tìm kiếm | Không thực hiện câu lệnh SQL, tìm kiếm bình thường | | High | | | SQL injection payload | Development | | |
| 47 | Bảo mật | BR_SEC_004 | XSS Prevention | Thử XSS attack qua tên thương hiệu | Admin đã đăng nhập | 1. Thêm thương hiệu với tên "&lt;script&gt;alert('XSS')&lt;/script&gt;"<br>2. Lưu và xem danh sách | Mã script không được thực thi, hiển thị dạng text | | High | | | XSS payload | Development | | |

## 8. KIỂM TRA HIỆU SUẤT VÀ CHỨC NĂNG NÂNG CAO

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 48 | Hiệu suất | BR_PERF_001 | Upload ảnh lớn | Upload ảnh gần giới hạn 2MB | Admin đã đăng nhập | 1. Upload ảnh 1.9MB<br>2. Đo thời gian upload | Upload thành công trong <10 giây | | Medium | | | Ảnh 1.9MB | Development | | |
| 49 | Hiệu suất | BR_PERF_002 | Tải trang với nhiều thương hiệu | Kiểm tra thời gian tải trang với DB lớn | DB có >100 thương hiệu | 1. Truy cập /admin/categories/brands<br>2. Đo thời gian tải | Trang tải trong <3 giây | | Medium | | | >100 thương hiệu | Development | | |
| 50 | Tích hợp | BR_INT_001 | Xóa file ảnh khi xóa vĩnh viễn | Kiểm tra file cleanup | Thương hiệu có ảnh bị xóa vĩnh viễn | 1. Xóa vĩnh viễn thương hiệu có ảnh<br>2. Kiểm tra thư mục storage | File ảnh được xóa khỏi thư mục storage/brands | | High | | | Thương hiệu có ảnh | Development | | |

---

## Ghi chú về Test Cases:

### Mức độ ưu tiên:
- **Critical**: Các chức năng cốt lõi, ảnh hưởng trực tiếp đến business logic
- **High**: Các chức năng quan trọng, ảnh hưởng đến trải nghiệm người dùng  
- **Medium**: Các chức năng hỗ trợ, cần thiết nhưng không critical
- **Low**: Các chức năng tối ưu, cải thiện UX

### Dữ liệu test cần chuẩn bị:
1. **Brands**: Ít nhất 15 thương hiệu với tên khác nhau như "Samsung", "Apple", "Nike", "Adidas"
2. **Books**: Một số thương hiệu có sách, một số không có
3. **Images**: Các file ảnh hợp lệ (JPG, PNG <2MB) và không hợp lệ để test upload
4. **Deleted Brands**: Một số thương hiệu đã xóa mềm để test thùng rác
5. **Users**: Admin user và user thường để test quyền

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
6. Verify file upload/delete trên storage thực tế
7. Test với dữ liệu Unicode (tiếng Việt có dấu)

### Test Data Suggestions:
- **Tên thương hiệu**: "Samsung", "Apple", "Nike", "Adidas", "Sony", "LG"
- **File ảnh hợp lệ**: JPG/PNG dưới 2MB
- **File ảnh không hợp lệ**: TXT, PDF, ảnh >2MB
- **Mô tả**: Văn bản có HTML tags để test strip_tags()
- **Tên đặc biệt**: Có dấu tiếng Việt, có số, có ký tự đặc biệt

### Chức năng đặc thù cần lưu ý:
- **strip_tags()**: Mô tả sẽ bị loại bỏ HTML tags
- **File management**: Ảnh cũ bị xóa khi upload ảnh mới
- **Soft delete**: Kiểm tra quan hệ với sách trước khi xóa
- **withCount('books')**: Hiển thị số lượng sách của thương hiệu
