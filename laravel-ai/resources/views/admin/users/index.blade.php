@extends('admin.layouts.app')

@section('title', 'Quản lý Users')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý Users</h2>
            <p class="muted">Tạo mới, theo dõi và cập nhật thông tin người dùng trong hệ thống.</p>
        </div>

        <a class="button" href="{{ route('admin.users.create') }}">Tạo user</a>
    </div>

    <div class="card">
        <form action="{{ route('admin.users.index') }}" class="toolbar" method="GET">
            <div class="field" style="flex: 1 1 280px;">
                <label for="search">Tìm kiếm</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Tìm theo tên hoặc email">
            </div>

            <div class="field" style="flex: 1 1 200px;">
                <label for="role_id">Role</label>
                <select id="role_id" name="role_id">
                    <option value="all" @selected($filters['role_id'] === 'all')>Tất cả</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected($filters['role_id'] === $role->id)>{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="flex: 1 1 200px;">
                <label for="sort_by">Sắp xếp theo</label>
                <select id="sort_by" name="sort_by">
                    <option value="created_at" @selected($filters['sort_by'] === 'created_at')>Ngày tạo</option>
                    <option value="updated_at" @selected($filters['sort_by'] === 'updated_at')>Ngày cập nhật</option>
                    <option value="name" @selected($filters['sort_by'] === 'name')>Tên</option>
                    <option value="email" @selected($filters['sort_by'] === 'email')>Email</option>
                </select>
            </div>

            <div class="field" style="flex: 1 1 180px;">
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
                @if ($filters['search'] !== '' || $filters['role_id'] !== 'all' || $filters['sort_by'] !== 'created_at' || $filters['sort_dir'] !== 'desc' || (int) $filters['per_page'] !== 10)
                    <a class="button-link" href="{{ route('admin.users.index') }}">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        @if ($users->count() === 0)
            <div class="empty-state">Chưa có user nào phù hợp với điều kiện hiện tại.</div>
        @else
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->role)
                                        <span class="role-badge {{ $user->role->name === 'admin' ? 'admin' : 'user' }}">
                                            {{ $user->role->display_name }}
                                        </span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button-secondary" href="{{ route('admin.users.show', $user) }}">Xem</a>
                                        <a class="button-secondary" href="{{ route('admin.users.edit', $user) }}">Sửa</a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa user này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button-danger" type="submit">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="pagination">
                    <span class="muted">
                        Hiển thị {{ $users->firstItem() }} - {{ $users->lastItem() }} trên tổng {{ $users->total() }} users
                    </span>

                    <div class="pagination-links">
                        @if ($users->onFirstPage())
                            <span class="pagination-link is-disabled">Trước</span>
                        @else
                            <a class="pagination-link" href="{{ $users->previousPageUrl() }}">Trước</a>
                        @endif

                        <span class="pagination-link">{{ $users->currentPage() }}/{{ $users->lastPage() }}</span>

                        @if ($users->hasMorePages())
                            <a class="pagination-link" href="{{ $users->nextPageUrl() }}">Sau</a>
                        @else
                            <span class="pagination-link is-disabled">Sau</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection