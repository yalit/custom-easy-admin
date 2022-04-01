<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Security;

use App\Entity\User;
use App\Tests\EasyAdmin\BaseAdminDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;

class AccessSecurityTest extends BaseEasyAdminWebTestCase
{
    use BaseAdminDataTrait;

    /**
     * @test
     */
    public function noAccessForAnonymousUser()
    {
        $this->client->request('GET', '/en/easyadmin');

        self::assertResponseRedirects();
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertRouteSame('security_login');
    }

    /**
     * @test
     * @dataProvider getAllUserData
     */
    public function accessGrantedForLoggedInUsers(User $user)
    {
        $this->loginUser($user);
        $this->assertEasyAdminIsAccessible();
    }

    public function assertEasyAdminIsAccessible(): void
    {
        $this->client->request('GET', '/en/easyadmin');
        self::assertResponseRedirects();
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertRouteSame('easyadmin');
    }
}
