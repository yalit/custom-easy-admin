<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Panther\Client;

final class TestLoginHelpers
{
    static public function loginWebDriver(
        Client $client,
        string $username,
        string $password,
        ?string $targetPath = null
    ): void {
        $client->get('/en/login');

        TestFormHelpers::submitFormWebDriver(
            $client,
            [
                new ApplicationTestFormData('#username', $username),
                new ApplicationTestFormData('#password', $password),
            ],
            'button[type="submit"]'
        );

        if ($targetPath) {
            $client->get($targetPath);
        }
    }

    static public function loginKernelBrowser(
        KernelBrowser $client,
        string $username,
        string $password,
        ?string $targetPath = null
    ): void {
        $client->request('GET', '/en/login');

        TestFormHelpers::submitFormKernelBrowser(
            $client,
            [
                new ApplicationTestFormData('_username', $username),
                new ApplicationTestFormData('_password', $password),
            ],
            'button[type="submit"]'
        );

        $client->followRedirect();

        if ($targetPath) {
            $client->request('GET', $targetPath);
        }
    }
}
