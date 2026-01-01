<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'current_password_is_required',
            'current_password.min' => 'password_must_be_at_least_8_characters',
            'password.required' => 'new_password_is_required',
            'password.min' => 'password_must_be_at_least_8_characters',
            'password.confirmed' => 'passwords_do_not_match',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'current_password' => [
                'description' => 'The current password of the user.',
                'example' => 'currentpassword123',
            ],
            'password' => [
                'description' => 'The new password for the user account.',
                'example' => 'newsecurepassword456',
            ],
            'password_confirmation' => [
                'description' => 'Confirmation of the new password.',
                'example' => 'newsecurepassword456',
            ],
        ];
    }
}
