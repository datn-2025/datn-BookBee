# Test Cases - Quản Lý Danh Mục Tác Giả (Admin)

## Thông tin chung
- Module: Quản lý danh mục tác giả
- Controller: AuthorController
- Tester: [Tên người test]
- Ngày tạo: 30/07/2025

---

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|

## 1. HIỂN THỊ DANH SÁCH TÁC GIẢ

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 1 | Hiển thị danh sách | AU_LIST_001 | Hiển thị danh sách tác giả | Kiểm tra hiển thị danh sách tất cả tác giả | Admin đã đăng nhập, có tác giả trong DB | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/authors | Hiển thị danh sách tác giả với: tên, ảnh, số sách, thời gian tạo, sắp xếp theo mới nhất | | High | | | Có ít nhất 5 tác giả | Development | | |
| 2 | Hiển thị danh sách | AU_LIST_002 | Không có tác giả | Kiểm tra hiển thị khi chưa có tác giả nào | Admin đã đăng nhập, DB trống tác giả | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/authors | Hiển thị thông báo "Không có tác giả nào" hoặc bảng trống | | Medium | | | DB không có tác giả | Development | | |
| 3 | Hiển thị danh sách | AU_LIST_003 | Phân trang tác giả | Kiểm tra chức năng phân trang | Admin đã đăng nhập, có >10 tác giả | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/authors<br>3. Kiểm tra nút phân trang | Hiển thị 10 tác giả/trang, có nút chuyển trang, số trang hiện tại | | High | | | Có >15 tác giả | Development | | |
| 4 | Hiển thị danh sách | AU_LIST_004 | Hiển thị số lượng sách của tác giả | Kiểm tra hiển thị số sách của từng tác giả | Admin đã đăng nhập, tác giả có sách | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/authors<br>3. Kiểm tra cột số sách | Hiển thị chính xác số lượng sách của từng tác giả | | High | | | Tác giả có số sách khác nhau | Development | | |
| 5 | Hiển thị danh sách | AU_LIST_005 | Hiển thị số thùng rác | Kiểm tra hiển thị số tác giả đã xóa | Admin đã đăng nhập, có tác giả đã xóa | 1. Đăng nhập admin<br>2. Truy cập /admin/categories/authors<br>3. Kiểm tra số thùng rác | Hiển thị đúng số lượng tác giả trong thùng rác | | Medium | | | Có tác giả đã xóa mềm | Development | | |

## 2. TÌM KIẾM TÁC GIẢ

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 6 | Tìm kiếm | AU_SEARCH_001 | Tìm kiếm theo tên chính xác | Tìm kiếm tác giả theo tên chính xác | Admin đã đăng nhập, có tác giả "Nguyễn Du" | 1. Nhập "Nguyễn Du" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị tác giả "Nguyễn Du" | | High | | | Tác giả: "Nguyễn Du" | Development | | |
| 7 | Tìm kiếm | AU_SEARCH_002 | Tìm kiếm theo tên gần đúng | Tìm kiếm tác giả theo tên một phần | Admin đã đăng nhập, có tác giả "Nguyễn Du" | 1. Nhập "Nguyễn" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị tất cả tác giả có tên chứa "Nguyễn" | | High | | | Tác giả có tên chứa "Nguyễn" | Development | | |
| 8 | Tìm kiếm | AU_SEARCH_003 | Tìm kiếm tác giả không tồn tại | Tìm kiếm tác giả không có trong hệ thống | Admin đã đăng nhập | 1. Nhập "TacGiaKhongTonTai" vào ô tìm kiếm<br>2. Click tìm kiếm | Hiển thị thông báo "Không tìm thấy kết quả" hoặc danh sách trống | | Medium | | | Tên không tồn tại | Development | | |
| 9 | Tìm kiếm | AU_SEARCH_004 | Trạng thái phân trang giữ từ khóa tìm kiếm | Kiểm tra từ khóa được giữ khi chuyển trang | Admin đã đăng nhập, tìm kiếm có >10 kết quả | 1. Tìm kiếm "Nguyễn"<br>2. Chuyển sang trang 2<br>3. Kiểm tra ô tìm kiếm | Từ khóa "Nguyễn" vẫn được giữ trong ô tìm kiếm | | Medium | | | >10 tác giả có tên chứa "Nguyễn" | Development | | |
| 10 | Tìm kiếm | AU_SEARCH_005 | Nút làm mới tìm kiếm | Kiểm tra nút reset tìm kiếm | Admin đã thực hiện tìm kiếm | 1. Tìm kiếm "Nguyễn"<br>2. Click nút "Làm mới"/"Reset" | Ô tìm kiếm được xóa, hiển thị tất cả tác giả | | Low | | | Có từ khóa tìm kiếm | Development | | |
| 11 | Tìm kiếm | AU_SEARCH_006 | Giữ lại từ khóa sau khi tìm kiếm | Kiểm tra từ khóa được giữ trong form | Admin thực hiện tìm kiếm | 1. Tìm kiếm "Truyện Kiều"<br>2. Kiểm tra ô input | Ô tìm kiếm vẫn hiển thị "Truyện Kiều" | | Low | | | Từ khóa tìm kiếm | Development | | |

