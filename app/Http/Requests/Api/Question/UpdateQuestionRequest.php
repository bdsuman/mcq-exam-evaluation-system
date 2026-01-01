<?php

namespace App\Http\Requests\Api\Question;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', Rule::in(array_column(QuestionType::cases(), 'value'))],
            'question' => ['nullable', 'string', 'max:1000'],
            'mark' => ['nullable', 'integer', 'min:1', 'max:100'],
            'published' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array', 'min:2'],
            'options.*.option_text' => ['required', 'string', 'max:500'],
            'options.*.is_correct' => ['required', 'boolean'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'type.in' => 'invalid_question_type',
            'question.max' => 'question_text_must_not_exceed_1000_characters',
            'mark.integer' => 'mark_must_be_an_integer',
            'mark.min' => 'mark_must_be_at_least_1',
            'mark.max' => 'mark_must_not_exceed_100',
            'published.boolean' => 'published_must_be_boolean',
            'options.array' => 'options_must_be_an_array',
            'options.min' => 'at_least_2_options_required',
            'options.*.option_text.required' => 'option_text_is_required',
            'options.*.option_text.max' => 'option_text_must_not_exceed_500_characters',
            'options.*.is_correct.required' => 'is_correct_field_is_required',
            'options.*.is_correct.boolean' => 'is_correct_must_be_boolean',
        ];
    }

    /**
     * Example body params for API docs
     */
    public function bodyParameters(): array
    {
        return [
            'type' => [
                'example' => 'multiple_choice',
                'description' => 'Question type (single_choice or multiple_choice, optional)'
            ],
            'question' => [
                'example' => 'What is the capital of England?',
                'description' => 'The question text (max 1000 characters, optional)'
            ],
            'mark' => [
                'example' => 10,
                'description' => 'Points awarded for correct answer (1-100, optional)'
            ],
            'published' => [
                'example' => false,
                'description' => 'Whether the question is published (optional)'
            ],
            'options' => [
                'example' => [
                    ['option_text' => 'London', 'is_correct' => true],
                    ['option_text' => 'Paris', 'is_correct' => false],
                    ['option_text' => 'Berlin', 'is_correct' => false],
                ],
                'description' => 'Array of options to replace existing ones (minimum 2 required, optional)'
            ],
        ];
    }
}
