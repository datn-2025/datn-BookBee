@section('title', 'Báo cáo doanh thu')

<div>
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Báo cáo doanh thu</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Báo cáo</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc và biểu đồ -->
    <div class="row align-items-end mb-3">
        <!-- Cột trái: dropdown -->
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

        <!-- Cột phải: bộ lọc ngày + nút -->
        @if ($timeRange === 'all')
            <div class="col-md-9 d-flex justify-content-end gap-2 flex-wrap">
                <div>
                    <label>Từ ngày:</label>
                    <input type="date" wire:model.defer="fromDate" class="form-control" />
                </div>
                <div>
                    <label>Đến ngày:</label>
                    <input type="date" wire:model.defer="toDate" class="form-control" />
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <button wire:click="applyCustomFilter" class="btn btn-primary">Áp dụng</button>
                    <button wire:click="resetFilters" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        @endif
    </div>





    <canvas id="revenueChart" wire:ignore></canvas>
</div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Livewire.on('refreshChart', ({ chartLabels = [], chartData = [] }) => {
            const ctx = document.getElementById('revenueChart');
            const existingChart = Chart.getChart(ctx);
            if (existingChart) existingChart.destroy();

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Doanh thu',
                        data: chartData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true },
                        x: {
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }
                    }
                }
            });
        });

        Livewire.on('resetDateInputs', () => {
            document.querySelector('input[wire\\:model\\.defer="fromDate"]').value = '';
            document.querySelector('input[wire\\:model\\.defer="toDate"]').value = '';
        });
    </script>
@endpush