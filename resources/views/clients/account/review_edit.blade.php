@extends('layouts.account.layout')
@section('title', 'Cập nhât đánh giá')

@section('account_content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white py-8">

        <!-- Back Button - Adidas Style -->
        <div class="mb-8">
            <a href="{{ route('account.purchase') }}"
                class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-gray-300 hover:border-black text-black font-bold uppercase tracking-wide transition-all duration-300 hover:bg-gray-50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                QUAY LẠI DANH SÁCH
            </a>
        </div>

        <!-- Edit Review Section -->
        <div class="mt-8">
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
                                        <div class="w-16 h-20 bg-gray-200 border-2 border-gray-300 overflow-hidden">
                                            @if ($item->isCombo())
                                                @if ($item->collection && $item->collection->cover_image)
                                                    <img src="{{ asset('storage/' . $item->collection->cover_image) }}"
                                                        alt="{{ $item->collection->name }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full bg-black flex items-center justify-center">
                                                        <span class="text-white text-xs font-bold">COMBO</span>
                                                    </div>
                                                @endif
                                            @else
                                                @if ($item->book && $item->book->cover_image)
                                                    <img src="{{ asset('storage/' . $item->book->cover_image) }}"
                                                        alt="{{ $item->book->title }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full bg-gray-300 flex items-center justify-center">
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
                                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z">
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
                                                                            fill="currentColor" viewBox="0 0 20 20">
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
                <form action="{{ route('account.reviews.update', $review->id) }}" method="POST" class="space-y-6"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Rating Stars -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá sao <span
                                class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button"
                                    class="rating-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-300 transition-colors duration-150"
                                    data-rating="{{ $i }}">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="1000"
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
                        @if ($review->images)
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-4">
                                @foreach ($review->images as $image)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $image) }}" alt="Review image"
                                            class="w-full h-24 object-cover rounded-lg">
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                            <button type="button"
                                                class="text-white hover:text-red-500 transition-colors duration-200"
                                                onclick="removeImage(this)">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                            accept="image/*" onchange="previewImages(this)">

                        <!-- Image Preview -->
                        <div id="imagePreview"
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4"></div>

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
                                <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    @if ($remainingTime->invert)
                                        <span class="font-medium">Đã hết thời gian sửa đánh giá.</span>
                                    @else
                                        <span class="font-medium">Còn {{ $remainingTime->h }} giờ
                                            {{ $remainingTime->i }} phút</span>
                                        (24 giờ sau khi đánh giá, bạn sẽ không thể sửa đổi đánh giá nữa.)
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
                        @if (!$remainingTime->invert)
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white 
              bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cập nhật đánh giá
                            </button>
                        @endif
                    </div>
                </form>
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
