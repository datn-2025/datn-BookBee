<div class="card-body">
     <h5 class="card-title text-center">Xu hướng bán sách theo định dạng (theo tháng)</h5>
     <canvas id="bookTypeLineChart" height="120"></canvas>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const chartLabels = @json($labels);
    // Inject barPercentage and categoryPercentage into each dataset
    let chartDatasets = @json($datasets);
    chartDatasets = chartDatasets.map(ds => ({
        ...ds,
        barPercentage: 0.2,
        categoryPercentage: 0.5
    }));
    const ctx = document.getElementById('bookTypeLineChart').getContext('2d');
    Chart.register(ChartDataLabels);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: chartDatasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                datalabels: {
                    display: true,
                    color: '#b0b0b0', 
                    anchor: 'center', 
                    align: 'center',  
                    font: {
                        family: 'Segoe UI, sans-serif',
                        weight: 'normal',
                        size: 12
                    },
                    formatter: function(value, context) {
                        return value;
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Số lượng sách đã bán' }
                },
                // x: {
                //     title: { display: true, text: 'Tháng' ,  align: 'start' }
                // }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>
@endpush
</div>
