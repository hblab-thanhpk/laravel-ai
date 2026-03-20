@extends('admin.layouts.app')

@section('title', 'Quản lý Variants')

@section('content')
    <div class="page-header">
        <div>
            <h2>Variants của {{ $product->name }}</h2>
            <p class="muted">Quản lý biến thể size/color cho sản phẩm đang chọn.</p>
        </div>

        <div class="form-actions" style="margin-top: 0;">
            <a class="button-secondary" href="{{ route('admin.products.show', $product) }}">Quay lại product</a>
            <a class="button" href="{{ route('admin.products.variants.create', $product) }}">Tạo variant</a>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.products.variants.index', $product) }}" class="toolbar" method="GET">
            <div class="field" style="flex: 1 1 260px;">
                <label for="search">Tìm kiếm</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Tìm theo SKU, size hoặc color">
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                    <option value="active" @selected($filters['status'] === 'active')>Hoạt động</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Ngừng hoạt động</option>
                </select>
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="sort_by">Sắp xếp theo</label>
                <select id="sort_by" name="sort_by">
                    <option value="created_at" @selected($filters['sort_by'] === 'created_at')>Ngày tạo</option>
                    <option value="updated_at" @selected($filters['sort_by'] === 'updated_at')>Ngày cập nhật</option>
                    <option value="sku" @selected($filters['sort_by'] === 'sku')>SKU</option>
                    <option value="size" @selected($filters['sort_by'] === 'size')>Size</option>
                    <option value="color" @selected($filters['sort_by'] === 'color')>Color</option>
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
                @if ($filters['search'] !== '' || $filters['status'] !== 'all' || $filters['sort_by'] !== 'created_at' || $filters['sort_dir'] !== 'desc' || (int) $filters['per_page'] !== 10)
                    <a class="button-link" href="{{ route('admin.products.variants.index', $product) }}">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        @if ($variants->count() === 0)
            <div class="empty-state">Sản phẩm này chưa có biến thể nào.</div>
        @else
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $variant)
                            <tr>
                                <td><span class="inline-code">{{ $variant->sku }}</span></td>
                                <td>{{ $variant->size ?? '—' }}</td>
                                <td>{{ $variant->color ?? '—' }}</td>
                                <td>{{ $variant->price !== null ? number_format((float) $variant->price, 2, '.', ',') : 'Mặc định theo product' }}</td>
                                <td>{{ $variant->stock }}</td>
                                <td>
                                    <span class="role-badge {{ $variant->is_active ? 'admin' : 'user' }}">
                                        {{ $variant->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button-secondary" href="{{ route('admin.products.variants.edit', [$product, $variant]) }}">Sửa</a>
                                        <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa variant này?');">
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

            @if ($variants->hasPages())
                <div class="pagination">
                    <span class="muted">Hiển thị {{ $variants->firstItem() }} - {{ $variants->lastItem() }} trên tổng {{ $variants->total() }} variants</span>

                    <div class="pagination-links">
                        @if ($variants->onFirstPage())
                            <span class="pagination-link is-disabled">Trước</span>
                        @else
                            <a class="pagination-link" href="{{ $variants->previousPageUrl() }}">Trước</a>
                        @endif

                        <span class="pagination-link">{{ $variants->currentPage() }}/{{ $variants->lastPage() }}</span>

                        @if ($variants->hasMorePages())
                            <a class="pagination-link" href="{{ $variants->nextPageUrl() }}">Sau</a>
                        @else
                            <span class="pagination-link is-disabled">Sau</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
