<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use App\DataFixtures\AppFixtures;
use App\Entity\User;

trait BaseAdminDataTrait
{
    /**
     * @return Array<string, Array<array-key, User>>
     */
    public function getAllUserData(): array
    {
        return array_map(function($userData){
            $user = new User();
            $user->setFullName($userData[0]);
            $user->setUsername($userData[1]);
            $user->setPassword($userData[2]);
            $user->setEmail($userData[3]);
            $user->setRoles($userData[4]);
            return [$user];
        }, AppFixtures::getUserData());
    }
}