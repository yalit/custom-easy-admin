<?php

declare(strict_types=1);

namespace App\Tests\EasyAdmin\Traits;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Component\HttpKernel\KernelInterface;

trait DatabaseReloadTrait
{
    /**
     * Boots the Kernel for this test.
     */
    protected static function bootKernel(array $options = []): KernelInterface
    {
        $kernel = parent::bootKernel($options);
        static::populateDatabase();

        return $kernel;
    }

    protected static function populateDatabase(): void
    {
        /** @var AbstractDatabaseTool $databaseTool */
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadAllFixtures();
    }
}