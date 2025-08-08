# TEST CASES - QUẢN LÝ SÁCH BÊN ADMIN

**Dự án**: BookBee - Hệ thống bán sách online  
**Module**: Admin - Quản lý sách (AdminBookController)  
**Ngày tạo**: 28/07/2025  
**Người tạo**: Admin QA Team  

## 📋 THÔNG TIN CHUNG

**Controller**: `AdminBookController.php`  
**Routes**: `/admin/books/*`  
**Model chính**: `Book`  
**Models liên quan**: `BookFormat`, `BookImage`, `BookAttributeValue`, `BookGift`, `Author`, `Brand`, `Category`, `Attribute`  

## 🎯 CHỨC NĂNG CHÍNH

1. **Danh sách sách** (index) - Xem, tìm kiếm, lọc sách
2. **Thêm sách mới** (create/store) - Tạo sách với đầy đủ thông tin  
3. **Xem chi tiết** (show) - Hiển thị thông tin chi tiết sách
4. **Chỉnh sửa** (edit/update) - Cập nhật thông tin sách
5. **Xóa tạm thời** (destroy) - Chuyển sách vào thùng rác
6. **Quản lý thùng rác** (trash) - Xem sách đã xóa
7. **Khôi phục** (restore) - Khôi phục sách từ thùng rác  
8. **Xóa vĩnh viễn** (forceDelete) - Xóa hoàn toàn sách

---

## 📊 BẢNG TEST CASES

