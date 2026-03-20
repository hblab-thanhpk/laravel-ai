@extends('admin.layouts.app')

@section('title', 'Tạo Permission')

@section('content')
    <div class="page-header">
        <div>
            <h2>Tạo Permission</h2>
            <p class="muted">Tạo quyền mới để gán cho role.</p>
        </div>
    </div>

    <form action="{{ route('admin.permissions.store') }}" method="POST">
        @csrf
        @include('admin.permissions.form', ['submitLabel' => 'Tạo permission'])
    </form>
@endsection
