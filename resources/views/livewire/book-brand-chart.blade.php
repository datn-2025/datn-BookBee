<div class="card-body">
    <h5 class="card-title text-center">Số Lượng Sách Theo Thương Hiệu</h5>
    <canvas id="brandChart" height="180"></canvas>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    Chart.register(ChartDataLabels);
    const ctxBrand = document.getElementById('brandChart').getContext('2d');
    new Chart(ctxBrand, {
        type: 'pie',
        data: {
            labels: @json($brandStats->pluck('name')),
            datasets: [{
                data: @json($brandStats->pluck('books_count')),
                backgroundColor: ['#FF5722', '#3F51B5', '#4CAF50', '#9C27B0', '#FF9800', '#009688', '#E91E63']
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
