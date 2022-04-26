<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use App\Entity\User;
use App\Tests\EasyAdmin\Traits\DatabaseReloadTrait;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseEasyAdminWebTestCase extends WebTestCase implements BaseAdminTest
{
    use DatabaseReloadTrait;

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
        TestLoginHelpers::loginKernelBrowser(
            $this->client,
            $user->getUsername(),
            $user->getPassword(),
            '/en/easyadmin'
        );

        $this->client->followRedirect();
    }

    public function getAdminUrl(string $CRUDControllerClass, string $action, string $entityId = null)
    {
        $this->adminUrlGenerator->setController($CRUDControllerClass)->setAction($action);

        if (null !== $entityId) {
            $this->adminUrlGenerator->setEntityId($entityId);
        }

        $this->client->request('GET', $this->adminUrlGenerator->generateUrl());
    }
}
