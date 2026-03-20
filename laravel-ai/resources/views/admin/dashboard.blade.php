@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h2>Dashboard</h2>
            <p class="muted">Chào mừng, {{ auth()->user()->name }}. Bạn có thể quản lý users, catalog và phân quyền từ đây.</p>
        </div>
    </div>

    <div class="card">
        <p>Hệ thống admin đã sẵn sàng. Chọn module phù hợp để quản trị dữ liệu.</p>

        <div class="form-actions" style="margin-top: 1rem;">
            @if (auth()->user()->hasPermission('users_view'))
                <a class="button" href="{{ route('admin.users.index') }}">Quản lý users</a>
            @endif

            @if (auth()->user()->hasPermission('categories_view'))
                <a class="button-secondary" href="{{ route('admin.categories.index') }}">Quản lý categories</a>
            @endif

            @if (auth()->user()->hasPermission('products_view'))
                <a class="button-secondary" href="{{ route('admin.products.index') }}">Quản lý products</a>
            @endif

            @if (auth()->user()->hasPermission('roles_view'))
                <a class="button-secondary" href="{{ route('admin.roles.index') }}">Quản lý roles</a>
            @endif
        </div>
    </div>
@endsection
