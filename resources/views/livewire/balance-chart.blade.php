@section('title', 'Biến động số dư')

<div>
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Biến Động Số Dư</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Số dư</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="row align-items-end mb-3">
        <div class="col-md-3">
            <label>Chọn thời gian:</label>
            <select wire:model="timeRange" wire:change="loadData" class="form-select">
                <option value="all">Tất cả</option>
                <option value="day">Hôm nay</option>
                <option value="week">Tuần này</option>
                <option value="month">Tháng này</option>
                <option value="quarter">Quý này</option>
            </select>
        </div>

        @if ($timeRange === 'all')
            <div class="col-md-9 d-flex justify-content-end gap-2 flex-wrap">
                <div>
                    <label>Từ ngày:</label>
                    <input type="date" wire:model="fromDate" class="form-control" />
                </div>
                <div>
                    <label>Đến ngày:</label>
                    <input type="date" wire:model="toDate" class="form-control" />
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <button wire:click="applyCustomFilter" class="btn btn-primary">Áp dụng</button>
                    <button wire:click="resetFilters" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-center">Biến Động Số Dư Theo Thời Gian</h5>
            <canvas id="balanceChart" height="120" wire:ignore></canvas>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Livewire.on('refreshChart', ({ chartLabels = [], chartData = [] }) => {
            const ctx = document.getElementById('balanceChart');
            const existingChart = Chart.getChart(ctx);
            if (existingChart) existingChart.destroy();

            const hasData = chartData && chartData.length > 0 && chartData.some(val => val > 0);

            // Custom plugin để vẽ text khi không có dữ liệu
            const noDataPlugin = {
                id: 'noData',
                afterDraw: (chart) => {
                    if (!hasData) {
                        const { ctx, chartArea } = chart;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = '18px Arial';
                        ctx.fillStyle = '#999';
                        ctx.fillText(
                            'Không có dữ liệu trong khoảng thời gian đã chọn.',
                            (chartArea.left + chartArea.right) / 2,
                            (chartArea.top + chartArea.bottom) / 2
                        );
                        ctx.restore();
                    }
                }
            };

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Biến động số dư',
                        data: chartData,
                        fill: true,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: '#2196f3',
                        borderWidth: 1,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            ticks: {
                                callback: value => '₫' + value.toLocaleString()
                            }
                        }
                    }
                },
                plugins: [noDataPlugin]
            });
        });


    </script>
@endpush
