<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sku'           => 'nullable|string|max:50|unique:items,sku',
            'name'          => 'required|string|max:200',
            'type'          => 'required|in:consumable,equipment',
            'department'    => 'required|in:kitchen,bar',
            'category_id'   => 'required|exists:categories,id',
            'unit_id'       => 'required|exists:units,id',
            'minimum_stock' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'harga_satuan'  => 'nullable|numeric|min:0',
        ];
    }
}
