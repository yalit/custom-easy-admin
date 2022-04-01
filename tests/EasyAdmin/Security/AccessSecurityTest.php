<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Security;

use App\DataFixtures\AppFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessSecurityTest extends WebTestCase
{
    /**
     * @test
     */
    public function noAccessForAnonymousUser()
    {
        $client = static::createClient();
        $client->request('GET', '/en/easyadmin');

        self::assertResponseRedirects();
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertRouteSame('security_login');
    }

    /**
     * @test
     * @dataProvider getUserData
     */
    public function accessGrantedForLoggedInUsers(string $username, string $password)
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW' => $password,
        ]);
        $client->request('GET', '/en/easyadmin');
        self::assertResponseRedirects();
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertRouteSame('easyadmin');
    }

    public function getUserData(): array
    {
        return array_map(function($user){
            return [$user[1], $user[2]];
        }, AppFixtures::getUserData());
    }
}
