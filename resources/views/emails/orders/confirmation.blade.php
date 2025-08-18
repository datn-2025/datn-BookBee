<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng</title>
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
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 3px solid #2c3e50;
        }
        
        .order-info p {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }
        
        .order-info strong {
            font-weight: 500;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .order-table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 500;
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .order-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .order-table tbody tr:hover {
            background-color: #fafafa;
        }
        
        .product-cell {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .product-image {
            width: 50px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
            flex-shrink: 0;
        }
        
        .product-image-placeholder {
            width: 50px;
            height: 60px;
            background-color: #f0f0f0;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #999;
            flex-shrink: 0;
        }
        
        .product-info {
            flex-grow: 1;
        }
        
        .product-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .product-format {
            font-size: 12px;
            color: #666;
            background-color: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .quantity-cell, .price-cell {
            text-align: center;
            font-weight: 500;
        }
        
        .total-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .total-row:last-child {
            border-bottom: 2px solid #333;
            padding-top: 12px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .total-row.discount .total-value {
            color: #d73527;
        }
        
        .shipping-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 3px solid #28a745;
        }
        
        .delivery-method {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: white;
            border-radius: 4px;
            display: inline-block;
        }
        
        .contact-info p {
            margin-bottom: 8px;
            display: flex;
        }
        
        .contact-info strong {
            min-width: 140px;
            font-weight: 500;
        }
        
        .note-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 12px;
            border-radius: 4px;
            margin-top: 15px;
            font-style: italic;
            color: #856404;
        }
        
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 25px;
            border-top: 1px solid #eee;
        }
        
        .footer p {
            margin-bottom: 5px;
            color: #666;
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
            
            .order-table {
                font-size: 14px;
            }
            
            .order-table th,
            .order-table td {
                padding: 10px 8px;
            }
            
            .product-cell {
                flex-direction: column;
                gap: 8px;
            }
            
            .product-image,
            .product-image-placeholder {
                align-self: flex-start;
            }
            
            .contact-info p {
                flex-direction: column;
            }
            
            .contact-info strong {
                min-width: auto;
                margin-bottom: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Xác nhận đơn hàng</h1>
            <p>Cảm ơn bạn đã đặt hàng tại BookBee!</p>
        </div>

        <div class="content">
            <div class="section">
                <h2 class="section-title">Thông tin đơn hàng</h2>
                <div class="order-info">
                    <p><strong>Mã đơn hàng:</strong> <span>{{ $order->order_code }}</span></p>
                    <p><strong>Ngày đặt:</strong> <span>{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
                    <p><strong>Trạng thái:</strong> <span>{{ $order->orderStatus->name }}</span></p>
                    <p><strong>Phương thức thanh toán:</strong> <span>{{ $order->paymentMethod->name }}</span></p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Chi tiết đơn hàng</h2>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center; width: 80px;">Số lượng</th>
                            <th style="text-align: center; width: 120px;">Đơn giá</th>
                            <th style="text-align: center; width: 120px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <div class="product-cell">
                                    @if($item->is_combo)
                                        @if(isset($item->collection->image) && $item->collection->image)
                                            <img src="{{ $item->collection->image }}" alt="Combo" class="product-image">
                                        @else
                                            <div class="product-image-placeholder">COMBO</div>
                                        @endif
                                    @else
                                        @if(isset($item->book->cover_image) && $item->book->cover_image)
                                            <img src="{{ $item->book->cover_image }}" alt="Book cover" class="product-image">
                                        @else
                                            <div class="product-image-placeholder">📖</div>
                                        @endif
                                    @endif
                                    
                                    <div class="product-info">
                                        <div class="product-name">
                                            @if($item->is_combo)
                                                {{ $item->collection->name ?? 'Combo không xác định' }}
                                            @else
                                                {{ $item->book->title ?? 'Sách không xác định' }}
                                            @endif
                                        </div>
                                        @if($item->is_combo)
                                            <span class="product-format">Combo</span>
                                        @elseif($item->bookFormat)
                                            <span class="product-format">{{ $item->bookFormat->format_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="quantity-cell">{{ $item->quantity }}</td>
                            <td class="price-cell">{{ number_format($item->price) }} VNĐ</td>
                            <td class="price-cell">{{ number_format($item->total) }} VNĐ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="total-section">
                    <div class="total-row">
                        <span>Tạm tính:</span>
                        <span class="total-value">{{ number_format($order->orderItems->sum('total')) }} VNĐ</span>
                    </div>
                    <div class="total-row">
                        <span>Phí vận chuyển:</span>
                        <span class="total-value">{{ number_format($order->shipping_fee) }} VNĐ</span>
                    </div>
                    @if($order->voucher)
                    <div class="total-row discount">
                        <span>Giảm giá:</span>
                        <span class="total-value">{{ number_format($order->orderItems->sum('total') + $order->shipping_fee - $order->total_amount) }} VNĐ</span>
                    </div>
                    @endif
                    <div class="total-row">
                        <span>Tổng tiền:</span>
                        <span class="total-value">{{ number_format($order->total_amount) }} VNĐ</span>
                    </div>
                </div>
            </div>

            <div class="section">
                @if($order->delivery_method === 'ebook')
                <h2 class="section-title">Thông tin ebook</h2>
                <div class="shipping-section">
                    <div class="delivery-method">Sách điện tử (Ebook)</div>
                    <div class="contact-info">
                        <p><strong>Người nhận:</strong> {{ $order->recipient_name }}</p>
                        <p><strong>Email:</strong> {{ $order->recipient_email }}</p>
                    </div>
                    <div class="note-box">
                        <strong>Lưu ý:</strong> Link tải ebook sẽ được gửi đến email của bạn sau khi đơn hàng được xác nhận.
                    </div>
                </div>
                @elseif($order->delivery_method === 'pickup')
                <h2 class="section-title">Thông tin nhận hàng</h2>
                <div class="shipping-section">
                    <div class="delivery-method">Nhận tại cửa hàng</div>
                    <div class="contact-info">
                        <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? ($order->address ? $order->address->recipient_name : '') }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? ($order->address ? $order->address->phone : '') }}</p>
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
                    </div>
                    <div class="note-box">
                        Vui lòng mang theo mã đơn hàng <strong>{{ $order->order_code }}</strong> khi đến nhận sách.
                    </div>
                </div>
                @else
                <h2 class="section-title">Thông tin giao hàng</h2>
                <div class="shipping-section">
                    <div class="delivery-method">Giao hàng tận nơi</div>
                    <div class="contact-info">
                        <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? ($order->address ? $order->address->recipient_name : '') }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? ($order->address ? $order->address->phone : '') }}</p>
                        @if($order->address)
                        <p><strong>Địa chỉ:</strong> {{ $order->address->address_detail }}, {{ $order->address->ward }}, {{ $order->address->district }}, {{ $order->address->city }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="footer">
            <p><strong>BookBee - Nơi mua sắm sách trực tuyến</strong></p>
            <p>Email: support@bookbee.com</p>
            <p>Hotline: 1900 1234</p>
        </div>
    </div>
</body>
</html><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng</title>
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
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 3px solid #2c3e50;
        }
        
        .order-info p {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }
        
        .order-info strong {
            font-weight: 500;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .order-table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 500;
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .order-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .order-table tbody tr:hover {
            background-color: #fafafa;
        }
        
        .product-cell {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .product-image {
            width: 50px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
            flex-shrink: 0;
        }
        
        .product-image-placeholder {
            width: 50px;
            height: 60px;
            background-color: #f0f0f0;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #999;
            flex-shrink: 0;
        }
        
        .product-info {
            flex-grow: 1;
        }
        
        .product-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .product-format {
            font-size: 12px;
            color: #666;
            background-color: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .quantity-cell, .price-cell {
            text-align: center;
            font-weight: 500;
        }
        
        .total-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .total-row:last-child {
            border-bottom: 2px solid #333;
            padding-top: 12px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .total-row.discount .total-value {
            color: #d73527;
        }
        
        .shipping-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 3px solid #28a745;
        }
        
        .delivery-method {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: white;
            border-radius: 4px;
            display: inline-block;
        }
        
        .contact-info p {
            margin-bottom: 8px;
            display: flex;
        }
        
        .contact-info strong {
            min-width: 140px;
            font-weight: 500;
        }
        
        .note-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 12px;
            border-radius: 4px;
            margin-top: 15px;
            font-style: italic;
            color: #856404;
        }
        
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 25px;
            border-top: 1px solid #eee;
        }
        
        .footer p {
            margin-bottom: 5px;
            color: #666;
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
            
            .order-table {
                font-size: 14px;
            }
            
            .order-table th,
            .order-table td {
                padding: 10px 8px;
            }
            
            .product-cell {
                flex-direction: column;
                gap: 8px;
            }
            
            .product-image,
            .product-image-placeholder {
                align-self: flex-start;
            }
            
            .contact-info p {
                flex-direction: column;
            }
            
            .contact-info strong {
                min-width: auto;
                margin-bottom: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Xác nhận đơn hàng</h1>
            <p>Cảm ơn bạn đã đặt hàng tại BookBee!</p>
        </div>

        <div class="content">
            <div class="section">
                <h2 class="section-title">Thông tin đơn hàng</h2>
                <div class="order-info">
                    <p><strong>Mã đơn hàng:</strong> <span>{{ $order->order_code }}</span></p>
                    <p><strong>Ngày đặt:</strong> <span>{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
                    <p><strong>Trạng thái:</strong> <span>{{ $order->orderStatus->name }}</span></p>
                    <p><strong>Phương thức thanh toán:</strong> <span>{{ $order->paymentMethod->name }}</span></p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Chi tiết đơn hàng</h2>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center; width: 80px;">Số lượng</th>
                            <th style="text-align: center; width: 120px;">Đơn giá</th>
                            <th style="text-align: center; width: 120px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <div class="product-cell">
                                    @if($item->is_combo)
                                        @if(isset($item->collection->cover_image) && $item->collection->cover_image)
                                            <img src="{{ $item->collection->cover_image }}" alt="Combo" class="product-image">
                                        @else
                                            <div class="product-image-placeholder">COMBO</div>
                                        @endif
                                    @else
                                        @if(isset($item->book->cover_image) && $item->book->cover_image)
                                            <img src="{{ $item->book->cover_image }}" alt="Book cover" class="product-image">
                                        @else
                                            <div class="product-image-placeholder">📖</div>
                                        @endif
                                    @endif
                                    
                                    <div class="product-info">
                                        <div class="product-name">
                                            @if($item->is_combo)
                                                {{ $item->collection->name ?? 'Combo không xác định' }}
                                            @else
                                                {{ $item->book->title ?? 'Sách không xác định' }}
                                            @endif
                                        </div>
                                        @if($item->is_combo)
                                            <span class="product-format">{{$item->collection->name}}</span>
                                        @elseif($item->bookFormat)
                                            <span class="product-format">{{ $item->bookFormat->format_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="quantity-cell">{{ $item->quantity }}</td>
                            <td class="price-cell">{{ number_format($item->price) }} VNĐ</td>
                            <td class="price-cell">{{ number_format($item->total) }} VNĐ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="total-section">
                    <div class="total-row">
                        <span>Tạm tính:</span>
                        <span class="total-value">{{ number_format($order->orderItems->sum('total')) }} VNĐ</span>
                    </div>
                    <div class="total-row">
                        <span>Phí vận chuyển:</span>
                        <span class="total-value">{{ number_format($order->shipping_fee) }} VNĐ</span>
                    </div>
                    @if($order->voucher)
                    <div class="total-row discount">
                        <span>Giảm giá:</span>
                        <span class="total-value">{{ number_format($order->orderItems->sum('total') + $order->shipping_fee - $order->total_amount) }} VNĐ</span>
                    </div>
                    @endif
                    <div class="total-row">
                        <span>Tổng tiền:</span>
                        <span class="total-value">{{ number_format($order->total_amount) }} VNĐ</span>
                    </div>
                </div>
            </div>

            <div class="section">
                @if($order->delivery_method === 'ebook')
                <h2 class="section-title">Thông tin ebook</h2>
                <div class="shipping-section">
                    <div class="delivery-method">Sách điện tử (Ebook)</div>
                    <div class="contact-info">
                        <p><strong>Người nhận:</strong> {{ $order->recipient_name }}</p>
                        <p><strong>Email:</strong> {{ $order->recipient_email }}</p>
                    </div>
                    <div class="note-box">
                        <strong>Lưu ý:</strong> Link tải ebook sẽ được gửi đến email của bạn sau khi đơn hàng được xác nhận.
                    </div>
                </div>
                @elseif($order->delivery_method === 'pickup')
                <h2 class="section-title">Thông tin nhận hàng</h2>
                <div class="shipping-section">
                    <div class="delivery-method">Nhận tại cửa hàng</div>
                    <div class="contact-info">
                        <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? ($order->address ? $order->address->recipient_name : '') }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? ($order->address ? $order->address->phone : '') }}</p>
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
                    </div>
                    <div class="note-box">
                        Vui lòng mang theo mã đơn hàng <strong>{{ $order->order_code }}</strong> khi đến nhận sách.
                    </div>
                </div>
                @else
                <h2 class="section-title">Thông tin giao hàng</h2>
                <div class="shipping-section">
                    <div class="delivery-method">Giao hàng tận nơi</div>
                    <div class="contact-info">
                        <p><strong>Người nhận:</strong> {{ $order->recipient_name ?? ($order->address ? $order->address->recipient_name : '') }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->recipient_phone ?? ($order->address ? $order->address->phone : '') }}</p>
                        @if($order->address)
                        <p><strong>Địa chỉ:</strong> {{ $order->address->address_detail }}, {{ $order->address->ward }}, {{ $order->address->district }}, {{ $order->address->city }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="footer">
            <p><strong>BookBee - Nơi mua sắm sách trực tuyến</strong></p>
            <p>Email: bookbee2025@gmail.com</p>
            <p>Hotline: 0966701154</p>
        </div>
    </div>
</body>
</html>