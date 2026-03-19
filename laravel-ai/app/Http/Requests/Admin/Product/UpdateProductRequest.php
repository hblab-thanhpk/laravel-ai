<?php

namespace App\Http\Requests\Admin\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        /** @var Product $product */
        $product = $this->route('product');

        return [
            'category_id' => ['nullable', 'uuid', Rule::exists('categories', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('products', 'slug')->ignore($product->getKey()),
            ],
            'sku' => [
                'required',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('products', 'sku')->ignore($product->getKey()),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'slug.required' => 'Slug sản phẩm là bắt buộc.',
            'slug.regex' => 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.',
            'slug.unique' => 'Slug sản phẩm đã tồn tại.',
            'sku.required' => 'SKU là bắt buộc.',
            'sku.regex' => 'SKU chỉ gồm chữ, số, dấu gạch ngang hoặc gạch dưới.',
            'sku.unique' => 'SKU đã tồn tại.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.min' => 'Giá sản phẩm không được âm.',
            'stock.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock.min' => 'Tồn kho không được âm.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'is_active.required' => 'Trạng thái hoạt động là bắt buộc.',
        ];
    }
}
