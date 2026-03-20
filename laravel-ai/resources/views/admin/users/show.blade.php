@extends('admin.layouts.app')

@section('title', 'Chi Tiết User')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết User</h2>
            <p class="muted">Thông tin đầy đủ của người dùng {{ $user->email }}.</p>
        </div>

        <div class="form-actions" style="margin-top: 0;">
            <a class="button-secondary" href="{{ route('admin.users.index') }}">Quay lại danh sách</a>
            <a class="button" href="{{ route('admin.users.edit', $user) }}">Chỉnh sửa</a>
        </div>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <span>ID</span>
                <strong>{{ $user->id }}</strong>
            </div>

            <div class="detail-item">
                <span>Tên</span>
                <strong>{{ $user->name }}</strong>
            </div>

            <div class="detail-item">
                <span>Email</span>
                <strong>{{ $user->email }}</strong>
            </div>

            <div class="detail-item">
                <span>Role</span>
                <strong>
                    @if ($user->role)
                        <span class="role-badge {{ $user->role->name === 'admin' ? 'admin' : 'user' }}">
                            {{ $user->role->display_name }}
                        </span>
                    @else
                        <span class="muted">Chưa có role</span>
                    @endif
                </strong>
            </div>

            <div class="detail-item">
                <span>Email verified at</span>
                <strong>{{ $user->email_verified_at?->format('d/m/Y H:i') ?? 'Chưa xác thực' }}</strong>
            </div>

            <div class="detail-item">
                <span>Ngày tạo</span>
                <strong>{{ $user->created_at?->format('d/m/Y H:i') }}</strong>
            </div>

            <div class="detail-item">
                <span>Cập nhật lần cuối</span>
                <strong>{{ $user->updated_at?->format('d/m/Y H:i') }}</strong>
            </div>
        </div>

        <div class="form-actions">
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa user này?');">
                @csrf
                @method('DELETE')
                <button class="button-danger" type="submit">Xóa user</button>
            </form>
        </div>
    </div>
@endsection