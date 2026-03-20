@extends('admin.layouts.app')

@section('title', 'Quản lý Roles')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý Roles</h2>
            <p class="muted">Quản trị vai trò và gán permission cho từng vai trò.</p>
        </div>

        <a class="button" href="{{ route('admin.roles.create') }}">Tạo role</a>
    </div>

    <div class="card">
        <form action="{{ route('admin.roles.index') }}" class="toolbar" method="GET">
            <div class="field" style="flex: 1 1 280px;">
                <label for="search">Tìm kiếm</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Tìm theo tên định danh hoặc tên hiển thị">
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="sort_by">Sắp xếp theo</label>
                <select id="sort_by" name="sort_by">
                    <option value="created_at" @selected($filters['sort_by'] === 'created_at')>Ngày tạo</option>
                    <option value="updated_at" @selected($filters['sort_by'] === 'updated_at')>Ngày cập nhật</option>
                    <option value="display_name" @selected($filters['sort_by'] === 'display_name')>Tên hiển thị</option>
                    <option value="name" @selected($filters['sort_by'] === 'name')>Tên định danh</option>
                </select>
            </div>

            <div class="field" style="flex: 1 1 160px;">
                <label for="sort_dir">Chiều sắp xếp</label>
                <select id="sort_dir" name="sort_dir">
                    <option value="desc" @selected($filters['sort_dir'] === 'desc')>Giảm dần</option>
                    <option value="asc" @selected($filters['sort_dir'] === 'asc')>Tăng dần</option>
                </select>
            </div>

            <div class="field" style="flex: 1 1 140px;">
                <label for="per_page">Số dòng/trang</label>
                <select id="per_page" name="per_page">
                    @foreach ([10, 25, 50, 100] as $perPage)
                        <option value="{{ $perPage }}" @selected((int) $filters['per_page'] === $perPage)>{{ $perPage }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions" style="margin-top: 0;">
                <button class="button-secondary" type="submit">Lọc</button>
                @if ($filters['search'] !== '' || $filters['sort_by'] !== 'created_at' || $filters['sort_dir'] !== 'desc' || (int) $filters['per_page'] !== 10)
                    <a class="button-link" href="{{ route('admin.roles.index') }}">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        @if ($roles->count() === 0)
            <div class="empty-state">Chưa có role nào.</div>
        @else
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên hiển thị</th>
                            <th>Tên định danh</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Loại</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->display_name }}</td>
                                <td><span class="inline-code">{{ $role->name }}</span></td>
                                <td>{{ $role->users_count }}</td>
                                <td>{{ $role->permissions_count }}</td>
                                <td>
                                    <span class="role-badge {{ $role->is_system ? 'admin' : 'user' }}">
                                        {{ $role->is_system ? 'System' : 'Custom' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button-secondary" href="{{ route('admin.roles.show', $role) }}">Xem</a>
                                        <a class="button-secondary" href="{{ route('admin.roles.edit', $role) }}">Sửa</a>
                                        @if (! $role->is_system)
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa role này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button-danger" type="submit">Xóa</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="pagination">
                    <span class="muted">Hiển thị {{ $roles->firstItem() }} - {{ $roles->lastItem() }} trên tổng {{ $roles->total() }} roles</span>

                    <div class="pagination-links">
                        @if ($roles->onFirstPage())
                            <span class="pagination-link is-disabled">Trước</span>
                        @else
                            <a class="pagination-link" href="{{ $roles->previousPageUrl() }}">Trước</a>
                        @endif

                        <span class="pagination-link">{{ $roles->currentPage() }}/{{ $roles->lastPage() }}</span>

                        @if ($roles->hasMorePages())
                            <a class="pagination-link" href="{{ $roles->nextPageUrl() }}">Sau</a>
                        @else
                            <span class="pagination-link is-disabled">Sau</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
