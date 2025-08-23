<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đặt trước sách</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .preorder-info {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .book-details {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .book-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .book-details th, .book-details td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .book-details th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
            font-size: 18px;
            color: #28a745;
        }
        .shipping-info {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #ffc107;
            color: #212529;
            border-radius: 4px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .note {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Xác nhận đặt trước sách</h1>
            <p>Cảm ơn bạn đã đặt trước sách tại <strong>BookBee</strong>!</p>
        </div>

        <div class="preorder-info">
            <h2>📋 Thông tin đơn đặt trước</h2>
            <p><strong>Mã đơn đặt trước:</strong> #{{ $preorder->id }}</p>
            <p><strong>Ngày đặt:</strong> {{ $preorder->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Trạng thái:</strong> <span class="status-badge">{{ $preorder->status }}</span></p>
            <p><strong>Khách hàng:</strong> {{ $preorder->customer_name }}</p>
            <p><strong>Email:</strong> {{ $preorder->email }}</p>
            <p><strong>Số điện thoại:</strong> {{ $preorder->phone }}</p>
        </div>

        <div class="book-details">
            <h2>📚 Chi tiết sách đặt trước</h2>
            <table>
                <thead>
                    <tr>
                        <th>Thông tin sách</th>
                        <th>Giá trị</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Tên sách</strong></td>
                        <td>{{ $book->title }}</td>
                    </tr>
                    @if($book->authors->count() > 0)
                    <tr>
                        <td><strong>Tác giả</strong></td>
                        <td>{{ $book->authors->pluck('name')->join(', ') }}</td>
                    </tr>
                    @endif
                    @if($format)
                    <tr>
                        <td><strong>Định dạng</strong></td>
                        <td>{{ $format->format_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Số lượng</strong></td>
                        <td>{{ $preorder->quantity }}</td>
                    </tr>
                    <tr>
                        <td><strong>Đơn giá</strong></td>
                        <td>{{ number_format($preorder->unit_price) }} VNĐ</td>
                    </tr>
                    @if($book->release_date)
                    <tr>
                        <td><strong>Ngày phát hành dự kiến</strong></td>
                        <td>{{ $book->release_date->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            
            <div class="total">
                <strong>Tổng tiền: {{ number_format($preorder->total_amount) }} VNĐ</strong>
            </div>
        </div>

        @if($preorder->address)
        <div class="shipping-info">
            <h2>🚚 Thông tin giao hàng</h2>
            <p><strong>Địa chỉ:</strong> {{ $preorder->address }}</p>
            @if($preorder->ward_name)
                <p><strong>Phường/Xã:</strong> {{ $preorder->ward_name }}</p>
            @endif
            @if($preorder->district_name)
                <p><strong>Quận/Huyện:</strong> {{ $preorder->district_name }}</p>
            @endif
            @if($preorder->province_name)
                <p><strong>Tỉnh/Thành phố:</strong> {{ $preorder->province_name }}</p>
            @endif
        </div>
        @endif

        <div class="note">
            <h3>📝 Lưu ý quan trọng:</h3>
            <ul>
                <li>Đây là đơn <strong>đặt trước</strong>, sách sẽ được giao sau khi phát hành chính thức.</li>
                <li>Chúng tôi sẽ thông báo cho bạn khi sách sẵn sàng để giao.</li>
                <li>Bạn có thể hủy đơn đặt trước bất kỳ lúc nào trước khi sách được giao.</li>
                <li>Nếu có thay đổi về ngày phát hành, chúng tôi sẽ thông báo sớm nhất.</li>
            </ul>
        </div>

        @if($preorder->notes)
        <div class="preorder-info">
            <h2>💬 Ghi chú</h2>
            <p>{{ $preorder->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Cảm ơn bạn đã tin tưởng BookBee!</p>
            <p>Nếu có thắc mắc, vui lòng liên hệ: support@bookbee.com | 1900-xxxx</p>
            <p>&copy; {{ date('Y') }} BookBee. All rights reserved.</p>
        </div>
    </div>
</body>
</html>