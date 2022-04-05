<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\User;

use App\Controller\EasyAdmin\UserCrudController;
use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Tests\EasyAdmin\Traits\EasyAdminUserDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class UserCRUDIndexTest extends BaseEasyAdminWebTestCase
{
    use EasyAdminUserDataTrait;

    /**
     * @test
     * @dataProvider getAllEasyAdminUsers
     */
    public function indexIsDisplayedForAllUsers(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        self::assertResponseIsSuccessful();
    }

    /**
     * @test
     * @dataProvider getAllEasyAdminUsers
     */
    public function allUserInDBAreListedInUserIndex(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $users = $this->client->getCrawler()->filter('tbody tr');
        self::assertCount(count(AppFixtures::getUserData()), $users);
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function adminUsersCanSeeCreateAction(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $actionNewButton = $this->client->getCrawler()->filter(".action-new");
        self::assertCount(1, $actionNewButton);
    }

    /**
     * @test
     * @dataProvider getAllNonAdminUsers
     */
    public function nonAdminUsersCannotSeeCreateAction(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $actionNewButton = $this->client->getCrawler()->filter(".action-new");
        self::assertCount(0, $actionNewButton);
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function adminUsersCanSeeEditAction(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $actionNewButton = $this->client->getCrawler()->filter(".action-edit");
        self::assertCount(count(AppFixtures::getUserData()), $actionNewButton);
    }

    /**
     * @test
     * @dataProvider getAllNonAdminUsers
     */
    public function nonAdminUsersCannotSeeEditAction(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $actionNewButton = $this->client->getCrawler()->filter(".action-edit");
        self::assertCount(0, $actionNewButton);
    }

    /**
     * @test
     * @dataProvider getAllAdminUsers
     */
    public function adminUsersCanSeeDeleteAction(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $actionNewButton = $this->client->getCrawler()->filter(".action-delete");
        self::assertCount(count(AppFixtures::getUserData()), $actionNewButton);
    }

    /**
     * @test
     * @dataProvider getAllNonAdminUsers
     */
    public function nonAdminUsersCannotSeeDeleteAction(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $actionNewButton = $this->client->getCrawler()->filter(".action-delete");
        self::assertCount(0, $actionNewButton);
    }
}