## 3. THÊM TÁC GIẢ MỚI

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 12 | Thêm tác giả | AU_ADD_001 | Thêm thành công | Thêm tác giả mới với đầy đủ thông tin hợp lệ | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Nhập tên "Hồ Chí Minh"<br>3. Nhập tiểu sử<br>4. Upload ảnh hợp lệ<br>5. Click "Lưu" | Tác giả được thêm thành công, thông báo "Thêm tác giả mới thành công", chuyển về danh sách | | Critical | | | Tên: "Hồ Chí Minh", Ảnh: JPG 1MB | Development | | |
| 13 | Thêm tác giả | AU_ADD_002 | Thiếu tên tác giả | Thêm tác giả không nhập tên | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Để trống tên<br>3. Nhập tiểu sử<br>4. Click "Lưu" | Thông báo lỗi "Vui lòng nhập tên tác giả", không lưu | | High | | | Tên trống | Development | | |
| 14 | Thêm tác giả | AU_ADD_003 | Tên bị trùng | Thêm tác giả với tên đã tồn tại | Admin đã đăng nhập, có tác giả "Nguyễn Du" | 1. Truy cập trang thêm tác giả<br>2. Nhập tên "Nguyễn Du"<br>3. Click "Lưu" | Thông báo lỗi "Tên tác giả đã tồn tại trong hệ thống", không lưu | | High | | | Tên trùng: "Nguyễn Du" | Development | | |
| 15 | Thêm tác giả | AU_ADD_004 | Tên vượt quá 255 ký tự | Thêm tác giả với tên quá dài | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Nhập tên 300 ký tự<br>3. Click "Lưu" | Thông báo lỗi "Tên tác giả không được vượt quá 255 ký tự", không lưu | | Medium | | | Tên 300 ký tự | Development | | |
| 16 | Thêm tác giả | AU_ADD_005 | Tên có chứa mã HTML | Thêm tác giả với tên chứa HTML | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Nhập tên "&lt;script&gt;alert('XSS')&lt;/script&gt;"<br>3. Click "Lưu" | Tên được escape hoặc filter, không thực thi mã HTML | | High | | | Tên chứa HTML/JS | Development | | |
| 17 | Thêm tác giả | AU_ADD_006 | Tiểu sử hợp lệ | Thêm tác giả với tiểu sử dài | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Nhập tên hợp lệ<br>3. Nhập tiểu sử dài 500 từ<br>4. Click "Lưu" | Tác giả được thêm thành công với tiểu sử đầy đủ | | Medium | | | Tiểu sử dài | Development | | |
| 18 | Thêm tác giả | AU_ADD_007 | Không nhập tiểu sử | Thêm tác giả không có tiểu sử | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Nhập tên hợp lệ<br>3. Để trống tiểu sử<br>4. Click "Lưu" | Tác giả được thêm thành công, tiểu sử null | | Low | | | Tiểu sử trống | Development | | |
| 19 | Thêm tác giả | AU_ADD_008 | Sai định dạng ảnh | Upload file không phải ảnh | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Nhập tên hợp lệ<br>3. Upload file .txt<br>4. Click "Lưu" | Thông báo lỗi "File phải là hình ảnh", không lưu | | Medium | | | File .txt | Development | | |
| 20 | Thêm tác giả | AU_ADD_009 | Không upload ảnh | Thêm tác giả không có ảnh | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Nhập tên hợp lệ<br>3. Không upload ảnh<br>4. Click "Lưu" | Tác giả được thêm thành công, ảnh null | | Low | | | Không có ảnh | Development | | |
| 21 | Thêm tác giả | AU_ADD_010 | Xem trước ảnh trước khi lưu | Upload ảnh và xem preview | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Upload ảnh hợp lệ<br>3. Kiểm tra preview | Hiển thị preview ảnh trước khi lưu | | Low | | | Ảnh JPG hợp lệ | Development | | |
| 22 | Thêm tác giả | AU_ADD_011 | Upload file giả ảnh có đuôi .jpg | Upload file txt đổi đuôi thành jpg | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Upload file .txt đổi thành .jpg<br>3. Click "Lưu" | Thông báo lỗi "File phải là hình ảnh", không lưu | | Medium | | | File giả .jpg | Development | | |
| 23 | Thêm tác giả | AU_ADD_012 | Ảnh quá 2MB | Upload ảnh vượt giới hạn kích thước | Admin đã đăng nhập | 1. Truy cập trang thêm tác giả<br>2. Upload ảnh 3MB<br>3. Click "Lưu" | Thông báo lỗi "Kích thước hình ảnh không được vượt quá 2MB", không lưu | | Medium | | | Ảnh 3MB | Development | | |
| 24 | Thêm tác giả | AU_ADD_013 | Click nút "Quay lại" | Kiểm tra nút quay lại | Admin đang ở trang thêm | 1. Truy cập trang thêm tác giả<br>2. Click nút "Quay lại" | Chuyển về trang danh sách tác giả | | Low | | | Nút quay lại | Development | | |
| 25 | Thêm tác giả | AU_ADD_014 | Thêm nhanh liên tục | Thêm nhiều tác giả liên tiếp | Admin đã đăng nhập | 1. Thêm tác giả A<br>2. Thêm ngay tác giả B<br>3. Thêm ngay tác giả C | Tất cả tác giả được thêm thành công, không xung đột | | Medium | | | 3 tác giả khác nhau | Development | | |
| 26 | Thêm tác giả | AU_ADD_015 | Giữ lại dữ liệu khi lỗi | Dữ liệu được giữ khi validation fail | Admin nhập dữ liệu hợp lệ + 1 field lỗi | 1. Nhập đầy đủ form<br>2. Upload ảnh sai định dạng<br>3. Click "Lưu" | Các field hợp lệ vẫn giữ nguyên giá trị đã nhập | | Medium | | | Dữ liệu mixed hợp lệ/lỗi | Development | | |

