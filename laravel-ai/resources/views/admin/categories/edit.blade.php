@extends('admin.layouts.app')

@section('title', 'Cập Nhật Category')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cập nhật Category</h2>
            <p class="muted">Điều chỉnh thông tin danh mục {{ $category->name }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.categories.form', ['submitLabel' => 'Lưu thay đổi', 'category' => $category, 'parentOptions' => $parentOptions])
    </form>
@endsection
