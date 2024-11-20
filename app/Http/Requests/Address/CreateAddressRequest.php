<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
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
            /*
             * To enforce validation for a full name input where the user must provide at least a first name and a last name
             *  (two or more parts separated by a space)
             */
            'full_name' => ['required', 'regex:/^[a-zA-Z]+(?:\s+[a-zA-Z]+)+$/'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,15'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'street' => ['nullable', 'string', 'max:150'],
            'building' => ['required', 'string', 'max:10'],
            'apartment' => ['nullable', 'string', 'max:10'],
            'primary' => ['boolean'],
        ];
    }
    public function messages()
    {
        return [
            'full_name.regex' => 'The full name must contain at least a first and last name, separated by a space.',
        ];
    }
}
