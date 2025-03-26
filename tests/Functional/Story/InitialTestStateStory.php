<?php

namespace App\Tests\Functional\Story;

use App\Entity\Enums\UserRole;
use App\Factory\PostFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class InitialTestStateStory extends Story
{
    public function build(): void
    {
        TagFactory::createMany(3);
        UserFactory::admin();
        UserFactory::anyPublisher();
        UserFactory::anyAuthor();
        PostFactory::draft(1);
        PostFactory::inReview(1);
        PostFactory::published(1);
        PostFactory::draft(1, UserRole::ADMIN);
        PostFactory::inReview(1, UserRole::ADMIN);
        PostFactory::published(1, UserRole::ADMIN);
    }
}
