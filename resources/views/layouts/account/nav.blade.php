<div class="sidebar-nav-outer">
    <div class="sidebar-profile">
        @php $user = Auth::user(); @endphp
        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=fff&color=111' }}" alt="User Avatar" class="sidebar-profile-avatar">
        <div class="sidebar-profile-info text-center">
            <div class="sidebar-profile-name font-semibold text-xl">{{ $user->name }}</div>
            <div class="sidebar-profile-email text-sm text-gray-600">{{ $user->email }}</div>
        </div>
    </div>

    <nav class="sidebar-nav-list">
        @foreach ([
            ['route' => 'account.profile', 'icon' => 'fas fa-user', 'title' => 'Thông tin cá nhân'],
            ['route' => 'account.orders.index', 'icon' => 'fas fa-shopping-bag', 'title' => 'Đơn hàng'],
            ['route' => 'account.purchase', 'icon' => 'fas fa-star', 'title' => 'Đánh giá'],
            ['route' => 'home', 'icon' => 'fas fa-home', 'title' => 'Trang chủ'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="sidebar-link{{ request()->routeIs($item['route']) ? ' active' : '' }}">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['title'] }}</span>
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="mt-auto">
        @csrf
        <button type="submit" class="sidebar-logout-btn">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </button>
    </form>
</div>

<style>
    .sidebar-nav-outer {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #fff;
    }

    .sidebar-profile {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 26px 8px 14px;
        border-bottom: 1px solid #111;
    }

    .sidebar-profile-avatar {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 1px solid #111;
        margin-bottom: 12px;
        transition: border-color 0.18s;
    }

    .sidebar-profile-avatar:hover {
        border-color: #333;
    }

    .sidebar-profile-info {
        text-align: center;
    }

    .sidebar-profile-name {
        font-weight: 800;
        color: #111;
        font-size: 1.08rem;
        margin-bottom: 2px;
    }

    .sidebar-profile-email {
        color: #222;
        font-size: 0.98rem;
        font-weight: 600;
        opacity: 0.92;
    }

    .sidebar-nav-list {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 18px;
        color: #111;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.01rem;
        border: 1px solid #e5e5e5;
        background: #fff;
        transition: background 0.15s, color 0.15s, transform 0.2s;
    }

    .sidebar-link:hover, .sidebar-link.active {
        background: #111;
        color: #fff;
        transform: none;
    }

    .sidebar-link i {
        font-size: 1.08rem;
        color: #111;
        transition: color 0.2s, transform 0.2s;
    }

    .sidebar-link:hover i, .sidebar-link.active i {
        color: #fff;
        transform: scale(1.1);
    }

    .sidebar-logout-btn {
        width: 100%;
        text-align: left;
        color: red !important;
        background: none;
        border: none;
        padding: 12px 18px;
        font-weight: 700;
        font-size: 1.01rem;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        border-top: 1px solid #111;
        transition: background 0.3s;
    }

    .sidebar-logout-btn:hover {
        background: #ffa5a5;
        color: red !important;
    }

    /* Media Queries */
    @media (max-width: 1100px) {
        .sidebar-profile {
            padding: 16px 2px 8px;
        }
        .sidebar-profile-avatar {
            width: 60px;
            height: 60px;
        }
        .sidebar-link, .sidebar-logout-btn {
            padding: 10px 6px;
            font-size: 0.95rem;
        }
    }

    @media (max-width: 700px) {
        .sidebar-nav-list {
            flex-direction: row;
            gap: 0;
            overflow-x: auto;
            border-bottom: 1px solid #111;
            margin-top: 0;
        }

        .sidebar-link {
            border-left: none;
            border-bottom: 2px solid transparent;
            padding: 10px 12px;
            font-size: 0.95rem;
        }

        .sidebar-link.active, .sidebar-link:hover {
            background: #111;
            color: #fff;
            border-bottom: 1px solid #fff;
        }

        .sidebar-profile {
            padding: 10px 0 4px;
        }
    }
</style>

