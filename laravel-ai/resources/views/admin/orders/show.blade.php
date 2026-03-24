@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết đơn hàng</h2>
            <p class="muted">Đơn hàng <span class="inline-code">{{ substr($order->id, 0, 8) }}…</span></p>
        </div>
        <div class="form-actions" style="margin-top: 0;">
            <a class="button-secondary" href="{{ route('admin.orders.index') }}">Quay lại danh sách</a>
            @if (! $order->status->isFinal())
                <a class="button" href="{{ route('admin.orders.edit', $order) }}">Cập nhật trạng thái</a>
            @endif
        </div>
    </div>

    {{-- Order overview --}}
    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <span>ID</span>
                <strong><span class="inline-code">{{ $order->id }}</span></strong>
            </div>
            <div class="detail-item">
                <span>Khách hàng</span>
                @if ($order->user)
                    <strong>{{ $order->user->name }}</strong>
                    <small class="muted" style="display:block;">{{ $order->user->email }}</small>
                @else
                    <strong class="muted">—</strong>
                @endif
            </div>
            <div class="detail-item">
                <span>Tổng tiền</span>
                <strong>{{ number_format((float) $order->total_price, 0, ',', '.') }} ₫</strong>
            </div>
            <div class="detail-item">
                <span>Trạng thái</span>
                <strong>
                    <span class="order-badge {{ $order->status->badgeClass() }}">
                        {{ $order->status->label() }}
                    </span>
                </strong>
            </div>
            <div class="detail-item">
                <span>Ngày tạo</span>
                <strong>{{ $order->created_at?->format('d/m/Y H:i') }}</strong>
            </div>
            <div class="detail-item">
                <span>Cập nhật lần cuối</span>
                <strong>{{ $order->updated_at?->format('d/m/Y H:i') }}</strong>
            </div>
        </div>

        @if ($order->notes)
            <div class="card" style="margin-top: 1rem; margin-bottom: 0;">
                <strong>Ghi chú</strong>
                <p style="margin-top: 0.5rem;">{{ $order->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Order items --}}
    <div class="card">
        <h3 style="margin-bottom: 1rem;">Sản phẩm trong đơn</h3>

        @if ($order->items->count() === 0)
            <div class="empty-state">Đơn hàng chưa có sản phẩm nào.</div>
        @else
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Biến thể</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if ($item->product)
                                        <br><small class="muted">SKU: {{ $item->product->sku }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->variant)
                                        {{ $item->variant->size }} / {{ $item->variant->color }}
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>{{ number_format((float) $item->unit_price, 0, ',', '.') }} ₫</td>
                                <td>{{ $item->quantity }}</td>
                                <td><strong>{{ number_format((float) $item->subtotal, 0, ',', '.') }} ₫</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                            <td><strong>{{ number_format((float) $order->total_price, 0, ',', '.') }} ₫</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Danger zone --}}
    @can('orders_delete')
    <div class="card">
        <h3 style="margin-bottom: 1rem; color: #721c24;">Vùng nguy hiểm</h3>
        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
              onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này? Hành động này không thể hoàn tác.');">
            @csrf
            @method('DELETE')
            <button class="button-danger" type="submit">Xóa đơn hàng</button>
        </form>
    </div>
    @endcan
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
    tfoot td { border-top: 2px solid #edf1f5; padding-top: 1rem; }
</style>
@endpush
