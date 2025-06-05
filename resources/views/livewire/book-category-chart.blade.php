<div class="card-body">
    <h5 class="card-title text-center">Số Lượng Sách Theo Danh Mục</h5>
    <canvas id="categoryChart" height="180"></canvas>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'pie',
        data: {
            labels: @json($categoryStats->pluck('name')),
            datasets: [{
                data: @json($categoryStats->pluck('books_count')),
                backgroundColor: ['#1E88E5', '#D32F2F', '#F57C00', '#43A047', '#9C27B0', '#0288D1', '#8E24AA']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
