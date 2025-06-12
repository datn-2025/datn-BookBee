@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Thanh toán</h1>

    @if(isset($mixedFormatCart) && $mixedFormatCart)
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow" role="alert">
        <p class="font-bold">Lưu ý về phương thức thanh toán</p>
        <p>Giỏ hàng của bạn có cả sách vật lý và sách điện tử (ebook). Phương thức thanh toán khi nhận hàng không khả dụng cho đơn hàng này.</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Form thanh toán -->
        <div class="bg-white rounded-lg shadow p-6 md:col-span-8">
            <h5 class="text-2xl font-semibold mb-4 text-gray-900">Thông tin thanh toán</h5>

            <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
                @csrf
                {{-- Hidden fields for form submission --}}
                <input type="hidden" name="final_total_amount" id="form_hidden_total_amount"
                    value="{{ $subtotal + 20000 }}"> {{-- Default, JS will update --}}
                <input type="hidden" name="discount_amount_applied" id="form_hidden_discount_amount" value="0">
                <input type="hidden" name="applied_voucher_code" id="form_hidden_applied_voucher_code" value="">
                <input type="hidden" name="shipping_fee_applied" id="form_hidden_shipping_fee" value="20000"> {{--
                Default, JS will update --}}
                {{-- Khu vực nhập địa chỉ mới --}}
                <div id="new-address-form" class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="new_recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Tên
                                người nhận:</label>
                            <input type="text" name="new_recipient_name" id="new_recipient_name"
                                class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nguyễn Văn A" value="{{ old('new_recipient_name') }}">
                            @error('new_recipient_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="new_phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện
                                thoại:</label>
                            <input type="text" name="new_phone" id="new_phone"
                                class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500"
                                placeholder="09xxxxxxxx" value="{{ old('new_phone') }}">
                            @error('new_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Địa chỉ giao hàng</label>
                        <select name="address_id" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Chọn địa chỉ</option>
                            @foreach($addresses as $address)
                            <option value="{{ $address->id }}">
                                {{ $address->recipient_name }} - {{ $address->phone }} - {{ $address->address_detail }},
                                {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}
                            </option>
                            @endforeach
                        </select>
                        @error('address_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <label class="text-md font-semibold text-gray-800 mb-4">Hoặc nhập địa chỉ giao hàng mới:</label>
                    <div class="d-flex gap-4">
                        <div class="mb-4 col-4">
                            <label for="tinh" class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành
                                phố:</label>
                            <select id="tinh" name="new_address_city_id"
                                class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                            <input type="hidden" name="new_address_city_name" id="ten_tinh">
                            @error('new_address_city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('new_address_city_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 col-4">
                            <label for="quan" class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện:</label>
                            <select id="quan" name="new_address_district_id"
                                class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                            <input type="hidden" name="new_address_district_name" id="ten_quan">
                            @error('new_address_district_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('new_address_district_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4 col-3">
                            <label for="phuong" class="block text-sm font-medium text-gray-700 mb-1">Phường/Xã:</label>
                            <select id="phuong" name="new_address_ward_id"
                                class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                            <input type="hidden" name="new_address_ward_name" id="ten_phuong">
                            @error('new_address_ward_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('new_address_ward_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="new_address_detail" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ cụ
                            thể (Số nhà, tên đường):</label>
                        <input type="text" name="new_address_detail" id="new_address_detail"
                            class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ví dụ: Số 123, Đường ABC" value="{{ old('new_address_detail') }}">
                        @error('new_address_detail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <!-- Phương thức vận chuyển -->
                <div class="mb-6">
                    <h5 class="text-xl font-semibold text-gray-800 mb-3">Phương thức vận chuyển</h5>
                    <div class="space-y-2">
                        <label
                            class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center">
                                <input type="radio" name="shipping_method" value="standard" class="mr-2" checked>
                                <div>
                                    <span class="font-medium">Giao hàng tiết kiệm</span>
                                    <p class="text-sm text-gray-600">Giao hàng trong 3-5 ngày</p>
                                </div>
                            </div>
                            <span class="font-medium">20.000đ</span>
                        </label>
                        <label
                            class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center">
                                <input type="radio" name="shipping_method" value="express" class="mr-2">
                                <div>
                                    <span class="font-medium">Giao hàng nhanh</span>
                                    <p class="text-sm text-gray-600">Giao hàng trong 1-2 ngày</p>
                                </div>
                            </div>
                            <span class="font-medium">40.000đ</span>
                        </label>
                    </div>
                    @error('shipping_method')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Phương thức thanh toán -->
                <div class="mb-6">
                    <h5 class="text-xl font-semibold text-gray-800 mb-3">Phương thức thanh toán</h5>
                    <div class="space-y-2">
                        @foreach($paymentMethods as $method)
                        <label
                            class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                            <span class="font-medium text-gray-700">{{ $method->name }}</span>
                            <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 ml-3"
                                required>
                        </label>
                        @endforeach
                    </div>
                    @error('payment_method_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ghi chú -->
                <div class="mb-6">
                    <h5 class="text-xl font-semibold text-gray-800 mb-3">Ghi chú</h5>
                    <textarea name="note" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"
                        placeholder="Nhập ghi chú (nếu có)"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-green-500 text-white py-3 px-6 rounded-lg text-lg font-semibold hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-md hover:shadow-lg transition-all duration-150 ease-in-out">
                    Đặt hàng
                </button>
            </form>
        </div>

        <!-- Thông tin đơn hàng -->
        <div class="bg-white rounded-lg shadow p-6 md:col-span-4">
            <div class="space-y-5 mb-6">
                <h5 class="text-2xl font-semibold text-gray-900 tracking-wide mb-6">
                    Đơn hàng của bạn
                </h5>

                @foreach($cartItems as $item)
                <div class="flex items-center space-x-4 border-b pb-4 mt-4">
                    <img src="{{ asset('storage/' . $item->book->cover_image) }}"
                        alt="{{ $item->book->title }}" class="w-24 h-24 object-cover rounded shadow-md">
                    <div class="flex-1">
                        <h6 class="font-semibold text-gray-900 text-lg">{{ $item->book->title }}</h6>
                        <p class="text-sm text-gray-600">{{ $item->bookFormat->name }}</p>
                        <p class="text-sm text-gray-700 font-medium">Số lượng: {{ $item->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold text-indigo-700">
                            {{ number_format($item->price * $item->quantity) }}đ
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Mã giảm giá  -->
            <div class="my-6 border-gray-200">
                <label for="voucher_code_input" class="block text-sm font-medium text-gray-700 mb-1">Nhập mã hoặc chọn từ danh sách</label>
                <div class="flex items-center space-x-2 mt-1">
                    <input type="text" name="voucher_code_input" id="voucher_code_input"
                        class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2"
                        placeholder="Nhập mã">
                    <button type="button" id="open-voucher-modal-btn"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm transition-all duration-150">
                        Chọn mã
                    </button>
                </div>
                <button type="button" id="apply-voucher-btn-new"
                    class="mt-3 w-full bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:text-sm transition-all duration-300">
                    Áp dụng mã giảm giá
                </button>
                <div id="voucher-message-new" class="mt-2 text-sm"></div> <!-- For success/error messages -->
            </div>

            <!-- Tổng tiền -->
            <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tạm tính:</span>
                    <span class="text-gray-800 font-medium">{{ number_format($item->price * $item->quantity) }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phí vận chuyển:</span>
                    <span id="shipping-fee" class="text-gray-800 font-medium">20.000đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Giảm giá:</span>
                    <span id="discount-amount" class="text-gray-800 font-medium">0đ</span>
                </div>
                <div class="flex justify-between items-center font-bold text-lg">
                    <span class="text-gray-900 font-bold text-lg">Tổng cộng:</span>
                    <span id="total-amount" class="text-green-600 font-bold text-xl">
                        {{ number_format($item->price * $item->quantity + 20000) }}đ
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Voucher Modal -->
<div id="voucher-modal" class="fixed inset-0 backdrop-blur bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none">
    <div class="relative mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <p class="text-2xl font-bold text-gray-700">Chọn mã giảm giá</p>
            <button id="close-voucher-modal-btn" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="voucher-list-modal" class="mt-3 space-y-3 max-h-96 overflow-y-auto">
            <!-- Voucher items will be populated here by JavaScript -->
            @if(isset($vouchers) && count($vouchers))
                @foreach($vouchers as $voucher)
                <div class="voucher-item-modal p-4 border rounded-lg hover:bg-gray-50 cursor-pointer" data-code="{{ $voucher->code }}">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-indigo-600 voucher-code-modal">{{ $voucher->code }}</p>
                            <p class="text-sm text-gray-600">{{ $voucher->name }}</p>
                            <p class="text-xs text-gray-500">
                                @if($voucher->discount_type === 'percentage')
                                    Giảm {{ $voucher->discount_value }}%
                                    @if($voucher->max_discount_amount)
                                        (tối đa {{ number_format($voucher->max_discount_amount) }}đ)
                                    @endif
                                @elseif($voucher->discount_type === 'fixed')
                                    Giảm {{ number_format($voucher->discount_value) }}đ
                                @endif
                            </p>
                            @if($voucher->min_purchase_amount)
                                <p class="text-xs text-gray-500">Đơn tối thiểu: {{ number_format($voucher->min_purchase_amount) }}đ</p>
                            @endif
                        </div>
                        <button type="button" class="select-voucher-from-modal-btn bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600" data-code="{{ $voucher->code }}">Chọn</button>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-gray-500">Không có mã giảm giá nào.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hàm hỗ trợ định dạng số tiền
let discountValue = 0; // Global discount value

// Hàm hỗ trợ định dạng số tiền
function number_format(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

// Hàm cập nhật tổng tiền hiển thị
function updateTotal() {
    console.log('updateTotal called');
    const subtotalValue = {{ $subtotal }}; // Use the correct subtotal from controller
    const shippingFeeText = document.getElementById('shipping-fee').textContent.trim();
    const shippingFee = parseFloat(shippingFeeText.replace(/\./g, "")) || 0;

    console.log(`Subtotal: ${subtotalValue}, Discount: ${discountValue}, Shipping: ${shippingFee}`);

    let total = subtotalValue - discountValue + shippingFee;
    total = Math.max(0, total); // Ensure total is not negative
    document.getElementById('total-amount').textContent = `${number_format(total)}đ`;

    // Update hidden form fields
    document.getElementById('form_hidden_total_amount').value = total;
    document.getElementById('form_hidden_discount_amount').value = discountValue; // Use global discountValue
    document.getElementById('form_hidden_shipping_fee').value = shippingFee;
    console.log('Hidden fields updated:', {
        total: document.getElementById('form_hidden_total_amount').value,
        discount: document.getElementById('form_hidden_discount_amount').value,
        shipping: document.getElementById('form_hidden_shipping_fee').value,
        voucher_code: document.getElementById('form_hidden_applied_voucher_code').value
    });
}

// Cập nhật phí vận chuyển khi thay đổi phương thức
document.querySelectorAll('input[name="shipping_method"]').forEach(input => {
    input.addEventListener('change', function() {
        const shippingFee = this.value === 'standard' ? 20000 : 40000;
        document.getElementById('shipping-fee').textContent = `${number_format(shippingFee)}đ`;
        updateTotal();
    });
});

document.getElementById('apply-voucher-btn-new').addEventListener('click', function() {
    const applyBtn = this;
    const originalBtnText = applyBtn.textContent;
    const voucherCode = document.querySelector('input[name="voucher_code"]').value;
    const discountEl = document.getElementById('discount-amount');
    console.log(voucherCode);

    if (!voucherCode) {
        toastr.warning('Vui lòng nhập mã giảm giá.', '⚠️ Lưu ý!');
        discountEl.textContent = '0đ';
        discountValue = 0; // Reset global discount
        updateTotal();
        return;
    }

    applyBtn.disabled = true;
    applyBtn.textContent = 'Đang xử lý...';

    const currentSubtotal = {{ $subtotal }};

    fetch(`{{ route('orders.apply-voucher') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ voucher_code: voucherCode, subtotal: currentSubtotal })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Apply voucher response:', data);

        if (data.success == true) {
            discountValue = parseFloat(data.discount_amount) || 0;
            if (isNaN(discountValue)) {
                console.error('Failed to parse discount_amount from server:', data.discount_amount);
                discountValue = 0;
            }

            const successMessage = data.max_discount_applied_message
                ? data.max_discount_applied_message
                : `Áp dụng mã giảm giá "${data.voucher_code}" thành công. Bạn được giảm ${number_format(discountValue)}đ.`;

            toastr.success(successMessage, '🎉 Thành công!');
            discountEl.textContent = `-${number_format(discountValue)}đ`;
            document.getElementById('form_hidden_applied_voucher_code').value = data.voucher_code;
            document.getElementById('form_hidden_discount_amount').value = discountValue;

        } else {
            if (data.errors && Array.isArray(data.errors)) {
                data.errors.forEach(error => toastr.error(error, '❌ Lỗi!'));
            } else {
                toastr.error(data.message || 'Mã giảm giá không hợp lệ hoặc có lỗi xảy ra.', '❌ Lỗi!');
            }
            discountEl.textContent = '0đ';
            document.getElementById('form_hidden_applied_voucher_code').value = '';
            document.getElementById('form_hidden_discount_amount').value = 0;
        }

        updateTotal();
    })
    .catch(error => {
        console.error('Error applying voucher:', error);
        toastr.error('Có lỗi xảy ra khi áp dụng mã giảm giá.', '❌ Lỗi!');
        discountEl.textContent = '0đ';
        document.getElementById('form_hidden_applied_voucher_code').value = '';
        document.getElementById('form_hidden_discount_amount').value = 0;
        updateTotal();
    })
    .finally(() => {
        applyBtn.disabled = false;
        applyBtn.textContent = originalBtnText;
    });
});

// Hàm áp dụng mã giảm giá được gợi ý
function applySuggestedVoucher(code, event) {
    event.preventDefault();
    const input = document.querySelector('input[name="voucher_code"]');
    input.value = code;
    document.getElementById('apply-voucher').click();
}

// Cập nhật tổng tiền lần đầu khi trang load
updateTotal();


document.addEventListener('DOMContentLoaded', function () {
    const voucherModal = document.getElementById('voucher-modal');
    const openModalBtn = document.getElementById('open-voucher-modal-btn');
    const closeModalBtn = document.getElementById('close-voucher-modal-btn');
    const voucherCodeInput = document.getElementById('voucher_code_input');
    const applyVoucherBtnNew = document.getElementById('apply-voucher-btn-new');
    const voucherMessageElNew = document.getElementById('voucher-message-new');
    const hiddenAppliedVoucherCode = document.getElementById('form_hidden_applied_voucher_code');
    const discountAmountEl = document.getElementById('discount-amount');

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            if (voucherModal) {
                voucherModal.classList.remove('opacity-0', 'pointer-events-none');
                voucherModal.classList.add('opacity-100');
            }
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function () {
            if (voucherModal) {
                voucherModal.classList.remove('opacity-100');
                voucherModal.classList.add('opacity-0', 'pointer-events-none');
            }
        });
    }

    if (voucherModal) {
        voucherModal.addEventListener('click', function (event) {
            if (event.target === voucherModal) {
                voucherModal.classList.remove('opacity-100');
                voucherModal.classList.add('opacity-0', 'pointer-events-none');
            }
        });
    }

    const voucherItems = document.querySelectorAll('.voucher-item-modal');
    voucherItems.forEach(item => {
        item.addEventListener('click', function (event) {
            if (event.target.closest('.select-voucher-from-modal-btn')) {
                return;
            }
            const code = this.dataset.code;
            if (voucherCodeInput) voucherCodeInput.value = code;
            if (voucherModal) voucherModal.style.display = 'none';
        });
    });

    const selectVoucherButtonsModal = document.querySelectorAll('.select-voucher-from-modal-btn');
    selectVoucherButtonsModal.forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const code = this.dataset.code;
            if (voucherCodeInput) voucherCodeInput.value = code;
            if (voucherModal) voucherModal.style.display = 'none';
        });
    });

    if (applyVoucherBtnNew && voucherCodeInput && voucherMessageElNew && hiddenAppliedVoucherCode && discountAmountEl) {
        applyVoucherBtnNew.addEventListener('click', function() {
            const voucherCode = voucherCodeInput.value.trim();
            const subtotalForVoucher = {{ $subtotal }};

            if (!voucherCode) {
                voucherMessageElNew.innerHTML = '<p class="text-red-500">Vui lòng nhập mã giảm giá hoặc chọn từ danh sách.</p>';
                if (typeof toastr !== 'undefined') toastr.warning('Vui lòng nhập mã giảm giá hoặc chọn từ danh sách.');
                return;
            }

            applyVoucherBtnNew.disabled = true;
            applyVoucherBtnNew.textContent = 'Đang áp dụng...';
            voucherMessageElNew.innerHTML = '';

            fetch(`{{ route('orders.apply-voucher') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    voucher_code: voucherCode,
                    subtotal: subtotalForVoucher
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw { status: response.status, data: errData };
                    }).catch(() => {
                        throw { status: response.status, data: { message: `Lỗi HTTP: ${response.status}` } };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    discountValue = parseFloat(data.discount_amount || 0);
                    hiddenAppliedVoucherCode.value = data.voucher_code || '';
                    discountAmountEl.textContent = `${number_format(discountValue)}đ`;

                    voucherMessageElNew.innerHTML = `<p class="text-green-500">${data.message || `Áp dụng mã giảm giá "${data.voucher_code}" thành công. Bạn được giảm ${number_format(discountValue)}đ.`}</p>`;
                    if (typeof toastr !== 'undefined') toastr.success(data.message || `Áp dụng mã giảm giá "${data.voucher_code}" thành công. Bạn được giảm ${number_format(discountValue)}đ.`);
                } else {
                    discountValue = 0;
                    hiddenAppliedVoucherCode.value = '';
                    discountAmountEl.textContent = '0đ';

                    let errorMessages = '<ul class="list-disc list-inside">';
                    if (data.errors && typeof data.errors === 'object' && Object.keys(data.errors).length > 0) {
                        for (const key in data.errors) {
                            if (Array.isArray(data.errors[key])) {
                                data.errors[key].forEach(msg => { errorMessages += `<li class="text-red-500">${msg}</li>`; });
                            } else { errorMessages += `<li class="text-red-500">${data.errors[key]}</li>`; }
                        }
                    } else if (data.message) {
                         errorMessages += `<li class="text-red-500">${data.message}</li>`;
                    } else {
                        errorMessages += '<li class="text-red-500">Không thể áp dụng mã giảm giá.</li>';
                    }
                    errorMessages += '</ul>';
                    voucherMessageElNew.innerHTML = errorMessages;
                    if (typeof toastr !== 'undefined') toastr.error(data.message || 'Không thể áp dụng mã giảm giá. Vui lòng kiểm tra lại.');
                }
            })
            .catch(error => {
                console.error('Lỗi khi áp dụng voucher:', error);
                discountValue = 0;
                hiddenAppliedVoucherCode.value = '';
                discountAmountEl.textContent = '0đ';

                let errorMessageText = 'Có lỗi xảy ra khi áp dụng mã giảm giá.';
                let detailedErrorsHtml = '<ul class="list-disc list-inside">';
                let hasDetailedErrors = false;

                if (error && error.data) {
                    if (error.data.message) errorMessageText = error.data.message;
                    if (error.data.errors && typeof error.data.errors === 'object') {
                        for (const key in error.data.errors) {
                            if (Array.isArray(error.data.errors[key])) {
                                error.data.errors[key].forEach(msg => { detailedErrorsHtml += `<li class="text-red-500">${msg}</li>`; hasDetailedErrors = true;});
                            } else { detailedErrorsHtml += `<li class="text-red-500">${error.data.errors[key]}</li>`; hasDetailedErrors = true; }
                        }
                    }
                } else if (error && error.message) {
                    errorMessageText = error.message;
                }
                detailedErrorsHtml += '</ul>';

                voucherMessageElNew.innerHTML = hasDetailedErrors ? detailedErrorsHtml : `<p class="text-red-500">${errorMessageText}</p>`;
                if (typeof toastr !== 'undefined') toastr.error(errorMessageText);
            })
            .finally(() => {
                applyVoucherBtnNew.disabled = false;
                applyVoucherBtnNew.textContent = 'Áp dụng mã giảm giá';
                updateTotal();
            });
        });
    } else {
        console.warn('Một hoặc nhiều phần tử UI cho voucher mới không được tìm thấy. Các chức năng có thể không hoạt động.');
    }
});
</script>
@endpush
@endsection
