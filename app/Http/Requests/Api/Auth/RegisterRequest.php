<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\AppLanguageEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'full_name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'language' => 'nullable|string|in:' . implode(',', array_column(AppLanguageEnum::cases(), 'value')),
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name is required',
            'full_name.min' => 'Full name must be at least 2 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be valid',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
        ];
    }

    public function bodyParameters(): array{
        return [
            'full_name' => [
                'description' => 'Full name of the user',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'Email address of the user',
                'example' => 'user@example.com',
            ],
            'password' => [
                'description' => 'Password of the user (minimum 8 characters)',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Password confirmation (must match password)',
                'example' => 'password123',
            ],
            'language' => [
                'description' => 'Preferred language of the user (en, es, fr, etc.)',
                'example' => 'en',
            ],
        ];
    }
}