## 4. SỬA TÁC GIẢ

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 27 | Sửa tác giả | AU_EDIT_001 | Truy cập trang sửa tác giả | Kiểm tra mở trang chỉnh sửa | Admin đã đăng nhập, có tác giả ID=1 | 1. Truy cập danh sách tác giả<br>2. Click "Sửa" tác giả ID=1 | Mở trang sửa với dữ liệu tác giả được load sẵn | | High | | | Tác giả ID=1 tồn tại | Development | | |
| 28 | Sửa tác giả | AU_EDIT_002 | Để trống tên tác giả | Cập nhật tác giả với tên trống | Admin đang ở trang sửa tác giả | 1. Xóa hết tên tác giả<br>2. Click "Cập nhật" | Thông báo lỗi "Vui lòng nhập tên tác giả", không cập nhật | | High | | | Tên trống | Development | | |
| 29 | Sửa tác giả | AU_EDIT_003 | Cập nhật tên hợp lệ | Cập nhật tên tác giả mới hợp lệ | Admin đang ở trang sửa tác giả | 1. Đổi tên thành "Lê Quý Đôn"<br>2. Click "Cập nhật" | Tác giả được cập nhật thành công, thông báo "Cập nhật tác giả thành công" | | Critical | | | Tên mới: "Lê Quý Đôn" | Development | | |
| 30 | Sửa tác giả | AU_EDIT_004 | Cập nhật tên bị trùng | Cập nhật tên trùng với tác giả khác | Admin sửa tác giả A, có tác giả B | 1. Đổi tên tác giả A thành tên của tác giả B<br>2. Click "Cập nhật" | Thông báo lỗi "Tên tác giả đã tồn tại trong hệ thống", không cập nhật | | High | | | Tên trùng với tác giả khác | Development | | |
| 31 | Sửa tác giả | AU_EDIT_005 | Cập nhật tên quá dài | Cập nhật tên vượt quá 255 ký tự | Admin đang ở trang sửa tác giả | 1. Nhập tên 300 ký tự<br>2. Click "Cập nhật" | Thông báo lỗi "Tên tác giả không được vượt quá 255 ký tự", không cập nhật | | Medium | | | Tên 300 ký tự | Development | | |
| 32 | Sửa tác giả | AU_EDIT_006 | Cập nhật tên có chứa mã HTML | Cập nhật tên chứa HTML/JS | Admin đang ở trang sửa tác giả | 1. Nhập tên chứa "&lt;script&gt;"<br>2. Click "Cập nhật" | Tên được escape hoặc filter, không thực thi mã | | High | | | Tên chứa HTML | Development | | |
| 33 | Sửa tác giả | AU_EDIT_007 | Cập nhật tiểu sử hợp lệ | Cập nhật tiểu sử mới | Admin đang ở trang sửa tác giả | 1. Cập nhật tiểu sử mới<br>2. Click "Cập nhật" | Tiểu sử được cập nhật thành công | | Medium | | | Tiểu sử mới | Development | | |
| 34 | Sửa tác giả | AU_EDIT_008 | Cập nhật tiểu sử null | Xóa hết tiểu sử | Admin đang ở trang sửa tác giả có tiểu sử | 1. Xóa hết tiểu sử<br>2. Click "Cập nhật" | Tiểu sử được set về null, cập nhật thành công | | Low | | | Xóa tiểu sử | Development | | |
| 35 | Sửa tác giả | AU_EDIT_009 | Cập nhật sai định dạng ảnh | Upload ảnh sai định dạng | Admin đang ở trang sửa tác giả | 1. Upload file .pdf<br>2. Click "Cập nhật" | Thông báo lỗi "File phải là hình ảnh", không cập nhật | | Medium | | | File .pdf | Development | | |
| 36 | Sửa tác giả | AU_EDIT_010 | Không upload ảnh | Cập nhật mà không đổi ảnh | Admin đang ở trang sửa tác giả | 1. Chỉ sửa tên<br>2. Không chọn ảnh mới<br>3. Click "Cập nhật" | Tên được cập nhật, ảnh cũ được giữ nguyên | | Medium | | | Giữ ảnh cũ | Development | | |
| 37 | Sửa tác giả | AU_EDIT_011 | Cập nhật không thay đổi gì | Submit form mà không sửa gì | Admin đang ở trang sửa tác giả | 1. Không thay đổi gì<br>2. Click "Cập nhật" | Thông báo cập nhật thành công, dữ liệu không đổi | | Low | | | Không thay đổi | Development | | |
| 38 | Sửa tác giả | AU_EDIT_012 | Xem trước ảnh trước khi lưu | Upload ảnh mới và xem preview | Admin đang ở trang sửa tác giả | 1. Upload ảnh mới<br>2. Kiểm tra preview | Hiển thị preview ảnh mới trước khi lưu | | Low | | | Ảnh mới | Development | | |
| 39 | Sửa tác giả | AU_EDIT_013 | Cập nhật file giả ảnh .jpg | Upload file giả định dạng jpg | Admin đang ở trang sửa tác giả | 1. Upload file .txt đổi thành .jpg<br>2. Click "Cập nhật" | Thông báo lỗi "File phải là hình ảnh", không cập nhật | | Medium | | | File giả .jpg | Development | | |
| 40 | Sửa tác giả | AU_EDIT_014 | Ảnh quá 2MB | Upload ảnh vượt giới hạn | Admin đang ở trang sửa tác giả | 1. Upload ảnh 3MB<br>2. Click "Cập nhật" | Thông báo lỗi "Kích thước hình ảnh không được vượt quá 2MB" | | Medium | | | Ảnh 3MB | Development | | |
| 41 | Sửa tác giả | AU_EDIT_015 | Xóa ảnh hiện tại | Xóa ảnh của tác giả | Admin sửa tác giả có ảnh | 1. Chọn xóa ảnh hiện tại<br>2. Click "Cập nhật" | Ảnh bị xóa, tác giả không còn ảnh | | Low | | | Tác giả có ảnh | Development | | |
| 42 | Sửa tác giả | AU_EDIT_016 | Click nút "Quay lại" | Kiểm tra nút quay lại từ trang sửa | Admin đang ở trang sửa | 1. Click nút "Quay lại" | Chuyển về trang danh sách tác giả | | Low | | | Nút quay lại | Development | | |
| 43 | Sửa tác giả | AU_EDIT_017 | Giữ lại dữ liệu khi lỗi | Dữ liệu được giữ khi có lỗi validation | Admin sửa với dữ liệu hợp lệ + 1 lỗi | 1. Sửa tên hợp lệ<br>2. Upload ảnh sai<br>3. Click "Cập nhật" | Tên hợp lệ vẫn được giữ trong form | | Medium | | | Dữ liệu mixed | Development | | |
| 44 | Sửa tác giả | AU_EDIT_018 | Cập nhật nhanh liên tục | Cập nhật nhiều lần liên tiếp | Admin đang ở trang sửa | 1. Cập nhật lần 1<br>2. Cập nhật lần 2 ngay sau<br>3. Cập nhật lần 3 | Tất cả cập nhật thành công, không xung đột | | Medium | | | Cập nhật liên tiếp | Development | | |
| 45 | Sửa tác giả | AU_EDIT_019 | Truy cập bằng ID không tồn tại | Sửa tác giả với ID không hợp lệ | Admin đã đăng nhập | 1. Truy cập /admin/categories/authors/99999/edit | Lỗi 404 hoặc thông báo "Tác giả không tồn tại" | | Medium | | | ID không tồn tại: 99999 | Development | | |

