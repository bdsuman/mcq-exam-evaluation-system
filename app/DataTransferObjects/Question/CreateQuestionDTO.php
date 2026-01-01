<?php

namespace App\DataTransferObjects\Question;

class CreateQuestionDTO
{
    public function __construct(
        public readonly string $type,
        public readonly string $question,
        public readonly int $mark,
        public readonly bool $published = false,
        public readonly array $options = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'question' => $this->question,
            'mark' => $this->mark,
            'published' => $this->published,
            'options' => $this->options,
        ];
    }
}
