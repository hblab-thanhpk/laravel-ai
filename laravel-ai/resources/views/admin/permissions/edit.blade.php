@extends('admin.layouts.app')

@section('title', 'Cập Nhật Permission')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cập nhật Permission</h2>
            <p class="muted">Cập nhật thông tin permission {{ $permission->display_name }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.permissions.form', ['submitLabel' => 'Lưu thay đổi', 'permission' => $permission, 'isSystemPermission' => $isSystemPermission])
    </form>
@endsection
