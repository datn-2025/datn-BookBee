# Tài liệu Quản lý DRM cho Ebook

## 1. Tổng quan

Hệ thống Digital Rights Management (DRM) được xây dựng để bảo vệ bản quyền của ebook bằng cách giới hạn số lần tải xuống và thời gian truy cập của người dùng sau khi mua.

Tính năng này được quản lý thông qua định dạng sách (Book Format) của mỗi cuốn sách trong hệ thống.

## 2. Cấu trúc Dữ liệu

Các thiết lập DRM được lưu trong bảng `book_formats` với các cột sau:

-   `drm_enabled` (boolean, mặc định: `true`):
    -   `true`: Bật tính năng DRM. Hệ thống sẽ kiểm tra giới hạn tải và ngày hết hạn.
    -   `false`: Tắt tính năng DRM. Người dùng có thể tải ebook không giới hạn.
-   `max_downloads` (integer, mặc định: `5`): Số lần tải xuống tối đa cho phép đối với một ebook khi DRM được bật.
-   `download_expiry_days` (integer, mặc định: `365`): Số ngày kể từ ngày mua mà người dùng được phép tải ebook. Sau thời gian này, link tải sẽ hết hạn (chức năng này cần được kiểm tra lại trong logic controller).

## 3. Luồng hoạt động

### 3.1. Quản lý trong trang Admin

Quản trị viên có thể cấu hình các thiết lập DRM cho từng ebook trong trang **Chỉnh sửa sách** (`/admin/books/{id}/edit`).

-   **Vị trí**: Trong phần "Định dạng & Giá bán" -> "Ebook".

-   **Các trường cấu hình**:
    1.  **Bật/Tắt DRM**: Một công tắc (checkbox) cho phép bật hoặc tắt hoàn toàn DRM cho ebook.
    2.  **Số lần tải tối đa**: Một ô nhập số để thiết lập `max_downloads`.
    3.  **Số ngày hết hạn tải**: Một ô nhập số để thiết lập `download_expiry_days`.

-   **Logic**: Khi quản trị viên cập nhật sách, các giá trị này sẽ được lưu vào bản ghi `BookFormat` tương ứng của ebook.

### 3.2. Kiểm tra phía người dùng

Khi người dùng thực hiện thao tác tải ebook, hệ thống sẽ kiểm tra quyền tải dựa trên logic trong model `App\Models\BookFormat`:

-   **Phương thức `canUserDownload($userId, $orderId = null)`**:
    -   Nếu `drm_enabled` là `false`, phương thức luôn trả về `true` (cho phép tải).
    -   Nếu `drm_enabled` là `true`, hệ thống sẽ thực hiện hai bước kiểm tra:
        1.  **Kiểm tra ngày hết hạn**: Lấy ngày mua của đơn hàng. Nếu ngày hiện tại đã vượt quá `ngày mua + download_expiry_days`, người dùng không thể tải.
        2.  **Kiểm tra số lần tải**: Đếm số lần người dùng đã tải ebook này và so sánh với `max_downloads`. Nếu vẫn còn lượt, người dùng được phép tải.

-   **Phương thức `getRemainingDownloads($userId, $orderId = null)`**:
    -   Hiển thị số lượt tải còn lại cho người dùng. Trả về một số lớn (ví dụ: 999) nếu DRM bị tắt để biểu thị là "không giới hạn".

### 3.3. Gợi ý cải thiện UI/UX phía người dùng

Để mang lại trải nghiệm tốt nhất và tránh gây nhầm lẫn cho khách hàng, ở các trang như **Tủ sách** hoặc **Chi tiết đơn hàng**, nên hiển thị rõ ràng các thông tin sau cho mỗi ebook:

-   **Lượt tải còn lại**: Sử dụng phương thức `getRemainingDownloads()`.
-   **Ngày hết hạn tải**: Hiển thị ngày cụ thể nếu DRM và `download_expiry_days` được áp dụng.

## 4. Hướng dẫn sử dụng

1.  Truy cập trang quản trị.
2.  Đi đến `Quản lý Sách` -> `Sách`.
3.  Tìm sách bạn muốn cấu hình và nhấp vào nút `Chỉnh sửa`.
4.  Cuộn xuống phần `Định dạng & Giá bán`.
5.  Nếu sách có định dạng Ebook, bạn sẽ thấy mục **"Cài đặt DRM cho Ebook"**.
6.  **Bật/Tắt DRM**: Sử dụng công tắc để kích hoạt hoặc vô hiệu hóa DRM.
    -   **Khi bật**: Nhập **Số lần tải tối đa** và **Số ngày hết hạn tải**.
    -   **Khi tắt**: Các ô nhập liệu sẽ bị vô hiệu hóa.
7.  Nhấn nút **"Cập nhật"** để lưu thay đổi.
