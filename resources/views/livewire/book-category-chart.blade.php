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

        const hasCategoryData = categoryData.some(value => value > 0);
        const categoryChartData = hasCategoryData
            ? {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: colors.slice(0, categoryLabels.length)
                }]
            }
            : {
                labels: ['Chưa có dữ liệu'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['#e0e0e0']
                }]
            };
        const categoryChartOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                datalabels: {
                    display: hasCategoryData,
                    color: '#fff',
                    formatter: (value, ctx) => {
                        if (!hasCategoryData) return '';
                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        if (total === 0) return '';
const percent = (value / total) * 100;
return percent === 0 ? '' : percent.toFixed(1) + '%';
                    },
                    anchor: 'center',
                    align: 'center',
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            }
        };
        new Chart(ctxCat, {
            type: 'pie',
            data: categoryChartData,
            options: categoryChartOptions,
            plugins: [ChartDataLabels]
        });
    </script>
@endpush