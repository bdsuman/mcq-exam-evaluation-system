<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|min:6',
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
            'email.required' => 'field_input_is_missing',
            'email.email' => 'invalid_email_format',
            'password.required' => 'field_input_is_missing',
            'password.min' => 'password_must_be_at_least_6_characters',
        ];
    }
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'The email address of the user.',
                'example' => 'user@example.com',
            ],
            'password' => [
                'description' => 'The password of the user.',
                'example' => 'password123',
            ],
        ];
    }
}