## 5. XÓA TÁC GIẢ

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 46 | Xóa tác giả | AU_DEL_001 | Xóa mềm thành công | Xóa mềm tác giả không có sách | Admin đã đăng nhập, tác giả không có sách | 1. Tìm tác giả không có sách<br>2. Click "Xóa"<br>3. Xác nhận xóa | Tác giả được xóa mềm, thông báo "Tác giả đã được xóa tạm thời thành công" | | Critical | | | Tác giả không có sách | Development | | |
| 47 | Xóa tác giả | AU_DEL_002 | Không cho xóa mềm tác giả có sách | Thử xóa tác giả đang có sách | Admin đã đăng nhập, tác giả có sách | 1. Tìm tác giả có sách<br>2. Click "Xóa" | Thông báo lỗi "Không thể xóa tác giả đang có sách trong hệ thống", không xóa | | Critical | | | Tác giả có ít nhất 1 sách | Development | | |
| 48 | Xóa tác giả | AU_DEL_003 | Xóa mềm tác giả không tồn tại | Xóa tác giả với ID không hợp lệ | Admin đã đăng nhập | 1. Truy cập URL /admin/categories/authors/99999 với method DELETE | Lỗi 404 hoặc thông báo "Tác giả không tồn tại" | | Medium | | | ID không tồn tại: 99999 | Development | | |
| 49 | Xóa tác giả | AU_DEL_004 | Xóa vĩnh viễn tác giả không có sách | Xóa vĩnh viễn từ thùng rác | Admin đã đăng nhập, tác giả trong thùng rác không có sách | 1. Vào thùng rác<br>2. Tìm tác giả không có sách<br>3. Click "Xóa vĩnh viễn" | Tác giả được xóa vĩnh viễn khỏi DB, thông báo "Tác giả đã được xóa vĩnh viễn" | | High | | | Tác giả đã xóa mềm, không có sách | Development | | |
| 50 | Xóa tác giả | AU_DEL_005 | Không cho xóa vĩnh viễn nếu tác giả có sách | Thử xóa vĩnh viễn tác giả có sách | Admin đã đăng nhập, tác giả trong thùng rác có sách | 1. Vào thùng rác<br>2. Tìm tác giả có sách<br>3. Click "Xóa vĩnh viễn" | Thông báo lỗi "Không thể xóa vĩnh viễn tác giả đang có sách trong hệ thống. Vui lòng gán sách cho tác giả khác hoặc xóa mềm" | | Critical | | | Tác giả đã xóa mềm nhưng có sách | Development | | |
| 51 | Xóa tác giả | AU_DEL_006 | Khôi phục tác giả đã xóa mềm | Khôi phục tác giả từ thùng rác | Admin đã đăng nhập, có tác giả trong thùng rác | 1. Vào thùng rác<br>2. Tìm tác giả đã xóa<br>3. Click "Khôi phục" | Tác giả được khôi phục, thông báo "Tác giả đã được khôi phục thành công", xuất hiện lại trong danh sách chính | | High | | | Tác giả đã xóa mềm | Development | | |
| 52 | Xóa tác giả | AU_DEL_007 | Khôi phục tác giả không tồn tại | Khôi phục với ID không hợp lệ | Admin đã đăng nhập | 1. Truy cập URL khôi phục với ID không tồn tại | Thông báo lỗi "Không thể khôi phục tác giả. Vui lòng thử lại sau." | | Medium | | | ID không tồn tại | Development | | |

