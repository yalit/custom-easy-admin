<?php

declare(strict_types=1);

namespace App\Tests\Functional\EasyAdmin\User;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Functional\Story\TestLoginStory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use PHPUnit\Framework\Attributes\DataProvider;

class UserListingTest extends AbstractAppCrudTestCase
{
    use CrudTestIndexAsserts;

    public static function allUserEmails(): array
    {
        return [
            'admin' => ['admin@email.com'],
            'publisher' => ['publisher_1@email.com'],
            'author' => ['author_1@email.com'],
        ];
    }

    public static function nonAdminUserEmails(): array
    {
        return [
            'publisher' => ['publisher_1@email.com'],
            'author' => ['author_1@email.com'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        TestLoginStory::load();
    }

    #[DataProvider('allUserEmails')]
    public function testUserIndexDisplays(string $email): void
    {
        $this->login($email);
        $this->client->request("GET", $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    public function testUserListingData(): void
    {
        $this->login('admin@email.com');
        $this->client->request("GET", $this->generateIndexUrl());

        $this->assertIndexColumnExists('fullName');
        $this->assertIndexColumnExists('username');
        $this->assertIndexColumnExists('email');
        $this->assertIndexColumnExists('roles');
    }

    public function testGenericUserActionsDisplaysForAdmin(): void
    {
        $this->login('admin@email.com');
        $this->client->request("GET", $this->generateIndexUrl());

        $users = $this->entityManager->getRepository(User::class)->findAll();
        self::assertIndexFullEntityCount(count($users));

        $user = $users[0];
        self::assertNotNull($user->getId());

        $this->assertIndexEntityActionExists(Action::DETAIL, $user->getId());
        $this->assertIndexEntityActionExists(Action::EDIT, $user->getId());
        $this->assertIndexEntityActionExists(Action::DELETE, $user->getId());
    }

    #[DataProvider('nonAdminUserEmails')]
    public function testGenericUserActionsDisplaysForNonAdmin(string $userEmail): void
    {
        $this->login($userEmail);
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

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }
}
