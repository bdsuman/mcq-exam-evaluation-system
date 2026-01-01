<?php

namespace App\DataTransferObjects\Auth;

class RegisterDTO
{
    public function __construct(
        public readonly string $full_name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $language = 'en',
    ) {
    }
}
