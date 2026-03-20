<?php

namespace App\Http\Requests\Admin\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Category $category */
        $category = $this->route('category');

        return [
            'name'      => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->getKey())],
            'slug'      => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($category->getKey()),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active'   => ['required', 'boolean'],
            'parent_id'   => ['nullable', 'uuid', 'exists:categories,id', Rule::notIn([$category->getKey()])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'slug.required' => 'Slug là bắt buộc.',
            'slug.regex' => 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.',
            'slug.unique' => 'Slug danh mục đã tồn tại.',
            'is_active.required' => 'Trạng thái hoạt động là bắt buộc.',
        ];
    }
}
