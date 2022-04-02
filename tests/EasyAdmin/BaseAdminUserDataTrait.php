<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use App\DataFixtures\AppFixtures;
use App\Entity\User;

trait BaseAdminUserDataTrait
{
    /**
     * @return Array<string, Array<array-key, User>>
     */
    public function getAllUserData(): array
    {
        return array_map(fn($userData) => [$this->createUser($userData)], AppFixtures::getUserData());
    }

    private function createUser(array $userData): User
    {
        $user = new User();
        $user->setFullName($userData[0]);
        $user->setUsername($userData[1]);
        $user->setPassword($userData[2]);
        $user->setEmail($userData[3]);
        $user->setRoles($userData[4]);

        return $user;
    }

    private function getUserFromUserName(string $username): User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
    }
}