<?php

namespace App\Tests\Integration;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractAppKernelTestCase extends KernelTestCase
{
    use ResetDatabase, Factories;

    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        unset($this->entityManager);
    }

    protected function login(string $email = "admin@email.com"): void
    {
        /** @var Security $security */
        $security = static::getContainer()->get(Security::class);
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        self::assertNotNull($user);

        $security->login($user);
    }
}
