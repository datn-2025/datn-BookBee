<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận mua ebook thành công</title>
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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #4CAF50;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
        }
        .book-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác nhận mua ebook thành công</h1>
        </div>
        
        <div class="content">
            <p>Xin chào {{ $order->user->name }},</p>
            
            <p>Cảm ơn bạn đã mua ebook tại BookBee. Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn đã được xác nhận thanh toán thành công.</p>

            <h3>Chi tiết đơn hàng:</h3>
            @foreach($order->orderItems as $item)
                @if($item->bookFormat && $item->bookFormat->format_name === 'Ebook')
                <div class="book-item">
                    <h4>{{ $item->book->title }}</h4>
                    <p>Định dạng: Ebook</p>
                    <p>Tác giả: {{ $item->book->authors->first()->name }}</p>
                    <a href="{{ asset('storage/' . $item->bookFormat->file_url) }}" class="button" target="_blank">
                        Tải Ebook
                    </a>
                </div>
                @endif
            @endforeach

            <p>Nếu bạn gặp bất kỳ vấn đề nào trong việc tải hoặc đọc ebook, vui lòng liên hệ với chúng tôi qua email support@bookbee.com.</p>

            <p>Lưu ý: Link tải ebook có thể hết hạn sau một thời gian, vui lòng tải về càng sớm càng tốt.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} BookBee. Mọi quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
