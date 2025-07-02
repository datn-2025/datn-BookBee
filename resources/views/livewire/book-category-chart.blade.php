<div class="card-body">
    <h5 class="card-title text-center">Số Lượng Sách Theo Danh Mục</h5>
    <canvas id="categoryChart" height="180"></canvas>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        Chart.register(ChartDataLabels);

        const ctxCat = document.getElementById('categoryChart').getContext('2d');

        const categoryLabels = @json($categoryStats->pluck('name'));
        const categoryData = @json($categoryStats->pluck('books_count'));

        // Bảng màu phong phú, tránh trùng lặp
        const colors = [
            '#e6194b', '#3cb44b', '#ffe119', '#0082c8', '#f58231',
            '#911eb4', '#46f0f0', '#f032e6', '#d2f53c', '#fabebe',
            '#008080', '#e6beff', '#aa6e28', '#fffac8', '#800000'
        ];

        new Chart(ctxCat, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: colors.slice(0, categoryLabels.length)
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    datalabels: {
                        display: true, // Hiển thị tất cả
                        color: '#fff',
                        formatter: (value, ctx) => {
                            const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${percentage}%`;
                        },
                        anchor: 'center',
                        align: 'center',
                        font: {
                            weight: 'bold',
                            size: 12
                        }
                    },
                }
            }
        });
    </script>
@endpush