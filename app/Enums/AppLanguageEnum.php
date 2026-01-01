<?php

namespace App\Enums;

enum AppLanguageEnum: string
{
    case EN = 'en';
    case BN = 'bn';

    public function label(): string
    {
        return match ($this) {
            self::EN => 'English',
            self::BN => 'Bangla',
        };
    }
}
