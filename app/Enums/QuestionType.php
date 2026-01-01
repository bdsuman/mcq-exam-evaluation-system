<?php

namespace App\Enums;

enum QuestionType: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case SINGLE_CHOICE = 'single_choice';

    public function label(): string
    {
        return match ($this) {
            self::MULTIPLE_CHOICE => 'Multiple Choice',
            self::SINGLE_CHOICE => 'Single Choice',
        };
    }

    public static function defaults(): array
    {
        return [self::SINGLE_CHOICE->value];
    }
}
