@extends('admin.layouts.app')

@section('title', 'Cập Nhật Product')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cập nhật Product</h2>
            <p class="muted">Chỉnh sửa thông tin cho sản phẩm {{ $product->name }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.products.form', ['submitLabel' => 'Lưu thay đổi', 'product' => $product])
    </form>
@endsection
