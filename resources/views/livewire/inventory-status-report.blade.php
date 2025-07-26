<div class="card-body">
    <h5 class="card-title mb-3 fs-5" style="font-weight: 400; color: #2c3e50; letter-spacing: 0.3px;">Quản lý tồn kho</h5>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#lowStock">Sách tồn kho thấp </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#outOfStock">Sách hết hàng </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Tồn kho thấp -->
        <div class="tab-pane fade show active" id="lowStock">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sách</th>
                            <th>Tổng tồn kho</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockBooks as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="" class="avatar-xs me-2" />
                                        <span class="fw-medium">{{ $book->title }}</span>
                                    </div>
                                </td>
                                <td>{{ $book->total_stock }} cuốn</td>
                                <td>
                                    <span class="badge 
                                            @if($book->status == 'Còn Hàng') bg-success-subtle text-success
                                            @elseif($book->status == 'Sắp Ra Mắt') bg-warning-subtle text-warning
                                            @elseif($book->status == 'Hết Hàng Tồn Kho') bg-danger-subtle text-danger
                                            @else bg-secondary-subtle text-secondary @endif">
                                        {{ $book->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Không có sách tồn kho thấp.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hết hàng -->
        <div class="tab-pane fade" id="outOfStock">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sách</th>
                            <th>Tổng tồn kho</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($outOfStockBooks as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="" class="avatar-xs me-2" />
                                        <span class="fw-medium">{{ $book->title }}</span>
                                    </div>
                                </td>
                                <td>0 cuốn</td>
                                <td>
                                    <span class="badge 
                                            @if($book->status == 'Còn Hàng') bg-success-subtle text-success
                                            @elseif($book->status == 'Sắp Ra Mắt') bg-warning-subtle text-warning
                                            @elseif($book->status == 'Hết Hàng Tồn Kho') bg-danger-subtle text-danger
                                            @else bg-secondary-subtle text-secondary @endif">
                                        {{ $book->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Không có sách hết hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
