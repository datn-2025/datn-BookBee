<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .order-info {
            margin-bottom: 30px;
        }
        .order-items {
            margin-bottom: 30px;
        }
        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-items th, .order-items td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .order-items th {
            background-color: #f5f5f5;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .shipping-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác nhận đơn hàng</h1>
            <p>Cảm ơn bạn đã đặt hàng tại BookBee!</p>
        </div>

        <div class="order-info">
            <h2>Thông tin đơn hàng</h2>
            <p>Mã đơn hàng: {{ $order->order_code }}</p>
            <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p>Trạng thái: {{ $order->orderStatus->name }}</p>
            <p>Phương thức thanh toán: {{ $order->paymentMethod->name }}</p>
        </div>

        <div class="order-items">
            <h2>Chi tiết đơn hàng</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->book->title }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price) }} VNĐ</td>
                        <td>{{ number_format($item->total) }} VNĐ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                <p>Tạm tính: {{ number_format($order->orderItems->sum('total')) }} VNĐ</p>
                <p>Phí vận chuyển: {{ number_format($order->shipping_fee) }} VNĐ</p>
                @if($order->voucher)
                <p>Giảm giá: {{ number_format($order->orderItems->sum('total') + $order->shipping_fee - $order->total_amount) }} VNĐ</p>
                @endif
                <p>Tổng tiền: {{ number_format($order->total_amount) }} VNĐ</p>
            </div>
        </div>

        <div class="shipping-info">
            @if($order->delivery_method === 'pickup')
            <h2>Thông tin nhận hàng</h2>
            <p><strong>Phương thức:</strong> Nhận tại cửa hàng</p>
            <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? $order->address->recipient_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? $order->address->phone }}</p>
            <p><strong>Địa chỉ cửa hàng:</strong> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
            <p><strong>Giờ mở cửa:</strong> 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>
            <p><em>Vui lòng mang theo mã đơn hàng {{ $order->order_code }} khi đến nhận sách.</em></p>
            @else
            <h2>Thông tin giao hàng</h2>
            <p><strong>Phương thức:</strong> Giao hàng tận nơi</p>
            <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? $order->address->recipient_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? $order->address->phone }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->address->address_detail }}, {{ $order->address->ward }}, {{ $order->address->district }}, {{ $order->address->city }}</p>
            @endif
        </div>

        <div class="footer">
            <p>BookBee - Nơi mua sắm sách trực tuyến</p>
            <p>Email: support@bookbee.com</p>
            <p>Hotline: 1900 1234</p>
        </div>
    </div>
</body>
</html>
