<?php

namespace App\Http\Requests\Admin\ProductVariant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductVariantRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('product_variants', 'sku')],
            'size' => ['nullable', 'string', 'max:50', 'required_without:color'],
            'color' => ['nullable', 'string', 'max:50', 'required_without:size'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'SKU biến thể là bắt buộc.',
            'sku.regex' => 'SKU chỉ gồm chữ, số, dấu gạch ngang hoặc gạch dưới.',
            'sku.unique' => 'SKU biến thể đã tồn tại.',
            'size.required_without' => 'Vui lòng nhập size hoặc color.',
            'color.required_without' => 'Vui lòng nhập size hoặc color.',
            'price.min' => 'Giá biến thể không được âm.',
            'stock.required' => 'Tồn kho biến thể là bắt buộc.',
            'stock.min' => 'Tồn kho biến thể không được âm.',
            'is_active.required' => 'Trạng thái hoạt động là bắt buộc.',
        ];
    }
}
