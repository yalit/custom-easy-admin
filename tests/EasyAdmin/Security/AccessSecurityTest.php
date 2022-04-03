<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Security;

use App\Entity\User;
use App\Tests\EasyAdmin\BaseAdminUserDataTrait;
use App\Tests\EasyAdmin\BaseEasyAdminWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccessSecurityTest extends BaseEasyAdminWebTestCase
{
    use BaseAdminUserDataTrait;

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
     * @dataProvider getAllUserUsers
     */
    public function noAccessForUserWithOnlyROLE_USER(User $user)
    {
        $this->client->request('GET', '/en/login');

        $this->client->submitForm('Sign in', [
            '_username' => $user->getUsername(),
            '_password' => $user->getPassword()
        ]);

        $this->client->request('GET', '/en/easyadmin');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     * @dataProvider getAllEasyAdminUsers
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
