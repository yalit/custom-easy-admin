<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use App\Factory\UserFactory;
use App\Tests\AbstractAppWebTestCase;
use App\Tests\Functional\Story\TestLoginStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginTestCaseTest extends AbstractAppWebTestCase
{
    use ResetDatabase, Factories;

    protected function setUp(): void
    {
        parent::setUp();
        TestLoginStory::load();
    }

    public function testLoginAccessIsPublic(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form#login');
    }

    #[DataProvider('loginUserEmails')]
    public function testLoginForUser(string $userEmail): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('login_submit', [
            'email' => $userEmail,
            'password' => UserFactory::PASSWORD,
        ]);

        self::assertResponseRedirects();
        $this->client->followRedirect();
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertRouteSame('admin');
    }

    public static function loginUserEmails(): array
    {
        return [
            'admin' => ['admin@email.com'],
            'publisher' => ['publisher_1@email.com'],
            'author' => ['author_1@email.com'],
        ];
    }
}
