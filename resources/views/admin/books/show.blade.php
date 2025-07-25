@extends('layouts.backend')

@section('title', 'Chi tiết sách')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chi Tiết Sách</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.books.index') }}"
                            style="color: inherit;">Sách</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ $book->title }}</h4>
                <div>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-sm">
                        <i class="ri-arrow-left-line"></i> Quay lại
                    </a>
                    <a href="{{ route('admin.books.edit', [$book->id, $book->slug]) }}" class="btn btn-primary btn-sm" style="background-color:#405189; border-color: #405189">
                        <i class="ri-pencil-line"></i> Chỉnh sửa
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Thông tin cơ bản -->
                    <div class="col-md-8">
                        <div class="card border shadow-none mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Tiêu đề:</p>
                                        <h6>{{ $book->title }}</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Slug:</p>
                                        <h6>{{ $book->slug }}</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Tác giả:</p>
                                        <h6>
                                            {{ $book->author ? $book->author->pluck('name')->join(', ') : '' }}
                                        </h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Thương hiệu:</p>
                                        <h6>{{ $book->brand->name }}</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Danh mục:</p>
                                        <h6>{{ $book->category->name }}</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Trạng thái:</p>
                                        <h6>
                                            <span class="badge
                                             @switch($book->status)
                                                @case('Còn Hàng')
                                                    bg-success
                                                    @break
                                                @case('Hết Hàng Tồn Kho')
                                                    bg-danger
                                                    @break
                                                @case('Sắp Ra Mắt')
                                                    bg-warning
                                                    @break
                                                @case('Ngừng Kinh Doanh')
                                                    bg-secondary
                                                    @break
                                            @endswitch">
                                                {{ $book->status  }}
                                            </span>
                                        </h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">ISBN:</p>
                                        <h6>{{ $book->isbn ?? 'N/A' }}</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Ngày xuất bản:</p>
                                        <h6>{{ $book->publication_date ? date('d/m/Y', strtotime($book->publication_date)) : 'N/A' }}</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Số trang:</p>
                                        <h6>{{ $book->page_count ?? 'N/A' }}</h6>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <p class="text-muted mb-1">Ngày tạo:</p>
                                        <h6>{{ date('d/m/Y H:i', strtotime($book->created_at)) }}</h6>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <p class="text-muted mb-1">Mô tả:</p>
                                        <div id="mota" class="border rounded p-3 bg-light" style="max-height: 150px; overflow-y: hidden;">
                                            {!! $book->description ?? 'Không có mô tả' !!}
                                        </div>
                                        <button id="toggleDescription" onclick="toggleDescription()" class="btn btn-sm mt-2 text-white"  style="background-color:#405189; border-color: #405189">Xem thêm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hình ảnh bìa và gallery -->
                    <div class="col-md-4">
                        <div class="card border shadow-none mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Hình ảnh bìa</h5>
                            </div>
                            <div class="card-body text-center">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                        class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                                @else
                                    <div class="border rounded p-3 bg-light text-center">
                                        <i class="ri-image-line ri-3x text-muted"></i>
                                        <p class="mt-2">Không có hình ảnh bìa</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thư viện ảnh -->
                <div class="card border shadow-none mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Thư viện ảnh</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(count($book->images) > 0)
                                @foreach($book->images as $image)
                                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                                        <a href="{{ asset('storage/' . $image->image_url) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $image->image_url) }}" alt="Hình ảnh sách"
                                                class="img-fluid rounded shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        Không có hình ảnh nào trong thư viện.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Định dạng sách -->
                <div class="card border shadow-none mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Định dạng sách</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Định dạng</th>
                                        <th>Giá gốc</th>
                                        <th>Giảm giá</th>
                                        <th>Giá sau giảm</th>
                                        <th>Tồn kho</th>
                                        <th>Tệp</th>
                                        <th>Tệp xem thử</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($book->formats) > 0)
                                        @foreach($book->formats as $format)
                                            <tr>
                                                <td>{{ $format->format_name }}</td>
                                                <td>{{ number_format($format->price, 0, ',', '.') }} đ</td>
                                                <td>
                                                    @if($format->discount)
                                                        {{ $format->discount }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $discountedPrice = $format->price;
                                                        if($format->discount) {
                                                            $discountedPrice = $format->price - ($format->price * $format->discount / 100);
                                                        }
                                                    @endphp
                                                    {{ number_format($discountedPrice, 0, ',', '.') }} đ
                                                </td>
                                                <td>
                                                    @if(in_array($format->format_name, ['Sách Vật Lý', 'Bìa mềm', 'Bìa cứng']))
                                                        {{ $format->stock ?? 0 }}
                                                    @else
                                                        <span class="badge bg-success">Không giới hạn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($format->file_url)
                                                        <a href="{{ asset('storage/' . $format->file_url) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="ri-download-line"></i> Tải xuống
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Không có</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($format->sample_file_url)
                                                        <a href="{{ asset('storage/' . $format->sample_file_url) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                            <i class="ri-eye-line"></i> Xem thử
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Không có</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">Không có định dạng nào được thiết lập.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Thuộc tính sách -->
                <div class="card border shadow-none mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Thuộc tính sách</h5>
                    </div>
                    <div class="card-body">
                        @if(count($attributes) > 0)
                            <div class="row">
                                @foreach($attributes as $attributeName => $values)
                                    <div class="col-md-4 mb-4">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="card-title mb-0">{{ $attributeName }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    @foreach($values as $value)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $value['value'] }}
                                                            @if($value['extra_price'] > 0)
                                                                <span class="badge rounded-pill" style="background-color:#405189; border-color: #405189">
                                                                    +{{ number_format($value['extra_price']) }} đ
                                                                </span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                Sách này không có thuộc tính nào.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Đánh giá sách -->
                <div class="card border shadow-none mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Đánh giá từ người dùng</h5>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="ri-star-fill"></i>
                                        @else
                                            <i class="ri-star-line"></i>
                                        @endif
                                    @endfor
                                </span>
                                <span class="ms-1">{{ number_format($averageRating, 1) }}/5 ({{ $reviewCount }} đánh giá)</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($book->reviews) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Người dùng</th>
                                            <th>Đánh giá</th>
                                            <th>Bình luận</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày đánh giá</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($book->reviews as $review)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div class="avatar-title bg-light text-primary rounded-circle">
                                                                {{ substr($review->user->name, 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $review->user->name }}</h6>
                                                            <small class="text-muted">{{ $review->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-warning">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rating)
                                                                <i class="ri-star-fill"></i>
                                                            @else
                                                                <i class="ri-star-line"></i>
                                                            @endif
                                                        @endfor
                                                    </span>
                                                </td>
                                                <td>{{ $review->comment ?? 'Không có bình luận' }}</td>
                                                <td>
                                                    <span class="badge {{ $review->status === 'approved' ? 'bg-success' : ($review->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $review->status === 'approved' ? 'Đã duyệt' : ($review->status === 'pending' ? 'Chờ duyệt' : 'Từ chối') }}
                                                    </span>
                                                </td>
                                                <td>{{ date('d/m/Y H:i', strtotime($review->created_at)) }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="ri-more-2-fill"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @if($review->status !== 'approved')
                                                                <li><a class="dropdown-item" href="#"><i class="ri-check-line text-success me-1"></i> Duyệt</a></li>
                                                            @endif
                                                            @if($review->status !== 'rejected')
                                                                <li><a class="dropdown-item" href="#"><i class="ri-close-line text-danger me-1"></i> Từ chối</a></li>
                                                            @endif
                                                            <li><a class="dropdown-item" href="#"><i class="ri-delete-bin-line text-danger me-1"></i> Xóa</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert mb-0">
                                Sách này chưa có đánh giá nào.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quà tặng kèm sách -->
                <div class="card border shadow-none mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Quà tặng kèm sách</h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="toggle-gift-list" {{ count($book->gifts) ? 'checked' : '' }}>
                            <label class="form-check-label" for="toggle-gift-list">Hiển thị quà tặng</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" id="gift-list-section" style="display:{{ count($book->gifts) ? '' : 'none' }};">
                            <label class="form-label">Quà tặng kèm sách</label>
                            <ul>
                                @foreach($book->gifts as $gift)
                                    <li>
                                        <strong>{{ $gift->gift_name }}</strong>
                                        @if($gift->gift_description)
                                            - {{ $gift->gift_description }}
                                        @endif
                                        @if($gift->gift_image)
                                            <img src="{{ asset('storage/' . $gift->gift_image) }}" alt="gift" width="32">
                                        @endif
                                        <span class="badge bg-info ms-2">Số lượng: {{ $gift->quantity }}</span>
                                        @if($gift->start_date || $gift->end_date)
                                            <span class="badge bg-secondary ms-2">
                                                @if($gift->start_date) Bắt đầu: {{ date('d/m/Y', strtotime($gift->start_date)) }} @endif
                                                @if($gift->end_date) - Kết thúc: {{ date('d/m/Y', strtotime($gift->end_date)) }} @endif
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('toggle-gift-list').addEventListener('change', function() {
        document.getElementById('gift-list-section').style.display = this.checked ? '' : 'none';
    });
</script>
@endpush
