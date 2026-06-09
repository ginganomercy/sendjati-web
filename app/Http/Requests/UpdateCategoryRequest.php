<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            // $this->category merujuk pada parameter route {category}
            'name' => 'required|string|max:100|unique:categories,name,' . $this->category->id,
            'type' => 'required|in:raw_material,ready_to_sell',
            'department' => 'required|in:kitchen,bar',
        ];
    }
}
