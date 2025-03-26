<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Tests\AbstractAppWebTestCase;
use App\Tests\Functional\Story\FunctionalTestStory;
use App\Tests\Functional\Story\InitialTestStateStory;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Foundry\Attribute\WithStory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[WithStory(InitialTestStateStory::class)]
class LoginTestCaseTest extends AbstractAppWebTestCase
{
    use ResetDatabase, Factories;

    public function testLoginAccessIsPublic(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form#login');
    }

    #[DataProvider('loginUserEmails')]
    public function testLoginForUser(User $user): void
    {
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

    public static function loginUserEmails(): array
    {
        return FunctionalTestStory::oneUserOfEach();
    }
}
