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
    new Chart(ctxAuth, {
        type: 'pie',
        data: {
            labels: @json($authorStats->pluck('name')),
            datasets: [{
                data: @json($authorStats->pluck('books_count')),
                backgroundColor: ['#3F51B5', '#FF9800', '#4CAF50', '#E91E63', '#00BCD4', '#9C27B0', '#607D8B']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                datalabels: {
                    display: true,
                    color: '#fff',
                    formatter: (value, ctx) => {
                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        if (total === 0) {
                            return '';
                        }
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
