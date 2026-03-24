@extends('admin.layouts.app')

@section('title', 'Quản lý Orders')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý Orders</h2>
            <p class="muted">Theo dõi và xử lý đơn hàng trong hệ thống.</p>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.orders.index') }}" class="toolbar" method="GET">
            <div class="field" style="flex: 1 1 260px;">
                <label for="search">Tìm kiếm</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Tìm theo ID, tên hoặc email khách hàng">
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                    <option value="pending"   @selected($filters['status'] === 'pending')>Chờ xử lý</option>
                    <option value="paid"      @selected($filters['status'] === 'paid')>Đã thanh toán</option>
                    <option value="shipped"   @selected($filters['status'] === 'shipped')>Đang giao hàng</option>
                    <option value="completed" @selected($filters['status'] === 'completed')>Hoàn thành</option>
                    <option value="cancelled" @selected($filters['status'] === 'cancelled')>Đã hủy</option>
                </select>
            </div>

            <div class="field" style="flex: 1 1 180px;">
                <label for="sort_by">Sắp xếp theo</label>
                <select id="sort_by" name="sort_by">
                    <option value="created_at"  @selected($filters['sort_by'] === 'created_at')>Ngày tạo</option>
                    <option value="updated_at"  @selected($filters['sort_by'] === 'updated_at')>Ngày cập nhật</option>
                    <option value="total_price" @selected($filters['sort_by'] === 'total_price')>Tổng tiền</option>
                </select>
            </div>

            <div class="field" style="flex: 1 1 160px;">
                <label for="sort_dir">Chiều sắp xếp</label>
                <select id="sort_dir" name="sort_dir">
                    <option value="desc" @selected($filters['sort_dir'] === 'desc')>Giảm dần</option>
                    <option value="asc"  @selected($filters['sort_dir'] === 'asc')>Tăng dần</option>
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
                    <a class="button-link" href="{{ route('admin.orders.index') }}">Xóa bộ lọc</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card">
        @if ($orders->count() === 0)
            <div class="empty-state">Chưa có đơn hàng nào.</div>
        @else
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <span class="inline-code">{{ substr($order->id, 0, 8) }}…</span>
                                </td>
                                <td>
                                    @if ($order->user)
                                        <strong>{{ $order->user->name }}</strong><br>
                                        <small class="muted">{{ $order->user->email }}</small>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>{{ number_format((float) $order->total_price, 0, ',', '.') }} ₫</td>
                                <td>
                                    <span class="order-badge {{ $order->status->badgeClass() }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a class="button-secondary" href="{{ route('admin.orders.show', $order) }}">Xem</a>
                                        @if (! $order->status->isFinal())
                                            <a class="button" href="{{ route('admin.orders.edit', $order) }}">Cập nhật</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <span class="muted">Hiển thị {{ $orders->firstItem() }}–{{ $orders->lastItem() }} / {{ $orders->total() }} đơn hàng</span>
                <div class="pagination-links">
                    @if ($orders->onFirstPage())
                        <span class="pagination-link is-disabled">‹</span>
                    @else
                        <a class="pagination-link" href="{{ $orders->previousPageUrl() }}">‹</a>
                    @endif

                    @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                        @if ($page === $orders->currentPage())
                            <span class="pagination-link is-disabled">{{ $page }}</span>
                        @else
                            <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($orders->hasMorePages())
                        <a class="pagination-link" href="{{ $orders->nextPageUrl() }}">›</a>
                    @else
                        <span class="pagination-link is-disabled">›</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .order-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.35rem 0.65rem;
        white-space: nowrap;
    }
    .badge-warning  { background-color: #fff3cd; color: #856404; }
    .badge-info     { background-color: #cfe2ff; color: #0a3272; }
    .badge-primary  { background-color: #d2e8ff; color: #0a3d62; }
    .badge-success  { background-color: #d4edda; color: #155724; }
    .badge-danger   { background-color: #f8d7da; color: #721c24; }
</style>
@endpush