## 6. QUẢN LÝ THÙNG RÁC

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 53 | Thùng rác | AU_TRASH_001 | Hiển thị tác giả đã xóa mềm | Xem danh sách tác giả trong thùng rác | Admin đã đăng nhập, có tác giả đã xóa mềm | 1. Click vào "Thùng rác" hoặc số thùng rác<br>2. Kiểm tra danh sách | Hiển thị danh sách tác giả đã xóa mềm với thông tin đầy đủ | | High | | | Tác giả đã xóa mềm | Development | | |
| 54 | Thùng rác | AU_TRASH_002 | Tìm kiếm tác giả đã xóa mềm | Tìm kiếm trong thùng rác | Admin đã đăng nhập, có tác giả đã xóa trong thùng rác | 1. Vào thùng rác<br>2. Nhập tên tác giả vào ô tìm kiếm<br>3. Click tìm kiếm | Hiển thị tác giả đã xóa khớp với từ khóa tìm kiếm | | Medium | | | Tác giả đã xóa có tên cụ thể | Development | | |
| 55 | Thùng rác | AU_TRASH_003 | Tìm kiếm không có kết quả | Tìm kiếm tác giả không tồn tại trong thùng rác | Admin đã đăng nhập, trong thùng rác | 1. Vào thùng rác<br>2. Tìm kiếm tên không tồn tại | Hiển thị "Không tìm thấy kết quả" hoặc danh sách trống | | Low | | | Tên không tồn tại trong thùng rác | Development | | |
| 56 | Thùng rác | AU_TRASH_004 | Thùng rác trống | Kiểm tra thùng rác khi không có tác giả nào bị xóa | Admin đã đăng nhập, chưa xóa tác giả nào | 1. Click vào thùng rác | Hiển thị thông báo "Thùng rác trống" hoặc số 0 | | Low | | | Không có tác giả bị xóa | Development | | |
| 57 | Thùng rác | AU_TRASH_005 | Kiểm tra phân trang thùng rác | Phân trang trong thùng rác | Admin đã đăng nhập, có >10 tác giả trong thùng rác | 1. Vào thùng rác<br>2. Kiểm tra phân trang | Hiển thị 10 tác giả/trang, có điều hướng trang | | Medium | | | >10 tác giả trong thùng rác | Development | | |

