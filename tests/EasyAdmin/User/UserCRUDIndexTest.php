<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\User;

use App\Controller\EasyAdmin\UserCrudController;
use App\Entity\User;
use App\Tests\EasyAdmin\BaseAdminUserDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class UserCRUDIndexTest extends BaseEasyAdminWebTestCase
{
    use BaseAdminUserDataTrait;

    /**
     * @test
     * @dataProvider getAllUserData
     */
    public function indexIsDisplayedForAllUsers(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        self::assertResponseIsSuccessful();
    }

    /**
     * @test
     * @dataProvider getAllUserData
     */
    public function allUserInDBAreListedInUserIndex(User $user): void
    {
        $this->loginUser($user);
        $this->getAdminUrl(UserCrudController::class, Action::INDEX);

        $users = $this->client->getCrawler()->filter('tbody tr');
        self::assertCount(7, $users);
    }
}
