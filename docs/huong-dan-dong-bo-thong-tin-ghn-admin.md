# Hướng Dẫn Đồng Bộ Thông Tin GHN Cho Admin

## Tình Huống
Khi đơn hàng đã được tạo trên hệ thống GHN (có mã vận đơn) nhưng thông tin vận chuyển chưa hiển thị đầy đủ trong admin panel.

## Nguyên Nhân
- Đơn hàng đã được tạo trên GHN thành công
- Thông tin theo dõi (`ghn_tracking_data`) chưa được cập nhật từ GHN về hệ thống
- Admin cần thực hiện đồng bộ thủ công để lấy thông tin mới nhất

## Cách Khắc Phục

### Bước 1: Truy Cập Chi Tiết Đơn Hàng
1. Đăng nhập vào admin panel
2. Vào **Quản lý đơn hàng** > **Danh sách đơn hàng**
3. Click vào đơn hàng cần kiểm tra

### Bước 2: Kiểm Tra Thông Tin GHN
Trong trang chi tiết đơn hàng, tìm phần **"Thông tin vận chuyển GHN"** ở cột bên phải:

- **Nếu hiển thị "Đã tạo vận đơn"**: Đơn hàng đã có mã vận đơn GHN
- **Nếu hiển thị "Chưa tạo vận đơn"**: Cần tạo đơn hàng GHN trước

### Bước 3: Đồng Bộ Thông Tin Từ GHN

#### Trường Hợp 1: Đã Có Mã Vận Đơn
1. Click nút **"Cập nhật theo dõi"** (màu xanh dương)
2. Hệ thống sẽ gọi API GHN để lấy thông tin mới nhất
3. Thông tin sẽ được cập nhật vào database và hiển thị ngay

#### Trường Hợp 2: Chưa Có Mã Vận Đơn
1. Click nút **"Tạo đơn hàng GHN"** (màu xanh)
2. Hệ thống sẽ tạo đơn hàng trên GHN và lưu mã vận đơn
3. Sau khi tạo thành công, có thể click "Cập nhật theo dõi" để lấy thông tin chi tiết

## Thông Tin Được Đồng Bộ

Sau khi cập nhật thành công, admin sẽ thấy:

### Thông Tin Cơ Bản
- **Mã vận đơn GHN**: Mã để tra cứu trên website GHN
- **Loại dịch vụ**: ID dịch vụ vận chuyển (1: nhanh, 2: tiêu chuẩn)
- **Ngày giao dự kiến**: Thời gian dự kiến giao hàng

### Trạng Thái Vận Chuyển
- **Trạng thái hiện tại**: Tình trạng đơn hàng trên hệ thống GHN
- **Mô tả chi tiết**: Thông tin bổ sung về trạng thái
- **Lịch sử vận chuyển**: Timeline các sự kiện vận chuyển

## Các Trạng Thái GHN Thường Gặp

| Trạng thái | Ý nghĩa |
|------------|----------|
| `ready_to_pick` | Chờ lấy hàng |
| `picking` | Đang lấy hàng |
| `picked` | Đã lấy hàng |
| `storing` | Đang lưu kho |
| `transporting` | Đang vận chuyển |
| `sorting` | Đang phân loại |
| `delivering` | Đang giao hàng |
| `delivered` | Đã giao hàng |
| `return` | Hoàn trả |
| `exception` | Có vấn đề |

## Xử Lý Lỗi

### Lỗi "Không thể lấy thông tin theo dõi từ GHN"
**Nguyên nhân:**
- Mã vận đơn không tồn tại trên GHN
- API GHN tạm thời không khả dụng
- Token GHN hết hạn

**Giải pháp:**
1. Kiểm tra mã vận đơn trên website GHN trực tiếp
2. Thử lại sau vài phút
3. Liên hệ IT để kiểm tra cấu hình GHN

### Lỗi "Đơn hàng chưa có mã vận đơn GHN"
**Nguyên nhân:**
- Đơn hàng chưa được tạo trên GHN
- Quá trình tạo đơn GHN bị lỗi

**Giải pháp:**
1. Click "Tạo đơn hàng GHN" trước
2. Kiểm tra thông tin địa chỉ giao hàng đầy đủ
3. Đảm bảo đơn hàng ở trạng thái "Chờ Xác Nhận" hoặc "Đã Xác Nhận"

## Lưu Ý Quan Trọng

### Điều Kiện Tạo Đơn GHN
- Đơn hàng phải có `delivery_method = 'delivery'` (giao hàng tận nơi)
- Đơn hàng phải ở trạng thái "Chờ Xác Nhận" hoặc "Đã Xác Nhận"
- Địa chỉ giao hàng phải có đầy đủ thông tin GHN (district_id, ward_code)

### Tần Suất Cập Nhật
- **Khuyến nghị**: Cập nhật 1-2 lần/ngày cho đơn hàng đang vận chuyển
- **Tự động**: Hệ thống không tự động cập nhật, cần thao tác thủ công
- **Real-time**: Thông tin được cập nhật ngay lập tức khi click nút

### Bảo Mật
- Chỉ admin có quyền truy cập chức năng này
- Mọi thao tác đều được ghi log để audit
- Không thể xóa/sửa thông tin GHN trực tiếp, chỉ đồng bộ từ API

## Quy Trình Xử Lý Đơn Hàng GHN

### 1. Đơn Hàng Mới
```
Đặt hàng → Thanh toán → Tự động tạo GHN (nếu giao hàng) → Cập nhật theo dõi
```

### 2. Đơn Hàng Cũ (Chưa Có GHN)
```
Vào admin → Chi tiết đơn hàng → Tạo đơn GHN → Cập nhật theo dõi
```

### 3. Theo Dõi Hàng Ngày
```
Vào admin → Chi tiết đơn hàng → Cập nhật theo dõi → Kiểm tra trạng thái
```

## Liên Hệ Hỗ Trợ

Nếu gặp vấn đề không thể tự giải quyết:
1. Chụp ảnh màn hình lỗi
2. Ghi chú mã đơn hàng và mã vận đơn GHN
3. Liên hệ bộ phận IT để được hỗ trợ

---

**Lưu ý**: Tài liệu này được cập nhật theo phiên bản hệ thống hiện tại. Vui lòng kiểm tra phiên bản mới nhất khi có thay đổi.