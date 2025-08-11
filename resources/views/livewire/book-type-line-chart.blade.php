<div class="card-body">
     <h5 class="card-title text-center mb-3">Xu hướng bán sách theo định dạng (theo tháng)</h5>
     <div style="position: relative; height: 500px; width: 100%;">
         <canvas id="bookTypeLineChart"></canvas>
     </div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const chartLabels = @json($labels);
    let chartDatasets = @json($datasets);
    chartDatasets = chartDatasets.map(ds => ({
        ...ds,
        barPercentage: 0.9,
        categoryPercentage: 0.95,
        borderRadius: 2,
        borderSkipped: false,
        datalabels: {
            display: function(context) {
                return context.dataset.data[context.dataIndex] > 0;
            }
        }
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
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    right: 15,
                    bottom: 5,
                    left: 10
                }
            },
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                },
                datalabels: {
                    color: '#666',
                    anchor: 'center',
                    align: 'top',
                    offset: 2,
                    font: {
                        family: 'Segoe UI, sans-serif',
                        weight: 'normal',
                        size: 12
                    },
                    formatter: function(value) {
                        return value > 0 ? value : '';
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        drawBorder: false
                    },
                    title: { 
                        display: true, 
                        text: 'Số lượng sách đã bán',
                        padding: {top: 0, bottom: 10}
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        padding: 3,
                        autoSkip: false,
                        font: {
                            size: 12
                        }
                    },
                    offset: true
                }
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
