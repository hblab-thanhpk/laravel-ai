@extends('admin.layouts.app')

@section('title', 'Chi Tiết Role')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết Role</h2>
            <p class="muted">Thông tin vai trò {{ $role->display_name }}.</p>
        </div>

        <div class="form-actions" style="margin-top: 0;">
            <a class="button-secondary" href="{{ route('admin.roles.index') }}">Quay lại danh sách</a>
            <a class="button" href="{{ route('admin.roles.edit', $role) }}">Chỉnh sửa</a>
        </div>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <span>ID</span>
                <strong>{{ $role->id }}</strong>
            </div>

            <div class="detail-item">
                <span>Tên hiển thị</span>
                <strong>{{ $role->display_name }}</strong>
            </div>

            <div class="detail-item">
                <span>Tên định danh</span>
                <strong><span class="inline-code">{{ $role->name }}</span></strong>
            </div>

            <div class="detail-item">
                <span>Loại</span>
                <strong>{{ $role->is_system ? 'System' : 'Custom' }}</strong>
            </div>

            <div class="detail-item">
                <span>Số user đang dùng</span>
                <strong>{{ $role->users_count }}</strong>
            </div>

            <div class="detail-item">
                <span>Số permission</span>
                <strong>{{ $role->permissions->count() }}</strong>
            </div>
        </div>

        @if ($role->description)
            <div class="card" style="margin-top: 1rem; margin-bottom: 0;">
                <strong>Mô tả</strong>
                <p style="margin-top: 0.5rem;">{{ $role->description }}</p>
            </div>
        @endif

        <div style="margin-top: 1.5rem;">
            <strong>Permissions</strong>
            @if ($role->permissions->isEmpty())
                <p class="help-text" style="margin-top: 0.5rem;">Role này chưa được gán permission.</p>
            @else
                <div class="checkbox-grid" style="margin-top: 0.75rem;">
                    @foreach ($role->permissions as $permission)
                        <div class="checkbox-item">
                            <div>
                                <strong>{{ $permission->display_name }}</strong>
                                <span class="help-text">{{ $permission->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if (! $role->is_system)
            <div class="form-actions">
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa role này?');">
                    @csrf
                    @method('DELETE')
                    <button class="button-danger" type="submit">Xóa role</button>
                </form>
            </div>
        @endif
    </div>
@endsection
