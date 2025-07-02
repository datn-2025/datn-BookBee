<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Hóa đơn #{{ $invoice->order->order_code }}</title>
    <style>
        @font-face {
            font-family: 'Roboto';
            src: url({{ storage_path('fonts/Roboto-Regular.ttf') }}) format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Roboto';
            src: url({{ storage_path('fonts/Roboto-Bold.ttf') }}) format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'Roboto', 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #2D3748;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4A5568;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 10px;
        }

        .invoice-title {
            color: #2D3748;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .invoice-number {
            color: #718096;
            font-size: 16px;
            margin: 5px 0;
        }

        .invoice-date {
            color: #718096;
            font-size: 14px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #4A5568;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #E2E8F0;
        }

        .info-grid {
            margin-bottom: 30px;
        }

        .info-box {
            background-color: #F7FAFC;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            color: #4A5568;
            font-size: 16px;
            margin: 0 0 10px 0;
        }

        .info-box p {
            margin: 5px 0;
            color: #4A5568;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: white;
        }

        th {
            background-color: #F7FAFC;
            color: #4A5568;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #E2E8F0;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #E2E8F0;
            color: #4A5568;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            float: right;
            width: 350px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #E2E8F0;
        }

        .total-row.final {
            border-top: 2px solid #4A5568;
            border-bottom: 2px solid #4A5568;
            font-weight: bold;
            font-size: 16px;
            color: #2D3748;
            padding: 12px 0;
            margin-top: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #718096;
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-paid {
            background-color: #C6F6D5;
            color: #2F855A;
        }

        .status-pending {
            background-color: #FEEBC8;
            color: #C05621;
        }

        .highlight {
            color: #4A5568;
            font-weight: bold;
        }

        .book-title {
            color: #2D3748;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .book-author {
            color: #718096;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" alt="BookBee Logo" class="logo">
            <h1 class="invoice-title">HÓA ĐƠN BOOKBEE</h1>
            <p class="invoice-number">Mã đơn hàng: #{{ $invoice->order->order_code }}</p>
            <p class="invoice-date">Ngày: {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>THÔNG TIN KHÁCH HÀNG</h3>
                <p><span class="highlight">{{ $invoice->order->user->name }}</span></p>
                <p>Email: {{ $invoice->order->user->email }}</p>
                <p>SĐT: {{ $invoice->order->user->phone }}</p>
                <p>Địa chỉ giao hàng:<br>{{ $invoice->order->address->full_address }}</p>
            </div>

            <div class="info-box">
                <h3>THÔNG TIN ĐƠN HÀNG</h3>
                <p><strong>Phương thức thanh toán:</strong><br>{{ $invoice->order->payment_method }}</p>
                <p><strong>Trạng thái thanh toán:</strong><br>
                    <span
                        class="status-badge {{ $invoice->order->payment_status == 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ $invoice->order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                    </span>
                </p>
                <p><strong>Ngày đặt hàng:</strong><br>{{ $invoice->order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">CHI TIẾT ĐƠN HÀNG</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="width: 100px">Đơn giá</th>
                        <th style="width: 80px">Số lượng</th>
                        <th style="width: 120px" class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalQuantity = 0;
                    @endphp
                    @foreach ($invoice->items as $item)
                        @php
                            $totalQuantity += $item->quantity;
                        @endphp
                        <tr>
                            <td>
                                <div class="book-title">{{ $item->book->title }}</div>
                                <div class="book-author">Tác giả: {{ $item->book->author->name }}</div>
                            </td>
                            <td>{{ number_format($item->price) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->quantity * $item->price) }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="totals">
                <div class="total-row">
                    <span>Tổng số lượng:</span>
                    <span>{{ number_format($totalQuantity) }}</span>
                </div>
                <div class="total-row">
                    <span>Tổng tiền hàng:</span>
                    <span>{{ number_format($invoice->subtotal) }}đ</span>
                </div>
                @if ($invoice->discount > 0)
                    <div class="total-row">
                        <span>Giảm giá:</span>
                        <span>-{{ number_format($invoice->discount) }}đ</span>
                    </div>
                @endif
                <div class="total-row">
                    <span>Phí vận chuyển:</span>
                    <span>{{ number_format($invoice->shipping_fee) }}đ</span>
                </div>
                <div class="total-row final">
                    <span>Tổng thanh toán:</span>
                    <span>{{ number_format($invoice->total_amount) }}đ</span>
                </div>
            </div>
        </div>

        <div style="clear: both;"></div>

        <!-- <div class="footer">
            <p>Cảm ơn bạn đã mua sách tại BookBee!</p>
            <p>Mọi thắc mắc xin vui lòng liên hệ: {{ config('app.phone') }} hoặc email {{ config('app.email') }}</p>
            <p>Website: {{ config('app.url') }}</p>
        </div> -->
        <div class="footer">
            <div style="margin-bottom: 30px;">
                <p>Ngày in hóa đơn: {{ now()->format('H:i:s d/m/Y') }}</p>
            </div>

            <div style="text-align: center; margin-top: 50px; position: relative;">
                <div style="display: inline-block; text-align: center;">
                    <div
                        style="width: 150px; height: 150px; border: 2px solid #f00; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; position: relative;">
                        <div style="text-align: center;">
                            <div style="font-weight: bold; color: #f00; font-size: 16px;">CÔNG TY BOOKBEE</div>
                            <div style="color: #f00; font-size: 14px;">ĐÃ THANH TOÁN</div>
                        </div>
                    </div>
                    <div style="font-style: italic; color: #666;">(Ký và đóng dấu)</div>
                </div>

                <div style="margin-top: 50px; text-align: center;">
                    <div style="font-weight: bold;">Người lập hóa đơn</div>
                    <div style="font-style: italic; margin-top: 40px;">(Ký và ghi rõ họ tên)</div>
                </div>
            </div>

            <p>-----------------------------------</p>
            <p>Trân trọng cảm ơn quý khách đã sử dụng dịch vụ của BookBee!</p>
            <p>Mọi thắc mắc xin liên hệ: 1900 1234 - Email: support@bookbee.vn</p>
        </div>
    </div>
</body>

</html>
