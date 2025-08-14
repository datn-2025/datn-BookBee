<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Biên lai giao dịch ví #{{ $transaction->id }}</title>
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
            margin-top: 5px;
        }

        .section {
            margin-bottom: 30px;
        }

        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-box {
            width: 48%;
            background-color: #F7FAFC;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #4299E1;
        }

        .info-box h3 {
            color: #2D3748;
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 15px 0;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 8px;
        }

        .info-box p {
            margin: 8px 0;
            color: #4A5568;
        }

        .highlight {
            font-weight: bold;
            color: #2D3748;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-completed {
            background-color: #C6F6D5;
            color: #2F855A;
        }

        .status-pending {
            background-color: #FEEBC8;
            color: #C05621;
        }

        .status-failed {
            background-color: #FED7D7;
            color: #C53030;
        }

        .transaction-details {
            background-color: #F7FAFC;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #2D3748;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #EDF2F7;
            border-radius: 8px;
        }

        .amount.deposit {
            color: #2F855A;
        }

        .amount.withdraw {
            color: #C53030;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #718096;
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" alt="BookBee Logo" class="logo">
            <h1 class="invoice-title">BIÊN LAI GIAO DỊCH VÍ ĐIỆN TỬ</h1>
            <p class="invoice-number">Mã giao dịch: #{{ $transaction->id }}</p>
            <p class="invoice-number">Ngày giao dịch: {{ $transaction->created_at->format('H:i:s d/m/Y') }}</p>
        </div>

        <div class="info-container">
            <div class="info-box">
                <h3>THÔNG TIN KHÁCH HÀNG</h3>
                <p><span class="highlight">{{ $transaction->wallet->user->name }}</span></p>
                <p>Email: {{ $transaction->wallet->user->email }}</p>
                <p>SĐT: {{ $transaction->wallet->user->phone ?? 'N/A' }}</p>
                <p>Số dư ví hiện tại: <span class="highlight">{{ number_format($transaction->wallet->balance) }}đ</span></p>
            </div>

            <div class="info-box">
                <h3>THÔNG TIN GIAO DỊCH</h3>
                <p><strong>Loại giao dịch:</strong><br>
                    @if($transaction->type === 'deposit')
                        Nạp tiền vào ví
                    @elseif($transaction->type === 'withdraw')
                        Rút tiền từ ví
                    @elseif($transaction->type === 'payment')
                        Thanh toán đơn hàng
                    @elseif($transaction->type === 'refund')
                        Hoàn tiền
                    @else
                        {{ ucfirst($transaction->type) }}
                    @endif
                </p>
                <p><strong>Phương thức:</strong><br>
                    @if($transaction->payment_method == 'bank_transfer')
                        Chuyển khoản ngân hàng
                    @elseif($transaction->payment_method == 'vnpay')
                        VNPay
                    @else
                        {{ $transaction->payment_method ?? 'N/A' }}
                    @endif
                </p>
                <p><strong>Trạng thái:</strong><br>
                    <span class="status-badge 
                        @if($transaction->status == 'completed') status-completed
                        @elseif($transaction->status == 'pending') status-pending
                        @else status-failed @endif">
                        @if($transaction->status == 'completed')
                            Thành công
                        @elseif($transaction->status == 'pending') 
                            Đang xử lý
                        @elseif($transaction->status == 'failed')
                            Thất bại
                        @else
                            {{ ucfirst($transaction->status) }}
                        @endif
                    </span>
                </p>
            </div>
        </div>

        <div class="amount {{ $transaction->type == 'deposit' ? 'deposit' : 'withdraw' }}">
            @if($transaction->type == 'deposit')
                + {{ number_format($transaction->amount) }}đ
            @else
                - {{ number_format($transaction->amount) }}đ
            @endif
        </div>

        @if($transaction->description)
        <div class="transaction-details">
            <h3>Mô tả giao dịch</h3>
            <p>{{ $transaction->description }}</p>
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <div style="width: 150px; height: 150px; border: 2px solid #f00; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; position: relative;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; color: #f00; font-size: 16px;">CÔNG TY BOOKBEE</div>
                        <div style="color: #f00; font-size: 14px;">
                            @if($transaction->status == 'completed')
                                ĐÃ XÁC NHẬN
                            @else
                                ĐANG XỬ LÝ
                            @endif
                        </div>
                    </div>
                </div>
                <div style="font-style: italic; color: #666;">(Ký và đóng dấu)</div>
            </div>

            <div style="margin-top: 50px; text-align: center;">
                <div style="font-weight: bold;">Người xử lý giao dịch</div>
                <div style="margin-top: 40px; font-weight: bold;">Dũng</div>
                <div style="font-style: italic;">Nguyễn Tiến Dũng</div>
            </div>
        </div>

        <div class="footer">
            <p>-----------------------------------</p>
            <p>Trân trọng cảm ơn quý khách đã sử dụng dịch vụ ví điện tử BookBee!</p>
            <p>Mọi thắc mắc xin liên hệ: 1900 1234 - Email: support@bookbee.vn</p>
            <p>Biên lai này được tạo tự động bởi hệ thống vào {{ now()->format('H:i:s d/m/Y') }}</p>
        </div>
    </div>
</body>

</html>
