<?php

namespace App\DataFixtures;

use App\Entity\Enums\PostStatus;
use App\Entity\Enums\UserRole;
use App\Story\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
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
        PostFactory::manyForStatus(PostStatus::DRAFT, $nb);
        PostFactory::manyForStatus(PostStatus::IN_REVIEW, $nb);
        PostFactory::manyForStatus(PostStatus::PUBLISHED, $nb);
        PostFactory::manyForStatus(PostStatus::ARCHIVED, $nb);
        PostFactory::manyForStatus(PostStatus::DRAFT, $nb, UserRole::ADMIN);
        PostFactory::manyForStatus(PostStatus::IN_REVIEW, $nb, UserRole::ADMIN);
        PostFactory::manyForStatus(PostStatus::PUBLISHED, $nb, UserRole::ADMIN);
        PostFactory::manyForStatus(PostStatus::ARCHIVED, $nb, UserRole::ADMIN);
    }

    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
            UserFixtures::class,
        ];
    }
}