| STT | Chức năng | Test Case ID | Mô tả | Dữ liệu đầu vào | Kết quả mong đợi | Độ ưu tiên | Loại test |
|-----|-----------|--------------|--------|----------------|------------------|------------|-----------|
| **DANH SÁCH SÁCH** | | | | | | | |
| 1 | Hiển thị danh sách | TC_BOOKS_001 | Hiển thị tất cả sách có phân trang | URL: `/admin/books` | Hiển thị danh sách 10 sách/trang với thông tin đầy đủ | Cao | Functional |
| 2 | Tìm kiếm theo tên | TC_BOOKS_002 | Tìm kiếm sách theo tiêu đề | search="Harry Potter" | Hiển thị các sách có tiêu đề chứa "Harry Potter" | Cao | Functional |
| 3 | Tìm kiếm theo ISBN | TC_BOOKS_003 | Tìm kiếm sách theo mã ISBN | search="9781234567890" | Hiển thị sách có ISBN trùng khớp | Cao | Functional |
| 4 | Lọc theo danh mục | TC_BOOKS_004 | Lọc sách theo category | category="uuid-category-id" | Hiển thị các sách thuộc danh mục đó | Trung bình | Functional |
| 5 | Lọc theo thương hiệu | TC_BOOKS_005 | Lọc sách theo brand | brand="uuid-brand-id" | Hiển thị các sách thuộc thương hiệu đó | Trung bình | Functional |
| 6 | Lọc theo tác giả | TC_BOOKS_006 | Lọc sách theo author | author="uuid-author-id" | Hiển thị các sách của tác giả đó | Trung bình | Functional |
| 7 | Lọc theo số trang | TC_BOOKS_007 | Lọc theo khoảng số trang | min_pages=100, max_pages=500 | Hiển thị sách có 100-500 trang | Thấp | Functional |
| 8 | Lọc theo trạng thái | TC_BOOKS_008 | Lọc sách theo status | status="available" | Hiển thị sách có trạng thái tương ứng | Trung bình | Functional |
| 9 | Lọc theo giá | TC_BOOKS_009 | Lọc theo khoảng giá format | min_price=100000, max_price=500000 | Hiển thị sách trong khoảng giá đó | Trung bình | Functional |
| 10 | Sắp xếp theo trang | TC_BOOKS_010 | Sắp xếp theo số trang | sort="pages_asc" | Sách được sắp xếp tăng dần theo số trang | Thấp | Functional |
| 11 | Sắp xếp theo giá | TC_BOOKS_011 | Sắp xếp theo giá | sort="price_desc" | Sách được sắp xếp giảm dần theo giá | Thấp | Functional |
| **THÊM SÁCH MỚI** | | | | | | | |
| 12 | Form thêm sách | TC_BOOKS_012 | Hiển thị form tạo sách mới | URL: `/admin/books/create` | Form hiển thị đầy đủ các trường cần thiết | Cao | Functional |
| 13 | Thêm sách hợp lệ | TC_BOOKS_013 | Thêm sách với đữ liệu hợp lệ | Tất cả trường bắt buộc hợp lệ, có ảnh bìa | Tạo sách thành công, chuyển về danh sách | Cao | Functional |
| 14 | Thiếu tiêu đề | TC_BOOKS_014 | Thêm sách không có tiêu đề | title="" | Hiển thị lỗi "Vui lòng nhập tiêu đề sách" | Cao | Validation |
| 15 | Tiêu đề trùng lặp | TC_BOOKS_015 | Thêm sách với tiêu đề đã tồn tại | title="Sách đã có trong DB" | Hiển thị lỗi "Tiêu đề sách đã tồn tại" | Cao | Validation |
| 16 | Thiếu ISBN | TC_BOOKS_016 | Thêm sách không có ISBN | isbn="" | Hiển thị lỗi "Vui lòng nhập mã ISBN" | Cao | Validation |
| 17 | Thiếu số trang | TC_BOOKS_017 | Thêm sách không có page_count | page_count="" | Hiển thị lỗi "Vui lòng nhập số trang" | Cao | Validation |
| 18 | Thiếu danh mục | TC_BOOKS_018 | Thêm sách không chọn category | category_id="" | Hiển thị lỗi "Vui lòng chọn danh mục" | Cao | Validation |
| 19 | Thiếu tác giả | TC_BOOKS_019 | Thêm sách không chọn tác giả | author_ids=[] | Hiển thị lỗi "Vui lòng chọn ít nhất một tác giả" | Cao | Validation |
| 20 | Thiếu thương hiệu | TC_BOOKS_020 | Thêm sách không chọn brand | brand_id="" | Hiển thị lỗi "Vui lòng chọn thương hiệu" | Cao | Validation |
| 21 | Thiếu ngày xuất bản | TC_BOOKS_021 | Thêm sách không có publication_date | publication_date="" | Hiển thị lỗi "Vui lòng nhập ngày xuất bản" | Cao | Validation |
| 22 | Thiếu ảnh bìa | TC_BOOKS_022 | Thêm sách không có cover_image | cover_image=null | Hiển thị lỗi "Vui lòng chọn ảnh bìa cho sách" | Cao | Validation |
| 23 | Ảnh bìa không hợp lệ | TC_BOOKS_023 | Upload file không phải ảnh | cover_image="file.txt" | Hiển thị lỗi "File ảnh bìa không hợp lệ" | Cao | Validation |
| 24 | Ảnh bìa quá lớn | TC_BOOKS_024 | Upload ảnh > 2MB | cover_image=3MB | Hiển thị lỗi "Kích thước ảnh bìa không được vượt quá 2MB" | Trung bình | Validation |
| 25 | Định dạng sách vật lý | TC_BOOKS_025 | Thêm sách chỉ có định dạng vật lý | has_physical=true, giá+stock hợp lệ | Tạo sách với BookFormat "Sách Vật Lý" | Cao | Functional |
| 26 | Định dạng ebook | TC_BOOKS_026 | Thêm sách chỉ có định dạng ebook | has_ebook=true, file PDF/EPUB | Tạo sách với BookFormat "Ebook" | Cao | Functional |
| 27 | Cả hai định dạng | TC_BOOKS_027 | Thêm sách có cả 2 định dạng | has_physical=true, has_ebook=true | Tạo sách với 2 BookFormat | Trung bình | Functional |
| 28 | File ebook không hợp lệ | TC_BOOKS_028 | Upload file ebook không đúng định dạng | ebook_file="file.doc" | Hiển thị lỗi "File ebook phải có định dạng PDF hoặc EPUB" | Cao | Validation |
| 29 | File ebook quá lớn | TC_BOOKS_029 | Upload file ebook > 50MB | ebook_file=60MB | Hiển thị lỗi "Kích thước file ebook không được vượt quá 50MB" | Trung bình | Validation |
| 30 | Thêm ảnh phụ | TC_BOOKS_030 | Thêm sách có nhiều ảnh phụ | images=[image1.jpg, image2.png] | Tạo các BookImage tương ứng | Trung bình | Functional |
| 31 | Thêm thuộc tính | TC_BOOKS_031 | Thêm sách có thuộc tính và giá thêm | attribute_values=[{id:1, extra_price:5000}] | Tạo BookAttributeValue tương ứng | Trung bình | Functional |
| 32 | Thuộc tính trùng lặp | TC_BOOKS_032 | Chọn trùng thuộc tính cho sách | attribute_values có ID trùng | Hiển thị lỗi "Không được chọn trùng thuộc tính" | Trung bình | Validation |
| 33 | Thêm quà tặng | TC_BOOKS_033 | Thêm sách có quà tặng | gift_name, gift_description, quantity | Tạo BookGift tương ứng | Thấp | Functional |
| **XEM CHI TIẾT SÁCH** | | | | | | | |
| 34 | Xem chi tiết hợp lệ | TC_BOOKS_034 | Xem chi tiết sách tồn tại | URL: `/admin/books/{id}/{slug}` | Hiển thị đầy đủ thông tin sách và relationships | Cao | Functional |
| 35 | Sách không tồn tại | TC_BOOKS_035 | Xem chi tiết sách không tồn tại | id="non-existent-uuid" | Hiển thị lỗi 404 Not Found | Cao | Exception |
| 36 | Hiển thị đánh giá | TC_BOOKS_036 | Xem chi tiết với đánh giá | Sách có reviews | Hiển thị rating trung bình và số lượng review | Trung bình | Functional |
| 37 | Hiển thị thuộc tính | TC_BOOKS_037 | Xem chi tiết với thuộc tính | Sách có attributes | Hiển thị attributes nhóm theo tên và giá thêm | Trung bình | Functional |
| **CHỈNH SỬA SÁCH** | | | | | | | |
| 38 | Form chỉnh sửa | TC_BOOKS_038 | Hiển thị form edit | URL: `/admin/books/{id}/{slug}/edit` | Form hiển thị với dữ liệu hiện tại đã điền | Cao | Functional |
| 39 | Cập nhật hợp lệ | TC_BOOKS_039 | Cập nhật sách với dữ liệu hợp lệ | Các trường hợp lệ | Cập nhật thành công, chuyển về danh sách | Cao | Functional |
| 40 | Cập nhật thiếu trường | TC_BOOKS_040 | Cập nhật với thiếu trường bắt buộc | title="" | Hiển thị lỗi validation tương ứng | Cao | Validation |
| 41 | Slug trùng lặp khi edit | TC_BOOKS_041 | Đổi title thành title đã tồn tại | title trùng với sách khác | Hiển thị lỗi "Tiêu đề sách đã tồn tại" | Cao | Validation |
| 42 | Thay đổi ảnh bìa | TC_BOOKS_042 | Cập nhật ảnh bìa mới | cover_image=new_image.jpg | Xóa ảnh cũ, lưu ảnh mới | Trung bình | Functional |
| 43 | Thêm ảnh phụ mới | TC_BOOKS_043 | Thêm ảnh phụ khi edit | images=[new_image.jpg] | Thêm BookImage mới không xóa ảnh cũ | Trung bình | Functional |
| 44 | Xóa ảnh phụ | TC_BOOKS_044 | Xóa ảnh phụ đã chọn | delete_images=[image_id] | Xóa file và record BookImage | Trung bình | Functional |
| 45 | Thay đổi định dạng | TC_BOOKS_045 | Từ vật lý sang ebook | has_physical=false, has_ebook=true | Xóa BookFormat vật lý, tạo ebook format | Trung bình | Functional |
| 46 | Cập nhật file ebook | TC_BOOKS_046 | Thay đổi file ebook | formats.ebook.file=new_ebook.pdf | Xóa file cũ, lưu file mới | Trung bình | Functional |
| 47 | Cập nhật thuộc tính | TC_BOOKS_047 | Thay đổi thuộc tính sách | attribute_values mới | Xóa tất cả BookAttributeValue cũ, tạo mới | Trung bình | Functional |
| 48 | Cập nhật tác giả | TC_BOOKS_048 | Thay đổi danh sách tác giả | author_ids=[new_authors] | Đồng bộ lại quan hệ many-to-many | Trung bình | Functional |
| **XÓA SÁCH** | | | | | | | |
| 49 | Xóa tạm thời | TC_BOOKS_049 | Xóa sách vào thùng rác | DELETE /admin/books/{id} | Sách được soft delete, hiển thị thông báo thành công | Cao | Functional |
| 50 | Xóa sách không tồn tại | TC_BOOKS_050 | Xóa sách không tồn tại | id="non-existent-uuid" | Hiển thị lỗi 404 Not Found | Cao | Exception |
| **QUẢN LÝ THÙNG RÁC** | | | | | | | |
| 51 | Danh sách thùng rác | TC_BOOKS_051 | Xem sách đã xóa | URL: `/admin/books/trash` | Hiển thị các sách đã bị soft delete | Trung bình | Functional |
| 52 | Tìm kiếm trong thùng rác | TC_BOOKS_052 | Tìm kiếm sách đã xóa | search="book_title" | Tìm kiếm trong các sách đã xóa | Thấp | Functional |
| 53 | Khôi phục sách | TC_BOOKS_053 | Khôi phục sách từ thùng rác | POST /admin/books/{id}/restore | Sách được khôi phục, hiển thị lại ở danh sách chính | Trung bình | Functional |
| 54 | Khôi phục sách không tồn tại | TC_BOOKS_054 | Khôi phục sách không có trong thùng rác | id="non-existent-uuid" | Hiển thị lỗi 404 Not Found | Trung bình | Exception |
| 55 | Xóa vĩnh viễn | TC_BOOKS_055 | Xóa hoàn toàn sách | DELETE /admin/books/{id}/force-delete | Xóa toàn bộ dữ liệu và file liên quan | Cao | Functional |
| 56 | Xóa vĩnh viễn có đơn hàng | TC_BOOKS_056 | Xóa sách đã có trong đơn hàng | Sách có orderItems | Hiển thị lỗi "Không thể xóa vĩnh viễn sách này vì đã có đơn hàng liên quan" | Cao | Business Logic |
| **BẢO MẬT & PHÂN QUYỀN** | | | | | | | |
| 57 | Truy cập không đăng nhập | TC_BOOKS_057 | Truy cập khi chưa đăng nhập | Không có session | Chuyển hướng đến trang đăng nhập | Cao | Security |
| 58 | Truy cập không phải admin | TC_BOOKS_058 | User thường truy cập admin | role="user" | Hiển thị lỗi 403 Forbidden | Cao | Security |
| 59 | CSRF Protection | TC_BOOKS_059 | Gửi form không có CSRF token | _token="" | Hiển thị lỗi 419 Page Expired | Cao | Security |
| **HIỆU SUẤT** | | | | | | | |
| 60 | Tải trang với nhiều sách | TC_BOOKS_060 | Danh sách với 1000+ sách | Database có nhiều records | Trang tải < 3 giây với pagination | Trung bình | Performance |
| 61 | Upload file lớn | TC_BOOKS_061 | Upload ebook 45MB | File ebook gần giới hạn | Upload thành công trong thời gian hợp lý | Thấp | Performance |
| 62 | Tìm kiếm phức tạp | TC_BOOKS_062 | Kết hợp nhiều bộ lọc | search + category + brand + author + price | Kết quả trả về chính xác và nhanh chóng | Trung bình | Performance |
| **TÍCH HỢP** | | | | | | | |
| 63 | Tích hợp Storage | TC_BOOKS_063 | Lưu file vào storage | Upload ảnh và ebook | File được lưu đúng thư mục public/storage | Cao | Integration |
| 64 | Tích hợp Toastr | TC_BOOKS_064 | Hiển thị thông báo | Thao tác thành công/lỗi | Hiển thị toastr notification | Thấp | Integration |
| 65 | Tích hợp Slugify | TC_BOOKS_065 | Tự động tạo slug | title="Sách Hay Nhất" | slug="sach-hay-nhat" | Trung bình | Integration |
| 66 | Quan hệ Database | TC_BOOKS_066 | Load relationships | Sách có đầy đủ quan hệ | Eager loading hoạt động chính xác | Cao | Integration |

