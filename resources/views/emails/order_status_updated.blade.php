<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật trạng thái đơn hàng</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <tr style="background-color: #4CAF50; color: white;">
            <td style="padding: 20px;">
                <h2 style="margin: 0;">BookBee - Cập nhật đơn hàng</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Xin chào <strong>{{ $order->user->name }}</strong>,</p>

                <p>Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn đã được cập nhật trạng thái:</p>

                <p style="font-size: 16px; background-color: #e0f7e9; padding: 10px 15px; border-left: 5px solid #4CAF50;">
                    <strong>{{ $newStatus }}</strong>
                </p>

                <h4 style="margin-top: 30px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Thông tin đơn hàng:</h4>
                <ul style="list-style: none; padding-left: 0; line-height: 1.8;">
                    <li><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</li>
                    <li><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</li>
                    <li><strong>Tổng tiền:</strong> {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</li>
                    <li><strong>Trạng thái mới:</strong> {{ $newStatus }}</li>
                </ul>

                <p>Quý khách có thể theo dõi trạng thái đơn hàng trong tài khoản trên trang BookBee.</p>

                <p style="margin-top: 30px;">Cảm ơn bạn đã tin tưởng BookBee!<br>Chúng tôi rất hân hạnh được phục vụ bạn.</p>

                <p style="color: #888;">— BookBee Team</p>
            </td>
        </tr>
        <tr style="background-color: #f1f1f1; text-align: center;">
            <td style="padding: 10px; font-size: 12px; color: #666;">
                © {{ now()->year }} BookBee. Mọi quyền được bảo lưu.
            </td>
        </tr>
    </table>
</body>
</html>
