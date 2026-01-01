<?php

namespace App\DataTransferObjects\Auth;

class GoogleLoginDTO
{
    public function __construct(
        public readonly string $token,
        public readonly ?string $language = null,
    ) {
    }
}
