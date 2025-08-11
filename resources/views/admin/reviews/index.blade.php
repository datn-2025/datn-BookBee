@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.backend')

@section('title', 'Quản lý đánh giá')

@section('content')
    <div class="container-fluid">
        <!-- Tiêu đề -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quản lý đánh giá</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Đánh giá</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nội dung -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Danh sách đánh giá</h4>
                    </div>

                    <div class="card-body">
                        {{-- Thông báo --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Bộ lọc --}}
                        <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái phản hồi</label>
                                <select name="admin_response" class="form-select">
                                    <option value="">Tất cả</option>
                                    <option value="responded"
                                        {{ request('admin_response') == 'responded' ? 'selected' : '' }}>Đã phản hồi
                                    </option>
                                    <option value="not_responded"
                                        {{ request('admin_response') == 'not_responded' ? 'selected' : '' }}>Chưa phản hồi
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái hiển thị</label>
                                <select name="status" class="form-select">
                                    <option value="">Tất cả</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                    <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                                    <option value="visible" {{ request('status') == 'visible' ? 'selected' : '' }}>Hiện (Legacy)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tên Sách</label>
                                <input type="text" name="product_name" class="form-control"
                                    value="{{ request('product_name') }}" placeholder="Tên sản phẩm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tên khách hàng</label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="{{ request('customer_name') }}" placeholder="Tên khách hàng">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Số sao đánh giá</label>
                                <select name="rating" class="form-select">
                                    <option value="">Tất cả</option>
                                    @foreach (range(5, 1) as $i)
                                        <option value="{{ $i }}"
                                            {{ request('rating') == $i ? 'selected' : '' }}>
                                            {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }} ({{ $i }}
                                            sao)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Nội dung bình luận</label>
                                <input type="text" name="cmt" class="form-control" value="{{ request('cmt') }}"
                                    placeholder="Tìm theo nội dung đánh giá hoặc phản hồi">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i> Làm mới
                                </a>
                            </div>
                        </form>

                        {{-- Danh sách --}}
                        <div class="table-responsive table-card mt-3">
                            @if ($reviews->isEmpty())
                                <div class="noresult text-center py-5">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/{{ filled(request()->all()) ? 'msoeawqm.json' : 'nocovwne.json' }}"
                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                        style="width:100px;height:100px">
                                    </lord-icon>
                                    <h5 class="mt-3 text-{{ filled(request()->all()) ? 'danger' : 'muted' }}">
                                        {{ filled(request()->all()) ? 'Không tìm thấy đánh giá phù hợp' : 'Danh sách đánh giá hiện đang trống' }}
                                    </h5>
                                    <p class="text-muted">
                                        {{ filled(request()->all()) ? 'Vui lòng kiểm tra lại bộ lọc hoặc thử từ khóa khác.' : 'Hãy đợi khách hàng để lại đánh giá để bắt đầu quản lý.' }}
                                    </p>
                                </div>
                            @else
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Sản phẩm</th>
                                            <th>Loại & Định dạng</th>
                                            <th>Bình luận</th>
                                            <th>Phản hồi Admin</th>
                                            <th class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reviews as $index => $review)
                                            <tr class="{{ in_array($review->status, ['hidden', 'pending']) ? 'table-light text-muted' : '' }}">
                                                <td>{{ $reviews->firstItem() + $index }}</td>
                                                <td style="max-width: 200px; white-space: normal;">
                                                    @if($review->isComboReview())
                                                        @if($review->collection)
                                                            <a href="{{ route('combos.show', $review->collection->id) }}" 
                                                               class="text-decoration-none fw-medium text-success" 
                                                               target="_blank">
                                                                {{ $review->collection->name }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Combo đã bị xóa</span>
                                                        @endif
                                                    @else
                                                        @if ($review->book)
                                                        @permission('review.show')
                                                            <a href="{{ route('admin.books.show', ['id' => $review->book->id, 'slug' => Str::slug($review->book->title)]) }}"
                                                                class="text-decoration-none fw-medium">
                                                                {{ $review->book->title }}
                                                            </a>
                                                            @endpermission
                                                        @else
                                                            <span class="text-muted">Sản phẩm đã xóa</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge {{ $review->isComboReview() ? 'bg-success' : 'bg-primary' }} text-white">
                                                            {{ $review->product_type }}
                                                        </span>
                                                        @if($review->order && $review->order->orderItems->count() > 0)
                                                            @php
                                                                $orderItem = $review->isComboReview() 
                                                                    ? $review->order->orderItems->where('collection_id', $review->collection_id)->first()
                                                                    : $review->order->orderItems->where('book_id', $review->book_id)->first();
                                                            @endphp
                                                            @if($orderItem && $orderItem->bookFormat)
                                                <span class="badge {{ strtolower($orderItem->bookFormat->format_name) === 'ebook' ? 'bg-info' : 'bg-secondary' }} text-white">
                                                    {{ $orderItem->bookFormat->format_name }}
                                                </span>
                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                                <td style="max-width: 400px;">
                                                    <div class="fw-semibold">
                                                        {{ $review->user->name ?? 'Người dùng đã xóa' }}</div>
                                                    <div class="text-muted small mb-1">
                                                        <i
                                                            class="ri-calendar-line me-1"></i>{{ $review->created_at->format('H:i d/m/Y') }}
                                                    </div>
                                                    <div>
                                                        @foreach (range(1, 5) as $i)
                                                            <i
                                                                class="fas fa-star{{ $i <= $review->rating ? ' text-warning' : ' text-muted' }}"></i>
                                                        @endforeach
                                                        @php
                                                            $statusConfig = match($review->status) {
                                                                'approved' => ['class' => 'bg-success', 'text' => 'Đã duyệt'],
                                                                'pending' => ['class' => 'bg-warning text-dark', 'text' => 'Chờ duyệt'],
                                                                'hidden' => ['class' => 'bg-secondary', 'text' => 'Đã ẩn'],
                                                                'visible' => ['class' => 'bg-info', 'text' => 'Legacy'],
                                                                default => ['class' => 'bg-dark', 'text' => $review->status]
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $statusConfig['class'] }} ms-2">
                                                            {{ $statusConfig['text'] }}
                                                        </span>
                                                    </div>
                                                    <div class="text-truncate small mt-1">{{ $review->comment }}</div>
                                                    @if($review->images && count($review->images) > 0)
                                                        <div class="mt-2">
                                                            <small class="text-muted">Ảnh đánh giá:</small>
                                                            <div class="d-flex gap-1 mt-1">
                                                                @foreach(array_slice($review->images, 0, 3) as $imagePath)
                                                                    <img src="{{ asset('storage/' . $imagePath) }}" 
                                                                         alt="Review Image" 
                                                                         class="rounded" 
                                                                         style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
                                                                         onclick="showImageModal('{{ asset('storage/' . $imagePath) }}')"
                                                                         title="Click để xem phóng to">
                                                                @endforeach
                                                                @if(count($review->images) > 3)
                                                                    <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                                                                         style="width: 40px; height: 40px; font-size: 12px; color: #666;">
                                                                        +{{ count($review->images) - 3 }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="max-width: 300px;">
                                                    <span
                                                        class="badge bg-{{ $review->admin_response ? 'success' : 'secondary' }}">
                                                        {{ $review->admin_response ? 'Đã phản hồi' : 'Chưa phản hồi' }}
                                                    </span>
                                                    <div class="mt-1 text-truncate small" style="max-height: 60px;">
                                                        {{ $review->admin_response ?? 'Chưa có phản hồi' }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @permission('review.response')
                                                    <a href="{{ route('admin.reviews.response', $review) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Xem & phản hồi">
                                                        <i class="ri-chat-3-fill"></i>
                                                    </a>
                                                    @endpermission
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Phân trang --}}
                                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                                    <div class="text-muted">
                                        Hiển thị <strong>{{ $reviews->firstItem() }}</strong> đến
                                        <strong>{{ $reviews->lastItem() }}</strong> trong tổng số
                                        <strong>{{ $reviews->total() }}</strong> đánh giá
                                    </div>
                                    <div>
                                        {{ $reviews->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal hiển thị ảnh phóng to --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Ảnh đánh giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Review Image" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    {{-- Reset Toastr để không hiện lại khi dùng nút quay lại --}}
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Function để hiển thị ảnh trong modal
        function showImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }
    </script>
@endsection
