@extends('layouts.backend')

@section('title', 'Danh sách sách')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Danh Sách Sách</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.books.index') }}"
                            style="color: inherit;">sách</a></li>
                    <li class="breadcrumb-item active">Danh sách</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Danh Sách Sách</h4>
                    <div>
                        <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm me-2" style="background-color:#405189; border-color: #405189">
                            <i class="ri-add-line me-1"></i> Thêm sách mới
                        </a>
                        <a href="{{ route('admin.books.trash') }}" class="btn btn-outline-danger btn-sm">
                            <i class="ri-delete-bin-line me-1"></i> Thùng rác
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.books.index') }}" method="GET" class="mb-4 border-bottom pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-lg-3">
                            <div class="search-box">
                                <input type="text" name="search" class="form-control search"
                                    placeholder="Tìm theo tiêu đề hoặc ISBN..." value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <select name="category" class="form-select">
                                        <option value="">Tất cả danh mục</option>
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category')==$category->id ?
                                            'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select name="brand" class="form-select">
                                        <option value="">Tất cả nhà phát hành</option>
                                        @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ request('brand')==$brand->id ? 'selected' :
                                            '' }}>
                                            {{ $brand->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select name="author" class="form-select">
                                        <option value="">Tất cả tác giả</option>
                                        @foreach ($authors as $author)
                                        <option value="{{ $author->id }}" {{ request('author')==$author->id ? 'selected'
                                            : '' }}>
                                            {{ $author->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Còn Hàng" {{ request('status') == 'Còn Hàng' ? 'selected' : '' }}>
                                    Còn Hàng</option>
                                <option value="Hết Hàng Tồn Kho" {{ request('status') == 'Hết Hàng Tồn Kho' ? 'selected' : '' }}>
                                    Hết Hàng Tồn Kho</option>
                                <option value="Sắp Ra Mắt" {{ request('status') == 'Sắp Ra Mắt' ? 'selected' : '' }}>
                                    Sắp Ra Mắt</option>
                                <option value="Ngừng Kinh Doanh" {{ request('status') == 'Ngừng Kinh Doanh' ? 'selected' : '' }}>
                                    Ngừng Kinh Doanh</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Số trang</span>
                                <input type="number" name="min_pages" class="form-control" placeholder="Từ..."
                                    value="{{ request('min_pages') }}">
                                <input type="number" name="max_pages" class="form-control" placeholder="Đến..."
                                    value="{{ request('max_pages') }}">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text">Khoảng giá</span>
                                <input type="number" name="min_price" class="form-control" placeholder="Từ..."
                                    value="{{ request('min_price') }}">
                                <input type="number" name="max_price" class="form-control" placeholder="Đến..."
                                    value="{{ request('max_price') }}">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="btn btn-primary me-2" style="background-color:#405189; border-color: #405189">
                                <i class="ri-search-2-line"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('admin.books.index') }}" class="btn btn-light me-2">
                                <i class="ri-refresh-line"></i> Đặt lại
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th class="text-center">ISBN</th>
                                <th class="text-center" style="min-width: 250px;">Thông tin sách</th>
                                <th class="text-center">Ảnh bìa</th>
                                <th class="text-center">Danh Mục</th>
                                <th class="text-center">Số trang</th>
                                <th class="text-center" style="min-width: 200px;">Giá các phiên bản</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center" style="width: 100px;">Tùy chọn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($books as $key => $book)                            @php
                                // Get status badge class based on book status
                                switch($book->status) {
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
                                        $statusClass = 'badge bg-warning';
                                        break;
                                    default:
                                        $statusText = $book->status;
                                        $statusClass = 'badge bg-danger';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $books->firstItem() + $key }}</td>
                                <td class="text-center">{{ $book->isbn }}</td>
                                <td>
                                    <div class="text-wrap" style="max-width: 270px;">
                                        <div class="fw-medium mb-1">{{ $book->title }}</div>
                                        <div class="text-muted small">
                                           <div>Tác giả: {{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}</div>
                                            <div>NXB: {{ $book->brand->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                        class="avatar-sm rounded-circle">
                                    @else
                                    <div class="avatar-sm">
                                        <i class="ri-image-line"></i> Không có ảnh
                                    </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $book->category ? $book->category->name : 'Không có danh mục' }}
                                </td>
                                <td>{{ number_format($book->page_count) }} trang</td>
                                <td>
                                    @if($book->formats->isNotEmpty())
                                    @foreach($book->formats as $format)
                                    <div class="text">
                                        {{ $format->format_name }}:
                                        @if($format->discount)
                                        <del>{{ number_format($format->price) }}đ</del>
                                        <span class="text-danger">
                                            {{ number_format($format->price * (1 - $format->discount/100)) }}đ
                                        </span>
                                        @else
                                        {{ number_format($format->price) }}đ
                                        @endif
                                        
                                        @if(stripos($format->format_name, 'ebook') !== false)
                                            <span class="badge bg-info">Không giới hạn</span>
                                        @elseif($format->stock !== null)
                                            ({{ $format->stock }} cuốn)
                                        @endif
                                    </div>
                                    @endforeach
                                    @endif
                                </td>
                                <td><span class="{{ $statusClass }}">{{ $statusText }}</span></td>

                                <td class="text-center">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="{{ route('admin.books.show', [$book->id, $book->slug]) }}"
                                                    class="dropdown-item">
                                                    <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Xem chi
                                                    tiết
                                                </a></li>
                                            <li><a href="{{ route('admin.books.edit', [$book->id, $book->slug]) }}"
                                                    class="dropdown-item">
                                                    <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Sửa
                                                </a></li>
                                            <li>
                                                <form action="{{ route('admin.books.destroy', $book->id) }}"
                                                    method="post" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger delete-item">
                                                        <i class="ri-delete-bin-fill align-bottom me-2"></i> Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {!! $books->links('layouts.pagination') !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
