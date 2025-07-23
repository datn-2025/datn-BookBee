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
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background: #dc3545;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .content {
            padding: 20px;
        }
        .invoice-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .refund-amount {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            margin: 20px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .reason-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>HÓA ĐƠN HOÀN TIỀN</h1>
            <p>Mã hóa đơn: {{ $invoice->id }}</p>
        </div>

        <div class="content">
            <div class="invoice-info">
                <div class="info-row">
                    <span class="info-label">Mã đơn hàng:</span>
                    <span>{{ $order->order_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Khách hàng:</span>
                    <span>{{ $order->user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span>{{ $order->user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày hoàn tiền:</span>
                    <span>{{ $invoice->refund_processed_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phương thức hoàn tiền:</span>
                    <span>
                        @if($invoice->refund_method === 'wallet')
                            Hoàn vào ví tài khoản
                        @else
                            Hoàn qua VNPay
                        @endif
                    </span>
                </div>
            </div>

            @if($refundRequest->reason)
            <div class="reason-box">
                <h3 style="margin-top: 0;">Lý do hoàn tiền:</h3>
                <p style="margin-bottom: 0;">
                    @switch($refundRequest->reason)
                        @case('wrong_item')
                            Sản phẩm không đúng mô tả
                            @break
                        @case('quality_issue')
                            Sản phẩm có vấn đề về chất lượng
                            @break
                        @case('shipping_delay')
                            Giao hàng quá chậm
                            @break
                        @case('wrong_qty')
                            Số lượng không đúng
                            @break
                        @default
                            Khác
                    @endswitch
                </p>
                @if($refundRequest->details)
                    <p><strong>Chi tiết:</strong> {{ $refundRequest->details }}</p>
                @endif
            </div>
            @endif

            <div class="refund-amount">
                Số tiền hoàn: {{ number_format($invoice->refund_amount, 0, ',', '.') }}đ
            </div>

            @if($invoice->items->count() > 0)
            <h3>Chi tiết sản phẩm hoàn tiền:</h3>
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
                        <td>{{ $item->book->title ?? 'N/A' }}</td>
                        <td>{{ abs($item->quantity) }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                        <td>{{ number_format(abs($item->quantity) * $item->price, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <p>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi. Chúng tôi xin lỗi vì sự bất tiện này và hy vọng được phục vụ quý khách tốt hơn trong tương lai.</p>
        </div>

        <div class="footer">
            <p>Đây là email tự động, vui lòng không trả lời email này.</p>
            <p>Nếu có thắc mắc, vui lòng liên hệ: support@bookstore.com</p>
        </div>
    </div>
</body>
</html>