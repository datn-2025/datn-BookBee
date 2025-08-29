📘 Tài liệu: Quản lý sản phẩm với nhiều thuộc tính trong website bán sách
1. Giới thiệu

Hệ thống quản lý sản phẩm trong website bán sách phải hỗ trợ cả sách điện tử (ebook) và sách vật lý.
Mỗi loại sách có đặc thù khác nhau:

Ebook: chỉ có định dạng (PDF, ePub, Mobi), không giới hạn số lượng.

Sách vật lý: cần quản lý tồn kho theo số lượng và có thể có nhiều thuộc tính (ví dụ: loại bìa, ngôn ngữ, kích thước).

2 Sách vật lý

Cần quản lý số lượng tồn kho.

Có thể có một hoặc nhiều thuộc tính (ví dụ: loại bìa, ngôn ngữ, kích thước…).

Nếu có nhiều thuộc tính, thì phải sinh ra biến thể (variant) từ các tổ hợp thuộc tính.

Mỗi biến thể có số lượng riêng → hệ thống tự cộng lại để ra tổng tồn kho.

Ví dụ:

Sách "Lập trình PHP" có 2 thuộc tính:

Loại bìa: Bìa cứng, Bìa mềm

Ngôn ngữ: Tiếng Việt, Tiếng Anh

Biến thể cần nhập số lượng:

Loại bìa	Ngôn ngữ	Số lượng
Bìa cứng	Tiếng Việt	8
Bìa cứng	Tiếng Anh	2
Bìa mềm	Tiếng Việt	2
Bìa mềm	Tiếng Anh	3

Tổng tồn kho = 15 (tính tự động).

4. Giao diện quản trị (Admin)
Khi thêm Ebook:

Nhập thông tin: tên, tác giả, giá, mô tả.

Ẩn phần số lượng & thuộc tính.

Khi thêm Sách vật lý:

Nhập thông tin: tên, tác giả, giá, mô tả.

Chọn thuộc tính áp dụng (VD: bìa, ngôn ngữ).

Hệ thống sinh bảng biến thể để admin nhập số lượng.

Ví dụ bảng nhập:

Loại bìa	Ngôn ngữ	Số lượng
Bìa cứng	Tiếng Việt	[ ]
Bìa cứng	Tiếng Anh	[ ]
Bìa mềm	Tiếng Việt	[ ]
Bìa mềm	Tiếng Anh	[ ]

Sách vật lý: quản lý tồn kho theo biến thể.

Không nhập tổng số lượng thủ công, hệ thống tự tính từ các biến thể.