# ADMIN - Quản lý tin tức - Test Cases

## Thông tin chung
- **Module**: Quản lý tin tức (News Article Management)
- **Controller**: App\Http\Controllers\Admin\NewsArticleController  
- **Model**: App\Models\NewsArticle
- **Routes**: /admin/news/*
- **Views**: resources/views/admin/news/*

## Chi tiết Test Cases

| STT | Chức năng | Test Case ID | Tên Kiểm Thử | Mô Tả | Điều Kiện Tiên Quyết | Các Bước Thực Hiện | Kết Quả Dự Kiến | Kết Quả Thực Tế | Mức độ ưu tiên | Người Test | Ngày Test | Dữ Liệu Test | Môi Trường Test | Trạng Thái | Ghi Chú |
|-----|-----------|--------------|---------------|-------|---------------------|-------------------|----------------|-----------------|----------------|------------|-----------|-------------|----------------|------------|---------|
| 1 | Xem danh sách tin tức | TC_NEWS_01 | Hiển thị danh sách tin tức | Kiểm tra việc hiển thị danh sách tất cả tin tức với phân trang | Đã đăng nhập admin, có ít nhất 1 tin tức trong DB | B1: Truy cập `/admin/news` → B2: Quan sát danh sách hiển thị | Hiển thị bảng tin tức với các cột: STT, Ảnh, Tiêu đề, Danh mục, Trạng thái, Ngày tạo, Thao tác. Có phân trang 10 records/page |  | Cao | | | NewsArticle có đầy đủ field: title, thumbnail, summary, content, category, is_featured | Local | | |
| 2 | | TC_NEWS_02 | Tìm kiếm tin tức theo tiêu đề | Tìm kiếm tin tức bằng tiêu đề bài viết | Có nhiều tin tức với tiêu đề khác nhau | B1: Vào `/admin/news` → B2: Nhập tiêu đề vào ô "Tìm kiếm tin tức..." → B3: Nhấn Enter hoặc Click ngoài ô | Hiển thị chỉ những tin tức có tiêu đề chứa từ khóa tìm kiếm, không phân biệt hoa thường |  | Cao | | | Title test: "Top 10 cuốn sách", "Kinh doanh hiệu quả" | Local | | |
| 3 | | TC_NEWS_03 | Tìm kiếm tin tức theo danh mục | Tìm kiếm tin tức bằng tên danh mục | Có nhiều tin tức với danh mục khác nhau | B1: Vào `/admin/news` → B2: Nhập danh mục vào ô tìm kiếm → B3: Submit | Hiển thị chỉ những tin tức có danh mục chứa từ khóa tìm kiếm |  | Cao | | | Category test: "Sách", "Kinh doanh", "Sức khỏe" | Local | | |
| 4 | | TC_NEWS_04 | Lọc theo danh mục | Lọc tin tức theo danh mục cụ thể | Có tin tức với các danh mục khác nhau | B1: Vào `/admin/news` → B2: Chọn danh mục từ dropdown "Tất cả danh mục" → B3: Submit | Hiển thị chỉ tin tức thuộc danh mục được chọn |  | Cao | | | Các danh mục: Books, Health, Business, Children | Local | | |
| 5 | | TC_NEWS_05 | Lọc theo trạng thái nổi bật | Lọc tin tức theo trạng thái is_featured | Có tin tức nổi bật và không nổi bật | B1: Vào `/admin/news` → B2: Chọn "Nổi bật" hoặc "Thường" → B3: Submit | Hiển thị chỉ tin tức có trạng thái is_featured tương ứng |  | Trung bình | | | is_featured: true/false | Local | | |
| 6 | | TC_NEWS_06 | Đặt lại bộ lọc | Reset tất cả bộ lọc về trạng thái ban đầu | Đã áp dụng bộ lọc hoặc tìm kiếm | B1: Sau khi lọc/tìm kiếm → B2: Nhấn nút "Đặt lại" | Trở về trang danh sách ban đầu, hiển thị tất cả tin tức, các ô lọc được reset |  | Trung bình | | | | Local | | |
| 7 | Thêm tin tức mới | TC_NEWS_07 | Tạo tin tức thành công | Tạo mới một tin tức với đầy đủ thông tin hợp lệ | Admin đã đăng nhập | B1: Nhấn "Thêm tin tức" → B2: Nhập đầy đủ: title, category, summary, content, thumbnail → B3: Nhấn "Tạo tin tức" | Tin tức được tạo thành công, redirect về danh sách, hiển thị thông báo "Tin tức đã được tạo thành công!" |  | Cao | | | Title unique, image <2MB, summary <200 chars | Local | | |
| 8 | | TC_NEWS_08 | Validation - Tiêu đề trống | Kiểm tra validation khi để trống tiêu đề | Truy cập form tạo tin tức | B1: Vào form tạo → B2: Để trống tiêu đề → B3: Nhập các field khác → B4: Submit | Hiển thị lỗi "Tiêu đề bài viết là bắt buộc", form không submit |  | Cao | | | | Local | | |
| 9 | | TC_NEWS_09 | Validation - Tiêu đề trùng lặp | Kiểm tra validation khi tiêu đề đã tồn tại | Đã có tin tức với tiêu đề "Test Article" | B1: Tạo tin tức mới → B2: Nhập tiêu đề "Test Article" → B3: Submit | Hiển thị lỗi "Tiêu đề bài viết đã tồn tại", form không submit |  | Cao | | | Existing title: "Test Article" | Local | | |
| 10 | | TC_NEWS_10 | Validation - Ảnh quá kích thước | Kiểm tra validation khi upload ảnh >2MB | File ảnh >2MB | B1: Vào form tạo → B2: Upload ảnh >2MB → B3: Submit | Hiển thị lỗi "Ảnh không được vượt quá 2MB", form không submit |  | Trung bình | | | Image file >2MB | Local | | |
| 11 | | TC_NEWS_11 | Validation - Tóm tắt quá dài | Kiểm tra validation khi summary >200 ký tự | Form tạo tin tức | B1: Nhập summary >200 ký tự → B2: Submit | Hiển thị lỗi "Tóm tắt không được vượt quá 200 ký tự" |  | Trung bình | | | Summary >200 characters | Local | | |
| 12 | | TC_NEWS_12 | Tạo tin tức nổi bật | Tạo tin tức với checkbox "Bài viết nổi bật" | Form tạo tin tức | B1: Điền form → B2: Check "Bài viết nổi bật" → B3: Submit | Tin tức được tạo với is_featured=true, hiển thị badge "Nổi bật" |  | Trung bình | | | is_featured checkbox | Local | | |
| 13 | Xem chi tiết tin tức | TC_NEWS_13 | Xem thông tin chi tiết | Kiểm tra việc hiển thị đầy đủ thông tin chi tiết tin tức | Có ít nhất 1 tin tức | B1: Vào danh sách tin tức → B2: Nhấn nút "Xem" (icon mắt) → B3: Quan sát trang chi tiết | Hiển thị đầy đủ: Tiêu đề, Ảnh, Tóm tắt, Nội dung, Danh mục, ID, Ngày tạo, Ngày cập nhật, Trạng thái nổi bật |  | Cao | | | NewsArticle đầy đủ thông tin | Local | | |
| 14 | Chỉnh sửa tin tức | TC_NEWS_14 | Cập nhật tin tức thành công | Chỉnh sửa thông tin tin tức | Có tin tức để chỉnh sửa | B1: Nhấn nút "Sửa" → B2: Thay đổi title, summary, content → B3: Nhấn "Cập nhật" | Tin tức được cập nhật, redirect về danh sách, thông báo "Tin tức đã được cập nhật thành công!" |  | Cao | | | Valid update data | Local | | |
| 15 | | TC_NEWS_15 | Cập nhật ảnh tin tức | Thay đổi ảnh đại diện của tin tức | Tin tức có ảnh cũ | B1: Vào form edit → B2: Upload ảnh mới → B3: Cập nhật | Ảnh cũ bị xóa, ảnh mới được lưu và hiển thị |  | Cao | | | New image file <2MB | Local | | |
| 16 | | TC_NEWS_16 | Cập nhật không thay đổi gì | Kiểm tra khi submit form mà không thay đổi dữ liệu | Tin tức hiện tại | B1: Vào form edit → B2: Không thay đổi gì → B3: Submit | Hiển thị thông báo "Không có thay đổi nào được thực hiện", sử dụng isDirty() check |  | Trung bình | | | No data changes | Local | | |
| 17 | | TC_NEWS_17 | Validation edit - Tiêu đề trùng | Chỉnh sửa với tiêu đề đã tồn tại (khác ID hiện tại) | 2 tin tức A, B khác nhau | B1: Edit tin tức A → B2: Đặt tiêu đề giống tin tức B → B3: Submit | Hiển thị lỗi "Tiêu đề bài viết đã tồn tại", không cập nhật |  | Cao | | | Title exists in different article | Local | | |
| 18 | | TC_NEWS_18 | Toggle trạng thái nổi bật | Thay đổi trạng thái is_featured | Tin tức không nổi bật | B1: Edit tin tức → B2: Check/Uncheck "Bài viết nổi bật" → B3: Update | Trạng thái is_featured được thay đổi, hiển thị/ẩn badge "Nổi bật" |  | Trung bình | | | Toggle is_featured checkbox | Local | | |
| 19 | Xóa tin tức | TC_NEWS_19 | Xóa tin tức thành công | Xóa tin tức khỏi hệ thống | Có ít nhất 1 tin tức | B1: Tại danh sách → B2: Nhấn nút "Xóa" (icon thùng rác) → B3: Confirm "OK" | Tin tức bị xóa khỏi DB, ảnh thumbnail bị xóa khỏi storage, thông báo "Tin tức đã được xóa thành công!" |  | Cao | | | NewsArticle with thumbnail | Local | | |
| 20 | | TC_NEWS_20 | Hủy xóa tin tức | Hủy thao tác xóa | Có tin tức muốn xóa | B1: Nhấn "Xóa" → B2: Nhấn "Cancel" trong confirm dialog | Tin tức không bị xóa, ở lại trang danh sách |  | Trung bình | | | | Local | | |
| 21 | | TC_NEWS_21 | Xử lý lỗi khi xóa | Xử lý exception khi xóa không thành công | Tin tức bị khóa bởi foreign key hoặc file system error | B1: Tạo điều kiện lỗi → B2: Thực hiện xóa | Hiển thị thông báo "Có lỗi xảy ra khi xóa tin tức!", tin tức không bị xóa |  | Thấp | | | Exception scenario | Local | | |
| 22 | File Management | TC_NEWS_22 | Upload ảnh hợp lệ | Upload các định dạng ảnh được hỗ trợ | Files: jpg, png, gif, svg, webp | B1: Upload từng loại file → B2: Submit | Tất cả các định dạng được chấp nhận, lưu vào storage/articles/ |  | Trung bình | | | Valid image formats | Local | | |
| 23 | | TC_NEWS_23 | Upload file không phải ảnh | Upload file không phải ảnh (pdf, doc, txt) | File không phải ảnh | B1: Upload file .pdf/.doc → B2: Submit | Hiển thị lỗi "Tệp tải lên phải là ảnh hợp lệ" |  | Trung bình | | | Non-image files | Local | | |
| 24 | | TC_NEWS_24 | Xóa ảnh cũ khi cập nhật | Kiểm tra xóa ảnh cũ khi upload ảnh mới | Tin tức có ảnh cũ | B1: Edit tin tức → B2: Upload ảnh mới → B3: Update | Ảnh cũ bị xóa khỏi storage, ảnh mới được lưu |  | Cao | | | Article with existing thumbnail | Local | | |
| 25 | Error Handling | TC_NEWS_25 | Xử lý lỗi khi tạo | Exception handling trong store method | Tạo điều kiện lỗi (DB error, storage error) | B1: Tạo điều kiện lỗi → B2: Submit form tạo | Hiển thị "Có lỗi xảy ra khi tạo tin tức!", rollback thumbnail nếu đã upload |  | Trung bình | | | Exception scenario | Local | | |
| 26 | | TC_NEWS_26 | Xử lý lỗi khi cập nhật | Exception handling trong update method | Điều kiện lỗi khi update | B1: Tạo lỗi → B2: Submit form edit | Hiển thị "Có lỗi xảy ra khi cập nhật tin tức!", rollback ảnh mới nếu đã upload |  | Trung bình | | | Exception scenario | Local | | |
| 27 | Phân trang & Sắp xếp | TC_NEWS_27 | Kiểm tra phân trang | Kiểm tra hoạt động của phân trang | Có >10 tin tức trong DB | B1: Vào danh sách tin tức → B2: Quan sát phân trang → B3: Nhấn trang 2, 3... | Mỗi trang hiển thị tối đa 10 records, navigation phân trang hoạt động đúng |  | Trung bình | | | 25+ NewsArticle records | Local | | |
| 28 | | TC_NEWS_28 | Sắp xếp theo ngày tạo | Kiểm tra thứ tự hiển thị | Có nhiều tin tức với thời gian tạo khác nhau | B1: Vào danh sách tin tức → B2: Quan sát thứ tự | Tin tức được sắp xếp theo created_at DESC (mới nhất lên đầu) |  | Trung bình | | | Articles với thời gian khác nhau | Local | | |
| 29 | | TC_NEWS_29 | Giữ query string khi phân trang | Kiểm tra preserve search/filter khi chuyển trang | Đã áp dụng filter/search | B1: Lọc theo danh mục → B2: Chuyển sang trang 2 → B3: Quan sát | Bộ lọc được giữ nguyên khi chuyển trang (withQueryString) |  | Trung bình | | | Filtered results với multiple pages | Local | | |
| 30 | UI/UX | TC_NEWS_30 | Hiển thị ảnh placeholder | Xử lý khi ảnh không tồn tại hoặc bị lỗi | Tin tức có thumbnail path không hợp lệ | B1: Vào danh sách → B2: Quan sát ảnh bị lỗi | Hiển thị ảnh placeholder "No Image" khi ảnh lỗi (onerror handler) |  | Thấp | | | Invalid thumbnail path | Local | | |
| 31 | | TC_NEWS_31 | Responsive design | Kiểm tra giao diện responsive | Truy cập từ thiết bị mobile | B1: Mở admin panel trên mobile → B2: Vào quản lý tin tức → B3: Thực hiện các thao tác | Giao diện hiển thị đúng, bảng responsive, các nút hoạt động bình thường |  | Thấp | | | Mobile device/responsive mode | Local | | |
| 32 | | TC_NEWS_32 | Hiển thị badge trạng thái | Kiểm tra hiển thị trạng thái nổi bật | Có tin tức nổi bật và thường | B1: Vào danh sách → B2: Quan sát cột trạng thái | Tin tức nổi bật hiển thị badge success "Nổi bật", tin tức thường hiển thị badge secondary "Thường" |  | Thấp | | | Mix of featured & normal articles | Local | | |
| 33 | Security | TC_NEWS_33 | Truy cập không có quyền | Kiểm tra phân quyền admin | User không phải admin | B1: Logout admin → B2: Login user thường → B3: Truy cập `/admin/news` | Redirect về trang login hoặc hiển thị lỗi 403 Forbidden |  | Cao | | | User role = "user" | Local | | |
| 34 | | TC_NEWS_34 | CSRF Protection | Kiểm tra bảo mật CSRF token | Admin đã login | B1: Gửi request tạo/sửa/xóa không có CSRF token | Request bị từ chối với lỗi 419 CSRF Token Mismatch |  | Cao | | | Request without _token | Local | | |
| 35 | | TC_NEWS_35 | XSS Prevention | Kiểm tra chống XSS trong content | Admin tạo tin tức | B1: Nhập script tag trong content → B2: Submit → B3: Xem chi tiết | Script được escape/sanitize, không thực thi khi hiển thị |  | Cao | | | Content: `<script>alert('xss')</script>` | Local | | |
| 36 | Performance | TC_NEWS_36 | Load trang với nhiều dữ liệu | Kiểm tra hiệu suất khi có nhiều tin tức | DB có >1000 tin tức | B1: Vào danh sách tin tức → B2: Đo thời gian load | Trang load trong <3 giây, phân trang hoạt động mượt mà |  | Trung bình | | | 1000+ NewsArticle records | Local | | |
| 37 | | TC_NEWS_37 | Tối ưu query với distinct categories | Kiểm tra performance query lấy categories | DB có nhiều tin tức | B1: Vào trang index/create → B2: Kiểm tra query log | Query `distinct().pluck('category')` chỉ chạy 1 lần, không N+1 problem |  | Thấp | | | Query monitoring | Local | | |
| 38 | Integration | TC_NEWS_38 | Tích hợp với storage system | Kiểm tra tích hợp với Laravel Storage | Storage system hoạt động | B1: Upload ảnh → B2: Kiểm tra file system | File được lưu vào `storage/app/public/articles/`, accessible qua symlink |  | Cao | | | Storage symlink exists | Local | | |
| 39 | | TC_NEWS_39 | Tích hợp với Toastr notifications | Kiểm tra thông báo toast | Thực hiện các actions | B1: Tạo/sửa/xóa tin tức → B2: Quan sát thông báo | Hiển thị đúng thông báo success/error với Toastr library |  | Trung bình | | | Toastr library loaded | Local | | |
| 40 | Route Model Binding | TC_NEWS_40 | Route model binding cho show/edit/delete | Kiểm tra route model binding | Có tin tức với ID hợp lệ | B1: Truy cập `/admin/news/show/{id}` → B2: Kiểm tra tự động load model | Model NewsArticle được tự động inject, không cần manual findOrFail |  | Thấp | | | Valid NewsArticle ID | Local | | |
| 41 | | TC_NEWS_41 | 404 khi model không tồn tại | Xử lý khi ID không tồn tại | ID không hợp lệ | B1: Truy cập URL với ID không tồn tại | Hiển thị lỗi 404 Model Not Found |  | Thấp | | | Non-existent ID: 999999 | Local | | |

## Lưu ý về Test Environment

### Database Schema cần thiết:
```sql
news_articles table:
- id (uuid, primary key)
- title (varchar, unique)
- thumbnail (varchar, nullable) -- path to image
- summary (text, max 200 chars)
- content (longtext)
- category (varchar 50)
- is_featured (boolean, default false)
- created_at (timestamp)
- updated_at (timestamp)
```

### Test Data Setup:
```php
// Tạo tin tức với các trạng thái khác nhau
NewsArticle::factory()->create(['title' => 'Test Article 1', 'is_featured' => true]);
NewsArticle::factory()->create(['title' => 'Test Article 2', 'category' => 'Sách']);
NewsArticle::factory()->create(['title' => 'Test Article 3', 'category' => 'Kinh doanh']);
NewsArticle::factory()->create(['title' => 'Test Article 4', 'is_featured' => false]);
```

### Environment Config:
```env
# Storage
FILESYSTEM_DISK=public

# File upload limits
UPLOAD_MAX_FILESIZE=2M
POST_MAX_SIZE=8M
```

### Required Files/Directories:
```bash
# Tạo symbolic link cho storage
php artisan storage:link

# Tạo thư mục articles
mkdir storage/app/public/articles

# Set permissions
chmod 755 storage/app/public/articles
```

## Validation Rules Summary:
- **title**: required, unique, max:255
- **category**: required, max:50
- **summary**: required, max:200
- **content**: required
- **thumbnail**: required (store), nullable (update), image, max:2048 (2MB)
- **is_featured**: boolean

## Error Handling:
- Storage rollback khi có exception
- Xóa ảnh cũ khi update thành công
- isDirty() check để tối ưu update
- Try-catch với Toastr messages

## Automated Test Commands:
```bash
# Chạy news tests
php artisan test --filter NewsArticleTest

# Seed test data
php artisan db:seed --class=NewsArticleSeeder

# Clear storage cache
php artisan cache:clear
php artisan config:clear
```
