<div class="card-body">
    <!-- Revenue by Author -->
    <h5 class="card-title mb-3">Revenue by Author</h5>
    <ul class="list-group list-group-flush mb-4">
        @forelse ($authorRevenue as $author)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $author['name'] }}
                <span class="badge bg-primary rounded-pill">₫{{ number_format($author['revenue'], 0, ',', '.') }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted text-center">No author data.</li>
        @endforelse
    </ul>

    <!-- Revenue by Publisher -->
    <h5 class="card-title mb-3">Revenue by Publisher</h5>
    <ul class="list-group list-group-flush">
        @forelse ($publisherRevenue as $brand)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $brand['name'] }}
                <span class="badge bg-info rounded-pill">₫{{ number_format($brand['revenue'], 0, ',', '.') }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted text-center">No publisher data.</li>
        @endforelse
    </ul>
</div>
