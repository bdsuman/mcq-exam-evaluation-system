<?php

namespace App\Http\Requests\Api\Student;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses' => ['required', 'array', 'min:1'],
            'responses.*.question_id' => ['required', 'integer', 'exists:questions,id'],
            'responses.*.option_ids' => ['required', 'array', 'min:1'],
            'responses.*.option_ids.*' => ['required', 'integer', 'exists:options,id'],
        ];
    }
}
