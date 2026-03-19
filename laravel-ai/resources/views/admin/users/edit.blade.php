@extends('admin.layouts.app')

@section('title', 'Cập Nhật User')

@section('content')
    <div class="page-header">
        <div>
            <h2>Cập nhật User</h2>
            <p class="muted">Chỉnh sửa thông tin cho {{ $user->email }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.users.form', ['submitLabel' => 'Lưu thay đổi', 'user' => $user])
    </form>
@endsection