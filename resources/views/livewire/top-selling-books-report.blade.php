
<div class="card-body">
    <h5 class="card-title mb-3">Top Selling Books</h5>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Book</th>
                    <th>Sold</th>
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
                        <td class="fw-semibold">{{ $book->total_sold }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted text-center">No data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>