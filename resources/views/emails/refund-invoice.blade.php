<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hóa đơn hoàn tiền</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 20px;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .customer-info, .invoice-details {
            width: 48%;
        }
        .info-title {
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .items-table th {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .refund-summary {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .refund-summary h3 {
            color: #e17055;
            margin-top: 0;
        }
        .total-section {
            text-align: right;
            margin-bottom: 30px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-label {
            width: 200px;
            text-align: right;
            padding-right: 20px;
            font-weight: bold;
        }
        .total-amount {
            width: 120px;
            text-align: right;
            font-size: 18px;
            color: #e74c3c;
            font-weight: bold;
        }
        .footer {
            border-top: 2px solid #e74c3c;
            padding-top: 20px;
            text-align: center;
            color: #666;
        }
        .note {
            background-color: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">BOOKSTORE</div>
        <div class="invoice-title">HÓA ĐƠN HOÀN TIỀN</div>
        <div>Mã hóa đơn: {{ $invoice->id }}</div>
        <div>Ngày tạo: {{ $invoice->invoice_date->format('d/m/Y H:i') }}</div>
    </div>

    <div class="invoice-info">
        <div class="customer-info">
            <div class="info-title">THÔNG TIN KHÁCH HÀNG</div>
            <div><strong>Họ tên:</strong> {{ $order->user->name }}</div>
            <div><strong>Email:</strong> {{ $order->user->email }}</div>
            <div><strong>Điện thoại:</strong> {{ $order->user->phone ?? 'Không có' }}</div>
        </div>
        
        <div class="invoice-details">
            <div class="info-title">THÔNG TIN ĐƠN HÀNG</div>
            <div><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</div>
            <div><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
            <div><strong>Phương thức thanh toán:</strong> {{ $order->paymentMethod->name ?? 'Không xác định' }}</div>
        </div>
    </div>

    <div class="refund-summary">
        <h3>THÔNG TIN HOÀN TIỀN</h3>
        <div><strong>Lý do hoàn tiền:</strong> 
            @switch($refundRequest->reason)
                @case('wrong_item')
                    Sản phẩm không đúng mô tả
                    @break
                @case('quality_issue')
                    Sản phẩm có vấn đề về chất lượng
                    @break
                @case('shipping_delay')
                    Giao hàng chậm trễ
                    @break
                @case('wrong_qty')
                    Số lượng không chính xác
                    @break
                @default
                    Khác
            @endswitch
        </div>
        <div><strong>Chi tiết:</strong> {{ $refundRequest->details }}</div>
        <div><strong>Phương thức hoàn tiền:</strong> 
            @if($refundRequest->refund_method === 'wallet')
                Hoàn vào ví
            @else
                Hoàn qua VNPay
            @endif
        </div>
        <div><strong>Ngày xử lý:</strong> {{ $invoice->refund_processed_at->format('d/m/Y H:i') }}</div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->book->title ?? 'Sản phẩm đã xóa' }}</td>
                <td>{{ abs($item->quantity) }}</td>
                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                <td>{{ number_format(abs($item->quantity * $item->price), 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <div class="total-label">Số tiền hoàn:</div>
            <div class="total-amount">{{ number_format($invoice->refund_amount, 0, ',', '.') }}đ</div>
        </div>
    </div>

    @if($refundRequest->refund_method === 'wallet')
    <div class="note">
        <strong>Lưu ý:</strong> Số tiền {{ number_format($invoice->refund_amount, 0, ',', '.') }}đ đã được cộng vào ví của bạn và có thể sử dụng ngay lập tức.
    </div>
    @else
    <div class="note">
        <strong>Lưu ý:</strong> Số tiền {{ number_format($invoice->refund_amount, 0, ',', '.') }}đ sẽ được hoàn về phương thức thanh toán ban đầu trong vòng 3-5 ngày làm việc.
    </div>
    @endif

    <div class="footer">
        <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
        <p>Mọi thắc mắc xin vui lòng liên hệ: support@bookstore.com | Hotline: 1900-xxxx</p>
    </div>
</body>
</html>
