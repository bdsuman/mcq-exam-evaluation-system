<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:250'],
            'description' => ['required', 'string', 'max:500'],
            'avatar'      => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:20480'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [

            'title.required'       => 'title_is_required',
            'title.max'            => 'title_must_not_exceed_250_characters',
            'description.required' => 'description_is_required',
            'description.max'      => 'description_must_not_exceed_500_characters',
            'avatar.mimes'         => 'avatar_must_be_jpg_jpeg_or_png',
            'avatar.max'           => 'avatar_must_not_exceed_20MB',
        ];
    }

    /**
     * Example body params for API docs
     */
    public function bodyParameters(): array
    {
        return [
            'title' => [
                'example' => 'Excellent Service!',
                'description' => 'Short title or heading of the testimonial'
            ],
            'description' => [
                'example' => 'The service was outstanding and exceeded expectations.',
                'description' => 'Main testimonial content (max 500 characters)'
            ],
            'avatar' => [
                'description' => 'Uploaded file (jpg, jpeg, png, max 20MB)'
            ],
        ];
    }
}
