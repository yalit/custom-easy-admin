<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Security;

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
}
