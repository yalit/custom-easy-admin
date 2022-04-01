<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

class UserRoles
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_AUTHOR = 'ROLE_AUTHOR';
    public const ROLE_PUBLISHER = 'ROLE_PUBLISHER';
    public const ROLE_REVIEWER = 'ROLE_REVIEWER';
    public const ROLE_USER = 'ROLE_USER';

    /**
     * @return array<string, string>
     */
    public static function getAllRoles(): array
    {
        return [
            'Admin' => self::ROLE_ADMIN,
            'Author' => self::ROLE_AUTHOR,
            'Publisher' => self::ROLE_PUBLISHER,
            'Reviewer' => self::ROLE_REVIEWER,
            'User' => self::ROLE_USER,
        ];
    }
}
