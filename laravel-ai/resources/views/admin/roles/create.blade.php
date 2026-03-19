@extends('admin.layouts.app')

@section('title', 'Tạo Role')

@section('content')
    <div class="page-header">
        <div>
            <h2>Tạo Role</h2>
            <p class="muted">Tạo vai trò mới và gán permissions tương ứng.</p>
        </div>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        @include('admin.roles.form', ['submitLabel' => 'Tạo role'])
    </form>
@endsection
