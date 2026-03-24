<?php

namespace App\Http\Requests\Admin\Permission;

use App\Models\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Permission $permission */
        $permission = $this->route('permission');

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('permissions', 'name')->ignore($permission->getKey()),
            ],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên định danh permission là bắt buộc.',
            'name.regex' => 'Tên định danh chỉ gồm chữ thường, số và dấu gạch dưới.',
            'name.unique' => 'Tên định danh permission đã tồn tại.',
            'display_name.required' => 'Tên hiển thị permission là bắt buộc.',
        ];
    }
}
