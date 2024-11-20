<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
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
            'old_password' => 'required',
            'new_password' => 'required|confirmed|between:8,20|different:old_password'
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Hash::check($this->old_password, Auth::user()->password)) {
                $validator->errors()->add('old_password', 'The old password is incorrect.');
            }
        });
    }
}