## 7. KIỂM TRA BẢO MẬT VÀ QUYỀN HẠN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 58 | Bảo mật | AU_SEC_001 | Truy cập không có quyền admin | User thường truy cập trang admin tác giả | User thường đã đăng nhập | 1. Đăng nhập user thường<br>2. Truy cập /admin/categories/authors | Chuyển hướng đến trang login hoặc thông báo "Không có quyền" | | Critical | | | User thường: user@test.com | Development | | |
| 59 | Bảo mật | AU_SEC_002 | Truy cập khi chưa đăng nhập | Truy cập trang admin tác giả khi chưa login | Chưa đăng nhập | 1. Không đăng nhập<br>2. Truy cập /admin/categories/authors | Chuyển hướng đến trang đăng nhập | | Critical | | | Chưa đăng nhập | Development | | |
| 60 | Bảo mật | AU_SEC_003 | CSRF Protection | Gửi form không có CSRF token | Admin đã đăng nhập | 1. Tạo form thêm tác giả<br>2. Xóa CSRF token<br>3. Submit form | Báo lỗi CSRF, không thực hiện thao tác | | High | | | Form không có CSRF token | Development | | |
| 61 | Bảo mật | AU_SEC_004 | SQL Injection trong tìm kiếm | Thử SQL injection qua ô tìm kiếm | Admin đã đăng nhập | 1. Nhập "'; DROP TABLE authors; --" vào ô tìm kiếm<br>2. Click tìm kiếm | Không thực hiện câu lệnh SQL, tìm kiếm bình thường | | High | | | SQL injection payload | Development | | |
| 62 | Bảo mật | AU_SEC_005 | XSS Prevention | Thử XSS attack qua tên tác giả | Admin đã đăng nhập | 1. Thêm tác giả với tên "&lt;script&gt;alert('XSS')&lt;/script&gt;"<br>2. Lưu và xem danh sách | Mã script không được thực thi, hiển thị dạng text | | High | | | XSS payload | Development | | |

