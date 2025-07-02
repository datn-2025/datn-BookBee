<div class="card-body">
    <h5 class="card-title mb-3 fs-5" style="font-weight: 400; color: #2c3e50; letter-spacing: 0.3px;">
        Sách được săn đón nhiều nhất
    </h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 60%;">Sách</th>
                    <th style="width: 40%;">Đã bán</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Bìa sách"
                                        class="rounded border" style="width: 40px; height: 55px; object-fit: cover;" />
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-medium">{{ $book->title }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2">
                                <i class="bi bi-cart-check me-1"></i> {{ $book->total_sold }} lượt
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">Không có dữ liệu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>