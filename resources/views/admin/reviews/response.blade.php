@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Phản hồi đánh giá</h3>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-default float-right">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
                <div class="card-body">
                    <!-- Thông tin đánh giá hiện tại -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5>Thông tin đánh giá</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Khách hàng:</strong> {{ $review->user->name ?? 'Khách' }}</p>
                                    <p><strong>Ngày đánh giá:</strong> {{ $review->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Đánh giá:</strong> 
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? ' text-warning' : ' text-muted' }}"></i>
                                        @endfor
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Sản phẩm:</strong> {{ $review->book->title ?? 'Đã xóa' }}</p>
                                    <p><strong>Tác giả:</strong> {{ $review->book->authors->first()->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <strong>Nội dung:</strong>
                                <p class="border p-3 rounded">{{ $review->comment }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Phản hồi của admin -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5>Phản hồi của quản trị viên</h5>
                        </div>
                        <div class="card-body">
                            @if($review->admin_response)
                                <div class="alert alert-light">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Ngày phản hồi:</strong>
                                        <span>{{ $review->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="mb-0">{{ $review->admin_response }}</p>
                                </div>
                            @else
                                <form action="{{ route('admin.reviews.response', $review) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="admin_response">Nội dung phản hồi</label>
                                        <textarea name="admin_response" id="admin_response" 
                                                class="form-control" rows="4" required
                                                placeholder="Nhập nội dung phản hồi..."></textarea>
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Danh sách đánh giá khác của sản phẩm -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5>Đánh giá khác của sản phẩm</h5>
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
                                        @forelse($otherReviews as $otherReview)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $otherReview->user->name ?? 'Khách' }}</td>
                                            <td>{{ Str::limit($otherReview->comment, 50) }}</td>
                                            <td>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $otherReview->rating ? ' text-warning' : ' text-muted' }}"></i>
                                                @endfor
                                            </td>
                                            <td>{{ $otherReview->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Không có đánh giá nào khác</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($otherReviews->hasPages())
                            <div class="card-footer">
                                {{ $otherReviews->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection