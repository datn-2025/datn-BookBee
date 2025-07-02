<div class="card-body">
    <h5 class="card-title text-center">Số Lượng Sách Theo Tác Giả</h5>
    <canvas id="authorChart" height="180"></canvas>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
Chart.register(ChartDataLabels);

const ctxAuth = document.getElementById('authorChart').getContext('2d');

const authorLabels = @json($authorStats->pluck('name'));
const authorData = @json($authorStats->pluck('books_count'));

const authorColors = [
    '#3F51B5', '#FF9800', '#4CAF50', '#E91E63', '#00BCD4',
    '#9C27B0', '#607D8B', '#FF5722', '#CDDC39', '#FFC107',
    '#009688', '#673AB7', '#8BC34A', '#03A9F4', '#FF5252'
];

new Chart(ctxAuth, {
    type: 'pie',
    data: {
        labels: authorLabels,
        datasets: [{
            data: authorData,
            backgroundColor: authorColors.slice(0, authorLabels.length)
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            datalabels: {
                display: (ctx) => {
                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    const value = ctx.dataset.data[ctx.dataIndex];
                    const percentage = (value / total) * 100;
                    return percentage > 2; // Ẩn phần trăm nếu nhỏ hơn 2%
                },
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
            }
        }
    }
});
</script>
@endpush
