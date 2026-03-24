<?php

namespace App\Http\Requests\Admin\Role;

use App\DTOs\Access\RoleQueryData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRoleRequest extends FormRequest
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
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', Rule::in(RoleQueryData::PER_PAGE_OPTIONS)],
            'sort_by' => ['nullable', 'string', Rule::in(RoleQueryData::SORTABLE_COLUMNS)],
            'sort_dir' => ['nullable', 'string', Rule::in(RoleQueryData::SORT_DIRECTIONS)],
        ];
    }
}
