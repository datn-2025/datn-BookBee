<div class="max-w-7xl mx-auto px-4 mt-6">
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 md:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <span class="text-2xl">💡</span>
                <h3 class="text-lg md:text-xl font-bold text-amber-800 uppercase tracking-wide">Thông tin nổi bật</h3>
            </div>
            {{-- <span class="text-xs text-amber-700">Tự động từ dữ liệu (demo)</span> --}}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-amber-100">
                <span class="text-xl">{{ $insights['revenue']['icon'] }}</span>
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ $insights['revenue']['title'] }}</div>
                    <div class="text-sm text-gray-600">{{ $insights['revenue']['text'] }}</div>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-amber-100">
                <span class="text-xl">{{ $insights['hot_category']['icon'] }}</span>
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ $insights['hot_category']['title'] }}</div>
                    <div class="text-sm text-gray-600">{{ $insights['hot_category']['text'] }}</div>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-amber-100">
                <span class="text-xl">{{ $insights['vip']['icon'] }}</span>
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ $insights['vip']['title'] }}</div>
                    <div class="text-sm text-gray-600">{{ $insights['vip']['text'] }}</div>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-amber-100">
                <span class="text-xl">{{ $insights['warning']['icon'] }}</span>
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ $insights['warning']['title'] }}</div>
                    <div class="text-sm text-gray-600">{{ $insights['warning']['text'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
