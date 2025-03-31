<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use App\Entity\Enums\UserRole;
use App\Story\Factory\UserFactory;
use App\Tests\AbstractAppWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class LoginTestCaseTest extends AbstractAppWebTestCase
{
    public function testLoginAccessIsPublic(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form#login');
    }

    public static function loginUserRoles(): iterable
    {
        yield "Author" => [UserRole::AUTHOR];
        yield "Admin" => [UserRole::ADMIN];
        yield "Publisher" => [UserRole::PUBLISHER];
    }

    #[DataProvider('loginUserRoles')]
    public function testLoginForUser(UserRole $role): void
    {
        $user = $this->anyUser($role);
        $this->client->request('GET', '/login');

        $this->client->submitForm('login_submit', [
            'email' => $user->getEmail(),
            'password' => UserFactory::PASSWORD,
        ]);

        self::assertResponseRedirects();
        $this->client->followRedirect();
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertRouteSame('admin');
    }
}
