<?php

namespace App\Entity\Enums;

enum UserRoles : string
{
    case AUTHOR = "ROLE_AUTHOR";
    case ADMIN = "ROLE_ADMIN";
    case PUBLISHER = "ROLE_PUBLISHER";

    public static function all(): array
    {
        return array_combine(array_map(fn($e) => $e->value, self::cases()), array_map(fn($e) => $e->value, self::cases()));
    }
}
