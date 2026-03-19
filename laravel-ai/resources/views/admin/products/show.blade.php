@extends('admin.layouts.app')

@section('title', 'Chi Tiết Product')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết Product</h2>
            <p class="muted">Thông tin đầy đủ của sản phẩm {{ $product->name }}.</p>
        </div>

        <div class="form-actions" style="margin-top: 0;">
            <a class="button-secondary" href="{{ route('admin.products.index') }}">Quay lại danh sách</a>
            <a class="button" href="{{ route('admin.products.edit', $product) }}">Chỉnh sửa</a>
            @if (auth()->user()->hasPermission('product_variants_view'))
                <a class="button-secondary" href="{{ route('admin.products.variants.index', $product) }}">Quản lý variants</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <span>ID</span>
                <strong>{{ $product->id }}</strong>
            </div>

            <div class="detail-item">
                <span>Tên sản phẩm</span>
                <strong>{{ $product->name }}</strong>
            </div>

            <div class="detail-item">
                <span>Danh mục</span>
                <strong>{{ $product->category?->name ?? 'Chưa phân loại' }}</strong>
            </div>

            <div class="detail-item">
                <span>Slug</span>
                <strong><span class="inline-code">{{ $product->slug }}</span></strong>
            </div>

            <div class="detail-item">
                <span>SKU</span>
                <strong><span class="inline-code">{{ $product->sku }}</span></strong>
            </div>

            <div class="detail-item">
                <span>Giá</span>
                <strong>{{ number_format((float) $product->price, 2, '.', ',') }}</strong>
            </div>

            <div class="detail-item">
                <span>Tồn kho</span>
                <strong>{{ $product->stock }}</strong>
            </div>

            <div class="detail-item">
                <span>Số biến thể</span>
                <strong>{{ $product->variants_count }}</strong>
            </div>

            <div class="detail-item">
                <span>Trạng thái</span>
                <strong>{{ $product->is_active ? 'Đang bán' : 'Ngừng bán' }}</strong>
            </div>

            <div class="detail-item">
                <span>Ngày tạo</span>
                <strong>{{ $product->created_at?->format('d/m/Y H:i') }}</strong>
            </div>
        </div>

        @if ($product->description)
            <div class="card" style="margin-top: 1rem; margin-bottom: 0;">
                <strong>Mô tả</strong>
                <p style="margin-top: 0.5rem;">{{ $product->description }}</p>
            </div>
        @endif

        <div style="margin-top: 1.5rem;">
            <strong>Biến thể mới nhất</strong>

            @if ($latestVariants->isEmpty())
                <p class="help-text" style="margin-top: 0.5rem;">Sản phẩm chưa có biến thể nào.</p>
            @else
                <div class="table-wrapper" style="margin-top: 0.75rem;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestVariants as $variant)
                                <tr>
                                    <td><span class="inline-code">{{ $variant->sku }}</span></td>
                                    <td>{{ $variant->size ?? '—' }}</td>
                                    <td>{{ $variant->color ?? '—' }}</td>
                                    <td>{{ $variant->price !== null ? number_format((float) $variant->price, 2, '.', ',') : 'Mặc định theo product' }}</td>
                                    <td>{{ $variant->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="form-actions">
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa product này?');">
                @csrf
                @method('DELETE')
                <button class="button-danger" type="submit">Xóa product</button>
            </form>
        </div>
    </div>
@endsection