## 8. KIỂM TRA HIỆU SUẤT VÀ GIỚI HẠN

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 63 | Hiệu suất | AU_PERF_001 | Tải trang với nhiều tác giả | Kiểm tra thời gian tải trang với DB lớn | DB có >1000 tác giả | 1. Truy cập /admin/categories/authors<br>2. Đo thời gian tải | Trang tải trong <3 giây | | Medium | | | >1000 tác giả | Development | | |
| 64 | Hiệu suất | AU_PERF_002 | Tìm kiếm với nhiều kết quả | Tìm kiếm trả về nhiều kết quả | Có từ khóa trả về >100 kết quả | 1. Tìm kiếm từ khóa phổ biến<br>2. Đo thời gian response | Kết quả trả về trong <2 giây | | Medium | | | Từ khóa phổ biến | Development | | |
| 65 | Hiệu suất | AU_PERF_003 | Upload ảnh lớn | Upload ảnh gần giới hạn 2MB | Admin đã đăng nhập | 1. Upload ảnh 1.9MB<br>2. Đo thời gian upload | Upload thành công trong <10 giây | | Medium | | | Ảnh 1.9MB | Development | | |
| 66 | Hiệu suất | AU_PERF_004 | Thao tác liên tục | Thực hiện nhiều thao tác liên tiếp | Admin đã đăng nhập | 1. Thêm, sửa, xóa liên tục 10 tác giả<br>2. Đo thời gian xử lý | Mỗi thao tác <1 giây, không bị treo | | Low | | | 10 thao tác liên tiếp | Development | | |
| 67 | Hiệu suất | AU_PERF_005 | Phân trang trang cuối | Truy cập trang cuối của phân trang | DB có >100 tác giả | 1. Truy cập trang cuối<br>2. Đo thời gian tải | Trang tải trong <2 giây | | Low | | | >100 tác giả | Development | | |

## 9. KIỂM TRA GIAO DIỆN VÀ TRẢI NGHIỆM NGƯỜI DÙNG

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 68 | UI/UX | AU_UI_001 | Responsive trên mobile | Kiểm tra giao diện trên thiết bị di động | Admin truy cập bằng mobile | 1. Truy cập bằng mobile<br>2. Kiểm tra layout | Giao diện hiển thị tốt, không bị vỡ layout, các nút bấm dễ sử dụng | | Medium | | | Mobile device | Development | | |
| 69 | UI/UX | AU_UI_002 | Responsive trên tablet | Kiểm tra giao diện trên tablet | Admin truy cập bằng tablet | 1. Truy cập bằng tablet<br>2. Kiểm tra layout | Giao diện điều chỉnh phù hợp với màn hình tablet | | Medium | | | Tablet device | Development | | |
| 70 | UI/UX | AU_UI_003 | Hiển thị ảnh tác giả | Kiểm tra hiển thị ảnh trong danh sách | Có tác giả có ảnh và không có ảnh | 1. Xem danh sách tác giả<br>2. Kiểm tra hiển thị ảnh | Tác giả có ảnh hiển thị đúng, tác giả không có ảnh hiển thị ảnh mặc định | | Medium | | | Tác giả có/không có ảnh | Development | | |
| 71 | UI/UX | AU_UI_004 | Loading indicator | Kiểm tra hiển thị loading khi xử lý | Admin thực hiện thao tác | 1. Click thêm/sửa/xóa tác giả<br>2. Quan sát màn hình | Hiển thị loading indicator trong quá trình xử lý | | Low | | | Thao tác CRUD | Development | | |
| 72 | UI/UX | AU_UI_005 | Thông báo lỗi rõ ràng | Kiểm tra thông báo lỗi dễ hiểu | Admin thực hiện thao tác sai | 1. Thử các thao tác sai<br>2. Đọc thông báo lỗi | Thông báo lỗi rõ ràng, dễ hiểu, bằng tiếng Việt | | Medium | | | Các lỗi validation | Development | | |

