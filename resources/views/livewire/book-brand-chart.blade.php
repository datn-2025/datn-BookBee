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

const brandLabels = @json($brandStats->pluck('name'));
const brandData = @json($brandStats->pluck('books_count'));

// Bảng màu mở rộng
const brandColors = [
    '#FF5722', '#3F51B5', '#4CAF50', '#9C27B0', '#FF9800',
    '#009688', '#E91E63', '#00BCD4', '#8BC34A', '#FFC107',
    '#FF5252', '#673AB7', '#CDDC39', '#03A9F4', '#FFEB3B'
];

new Chart(ctxBrand, {
    type: 'pie',
    data: {
        labels: brandLabels,
        datasets: [{
            data: brandData,
            backgroundColor: brandColors.slice(0, brandLabels.length)
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
                    return percentage > 2; // Ẩn nhãn nếu quá nhỏ, tùy chỉnh theo ý muốn
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
