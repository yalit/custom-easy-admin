<?php

namespace App\Tests\Story;

use App\Entity\Enums\UserRole;
use App\Story\Factory\PostFactory;
use App\Story\Factory\TagFactory;
use App\Story\Factory\UserFactory;
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
        PostFactory::archived(1);
        PostFactory::rejected(1);
        PostFactory::draft(1, UserRole::ADMIN);
        PostFactory::inReview(1, UserRole::ADMIN);
        PostFactory::published(1, UserRole::ADMIN);
        PostFactory::archived(1, UserRole::ADMIN);
        PostFactory::rejected(1, UserRole::ADMIN);
    }
}
