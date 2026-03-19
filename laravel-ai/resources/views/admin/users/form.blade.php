<div class="card">
    @php
        $isLastAdmin = (bool) ($isLastAdmin ?? false);
        $adminRoleId = \App\Models\Role::query()->where('name', 'admin')->value('id');
        $disableRoleSelect = isset($user) && $isLastAdmin;
        $currentRoleId = old('role_id', isset($user) ? $user->role_id : '');
    @endphp

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
            <label for="name">Tên</label>
            <input id="name" name="name" type="text" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
        </div>

        <div class="field">
            <label for="password">Mật khẩu</label>
            <input id="password" name="password" type="password" {{ isset($user) ? '' : 'required' }}>
            <span class="help-text">
                {{ isset($user) ? 'Để trống nếu không muốn thay đổi mật khẩu.' : 'Mật khẩu phải có ít nhất 8 ký tự.' }}
            </span>
        </div>

        <div class="field">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input id="password_confirmation" name="password_confirmation" type="password" {{ isset($user) ? '' : 'required' }}>
        </div>

        <div class="field">
            <label for="role_id">Role</label>
            <select id="role_id" name="role_id" @disabled($disableRoleSelect)>
                <option value="">-- Không có role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected($currentRoleId === $role->id)>
                        {{ $role->display_name }}
                    </option>
                @endforeach
            </select>

            @if ($disableRoleSelect)
                <input type="hidden" name="role_id" value="{{ $adminRoleId }}">
                <span class="help-text">Không thể thay đổi role vì đây là admin cuối cùng trong hệ thống.</span>
            @endif
        </div>
    </div>

    <div class="form-actions">
        <button class="button" type="submit">{{ $submitLabel }}</button>
        <a class="button-secondary" href="{{ isset($user) ? route('admin.users.show', $user) : route('admin.users.index') }}">Hủy</a>
    </div>
</div>