## 10. KIỂM TRA TÍCH HỢP VÀ LIÊN KẾT

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|--------|----------------------|---------------------|------------------|------------------|-----------------|-----------|-----------|--------------|-----------------|------------|---------|
| 73 | Tích hợp | AU_INT_001 | Liên kết với module sách | Kiểm tra quan hệ tác giả-sách | Có tác giả có sách | 1. Xem thông tin tác giả<br>2. Kiểm tra số sách | Hiển thị đúng số lượng sách của tác giả | | High | | | Tác giả có sách | Development | | |
| 74 | Tích hợp | AU_INT_002 | Lưu trữ ảnh | Kiểm tra lưu trữ file ảnh | Admin upload ảnh | 1. Upload ảnh tác giả<br>2. Kiểm tra thư mục storage | Ảnh được lưu đúng đường dẫn /storage/authors/ | | Medium | | | Ảnh upload | Development | | |
| 75 | Tích hợp | AU_INT_003 | Database transaction | Kiểm tra rollback khi có lỗi | Tạo lỗi giả trong quá trình lưu | 1. Tạo lỗi trong quá trình thêm tác giả<br>2. Kiểm tra DB | DB không bị thay đổi khi có lỗi (rollback thành công) | | High | | | Error simulation | Development | | |
| 76 | Tích hợp | AU_INT_004 | Soft delete relationship | Kiểm tra soft delete với quan hệ | Tác giả có sách bị xóa mềm | 1. Xóa mềm tác giả có sách<br>2. Kiểm tra trong DB | Tác giả không bị xóa, chỉ nhận thông báo lỗi | | High | | | Tác giả có sách | Development | | |
| 77 | Tích hợp | AU_INT_005 | File cleanup khi xóa | Kiểm tra xóa file ảnh khi xóa tác giả | Tác giả có ảnh bị xóa vĩnh viễn | 1. Xóa vĩnh viễn tác giả có ảnh<br>2. Kiểm tra thư mục storage | File ảnh được xóa khỏi thư mục storage | | Medium | | | Tác giả có ảnh | Development | | |

---

## Ghi chú về Test Cases:

### Mức độ ưu tiên:
- **Critical**: Các chức năng cốt lõi, ảnh hưởng trực tiếp đến business logic
- **High**: Các chức năng quan trọng, ảnh hưởng đến trải nghiệm người dùng  
- **Medium**: Các chức năng hỗ trợ, cần thiết nhưng không critical
- **Low**: Các chức năng tối ưu, cải thiện UX

### Dữ liệu test cần chuẩn bị:
1. **Authors**: Ít nhất 15 tác giả với tên khác nhau
2. **Books**: Một số tác giả có sách, một số không có
3. **Images**: Các file ảnh hợp lệ và không hợp lệ để test upload
4. **Deleted Authors**: Một số tác giả đã xóa mềm để test thùng rác
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
- **Tên tác giả**: "Nguyễn Du", "Hồ Chí Minh", "Tố Hữu", "Xuân Diệu"
- **File ảnh hợp lệ**: JPG/PNG dưới 2MB
- **File ảnh không hợp lệ**: TXT, PDF, ảnh >2MB
- **Tên đặc biệt**: Có dấu, có số, có ký tự đặc biệt
- **Tiểu sử**: Văn bản dài, có ký tự Unicode
