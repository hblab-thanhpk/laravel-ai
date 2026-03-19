@extends('admin.layouts.app')

@section('title', 'Tạo User')

@section('content')
    <div class="page-header">
        <div>
            <h2>Tạo User</h2>
            <p class="muted">Tạo mới tài khoản người dùng để sử dụng hệ thống.</p>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        @include('admin.users.form', ['submitLabel' => 'Tạo user'])
    </form>
@endsection