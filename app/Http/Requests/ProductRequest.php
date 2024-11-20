<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'details' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
            ];

    }
    public function messages()
    {
        return [
            'name.required' => 'The product name is required.',
            'price.required' => 'The product price is required.',
            'quantity.required' => 'The quantity is required.',
            'category_id.required' => 'A valid category is required.',
            'brand_id.required' => 'A valid brand is required.',
        ];
    }
}
