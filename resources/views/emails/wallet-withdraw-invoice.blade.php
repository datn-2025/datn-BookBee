<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn rút tiền từ ví điện tử</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin: 20px 0;
            text-align: center;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .info-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
        }
        .info-section h3 {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-section p {
            margin: 5px 0;
            font-size: 14px;
        }
        .transaction-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            color: #000;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .bank-info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .bank-info h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        @media (max-width: 600px) {
            .invoice-info {
                flex-direction: column;
            }
            .detail-row {
                flex-direction: column;
                text-align: left;
            }
            .detail-value {
                margin-top: 5px;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="logo">
                {{ $storeSettings->store_name ?? 'BOOKSTORE' }}
            </div>
            <p style="margin: 0; color: #666; font-size: 14px;">
                {{ $storeSettings->store_address ?? 'Địa chỉ cửa hàng' }}
            </p>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                Email: {{ $storeSettings->store_email ?? 'email@store.com' }} | 
                Điện thoại: {{ $storeSettings->store_phone ?? '0123456789' }}
            </p>
        </div>

        {{-- Invoice Title --}}
        <div class="invoice-title">
            HÓA ĐƠN RÚT TIỀN TỪ VÍ ĐIỆN TỬ
        </div>

        {{-- Invoice Info --}}
        <div class="invoice-info">
            <div class="info-section">
                <h3>Thông tin hóa đơn</h3>
                <p><strong>Số hóa đơn:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Ngày tạo:</strong> {{ $invoice->issued_at->format('d/m/Y H:i:s') }}</p>
                <p><strong>Mã giao dịch:</strong> {{ $transaction->id }}</p>
                <p><strong>Trạng thái:</strong> 
                    <span class="status-badge status-success">Đã xử lý</span>
                </p>
            </div>
            
            <div class="info-section">
                <h3>Thông tin khách hàng</h3>
                <p><strong>Họ tên:</strong> {{ $transaction->wallet->user->name }}</p>
                <p><strong>Email:</strong> {{ $transaction->wallet->user->email }}</p>
                <p><strong>Điện thoại:</strong> {{ $transaction->wallet->user->phone ?? 'Chưa cập nhật' }}</p>
            </div>
        </div>

        {{-- Transaction Details --}}
        <div class="transaction-details">
            <h3 style="margin-top: 0; color: #000; font-size: 18px; margin-bottom: 15px;">CHI TIẾT GIAO DỊCH</h3>
            
            <div class="detail-row">
                <span class="detail-label">Loại giao dịch:</span>
                <span class="detail-value">Rút tiền từ ví điện tử</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Số tiền rút:</span>
                <span class="detail-value">{{ number_format($transaction->amount, 0, ',', '.') }} VNĐ</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Phí giao dịch:</span>
                <span class="detail-value">0 VNĐ</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tổng tiền nhận được:</span>
                <span class="detail-value">{{ number_format($transaction->amount, 0, ',', '.') }} VNĐ</span>
            </div>
        </div>

        {{-- Bank Info --}}
        @if($transaction->bank_name && $transaction->bank_number)
        <div class="bank-info">
            <h4>THÔNG TIN TÀI KHOẢN NHẬN TIỀN</h4>
            <p><strong>Ngân hàng:</strong> {{ $transaction->bank_name }}</p>
            <p><strong>Số tài khoản:</strong> {{ $transaction->bank_number }}</p>
            <p><strong>Chủ tài khoản:</strong> {{ $transaction->customer_name }}</p>
        </div>
        @endif

        {{-- Description --}}
        @if($transaction->description)
        <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
            <h4 style="margin: 0 0 10px 0; color: #000;">Ghi chú:</h4>
            <p style="margin: 0; font-style: italic;">{{ $transaction->description }}</p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p><strong>Cảm ơn bạn đã sử dụng dịch vụ ví điện tử của chúng tôi!</strong></p>
            <p>Đây là email tự động, vui lòng không trả lời email này.</p>
            <p>Nếu có thắc mắc, vui lòng liên hệ: {{ $storeSettings->store_email ?? 'support@store.com' }}</p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #e9ecef;">
            <p style="margin: 0; font-size: 11px;">
                © {{ date('Y') }} {{ $storeSettings->store_name ?? 'BOOKSTORE' }}. Tất cả quyền được bảo lưu.
            </p>
        </div>
    </div>
</body>
</html>