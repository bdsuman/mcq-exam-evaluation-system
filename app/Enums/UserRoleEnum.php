<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::STUDENT => 'Student',
        };
    }

    public static function defaults(): array
    {
        return [self::STUDENT->value];
    }
}
