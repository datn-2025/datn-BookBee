@extends('layouts.account.layout')
@section('title', 'Cập nhât đánh giá')

@push('styles')
    <style>
        .order-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .order-card:hover {
            border-color: #000;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .geometric-bg::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(0, 0, 0, 0.05);
            transform: rotate(45deg) translate(50px, -50px);
        }

        .cancel-form {
            display: none;
        }

        .cancel-form.active {
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Back Button - Adidas Style -->
            <div class="mb-8">
                <a href="{{ route('account.orders.unified') }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-gray-300 hover:border-black text-black font-bold uppercase tracking-wide transition-all duration-300 hover:bg-gray-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    QUAY LẠI DANH SÁCH
                </a>
            </div>

            <!-- Order Header - Adidas Style -->
            <div
                class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 relative overflow-hidden group mb-8 geometric-bg">
                <div class="bg-black text-white px-8 py-6 relative">
                    <div
                        class="absolute top-0 right-0 w-16 h-16 bg-white/10 transform rotate-45 translate-x-8 -translate-y-8">
                    </div>
                    <div class="relative z-10">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-4 mb-2">
                                    <div class="w-1 h-8 bg-white"></div>
                                    <h1 class="text-3xl font-black uppercase tracking-wide">CHI TIẾT ĐƠN HÀNG</h1>
                                </div>
                                <p class="text-gray-300 text-sm uppercase tracking-wider">MÃ ĐƠN HÀNG:
                                    {{ $order->order_code }}</p>
                                @if ($order->delivery_method === 'mixed')
                                    <div
                                        class="mt-2 px-3 py-1 bg-yellow-500 text-black text-xs font-bold uppercase tracking-wide rounded">
                                        ĐƠN HÀNG HỖN HỢP (SÁCH VẬT LÝ + EBOOK)
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                @if ($order->refundRequests->isNotEmpty())
                                    @php
                                        $latestRefund = $order->refundRequests->sortByDesc('created_at')->first();
                                        $refundStatusClass = match ($latestRefund->status) {
                                            'pending' => 'bg-yellow-500 text-white',
                                            'processing' => 'bg-blue-500 text-white',
                                            'completed' => 'bg-green-500 text-white',
                                            'rejected' => 'bg-red-500 text-white',
                                            default => 'bg-gray-500 text-white',
                                        };
                                        $refundStatusText = match ($latestRefund->status) {
                                            'pending' => 'CHỜ HOÀN TIỀN',
                                            'processing' => 'ĐANG HOÀN TIỀN',
                                            'completed' => 'ĐÃ HOÀN TIỀN',
                                            'rejected' => 'TỪ CHỐI HOÀN TIỀN',
                                            default => 'HOÀN TIỀN',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $refundStatusClass }}">
                                        {{ $refundStatusText }}
                                    </span>
                                @else
                                    @php
                                        $orderStatusName = $order->orderStatus->name ?? '';
                                        $orderStatusClass = match ($orderStatusName) {
                                            'Chờ xác nhận' => 'bg-yellow-500 text-white',
                                            'Đã xác nhận' => 'bg-blue-500 text-white',
                                            'Đang chuẩn bị' => 'bg-indigo-500 text-white',
                                            'Đang đóng gói' => 'bg-orange-500 text-white',
                                            'Đang giao hàng' => 'bg-purple-500 text-white',
                                            'Đã giao hàng' => 'bg-green-500 text-white',
                                            'Đã giao thành công' => 'bg-green-500 text-white',
                                            'Đã giao', 'Thành công' => 'bg-green-500 text-white',
                                            'Đã hủy' => 'bg-red-500 text-white',
                                            'Hoàn trả' => 'bg-gray-500 text-white',
                                            default => 'bg-gray-500 text-white',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $orderStatusClass }}">
                                        {{ $order->orderStatus->name }}
                                    </span>
                                @endif
                                <div class="text-right">
                                    <p class="text-sm text-gray-300 uppercase tracking-wide">Tổng tiền</p>
                                    <p class="text-2xl font-black text-white">
                                        {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Content -->
                <div class="p-8">
                    <!-- Order Info Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Order Details -->
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-1 h-5 bg-black"></div>
                                <h4 class="text-base font-bold uppercase tracking-wide text-black">THÔNG TIN ĐƠN HÀNG</h4>
                            </div>

                            @if ($order->delivery_method === 'mixed')
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                                    <h5 class="font-bold text-blue-800 text-sm mb-4">📦 ĐƠN HÀNG ĐÃ ĐƯỢC CHIA THÀNH 2 PHẦN:
                                    </h5>
                                    @if ($order->childOrders->count() > 0)
                                        <div class="space-y-4">
                                            @foreach ($order->childOrders as $childOrder)
                                                <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                                                    <!-- Child Order Header -->
                                                    <div
                                                        class="flex justify-between items-center mb-3 pb-3 border-b border-gray-200">
                                                        <div>
                                                            <h6
                                                                class="font-bold text-black text-sm uppercase tracking-wide">
                                                                {{ $childOrder->order_code }}</h6>
                                                            <span class="text-xs text-gray-600 uppercase tracking-wide">
                                                                {{ $childOrder->delivery_method === 'delivery' ? '📚 Sách vật lý - Giao hàng' : '💻 Ebook - Gửi email' }}
                                                            </span>
                                                        </div>
                                                        <span
                                                            class="font-bold text-blue-600 text-lg">{{ number_format($childOrder->total_amount) }}đ</span>
                                                    </div>

                                                    <!-- Child Order Items -->
                                                    @if ($childOrder->orderItems->count() > 0)
                                                        <div class="space-y-2">
                                                            @foreach ($childOrder->orderItems as $item)
                                                                <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                                                                    <!-- Product Image -->
                                                                    <div class="flex-shrink-0">
                                                                        <div
                                                                            class="w-12 h-16 bg-gray-200 border border-gray-300 overflow-hidden rounded">
                                                                            @if ($item->isCombo())
                                                                                @if ($item->collection && $item->collection->cover_image)
                                                                                    <img src="{{ asset('storage/' . $item->collection->cover_image) }}"
                                                                                        alt="{{ $item->collection->name }}"
                                                                                        class="h-full w-full object-cover">
                                                                                @else
                                                                                    <div
                                                                                        class="h-full w-full bg-black flex items-center justify-center">
                                                                                        <span
                                                                                            class="text-white text-xs font-bold">CB</span>
                                                                                    </div>
                                                                                @endif
                                                                            @else
                                                                                @if ($item->book && $item->book->cover_image)
                                                                                    <img src="{{ asset('storage/' . $item->book->cover_image) }}"
                                                                                        alt="{{ $item->book->title }}"
                                                                                        class="h-full w-full object-cover">
                                                                                @else
                                                                                    <div
                                                                                        class="h-full w-full bg-gray-300 flex items-center justify-center">
                                                                                        <span
                                                                                            class="text-gray-600 text-xs">IMG</span>
                                                                                    </div>
                                                                                @endif
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <!-- Product Info -->
                                                                    <div class="flex-1">
                                                                        @if ($item->isCombo())
                                                                            <h6
                                                                                class="font-bold text-black text-xs uppercase tracking-wide">
                                                                                {{ $item->collection->name ?? 'Combo không xác định' }}
                                                                            </h6>
                                                                        @else
                                                                            <h6
                                                                                class="font-bold text-black text-xs uppercase tracking-wide">
                                                                                {{ $item->book->title ?? 'Sách không xác định' }}
                                                                                @if ($item->bookFormat)
                                                                                    <span
                                                                                        class="text-gray-600">({{ $item->bookFormat->format_name }})</span>
                                                                                @endif
                                                                            </h6>

                                                                            <!-- Hiển thị thuộc tính biến thể cho child order -->
                                                                            @if ($item->attributeValues && $item->attributeValues->count() > 0)
                                                                                <div class="flex flex-wrap gap-1 mt-1">
                                                                                    @foreach ($item->attributeValues as $attributeValue)
                                                                                        <span
                                                                                            class="px-1 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded border">
                                                                                            {{ $attributeValue->attribute->name }}:
                                                                                            {{ $attributeValue->value }}
                                                                                        </span>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif

                                                                            <!-- Hiển thị quà tặng cho child order -->
                                                                            @if (
                                                                                $item->book &&
                                                                                    $item->book->gifts &&
                                                                                    $item->book->gifts->count() > 0 &&
                                                                                    $item->bookFormat &&
                                                                                    $item->bookFormat->format_name !== 'Ebook')
                                                                                <div class="mt-1">
                                                                                    <div
                                                                                        class="flex items-center gap-1 mb-1">
                                                                                        <svg class="w-3 h-3 text-red-500"
                                                                                            fill="currentColor"
                                                                                            viewBox="0 0 20 20">
                                                                                            <path
                                                                                                d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z">
                                                                                            </path>
                                                                                        </svg>
                                                                                        <span
                                                                                            class="text-xs font-bold text-red-600">Quà
                                                                                            tặng:</span>
                                                                                    </div>
                                                                                    @foreach ($item->book->gifts as $gift)
                                                                                        <div
                                                                                            class="text-xs text-red-600 bg-red-50 px-1 py-0.5 rounded border border-red-200 mb-1">
                                                                                            {{ $gift->gift_name }}
                                                                                            (x{{ $item->quantity }})
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        @endif

                                                                        <div
                                                                            class="flex items-center gap-3 mt-1 text-xs text-gray-600">
                                                                            <span>SL: {{ $item->quantity }}</span>
                                                                            <span>{{ number_format($item->price) }}đ</span>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Item Total -->
                                                                    <div class="text-right">
                                                                        <p class="text-sm font-bold text-black">
                                                                            {{ number_format($item->price * $item->quantity) }}đ
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-center py-4">
                                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Không
                                                                có sản phẩm</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-sm text-gray-600">Không có đơn hàng con nào.</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">Ngày đặt:</span>
                                    <span class="font-bold text-black">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">Phương thức thanh toán:</span>
                                    <span
                                        class="font-bold text-black">{{ $order->paymentMethod->name ?? 'Không xác định' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">Trạng thái thanh toán:</span>
                                    @php
                                        $paymentStatusName = $order->paymentStatus->name ?? 'Chưa thanh toán';
                                        $paymentStatusClass = match ($paymentStatusName) {
                                            'Đã Thanh Toán' => 'text-green-600 font-bold',
                                            'Chờ Thanh Toán', 'Chờ Xử Lý' => 'text-yellow-600 font-bold',
                                            'Đang Xử Lý' => 'text-blue-600 font-bold',
                                            'Thất Bại' => 'text-red-600 font-bold',
                                            'Chưa thanh toán' => 'text-gray-600 font-bold',
                                            default => 'text-black font-bold',
                                        };
                                    @endphp
                                    <span class="{{ $paymentStatusClass }}">{{ $paymentStatusName }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">Phí vận chuyển:</span>
                                    <span
                                        class="font-bold text-black">{{ number_format($order->shipping_fee ?? 0) }}đ</span>
                                </div>
                                @if ($order->ghn_order_code)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 uppercase tracking-wide">Mã vận đơn GHN:</span>
                                        <span class="font-bold text-black">{{ $order->ghn_order_code }}</span>
                                    </div>
                                @endif
                                @if ($order->expected_delivery_date)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 uppercase tracking-wide">Ngày giao dự kiến:</span>
                                        <span
                                            class="font-bold text-black">{{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Delivery Info -->
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-1 h-5 bg-black"></div>
                                <h4 class="text-base font-bold uppercase tracking-wide text-black">
                                    {{ $order->delivery_method === 'pickup' ? 'THÔNG TIN NHẬN HÀNG' : 'ĐỊA CHỈ GIAO HÀNG' }}
                                </h4>
                            </div>
                            <div class="space-y-2 text-sm">
                                @if ($order->delivery_method === 'ebook')
                                    <p><span class="font-bold text-black">Phương thức:</span> Sách điện tử (Ebook)</p>
                                    <p><span class="font-bold text-black">Người nhận:</span>
                                        {{ $order->recipient_name ?? 'Không có thông tin' }}</p>
                                    <p><span class="font-bold text-black">Email:</span>
                                        {{ $order->recipient_email ?? '' }}</p>
                                    <p class="text-black font-bold bg-blue-100 p-2 border-l-4 border-black">Link tải ebook
                                        sẽ được gửi đến email của bạn sau khi đơn hàng được xác nhận.</p>
                                @elseif($order->delivery_method === 'pickup')
                                    <p><span class="font-bold text-black">Phương thức:</span> Nhận tại cửa hàng</p>
                                    <p><span class="font-bold text-black">Người nhận:</span>
                                        {{ $order->recipient_name ?? 'Không có thông tin' }}</p>
                                    <p><span class="font-bold text-black">Số điện thoại:</span>
                                        {{ $order->recipient_phone ?? '' }}</p>
                                    <p><span class="font-bold text-black">Địa chỉ cửa hàng:</span>
                                        @if (isset($storeSettings) && $storeSettings->address)
                                            {{ $storeSettings->address }}
                                        @else
                                            123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                                        @endif
                                    </p>
                                    <p class="text-black font-bold bg-yellow-100 p-2 border-l-4 border-black">Vui lòng mang
                                        theo mã đơn hàng {{ $order->order_code }} khi đến nhận sách.</p>
                                @else
                                    <p><span class="font-bold text-black">Phương thức:</span> Giao hàng tận nơi</p>
                                    <p class="font-bold text-black">{{ $order->recipient_name ?? 'Không có thông tin' }}
                                    </p>
                                    <p>{{ $order->recipient_phone ?? '' }}</p>
                                    @if ($order->address)
                                        <p>{{ $order->address->address_detail ?? '' }}</p>
                                        <p>{{ $order->address->ward ?? '' }}, {{ $order->address->district ?? '' }},
                                            {{ $order->address->city ?? '' }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                     <!-- Order Summary -->
                    <div class="mt-8 border-t-2 border-gray-200 pt-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-5 bg-black"></div>
                            <h4 class="text-base font-bold uppercase tracking-wide text-black">TÓM TẮT ĐƠN HÀNG</h4>
                        </div>
                        <div class="bg-gray-50 border-2 border-gray-200 p-6">
                            <div class="space-y-4">
                                @php
                                    // Tính toán tạm tính dựa trên loại đơn hàng
                                    $subtotal = 0;
                                    $discountAmount = 0;
                                    $appliedVoucher = null;
                                    $voucherDiscount = 0;

                                    // Kiểm tra loại đơn hàng và tính subtotal
                                    if (
                                        $order->delivery_method === 'mixed' &&
                                        is_null($order->parent_order_id) &&
                                        $order->childOrders->count() > 0
                                    ) {
                                        // Đơn hàng cha của mixed order - tính tổng từ các đơn con
                                        $subtotal = $order->childOrders->sum(function ($childOrder) {
                                            return $childOrder->orderItems->sum(function ($item) {
                                                return $item->price * $item->quantity;
                                            });
                                        });

                                        // Nếu vẫn = 0, dùng fallback
                                        if ($subtotal == 0 && $order->total_amount > 0) {
                                            $subtotal =
                                                $order->total_amount - $order->shipping_fee + $order->discount_amount;
                                        }

                                        // Lấy voucher và discount từ đơn cha
                                        if ($order->voucher) {
                                            $appliedVoucher = $order->voucher;
                                        }
                                        $discountAmount = $order->discount_amount;
                                    } elseif ($order->parent_order_id) {
                                        // Đây là đơn hàng con - tính từ orderItems của chính nó
                                        $subtotal = $order->orderItems->sum(function ($item) {
                                            return $item->price * $item->quantity;
                                        });

                                        // Lấy thông tin voucher từ đơn cha
                                        $parentOrder = $order->parentOrder;
                                        if ($parentOrder && $parentOrder->voucher) {
                                            $appliedVoucher = $parentOrder->voucher;
                                        }
                                        // Sử dụng discount_amount được phân bổ cho đơn con
                                        $discountAmount = $order->discount_amount;
                                    } else {
                                        // Đơn hàng đơn lẻ bình thường
                                        $subtotal = $order->orderItems->sum(function ($item) {
                                            return $item->price * $item->quantity;
                                        });

                                        // Fallback nếu subtotal từ orderItems = 0
                                        if ($subtotal == 0 && $order->total_amount > 0) {
                                            $calculatedSubtotal =
                                                $order->total_amount - $order->shipping_fee + $order->discount_amount;
                                            $subtotal = max(0, $calculatedSubtotal); // Đảm bảo không âm
                                        }

                                        if ($order->voucher) {
                                            $appliedVoucher = $order->voucher;
                                            // Tính toán giảm giá dựa trên phần trăm hoặc số tiền cố định
                                            if ($appliedVoucher->discount_percent > 0) {
                                                // Giảm giá theo phần trăm
                                                $discountByPercent =
                                                    $subtotal * ($appliedVoucher->discount_percent / 100);
                                                $voucherDiscount =
                                                    $appliedVoucher->max_discount > 0
                                                        ? min($discountByPercent, $appliedVoucher->max_discount)
                                                        : $discountByPercent;
                                            } else {
                                                // Giảm giá cố định
                                                $voucherDiscount = $appliedVoucher->discount_amount;
                                            }
                                            $discountAmount = $voucherDiscount;
                                        } else {
                                            // Nếu không có voucher, sử dụng giá trị discount_amount từ đơn hàng
                                            $discountAmount = $order->discount_amount;
                                        }
                                    }

                                    // Đảm bảo giảm giá không vượt quá tổng tiền và không âm
                                    $discountAmount = max(0, min($discountAmount, $subtotal));
                                @endphp

                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">Tạm tính</span>
                                    <span class="font-bold text-black"
                                        id="subtotal-amount">{{ number_format($subtotal) }}đ</span>
                                </div>

                                @if ($discountAmount > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 uppercase tracking-wide">
                                            @if ($appliedVoucher)
                                                Mã giảm giá ({{ $appliedVoucher->code }})
                                                @if ($appliedVoucher->discount_percent > 0)
                                                    - {{ $appliedVoucher->discount_percent }}%
                                                    @if ($appliedVoucher->max_discount > 0)
                                                        (tối đa {{ number_format($appliedVoucher->max_discount) }}đ)
                                                    @endif
                                                @endif
                                                @if ($order->parent_order_id)
                                                    <small class="block text-xs text-gray-500 mt-1">
                                                        (Phân bổ từ đơn hàng hỗn hợp)
                                                    </small>
                                                @endif
                                            @else
                                                Giảm giá
                                                @if ($order->parent_order_id)
                                                    <small class="block text-xs text-gray-500 mt-1">
                                                        (Phân bổ từ đơn hàng hỗn hợp)
                                                    </small>
                                                @endif
                                            @endif
                                        </span>
                                        <span class="text-red-600 font-bold"
                                            id="discount-amount">-{{ number_format($discountAmount) }}đ</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600 uppercase tracking-wide">Phí vận chuyển</span>
                                    <span class="font-bold text-black">{{ number_format($order->shipping_fee) }}đ</span>
                                </div>
                                <div class="border-t-2 border-black pt-4 flex justify-between">
                                    <span class="text-lg font-black text-black uppercase tracking-wide">Tổng cộng</span>
                                    <span
                                        class="text-2xl font-black text-black">{{ number_format($order->total_amount) }}đ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Review Section -->
                    <div class="mt-8 border-t-2 border-gray-200 pt-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1 h-5 bg-blue-600"></div>
                            <h4 class="text-base font-bold uppercase tracking-wide text-black">SỬA ĐÁNH GIÁ SẢN PHẨM</h4>
                        </div>

                        <!-- Product Info -->
                          <!-- Order Items -->
                    <div class="border-gray-200">
                        @if (!$order->isParentOrder())
                            @if ($order->orderItems->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($order->orderItems as $item)
                                        <div
                                            class="flex items-center gap-4 p-4 border-2 border-gray-200 hover:border-black transition-all duration-300">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-16 h-20 bg-gray-200 border-2 border-gray-300 overflow-hidden">
                                                    @if ($item->isCombo())
                                                        @if ($item->collection && $item->collection->cover_image)
                                                            <img src="{{ asset('storage/' . $item->collection->cover_image) }}"
                                                                alt="{{ $item->collection->name }}"
                                                                class="h-full w-full object-cover">
                                                        @else
                                                            <div
                                                                class="h-full w-full bg-black flex items-center justify-center">
                                                                <span class="text-white text-xs font-bold">COMBO</span>
                                                            </div>
                                                        @endif
                                                    @else
                                                        @if ($item->book && $item->book->cover_image)
                                                            <img src="{{ asset('storage/' . $item->book->cover_image) }}"
                                                                alt="{{ $item->book->title }}"
                                                                class="h-full w-full object-cover">
                                                        @else
                                                            <div
                                                                class="h-full w-full bg-gray-300 flex items-center justify-center">
                                                                <span class="text-gray-600 text-xs">IMG</span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1">
                                                @if ($item->isCombo())
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span
                                                            class="px-2 py-1 bg-black text-white text-xs font-bold uppercase">COMBO</span>
                                                    </div>
                                                    <h5 class="font-bold text-black text-sm uppercase tracking-wide">
                                                        {{ $item->collection->name ?? 'Combo không xác định' }}
                                                    </h5>
                                                @else
                                                    <h5 class="font-bold text-black text-sm uppercase tracking-wide">
                                                        {{ $item->book->title ?? 'Sách không xác định' }}
                                                        @if ($item->bookFormat)
                                                            <span
                                                                class="text-gray-600">({{ $item->bookFormat->format_name }})</span>
                                                        @endif
                                                    </h5>

                                                    <!-- Hiển thị thuộc tính biến thể -->
                                                    @if ($item->attributeValues && $item->attributeValues->count() > 0)
                                                        <div class="flex flex-wrap gap-2 mt-1">
                                                            @foreach ($item->attributeValues as $attributeValue)
                                                                <span
                                                                    class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded border">
                                                                    {{ $attributeValue->attribute->name }}:
                                                                    {{ $attributeValue->value }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <!-- Hiển thị quà tặng -->
                                                    @if (
                                                        $item->book &&
                                                            $item->book->gifts &&
                                                            $item->book->gifts->count() > 0 &&
                                                            $item->bookFormat &&
                                                            $item->bookFormat->format_name !== 'Ebook')
                                                        <div class="mt-2">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <svg class="w-4 h-4 text-red-500" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z">
                                                                    </path>
                                                                </svg>
                                                                <span
                                                                    class="text-xs font-bold text-red-600 uppercase tracking-wide">Quà
                                                                    tặng kèm:</span>
                                                            </div>
                                                            <div class="space-y-1">
                                                                @foreach ($item->book->gifts as $gift)
                                                                    <div
                                                                        class="flex items-center gap-2 p-2 bg-red-50 border border-red-200 rounded">
                                                                        @if ($gift->gift_image)
                                                                            <img src="{{ asset('storage/' . $gift->gift_image) }}"
                                                                                alt="{{ $gift->gift_name }}"
                                                                                class="w-8 h-8 object-cover rounded border">
                                                                        @else
                                                                            <div
                                                                                class="w-8 h-8 bg-red-200 rounded flex items-center justify-center">
                                                                                <svg class="w-4 h-4 text-red-600"
                                                                                    fill="currentColor"
                                                                                    viewBox="0 0 20 20">
                                                                                    <path
                                                                                        d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z">
                                                                                    </path>
                                                                                </svg>
                                                                            </div>
                                                                        @endif
                                                                        <div class="flex-1">
                                                                            <p class="text-xs font-medium text-red-800">
                                                                                {{ $gift->gift_name }}</p>
                                                                            @if ($gift->gift_description)
                                                                                <p class="text-xs text-red-600">
                                                                                    {{ $gift->gift_description }}</p>
                                                                            @endif
                                                                        </div>
                                                                        <span
                                                                            class="text-xs font-bold text-red-600">x{{ $item->quantity }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                                <div
                                                    class="flex items-center gap-4 mt-2 text-xs text-gray-600 uppercase tracking-wide">
                                                    <span>SL: {{ $item->quantity }}</span>
                                                    <span>GIÁ: {{ number_format($item->price) }}đ</span>
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="text-right">
                                                <p class="text-lg font-black text-black">
                                                    {{ number_format($item->price * $item->quantity) }}đ
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div
                                        class="w-20 h-20 bg-gray-100 border-2 border-gray-300 flex items-center justify-center mx-auto mb-6">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H6" />
                                        </svg>
                                    </div>
                                    <h5 class="text-xl font-bold text-black mb-3 uppercase tracking-wide">KHÔNG CÓ SẢN PHẨM
                                    </h5>
                                    <p class="text-gray-600 text-sm uppercase tracking-wide">Đơn hàng này chưa có sản phẩm
                                        nào được thêm vào.</p>
                                </div>
                            @endif
                        @endif
                    </div>

                        <!-- Review Form -->
                        <div class="bg-white border-2 border-gray-200 p-6">
                            <form action="{{ route('account.reviews.update', $review->id) }}" 
                                method="POST" 
                                class="space-y-6"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <!-- Rating Stars -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá sao <span class="text-red-500">*</span></label>
                                    <div class="flex gap-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button"
                                                class="rating-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-300 transition-colors duration-150"
                                                data-rating="{{ $i }}">
                                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" id="rating-input" value="{{ $review->rating }}">
                                    </div>
                                    @error('rating')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Review Comment -->
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nội dung đánh giá <span class="text-red-500">*</span>
                                        <span class="text-gray-400 text-xs">(Tối đa 1000 ký tự)</span>
                                    </label>
                                    <textarea name="comment" id="comment" rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        maxlength="1000"
                                        required>{{ old('comment', $review->comment) }}</textarea>
                                    @error('comment')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Review Images -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Hình ảnh đánh giá
                                        <span class="text-gray-400 text-xs">(Tối đa 5 ảnh, mỗi ảnh tối đa 2MB)</span>
                                    </label>
                                    
                                    <!-- Current Images -->
                                    @if($review->images)
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-4">
                                            @foreach($review->images as $image)
                                                <div class="relative group">
                                                    <img src="{{ asset('storage/' . $image) }}" 
                                                        alt="Review image" 
                                                        class="w-full h-24 object-cover rounded-lg">
                                                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                                        <button type="button" class="text-white hover:text-red-500 transition-colors duration-200"
                                                            onclick="removeImage(this)">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- New Images Upload -->
                                    <input type="file" name="images[]" id="images" multiple
                                        class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100"
                                        accept="image/*"
                                        onchange="previewImages(this)">
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4"></div>
                                    
                                    @error('images')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    @error('images.*')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Review Time Limit Warning -->
                                @php
                                    $timeLimit = $review->created_at->addHours(24);
                                    $remainingTime = now()->diff($timeLimit);
                                @endphp
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                Bạn có thể sửa đánh giá trong vòng 24 giờ kể từ khi đánh giá.
                                                @if($remainingTime->invert)
                                                    <span class="font-medium">Đã hết thời gian sửa đánh giá.</span>
                                                @else
                                                    <span class="font-medium">Còn {{ $remainingTime->h }} giờ {{ $remainingTime->i }} phút</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('account.purchase') }}" 
                                        class="inline-flex items-center px-6 py-3 border-2 border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Hủy
                                    </a>
                                    <button type="submit"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        @if($remainingTime->invert) disabled @endif>
                                        Cập nhật đánh giá
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                   

                    <!-- Ebook Download Section -->
                    @if ($order->paymentStatus->name === 'Đã Thanh Toán')
                        @php
                            // Kiểm tra đơn có ít nhất một ebook không
                            $hasEbook = $order->orderItems->contains(function ($item) {
                                return !$item->is_combo &&
                                    $item->bookFormat &&
                                    $item->bookFormat->format_name === 'Ebook';
                            });

                            $ebookItems = $order->orderItems->filter(function ($item) {
                                // Chỉ hiển thị ebook khi mua trực tiếp ebook, không bao gồm sách vật lý có ebook kèm theo
                                if (
                                    !$item->is_combo &&
                                    $item->bookFormat &&
                                    $item->bookFormat->format_name === 'Ebook'
                                ) {
                                    return true;
                                }
                                return false;
                            });
                        @endphp
                    @endif

                    <!-- Order Actions -->
                    <div class="mt-8 pt-8 border-t-2 border-gray-200">
                        @php
                            $hasEbook = $order
                                ->orderItems()
                                ->whereHas('bookFormat', function ($query) {
                                    $query->where('format_name', 'Ebook');
                                })
                                ->exists();
                            $canRefundEbook = false;
                            if ($hasEbook) {
                                $ebookRefundService = app(\App\Services\EbookRefundService::class);
                                $canRefundResult = $ebookRefundService->canRefundEbook($order, auth()->user());
                                $canRefundEbook = $canRefundResult['can_refund'];
                            }
                        @endphp

                        @if (
                            !$order->isParentOrder() &&
                                (\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name) || $canRefundEbook))
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-1 h-5 bg-black"></div>
                                <h4 class="text-base font-bold uppercase tracking-wide text-black">THAO TÁC ĐƠN HÀNG</h4>
                            </div>

                            <div class="space-y-4">
                                @if ($canRefundEbook)
                                    <!-- Ebook Refund Button -->
                                    <a href="{{ route('ebook-refund.show', $order->id) }}"
                                        class="inline-flex items-center gap-3 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        YÊU CẦU HOÀN TIỀN EBOOK
                                    </a>
                                @endif

                                @if (\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name))
                                    <!-- Cancel Button -->
                                    <button type="button" onclick="toggleCancelForm()"
                                        class="inline-flex items-center gap-3 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        HỦY ĐƠN HÀNG
                                    </button>
                                @endif

                                <!-- Cancel Form -->
                                <div id="cancelForm" class="cancel-form bg-red-50 border-2 border-red-200 p-6">
                                    <form action="{{ route('account.orders.cancel', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-bold text-red-800 uppercase tracking-wide mb-2">
                                                LÝ DO HỦY ĐƠN HÀNG *
                                            </label>
                                            <textarea name="cancellation_reason" rows="4" required placeholder="Vui lòng nhập lý do hủy đơn hàng..."
                                                class="w-full px-4 py-3 border-2 border-red-300 focus:border-red-500 focus:outline-none text-sm"></textarea>
                                        </div>

                                        <div class="flex gap-3">
                                            <button type="submit"
                                                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold uppercase tracking-wide transition-all duration-300">
                                                XÁC NHẬN HỦY
                                            </button>
                                            <button type="button" onclick="toggleCancelForm()"
                                                class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-black font-bold uppercase tracking-wide transition-all duration-300">
                                                HỦY BỎ
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                        @php
                            $latestRefundRequest = $order->refundRequests->sortByDesc('created_at')->first();
                        @endphp
                        {{-- Hiển thị phần yêu cầu hoàn tiền chỉ cho đơn hàng vật lý và hỗn hợp (không phải ebook thuần túy) --}}
                        @if (
                            $order->orderStatus->name === 'Thành công' &&
                                $order->paymentStatus->name === 'Đã Thanh Toán' &&
                                in_array($order->delivery_method, ['delivery', 'pickup', 'mixed']))
                            @php
                                $hasRefundRequest = $order->refundRequests()->exists();
                            @endphp

                            <!-- Refund Request Section -->
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8 border-2 border-gray-200">
                                <!-- Header -->
                                <div class="bg-black text-white px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        <h4 class="text-lg font-bold uppercase tracking-wide text-white">YÊU CẦU HOÀN TIỀN
                                        </h4>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-6 bg-white">
                                    @if (!$hasRefundRequest)
                                        <div class="text-center py-8">
                                            <div class="mb-4">
                                                <svg class="h-16 w-16 mx-auto text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chưa có yêu cầu hoàn tiền
                                            </h3>
                                            <p class="text-gray-600 mb-6">Bạn có thể yêu cầu hoàn tiền cho đơn hàng này nếu
                                                có vấn đề với sản phẩm.</p>
                                            <a href="{{ route('account.orders.refund.create', $order->id) }}"
                                                class="inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold uppercase tracking-wide rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg"
                                                style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important; color: white !important; background: linear-gradient(to right, #f97316, #ef4444) !important;">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    style="color: white !important; stroke: white !important;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                <span class="text-white font-bold"
                                                    style="color: white !important; font-weight: bold !important;">TẠO YÊU
                                                    CẦU HOÀN TIỀN</span>
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <div class="mb-4">
                                                <svg class="h-16 w-16 mx-auto text-blue-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Đã có yêu cầu hoàn tiền
                                            </h3>
                                            <p class="text-gray-600 mb-6">Bạn đã gửi yêu cầu hoàn tiền cho đơn hàng này.
                                                Nhấn vào nút bên dưới để xem trạng thái.</p>
                                            <a href="{{ route('account.orders.refund.status', $order->id) }}"
                                                class="inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-bold uppercase tracking-wide rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg"
                                                style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important; color: white !important; background: linear-gradient(to right, #3b82f6, #6366f1) !important;">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    style="color: white !important; stroke: white !important;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span class="text-white font-bold"
                                                    style="color: white !important; font-weight: bold !important;">XEM
                                                    TRẠNG THÁI HOÀN TIỀN</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleCancelForm() {
                const form = document.getElementById('cancelForm');
                form.classList.toggle('active');
            }



            // GHN Tracking functionality
            @if ($order->delivery_method === 'delivery' && $order->ghn_order_code)
                document.addEventListener('DOMContentLoaded', function() {
                    const ghnOrderCode = '{{ $order->ghn_order_code }}';
                    const refreshBtn = document.getElementById('refresh-tracking-btn');
                    const toggleTimelineBtn = document.getElementById('toggle-timeline-btn');
                    const trackingStatus = document.getElementById('ghn-tracking-status');
                    const trackingTimeline = document.getElementById('ghn-tracking-timeline');

                    // Load tracking info on page load
                    loadTrackingInfo();

                    // Refresh button event
                    if (refreshBtn) {
                        refreshBtn.addEventListener('click', function() {
                            this.disabled = true;
                            this.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Đang cập nhật...
            `;

                            loadTrackingInfo().finally(() => {
                                this.disabled = false;
                                this.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Cập nhật
                `;
                            });
                        });
                    }

                    // Toggle timeline button event
                    if (toggleTimelineBtn) {
                        toggleTimelineBtn.addEventListener('click', function() {
                            const isHidden = trackingTimeline.classList.contains('hidden');

                            if (isHidden) {
                                trackingTimeline.classList.remove('hidden');
                                this.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    Ẩn chi tiết
                `;
                            } else {
                                trackingTimeline.classList.add('hidden');
                                this.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    Xem chi tiết
                `;
                            }
                        });
                    }

                    async function loadTrackingInfo() {
                        try {
                            const response = await fetch(`/api/ghn/tracking/${ghnOrderCode}`, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                }
                            });

                            const data = await response.json();

                            if (data.success && data.data) {
                                updateTrackingStatus(data.data);
                                updateTrackingTimeline(data.data.logs || []);
                            } else {
                                showTrackingError('Không thể tải thông tin theo dõi');
                            }
                        } catch (error) {
                            console.error('Error loading tracking info:', error);
                            showTrackingError('Lỗi khi tải thông tin theo dõi');
                        }
                    }

                    function updateTrackingStatus(trackingData) {
                        const statusElement = trackingStatus;
                        const currentStatus = trackingData.status || 'Không xác định';
                        const statusColor = getStatusColor(currentStatus);

                        statusElement.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 ${statusColor} rounded-full"></div>
                <span class="text-sm font-medium">${currentStatus}</span>
            </div>
            ${trackingData.description ? `<p class="text-xs text-gray-600 mt-1">${trackingData.description}</p>` : ''}
        `;
                    }

                    function updateTrackingTimeline(logs) {
                        const timelineContainer = trackingTimeline.querySelector('.space-y-3');

                        if (logs.length === 0) {
                            timelineContainer.innerHTML =
                                '<p class="text-sm text-gray-600">Chưa có thông tin lịch sử vận chuyển</p>';
                            return;
                        }

                        timelineContainer.innerHTML = logs.map((log, index) => {
                            const isLatest = index === 0;
                            return `
                <div class="flex items-start gap-3 ${isLatest ? 'bg-blue-100 p-3 rounded' : ''}">
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-3 h-3 ${isLatest ? 'bg-blue-600' : 'bg-gray-400'} rounded-full"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-black">${log.status || 'Cập nhật trạng thái'}</p>
                        <p class="text-xs text-gray-600">${log.updated_date || ''}</p>
                        ${log.description ? `<p class="text-xs text-gray-700 mt-1">${log.description}</p>` : ''}
                    </div>
                </div>
            `;
                        }).join('');
                    }

                    function showTrackingError(message) {
                        trackingStatus.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                <span class="text-sm font-medium text-red-600">${message}</span>
            </div>
        `;
                    }

                    function getStatusColor(status) {
                        const statusLower = status.toLowerCase();
                        if (statusLower.includes('giao thành công') || statusLower.includes('delivered')) {
                            return 'bg-green-500';
                        } else if (statusLower.includes('đang giao') || statusLower.includes('shipping')) {
                            return 'bg-blue-500';
                        } else if (statusLower.includes('đã lấy') || statusLower.includes('picked')) {
                            return 'bg-yellow-500';
                        } else if (statusLower.includes('hủy') || statusLower.includes('cancel')) {
                            return 'bg-red-500';
                        } else {
                            return 'bg-gray-500';
                        }
                    }
                });
            @endif

            // Handle Rating Stars
            document.addEventListener('DOMContentLoaded', function() {
                const stars = document.querySelectorAll('.rating-star');
                const ratingInput = document.getElementById('rating-input');

                stars.forEach(star => {
                    star.addEventListener('click', () => {
                        const rating = parseInt(star.dataset.rating);
                        ratingInput.value = rating;
                        
                        // Update star colors
                        stars.forEach(s => {
                            const starRating = parseInt(s.dataset.rating);
                            if (starRating <= rating) {
                                s.classList.remove('text-gray-300');
                                s.classList.add('text-yellow-400');
                            } else {
                                s.classList.remove('text-yellow-400');
                                s.classList.add('text-gray-300');
                            }
                        });
                    });

                    // Hover effects
                    star.addEventListener('mouseover', () => {
                        const rating = parseInt(star.dataset.rating);
                        stars.forEach(s => {
                            const starRating = parseInt(s.dataset.rating);
                            if (starRating <= rating) {
                                s.classList.add('text-yellow-300');
                            }
                        });
                    });

                    star.addEventListener('mouseout', () => {
                        const currentRating = parseInt(ratingInput.value);
                        stars.forEach(s => {
                            const starRating = parseInt(s.dataset.rating);
                            s.classList.remove('text-yellow-300');
                            if (starRating <= currentRating) {
                                s.classList.add('text-yellow-400');
                            } else {
                                s.classList.add('text-gray-300');
                            }
                        });
                    });
                });

                // Handle Image Preview and Remove
                function previewImages(input) {
                    const preview = document.getElementById('imagePreview');
                    preview.innerHTML = '';

                    if (input.files) {
                        const maxFiles = 5;
                        const totalFiles = input.files.length;
                        
                        if (totalFiles > maxFiles) {
                            alert(`Chỉ được chọn tối đa ${maxFiles} ảnh`);
                            input.value = '';
                            return;
                        }

                        [...input.files].forEach(file => {
                            if (file.size > 2 * 1024 * 1024) {
                                alert('Mỗi ảnh không được vượt quá 2MB');
                                input.value = '';
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.className = 'relative group';
                                div.innerHTML = `
                                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                        <button type="button" class="text-white hover:text-red-500 transition-colors duration-200" onclick="this.closest('.relative').remove()">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                `;
                                preview.appendChild(div);
                            }
                            reader.readAsDataURL(file);
                        });
                    }
                }

                // Make previewImages available globally
                window.previewImages = previewImages;

                // Handle removing existing images
                function removeImage(button) {
                    const imageContainer = button.closest('.relative');
                    if (imageContainer) {
                        imageContainer.remove();
                    }
                }

                // Make removeImage available globally
                window.removeImage = removeImage;
            });
        </script>
    @endpush
@endsection
