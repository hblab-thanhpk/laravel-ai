@extends('admin.layouts.app')

@section('title', 'Tạo Category')

@section('content')
    <div class="page-header">
        <div>
            <h2>Tạo Category</h2>
            <p class="muted">Tạo mới danh mục để phân loại sản phẩm.</p>
        </div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('admin.categories.form', ['submitLabel' => 'Tạo category', 'parentOptions' => $parentOptions])
    </form>
@endsection
