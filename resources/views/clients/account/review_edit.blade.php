@extends('layouts.account.layout')

@section('account_content')
<div class="max-w-xl mx-auto bg-white border border-black shadow mb-8 rounded-none">
    <div class="px-6 py-4 border-b border-black bg-black rounded-t-none">
        <h1 class="text-2xl font-bold text-white uppercase tracking-wide">Sửa đánh giá sản phẩm</h1>
    </div>
    <div class="p-6">
        <div class="flex flex-col sm:flex-row gap-6 items-center mb-6">
            @if($productType === 'book')
                @php
                    $bookImageUrl = asset('images/default-book.jpg');
                    if ($orderItem->book->cover_image) {
                        $bookImageUrl = asset('storage/' . $orderItem->book->cover_image);
                    } elseif ($orderItem->book->images && $orderItem->book->images->isNotEmpty()) {
                        $bookImageUrl = asset('storage/' . $orderItem->book->images->first()->image_url);
                    }
                @endphp
                <img src="{{ $bookImageUrl }}" alt="{{ $orderItem->book->title }}" 
                     class="w-24 h-32 object-cover border border-slate-300 shadow-sm rounded-none" 
                     onerror="this.src='{{ asset('images/default-book.jpg') }}'; this.onerror=null;">
                <div class="flex-1 w-full">
                    <div class="font-semibold text-lg text-black mb-1">{{ $orderItem->book->title }}</div>
                    <div class="text-xs text-gray-500 mb-1">Tác giả: <span class="font-medium text-black">{{ $orderItem->book->authors->first()->name ?? 'N/A' }}</span></div>
                    <div class="text-xs text-gray-500 mb-1">Nhà xuất bản: <span class="font-medium text-black">{{ $orderItem->book->brand->name ?? 'N/A' }}</span></div>
                    <div class="text-xs text-gray-500 mb-1">Danh mục: <span class="font-medium text-black">{{ $orderItem->book->category->name ?? 'N/A' }}</span></div>
                    <div class="text-xs text-gray-500 mb-1">Định dạng sách: <span class="font-medium text-black">{{ $orderItem->book->is_ebook ? 'Ebook' : 'Sách vật lý' }}</span></div>
                    <div class="text-xs text-gray-500 mb-1">Ngôn ngữ: <span class="font-medium text-black">{{ $orderItem->book->language ?? 'N/A' }}</span></div>
                    <div class="text-xs text-gray-500 mb-1">Loại bìa: <span class="font-medium text-black">{{ $orderItem->book->cover_type ?? 'N/A' }}</span></div>
                    <div class="text-xs text-gray-500 mb-1">Kích thước: <span class="font-medium text-black">{{ $orderItem->book->size ?? 'N/A' }}</span></div>
                    <div class="text-xs text-gray-500">Giá: <span class="font-medium text-black">{{ number_format($orderItem->price, 0, ',', '.') }} đ</span></div>
                    <div class="text-xs text-gray-500">Số lượng: <span class="font-medium text-black">{{ $orderItem->quantity }}</span></div>
                </div>
            @else
                @php
                    $collectionImageUrl = asset('images/default-book.jpg');
                    if ($orderItem->collection->cover_image) {
                        $collectionImageUrl = asset('storage/' . $orderItem->collection->cover_image);
                    }
                @endphp
                <img src="{{ $collectionImageUrl }}" alt="{{ $orderItem->collection->name }}" 
                     class="w-24 h-32 object-cover border border-slate-300 shadow-sm rounded-none"
                     onerror="this.src='{{ asset('images/default-book.jpg') }}'; this.onerror=null;">
                <div class="flex-1 w-full">
                    <div class="font-semibold text-lg text-black mb-1">{{ $orderItem->collection->name }}</div>
                    <div class="text-xs text-gray-500 mb-1">Loại: <span class="font-medium text-black">Combo</span></div>
                    <div class="text-xs text-gray-500 mb-1">Mô tả: <span class="font-medium text-black">{{ $orderItem->collection->description ?? 'N/A' }}</span></div>
                    <div class="text-xs text-gray-500">Giá: <span class="font-medium text-black">{{ number_format($orderItem->price, 0, ',', '.') }} đ</span></div>
                    <div class="text-xs text-gray-500">Số lượng: <span class="font-medium text-black">{{ $orderItem->quantity }}</span></div>
                </div>
            @endif
        </div>
        <div class="bg-gray-50 border border-slate-200 rounded-none p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-xs text-gray-600">
                <div>Mã đơn hàng: <span class="font-medium text-black">{{ $order->order_code }}</span></div>
                <div>Trạng thái: <span class="font-medium text-black">{{ $order->orderStatus->name }}</span></div>
                <div>Phí vận chuyển: <span class="font-medium text-black">{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }} đ</span></div>
                <div>Địa chỉ nhận: <span class="font-medium text-black">{{ $order->address->address_detail ?? 'Không có địa chỉ' }}</span></div>
                <div>Phương thức thanh toán: <span class="font-medium text-black">{{ $order->paymentMethod->name ?? 'Chưa xác định' }}</span></div>
                <div>Thời gian đặt hàng: <span class="font-medium text-black">{{ $order->created_at }}</span></div>
            </div>
            <div class="mt-4 border-t pt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-gray-600">Tạm tính:</span><span class="font-medium">{{ number_format($orderItem->price * $orderItem->quantity, 0, ',', '.') }} đ</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Phí vận chuyển:</span><span class="font-medium">{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }} đ</span></div>
                @if (($order->discount_amount ?? 0) > 0)
                <div class="flex justify-between text-green-600"><span class="font-medium">Giảm giá ({{ $order->applied_voucher_code ?? 'Voucher' }}):</span><span class="font-medium">- {{ number_format($order->discount_amount, 0, ',', '.') }} đ</span></div>
                @endif
                <div class="flex justify-between text-base font-bold text-black border-t pt-2 mt-2"><span>Tổng cộng:</span><span>{{ number_format($order->total_amount, 0, ',', '.') }} đ</span></div>
            </div>
        </div>
        <form action="{{ route('account.reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5" id="edit-review-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            @if($productType === 'book')
                <input type="hidden" name="book_id" value="{{ $orderItem->book->id }}">
            @else
                <input type="hidden" name="collection_id" value="{{ $orderItem->collection->id }}">
            @endif
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Số sao đánh giá:</label>
                <div class="flex flex-row-reverse justify-start gap-1" id="star-group">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="sr-only" {{ (string)old('rating', $review->rating) === (string)$i ? 'checked' : '' }}>
                        <label for="star{{ $i }}" class="star-label cursor-pointer text-3xl transition-colors duration-150 {{ (string)old('rating', $review->rating) >= (string)$i ? 'text-yellow-400' : 'text-slate-300 hover:text-yellow-400 focus:text-yellow-400' }}" data-star="{{ $i }}" title="{{ $i }} sao">★</label>
                    @endfor
                </div>
            </div>
            <div>
                <label for="comment" class="block text-sm font-semibold text-black mb-2">Nhận xét chi tiết:</label>
                <textarea id="comment" name="comment" rows="4" class="w-full px-3 py-2 border border-black rounded-none focus:ring-2 focus:ring-black focus:border-black transition-colors duration-200 text-sm resize-none text-black bg-white" placeholder="Nhận xét về sản phẩm..." required>{{ old('comment', $review->comment) }}</textarea>
            </div>
            
            <!-- Current Images Section -->
            @php
                $reviewImages = $review->images;
                if (is_string($reviewImages)) {
                    $reviewImages = json_decode($reviewImages, true) ?? [];
                }
                $reviewImages = is_array($reviewImages) ? $reviewImages : [];
            @endphp
            @if(!empty($reviewImages))
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Hình ảnh hiện tại:</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
                    @foreach($reviewImages as $imagePath)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $imagePath) }}" alt="Review Image" class="w-full h-24 object-cover border border-gray-300 rounded">
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Upload New Images Section -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Cập nhật hình ảnh đánh giá (tùy chọn):</label>
                <div class="border-2 border-dashed border-gray-300 rounded-none p-6 text-center hover:border-black transition-colors duration-200">
                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                    <label for="images" class="cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-gray-600 mb-1">Nhấp để chọn hình ảnh mới</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF tối đa 2MB mỗi ảnh (tối đa 5 ảnh)</p>
                            <p class="text-xs text-red-500 mt-1">Lưu ý: Chọn hình ảnh mới sẽ thay thế toàn bộ hình ảnh cũ</p>
                        </div>
                    </label>
                </div>
                
                <!-- Preview New Images -->
                <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 hidden">
                    <!-- Images will be displayed here -->
                </div>
                
                @error('images')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col sm:flex-row gap-2 mt-6">
                <a href="{{ route('account.orders.show', $order->id) }}" class="flex-1 px-4 py-2 bg-gray-200 text-black text-sm font-medium rounded-none hover:bg-gray-300 transition-colors duration-150 text-center">Xem chi tiết đơn hàng</a>
                <a href="{{ route('account.purchase') }}" class="flex-1 px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-none hover:bg-gray-200 transition-colors duration-150 text-center">Quay lại danh sách đánh giá</a>
                @if($productType === 'book')
                    <a href="{{ route('books.show', $orderItem->book->slug) }}" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-none hover:bg-green-700 transition-colors duration-150 text-center">Mua lại</a>
                @else
                    <a href="{{ route('combos.show', $orderItem->collection->slug) }}" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-none hover:bg-green-700 transition-colors duration-150 text-center">Mua lại</a>
                @endif
                <button type="submit" class="flex-1 px-4 py-2 bg-black hover:bg-gray-900 text-white text-base font-semibold rounded-none transition-colors duration-200 focus:ring-2 focus:ring-black focus:ring-offset-2">
                    Cập nhật đánh giá
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const starLabels = document.querySelectorAll('#star-group .star-label');
    const starInputs = document.querySelectorAll('#star-group input[type=radio]');
    let currentRating = parseInt(document.querySelector('#star-group input[type=radio]:checked')?.value || 0);

    function highlightStars(rating) {
        starLabels.forEach(label => {
            const star = parseInt(label.getAttribute('data-star'));
            if (star <= rating) {
                label.classList.add('text-yellow-400');
                label.classList.remove('text-slate-300');
            } else {
                label.classList.remove('text-yellow-400');
                label.classList.add('text-slate-300');
            }
        });
    }

    starLabels.forEach(label => {
        label.addEventListener('mouseenter', function () {
            highlightStars(parseInt(label.getAttribute('data-star')));
        });
        label.addEventListener('mouseleave', function () {
            highlightStars(currentRating);
        });
        label.addEventListener('click', function () {
            const val = parseInt(label.getAttribute('data-star'));
            currentRating = val;
            document.querySelector(`#star${val}`).checked = true;
            highlightStars(currentRating);
        });
    });

    // Đảm bảo highlight đúng khi load lại
    highlightStars(currentRating);
});

function previewImages(input) {
    const previewContainer = document.getElementById('imagePreview');
    previewContainer.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        // Giới hạn tối đa 5 ảnh
        const maxFiles = 5;
        const files = Array.from(input.files).slice(0, maxFiles);
        
        if (input.files.length > maxFiles) {
            alert(`Chỉ được chọn tối đa ${maxFiles} hình ảnh. ${maxFiles} hình ảnh đầu tiên sẽ được sử dụng.`);
        }
        
        previewContainer.classList.remove('hidden');
        
        files.forEach((file, index) => {
            // Kiểm tra kích thước file (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert(`Hình ảnh "${file.name}" vượt quá 2MB. Vui lòng chọn hình ảnh nhỏ hơn.`);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageDiv = document.createElement('div');
                imageDiv.className = 'relative group';
                imageDiv.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-24 object-cover border border-gray-300 rounded">
                    <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        ×
                    </button>
                `;
                previewContainer.appendChild(imageDiv);
            };
            reader.readAsDataURL(file);
        });
    } else {
        previewContainer.classList.add('hidden');
    }
}

function removeImage(index) {
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    
    // Thêm lại tất cả files trừ file tại index cần xóa
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    previewImages(input);
}
</script>
@endpush
@endsection
