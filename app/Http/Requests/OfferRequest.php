<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:offers,name',
            'type' => 'required|in:discount,fixed_price',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date|before_or_equal:end_date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|after_or_equal:today',
            'products' => 'required|array',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
        ];

    }
    public function messages()
    {
        return [
            'name.required' => 'Offer name is required',
            'value.required' => 'Offer value is required',
            'products.required' => 'At least one product must be selected',
            'products.*.quantity.required' => 'Each product must have a quantity',
        ];
    }
}
