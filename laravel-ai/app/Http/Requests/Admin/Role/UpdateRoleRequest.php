<?php

namespace App\Http\Requests\Admin\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('roles', 'name')->ignore($role->getKey()),
            ],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['uuid', Rule::exists('permissions', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên định danh role là bắt buộc.',
            'name.regex' => 'Tên định danh chỉ gồm chữ thường, số và dấu gạch dưới.',
            'name.unique' => 'Tên định danh role đã tồn tại.',
            'display_name.required' => 'Tên hiển thị role là bắt buộc.',
            'permission_ids.array' => 'Danh sách permission không hợp lệ.',
            'permission_ids.*.uuid' => 'Permission không hợp lệ.',
            'permission_ids.*.exists' => 'Permission không tồn tại.',
        ];
    }
}
