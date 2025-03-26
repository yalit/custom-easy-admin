<?php

declare(strict_types=1);

namespace App\Tests\Functional\EasyAdmin\User;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use App\Tests\Functional\EasyAdmin\AbstractAppCrudTestCase;
use App\Tests\Functional\Story\FunctionalTestStory;
use App\Tests\Functional\Story\InitialTestStateStory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\WithStory;

#[WithStory(InitialTestStateStory::class)]
class UserListingTest extends AbstractAppCrudTestCase
{
    use CrudTestIndexAsserts;

    public static function allUsers(): array
    {
        return FunctionalTestStory::oneUserOfEach();
    }

    public static function nonAdminUsers(): array
    {
        return FunctionalTestStory::noAdminUsers();
    }

    #[DataProvider('allUsers')]
    public function testUserIndexDisplays(User $user): void
    {
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

    #[DataProvider('nonAdminUsers')]
    public function testGenericUserActionsDisplaysForNonAdmin(User $user): void
    {
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
