<?php

namespace App\DataTransferObjects\Auth;

class LoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}
