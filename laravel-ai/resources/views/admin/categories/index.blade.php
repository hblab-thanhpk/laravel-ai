@extends('admin.layouts.app')

@section('title', 'Quản lý Categories')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý Categories</h2>
            <p class="muted">Quản trị danh mục sản phẩm theo cấu trúc cha–con.</p>
        </div>

        <a class="button" href="{{ route('admin.categories.create') }}">Tạo category</a>
    </div>

    <div class="card">
        <form action="{{ route('admin.categories.index') }}" class="toolbar" method="GET">
            <div class="field" style="flex: 1 1 260px;">
                <label for="search">Tìm kiếm</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Tìm theo tên hoặc slug">
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                    <option value="active" @selected($filters['status'] === 'active')>Đang hoạt động</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Ngừng hoạt động</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top: 0;">
                <button class="button-secondary" type="submit">Lọc</button>
                @if ($filters['search'] !== '' || $filters['status'] !== 'all')
                    <a class="button-link" href="{{ route('admin.categories.index') }}">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        @if ($categories->count() === 0)
            <div class="empty-state">Chưa có danh mục nào.</div>
        @else
            @if (!$isFiltered)
                <p class="muted" style="margin-bottom: 1rem; font-size: 0.85rem;">
                    Cấu trúc cây — dùng nút <strong>↑ ↓</strong> để thay đổi thứ tự trong cùng cấp.
                </p>
            @endif

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Số SP</th>
                            <th>Trạng thái</th>
                            <th>NSM (lft / rgt)</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>
                                    @if (!$isFiltered)
                                        <span style="display: inline-block; width: {{ $category->depth * 24 }}px;"></span>
                                        @if ($category->depth > 0)
                                            <span class="muted" style="margin-right: 4px;">└</span>
                                        @endif
                                    @endif
                                    <a href="{{ route('admin.categories.show', $category) }}" style="font-weight: 500;">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td><span class="inline-code">{{ $category->slug }}</span></td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <span class="role-badge {{ $category->is_active ? 'admin' : 'user' }}">
                                        {{ $category->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="inline-code" style="font-size: 0.8rem;">{{ $category->_lft }} / {{ $category->_rgt }}</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        @if (!$isFiltered)
                                            <form action="{{ route('admin.categories.move', $category) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="direction" value="up">
                                                <button class="button-secondary" type="submit" title="Di chuyển lên">↑</button>
                                            </form>
                                            <form action="{{ route('admin.categories.move', $category) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="direction" value="down">
                                                <button class="button-secondary" type="submit" title="Di chuyển xuống">↓</button>
                                            </form>
                                        @endif
                                        <a class="button-secondary" href="{{ route('admin.categories.edit', $category) }}">Sửa</a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa category này?');">
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
        @endif
    </div>
@endsection
