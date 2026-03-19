@extends('admin.layouts.app')

@section('title', 'Cập Nhật Role')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cập nhật Role</h2>
            <p class="muted">Cập nhật thông tin role {{ $role->display_name }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.roles.form', ['submitLabel' => 'Lưu thay đổi', 'role' => $role, 'isSystemRole' => $isSystemRole])
    </form>
@endsection
