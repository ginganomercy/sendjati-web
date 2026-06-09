<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'               => 'required|in:in,out,return,opname',
            'details'            => 'required|array|min:1',
            'details.*.item_id'  => 'required|exists:items,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unit_price' => 'nullable|numeric|min:0',
        ];
    }
}
