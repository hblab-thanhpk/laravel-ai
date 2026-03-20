<?php

namespace App\Http\Requests\Admin\Order;

use App\DTOs\Order\OrderQueryData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexOrderRequest extends FormRequest
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
            'search'   => ['nullable', 'string', 'max:255'],
            'status'   => ['nullable', 'string', Rule::in(['all', 'pending', 'paid', 'shipped', 'completed', 'cancelled'])],
            'per_page' => ['nullable', 'integer', Rule::in(OrderQueryData::PER_PAGE_OPTIONS)],
            'sort_by'  => ['nullable', 'string', Rule::in(OrderQueryData::SORTABLE_COLUMNS)],
            'sort_dir' => ['nullable', 'string', Rule::in(OrderQueryData::SORT_DIRECTIONS)],
        ];
    }
}