---

## 🔧 THIẾT LẬP TESTING

### Môi trường test:
```bash
# Tạo database test
php artisan migrate --env=testing

# Seed dữ liệu test
php artisan db:seed --env=testing

# Chạy test
php artisan test --filter AdminBookControllerTest
```

### Dữ liệu test cần chuẩn bị:
- Categories: Ít nhất 5 danh mục
- Brands: Ít nhất 5 thương hiệu  
- Authors: Ít nhất 10 tác giả
- Attributes: Màu sắc, Kích thước, Chất liệu...
- Test files: Ảnh JPG/PNG < 2MB, PDF/EPUB < 50MB
- Books: Ít nhất 20 sách để test pagination và tìm kiếm

### Commands để chạy automated tests:
```bash
# Test cơ bản
php artisan test tests/Feature/Admin/AdminBookControllerTest.php

# Test với coverage
php artisan test --coverage tests/Feature/Admin/

# Test performance  
php artisan test tests/Performance/AdminBookPerformanceTest.php
```

---

## ✅ KIỂM TRA VALIDATION

### Trường bắt buộc:
- ✅ title (required|string|max:255)
- ✅ isbn (required|string|max:20)  
- ✅ page_count (required|integer)
- ✅ category_id (required|uuid|exists:categories,id)
- ✅ author_ids (required|array|min:1)
- ✅ brand_id (required|uuid|exists:brands,id)
- ✅ publication_date (required|date)
- ✅ cover_image (required|image|max:2048) - store only
- ✅ status (required|string|max:50)

### Validation có điều kiện:
- ✅ formats.physical.price (required_if:has_physical,true)
- ✅ formats.physical.stock (required_if:has_physical,true)  
- ✅ formats.ebook.price (required_if:has_ebook,true)
- ✅ formats.ebook.file (required_if:has_ebook,true) - store only

### File upload validation:
- ✅ cover_image: image|mimes:jpeg,png,jpg,gif,webp|max:2048
- ✅ ebook file: mimes:pdf,epub|max:50000
- ✅ sample file: mimes:pdf,epub|max:10000
- ✅ images.*: image|mimes:jpeg,png,jpg,gif,webp|max:2048

---

**Tổng số test cases: 66**  
**Ưu tiên Cao: 32 | Trung bình: 27 | Thấp: 7**  
**Functional: 40 | Validation: 16 | Security: 3 | Performance: 3 | Integration: 4**
