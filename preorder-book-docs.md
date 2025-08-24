
# 📘 TÀI LIỆU CHỨC NĂNG: ĐẶT TRƯỚC SÁCH (PRE-ORDER BOOK)

## 🔹 1. MỤC TIÊU
Cho phép người dùng **đặt trước** các cuốn sách sắp ra mắt (ebook hoặc sách vật lý), với lựa chọn thuộc tính, địa chỉ nhận hàng (nếu là sách vật lý), và thanh toán trước hoặc giữ đơn.

## 🔹 2. PHÂN LOẠI SÁCH
- **Sách điện tử (Ebook)**:
  - Không cần địa chỉ nhận hàng.
  - Sau ngày ra mắt, người dùng có thể tải về.

- **Sách vật lý (Physical Book)**:
  - Cần chọn địa chỉ nhận hàng.
  - Sau ngày ra mắt sẽ tiến hành giao hàng.

## 🔹 3. CHỨC NĂNG CLIENT (NGƯỜI DÙNG)

### 3.1. Hiển thị danh sách sách đặt trước trên trang chủ
- Vị trí: **Trang chủ** hoặc **trang danh mục sách**.
- Sách được đánh dấu bằng **label "Đặt trước"** (ví dụ: 🔖 *Sắp ra mắt*).
- Chỉ hiển thị các sách có `release_date > hiện tại`.

### 3.2. Modal Form khi click "Đặt trước"
Khi người dùng nhấn **nút “Đặt trước”**, hiển thị **modal** với form gồm:

- **Tên sách**
- **Chọn thuộc tính** (nếu có): ví dụ: bìa mềm/bìa cứng, định dạng PDF/ePub,...
- **Số lượng**
- **Chọn địa chỉ nhận hàng** *(chỉ hiển thị nếu là sách vật lý)*:
  - Sử dụng địa chỉ có sẵn hoặc thêm mới.
- **Chọn phương thức thanh toán**:
  - Thanh toán online

> ✅ Khi submit, hệ thống ghi nhận đơn đặt trước và gửi email xác nhận cho người dùng.

## 🔹 4. CHỨC NĂNG ADMIN (QUẢN TRỊ VIÊN)

### 4.1. Thêm mới sách (Form thêm sách)
- Có trường `Ngày ra mắt (release_date)` – bắt buộc.
- Có tùy chọn: **Cho phép đặt trước** ✅
- Chọn loại sách: `ebook`, `vật lý`, hoặc `cả hai`.
- Nếu là sách vật lý → có thể cấu hình thuộc tính như loại bìa, phiên bản đặc biệt,...
- Cho phép nhập giá ưu đãi(nếu có)

### 4.2. Quản lý đơn đặt trước
Trang: `/admin/preorders`  
Thông tin hiển thị:

| Mã đơn | Tên khách hàng | Tên sách | Loại | Trạng thái | Ngày đặt | Ngày ra mắt |
|--------|----------------|----------|------|------------|----------|-------------|

- Trạng thái: Chờ xử lý, Đã thanh toán, Đã giao, Hết hàng,...
- Có nút để chuyển đơn sang **đơn hàng chính thức** sau khi sách phát hành.

### 4.3. Tự động cập nhật trạng thái
- Vào **ngày ra mắt**, hệ thống sẽ:
  - Kích hoạt việc gửi sách điện tử cho người đã đặt.
  - Chuyển các đơn sách vật lý sang trạng thái "Chờ giao hàng".
  - Gửi email thông báo cho người dùng.

## 🔹 5. CƠ SỞ DỮ LIỆU GỢI Ý

### Bảng `books`
```sql
id | title | type | release_date | allow_preorder | ...
```

### Bảng `preorders`
```sql
id | user_id | book_id | quantity | attributes | address_id | payment_method | status | created_at
```

### Bảng `addresses`
```sql
id | user_id | receiver_name | phone | address_line
```

## 🔹 6. LUỒNG CHẠY (FLOW)

```mermaid
flowchart TD
  A[Trang chủ hiển thị sách đặt trước]
  B[Người dùng nhấn "Đặt trước"]
  C[Hiện Modal form]
  D[Người dùng chọn thuộc tính + địa chỉ + thanh toán]
  E[Gửi đơn đặt trước]
  F[Hệ thống lưu đơn và gửi email]
  G[Chờ tới ngày ra mắt]
  H[Ra mắt: Giao sách hoặc kích hoạt ebook]

  A --> B --> C --> D --> E --> F --> G --> H
```

## 🔹 7. GỢI Ý GIAO DIỆN (FRONTEND)

### 7.1. Trang chủ hiển thị sách đặt trước

```html
<div class="book-card">
  <span class="label label-warning">Đặt trước</span>
  <h3>Tên sách</h3>
  <p>Ngày ra mắt: 20/08/2025</p>
  <button class="btn btn-primary" data-toggle="modal" data-target="#preorderModal">Đặt trước</button>
</div>
```

### 7.2. Modal form đặt trước

```html
<form id="preorder-form">
  <label>Thuộc tính:</label>
  <select name="variant">...</select>

  <label>Số lượng:</label>
  <input type="number" name="quantity" />

  <div id="address-section">
    <label>Địa chỉ nhận hàng:</label>
    <select name="address_id">...</select>
  </div>

  <label>Phương thức thanh toán:</label>
  <select name="payment_method">
    <option value="online">Thanh toán online</option>
    <option value="cod">COD</option>
  </select>

  <button type="submit">Xác nhận đặt trước</button>
</form>
```

## 🔹 8. GỢI Ý XỬ LÝ BACKEND

- Kiểm tra `release_date` để xác định sách còn cho đặt trước hay không.
- Nếu `type == physical`, bắt buộc có địa chỉ.
- gửi hóa đơn sau khi thanh toán
- Gửi mail sau khi đặt thành công.
- gửi mail sau khi sách ra mắt và tạo đơn hàng
