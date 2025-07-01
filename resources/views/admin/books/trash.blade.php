@extends('layouts.backend')

@section('title', 'Thùng rác - Sách')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Thùng rác - Sách</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.books.index') }}">Sách</a></li>
                        <li class="breadcrumb-item active">Thùng rác</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Danh sách sách đã xóa</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-primary btn-sm">
                                <i class="ri-book-line me-1"></i> Quay lại danh sách sách
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <form action="{{ route('admin.books.trash') }}" method="GET" class="row g-3">
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên hoặc ISBN..." value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($trashedBooks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 50px">STT</th>
                                    <th style="width: 80px">Ảnh</th>
                                    <th>Tên sách</th>
                                    <th>Danh mục</th>
                                    <th>Tác giả</th>
                                    <th>Ngày xóa</th>
                                    <th style="width: 150px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trashedBooks as $index => $book)
                                <tr>
                                    <td>{{ $trashedBooks->firstItem() + $index }}</td>
                                    <td>
                                        @if($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="img-thumbnail" style="width: 60px; height: 80px; object-fit: cover;">
                                        @else
                                        <div class="bg-light text-center p-2 rounded">
                                            <i class="ri-image-line text-muted"></i>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $book->title }}</h6>
                                        <small class="text-muted">ISBN: {{ $book->isbn ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $book->category->name ?? 'N/A' }}</td>
                                    <td>{{ $book->author && $book->author->count() ? $book->author->pluck('name')->join(', ') : 'N/A' }}</td>
                                    <td>{{ $book->deleted_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.books.restore', $book->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Khôi phục">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.books.force-delete', $book->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Xóa vĩnh viễn" onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn sách này? Hành động này không thể hoàn tác!')">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        {{ $trashedBooks->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <img src="{{ asset('assets/images/empty-box.png') }}" alt="Empty" class="img-fluid" style="max-width: 150px;">
                        <h5 class="mt-4">Thùng rác trống</h5>
                        <p class="text-muted">Không có sách nào trong thùng rác</p>
                        <a href="{{ route('admin.books.index') }}" class="btn btn-primary mt-2">
                            <i class="ri-arrow-left-line me-1"></i> Quay lại danh sách sách
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
