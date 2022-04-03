<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Entity\UserRoles;

trait BaseAdminUserDataTrait
{
    /**
     * @return Array<string, Array<array-key, User>>
     */
    public function getAllEasyAdminUsers(): array
    {
        $roles = [
            UserRoles::ROLE_ADMIN,
            UserRoles::ROLE_PUBLISHER,
            UserRoles::ROLE_REVIEWER,
            UserRoles::ROLE_AUTHOR,
        ];

        return $this->getFilteredUsersForTests($roles);
    }

    /**
     * @return Array<string, Array<array-key, User>>
     */
    public function getAllAdminUsers(): array
    {
        $roles = [
            UserRoles::ROLE_ADMIN,
        ];

        return $this->getFilteredUsersForTests($roles);
    }

    /**
     * @return Array<string, Array<array-key, User>>
     */
    public function getAllNonAdminUsers(): array
    {
        $roles = [
            UserRoles::ROLE_PUBLISHER,
            UserRoles::ROLE_REVIEWER,
            UserRoles::ROLE_AUTHOR,
        ];

        return $this->getFilteredUsersForTests($roles);
    }

    /**
     * @return Array<string, Array<array-key, User>>
     */
    public function getAllUserUsers(): array
    {
        return array_map(fn($user) => [$user], array_filter(
            $this->getUsersFromUserData(),
            fn(User $user) => count($user->getRoles()) === 1 && in_array(UserRoles::ROLE_USER, $user->getRoles())
        ));
    }

    /**
     * @param Array<array-key, string> $roles
     * @return Array<string, Array<array-key, User>>
     */
    private function getFilteredUsersForTests(array $roles): array
    {
        return array_map(fn($user) => [$user], array_filter(
            $this->getUsersFromUserData(),
            fn(User $user) => count(array_intersect($roles,$user->getRoles())) > 0
        ));
    }

    /**
     * @return Array<User>
     */
    private function getUsersFromUserData(): array
    {
        return array_map(fn($userData) => $this->createUser($userData), AppFixtures::getUserData());
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