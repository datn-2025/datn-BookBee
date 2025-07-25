<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hóa đơn đơn hàng</title>
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
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-items {
            margin-bottom: 20px;
        }
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-items th, .invoice-items td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .invoice-items th {
            background-color: #f5f5f5;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
        .payment-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
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
            <h1>Hóa đơn đơn hàng</h1>
            <p>BookBee - Nơi mua sắm sách trực tuyến</p>
        </div>

        <div class="invoice-info">
            <h2>Thông tin hóa đơn</h2>
            <p>Mã đơn hàng: {{ $order->order_code }}</p>
            <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p>Ngày thanh toán: {{ $order->payments->first()->paid_at->format('d/m/Y H:i') }}</p>
            <p>Phương thức thanh toán: {{ $order->payments->first()->paymentMethod->name }}</p>
        </div>

        <div class="invoice-items">
            <h2>Chi tiết hóa đơn</h2>
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
        </div>

        <div class="total">
            <p>Tổng tiền: {{ number_format($order->total_amount) }} VNĐ</p>
        </div>

        <div class="payment-info">
            <h2>Thông tin thanh toán</h2>
            <p>Mã giao dịch: {{ $order->payments->first()->transaction_id }}</p>
            <p>Số tiền: {{ number_format($order->payments->first()->amount) }} VNĐ</p>
            <p>Trạng thái: {{ $order->paymentStatus->name }}</p>
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
