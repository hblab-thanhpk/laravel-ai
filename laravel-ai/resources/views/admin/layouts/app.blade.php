<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .navbar {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-link {
            color: #d7e1ea;
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.is-active {
            background-color: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-user form {
            display: inline;
        }

        .navbar-user button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .navbar-user button:hover {
            background-color: #c0392b;
        }

        .container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            margin-bottom: 0.35rem;
        }

        .muted {
            color: #6c757d;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .button,
        .button-secondary,
        .button-danger,
        .button-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.95rem;
            line-height: 1;
        }

        .button {
            background-color: #1f7a5a;
            color: white;
        }

        .button:hover {
            background-color: #196349;
        }

        .button-secondary {
            background-color: #eef2f6;
            color: #2c3e50;
        }

        .button-secondary:hover {
            background-color: #dce4ec;
        }

        .button-danger {
            background-color: #e74c3c;
            color: white;
        }

        .button-danger:hover {
            background-color: #c0392b;
        }

        .button-link {
            color: #2c3e50;
            background: transparent;
            padding-left: 0;
            padding-right: 0;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .field input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d6dde5;
            border-radius: 4px;
            font-size: 1rem;
        }

        .field textarea {
            width: 100%;
            min-height: 120px;
            padding: 0.75rem;
            border: 1px solid #d6dde5;
            border-radius: 4px;
            font-size: 1rem;
            resize: vertical;
        }

        .field select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d6dde5;
            border-radius: 4px;
            font-size: 1rem;
            background-color: white;
        }

        .field-checkbox {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 1.5rem;
        }

        .field-checkbox input {
            width: auto;
            margin: 0;
        }

        .help-text {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid #edf1f5;
            text-align: left;
            vertical-align: top;
        }

        .data-table th {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .table-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .pagination-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            padding: 0.65rem 0.85rem;
            border-radius: 4px;
            background-color: #eef2f6;
            color: #2c3e50;
            text-decoration: none;
        }

        .pagination-link.is-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            padding: 1rem;
            border-radius: 6px;
            background-color: #f8fafc;
            border: 1px solid #edf1f5;
        }

        .detail-item span {
            display: block;
            color: #6c757d;
            margin-bottom: 0.35rem;
            font-size: 0.9rem;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .checkbox-item {
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
            padding: 0.75rem;
            border: 1px solid #edf1f5;
            border-radius: 6px;
            background-color: #f8fafc;
        }

        .checkbox-item input {
            margin-top: 0.15rem;
        }

        .checkbox-item strong {
            display: block;
            margin-bottom: 0.2rem;
        }

        .inline-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background-color: #edf1f5;
            padding: 0.1rem 0.35rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
        }

        .role-badge.admin {
            background-color: #d9f2e7;
            color: #1b6f53;
        }

        .role-badge.user {
            background-color: #edf1f5;
            color: #4f5f70;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar-left,
            .navbar-links,
            .navbar-user,
            .page-header,
            .toolbar {
                width: 100%;
            }

            .navbar-left,
            .navbar-links,
            .page-header,
            .toolbar,
            .form-actions,
            .pagination {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <a class="navbar-brand" href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login.form') }}">Admin Panel</a>
            @auth
                <div class="navbar-links">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    @if (auth()->user()->hasPermission('users_view'))
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
                    @endif
                    @if (auth()->user()->hasPermission('roles_view'))
                        <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'is-active' : '' }}" href="{{ route('admin.roles.index') }}">Roles</a>
                    @endif
                    @if (auth()->user()->hasPermission('permissions_view'))
                        <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'is-active' : '' }}" href="{{ route('admin.permissions.index') }}">Permissions</a>
                    @endif
                    @if (auth()->user()->hasPermission('categories_view'))
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}" href="{{ route('admin.categories.index') }}">Categories</a>
                    @endif
                    @if (auth()->user()->hasPermission('products_view'))
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
                    @endif
                    @if (auth()->user()->hasPermission('orders_view'))
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}" href="{{ route('admin.orders.index') }}">Orders</a>
                    @endif
                </div>
            @endauth
        </div>
        @auth
            <div class="navbar-user">
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit">Đăng xuất</button>
                </form>
            </div>
        @endauth
    </div>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
