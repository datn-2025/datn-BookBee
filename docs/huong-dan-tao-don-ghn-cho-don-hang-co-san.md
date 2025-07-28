# Hướng Dẫn Tạo Đơn GHN Cho Đơn Hàng Có Sẵn

## Tình Huống
Bạn đang xem đơn hàng **#BBE-1753626524** và các đơn hàng tương tự có:
- ✅ Phương thức: "Giao hàng tận nơi" 
- ✅ Trạng thái: "Chờ xác nhận"
- ❌ **Chưa có mã vận đơn GHN**

## Nguyên Nhân
Đơn hàng này được tạo nhưng chưa được tạo đơn GHN tự động hoặc thủ công. Đây là lý do tại sao admin không thấy thông tin vận chuyển GHN.

## Giải Pháp: Tạo Đơn GHN Thủ Công

### Bước 1: Truy Cập Chi Tiết Đơn Hàng
1. Vào admin panel: `http://localhost:8000/admin`
2. Vào **Quản lý đơn hàng**
3. Tìm và click vào đơn hàng **#BBE-1753626524**

### Bước 2: Kiểm Tra Điều Kiện
Trong trang chi tiết đơn hàng, kiểm tra:

#### Thông Tin Đơn Hàng
- **Phương thức giao hàng**: Phải là "Giao hàng tận nơi"
- **Trạng thái**: "Chờ xác nhận" hoặc "Đã xác nhận"
- **Địa chỉ giao hàng**: Phải có đầy đủ thông tin

#### Phần "Thông tin vận chuyển GHN"
Sẽ hiển thị:
```
┌─ Thông tin vận chuyển GHN ─────────────┐
│ ⚠️  Chưa tạo vận đơn                   │
│                                        │
│ 🚚 Chưa tạo đơn hàng GHN cho đơn hàng │
│    này                                 │
│                                        │
│ Chỉ có thể tạo đơn GHN khi đơn hàng   │
│ ở trạng thái "Chờ Xác Nhận" hoặc "Đã  │
│ Xác Nhận"                             │
│                                        │
│ [✅ Tạo đơn hàng GHN]                 │
└────────────────────────────────────────┘
```

### Bước 3: Tạo Đơn GHN
1. Click nút **"Tạo đơn hàng GHN"** (màu xanh)
2. Hệ thống sẽ:
   - Gửi thông tin đến API GHN
   - Tạo đơn vận chuyển
   - Lưu mã vận đơn vào database
   - Hiển thị kết quả

### Bước 4: Kết Quả Mong Đợi

#### Khi Thành Công
```
✅ Tạo đơn hàng GHN thành công. Mã vận đơn: L338UP

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

#### Khi Thất Bại
```
❌ Không thể tạo đơn hàng GHN. Vui lòng kiểm tra thông tin địa chỉ và thử lại.
```

## Xử Lý Lỗi Thường Gặp

### Lỗi 1: "Chỉ có thể tạo đơn GHN cho đơn hàng giao hàng tận nơi"
**Nguyên nhân**: `delivery_method` không phải 'delivery'
**Giải pháp**: Kiểm tra lại phương thức giao hàng của đơn hàng

### Lỗi 2: "Chỉ có thể tạo đơn GHN khi đơn hàng ở trạng thái..."
**Nguyên nhân**: Trạng thái đơn hàng không phù hợp
**Giải pháp**: 
1. Cập nhật trạng thái đơn hàng thành "Đã xác nhận"
2. Hoặc giữ ở "Chờ xác nhận"

### Lỗi 3: "Không thể tạo đơn hàng GHN"
**Nguyên nhân**: 
- Thiếu thông tin địa chỉ GHN (district_id, ward_code)
- API GHN không khả dụng
- Cấu hình GHN sai

**Giải pháp**:
1. **Kiểm tra địa chỉ**:
   - Vào phần "Thông tin giao hàng"
   - Đảm bảo có đầy đủ: Tỉnh/Thành phố, Quận/Huyện, Phường/Xã
   - Địa chỉ phải được chọn từ dropdown GHN

2. **Kiểm tra cấu hình GHN**:
   ```env
   GHN_API_URL=https://dev-online-gateway.ghn.vn
   GHN_API_KEY=your_token_here
   GHN_SHOP_ID=your_shop_id_here
   ```

3. **Kiểm tra log**:
   - Xem file `storage/logs/laravel.log`
   - Tìm lỗi liên quan đến GHN

## Sau Khi Tạo Thành Công

### Chức Năng Có Thể Sử Dụng
1. **Cập nhật theo dõi**: Lấy thông tin mới nhất từ GHN
2. **Hủy liên kết GHN**: Xóa thông tin GHN (nếu cần tạo lại)

### Thông Tin Hiển Thị
- **Mã vận đơn GHN**: Để tra cứu trên website GHN
- **Loại dịch vụ**: 1 = Nhanh, 2 = Tiêu chuẩn
- **Ngày giao dự kiến**: Thời gian dự kiến giao hàng
- **Trạng thái vận chuyển**: Tình trạng hiện tại

### Cập Nhật Thông Tin Theo Dõi
1. Click nút **"Cập nhật theo dõi"**
2. Hệ thống sẽ lấy thông tin mới nhất từ GHN
3. Cập nhật trạng thái và lịch sử vận chuyển

## Quy Trình Xử Lý Đơn Hàng

### Cho Đơn Hàng #BBE-1753626524
1. ✅ **Hiện tại**: Đơn hàng đã tạo, chờ xác nhận
2. 🔄 **Tiếp theo**: Tạo đơn GHN (theo hướng dẫn trên)
3. 📦 **Sau đó**: Cập nhật theo dõi định kỳ
4. 🚚 **Cuối cùng**: Theo dõi đến khi giao hàng thành công

### Cho Các Đơn Hàng Tương Tự
Áp dụng quy trình tương tự cho:
- Đơn hàng #BBE-1753625380
- Các đơn hàng khác có `delivery_method = 'delivery'` nhưng chưa có mã GHN

## Lưu Ý Quan Trọng

### Điều Kiện Bắt Buộc
- ✅ `delivery_method = 'delivery'`
- ✅ Trạng thái: "Chờ xác nhận" hoặc "Đã xác nhận"
- ✅ Địa chỉ đầy đủ thông tin GHN
- ✅ Chưa có mã GHN (`ghn_order_code = null`)

### Thời Điểm Tạo
- **Tốt nhất**: Ngay sau khi xác nhận đơn hàng
- **Muộn nhất**: Trước khi chuẩn bị hàng hóa
- **Không nên**: Tạo cho đơn hàng đã hủy hoặc hoàn trả

### Bảo Mật
- Chỉ admin có quyền tạo đơn GHN
- Mọi thao tác đều được ghi log
- Không được tạo đơn GHN trùng lặp

## Liên Hệ Hỗ Trợ

Nếu gặp vấn đề khi tạo đơn GHN:
1. **Chụp ảnh màn hình** lỗi
2. **Ghi chú thông tin**:
   - Mã đơn hàng: #BBE-1753626524
   - Thời gian thực hiện
   - Thông báo lỗi cụ thể
3. **Kiểm tra log**: `storage/logs/laravel.log`
4. **Liên hệ IT** với thông tin trên

---

**Kết luận**: Đơn hàng #BBE-1753626524 hoàn toàn có thể tạo đơn GHN. Admin chỉ cần vào chi tiết đơn hàng và click nút "Tạo đơn hàng GHN" để bắt đầu quá trình vận chuyển qua GHN.