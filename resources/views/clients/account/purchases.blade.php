@extends('layouts.account.layout')
@section('title', 'Đánh Giá Của Tôi')
@section('account_content')
    <div class="bg-white border border-black shadow mb-8" style="border-radius:0;">
        <div class="px-8 py-6 border-b border-black bg-black">
            <h1 class="text-2xl font-bold text-white uppercase tracking-wide">Đánh giá của tôi</h1>
        </div>
        <div class="p-8">
            <!-- Tabs -->
            <div class="flex space-x-1 mb-8 border-b border-black">
                @foreach ([1 => 'Tất cả đánh giá', 2 => 'Chưa đánh giá', 3 => 'Đã đánh giá'] as $type => $label)
                    <a href="{{ route('account.purchase', ['type' => $type]) }}"
                        class="flex-1 text-center px-6 py-3 text-base font-semibold border-b-2 transition
                       {{ request('type', '1') == $type ? 'border-black text-black bg-white' : 'border-transparent text-gray-500 hover:text-black hover:bg-gray-100' }}">{{ $label }}</a>
                @endforeach
            </div>
            <!-- Orders List -->
            <div class="space-y-6">
                @forelse($orders as $order)
                    <div class="bg-white order-card border-2 border-gray-200 relative overflow-hidden">
                        <!-- Order Header -->
                        <div class="bg-gray-50 border-b-2 border-gray-200 px-8 py-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                                <!-- Left: Order Info -->
                                <div class="flex items-center gap-6">
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-1 h-6 bg-black"></div>
                                            <h3 class="text-xl font-black uppercase tracking-wide text-black">
                                                ĐƠN HÀNG #{{ $order->order_code }}

                                                @if ($order->delivery_method === 'mixed')
                                                    <span class="ml-2 px-2 py-1 badge bg-yellow-500 text-black">HỖN
                                                        HỢP</span>
                                                @endif

                                                @if ($order->isParentOrder())
                                                    <span class="ml-2 px-2 py-1 badge bg-blue-500 text-white">ĐƠN HÀNG
                                                        CHA</span>
                                                @endif
                                            </h3>
                                        </div>
                                        <p class="text-sm text-gray-600 uppercase tracking-wide">
                                            Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Right: Status + Total -->
                                <div class="flex items-center gap-4">

                                    <!-- Refund or Order Status -->
                                    @php
                                        if ($order->refundRequests->isNotEmpty()) {
                                            $latestRefund = $order->refundRequests->sortByDesc('created_at')->first();
                                            $statusMap = [
                                                'pending' => ['CHỜ HOÀN TIỀN', 'bg-yellow-500 text-white'],
                                                'processing' => ['ĐANG HOÀN TIỀN', 'bg-blue-500 text-white'],
                                                'completed' => ['ĐÃ HOÀN TIỀN', 'bg-green-500 text-white'],
                                                'rejected' => ['TỪ CHỐI HOÀN TIỀN', 'bg-red-500 text-white'],
                                            ];
                                            [$statusText, $statusClass] = $statusMap[$latestRefund->status] ?? [
                                                'HOÀN TIỀN',
                                                'bg-gray-500 text-white',
                                            ];
                                        } else {
                                            $statusName = $order->orderStatus->name ?? '';
                                            $statusMap = [
                                                'Chờ xác nhận' => 'bg-yellow-500 text-white',
                                                'Đã xác nhận' => 'bg-blue-500 text-white',
                                                'Đang chuẩn bị' => 'bg-indigo-500 text-white',
                                                'Đang đóng gói' => 'bg-orange-500 text-white',
                                                'Đang giao hàng' => 'bg-purple-500 text-white',
                                                'Đã giao hàng' => 'bg-green-500 text-white',
                                                'Đã giao thành công' => 'bg-green-500 text-white',
                                                'Đã giao' => 'bg-green-500 text-white',
                                                'Thành công' => 'bg-green-500 text-white',
                                                'Đã hủy' => 'bg-red-500 text-white',
                                                'Hoàn trả' => 'bg-gray-500 text-white',
                                            ];
                                            $statusClass = $statusMap[$statusName] ?? 'bg-gray-500 text-white';
                                        }
                                    @endphp

                                    @if ($order->refundRequests->isNotEmpty())
                                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                    @else
                                        @if ($statusName !== 'Đã giao thành công')
                                            <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                                        @else
                                            <form action="{{ route('account.orders.confirm-received', $order->id) }}"
                                                method="POST" class="inline-block ml-2">
                                                @csrf
                                                <button type="submit" class="btn-confirm"
                                                    onclick="return confirm('Bạn có chắc chắn đã nhận được hàng? Hành động này không thể hoàn tác.')">
                                                    ĐÃ NHẬN HÀNG
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <!-- Total Amount + Parent Toggle -->
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 uppercase tracking-wide">Tổng tiền</p>
                                        <p class="text-2xl font-black text-black">
                                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                        </p>

                                        @if ($order->isParentOrder())
                                            <button type="button" class="btn-toggle"
                                                onclick="toggleChildOrders('{{ $order->id }}')">
                                                <span id="toggle-text-{{ $order->id }}">XEM CHI TIẾT</span>
                                                <svg id="toggle-icon-{{ $order->id }}"
                                                    class="inline-block w-3 h-3 ml-1 transition-transform"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0
                                                                          111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1
                                                                          0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Content -->
                        <div class="p-8">
                            @if ($order->isParentOrder())
                                <!-- Child Orders Section -->
                                <div id="child-orders-{{ $order->id }}" class="hidden mb-8">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-1 h-5 bg-blue-500"></div>
                                        <h4 class="section-title">ĐƠN HÀNG CON</h4>
                                    </div>

                                    <div class="space-y-4">
                                        @foreach ($order->childOrders as $childOrder)
                                            <div class="card p-6">
                                                <!-- Child Order Header -->
                                                <div
                                                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                                                    <div>
                                                        <h5 class="text-lg font-bold text-black mb-2">
                                                            #{{ $childOrder->order_code }}
                                                            <span class="badge bg-gray-500 text-white">
                                                                @switch($childOrder->delivery_method)
                                                                    @case('pickup')
                                                                        NHẬN TẠI CỬA HÀNG
                                                                    @break

                                                                    @case('ebook')
                                                                        EBOOK
                                                                    @break

                                                                    @default
                                                                        GIAO HÀNG
                                                                @endswitch
                                                            </span>
                                                        </h5>
                                                        <p class="text-sm text-gray-600">
                                                            {{ $childOrder->created_at->format('d/m/Y H:i') }}
                                                        </p>
                                                    </div>

                                                    <!-- Child Order Status + Total -->
                                                    <div class="flex items-center gap-4">
                                                        @php
                                                            $statusMap = [
                                                                'Chờ xác nhận' => 'bg-yellow-500 text-white',
                                                                'Đã xác nhận' => 'bg-blue-500 text-white',
                                                                'Đang chuẩn bị' => 'bg-indigo-500 text-white',
                                                                'Đang đóng gói' => 'bg-orange-500 text-white',
                                                                'Đang giao hàng' => 'bg-purple-500 text-white',
                                                                'Đã giao hàng' => 'bg-green-500 text-white',
                                                                'Đã giao thành công' => 'bg-green-500 text-white',
                                                                'Đã giao' => 'bg-green-500 text-white',
                                                                'Thành công' => 'bg-green-500 text-white',
                                                                'Đã hủy' => 'bg-red-500 text-white',
                                                                'Hoàn trả' => 'bg-gray-500 text-white',
                                                            ];
                                                            $statusName = $childOrder->orderStatus->name ?? '';
                                                            $statusClass =
                                                                $statusMap[$statusName] ?? 'bg-gray-500 text-white';
                                                        @endphp

                                                        <span
                                                            class="status-badge {{ $statusClass }}">{{ $statusName }}</span>

                                                        @if ($statusName === 'Đã giao thành công')
                                                            <form
                                                                action="{{ route('account.orders.confirm-received', $childOrder->id) }}"
                                                                method="POST" class="inline-block ml-2">
                                                                @csrf
                                                                <button type="submit" class="btn-primary"
                                                                    onclick="return confirm('Bạn có chắc chắn đã nhận được hàng? Hành động này không thể hoàn tác.')">
                                                                    ĐÃ NHẬN HÀNG
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <div class="text-right">
                                                            <p class="text-lg font-bold text-black">
                                                                {{ number_format($childOrder->total_amount, 0, ',', '.') }}đ
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Child Order Products -->
                                                @if ($childOrder->orderItems->count() > 0)
                                                    <div class="mt-6 border-t border-gray-300 pt-4">
                                                        <h6 class="section-subtitle">
                                                            SẢN PHẨM ({{ $childOrder->orderItems->sum('quantity') }} sản
                                                            phẩm)
                                                        </h6>

                                                        <div class="space-y-6">
                                                            @foreach ($childOrder->orderItems as $item)
                                                                <div class="card-item">
                                                                    <!-- Product Image -->
                                                                    <div class="product-thumb">
                                                                        @include(
                                                                            'components.product-thumb',
                                                                            ['item' => $item]
                                                                        )
                                                                    </div>

                                                                    <!-- Product Info -->
                                                                    <div class="flex-1">
                                                                        @include(
                                                                            'components.product-info',
                                                                            ['item' => $item]
                                                                        )

                                                                        <div class="flex items-center gap-6 text-sm">
                                                                            <span class="meta">Số lượng:
                                                                                <strong>{{ $item->quantity }}</strong></span>
                                                                            <span class="meta">Đơn giá:
                                                                                <strong>{{ number_format($item->price) }}đ</strong></span>
                                                                            <span class="meta">Thành tiền:
                                                                                <strong>{{ number_format($item->price * $item->quantity) }}đ</strong></span>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Review Section -->
                                                                    @if (in_array($statusName, ['Đã giao', 'Thành công']))
                                                                        <div class="lg:w-80 flex-shrink-0">
                                                                            @include(
                                                                                'components.review-box',
                                                                                [
                                                                                    'childOrder' => $childOrder,
                                                                                    'item' => $item,
                                                                                ]
                                                                            )
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Child Order Actions -->
                                                <div class="flex gap-2 mt-4">
                                                    <a href="{{ route('orders.show', $childOrder->id) }}"
                                                        class="btn-primary">
                                                        XEM CHI TIẾT
                                                    </a>

                                                    @if (\App\Helpers\OrderStatusHelper::canBeCancelled($childOrder->orderStatus->name))
                                                        <form
                                                            action="{{ route('account.orders.cancel', $childOrder->id) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn-danger"
                                                                onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                                                HỦY ĐƠN HÀNG
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <!-- Order Items -->
                            @if (!$order->isParentOrder() && $order->orderItems->count() > 0)
                                <div class="">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-1 h-5 bg-black"></div>
                                        <h4 class="text-base font-bold uppercase tracking-wide text-black">SẢN PHẨM ĐÃ ĐẶT
                                            ({{ $order->orderItems->sum('quantity') }} sản phẩm)</h4>
                                    </div>

                                    <div class="space-y-6">
                                        @foreach ($order->orderItems as $item)
                                            <div
                                                class="flex flex-col lg:flex-row gap-6 p-6 border-2 border-gray-200 hover:border-black transition-all duration-300">
                                                <!-- Product Image -->
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-24 h-32 bg-gray-200 border-2 border-gray-300 overflow-hidden">
                                                        @if ($item->isCombo())
                                                            @php
                                                                $comboImageUrl = asset('images/default-book.jpg');
                                                                if (
                                                                    $item->collection &&
                                                                    $item->collection->cover_image
                                                                ) {
                                                                    $comboImageUrl = asset(
                                                                        'storage/' . $item->collection->cover_image,
                                                                    );
                                                                }
                                                            @endphp
                                                            <img src="{{ $comboImageUrl }}"
                                                                alt="{{ $item->collection ? $item->collection->name : 'Combo không tồn tại' }}"
                                                                class="w-full h-full object-cover"
                                                                onerror="this.src='{{ asset('images/default-book.jpg') }}'; this.onerror=null;">
                                                        @else
                                                            @php
                                                                $bookImageUrl = asset('images/default-book.jpg');
                                                                if ($item->book && $item->book->cover_image) {
                                                                    $bookImageUrl = asset(
                                                                        'storage/' . $item->book->cover_image,
                                                                    );
                                                                } elseif (
                                                                    $item->book &&
                                                                    $item->book->images &&
                                                                    $item->book->images->isNotEmpty()
                                                                ) {
                                                                    $bookImageUrl = asset(
                                                                        'storage/' .
                                                                            $item->book->images->first()->image_url,
                                                                    );
                                                                }
                                                            @endphp
                                                            <img src="{{ $bookImageUrl }}"
                                                                alt="{{ $item->book ? $item->book->title : 'Sản phẩm không tồn tại' }}"
                                                                class="w-full h-full object-cover"
                                                                onerror="this.src='{{ asset('images/default-book.jpg') }}'; this.onerror=null;">
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Product Info -->
                                                <div class="flex-1">
                                                    @if ($item->isCombo())
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase tracking-wide bg-purple-100 text-purple-800 border border-purple-200">
                                                                COMBO
                                                            </span>
                                                        </div>
                                                        <h5 class="text-lg font-bold text-black mb-2">
                                                            {{ $item->collection->name ?? 'Combo không xác định' }}
                                                        </h5>
                                                    @else
                                                        <h5 class="text-lg font-bold text-black mb-2">
                                                            {{ $item->book->title ?? 'Sách không xác định' }}
                                                        </h5>
                                                        @if ($item->bookFormat)
                                                            <p class="text-sm text-gray-600 uppercase tracking-wide mb-2">
                                                                Định dạng: {{ $item->bookFormat->format_name }}
                                                            </p>
                                                        @endif

                                                        @if (!$item->isCombo() && $item->attributeValues && $item->attributeValues->count() > 0)
                                                            <div class="space-y-1 mb-2">
                                                                @foreach ($item->attributeValues as $attributeValue)
                                                                    <p
                                                                        class="text-sm text-gray-600 uppercase tracking-wide">
                                                                        <span
                                                                            class="font-semibold">{{ $attributeValue->attribute->name ?? 'Thuộc tính' }}:</span>
                                                                        {{ $attributeValue->value }}
                                                                    </p>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <!-- Hiển thị quà tặng -->
                                                        @if (
                                                            !$item->isCombo() &&
                                                                $item->book &&
                                                                $item->book->gifts &&
                                                                $item->book->gifts->count() > 0 &&
                                                                $item->bookFormat &&
                                                                $item->bookFormat->format_name !== 'Ebook')
                                                            <div class="mb-2">
                                                                <p
                                                                    class="text-sm font-semibold text-red-600 uppercase tracking-wide mb-1">
                                                                    🎁 Quà tặng kèm:</p>
                                                                <div class="flex flex-wrap gap-2">
                                                                    @foreach ($item->book->gifts as $gift)
                                                                        <div
                                                                            class="flex items-center gap-2 px-2 py-1 bg-red-50 text-red-700 text-xs font-medium rounded border border-red-200">
                                                                            @if ($gift->image)
                                                                                <img src="{{ asset('storage/' . $gift->image) }}"
                                                                                    alt="{{ $gift->name }}"
                                                                                    class="w-4 h-4 object-cover rounded">
                                                                            @endif
                                                                            <span>{{ $gift->name }}</span>
                                                                            <span
                                                                                class="text-red-500">x{{ $gift->pivot->quantity ?? 1 }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif

                                                    <div class="flex items-center gap-6 text-sm">
                                                        <span class="text-gray-600 uppercase tracking-wide">Số lượng:
                                                            <span
                                                                class="font-bold text-black">{{ $item->quantity }}</span>
                                                        </span>
                                                        <span class="text-gray-600 uppercase tracking-wide">Đơn giá:
                                                            <span
                                                                class="font-bold text-black">{{ number_format($item->price) }}đ</span>
                                                        </span>
                                                        <span class="text-gray-600 uppercase tracking-wide">Thành tiền:
                                                            <span
                                                                class="font-bold text-black">{{ number_format($item->price * $item->quantity) }}đ</span>
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Review Section -->
                                                @if (in_array($order->orderStatus->name, ['Đã giao', 'Thành công']))
                                                    <div class="lg:w-80 flex-shrink-0">
                                                        @php
                                                            // Kiểm tra đánh giá cho combo hoặc sách
                                                            if ($item->isCombo()) {
                                                                $review = $order
                                                                    ->reviews()
                                                                    ->withTrashed()
                                                                    ->where('collection_id', $item->collection_id)
                                                                    ->first();
                                                            } else {
                                                                $review = $order
                                                                    ->reviews()
                                                                    ->withTrashed()
                                                                    ->where('book_id', $item->book_id)
                                                                    ->first();
                                                            }

                                                            // Kiểm tra xem đơn hàng có yêu cầu hoàn tiền không
                                                            $hasRefundRequest = $order
                                                                ->refundRequests()
                                                                ->whereIn('status', ['pending', 'processing'])
                                                                ->exists();
                                                        @endphp
                                                        @php
                                                            $canReview =
                                                                !$hasRefundRequest &&
                                                                !$item->review &&
                                                                $order->updated_at->addMonth()->gt(now());
                                                            $hasReview = $review && !$review->trashed();
                                                            $isExpired = !$canReview && !$hasReview;
                                                        @endphp

                                                        @if ($hasReview)
                                                            <!-- Hiển thị đánh giá đã tồn tại -->
                                                            <div class="bg-gray-50 border-2 border-gray-200 p-4">
                                                                <div class="flex items-center gap-2 mb-2">
                                                                    <div class="w-1 h-4 bg-green-500"></div>
                                                                    <h6
                                                                        class="font-bold text-sm uppercase tracking-wide text-black">
                                                                        ĐÁNH GIÁ CỦA BẠN</h6>
                                                                </div>
                                                                <div class="flex items-center text-yellow-400 mb-2">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        <i
                                                                            class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                                                    @endfor
                                                                </div>
                                                                <p class="text-sm text-gray-600 italic">
                                                                    "{{ $review->comment ?? 'Không có nhận xét' }}"</p>

                                                                @php
                                                                    $reviewImages = $review->images;
                                                                    if (is_string($reviewImages)) {
                                                                        $reviewImages =
                                                                            json_decode($reviewImages, true) ?? [];
                                                                    }
                                                                    $reviewImages = is_array($reviewImages)
                                                                        ? $reviewImages
                                                                        : [];
                                                                @endphp
                                                                @if (!empty($reviewImages))
                                                                    <div class="mt-3">
                                                                        <p
                                                                            class="text-xs font-bold uppercase tracking-wide text-black mb-2">
                                                                            Hình ảnh đánh giá:</p>
                                                                        <div class="grid grid-cols-3 gap-2">
                                                                            @foreach ($reviewImages as $imagePath)
                                                                                <img src="{{ asset('storage/' . $imagePath) }}"
                                                                                    alt="Review Image"
                                                                                    class="w-full h-16 object-cover border border-gray-300 rounded cursor-pointer hover:opacity-80 transition-opacity"
                                                                                    onclick="openImageModal('{{ asset('storage/' . $imagePath) }}')">
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($review->admin_response)
                                                                    <div class="mt-3 pt-3 border-t border-gray-300">
                                                                        <p
                                                                            class="text-xs font-bold uppercase tracking-wide text-black mb-1">
                                                                            Phản hồi từ BookBee:</p>
                                                                        <p class="text-sm text-gray-600">
                                                                            {{ $review->admin_response }}</p>
                                                                    </div>
                                                                @endif
                                                                <!-- Nút Sửa và Xóa -->
                                                                <div class="flex gap-2 mt-3">
                                                                    @if ($review->user_id === auth()->id())
                                                                        @if ($review->created_at->addHours(24)->isFuture())
                                                                            <a href="{{ route('account.reviews.edit', $review->id) }}"
                                                                                class="px-3 py-1 bg-black text-white text-xs font-medium hover:bg-gray-900 transition-colors duration-150">
                                                                                Sửa đánh giá
                                                                            </a>
                                                                        @endif
                                                                        @if (!$review->created_at->addDays(7)->isPast())
                                                                            <form
                                                                                action="{{ route('account.reviews.destroy', $review->id) }}"
                                                                                method="POST"
                                                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');"
                                                                                class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="px-3 py-1 bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition-colors duration-150">
                                                                                    Xóa
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @elseif($canReview)
                                                            <!-- Form thêm đánh giá mới -->
                                                            <form action="{{ route('account.reviews.store') }}"
                                                                method="POST" enctype="multipart/form-data"
                                                                class="space-y-4 review-form bg-gray-50 border-2 border-gray-200 p-4 quick-review-form">
                                                                @csrf
                                                                <input type="hidden" name="order_id"
                                                                    value="{{ $order->id }}">
                                                                @if ($item->isCombo())
                                                                    <input type="hidden" name="collection_id"
                                                                        value="{{ $item->collection_id }}">
                                                                @else
                                                                    <input type="hidden" name="book_id"
                                                                        value="{{ $item->book_id }}">
                                                                @endif

                                                                <div class="flex items-center gap-2 mb-3">
                                                                    <div class="w-1 h-4 bg-blue-500"></div>
                                                                    <h6
                                                                        class="font-bold text-sm uppercase tracking-wide text-black">
                                                                        ĐÁNH GIÁ
                                                                        {{ $item->isCombo() ? 'COMBO' : 'SẢN PHẨM' }}
                                                                    </h6>
                                                                </div>

                                                                <div class="star-rating flex flex-row-reverse justify-end items-center gap-1 quick-star-group"
                                                                    data-order="{{ $order->id }}"
                                                                    data-item="{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}">
                                                                    @for ($i = 5; $i >= 1; $i--)
                                                                        <input type="radio"
                                                                            id="star-{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}-{{ $i }}"
                                                                            name="rating" value="{{ $i }}"
                                                                            class="sr-only"
                                                                            {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                                                        <label
                                                                            for="star-{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}-{{ $i }}"
                                                                            class="text-gray-300 text-2xl cursor-pointer transition-colors hover:text-yellow-400 quick-star-label"
                                                                            data-star="{{ $i }}">★</label>
                                                                    @endfor
                                                                </div>

                                                                <textarea name="comment" rows="3"
                                                                    class="w-full px-3 py-2 border-2 border-gray-300 focus:border-black focus:ring-0 text-sm"
                                                                    placeholder="Chia sẻ trải nghiệm của bạn về {{ $item->isCombo() ? 'combo' : 'sản phẩm' }} này...">{{ old('comment') }}</textarea>

                                                                <!-- Upload hình ảnh -->
                                                                <div class="space-y-2">
                                                                    <label
                                                                        class="block text-sm font-medium text-gray-700">Hình
                                                                        ảnh đánh giá (tùy chọn)</label>
                                                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors"
                                                                        id="quick-drop-zone-{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}">
                                                                        <input type="file" name="images[]" multiple
                                                                            accept="image/*" class="hidden"
                                                                            id="quick-images-{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}"
                                                                            onchange="previewQuickImages(this, '{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}')">
                                                                        <div class="text-gray-500">
                                                                            <svg class="mx-auto h-8 w-8 text-gray-400"
                                                                                stroke="currentColor" fill="none"
                                                                                viewBox="0 0 48 48">
                                                                                <path
                                                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                                    stroke-width="2"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round" />
                                                                            </svg>
                                                                            <p class="mt-1 text-xs">Kéo thả hoặc <button
                                                                                    type="button"
                                                                                    class="text-blue-600 hover:text-blue-500"
                                                                                    onclick="document.getElementById('quick-images-{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}').click()">chọn
                                                                                    ảnh</button></p>
                                                                            <p class="text-xs text-gray-400">Tối đa 5 ảnh,
                                                                                mỗi ảnh < 2MB</p>
                                                                        </div>
                                                                    </div>
                                                                    <div id="quick-preview-{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}"
                                                                        class="grid grid-cols-5 gap-2 mt-2"></div>
                                                                    <div id="quick-error-{{ $order->id }}-{{ $item->isCombo() ? 'combo_' . $item->collection_id : 'book_' . $item->book_id }}"
                                                                        class="text-red-500 text-xs mt-1"></div>
                                                                </div>

                                                                <div class="flex gap-2">
                                                                    <button type="submit"
                                                                        class="flex-1 px-4 py-3 bg-black hover:bg-gray-800 text-white text-sm font-bold uppercase tracking-wide transition-colors duration-300">
                                                                        GỬI ĐÁNH GIÁ
                                                                    </button>
                                                                    @if ($item->isCombo())
                                                                        <a href="{{ route('combos.show', $item->collection->slug) }}"
                                                                            class="px-3 py-3 bg-gray-200 text-black text-xs font-medium hover:bg-gray-300 transition-colors duration-150">
                                                                            Chi tiết
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('books.show', $item->book->slug) }}"
                                                                            class="px-3 py-3 bg-gray-200 text-black text-xs font-medium hover:bg-gray-300 transition-colors duration-150">
                                                                            Chi tiết
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </form>
                                                        @else
                                                            {{-- <!-- Thông báo hết hạn đánh giá -->
                                                            <div class="bg-gray-50 border-2 border-gray-200 p-4">
                                                                <div class="flex items-center gap-2 mb-2">
                                                                    <div class="w-1 h-4 bg-gray-400"></div>
                                                                    <h6
                                                                        class="font-bold text-sm uppercase tracking-wide text-gray-600">
                                                                        ĐÁNH GIÁ ĐÃ HẾT HẠN</h6>
                                                                </div>
                                                                <p class="text-sm text-gray-500">
                                                                    Đã quá thời hạn đánh giá sản phẩm này. Bạn chỉ có thể
                                                                    đánh giá trong vòng 1 tháng sau khi đơn hàng hoàn thành.
                                                                </p>
                                                            </div> --}}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Order Actions -->
                            <div class="mt-8">
                                <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                                    <div>
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="inline-flex items-center px-6 py-3 border-2 border-black text-black hover:bg-black hover:text-white font-bold text-sm uppercase tracking-wide transition-all duration-300">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            XEM CHI TIẾT
                                        </a>
                                    </div>

                                    <!-- Order Chat Button -->
                                    @include('components.order-chat-button', ['order' => $order])

                                    @if (!$order->isParentOrder() && \App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name))
                                        <form action="{{ route('account.orders.cancel', $order->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="inline-flex items-center px-6 py-3 border-2 border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold text-sm uppercase tracking-wide transition-all duration-300"
                                                onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                HỦY ĐƠN HÀNG
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="bg-white border-2 border-gray-200 text-center py-16 px-6">
                            <div class="max-w-md mx-auto">
                                <div
                                    class="w-24 h-24 bg-gray-100 border-2 border-gray-300 flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-black uppercase tracking-wide text-black mb-4">CHƯA CÓ ĐƠN HÀNG NÀO
                                </h3>
                                <p class="text-gray-600 uppercase tracking-wide mb-6">Tất cả đơn hàng của bạn sẽ được hiển thị
                                    ở đây</p>
                                <a href="{{ route('home') }}"
                                    class="inline-flex items-center px-8 py-4 bg-black text-white font-bold text-sm uppercase tracking-wide hover:bg-gray-800 transition-colors duration-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    MUA SẮM NGAY
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
                <!-- Pagination -->
                @if ($orders->hasPages())
                    <div class="mt-12">
                        <div class="bg-white border-2 border-gray-200 hover:border-black transition-all duration-500 p-6">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Image Modal -->
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
            <div class="relative max-w-4xl max-h-full">
                <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img id="modalImage" src="" alt="Review Image" class="max-w-full max-h-full object-contain">
            </div>
        </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            (function() {
                document.addEventListener('DOMContentLoaded', () => {
                    /* -------------------- Toggle Child Orders -------------------- */
                    window.toggleChildOrders = orderId => {
                        const div = document.getElementById(`child-orders-${orderId}`);
                        const text = document.getElementById(`toggle-text-${orderId}`);
                        const icon = document.getElementById(`toggle-icon-${orderId}`);
                        const isHidden = div.classList.toggle('hidden');
                        text.textContent = isHidden ? 'XEM CHI TIẾT' : 'ẨN CHI TIẾT';
                        icon.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
                    };

                    /* -------------------- Star Rating (detail & quick) -------------------- */
                    const highlightStars = (stars, rating) => {
                        stars.forEach(star => {
                            const val = parseInt(star.dataset.star);
                            star.style.color = val <= rating ? '#f59e0b' : '#d1d5db';
                        });
                    };
                    const resetStars = stars => stars.forEach(s => s.style.color = '#d1d5db');

                    document.querySelectorAll('.star-rating, .quick-star-group').forEach(group => {
                        const stars = group.querySelectorAll('label, .quick-star-label');
                        group.addEventListener('mouseenter', e => {
                            if (e.target.dataset?.star) {
                                highlightStars(stars, parseInt(e.target.dataset.star));
                            }
                        }, true);

                        group.addEventListener('click', e => {
                            if (e.target.dataset?.star) {
                                const rating = parseInt(e.target.dataset.star);
                                const input = group.querySelector(`input[value="${rating}"]`);
                                if (input) input.checked = true;
                                highlightStars(stars, rating);
                            }
                        });

                        group.addEventListener('mouseleave', () => {
                            const checked = group.querySelector('input:checked');
                            checked ? highlightStars(stars, parseInt(checked.value)) : resetStars(
                                stars);
                        });
                    });

                    /* -------------------- Quick Review Submission -------------------- */
                    const showMessage = (form, type, message) => {
                        const div = document.createElement('div');
                        div.className = `${type === 'success'
                ? 'bg-green-100 border-green-400 text-green-700'
                : 'bg-red-100 border-red-400 text-red-700'
            } border px-4 py-3 rounded mb-4`;
                        div.innerHTML =
                            `<strong>${type === 'success' ? 'Thành công!' : 'Lỗi!'}</strong> ${message}`;
                        form.parentNode.insertBefore(div, form);
                    };

                    document.querySelectorAll('.quick-review-form').forEach(form => {
                        form.addEventListener('submit', e => {
                            e.preventDefault();
                            const btn = form.querySelector('button[type="submit"]');
                            const originalText = btn.textContent;
                            btn.disabled = true;
                            btn.textContent = 'ĐANG GỬI...';

                            fetch(form.action, {
                                    method: 'POST',
                                    body: new FormData(form),
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if (data.success) {
                                        showMessage(form, 'success', data.message);
                                        setTimeout(() => location.reload(), 2000);
                                    } else {
                                        showMessage(form, 'error', data.message ||
                                            'Có lỗi xảy ra, vui lòng thử lại.');
                                        btn.disabled = false;
                                        btn.textContent = originalText;
                                    }
                                })
                                .catch(() => {
                                    showMessage(form, 'error',
                                        'Có lỗi xảy ra, vui lòng thử lại.');
                                    btn.disabled = false;
                                    btn.textContent = originalText;
                                });
                        });
                    });

                    /* -------------------- Image Modal -------------------- */
                    const modal = document.getElementById('imageModal');
                    const modalImg = document.getElementById('modalImage');
                    window.openImageModal = src => {
                        modalImg.src = src;
                        modal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    };
                    window.closeImageModal = () => {
                        modal.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    };
                    modal?.addEventListener('click', e => {
                        if (e.target === modal) closeImageModal();
                    });

                    /* -------------------- Quick Review Image Preview -------------------- */
                    window.previewQuickImages = (input, formId) => {
                        const preview = document.getElementById(`quick-preview-${formId}`);
                        const errorBox = document.getElementById(`quick-error-${formId}`);
                        preview.innerHTML = '';
                        errorBox.innerHTML = '';

                        const files = Array.from(input.files);
                        if (files.length > 5) {
                            errorBox.textContent = 'Chỉ được chọn tối đa 5 hình ảnh';
                            input.value = '';
                            return;
                        }

                        files.forEach((file, i) => {
                            if (file.size > 2 * 1024 * 1024) {
                                errorBox.textContent = `Hình "${file.name}" vượt quá 2MB`;
                                input.value = '';
                                return;
                            }
                            if (!file.type.startsWith('image/')) {
                                errorBox.textContent = `File "${file.name}" không phải hình ảnh`;
                                input.value = '';
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = e => {
                                const div = document.createElement('div');
                                div.className = 'relative';
                                div.innerHTML =
                                    `
                        <img src="${e.target.result}" class="w-full h-20 object-cover rounded border">
                        <button type="button" onclick="removeQuickImage(this, '${formId}', ${i})"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">×</button>`;
                                preview.appendChild(div);
                            };
                            reader.readAsDataURL(file);
                        });
                    };

                    window.removeQuickImage = (btn, formId, index) => {
                        const input = document.getElementById(`quick-images-${formId}`);
                        const dt = new DataTransfer();
                        Array.from(input.files).forEach((f, i) => {
                            if (i !== index) dt.items.add(f);
                        });
                        input.files = dt.files;
                        btn.parentElement.remove();
                    };
                });
            })();
        </script>
    @endpush
