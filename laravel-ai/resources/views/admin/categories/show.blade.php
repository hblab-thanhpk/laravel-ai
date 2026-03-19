@extends('admin.layouts.app')

@section('title', 'Chi Tiết Category')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết Category</h2>
            <p class="muted">Thông tin danh mục {{ $category->name }}.</p>
        </div>

        <div class="form-actions" style="margin-top: 0;">
            <a class="button-secondary" href="{{ route('admin.categories.index') }}">Quay lại danh sách</a>
            <a class="button" href="{{ route('admin.categories.edit', $category) }}">Chỉnh sửa</a>
        </div>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <span>ID</span>
                <strong>{{ $category->id }}</strong>
            </div>

            <div class="detail-item">
                <span>Tên</span>
                <strong>{{ $category->name }}</strong>
            </div>

            <div class="detail-item">
                <span>Slug</span>
                <strong><span class="inline-code">{{ $category->slug }}</span></strong>
            </div>

            <div class="detail-item">
                <span>Trạng thái</span>
                <strong>{{ $category->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}</strong>
            </div>

            <div class="detail-item">
                <span>Số sản phẩm</span>
                <strong>{{ $category->products_count }}</strong>
            </div>

            <div class="detail-item">
                <span>Ngày tạo</span>
                <strong>{{ $category->created_at?->format('d/m/Y H:i') }}</strong>
            </div>
        </div>

        @if ($category->description)
            <div class="card" style="margin-top: 1rem; margin-bottom: 0;">
                <strong>Mô tả</strong>
                <p style="margin-top: 0.5rem;">{{ $category->description }}</p>
            </div>
        @endif

        <div class="form-actions">
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa category này?');">
                @csrf
                @method('DELETE')
                <button class="button-danger" type="submit">Xóa category</button>
            </form>
        </div>
    </div>
@endsection
