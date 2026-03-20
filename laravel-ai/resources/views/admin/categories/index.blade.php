@extends('admin.layouts.app')

@section('title', 'Quản lý Categories')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý Categories</h2>
            <p class="muted">Quản trị danh mục sản phẩm trong hệ thống.</p>
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

            <div class="field" style="flex: 1 1 180px;">
                <label for="sort_by">Sắp xếp theo</label>
                <select id="sort_by" name="sort_by">
                    <option value="created_at" @selected($filters['sort_by'] === 'created_at')>Ngày tạo</option>
                    <option value="updated_at" @selected($filters['sort_by'] === 'updated_at')>Ngày cập nhật</option>
                    <option value="name" @selected($filters['sort_by'] === 'name')>Tên</option>
                    <option value="slug" @selected($filters['sort_by'] === 'slug')>Slug</option>
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
                @if ($filters['search'] !== '' || $filters['status'] !== 'all' || $filters['sort_by'] !== 'created_at' || $filters['sort_dir'] !== 'desc' || (int) $filters['per_page'] !== 10)
                    <a class="button-link" href="{{ route('admin.categories.index') }}">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        @if ($categories->count() === 0)
            <div class="empty-state">Chưa có danh mục nào.</div>
        @else
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Slug</th>
                            <th>Số sản phẩm</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td><span class="inline-code">{{ $category->slug }}</span></td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <span class="role-badge {{ $category->is_active ? 'admin' : 'user' }}">
                                        {{ $category->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                    </span>
                                </td>
                                <td>{{ $category->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button-secondary" href="{{ route('admin.categories.show', $category) }}">Xem</a>
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

            @if ($categories->hasPages())
                <div class="pagination">
                    <span class="muted">Hiển thị {{ $categories->firstItem() }} - {{ $categories->lastItem() }} trên tổng {{ $categories->total() }} categories</span>

                    <div class="pagination-links">
                        @if ($categories->onFirstPage())
                            <span class="pagination-link is-disabled">Trước</span>
                        @else
                            <a class="pagination-link" href="{{ $categories->previousPageUrl() }}">Trước</a>
                        @endif

                        <span class="pagination-link">{{ $categories->currentPage() }}/{{ $categories->lastPage() }}</span>

                        @if ($categories->hasMorePages())
                            <a class="pagination-link" href="{{ $categories->nextPageUrl() }}">Sau</a>
                        @else
                            <span class="pagination-link is-disabled">Sau</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
