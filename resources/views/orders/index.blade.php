@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Đơn hàng của tôi</h1>

    @if($orders->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">Bạn chưa có đơn hàng nào</p>
        <a href="{{ route('books.index') }}" class="inline-block mt-4 text-blue-500 hover:text-blue-600">
            Tiếp tục mua sắm
        </a>
    </div>
    @else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Mã đơn hàng
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ngày đặt
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tổng tiền
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Trạng thái
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Thanh toán
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Thao tác
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            #{{ $order->order_code }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ number_format($order->total_amount) }} VNĐ
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="
                            @if($order->orderStatus->name === 'Đã hủy') bg-red-100 text-red-800
                            @elseif($order->orderStatus->name === 'Đã xác nhận') bg-blue-100 text-blue-800
                            @elseif($order->orderStatus->name === 'Giao thất bại') bg-gray-100 text-gray-800
                            @elseif($order->orderStatus->name === 'Đang giao hàng') bg-teal-100 text-teal-800
                            @elseif($order->orderStatus->name === 'Hoàn tiền') bg-green-100 text-green-800
                            @elseif($order->orderStatus->name === 'Đang chuẩn bị') bg-orange-100 text-orange-800
                            @elseif($order->orderStatus->name === 'Đã giao thành công') bg-green-100 text-green-800
                            @elseif($order->orderStatus->name === 'Thành công') bg-green-100 text-green-800
                            @elseif($order->orderStatus->name === 'Đã nhận hàng') bg-blue-100 text-blue-800
                            @elseif($order->orderStatus->name === 'Chờ xác nhận') bg-yellow-100 text-yellow-800
                            @endif
                        ">
                            {{ $order->orderStatus->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                       <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($order->paymentStatus->name === 'Đã Thanh Toán') bg-green-100 text-green-800
                            @elseif($order->paymentStatus->name === 'Chưa Thanh Toán') bg-yellow-100 text-yellow-800
                            @elseif($order->paymentStatus->name === 'Thất Bại') bg-red-100 text-red-800
                            @elseif($order->paymentStatus->name === 'Chờ Xử Lý') bg-blue-100 text-blue-800
                            @endif">
                            {{ $order->paymentStatus->name }}
                        </span>
                    </td>
                    <td class="px-6 text-center py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('orders.show', $order->id) }}"
                        class="text-blue-600 hover:text-blue-900 transition-transform transform hover:scale-105 text-decoration-none">
                            📜 Chi tiết
                        </a>
                        <button type="button"
                            data-order-id="{{ $order->id }}"
                            data-order-code="{{ $order->order_code }}"
                            class="open-cancel-modal-btn text-red-600 hover:text-red-900 transition-transform transform hover:scale-105 ml-2 text-decoration-none">
                            ❌ Hủy đơn hàng
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    @endif
</div>
<!-- Cancellation Modal -->
<div id="cancelOrderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-6 transition-all duration-300 ease-out opacity-0 pointer-events-none scale-95 backdrop-blur-md">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 ease-in-out">
        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-300">
            <h3 class="text-2xl font-bold text-gray-800">
                ❗ Xác nhận hủy đơn hàng <span id="modalOrderCode" class="text-indigo-500 font-bold"></span>
            </h3>
            <button id="closeCancelModalBtn" class="text-gray-400 hover:text-red-500 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="cancelOrderForm" method="POST" action="{{route('orders.cancel')}}">
            @csrf
            <input type="hidden" id="order_id" name="order_id">
            <div class="px-6 py-5">
                <label for="cancellation_reason" class="block text-lg font-semibold text-gray-700 mb-2">
                    ✏️ Lý do hủy đơn hàng:
                </label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="reason[]" value="Thay đổi ý định" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-gray-700">Thay đổi ý định</span>
                    </label> <br>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="reason[]" value="Tìm thấy giá tốt hơn" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-gray-700">Tìm thấy giá tốt hơn</span>
                    </label> <br>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="reason[]" value="Giao hàng quá lâu" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-gray-700">Giao hàng quá lâu</span>
                    </label> <br>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="reason[]" value="Lý do khác" id="otherReasonRadio" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-gray-700">Lý do khác</span>
                    </label>
                </div>
                <textarea id="cancellation_reason" name="other_reason" rows="4"
                          class="hidden mt-2 w-full border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition resize-none text-gray-700 text-md p-3"
                          placeholder="Vui lòng nhập lý do..."></textarea>
                <p id="reasonError" class="text-red-600 text-xs mt-2 hidden">⚠️ Vui lòng nhập lý do hủy đơn.</p>
            </div>

            <!-- Footer -->
            <div class="flex justify-end space-x-3 px-6 py-4 bg-gray-100 border-t border-gray-300 rounded-b-xl">
                <button type="button" id="cancelModalActionBtn"
                        class="px-4 py-2 text-lg font-semibold bg-gray-200 rounded-xl text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all">
                    ❌ Không, giữ lại
                </button>
                <button type="submit" id="confirmCancelSubmitBtn"
                        class="px-4 py-2 text-lg font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all flex items-center">
                    <span class="btn-text">✔️ Có, hủy đơn hàng</span>
                    <svg class="animate-spin -mr-1 ml-2 h-5 w-5 text-white hidden btn-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelOrderModal = document.getElementById('cancelOrderModal');
    const closeCancelModalBtn = document.getElementById('closeCancelModalBtn');
    const cancelModalActionBtn = document.getElementById('cancelModalActionBtn'); // Nút "Không, giữ lại"
    const modalOrderCodeSpan = document.getElementById('modalOrderCode');
    const openCancelModalBtns = document.querySelectorAll('.open-cancel-modal-btn');
    const order_id = document.getElementById('order_id');
    // xử lý khi chọn lý do khác
    const otherReasonCheckbox = document.getElementById("otherReasonRadio");
    const cancellationReasonTextarea = document.getElementById("cancellation_reason");

    otherReasonCheckbox.addEventListener("change", function () {
        cancellationReasonTextarea.classList.toggle("hidden", !this.checked);
        cancellationReasonTextarea.required = this.checked;
    });

    function openModal(orderId, orderCode) {
        if (!cancelOrderModal || !modalOrderCodeSpan) return;

        modalOrderCodeSpan.textContent = `#${orderCode}`; // Cập nhật mã đơn hàng hiển thị
        order_id.value  = orderId;

        // Hiển thị modal với hiệu ứng mượt
        cancelOrderModal.classList.remove('pointer-events-none', 'opacity-0', 'scale-95');
        void cancelOrderModal.offsetWidth; // Đảm bảo hiệu ứng transition hoạt động
        cancelOrderModal.classList.add('opacity-100', 'scale-100');
    }

    function closeModal() {
        if (!cancelOrderModal) return;
        cancelOrderModal.classList.remove('opacity-100', 'scale-100');
        cancelOrderModal.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            cancelOrderModal.classList.add('pointer-events-none');
        }, 300); // Đợi transition kết thúc
    }

    openCancelModalBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const orderId = this.dataset.orderId;
            const orderCode = this.dataset.orderCode;
            openModal(orderId, orderCode);
        });
    });

    if (closeCancelModalBtn) closeCancelModalBtn.addEventListener('click', closeModal);
    if (cancelModalActionBtn) cancelModalActionBtn.addEventListener('click', closeModal);

    // Đóng modal khi nhấn vào nền mờ
    if (cancelOrderModal) {
        cancelOrderModal.addEventListener('click', function (event) {
            if (event.target === cancelOrderModal) closeModal();
        });
    }
});
</script>
@endpush

@endsection

