<?php

namespace App\Tests;

use App\Story\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractAppWebTestCase extends WebTestCase
{
    use ResetDatabase, Factories;

    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    protected function login(string $email = "admin@email.com", $password = UserFactory::PASSWORD): void
    {
        $this->client->request(Request::METHOD_GET, 'login');

        $this->client->submitForm('login_submit', [
            'email' => $email,
            'password' => $password,
        ]);
        $this->client->followRedirect();
        $this->client->followRedirect();
    }
}
