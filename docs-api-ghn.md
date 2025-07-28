📦 TÀI LIỆU TÍCH HỢP API GHN VÀO WEBSITE BÁN SÁCH (LARAVEL)
 3. Tính phí vận chuyển

3.1 API: POST /shiip/public-api/v2/shipping-order/fee

Request cần:

from_district_id

service_type_id: thường là 2 (tiêu chuẩn)

to_district_id, to_ward_code

weight, height, length, width

Kết quả trả về:

Tổng phí vận chuyển (total) sử dụng để cộng vào đơn hàng.

⏳ 4. Lấy ngày giao hàng dự kiến

API: POST /shiip/public-api/v2/shipping-order/leadtime

Request cần:

from_district_id, to_district_id, service_type_id

Kết quả trả về:

leadtime: timestamp -> hiển thị ngày dự kiến giao hàng cho khách.

✅ 5. Tạo đơn hàng GHN

API: POST /shiip/public-api/v2/shipping-order/create

Thông tin cần:

Thông tin người nhận (tên, SDT, địa chỉ).

Địa chỉ kho hàng.

Thông tin sản phẩm (tên sách, số lượng, khối lượng).

Hình thức thanh toán (COD hoặc prepaid).

Kết quả trả về:

order_code: mã vận đơn (lưu lại để tra cứu).

🔄 6. Tra cứu đơn hàng

API: GET /shiip/public-api/v2/shipping-order/detail

Dùng order_code để:

Theo dõi trạng thái vận chuyển.

Hiển thị tiến trình giao hàng trong trang "Đơn hàng của tôi".