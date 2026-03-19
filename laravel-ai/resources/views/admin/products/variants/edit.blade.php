@extends('admin.layouts.app')

@section('title', 'Cập Nhật Variant')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cập nhật Variant</h2>
            <p class="muted">Chỉnh sửa biến thể {{ $variant->sku }} của sản phẩm {{ $product->name }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.products.variants.form', ['submitLabel' => 'Lưu thay đổi', 'product' => $product, 'variant' => $variant])
    </form>
@endsection
