@extends('layouts.backend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Chi tiết đánh giá & Phản hồi</h3>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>

                    <div class="card-body">
                        {{-- Thông tin đánh giá khách hàng --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Đánh giá khách hàng</h5>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-6">
                                    <p><strong>Khách hàng:</strong> {{ $review->user->name ?? 'Khách' }}</p>
                                    <p><strong>Ngày đánh giá:</strong> {{ $review->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Số sao:</strong>
                                        @foreach (range(1, 5) as $i)
                                            <i class="fas fa-star{{ $i <= $review->rating ? ' text-warning' : ' text-muted' }}"></i>
                                        @endforeach
                                    </p>
                                    <p><strong>Trạng thái đánh giá:</strong>
                                        <span class="badge {{ $review->status === 'visible' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $review->status === 'visible' ? 'Hiển thị' : 'Đã ẩn' }}
                                        </span>
                                    </p>
                                    <form action="{{ route('admin.reviews.update-status', $review->id) }}" method="POST" onsubmit="return disableSubmitOnce(this)">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $review->status === 'hidden' ? 'danger' : 'primary' }}" title="{{ $review->status === 'hidden' ? 'Hiển thị' : 'Ẩn' }}">
                                            <i class="fas fa-eye{{ $review->status === 'hidden' ? '-slash' : '' }}"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="col-md-6">
                                    <p><strong>Bình luận:</strong></p>
                                    <div class="border p-3 rounded bg-light">{{ $review->comment }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Phản hồi của admin --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Phản hồi của quản trị viên</h5>
                            </div>
                            <div class="card-body">
                                @if ($review->admin_response)
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Ngày phản hồi:</strong>
                                        <span>{{ $review->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="bg-light border p-3 rounded">{{ $review->admin_response }}</div>
                                @else
                                    <form action="{{ route('admin.reviews.response.store', $review) }}" method="POST" onsubmit="return disableSubmitOnce(this)">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="admin_response" class="form-label">Nội dung phản hồi</label>
                                            <textarea name="admin_response" id="admin_response" class="form-control @error('admin_response') is-invalid @enderror" rows="4" required placeholder="Nhập phản hồi...">{{ old('admin_response', $review->admin_response) }}</textarea>
                                            @error('admin_response')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-1"></i> Gửi phản hồi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Thông tin sản phẩm --}}
                        @isset($review->book)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Thông tin sản phẩm</h5>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-3 text-center">
                                        <img src="{{ $review->book->cover_image ? asset('storage/' . $review->book->cover_image) : asset('images/placeholder.jpg') }}" class="img-fluid rounded shadow-sm" alt="{{ $review->book->title }}" style="max-height: 300px;">
                                    </div>

                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @isset($review->book->isbn)
                                                    <p><strong>ISBN:</strong> {{ $review->book->isbn }}</p>
                                                @endisset
                                                <p><strong>Tên:</strong> {{ $review->book->title }}</p>
                                                @isset($review->book->author)
                                                    <p><strong>Tác giả:</strong> {{ $review->book->author->name }}</p>
                                                @endisset
                                                @isset($review->book->brand)
                                                    <p><strong>Thương hiệu:</strong> {{ $review->book->brand->name }}</p>
                                                @endisset
                                                @isset($review->book->category)
                                                    <p><strong>Thể loại:</strong> {{ $review->book->category->name }}</p>
                                                @endisset
                                                @isset($review->book->publication_date)
                                                    <p><strong>Ngày xuất bản:</strong> {{ $review->book->publication_date->format('d/m/Y') }}</p>
                                                @endisset
                                                @isset($review->book->page_count)
                                                    <p><strong>Số trang:</strong> {{ $review->book->page_count }}</p>
                                                @endisset
                                            </div>

                                            <div class="col-md-6">
                                                <p><strong>Trạng thái sản phẩm:</strong>
                                                    <span class="badge {{ $review->book->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $review->book->is_active ? 'Còn hàng' : 'Hết hàng' }}
                                                    </span>
                                                </p>
                                                @isset($review->orderItem)
                                                    <p><strong>Định dạng:</strong> <span class="badge {{ strtolower($review->orderItem->format) === 'ebook' ? 'bg-info' : 'bg-primary' }}">{{ ucfirst($review->orderItem->format) }}</span></p>
                                                    @isset($review->orderItem->language)
                                                        <p><strong>Ngôn ngữ:</strong> {{ $review->orderItem->language }}</p>
                                                    @endisset
                                                    @isset($review->orderItem->dimensions)
                                                        <p><strong>Kích thước:</strong> {{ $review->orderItem->dimensions }}</p>
                                                    @endisset
                                                @else
                                                    <p class="text-muted">Không có thông tin đơn hàng liên quan.</p>
                                                @endisset
                                            </div>
                                        </div>

                                        <hr>

                                        <p><strong>Đánh giá trung bình:</strong>
                                            @if ($review->book->reviews_avg_rating)
                                                @foreach (range(1, 5) as $i)
                                                    <i class="fas fa-star{{ $i <= round($review->book->reviews_avg_rating) ? ' text-warning' : ' text-muted' }}"></i>
                                                @endforeach
                                                ({{ number_format($review->book->reviews_avg_rating, 1) }}/5)
                                            @else
                                                <span class="text-muted">Chưa có đánh giá</span>
                                            @endif
                                        </p>

                                        <p><strong>Số lượng đánh giá:</strong> {{ $review->book->reviews_count ?? 0 }}</p>
                                        <p><strong>Số lượng đã bán:</strong> {{ $review->book->sold_count ?? 0 }}</p>

                                        <div class="mt-3 d-flex gap-2">
                                            <a href="{{ route('admin.books.show', ['id' => $review->book->id, 'slug' => $review->book->slug ?? Str::slug($review->book->title)]) }}" class="btn btn-outline-dark" target="_blank">
                                                <i class="fas fa-cogs me-1"></i> Xem ở trang quản trị
                                            </a>
                                            <a href="{{ route('books.show', $review->book->slug ?? $review->book->id) }}" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye me-1"></i> Xem ở giao diện khách hàng
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Đánh giá khác --}}
                        @if ($otherReviews->count())
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Các đánh giá khác của sản phẩm</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Người đánh giá</th>
                                                    <th>Nội dung</th>
                                                    <th>Đánh giá</th>
                                                    <th>Ngày đăng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($otherReviews as $otherReview)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $otherReview->user->name ?? 'Khách' }}</td>
                                                        <td class="text-truncate" style="max-width: 250px;">{{ $otherReview->comment }}</td>
                                                        <td>
                                                            @foreach (range(1, 5) as $i)
                                                                <i class="fas fa-star{{ $i <= $otherReview->rating ? ' text-warning' : ' text-muted' }}"></i>
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $otherReview->created_at->format('d/m/Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($otherReviews->hasPages())
                                        <div class="card-footer">
                                            {{ $otherReviews->links() }}
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
@endsection

@push('scripts')
    <script>
        // Chống submit nhiều lần
        function disableSubmitOnce(form) {
            const btn = form.querySelector("button[type=submit]");
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...`;
            }
            return true;
        }
    </script>
@endpush
