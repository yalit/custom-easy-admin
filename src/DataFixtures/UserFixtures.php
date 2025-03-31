<?php

namespace App\DataFixtures;

use App\Story\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly string $kernelEnvironment
    ) { }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $nb = $this->kernelEnvironment === 'test' ? 1 : 5;
        UserFactory::admin();
        UserFactory::publishers(nb: $nb);
        UserFactory::authors(nb: $nb);
    }
}
