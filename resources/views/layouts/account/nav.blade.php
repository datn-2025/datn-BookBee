<div class="account-sidebar-inner">
    <div class="account-user-info">
        @php $user = Auth::user(); @endphp
        <a href="{{ route('account.profile') }}">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=000000&color=FFFFFF&bold=true' }}" alt="User Avatar" class="user-avatar">
        </a>
        <div class="user-name">{{ $user->name }}</div>
        <div class="user-email">{{ $user->email }}</div>
    </div>

    <nav class="account-nav">
        @foreach ([
            ['route' => 'account.profile', 'icon' => 'fas fa-id-card', 'title' => 'Tài khoản của tôi'],
            ['route' => 'account.orders.index', 'icon' => 'fas fa-box-open', 'title' => 'Quản lý đơn hàng'],
            ['route' => 'wallet.index', 'icon' => 'fas fa-wallet', 'title' => 'Ví của tôi'],
            ['route' => 'account.purchase', 'icon' => 'fas fa-star', 'title' => 'Đánh giá sản phẩm'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="nav-link{{ request()->routeIs($item['route'].'*') ? ' active' : '' }}">
                <i class="{{ $item['icon'] }} nav-icon"></i>
                <span>{{ $item['title'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="account-logout">
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="nav-link logout-btn">
                <i class="fas fa-sign-out-alt nav-icon"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
</div>