<?php

namespace App\DataTransferObjects\Task;

class CreateTaskDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly string $title,
        public readonly string $description,
        public readonly bool $is_completed,
        public readonly ?string $avatar = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'user_id' => $this->user_id,
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
