@extends('layouts.app')

@section('content')
<style>
    :root {
        --account-bg-color: #f4f4f4;
        --sidebar-bg-color: #ffffff;
        --border-color: #e5e7eb; /* A light grey for subtle borders */
        --text-primary: #000000;
        --text-secondary: #666666;
        --accent-color: #000000;
        --accent-text-color: #ffffff;
        --danger-color: #d9534f;
    }

    body {
        background-color: var(--account-bg-color);
        font-family: 'Roboto', sans-serif;
    }

    .account-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .account-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
    }

    .account-sidebar {
        background-color: var(--sidebar-bg-color);
        height: fit-content;
        position: sticky;
        top: 2rem;
        border-radius: 0.75rem; /* Rounded corners */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* Soft shadow instead of border */
    }

    .account-sidebar-inner {
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
    }

    .account-user-info {
        text-align: center;
        border-bottom: 1px solid var(--border-color); /* Lighter border color */
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1rem;
        border: 2px solid var(--border-color); /* Lighter border for avatar */
    }

    .user-name {
        font-weight: 900;
        color: var(--text-primary);
        font-size: 1.25rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .user-email {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .account-nav {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.8rem 1rem;
        text-decoration: none;
        color: var(--text-primary);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.875rem;
        transition: background-color 0.2s, color 0.2s;
        border-radius: 0.5rem; /* Rounded links */
    }

    .nav-link:hover {
        background-color: #f3f4f6;
    }

    .nav-link.active {
        background-color: var(--accent-color);
        color: var(--accent-text-color);
    }

    .nav-link.active .nav-icon {
        color: var(--accent-text-color);
    }

    .nav-icon {
        width: 20px;
        text-align: center;
        margin-right: 1rem;
        font-size: 1rem;
        transition: color 0.2s;
    }

    .account-logout {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color); /* Lighter border color */
    }
    
    .logout-form { margin: 0; }

    .logout-btn {
        width: 100%;
        justify-content: flex-start;
        color: var(--danger-color);
    }

    .logout-btn:hover {
        background-color: #fef2f2;
    }
    
    .logout-btn .nav-icon { color: var(--danger-color); }

    .main-content {
        background-color: var(--sidebar-bg-color);
        padding: 2.5rem;
        border-radius: 0.75rem; /* Rounded corners */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* Soft shadow instead of border */
    }

    /* Responsive */
    @media (max-width: 991px) {
        .account-grid {
            grid-template-columns: 240px 1fr;
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .account-grid {
            grid-template-columns: 1fr;
        }
        .account-sidebar {
            position: static;
            margin-bottom: 1.5rem;
        }
        .main-content {
            padding: 1.5rem;
        }
    }
</style>

<div class="account-container">
    <div class="account-grid">
        <aside class="account-sidebar">
            @include('layouts.account.nav')
        </aside>
        <main class="main-content">
            @yield('account_content')
        </main>
    </div>
</div>
@endsection