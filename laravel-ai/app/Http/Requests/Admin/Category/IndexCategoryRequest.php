<?php

namespace App\Http\Requests\Admin\Category;

use App\DTOs\Catalog\CategoryQueryData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCategoryRequest extends FormRequest
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
            'status' => ['nullable', 'string', Rule::in(['all', 'active', 'inactive'])],
            'per_page' => ['nullable', 'integer', Rule::in(CategoryQueryData::PER_PAGE_OPTIONS)],
            'sort_by' => ['nullable', 'string', Rule::in(CategoryQueryData::SORTABLE_COLUMNS)],
            'sort_dir' => ['nullable', 'string', Rule::in(CategoryQueryData::SORT_DIRECTIONS)],
        ];
    }
}
