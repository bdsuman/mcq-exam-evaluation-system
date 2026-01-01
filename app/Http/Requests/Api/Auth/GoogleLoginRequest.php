<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\AppLanguageEnum;
use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'language' => 'nullable|string|in:' . implode(',', array_column(AppLanguageEnum::cases(), 'value')),
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Google token is required',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'token' => [
                'description' => 'Google ID token obtained from the client',
                'example' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6Ijc4NjU0MyJ9...'
            ],
            'language' => [
                'description' => 'Optional language code to store with the user',
                'example' => 'en',
            ],
        ];
    }
}
