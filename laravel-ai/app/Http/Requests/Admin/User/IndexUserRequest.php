<?php

namespace App\Http\Requests\Admin\User;

use App\DTOs\User\UserQueryData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexUserRequest extends FormRequest
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
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'role_id' => ['nullable', Rule::in(array_merge(['all'], \App\Models\Role::query()->pluck('id')->all()))],
            'per_page' => ['nullable', 'integer', Rule::in(UserQueryData::PER_PAGE_OPTIONS)],
            'sort_by' => ['nullable', 'string', Rule::in(UserQueryData::SORTABLE_COLUMNS)],
            'sort_dir' => ['nullable', 'string', Rule::in(UserQueryData::SORT_DIRECTIONS)],
        ];
    }
}