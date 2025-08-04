2. Quy trình Quản lý Biến thể
2.1. Thiết lập thông tin biến thể
a. Mã định danh
Mỗi biến thể phải có:

Mã cha (Parent ID): Đại diện cho sản phẩm gốc (VD: SACH001).

Mã biến thể (Variant ID/SKU): Thêm hậu tố phân biệt (VD: SACH001-BC = bìa cứng, SACH001-BM = bìa mềm).

ISBN/EAN riêng (nếu có).

b. Thuộc tính biến thể
Thuộc tính	Ví dụ
Định dạng	Bìa cứng, bìa mềm, ebook
Ngôn ngữ	Tiếng Việt, tiếng Anh
Giá bán	150.000đ (bìa cứng), 80.000đ (bìa mềm)
Kích thước/Trọng lượng	14x21 cm, 300g
→ Lưu trữ trong database hoặc bảng tính (Excel/Google Sheets).

2.2. Quản lý số lượng tồn kho
a. Nhập kho
Khi nhập hàng, ghi rõ:

Số lượng từng biến thể.

Vị trí lưu trữ (nếu có).

b. Kiểm soát xuất/nhập
Tự động trừ kho khi bán/cho mượn đúng biến thể.

Cảnh báo khi tồn kho dưới mức tối thiểu (VD: <10 cuốn).

c. Báo cáo tồn kho
Thống kê theo:

Biến thể bán chạy/chậm.

Tồn kho ứ đọng cần xử lý.

3. Công cụ hỗ trợ
3.1. Phần mềm quản lý
Loại	Ví dụ	Chức năng liên quan
ERP	SAP, Odoo	Theo dõi đa biến thể, báo cáo tồn kho
Thư viện số	Koha, Alma	Quản lý định dạng sách (in/ebook)
E-commerce	Shopify, Magento	Hiển thị và bán đa biến thể
3.2. Giải pháp thủ công
Excel/Google Sheets: Dùng bảng tính với các sheet phân loại, filter theo biến thể.

Mã vạch/QR code: Dán nhãn riêng để quét kiểm kho.

4. Ví dụ thực tế
Sách "Đắc Nhân Tâm"

Mã biến thể	Tên biến thể	Số lượng tồn	Giá bán
DNT-BC	Bìa cứng	30	150.000đ
DNT-BM	Bìa mềm	85	80.000đ
DNT-EB	Ebook	∞	50.000đ
→ Khi khách mua DNT-BC, hệ thống chỉ trừ tồn kho của bìa cứng.

5. Xử lý sự cố thường gặp
Sự cố	Nguyên nhân	Cách khắc phục
Nhầm lẫn khi nhập liệu	Thiếu mã biến thể	Quy định bắt buộc nhập SKU riêng
Bán sai biến thể	Không kiểm tra kỹ	Thiết lập cảnh báo trên hệ thống
Thống kê sai số lượng	Không đồng bộ dữ liệu	Dùng phần mềm tự động cập nhật
