<?php

namespace App\Http\Requests\Admin\Product;

use App\DTOs\Catalog\ProductQueryData;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexProductRequest extends FormRequest
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
            'category_id' => ['nullable', 'string', Rule::in(array_merge(['all'], Category::query()->pluck('id')->all()))],
            'status' => ['nullable', 'string', Rule::in(['all', 'active', 'inactive'])],
            'per_page' => ['nullable', 'integer', Rule::in(ProductQueryData::PER_PAGE_OPTIONS)],
            'sort_by' => ['nullable', 'string', Rule::in(ProductQueryData::SORTABLE_COLUMNS)],
            'sort_dir' => ['nullable', 'string', Rule::in(ProductQueryData::SORT_DIRECTIONS)],
        ];
    }
}
