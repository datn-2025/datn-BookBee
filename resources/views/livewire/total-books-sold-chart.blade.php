<div class="chart-container" style="position: relative; height: 450px;">
    <h4>Tổng số sách đã bán: {{ $totalBooksSold }}</h4>
    <canvas id="booksSoldChart"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const ctx = document.getElementById('booksSoldChart').getContext('2d');

            const colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#C9CBCF', '#64B5F6',
                '#EF5350', '#66BB6A'
            ];

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($chartData)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($chartData)) !!},
                        backgroundColor: colors,
                        borderColor: colors,
                        borderWidth: 1,
                        barPercentage: 0.2,
                        categoryPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                generateLabels: (chart) => {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => ({
                                            text: label,
                                            fillStyle: colors[i % colors.length],
                                            strokeStyle: colors[i % colors.length],
                                            lineWidth: 1,
                                            hidden: false,
                                            index: i
                                        }));
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.label}: ${context.raw}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>
