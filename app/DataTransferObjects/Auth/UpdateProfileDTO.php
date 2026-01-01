<?php

namespace App\DataTransferObjects\Auth;

class UpdateProfileDTO
{
    public function __construct(
        public readonly string $full_name,
        public readonly ?string $language = null,
        public readonly ?string $gender = null,
        public readonly ?string $password = null,
        public readonly ?string $avatar = null,
    ) {
    }
}
