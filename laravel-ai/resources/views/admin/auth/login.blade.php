@extends('admin.layouts.app')

@section('title', 'Admin Login')

@section('content')
    <div style="max-width: 400px; margin: 4rem auto;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Admin Login</h2>

        <form method="POST" action="{{ route('admin.login') }}" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 1rem;">
                    <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="margin-bottom: 1.5rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;"
                >
            </div>

            <div style="margin-bottom: 2rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;"
                >
            </div>

            <button
                type="submit"
                style="width: 100%; padding: 0.75rem; background-color: #2c3e50; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: 500; cursor: pointer;"
            >
                Đăng nhập
            </button>
        </form>
    </div>
@endsection
