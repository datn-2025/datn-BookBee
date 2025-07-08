<div class="card-body">
    <div class="row">
        <!-- Doanh thu theo tác giả -->
        <div class="col-12 col-md-6 mb-4">
            <h5 class="card-title mb-3 fs-5" style="font-weight: 400; color: #2c3e50; letter-spacing: 0.3px;">
                Doanh thu theo tác giả
            </h5>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tác giả</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($authorRevenue as $author)
                            <tr>
                                <td class="fw-medium">{{ $author['name'] }}</td>
                                 <td>
                                    <span class="badge bg-primary rounded-pill">
                                        ₫{{ number_format($author['revenue'], 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Không có dữ liệu tác giả.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Doanh thu theo nhà xuất bản -->
        <div class="col-12 col-md-6 mb-4">
            <h5 class="card-title mb-3 fs-5" style="font-weight: 400; color: #2c3e50; letter-spacing: 0.3px;">
                Doanh thu theo nhà xuất bản
            </h5>

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nhà xuất bản</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($publisherRevenue as $brand)
                            <tr>
                                <td class="fw-medium">{{ $brand['name'] }}</td>
                                <td>
                                    <span class="badge bg-info rounded-pill">
                                        ₫{{ number_format($brand['revenue'], 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Không có dữ liệu nhà xuất bản.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
