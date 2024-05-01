<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
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
            'original_quantity' => 'integer',
            'available_quantity' => 'integer',
            'available_parts' => 'integer',
            'discount' => 'numeric|min:0|max:100',
            'discount_limit' => 'required|numeric|min:0|max:100',
            'expire_date' => 'nullable|date',
            'price' => 'numeric',
        ];
    }
}
