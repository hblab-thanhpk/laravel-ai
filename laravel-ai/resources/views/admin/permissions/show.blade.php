@extends('admin.layouts.app')

@section('title', 'Chi Tiết Permission')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết Permission</h2>
            <p class="muted">Thông tin quyền {{ $permission->display_name }}.</p>
        </div>

        <div class="form-actions" style="margin-top: 0;">
            <a class="button-secondary" href="{{ route('admin.permissions.index') }}">Quay lại danh sách</a>
            <a class="button" href="{{ route('admin.permissions.edit', $permission) }}">Chỉnh sửa</a>
        </div>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <span>ID</span>
                <strong>{{ $permission->id }}</strong>
            </div>

            <div class="detail-item">
                <span>Tên hiển thị</span>
                <strong>{{ $permission->display_name }}</strong>
            </div>

            <div class="detail-item">
                <span>Tên định danh</span>
                <strong><span class="inline-code">{{ $permission->name }}</span></strong>
            </div>

            <div class="detail-item">
                <span>Loại</span>
                <strong>{{ $permission->is_system ? 'System' : 'Custom' }}</strong>
            </div>

            <div class="detail-item">
                <span>Số role sử dụng</span>
                <strong>{{ $permission->roles_count }}</strong>
            </div>
        </div>

        @if ($permission->description)
            <div class="card" style="margin-top: 1rem; margin-bottom: 0;">
                <strong>Mô tả</strong>
                <p style="margin-top: 0.5rem;">{{ $permission->description }}</p>
            </div>
        @endif

        <div style="margin-top: 1.5rem;">
            <strong>Được gán cho roles</strong>
            @if ($permission->roles->isEmpty())
                <p class="help-text" style="margin-top: 0.5rem;">Permission này chưa được gán cho role nào.</p>
            @else
                <div class="checkbox-grid" style="margin-top: 0.75rem;">
                    @foreach ($permission->roles as $role)
                        <div class="checkbox-item">
                            <div>
                                <strong>{{ $role->display_name }}</strong>
                                <span class="help-text">{{ $role->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if (! $permission->is_system)
            <div class="form-actions">
                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa permission này?');">
                    @csrf
                    @method('DELETE')
                    <button class="button-danger" type="submit">Xóa permission</button>
                </form>
            </div>
        @endif
    </div>
@endsection
