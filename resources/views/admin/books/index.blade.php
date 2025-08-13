@extends('layouts.backend')

@section('title', 'Danh sách sách')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 text-dark fw-semibold">Danh Sách Sách</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.books.index') }}" class="text-muted">Sách</a></li>
                        <li class="breadcrumb-item active">Danh sách</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-dark fw-semibold">
                            <i class="ri-book-line me-2 text-primary"></i>Danh Sách Sách
                        </h5>
                        <div>
                            @permission('book.create')
                            <a href="{{ route('admin.books.create') }}" class="btn btn-primary me-2">
                                <i class="ri-add-line me-1"></i>Thêm sách mới
                            </a>
                            @endpermission
                            @permission('book.trash')
                            <a href="{{ route('admin.books.trash') }}" class="btn btn-outline-danger">
                                <i class="ri-delete-bin-line me-1"></i>Thùng rác
                            </a>
                            @endpermission
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.books.index') }}" method="GET" class="mb-4 border-bottom pb-4">
                        <div class="row g-3">
                            <div class="col-lg-3">
                                <label class="form-label fw-medium">Tìm kiếm</label>
                                <div class="position-relative">
                                    <input type="text" name="search" class="form-control ps-4"
                                        placeholder="Tìm theo tiêu đề hoặc ISBN..." value="{{ request('search') }}">
                                    <i class="ri-search-line position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="row g-3">
                                    <div class="col-sm-4">
                                        <label class="form-label fw-medium">Danh mục</label>
                                        <select name="category" class="form-select">
                                            <option value="">Tất cả danh mục</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label fw-medium">Nhà phát hành</label>
                                        <select name="brand" class="form-select">
                                            <option value="">Tất cả nhà phát hành</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label fw-medium">Tác giả</label>
                                        <select name="author" class="form-select">
                                            <option value="">Tất cả tác giả</option>
                                            @foreach ($authors as $author)
                                                <option value="{{ $author->id }}"
                                                    {{ request('author') == $author->id ? 'selected' : '' }}>
                                                    {{ $author->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label fw-medium">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="Còn Hàng" {{ request('status') == 'Còn Hàng' ? 'selected' : '' }}>
                                        Còn Hàng</option>
                                    <option value="Hết Hàng Tồn Kho"
                                        {{ request('status') == 'Hết Hàng Tồn Kho' ? 'selected' : '' }}>
                                        Hết Hàng Tồn Kho</option>
                                    <option value="Sắp Ra Mắt" {{ request('status') == 'Sắp Ra Mắt' ? 'selected' : '' }}>
                                        Sắp Ra Mắt</option>
                                    <option value="Ngừng Kinh Doanh"
                                        {{ request('status') == 'Ngừng Kinh Doanh' ? 'selected' : '' }}>
                                        Ngừng Kinh Doanh</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label fw-medium">Số trang</label>
                                <div class="d-flex gap-2">
                                    <input type="number" name="min_pages" class="form-control" placeholder="Từ..."
                                        value="{{ request('min_pages') }}">
                                    <input type="number" name="max_pages" class="form-control" placeholder="Đến..."
                                        value="{{ request('max_pages') }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label fw-medium">Khoảng giá</label>
                                <div class="d-flex gap-2">
                                    <input type="number" name="min_price" class="form-control" placeholder="Từ..."
                                        value="{{ request('min_price') }}">
                                    <input type="number" name="max_price" class="form-control" placeholder="Đến..."
                                        value="{{ request('max_price') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-search-2-line me-1"></i>Tìm kiếm
                                </button>
                                @permission('book.view')
                                <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-refresh-line me-1"></i>Đặt lại
                                </a>
                                @endpermission
                            </div>
                        </div>
                    </form>

               
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th class="text-center">ISBN</th>
                                    <th class="text-center fw-semibold" style="min-width: 250px;">Thông tin sách</th>
                                    <th class="text-center fw-semibold">Ảnh bìa</th>
                                    <th class="text-center fw-semibold">Danh Mục</th>
                                    <th class="text-center fw-semibold">Số trang</th>
                                    <th class="text-center fw-semibold" style="min-width: 200px;">Giá các phiên bản</th>
                                    <th class="text-center fw-semibold" style="min-width: 150px;">Biến thể</th>
                                    <th class="text-center fw-semibold">Trạng thái</th>
                                    <th class="text-center fw-semibold" style="width: 100px;">Tùy chọn</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($books as $key => $book)
                                    @php
                                        // Kiểm tra số lượng tồn kho của sách vật lý
                                        $physicalFormat = $book->formats->where('format_name', 'Sách Vật Lý')->first();
                                        $stock = $physicalFormat ? $physicalFormat->stock : null;

                                        // Xác định trạng thái dựa trên tồn kho và trạng thái gốc
                                        if ($stock !== null && $stock == 0) {
                                            $statusText = 'Hết Hàng';
                                            $statusClass = 'badge bg-danger';
                                        } elseif ($stock !== null && $stock > 0 && $stock < 10) {
                                            $statusText = 'Sắp Hết Hàng';
                                            $statusClass = 'badge bg-warning';
                                        } else {
                                            // Giữ nguyên trạng thái gốc cho các trường hợp khác
                                            switch ($book->status) {
                                                case 'Còn Hàng':
                                                    $statusText = 'Còn Hàng';
                                                    $statusClass = 'badge bg-success';
                                                    break;
                                                case 'Hết Hàng Tồn Kho':
                                                    $statusText = 'Hết Hàng Tồn Kho';
                                                    $statusClass = 'badge bg-dark';
                                                    break;
                                                case 'Ngừng Kinh Doanh':
                                                    $statusText = 'Ngừng Kinh Doanh';
                                                    $statusClass = 'badge bg-secondary';
                                                    break;
                                                case 'Sắp Ra Mắt':
                                                    $statusText = 'Sắp Ra Mắt';
                                                    $statusClass = 'badge bg-info';
                                                    break;
                                                default:
                                                    $statusText = $book->status;
                                                    $statusClass = 'badge bg-primary';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $books->firstItem() + $key }}</td>
                                        <td class="text-center">{{ $book->isbn }}</td>
                                        <td>
                                            <div class="text-wrap" style="max-width: 270px;">
                                                <div class="fw-medium mb-1">{{ $book->title }}</div>
                                                <div class="text-muted small">
                                                    <div>Tác giả:
                                                        {{ $book->authors && $book->authors->count() ? $book->authors->pluck('name')->join(', ') : 'N/A' }}
                                                    </div>
                                                    <div>NXB: {{ $book->brand->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($book->cover_image)
                                                <img src="{{ asset('storage/' . $book->cover_image) }}"
                                                    alt="{{ $book->title }}" class="rounded" style="width: 50px; height: 70px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 50px; height: 70px;">
                                                    <i class="ri-image-line text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ $book->category ? $book->category->name : 'Không có danh mục' }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ number_format($book->page_count) }} trang</td>
                                        <td>
                                            @if ($book->formats->isNotEmpty())
                                                @foreach ($book->formats as $format)
                                                    <div class="mb-1">
                                                        <span class="badge bg-primary">{{ $format->format_name }}</span>:
                                                        @if ($format->discount)
                                                            <del class="text-muted">{{ number_format($format->price) }}đ</del>
                                                            <span class="text-danger fw-semibold">
                                                                {{ number_format($format->price - $format->discount) }}đ
                                                            </span>
                                                        @else
                                                            <span class="fw-semibold">{{ number_format($format->price) }}đ</span>
                                                        @endif

                                                        @if (stripos($format->format_name, 'ebook') !== false)
                                                            <span class="badge bg-info">Không giới hạn</span>
                                                        @elseif($format->stock !== null)
                                                            <small class="text-muted">({{ $format->stock }} cuốn)</small>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($book->attributeValues->count() > 0)
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($book->attributeValues->take(3) as $variant)
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge bg-light text-dark" style="font-size: 10px; max-width: 80px;" title="{{ $variant->value ?? 'N/A' }}">
                                                                {{ $variant->value ?? 'N/A' }}
                                                            </span>
                                                            <span class="badge {{ $variant->pivot->stock > 0 ? ($variant->pivot->stock <= 10 ? 'bg-warning' : 'bg-success') : 'bg-danger' }}" style="font-size: 9px;">
                                                                {{ $variant->pivot->stock ?? 0 }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                    @if($book->attributeValues->count() > 3)
                                                        <small class="text-muted">+{{ $book->attributeValues->count() - 3 }} khác</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td class="text-center"><span class="{{ $statusClass }}">{{ $statusText }}</span></td>

                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-line"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @permission('book.show')
                                                        <li><a href="{{ route('admin.books.show', [$book->id, $book->slug]) }}"
                                                                class="dropdown-item">
                                                                <i class="ri-eye-line me-2"></i>Xem chi tiết
                                                            </a></li>
                                                    @endpermission
                                                    @permission('book.edit')
                                                        <li><a href="{{ route('admin.books.edit', [$book->id, $book->slug]) }}"
                                                                class="dropdown-item">
                                                                <i class="ri-pencil-line me-2"></i>Sửa
                                                            </a></li>
                                                    @endpermission
                                                    @permission('book.delete')
                                                        <li>
                                                            <form action="{{ route('admin.books.destroy', $book->id) }}"
                                                                method="post" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger">
                                                                    <i class="ri-delete-bin-line me-2"></i>Xóa
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endpermission
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($books->hasPages())
                        <div class="d-flex justify-content-end mt-4">
                            {!! $books->links('layouts.pagination') !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
