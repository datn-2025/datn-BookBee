@extends('layouts.app') {{-- Or your main layout file, e.g., layouts.frontend --}}

@section('title', 'Chi Tiết Đơn Hàng - #' . $order->id)

@push('styles')
<style>
    /* Custom styles if needed, though Tailwind should cover most */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: capitalize;
    }
    .status-pending { background-color: #fef3c7; color: #92400e; } /* yellow-200, yellow-800 */
    .status-processing { background-color: #dbeafe; color: #1e40af; } /* blue-200, blue-800 */
    .status-shipped { background-color: #ccfbf1; color: #065f46; } /* teal-100, teal-800 */
    .status-delivered { background-color: #dcfce7; color: #166534; } /* green-100, green-800 */
    .status-completed { background-color: #d1fae5; color: #047857; } /* emerald-200, emerald-700 */
    .status-cancelled { background-color: #fee2e2; color: #991b1b; } /* red-200, red-800 */
    .status-refunded { background-color: #e5e7eb; color: #374151; } /* gray-200, gray-700 */
    .status-failed { background-color: #fee2e2; color: #991b1b; } /* red-200, red-800 */
    .status-on-hold { background-color: #ffedd5; color: #9a3412; } /* orange-100, orange-800 */
    .status-default { background-color: #f3f4f6; color: #4b5563; } /* gray-100, gray-600 */
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        {{-- Order Header --}}
        <div class="bg-gray-800 text-white p-6 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h2 class="text-2xl md:text-3xl font-semibold">Chi Tiết Đơn Hàng</h4>
                    <p class="text-sm text-gray-300 mt-1">Mã đơn hàng: <span class="font-bold text-indigo-400">#{{ $order->order_code }}</span></p>
                </div>
                <div class="mt-4 sm:mt-0 text-sm">
                    <p class="text-gray-300">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                    @php
                        $statusClass = 'status-default';
                        $statusText = $order->orderStatus->name; // Default to the raw status
                        switch (strtolower($order->orderStatus->name)) {
                            case 'chờ xác nhận': 
                                $statusClass = 'status-pending'; 
                                $statusText = 'Chờ xử lý'; 
                                break;
                            case 'đã xác nhận': 
                                $statusClass = 'status-processing'; 
                                $statusText = 'Đang xử lý'; 
                                break;
                            case 'đang giao hàng': 
                                $statusClass = 'status-shipped'; 
                                $statusText = 'Đã giao hàng'; 
                                break;
                            case 'đã nhận hàng': 
                                $statusClass = 'status-delivered'; 
                                $statusText = 'Đã nhận hàng'; 
                                break;
                            case 'đã hoàn thành': 
                                $statusClass = 'status-completed'; 
                                $statusText = 'Hoàn thành'; 
                                break;
                            case 'đã hủy': 
                                $statusClass = 'status-cancelled'; 
                                $statusText = 'Đã hủy'; 
                                break;
                        }
                    @endphp
                    <p class="mt-1">Trạng thái: <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></p>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                {{-- Customer Information --}}
                <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                    <h6 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Thông Tin Khách Hàng</h4>
                    <dl class="space-y-1 text-sm text-gray-600 font-medium" style="font-size: 14px;">
                        <div class="flex">
                            <dt class="font-medium text-gray-800">Tên:</dt>
                            <dd class="ms-2">{{ $order->user->name }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="font-medium text-gray-800">Email:</dt>
                            <dd class="ms-2">{{ $order->user->email }}</dd>
                        </div>
                    </dl>
                    <h6 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Người Nhận</h6>
                    <dl class="space-y-1 text-sm text-gray-600 font-medium" style="font-size: 14px;">
                        <div class="flex">
                            <dt class="font-medium text-gray-800">Tên:</dt>
                            <dd class="ms-2">{{ $order->recipient_name }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="font-medium text-gray-800">Số điện thoại:</dt>
                            <dd class="ms-2">{{ $order->recipient_phone }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Địa Chỉ Giao Hàng</h4>
                    <address class="not-italic text-sm text-gray-600 space-y-1">
                        <p>{{ $order->address->address_detail }}</p>
                        <p>{{ $order->address->ward }}, {{ $order->address->district }}</p>
                        <p>{{ $order->address->city }}</p>
                    </address>
                </div>

                {{-- Payment Information --}}
                <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Thông Tin Thanh Toán</h4>
                    <dl class="space-y-1 text-sm text-gray-600">
                        <div>
                            <dt class="font-medium text-gray-800">Phương thức:</dt>
                            <dd>{{ $order->paymentMethod->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-800">Trạng thái:</dt>
                            <dd class="font-semibold {{ $order->paymentStatus->name == 'Đã Thanh Toán' ? 'text-green-600' : ($order->paymentStatus->name == 'Chưa thanh toán' || $order->paymentStatus->name == 'Chờ Xử Lý' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $order->paymentStatus->name }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="mb-8">
                <h4 class="text-xl font-semibold text-gray-800 mb-4">Các Sản Phẩm Đã Đặt</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($order->orderItems as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-16 w-16">
                                                <img class="h-16 w-16 rounded-md object-cover" src="{{ asset('storage/' . $item->book->cover_image) }}" alt="{{ $item->book->title ?? 'Sản phẩm' }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->book->title ?? 'Tên sản phẩm' }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->book->author->name ?? 'Tác giả' }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->bookFormat->format_name ?? 'Nhà xuất bản' }}</div>
                                                {{-- You can add SKU or other details here if available --}}
                                                {{-- <div class="text-xs text-gray-500">SKU: {{ $item->book->sku ?? 'N/A' }}</div> --}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">Không có sản phẩm nào trong đơn hàng này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Order Summary & Notes --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    @if($order->note)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md shadow-sm">
                        <h5 class="text-md font-semibold text-yellow-800 mb-2">Ghi Chú Từ Khách Hàng:</h5>
                        <p class="text-sm text-yellow-700">{{ $order->note }}</p>
                    </div>
                    @endif
                </div>

                <div class="md:col-span-1 bg-gray-50 p-6 rounded-lg shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Tổng Cộng Đơn Hàng</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tạm tính:</span>
                            <span class="text-gray-800 font-medium">{{ number_format($order->subtotal_amount, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phí vận chuyển:</span>
                            <span class="text-gray-800 font-medium">{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span class="font-medium">Giảm giá ({{ $order->applied_voucher_code ?? 'Voucher' }}):</span>
                            <span class="font-medium">- {{ number_format($order->discount_amount, 0, ',', '.') }} đ</span>
                        </div>
                        @endif
                        <div class="border-t pt-2 mt-2"></div>
                        <div class="flex justify-between text-xl font-bold text-gray-900">
                            <span>Tổng cộng:</span>
                            <span>{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons (Optional) --}}
            <div class="mt-10 pt-6 border-t border-gray-200 text-right">
                <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium mr-4">
                    &larr; Quay lại danh sách đơn hàng
                </a>
                {{-- Example: Print button --}}
                {{-- <button onclick="window.print();" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                    In Đơn Hàng
                </button> --}}
            </div>
        </div>
    </div>
</div>
@endsection
