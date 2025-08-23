<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận mua ebook thành công</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }
        
        .email-container {
            max-width: 650px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .content {
            padding: 30px;
        }
        
        .greeting {
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 3px solid #2c3e50;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        
        .book-item {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        
        .book-image {
            width: 60px;
            height: 75px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            flex-shrink: 0;
        }
        
        .book-image-placeholder {
            width: 60px;
            height: 75px;
            background-color: #e9ecef;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #6c757d;
            flex-shrink: 0;
        }
        
        .book-info {
            flex-grow: 1;
        }
        
        .book-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .book-format {
            font-size: 13px;
            color: #6c757d;
            background-color: #e9ecef;
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
            margin-bottom: 8px;
        }
        
        .book-author {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 12px;
        }
        
        .download-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .download-button:hover {
            background-color: #34495e;
        }
        
        .note-section {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 6px;
            margin-top: 25px;
            color: #856404;
        }
        
        .note-section p {
            margin-bottom: 10px;
        }
        
        .note-section p:last-child {
            margin-bottom: 0;
        }
        
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 25px;
            border-top: 1px solid #eee;
            color: #6c757d;
            font-size: 14px;
        }
        
        .footer strong {
            color: #2c3e50;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 4px;
            }
            
            .content {
                padding: 20px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .book-item {
                flex-direction: column;
                gap: 12px;
            }
            
            .book-image,
            .book-image-placeholder {
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Xác nhận mua ebook thành công</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                <p>Xin chào <strong>{{ $order->user->name }}</strong>,</p>
            </div>
            
            <div class="order-info">
                <p>Cảm ơn bạn đã mua ebook tại BookBee. Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn đã được xác nhận thanh toán thành công.</p>
            </div>

            <h3 class="section-title">Chi tiết ebook có thể tải:</h3>
            
            @foreach($order->orderItems as $item)
                @if(!$item->is_combo && $item->book)
                    {{-- Trường hợp 1: Mua trực tiếp ebook --}}
                    @if($item->bookFormat && $item->bookFormat->format_name === 'Ebook')
                    <div class="book-item">
                        @if(isset($item->book->cover_image) && $item->book->cover_image)
                            <img src="{{ $item->book->cover_image }}" alt="Book cover" class="book-image">
                        @else
                            <div class="book-image-placeholder">📖</div>
                        @endif
                        
                        <div class="book-info">
                            <h4 class="book-title">{{ $item->book->title ?? 'Sách không xác định' }}</h4>
                            <div class="book-format">Ebook (Mua trực tiếp)</div>
                            @if($item->book->authors->isNotEmpty())
                                <p class="book-author">Tác giả: {{ $item->book->authors->first()->name }}</p>
                            @endif
                            <a href="{{ route('ebook.download', $item->bookFormat->id) }}?order_id={{ $order->id }}" class="download-button" target="_blank">
                                Tải Ebook
                            </a>
                        </div>
                    </div>
                    
                    {{-- Trường hợp 2: Mua sách vật lý nhưng có ebook kèm theo --}}
                    @elseif($item->bookFormat && $item->bookFormat->format_name !== 'Ebook' && $item->book->formats->contains('format_name', 'Ebook'))
                        @php
                            $ebookFormat = $item->book->formats->where('format_name', 'Ebook')->first();
                        @endphp
                        @if($ebookFormat && $ebookFormat->file_url)
                        <div class="book-item">
                            @if(isset($item->book->cover_image) && $item->book->cover_image)
                                <img src="{{ $item->book->cover_image }}" alt="Book cover" class="book-image">
                            @else
                                <div class="book-image-placeholder">📖</div>
                            @endif
                            
                            <div class="book-info">
                                <h4 class="book-title">{{ $item->book->title ?? 'Sách không xác định' }}</h4>
                                <div class="book-format">Ebook (Kèm theo sách vật lý)</div>
                                @if($item->book->authors->isNotEmpty())
                                    <p class="book-author">Tác giả: {{ $item->book->authors->first()->name }}</p>
                                @endif
                                <a href="{{ route('ebook.download', $ebookFormat->id) }}?order_id={{ $order->id }}" class="download-button" target="_blank">
                                    Tải Ebook
                                </a>
                            </div>
                        </div>
                        @endif
                    @endif
                @endif
            @endforeach

            <div class="note-section">
                <p><strong>Lưu ý quan trọng:</strong></p>
                <p>• Nếu bạn gặp bất kỳ vấn đề nào trong việc tải hoặc đọc ebook, vui lòng liên hệ với chúng tôi qua email <strong>support@bookbee.com</strong>.</p>
                <p>• Link tải ebook có thể hết hạn sau một thời gian, vui lòng tải về càng sớm càng tốt.</p>
            </div>
        </div>

        <div class="footer">
            <p><strong>BookBee - Nơi mua sắm sách trực tuyến</strong></p>
            <p>© {{ date('Y') }} BookBee. Mọi quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>