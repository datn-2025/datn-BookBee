# Hướng Dẫn Sử Dụng GHN Cho Admin - Chi Tiết

## Tình Huống Hiện Tại
Hiện tại trong hệ thống **chưa có đơn hàng nào được tạo trên GHN**. Đây là lý do tại sao admin không thấy thông tin GHN trong trang chi tiết đơn hàng.

## Quy Trình Tạo Đơn Hàng GHN

### Bước 1: Kiểm Tra Điều Kiện
Trước khi tạo đơn GHN, đảm bảo đơn hàng đáp ứng các điều kiện:

✅ **Phương thức giao hàng**: Phải là "Giao hàng tận nơi" (delivery_method = 'delivery')  
✅ **Trạng thái đơn hàng**: "Chờ Xác Nhận" hoặc "Đã Xác Nhận"  
✅ **Thông tin địa chỉ**: Đầy đủ tỉnh/thành, quận/huyện, phường/xã theo GHN  
✅ **Chưa có mã GHN**: Đơn hàng chưa được tạo trên GHN trước đó  

### Bước 2: Truy Cập Trang Chi Tiết Đơn Hàng
1. Đăng nhập admin panel
2. Vào **Quản lý đơn hàng** → **Danh sách đơn hàng**
3. Click vào đơn hàng cần tạo GHN
4. Tìm phần **"Thông tin vận chuyển GHN"** ở cột bên phải

### Bước 3: Tạo Đơn Hàng GHN

#### Trường Hợp 1: Đơn Hàng Đủ Điều Kiện
- Sẽ hiển thị nút **"Tạo đơn hàng GHN"** (màu xanh)
- Click nút để tạo đơn hàng trên hệ thống GHN
- Hệ thống sẽ:
  - Gửi thông tin đơn hàng lên GHN
  - Nhận mã vận đơn từ GHN
  - Lưu thông tin vào database
  - Hiển thị thông báo thành công

#### Trường Hợp 2: Đơn Hàng Không Đủ Điều Kiện
- Sẽ hiển thị thông báo: *"Chỉ có thể tạo đơn GHN khi đơn hàng ở trạng thái 'Chờ Xác Nhận' hoặc 'Đã Xác Nhận'"*
- Hoặc: *"Chỉ có thể tạo đơn GHN cho đơn hàng giao hàng tận nơi"*

## Sau Khi Tạo Đơn GHN Thành Công

### Thông Tin Sẽ Hiển Thị
```
┌─ Thông tin vận chuyển GHN ─────────────┐
│ ✅ Đã tạo vận đơn                      │
│                                        │
│ 🚚 Mã vận đơn GHN: L338UP             │
│ ⚙️  Loại dịch vụ: ID 2                │
│ 📅 Ngày giao dự kiến: 28/01/2025      │
│                                        │
│ 📊 Trạng thái vận chuyển              │
│ ● Chờ lấy hàng                        │
│                                        │
│ [🔄 Cập nhật theo dõi] [❌ Hủy GHN]   │
└────────────────────────────────────────┘
```

### Các Chức Năng Có Sẵn

#### 1. Cập Nhật Theo Dõi
- **Mục đích**: Lấy thông tin mới nhất từ GHN
- **Khi nào dùng**: 
  - Kiểm tra trạng thái vận chuyển
  - Cập nhật lịch sử di chuyển
  - Xem thông tin giao hàng
- **Tần suất**: 1-2 lần/ngày cho đơn hàng đang vận chuyển

#### 2. Hủy Liên Kết GHN
- **Mục đích**: Xóa thông tin GHN khỏi đơn hàng
- **Lưu ý**: Chỉ xóa trong hệ thống, không hủy đơn trên GHN
- **Khi nào dùng**: Khi tạo nhầm hoặc cần tạo lại

## Các Trạng Thái GHN Thường Gặp

