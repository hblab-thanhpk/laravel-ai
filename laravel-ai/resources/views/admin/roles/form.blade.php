@php
    $isSystemRole = (bool) ($isSystemRole ?? false);
    $selectedPermissionIds = collect(old('permission_ids', isset($role) ? $role->permissions->pluck('id')->all() : []))
        ->map(static fn ($id): string => (string) $id)
        ->values()
        ->all();
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
                value="{{ old('name', isset($role) ? $role->name : '') }}"
                required
                @disabled($isSystemRole)
            >
            <span class="help-text">Ví dụ: <span class="inline-code">admin</span>, <span class="inline-code">staff</span>, <span class="inline-code">customer</span>.</span>

            @if ($isSystemRole)
                <input type="hidden" name="name" value="{{ old('name', isset($role) ? $role->name : '') }}">
                <span class="help-text">Role hệ thống không cho phép đổi tên định danh.</span>
            @endif
        </div>

        <div class="field">
            <label for="display_name">Tên hiển thị</label>
            <input
                id="display_name"
                name="display_name"
                type="text"
                value="{{ old('display_name', isset($role) ? $role->display_name : '') }}"
                required
            >
        </div>
    </div>

    <div class="field" style="margin-top: 1rem;">
        <label for="description">Mô tả</label>
        <textarea id="description" name="description">{{ old('description', isset($role) ? $role->description : '') }}</textarea>
    </div>

    <div class="field" style="margin-top: 1rem;">
        <label>Permissions</label>

        @if ($permissions->isEmpty())
            <span class="help-text">Chưa có permission nào. Hãy tạo permission trước.</span>
        @else
            <div class="checkbox-grid">
                @foreach ($permissions as $permission)
                    <label class="checkbox-item">
                        <input
                            type="checkbox"
                            name="permission_ids[]"
                            value="{{ $permission->id }}"
                            @checked(in_array((string) $permission->id, $selectedPermissionIds, true))
                        >
                        <span>
                            <strong>{{ $permission->display_name }}</strong>
                            <span class="help-text">{{ $permission->name }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
    </div>

    <div class="form-actions">
        <button class="button" type="submit">{{ $submitLabel }}</button>
        <a class="button-secondary" href="{{ isset($role) ? route('admin.roles.show', $role) : route('admin.roles.index') }}">Hủy</a>
    </div>
</div>
