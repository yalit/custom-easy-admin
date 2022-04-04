<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseEasyAdminWebTestCase extends WebTestCase implements BaseAdminTest
{
    protected KernelBrowser $client;
    protected AdminUrlGenerator $adminUrlGenerator;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->adminUrlGenerator = static::getContainer()->get(AdminUrlGenerator::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->adminUrlGenerator);
        unset($this->entityManager);
    }

    public function loginUser(User $user): void
    {
        $this->client->request('GET', '/en/login');

        $this->client->submitForm('Sign in', [
            '_username' => $user->getUsername(),
            '_password' => $user->getPassword()
        ]);

        $this->client->request('GET', '/en/easyadmin');
        $this->client->followRedirect();
    }

    public function getAdminUrl(string $CRUDControllerFqcn, string $action, string $entityId = null)
    {
        $this->adminUrlGenerator->setController($CRUDControllerFqcn)->setAction($action);

        if (null !== $entityId) {
            $this->adminUrlGenerator->setEntityId($entityId);
        }

        $this->client->request('GET', $this->adminUrlGenerator->generateUrl());
    }
}
