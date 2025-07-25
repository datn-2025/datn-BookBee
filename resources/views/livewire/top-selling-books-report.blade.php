<div class="card-body">
    <h5 class="card-title mb-3 fs-5" style="font-weight: 400; color: #2c3e50; letter-spacing: 0.3px;">Sách được săn đón nhiều nhất</h5>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#bestSelling">Sách bán chạy nhất</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#mostFavorite">Sách được yêu thích nhất</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Sách bán chạy nhất -->
        <div class="tab-pane fade show active" id="bestSelling">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sách</th>
                            <th>Đã bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="" class="avatar-xs me-2" />
                                        <span class="fw-medium">{{ $book->title }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info px-3 py-1">
                                        <i class="bi bi-cart-check me-1"></i> {{ $book->total_sold }} lượt
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Không có dữ liệu sách bán chạy.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sách được yêu thích nhất -->
        <div class="tab-pane fade" id="mostFavorite">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sách</th>
                            <th>Lượt yêu thích</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($favoriteBooks as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="" class="avatar-xs me-2" />
                                        <span class="fw-medium">{{ $book->title }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info px-3 py-1">
                                        <i class="bi bi-heart-fill me-1"></i> {{ $book->favorites_count }} lượt
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Không có dữ liệu sách yêu thích.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>