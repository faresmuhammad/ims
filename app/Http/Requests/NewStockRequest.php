<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewStockRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stocks' => 'required|array|min:1',
            'stocks.*.quantity' => 'required|integer',
            'stocks.*.parts' => 'required|integer',
            'stocks.*.discount' => 'required|numeric|min:0|max:100',
            'stocks.*.discount_limit' => 'required|numeric|min:0|max:100',
            'stocks.*.expire_date' => 'nullable|string',
            'stocks.*.price' => 'required|numeric',
            'stocks.*.product_id' => 'required',
            'stocks.*.supplier_id' => 'required',
            'order_id' => 'required'
        ];
    }
}
