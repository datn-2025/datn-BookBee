<div class="row mb-3">
    <!-- Bộ lọc thời gian -->
    <div class="col-12 mb-3 d-flex justify-content-end">
        <select wire:model="timePeriod" wire:change='updateStats' class="form-select w-auto">
            <option value="">Tất cả</option>
            <option value="day">Hôm nay</option>
            <option value="week">Tuần này</option>
            <option value="month">Tháng này</option>
            <option value="quarter">Quý này</option>
        </select>
    </div>

    <!-- Tổng đơn hàng -->
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng đơn hàng</p>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $orderCount }}</h4>
                <a href="{{ route('admin.orders.index') }}" class="text-decoration-underline">Xem tất cả đơn</a>
                <div class="avatar-sm flex-shrink-0 float-end">
                    <span class="avatar-title bg-info-subtle rounded fs-3">
                        <i class="bx bx-shopping-bag text-info"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tổng khách hàng -->
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng khách hàng</p>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $customerCount }}</h4>
                <a href="{{ route('admin.users.index') }}" class="text-decoration-underline">Chi tiết khách hàng</a>
                <div class="avatar-sm flex-shrink-0 float-end">
                    <span class="avatar-title bg-warning-subtle rounded fs-3">
                        <i class="bx bx-user-circle text-warning"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu -->
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Doanh thu</p>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">₫{{ number_format($revenue, 0, ',', '.') }}</h4>
                <a href="{{ route('admin.revenue-report') }}" class="text-decoration-underline">Xem doanh thu</a>
                <div class="avatar-sm flex-shrink-0 float-end">
                    <span class="avatar-title bg-success-subtle rounded fs-3">
                        <i class="bx bx-wallet text-success"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Số dư -->
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Số dư</p>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">₫{{ number_format($balance, 0, ',', '.') }}</h4>
                <a href="{{ route('admin.balance-chart') }}" class="text-decoration-underline">Xem chi tiết</a>
                <div class="avatar-sm flex-shrink-0 float-end">
                    <span class="avatar-title bg-primary-subtle rounded fs-3">
                        <i class="bx bx-credit-card text-primary"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>