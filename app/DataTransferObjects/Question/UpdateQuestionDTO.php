<?php

namespace App\DataTransferObjects\Question;

class UpdateQuestionDTO
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $question = null,
        public readonly ?int $mark = null,
        public readonly ?bool $published = null,
        public readonly ?array $options = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->type !== null) {
            $data['type'] = $this->type;
        }

        if ($this->question !== null) {
            $data['question'] = $this->question;
        }

        if ($this->mark !== null) {
            $data['mark'] = $this->mark;
        }

        if ($this->published !== null) {
            $data['published'] = $this->published;
        }

        if ($this->options !== null) {
            $data['options'] = $this->options;
        }

        return $data;
    }
}
