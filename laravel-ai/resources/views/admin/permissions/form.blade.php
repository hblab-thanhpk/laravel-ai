@php
    $isSystemPermission = (bool) ($isSystemPermission ?? false);
@endphp

<div class="card">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-grid">
        <div class="field">
            <label for="name">Tên định danh (slug)</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', isset($permission) ? $permission->name : '') }}"
                required
                @disabled($isSystemPermission)
            >

            @if ($isSystemPermission)
                <input type="hidden" name="name" value="{{ old('name', isset($permission) ? $permission->name : '') }}">
                <span class="help-text">Permission hệ thống không cho phép đổi tên định danh.</span>
            @endif
        </div>

        <div class="field">
            <label for="display_name">Tên hiển thị</label>
            <input
                id="display_name"
                name="display_name"
                type="text"
                value="{{ old('display_name', isset($permission) ? $permission->display_name : '') }}"
                required
            >
        </div>
    </div>

    <div class="field" style="margin-top: 1rem;">
        <label for="description">Mô tả</label>
        <textarea id="description" name="description">{{ old('description', isset($permission) ? $permission->description : '') }}</textarea>
    </div>

    <div class="form-actions">
        <button class="button" type="submit">{{ $submitLabel }}</button>
        <a class="button-secondary" href="{{ isset($permission) ? route('admin.permissions.show', $permission) : route('admin.permissions.index') }}">Hủy</a>
    </div>
</div>
