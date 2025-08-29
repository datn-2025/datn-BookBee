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

    <!-- KPIs: Tỉ lệ hoàn tiền & Chọn Ebook -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Tỉ lệ hoàn tiền Ebook</h6>
                            <div class="h3 mb-0">{{ number_format($this->refundRateEbook, 2) }}%</div>
                        </div>
                        <span class="badge bg-warning-subtle text-warning">Theo số đơn</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Tỉ lệ hoàn tiền Sách vật lý</h6>
                            <div class="h3 mb-0">{{ number_format($this->refundRatePhysical, 2) }}%</div>
                        </div>
                        <span class="badge bg-warning-subtle text-warning">Theo số đơn</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Tỉ lệ đơn hàng Ebook</h6>
                            <div class="h3 mb-0">{{ number_format($this->ebookOrderShare, 2) }}%</div>
                        </div>
                        <span class="badge bg-info-subtle text-info">Trên tổng số đơn</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Doanh thu Ebook</h6>
                        <div class="h4 mb-0">{{ number_format($sumEbook, 0, ',', '.') }} ₫</div>
                    </div>
                    <span class="badge bg-orange-100 text-orange-600">Ebook</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Doanh thu Sách vật lý</h6>
                        <div class="h4 mb-0">{{ number_format($sumPhysical, 0, ',', '.') }} ₫</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Sách vật lý</span>
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
        // Lắng nghe dữ liệu mới và vẽ 2 dataset: Sách vật lý vs Ebook
        Livewire.on('refreshChart', ({ chartLabels = [], chartDataPhysical = [], chartDataEbook = [] }) => {
            const ctx = document.getElementById('revenueChart');
            const existingChart = Chart.getChart(ctx);
            if (existingChart) existingChart.destroy();

            const hasData = (
                (chartDataPhysical && chartDataPhysical.some(v => v > 0)) ||
                (chartDataEbook && chartDataEbook.some(v => v > 0))
            );

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
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Sách vật lý',
                            data: chartDataPhysical,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            maxBarThickness: 40
                        },
                        {
                            label: 'Ebook',
                            data: chartDataEbook,
                            backgroundColor: 'rgba(255, 159, 64, 0.5)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1,
                            maxBarThickness: 40
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { beginAtZero: true },
                        x: {
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }
                    }
                },
                plugins: [noDataPlugin]
            });
        });

        Livewire.on('resetDateInputs', () => {
            document.querySelector('input[wire\\:model\\.defer="fromDate"]').value = '';
            document.querySelector('input[wire\\:model\\.defer="toDate"]').value = '';
        });
    </script>
@endpush