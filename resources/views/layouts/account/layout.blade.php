@extends('layouts.app')

@section('content')
<style>
    body {
        background: #fff;
        color: #111;
        font-family: 'Inter', sans-serif;
    }

    .account-container {
        padding: 32px 0 40px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .account-flex {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
    }

    .sidebar {
        background: #fff;
        border: 1px solid #111;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        flex: 0 0 auto;
        min-width: 200px;
        max-width: 260px;
        height: 100%;
    }

    .main-content {
        flex: 1;
        padding: 0;
    }

    /* Media Queries */
    @media (max-width: 1100px) {
        .account-container {
            max-width: 98vw;
            padding: 0 8px;
        }

        .account-flex {
            gap: 18px;
        }

        .sidebar {
            min-width: 160px;
            max-width: 200px;
        }
    }

    @media (max-width: 991px) {
        .account-container {
            max-width: 100vw;
        }

        .account-flex {
            gap: 8px;
        }

        .sidebar {
            min-width: 120px;
            max-width: 140px;
        }
    }

    @media (max-width: 700px) {
        .account-flex {
            flex-direction: column;
            gap: 0;
        }

        .sidebar {
            min-width: 100%;
            margin-bottom: 18px;
        }
    }
</style>

<div class="account-container">
    <div class="account-flex">
        <div class="sidebar">
            @include('layouts.account.nav')
        </div>
        <div class="main-content">
            @yield('account_content')
        </div>
    </div>
</div>
@endsection
