<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hóa đơn đơn hàng - BookBee</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px 0;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .header h1 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 28px;
        }
        .header p {
            color: #7f8c8d;
            margin-top: 0;
            font-size: 16px;
        }
        .invoice-info {
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .invoice-info h2 {
            color: #2c3e50;
            margin-top: 0;
            font-size: 18px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 8px;
        }
        .invoice-items {
            margin-bottom: 25px;
        }
        .invoice-items h2 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .invoice-items th {
            background-color: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 500;
        }
        .invoice-items td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        .invoice-items tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .invoice-items tr:hover {
            background-color: #f1f1f1;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 18px;
            color: #2c3e50;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .payment-info, .shipping-info {
            margin-top: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .payment-info h2, .shipping-info h2 {
            color: #2c3e50;
            margin-top: 0;
            font-size: 18px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 8px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        .footer p {
            margin: 5px 0;
        }
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        .note {
            font-style: italic;
            color: #7f8c8d;
            margin-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 5px;
        }
        .badge-combo {
            background-color: #e67e22;
            color: white;
        }
        .badge-format {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>HÓA ĐƠN ĐƠN HÀNG</h1>
            <p>BookBee - Thiên đường sách trực tuyến</p>
        </div>

        <div class="invoice-info">
            <h2>THÔNG TIN HÓA ĐƠN</h2>
            <p><strong>Mã đơn hàng:</strong> <span class="highlight">{{ $order->order_code }}</span></p>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Ngày thanh toán:</strong> {{ $order->payments->first()->paid_at->format('d/m/Y H:i') }}</p>
            <p><strong>Phương thức thanh toán:</strong> {{ $order->payments->first()->paymentMethod->name }}</p>
        </div>

        <div class="invoice-items">
            <h2>CHI TIẾT ĐƠN HÀNG</h2>
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
                        <td>
                            @if($item->is_combo)
                                {{ $item->collection->name ?? 'Combo không xác định' }}
                                <span class="badge badge-combo">COMBO</span>
                            @else
                                {{ $item->book->title ?? 'Sách không xác định' }}
                                @if($item->bookFormat)
                                    <span class="badge badge-format">{{ $item->bookFormat->format_name }}</span>
                                @endif
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price) }} ₫</td>
                        <td>{{ number_format($item->total) }} ₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total">
            <p>TỔNG CỘNG: <span class="highlight">{{ number_format($order->total_amount) }} ₫</span></p>
        </div>

        <div class="payment-info">
            <h2>THÔNG TIN THANH TOÁN</h2>
            <p><strong>Mã giao dịch:</strong> {{ $order->payments->first()->transaction_id }}</p>
            <p><strong>Số tiền:</strong> <span class="highlight">{{ number_format($order->payments->first()->amount) }} ₫</span></p>
            <p><strong>Trạng thái:</strong> 
                <span style="color: {{ $order->paymentStatus->name === 'Thành công' ? '#27ae60' : '#e74c3c' }}">
                    {{ $order->paymentStatus->name }}
                </span>
            </p>
        </div>

        <div class="shipping-info">
            @if($order->delivery_method === 'pickup')
            <h2>THÔNG TIN NHẬN HÀNG</h2>
            <p><strong>Phương thức:</strong> Nhận tại cửa hàng</p>
            <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? $order->address->recipient_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? $order->address->phone }}</p>
            <p><strong>Địa chỉ cửa hàng:</strong> 
                @if(isset($storeSettings) && $storeSettings->address)
                    {{ $storeSettings->address }}
                @else
                    123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                @endif
            </p>
            <p><strong>Điện thoại:</strong> 
                @if(isset($storeSettings) && $storeSettings->phone)
                    {{ $storeSettings->phone }}
                @else
                    1900 1234
                @endif
            </p>
            <p><strong>Giờ mở cửa:</strong> 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>
            <p class="note">Vui lòng mang theo mã đơn hàng <strong>{{ $order->order_code }}</strong> khi đến nhận sách.</p>
            @else
            <h2>THÔNG TIN GIAO HÀNG</h2>
            <p><strong>Phương thức:</strong> Giao hàng tận nơi</p>
            <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? $order->address->recipient_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? $order->address->phone }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->address->address_detail }}, {{ $order->address->ward }}, {{ $order->address->district }}, {{ $order->address->city }}</p>
            @endif
        </div>

        <div class="footer">
            <p><strong>BookBee - Thiên đường sách trực tuyến</strong></p>
            <p>Email: support@bookbee.com | Hotline: 1900 1234</p>
            <p>© 2023 BookBee. All rights reserved.</p>
        </div>
    </div>
</body>
</html>