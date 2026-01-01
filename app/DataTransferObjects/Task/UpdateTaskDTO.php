<?php

namespace App\DataTransferObjects\Task;

class UpdateTaskDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly bool $is_completed,
        public readonly string $language,
        public readonly ?string $avatar = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
        ];

        if ($this->avatar) {
            $data['avatar'] = $this->avatar;
        }

        return $data;
    }
}
