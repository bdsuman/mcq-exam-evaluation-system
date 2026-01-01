<?php

namespace App\Http\Requests\Api\Auth;

use App\Enums\AppLanguageEnum;
use App\Enums\UserGenderEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'password' => 'nullable|string|min:8|confirmed',
            'language' => 'nullable|string|in:' . implode(',', array_column(AppLanguageEnum::cases(), 'value')),
            'gender' => 'nullable|string|in:' . implode(',', array_column(UserGenderEnum::cases(), 'value')),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
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
            'full_name.required' => 'field_input_is_missing',
            'full_name.min' => 'must_be_at_least_2_characters',
            'password.min' => 'password_must_be_at_least_8_characters',
            'password.confirmed' => 'passwords_do_not_match',
            'gender.in' => 'invalid_gender_value',
            'avatar.image' => 'avatar_must_be_an_image',
            'avatar.mimes' => 'avatar_must_be_jpg_or_png',
            'avatar.max' => 'avatar_size_exceeds_limit',
        ];
    }

    public function bodyParameters(): array{
        return [
            'full_name' => [
                'description' => 'Full name of the user',
                'example' => 'John Doe',
            ],
            'password' => [
                'description' => 'Password of the user (optional, only required if changing password)',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Password confirmation (required if password is provided)',
                'example' => 'password123',
            ],
            'language' => [
                'description' => 'Preferred language of the user (en, es, fr, etc.)',
                'example' => 'en',
            ],
            'gender' => [
                'description' => 'Gender of the user (male, female, other)',
                'example' => 'male',
            ],
            'avatar' => [
                'description' => 'Avatar image of the user (JPEG, PNG, JPG - max 20MB)',
                'type' => 'file',
            ],
        ];
    }
}