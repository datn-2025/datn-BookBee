<div>
    <h4>Báo cáo hiệu quả voucher (chương trình khuyến mãi)</h4>
    <div class="row align-items-end mb-3 justify-content-end">
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
    </div>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Mã voucher</th>
                <th>Thời gian</th>
                <th>Số đơn hàng</th>
                <th>Doanh thu</th>
                <th>Giảm giá</th>
                <th>Tỷ lệ chuyển đổi (%)</th>
            </tr>
        </thead>
        <tbody>
            @if($report->isEmpty())
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Không có dữ liệu/voucher nào trong khoảng thời gian đã chọn.
                    </td>
                </tr>
            @else
                @foreach($report as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['period'] }}</td>
                        <td>{{ $item['orderCount'] }}</td>
                        <td>{{ number_format($item['totalRevenue']) }} đ</td>
                        <td>{{ number_format($item['totalDiscount']) }} đ</td>
                        <td>{{ $item['conversionRate'] }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
