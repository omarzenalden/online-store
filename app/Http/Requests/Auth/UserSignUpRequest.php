<?php

namespace App\Http\Requests\Auth;

use App\Http\Responses\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UserSignUpRequest extends FormRequest
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
            'name' => ['string','required' , 'between:4,20'],
            'email' => ['required' , 'email', 'unique:users'],
            'password' => ['required' , 'confirmed' , 'min:8'],
            'referral_code' => [],
            'referred_by_code' => [],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, Response::Validation([],$validator->errors()));
    }
}
