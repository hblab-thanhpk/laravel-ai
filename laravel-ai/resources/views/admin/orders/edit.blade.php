@extends('admin.layouts.app')

@section('title', 'Cập Nhật Trạng Thái Đơn Hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cập nhật trạng thái</h2>
            <p class="muted">Đơn hàng <span class="inline-code">{{ substr($order->id, 0, 8) }}…</span></p>
        </div>
    </div>

    <div class="card">
        <div class="detail-grid" style="margin-bottom: 1.5rem;">
            <div class="detail-item">
                <span>Khách hàng</span>
                <strong>{{ $order->user?->name ?? '—' }}</strong>
            </div>
            <div class="detail-item">
                <span>Tổng tiền</span>
                <strong>{{ number_format((float) $order->total_price, 0, ',', '.') }} ₫</strong>
            </div>
            <div class="detail-item">
                <span>Trạng thái hiện tại</span>
                <strong>
                    <span class="order-badge {{ $order->status->badgeClass() }}">
                        {{ $order->status->label() }}
                    </span>
                </strong>
            </div>
        </div>

        @if (count($allowedTransitions) === 0)
            <div class="alert alert-danger">
                Đơn hàng đã ở trạng thái <strong>{{ $order->status->label() }}</strong> và không thể thay đổi.
            </div>
            <div class="form-actions">
                <a class="button-secondary" href="{{ route('admin.orders.show', $order) }}">Quay lại chi tiết</a>
            </div>
        @else
            <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field">
                        <label for="status">Chuyển sang trạng thái</label>
                        <select id="status" name="status">
                            @foreach ($allowedTransitions as $transition)
                                <option value="{{ $transition->value }}" @selected(old('status') === $transition->value)>
                                    {{ $transition->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="help-text" style="color: #721c24;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Xác nhận cập nhật</button>
                    <a class="button-secondary" href="{{ route('admin.orders.show', $order) }}">Hủy</a>
                </div>
            </form>
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
