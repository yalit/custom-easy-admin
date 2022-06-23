<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin;

use App\Entity\User;
use App\Tests\EasyAdmin\Traits\DatabaseReloadTrait;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

abstract class BaseEasyAdminPantherTestCase extends PantherTestCase implements BaseAdminTest
{
    use DatabaseReloadTrait;

    protected const EA_URL = '/en/easyadmin';

    protected Client $client;
    protected AdminUrlGenerator $adminUrlGenerator;
    protected EntityManagerInterface $entityManager;
    protected bool $logged = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createPantherClient([
            'browser' => PantherTestCase::FIREFOX,
        ]);
        $this->adminUrlGenerator = static::getContainer()->get(AdminUrlGenerator::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if ($this->logged) {
            $this->client->request('GET', '/en/logout');
            $this->logged = false;
        }
        unset($this->client);
        unset($this->adminUrlGenerator);
        unset($this->entityManager);
    }

    public function loginUser(User $user): void
    {
        TestLoginHelpers::loginWebDriver(
            $this->client,
            $user->getUsername(),
            $user->getPassword(),
            '/en/easyadmin'
        );

        $this->logged = true;

        $this->client->request('GET', self::EA_URL);
    }

    public function getAdminUrl(string $CRUDControllerClass, string $action, string $entityId = null)
    {
        throw new \Exception("Not compatible with EasyAdmin URL Generator");
    }

    public function goToNextPage(): void
    {
        $crawler = $this->client->getCrawler();
        $navigationButtons = $crawler->filter('a.page-link .btn-label');

        $clicked = false;
        /** @var RemoteWebElement $navigationButton */
        foreach ($navigationButtons as $navigationButton) {
            $navigationButton->getLocationOnScreenOnceScrolledIntoView();
            if (!$clicked && $navigationButton->getText() === "Next") {
                $navigationButton->click();
                $clicked = true;
            }
        }

        $this->client->refreshCrawler();
    }
}
