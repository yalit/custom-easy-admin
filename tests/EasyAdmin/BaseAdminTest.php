<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use App\Entity\User;

interface BaseAdminTest
{
    function loginUser(User $user): void;
    function getAdminUrl(string $CRUDControllerClass, string $action, string $id = null);
}