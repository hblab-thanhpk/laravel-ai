@extends('admin.layouts.app')

@section('title', 'Quản lý Products')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý Products</h2>
            <p class="muted">Quản trị thông tin sản phẩm và theo dõi tồn kho cơ bản.</p>
        </div>

        <a class="button" href="{{ route('admin.products.create') }}">Tạo product</a>
    </div>

    <div class="card">
        <form action="{{ route('admin.products.index') }}" class="toolbar" method="GET">
            <div class="field" style="flex: 1 1 280px;">
                <label for="search">Tìm kiếm</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Tìm theo tên, SKU hoặc slug">
            </div>

            <div class="field" style="flex: 1 1 220px;">
                <label for="category_id">Danh mục</label>
                <select id="category_id" name="category_id">
                    <option value="all" @selected($filters['category_id'] === 'all')>Tất cả</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['category_id'] === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                    <option value="active" @selected($filters['status'] === 'active')>Đang bán</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Ngừng bán</option>
                </select>
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="sort_by">Sắp xếp theo</label>
                <select id="sort_by" name="sort_by">
                    <option value="created_at" @selected($filters['sort_by'] === 'created_at')>Ngày tạo</option>
                    <option value="updated_at" @selected($filters['sort_by'] === 'updated_at')>Ngày cập nhật</option>
                    <option value="name" @selected($filters['sort_by'] === 'name')>Tên</option>
                    <option value="sku" @selected($filters['sort_by'] === 'sku')>SKU</option>
                    <option value="price" @selected($filters['sort_by'] === 'price')>Giá</option>
                    <option value="stock" @selected($filters['sort_by'] === 'stock')>Tồn kho</option>
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
                @if ($filters['search'] !== '' || $filters['category_id'] !== 'all' || $filters['status'] !== 'all' || $filters['sort_by'] !== 'created_at' || $filters['sort_dir'] !== 'desc' || (int) $filters['per_page'] !== 10)
                    <a class="button-link" href="{{ route('admin.products.index') }}">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        @if ($products->count() === 0)
            <div class="empty-state">Chưa có sản phẩm nào phù hợp điều kiện hiện tại.</div>
        @else
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>SKU</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Variants</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><span class="inline-code">{{ $product->sku }}</span></td>
                                <td>{{ $product->category?->name ?? 'Chưa phân loại' }}</td>
                                <td>{{ number_format((float) $product->price, 2, '.', ',') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ $product->variants_count }}</td>
                                <td>
                                    <span class="role-badge {{ $product->is_active ? 'admin' : 'user' }}">
                                        {{ $product->is_active ? 'Đang bán' : 'Ngừng bán' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button-secondary" href="{{ route('admin.products.show', $product) }}">Xem</a>
                                        <a class="button-secondary" href="{{ route('admin.products.edit', $product) }}">Sửa</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa product này?');">
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

            @if ($products->hasPages())
                <div class="pagination">
                    <span class="muted">Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }} trên tổng {{ $products->total() }} products</span>

                    <div class="pagination-links">
                        @if ($products->onFirstPage())
                            <span class="pagination-link is-disabled">Trước</span>
                        @else
                            <a class="pagination-link" href="{{ $products->previousPageUrl() }}">Trước</a>
                        @endif

                        <span class="pagination-link">{{ $products->currentPage() }}/{{ $products->lastPage() }}</span>

                        @if ($products->hasMorePages())
                            <a class="pagination-link" href="{{ $products->nextPageUrl() }}">Sau</a>
                        @else
                            <span class="pagination-link is-disabled">Sau</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
