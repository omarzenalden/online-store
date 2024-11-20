<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'comment' => 'string|max:500',
            'rate' => 'integer|min:1|max:5',
//            'like' => 'boolean',
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ];
    }
}