| Trạng Thái | Mô Tả | Hành Động Admin |
|------------|-------|------------------|
| `ready_to_pick` | Chờ lấy hàng | Chuẩn bị hàng hóa |
| `picking` | Đang lấy hàng | Theo dõi |
| `picked` | Đã lấy hàng | Cập nhật trạng thái đơn |
| `storing` | Đang lưu kho | Chờ vận chuyển |
| `transporting` | Đang vận chuyển | Thông báo khách hàng |
| `delivering` | Đang giao hàng | Chuẩn bị xác nhận |
| `delivered` | Đã giao hàng | Hoàn tất đơn hàng |
| `return` | Hoàn trả | Xử lý hoàn trả |
| `exception` | Có vấn đề | Liên hệ GHN |

## Xử Lý Các Tình Huống Thường Gặp

### 1. Không Thấy Nút "Tạo Đơn Hàng GHN"
**Nguyên nhân:**
- Đơn hàng không phải giao hàng tận nơi
- Trạng thái đơn hàng không phù hợp
- Đã có mã GHN rồi

**Giải pháp:**
- Kiểm tra phương thức giao hàng
- Cập nhật trạng thái đơn hàng
- Xem phần "Thông tin đơn hàng"

### 2. Tạo Đơn GHN Thất Bại
**Thông báo lỗi:** *"Không thể tạo đơn hàng GHN"*

**Nguyên nhân:**
- Thiếu thông tin địa chỉ GHN (district_id, ward_code)
- API GHN tạm thời không khả dụng
- Thông tin sản phẩm không hợp lệ

**Giải pháp:**
1. Kiểm tra địa chỉ giao hàng đầy đủ
2. Thử lại sau vài phút
3. Liên hệ IT kiểm tra cấu hình GHN

### 3. Không Cập Nhật Được Thông Tin Theo Dõi
**Thông báo lỗi:** *"Không thể lấy thông tin theo dõi từ GHN"*

**Nguyên nhân:**
- Mã vận đơn không tồn tại trên GHN
- API GHN bảo trì
- Token hết hạn

**Giải pháp:**
1. Kiểm tra mã vận đơn trên website GHN
2. Thử lại sau 15-30 phút
3. Báo cáo IT nếu lỗi kéo dài

## Quy Trình Làm Việc Hàng Ngày

### Sáng (8:00 - 9:00)
1. Kiểm tra đơn hàng mới cần tạo GHN
2. Tạo đơn GHN cho các đơn đã xác nhận
3. Cập nhật trạng thái theo dõi cho đơn đang vận chuyển

### Chiều (14:00 - 15:00)
1. Cập nhật thông tin theo dõi lần 2
2. Kiểm tra đơn hàng đã giao
3. Xử lý các đơn có vấn đề

### Tối (17:00 - 18:00)
1. Tổng kết đơn hàng trong ngày
2. Cập nhật trạng thái cuối cùng
3. Chuẩn bị cho ngày hôm sau

## Lưu Ý Quan Trọng

### Bảo Mật
- Chỉ admin có quyền truy cập chức năng GHN
- Mọi thao tác đều được ghi log
- Không được chia sẻ thông tin mã vận đơn

### Hiệu Suất
- Không spam nút "Cập nhật theo dõi"
- Chỉ tạo đơn GHN khi cần thiết
- Theo dõi định kỳ, không liên tục

### Khách Hàng
- Thông báo mã vận đơn cho khách hàng
- Hướng dẫn khách hàng tra cứu trên GHN
- Cập nhật trạng thái kịp thời

## Liên Hệ Hỗ Trợ

### Khi Cần Hỗ Trợ Kỹ Thuật
1. Chụp ảnh màn hình lỗi
2. Ghi chú:
   - ID đơn hàng
   - Mã vận đơn GHN (nếu có)
   - Thời gian xảy ra lỗi
   - Thao tác đang thực hiện
3. Liên hệ bộ phận IT

### Khi Cần Hỗ Trợ Từ GHN
- Hotline: 1900 636 677
- Website: ghn.vn
- Email: hotro@ghn.vn

---

**Ghi chú**: Tài liệu này được cập nhật dựa trên tình trạng hệ thống hiện tại (chưa có đơn hàng GHN nào). Khi đã có đơn hàng GHN đầu tiên, các chức năng sẽ hoạt động như mô tả.