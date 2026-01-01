<?php

namespace App\DataTransferObjects\Auth;

class ChangePasswordDTO
{
    public function __construct(
        public readonly string $current_password,
        public readonly string $password,
    ) {
    }
}
