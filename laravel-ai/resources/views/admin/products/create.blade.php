@extends('admin.layouts.app')

@section('title', 'Tạo Product')

@section('content')
    <div class="page-header">
        <div>
            <h2>Tạo Product</h2>
            <p class="muted">Tạo mới sản phẩm cho catalog.</p>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST">
        @csrf
        @include('admin.products.form', ['submitLabel' => 'Tạo product'])
    </form>
@endsection
