@extends('admin.layouts.app')

@section('title', 'Tạo Variant')

@section('content')
    <div class="page-header">
        <div>
            <h2>Tạo Variant</h2>
            <p class="muted">Thêm biến thể mới cho sản phẩm {{ $product->name }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.products.variants.store', $product) }}" method="POST">
        @csrf
        @include('admin.products.variants.form', ['submitLabel' => 'Tạo variant', 'product' => $product])
    </form>
@endsection
