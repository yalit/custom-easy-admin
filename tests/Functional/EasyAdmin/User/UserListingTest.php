<?php

declare(strict_types=1);

namespace App\Tests\Functional\EasyAdmin\User;

use App\Controller\Admin\UserCrudController;
use App\Entity\Enums\UserRole;
use App\Entity\User;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use PHPUnit\Framework\Attributes\DataProvider;

class UserListingTest extends AbstractAppCrudTestCase
{
    use CrudTestIndexAsserts;

    public static function allUserRoles(): iterable
    {
        yield "Author" => [UserRole::AUTHOR];
        yield "Admin" => [UserRole::ADMIN];
        yield "Publisher" => [UserRole::PUBLISHER];
    }

    #[DataProvider('allUserRoles')]
    public function testUserIndexDisplays(UserRole $userRole): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    public function testUserListingData(): void
    {
        $this->login();
        $this->client->request("GET", $this->generateIndexUrl());

        $this->assertIndexColumnExists('fullName');
        $this->assertIndexColumnExists('username');
        $this->assertIndexColumnExists('email');
        $this->assertIndexColumnExists('roles');
    }

    public function testGenericUserActionsDisplaysForAdmin(): void
    {
        $this->login();
        $this->client->request("GET", $this->generateIndexUrl());

        $users = $this->entityManager->getRepository(User::class)->findAll();
        self::assertIndexFullEntityCount(count($users));

        $user = $users[0];
        self::assertNotNull($user->getId());

        $this->assertIndexEntityActionExists(Action::DETAIL, $user->getId());
        $this->assertIndexEntityActionExists(Action::EDIT, $user->getId());
        $this->assertIndexEntityActionExists(Action::DELETE, $user->getId());
    }

    public static function nonAdminUsers(): iterable
    {
        yield "Author" => [UserRole::AUTHOR];
        yield "Publisher" => [UserRole::PUBLISHER];
    }

    #[DataProvider('nonAdminUsers')]
    public function testGenericUserActionsDisplaysForNonAdmin(UserRole $userRole): void
    {
        $user = $this->anyUser($userRole);
        $this->login($user->getEmail());
        $this->client->request("GET", $this->generateIndexUrl());

        $users = $this->entityManager->getRepository(User::class)->findAll();
        self::assertIndexFullEntityCount(count($users));

        $user = $users[0];
        self::assertNotNull($user->getId());

        $this->assertIndexEntityActionExists(Action::DETAIL, $user->getId());
        $this->assertIndexEntityActionNotExists(Action::EDIT, $user->getId());
        $this->assertIndexEntityActionNotExists(Action::DELETE, $user->getId());
    }

    protected function getControllerFqcn(): string
    {
        return UserCrudController::class;
    }
}
