<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // RBAC akan diatur di level Middleware/Gate nantinya (Sprint C)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:categories,name',
            'type' => 'required|in:raw_material,ready_to_sell',
            'department' => 'required|in:kitchen,bar',
        ];
    }
}
